<?php
/**
 * Plugin Name: An Mi Tools - CSV Product Importer for WooCommerce
 * Plugin URI: https://anmitools.com
 * Description: Nhập/cập nhật sản phẩm WooCommerce hàng loạt từ file CSV với hỗ trợ tiếng Việt
 * Version: 1.0.0
 * Author: An Mi Tools Technical Team
 * Author URI: https://anmitools.com
 * Text Domain: anmi-csv-importer
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ANMI_CSV_VERSION', '1.0.0');
define('ANMI_CSV_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ANMI_CSV_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ANMI_CSV_PLUGIN_FILE', __FILE__);

/**
 * Main Plugin Class
 */
class AnMi_CSV_Importer {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Check if WooCommerce is active
        add_action('plugins_loaded', array($this, 'check_woocommerce'));
        
        // Initialize plugin
        add_action('init', array($this, 'init'));
        
        // Admin menu
        add_action('admin_menu', array($this, 'admin_menu'));
        
        // Admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        
        // AJAX handlers
        add_action('wp_ajax_anmi_csv_import', array($this, 'ajax_import'));
        add_action('wp_ajax_anmi_csv_export_template', array($this, 'ajax_export_template'));
        add_action('wp_ajax_anmi_csv_validate', array($this, 'ajax_validate'));
    }
    
    /**
     * Check if WooCommerce is active
     */
    public function check_woocommerce() {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
    }
    
    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p>
                <strong>An Mi CSV Importer</strong> yêu cầu WooCommerce phải được cài đặt và kích hoạt.
                <a href="<?php echo admin_url('plugin-install.php?s=woocommerce&tab=search&type=term'); ?>">
                    Cài đặt WooCommerce
                </a>
            </p>
        </div>
        <?php
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Load text domain
        load_plugin_textdomain('anmi-csv-importer', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Include required files
        $this->includes();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        require_once ANMI_CSV_PLUGIN_DIR . 'includes/class-csv-processor.php';
        require_once ANMI_CSV_PLUGIN_DIR . 'includes/class-product-importer.php';
        require_once ANMI_CSV_PLUGIN_DIR . 'includes/class-csv-validator.php';
    }
    
    /**
     * Add admin menu
     */
    public function admin_menu() {
        add_submenu_page(
            'woocommerce',
            'Nhập CSV Sản Phẩm',
            '📊 Nhập CSV',
            'manage_woocommerce',
            'anmi-csv-importer',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_scripts($hook) {
        if ('woocommerce_page_anmi-csv-importer' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'anmi-csv-importer-admin',
            ANMI_CSV_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            ANMI_CSV_VERSION
        );
        
        wp_enqueue_script(
            'anmi-csv-importer-admin',
            ANMI_CSV_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            ANMI_CSV_VERSION,
            true
        );
        
        wp_localize_script('anmi-csv-importer-admin', 'anmiCsvImporter', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('anmi_csv_importer'),
            'max_file_size' => wp_max_upload_size(),
            'i18n' => array(
                'uploading' => __('Đang tải lên...', 'anmi-csv-importer'),
                'processing' => __('Đang xử lý...', 'anmi-csv-importer'),
                'validating' => __('Đang kiểm tra...', 'anmi-csv-importer'),
                'success' => __('Thành công!', 'anmi-csv-importer'),
                'error' => __('Lỗi!', 'anmi-csv-importer'),
            )
        ));
    }
    
    /**
     * Admin page
     */
    public function admin_page() {
        require_once ANMI_CSV_PLUGIN_DIR . 'includes/admin-page.php';
    }
    
    /**
     * AJAX Import handler
     */
    public function ajax_import() {
        check_ajax_referer('anmi_csv_importer', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => 'Không có quyền truy cập'));
        }
        
        if (empty($_FILES['csv_file'])) {
            wp_send_json_error(array('message' => 'Không có file được upload'));
        }
        
        $options = array(
            'update_existing' => isset($_POST['update_existing']),
            'delimiter' => sanitize_text_field($_POST['delimiter'] ?? ','),
            'encoding' => sanitize_text_field($_POST['encoding'] ?? 'UTF-8'),
            'create_categories' => isset($_POST['create_categories']),
            'update_stock' => isset($_POST['update_stock']),
            'update_price' => isset($_POST['update_price']),
        );
        
        try {
            $importer = new AnMi_Product_Importer();
            $result = $importer->import_from_upload($_FILES['csv_file'], $options);
            
            if ($result['success']) {
                wp_send_json_success($result);
            } else {
                wp_send_json_error($result);
            }
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage()
            ));
        }
    }
    
    /**
     * AJAX Export template
     */
    public function ajax_export_template() {
        check_ajax_referer('anmi_csv_importer', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_die('Không có quyền truy cập');
        }
        
        $exporter = new AnMi_CSV_Processor();
        $exporter->export_template();
    }
    
    /**
     * AJAX Validate
     */
    public function ajax_validate() {
        check_ajax_referer('anmi_csv_importer', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => 'Không có quyền truy cập'));
        }
        
        if (empty($_FILES['csv_file'])) {
            wp_send_json_error(array('message' => 'Không có file được upload'));
        }
        
        try {
            $validator = new AnMi_CSV_Validator();
            $result = $validator->validate_file($_FILES['csv_file']);
            
            wp_send_json_success($result);
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage()
            ));
        }
    }
}

// Initialize plugin
function anmi_csv_importer() {
    return AnMi_CSV_Importer::get_instance();
}

// Start the plugin
anmi_csv_importer();
