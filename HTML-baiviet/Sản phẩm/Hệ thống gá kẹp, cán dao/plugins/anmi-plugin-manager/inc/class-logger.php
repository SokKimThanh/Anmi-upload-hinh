<?php
/**
 * Logger - Ghi log các hành động
 */
class Anmi_PM_Logger {

    const MAX_LOGS       = 500;
    const LOGS_PER_PAGE  = 25;

    /**
     * Log an action
     */
    public function log($action, $data = [], $old_data = [], $new_data = []) {
        $user_id = get_current_user_id();

        $log_data = [
            'action'    => $action,
            'data'      => $data,
            'old'       => $old_data,
            'new'       => $new_data,
            'user_id'   => $user_id,
            'timestamp' => current_time('mysql'),
            'time'      => time(),
        ];

        $post_id = wp_insert_post([
            'post_type'    => 'anmi_plugin_log',
            'post_title'   => $action . ' - ' . current_time('mysql'),
            'post_status'  => 'publish',
            'post_content' => wp_json_encode($log_data),
        ]);

        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, '_action', $action);

            if (!empty($data['plugin_file'])) {
                update_post_meta($post_id, '_plugin_file', $data['plugin_file']);
            }
        }

        $this->clean_old_logs();

        return $post_id;
    }

    /**
     * Render logs page via template
     */
    public function render_logs_page() {
        $filters = $this->parse_filters();
        $results = $this->query_logs($filters);

        $view = [
            'filters'    => $filters,
            'logs'       => $results['logs'],
            'pagination' => [
                'total'        => $results['total'],
                'total_pages'  => $results['total_pages'],
                'current_page' => $filters['paged'],
                'per_page'     => $filters['per_page'],
            ],
            'actions'    => $this->get_unique_actions(),
            'plugins'    => $this->get_unique_plugins(),
        ];

        include ANMI_PM_DIR . 'admin/templates/logs.php';
    }

    /**
     * Parse filters from request
     */
    private function parse_filters() {
        $action = isset($_GET['filter_action']) ? sanitize_text_field($_GET['filter_action']) : '';
        $plugin = isset($_GET['filter_plugin']) ? sanitize_text_field($_GET['filter_plugin']) : '';
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $paged  = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;

        return [
            'action'   => $action,
            'plugin'   => $plugin,
            'search'   => $search,
            'paged'    => $paged,
            'per_page' => self::LOGS_PER_PAGE,
        ];
    }

    /**
     * Query logs with filters
     */
    private function query_logs(array $filters) {
        $query_args = [
            'post_type'      => 'anmi_plugin_log',
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'posts_per_page' => $filters['per_page'],
            'paged'          => $filters['paged'],
            'no_found_rows'  => false,
        ];

        $meta_query = [];
        if (!empty($filters['action'])) {
            $meta_query[] = [
                'key'   => '_action',
                'value' => $filters['action'],
            ];
        }

        if (!empty($filters['plugin'])) {
            $meta_query[] = [
                'key'   => '_plugin_file',
                'value' => $filters['plugin'],
            ];
        }

        if (!empty($meta_query)) {
            $query_args['meta_query'] = $meta_query;
        }

        if (!empty($filters['search'])) {
            $query_args['s'] = $filters['search'];
        }

        $query = new WP_Query($query_args);

        $logs = [];
        foreach ($query->posts as $post) {
            $formatted = $this->format_log($post);
            if ($formatted) {
                $logs[] = $formatted;
            }
        }

        wp_reset_postdata();

        return [
            'logs'        => $logs,
            'total'       => (int) $query->found_posts,
            'total_pages' => max(1, (int) $query->max_num_pages),
        ];
    }

    /**
     * Format log entry
     */
    private function format_log(WP_Post $post) {
        $log_data = json_decode($post->post_content, true);
        if (!$log_data) {
            return null;
        }

        $user = isset($log_data['user_id']) ? get_userdata($log_data['user_id']) : null;

        return [
            'id'           => $post->ID,
            'action'       => $log_data['action'] ?? $post->post_title,
            'data'         => $log_data['data'] ?? [],
            'old'          => $log_data['old'] ?? [],
            'new'          => $log_data['new'] ?? [],
            'user'         => $user ? $user->user_login : __('System', 'anmi-plugin-manager'),
            'user_id'      => $log_data['user_id'] ?? 0,
            'timestamp'    => $log_data['timestamp'] ?? $post->post_date,
            'action_style' => self::get_action_color($log_data['action'] ?? ''),
            'plugin_file'  => $log_data['data']['plugin_file'] ?? '',
            'json'         => wp_json_encode($log_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ];
    }

    /**
     * Retrieve unique action names for filter dropdown
     */
    private function get_unique_actions() {
        $posts = get_posts([
            'post_type'      => 'anmi_plugin_log',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        $actions = [];
        foreach ($posts as $post_id) {
            $action = get_post_meta($post_id, '_action', true);
            if ($action) {
                $actions[$action] = $action;
            }
        }

        ksort($actions, SORT_NATURAL | SORT_FLAG_CASE);

        return array_values($actions);
    }

    /**
     * Retrieve unique plugin file references for filtering
     */
    private function get_unique_plugins() {
        $posts = get_posts([
            'post_type'      => 'anmi_plugin_log',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        $plugins = [];
        foreach ($posts as $post_id) {
            $plugin = get_post_meta($post_id, '_plugin_file', true);
            if ($plugin) {
                $plugins[$plugin] = $plugin;
            }
        }

        ksort($plugins, SORT_NATURAL | SORT_FLAG_CASE);

        return array_values($plugins);
    }

    /**
     * Clean old logs to cap storage
     */
    private function clean_old_logs() {
        $limit = $this->get_retention_limit();
        if ($limit === -1) {
            return;
        }

        $posts = get_posts([
            'post_type'      => 'anmi_plugin_log',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
        ]);

        if (count($posts) > $limit) {
            $to_delete = array_slice($posts, $limit);
            foreach ($to_delete as $post_id) {
                wp_delete_post($post_id, true);
            }
        }
    }

    /**
     * Determine retention limit based on settings
     */
    private function get_retention_limit() {
        $settings = Anmi_PM_Settings::get_settings();
        $policy   = $settings['purge_policy'] ?? 'keep_100';

        switch ($policy) {
            case 'keep_50':
                return 50;
            case 'keep_100':
                return 100;
            case 'keep_250':
                return 250;
            case 'keep_all':
                return -1; // Unlimited
            default:
                return self::MAX_LOGS;
        }
    }

    /**
     * Get color for action type
     */
    private static function get_action_color($action) {
        $colors = [
            'upload_success'        => 'badge-success',
            'activate_success'      => 'badge-success',
            'activate_failed'       => 'badge-danger',
            'upload_rejected'       => 'badge-danger',
            'health_check_failed'   => 'badge-warning',
            'rollback_restore'      => 'badge-warning',
            'delete_success'        => 'badge-neutral',
            'plugin_renamed_detected'=> 'badge-info',
        ];

        return $colors[$action] ?? 'badge-neutral';
    }
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
