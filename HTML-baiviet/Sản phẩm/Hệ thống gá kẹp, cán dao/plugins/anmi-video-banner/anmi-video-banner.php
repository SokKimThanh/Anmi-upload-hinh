<?php
/**
 * Plugin Name: An Mi Video Banner
 * Plugin URI: https://anmitools.com
 * Description: Video banner với slider tự động + Admin CRUD panel - YouTube/Vimeo/MP4 support
 * Version: 1.2.0
 * Author: An Mi Tools Technical Team
 * Author URI: https://anmitools.com
 * License: GPL v2 or later
 * Text Domain: anmi-video-banner
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ANMI_VIDEO_BANNER_VERSION', '1.2.0');
define('ANMI_VIDEO_BANNER_FILE', __FILE__);
define('ANMI_VIDEO_BANNER_PATH', plugin_dir_path(__FILE__));
define('ANMI_VIDEO_BANNER_URL', plugin_dir_url(__FILE__));

// Include admin panel
require_once ANMI_VIDEO_BANNER_PATH . 'includes/admin-panel.php';

class AnMi_Video_Banner {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        
        // Register shortcode
        add_shortcode('anmi_video_banner', array($this, 'render_video_banner'));
        
        // Add Elementor widget (if Elementor is active)
        add_action('elementor/widgets/register', array($this, 'register_elementor_widget'));
    }
    
    /**
     * Enqueue CSS and JavaScript
     */
    public function enqueue_assets() {
        // CSS
        wp_enqueue_style(
            'anmi-video-banner-style',
            plugin_dir_url(__FILE__) . 'assets/css/video-banner.css',
            array(),
            '1.0.0'
        );
        
        // JavaScript
        wp_enqueue_script(
            'anmi-video-banner-script',
            plugin_dir_url(__FILE__) . 'assets/js/video-banner.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
    
    /**
     * Render video banner shortcode
     * 
     * Usage: [anmi_video_banner id="1"] OR [anmi_video_banner video_url="..." images="..."]
     */
    public function render_video_banner($atts) {
        $atts = shortcode_atts(array(
            'id' => '', // Banner ID from database
            'video_url' => '',
            'images' => '', // Comma-separated image URLs for slider
            'image_url' => '', // Backward compatibility - single image
            'height' => '600px',
            'title' => '',
            'subtitle' => '',
            'button_text' => '',
            'button_link' => '#',
            'show_title' => '0',
            'show_subtitle' => '0',
            'show_button' => '0',
            'transition' => 'fade', // fade, slide, zoom, blur
            'mobile_behavior' => 'image', // image, video, both
            'autoplay_delay' => '0', // delay in seconds before video plays on hover
            'slider_speed' => '3000', // milliseconds between slides
            'slider_effect' => 'fade', // fade, slide
        ), $atts, 'anmi_video_banner');
        
        // If ID is provided, load banner from database
        if (!empty($atts['id'])) {
            $banner = AnMi_Video_Banner_Admin::get_banner(intval($atts['id']));
            
            if (!$banner) {
                return '<p style="color:red;">Error: Banner not found!</p>';
            }
            
            // Override attributes with database values
            $atts['video_url'] = $banner->video_url;
            $atts['images'] = $banner->images;
            $atts['height'] = $banner->height;
            $atts['title'] = $banner->title;
            $atts['subtitle'] = $banner->subtitle;
            $atts['button_text'] = $banner->button_text;
            $atts['button_link'] = $banner->button_link;
            $atts['show_title'] = isset($banner->show_title) ? $banner->show_title : 0;
            $atts['show_subtitle'] = isset($banner->show_subtitle) ? $banner->show_subtitle : 0;
            $atts['show_button'] = isset($banner->show_button) ? $banner->show_button : 0;
            $atts['transition'] = $banner->transition;
            $atts['mobile_behavior'] = $banner->mobile_behavior;
            $atts['autoplay_delay'] = $banner->autoplay_delay;
            $atts['slider_speed'] = $banner->slider_speed;
            $atts['slider_effect'] = $banner->slider_effect;
        }
        
        // Parse images - support both comma-separated and single image
        $image_urls = array();
        if (!empty($atts['images'])) {
            // Check if it's JSON from database
            if (is_string($atts['images']) && strpos($atts['images'], '[') === 0) {
                $image_urls = json_decode($atts['images'], true);
            } else {
                $image_urls = array_map('trim', explode(',', $atts['images']));
            }
        } elseif (!empty($atts['image_url'])) {
            $image_urls = array($atts['image_url']);
        }
        
        // Validate required fields
        if (empty($atts['video_url']) || empty($image_urls)) {
            return '<p style="color:red;">Error: Video URL and at least one image are required!</p>';
        }
        
        // Generate unique ID
        $unique_id = 'anmi-vb-' . uniqid();
        
        // Start output buffering
        ob_start();
        ?>
        
        <div class="anmi-video-banner-container <?php echo esc_attr($unique_id); ?> transition-<?php echo esc_attr($atts['transition']); ?>" 
             style="height: <?php echo esc_attr($atts['height']); ?>;"
             data-autoplay-delay="<?php echo esc_attr($atts['autoplay_delay']); ?>"
             data-mobile-behavior="<?php echo esc_attr($atts['mobile_behavior']); ?>"
             data-slider-speed="<?php echo esc_attr($atts['slider_speed']); ?>"
             data-slider-effect="<?php echo esc_attr($atts['slider_effect']); ?>">
            
            <!-- Video Background -->
            <video class="anmi-banner-video" loop muted playsinline preload="auto">
                <source src="<?php echo esc_url($atts['video_url']); ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            
            <!-- Image Slider Overlay -->
            <div class="anmi-banner-slider">
                <?php foreach ($image_urls as $index => $image_url): ?>
                    <div class="anmi-slider-slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                         style="background-image: url('<?php echo esc_url($image_url); ?>');"></div>
                <?php endforeach; ?>
                
                <?php if (count($image_urls) > 1): ?>
                    <!-- Slider Navigation Dots -->
                    <div class="anmi-slider-dots">
                        <?php foreach ($image_urls as $index => $image_url): ?>
                            <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($atts['title']) || !empty($atts['subtitle']) || !empty($atts['button_text'])): ?>
            <!-- Content Overlay -->
            <div class="anmi-banner-content"
                 data-show-title="<?php echo esc_attr($atts['show_title']); ?>"
                 data-show-subtitle="<?php echo esc_attr($atts['show_subtitle']); ?>"
                 data-show-button="<?php echo esc_attr($atts['show_button']); ?>">
                <?php if (!empty($atts['title']) && $atts['show_title']): ?>
                    <h1 class="anmi-banner-title"><?php echo esc_html($atts['title']); ?></h1>
                <?php endif; ?>
                
                <?php if (!empty($atts['subtitle']) && $atts['show_subtitle']): ?>
                    <p class="anmi-banner-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($atts['button_text']) && $atts['show_button']): ?>
                    <a href="<?php echo esc_url($atts['button_link']); ?>" class="anmi-banner-btn">
                        <?php echo esc_html($atts['button_text']); ?>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Loading Spinner -->
            <div class="anmi-banner-loader">
                <div class="spinner"></div>
            </div>
        </div>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Register Elementor Widget
     */
    public function register_elementor_widget($widgets_manager) {
        if (!class_exists('Elementor\Widget_Base')) {
            return;
        }
        
        require_once plugin_dir_path(__FILE__) . 'includes/elementor-widget.php';
        $widgets_manager->register(new \AnMi_Video_Banner_Elementor_Widget());
    }
}

// Initialize plugin
AnMi_Video_Banner::get_instance();
