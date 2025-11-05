<?php
/**
 * Plugin Activator - Safe Activate với Watchdog Integration
 */

defined('ABSPATH') || exit;

class Anmi_PM_Plugin_Activator {
    
    const HEALTH_CHECK_TIMEOUT = 10; // seconds
    
    /**
     * Safe activate plugin với watchdog protection
     */
    public static function safe_activate($plugin_file) {
        $logger = new Anmi_PM_Logger();
        
        // Get plugin metadata
        $meta = Anmi_PM_Metadata_Manager::get_plugin_meta($plugin_file);
        if (!$meta) {
            return [
                'success' => false,
                'message' => 'Plugin metadata not found. Mark as managed first.'
            ];
        }
        
        // Check if already active
        if (is_plugin_active($plugin_file)) {
            return [
                'success' => false,
                'message' => 'Plugin already active'
            ];
        }
        
        $logger->log('safe_activate_start', ['plugin_file' => $plugin_file]);
        
        // Step 1: Set pending activation trong watchdog
        $pending_data = [
            $plugin_file => [
                'time' => time(),
                'plugin_dir' => $meta['plugin_dir'],
                'backup_zip' => $meta['backup_zip']
            ]
        ];
        update_option('anmi_pm_pending_activation', $pending_data);
        
        // Step 2: Activate plugin
        $activate_result = activate_plugin($plugin_file, '', false, true);
        
        if (is_wp_error($activate_result)) {
            // Activation failed immediately
            delete_option('anmi_pm_pending_activation');
            
            $logger->log('activate_failed_immediate', [
                'plugin_file' => $plugin_file,
                'error' => $activate_result->get_error_message()
            ]);
            
            return [
                'success' => false,
                'message' => 'Activation failed: ' . $activate_result->get_error_message()
            ];
        }
        
        // Step 3: Health check
        sleep(1); // Give plugin time to initialize
        
        $health_result = self::health_check();
        
        if (!$health_result['healthy']) {
            // Health check failed → rollback
            $logger->log('health_check_failed', [
                'plugin_file' => $plugin_file,
                'error' => $health_result['error']
            ]);
            
            // Deactivate
            deactivate_plugins($plugin_file, true);
            
            // Restore from backup if available
            if (!empty($meta['backup_zip']) && file_exists($meta['backup_zip'])) {
                self::restore_from_backup($meta['backup_zip'], $meta['plugin_dir']);
                $logger->log('rollback_restore', [
                    'plugin_file' => $plugin_file,
                    'backup_zip' => $meta['backup_zip']
                ]);
            }
            
            delete_option('anmi_pm_pending_activation');
            
            return [
                'success' => false,
                'message' => 'Health check failed, rolled back: ' . $health_result['error']
            ];
        }
        
        // Step 4: Success - clear pending
        delete_option('anmi_pm_pending_activation');
        
        // Update metadata
        Anmi_PM_Metadata_Manager::update_active_status($plugin_file, true);
        
        $logger->log('activate_success', [
            'plugin_file' => $plugin_file,
            'version' => $meta['version']
        ]);
        
        return [
            'success' => true,
            'message' => 'Plugin activated successfully'
        ];
    }
    
    /**
     * Health check - kiểm tra site còn hoạt động không
     */
    private static function health_check() {
        // Method 1: Try admin-ajax.php
        $ajax_url = admin_url('admin-ajax.php');
        $ajax_url = add_query_arg('action', 'anmi_pm_health_check', $ajax_url);
        
        $response = wp_remote_post($ajax_url, [
            'timeout' => self::HEALTH_CHECK_TIMEOUT,
            'sslverify' => false,
            'body' => ['nonce' => wp_create_nonce('anmi_pm_health')]
        ]);
        
        if (is_wp_error($response)) {
            return [
                'healthy' => false,
                'error' => 'HTTP request failed: ' . $response->get_error_message()
            ];
        }
        
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($code !== 200) {
            return [
                'healthy' => false,
                'error' => 'HTTP ' . $code
            ];
        }
        
        // Check response contains expected content
        $data = json_decode($body, true);
        if (isset($data['success']) && $data['success']) {
            return ['healthy' => true];
        }
        
        // Method 2: Check if WordPress core functions still work
        if (function_exists('get_option') && function_exists('wp_get_current_user')) {
            return ['healthy' => true];
        }
        
        return [
            'healthy' => false,
            'error' => 'Health check response invalid'
        ];
    }
    
