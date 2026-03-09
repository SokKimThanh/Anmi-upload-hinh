<?php
/**
 * Plugin Name: AnMi News Styling
 * Plugin URI: https://anmitools.com
 * Description: Tự động inject CSS và JS cho các trang tin tức (Truyền thông, Tin nội bộ, Báo chí)
 * Version: 1.0.0
 * Author: AnMi Tools Team
 * Author URI: https://anmitools.com
 * License: GPL v2 or later
 * Text Domain: anmi-news-styling
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class AnMi_News_Styling {

    /**
     * Constructor
     */
    public function __construct() {
        // Enqueue styles and scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // Add body class for news & recruitment pages
        add_filter( 'body_class', array( $this, 'add_news_body_class' ) );

        // Disable wpautop (auto <p>/<br>) on recruitment pages
        add_action( 'wp', array( $this, 'disable_wpautop_for_recruitment' ) );
    }

    /**
     * Tắt wpautop cho trang tuyển dụng để HTML thuần không bị vỡ layout
     */
    public function disable_wpautop_for_recruitment() {
        if ( $this->is_recruitment_page() ) {
            remove_filter( 'the_content', 'wpautop' );
            remove_filter( 'the_content', 'wptexturize' );
        }
    }
    
    /**
     * Check if current page is a news category
     */
    private function is_news_category() {
        // Check if it's category archive
        if (is_category()) {
            $category = get_queried_object();
            $news_slugs = array('truyen-thong', 'tin-noi-bo', 'bao-chi');
            
            if (in_array($category->slug, $news_slugs)) {
                return true;
            }
        }
        
        // Check if it's single post in news category
        if (is_single()) {
            $categories = get_the_category();
            $news_slugs = array('truyen-thong', 'tin-noi-bo', 'bao-chi');
            
            foreach ($categories as $category) {
                if (in_array($category->slug, $news_slugs)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Check if current page is a recruitment page
     * Hỗ trợ: post type 'tuyen-dung' hoặc category slug 'tuyen-dung'
     */
    private function is_recruitment_page() {
        // Post type riêng (nếu dùng CPT)
        if ( is_singular( 'tuyen-dung' ) || is_post_type_archive( 'tuyen-dung' ) ) {
            return true;
        }

        // Category slug 'tuyen-dung'
        if ( is_category( 'tuyen-dung' ) ) {
            return true;
        }

        // Single post thuộc category tuyển dụng
        if ( is_single() ) {
            foreach ( get_the_category() as $cat ) {
                if ( $cat->slug === 'tuyen-dung' ) {
                    return true;
                }
            }
        }

        // Page có slug chứa 'tuyen-dung'
        if ( is_page() ) {
            $page = get_queried_object();
            if ( $page && strpos( $page->post_name, 'tuyen-dung' ) !== false ) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * Enqueue CSS and JS files
     */
    public function enqueue_assets() {
        // ── News pages ─────────────────────────────────────────
        if ( $this->is_news_category() ) {
            wp_enqueue_style(
                'anmi-news-style',
                plugin_dir_url( __FILE__ ) . 'assets/css/anmi-news-style.css',
                array(),
                '1.0.0',
                'all'
            );

            if ( ! wp_style_is( 'lightbox', 'enqueued' ) ) {
                wp_enqueue_style(
                    'lightbox-css',
                    'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css',
                    array(),
                    '2.11.4'
                );
            }

            wp_enqueue_script( 'jquery' );

            if ( ! wp_script_is( 'lightbox', 'enqueued' ) ) {
                wp_enqueue_script(
                    'lightbox-js',
                    'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js',
                    array( 'jquery' ),
                    '2.11.4',
                    true
                );
            }

            wp_enqueue_script(
                'anmi-news-script',
                plugin_dir_url( __FILE__ ) . 'assets/js/anmi-news-script.js',
                array( 'jquery', 'lightbox-js' ),
                '1.0.0',
                true
            );

            wp_localize_script( 'anmi-news-script', 'anmiNewsData', array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'anmi_news_nonce' ),
            ) );
        }

        // ── Recruitment pages ──────────────────────────────────
        if ( $this->is_recruitment_page() ) {
            wp_enqueue_style(
                'anmi-recruitment-style',
                plugin_dir_url( __FILE__ ) . 'assets/css/anmi-recruitment-style.css',
                array(),
                '1.0.1',
                'all'
            );
        }
    }
    
    /**
     * Add body class for news & recruitment pages
     */
    public function add_news_body_class( $classes ) {
        if ( $this->is_news_category() ) {
            $classes[] = 'anmi-news-page';

            if ( is_category() ) {
                $category  = get_queried_object();
                $classes[] = 'anmi-category-' . $category->slug;
            } elseif ( is_single() ) {
                foreach ( get_the_category() as $category ) {
                    $classes[] = 'anmi-category-' . $category->slug;
                }
            }
        }

        if ( $this->is_recruitment_page() ) {
            $classes[] = 'anmi-recruitment-page';
        }

        return $classes;
    }
}

// Initialize plugin
new AnMi_News_Styling();
