<?php
/**
 * Banner Output Template
 * 
 * Available variables:
 * - $atts: Shortcode attributes array
 * - $video_data: Parsed video data array ['type', 'embed_url', 'video_id']
 * - $image_urls: Array of image URLs
 * - $unique_id: Unique banner ID
 * - $banner: Banner object from database (if loaded by ID)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$enable_autoplay  = isset($banner->enable_autoplay) ? (int) $banner->enable_autoplay : 1;
$enable_muted     = isset($banner->enable_muted) ? (int) $banner->enable_muted : 1;
$enable_loop      = isset($banner->enable_loop) ? (int) $banner->enable_loop : 1;
$enable_controls  = isset($banner->enable_controls) ? (int) $banner->enable_controls : 1;
$enable_slider    = isset($banner->enable_slider) ? (int) $banner->enable_slider : 1;
$enable_slider_desktop = isset($banner->enable_slider_desktop) ? (int) $banner->enable_slider_desktop : 1;
$enable_slider_mobile = isset($banner->enable_slider_mobile) ? (int) $banner->enable_slider_mobile : 1;
$enable_overlay   = isset($banner->enable_overlay) ? (int) $banner->enable_overlay : 1;
$enable_overlay_desktop = isset($banner->enable_overlay_desktop) ? (int) $banner->enable_overlay_desktop : 1;
$enable_overlay_mobile = isset($banner->enable_overlay_mobile) ? (int) $banner->enable_overlay_mobile : 1;
$enable_hover     = isset($banner->enable_hover) ? (int) $banner->enable_hover : 1;
$enable_hover_desktop = isset($banner->enable_hover_desktop) ? (int) $banner->enable_hover_desktop : 1;
$enable_hover_mobile = isset($banner->enable_hover_mobile) ? (int) $banner->enable_hover_mobile : 1;
$image_count      = count($image_urls);
$poster_image     = !empty($image_urls) ? $image_urls[0] : '';
$has_slider       = ($enable_slider && $image_count > 0) ? 1 : 0;
$mobile_behavior  = isset($atts['mobile_behavior']) ? $atts['mobile_behavior'] : 'image';
$autoplay_delay   = isset($atts['autoplay_delay']) ? intval($atts['autoplay_delay']) : 0;

// Dedicated overlay image - use custom uploaded image or fallback to first slider image
$overlay_image    = isset($banner->overlay_image) && !empty($banner->overlay_image) ? $banner->overlay_image : $poster_image;
?>

<div class="abvp-banner-wrapper anmi-video-banner-container transition-<?php echo esc_attr($atts['transition']); ?> <?php echo esc_attr($unique_id); ?> effect-<?php echo esc_attr($atts['transition']); ?>" 
     style="height: <?php echo esc_attr($atts['height']); ?>; position: relative; cursor: pointer;"
    data-autoplay-delay="<?php echo esc_attr($autoplay_delay); ?>"
    data-mobile-behavior="<?php echo esc_attr($mobile_behavior); ?>"
     data-slider-speed="<?php echo esc_attr($atts['slider_speed']); ?>"
     data-slider-effect="<?php echo esc_attr($atts['slider_effect']); ?>"
    data-video-type="<?php echo esc_attr($video_data['type']); ?>"
    data-enable-autoplay="<?php echo esc_attr($enable_autoplay); ?>"
    data-enable-muted="<?php echo esc_attr($enable_muted); ?>"
    data-enable-loop="<?php echo esc_attr($enable_loop); ?>"
    data-enable-controls="<?php echo esc_attr($enable_controls); ?>"
    data-enable-slider="<?php echo esc_attr($enable_slider); ?>"
    data-enable-slider-desktop="<?php echo esc_attr($enable_slider_desktop); ?>"
    data-enable-slider-mobile="<?php echo esc_attr($enable_slider_mobile); ?>"
    data-enable-overlay="<?php echo esc_attr($enable_overlay); ?>"
    data-enable-overlay-desktop="<?php echo esc_attr($enable_overlay_desktop); ?>"
    data-enable-overlay-mobile="<?php echo esc_attr($enable_overlay_mobile); ?>"
    data-enable-hover="<?php echo esc_attr($enable_hover); ?>"
    data-enable-hover-desktop="<?php echo esc_attr($enable_hover_desktop); ?>"
    data-enable-hover-mobile="<?php echo esc_attr($enable_hover_mobile); ?>"
    data-image-count="<?php echo esc_attr($image_count); ?>"
    data-has-slider="<?php echo esc_attr($has_slider); ?>">,
    
    <?php if ($has_slider): ?>
    <!-- Image Slider (visible by default) -->
    <?php foreach ($image_urls as $index => $image_url): ?>
        <div class="abvp-slide-image anmi-banner-image <?php echo $index === 0 ? 'active' : ''; ?>" 
             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
                    background-image: url('<?php echo esc_url($image_url); ?>'); 
                    background-size: cover; background-position: center; 
                    opacity: <?php echo $index === 0 ? '1' : '0'; ?>; 
                    transition: opacity 0.8s ease; z-index: 2;"></div>
    <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Video Background using WordPress Core Widget -->
    <?php 
    // Prepare video settings
    $video_settings = array(
        'enable_autoplay' => $enable_autoplay,
        'enable_muted' => $enable_muted,
        'enable_loop' => $enable_loop,
        'enable_controls' => $enable_controls,
    );
    
    // Prepare Elementor-style args
    $elementor_args = array(
        'aspect_ratio' => '16:9',
        'show_image_overlay' => 'yes',
        'image_overlay' => $poster_image,
        'show_play_icon' => 'yes',
        'lightbox' => 'no',
        'lazy_load' => 'yes',
        'preload' => 'metadata',
    );
    
    // Get plugin instance and render using WordPress core widget
    $plugin = AnMi_Banner_Video_Pro::get_instance();
    $wp_video_html = $plugin->render_wp_core_video(
        $video_data['embed_url'], 
        $video_data['type'],
        $video_settings,
        $poster_image,
        $elementor_args
    );
    
    if (!empty($wp_video_html)) {
        echo $wp_video_html;
    } else {
        // Ultimate fallback - should rarely happen
        echo '<div class="abvp-video-error" style="background: #000; color: #fff; padding: 20px; text-align: center;">';
        echo '<p>Video cannot be loaded. Please check the video URL.</p>';
        echo '</div>';
    }
    ?>
    
    <!-- Volume Control Button (hidden by default, shown by JS when video plays) -->
    <button class="abvp-audio-toggle anmi-volume-control" title="Unmute video">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="white" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <!-- Muted icon (default) -->
            <path class="abvp-icon-muted volume-icon-muted" d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
            
            <!-- Unmuted icon (hidden initially) -->
            <path class="abvp-icon-unmuted volume-icon-unmuted" style="display: none;" d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
        </svg>
    </button>
    
    <?php if (!empty($atts['title']) || !empty($atts['subtitle']) || !empty($atts['button_text'])): ?>
    <!-- Content Overlay -->
    <div class="abvp-content-overlay"
         data-show-title="<?php echo esc_attr($atts['show_title']); ?>"
         data-show-subtitle="<?php echo esc_attr($atts['show_subtitle']); ?>"
         data-show-button="<?php echo esc_attr($atts['show_button']); ?>">
        <?php if (!empty($atts['title']) && $atts['show_title']): ?>
            <h1 class="abvp-title-text"><?php echo esc_html($atts['title']); ?></h1>
        <?php endif; ?>
        
        <?php if (!empty($atts['subtitle']) && $atts['show_subtitle']): ?>
            <p class="abvp-subtitle-text"><?php echo esc_html($atts['subtitle']); ?></p>
        <?php endif; ?>
        
        <?php if (!empty($atts['button_text']) && $atts['show_button']): ?>
            <a href="<?php echo esc_url($atts['button_link']); ?>" class="abvp-cta-button">
                <?php echo esc_html($atts['button_text']); ?>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php if ($image_count > 1): ?>
    <!-- Slider Navigation Dots -->
    <div class="abvp-nav-dots anmi-slider-dots" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 10;">
        <?php foreach ($image_urls as $index => $image_url): ?>
            <span class="abvp-dot-indicator anmi-banner-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                  data-slide="<?php echo $index; ?>"
                  style="width: 12px; height: 12px; border-radius: 50%; background: <?php echo $index === 0 ? '#fff' : 'rgba(255,255,255,0.5)'; ?>; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Dedicated Overlay Layer with Play Button (independent from slider) -->
    <?php 
    // Only render overlay if:
    // 1. Overlay is enabled (master switch OR desktop OR mobile)
    // 2. Has overlay image (custom or fallback)
    $should_render_overlay = !empty($overlay_image) && (
        $enable_overlay || 
        $enable_overlay_desktop || 
        $enable_overlay_mobile
    );
    ?>
    <?php if ($should_render_overlay): ?>
    <div class="abvp-dedicated-overlay" 
         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 35;">
        <!-- Overlay Background Image -->
        <div class="abvp-overlay-background" 
             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
                    background-image: url('<?php echo esc_url($overlay_image); ?>'); 
                    background-size: cover; background-position: center;"></div>
        
        <!-- Play Button Container -->
        <div class="abvp-play-button-wrapper" 
             style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); 
                    width: 80px; height: 80px; cursor: pointer; z-index: 36;">
            <!-- Play Icon SVG -->
            <svg class="abvp-play-icon" width="80" height="80" viewBox="0 0 80 80" style="filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));">
                <circle cx="40" cy="40" r="38" fill="rgba(255,255,255,0.9)" stroke="rgba(0,0,0,0.1)" stroke-width="2"/>
                <polygon points="32,25 32,55 55,40" fill="#333" />
            </svg>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- OLD OVERLAY REMOVED: Now using dedicated overlay system above -->
    
    <!-- Loading Spinner -->
    <div class="abvp-loading-spinner anmi-banner-loader">
        <div class="spinner"></div>
    </div>
</div>
