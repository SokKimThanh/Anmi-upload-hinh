<?php
/**
 * Plugin Name: An Mi Video Banner
 * Plugin URI: https://anmitools.com
 * Description: Video banner với slider tự động + Admin CRUD panel - YouTube/Vimeo/MP4 support + Iframe Embed + Modal Preview
 * Version: 1.6.10
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
define('ANMI_VIDEO_BANNER_VERSION', '1.6.10');
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
     * Detect video type and convert URL to embed format
     * 
     * @param string $url Video URL (YouTube, Vimeo, or direct MP4)
     * @param bool $player_mode Enable player controls (default: false for background mode)
     * @return array ['type' => 'youtube|vimeo|direct', 'embed_url' => '...', 'video_id' => '...']
     */
    private function parse_video_url($url, $player_mode = false) {
        $result = array(
            'type' => 'direct',
            'embed_url' => $url,
            'video_id' => null
        );
        
        // YouTube detection (supports regular URLs, youtu.be with query params, and iframe embed codes)
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})(?:[?&]|$)/i', $url, $match)) {
            $result['type'] = 'youtube';
            $result['video_id'] = $match[1];
            
            if ($player_mode) {
                // Player mode: Show controls, no autoplay, allow user interaction
                $result['embed_url'] = 'https://www.youtube.com/embed/' . $match[1] . '?controls=1&rel=0&modestbranding=1&playsinline=1';
            } else {
                // Background mode: Autoplay, muted, no controls
                $result['embed_url'] = 'https://www.youtube.com/embed/' . $match[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $match[1] . '&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1';
            }
        }
        // Vimeo detection
        elseif (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)(?:$|\/|\?)/i', $url, $match)) {
            $result['type'] = 'vimeo';
            $result['video_id'] = $match[1];
            
            if ($player_mode) {
                // Player mode: Show controls
                $result['embed_url'] = 'https://player.vimeo.com/video/' . $match[1] . '?controls=1';
            } else {
                // Background mode
                $result['embed_url'] = 'https://player.vimeo.com/video/' . $match[1] . '?autoplay=1&muted=1&loop=1&background=1&controls=0';
            }
        }
        
        return $result;
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
            
            // Extract video URL from embed code if video_type is 'embed'
            $video_url = $banner->video_url;
            if (isset($banner->video_type) && $banner->video_type === 'embed') {
                // Extract src URL from iframe embed code
                if (preg_match('/src=["\'](https?:\/\/[^"\']+)["\']/i', $banner->video_url, $match)) {
                    $video_url = $match[1];
                }
            }
            
            // Override attributes with database values
            $atts['video_url'] = $video_url;
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
        
        // Parse video URL to detect type (YouTube, Vimeo, or Direct)
        // Use player mode (controls visible) for better UX
        $video_data = $this->parse_video_url($atts['video_url'], true);
        
        // Generate unique ID
        $unique_id = 'anmi-vb-' . uniqid();
        
        // Start output buffering
        ob_start();
        ?>
        
        <div class="anmi-video-banner-container <?php echo esc_attr($unique_id); ?> transition-<?php echo esc_attr($atts['transition']); ?>" 
             style="height: <?php echo esc_attr($atts['height']); ?>; position: relative; cursor: pointer;"
             data-autoplay-delay="<?php echo esc_attr($atts['autoplay_delay']); ?>"
             data-mobile-behavior="<?php echo esc_attr($atts['mobile_behavior']); ?>"
             data-slider-speed="<?php echo esc_attr($atts['slider_speed']); ?>"
             data-slider-effect="<?php echo esc_attr($atts['slider_effect']); ?>"
             data-video-type="<?php echo esc_attr($video_data['type']); ?>">
            
            <!-- Image Slider (visible by default) -->
            <?php foreach ($image_urls as $index => $image_url): ?>
                <div class="anmi-banner-image <?php echo $index === 0 ? 'active' : ''; ?>" 
                     style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
                            background-image: url('<?php echo esc_url($image_url); ?>'); 
                            background-size: cover; background-position: center; 
                            opacity: <?php echo $index === 0 ? '1' : '0'; ?>; 
                            transition: opacity 0.8s ease; z-index: 2;"></div>
            <?php endforeach; ?>
            
            <!-- Video Background (hidden by default) -->
            <?php if ($video_data['type'] === 'youtube' || $video_data['type'] === 'vimeo'): ?>
                <iframe class="anmi-banner-video anmi-banner-iframe" 
                        src="<?php echo esc_url($video_data['embed_url']); ?>"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen></iframe>
            <?php else: ?>
                <video class="anmi-banner-video" 
                       loop 
                       muted 
                       playsinline 
                       preload="metadata"
                       poster="<?php echo esc_url($image_urls[0]); ?>">
                    <source src="<?php echo esc_url($video_data['embed_url']); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php endif; ?>
            
            
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
            
            <?php if (count($image_urls) > 1): ?>
            <!-- Slider Navigation Dots -->
            <div class="anmi-banner-dots" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 10;">
                <?php foreach ($image_urls as $index => $image_url): ?>
                    <span class="anmi-banner-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                          data-slide="<?php echo $index; ?>"
                          style="width: 12px; height: 12px; border-radius: 50%; background: <?php echo $index === 0 ? '#fff' : 'rgba(255,255,255,0.5)'; ?>; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Play Button Overlay -->
            <div class="anmi-play-overlay" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 5; pointer-events: none;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.9); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                </div>
            </div>
            
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
