<?php
/**
 * Plugin Name: An Mi Tools - Product Style Injector
 * Plugin URI: https://anmitools.com/plugins/product-style-injector
 * Description: Injects common CSS/JS for holder product pages and conditionally enqueues helper scripts (image lightbox, tabs, contact slider).
 * Version: 2.2.0
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
 * @version 2.2.0
 * 
 * CHANGELOG v2.2.0 (2025-11-23):
 * - SIMPLIFIED: Always enqueue tab-navigation.js for all holder products
 * - REMOVED: Complex tab detection logic (strpos, detect_product_slugs)
 * - REASON: Tab detection was causing tabs to disappear on some pages
 * - RESULT: More reliable, simpler code, tabs always work
 */

// Exit early if loaded outside of WordPress
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class: responsible for enqueueing the shared CSS/JS
 */
class AnMi_Product_Style_Injector {

    /** @var string Plugin semantic version (also used as fallback cache-bust) */
    private $version = '2.2.0';

    /** @var string Absolute path to plugin css directory */
    private $css_dir;

    /** @var string URL to plugin css directory */
    private $css_url;

    /** @var string Parent category slug used to detect holder products */
    private $parent_slug = 'he-thong-ga-kep-can-dao';

    /** @var string Common CSS filename used for holder products */
    private $common_css_file = 'anmi-holder-products.css';

    /** @var array Prefixes/patterns used to detect holder product slugs */
    private $holder_slug_patterns = array(
        'bt-', 'hsk-', 'nbh', 'nbj', 'ewn', 'rbh', 'cbh', 'bst',
        'ck-', 'lbk', 'cbs', 'sb-', 'gc-', 'er-', 'sk-', 'nt-'
    );

    /**
     * Constructor
     */
    public function __construct() {
        $this->css_dir = dirname(__FILE__) . '/css/';
        $this->css_url = plugins_url('css/', __FILE__);

        $this->init_hooks();
    }

    /**
     * Attach WordPress hooks
     */
    private function init_hooks() {
        // Enqueue front-end assets (conditionally)
        add_action('wp_enqueue_scripts', array($this, 'enqueue_product_styles'), 20);

        // Enqueue styles inside the block editor for convenience
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_styles'));

        // Register Classic Editor support
        add_editor_style($this->css_url . $this->common_css_file);

        // Add a lightweight admin page for debugging/file info
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    /* ---------------------------------------------------------------------
     * Helpers: detection + file checks
     * ------------------------------------------------------------------ */

    /**
     * Check for the existence of a CSS file for a given slug
     * @param string $slug
     * @return bool
     */
    private function css_file_exists($slug) {
        return file_exists($this->css_dir . $slug . '.css');
    }

    /* ---------------------------------------------------------------------
     * Front-end enqueue logic
     * ------------------------------------------------------------------ */

    /**
     * Conditionally enqueue common CSS and helper scripts for holder product pages
     */
    public function enqueue_product_styles() {
        if (!is_singular()) {
            return;
        }

        global $post;
        if (empty($post) || empty($post->post_content)) {
            return;
        }

        // Determine whether this post should be treated as a holder product
        $is_holder_product = $this->is_holder_product($post);
        if (!$is_holder_product) {
            return;
        }

        // Prepare common CSS
        $css_path = $this->css_dir . $this->common_css_file;
        $css_url = $this->css_url . $this->common_css_file;
        $version = file_exists($css_path) ? filemtime($css_path) : $this->version;

        wp_enqueue_style('anmi-holder-products', $css_url, array(), $version, 'all');

        // Enqueue shared helper scripts (image lightbox always for holder pages)
        wp_enqueue_script('anmi-image-lightbox', plugins_url('js/image-lightbox.js', __FILE__), array(), $version, true);

        // ✅ SIMPLIFIED: Always enqueue tab-navigation.js for all holder products
        // No complex detection needed - if it's a holder product, it likely has tabs
        $tab_js_path = dirname(__FILE__) . '/js/tab-navigation.js';
        if (file_exists($tab_js_path)) {
            wp_enqueue_script('anmi-tab-navigation', plugins_url('js/tab-navigation.js', __FILE__), array(), $version, true);
        }

        // Contact slider (mobile swipe) - keep enqueued for holder pages
        wp_enqueue_script('anmi-contact-slider', plugins_url('js/contact-slider.js', __FILE__), array(), $version, true);

        // Optional debug logging when WP_DEBUG is enabled
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("An Mi Product Style Injector v{$this->version}: assets enqueued for post {$post->ID}");
        }
    }

