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
            <div class="stat-number"><?php echo count(array_filter($banners, function($b) { return $b->status == 'active'; })); ?></div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?php echo count(array_filter($banners, function($b) { return $b->status == 'inactive'; })); ?></div>
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
                    $images = json_decode($banner->images, true);
                    $first_image = !empty($images) && is_array($images) ? $images[0] : '';
                    $image_count = is_array($images) ? count($images) : 0;
                    
                    // Determine video type and icon
                    $video_type = 'Direct URL';
                    $video_icon = '🎥';
                    if (strpos($banner->video_url, 'youtube.com') !== false || strpos($banner->video_url, 'youtu.be') !== false) {
                        $video_type = 'YouTube';
                        $video_icon = '📺';
                    } elseif (strpos($banner->video_url, 'vimeo.com') !== false) {
                        $video_type = 'Vimeo';
                        $video_icon = '🎞️';
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
                            <a href="<?php echo admin_url('admin.php?page=anmi-video-banner-edit&id=' . $banner->id); ?>">
                                <?php echo esc_html($banner->name); ?>
                            </a>
                        </strong>
                        <?php if ($banner->title): ?>
                            <br><small class="banner-subtitle"><?php echo esc_html($banner->title); ?></small>
                        <?php endif; ?>
                    </td>
                    
                    <td class="column-video">
                        <span class="video-type-badge"><?php echo $video_icon; ?> <?php echo $video_type; ?></span>
                        <br><small class="video-url" title="<?php echo esc_attr($banner->video_url); ?>">
                            <?php echo esc_html(substr($banner->video_url, 0, 40) . '...'); ?>
                        </small>
                    </td>
                    
                    <td class="column-images">
                        <span class="image-count-badge">
                            <span class="dashicons dashicons-images-alt2"></span>
                            <?php echo $image_count; ?> images
                        </span>
                        <br><small>Slider: <?php echo ($banner->slider_speed / 1000); ?>s/slide</small>
                    </td>
                    
                    <td class="column-shortcode">
                        <input type="text" 
                               class="shortcode-input" 
                               value='[anmi_video_banner id="<?php echo $banner->id; ?>"]' 
                               readonly 
                               onclick="this.select()">
                        <button class="button button-small copy-shortcode" data-shortcode='[anmi_video_banner id="<?php echo $banner->id; ?>"]'>
                            <span class="dashicons dashicons-clipboard"></span> Copy
                        </button>
                    </td>
                    
                    <td class="column-status">
                        <?php if ($banner->status == 'active'): ?>
                            <span class="status-badge status-active">Active</span>
                        <?php else: ?>
                            <span class="status-badge status-inactive">Inactive</span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="column-date">
                        <?php echo date('M j, Y', strtotime($banner->created_at)); ?>
                        <br><small><?php echo date('g:i A', strtotime($banner->created_at)); ?></small>
                    </td>
                    
                    <td class="column-actions">
                        <a href="<?php echo admin_url('admin.php?page=anmi-video-banner-edit&id=' . $banner->id); ?>" 
                           class="button button-small">
                            <span class="dashicons dashicons-edit"></span> Edit
                        </a>
                        <button class="button button-small button-link-delete delete-banner" 
                                data-banner-id="<?php echo $banner->id; ?>"
                                data-banner-name="<?php echo esc_attr($banner->name); ?>">
                            <span class="dashicons dashicons-trash"></span> Delete
                        </button>
                        <br>
                        <a href="#" class="button button-small preview-banner" 
                           data-banner-id="<?php echo $banner->id; ?>">
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
            url: anmiBannerAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'anmi_delete_banner',
                nonce: anmiBannerAdmin.nonce,
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
        console.log('AJAX URL:', anmiBannerAdmin.ajax_url);
        console.log('Nonce:', anmiBannerAdmin.nonce);
        
        $.ajax({
            url: anmiBannerAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'anmi_get_banner_preview',
                banner_id: bannerId,
                nonce: anmiBannerAdmin.nonce
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
        
        // Parse images
        var images = [];
        try {
            images = JSON.parse(banner.images);
            console.log('Parsed images:', images);
        } catch(e) {
            console.error('Failed to parse images:', e);
            images = [banner.images];
        }
        
        if (!Array.isArray(images)) {
            images = [images];
        }
        
        // Get video URL - extract from embed code if needed
        var videoUrl = banner.video_url;
        console.log('Original video_url:', videoUrl);
        console.log('video_type:', banner.video_type);
        
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
        
        // YouTube detection (supports youtu.be with query parameters)
        var youtubeMatch = videoUrl.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})(?:[?&]|$)/i);
        if (youtubeMatch) {
            videoType = 'youtube';
            videoId = youtubeMatch[1];
            embedUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&mute=1&loop=1&playlist=' + videoId + '&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1';
            console.log('YouTube detected - Video ID:', videoId, 'Embed URL:', embedUrl);
        }
        
        // Vimeo detection
        var vimeoMatch = videoUrl.match(/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)(?:$|\/|\?)/i);
        if (vimeoMatch) {
            videoType = 'vimeo';
            videoId = vimeoMatch[1];
            embedUrl = 'https://player.vimeo.com/video/' + videoId + '?autoplay=1&muted=1&loop=1&background=1&controls=0';
            console.log('Vimeo detected - Video ID:', videoId, 'Embed URL:', embedUrl);
        }
        
        console.log('Final video detection - Type:', videoType, 'Embed URL:', embedUrl);
        
        // Generate unique ID
        var uniqueId = 'anmi-modal-preview-' + Date.now();
        
        // Build HTML
        var html = '<div class="anmi-video-banner-container ' + uniqueId + ' transition-' + banner.transition + '" ' +
                   'style="height: 500px;" ' +
                   'data-autoplay-delay="' + banner.autoplay_delay + '" ' +
                   'data-mobile-behavior="both" ' +
                   'data-slider-speed="' + banner.slider_speed + '" ' +
                   'data-slider-effect="' + banner.slider_effect + '" ' +
                   'data-video-type="' + videoType + '">';
        
        // Video/Iframe
        if (videoType === 'youtube' || videoType === 'vimeo') {
            console.log('Creating iframe for', videoType, 'with URL:', embedUrl);
            html += '<iframe class="anmi-banner-video anmi-banner-iframe" ' +
                    'src="' + embedUrl + '" ' +
                    'frameborder="0" ' +
                    'allow="autoplay; fullscreen; picture-in-picture" ' +
                    'allowfullscreen ' +
                    'style="pointer-events: none;"></iframe>';
        } else {
            console.log('Creating video element with URL:', embedUrl);
            html += '<video class="anmi-banner-video" loop muted playsinline preload="metadata" ' +
                    'poster="' + (images[0] || '') + '">' +
                    '<source src="' + embedUrl + '" type="video/mp4">' +
                    '</video>';
        }
        
        // Image Slider
        html += '<div class="anmi-banner-slider">';
        images.forEach(function(imageUrl, index) {
            html += '<div class="anmi-slider-slide ' + (index === 0 ? 'active' : '') + '" ' +
                    'style="background-image: url(\'' + imageUrl + '\');"></div>';
        });
        
        // Slider dots
        if (images.length > 1) {
            html += '<div class="anmi-slider-dots">';
            images.forEach(function(imageUrl, index) {
                html += '<span class="dot ' + (index === 0 ? 'active' : '') + '" data-slide="' + index + '"></span>';
            });
            html += '</div>';
        }
        html += '</div>'; // Close slider
        
        // Content Overlay
        if (banner.title || banner.subtitle || banner.button_text) {
            html += '<div class="anmi-banner-content" ' +
                    'data-show-title="' + banner.show_title + '" ' +
                    'data-show-subtitle="' + banner.show_subtitle + '" ' +
                    'data-show-button="' + banner.show_button + '">';
            
            if (banner.title && banner.show_title == '1') {
                html += '<h1 class="anmi-banner-title">' + banner.title + '</h1>';
            }
            if (banner.subtitle && banner.show_subtitle == '1') {
                html += '<p class="anmi-banner-subtitle">' + banner.subtitle + '</p>';
            }
            if (banner.button_text && banner.show_button == '1') {
                html += '<a href="' + banner.button_link + '" class="anmi-banner-btn" target="_blank">' + banner.button_text + '</a>';
            }
            
            html += '</div>';
        }
        
        // Loading spinner
        html += '<div class="anmi-banner-loader"><div class="spinner"></div></div>';
        
        html += '</div>'; // Close container
        
        // Add info box
        html += '<div class="anmi-preview-info">' +
                '<p><strong>💡 Tip:</strong> Hover chuột vào banner để xem video phát</p>' +
                '<p><strong>📋 Shortcode:</strong> <code>[anmi_video_banner id="' + banner.id + '"]</code></p>' +
                '</div>';
        
        console.log('HTML length:', html.length);
        console.log('Injecting HTML into #anmi-preview-container...');
        
        // Inject HTML
        $('#anmi-preview-container').html(html).fadeIn(300);
        
        console.log('HTML injected, preview container display:', $('#anmi-preview-container').css('display'));
        console.log('Preview container children count:', $('#anmi-preview-container').children().length);
        
        // Initialize banner functionality
        setTimeout(function() {
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
        }, 100);
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
        
        // Clear preview content after animation
        setTimeout(function() {
            $('#anmi-preview-container').html('').hide();
            $('.anmi-modal-loading').show();
        }, 300);
    }
});
</script>

