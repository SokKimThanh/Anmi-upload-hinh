<?php
/**
 * Plugin Name: An Mi Video Banner
 * Plugin URI: https://anmitools.com
 * Description: Tạo video banner với hiệu ứng hover transition - chuyển từ ảnh sang video khi rê chuột
 * Version: 1.0.0
 * Author: An Mi Tools Technical Team
 * Author URI: https://anmitools.com
 * License: GPL v2 or later
 * Text Domain: anmi-video-banner
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

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
     * Usage: [anmi_video_banner video_url="video.mp4" image_url="image.jpg" height="600px" title="Title" subtitle="Subtitle" button_text="Learn More" button_link="#"]
     */
    public function render_video_banner($atts) {
        $atts = shortcode_atts(array(
            'video_url' => '',
            'image_url' => '',
            'height' => '600px',
            'title' => '',
            'subtitle' => '',
            'button_text' => '',
            'button_link' => '#',
            'transition' => 'fade', // fade, slide, zoom, blur
            'mobile_behavior' => 'image', // image, video, both
            'autoplay_delay' => '0', // delay in seconds before video plays on hover
        ), $atts, 'anmi_video_banner');
        
        // Validate required fields
        if (empty($atts['video_url']) || empty($atts['image_url'])) {
            return '<p style="color:red;">Error: Video URL and Image URL are required!</p>';
        }
        
        // Generate unique ID
        $unique_id = 'anmi-vb-' . uniqid();
        
        // Start output buffering
        ob_start();
        ?>
        
        <div class="anmi-video-banner-container <?php echo esc_attr($unique_id); ?> transition-<?php echo esc_attr($atts['transition']); ?>" 
             style="height: <?php echo esc_attr($atts['height']); ?>;"
             data-autoplay-delay="<?php echo esc_attr($atts['autoplay_delay']); ?>"
             data-mobile-behavior="<?php echo esc_attr($atts['mobile_behavior']); ?>">
            
            <!-- Video Background -->
            <video class="anmi-banner-video" loop muted playsinline preload="auto">
                <source src="<?php echo esc_url($atts['video_url']); ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            
            <!-- Image Overlay -->
            <div class="anmi-banner-image" style="background-image: url('<?php echo esc_url($atts['image_url']); ?>');"></div>
            
            <?php if (!empty($atts['title']) || !empty($atts['subtitle']) || !empty($atts['button_text'])): ?>
            <!-- Content Overlay -->
            <div class="anmi-banner-content">
                <?php if (!empty($atts['title'])): ?>
                    <h1 class="anmi-banner-title"><?php echo esc_html($atts['title']); ?></h1>
                <?php endif; ?>
                
                <?php if (!empty($atts['subtitle'])): ?>
                    <p class="anmi-banner-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($atts['button_text'])): ?>
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
