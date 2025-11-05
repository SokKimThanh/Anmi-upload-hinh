<?php
/**
 * Plugin Name: Anmi Watchdog
 * Description: Mu-plugin bảo vệ site khỏi fatal errors từ plugins mới được kích hoạt
 * Version: 1.0.0
 * Author: Anmi
 * Requires PHP: 7.4
 */

defined('ABSPATH') || exit;

class Anmi_Watchdog {
    
    const PENDING_OPTION = 'anmi_pm_pending_activation';
    const LOGS_OPTION = 'anmi_pm_watchdog_logs';
    const KILLSWITCH_OPTION = 'anmi_pm_killswitch';
    const WINDOW_SECONDS = 60;
    const MAX_LOG_ENTRIES = 200;
    
    private static $instance = null;
    
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Đăng ký shutdown handler để bắt fatal errors
        register_shutdown_function([$this, 'handle_shutdown']);
        
        // Đăng ký admin endpoints
        add_action('admin_init', [$this, 'handle_admin_actions']);
        
        // Auto-clear expired pending
        add_action('init', [$this, 'clear_expired_pending']);
    }
    
    /**
     * Handler chạy khi PHP shutdown - bắt fatal errors
     */
    public function handle_shutdown() {
        $error = error_get_last();
        
        // Kiểm tra nếu là fatal error
        if (!$error || !in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            return;
        }
        
        // Kiểm tra killswitch
        if (get_option(self::KILLSWITCH_OPTION, 0)) {
            return; // Đã bị disable
        }
        
        // Lấy danh sách pending plugins
        $pending = get_option(self::PENDING_OPTION, []);
        if (empty($pending) || !is_array($pending)) {
            return;
        }
        
        $current_time = time();
        $recovered = false;
        
        foreach ($pending as $plugin_file => $meta) {
            // Kiểm tra trong window time (60s)
            $activation_time = isset($meta['time']) ? (int)$meta['time'] : 0;
            if (($current_time - $activation_time) > self::WINDOW_SECONDS) {
                continue;
            }
            
            // Fatal error trong window → thực hiện recovery
            $recovery_result = $this->attempt_recovery($plugin_file, $meta, $error);
            
            if ($recovery_result['success']) {
                $recovered = true;
                $this->log_action('recovery_success', [
                    'plugin_file' => $plugin_file,
                    'error' => $error,
                    'actions_taken' => $recovery_result['actions']
                ]);
            } else {
                $this->log_action('recovery_failed', [
                    'plugin_file' => $plugin_file,
                    'error' => $error,
                    'reason' => $recovery_result['reason']
                ]);
            }
        }
        
        if ($recovered) {
            // Set killswitch để ngăn loop
            update_option(self::KILLSWITCH_OPTION, 1);
            
            // Clear pending
            delete_option(self::PENDING_OPTION);
        }
    }
    
    /**
     * Thử khôi phục từ fatal error
     */
    private function attempt_recovery($plugin_file, $meta, $error) {
        $actions = [];
        $success = false;
        
        try {
            // Step 1: Deactivate plugin
            if (function_exists('deactivate_plugins')) {
                @deactivate_plugins($plugin_file, true);
                $actions[] = 'deactivated';
            }
            
            // Step 2: Restore từ backup hoặc quarantine
            $plugin_dir = isset($meta['plugin_dir']) ? $meta['plugin_dir'] : '';
            $backup_zip = isset($meta['backup_zip']) ? $meta['backup_zip'] : '';
            
            if (!empty($backup_zip) && file_exists($backup_zip)) {
                // Restore từ backup
                $restore_result = $this->restore_from_backup($backup_zip, $plugin_dir);
                if ($restore_result) {
                    $actions[] = 'restored_from_backup';
                    $success = true;
                }
            } elseif (!empty($plugin_dir)) {
                // Quarantine plugin
                $quarantine_result = $this->quarantine_plugin($plugin_dir);
                if ($quarantine_result) {
                    $actions[] = 'quarantined';
                    $success = true;
                }
            }
            
            return [
                'success' => $success,
                'actions' => $actions
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'reason' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Restore plugin từ backup zip
     */
    private function restore_from_backup($backup_zip, $plugin_dir) {
        if (!class_exists('ZipArchive')) {
            return false;
        }
        
        try {
            $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_dir;
            
            // Xóa thư mục hiện tại (có thể corrupt)
            if (is_dir($plugin_path)) {
                $this->recursive_delete($plugin_path);
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
     * Di chuyển plugin vào quarantine
     */
    private function quarantine_plugin($plugin_dir) {
        try {
            $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_dir;
            if (!is_dir($plugin_path)) {
                return false;
            }
            
            $quarantine_dir = WP_CONTENT_DIR . '/anmi-quarantine';
            if (!is_dir($quarantine_dir)) {
                @mkdir($quarantine_dir, 0755, true);
            }
            
            $timestamp = date('Ymd_His');
            $quarantine_path = $quarantine_dir . '/' . basename($plugin_dir) . '_' . $timestamp;
            
            return @rename($plugin_path, $quarantine_path);
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Xóa thư mục đệ quy
     */
    private function recursive_delete($dir) {
        if (!is_dir($dir)) {
            return @unlink($dir);
        }
        
        $items = @scandir($dir);
        if (!$items) {
            return false;
        }
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->recursive_delete($path);
            } else {
                @unlink($path);
            }
        }
        
        return @rmdir($dir);
    }
    
    /**
     * Ghi log hành động
     */
    private function log_action($action, $data) {
        $logs = get_option(self::LOGS_OPTION, []);
        if (!is_array($logs)) {
            $logs = [];
        }
        
        $logs[] = [
            'action' => $action,
            'data' => $data,
            'timestamp' => current_time('mysql'),
            'time' => time()
        ];
        
        // Giới hạn số lượng logs
        if (count($logs) > self::MAX_LOG_ENTRIES) {
            $logs = array_slice($logs, -self::MAX_LOG_ENTRIES);
        }
        
        update_option(self::LOGS_OPTION, $logs);
    }
    
    /**
     * Tự động xóa pending đã hết hạn
     */
    public function clear_expired_pending() {
        $pending = get_option(self::PENDING_OPTION, []);
        if (empty($pending) || !is_array($pending)) {
            return;
        }
        
        $current_time = time();
        $updated = false;
        
        foreach ($pending as $plugin_file => $meta) {
            $activation_time = isset($meta['time']) ? (int)$meta['time'] : 0;
            if (($current_time - $activation_time) > self::WINDOW_SECONDS) {
                unset($pending[$plugin_file]);
                $updated = true;
            }
        }
        
        if ($updated) {
            if (empty($pending)) {
                delete_option(self::PENDING_OPTION);
            } else {
                update_option(self::PENDING_OPTION, $pending);
            }
        }
    }
    
    /**
     * Xử lý admin actions
     */
    public function handle_admin_actions() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $action = isset($_GET['anmi_watchdog_action']) ? sanitize_text_field($_GET['anmi_watchdog_action']) : '';
        if (empty($action)) {
            return;
        }
        
        // Verify nonce
        $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';
        if (!wp_verify_nonce($nonce, 'anmi_watchdog_' . $action)) {
            wp_die('Nonce verification failed');
        }
        
        switch ($action) {
            case 'view_logs':
                $this->display_logs();
                break;
                
            case 'clear_pending':
                delete_option(self::PENDING_OPTION);
                wp_redirect(admin_url('admin.php?page=anmi-plugins&watchdog_msg=pending_cleared'));
                exit;
                
            case 'enable_kill':
                update_option(self::KILLSWITCH_OPTION, 1);
                wp_redirect(admin_url('admin.php?page=anmi-plugins&watchdog_msg=killswitch_enabled'));
                exit;
                
            case 'disable_kill':
                update_option(self::KILLSWITCH_OPTION, 0);
                wp_redirect(admin_url('admin.php?page=anmi-plugins&watchdog_msg=killswitch_disabled'));
                exit;
                
            case 'clear_logs':
                delete_option(self::LOGS_OPTION);
                wp_redirect(admin_url('admin.php?page=anmi-plugins&watchdog_msg=logs_cleared'));
                exit;
        }
    }
    
    /**
     * Hiển thị logs
     */
    private function display_logs() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $logs = get_option(self::LOGS_OPTION, []);
        $killswitch = get_option(self::KILLSWITCH_OPTION, 0);
        $pending = get_option(self::PENDING_OPTION, []);
        
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Anmi Watchdog Logs</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 20px; background: #f0f0f1; }
                .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
                h1 { color: #1d2327; margin-bottom: 20px; }
                .status { padding: 10px; border-radius: 4px; margin-bottom: 20px; }
                .status.active { background: #d1f0d1; border-left: 4px solid #00a32a; }
                .status.disabled { background: #ffe4e4; border-left: 4px solid #d63638; }
                .actions { margin-bottom: 20px; }
                .btn { display: inline-block; padding: 8px 16px; background: #2271b1; color: white; text-decoration: none; border-radius: 4px; margin-right: 8px; border: none; cursor: pointer; }
                .btn:hover { background: #135e96; }
                .btn.danger { background: #d63638; }
                .btn.danger:hover { background: #b32d2e; }
                .log-entry { border-bottom: 1px solid #e0e0e0; padding: 12px 0; }
                .log-entry:last-child { border-bottom: none; }
                .log-time { color: #666; font-size: 0.9em; }
                .log-action { font-weight: bold; color: #2271b1; }
                .log-data { margin-top: 8px; background: #f6f7f7; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 0.85em; white-space: pre-wrap; }
                .pending-list { background: #fff3cd; border-left: 4px solid #ffb900; padding: 10px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>🛡️ Anmi Watchdog Status</h1>
                
                <div class="status <?php echo $killswitch ? 'disabled' : 'active'; ?>">
                    <strong>Killswitch:</strong> <?php echo $killswitch ? '🔴 DISABLED' : '🟢 ACTIVE'; ?>
                </div>
                
                <?php if (!empty($pending)): ?>
                <div class="pending-list">
                    <strong>⏱️ Pending Activations:</strong>
                    <ul>
                        <?php foreach ($pending as $file => $meta): ?>
                        <li><?php echo esc_html($file); ?> (<?php echo esc_html(date('H:i:s', $meta['time'])); ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="actions">
                    <?php
                    $clear_pending_url = wp_nonce_url(admin_url('admin.php?anmi_watchdog_action=clear_pending'), 'anmi_watchdog_clear_pending');
                    $toggle_kill_url = $killswitch 
                        ? wp_nonce_url(admin_url('admin.php?anmi_watchdog_action=disable_kill'), 'anmi_watchdog_disable_kill')
                        : wp_nonce_url(admin_url('admin.php?anmi_watchdog_action=enable_kill'), 'anmi_watchdog_enable_kill');
                    $clear_logs_url = wp_nonce_url(admin_url('admin.php?anmi_watchdog_action=clear_logs'), 'anmi_watchdog_clear_logs');
                    ?>
                    <a href="<?php echo esc_url($clear_pending_url); ?>" class="btn">Clear Pending</a>
                    <a href="<?php echo esc_url($toggle_kill_url); ?>" class="btn <?php echo $killswitch ? '' : 'danger'; ?>">
                        <?php echo $killswitch ? 'Enable Watchdog' : 'Disable Watchdog'; ?>
                    </a>
                    <a href="<?php echo esc_url($clear_logs_url); ?>" class="btn danger" onclick="return confirm('Xóa tất cả logs?');">Clear Logs</a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=anmi-plugins')); ?>" class="btn">← Back to Plugin Manager</a>
                </div>
                
                <h2>📋 Recovery Logs (<?php echo count($logs); ?>)</h2>
                
                <?php if (empty($logs)): ?>
                    <p style="color: #666;">Chưa có logs nào.</p>
                <?php else: ?>
                    <?php foreach (array_reverse($logs) as $log): ?>
                    <div class="log-entry">
                        <div class="log-time"><?php echo esc_html($log['timestamp']); ?></div>
                        <div class="log-action"><?php echo esc_html($log['action']); ?></div>
                        <div class="log-data"><?php echo esc_html(json_encode($log['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Khởi tạo watchdog
Anmi_Watchdog::instance();
