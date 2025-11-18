<?php
/**
 * Plugin Name: AnMi News Styling
 * Plugin URI: https://anmitools.com
 * Description: Tự động inject CSS và JS cho các trang tin tức (Truyền thông, Tin nội bộ, Báo chí)
 * Version: 1.0.0
 * Author: Thanh - Content Marketing / Nội dung Kỹ thuật
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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        
        // Add body class for news categories
        add_filter('body_class', array($this, 'add_news_body_class'));
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
     * Enqueue CSS and JS files
     */
    public function enqueue_assets() {
        // Only load on news category pages
        if (!$this->is_news_category()) {
            return;
        }
        
        // Enqueue CSS
        wp_enqueue_style(
            'anmi-news-style',
            plugin_dir_url(__FILE__) . 'assets/css/anmi-news-style.css',
            array(),
            '1.0.0',
            'all'
        );
        
        // Enqueue Lightbox CSS (if not already loaded)
        if (!wp_style_is('lightbox', 'enqueued')) {
            wp_enqueue_style(
                'lightbox-css',
                'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css',
                array(),
                '2.11.4'
            );
        }
        
        // Enqueue jQuery (WordPress includes it)
        wp_enqueue_script('jquery');
        
        // Enqueue Lightbox JS
        if (!wp_script_is('lightbox', 'enqueued')) {
            wp_enqueue_script(
                'lightbox-js',
                'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js',
                array('jquery'),
                '2.11.4',
                true
            );
        }
        
        // Enqueue custom JS
        wp_enqueue_script(
            'anmi-news-script',
            plugin_dir_url(__FILE__) . 'assets/js/anmi-news-script.js',
            array('jquery', 'lightbox-js'),
            '1.0.0',
            true
        );
        
        // Pass data to JS
        wp_localize_script('anmi-news-script', 'anmiNewsData', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('anmi_news_nonce')
        ));
    }
    
    /**
     * Add body class for news categories
     */
    public function add_news_body_class($classes) {
        if ($this->is_news_category()) {
            $classes[] = 'anmi-news-page';
            
            // Add specific category class
            if (is_category()) {
                $category = get_queried_object();
                $classes[] = 'anmi-category-' . $category->slug;
            } elseif (is_single()) {
                $categories = get_the_category();
                foreach ($categories as $category) {
                    $classes[] = 'anmi-category-' . $category->slug;
                }
            }
        }
        
        return $classes;
    }
}

// Initialize plugin
new AnMi_News_Styling();
