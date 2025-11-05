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
?>

<div class="abvp-banner-wrapper <?php echo esc_attr($unique_id); ?> effect-<?php echo esc_attr($atts['transition']); ?>" 
     style="height: <?php echo esc_attr($atts['height']); ?>; position: relative; cursor: pointer;"
     data-autoplay-delay="<?php echo esc_attr($atts['autoplay_delay']); ?>"
     data-mobile-behavior="<?php echo esc_attr($atts['mobile_behavior']); ?>"
     data-slider-speed="<?php echo esc_attr($atts['slider_speed']); ?>"
     data-slider-effect="<?php echo esc_attr($atts['slider_effect']); ?>"
     data-video-type="<?php echo esc_attr($video_data['type']); ?>">
    
    <!-- Image Slider (visible by default) -->
    <?php foreach ($image_urls as $index => $image_url): ?>
        <div class="abvp-slide-image <?php echo $index === 0 ? 'active' : ''; ?>" 
             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
                    background-image: url('<?php echo esc_url($image_url); ?>'); 
                    background-size: cover; background-position: center; 
                    opacity: <?php echo $index === 0 ? '1' : '0'; ?>; 
                    transition: opacity 0.8s ease; z-index: 2;"></div>
    <?php endforeach; ?>
    
    <!-- Video Background (hidden by default) -->
    <?php if ($video_data['type'] === 'youtube' || $video_data['type'] === 'vimeo'): ?>
        <iframe class="abvp-video-frame abvp-video-iframe" 
                src="<?php echo esc_url($video_data['embed_url']); ?>" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                allowfullscreen></iframe>
    <?php else: ?>
        <video class="abvp-video-frame" 
               loop 
               muted 
               playsinline 
               preload="metadata"
               poster="<?php echo esc_url($image_urls[0]); ?>">
            <source src="<?php echo esc_url($video_data['embed_url']); ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    <?php endif; ?>
    
    <!-- Volume Control Button (hidden by default, shown by JS when video plays) -->
    <button class="abvp-audio-toggle" title="Unmute video">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="white" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <!-- Muted icon (default) -->
            <path class="abvp-icon-muted" d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
            
            <!-- Unmuted icon (hidden initially) -->
            <path class="abvp-icon-unmuted" style="display: none;" d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
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
    
    <?php if (count($image_urls) > 1): ?>
    <!-- Slider Navigation Dots -->
    <div class="abvp-nav-dots" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 10;">
        <?php foreach ($image_urls as $index => $image_url): ?>
            <span class="abvp-dot-indicator <?php echo $index === 0 ? 'active' : ''; ?>" 
                  data-slide="<?php echo $index; ?>"
                  style="width: 12px; height: 12px; border-radius: 50%; background: <?php echo $index === 0 ? '#fff' : 'rgba(255,255,255,0.5)'; ?>; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Play Button Overlay -->
    <div class="abvp-play-icon" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 5; pointer-events: none;">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.9); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="5 3 19 12 5 21 5 3"></polygon>
            </svg>
        </div>
    </div>
    
    <!-- Loading Spinner -->
    <div class="abvp-loading-spinner">
        <div class="spinner"></div>
    </div>
</div>
