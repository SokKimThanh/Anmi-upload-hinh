<?php
/**
 * AN MI BANNER VIDEO PRO - ADMIN PANEL
 * Version: 2.1.0
 * CRUD Interface for managing video banners
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class AnMi_Banner_Video_Pro_Admin {
    
    private static $instance = null;
    private $db_table_name;
    
    public static function get_instance() {
        if (self::$instance == null) {
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
    public function remove_conflicting_scripts() {
        $screen = get_current_screen();
        
        if ($screen && strpos($screen->id, 'abvp-banner-video-pro') !== false) {
            wp_dequeue_script('webfontloader');
            wp_deregister_script('webfontloader');
        }
    }
    
    /**
     * Create database table for banners
     */
    public function setup_database_table() {
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
    public function register_admin_menu() {
        add_menu_page(
            'An Mi Banner Video Pro',
            'Banner Video Pro',
            'manage_options',
            'abvp-banner-video-pro',
            array($this, 'display_admin_list_page'),
            'dashicons-video-alt3',
            31
        );
        
        add_submenu_page(
            'abvp-banner-video-pro',
            'All Banners',
            'All Banners',
            'manage_options',
            'abvp-banner-video-pro',
            array($this, 'display_admin_list_page')
        );
        
        add_submenu_page(
            'abvp-banner-video-pro',
            'Add New Banner',
            'Add New',
            'manage_options',
            'abvp-banner-video-pro-new',
            array($this, 'display_admin_edit_page')
        );
        
        add_submenu_page(
            null, // Hidden from menu
            'Edit Banner',
            'Edit Banner',
            'manage_options',
            'abvp-banner-video-pro-edit',
            array($this, 'display_admin_edit_page')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function load_admin_assets($hook) {
        // Only load on our admin pages
        if (strpos($hook, 'abvp-banner-video-pro') === false) {
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
        
        // Frontend CSS for preview
        wp_enqueue_style(
            'abvp-banner-style',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/video-banner.css',
            array(),
            ABVP_VERSION
        );
        
        // Admin JS
        wp_enqueue_script(
            'abvp-admin-script',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/admin-script.js',
            array('jquery', 'jquery-ui-sortable'),
            ABVP_VERSION,
            true
        );
        
        // Frontend JS for preview
        wp_enqueue_script(
            'abvp-banner-script',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/video-banner.js',
            array('jquery'),
            ABVP_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('abvp-admin-script', 'abvpBannerAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('abvp_banner_nonce')
        ));
    }
    
    /**
     * Render main admin page (list of banners)
     */
    public function display_admin_list_page() {
        global $wpdb;
        
        // Get all banners
        $banners = $wpdb->get_results("SELECT * FROM {$this->db_table_name} ORDER BY created_date DESC");
        
        include plugin_dir_path(dirname(__FILE__)) . 'includes/views/admin-list.php';
    }
    
    /**
     * Render edit/add banner page
     */
    public function display_admin_edit_page() {
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
    public function handle_save_banner() {
        check_ajax_referer('abvp_banner_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }
        
        global $wpdb;
        
        error_log('=== ABVP SAVE BANNER DEBUG ===');
        error_log('POST data: ' . print_r($_POST, true));
        
        $banner_id = isset($_POST['banner_id']) ? intval($_POST['banner_id']) : 0;
        $banner_name = sanitize_text_field($_POST['name']);
        $video_url_value = esc_url_raw($_POST['video_url']);
        $video_input_type = sanitize_text_field($_POST['video_type']);
        
        // Validate and sanitize images JSON
        $image_list = isset($_POST['images']) ? wp_unslash($_POST['images']) : '[]';
        $images_test = json_decode($image_list);
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error('Invalid images format. Please re-upload images.');
            return;
        }
        
        $banner_title = sanitize_text_field($_POST['title']);
        $banner_subtitle = sanitize_textarea_field($_POST['subtitle']);
        $cta_button_text = sanitize_text_field($_POST['button_text']);
        $cta_button_link = esc_url_raw($_POST['button_link']);
        $display_title = isset($_POST['show_title']) ? 1 : 0;
        $display_subtitle = isset($_POST['show_subtitle']) ? 1 : 0;
        $display_button = isset($_POST['show_button']) ? 1 : 0;
        $banner_height = sanitize_text_field($_POST['height']);
        $transition_effect = sanitize_text_field($_POST['transition']);
        $image_slider_speed = intval($_POST['slider_speed']);
        $image_slider_effect = sanitize_text_field($_POST['slider_effect']);
        $video_autoplay_delay = intval($_POST['autoplay_delay']);
        $mobile_display_mode = sanitize_text_field($_POST['mobile_behavior']);
        $banner_status = sanitize_text_field($_POST['status']);
        
        // Video playback settings
        $enable_autoplay = isset($_POST['video_autoplay']) ? intval($_POST['video_autoplay']) : 1;
        $enable_muted = isset($_POST['video_muted']) ? intval($_POST['video_muted']) : 1;
        $enable_loop = isset($_POST['video_loop']) ? intval($_POST['video_loop']) : 1;
        $enable_controls = isset($_POST['video_controls']) ? intval($_POST['video_controls']) : 1;
        $enable_modestbranding = isset($_POST['video_modestbranding']) ? intval($_POST['video_modestbranding']) : 1;
        $enable_rel = isset($_POST['video_rel']) ? intval($_POST['video_rel']) : 0;
        
        $data = array(
            'banner_name' => $banner_name,
            'video_url_value' => $video_url_value,
            'video_input_type' => $video_input_type,
            'image_list' => $image_list,
            'banner_title' => $banner_title,
            'banner_subtitle' => $banner_subtitle,
            'cta_button_text' => $cta_button_text,
            'cta_button_link' => $cta_button_link,
            'display_title' => $display_title,
            'display_subtitle' => $display_subtitle,
            'display_button' => $display_button,
            'banner_height' => $banner_height,
            'transition_effect' => $transition_effect,
            'image_slider_speed' => $image_slider_speed,
            'image_slider_effect' => $image_slider_effect,
            'video_autoplay_delay' => $video_autoplay_delay,
            'mobile_display_mode' => $mobile_display_mode,
            'banner_status' => $banner_status,
            'enable_autoplay' => $enable_autoplay,
            'enable_muted' => $enable_muted,
            'enable_loop' => $enable_loop,
            'enable_controls' => $enable_controls,
            'enable_modestbranding' => $enable_modestbranding,
            'enable_rel' => $enable_rel
        );
        
        if ($banner_id > 0) {
            // Update existing banner
            $result = $wpdb->update(
                $this->db_table_name,
                $data,
                array('banner_id' => $banner_id),
                array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d'),
                array('%d')
            );
            
            if ($result !== false) {
                wp_send_json_success(array(
                    'message' => 'Banner updated successfully!',
                    'banner_id' => $banner_id
                ));
            } else {
                wp_send_json_error('Database update failed: ' . $wpdb->last_error);
            }
        } else {
            // Insert new banner
            $result = $wpdb->insert(
                $this->db_table_name,
                $data,
                array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d')
            );
            
            if ($result) {
                wp_send_json_success(array(
                    'message' => 'Banner created successfully!',
                    'banner_id' => $wpdb->insert_id
                ));
            } else {
                wp_send_json_error('Database insert failed: ' . $wpdb->last_error);
            }
        }
        
        wp_send_json_error('Failed to save banner - unknown error');
    }
    
    /**
     * AJAX: Delete banner
     */
    public function handle_delete_banner() {
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
    public function handle_get_banner() {
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
    public static function fetch_all_active_banners() {
        global $wpdb;
        $table_name = $wpdb->prefix . ABVP_TABLE_NAME;
        
        return $wpdb->get_results("SELECT banner_id, banner_name FROM {$table_name} WHERE banner_status = 'active' ORDER BY banner_name ASC");
    }
    
    /**
     * Get banner by ID
     */
    public static function fetch_banner_by_id($banner_id) {
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
    public function handle_get_banner_preview() {
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
            
            // Return banner data
            wp_send_json_success(array(
                'id' => $banner->banner_id,
                'name' => $banner->banner_name,
                'video_url' => $banner->video_url_value,
                'images' => $banner->image_list,
                'title' => $banner->banner_title,
                'subtitle' => $banner->banner_subtitle,
                'button_text' => $banner->cta_button_text,
                'button_link' => $banner->cta_button_link,
                'show_title' => $banner->display_title,
                'show_subtitle' => $banner->display_subtitle,
                'show_button' => $banner->display_button,
                'height' => $banner->banner_height,
                'transition' => $banner->transition_effect,
                'slider_speed' => $banner->image_slider_speed,
                'slider_effect' => $banner->image_slider_effect,
                'autoplay_delay' => $banner->video_autoplay_delay,
                'mobile_behavior' => $banner->mobile_display_mode
            ));
        } catch (Exception $e) {
            error_log('ABVP Banner Preview: Exception - ' . $e->getMessage());
            wp_send_json_error('Server error: ' . $e->getMessage());
        }
    }
}
