<?php
/**
 * AN MI BANNER VIDEO PRO - ADMIN PANEL
 * Version: 2.3.0
 * CRUD Interface for managing video banners
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class AnMi_Banner_Video_Pro_Admin {

    /**
     * Singleton instance holder.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Database table name including the WordPress prefix.
     *
     * @var string
     */
    private $db_table_name;

    /**
     * Format definitions for banner database columns.
     */
    private const DB_DATA_FORMAT = array(
        '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d'
    );

    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        global $wpdb;
        $this->db_table_name = $wpdb->prefix . ABVP_TABLE_NAME;
        
        // Admin menu
        add_action('admin_menu', array($this, 'register_admin_menu'));
        
        // Admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'load_admin_assets'), 999);
        
        // Dequeue problematic scripts early
        add_action('admin_init', array($this, 'remove_conflicting_scripts'), 9999);
        
        // AJAX handlers
        add_action('wp_ajax_abvp_save_banner', array($this, 'handle_save_banner'));
        add_action('wp_ajax_abvp_delete_banner', array($this, 'handle_delete_banner'));
        add_action('wp_ajax_abvp_get_banner', array($this, 'handle_get_banner'));
        add_action('wp_ajax_abvp_get_banner_preview', array($this, 'handle_get_banner_preview'));
    }
    
    /**
     * Remove conflicting scripts to prevent conflicts
     */
    public function remove_conflicting_scripts(): void {
        $screen = get_current_screen();
        
        if ($screen && strpos($screen->id, 'anmi-video-banner') !== false) {
            wp_dequeue_script('webfontloader');
            wp_deregister_script('webfontloader');
        }
    }
    
    /**
     * Create database table for banners
     */
    public function setup_database_table(): void {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->db_table_name} (
            banner_id bigint(20) NOT NULL AUTO_INCREMENT,
            banner_name varchar(255) NOT NULL,
            video_url_value text NOT NULL,
            video_input_type varchar(50) DEFAULT 'url',
            image_list text NOT NULL,
            banner_title varchar(255) DEFAULT '',
            banner_subtitle text DEFAULT '',
            cta_button_text varchar(100) DEFAULT '',
            cta_button_link varchar(255) DEFAULT '',
            display_title tinyint(1) DEFAULT 0,
            display_subtitle tinyint(1) DEFAULT 0,
            display_button tinyint(1) DEFAULT 0,
            banner_height varchar(50) DEFAULT '600px',
            transition_effect varchar(50) DEFAULT 'fade',
            image_slider_speed int(11) DEFAULT 3000,
            image_slider_effect varchar(50) DEFAULT 'fade',
            video_autoplay_delay int(11) DEFAULT 0,
            mobile_display_mode varchar(50) DEFAULT 'video',
            banner_status varchar(20) DEFAULT 'active',
            enable_autoplay tinyint(1) DEFAULT 1,
            enable_muted tinyint(1) DEFAULT 1,
            enable_loop tinyint(1) DEFAULT 1,
            enable_controls tinyint(1) DEFAULT 1,
            enable_slider tinyint(1) DEFAULT 1,
            enable_slider_desktop tinyint(1) DEFAULT 1,
            enable_slider_mobile tinyint(1) DEFAULT 1,
            enable_modestbranding tinyint(1) DEFAULT 1,
            enable_rel tinyint(1) DEFAULT 0,
            created_date datetime DEFAULT CURRENT_TIMESTAMP,
            modified_date datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (banner_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        error_log('AnMi Banner Video Pro: Database table created/updated - ' . $this->db_table_name);
    }
    
    /**
     * Add admin menu
     */
    public function register_admin_menu(): void {
        add_menu_page(
            'An Mi Banner Video Pro',
            'Banner Video Pro',
            'manage_options',
            'anmi-video-banner',
            array($this, 'display_admin_list_page'),
            'dashicons-video-alt3',
            31
        );
        
        add_submenu_page(
            'anmi-video-banner',
            'All Banners',
            'All Banners',
            'manage_options',
            'anmi-video-banner',
            array($this, 'display_admin_list_page')
        );
        
        add_submenu_page(
            'anmi-video-banner',
            'Add New Banner',
            'Add New',
            'manage_options',
            'anmi-video-banner-new',
            array($this, 'display_admin_edit_page')
        );
        
        add_submenu_page(
            null, // Hidden from menu
            'Edit Banner',
            'Edit Banner',
            'manage_options',
            'anmi-video-banner-edit',
            array($this, 'display_admin_edit_page')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function load_admin_assets(string $hook): void {
        // Only load on our admin pages
        if (strpos($hook, 'anmi-video-banner') === false) {
            return;
        }
        
        // Dequeue problematic webfontloader
        wp_dequeue_script('webfontloader');
        wp_deregister_script('webfontloader');
        
        // WordPress Media Library
        wp_enqueue_media();
        
        // Admin CSS
        wp_enqueue_style(
            'abvp-admin-style',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin-style.css',
            array(),
            ABVP_VERSION
        );
        
        $shared_style_url = plugin_dir_url(dirname(__FILE__)) . 'assets/css/video-banner.css';
        $shared_script_url = plugin_dir_url(dirname(__FILE__)) . 'assets/js/video-banner.js';

        // Frontend CSS for preview
        wp_register_style(
            'anmi-video-banner-style',
            $shared_style_url,
            array(),
            ABVP_VERSION
        );

        // Backward compatible alias
        wp_register_style(
            'abvp-banner-style',
            $shared_style_url,
            array(),
            ABVP_VERSION
        );

        wp_enqueue_style('anmi-video-banner-style');
        
        // Admin JS
        wp_enqueue_script(
            'abvp-admin-script',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/admin-script.js',
            array('jquery', 'jquery-ui-sortable'),
            ABVP_VERSION,
            true
        );
        
        // Frontend JS for preview
        wp_register_script(
            'anmi-video-banner-script',
            $shared_script_url,
            array('jquery'),
            ABVP_VERSION,
            true
        );

        // Backward compatible alias
        wp_register_script(
            'abvp-banner-script',
            $shared_script_url,
            array('jquery'),
            ABVP_VERSION,
            true
        );

        wp_enqueue_script('anmi-video-banner-script');
        
        // Localize script
        wp_localize_script('abvp-admin-script', 'abvpBannerAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('abvp_banner_nonce')
        ));
    }

    /**
     * Build the whitelist of allowed iframe attributes for sanitizing embeds.
     *
     * @return array<string, array<string, bool>>
     */
    private function get_allowed_iframe_tags(): array {
        return array(
            'iframe' => array(
                'src' => true,
                'width' => true,
                'height' => true,
                'title' => true,
                'frameborder' => true,
                'allow' => true,
                'allowfullscreen' => true,
                'referrerpolicy' => true,
                'loading' => true,
                'style' => true,
                'class' => true,
            ),
        );
    }

    /**
     * Sanitize and normalize banner payload coming from $_POST.
     *
     * @param array $request Raw request data.
     *
     * @throws \InvalidArgumentException When image data is invalid.
     */
    private function sanitize_banner_payload(array $request): array {
        $video_input_type = sanitize_text_field(wp_unslash($request['video_type'] ?? 'url'));

        return array(
            'banner_name' => sanitize_text_field(wp_unslash($request['name'] ?? '')),
            'video_url_value' => $this->sanitize_video_value($video_input_type, $request),
            'video_input_type' => $video_input_type,
            'image_list' => $this->sanitize_image_list($request),
            'banner_title' => sanitize_text_field(wp_unslash($request['title'] ?? '')),
            'banner_subtitle' => sanitize_textarea_field(wp_unslash($request['subtitle'] ?? '')),
            'cta_button_text' => sanitize_text_field(wp_unslash($request['button_text'] ?? '')),
            'cta_button_link' => esc_url_raw(wp_unslash($request['button_link'] ?? '')),
            'display_title' => $this->sanitize_checkbox($request, 'show_title'),
            'display_subtitle' => $this->sanitize_checkbox($request, 'show_subtitle'),
            'display_button' => $this->sanitize_checkbox($request, 'show_button'),
            'banner_height' => sanitize_text_field(wp_unslash($request['height'] ?? '600px')),
            'transition_effect' => sanitize_text_field(wp_unslash($request['transition'] ?? 'fade')),
            'image_slider_speed' => isset($request['slider_speed']) ? intval($request['slider_speed']) : 3000,
            'image_slider_effect' => sanitize_text_field(wp_unslash($request['slider_effect'] ?? 'fade')),
            'video_autoplay_delay' => isset($request['autoplay_delay']) ? intval($request['autoplay_delay']) : 0,
            'mobile_display_mode' => sanitize_text_field(wp_unslash($request['mobile_behavior'] ?? 'video')),
            'banner_status' => sanitize_text_field(wp_unslash($request['status'] ?? 'active')),
            'enable_autoplay' => $this->sanitize_checkbox($request, 'video_autoplay', 1),
            'enable_muted' => $this->sanitize_checkbox($request, 'video_muted', 1),
            'enable_loop' => $this->sanitize_checkbox($request, 'video_loop', 1),
            'enable_controls' => $this->sanitize_checkbox($request, 'video_controls', 1),
            'enable_slider' => $this->sanitize_checkbox($request, 'enable_slider', 1),
            'enable_slider_desktop' => $this->sanitize_checkbox($request, 'enable_slider_desktop', 1),
            'enable_slider_mobile' => $this->sanitize_checkbox($request, 'enable_slider_mobile', 1),
            'enable_modestbranding' => $this->sanitize_checkbox($request, 'video_modestbranding', 1),
            'enable_rel' => $this->sanitize_checkbox($request, 'video_rel', 0),
        );
    }

    /**
     * Normalize checkbox style inputs to integer flags.
     */
    private function sanitize_checkbox(array $request, string $key, int $default = 0): int {
        if (!isset($request[$key])) {
            return $default;
        }

        $value = $request[$key];
        if (is_array($value)) {
            $value = reset($value);
        }

        $value = strtolower(trim((string) wp_unslash($value)));

        if ($value === '0' || $value === '' || $value === 'off' || $value === 'false') {
            return 0;
        }

        return 1;
    }

    /**
     * Sanitize embedded or direct video URL values.
     */
    private function sanitize_video_value(string $video_input_type, array $request): string {
        $raw_value = wp_unslash($request['video_url'] ?? '');

        if ($video_input_type === 'embed') {
            return wp_kses($raw_value, $this->get_allowed_iframe_tags());
        }

        return esc_url_raw($raw_value);
    }

    /**
     * Sanitize the image list JSON payload from the request.
     *
     * @throws \InvalidArgumentException When JSON parsing fails.
     */
    private function sanitize_image_list(array $request): string {
        $image_list_raw = isset($request['images']) ? wp_unslash($request['images']) : '[]';
        $decoded = json_decode($image_list_raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            throw new \InvalidArgumentException('Invalid images format. Please re-upload images.');
        }

        $clean_images = array();
        foreach ($decoded as $image_url) {
            $url = esc_url_raw(is_string($image_url) ? $image_url : '');
            if (!empty($url)) {
                $clean_images[] = $url;
            }
        }

        return wp_json_encode($clean_images);
    }

    /**
     * Insert a new banner record.
     */
    private function insert_banner_record(array $data): void {
        global $wpdb;
        
        // Ensure database schema is up to date before insert
        $this->ensure_columns_exist();

        $result = $wpdb->insert(
            $this->db_table_name,
            $data,
            self::DB_DATA_FORMAT
        );

        if ($result) {
            wp_send_json_success(array(
                'message' => 'Banner created successfully!',
                'banner_id' => $wpdb->insert_id,
            ));
        }

        wp_send_json_error('Database insert failed: ' . $wpdb->last_error);
    }
    
    /**
     * Ensure required columns exist in database
     */
    private function ensure_columns_exist(): void {
        global $wpdb;
        
        // Get current columns
        $columns = $wpdb->get_results("SHOW COLUMNS FROM {$this->db_table_name}");
        $column_names = array_column($columns, 'Field');
        
        $missing_columns = [];
        
        // Check for new columns
        if (!in_array('enable_slider', $column_names)) {
            $missing_columns[] = 'enable_slider';
        }
        if (!in_array('enable_slider_desktop', $column_names)) {
            $missing_columns[] = 'enable_slider_desktop';
        }
        if (!in_array('enable_slider_mobile', $column_names)) {
            $missing_columns[] = 'enable_slider_mobile';
        }
        
        // If columns missing, run migration
        if (!empty($missing_columns)) {
            error_log('AnMi Banner Video Pro: Missing columns detected: ' . implode(', ', $missing_columns));
            error_log('AnMi Banner Video Pro: Running emergency migration...');
            $this->setup_database_table();
        }
    }

    /**
     * Update an existing banner record.
     */
    private function update_banner_record(int $banner_id, array $data): void {
        global $wpdb;
        
        // Ensure database schema is up to date before update
        $this->ensure_columns_exist();

        $result = $wpdb->update(
            $this->db_table_name,
            $data,
            array('banner_id' => $banner_id),
            self::DB_DATA_FORMAT,
            array('%d')
        );

        if ($result !== false) {
            wp_send_json_success(array(
                'message' => 'Banner updated successfully!',
                'banner_id' => $banner_id,
            ));
        }

        wp_send_json_error('Database update failed: ' . $wpdb->last_error);
    }

    /**
     * Sanitize stored image list for preview responses.
     */
    private function sanitize_preview_images(?string $image_list): array {
        if (empty($image_list)) {
            return array();
        }

        $decoded = json_decode($image_list, true);
        if (!is_array($decoded)) {
            return array();
        }

        $clean_images = array();
        foreach ($decoded as $image_url) {
            $url = esc_url_raw(is_string($image_url) ? $image_url : '');
            if (!empty($url)) {
                $clean_images[] = $url;
            }
        }

        return $clean_images;
    }
    
    /**
     * Render main admin page (list of banners)
     */
    public function display_admin_list_page(): void {
        global $wpdb;
        
        // Get all banners
        $banners = $wpdb->get_results("SELECT * FROM {$this->db_table_name} ORDER BY created_date DESC");
        
        include plugin_dir_path(dirname(__FILE__)) . 'includes/views/admin-list.php';
    }
    
    /**
     * Render edit/add banner page
     */
    public function display_admin_edit_page(): void {
        global $wpdb;
        
        $banner_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $banner = null;
        
        if ($banner_id > 0) {
            $banner = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$this->db_table_name} WHERE banner_id = %d",
                $banner_id
            ));
        }
        
        include plugin_dir_path(dirname(__FILE__)) . 'includes/views/admin-edit.php';
    }
    
    /**
     * AJAX: Save banner
     */
    public function handle_save_banner(): void {
        check_ajax_referer('abvp_banner_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }
        
        error_log('=== ABVP SAVE BANNER DEBUG ===');
        error_log('POST data: ' . print_r($_POST, true));
        
        $banner_id = isset($_POST['banner_id']) ? intval($_POST['banner_id']) : 0;

        try {
            $data = $this->sanitize_banner_payload($_POST);
        } catch (\InvalidArgumentException $exception) {
            wp_send_json_error($exception->getMessage());
            return;
        }

        if ($banner_id > 0) {
            $this->update_banner_record($banner_id, $data);
        } else {
            $this->insert_banner_record($data);
        }
        
        wp_send_json_error('Failed to save banner - unknown error');
    }
    
    /**
     * AJAX: Delete banner
     */
    public function handle_delete_banner(): void {
        check_ajax_referer('abvp_banner_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }
        
        global $wpdb;
        
        $banner_id = intval($_POST['banner_id']);
        
        $result = $wpdb->delete(
            $this->db_table_name,
            array('banner_id' => $banner_id),
            array('%d')
        );
        
        if ($result) {
            wp_send_json_success('Banner deleted successfully!');
        }
        
        wp_send_json_error('Failed to delete banner');
    }
    
    /**
     * AJAX: Get banner data
     */
    public function handle_get_banner(): void {
        check_ajax_referer('abvp_banner_nonce', 'nonce');
        
        global $wpdb;
        
        $banner_id = intval($_POST['banner_id']);
        
        $banner = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->db_table_name} WHERE banner_id = %d",
            $banner_id
        ));
        
        if ($banner) {
            wp_send_json_success($banner);
        }
        
        wp_send_json_error('Banner not found');
    }
    
    /**
     * Get all banners (for Elementor widget)
     */
    public static function fetch_all_active_banners(): array {
        global $wpdb;
        $table_name = $wpdb->prefix . ABVP_TABLE_NAME;
        
        return $wpdb->get_results("SELECT banner_id, banner_name FROM {$table_name} WHERE banner_status = 'active' ORDER BY banner_name ASC");
    }
    
    /**
     * Get banner by ID
     */
    public static function fetch_banner_by_id(int $banner_id): ?object {
        global $wpdb;
        $table_name = $wpdb->prefix . ABVP_TABLE_NAME;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE banner_id = %d",
            $banner_id
        ));
    }
    
    /**
     * AJAX handler for preview modal
     */
    public function handle_get_banner_preview(): void {
        error_log('ABVP Banner Preview: AJAX called');
        
        try {
            if (!check_ajax_referer('abvp_banner_nonce', 'nonce', false)) {
                error_log('ABVP Banner Preview: Nonce check failed');
                wp_send_json_error('Invalid security token');
                return;
            }
            
            $banner_id = isset($_POST['banner_id']) ? intval($_POST['banner_id']) : 0;
            
            if (!$banner_id) {
                wp_send_json_error('Invalid banner ID');
                return;
            }
            
            $banner = self::fetch_banner_by_id($banner_id);
            
            if (!$banner) {
                wp_send_json_error('Banner not found');
                return;
            }
            
            $sanitized_video_value = $banner->video_input_type === 'embed'
                ? wp_kses($banner->video_url_value, $this->get_allowed_iframe_tags())
                : esc_url_raw($banner->video_url_value);

            $decoded_images = $this->sanitize_preview_images($banner->image_list);

            $preview_payload = array(
                'banner_id' => (int) $banner->banner_id,
                'banner_name' => sanitize_text_field($banner->banner_name ?? ''),
                'banner_title' => sanitize_text_field($banner->banner_title ?? ''),
                'banner_subtitle' => sanitize_textarea_field($banner->banner_subtitle ?? ''),
                'banner_status' => sanitize_key($banner->banner_status ?? 'inactive'),
                'banner_height' => sanitize_text_field($banner->banner_height ?? ''),
                'video_url_value' => $sanitized_video_value,
                'video_input_type' => sanitize_text_field($banner->video_input_type ?? ''),
                'video_autoplay' => isset($banner->enable_autoplay) ? (int) $banner->enable_autoplay : 1,
                'video_muted' => isset($banner->enable_muted) ? (int) $banner->enable_muted : 1,
                'video_loop' => isset($banner->enable_loop) ? (int) $banner->enable_loop : 1,
                'video_controls' => isset($banner->enable_controls) ? (int) $banner->enable_controls : 1,
                'video_modestbranding' => isset($banner->enable_modestbranding) ? (int) $banner->enable_modestbranding : 1,
                'video_rel' => isset($banner->enable_rel) ? (int) $banner->enable_rel : 0,
                'video_autoplay_delay' => isset($banner->video_autoplay_delay) ? (int) $banner->video_autoplay_delay : 3000,
                'image_list' => wp_json_encode($decoded_images),
                'image_slider_speed' => isset($banner->image_slider_speed) ? (int) $banner->image_slider_speed : 5000,
                'image_slider_effect' => sanitize_key($banner->image_slider_effect ?? 'fade'),
                'transition_effect' => sanitize_key($banner->transition_effect ?? 'fade'),
                'mobile_display_mode' => sanitize_key($banner->mobile_display_mode ?? 'both'),
                'cta_button_text' => sanitize_text_field($banner->cta_button_text ?? ''),
                'cta_button_link' => esc_url_raw($banner->cta_button_link ?? ''),
                'display_title' => isset($banner->display_title) ? (int) $banner->display_title : 0,
                'display_subtitle' => isset($banner->display_subtitle) ? (int) $banner->display_subtitle : 0,
                'display_button' => isset($banner->display_button) ? (int) $banner->display_button : 0,
                'created_date' => $banner->created_date,
                'modified_date' => $banner->modified_date,
            );

            $plugin = AnMi_Banner_Video_Pro::get_instance();
            $preview_payload['render_html'] = $plugin->render_video_banner(array(
                'id' => (string) $banner_id,
            ));

            wp_send_json_success($preview_payload);
        } catch (Exception $e) {
            error_log('ABVP Banner Preview: Exception - ' . $e->getMessage());
            wp_send_json_error('Server error: ' . $e->getMessage());
        }
    }
}
