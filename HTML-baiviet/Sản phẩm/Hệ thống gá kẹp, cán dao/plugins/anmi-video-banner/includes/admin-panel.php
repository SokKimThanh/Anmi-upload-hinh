<?php
/**
 * AN MI VIDEO BANNER - ADMIN PANEL
 * Version: 1.6.6
 * CRUD Interface for managing video banners
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class AnMi_Video_Banner_Admin {
    
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'anmi_video_banners';
        
        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Admin scripts and styles (late priority to dequeue webfontloader)
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'), 999);
        
        // Dequeue problematic scripts early
        add_action('admin_init', array($this, 'dequeue_problematic_scripts'), 9999);
        
        // AJAX handlers
        add_action('wp_ajax_anmi_save_banner', array($this, 'ajax_save_banner'));
        add_action('wp_ajax_anmi_delete_banner', array($this, 'ajax_delete_banner'));
        add_action('wp_ajax_anmi_get_banner', array($this, 'ajax_get_banner'));
        add_action('wp_ajax_anmi_get_banner_preview', array($this, 'ajax_get_banner_preview'));
        
        // Create database table on activation
        register_activation_hook(ANMI_VIDEO_BANNER_FILE, array($this, 'create_database_table'));
    }
    
    /**
     * Dequeue problematic scripts early to prevent conflicts
     */
    public function dequeue_problematic_scripts() {
        // Get current screen
        $screen = get_current_screen();
        
        // Only on our pages
        if ($screen && strpos($screen->id, 'anmi-video-banner') !== false) {
            wp_dequeue_script('webfontloader');
            wp_deregister_script('webfontloader');
        }
    }
    
    /**
     * Create database table for banners
     */
    public function create_database_table() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            video_url text NOT NULL,
            video_type varchar(50) DEFAULT 'url',
            images text NOT NULL,
            title varchar(255) DEFAULT '',
            subtitle text DEFAULT '',
            button_text varchar(100) DEFAULT '',
            button_link varchar(255) DEFAULT '',
            show_title tinyint(1) DEFAULT 0,
            show_subtitle tinyint(1) DEFAULT 0,
            show_button tinyint(1) DEFAULT 0,
            height varchar(50) DEFAULT '600px',
            transition varchar(50) DEFAULT 'fade',
            slider_speed int(11) DEFAULT 3000,
            slider_effect varchar(50) DEFAULT 'fade',
            autoplay_delay int(11) DEFAULT 0,
            mobile_behavior varchar(50) DEFAULT 'video',
            status varchar(20) DEFAULT 'active',
            video_autoplay tinyint(1) DEFAULT 1,
            video_muted tinyint(1) DEFAULT 1,
            video_loop tinyint(1) DEFAULT 1,
            video_controls tinyint(1) DEFAULT 1,
            video_modestbranding tinyint(1) DEFAULT 1,
            video_rel tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Force add columns if they don't exist (for existing installations)
        $this->add_missing_columns();
    }
    
    /**
     * Add missing columns to existing table (v1.6.13 migration)
     */
    private function add_missing_columns() {
        global $wpdb;
        
        // Check and add video settings columns
        $columns_to_add = array(
            'video_autoplay' => "ALTER TABLE {$this->table_name} ADD COLUMN video_autoplay tinyint(1) DEFAULT 1 AFTER status",
            'video_muted' => "ALTER TABLE {$this->table_name} ADD COLUMN video_muted tinyint(1) DEFAULT 1 AFTER video_autoplay",
            'video_loop' => "ALTER TABLE {$this->table_name} ADD COLUMN video_loop tinyint(1) DEFAULT 1 AFTER video_muted",
            'video_controls' => "ALTER TABLE {$this->table_name} ADD COLUMN video_controls tinyint(1) DEFAULT 1 AFTER video_loop",
            'video_modestbranding' => "ALTER TABLE {$this->table_name} ADD COLUMN video_modestbranding tinyint(1) DEFAULT 1 AFTER video_controls",
            'video_rel' => "ALTER TABLE {$this->table_name} ADD COLUMN video_rel tinyint(1) DEFAULT 0 AFTER video_modestbranding"
        );
        
        foreach ($columns_to_add as $column => $sql) {
            // Check if column exists
            $column_exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = %s 
                    AND TABLE_NAME = %s 
                    AND COLUMN_NAME = %s",
                    DB_NAME,
                    $this->table_name,
                    $column
                )
            );
            
            // Add column if it doesn't exist
            if (!$column_exists) {
                $wpdb->query($sql);
                error_log("AnMi Video Banner: Added missing column '{$column}'");
            }
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'An Mi Video Banners',
            'Video Banners',
            'manage_options',
            'anmi-video-banners',
            array($this, 'render_admin_page'),
            'dashicons-video-alt3',
            30
        );
        
        add_submenu_page(
            'anmi-video-banners',
            'All Banners',
            'All Banners',
            'manage_options',
            'anmi-video-banners',
            array($this, 'render_admin_page')
        );
        
        add_submenu_page(
            'anmi-video-banners',
            'Add New Banner',
            'Add New',
            'manage_options',
            'anmi-video-banner-new',
            array($this, 'render_edit_page')
        );
        
        add_submenu_page(
            null, // Hidden from menu
            'Edit Banner',
            'Edit Banner',
            'manage_options',
            'anmi-video-banner-edit',
            array($this, 'render_edit_page')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our admin pages
        if (strpos($hook, 'anmi-video-banner') === false) {
            return;
        }
        
        // Dequeue problematic webfontloader to prevent JS errors
        // This script causes "d[b].on is not a function" error in admin
        wp_dequeue_script('webfontloader');
        wp_deregister_script('webfontloader');
        
        // WordPress Media Library
        wp_enqueue_media();
        
        // Admin CSS
        wp_enqueue_style(
            'anmi-banner-admin-css',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin-style.css',
            array(),
            '1.6.6'
        );
        
        // Frontend CSS for preview
        wp_enqueue_style(
            'anmi-video-banner-style',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/video-banner.css',
            array(),
            '1.6.6'
        );
        
        // Admin JS
        wp_enqueue_script(
            'anmi-banner-admin-js',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/admin-script.js',
            array('jquery', 'jquery-ui-sortable'),
            '1.6.6',
            true
        );
        
        // Frontend JS for preview
        wp_enqueue_script(
            'anmi-video-banner-script',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/video-banner.js',
            array('jquery'),
            '1.6.6',
            true
        );
        
        // Localize script
        wp_localize_script('anmi-banner-admin-js', 'anmiBannerAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('anmi_banner_nonce')
        ));
    }
    
    /**
     * Render main admin page (list of banners)
     */
    public function render_admin_page() {
        global $wpdb;
        
        // Get all banners
        $banners = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY created_at DESC");
        
        include plugin_dir_path(dirname(__FILE__)) . 'includes/views/admin-list.php';
    }
    
    /**
     * Render edit/add banner page
     */
    public function render_edit_page() {
        global $wpdb;
        
        $banner_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $banner = null;
        
        if ($banner_id > 0) {
            $banner = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE id = %d",
                $banner_id
            ));
        }
        
        include plugin_dir_path(dirname(__FILE__)) . 'includes/views/admin-edit.php';
    }
    
    /**
     * AJAX: Save banner
     */
    public function ajax_save_banner() {
        check_ajax_referer('anmi_banner_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }
        
        global $wpdb;
        
        // DEBUG: Log received data
        error_log('=== ANMI SAVE BANNER DEBUG ===');
        error_log('POST data: ' . print_r($_POST, true));
        
        $banner_id = isset($_POST['banner_id']) ? intval($_POST['banner_id']) : 0;
        $name = sanitize_text_field($_POST['name']);
        $video_url = esc_url_raw($_POST['video_url']);
        $video_type = sanitize_text_field($_POST['video_type']);
        
        // Validate and sanitize images JSON
        $images = isset($_POST['images']) ? wp_unslash($_POST['images']) : '[]';
        // Validate it's proper JSON
        $images_test = json_decode($images);
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error('Invalid images format. Please re-upload images.');
            return;
        }
        
        $title = sanitize_text_field($_POST['title']);
        $subtitle = sanitize_textarea_field($_POST['subtitle']);
        $button_text = sanitize_text_field($_POST['button_text']);
        $button_link = esc_url_raw($_POST['button_link']);
        $show_title = isset($_POST['show_title']) ? 1 : 0;
        $show_subtitle = isset($_POST['show_subtitle']) ? 1 : 0;
        $show_button = isset($_POST['show_button']) ? 1 : 0;
        $height = sanitize_text_field($_POST['height']);
        $transition = sanitize_text_field($_POST['transition']);
        $slider_speed = intval($_POST['slider_speed']);
        $slider_effect = sanitize_text_field($_POST['slider_effect']);
        $autoplay_delay = intval($_POST['autoplay_delay']);
        $mobile_behavior = sanitize_text_field($_POST['mobile_behavior']);
        $status = sanitize_text_field($_POST['status']);
        
        // Video playback settings (new)
        $video_autoplay = isset($_POST['video_autoplay']) ? intval($_POST['video_autoplay']) : 1;
        $video_muted = isset($_POST['video_muted']) ? intval($_POST['video_muted']) : 1;
        $video_loop = isset($_POST['video_loop']) ? intval($_POST['video_loop']) : 1;
        $video_controls = isset($_POST['video_controls']) ? intval($_POST['video_controls']) : 1;
        $video_modestbranding = isset($_POST['video_modestbranding']) ? intval($_POST['video_modestbranding']) : 1;
        $video_rel = isset($_POST['video_rel']) ? intval($_POST['video_rel']) : 0;
        
        $data = array(
            'name' => $name,
            'video_url' => $video_url,
            'video_type' => $video_type,
            'images' => $images,
            'title' => $title,
            'subtitle' => $subtitle,
            'button_text' => $button_text,
            'button_link' => $button_link,
            'show_title' => $show_title,
            'show_subtitle' => $show_subtitle,
            'show_button' => $show_button,
            'height' => $height,
            'transition' => $transition,
            'slider_speed' => $slider_speed,
            'slider_effect' => $slider_effect,
            'autoplay_delay' => $autoplay_delay,
            'mobile_behavior' => $mobile_behavior,
            'status' => $status,
            'video_autoplay' => $video_autoplay,
            'video_muted' => $video_muted,
            'video_loop' => $video_loop,
            'video_controls' => $video_controls,
            'video_modestbranding' => $video_modestbranding,
            'video_rel' => $video_rel
        );
        
        if ($banner_id > 0) {
            // Update existing banner
            error_log('Updating banner ID: ' . $banner_id);
            error_log('Data to update: ' . print_r($data, true));
            
            $result = $wpdb->update(
                $this->table_name,
                $data,
                array('id' => $banner_id),
                array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d'),
                array('%d')
            );
            
            error_log('Update result: ' . var_export($result, true));
            error_log('Last wpdb error: ' . $wpdb->last_error);
            
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
            error_log('Inserting new banner');
            error_log('Data to insert: ' . print_r($data, true));
            
            $result = $wpdb->insert(
                $this->table_name,
                $data,
                array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d')
            );
            
            error_log('Insert result: ' . var_export($result, true));
            error_log('Last wpdb error: ' . $wpdb->last_error);
            
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
    public function ajax_delete_banner() {
        check_ajax_referer('anmi_banner_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }
        
        global $wpdb;
        
        $banner_id = intval($_POST['banner_id']);
        
        $result = $wpdb->delete(
            $this->table_name,
            array('id' => $banner_id),
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
    public function ajax_get_banner() {
        check_ajax_referer('anmi_banner_nonce', 'nonce');
        
        global $wpdb;
        
        $banner_id = intval($_POST['banner_id']);
        
        $banner = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE id = %d",
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
    public static function get_all_banners() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'anmi_video_banners';
        
        return $wpdb->get_results("SELECT id, name FROM {$table_name} WHERE status = 'active' ORDER BY name ASC");
    }
    
    /**
     * Get banner by ID
     */
    public static function get_banner($banner_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'anmi_video_banners';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE id = %d",
            $banner_id
        ));
    }
    
    /**
     * AJAX handler for preview modal
     */
    public function ajax_get_banner_preview() {
        // Enable error logging
        error_log('ANMI Banner Preview: AJAX called');
        
        try {
            // Check nonce
            if (!check_ajax_referer('anmi_banner_nonce', 'nonce', false)) {
                error_log('ANMI Banner Preview: Nonce check failed');
                wp_send_json_error('Invalid security token');
                return;
            }
            
            $banner_id = isset($_POST['banner_id']) ? intval($_POST['banner_id']) : 0;
            error_log('ANMI Banner Preview: Banner ID = ' . $banner_id);
            
            if (!$banner_id) {
                error_log('ANMI Banner Preview: Invalid banner ID');
                wp_send_json_error('Invalid banner ID');
                return;
            }
            
            $banner = self::get_banner($banner_id);
            error_log('ANMI Banner Preview: Banner found = ' . ($banner ? 'yes' : 'no'));
            
            if (!$banner) {
                error_log('ANMI Banner Preview: Banner not found');
                wp_send_json_error('Banner not found');
                return;
            }
            
            // Return banner data
            error_log('ANMI Banner Preview: Sending success response');
            wp_send_json_success(array(
                'id' => $banner->id,
                'name' => $banner->name,
                'video_url' => $banner->video_url,
                'images' => $banner->images,
                'title' => $banner->title,
                'subtitle' => $banner->subtitle,
                'button_text' => $banner->button_text,
                'button_link' => $banner->button_link,
                'show_title' => $banner->show_title,
                'show_subtitle' => $banner->show_subtitle,
                'show_button' => $banner->show_button,
                'height' => $banner->height,
                'transition' => $banner->transition,
                'slider_speed' => $banner->slider_speed,
                'slider_effect' => $banner->slider_effect,
                'autoplay_delay' => $banner->autoplay_delay,
                'mobile_behavior' => $banner->mobile_behavior
            ));
        } catch (Exception $e) {
            error_log('ANMI Banner Preview: Exception - ' . $e->getMessage());
            wp_send_json_error('Server error: ' . $e->getMessage());
        }
    }
}

// Initialize admin panel
new AnMi_Video_Banner_Admin();
