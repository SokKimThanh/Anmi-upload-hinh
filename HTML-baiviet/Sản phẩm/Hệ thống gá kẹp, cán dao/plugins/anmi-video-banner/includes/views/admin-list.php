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
    
    // Preview banner (modal - to be implemented)
    $('.preview-banner').on('click', function(e) {
        e.preventDefault();
        alert('Preview functionality coming soon!');
    });
});
</script>
