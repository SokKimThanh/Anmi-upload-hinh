<?php<?php

/**/**

 * Plugin Name: AnMi Banner Video Pro * Plugin Name: AnMi Banner Video Pro

 * Plugin URI: https://anmitools.com * Plugin URI: https://anmitools.com

 * Description: Professional video banner system with advanced controls - YouTube/Vimeo/MP4 support + Image Slider + Modal Preview + Full Video Settings * Description: Video banner với slider tự động + Admin CRUD panel - YouTube/Vimeo/MP4 support + Iframe Embed + Modal Preview + Video Settings Control

 * Version: 2.1.0 * Version: 2.0.1

 * Author: An Mi Tools Technical Team * Author: An Mi Tools Technical Team

 * Author URI: https://anmitools.com * Author URI: https://anmitools.com

 * License: GPL v2 or later * License: GPL v2 or later

 * Text Domain: anmi-banner-video-pro * Text Domain: anmi-banner-video-pro

 * Requires at least: 5.0 * Requires at least: 5.0

 * Requires PHP: 7.2 * Requires PHP: 7.2

 */ */



// Prevent direct access// Prevent direct access

if (!defined('ABSPATH')) {if (!defined('ABSPATH')) {

    exit;    exit;

}}



// Define plugin constants// Define plugin constants

define('ABVP_VERSION', '2.1.0');define('ANMI_VIDEO_BANNER_VERSION', '2.0.0');

define('ABVP_PLUGIN_FILE', __FILE__);define('ANMI_VIDEO_BANNER_FILE', __FILE__);

define('ABVP_PLUGIN_PATH', plugin_dir_path(__FILE__));define('ANMI_VIDEO_BANNER_PATH', plugin_dir_path(__FILE__));

define('ABVP_PLUGIN_URL', plugin_dir_url(__FILE__));define('ANMI_VIDEO_BANNER_URL', plugin_dir_url(__FILE__));

define('ABVP_TABLE_NAME', 'anmi_banner_video_pro');

// Include admin panel

// Include admin panelrequire_once ANMI_VIDEO_BANNER_PATH . 'includes/admin-panel.php';

require_once ABVP_PLUGIN_PATH . 'includes/admin-panel.php';

// Activation hook - Force database migration

// Activation hook - Force database migrationregister_activation_hook(__FILE__, 'anmi_video_banner_activate');

register_activation_hook(__FILE__, 'abvp_plugin_activate');

function anmi_video_banner_activate() {

function abvp_plugin_activate() {    // Initialize admin panel to create/update database

    // Initialize admin panel to create/update database    $admin = AnMi_Video_Banner_Admin::get_instance();

    $admin = AnMi_Banner_Video_Pro_Admin::get_instance();    $admin->create_database_table();

    $admin->setup_database_table();    

        // Log activation

    // Log activation    error_log('AnMi Video Banner Pro v' . ANMI_VIDEO_BANNER_VERSION . ' activated - Database migrated');

    error_log('AnMi Banner Video Pro v' . ABVP_VERSION . ' activated - Database initialized');}

}

class AnMi_Video_Banner {

class AnMi_Banner_Video_Pro {    

        private static $instance = null;

    private static $instance = null;    

        public static function get_instance() {

    public static function get_instance() {        if (self::$instance == null) {

        if (self::$instance == null) {            self::$instance = new self();

            self::$instance = new self();        }

        }        return self::$instance;

        return self::$instance;    }

    }    

        private function __construct() {

    private function __construct() {        // Enqueue scripts and styles

        // Enqueue scripts and styles        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));

        add_action('wp_enqueue_scripts', array($this, 'load_frontend_assets'));        

                // Register shortcode

        // Register shortcode        add_shortcode('anmi_video_banner', array($this, 'render_video_banner'));

        add_shortcode('anmi_banner_video_pro', array($this, 'display_video_banner'));        

                // Add Elementor widget (if Elementor is active)

        // Add Elementor widget (if Elementor is active)        add_action('elementor/widgets/register', array($this, 'register_elementor_widget'));

        add_action('elementor/widgets/register', array($this, 'init_elementor_widget'));    }

    }    

