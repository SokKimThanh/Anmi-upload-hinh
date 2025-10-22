<?php
/**
 * Plugin Name: An Mi Tools - Product Style Injector
 * Plugin URI: https://anmitools.com/plugins/product-style-injector
 * Description: Automatically inject common CSS for all An Mi Tools holder products. Detects product section and loads unified stylesheet.
 * Version: 2.0.1
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: An Mi Tools Vietnam
 * Author URI: https://anmitools.com/contact-us/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: anmi-tools-product-style
 * Domain Path: /languages
 * Network: false
 * Update URI: false
 * 
 * @package AnMiProductStyleInjector
 * @version 2.0.0
 * @since 1.0.0
 * 
 * Changelog:
 * 2.0.0 - Changed to use single common CSS file instead of individual files
 * 1.0.0 - Initial release with individual CSS files per product
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * An Mi Product Style Injector Main Class
 * 
 * @since 1.0.0
 */
class AnMi_Product_Style_Injector {
    
    /**
     * Plugin version
     * 
     * @var string
     */
    private $version = '1.0.0';
    
    /**
     * CSS directory path (relative to plugin)
     * 
     * @var string
     */
    private $css_dir;
    
    /**
     * CSS directory URL
     * 
     * @var string
     */
    private $css_url;
    
    /**
     * Parent category slug for holder products
     * 
     * @var string
     */
    private $parent_slug = 'he-thong-ga-kep-can-dao';
    
    /**
     * Common CSS file for all holder products
     * 
     * @var string
     */
    private $common_css_file = 'anmi-holder-products.css';
    
    /**
     * Holder product slug patterns
     * 
     * @var array
     */
    private $holder_slug_patterns = array(
        'bt-', 'hsk-', 'nbh', 'nbj', 'ewn', 'rbh', 'cbh', 'bst', 
        'ck-', 'lbk', 'cbs', 'sb-', 'gc-', 'er-', 'sk-', 'nt-'
    );
    
    /**
     * Constructor
     */
    public function __construct() {
        // Define CSS paths
        $this->css_dir = dirname(__FILE__) . '/../css/';
        $this->css_url = plugins_url('../css/', __FILE__);
        
        // Initialize hooks
        $this->init_hooks();
    }
    
    /**
     * Initialize WordPress hooks
     * 
     * @since 1.0.0
     */
    private function init_hooks() {
        // Enqueue styles based on post/page content
        add_action('wp_enqueue_scripts', array($this, 'enqueue_product_styles'), 20);
        
        // Add admin menu for testing
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    /**
     * Detect product slug from post content
     * 
     * @param string $content Post content
     * @return array Array of detected slugs
     * @since 1.0.0
     */
    private function detect_product_slugs($content) {
        $slugs = array();
        
        // Pattern to match: <section class="slug-name">
        preg_match_all('/<section\s+class=["\']([^"\']+)["\']/', $content, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $class) {
                // Extract individual classes
                $classes = explode(' ', $class);
                foreach ($classes as $single_class) {
                    $single_class = trim($single_class);
                    // Check if corresponding CSS file exists
                    if ($this->css_file_exists($single_class)) {
                        $slugs[] = $single_class;
                    }
                }
            }
        }
        
        return array_unique($slugs);
    }
    
    /**
     * Check if CSS file exists for given slug
     * 
     * @param string $slug Product slug
     * @return bool True if file exists
     * @since 1.0.0
     */
    private function css_file_exists($slug) {
        $file_path = $this->css_dir . $slug . '.css';
        return file_exists($file_path);
    }
    
