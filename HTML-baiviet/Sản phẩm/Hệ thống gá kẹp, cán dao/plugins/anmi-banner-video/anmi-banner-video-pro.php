<?php
/**
 * Plugin Name: AnMi Banner Video Pro
 * Plugin URI: https://anmitools.com
 * Description: Professional video banner system with advanced controls - YouTube/Vimeo/MP4 support + Image Slider + Modal Preview + Full Video Settings + Device-Specific Slider Control
 * Version: 2.5.0
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
define('ABVP_VERSION', '2.5.0');
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
        
        // Auto-update database if version changed
        $installed_version = get_option('abvp_version', '0.0.0');
        if (version_compare($installed_version, ABVP_VERSION, '<')) {
            error_log('AnMi Banner Video Pro: Version changed from ' . $installed_version . ' to ' . ABVP_VERSION . ', updating database...');
            $admin = AnMi_Banner_Video_Pro_Admin::get_instance();
            $admin->setup_database_table();
            update_option('abvp_version', ABVP_VERSION);
            error_log('AnMi Banner Video Pro: Database updated successfully');
        }
    }
});

// Activation hook
register_activation_hook(__FILE__, 'abvp_plugin_activate');

function abvp_plugin_activate() {
    $admin = AnMi_Banner_Video_Pro_Admin::get_instance();
    $admin->setup_database_table();
    update_option('abvp_version', ABVP_VERSION);
    error_log('AnMi Banner Video Pro v' . ABVP_VERSION . ' activated - Database updated');
}

class AnMi_Banner_Video_Pro {
    /**
     * Singleton instance holder.
     *
     * @var self|null
     */
    private static $instance = null;
    
    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('anmi_banner_video_pro', array($this, 'render_video_banner'));
        add_action('elementor/widgets/register', array($this, 'register_elementor_widget'));
    }

    public function enqueue_assets(): void {
        $style_url = ABVP_PLUGIN_URL . 'assets/css/video-banner.css';
        $script_url = ABVP_PLUGIN_URL . 'assets/js/video-banner.js';

        wp_register_style('anmi-video-banner-style', $style_url, array(), ABVP_VERSION);
        wp_register_style('abvp-banner-style', $style_url, array(), ABVP_VERSION);

        wp_register_script('anmi-video-banner-script', $script_url, array('jquery'), ABVP_VERSION, true);
        wp_register_script('abvp-banner-script', $script_url, array('jquery'), ABVP_VERSION, true);

        wp_enqueue_style('anmi-video-banner-style');
        wp_enqueue_script('anmi-video-banner-script');
        
        // Enqueue WordPress core MediaElement.js for video widget compatibility
        wp_enqueue_style('wp-mediaelement');
        wp_enqueue_script('wp-mediaelement');
    }
    
    /**
     * Prepare embed metadata for supported video providers.
     */
    private function parse_video_url(string $url, ?object $banner = null): array {
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
            
            $query_args = array(
                'autoplay' => (int) $autoplay,
                'mute' => (int) $muted,
                'loop' => (int) $loop,
                'controls' => (int) $controls,
                'showinfo' => 0,
                'rel' => (int) $rel,
                'modestbranding' => (int) $modestbranding,
                'playsinline' => 1,
                'playlist' => $loop ? $match[1] : null
            );

            $result['embed_url'] = 'https://www.youtube.com/embed/' . $match[1] . '?' . $this->build_query_string($query_args);
        }
        elseif (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)(?:$|\/|\?)/i', $url, $match)) {
            $result['type'] = 'vimeo';
            $result['video_id'] = $match[1];
            
            $query_args = array(
                'autoplay' => (int) $autoplay,
                'muted' => (int) $muted,
                'loop' => (int) $loop,
                'controls' => (int) $controls,
                'background' => $controls ? 0 : 1
            );

            $result['embed_url'] = 'https://player.vimeo.com/video/' . $match[1] . '?' . $this->build_query_string($query_args);
        }
        
        return $result;
    }

    private function build_query_string(array $args): string {
        $parts = array();
        foreach ($args as $key => $value) {
            if ($value === null) {
                continue;
            }
            $parts[] = rawurlencode($key) . '=' . rawurlencode($value);
        }
        return implode('&', $parts);
    }

    /**
     * Render video using WordPress core for better mobile compatibility.
     * Supports ALL video types via WordPress oEmbed and media widget.
     * 
     * @param string $video_url Video URL (MP4, YouTube, Vimeo, etc.)
     * @param string $video_type Video type: 'direct', 'youtube', 'vimeo', etc.
     * @param array $settings Video settings from plugin
     * @param string $poster_url Poster/fallback image URL
     * @param array $elementor_args Additional Elementor-specific args
     * @return string HTML output with custom wrapper
     */
    public function render_wp_core_video(string $video_url, string $video_type, array $settings, string $poster_url = '', array $elementor_args = array()): string {
        if (empty($video_url)) {
            return '';
        }

        // Extract Elementor settings
        $aspect_ratio = isset($elementor_args['aspect_ratio']) ? $elementor_args['aspect_ratio'] : '16:9';
        $show_overlay = isset($elementor_args['show_image_overlay']) ? $elementor_args['show_image_overlay'] : 'yes';
        $overlay_image = isset($elementor_args['image_overlay']) ? $elementor_args['image_overlay'] : $poster_url;
        $show_play_icon = isset($elementor_args['show_play_icon']) ? $elementor_args['show_play_icon'] : 'yes';
        $lightbox = isset($elementor_args['lightbox']) ? $elementor_args['lightbox'] : 'no';
        $lazy_load = isset($elementor_args['lazy_load']) ? $elementor_args['lazy_load'] : 'yes';

        // Build custom attributes
        $custom_attrs = array(
            'class' => 'abvp-wp-video-wrapper elementor-video-wrapper',
            'data-video-type' => esc_attr($video_type),
            'data-enable-autoplay' => !empty($settings['enable_autoplay']) ? '1' : '0',
            'data-enable-muted' => !empty($settings['enable_muted']) ? '1' : '0',
            'data-enable-loop' => !empty($settings['enable_loop']) ? '1' : '0',
            'data-poster' => esc_url($overlay_image),
            'data-video-url' => esc_url($video_url),
            'data-aspect-ratio' => esc_attr($aspect_ratio),
        );

        // Start output buffering
        ob_start();
        
        echo '<div ';
        foreach ($custom_attrs as $attr => $value) {
            echo esc_attr($attr) . '="' . esc_attr($value) . '" ';
        }
        echo 'style="position: relative;">';

        // Render video based on type
        if ($video_type === 'youtube' || $video_type === 'vimeo' || $video_type === 'dailymotion' || $video_type === 'videopress') {
            // Use WordPress oEmbed for all embed types
            echo $this->render_oembed_video($video_url, $settings, $elementor_args);
        } else {
            // MP4/Direct video - Use WordPress media widget
            echo $this->render_direct_video($video_url, $settings, $overlay_image, $elementor_args);
        }

        // Add image overlay if enabled
        if ($show_overlay === 'yes' && !empty($overlay_image)) {
            echo '<div class="elementor-custom-embed-image-overlay" style="background-image: url(\'' . esc_url($overlay_image) . '\');">';
            
            // Add play icon if enabled
            if ($show_play_icon === 'yes') {
                echo '<div class="elementor-custom-embed-play" role="button" aria-label="Play Video">';
                echo '<img src="https://anmitools.com/wp-content/uploads/2025/11/play-button.png" alt="Play" class="abvp-play-button-image">';
                echo '</div>';
            }
            
            echo '</div>';
        }

        echo '</div>'; // Close wrapper

        return ob_get_clean();
    }

    /**
     * Render oEmbed video (YouTube, Vimeo, etc.)
     */
    private function render_oembed_video(string $video_url, array $settings, array $elementor_args): string {
        // Build oEmbed args
        $oembed_args = array();
        
        // Add parameters to URL based on settings
        $params = array();
        
        if (!empty($settings['enable_autoplay'])) {
            $params['autoplay'] = '1';
        }
        if (!empty($settings['enable_muted'])) {
            $params['muted'] = '1';
        }
        if (!empty($settings['enable_loop'])) {
            $params['loop'] = '1';
        }
        if (!empty($settings['enable_controls'])) {
            $params['controls'] = '1';
        }
        
        // Add params to URL
        if (!empty($params)) {
            $separator = (strpos($video_url, '?') !== false) ? '&' : '?';
            $video_url .= $separator . http_build_query($params);
        }
        
        // Get WordPress oEmbed
        $oembed_html = wp_oembed_get($video_url, $oembed_args);
        
        if ($oembed_html) {
            // Wrap in container with aspect ratio
            $aspect_ratio = isset($elementor_args['aspect_ratio']) ? $elementor_args['aspect_ratio'] : '16:9';
            $padding = '56.25%'; // Default 16:9
            
            if ($aspect_ratio === '21:9') $padding = '42.857%';
            elseif ($aspect_ratio === '4:3') $padding = '75%';
            elseif ($aspect_ratio === '3:2') $padding = '66.667%';
            elseif ($aspect_ratio === '9:16') $padding = '177.778%';
            elseif ($aspect_ratio === '1:1') $padding = '100%';
            
            return '<div class="elementor-video elementor-fit-aspect-ratio" style="padding-bottom: ' . $padding . ';">' 
                   . '<div class="abvp-oembed-container">' . $oembed_html . '</div>'
                   . '</div>';
        }
        
        return '';
    }

    /**
     * Render direct MP4 video via WP media widget
     */
    private function render_direct_video(string $video_url, array $settings, string $poster_url, array $elementor_args): string {
        $attachment_id = attachment_url_to_postid($video_url);
        
        $instance = array(
            'url' => $video_url,
            'preload' => isset($elementor_args['preload']) ? $elementor_args['preload'] : 'metadata',
            'loop' => !empty($settings['enable_loop']),
            'content' => '',
        );

        if ($attachment_id > 0) {
            $instance['attachment_id'] = $attachment_id;
        }

        ob_start();
        
        if (class_exists('WP_Widget_Media_Video')) {
            the_widget(
                'WP_Widget_Media_Video',
                $instance,
                array(
                    'before_widget' => '<div class="elementor-video">',
                    'after_widget' => '</div>',
                    'before_title' => '',
                    'after_title' => '',
                )
            );
        } else {
            // Fallback
            echo '<video class="elementor-video" style="width: 100%;"';
            if (!empty($settings['enable_loop'])) echo ' loop';
            if (!empty($settings['enable_muted'])) echo ' muted';
            if (!empty($settings['enable_controls'])) echo ' controls';
            echo ' playsinline preload="metadata"';
            if (!empty($poster_url)) echo ' poster="' . esc_url($poster_url) . '"';
            echo '><source src="' . esc_url($video_url) . '" type="video/mp4"></video>';
        }

        return ob_get_clean();
    }

    /**
     * Fallback: No longer needed - removed custom iframe
     */
    private function render_custom_iframe(string $video_url, string $video_type, array $settings): string {
        // This method is deprecated - all videos now use WordPress oEmbed
        return $this->render_oembed_video($video_url, $settings, array());
    }
    
    public function render_video_banner(array $atts = array(), ?string $content = null): string {
        $atts = $this->normalize_shortcode_attributes($atts);

        $banner = null;

        if (!empty($atts['id'])) {
            $banner = $this->load_banner((int) $atts['id']);
            if ($banner === null) {
                return '<p style="color:red;">Error: Banner not found!</p>';
            }
        }

        $atts = $this->apply_banner_settings($atts, $banner);
        $image_urls = $this->extract_image_urls($atts);

        if (empty($atts['video_url']) || empty($image_urls)) {
            return '<p style="color:red;">Error: Video URL and image are required!</p>';
        }

    $banner_settings = $this->resolve_banner_settings($atts, $banner);
    $video_data = $this->parse_video_url($atts['video_url'], $banner_settings);
    $banner = $banner_settings;
        $unique_id = 'abvp-banner-' . uniqid();
        
        ob_start();
        include ABVP_PLUGIN_PATH . 'templates/banner-output.php';
        return ob_get_clean();
    }
    
    /**
     * Register the Elementor widget when Elementor is available.
     */
    public function register_elementor_widget($widgets_manager): void {
        if (!class_exists('Elementor\Widget_Base') || !is_object($widgets_manager)) {
            return;
        }
        require_once ABVP_PLUGIN_PATH . 'includes/elementor-widget.php';
        if (class_exists('AnMi_Video_Banner_Elementor_Widget')) {
            $widgets_manager->register(new \AnMi_Video_Banner_Elementor_Widget());
        }
    }

    /**
     * Normalize shortcode attributes with defaults.
     */
    private function normalize_shortcode_attributes(array $atts): array {
        $defaults = array(
            'id' => '',
            'video_url' => '',
            'images' => '',
            'image_url' => '',
            'height' => '600px',
            'title' => '',
            'subtitle' => '',
            'button_text' => '',
            'button_link' => '#',
            'show_title' => '0',
            'show_subtitle' => '0',
            'show_button' => '0',
            'transition' => 'fade',
            'mobile_behavior' => 'image',
            'autoplay_delay' => '0',
            'slider_speed' => '3000',
            'slider_effect' => 'fade',
            'enable_autoplay' => '1',
            'enable_muted' => '1',
            'enable_loop' => '1',
            'enable_controls' => '1',
            'enable_modestbranding' => '1',
            'enable_rel' => '0',
        );

        return shortcode_atts($defaults, $atts, 'anmi_banner_video_pro');
    }

    /**
     * Merge saved banner configuration onto shortcode attributes.
     */
    private function apply_banner_settings(array $atts, ?object $banner): array {
        if ($banner === null) {
            return $atts;
        }

        $video_url = $banner->video_url_value;
        if (isset($banner->video_input_type) && $banner->video_input_type === 'embed') {
            if (preg_match('/src=["\'](https?:\/\/[^"\']+)["\']/i', $banner->video_url_value, $match)) {
                $video_url = $match[1];
            }
        }

        $atts['video_url'] = $video_url;
        $atts['images'] = $banner->image_list;

        // Check for explicit widget overrides (flagged with _widget_override_*)
        $has_mobile_override = isset($atts['_widget_override_mobile']);
        $has_delay_override = isset($atts['_widget_override_delay']);
        
        // Remove override flags from atts (they're just markers)
        unset($atts['_widget_override_mobile']);
        unset($atts['_widget_override_delay']);
        
        // Map stored values to the attribute keys consumed by the template/JS layer.
        $mapping = array(
            'height' => 'banner_height',
            'title' => 'banner_title',
            'subtitle' => 'banner_subtitle',
            'button_text' => 'cta_button_text',
            'button_link' => 'cta_button_link',
            'show_title' => 'display_title',
            'show_subtitle' => 'display_subtitle',
            'show_button' => 'display_button',
            'transition' => 'transition_effect',
            'mobile_behavior' => 'mobile_display_mode',
            'autoplay_delay' => 'video_autoplay_delay',
            'slider_speed' => 'image_slider_speed',
            'slider_effect' => 'image_slider_effect',
        );

        foreach ($mapping as $attribute_key => $property_name) {
            // Skip if explicitly overridden by widget
            if ($attribute_key === 'mobile_behavior' && $has_mobile_override) {
                continue; // Keep widget value
            }
            if ($attribute_key === 'autoplay_delay' && $has_delay_override) {
                continue; // Keep widget value
            }
            
            // Apply from database
            if (isset($banner->$property_name)) {
                $atts[$attribute_key] = $banner->$property_name;
            }
        }

        $atts['enable_autoplay'] = isset($banner->enable_autoplay) ? (string) $banner->enable_autoplay : $atts['enable_autoplay'];
        $atts['enable_muted'] = isset($banner->enable_muted) ? (string) $banner->enable_muted : $atts['enable_muted'];
        $atts['enable_loop'] = isset($banner->enable_loop) ? (string) $banner->enable_loop : $atts['enable_loop'];
        $atts['enable_controls'] = isset($banner->enable_controls) ? (string) $banner->enable_controls : $atts['enable_controls'];
        $atts['enable_modestbranding'] = isset($banner->enable_modestbranding) ? (string) $banner->enable_modestbranding : $atts['enable_modestbranding'];
        $atts['enable_rel'] = isset($banner->enable_rel) ? (string) $banner->enable_rel : $atts['enable_rel'];

        return $atts;
    }

    /**
     * Extract image URLs from shortcode attributes, supporting JSON or CSV input.
     */
    private function extract_image_urls(array $atts): array {
        $trim_value = static function ($value): string {
            return trim((string) $value);
        };

        if (!empty($atts['images'])) {
            if (is_string($atts['images'])) {
                $images = trim($atts['images']);
                if (strlen($images) > 0 && $images[0] === '[') {
                    $decoded = json_decode($images, true);
                    if (is_array($decoded)) {
                        return array_map($trim_value, $decoded);
                    }
                }

                return array_map($trim_value, explode(',', $images));
            }

            if (is_array($atts['images'])) {
                return array_map($trim_value, $atts['images']);
            }
        }

        if (!empty($atts['image_url'])) {
            return array($trim_value($atts['image_url']));
        }

        return array();
    }

    /**
     * Safely load a banner object by ID.
     */
    private function load_banner(int $banner_id): ?object {
        $banner = AnMi_Banner_Video_Pro_Admin::fetch_banner_by_id($banner_id);

        return $banner ?: null;
    }

    /**
     * Resolve a banner-like settings object for templates and embeds.
     */
    private function resolve_banner_settings(array $atts, ?object $banner): object {
        if ($banner !== null) {
            return $banner;
        }

        $settings = array(
            'enable_autoplay' => (int) $atts['enable_autoplay'],
            'enable_muted' => (int) $atts['enable_muted'],
            'enable_loop' => (int) $atts['enable_loop'],
            'enable_controls' => (int) $atts['enable_controls'],
            'enable_modestbranding' => (int) $atts['enable_modestbranding'],
            'enable_rel' => (int) $atts['enable_rel'],
            'video_autoplay_delay' => isset($atts['autoplay_delay']) ? (int) $atts['autoplay_delay'] : 0,
            'image_slider_speed' => isset($atts['slider_speed']) ? (int) $atts['slider_speed'] : 3000,
            'image_slider_effect' => $atts['slider_effect'] ?? 'fade',
            'mobile_display_mode' => $atts['mobile_behavior'] ?? 'image',
        );

        return (object) $settings;
    }
}

AnMi_Banner_Video_Pro::get_instance();
