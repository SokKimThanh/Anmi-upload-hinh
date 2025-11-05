<?php
/**
 * Plugin Name: AnMi Banner Video Pro
 * Plugin URI: https://anmitools.com
 * Description: Professional video banner system with advanced controls - YouTube/Vimeo/MP4 support + Image Slider + Modal Preview + Full Video Settings
 * Version: 2.2.0
 * Author: An Mi Tools Technical Team
 * Author URI: https://anmitools.com
 * License: GPL v2 or later
 * Text Domain: anmi-banner-video-pro
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ABVP_VERSION', '2.2.0');
define('ABVP_PLUGIN_FILE', __FILE__);
define('ABVP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ABVP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ABVP_TABLE_NAME', 'anmi_banner_video_pro');

// Include admin panel
require_once ABVP_PLUGIN_PATH . 'includes/admin-panel.php';

// Initialize admin features after plugins are loaded
add_action('plugins_loaded', function() {
    if (is_admin()) {
        AnMi_Banner_Video_Pro_Admin::get_instance();
    }
});

// Activation hook
register_activation_hook(__FILE__, 'abvp_plugin_activate');

function abvp_plugin_activate() {
    $admin = AnMi_Banner_Video_Pro_Admin::get_instance();
    $admin->setup_database_table();
    error_log('AnMi Banner Video Pro v' . ABVP_VERSION . ' activated');
}

class AnMi_Banner_Video_Pro {
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('anmi_banner_video_pro', array($this, 'render_video_banner'));
        add_action('elementor/widgets/register', array($this, 'register_elementor_widget'));
    }
    
    public function enqueue_assets() {
        wp_enqueue_style('abvp-banner-style', ABVP_PLUGIN_URL . 'assets/css/video-banner.css', array(), ABVP_VERSION);
        wp_enqueue_script('abvp-banner-script', ABVP_PLUGIN_URL . 'assets/js/video-banner.js', array('jquery'), ABVP_VERSION, true);
    }
    
    private function parse_video_url($url, $player_mode = false, $banner = null) {
        $autoplay = isset($banner->enable_autoplay) ? $banner->enable_autoplay : 1;
        $muted = isset($banner->enable_muted) ? $banner->enable_muted : 1;
        $loop = isset($banner->enable_loop) ? $banner->enable_loop : 1;
        $controls = isset($banner->enable_controls) ? $banner->enable_controls : 1;
        $modestbranding = isset($banner->enable_modestbranding) ? $banner->enable_modestbranding : 1;
        $rel = isset($banner->enable_rel) ? $banner->enable_rel : 0;
        
        $result = array('type' => 'direct', 'embed_url' => $url, 'video_id' => null);
        
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})(?:[?&]|$)/i', $url, $match)) {
            $result['type'] = 'youtube';
            $result['video_id'] = $match[1];
            
            if ($player_mode) {
                $result['embed_url'] = 'https://www.youtube.com/embed/' . $match[1] . '?controls=1&rel=' . $rel . '&modestbranding=' . $modestbranding . '&playsinline=1';
            } else {
                $result['embed_url'] = 'https://www.youtube.com/embed/' . $match[1] . '?autoplay=' . $autoplay . '&mute=' . $muted . '&loop=' . $loop . '&playlist=' . $match[1] . '&controls=' . $controls . '&showinfo=0&rel=' . $rel . '&modestbranding=' . $modestbranding . '&playsinline=1';
            }
        }
        elseif (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)(?:$|\/|\?)/i', $url, $match)) {
            $result['type'] = 'vimeo';
            $result['video_id'] = $match[1];
            
            if ($player_mode) {
                $result['embed_url'] = 'https://player.vimeo.com/video/' . $match[1] . '?controls=1';
            } else {
                $result['embed_url'] = 'https://player.vimeo.com/video/' . $match[1] . '?autoplay=' . $autoplay . '&muted=' . $muted . '&loop=' . $loop . '&background=' . ($controls ? 0 : 1) . '&controls=' . $controls;
            }
        }
        
        return $result;
    }
    
    public function render_video_banner($atts) {
        $atts = shortcode_atts(array(
            'id' => '', 'video_url' => '', 'images' => '', 'image_url' => '', 'height' => '600px',
            'title' => '', 'subtitle' => '', 'button_text' => '', 'button_link' => '#',
            'show_title' => '0', 'show_subtitle' => '0', 'show_button' => '0',
            'transition' => 'fade', 'mobile_behavior' => 'image', 'autoplay_delay' => '0',
            'slider_speed' => '3000', 'slider_effect' => 'fade',
        ), $atts, 'anmi_banner_video_pro');
        
        $banner = null;
        
        if (!empty($atts['id'])) {
            $banner = AnMi_Banner_Video_Pro_Admin::fetch_banner_by_id(intval($atts['id']));
            if (!$banner) return '<p style=\"color:red;\">Error: Banner not found!</p>';
            
            $video_url = $banner->video_url_value;
            if (isset($banner->video_input_type) && $banner->video_input_type === 'embed') {
                if (preg_match('/src=["\'](https?:\/\/[^"\']+)["\']/i', $banner->video_url_value, $match)) {
                    $video_url = $match[1];
                }
            }
            
            $atts['video_url'] = $video_url;
            $atts['images'] = $banner->image_list;
            $atts['height'] = $banner->banner_height;
            $atts['title'] = $banner->banner_title;
            $atts['subtitle'] = $banner->banner_subtitle;
            $atts['button_text'] = $banner->cta_button_text;
            $atts['button_link'] = $banner->cta_button_link;
            $atts['show_title'] = isset($banner->display_title) ? $banner->display_title : 0;
            $atts['show_subtitle'] = isset($banner->display_subtitle) ? $banner->display_subtitle : 0;
            $atts['show_button'] = isset($banner->display_button) ? $banner->display_button : 0;
            $atts['transition'] = $banner->transition_effect;
            $atts['mobile_behavior'] = $banner->mobile_display_mode;
            $atts['autoplay_delay'] = $banner->video_autoplay_delay;
            $atts['slider_speed'] = $banner->image_slider_speed;
            $atts['slider_effect'] = $banner->image_slider_effect;
        }
        
        $image_urls = array();
        if (!empty($atts['images'])) {
            if (is_string($atts['images']) && strpos($atts['images'], '[') === 0) {
                $image_urls = json_decode($atts['images'], true);
            } else {
                $image_urls = array_map('trim', explode(',', $atts['images']));
            }
        } elseif (!empty($atts['image_url'])) {
            $image_urls = array($atts['image_url']);
        }
        
        if (empty($atts['video_url']) || empty($image_urls)) {
            return '<p style="color:red;">Error: Video URL and image are required!</p>';
        }
        
        $video_data = $this->parse_video_url($atts['video_url'], true, $banner);
        $unique_id = 'abvp-banner-' . uniqid();
        
        ob_start();
        include ABVP_PLUGIN_PATH . 'templates/banner-output.php';
        return ob_get_clean();
    }
    
    public function register_elementor_widget($widgets_manager) {
        if (!class_exists('Elementor\Widget_Base')) return;
        require_once ABVP_PLUGIN_PATH . 'includes/elementor-widget.php';
        $widgets_manager->register(new \AnMi_Banner_Video_Pro_Elementor_Widget());
    }
}

AnMi_Banner_Video_Pro::get_instance();
