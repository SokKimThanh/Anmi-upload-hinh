<?php
/**
 * Plugin Name: An Mi Tools - Product Style Injector
 * Plugin URI: https://anmitools.com/plugins/product-style-injector
 * Description: Injects common CSS/JS for holder product pages and conditionally enqueues helper scripts (image lightbox, tabs, contact slider).
 * Version: 2.3.1
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
 * @version 2.3.1
 * * CHANGELOG v2.3.1 (2025-11-29):
 * - FIXED: Critical syntax error (trailing curly brace)
 * - FIXED: Undefined properties holder_tools_cat and milling_tools_cat
 * - UPDATED: Editor styles now use the same detection logic as frontend
 */

// Exit early if loaded outside of WordPress
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class: responsible for enqueueing the shared CSS/JS
 */
class AnMi_Product_Style_Injector
{

    /** @var string Plugin semantic version */
    private $version = '2.3.1';

    /** @var string Absolute path to plugin css directory */
    private $css_dir;

    /** @var string URL to plugin css directory */
    private $css_url;

    /** @var string Parent category slug used to detect holder products */
    private $parent_slug = 'he-thong-ga-kep-can-dao';

    /** @var string Category slug for Holder Tools */
    private $holder_tools_cat = 'he-thong-ga-kep-can-dao';

    /** @var string Category slug for Milling Tools */
    private $milling_tools_cat = 'dung-cu-ghep-manh';

    /** @var string Common CSS filename used for holder products */
    private $common_css_file = 'anmi-holder-products.css';

    /** @var array Prefixes/patterns used to detect holder product slugs */
    private $holder_slug_patterns = array(
        'bt-',
        'hsk-',
        'nbh',
        'nbj',
        'ewn',
        'rbh',
        'cbh',
        'bst',
        'ck-',
        'lbk',
        'cbs',
        'sb-',
        'gc-',
        'er-',
        'sk-',
        'nt-'
    );

    /** @var array Prefixes used to detect milling holder families (FM/HM/RM...) */
    private $milling_holder_prefixes = array(
        'fm', // Face milling: FM451, FM454, FM452, FM752, FM882, FM901, FM901F, FM902, FM903, FM904, FM905
        'hm', // High feed: HM192
        'rm'  // Profiling: RM01, RM02
    );

    /** @var array Prefixes used to detect milling insert families (SE/OD/SN/...) */
    private $milling_insert_prefixes = array(
        'se',
        'od',
        'sn',
        'on',
        'ap',
        'bx',
        'tn',
        'wn',
        'ln',
        'sd',
        'rp',
        'pd'
    );

    /** @var array 4-letter insert codes (safer than 2-letter prefixes) */
    private $milling_insert_codes_4 = array(
        'sekt',
        'seet',
        'odmt',
        'snmx',
        'sngx',
        'onmu',
        'apmt',
        'bxkt',
        'tngx',
        'wnmx',
        'lngx',
        'sdkt',
        'rpmw',
        'rpkt',
        'pdmt',
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->css_dir = dirname(__FILE__) . '/css/';
        $this->css_url = plugins_url('css/', __FILE__);

        $this->init_hooks();
    }