    /**
     * Enqueue product styles based on content
     * 
     * @since 1.0.0
     * @updated 2.0.0 - Changed to use common CSS file for holder products
     */
    public function enqueue_product_styles() {
        // Only on singular posts/pages
        if (!is_singular()) {
            return;
        }
        
        global $post;
        
        if (empty($post->post_content)) {
            return;
        }
        
        // Check if this is a holder product page
        $is_holder_product = false;
        
        // Method 1: Check post content for holder slug patterns
        foreach ($this->holder_slug_patterns as $pattern) {
            if (strpos($post->post_content, 'class="' . $pattern) !== false ||
                strpos($post->post_content, "class='" . $pattern) !== false) {
                $is_holder_product = true;
                break;
            }
        }
        
        // Method 2: Check if current page is in holder category
        if (!$is_holder_product) {
            $categories = get_the_category($post->ID);
            if ($categories) {
                foreach ($categories as $category) {
                    if ($category->slug === $this->parent_slug || 
                        strpos($category->slug, 'bt-') === 0 ||
                        strpos($category->slug, 'hsk-') === 0) {
                        $is_holder_product = true;
                        break;
                    }
                }
            }
        }
        
        // Method 3: Check post slug
        if (!$is_holder_product && isset($post->post_name)) {
            foreach ($this->holder_slug_patterns as $pattern) {
                if (strpos($post->post_name, $pattern) === 0) {
                    $is_holder_product = true;
                    break;
                }
            }
        }
        
        // Enqueue common CSS if this is a holder product
        if ($is_holder_product) {
            $handle = 'anmi-holder-products';
            $css_url = $this->css_url . $this->common_css_file;
            $css_path = $this->css_dir . $this->common_css_file;
            
            // Get file modification time for cache busting
            $version = file_exists($css_path) ? filemtime($css_path) : $this->version;
            
            wp_enqueue_style(
                $handle,
                $css_url,
                array(),
                $version,
                'all'
            );
            
            // Debug log (only in WP_DEBUG mode)
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("An Mi Product Style Injector: Enqueued common CSS for holder product (Post ID: {$post->ID})");
            }
        }
    }
    
    /**
     * Add admin menu for plugin settings
     * 
     * @since 1.0.0
     */
    public function add_admin_menu() {
        add_options_page(
            'An Mi Product Styles',
            'An Mi Product Styles',
            'manage_options',
            'anmi-product-styles',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Render admin page
     * 
     * @since 1.0.0
     * @updated 2.0.0 - Show common CSS file info
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>An Mi Product Style Injector v2.0</h1>
            <p>Plugin này tự động load <strong>một file CSS chung</strong> cho tất cả sản phẩm khi phát hiện có section product trong nội dung.</p>
            
            <h2>File CSS chung:</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Tên file</th>
                        <th>Đường dẫn</th>
                        <th>Kích thước</th>
                        <th>Ngày sửa đổi</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $common_css = $this->css_dir . 'anmi-products-common.css';
                    if (file_exists($common_css)) {
                        $size = size_format(filesize($common_css));
                        $modified = date('Y-m-d H:i:s', filemtime($common_css));
                        echo "<tr>";
                        echo "<td><strong>anmi-products-common.css</strong></td>";
                        echo "<td><code>{$this->css_url}anmi-products-common.css</code></td>";
                        echo "<td>{$size}</td>";
                        echo "<td>{$modified}</td>";
                        echo "<td><span style='color: green; font-weight: bold;'>✓ Đã tồn tại</span></td>";
                        echo "</tr>";
                    } else {
                        echo "<tr>";
                        echo "<td><strong>anmi-products-common.css</strong></td>";
                        echo "<td colspan='3'><code>{$this->css_dir}anmi-products-common.css</code></td>";
                        echo "<td><span style='color: red; font-weight: bold;'>✗ Chưa tồn tại</span></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <h2>Hướng dẫn sử dụng:</h2>
            <ol>
                <li>Đặt file <code>anmi-products-common.css</code> vào thư mục: <code><?php echo $this->css_dir; ?></code></li>
                <li>Trong nội dung bài viết, sử dụng: <code>&lt;section class="ten-san-pham"&gt;...&lt;/section&gt;</code></li>
                <li>Plugin sẽ tự động load CSS chung khi phát hiện có section product</li>
                <li>Tất cả sản phẩm dùng chung một file CSS → tối ưu performance</li>
            </ol>
            
            <h2>Ưu điểm của CSS chung:</h2>
            <ul>
                <li>✓ <strong>Performance tốt hơn:</strong> Chỉ load 1 file CSS thay vì nhiều file</li>
                <li>✓ <strong>Cache hiệu quả:</strong> Browser cache 1 lần, dùng cho tất cả trang</li>
                <li>✓ <strong>Dễ bảo trì:</strong> Chỉnh sửa 1 file duy nhất</li>
                <li>✓ <strong>Giảm HTTP requests:</strong> Tăng tốc độ tải trang</li>
                <li>✓ <strong>Consistent design:</strong> Đồng nhất style giữa các sản phẩm</li>
            </ul>
            
            <h2>Quy tắc CSS:</h2>
            <ul>
                <li>Sử dụng attribute selector: <code>[class*="-holder"]</code>, <code>[class*="-chuck"]</code>...</li>
                <li>Áp dụng cho tất cả sản phẩm có pattern tương tự</li>
                <li>Màu sắc chuẩn An Mi: #FCF7EC (nền), #000000 (chữ), #0055AA (link)</li>
            </ul>
            
            <h2>Danh sách sản phẩm áp dụng:</h2>
            <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; max-height: 300px; overflow-y: auto;">
                <p>File CSS chung áp dụng cho tất cả 40 sản phẩm:</p>
                <ul style="column-count: 2; column-gap: 20px;">
                    <li>BT-SK High Speed Tool Holder</li>
                    <li>BT-GER High Speed ER Collet Chuck</li>
                    <li>BT-HGER High Speed ER Collet Chuck</li>
                    <li>BT-ER Collet Chuck Standard</li>
                    <li>BT-C Power Chuck Tool Holder</li>
                    <li>BT-OZ Heavy Duty Tool Holder</li>
                    <li>BT-APU Drill Chuck Holder</li>
                    <li>BT-FMA/FMB Face Milling Arbor</li>
                    <li>BT-SLA Weldon Tool Holder</li>
                    <li>BT-MTA/MTB Morse Taper</li>
                    <li>BT-SLO/ERO Oil-Feed Tool Holder</li>
                    <li>BT-SDC High Precision Tool Holder</li>
                    <li>BT-SR Shrink Fit Chuck</li>
                    <li>BT-HS Hydraulic Chuck</li>
                    <li>HSK-SR/ER/GSK/HS/FMB/SLA/APU/C</li>
                    <li>BT Tension-Compression Tapping</li>
                    <li>BT Rigid Tapping Tool Holder</li>
                    <li>NBH2084/NBJ16 Micro Boring System</li>
                    <li>EWN Micro Boring Head</li>
                    <li>RBH/CBH/BST/CK/LBK/CBS/SB/GC</li>
                    <li>ER/SK High Precision Collet</li>
                    <li>NT Tool Holder System</li>
                    <li>...và tất cả sản phẩm khác</li>
                </ul>
            </div>
        </div>
        <?php
    }
}

/**
 * Initialize the plugin
 * 
 * @since 1.0.0
 */
function anmi_product_style_injector_init() {
    new AnMi_Product_Style_Injector();
}
add_action('plugins_loaded', 'anmi_product_style_injector_init');