    /**
     * Decide if a post should be treated as a holder product
     * @param WP_Post $post
     * @return bool
     */
    private function is_holder_product($post) {
        // 1) Quick heuristic: search for class patterns in the content
        foreach ($this->holder_slug_patterns as $pattern) {
            if (strpos($post->post_content, 'class="' . $pattern) !== false || strpos($post->post_content, "class='{$pattern}") !== false) {
                return true;
            }
        }

        // 2) Category-based detection
        $categories = get_the_category($post->ID);
        if ($categories) {
            foreach ($categories as $category) {
                if ($category->slug === $this->parent_slug || strpos($category->slug, 'bt-') === 0 || strpos($category->slug, 'hsk-') === 0) {
                    return true;
                }
            }
        }

        // 3) Post slug prefix check
        if (!empty($post->post_name)) {
            foreach ($this->holder_slug_patterns as $pattern) {
                if (strpos($post->post_name, $pattern) === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /* ---------------------------------------------------------------------
     * Editor (Gutenberg/Classic) support
     * ------------------------------------------------------------------ */

    /**
     * Enqueue styles used inside the block editor for improved WYSIWYG editing
     */
    public function enqueue_editor_styles() {
        global $post;
        if (!$post) {
            return;
        }

        $should_load = false;

        if ($post->post_type === 'product') {
            $should_load = true;
        }

        if (!$should_load && !empty($post->post_name)) {
            foreach ($this->holder_slug_patterns as $pattern) {
                if (strpos($post->post_name, $pattern) === 0) {
                    $should_load = true;
                    break;
                }
            }
        }

        if (!$should_load) {
            $categories = get_the_terms($post->ID, 'category');
            if ($categories) {
                foreach ($categories as $category) {
                    if ($category->slug === $this->parent_slug || strpos($category->slug, 'holder') !== false || strpos($category->slug, 'ga-kep') !== false) {
                        $should_load = true;
                        break;
                    }
                }
            }

            $product_cats = get_the_terms($post->ID, 'product_cat');
            if ($product_cats) {
                foreach ($product_cats as $category) {
                    if ($category->slug === $this->parent_slug || strpos($category->slug, 'holder') !== false || strpos($category->slug, 'ga-kep') !== false) {
                        $should_load = true;
                        break;
                    }
                }
            }
        }

        if ($should_load) {
            $css_url = $this->css_url . $this->common_css_file;
            $css_path = $this->css_dir . $this->common_css_file;
            $version = file_exists($css_path) ? filemtime($css_path) : $this->version;

            wp_enqueue_style('anmi-holder-products-editor', $css_url, array('wp-edit-blocks'), $version, 'all');
            $custom_css = ".editor-styles-wrapper { background-color: #FCF7EC; padding: 20px; }";
            wp_add_inline_style('anmi-holder-products-editor', $custom_css);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("An Mi Product Style Injector: loaded editor CSS for post {$post->ID}");
            }
        }
    }

    /* ---------------------------------------------------------------------
     * Admin UI (debug/info)
     * ------------------------------------------------------------------ */

    public function add_admin_menu() {
        add_options_page('An Mi Product Styles', 'An Mi Product Styles', 'manage_options', 'anmi-product-styles', array($this, 'admin_page'));
    }

    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>An Mi Product Style Injector v<?php echo esc_html($this->version); ?></h1>
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
                    $common_css = $this->css_dir . $this->common_css_file;
                    if (file_exists($common_css)) {
                        $size = size_format(filesize($common_css));
                        $modified = date('Y-m-d H:i:s', filemtime($common_css));
                        echo "<tr>";
                        echo "<td><strong>{$this->common_css_file}</strong></td>";
                        echo "<td><code>{$this->css_url}{$this->common_css_file}</code></td>";
                        echo "<td>{$size}</td>";
                        echo "<td>{$modified}</td>";
                        echo "<td><span style='color: green; font-weight: bold;'>✓ Đã tồn tại</span></td>";
                        echo "</tr>";
                    } else {
                        echo "<tr>";
                        echo "<td><strong>{$this->common_css_file}</strong></td>";
                        echo "<td colspan='3'><code>{$this->css_dir}{$this->common_css_file}</code></td>";
                        echo "<td><span style='color: red; font-weight: bold;'>✗ Chưa tồn tại</span></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

            <h2>JavaScript Helper Files:</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Tên file</th>
                        <th>Chức năng</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $js_files = array(
                        'image-lightbox.js' => 'Click ảnh để phóng to (lightbox)',
                        'tab-navigation.js' => 'Chuyển tab nội dung (LUÔN LOAD)',
                        'contact-slider.js' => 'Vuốt xem chi nhánh (mobile)'
                    );
                    
                    foreach ($js_files as $filename => $description) {
                        $js_path = dirname(__FILE__) . '/js/' . $filename;
                        $exists = file_exists($js_path);
                        $status = $exists 
                            ? "<span style='color: green; font-weight: bold;'>✓ Đã tồn tại</span>" 
                            : "<span style='color: red; font-weight: bold;'>✗ Chưa tồn tại</span>";
                        
                        echo "<tr>";
                        echo "<td><strong>{$filename}</strong></td>";
                        echo "<td>{$description}</td>";
                        echo "<td>{$status}</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

            <h2>Hướng dẫn sử dụng:</h2>
            <ol>
                <li>Đặt file <code><?php echo esc_html($this->common_css_file); ?></code> vào thư mục: <code><?php echo esc_html($this->css_dir); ?></code></li>
                <li>Đặt 3 file JS vào thư mục: <code><?php echo esc_html(dirname(__FILE__) . '/js/'); ?></code></li>
                <li>Trong nội dung bài viết, sử dụng: <code>&lt;section class="ten-san-pham"&gt;...&lt;/section&gt;</code></li>
                <li>Plugin sẽ tự động load CSS + JS khi phát hiện holder product</li>
            </ol>

            <h2>Danh sách sản phẩm áp dụng:</h2>
            <div style="background: #f5f5f5; padding: 15px; border-radius: 5px;">
                <p><strong>Holder product patterns:</strong> <?php echo implode(', ', $this->holder_slug_patterns); ?></p>
                <p><strong>Parent category:</strong> <?php echo esc_html($this->parent_slug); ?></p>
            </div>

            <div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-left: 4px solid #0066cc; border-radius: 4px;">
                <h3 style="margin-top: 0;">📋 v2.2.0 Update Notes:</h3>
                <ul style="margin: 0;">
                    <li><strong>Tab Navigation:</strong> Luôn load cho tất cả holder products (không cần phát hiện)</li>
                    <li><strong>Lý do:</strong> Logic phát hiện phức tạp gây lỗi tabs biến mất</li>
                    <li><strong>Kết quả:</strong> Code đơn giản hơn, tabs luôn hoạt động</li>
                </ul>
            </div>
        </div>
        <?php
    }
}

/**
 * Plugin bootstrap
 */
function anmi_product_style_injector_init() {
    new AnMi_Product_Style_Injector();
}
add_action('plugins_loaded', 'anmi_product_style_injector_init');