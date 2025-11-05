<?php
/**
 * Admin List View - Display all banners
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap anmi-banner-admin">
    <h1 class="wp-heading-inline">Video Banners</h1>
    <a href="<?php echo admin_url('admin.php?page=anmi-video-banner-new'); ?>" class="page-title-action">Add New</a>
    <hr class="wp-header-end">
    
    <?php if (isset($_GET['message']) && $_GET['message'] == 'saved'): ?>
        <div class="notice notice-success is-dismissible">
            <p>Banner saved successfully!</p>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['message']) && $_GET['message'] == 'deleted'): ?>
        <div class="notice notice-success is-dismissible">
            <p>Banner deleted successfully!</p>
        </div>
    <?php endif; ?>
    
    <div class="anmi-banner-stats">
        <div class="stat-box">
            <div class="stat-number"><?php echo count($banners); ?></div>
            <div class="stat-label">Total Banners</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?php echo count(array_filter($banners, function($b) { return $b->banner_status == 'active'; })); ?></div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?php echo count(array_filter($banners, function($b) { return $b->banner_status == 'inactive'; })); ?></div>
            <div class="stat-label">Inactive</div>
        </div>
    </div>
    
    <?php if (empty($banners)): ?>
        
        <div class="anmi-empty-state">
            <div class="empty-icon">🎬</div>
            <h2>No Video Banners Yet</h2>
            <p>Create your first video banner with slider and hover effects.</p>
            <a href="<?php echo admin_url('admin.php?page=anmi-video-banner-new'); ?>" class="button button-primary button-hero">Create Your First Banner</a>
            
            <div class="quick-guide">
                <h3>Quick Start Guide:</h3>
                <ol>
                    <li><strong>Add Video URL:</strong> YouTube, Vimeo, or direct MP4 link</li>
                    <li><strong>Upload Images:</strong> Multiple images for slider</li>
                    <li><strong>Customize:</strong> Title, buttons, transitions</li>
                    <li><strong>Use Shortcode:</strong> [anmi_video_banner id="1"] in any page</li>
                    <li><strong>Or Elementor:</strong> Drag & drop widget with banner selector</li>
                </ol>
            </div>
        </div>
        
    <?php else: ?>
        
        <table class="wp-list-table widefat fixed striped anmi-banner-table">
            <thead>
                <tr>
                    <th class="column-thumbnail">Preview</th>
                    <th class="column-name">Banner Name</th>
                    <th class="column-video">Video</th>
                    <th class="column-images">Images</th>
                    <th class="column-shortcode">Shortcode</th>
                    <th class="column-status">Status</th>
                    <th class="column-date">Created</th>
                    <th class="column-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($banners as $banner): 
                    $images = json_decode($banner->image_list, true);
                    if (!is_array($images)) {
                        $images = array();
                    }
                    $first_image = !empty($images) ? $images[0] : '';
                    $image_count = count($images);

                    // Determine video type and icon
                    $video_display_source = $banner->video_url_value;
                    if ($banner->video_input_type === 'embed' && preg_match('/src=["\']([^"\']+)["\']/', $banner->video_url_value, $matches)) {
                        $video_display_source = $matches[1];
                    }

                    $video_type = 'Direct URL';
                    $video_icon = '🎥';
                    if ($video_display_source && (strpos($video_display_source, 'youtube.com') !== false || strpos($video_display_source, 'youtu.be') !== false)) {
                        $video_type = 'YouTube';
                        $video_icon = '📺';
                    } elseif ($video_display_source && strpos($video_display_source, 'vimeo.com') !== false) {
                        $video_type = 'Vimeo';
                        $video_icon = '🎞️';
                    }

                    $video_label = '';
                    if ($video_display_source) {
                        if (function_exists('mb_strimwidth')) {
                            $video_label = mb_strimwidth($video_display_source, 0, 40, '...');
                        } else {
                            $video_label = strlen($video_display_source) > 40
                                ? substr($video_display_source, 0, 40) . '...'
                                : $video_display_source;
                        }
                    }
                ?>
                <tr>
                    <td class="column-thumbnail">
                        <?php if ($first_image): ?>
                            <div class="banner-thumbnail" style="background-image: url('<?php echo esc_url($first_image); ?>');">
                                <div class="thumbnail-overlay">
                                    <span class="dashicons dashicons-format-video"></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="banner-thumbnail no-image">
                                <span class="dashicons dashicons-format-image"></span>
                            </div>
                        <?php endif; ?>
                    </td>
                    
                    <td class="column-name">
                        <strong>
                            <a href="<?php echo admin_url('admin.php?page=anmi-video-banner-edit&id=' . intval($banner->banner_id)); ?>">
                                <?php echo esc_html($banner->banner_name); ?>
                            </a>
                        </strong>
                        <?php if (!empty($banner->banner_title)): ?>
                            <br><small class="banner-subtitle"><?php echo esc_html($banner->banner_title); ?></small>
                        <?php endif; ?>
                    </td>
                    
                    <td class="column-video">
                        <span class="video-type-badge"><?php echo $video_icon; ?> <?php echo $video_type; ?></span>
                        <br><small class="video-url" title="<?php echo esc_attr($video_display_source); ?>">
                            <?php echo $video_display_source ? esc_html($video_label) : '&mdash;'; ?>
                        </small>
                    </td>
                    
                    <td class="column-images">
                        <span class="image-count-badge">
                            <span class="dashicons dashicons-images-alt2"></span>
                            <?php echo $image_count; ?> images
                        </span>
                        <br><small>Slider: <?php echo ($banner->image_slider_speed / 1000); ?>s/slide</small>
                    </td>
                    
                    <td class="column-shortcode">
                        <input type="text" 
                               class="shortcode-input" 
                               value='[anmi_video_banner id="<?php echo intval($banner->banner_id); ?>"]' 
                               readonly 
                               onclick="this.select()">
                        <button class="button button-small copy-shortcode" data-shortcode='[anmi_video_banner id="<?php echo intval($banner->banner_id); ?>"]'>
                            <span class="dashicons dashicons-clipboard"></span> Copy
                        </button>
                    </td>
                    
                    <td class="column-status">
                        <?php if ($banner->banner_status == 'active'): ?>
                            <span class="status-badge status-active">Active</span>
                        <?php else: ?>
                            <span class="status-badge status-inactive">Inactive</span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="column-date">
                        <?php echo date('M j, Y', strtotime($banner->created_date)); ?>
                        <br><small><?php echo date('g:i A', strtotime($banner->created_date)); ?></small>
                    </td>
                    
                    <td class="column-actions">
                        <a href="<?php echo admin_url('admin.php?page=anmi-video-banner-edit&id=' . intval($banner->banner_id)); ?>" 
                           class="button button-small">
                            <span class="dashicons dashicons-edit"></span> Edit
                        </a>
                        <button class="button button-small button-link-delete delete-banner" 
                                data-banner-id="<?php echo intval($banner->banner_id); ?>"
                                data-banner-name="<?php echo esc_attr($banner->banner_name); ?>">
                            <span class="dashicons dashicons-trash"></span> Delete
                        </button>
                        <br>
                        <a href="#" class="button button-small preview-banner" 
                           data-banner-id="<?php echo intval($banner->banner_id); ?>">
                            <span class="dashicons dashicons-visibility"></span> Preview
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    <?php endif; ?>
    
    <!-- Quick Info Box -->
    <div class="anmi-info-box">
        <h3>📌 How to Use Your Banners</h3>
        <div class="info-grid">
            <div class="info-item">
                <h4>📄 In Pages/Posts (Shortcode)</h4>
                <p>Copy the shortcode from the list above and paste it into any page, post, or widget.</p>
                <code>[anmi_video_banner id="1"]</code>
            </div>
            
            <div class="info-item">
                <h4>🎨 In Elementor</h4>
                <p>1. Open page in Elementor<br>
                   2. Search "An Mi Video Banner" widget<br>
                   3. Select banner from dropdown<br>
                   4. Customize and publish</p>
            </div>
            
            <div class="info-item">
                <h4>🎬 Video Sources</h4>
                <p>Supported video types:<br>
                   • YouTube (embed URL)<br>
                   • Vimeo (embed URL)<br>
                   • Direct MP4 link<br>
                   • Any video URL</p>
            </div>
            
            <div class="info-item">
                <h4>📱 Mobile Responsive</h4>
                <p>Banners automatically adapt to mobile devices. Configure mobile behavior in settings:</p>
                <ul>
                    <li><strong>Image:</strong> Show slider only</li>
                    <li><strong>Video:</strong> Show video only</li>
                    <li><strong>Both:</strong> Touch to play</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var activePreviewIntervals = [];
    var previewSliderInterval = null;

    // Copy shortcode
    $('.copy-shortcode').on('click', function(e) {
        e.preventDefault();
        var shortcode = $(this).data('shortcode');
        var $input = $(this).prev('.shortcode-input');
        
        $input.select();
        document.execCommand('copy');
        
        $(this).html('<span class="dashicons dashicons-yes"></span> Copied!');
        setTimeout(() => {
            $(this).html('<span class="dashicons dashicons-clipboard"></span> Copy');
        }, 2000);
    });
    
    // Delete banner
    $('.delete-banner').on('click', function(e) {
        e.preventDefault();
        
        var bannerId = $(this).data('banner-id');
        var bannerName = $(this).data('banner-name');
        var $row = $(this).closest('tr');
        
        if (!confirm('Are you sure you want to delete "' + bannerName + '"?\n\nThis action cannot be undone.')) {
            return;
        }
        
        $.ajax({
            url: abvpBannerAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'abvp_delete_banner',
                nonce: abvpBannerAdmin.nonce,
                banner_id: bannerId
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(400, function() {
                        $(this).remove();
                        
                        // Check if table is empty
                        if ($('.anmi-banner-table tbody tr').length === 0) {
                            location.reload();
                        }
                    });
                    
                    // Show success message
                    $('.wrap').prepend('<div class="notice notice-success is-dismissible"><p>' + response.data + '</p></div>');
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('Failed to delete banner. Please try again.');
            }
        });
    });
    
    // Preview banner modal
    $('.preview-banner').on('click', function(e) {
        e.preventDefault();
        console.log('Preview button clicked!');
        
        var bannerId = $(this).data('banner-id');
        console.log('Banner ID:', bannerId);
        
        // Show loading modal
        showPreviewModal(bannerId);
    });
    
    function showPreviewModal(bannerId) {
        console.log('showPreviewModal called with ID:', bannerId);
        
        // Create modal HTML if not exists
        if ($('#anmi-preview-modal').length === 0) {
            console.log('Creating modal HTML...');
            var modalHtml = '<div id="anmi-preview-modal" class="anmi-modal">' +
                '<div class="anmi-modal-overlay"></div>' +
                '<div class="anmi-modal-content">' +
                    '<div class="anmi-modal-header">' +
                        '<h2>👁️ Xem Trước Banner</h2>' +
                        '<button class="anmi-modal-close">&times;</button>' +
                    '</div>' +
                    '<div class="anmi-modal-body">' +
                        '<div class="anmi-modal-loading">' +
                            '<span class="dashicons dashicons-update spin"></span>' +
                            '<p>Đang tải preview...</p>' +
                        '</div>' +
                        '<div id="anmi-preview-container" style="display: none;"></div>' +
                    '</div>' +
                '</div>' +
            '</div>';
            
            $('body').append(modalHtml);
            
            // Close modal events
            $('.anmi-modal-close, .anmi-modal-overlay').on('click', function() {
                closePreviewModal();
            });
            
            // ESC key to close
            $(document).on('keyup', function(e) {
                if (e.key === 'Escape') {
                    closePreviewModal();
                }
            });
        } else {
            console.log('Modal already exists');
        }
        
        // Show modal
        console.log('Showing modal...');
        $('#anmi-preview-modal').fadeIn(300);
        $('body').addClass('anmi-modal-open');
        console.log('Modal display:', $('#anmi-preview-modal').css('display'));
        
        // Load banner data via AJAX
        console.log('Sending AJAX request...');
        console.log('AJAX URL:', abvpBannerAdmin.ajax_url);
        console.log('Nonce:', abvpBannerAdmin.nonce);
        
        $.ajax({
            url: abvpBannerAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'abvp_get_banner_preview',
                banner_id: bannerId,
                nonce: abvpBannerAdmin.nonce
            },
            success: function(response) {
                console.log('AJAX Success:', response);
                if (response.success) {
                    renderPreview(response.data);
                } else {
                    showPreviewError(response.data || 'Không thể tải banner');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                showPreviewError('Lỗi ' + xhr.status + ': ' + xhr.statusText + '. Server có thể đang bận, vui lòng thử lại.');
            }
        });
    }
    
    function renderPreview(banner) {
        console.log('renderPreview called with banner:', banner);
        
        $('.anmi-modal-loading').hide();
        console.log('Loading hidden');

        // Clear any previous preview timers before rendering a new one
        activePreviewIntervals.forEach(function(intervalId) {
            if (intervalId) {
                clearInterval(intervalId);
            }
        });
        activePreviewIntervals = [];
        previewSliderInterval = null;

        function ensureNumericFlag(value, fallback) {
            var parsed = parseInt(value, 10);
            return isNaN(parsed) ? fallback : parsed;
        }

        function ensureNonNegativeInt(value, fallback, min) {
            var parsed = parseInt(value, 10);
            if (isNaN(parsed)) {
                return fallback;
            }
            if (typeof min === 'number' && parsed < min) {
                return fallback;
            }
            return parsed;
        }

        function sanitizeEffect(value, fallback) {
            if (!value) {
                return fallback;
            }
            var sanitized = String(value).trim().toLowerCase();
            return sanitized === '' ? fallback : sanitized;
        }

        // Parse images
        var images = [];
        try {
            images = JSON.parse(banner.image_list);
            console.log('Parsed images:', images);
        } catch (e) {
            console.error('Failed to parse images:', e);
            images = [];
        }

        if (!Array.isArray(images)) {
            images = [];
        }

        images = images
            .filter(function(url) {
                return typeof url === 'string' && url.trim() !== '';
            })
            .map(function(url) {
                return url.trim();
            });

        var firstImage = images.length ? images[0] : '';
        
        // Get video URL - extract from embed code if needed
        var videoUrl = (banner.video_url_value || '').toString().trim();
        console.log('Original video_url:', videoUrl);
        console.log('video_type:', banner.video_input_type);
        
        // If it looks like iframe code, extract src URL
        if (videoUrl && videoUrl.indexOf('<iframe') !== -1) {
            var srcMatch = videoUrl.match(/src=["']([^"']+)["']/i);
            if (srcMatch && srcMatch[1]) {
                videoUrl = srcMatch[1];
                console.log('Extracted URL from embed code:', videoUrl);
            }
        } else {
            console.log('Not an iframe code, using URL directly');
        }
        
        // Detect video type
        var videoType = 'direct';
        var embedUrl = videoUrl;
        var videoId = null;

        var videoAutoplay = ensureNumericFlag(banner.video_autoplay, 1);
        var videoMuted = ensureNumericFlag(banner.video_muted, 1);
        var videoLoop = ensureNumericFlag(banner.video_loop, 1);
        var videoControls = ensureNumericFlag(banner.video_controls, 1);
        var videoModestbranding = ensureNumericFlag(banner.video_modestbranding, 1);
        var videoRel = ensureNumericFlag(banner.video_rel, 0);

        var autoplayDelay = ensureNonNegativeInt(banner.video_autoplay_delay, 3000, 0);
        var sliderSpeed = ensureNonNegativeInt(banner.image_slider_speed, 5000, 1000);
        var sliderEffect = sanitizeEffect(banner.image_slider_effect, 'fade');
        var transitionEffect = sanitizeEffect(banner.transition_effect, 'fade');
        var mobileDisplayMode = sanitizeEffect(banner.mobile_display_mode, 'both');

        var sliderSpeedSeconds = Math.round((sliderSpeed / 1000) * 10) / 10;
        var showTitle = ensureNumericFlag(banner.display_title, 0);
        var showSubtitle = ensureNumericFlag(banner.display_subtitle, 0);
        var showButton = ensureNumericFlag(banner.display_button, 0);
        var ctaButtonLink = banner.cta_button_link || '#';
        var ctaButtonText = (banner.cta_button_text || '').trim();
        var bannerTitle = (banner.banner_title || '').trim();
        var bannerSubtitle = (banner.banner_subtitle || '').trim();
        if (!ctaButtonLink) {
            ctaButtonLink = '#';
        }

        console.log('Video settings:', {
            autoplay: videoAutoplay,
            muted: videoMuted,
            loop: videoLoop,
            controls: videoControls,
            modestbranding: videoModestbranding,
            rel: videoRel,
            autoplayDelay: autoplayDelay,
            sliderSpeed: sliderSpeed,
            sliderEffect: sliderEffect,
            transitionEffect: transitionEffect,
            mobileDisplayMode: mobileDisplayMode,
            showTitle: showTitle,
            showSubtitle: showSubtitle,
            showButton: showButton
        });
        
        // YouTube detection (supports youtu.be with query parameters)
        var youtubeMatch = videoUrl.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})(?:[?&]|$)/i);
        if (youtubeMatch) {
            videoType = 'youtube';
            videoId = youtubeMatch[1];
            // Build YouTube URL with user settings
            embedUrl = 'https://www.youtube.com/embed/' + videoId + 
                '?autoplay=' + videoAutoplay +
                '&mute=' + videoMuted +
                '&loop=' + videoLoop +
                '&playlist=' + videoId +
                '&controls=' + videoControls +
                '&showinfo=0' +
                '&rel=' + videoRel +
                '&modestbranding=' + videoModestbranding +
                '&playsinline=1';
            console.log('YouTube detected - Video ID:', videoId, 'Embed URL:', embedUrl);
        }
        
        // Vimeo detection
        var vimeoMatch = videoUrl.match(/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)(?:$|\/|\?)/i);
        if (vimeoMatch) {
            videoType = 'vimeo';
            videoId = vimeoMatch[1];
            // Build Vimeo URL with user settings
            embedUrl = 'https://player.vimeo.com/video/' + videoId + 
                '?autoplay=' + videoAutoplay +
                '&muted=' + videoMuted +
                '&loop=' + videoLoop +
                '&background=' + (videoControls ? 0 : 1) +
                '&controls=' + videoControls;
            console.log('Vimeo detected - Video ID:', videoId, 'Embed URL:', embedUrl);
        }
        
        console.log('Final video detection - Type:', videoType, 'Embed URL:', embedUrl);
        
        // Generate unique ID
        var uniqueId = 'anmi-modal-preview-' + Date.now();
        
        // Build HTML with split layout: Video LEFT + Slider RIGHT
        var html = '<div class="anmi-preview-split-layout">';
        
        // LEFT COLUMN: Video Preview
        html += '<div class="anmi-preview-video-column">';
        html += '<h3>🎬 Video Preview</h3>';
    html += '<div class="anmi-video-banner-container ' + uniqueId + ' transition-' + transitionEffect + '" ' +
        'style="height: 400px; position: relative;" ' +
        'data-autoplay-delay="' + autoplayDelay + '" ' +
        'data-mobile-behavior="' + mobileDisplayMode + '" ' +
        'data-slider-speed="' + sliderSpeed + '" ' +
        'data-slider-effect="' + sliderEffect + '" ' +
        'data-video-type="' + videoType + '">';
        
        // Video/Iframe - Use PRODUCTION classes for CSS compatibility
        if (videoType === 'youtube' || videoType === 'vimeo') {
            console.log('Creating iframe for', videoType, 'with URL:', embedUrl);
            html += '<iframe class="anmi-banner-video anmi-banner-iframe" ' +
                    'src="' + embedUrl + '" ' +
                    'frameborder="0" ' +
                    'allow="autoplay; fullscreen; picture-in-picture" ' +
                    'allowfullscreen>' +
                    '</iframe>';
        } else {
            console.log('Creating video element with URL:', embedUrl);
            html += '<video class="anmi-banner-video" ' +
                    'loop muted playsinline preload="metadata" ' +
                    'poster="' + firstImage + '">' +
                    '<source src="' + embedUrl + '" type="video/mp4">' +
                    '</video>';
        }
        
        // Content Overlay (if exists)
        if (bannerTitle || bannerSubtitle || ctaButtonText) {
            html += '<div class="anmi-banner-content" ' +
                    'data-show-title="' + showTitle + '" ' +
                    'data-show-subtitle="' + showSubtitle + '" ' +
                    'data-show-button="' + showButton + '">';
            
            if (bannerTitle && showTitle === 1) {
                html += '<h1 class="anmi-banner-title">' + bannerTitle + '</h1>';
            }
            if (bannerSubtitle && showSubtitle === 1) {
                html += '<p class="anmi-banner-subtitle">' + bannerSubtitle + '</p>';
            }
            if (ctaButtonText && showButton === 1) {
                html += '<a href="' + ctaButtonLink + '" class="anmi-banner-btn" target="_blank">' + ctaButtonText + '</a>';
            }
            
            html += '</div>';
        }
        
        // Volume Control Button (for HTML5 video only)
        html += '<button class="anmi-volume-control" title="Toggle sound" ' +
                'style="position: absolute; bottom: 20px; right: 20px; z-index: 20; ' +
                'width: 44px; height: 44px; border: none; border-radius: 50%; ' +
                'background: rgba(0,0,0,0.6); cursor: pointer; display: none; ' +
                'transition: all 0.3s ease; opacity: 0.8;">' +
                '<svg width="24" height="24" viewBox="0 0 24 24" fill="white" style="display: block; margin: auto;">' +
                    '<!-- Muted icon (default) -->' +
                    '<path class="volume-icon-muted" d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>' +
                    '<!-- Unmuted icon (hidden initially) -->' +
                    '<path class="volume-icon-unmuted" style="display: none;" d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>' +
                '</svg>' +
                '</button>';
        
        // Loading spinner
        html += '<div class="anmi-banner-loader"><div class="spinner"></div></div>';
        
        html += '</div>'; // Close video container
        html += '</div>'; // Close video column
        
        // RIGHT COLUMN: Image Slider Preview
        html += '<div class="anmi-preview-slider-column">';
        html += '<h3>🖼️ Image Slider</h3>';
        html += '<div class="anmi-preview-slider-showcase" style="height: 400px; position: relative; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">';
        
        // Image Slider (standalone for preview)
        images.forEach(function(imageUrl, index) {
            html += '<div class="anmi-preview-slide ' + (index === 0 ? 'active' : '') + '" ' +
                    'style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; ' +
                    'background-image: url(\'' + imageUrl + '\'); background-size: cover; background-position: center; ' +
                    'opacity: ' + (index === 0 ? '1' : '0') + '; transition: opacity 0.5s ease;">' +
                    '</div>';
        });
        
        // Slider dots
        if (images.length > 1) {
            html += '<div class="anmi-preview-slider-dots" style="position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 10;">';
            images.forEach(function(imageUrl, index) {
                html += '<span class="anmi-preview-dot ' + (index === 0 ? 'active' : '') + '" data-slide="' + index + '" ' +
                        'style="width: 12px; height: 12px; border-radius: 50%; background: ' + (index === 0 ? '#fff' : 'rgba(255,255,255,0.5)') + '; ' +
                        'cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">' +
                        '</span>';
            });
            html += '</div>';
            
            // Add slider info
            html += '<div style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 5px 10px; border-radius: 4px; font-size: 12px;">' +
                    images.length + ' slides | ' + sliderSpeedSeconds + 's | ' + sliderEffect +
                    '</div>';
        }
        
        html += '</div>'; // Close slider showcase
        html += '</div>'; // Close slider column
        
        html += '</div>'; // Close split layout
        
        // ============================================
        // NEW ROW: Production Hover Effect Demo
        // ============================================
        html += '<div class="anmi-preview-production-demo" style="margin-top: 30px;">';
        html += '<h3 style="margin-bottom: 15px;">🎯 Production Preview (Hover + Click Effect)</h3>';
        html += '<p style="margin-bottom: 15px; color: #666;"><strong>Bước 1:</strong> Slider tự động chạy → <strong>Bước 2:</strong> Hover vào banner (slider dừng, video hiện) → <strong>Bước 3:</strong> Click play để phát video</p>';
        
        // Generate unique ID for production demo
        var productionId = 'anmi-production-demo-' + Date.now();
        
        // Production-style container
    html += '<div class="anmi-video-banner-container ' + productionId + ' transition-' + transitionEffect + '" ' +
        'style="height: 500px; position: relative; cursor: pointer;" ' +
        'data-autoplay-delay="' + autoplayDelay + '" ' +
        'data-mobile-behavior="' + mobileDisplayMode + '" ' +
        'data-slider-speed="' + sliderSpeed + '" ' +
        'data-slider-effect="' + sliderEffect + '" ' +
        'data-video-type="' + videoType + '">';
        
        // Image Slider (visible by default)
        images.forEach(function(imageUrl, index) {
            html += '<div class="anmi-banner-image ' + (index === 0 ? 'active' : '') + '" ' +
                    'style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; ' +
                    'background-image: url(\'' + imageUrl + '\'); background-size: cover; background-position: center; ' +
                    'opacity: ' + (index === 0 ? '1' : '0') + '; transition: opacity 0.8s ease; z-index: 2;">' +
                    '</div>';
        });
        
        // Video/Iframe (hidden by default, show on hover) - Use PRODUCTION classes
        if (videoType === 'youtube' || videoType === 'vimeo') {
            html += '<iframe class="anmi-banner-video anmi-banner-iframe" ' +
                    'src="' + embedUrl + '" ' +
                    'frameborder="0" ' +
                    'allow="autoplay; fullscreen; picture-in-picture" ' +
                    'allowfullscreen>' +
                    '</iframe>';
        } else {
        html += '<video class="anmi-banner-video" ' +
            'loop muted playsinline preload="metadata" ' +
            'poster="' + firstImage + '">' +
            '<source src="' + embedUrl + '" type="video/mp4">' +
            '</video>';
        }
        
        // Content Overlay (if exists)
        if (bannerTitle || bannerSubtitle || ctaButtonText) {
            html += '<div class="anmi-banner-content" ' +
                    'data-show-title="' + showTitle + '" ' +
                    'data-show-subtitle="' + showSubtitle + '" ' +
                    'data-show-button="' + showButton + '">';
            
            if (bannerTitle && showTitle === 1) {
                html += '<h1 class="anmi-banner-title">' + bannerTitle + '</h1>';
            }
            if (bannerSubtitle && showSubtitle === 1) {
                html += '<p class="anmi-banner-subtitle">' + bannerSubtitle + '</p>';
            }
            if (ctaButtonText && showButton === 1) {
                html += '<a href="' + ctaButtonLink + '" class="anmi-banner-btn" target="_blank">' + ctaButtonText + '</a>';
            }
            
            html += '</div>';
        }
        
        // Slider dots
        if (images.length > 1) {
            html += '<div class="anmi-banner-dots" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 10;">';
            images.forEach(function(imageUrl, index) {
                html += '<span class="anmi-banner-dot ' + (index === 0 ? 'active' : '') + '" data-slide="' + index + '" ' +
                        'style="width: 12px; height: 12px; border-radius: 50%; background: ' + (index === 0 ? '#fff' : 'rgba(255,255,255,0.5)') + '; ' +
                        'cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">' +
                        '</span>';
            });
            html += '</div>';
        }
        
        // Play button overlay
        html += '<div class="anmi-play-overlay" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 5; pointer-events: none;">' +
                '<div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.9); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">' +
                '<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
                '<polygon points="5 3 19 12 5 21 5 3"></polygon>' +
                '</svg>' +
                '</div>' +
                '</div>';
        
        // Volume Control Button (for HTML5 video only)
        html += '<button class="anmi-volume-control" title="Toggle sound" ' +
                'style="position: absolute; bottom: 20px; right: 20px; z-index: 20; ' +
                'width: 44px; height: 44px; border: none; border-radius: 50%; ' +
                'background: rgba(0,0,0,0.6); cursor: pointer; display: none; ' +
                'transition: all 0.3s ease; opacity: 0.8;">' +
                '<svg width="24" height="24" viewBox="0 0 24 24" fill="white" style="display: block; margin: auto;">' +
                    '<!-- Muted icon (default) -->' +
                    '<path class="volume-icon-muted" d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>' +
                    '<!-- Unmuted icon (hidden initially) -->' +
                    '<path class="volume-icon-unmuted" style="display: none;" d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>' +
                '</svg>' +
                '</button>';
        
        // Loading spinner
        html += '<div class="anmi-banner-loader"><div class="spinner"></div></div>';
        
        html += '</div>'; // Close production container
        
        html += '<div style="margin-top: 10px; padding: 10px; background: #f0f0f0; border-radius: 5px;">' +
                '<p style="margin: 0; font-size: 13px; color: #666;">' +
                '<strong>⚡ Flow:</strong> ' +
                '<span style="color: #2196F3;">①</span> Slider auto-play → ' +
                '<span style="color: #FF9800;">②</span> Hover (slider dừng, video hiện) → ' +
                '<span style="color: #4CAF50;">③</span> Click play button' +
                '</p>' +
                '</div>';
        
        html += '</div>'; // Close production demo section
        
        // Add info box
        html += '<div class="anmi-preview-info">' +
                '<p><strong>💡 Tip:</strong> Hover chuột vào banner để xem video phát</p>' +
                '<p><strong>💡Volume Control:</strong> Click volume button (bottom-right) để bật/tắt âm thanh khi video đang phát ' +
                '<span style="color: #FF9800;">(⚠️ Chỉ hoạt động với MP4 video, không hỗ trợ YouTube/Vimeo iframe)</span></p>' +
                '<p><strong>💡Shortcode:</strong> <code>[anmi_video_banner id="' + banner.banner_id + '"]</code></p>' +
                '</div>';
        
        console.log('HTML length:', html.length);
        console.log('Injecting HTML into #anmi-preview-container...');
        
        // Inject HTML
        $('#anmi-preview-container').html(html).fadeIn(300);
        
        console.log('HTML injected, preview container display:', $('#anmi-preview-container').css('display'));
        console.log('Preview container children count:', $('#anmi-preview-container').children().length);
        
        // Initialize banner functionality
        setTimeout(function() {
            // Initialize preview video/iframe (top section)
            var $container = $('.' + uniqueId);
            console.log('Initializing AnMiVideoBanner, container found:', $container.length > 0);
            if ($container.length && typeof AnMiVideoBanner !== 'undefined') {
                new AnMiVideoBanner($container[0]);
                console.log('AnMiVideoBanner initialized');
            } else {
                console.warn('AnMiVideoBanner not initialized:', {
                    containerFound: $container.length > 0,
                    classExists: typeof AnMiVideoBanner !== 'undefined'
                });
            }
            
            // Initialize standalone slider preview
            initPreviewSlider(sliderSpeed, sliderEffect, images.length);
            
            // ============================================
            // Initialize Production Demo (hover effect)
            // ============================================
            var $productionContainer = $('.' + productionId);
            console.log('Initializing Production Demo, container found:', $productionContainer.length > 0);
            
            if ($productionContainer.length) {
                var productionCurrentSlide = 0;
                var productionSliderInterval = null;
                var isHovered = false;
                var isVideoPlaying = false;
                
                // Get elements
                var $video = $productionContainer.find('.anmi-banner-video');
                var $images = $productionContainer.find('.anmi-banner-image');
                var $playOverlay = $productionContainer.find('.anmi-play-overlay');
                var $slides = $productionContainer.find('.anmi-banner-image');
                var $dots = $productionContainer.find('.anmi-banner-dot');
                
                // Start auto-play slider
                if (images.length > 1) {
                    productionSliderInterval = setInterval(function() {
                        if (!isHovered && !isVideoPlaying) {
                            // Fade out current
                            $slides.eq(productionCurrentSlide).css('opacity', '0');
                            $dots.eq(productionCurrentSlide).css('background', 'rgba(255,255,255,0.5)').removeClass('active');
                            
                            // Next slide
                            productionCurrentSlide = (productionCurrentSlide + 1) % images.length;
                            
                            // Fade in next
                            $slides.eq(productionCurrentSlide).css('opacity', '1');
                            $dots.eq(productionCurrentSlide).css('background', '#fff').addClass('active');
                        }
                    }, sliderSpeed);
                    activePreviewIntervals.push(productionSliderInterval);
                }
                
                // HOVER: Stop slider, show video/iframe (but don't play yet)
                $productionContainer.on('mouseenter', function() {
                    console.log('Production demo: Mouse enter - stopping slider, showing video');
                    isHovered = true;
                    
                    // Stop slider
                    if (productionSliderInterval) {
                        clearInterval(productionSliderInterval);
                    }
                    
                    // Fade out slider images
                    $images.css('opacity', '0');
                    
                    // Show video/iframe (but not playing yet)
                    $video.css('opacity', '1');
                    
                    // Keep play button visible (wait for click)
                    $playOverlay.css('opacity', '1');
                });
                
                // MOUSE LEAVE: Stop video and resume slider
                $productionContainer.on('mouseleave', function() {
                    console.log('Production demo: Mouse leave - stopping video, resuming slider');
                    isHovered = false;
                    
                    // Stop video if playing
                    if (isVideoPlaying) {
                        console.log('Stopping video playback');
                        if ($video.is('video')) {
                            $video[0].pause();
                            $video[0].currentTime = 0; // Reset to beginning
                        }
                        isVideoPlaying = false;
                    }
                    
                    // Hide video
                    $video.css('opacity', '0');
                    
                    // Show play button again
                    $playOverlay.css('opacity', '1').show();
                    
                    // Show current slide
                    $images.css('opacity', '0');
                    $images.eq(productionCurrentSlide).css('opacity', '1');
                    
                    // Resume slider
                    if (images.length > 1) {
                        if (productionSliderInterval) {
                            clearInterval(productionSliderInterval);
                        }
                        
                        productionSliderInterval = setInterval(function() {
                            if (!isHovered && !isVideoPlaying) {
                                $slides.eq(productionCurrentSlide).css('opacity', '0');
                                $dots.eq(productionCurrentSlide).css('background', 'rgba(255,255,255,0.5)').removeClass('active');
                                
                                productionCurrentSlide = (productionCurrentSlide + 1) % images.length;
                                
                                $slides.eq(productionCurrentSlide).css('opacity', '1');
                                $dots.eq(productionCurrentSlide).css('background', '#fff').addClass('active');
                            }
                        }, sliderSpeed);
                        activePreviewIntervals.push(productionSliderInterval);
                    }
                });
                
                // CLICK PLAY BUTTON: Start video playback
                $productionContainer.on('click', function() {
                    console.log('Production demo: Click - playing video');
                    
                    if (!isVideoPlaying) {
                        isVideoPlaying = true;
                        
                        // Hide play button
                        $playOverlay.fadeOut(300);
                        
                        // Play video
                        if ($video.length) {
                            if ($video.is('video')) {
                                $video[0].play();
                                console.log('Video element started playing');
                            } else if ($video.is('iframe')) {
                                console.log('Iframe already loaded (autoplay in URL)');
                            }
                        }
                    }
                });
                
                // Dot click handlers - Jump to slide and stop slider
                $dots.on('click', function(e) {
                    e.stopPropagation(); // Prevent triggering container click
                    
                    if (productionSliderInterval) {
                        clearInterval(productionSliderInterval);
                    }
                    
                    var slideIndex = $(this).data('slide');
                    
                    $slides.css('opacity', '0');
                    $dots.css('background', 'rgba(255,255,255,0.5)').removeClass('active');
                    
                    $slides.eq(slideIndex).css('opacity', '1');
                    $(this).css('background', '#fff').addClass('active');
                    
                    productionCurrentSlide = slideIndex;
                });
            }
        }, 100);
        
        // Hide loader when iframe/video loads (CRITICAL FIX)
        setTimeout(function() {
            var $modalContent = $('#anmi-preview-container');

            $modalContent.find('.anmi-banner-iframe')
                .off('load.preview')
                .on('load.preview', function() {
                    console.log('Preview iframe loaded - hiding loader');
                    $(this).closest('.anmi-video-banner-container').find('.anmi-banner-loader').fadeOut(300);
                });

            $modalContent.find('video.anmi-banner-video')
                .off('loadeddata.preview')
                .on('loadeddata.preview', function() {
                    console.log('Preview video loaded - hiding loader');
                    $(this).closest('.anmi-video-banner-container').find('.anmi-banner-loader').fadeOut(300);
                });

            setTimeout(function() {
                $modalContent.find('.anmi-banner-loader').fadeOut(300);
                console.log('Loader force-hidden after 3s timeout');
            }, 3000);
        }, 200);
    }
    
    // Slider preview auto-play
    function initPreviewSlider(speed, effect, totalSlides) {
        if (totalSlides <= 1) {
            return;
        }

        speed = parseInt(speed, 10);
        if (isNaN(speed) || speed <= 0) {
            speed = 5000;
        }

        effect = effect ? String(effect).toLowerCase() : 'fade';

        var currentSlide = 0;
        var $slides = $('.anmi-preview-slide');
        var $dots = $('.anmi-preview-dot');

        $dots.off('click.preview').on('click.preview', function() {
            var slideIndex = $(this).data('slide');
            showSlide(slideIndex);
            currentSlide = slideIndex;
        });

        if (previewSliderInterval) {
            clearInterval(previewSliderInterval);
        }

        previewSliderInterval = setInterval(function() {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }, speed);

        activePreviewIntervals.push(previewSliderInterval);

        function showSlide(index) {
            if (effect === 'fade') {
                $slides.css('opacity', '0');
                $slides.eq(index).css('opacity', '1');
            } else if (effect === 'slide') {
                $slides.css({
                    'transform': 'translateX(-' + (index * 100) + '%)',
                    'opacity': '1'
                });
            }

            $dots.css('background', 'rgba(255,255,255,0.5)').removeClass('active');
            $dots.eq(index).css('background', '#fff').addClass('active');
        }
    }
    
    function showPreviewError(message) {
        $('.anmi-modal-loading').hide();
        $('#anmi-preview-container').html(
            '<div class="anmi-preview-error">' +
            '<span class="dashicons dashicons-warning"></span>' +
            '<p>' + message + '</p>' +
            '</div>'
        ).fadeIn(300);
    }
    
    function closePreviewModal() {
        $('#anmi-preview-modal').fadeOut(300);
        $('body').removeClass('anmi-modal-open');

        activePreviewIntervals.forEach(function(intervalId) {
            if (intervalId) {
                clearInterval(intervalId);
            }
        });
        activePreviewIntervals = [];

        if (previewSliderInterval) {
            clearInterval(previewSliderInterval);
            previewSliderInterval = null;
        }
        
        // Clear preview content after animation
        setTimeout(function() {
            $('#anmi-preview-container').html('').hide();
            $('.anmi-modal-loading').show();
        }, 300);
    }
});
</script>

