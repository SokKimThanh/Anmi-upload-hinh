<?php
/**
 * Plugin List - Hiển thị danh sách plugins và actions
 */

defined('ABSPATH') || exit;

class Anmi_PM_Plugin_List {
    
    /**
     * Render plugin list page
     */
    public function render() {
        // Handle actions
        $this->handle_actions();
        
        // Get all installed plugins
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins', []);
        $managed_plugins = Anmi_PM_Metadata_Manager::get_all_plugins();
        
        // Filter Anmi plugins
        $anmi_plugins = $this->filter_anmi_plugins($all_plugins, $managed_plugins);
        
        // Display messages
        $this->display_messages();
        
        ?>
        <div class="wrap anmi-pm-wrap">
            <h1 class="wp-heading-inline">
                <?php _e('Anmi Plugin Manager', 'anmi-plugin-manager'); ?>
            </h1>
            
            <a href="<?php echo esc_url(admin_url('admin.php?page=anmi-plugins-upload')); ?>" class="page-title-action">
                <?php _e('Upload New Plugin', 'anmi-plugin-manager'); ?>
            </a>
            
            <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['page' => 'anmi-plugins', 'action' => 'resync'], admin_url('admin.php')), 'anmi_pm_resync')); ?>" class="page-title-action">
                <?php _e('Resync Renamed Plugins', 'anmi-plugin-manager'); ?>
            </a>
            
            <hr class="wp-header-end">
            
            <div class="anmi-pm-stats">
                <div class="stat-box">
                    <span class="stat-number"><?php echo count($anmi_plugins); ?></span>
                    <span class="stat-label">Total Plugins</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number"><?php echo count(array_filter($anmi_plugins, function($p) { return $p['is_active']; })); ?></span>
                    <span class="stat-label">Active</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number"><?php echo count(array_filter($anmi_plugins, function($p) { return $p['managed']; })); ?></span>
                    <span class="stat-label">Managed</span>
                </div>
            </div>
            
            <?php if (empty($anmi_plugins)): ?>
                <div class="notice notice-info">
                    <p><?php _e('Không tìm thấy plugin Anmi nào. Upload plugin mới hoặc mark plugin hiện có.', 'anmi-plugin-manager'); ?></p>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th class="column-name"><?php _e('Plugin Name', 'anmi-plugin-manager'); ?></th>
                            <th class="column-version"><?php _e('Version', 'anmi-plugin-manager'); ?></th>
                            <th class="column-author"><?php _e('Author', 'anmi-plugin-manager'); ?></th>
                            <th class="column-status"><?php _e('Status', 'anmi-plugin-manager'); ?></th>
                            <th class="column-managed"><?php _e('Managed', 'anmi-plugin-manager'); ?></th>
                            <th class="column-actions"><?php _e('Actions', 'anmi-plugin-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($anmi_plugins as $plugin_file => $plugin): ?>
                        <tr>
                            <td class="plugin-title">
                                <strong><?php echo esc_html($plugin['Name']); ?></strong>
                                <div class="plugin-file"><?php echo esc_html($plugin_file); ?></div>
                            </td>
                            <td><?php echo esc_html($plugin['Version']); ?></td>
                            <td><?php echo esc_html($plugin['Author']); ?></td>
                            <td>
                                <?php if ($plugin['is_active']): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($plugin['managed']): ?>
                                    <span class="badge badge-managed">✓ Managed</span>
                                <?php else: ?>
                                    <span class="badge badge-unmanaged">Not Managed</span>
                                <?php endif; ?>
                            </td>
                            <td class="plugin-actions">
                                <?php
                                $nonce = wp_create_nonce('anmi_pm_action_' . $plugin_file);
                                $base_url = admin_url('admin.php?page=anmi-plugins');
                                
                                // Mark/Unmark
                                if ($plugin['managed']) {
                                    $mark_url = add_query_arg([
                                        'action' => 'unmark',
                                        'plugin' => urlencode($plugin_file),
                                        '_wpnonce' => $nonce
                                    ], $base_url);
                                    echo '<a href="' . esc_url($mark_url) . '" class="button button-small">Unmark</a> ';
                                } else {
                                    $mark_url = add_query_arg([
                                        'action' => 'mark',
                                        'plugin' => urlencode($plugin_file),
                                        '_wpnonce' => $nonce
                                    ], $base_url);
                                    echo '<a href="' . esc_url($mark_url) . '" class="button button-small button-primary">Mark</a> ';
                                }
                                
                                // Activate/Deactivate
                                if ($plugin['is_active']) {
                                    $toggle_url = add_query_arg([
                                        'action' => 'deactivate',
                                        'plugin' => urlencode($plugin_file),
                                        '_wpnonce' => $nonce
                                    ], $base_url);
                                    echo '<a href="' . esc_url($toggle_url) . '" class="button button-small">Deactivate</a> ';
                                } else {
                                    $toggle_url = add_query_arg([
                                        'action' => 'activate',
                                        'plugin' => urlencode($plugin_file),
                                        '_wpnonce' => $nonce
                                    ], $base_url);
                                    echo '<a href="' . esc_url($toggle_url) . '" class="button button-small">Activate</a> ';
                                }
                                
                                // Delete
                                if (!$plugin['is_active']) {
                                    $delete_url = add_query_arg([
                                        'action' => 'delete',
                                        'plugin' => urlencode($plugin_file),
                                        '_wpnonce' => $nonce
                                    ], $base_url);
                                    echo '<a href="' . esc_url($delete_url) . '" class="button button-small button-link-delete" onclick="return confirm(\'Xác nhận xóa plugin này?\');">Delete</a>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Filter Anmi plugins
     */
    private function filter_anmi_plugins($all_plugins, $managed_plugins) {
        $active_plugins = get_option('active_plugins', []);
        $anmi_plugins = [];
        
        foreach ($all_plugins as $plugin_file => $plugin_data) {
            // Check if Anmi plugin (by author or managed)
            $is_anmi = (
                stripos($plugin_data['Author'], 'Anmi') !== false ||
                isset($managed_plugins[$plugin_file])
            );
            
            if ($is_anmi) {
                $meta = isset($managed_plugins[$plugin_file]) ? $managed_plugins[$plugin_file] : null;
                
                $anmi_plugins[$plugin_file] = [
                    'Name' => $plugin_data['Name'],
                    'Version' => $plugin_data['Version'],
                    'Author' => $plugin_data['Author'],
                    'is_active' => in_array($plugin_file, $active_plugins),
                    'managed' => $meta ? $meta['managed'] : false,
                    'meta' => $meta
                ];
            }
        }
        
        return $anmi_plugins;
    }
    
    /**
     * Handle actions
     */
    private function handle_actions() {
        if (!isset($_GET['action'])) {
            return;
        }
        
        $action = sanitize_text_field($_GET['action']);
        
        // Handle resync (no plugin parameter needed)
        if ($action === 'resync') {
            $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';
            if (!wp_verify_nonce($nonce, 'anmi_pm_resync')) {
                wp_die('Nonce verification failed');
            }
            
            if (!current_user_can('manage_options')) {
                wp_die('Unauthorized');
            }
            
            $result = Anmi_PM_Rename_Detector::resync_all();
            
            if ($result['renames_found'] > 0) {
                $this->redirect_with_message('resync_success', $result['renames_found'] . ' plugins resynced');
            } else {
                $this->redirect_with_message('resync_none', 'No renamed plugins found');
            }
        }
        
        // Other actions require plugin parameter
        if (!isset($_GET['plugin'])) {
            return;
        }
        
        $plugin_file = sanitize_text_field($_GET['plugin']);
        $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';
        
        // Verify nonce
        if (!wp_verify_nonce($nonce, 'anmi_pm_action_' . $plugin_file)) {
            wp_die('Nonce verification failed');
        }
        
        // Check capability
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $logger = new Anmi_PM_Logger();
        
        switch ($action) {
            case 'mark':
                $this->mark_plugin($plugin_file, true);
                $logger->log('mark_managed', [
                    'plugin_file' => $plugin_file,
                    'managed' => true
                ]);
                $this->redirect_with_message('marked');
                break;
                
            case 'unmark':
                $this->mark_plugin($plugin_file, false);
                $logger->log('unmark_managed', [
                    'plugin_file' => $plugin_file,
                    'managed' => false
                ]);
                $this->redirect_with_message('unmarked');
                break;
                
            case 'activate':
                // Safe activation
                $result = Anmi_PM_Plugin_Activator::safe_activate($plugin_file);
                if ($result['success']) {
                    $this->redirect_with_message('activated');
                } else {
                    $this->redirect_with_message('activate_failed', $result['message']);
                }
                break;
                
            case 'deactivate':
                $result = Anmi_PM_Plugin_Activator::safe_deactivate($plugin_file);
                $this->redirect_with_message('deactivated');
                break;
                
            case 'delete':
                // Safe delete
                $result = Anmi_PM_Plugin_Activator::safe_delete($plugin_file);
                if ($result['success']) {
                    $this->redirect_with_message('deleted');
                } else {
                    $this->redirect_with_message('delete_failed', $result['message']);
                }
                break;
        }
    }
    
    /**
     * Mark plugin as managed
     */
    private function mark_plugin($plugin_file, $managed) {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
        $plugin_data = get_plugin_data($plugin_path);
        
        $plugin_dir = dirname($plugin_file);
        if ($plugin_dir === '.') {
            $plugin_dir = basename($plugin_file, '.php');
        }
        
        $checksum = file_exists($plugin_path) ? sha1_file($plugin_path) : '';
        
        Anmi_PM_Metadata_Manager::save_plugin_meta([
            'plugin_file' => $plugin_file,
            'plugin_dir' => $plugin_dir,
            'name' => $plugin_data['Name'],
            'version' => $plugin_data['Version'],
            'author' => $plugin_data['Author'],
            'checksum' => $checksum,
            'installed_date' => current_time('mysql'),
            'active_status' => is_plugin_active($plugin_file) ? '1' : '0',
            'managed' => $managed
        ]);
    }
    
    /**
     * Redirect with message
     */
    private function redirect_with_message($message, $extra = '') {
        $args = ['page' => 'anmi-plugins', 'message' => $message];
        if ($extra) {
            $args['extra'] = urlencode($extra);
        }
        wp_redirect(add_query_arg($args, admin_url('admin.php')));
        exit;
    }
    
    /**
     * Display messages
     */
    private function display_messages() {
        if (!isset($_GET['message']) && !isset($_GET['upload'])) {
            return;
        }
        
        if (isset($_GET['upload']) && $_GET['upload'] === 'success') {
            $plugin = isset($_GET['plugin']) ? sanitize_text_field($_GET['plugin']) : '';
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo '<strong>Success!</strong> Plugin uploaded successfully: ' . esc_html($plugin);
            echo '</p></div>';
            return;
        }
        
        if (!isset($_GET['message'])) {
            return;
        }
        
        $message = sanitize_text_field($_GET['message']);
        $extra = isset($_GET['extra']) ? sanitize_text_field($_GET['extra']) : '';
        
        $messages = [
            'marked' => ['success', 'Plugin đã được đánh dấu là Managed.'],
            'unmarked' => ['success', 'Plugin đã được bỏ đánh dấu Managed.'],
            'activated' => ['success', 'Plugin đã được kích hoạt an toàn (safe-activated).'],
            'activate_failed' => ['error', 'Không thể kích hoạt plugin' . ($extra ? ': ' . $extra : '')],
            'deactivated' => ['success', 'Plugin đã được tắt.'],
            'deleted' => ['success', 'Plugin đã được xóa. Backup đã được tạo.'],
            'delete_failed' => ['error', 'Không thể xóa plugin' . ($extra ? ': ' . $extra : '')],
            'resync_success' => ['success', 'Resync thành công' . ($extra ? ': ' . $extra : '')],
            'resync_none' => ['info', 'Không tìm thấy plugin nào bị đổi tên.']
        ];
        
        if (isset($messages[$message])) {
            list($type, $text) = $messages[$message];
            echo '<div class="notice notice-' . esc_attr($type) . ' is-dismissible"><p>' . esc_html($text) . '</p></div>';
        }
    }
}
