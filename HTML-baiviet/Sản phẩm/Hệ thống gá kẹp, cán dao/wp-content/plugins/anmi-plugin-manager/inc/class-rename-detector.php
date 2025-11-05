<?php
/**
 * Rename Detector - Phát hiện plugins đã đổi tên
 */

defined('ABSPATH') || exit;

class Anmi_PM_Rename_Detector {
    
    /**
     * Resync tất cả plugins - tìm renamed plugins
     */
    public static function resync_all() {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $logger = new Anmi_PM_Logger();
        $all_plugins = get_plugins();
        $managed_plugins = Anmi_PM_Metadata_Manager::get_all_plugins();
        
        $found_renames = [];
        $not_found = [];
        
        foreach ($managed_plugins as $plugin_file => $meta) {
            // Check if plugin still exists at same location
            if (isset($all_plugins[$plugin_file])) {
                continue; // No change
            }
            
            // Plugin not found at original location - search by checksum/name
            $match = self::find_by_checksum_or_name($all_plugins, $meta);
            
            if ($match) {
                // Found renamed plugin
                $found_renames[] = [
                    'old' => $plugin_file,
                    'new' => $match['plugin_file'],
                    'method' => $match['method']
                ];
                
                // Update metadata
                Anmi_PM_Metadata_Manager::save_plugin_meta([
                    'plugin_file' => $match['plugin_file'],
                    'plugin_dir' => dirname($match['plugin_file']),
                    'name' => $match['name'],
                    'version' => $match['version'],
                    'author' => $match['author'],
                    'checksum' => $match['checksum'],
                    'installed_date' => $meta['installed_date'],
                    'active_status' => is_plugin_active($match['plugin_file']) ? '1' : '0',
                    'managed' => true,
                    'backup_zip' => $meta['backup_zip']
                ]);
                
                // Delete old metadata
                Anmi_PM_Metadata_Manager::delete_plugin_meta($plugin_file);
                
                $logger->log('plugin_renamed_detected', [
                    'old_file' => $plugin_file,
                    'new_file' => $match['plugin_file'],
                    'detection_method' => $match['method']
                ]);
                
            } else {
                $not_found[] = $plugin_file;
            }
        }
        
        return [
            'renames_found' => count($found_renames),
            'renames' => $found_renames,
            'not_found' => $not_found
        ];
    }
    
    /**
     * Find plugin by checksum or name
     */
    private static function find_by_checksum_or_name($all_plugins, $meta) {
        $old_checksum = isset($meta['checksum']) ? $meta['checksum'] : '';
        $old_name = isset($meta['name']) ? $meta['name'] : '';
        
        foreach ($all_plugins as $plugin_file => $plugin_data) {
            $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
            
            if (!file_exists($plugin_path)) {
                continue;
            }
            
            // Method 1: Checksum match
            if ($old_checksum) {
                $current_checksum = sha1_file($plugin_path);
                if ($current_checksum === $old_checksum) {
                    return [
                        'plugin_file' => $plugin_file,
                        'name' => $plugin_data['Name'],
                        'version' => $plugin_data['Version'],
                        'author' => $plugin_data['Author'],
                        'checksum' => $current_checksum,
                        'method' => 'checksum'
                    ];
                }
            }
            
            // Method 2: Name + Author match
            if ($old_name && $plugin_data['Name'] === $old_name) {
                $old_author = isset($meta['author']) ? $meta['author'] : '';
                if ($old_author && $plugin_data['Author'] === $old_author) {
                    return [
                        'plugin_file' => $plugin_file,
                        'name' => $plugin_data['Name'],
                        'version' => $plugin_data['Version'],
                        'author' => $plugin_data['Author'],
                        'checksum' => sha1_file($plugin_path),
                        'method' => 'name_author'
                    ];
                }
            }
        }
        
        return null;
    }
}
