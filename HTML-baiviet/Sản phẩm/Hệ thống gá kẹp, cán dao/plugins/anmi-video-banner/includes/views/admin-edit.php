<?php
/**
 * Admin Edit View - Add/Edit Banner Form
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Parse images if editing
$images_array = array();
if ($banner && !empty($banner->images)) {
    $images_array = json_decode($banner->images, true);
    if (!is_array($images_array)) {
        $images_array = array();
    }
}

$is_edit = ($banner && $banner->id > 0);
$page_title = $is_edit ? 'Edit Banner' : 'Add New Banner';
?>

<div class="wrap anmi-banner-admin anmi-banner-edit">
    <h1><?php echo $page_title; ?></h1>
    <hr class="wp-header-end">
    
    <form id="anmi-banner-form" method="post">
        <?php wp_nonce_field('anmi_banner_nonce', 'anmi_nonce'); ?>
        <input type="hidden" name="banner_id" id="banner_id" value="<?php echo $is_edit ? $banner->id : '0'; ?>">
        
        <div class="anmi-form-wrapper">
            
            <!-- Main Content -->
            <div class="anmi-form-main">
                
                <!-- Banner Name -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>Banner Information</h2>
                    </div>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><label for="banner_name">Banner Name <span class="required">*</span></label></th>
                                <td>
                                    <input type="text" 
                                           id="banner_name" 
                                           name="name" 
                                           class="regular-text" 
                                           value="<?php echo $is_edit ? esc_attr($banner->name) : ''; ?>" 
                                           required>
                                    <p class="description">Internal name for identifying this banner (e.g., "Homepage Hero Banner")</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Video Settings -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>🎬 Video Settings</h2>
                    </div>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><label for="video_type">Video Type</label></th>
                                <td>
                                    <select id="video_type" name="video_type" class="regular-text">
                                        <option value="url" <?php echo ($is_edit && $banner->video_type == 'url') ? 'selected' : ''; ?>>Direct URL (MP4)</option>
                                        <option value="youtube" <?php echo ($is_edit && $banner->video_type == 'youtube') ? 'selected' : ''; ?>>YouTube</option>
                                        <option value="vimeo" <?php echo ($is_edit && $banner->video_type == 'vimeo') ? 'selected' : ''; ?>>Vimeo</option>
                                        <option value="embed" <?php echo ($is_edit && $banner->video_type == 'embed') ? 'selected' : ''; ?>>Embed URL</option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="video_url">Video URL <span class="required">*</span></label></th>
                                <td>
                                    <input type="url" 
                                           id="video_url" 
                                           name="video_url" 
                                           class="large-text" 
                                           value="<?php echo $is_edit ? esc_url($banner->video_url) : ''; ?>" 
                                           placeholder="https://example.com/video.mp4" 
                                           required>
                                    
                                    <div id="video-url-hints" class="video-hints">
                                        <div class="hint-box hint-url active">
                                            <strong>📺 Direct MP4 URL:</strong>
                                            <code>https://yourdomain.com/videos/banner-video.mp4</code>
                                        </div>
                                        <div class="hint-box hint-youtube">
                                            <strong>📺 YouTube:</strong>
                                            <code>https://www.youtube.com/embed/VIDEO_ID</code><br>
                                            <small>Or: https://www.youtube.com/watch?v=VIDEO_ID</small>
                                        </div>
                                        <div class="hint-box hint-vimeo">
                                            <strong>🎞️ Vimeo:</strong>
                                            <code>https://player.vimeo.com/video/VIDEO_ID</code>
                                        </div>
                                        <div class="hint-box hint-embed">
                                            <strong>🎥 Any Video URL:</strong>
                                            <code>https://any-video-host.com/video-url</code>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Image Slider Settings -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>🖼️ Image Slider</h2>
                    </div>
                    <div class="inside">
                        <div class="images-upload-section">
                            <button type="button" class="button button-primary button-large" id="upload_images_button">
                                <span class="dashicons dashicons-upload"></span> Upload Images
                            </button>
                            <p class="description">Upload multiple images for the slider (displayed before hover)</p>
                            
                            <div id="images_preview_container" class="images-preview">
                                <?php if (!empty($images_array)): ?>
                                    <?php foreach ($images_array as $index => $image_url): ?>
                                        <div class="image-preview-item" data-index="<?php echo $index; ?>">
                                            <img src="<?php echo esc_url($image_url); ?>" alt="">
                                            <div class="image-actions">
                                                <button type="button" class="button button-small remove-image">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </button>
                                            </div>
                                            <input type="hidden" name="images[]" value="<?php echo esc_url($image_url); ?>">
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <input type="hidden" id="images_json" name="images" value="<?php echo $is_edit ? esc_attr($banner->images) : '[]'; ?>">
                        </div>
                        
                        <table class="form-table" style="margin-top: 20px;">
                            <tr>
                                <th><label for="slider_speed">Slider Speed</label></th>
                                <td>
                                    <input type="number" 
                                           id="slider_speed" 
                                           name="slider_speed" 
                                           class="small-text" 
                                           value="<?php echo $is_edit ? $banner->slider_speed : '3000'; ?>" 
                                           min="1000" 
                                           max="10000" 
                                           step="500">
                                    <span class="description">milliseconds (1000 = 1 second)</span>
                                    <p class="description">Time between slide transitions</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="transition">Transition Effect</label></th>
                                <td>
                                    <select id="transition" name="transition" class="regular-text">
                                        <option value="fade" <?php echo ($is_edit && $banner->transition == 'fade') ? 'selected' : ''; ?>>Fade</option>
                                        <option value="zoom" <?php echo ($is_edit && $banner->transition == 'zoom') ? 'selected' : ''; ?>>Zoom</option>
                                        <option value="blur" <?php echo ($is_edit && $banner->transition == 'blur') ? 'selected' : ''; ?>>Blur</option>
                                        <option value="slide" <?php echo ($is_edit && $banner->transition == 'slide') ? 'selected' : ''; ?>>Slide</option>
                                    </select>
                                    <p class="description">Transition effect when switching from slider to video</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Content Overlay -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>📝 Content Overlay (Optional)</h2>
                    </div>
                    <div class="inside">
                        <!-- Visibility Toggles -->
                        <div class="visibility-toggles" style="background: #f0f0f1; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                            <p style="margin-top: 0; font-weight: 600; color: #1d2327;">
                                <span class="dashicons dashicons-visibility" style="vertical-align: middle;"></span> 
                                Content Visibility Controls
                            </p>
                            <p class="description" style="margin-bottom: 15px;">
                                Toggle to show/hide content elements on the banner. By default, all elements are hidden.
                            </p>
                            
                            <table class="form-table" style="margin: 0;">
                                <tr>
                                    <th style="padding-top: 0;"><label for="show_title">Show Title</label></th>
                                    <td style="padding-top: 0;">
                                        <label class="switch">
                                            <input type="checkbox" 
                                                   id="show_title" 
                                                   name="show_title" 
                                                   value="1"
                                                   <?php echo ($is_edit && !empty($banner->show_title)) ? 'checked' : ''; ?>>
                                            <span class="slider-switch"></span>
                                        </label>
                                        <span class="description" style="margin-left: 10px;">Display the title text on banner</span>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th><label for="show_subtitle">Show Subtitle</label></th>
                                    <td>
                                        <label class="switch">
                                            <input type="checkbox" 
                                                   id="show_subtitle" 
                                                   name="show_subtitle" 
                                                   value="1"
                                                   <?php echo ($is_edit && !empty($banner->show_subtitle)) ? 'checked' : ''; ?>>
                                            <span class="slider-switch"></span>
                                        </label>
                                        <span class="description" style="margin-left: 10px;">Display the subtitle text on banner</span>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th><label for="show_button">Show CTA Button</label></th>
                                    <td>
                                        <label class="switch">
                                            <input type="checkbox" 
                                                   id="show_button" 
                                                   name="show_button" 
                                                   value="1"
                                                   <?php echo ($is_edit && !empty($banner->show_button)) ? 'checked' : ''; ?>>
                                            <span class="slider-switch"></span>
                                        </label>
                                        <span class="description" style="margin-left: 10px;">Display the call-to-action button</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th><label for="title">Title</label></th>
                                <td>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           class="large-text" 
                                           value="<?php echo $is_edit ? esc_attr($banner->title) : ''; ?>" 
                                           placeholder="An Mi Tools - CNC Solutions">
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="subtitle">Subtitle</label></th>
                                <td>
                                    <textarea id="subtitle" 
                                              name="subtitle" 
                                              class="large-text" 
                                              rows="3" 
                                              placeholder="Hơn 20 năm kinh nghiệm trong lĩnh vực công cụ cắt gọt"><?php echo $is_edit ? esc_textarea($banner->subtitle) : ''; ?></textarea>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="button_text">Button Text</label></th>
                                <td>
                                    <input type="text" 
                                           id="button_text" 
                                           name="button_text" 
                                           class="regular-text" 
                                           value="<?php echo $is_edit ? esc_attr($banner->button_text) : ''; ?>" 
                                           placeholder="Learn More">
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="button_link">Button Link</label></th>
                                <td>
                                    <input type="url" 
                                           id="button_link" 
                                           name="button_link" 
                                           class="large-text" 
                                           value="<?php echo $is_edit ? esc_url($banner->button_link) : ''; ?>" 
                                           placeholder="https://anmitools.com">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
            </div>
            
            <!-- Sidebar -->
            <div class="anmi-form-sidebar">
                
                <!-- Publish Box -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>Publish</h2>
                    </div>
                    <div class="inside">
                        <div class="submitbox">
                            <div class="status-section">
                                <label for="status"><strong>Status:</strong></label>
                                <select id="status" name="status" class="regular-text">
                                    <option value="active" <?php echo ($is_edit && $banner->status == 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo ($is_edit && $banner->status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            
                            <div class="submit-actions">
                                <button type="submit" class="button button-primary button-large">
                                    <span class="dashicons dashicons-saved"></span> 
                                    <?php echo $is_edit ? 'Update Banner' : 'Create Banner'; ?>
                                </button>
                                
                                <a href="<?php echo admin_url('admin.php?page=anmi-video-banners'); ?>" class="button button-secondary button-large">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Display Settings -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>⚙️ Display Settings</h2>
                    </div>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><label for="height">Height</label></th>
                                <td>
                                    <input type="text" 
                                           id="height" 
                                           name="height" 
                                           class="small-text" 
                                           value="<?php echo $is_edit ? esc_attr($banner->height) : '600px'; ?>">
                                    <p class="description">px, vh, or %</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="autoplay_delay">Autoplay Delay</label></th>
                                <td>
                                    <input type="number" 
                                           id="autoplay_delay" 
                                           name="autoplay_delay" 
                                           class="small-text" 
                                           value="<?php echo $is_edit ? $banner->autoplay_delay : '0'; ?>" 
                                           min="0" 
                                           max="10" 
                                           step="0.5">
                                    <span class="description">seconds</span>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="mobile_behavior">Mobile Behavior</label></th>
                                <td>
                                    <select id="mobile_behavior" name="mobile_behavior">
                                        <option value="image" <?php echo ($is_edit && $banner->mobile_behavior == 'image') ? 'selected' : ''; ?>>Image Only</option>
                                        <option value="video" <?php echo ($is_edit && $banner->mobile_behavior == 'video') ? 'selected' : ''; ?>>Video Only</option>
                                        <option value="both" <?php echo ($is_edit && $banner->mobile_behavior == 'both') ? 'selected' : ''; ?>>Both (Touch to Play)</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Shortcode Info -->
                <?php if ($is_edit): ?>
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>📋 Shortcode</h2>
                    </div>
                    <div class="inside">
                        <input type="text" 
                               class="shortcode-display" 
                               value='[anmi_video_banner id="<?php echo $banner->id; ?>"]' 
                               readonly 
                               onclick="this.select()">
                        <p class="description">Copy and paste this shortcode into any page, post, or widget.</p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Help -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>❓ Help</h2>
                    </div>
                    <div class="inside">
                        <ul class="help-list">
                            <li><strong>Video URL:</strong> Supports YouTube, Vimeo, or direct MP4 links</li>
                            <li><strong>Images:</strong> Upload multiple images for auto-rotating slider</li>
                            <li><strong>Hover Effect:</strong> Video plays when user hovers over banner</li>
                            <li><strong>Mobile:</strong> Choose slider-only, video-only, or touch-to-play</li>
                        </ul>
                    </div>
                </div>
                
            </div>
            
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    
    var imagesArray = <?php echo json_encode($images_array); ?>;
    
    // Video type change
    $('#video_type').on('change', function() {
        var type = $(this).val();
        $('.hint-box').removeClass('active');
        $('.hint-' + type).addClass('active');
    });
    
    // Upload images button
    $('#upload_images_button').on('click', function(e) {
        e.preventDefault();
        
        var mediaUploader = wp.media({
            title: 'Select Images for Slider',
            button: {
                text: 'Add to Slider'
            },
            multiple: true,
            library: {
                type: 'image'
            }
        });
        
        mediaUploader.on('select', function() {
            var attachments = mediaUploader.state().get('selection').toJSON();
            
            attachments.forEach(function(attachment) {
                imagesArray.push(attachment.url);
                
                var imageHtml = '<div class="image-preview-item" data-index="' + (imagesArray.length - 1) + '">' +
                    '<img src="' + attachment.url + '" alt="">' +
                    '<div class="image-actions">' +
                        '<button type="button" class="button button-small remove-image">' +
                            '<span class="dashicons dashicons-no-alt"></span>' +
                        '</button>' +
                    '</div>' +
                    '<input type="hidden" name="images[]" value="' + attachment.url + '">' +
                '</div>';
                
                $('#images_preview_container').append(imageHtml);
            });
            
            updateImagesJson();
        });
        
        mediaUploader.open();
    });
    
    // Remove image
    $(document).on('click', '.remove-image', function() {
        var $item = $(this).closest('.image-preview-item');
        var index = $item.data('index');
        
        imagesArray.splice(index, 1);
        $item.remove();
        
        // Reindex remaining items
        $('.image-preview-item').each(function(i) {
            $(this).attr('data-index', i);
        });
        
        updateImagesJson();
    });
    
    function updateImagesJson() {
        $('#images_json').val(JSON.stringify(imagesArray));
    }
    
    // Form submission
    $('#anmi-banner-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var originalText = $submitBtn.html();
        
        // Validate
        if (!$('#banner_name').val()) {
            alert('Please enter a banner name');
            return;
        }
        
        if (!$('#video_url').val()) {
            alert('Please enter a video URL');
            return;
        }
        
        if (imagesArray.length === 0) {
            alert('Please upload at least one image');
            return;
        }
        
        $submitBtn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Saving...');
        
        $.ajax({
            url: anmiBannerAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'anmi_save_banner',
                nonce: anmiBannerAdmin.nonce,
                banner_id: $('#banner_id').val(),
                name: $('#banner_name').val(),
                video_url: $('#video_url').val(),
                video_type: $('#video_type').val(),
                images: $('#images_json').val(),
                title: $('#title').val(),
                subtitle: $('#subtitle').val(),
                button_text: $('#button_text').val(),
                button_link: $('#button_link').val(),
                height: $('#height').val(),
                transition: $('#transition').val(),
                slider_speed: $('#slider_speed').val(),
                slider_effect: 'fade',
                autoplay_delay: $('#autoplay_delay').val(),
                mobile_behavior: $('#mobile_behavior').val(),
                status: $('#status').val()
            },
            success: function(response) {
                if (response.success) {
                    // Redirect to list page with success message
                    window.location.href = '<?php echo admin_url("admin.php?page=anmi-video-banners&message=saved"); ?>';
                } else {
                    alert('Error: ' + response.data);
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                alert('Failed to save banner. Please try again.');
                $submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Make images sortable
    $('#images_preview_container').sortable({
        update: function(event, ui) {
            var newOrder = [];
            $('.image-preview-item').each(function() {
                var imageUrl = $(this).find('input[name="images[]"]').val();
                newOrder.push(imageUrl);
            });
            imagesArray = newOrder;
            updateImagesJson();
        }
    });
});
</script>