    /**
     * Restore plugin from backup
     */
    private static function restore_from_backup($backup_zip, $plugin_dir) {
        if (!class_exists('ZipArchive')) {
            return false;
        }
        
        try {
            $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_dir;
            
            // Delete current (corrupt) version
            if (is_dir($plugin_path)) {
                self::recursive_delete($plugin_path);
            }
            
            // Extract backup
            $zip = new ZipArchive();
            if ($zip->open($backup_zip) === true) {
                $zip->extractTo(WP_PLUGIN_DIR);
                $zip->close();
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Safe deactivate (just wrapper for now, có thể extend sau)
     */
    public static function safe_deactivate($plugin_file) {
        $logger = new Anmi_PM_Logger();
        
        deactivate_plugins($plugin_file, true);
        Anmi_PM_Metadata_Manager::update_active_status($plugin_file, false);
        
        $logger->log('deactivate', ['plugin_file' => $plugin_file]);
        
        return [
            'success' => true,
            'message' => 'Plugin deactivated'
        ];
    }
    
    /**
     * Safe delete với backup
     */
    public static function safe_delete($plugin_file) {
        $logger = new Anmi_PM_Logger();
        
        // Get metadata
        $meta = Anmi_PM_Metadata_Manager::get_plugin_meta($plugin_file);
        
        // Must be inactive
        if (is_plugin_active($plugin_file)) {
            return [
                'success' => false,
                'message' => 'Plugin must be deactivated first'
            ];
        }
        
        // Create final backup before delete
        if ($meta && !empty($meta['plugin_dir'])) {
            $backup_zip = self::create_backup($meta['plugin_dir']);
            $logger->log('delete_backup_created', [
                'plugin_file' => $plugin_file,
                'backup_zip' => $backup_zip
            ]);
        }
        
        // Delete plugin files
        if (!function_exists('delete_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $result = delete_plugins([$plugin_file]);
        
        if (is_wp_error($result)) {
            $logger->log('delete_failed', [
                'plugin_file' => $plugin_file,
                'error' => $result->get_error_message()
            ]);
            
            return [
                'success' => false,
                'message' => 'Delete failed: ' . $result->get_error_message()
            ];
        }
        
        // Delete metadata
        Anmi_PM_Metadata_Manager::delete_plugin_meta($plugin_file);
        
        $logger->log('delete_success', ['plugin_file' => $plugin_file]);
        
        return [
            'success' => true,
            'message' => 'Plugin deleted successfully'
        ];
    }
    
    /**
     * Create backup of plugin
     */
    private static function create_backup($plugin_dir) {
        if (!class_exists('ZipArchive')) {
            return null;
        }
        
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_dir;
        if (!is_dir($plugin_path)) {
            return null;
        }
        
        if (!is_dir(ANMI_PM_BACKUP_DIR)) {
            @mkdir(ANMI_PM_BACKUP_DIR, 0755, true);
        }
        
        $timestamp = date('Ymd_His');
        $backup_file = ANMI_PM_BACKUP_DIR . '/' . basename($plugin_dir) . '_delete_' . $timestamp . '.zip';
        
        $zip = new ZipArchive();
        if ($zip->open($backup_file, ZipArchive::CREATE) !== true) {
            return null;
        }
        
        self::add_dir_to_zip($zip, $plugin_path, $plugin_dir);
        $zip->close();
        
        return $backup_file;
    }
    
    /**
     * Add directory to zip
     */
    private static function add_dir_to_zip($zip, $source_dir, $local_dir) {
        $items = @scandir($source_dir);
        if (!$items) return;
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $source_path = $source_dir . '/' . $item;
            $local_path = $local_dir . '/' . $item;
            
            if (is_dir($source_path)) {
                $zip->addEmptyDir($local_path);
                self::add_dir_to_zip($zip, $source_path, $local_path);
            } else {
                $zip->addFile($source_path, $local_path);
            }
        }
    }
    
    /**
     * Recursive delete
     */
    private static function recursive_delete($dir) {
        if (!is_dir($dir)) {
            return @unlink($dir);
        }
        
        $items = @scandir($dir);
        if (!$items) return false;
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                self::recursive_delete($path);
            } else {
                @unlink($path);
            }
        }
        
        return @rmdir($dir);
    }
}

// Register health check AJAX endpoint
add_action('wp_ajax_anmi_pm_health_check', function() {
    $nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
    
    // Simple check - nếu đến đây thì site còn hoạt động
    wp_send_json_success(['message' => 'Health check OK']);
});

add_action('wp_ajax_nopriv_anmi_pm_health_check', function() {
    wp_send_json_success(['message' => 'Health check OK']);
});
