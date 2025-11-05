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
        if ($this->handle_actions()) {
            return;
        }

        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $all_plugins     = get_plugins();
        $managed_plugins = Anmi_PM_Metadata_Manager::get_all_plugins();

        $plugins         = $this->prepare_plugins($all_plugins, $managed_plugins);
        $stats           = $this->build_stats($plugins);
        $filters         = $this->parse_filters();
        $filtered        = $this->apply_filters($plugins, $filters);
        $pagination      = $this->paginate($filtered, $filters['paged'], 10);
        $messages        = $this->collect_messages();
        $resync_url      = $this->get_resync_url();
        $settings        = Anmi_PM_Settings::get_settings();
        $kill_switch     = !empty($settings['kill_switch_enabled']);

        $note = $kill_switch ? trim($settings['kill_switch_note'] ?? '') : '';

        $view = [
            'stats'       => $stats,
            'filters'     => $filters,
            'plugins'     => $pagination['items'],
            'pagination'  => $pagination,
            'messages'    => $messages,
            'resync_url'  => $resync_url,
            'kill_switch' => $kill_switch,
            'confirm_phrase' => 'DELETE',
            'kill_switch_note' => $note,
        ];

        include ANMI_PM_DIR . 'admin/templates/plugin-list.php';
    }

    /**
     * Prepare full plugin dataset
     */
    private function prepare_plugins(array $all_plugins, array $managed_plugins) {
        $active_plugins = get_option('active_plugins', []);
        $records        = [];

        foreach ($all_plugins as $plugin_file => $plugin_data) {
            $is_managed = isset($managed_plugins[$plugin_file]) ? (bool) $managed_plugins[$plugin_file]['managed'] : false;
            $is_anmi    = stripos($plugin_data['Author'], 'anmi') !== false || $is_managed;

            if (!$is_anmi) {
                continue;
            }

            $records[$plugin_file] = [
                'plugin_file' => $plugin_file,
                'name'        => $plugin_data['Name'],
                'version'     => $plugin_data['Version'],
                'author'      => $plugin_data['Author'],
                'description' => $plugin_data['Description'],
                'is_active'   => in_array($plugin_file, $active_plugins, true),
                'managed'     => $is_managed,
                'meta'        => $managed_plugins[$plugin_file] ?? [],
            ];

            $records[$plugin_file]['actions'] = $this->build_action_links($plugin_file, $records[$plugin_file]);
        }

        uasort($records, function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        return $records;
    }

    /**
     * Build statistics for summary cards
     */
    private function build_stats(array $plugins) {
        $total   = count($plugins);
        $active  = count(array_filter($plugins, static function ($plugin) {
            return $plugin['is_active'];
        }));
        $managed = count(array_filter($plugins, static function ($plugin) {
            return $plugin['managed'];
        }));

        return [
            'total'   => $total,
            'active'  => $active,
            'managed' => $managed,
        ];
    }

    /**
     * Parse incoming filters
     */
    private function parse_filters() {
        $status = isset($_GET['filter_status']) ? sanitize_text_field($_GET['filter_status']) : 'all';
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $paged  = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;

        if (!in_array($status, ['all', 'active', 'inactive', 'managed', 'unmanaged'], true)) {
            $status = 'all';
        }

        return [
            'status' => $status,
            'search' => $search,
            'paged'  => $paged,
        ];
    }

    /**
     * Apply filters to dataset
     */
    private function apply_filters(array $plugins, array $filters) {
        $filtered = $plugins;

        if ($filters['status'] !== 'all') {
            $filtered = array_filter($filtered, function ($plugin) use ($filters) {
                switch ($filters['status']) {
                    case 'active':
                        return $plugin['is_active'];
                    case 'inactive':
                        return !$plugin['is_active'];
                    case 'managed':
                        return $plugin['managed'];
                    case 'unmanaged':
                        return !$plugin['managed'];
                    default:
                        return true;
                }
            });
        }

        if (!empty($filters['search'])) {
            $needle = strtolower($filters['search']);
            $filtered = array_filter($filtered, static function ($plugin) use ($needle) {
                return strpos(strtolower($plugin['name']), $needle) !== false
                    || strpos(strtolower($plugin['author']), $needle) !== false
                    || strpos(strtolower($plugin['plugin_file']), $needle) !== false;
            });
        }

        return $filtered;
    }

    /**
     * Paginate results
     */
    private function paginate(array $items, $page, $per_page) {
        $total       = count($items);
        $total_pages = max(1, (int) ceil($total / $per_page));
        $current     = max(1, min($page, $total_pages));
        $offset      = ($current - 1) * $per_page;
        $page_items  = array_slice($items, $offset, $per_page, true);

        return [
            'items'        => $page_items,
            'total'        => $total,
            'total_pages'  => $total_pages,
            'current_page' => $current,
            'per_page'     => $per_page,
            'offset'       => $offset,
        ];
    }

    /**
     * Build action links for plugin row
     */
    private function build_action_links($plugin_file, array $plugin) {
        $base  = admin_url('admin.php?page=anmi-plugins');
        $nonce = wp_create_nonce('anmi_pm_action_' . $plugin_file);

        $links = [];

        if ($plugin['managed']) {
            $links['unmark'] = add_query_arg([
                'action'   => 'unmark',
                'plugin'   => $plugin_file,
                '_wpnonce' => $nonce,
            ], $base);
        } else {
            $links['mark'] = add_query_arg([
                'action'   => 'mark',
                'plugin'   => $plugin_file,
                '_wpnonce' => $nonce,
            ], $base);
        }

        if ($plugin['is_active']) {
            $links['deactivate'] = add_query_arg([
                'action'   => 'deactivate',
                'plugin'   => $plugin_file,
                '_wpnonce' => $nonce,
            ], $base);
        } else {
            $links['activate'] = add_query_arg([
                'action'   => 'activate',
                'plugin'   => $plugin_file,
                '_wpnonce' => $nonce,
            ], $base);
            $links['delete'] = add_query_arg([
                'action'   => 'delete',
                'plugin'   => $plugin_file,
                '_wpnonce' => $nonce,
            ], $base);
        }

        $links['history'] = add_query_arg([
            'page'    => 'anmi-plugins-logs',
            'plugin'  => $plugin_file,
        ], admin_url('admin.php'));

        return $links;
    }

    /**
     * Handle all row and toolbar actions
     */
    private function handle_actions() {
        if (!isset($_GET['action'])) {
            return false;
        }

        $action = sanitize_text_field($_GET['action']);

        if ($action === 'resync') {
            $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field($_GET['_wpnonce']) : '';
            if (!wp_verify_nonce($nonce, 'anmi_pm_resync')) {
                wp_die(__('Nonce verification failed', 'anmi-plugin-manager'));
            }

            if (!current_user_can('manage_options')) {
                wp_die(__('Unauthorized', 'anmi-plugin-manager'));
            }

            if (Anmi_PM_Settings::is_kill_switch_enabled()) {
                $this->redirect_with_message('action_blocked');
            }

            $result = Anmi_PM_Rename_Detector::resync_all();

            if ($result['renames_found'] > 0) {
                $this->redirect_with_message('resync_success', $result['renames_found'] . ' plugins resynced');
            }

            $this->redirect_with_message('resync_none', '');
        }

        if (!isset($_GET['plugin'])) {
            return false;
        }

        $plugin_file = sanitize_text_field($_GET['plugin']);
        $nonce       = isset($_GET['_wpnonce']) ? sanitize_text_field($_GET['_wpnonce']) : '';

        if (!wp_verify_nonce($nonce, 'anmi_pm_action_' . $plugin_file)) {
            wp_die(__('Nonce verification failed', 'anmi-plugin-manager'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'anmi-plugin-manager'));
        }

        if (Anmi_PM_Settings::is_kill_switch_enabled()) {
            $this->redirect_with_message('action_blocked');
        }

        $logger = new Anmi_PM_Logger();

        switch ($action) {
            case 'mark':
                $this->mark_plugin($plugin_file, true);
                $logger->log('mark_managed', [
                    'plugin_file' => $plugin_file,
                    'managed'     => true,
                ]);
                $this->redirect_with_message('marked');
                break;

            case 'unmark':
                $this->mark_plugin($plugin_file, false);
                $logger->log('unmark_managed', [
                    'plugin_file' => $plugin_file,
                    'managed'     => false,
                ]);
                $this->redirect_with_message('unmarked');
                break;

            case 'activate':
                $result = Anmi_PM_Plugin_Activator::safe_activate($plugin_file);
                if ($result['success']) {
                    $this->redirect_with_message('activated');
                }
                $this->redirect_with_message('activate_failed', $result['message']);
                break;

            case 'deactivate':
                Anmi_PM_Plugin_Activator::safe_deactivate($plugin_file);
                $this->redirect_with_message('deactivated');
                break;

            case 'delete':
                $result = Anmi_PM_Plugin_Activator::safe_delete($plugin_file);
                if ($result['success']) {
                    $this->redirect_with_message('deleted');
                }
                $this->redirect_with_message('delete_failed', $result['message']);
                break;
        }

        return true;
    }

    /**
     * Mark plugin as managed/unmanaged
     */
    private function mark_plugin($plugin_file, $managed) {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
        $plugin_data = file_exists($plugin_path) ? get_plugin_data($plugin_path) : [];

        $plugin_dir = dirname($plugin_file);
        if ($plugin_dir === '.') {
            $plugin_dir = basename($plugin_file, '.php');
        }

        $checksum = file_exists($plugin_path) ? sha1_file($plugin_path) : '';

        Anmi_PM_Metadata_Manager::save_plugin_meta([
            'plugin_file'   => $plugin_file,
            'plugin_dir'    => $plugin_dir,
            'name'          => $plugin_data['Name'] ?? basename($plugin_file),
            'version'       => $plugin_data['Version'] ?? '',
            'author'        => $plugin_data['Author'] ?? '',
            'checksum'      => $checksum,
            'installed_date'=> current_time('mysql'),
            'active_status' => is_plugin_active($plugin_file) ? '1' : '0',
            'managed'       => $managed,
        ]);
    }

    /**
     * Redirect helper
     */
    private function redirect_with_message($message, $extra = '') {
        $args = ['page' => 'anmi-plugins', 'message' => $message];
        if (!empty($extra)) {
            $args['extra'] = rawurlencode($extra);
        }

        wp_safe_redirect(add_query_arg($args, admin_url('admin.php')));
        exit;
    }

    /**
     * Collect admin notices for display
     */
    private function collect_messages() {
        $messages = [];

        if (isset($_GET['upload']) && $_GET['upload'] === 'success') {
            $plugin = isset($_GET['plugin']) ? sanitize_text_field($_GET['plugin']) : '';
            $messages[] = [
                'type' => 'success',
                'text' => sprintf(__('Plugin uploaded successfully: %s', 'anmi-plugin-manager'), $plugin),
            ];
        }

        if (!isset($_GET['message'])) {
            return $messages;
        }

        $message = sanitize_text_field($_GET['message']);
        $extra   = isset($_GET['extra']) ? sanitize_text_field($_GET['extra']) : '';

        $map = [
            'marked'         => ['success', __('Plugin has been marked as managed.', 'anmi-plugin-manager')],
            'unmarked'       => ['success', __('Plugin has been unmarked.', 'anmi-plugin-manager')],
            'activated'      => ['success', __('Plugin activated safely.', 'anmi-plugin-manager')],
            'activate_failed'=> ['error', sprintf(__('Failed to activate plugin%s', 'anmi-plugin-manager'), $extra ? ': ' . $extra : '')],
            'deactivated'    => ['success', __('Plugin deactivated.', 'anmi-plugin-manager')],
            'deleted'        => ['success', __('Plugin deleted and backup created.', 'anmi-plugin-manager')],
            'delete_failed'  => ['error', sprintf(__('Failed to delete plugin%s', 'anmi-plugin-manager'), $extra ? ': ' . $extra : '')],
            'resync_success' => ['success', sprintf(__('Resync completed%s', 'anmi-plugin-manager'), $extra ? ': ' . $extra : '')],
            'resync_none'    => ['info', __('No renamed plugins detected.', 'anmi-plugin-manager')],
            'action_blocked' => ['warning', __('Kill-switch is active. Actions are temporarily blocked.', 'anmi-plugin-manager')],
        ];

        if (isset($map[$message])) {
            $messages[] = [
                'type' => $map[$message][0],
                'text' => $map[$message][1],
            ];
        }

        return $messages;
    }

    /**
     * Resync URL helper
     */
    private function get_resync_url() {
        return wp_nonce_url(
            add_query_arg([
                'page'   => 'anmi-plugins',
                'action' => 'resync',
            ], admin_url('admin.php')),
            'anmi_pm_resync'
        );
    }
}
