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
$page_title = $is_edit ? 'Chỉnh Sửa Banner' : 'Thêm Banner Mới';
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
                        <h2>📋 Thông Tin Banner</h2>
                    </div>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><label for="banner_name">Tên Banner <span class="required">*</span></label></th>
                                <td>
                                    <input type="text" 
                                           id="banner_name" 
                                           name="name" 
                                           class="regular-text" 
                                           value="<?php echo $is_edit ? esc_attr($banner->name) : ''; ?>" 
                                           required>
                                    <p class="description">Tên nội bộ để nhận diện banner (vd: "Banner Trang Chủ")</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Video Settings -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>🎬 Cài Đặt Video</h2>
                    </div>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><label for="video_type">Loại Video</label></th>
                                <td>
                                    <select id="video_type" name="video_type" class="regular-text">
                                        <option value="url" <?php echo ($is_edit && $banner->video_type == 'url') ? 'selected' : ''; ?>>URL Trực Tiếp (MP4)</option>
                                        <option value="youtube" <?php echo ($is_edit && $banner->video_type == 'youtube') ? 'selected' : ''; ?>>YouTube</option>
                                        <option value="vimeo" <?php echo ($is_edit && $banner->video_type == 'vimeo') ? 'selected' : ''; ?>>Vimeo</option>
                                        <option value="embed" <?php echo ($is_edit && $banner->video_type == 'embed') ? 'selected' : ''; ?>>Mã Nhúng</option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="video_url">URL Video <span class="required">*</span></label></th>
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
                        <h2>🖼️ Slider Hình Ảnh</h2>
                    </div>
                    <div class="inside">
                        <div class="images-upload-section">
                            <button type="button" class="button button-primary button-large" id="upload_images_button">
                                <span class="dashicons dashicons-upload"></span> Tải Lên Hình Ảnh
                            </button>
                            <p class="description">Tải lên nhiều hình ảnh cho slider (hiển thị trước khi hover)</p>
                            
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
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <input type="hidden" id="images_json" name="images" value="<?php echo $is_edit ? esc_attr($banner->images) : '[]'; ?>">
                        </div>
                        
                        <table class="form-table" style="margin-top: 20px;">
                            <tr>
                                <th><label for="slider_speed">Tốc Độ Slider</label></th>
                                <td>
                                    <input type="number" 
                                           id="slider_speed" 
                                           name="slider_speed" 
                                           class="small-text" 
                                           value="<?php echo $is_edit ? $banner->slider_speed : '3000'; ?>" 
                                           min="1000" 
                                           max="10000" 
                                           step="500">
                                    <span class="description">mili giây (1000 = 1 giây)</span>
                                    <p class="description">Thời gian giữa các lần chuyển slide</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="transition">Hiệu Ứng Chuyển</label></th>
                                <td>
                                    <select id="transition" name="transition" class="regular-text">
                                        <option value="fade" <?php echo ($is_edit && $banner->transition == 'fade') ? 'selected' : ''; ?>>Mờ Dần</option>
                                        <option value="zoom" <?php echo ($is_edit && $banner->transition == 'zoom') ? 'selected' : ''; ?>>Phóng To</option>
                                        <option value="blur" <?php echo ($is_edit && $banner->transition == 'blur') ? 'selected' : ''; ?>>Làm Mờ</option>
                                        <option value="slide" <?php echo ($is_edit && $banner->transition == 'slide') ? 'selected' : ''; ?>>Trượt</option>
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
                        <h2>📝 Nội Dung Hiển Thị (Tùy Chọn)</h2>
                    </div>
                    <div class="inside">
                        <!-- Visibility Toggles -->
                        <div class="visibility-toggles" style="background: #f0f0f1; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                            <p style="margin-top: 0; font-weight: 600; color: #1d2327;">
                                <span class="dashicons dashicons-visibility" style="vertical-align: middle;"></span> 
                                Điều Khiển Hiển Thị Nội Dung
                            </p>
                            <p class="description" style="margin-bottom: 15px;">
                                Bật/tắt để hiển thị/ẩn các thành phần nội dung trên banner. Mặc định, tất cả đều ẩn.
                            </p>
                            
                            <table class="form-table" style="margin: 0;">
                                <tr>
                                    <th style="padding-top: 0;"><label for="show_title">Hiển Thị Tiêu Đề</label></th>
                                    <td style="padding-top: 0;">
                                        <label class="switch">
                                            <input type="checkbox" 
                                                   id="show_title" 
                                                   name="show_title" 
                                                   value="1"
                                                   <?php echo ($is_edit && !empty($banner->show_title)) ? 'checked' : ''; ?>>
                                            <span class="slider-switch"></span>
                                        </label>
                                        <span class="description" style="margin-left: 10px;">Hiển thị tiêu đề trên banner</span>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th><label for="show_subtitle">Hiển Thị Phụ Đề</label></th>
                                    <td>
                                        <label class="switch">
                                            <input type="checkbox" 
                                                   id="show_subtitle" 
                                                   name="show_subtitle" 
                                                   value="1"
                                                   <?php echo ($is_edit && !empty($banner->show_subtitle)) ? 'checked' : ''; ?>>
                                            <span class="slider-switch"></span>
                                        </label>
                                        <span class="description" style="margin-left: 10px;">Hiển thị phụ đề trên banner</span>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th><label for="show_button">Hiển Thị Nút CTA</label></th>
                                    <td>
                                        <label class="switch">
                                            <input type="checkbox" 
                                                   id="show_button" 
                                                   name="show_button" 
                                                   value="1"
                                                   <?php echo ($is_edit && !empty($banner->show_button)) ? 'checked' : ''; ?>>
                                            <span class="slider-switch"></span>
                                        </label>
                                        <span class="description" style="margin-left: 10px;">Hiển thị nút kêu gọi hành động</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th><label for="title">Tiêu Đề</label></th>
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
                                <th><label for="subtitle">Phụ Đề</label></th>
                                <td>
                                    <textarea id="subtitle" 
                                              name="subtitle" 
                                              class="large-text" 
                                              rows="3" 
                                              placeholder="Hơn 20 năm kinh nghiệm trong lĩnh vực công cụ cắt gọt"><?php echo $is_edit ? esc_textarea($banner->subtitle) : ''; ?></textarea>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="button_text">Văn Bản Nút</label></th>
                                <td>
                                    <input type="text" 
                                           id="button_text" 
                                           name="button_text" 
                                           class="regular-text" 
                                           value="<?php echo $is_edit ? esc_attr($banner->button_text) : ''; ?>" 
                                           placeholder="Tìm Hiểu Thêm">
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="button_link">Liên Kết Nút</label></th>
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
                        <h2>Xuất Bản</h2>
                    </div>
                    <div class="inside">
                        <div class="submitbox">
                            <div class="status-section">
                                <label for="status"><strong>Trạng Thái:</strong></label>
                                <select id="status" name="status" class="regular-text">
                                    <option value="active" <?php echo ($is_edit && $banner->status == 'active') ? 'selected' : ''; ?>>Kích Hoạt</option>
                                    <option value="inactive" <?php echo ($is_edit && $banner->status == 'inactive') ? 'selected' : ''; ?>>Vô Hiệu</option>
                                </select>
                            </div>
                            
                            <div class="submit-actions">
                                <button type="submit" class="button button-primary button-large">
                                    <span class="dashicons dashicons-saved"></span> 
                                    <?php echo $is_edit ? 'Cập Nhật Banner' : 'Tạo Banner'; ?>
                                </button>
                                
                                <a href="<?php echo admin_url('admin.php?page=anmi-video-banners'); ?>" class="button button-secondary button-large">
                                    Hủy Bỏ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Display Settings -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>⚙️ Cài Đặt Hiển Thị</h2>
                    </div>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><label for="height">Chiều Cao</label></th>
                                <td>
                                    <input type="text" 
                                           id="height" 
                                           name="height" 
                                           class="small-text" 
                                           value="<?php echo $is_edit ? esc_attr($banner->height) : '600px'; ?>">
                                    <p class="description">px, vh, hoặc %</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="autoplay_delay">Độ Trễ Tự Chạy</label></th>
                                <td>
                                    <input type="number" 
                                           id="autoplay_delay" 
                                           name="autoplay_delay" 
                                           class="small-text" 
                                           value="<?php echo $is_edit ? $banner->autoplay_delay : '0'; ?>" 
                                           min="0" 
                                           max="10" 
                                           step="0.5">
                                    <span class="description">giây</span>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="mobile_behavior">Hành Vi Mobile</label></th>
                                <td>
                                    <select id="mobile_behavior" name="mobile_behavior">
                                        <option value="image" <?php echo ($is_edit && $banner->mobile_behavior == 'image') ? 'selected' : ''; ?>>Chỉ Hình Ảnh</option>
                                        <option value="video" <?php echo ($is_edit && $banner->mobile_behavior == 'video') ? 'selected' : ''; ?>>Chỉ Video</option>
                                        <option value="both" <?php echo ($is_edit && $banner->mobile_behavior == 'both') ? 'selected' : ''; ?>>Cả Hai (Chạm để Phát)</option>
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
                        <h2>📋 Mã Nhúng</h2>
                    </div>
                    <div class="inside">
                        <input type="text" 
                               class="shortcode-display" 
                               value='[anmi_video_banner id="<?php echo $banner->id; ?>"]' 
                               readonly 
                               onclick="this.select()">
                        <p class="description">Sao chép và dán mã nhúng này vào bất kỳ trang, bài viết hoặc widget nào.</p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Help -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>❓ Trợ Giúp</h2>
                    </div>
                    <div class="inside">
                        <ul class="help-list">
                            <li><strong>URL Video:</strong> Hỗ trợ YouTube, Vimeo, hoặc link MP4 trực tiếp</li>
                            <li><strong>Hình Ảnh:</strong> Tải lên nhiều hình ảnh cho slider tự động xoay</li>
                            <li><strong>Hiệu Ứng Hover:</strong> Video phát khi người dùng rê chuột lên banner</li>
                            <li><strong>Mobile:</strong> Chọn chỉ slider, chỉ video, hoặc chạm để phát</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Live Preview -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>👁️ Xem Trước Trực Tiếp</h2>
                    </div>
                    <div class="inside">
                        <p class="description" style="margin-bottom: 10px;">Hover chuột để xem video phát</p>
                        <div id="live_preview_container" style="height: 300px; position: relative; background: #000; border-radius: 4px; overflow: hidden;">
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #fff; opacity: 0.5;">
                                <span class="dashicons dashicons-format-video" style="font-size: 48px; width: 48px; height: 48px;"></span>
                                <p>Nhập Video URL và tải lên hình ảnh<br>để xem preview</p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    
    var imagesArray = <?php echo json_encode($images_array); ?>;
    
    // DEBUG: Log initial state
    console.log('=== ANMI VIDEO BANNER DEBUG ===');
    console.log('Initial imagesArray:', imagesArray);
    console.log('Images JSON field value:', $('#images_json').val());
    
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
                '</div>';
                
                $('#images_preview_container').append(imageHtml);
            });
            
            updateImagesJson();
            updateLivePreview(); // Update preview after adding images
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
        updateLivePreview(); // Update preview after removing image
    });
    
    function updateImagesJson() {
        var jsonString = JSON.stringify(imagesArray);
        $('#images_json').val(jsonString);
        console.log('Updated images JSON:', jsonString);
        console.log('imagesArray length:', imagesArray.length);
    }
    
    // Form submission
    $('#anmi-banner-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var originalText = $submitBtn.html();
        
        // DEBUG: Log form data before submit
        console.log('=== FORM SUBMIT DEBUG ===');
        console.log('imagesArray:', imagesArray);
        console.log('images_json value:', $('#images_json').val());
        
        // Validate
        if (!$('#banner_name').val()) {
            alert('Vui lòng nhập tên banner');
            return;
        }
        
        if (!$('#video_url').val()) {
            alert('Vui lòng nhập URL video');
            return;
        }
        
        if (imagesArray.length === 0) {
            alert('Vui lòng tải lên ít nhất một hình ảnh');
            return;
        }
        
        $submitBtn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Đang lưu...');
        
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
                    alert('Lỗi: ' + response.data);
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                alert('Không thể lưu banner. Vui lòng thử lại.');
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
            updateLivePreview(); // Update preview when reorder
        }
    });
    
    // ==========================================
    // LIVE PREVIEW FUNCTIONALITY
    // ==========================================
    
    var previewInstance = null;
    
    function updateLivePreview() {
        var videoUrl = $('#video_url').val();
        var title = $('#banner_title').val();
        var subtitle = $('#banner_subtitle').val();
        var buttonText = $('#button_text').val();
        var buttonLink = $('#button_link').val();
        var showTitle = $('#show_title').is(':checked') ? '1' : '0';
        var showSubtitle = $('#show_subtitle').is(':checked') ? '1' : '0';
        var showButton = $('#show_button').is(':checked') ? '1' : '0';
        var transition = $('#transition').val();
        var autoplayDelay = $('#autoplay_delay').val();
        var sliderSpeed = $('#slider_speed').val() || '3000';
        var sliderEffect = $('#slider_effect').val();
        
        // Check if we have minimum requirements
        if (!videoUrl || imagesArray.length === 0) {
            $('#live_preview_container').html(
                '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #fff; opacity: 0.5;">' +
                '<span class="dashicons dashicons-format-video" style="font-size: 48px; width: 48px; height: 48px;"></span>' +
                '<p>Nhập Video URL và tải lên hình ảnh<br>để xem preview</p>' +
                '</div>'
            );
            return;
        }
        
        // Detect video type
        var videoType = 'direct';
        var embedUrl = videoUrl;
        var videoId = null;
        
        // YouTube detection
        var youtubeMatch = videoUrl.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i);
        if (youtubeMatch) {
            videoType = 'youtube';
            videoId = youtubeMatch[1];
            embedUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&mute=1&loop=1&playlist=' + videoId + '&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1';
        }
        
        // Vimeo detection
        var vimeoMatch = videoUrl.match(/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)(?:$|\/|\?)/i);
        if (vimeoMatch) {
            videoType = 'vimeo';
            videoId = vimeoMatch[1];
            embedUrl = 'https://player.vimeo.com/video/' + videoId + '?autoplay=1&muted=1&loop=1&background=1&controls=0';
        }
        
        // Generate unique ID
        var uniqueId = 'anmi-preview-' + Date.now();
        
        // Build HTML
        var html = '<div class="anmi-video-banner-container ' + uniqueId + ' transition-' + transition + '" ' +
                   'style="height: 300px;" ' +
                   'data-autoplay-delay="' + autoplayDelay + '" ' +
                   'data-mobile-behavior="both" ' +
                   'data-slider-speed="' + sliderSpeed + '" ' +
                   'data-slider-effect="' + sliderEffect + '" ' +
                   'data-video-type="' + videoType + '">';
        
        // Video/Iframe
        if (videoType === 'youtube' || videoType === 'vimeo') {
            html += '<iframe class="anmi-banner-video anmi-banner-iframe" ' +
                    'src="' + embedUrl + '" ' +
                    'frameborder="0" ' +
                    'allow="autoplay; fullscreen; picture-in-picture" ' +
                    'allowfullscreen ' +
                    'style="pointer-events: none;"></iframe>';
        } else {
            html += '<video class="anmi-banner-video" loop muted playsinline preload="metadata" ' +
                    'poster="' + (imagesArray[0] || '') + '">' +
                    '<source src="' + embedUrl + '" type="video/mp4">' +
                    '</video>';
        }
        
        // Image Slider
        html += '<div class="anmi-banner-slider">';
        imagesArray.forEach(function(imageUrl, index) {
            html += '<div class="anmi-slider-slide ' + (index === 0 ? 'active' : '') + '" ' +
                    'style="background-image: url(\'' + imageUrl + '\');"></div>';
        });
        
        // Slider dots (if multiple images)
        if (imagesArray.length > 1) {
            html += '<div class="anmi-slider-dots">';
            imagesArray.forEach(function(imageUrl, index) {
                html += '<span class="dot ' + (index === 0 ? 'active' : '') + '" data-slide="' + index + '"></span>';
            });
            html += '</div>';
        }
        html += '</div>'; // Close slider
        
        // Content Overlay
        if (title || subtitle || buttonText) {
            html += '<div class="anmi-banner-content" ' +
                    'data-show-title="' + showTitle + '" ' +
                    'data-show-subtitle="' + showSubtitle + '" ' +
                    'data-show-button="' + showButton + '">';
            
            if (title && showTitle === '1') {
                html += '<h1 class="anmi-banner-title">' + title + '</h1>';
            }
            if (subtitle && showSubtitle === '1') {
                html += '<p class="anmi-banner-subtitle">' + subtitle + '</p>';
            }
            if (buttonText && showButton === '1') {
                html += '<a href="' + buttonLink + '" class="anmi-banner-btn">' + buttonText + '</a>';
            }
            
            html += '</div>';
        }
        
        // Loading spinner
        html += '<div class="anmi-banner-loader"><div class="spinner"></div></div>';
        
        html += '</div>'; // Close container
        
        // Inject HTML
        $('#live_preview_container').html(html);
        
        // Destroy previous instance if exists
        if (previewInstance) {
            try {
                previewInstance = null;
            } catch(e) {}
        }
        
        // Initialize banner functionality
        setTimeout(function() {
            var $container = $('.' + uniqueId);
            if ($container.length && typeof AnMiVideoBanner !== 'undefined') {
                previewInstance = new AnMiVideoBanner($container[0]);
            }
        }, 100);
    }
    
    // Trigger preview update on field changes
    $('#video_url, #banner_title, #banner_subtitle, #button_text, #button_link, #transition, #autoplay_delay, #slider_speed, #slider_effect').on('input change', function() {
        updateLivePreview();
    });
    
    $('#show_title, #show_subtitle, #show_button').on('change', function() {
        updateLivePreview();
    });
    
    // Initial preview render
    updateLivePreview();
});
</script>