    /**
     * Attach WordPress hooks
     */
    private function init_hooks()
    {
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
     * Front-end enqueue logic
     * ------------------------------------------------------------------ */

    /**
     * Check if current product belongs to either holder system or milling insert/holder categories
     * @param int $product_id
     * @return bool
     */
    private function is_supported_product_category($product_id)
    {
        if (!is_singular('product')) {
            return false;
        }

        $terms = get_the_terms($product_id, 'product_cat');
        if (empty($terms) || is_wp_error($terms)) {
            return false;
        }

        foreach ($terms as $term) {
            // Exact match holder or milling categories
            if ($term->slug === $this->holder_tools_cat || $term->slug === $this->milling_tools_cat) {
                return true;
            }

            // Child categories check
            if (strpos($term->slug, $this->holder_tools_cat . '-') === 0 || strpos($term->slug, $this->milling_tools_cat . '-') === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Conditionally enqueue common CSS and helper scripts for holder product pages
     */
    public function enqueue_product_styles()
    {
        if (!is_singular()) {
            return;
        }

        global $post;
        if (empty($post) || empty($post->post_content)) {
            return;
        }

        // Determine whether this post should be treated as a holder/milling product
        $is_holder_product = false;

        // 1) WooCommerce products in supported categories
        if ($post->post_type === 'product' && $this->is_supported_product_category($post->ID)) {
            $is_holder_product = true;
        } else {
            // 2) Fallback: legacy holder detection for other (non-product) content if needed
            $is_holder_product = $this->is_holder_product($post);
        }

        if (!$is_holder_product) {
            return;
        }

        // Prepare common CSS
        $css_path = $this->css_dir . $this->common_css_file;
        $css_url = $this->css_url . $this->common_css_file;
        $version = file_exists($css_path) ? filemtime($css_path) : $this->version;

        wp_enqueue_style('anmi-holder-products', $css_url, array(), $version, 'all');

        // Enqueue shared helper scripts
        wp_enqueue_script('anmi-image-lightbox', plugins_url('js/image-lightbox.js', __FILE__), array(), $version, true);

        // Always enqueue tab-navigation.js for all holder products
        $tab_js_path = dirname(__FILE__) . '/js/tab-navigation.js';
        if (file_exists($tab_js_path)) {
            wp_enqueue_script('anmi-tab-navigation', plugins_url('js/tab-navigation.js', __FILE__), array(), $version, true);
        }

        // Contact slider (mobile swipe)
        wp_enqueue_script('anmi-contact-slider', plugins_url('js/contact-slider.js', __FILE__), array(), $version, true);
    }

    /**
     * Decide if a post should be treated as a holder product
     * @param WP_Post $post
     * @return bool
     */
    private function is_holder_product($post)
    {
        // 1) Quick heuristic: search for specific CSS CLASSES in the content
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

        // 3) Post slug prefix / combined milling code check
        if (!empty($post->post_name)) {
            $slug = strtolower($post->post_name);

            // 3a) Existing holder prefixes (bt-, hsk-, ...)
            foreach ($this->holder_slug_patterns as $pattern) {
                if (strpos($slug, $pattern) === 0) {
                    return true;
                }
            }

            // 3b) Milling holder family prefixes (fm, hm, rm) at slug start
            foreach ($this->milling_holder_prefixes as $holder_prefix) {
                if (strpos($slug, $holder_prefix) === 0) {
                    return true;
                }
            }

            // 3c) Combined insert-holder slugs (e.g. sekt12t3-fm451)
            foreach ($this->milling_insert_codes_4 as $insert_code) {
                // Bước 1: slug phải chứa mã insert 4 ký tự (SEKT, ODMT, APMT, ...)
                if (strpos($slug, $insert_code) !== false) {
                    foreach ($this->milling_holder_prefixes as $holder_prefix) {
                        // Bước 2: mã holder nên xuất hiện sau dấu gạch ngang: -fm, -hm, -rm
                        if (strpos($slug, '-' . $holder_prefix) !== false) {
                            return true;
                        }

                        // Fallback: trường hợp slug bắt đầu trực tiếp bằng fm/hm/rm
                        if (strpos($slug, $holder_prefix) === 0) {
                            return true;
                        }
                    }
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
    public function enqueue_editor_styles()
    {
        global $post;
        if (!$post) {
            return;
        }

        $should_load = false;

        // Logic 1: Luôn load cho Post Type 'product' (WooCommerce)
        if ($post->post_type === 'product') {
            $should_load = true;
        }

        // Logic 2: Tái sử dụng logic is_holder_product để nhất quán
        if (!$should_load) {
            if ($this->is_holder_product($post)) {
                $should_load = true;
            }
        }

        // Fallback: Check term thủ công
        if (!$should_load) {
            $categories = get_the_terms($post->ID, 'category');
            if ($categories && !is_wp_error($categories)) {
                foreach ($categories as $category) {
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

            // Sửa màu nền editor để dễ nhìn code trắng
            $custom_css = ".editor-styles-wrapper { background-color: #FCF7EC; padding: 20px; }";
            wp_add_inline_style('anmi-holder-products-editor', $custom_css);
        }
    }

    /* ---------------------------------------------------------------------
     * Admin UI (debug/info)
     * ------------------------------------------------------------------ */

    public function add_admin_menu()
    {
        add_options_page('An Mi Product Styles', 'An Mi Product Styles', 'manage_options', 'anmi-product-styles', array($this, 'admin_page'));
    }

    public function admin_page()
    {
        ?>
        <div class="wrap">
            <h1>An Mi Product Style Injector v<?php echo esc_html($this->version); ?></h1>
            <p>Plugin tự động load CSS/JS cho sản phẩm Holder/Milling.</p>

            <h2>Trạng thái File CSS:</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Tên file</th>
                        <th>Đường dẫn</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $common_css = $this->css_dir . $this->common_css_file;
                    if (file_exists($common_css)) {
                        echo "<tr>";
                        echo "<td><strong>{$this->common_css_file}</strong></td>";
                        echo "<td><code>{$this->css_url}{$this->common_css_file}</code></td>";
                        echo "<td><span style='color: green; font-weight: bold;'>✓ Đã tồn tại</span></td>";
                        echo "</tr>";
                    } else {
                        echo "<tr>";
                        echo "<td><strong>{$this->common_css_file}</strong></td>";
                        echo "<td><code>{$this->css_dir}{$this->common_css_file}</code></td>";
                        echo "<td><span style='color: red; font-weight: bold;'>✗ Chưa tồn tại</span></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

            <h2>Cấu hình nhận diện:</h2>
            <div style="background: #f5f5f5; padding: 15px; border-radius: 5px;">
                <p><strong>Holder Category:</strong> <?php echo esc_html($this->holder_tools_cat); ?></p>
                <p><strong>Milling Category:</strong> <?php echo esc_html($this->milling_tools_cat); ?></p>
            </div>
        </div>
        <?php
    }
}

/**
 * Plugin bootstrap
 */
function anmi_product_style_injector_init()
{
    new AnMi_Product_Style_Injector();
}
add_action('plugins_loaded', 'anmi_product_style_injector_init');