        /**

    /**     * Enqueue CSS and JavaScript

     * Enqueue CSS and JavaScript     */

     */    public function enqueue_assets() {

    public function load_frontend_assets() {        // CSS

        // CSS        wp_enqueue_style(

        wp_enqueue_style(            'anmi-video-banner-style',

            'abvp-banner-style',            plugin_dir_url(__FILE__) . 'assets/css/video-banner.css',

            plugin_dir_url(__FILE__) . 'assets/css/video-banner.css',            array(),

            array(),            '1.0.0'

            ABVP_VERSION        );

        );        

                // JavaScript

        // JavaScript        wp_enqueue_script(

        wp_enqueue_script(            'anmi-video-banner-script',

            'abvp-banner-script',            plugin_dir_url(__FILE__) . 'assets/js/video-banner.js',

            plugin_dir_url(__FILE__) . 'assets/js/video-banner.js',            array('jquery'),

            array('jquery'),            '1.0.0',

            ABVP_VERSION,            true

            true        );

        );    }

    }    

        /**

    /**     * Detect video type and convert URL to embed format

     * Detect video type and convert URL to embed format     * 

     *      * @param string $url Video URL (YouTube, Vimeo, or direct MP4)

     * @param string $url Video URL (YouTube, Vimeo, or direct MP4)     * @param bool $player_mode Enable player controls (default: false for background mode)

     * @param bool $player_mode Enable player controls (default: false for background mode)     * @param object|null $banner Banner object with video settings (optional)

     * @param object|null $banner_obj Banner object with video settings (optional)     * @return array ['type' => 'youtube|vimeo|direct', 'embed_url' => '...', 'video_id' => '...']

     * @return array ['type' => 'youtube|vimeo|direct', 'embed_url' => '...', 'video_id' => '...']     */

     */    private function parse_video_url($url, $player_mode = false, $banner = null) {

    private function convert_video_url($url, $player_mode = false, $banner_obj = null) {        // Get video settings from banner object or use defaults

        // Get video settings from banner object or use defaults        $autoplay = isset($banner->video_autoplay) ? $banner->video_autoplay : 1;

        $enable_autoplay = isset($banner_obj->enable_autoplay) ? $banner_obj->enable_autoplay : 1;        $muted = isset($banner->video_muted) ? $banner->video_muted : 1;

        $enable_muted = isset($banner_obj->enable_muted) ? $banner_obj->enable_muted : 1;        $loop = isset($banner->video_loop) ? $banner->video_loop : 1;

        $enable_loop = isset($banner_obj->enable_loop) ? $banner_obj->enable_loop : 1;        $controls = isset($banner->video_controls) ? $banner->video_controls : 1;

        $enable_controls = isset($banner_obj->enable_controls) ? $banner_obj->enable_controls : 1;        $modestbranding = isset($banner->video_modestbranding) ? $banner->video_modestbranding : 1;

        $enable_modestbranding = isset($banner_obj->enable_modestbranding) ? $banner_obj->enable_modestbranding : 1;        $rel = isset($banner->video_rel) ? $banner->video_rel : 0;

        $enable_rel = isset($banner_obj->enable_rel) ? $banner_obj->enable_rel : 0;        

                $result = array(

        $video_info = array(            'type' => 'direct',

            'type' => 'direct',            'embed_url' => $url,

            'embed_url' => $url,            'video_id' => null

            'video_id' => null        );

        );        

                // YouTube detection (supports regular URLs, youtu.be with query params, and iframe embed codes)

        // YouTube detection (supports regular URLs, youtu.be with query params, and iframe embed codes)        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})(?:[?&]|$)/i', $url, $match)) {

        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})(?:[?&]|$)/i', $url, $match)) {            $result['type'] = 'youtube';

            $video_info['type'] = 'youtube';            $result['video_id'] = $match[1];

            $video_info['video_id'] = $match[1];            

                        if ($player_mode) {

            if ($player_mode) {                // Player mode: Show controls, no autoplay, allow user interaction

                // Player mode: Show controls, no autoplay, allow user interaction                $result['embed_url'] = 'https://www.youtube.com/embed/' . $match[1] . '?controls=1&rel=' . $rel . '&modestbranding=' . $modestbranding . '&playsinline=1';

                $video_info['embed_url'] = 'https://www.youtube.com/embed/' . $match[1] . '?controls=1&rel=' . $enable_rel . '&modestbranding=' . $enable_modestbranding . '&playsinline=1';            } else {

            } else {                // Background mode: Use user settings

                // Background mode: Use user settings                $result['embed_url'] = 'https://www.youtube.com/embed/' . $match[1] . 

                $video_info['embed_url'] = 'https://www.youtube.com/embed/' . $match[1] .                     '?autoplay=' . $autoplay .

                    '?autoplay=' . $enable_autoplay .                    '&mute=' . $muted .

                    '&mute=' . $enable_muted .                    '&loop=' . $loop .

                    '&loop=' . $enable_loop .                    '&playlist=' . $match[1] .

                    '&playlist=' . $match[1] .                    '&controls=' . $controls .

                    '&controls=' . $enable_controls .                    '&showinfo=0' .

                    '&showinfo=0' .                    '&rel=' . $rel .

                    '&rel=' . $enable_rel .                    '&modestbranding=' . $modestbranding .

                    '&modestbranding=' . $enable_modestbranding .                    '&playsinline=1';

                    '&playsinline=1';            }

            }        }

        }        // Vimeo detection

        // Vimeo detection        elseif (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)(?:$|\/|\?)/i', $url, $match)) {

        elseif (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)(?:$|\/|\?)/i', $url, $match)) {            $result['type'] = 'vimeo';

            $video_info['type'] = 'vimeo';            $result['video_id'] = $match[1];

            $video_info['video_id'] = $match[1];            

                        if ($player_mode) {

            if ($player_mode) {                // Player mode: Show controls

                // Player mode: Show controls                $result['embed_url'] = 'https://player.vimeo.com/video/' . $match[1] . '?controls=1';

                $video_info['embed_url'] = 'https://player.vimeo.com/video/' . $match[1] . '?controls=1';            } else {

            } else {                // Background mode: Use user settings

                // Background mode: Use user settings                $result['embed_url'] = 'https://player.vimeo.com/video/' . $match[1] . 

                $video_info['embed_url'] = 'https://player.vimeo.com/video/' . $match[1] .                     '?autoplay=' . $autoplay .

                    '?autoplay=' . $enable_autoplay .                    '&muted=' . $muted .

                    '&muted=' . $enable_muted .                    '&loop=' . $loop .

                    '&loop=' . $enable_loop .                    '&background=' . ($controls ? 0 : 1) .

                    '&background=' . ($enable_controls ? 0 : 1) .                    '&controls=' . $controls;

                    '&controls=' . $enable_controls;            }

            }        }

        }        

                return $result;

        return $video_info;    }

    }    

        /**

    /**     * Render video banner shortcode

     * Render video banner shortcode     * 

     *      * Usage: [anmi_video_banner id="1"] OR [anmi_video_banner video_url="..." images="..."]

     * Usage: [anmi_banner_video_pro id="1"] OR [anmi_banner_video_pro video_url="..." images="..."]     */

     */    public function render_video_banner($atts) {

    public function display_video_banner($atts) {        $atts = shortcode_atts(array(

        $atts = shortcode_atts(array(            'id' => '', // Banner ID from database

            'id' => '', // Banner ID from database            'video_url' => '',

            'video_url' => '',            'images' => '', // Comma-separated image URLs for slider

            'images' => '', // Comma-separated image URLs for slider            'image_url' => '', // Backward compatibility - single image

            'image_url' => '', // Backward compatibility - single image            'height' => '600px',

            'height' => '600px',            'title' => '',

            'title' => '',            'subtitle' => '',

            'subtitle' => '',            'button_text' => '',

            'button_text' => '',            'button_link' => '#',

            'button_link' => '#',            'show_title' => '0',

            'show_title' => '0',            'show_subtitle' => '0',

            'show_subtitle' => '0',            'show_button' => '0',

            'show_button' => '0',            'transition' => 'fade', // fade, slide, zoom, blur

            'transition' => 'fade', // fade, slide, zoom, blur            'mobile_behavior' => 'image', // image, video, both

            'mobile_behavior' => 'image', // image, video, both            'autoplay_delay' => '0', // delay in seconds before video plays on hover

            'autoplay_delay' => '0', // delay in seconds before video plays on hover            'slider_speed' => '3000', // milliseconds between slides

            'slider_speed' => '3000', // milliseconds between slides            'slider_effect' => 'fade', // fade, slide

            'slider_effect' => 'fade', // fade, slide        ), $atts, 'anmi_video_banner');

        ), $atts, 'anmi_banner_video_pro');        

                // Initialize banner object

        // Initialize banner object        $banner = null;

        $banner_obj = null;        

                // If ID is provided, load banner from database

        // If ID is provided, load banner from database        if (!empty($atts['id'])) {

        if (!empty($atts['id'])) {            $banner = AnMi_Video_Banner_Admin::get_banner(intval($atts['id']));

            $banner_obj = AnMi_Banner_Video_Pro_Admin::fetch_banner_by_id(intval($atts['id']));            

                        if (!$banner) {

            if (!$banner_obj) {                return '<p style="color:red;">Error: Banner not found!</p>';

                return '<p style="color:red;">Error: Banner not found!</p>';            }

            }            

                        // Extract video URL from embed code if video_type is 'embed'

            // Extract video URL from embed code if video_input_type is 'embed'            $video_url = $banner->video_url;

            $video_url_value = $banner_obj->video_url_value;            if (isset($banner->video_type) && $banner->video_type === 'embed') {

            if (isset($banner_obj->video_input_type) && $banner_obj->video_input_type === 'embed') {                // Extract src URL from iframe embed code

                // Extract src URL from iframe embed code                if (preg_match('/src=["\'](https?:\/\/[^"\']+)["\']/i', $banner->video_url, $match)) {

                if (preg_match('/src=["\'](https?:\/\/[^"\']+)["\']/i', $banner_obj->video_url_value, $match)) {                    $video_url = $match[1];

                    $video_url_value = $match[1];                }

                }            }

            }            

                        // Override attributes with database values

            // Override attributes with database values            $atts['video_url'] = $video_url;

            $atts['video_url'] = $video_url_value;            $atts['images'] = $banner->images;

            $atts['images'] = $banner_obj->image_list;            $atts['height'] = $banner->height;

            $atts['height'] = $banner_obj->banner_height;            $atts['title'] = $banner->title;

            $atts['title'] = $banner_obj->banner_title;            $atts['subtitle'] = $banner->subtitle;

            $atts['subtitle'] = $banner_obj->banner_subtitle;            $atts['button_text'] = $banner->button_text;

            $atts['button_text'] = $banner_obj->cta_button_text;            $atts['button_link'] = $banner->button_link;

            $atts['button_link'] = $banner_obj->cta_button_link;            $atts['show_title'] = isset($banner->show_title) ? $banner->show_title : 0;

            $atts['show_title'] = isset($banner_obj->display_title) ? $banner_obj->display_title : 0;            $atts['show_subtitle'] = isset($banner->show_subtitle) ? $banner->show_subtitle : 0;

            $atts['show_subtitle'] = isset($banner_obj->display_subtitle) ? $banner_obj->display_subtitle : 0;            $atts['show_button'] = isset($banner->show_button) ? $banner->show_button : 0;

            $atts['show_button'] = isset($banner_obj->display_button) ? $banner_obj->display_button : 0;            $atts['transition'] = $banner->transition;

            $atts['transition'] = $banner_obj->transition_effect;            $atts['mobile_behavior'] = $banner->mobile_behavior;

            $atts['mobile_behavior'] = $banner_obj->mobile_display_mode;            $atts['autoplay_delay'] = $banner->autoplay_delay;

            $atts['autoplay_delay'] = $banner_obj->video_autoplay_delay;            $atts['slider_speed'] = $banner->slider_speed;

            $atts['slider_speed'] = $banner_obj->image_slider_speed;            $atts['slider_effect'] = $banner->slider_effect;

            $atts['slider_effect'] = $banner_obj->image_slider_effect;        }

        }        

                // Parse images - support both comma-separated and single image

        // Parse images - support both comma-separated and single image        $image_urls = array();

        $image_list = array();        if (!empty($atts['images'])) {

        if (!empty($atts['images'])) {            // Check if it's JSON from database

            // Check if it's JSON from database            if (is_string($atts['images']) && strpos($atts['images'], '[') === 0) {

            if (is_string($atts['images']) && strpos($atts['images'], '[') === 0) {                $image_urls = json_decode($atts['images'], true);

                $image_list = json_decode($atts['images'], true);            } else {

            } else {                $image_urls = array_map('trim', explode(',', $atts['images']));

                $image_list = array_map('trim', explode(',', $atts['images']));            }

            }        } elseif (!empty($atts['image_url'])) {

        } elseif (!empty($atts['image_url'])) {            $image_urls = array($atts['image_url']);

            $image_list = array($atts['image_url']);        }

        }        

                // Validate required fields

        // Validate required fields        if (empty($atts['video_url']) || empty($image_urls)) {

        if (empty($atts['video_url']) || empty($image_list)) {            return '<p style="color:red;">Error: Video URL and at least one image are required!</p>';

            return '<p style="color:red;">Error: Video URL and at least one image are required!</p>';        }

        }        

                // Parse video URL to detect type (YouTube, Vimeo, or Direct)

        // Parse video URL to detect type (YouTube, Vimeo, or Direct)        // Use player mode (controls visible) for better UX

        // Use player mode (controls visible) for better UX        // Pass $banner object to use video settings

        // Pass $banner_obj object to use video settings        $video_data = $this->parse_video_url($atts['video_url'], true, $banner);

        $video_info = $this->convert_video_url($atts['video_url'], true, $banner_obj);        

                // Generate unique ID

        // Generate unique ID        $unique_id = 'anmi-vb-' . uniqid();

        $banner_uid = 'abvp-banner-' . uniqid();        

                // Start output buffering

        // Start output buffering        ob_start();

        ob_start();        ?>

        ?>        

                <div class="anmi-video-banner-container <?php echo esc_attr($unique_id); ?> transition-<?php echo esc_attr($atts['transition']); ?>" 

        <div class="abvp-banner-wrapper <?php echo esc_attr($banner_uid); ?> effect-<?php echo esc_attr($atts['transition']); ?>"              style="height: <?php echo esc_attr($atts['height']); ?>; position: relative; cursor: pointer;"

             style="height: <?php echo esc_attr($atts['height']); ?>; position: relative; cursor: pointer;"             data-autoplay-delay="<?php echo esc_attr($atts['autoplay_delay']); ?>"

             data-autoplay-delay="<?php echo esc_attr($atts['autoplay_delay']); ?>"             data-mobile-behavior="<?php echo esc_attr($atts['mobile_behavior']); ?>"

             data-mobile-behavior="<?php echo esc_attr($atts['mobile_behavior']); ?>"             data-slider-speed="<?php echo esc_attr($atts['slider_speed']); ?>"

             data-slider-speed="<?php echo esc_attr($atts['slider_speed']); ?>"             data-slider-effect="<?php echo esc_attr($atts['slider_effect']); ?>"

             data-slider-effect="<?php echo esc_attr($atts['slider_effect']); ?>"             data-video-type="<?php echo esc_attr($video_data['type']); ?>">

             data-video-type="<?php echo esc_attr($video_info['type']); ?>">            

                        <!-- Image Slider (visible by default) -->

            <!-- Image Slider (visible by default) -->            <?php foreach ($image_urls as $index => $image_url): ?>

            <?php foreach ($image_list as $img_index => $img_url): ?>                <div class="anmi-banner-image <?php echo $index === 0 ? 'active' : ''; ?>" 

                <div class="abvp-slide-image <?php echo $img_index === 0 ? 'active' : ''; ?>"                      style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; 

                     style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;                             background-image: url('<?php echo esc_url($image_url); ?>'); 

                            background-image: url('<?php echo esc_url($img_url); ?>');                             background-size: cover; background-position: center; 

                            background-size: cover; background-position: center;                             opacity: <?php echo $index === 0 ? '1' : '0'; ?>; 

                            opacity: <?php echo $img_index === 0 ? '1' : '0'; ?>;                             transition: opacity 0.8s ease; z-index: 2;"></div>

                            transition: opacity 0.8s ease; z-index: 2;"></div>            <?php endforeach; ?>

            <?php endforeach; ?>            

                        <!-- Video Background (hidden by default) -->

            <!-- Video Background (hidden by default) -->            <?php if ($video_data['type'] === 'youtube' || $video_data['type'] === 'vimeo'): ?>

            <?php if ($video_info['type'] === 'youtube' || $video_info['type'] === 'vimeo'): ?>                <iframe class="anmi-banner-video anmi-banner-iframe" 

                <iframe class="abvp-video-frame abvp-video-iframe"                         src="<?php echo esc_url($video_data['embed_url']); ?>"

                        src="<?php echo esc_url($video_info['embed_url']); ?>"                        frameborder="0" 

                        frameborder="0"                         allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 

                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"                         allowfullscreen></iframe>

                        allowfullscreen></iframe>            <?php else: ?>

            <?php else: ?>                <video class="anmi-banner-video" 

                <video class="abvp-video-frame"                        loop 

                       loop                        muted 

                       muted                        playsinline 

                       playsinline                        preload="metadata"

                       preload="metadata"                       poster="<?php echo esc_url($image_urls[0]); ?>">

                       poster="<?php echo esc_url($image_list[0]); ?>">                    <source src="<?php echo esc_url($video_data['embed_url']); ?>" type="video/mp4">

                    <source src="<?php echo esc_url($video_info['embed_url']); ?>" type="video/mp4">                    Your browser does not support the video tag.

                    Your browser does not support the video tag.                </video>

                </video>            <?php endif; ?>

            <?php endif; ?>            

                        <!-- Volume Control Button (hidden by default, shown by JS when video plays) -->

            <!-- Volume Control Button (hidden by default, shown by JS when video plays) -->            <button class="anmi-volume-control" title="Unmute video">

            <button class="abvp-audio-toggle" title="Unmute video">                <svg width="24" height="24" viewBox="0 0 24 24" fill="white" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">

                <svg width="24" height="24" viewBox="0 0 24 24" fill="white" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">                    <!-- Muted icon (default) -->

                    <!-- Muted icon (default) -->                    <path class="volume-icon-muted" d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>

                    <path class="abvp-icon-muted" d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>                    

                                        <!-- Unmuted icon (hidden initially) -->

                    <!-- Unmuted icon (hidden initially) -->                    <path class="volume-icon-unmuted" style="display: none;" d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>

                    <path class="abvp-icon-unmuted" style="display: none;" d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>                </svg>

                </svg>            </button>

            </button>            

                        <?php if (!empty($atts['title']) || !empty($atts['subtitle']) || !empty($atts['button_text'])): ?>

            <?php if (!empty($atts['title']) || !empty($atts['subtitle']) || !empty($atts['button_text'])): ?>            <!-- Content Overlay -->

            <!-- Content Overlay -->            <div class="anmi-banner-content"

            <div class="abvp-content-overlay"                 data-show-title="<?php echo esc_attr($atts['show_title']); ?>"

                 data-show-title="<?php echo esc_attr($atts['show_title']); ?>"                 data-show-subtitle="<?php echo esc_attr($atts['show_subtitle']); ?>"

                 data-show-subtitle="<?php echo esc_attr($atts['show_subtitle']); ?>"                 data-show-button="<?php echo esc_attr($atts['show_button']); ?>">

                 data-show-button="<?php echo esc_attr($atts['show_button']); ?>">                <?php if (!empty($atts['title']) && $atts['show_title']): ?>

                <?php if (!empty($atts['title']) && $atts['show_title']): ?>                    <h1 class="anmi-banner-title"><?php echo esc_html($atts['title']); ?></h1>

                    <h1 class="abvp-title-text"><?php echo esc_html($atts['title']); ?></h1>                <?php endif; ?>

                <?php endif; ?>                

                                <?php if (!empty($atts['subtitle']) && $atts['show_subtitle']): ?>

                <?php if (!empty($atts['subtitle']) && $atts['show_subtitle']): ?>                    <p class="anmi-banner-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>

                    <p class="abvp-subtitle-text"><?php echo esc_html($atts['subtitle']); ?></p>                <?php endif; ?>

                <?php endif; ?>                

                                <?php if (!empty($atts['button_text']) && $atts['show_button']): ?>

                <?php if (!empty($atts['button_text']) && $atts['show_button']): ?>                    <a href="<?php echo esc_url($atts['button_link']); ?>" class="anmi-banner-btn">

                    <a href="<?php echo esc_url($atts['button_link']); ?>" class="abvp-cta-button">                        <?php echo esc_html($atts['button_text']); ?>

                        <?php echo esc_html($atts['button_text']); ?>                    </a>

                    </a>                <?php endif; ?>

                <?php endif; ?>            </div>

            </div>            <?php endif; ?>

            <?php endif; ?>            

                        <?php if (count($image_urls) > 1): ?>

            <?php if (count($image_list) > 1): ?>            <!-- Slider Navigation Dots -->

            <!-- Slider Navigation Dots -->            <div class="anmi-banner-dots" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 10;">

            <div class="abvp-nav-dots" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 10;">                <?php foreach ($image_urls as $index => $image_url): ?>

                <?php foreach ($image_list as $dot_index => $img_url): ?>                    <span class="anmi-banner-dot <?php echo $index === 0 ? 'active' : ''; ?>" 

                    <span class="abvp-dot-indicator <?php echo $dot_index === 0 ? 'active' : ''; ?>"                           data-slide="<?php echo $index; ?>"

                          data-slide="<?php echo $dot_index; ?>"                          style="width: 12px; height: 12px; border-radius: 50%; background: <?php echo $index === 0 ? '#fff' : 'rgba(255,255,255,0.5)'; ?>; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>

                          style="width: 12px; height: 12px; border-radius: 50%; background: <?php echo $dot_index === 0 ? '#fff' : 'rgba(255,255,255,0.5)'; ?>; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>                <?php endforeach; ?>

                <?php endforeach; ?>            </div>

            </div>            <?php endif; ?>

            <?php endif; ?>            

                        <!-- Play Button Overlay -->

            <!-- Play Button Overlay -->            <div class="anmi-play-overlay" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 5; pointer-events: none;">

            <div class="abvp-play-icon" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 5; pointer-events: none;">                <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.9); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">

                <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.9); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">                        <polygon points="5 3 19 12 5 21 5 3"></polygon>

                        <polygon points="5 3 19 12 5 21 5 3"></polygon>                    </svg>

                    </svg>                </div>

                </div>            </div>

            </div>            

                        <!-- Loading Spinner -->

            <!-- Loading Spinner -->            <div class="anmi-banner-loader">

            <div class="abvp-loading-spinner">                <div class="spinner"></div>

                <div class="spinner"></div>            </div>

            </div>        </div>

        </div>        

                <?php

        <?php        return ob_get_clean();

        return ob_get_clean();    }

    }    

        /**

    /**     * Register Elementor Widget

     * Register Elementor Widget     */

     */    public function register_elementor_widget($widgets_manager) {

    public function init_elementor_widget($widgets_manager) {        if (!class_exists('Elementor\Widget_Base')) {

        if (!class_exists('Elementor\Widget_Base')) {            return;

            return;        }

        }        

                require_once plugin_dir_path(__FILE__) . 'includes/elementor-widget.php';

        require_once plugin_dir_path(__FILE__) . 'includes/elementor-widget.php';        $widgets_manager->register(new \AnMi_Video_Banner_Elementor_Widget());

        $widgets_manager->register(new \AnMi_Banner_Video_Pro_Elementor_Widget());    }

    }}

}

// Initialize plugin

// Initialize pluginAnMi_Video_Banner::get_instance();

AnMi_Banner_Video_Pro::get_instance();
