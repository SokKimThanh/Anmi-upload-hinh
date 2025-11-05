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
    public function get_logs($limit = 50, $offset = 0) {
        $posts = get_posts([
            'post_type' => 'anmi_plugin_log',
            'posts_per_page' => $limit,
            'offset' => $offset,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);
        
        $logs = [];
        foreach ($posts as $post) {
            $log_data = json_decode($post->post_content, true);
            if ($log_data) {
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
        
        $logs = $this->get_logs($per_page, $offset);
        
        // Count total logs
        $total_logs = wp_count_posts('anmi_plugin_log');
        $total = isset($total_logs->publish) ? $total_logs->publish : 0;
        $total_pages = ceil($total / $per_page);
        
        ?>
        <div class="wrap">
            <h1><?php _e('Plugin History Logs', 'anmi-plugin-manager'); ?></h1>
            
            <?php if (empty($logs)): ?>
                <p><?php _e('Chưa có logs nào.', 'anmi-plugin-manager'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 180px;"><?php _e('Timestamp', 'anmi-plugin-manager'); ?></th>
                            <th style="width: 150px;"><?php _e('Action', 'anmi-plugin-manager'); ?></th>
                            <th style="width: 100px;"><?php _e('User', 'anmi-plugin-manager'); ?></th>
                            <th><?php _e('Details', 'anmi-plugin-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo esc_html($log['timestamp']); ?></td>
                            <td><strong><?php echo esc_html($log['action']); ?></strong></td>
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
}
