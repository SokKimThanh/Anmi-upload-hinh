<?php
/**
 * Plugin Name: Anmi Plugin Manager
 * Plugin URI: https://anmi.com.vn
 * Description: Quản lý plugins Anmi với upload, staging, backup, safe-activate và logging
 * Version: 1.0.0
 * Author: Anmi
 * Author URI: https://anmi.com.vn
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Text Domain: anmi-plugin-manager
 */

defined('ABSPATH') || exit;

// Define constants
define('ANMI_PM_VERSION', '1.0.0');
define('ANMI_PM_FILE', __FILE__);
define('ANMI_PM_DIR', plugin_dir_path(__FILE__));
define('ANMI_PM_URL', plugin_dir_url(__FILE__));
define('ANMI_PM_STAGING_DIR', WP_CONTENT_DIR . '/anmi-staging-plugins');
define('ANMI_PM_BACKUP_DIR', WP_CONTENT_DIR . '/anmi-backups');
define('ANMI_PM_QUARANTINE_DIR', WP_CONTENT_DIR . '/anmi-quarantine');
define('ANMI_PM_TEMP_DIR', WP_CONTENT_DIR . '/uploads/anmi-temp');

/**
 * Main Plugin Manager Class
 */
class Anmi_Plugin_Manager {
    
    private static $instance = null;
    
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('init', [$this, 'register_post_types']);
        
        // Create directories on activation
        register_activation_hook(ANMI_PM_FILE, [$this, 'activate']);
    }
    
    /**
     * Load dependencies
     */
    private function load_dependencies() {
        require_once ANMI_PM_DIR . 'inc/class-metadata-manager.php';
        require_once ANMI_PM_DIR . 'inc/class-plugin-list.php';
        require_once ANMI_PM_DIR . 'inc/class-plugin-uploader.php';
        require_once ANMI_PM_DIR . 'inc/class-plugin-activator.php';
        require_once ANMI_PM_DIR . 'inc/class-logger.php';
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create necessary directories
        $dirs = [
            ANMI_PM_STAGING_DIR,
            ANMI_PM_BACKUP_DIR,
            ANMI_PM_QUARANTINE_DIR,
            ANMI_PM_TEMP_DIR
        ];
        
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
        }
        
        // Create .htaccess to protect directories
        $htaccess_content = "Deny from all\n";
        foreach ($dirs as $dir) {
            $htaccess_file = $dir . '/.htaccess';
            if (!file_exists($htaccess_file)) {
                @file_put_contents($htaccess_file, $htaccess_content);
            }
        }
        
        flush_rewrite_rules();
    }
    
    /**
     * Register custom post types
     */
    public function register_post_types() {
        // Custom Post Type for plugin metadata
        register_post_type('anmi_plugin', [
            'public' => false,
            'show_ui' => false,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => false,
            'query_var' => false,
            'supports' => ['title']
        ]);
        
        // Custom Post Type for logs
        register_post_type('anmi_plugin_log', [
            'public' => false,
            'show_ui' => false,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => false,
            'query_var' => false,
            'supports' => ['title']
        ]);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Anmi Plugins', 'anmi-plugin-manager'),
            __('Anmi Plugins', 'anmi-plugin-manager'),
            'manage_options',
            'anmi-plugins',
            [$this, 'render_main_page'],
            'dashicons-admin-plugins',
            59
        );
        
        add_submenu_page(
            'anmi-plugins',
            __('Upload Plugin', 'anmi-plugin-manager'),
            __('Upload Plugin', 'anmi-plugin-manager'),
            'manage_options',
            'anmi-plugins-upload',
            [$this, 'render_upload_page']
        );
        
        add_submenu_page(
            'anmi-plugins',
            __('History Logs', 'anmi-plugin-manager'),
            __('History Logs', 'anmi-plugin-manager'),
            'manage_options',
            'anmi-plugins-logs',
            [$this, 'render_logs_page']
        );
        
        add_submenu_page(
            'anmi-plugins',
            __('Watchdog Status', 'anmi-plugin-manager'),
            __('Watchdog Status', 'anmi-plugin-manager'),
            'manage_options',
            'anmi-plugins-watchdog',
            [$this, 'render_watchdog_page']
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'anmi-plugins') === false) {
            return;
        }
        
        wp_enqueue_style(
            'anmi-pm-admin',
            ANMI_PM_URL . 'assets/admin.css',
            [],
            ANMI_PM_VERSION
        );
        
        wp_enqueue_script(
            'anmi-pm-admin',
            ANMI_PM_URL . 'assets/admin.js',
            ['jquery'],
            ANMI_PM_VERSION,
            true
        );
        
        wp_localize_script('anmi-pm-admin', 'anmiPM', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('anmi_pm_ajax')
        ]);
    }
    
    /**
     * Render main page
     */
    public function render_main_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'anmi-plugin-manager'));
        }
        
        $plugin_list = new Anmi_PM_Plugin_List();
        $plugin_list->render();
    }
    
    /**
     * Render upload page
     */
    public function render_upload_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'anmi-plugin-manager'));
        }
        
        $uploader = new Anmi_PM_Plugin_Uploader();
        $uploader->render();
    }
    
    /**
     * Render logs page
     */
    public function render_logs_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'anmi-plugin-manager'));
        }
        
        $logger = new Anmi_PM_Logger();
        $logger->render_logs_page();
    }
    
    /**
     * Render watchdog page
     */
    public function render_watchdog_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'anmi-plugin-manager'));
        }
        
        // Redirect to watchdog's own view
        $watchdog_url = wp_nonce_url(
            admin_url('admin.php?anmi_watchdog_action=view_logs'),
            'anmi_watchdog_view_logs'
        );
        wp_redirect($watchdog_url);
        exit;
    }
}

// Initialize plugin
Anmi_Plugin_Manager::instance();
