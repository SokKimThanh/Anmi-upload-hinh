<?php
/**
 * Logger - Ghi log các hành động
 */

defined('ABSPATH') || exit;

class Anmi_PM_Logger {
    
    const MAX_LOGS = 500;
    
    /**
     * Log an action
     */
    public function log($action, $data = [], $old_data = [], $new_data = []) {
        $user_id = get_current_user_id();
        
        $log_data = [
            'action' => $action,
            'data' => $data,
            'old' => $old_data,
            'new' => $new_data,
            'user_id' => $user_id,
            'timestamp' => current_time('mysql'),
            'time' => time()
        ];
        
        // Create log post
        $post_id = wp_insert_post([
            'post_type' => 'anmi_plugin_log',
            'post_title' => $action . ' - ' . current_time('mysql'),
            'post_status' => 'publish',
            'post_content' => wp_json_encode($log_data)
        ]);
        
        // Clean old logs
        $this->clean_old_logs();
        
        return $post_id;
    }
    
    /**
     * Get logs
     */
    public function get_logs($limit = 50, $offset = 0, $filter_action = '') {
        $args = [
            'post_type' => 'anmi_plugin_log',
            'posts_per_page' => $limit,
            'offset' => $offset,
            'orderby' => 'date',
            'order' => 'DESC'
        ];
        
        // Add filter if specified
        if ($filter_action) {
            $args['meta_query'] = [
                [
                    'key' => '_action',
                    'value' => $filter_action,
                    'compare' => '='
                ]
            ];
        }
        
        $posts = get_posts($args);
        
        $logs = [];
        foreach ($posts as $post) {
            $log_data = json_decode($post->post_content, true);
            if ($log_data) {
                // Store action as meta for filtering
                if (!get_post_meta($post->ID, '_action', true)) {
                    update_post_meta($post->ID, '_action', $log_data['action']);
                }
                $logs[] = $log_data;
            }
        }
        
        return $logs;
    }
    
    /**
     * Clean old logs
     */
    private function clean_old_logs() {
        $posts = get_posts([
            'post_type' => 'anmi_plugin_log',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
            'fields' => 'ids'
        ]);
        
        if (count($posts) > self::MAX_LOGS) {
            $to_delete = array_slice($posts, self::MAX_LOGS);
            foreach ($to_delete as $post_id) {
                wp_delete_post($post_id, true);
            }
        }
    }
    
    /**
     * Render logs page
     */
    public function render_logs_page() {
        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 50;
        $offset = ($page - 1) * $per_page;
        
        $filter_action = isset($_GET['filter_action']) ? sanitize_text_field($_GET['filter_action']) : '';
        
        $logs = $this->get_logs($per_page, $offset, $filter_action);
        
        // Count total logs
        $total_logs = wp_count_posts('anmi_plugin_log');
        $total = isset($total_logs->publish) ? $total_logs->publish : 0;
        $total_pages = ceil($total / $per_page);
        
        // Get unique actions for filter
        $all_logs = $this->get_logs(-1, 0);
        $actions = array_unique(array_column($all_logs, 'action'));
        sort($actions);
        
        ?>
        <div class="wrap">
            <h1><?php _e('Plugin History Logs', 'anmi-plugin-manager'); ?></h1>
            
            <div class="tablenav top">
                <div class="alignleft actions">
                    <form method="get">
                        <input type="hidden" name="page" value="anmi-plugins-logs">
                        <select name="filter_action">
                            <option value=""><?php _e('All Actions', 'anmi-plugin-manager'); ?></option>
                            <?php foreach ($actions as $action): ?>
                            <option value="<?php echo esc_attr($action); ?>" <?php selected($filter_action, $action); ?>>
                                <?php echo esc_html($action); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="button"><?php _e('Filter', 'anmi-plugin-manager'); ?></button>
                    </form>
                </div>
            </div>
            
            <?php if (empty($logs)): ?>
                <p><?php _e('Chưa có logs nào.', 'anmi-plugin-manager'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 180px;"><?php _e('Timestamp', 'anmi-plugin-manager'); ?></th>
                            <th style="width: 200px;"><?php _e('Action', 'anmi-plugin-manager'); ?></th>
                            <th style="width: 100px;"><?php _e('User', 'anmi-plugin-manager'); ?></th>
                            <th><?php _e('Details', 'anmi-plugin-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo esc_html($log['timestamp']); ?></td>
                            <td>
                                <strong style="<?php echo self::get_action_color($log['action']); ?>">
                                    <?php echo esc_html($log['action']); ?>
                                </strong>
                            </td>
                            <td>
                                <?php 
                                $user = get_userdata($log['user_id']);
                                echo $user ? esc_html($user->user_login) : 'System';
                                ?>
                            </td>
                            <td>
                                <details>
                                    <summary style="cursor: pointer;">View Data</summary>
                                    <pre style="background: #f6f7f7; padding: 10px; margin-top: 8px; overflow-x: auto;"><?php 
                                        echo esc_html(json_encode($log['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); 
                                    ?></pre>
                                </details>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if ($total_pages > 1): ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <?php
                        echo paginate_links([
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'current' => $page,
                            'total' => $total_pages,
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;'
                        ]);
                        ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Get color for action type
     */
    private static function get_action_color($action) {
        $colors = [
            'upload_success' => 'color: #00a32a;',
            'activate_success' => 'color: #00a32a;',
            'activate_failed' => 'color: #d63638;',
            'upload_rejected' => 'color: #d63638;',
            'health_check_failed' => 'color: #d63638;',
            'rollback_restore' => 'color: #dba617;',
            'delete_success' => 'color: #646970;',
            'plugin_renamed_detected' => 'color: #2271b1;'
        ];
        
        return isset($colors[$action]) ? $colors[$action] : '';
    }
}
