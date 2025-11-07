<?php
/**
 * Admin Edit View - Add/Edit Banner Form
 * Last Updated: 2025-11-07 19:20 - Fixed videoType bug
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Parse images if editing
$images_array = array();
if ($banner && !empty($banner->image_list)) {
    $images_array = json_decode($banner->image_list, true);
    if (!is_array($images_array)) {
        $images_array = array();
    }
}

$is_edit = ($banner && $banner->banner_id > 0);
$page_title = $is_edit ? 'Chỉnh Sửa Banner' : 'Thêm Banner Mới';
?>

<div class="wrap anmi-banner-admin anmi-banner-edit">
    <h1><?php echo $page_title; ?></h1>
    <hr class="wp-header-end">
    
    <form id="anmi-banner-form" method="post">
    <?php wp_nonce_field('abvp_banner_nonce', 'abvp_nonce'); ?>
    <input type="hidden" name="banner_id" id="banner_id" value="<?php echo $is_edit ? $banner->banner_id : '0'; ?>">
        
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
                                           value="<?php echo $is_edit ? esc_attr($banner->banner_name) : ''; ?>" 
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
                                        <option value="url" <?php echo ($is_edit && $banner->video_input_type == 'url') ? 'selected' : ''; ?>>URL Trực Tiếp (MP4)</option>
                                        <option value="youtube" <?php echo ($is_edit && $banner->video_input_type == 'youtube') ? 'selected' : ''; ?>>YouTube</option>
                                        <option value="vimeo" <?php echo ($is_edit && $banner->video_input_type == 'vimeo') ? 'selected' : ''; ?>>Vimeo</option>
                                        <option value="embed" <?php echo ($is_edit && $banner->video_input_type == 'embed') ? 'selected' : ''; ?>>Mã Nhúng</option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr id="video-url-row">
                                <th><label for="video_url">URL Video <span class="required">*</span></label></th>
                                <td>
                                    <input type="url" 
                                           id="video_url" 
                                           name="video_url" 
                                           class="large-text" 
                                           value="<?php echo $is_edit ? esc_url($banner->video_url_value) : ''; ?>" 
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
                            
                            <tr id="video-embed-row" style="display: none;">
                                <th><label for="video_embed_code">Mã Nhúng Iframe <span class="required">*</span></label></th>
                                <td>
                                    <textarea 
                                        id="video_embed_code" 
                                        name="video_embed_code" 
                                        class="large-text" 
                                        rows="8"
                                        placeholder='Paste iframe code here...&#10;Example:&#10;<iframe width="560" height="315" src="https://www.youtube.com/embed/VIDEO_ID" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'
                                    ><?php echo $is_edit && $banner->video_input_type == 'embed' ? esc_textarea($banner->video_url_value) : ''; ?></textarea>
                                    <p class="description">
                                        <strong>📋 Cách lấy mã nhúng:</strong><br>
                                        • <strong>YouTube:</strong> Vào video → Click "Share" → Click "Embed" → Copy toàn bộ code <code>&lt;iframe...&gt;</code><br>
                                        • <strong>Vimeo:</strong> Vào video → Click biểu tượng "Share" → Copy embed code<br>
                                        • Plugin sẽ <strong>lưu toàn bộ mã nhúng</strong> và tự động trích xuất URL khi hiển thị
                                    </p>
                                </td>
                            </tr>
                        </table>
                        
                        <!-- Separator -->
                        <hr style="margin: 30px 0; border: none; border-top: 2px solid #e5e5e5;">
                        
                        <!-- Video Playback Settings (Embedded) -->
                        <h3 style="margin: 20px 0 15px 0; padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-radius: 4px;">
                            <span class="dashicons dashicons-controls-play" style="vertical-align: middle;"></span> 
                            🎛️ Cài Đặt Phát Video (YouTube/Vimeo)
                        </h3>
                        
                        <div style="background: #f0f0f1; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                            <p style="margin-top: 0; font-weight: 600; color: #1d2327;">
                                <span class="dashicons dashicons-info" style="vertical-align: middle;"></span> 
                                Điều Khiển Thông Số Video
                            </p>
                            <p class="description" style="margin-bottom: 0;">
                                Các cài đặt này chỉ áp dụng cho video YouTube và Vimeo. Video MP4 sẽ luôn tự động phát, lặp lại và tắt tiếng.
                            </p>
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th><label for="video_autoplay">🎬 Tự Động Phát</label></th>
                                <td>
                                    <label class="switch" title="Bật/Tắt tự động phát video">
                         <input type="checkbox" 
                             id="video_autoplay" 
                             name="video_autoplay" 
                             value="1"
                             <?php echo ($is_edit ? (!empty($banner->enable_autoplay) ? 'checked' : '') : 'checked'); ?>>
                                        <span class="slider-switch"></span>
                                    </label>
                                    <span class="description" style="margin-left: 10px;">
                                        <strong id="video_autoplay_status" style="color: #46b450;">✓ ĐANG BẬT</strong> - Video tự động phát khi trang tải
                                    </span>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="video_muted">🔇 Tắt Tiếng</label></th>
                                <td>
                                    <label class="switch" title="Bật/Tắt tiếng video">
                         <input type="checkbox" 
                             id="video_muted" 
                             name="video_muted" 
                             value="1"
                             <?php echo ($is_edit ? (!empty($banner->enable_muted) ? 'checked' : '') : 'checked'); ?>>
                                        <span class="slider-switch"></span>
                                    </label>
                                    <span class="description" style="margin-left: 10px;">
                                        <strong id="video_muted_status" style="color: #d63638;">✓ ĐANG BẬT</strong> - Video bắt đầu không có tiếng
                                    </span>
                                    <p class="description" style="margin: 8px 0 0 0; color: #FF9800;">
                                        <strong>💡 Khuyến nghị:</strong> Nên BẬT tắt tiếng khi dùng tự động phát để tránh bị trình duyệt chặn.
                                    </p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="video_loop">🔁 Lặp Lại</label></th>
                                <td>
                                    <label class="switch" title="Bật/Tắt lặp lại video">
                         <input type="checkbox" 
                             id="video_loop" 
                             name="video_loop" 
                             value="1"
                             <?php echo ($is_edit ? (!empty($banner->enable_loop) ? 'checked' : '') : 'checked'); ?>>
                                        <span class="slider-switch"></span>
                                    </label>
                                    <span class="description" style="margin-left: 10px;">
                                        <strong id="video_loop_status" style="color: #46b450;">✓ ĐANG BẬT</strong> - Video tự động phát lại khi kết thúc
                                    </span>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="video_controls">🎚️ Hiện Controls</label></th>
                                <td>
                                    <label class="switch" title="Bật/Tắt thanh điều khiển video">
                         <input type="checkbox" 
                             id="video_controls" 
                             name="video_controls" 
                             value="1"
                             <?php echo ($is_edit ? (!empty($banner->enable_controls) ? 'checked' : '') : 'checked'); ?>>
                                        <span class="slider-switch"></span>
                                    </label>
                                    <span class="description" style="margin-left: 10px;">
                                        <strong id="video_controls_status" style="color: #46b450;">✓ ĐANG BẬT</strong> - Hiển thị thanh điều khiển video (play/pause/volume)
                                    </span>
                                    <p class="description" style="margin: 8px 0 0 0; color: #2271b1;">
                                        <strong>💡 Khuyến nghị:</strong> Nên BẬT để người dùng có thể điều chỉnh âm lượng và tạm dừng video.
                                    </p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="enable_slider">🖼️ Bật Slider Hình Ảnh</label></th>
                                <td>
                                    <label class="switch" title="Bật/Tắt slider hình ảnh">
                         <input type="checkbox" 
                             id="enable_slider" 
                             name="enable_slider" 
                             value="1"
                             <?php echo ($is_edit ? (!empty($banner->enable_slider) ? 'checked' : '') : 'checked'); ?>>
                                        <span class="slider-switch"></span>
                                    </label>
                                    <span class="description" style="margin-left: 10px;">
                                        <strong id="enable_slider_status" style="color: #46b450;">✓ ĐANG BẬT</strong> - Hiển thị slider hình ảnh phía trên video
                                    </span>
                                    <p class="description" style="margin: 8px 0 0 0; color: #666;">
                                        <strong>💡 Lưu ý:</strong> Master switch để bật/tắt slider. Điều chỉnh theo thiết bị ở phần "⚙️ Thiết Lập Theo Thiết Bị" bên dưới.
                                    </p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="video_modestbranding">📺 Ẩn Logo YouTube</label></th>
                                <td>
                                    <label class="switch" title="Bật/Tắt ẩn logo YouTube">
                         <input type="checkbox" 
                             id="video_modestbranding" 
                             name="video_modestbranding" 
                             value="1"
                             <?php echo ($is_edit ? (!empty($banner->enable_modestbranding) ? 'checked' : '') : 'checked'); ?>>
                                        <span class="slider-switch"></span>
                                    </label>
                                    <span class="description" style="margin-left: 10px;">
                                        <strong id="video_modestbranding_status" style="color: #46b450;">✓ ĐANG BẬT</strong> - Ẩn logo YouTube trong player (chỉ YouTube)
                                    </span>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="video_rel">🔗 Hiện Video Liên Quan</label></th>
                                <td>
                                    <label class="switch" title="Bật/Tắt hiển thị video liên quan">
                         <input type="checkbox" 
                             id="video_rel" 
                             name="video_rel" 
                             value="1"
                             <?php echo ($is_edit ? (!empty($banner->enable_rel) ? 'checked' : '') : ''); ?>>
                                        <span class="slider-switch"></span>
                                    </label>
                                    <span class="description" style="margin-left: 10px;">
                                        <strong id="video_rel_status" style="color: #d63638;">✗ ĐANG TẮT</strong> - Không hiển thị video liên quan khi kết thúc (chỉ YouTube)
                                    </span>
                                </td>
                            </tr>
                        </table>
                        
                        <!-- Separator -->
                        <hr style="margin: 30px 0; border: none; border-top: 2px solid #e5e5e5;">
                        
                        <!-- CUSTOM OVERLAY IMAGE UPLOAD -->
                        <div class="abvp-overlay-image-upload" style="margin: 20px 0; padding: 20px; background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 5px;">
                            <h3 style="margin-top: 0; color: #2271b1; padding: 10px; background: #fff; border-left: 4px solid #2271b1; border-radius: 3px;">
                                <span class="dashicons dashicons-format-image" style="font-size: 20px; vertical-align: middle;"></span>
                                🎬 Hình Ảnh Lớp Phủ (Overlay Image)
                            </h3>
                            <p style="color: #646970; margin: 15px 0; line-height: 1.6;">
                                <strong>Chức năng:</strong> Upload hình ảnh riêng cho lớp phủ video (hiển thị trước khi video phát, kèm nút play button ở giữa).<br>
                                Lớp phủ này <strong>hoàn toàn độc lập</strong> với slider pagination, luôn hiển thị rõ ràng ở z-index cao nhất, không bị nhấp nháy.
                            </p>
                            
                            <div class="overlay-image-preview" style="margin: 15px 0;">
                                <?php 
                                // Debug: Log overlay_image value
                                if ($is_edit) {
                                    error_log('ABVP Debug - Banner ID: ' . $banner->banner_id);
                                    error_log('ABVP Debug - overlay_image value: ' . ($banner->overlay_image ?? 'NULL'));
                                    error_log('ABVP Debug - overlay_image empty: ' . (empty($banner->overlay_image) ? 'YES' : 'NO'));
                                }
                                ?>
                                <?php if ($is_edit && !empty($banner->overlay_image)): ?>
                                    <img src="<?php echo esc_url($banner->overlay_image); ?>" 
                                         style="max-width: 400px; max-height: 300px; display: block; border: 2px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" 
                                         id="overlay_image_preview">
                                    <p style="color: #46b450; margin-top: 10px;">
                                        <span class="dashicons dashicons-yes-alt"></span> 
                                        <strong>Đã có hình overlay</strong>
                                    </p>
                                <?php else: ?>
                                    <img src="" 
                                         style="max-width: 400px; max-height: 300px; display: none; border: 2px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" 
                                         id="overlay_image_preview">
                                    <p style="color: #999; font-style: italic;">
                                        <span class="dashicons dashicons-info"></span> 
                                        Chưa có hình overlay (sẽ dùng hình đầu tiên từ slider)
                                    </p>
                                    <?php if ($is_edit): ?>
                                    <p style="color: #d63638; font-size: 12px;">
                                        <span class="dashicons dashicons-warning"></span> 
                                        Debug: overlay_image = "<?php echo esc_attr($banner->overlay_image ?? 'NULL'); ?>"
                                    </p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            
                            <input type="hidden" 
                                   id="overlay_image" 
                                   name="overlay_image" 
                                   value="<?php echo $is_edit ? esc_attr($banner->overlay_image ?? '') : ''; ?>">
                            
                            <button type="button" class="button button-secondary" id="upload_overlay_image_button" style="margin-right: 10px;">
                                <span class="dashicons dashicons-upload"></span> Upload Hình Overlay
                            </button>
                            
                            <button type="button" class="button" id="remove_overlay_image_button" 
                                    style="<?php echo ($is_edit && !empty($banner->overlay_image)) ? '' : 'display:none;'; ?>">
                                <span class="dashicons dashicons-no"></span> Xóa Hình
                            </button>
                            
                            <div style="margin-top: 15px; padding: 12px; background: #fffbf0; border-left: 4px solid #ffb900; border-radius: 3px;">
                                <strong style="color: #8a6d3b;">💡 Gợi ý:</strong>
                                <ul style="margin: 10px 0 0 20px; color: #646970; line-height: 1.8;">
                                    <li>Kích thước khuyến nghị: <strong>1920x1080px</strong> hoặc tỷ lệ 16:9</li>
                                    <li>Nút play button sẽ <strong>tự động hiển thị ở giữa</strong> hình</li>
                                    <li>Nếu không upload, sẽ <strong>dùng hình đầu tiên từ slider</strong></li>
                                    <li>Lớp phủ này <strong>không bị ảnh hưởng</strong> bởi slider pagination transition</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Separator -->
                        <hr style="margin: 30px 0; border: none; border-top: 2px solid #e5e5e5;">
                        
                        <!-- Device-Specific Settings (Embedded) -->
                        <h3 style="margin: 20px 0 15px 0; padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-radius: 4px;">
                            <span class="dashicons dashicons-smartphone" style="vertical-align: middle;"></span> 
                            ⚙️ Thiết Lập Theo Thiết Bị
                        </h3>
                        
                        <p class="description" style="margin: 0 0 15px 0;">
                            Tùy chỉnh cách hiển thị overlay và hiệu ứng hover riêng cho Desktop và Mobile
                        </p>
                        
                        <table class="abvp-device-settings-table">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">
                                        <span class="settings-icon">🖥️</span> Desktop (≥769px)
                                    </th>
                                    <th style="width: 50%;">
                                        <span class="settings-icon">📱</span> Mobile (≤768px)
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- OVERLAY MASTER SWITCH -->
                                <tr>
                                    <td colspan="2">
                                        <div class="abvp-master-switch">
                                            <div class="abvp-setting-row">
                                                <span class="abvp-setting-icon">🎭</span>
                                                <div class="abvp-setting-content">
                                                    <label class="abvp-setting-label" for="enable_overlay">
                                                        Lớp Phủ Video (Overlay)
                                                    </label>
                                                    <div class="abvp-setting-description">
                                                        Master switch - Lớp phủ hiển thị trước khi video phát (poster image + play button)
                                                    </div>
                                                    <div class="abvp-setting-control">
                                                        <label class="switch">
                                                            <input type="checkbox" 
                                                                id="enable_overlay" 
                                                                name="enable_overlay" 
                                                                value="1"
                                                                <?php echo ($is_edit ? (!empty($banner->enable_overlay) ? 'checked' : '') : 'checked'); ?>>
                                                            <span class="slider-switch"></span>
                                                        </label>
                                                        <span class="abvp-status-badge enabled" id="enable_overlay_status">
                                                            ✓ ĐANG BẬT
                                                        </span>
                                                    </div>
                                                    <div class="abvp-info-box">
                                                        <strong>💡 Khi tắt:</strong> Video sẽ tự động phát ngay, không hiển thị play button
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- OVERLAY DEVICE SETTINGS -->
                                <tr>
                                    <td data-label="🖥️ Desktop">
                                        <div class="abvp-setting-row">
                                            <span class="abvp-setting-icon">🎭</span>
                                            <div class="abvp-setting-content">
                                                <label class="abvp-setting-label" for="enable_overlay_desktop">
                                                    Overlay trên Desktop
                                                </label>
                                                <div class="abvp-setting-description">
                                                    Hiển thị lớp phủ với play button trên màn hình desktop
                                                </div>
                                                <div class="abvp-setting-control">
                                                    <label class="switch">
                                                        <input type="checkbox" 
                                                            id="enable_overlay_desktop" 
                                                            name="enable_overlay_desktop" 
                                                            value="1"
                                                            <?php echo ($is_edit ? (!empty($banner->enable_overlay_desktop) ? 'checked' : '') : 'checked'); ?>>
                                                        <span class="slider-switch"></span>
                                                    </label>
                                                    <span class="abvp-status-badge enabled" id="enable_overlay_desktop_status">
                                                        ✓ BẬT
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="📱 Mobile">
                                        <div class="abvp-setting-row">
                                            <span class="abvp-setting-icon">🎭</span>
                                            <div class="abvp-setting-content">
                                                <label class="abvp-setting-label" for="enable_overlay_mobile">
                                                    Overlay trên Mobile
                                                </label>
                                                <div class="abvp-setting-description">
                                                    Hiển thị lớp phủ với play button trên thiết bị di động
                                                </div>
                                                <div class="abvp-setting-control">
                                                    <label class="switch">
                                                        <input type="checkbox" 
                                                            id="enable_overlay_mobile" 
                                                            name="enable_overlay_mobile" 
                                                            value="1"
                                                            <?php echo ($is_edit ? (!empty($banner->enable_overlay_mobile) ? 'checked' : '') : 'checked'); ?>>
                                                        <span class="slider-switch"></span>
                                                    </label>
                                                    <span class="abvp-status-badge enabled" id="enable_overlay_mobile_status">
                                                        ✓ BẬT
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- SLIDER DEVICE SETTINGS -->
                                <tr>
                                    <td data-label="🖥️ Desktop">
                                        <div class="abvp-setting-row">
                                            <span class="abvp-setting-icon">🖼️</span>
                                            <div class="abvp-setting-content">
                                                <label class="abvp-setting-label" for="enable_slider_desktop">
                                                    Slider trên Desktop
                                                </label>
                                                <div class="abvp-setting-description">
                                                    Hiển thị slider hình ảnh trên màn hình desktop
                                                </div>
                                                <div class="abvp-setting-control">
                                                    <label class="switch">
                                                        <input type="checkbox" 
                                                            id="enable_slider_desktop" 
                                                            name="enable_slider_desktop" 
                                                            value="1"
                                                            <?php echo ($is_edit ? (!empty($banner->enable_slider_desktop) ? 'checked' : '') : 'checked'); ?>>
                                                        <span class="slider-switch"></span>
                                                    </label>
                                                    <span class="abvp-status-badge enabled" id="enable_slider_desktop_status">
                                                        ✓ BẬT
                                                    </span>
                                                </div>
                                                <div class="abvp-info-box">
                                                    <strong>💡 Gợi ý:</strong> Tắt để chỉ hiển thị video background trên desktop (hiệu ứng professional hơn)
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="📱 Mobile">
                                        <div class="abvp-setting-row">
                                            <span class="abvp-setting-icon">🖼️</span>
                                            <div class="abvp-setting-content">
                                                <label class="abvp-setting-label" for="enable_slider_mobile">
                                                    Slider trên Mobile
                                                </label>
                                                <div class="abvp-setting-description">
                                                    Hiển thị slider hình ảnh trên thiết bị di động
                                                </div>
                                                <div class="abvp-setting-control">
                                                    <label class="switch">
                                                        <input type="checkbox" 
                                                            id="enable_slider_mobile" 
                                                            name="enable_slider_mobile" 
                                                            value="1"
                                                            <?php echo ($is_edit ? (!empty($banner->enable_slider_mobile) ? 'checked' : '') : 'checked'); ?>>
                                                        <span class="slider-switch"></span>
                                                    </label>
                                                    <span class="abvp-status-badge enabled" id="enable_slider_mobile_status">
                                                        ✓ BẬT
                                                    </span>
                                                </div>
                                                <div class="abvp-info-box">
                                                    <strong>💡 Khuyến nghị:</strong> Bật slider trên mobile giúp tiết kiệm data và tăng tốc tải trang
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- HOVER MASTER SWITCH -->
                                <tr>
                                    <td colspan="2">
                                        <div class="abvp-master-switch">
                                            <div class="abvp-setting-row">
                                                <span class="abvp-setting-icon">✨</span>
                                                <div class="abvp-setting-content">
                                                    <label class="abvp-setting-label" for="enable_hover">
                                                        Hiệu Ứng Hover
                                                    </label>
                                                    <div class="abvp-setting-description">
                                                        Master switch - Hiệu ứng khi di chuột/chạm vào banner (fade slider, show video)
                                                    </div>
                                                    <div class="abvp-setting-control">
                                                        <label class="switch">
                                                            <input type="checkbox" 
                                                                id="enable_hover" 
                                                                name="enable_hover" 
                                                                value="1"
                                                                <?php echo ($is_edit ? (!empty($banner->enable_hover) ? 'checked' : '') : 'checked'); ?>>
                                                            <span class="slider-switch"></span>
                                                        </label>
                                                        <span class="abvp-status-badge enabled" id="enable_hover_status">
                                                            ✓ ĐANG BẬT
                                                        </span>
                                                    </div>
                                                    <div class="abvp-info-box">
                                                        <strong>💡 Khi tắt:</strong> Slider sẽ không có hiệu ứng mờ khi hover/tap
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- HOVER DEVICE SETTINGS -->
                                <tr>
                                    <td data-label="🖥️ Desktop">
                                        <div class="abvp-setting-row">
                                            <span class="abvp-setting-icon">✨</span>
                                            <div class="abvp-setting-content">
                                                <label class="abvp-setting-label" for="enable_hover_desktop">
                                                    Hover trên Desktop
                                                </label>
                                                <div class="abvp-setting-description">
                                                    Slider mờ dần khi di chuột vào banner (mouse hover)
                                                </div>
                                                <div class="abvp-setting-control">
                                                    <label class="switch">
                                                        <input type="checkbox" 
                                                            id="enable_hover_desktop" 
                                                            name="enable_hover_desktop" 
                                                            value="1"
                                                            <?php echo ($is_edit ? (!empty($banner->enable_hover_desktop) ? 'checked' : '') : 'checked'); ?>>
                                                        <span class="slider-switch"></span>
                                                    </label>
                                                    <span class="abvp-status-badge enabled" id="enable_hover_desktop_status">
                                                        ✓ BẬT
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="📱 Mobile">
                                        <div class="abvp-setting-row">
                                            <span class="abvp-setting-icon">✨</span>
                                            <div class="abvp-setting-content">
                                                <label class="abvp-setting-label" for="enable_hover_mobile">
                                                    Hover trên Mobile
                                                </label>
                                                <div class="abvp-setting-description">
                                                    Slider mờ dần khi chạm vào banner (touch/tap)
                                                </div>
                                                <div class="abvp-setting-control">
                                                    <label class="switch">
                                                        <input type="checkbox" 
                                                            id="enable_hover_mobile" 
                                                            name="enable_hover_mobile" 
                                                            value="1"
                                                            <?php echo ($is_edit ? (!empty($banner->enable_hover_mobile) ? 'checked' : '') : 'checked'); ?>>
                                                        <span class="slider-switch"></span>
                                                    </label>
                                                    <span class="abvp-status-badge enabled" id="enable_hover_mobile_status">
                                                        ✓ BẬT
                                                    </span>
                                                </div>
                                                <div class="abvp-info-box">
                                                    <strong>💡 Lưu ý:</strong> Trên mobile, hover = tap (chạm vào banner)
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
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
                            
                            <input type="hidden" id="images_json" name="images" value="<?php echo $is_edit ? esc_attr($banner->image_list) : '[]'; ?>">
                        </div>
                        
                        <table class="form-table" style="margin-top: 20px;">
                            <tr>
                                <th><label for="slider_speed">Tốc Độ Slider</label></th>
                                <td>
                                    <input type="number" 
                                           id="slider_speed" 
                                           name="slider_speed" 
                                           class="small-text" 
                                           value="<?php echo $is_edit ? $banner->image_slider_speed : '3000'; ?>" 
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
                                        <option value="fade" <?php echo ($is_edit && $banner->transition_effect == 'fade') ? 'selected' : ''; ?>>Mờ Dần</option>
                                        <option value="zoom" <?php echo ($is_edit && $banner->transition_effect == 'zoom') ? 'selected' : ''; ?>>Phóng To</option>
                                        <option value="blur" <?php echo ($is_edit && $banner->transition_effect == 'blur') ? 'selected' : ''; ?>>Làm Mờ</option>
                                        <option value="slide" <?php echo ($is_edit && $banner->transition_effect == 'slide') ? 'selected' : ''; ?>>Trượt</option>
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
                                                   <?php echo ($is_edit && !empty($banner->display_title)) ? 'checked' : ''; ?>>
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
                                                   <?php echo ($is_edit && !empty($banner->display_subtitle)) ? 'checked' : ''; ?>>
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
                                                   <?php echo ($is_edit && !empty($banner->display_button)) ? 'checked' : ''; ?>>
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
                                           value="<?php echo $is_edit ? esc_attr($banner->banner_title) : ''; ?>" 
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
                                              placeholder="Hơn 20 năm kinh nghiệm trong lĩnh vực công cụ cắt gọt"><?php echo $is_edit ? esc_textarea($banner->banner_subtitle) : ''; ?></textarea>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><label for="button_text">Văn Bản Nút</label></th>
                                <td>
                                    <input type="text" 
                                           id="button_text" 
                                           name="button_text" 
                                           class="regular-text" 
                                           value="<?php echo $is_edit ? esc_attr($banner->cta_button_text) : ''; ?>" 
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
                                           value="<?php echo $is_edit ? esc_url($banner->cta_button_link) : ''; ?>" 
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
                                    <option value="active" <?php echo ($is_edit && $banner->banner_status == 'active') ? 'selected' : ''; ?>>Kích Hoạt</option>
                                    <option value="inactive" <?php echo ($is_edit && $banner->banner_status == 'inactive') ? 'selected' : ''; ?>>Vô Hiệu</option>
                                </select>
                            </div>
                            
                            <div class="submit-actions">
                                <button type="submit" class="button button-primary button-large">
                                    <span class="dashicons dashicons-saved"></span> 
                                    <?php echo $is_edit ? 'Cập Nhật Banner' : 'Tạo Banner'; ?>
                                </button>
                                
                                <a href="<?php echo admin_url('admin.php?page=anmi-video-banner'); ?>" class="button button-secondary button-large">
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
                                           value="<?php echo $is_edit ? esc_attr($banner->banner_height) : '600px'; ?>">
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
                                           value="<?php echo $is_edit ? $banner->video_autoplay_delay : '0'; ?>" 
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
                                        <option value="image" <?php echo ($is_edit && $banner->mobile_display_mode == 'image') ? 'selected' : ''; ?>>Chỉ Hình Ảnh</option>
                                        <option value="video" <?php echo ($is_edit && $banner->mobile_display_mode == 'video') ? 'selected' : ''; ?>>Chỉ Video</option>
                                        <option value="both" <?php echo ($is_edit && $banner->mobile_display_mode == 'both') ? 'selected' : ''; ?>>Cả Hai (Chạm để Phát)</option>
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
                   value='[anmi_video_banner id="<?php echo esc_attr($banner->banner_id); ?>"]' 
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
                        <p class="description" style="margin-bottom: 5px;">
                            <strong>Desktop:</strong> Hover chuột để xem video phát → Click play button
                        </p>
                        <p class="description" style="margin-bottom: 10px; color: #2196F3;">
                            <strong>🎮 Video Controls:</strong> Click vào video để access YouTube/Vimeo controls (play, pause, volume, fullscreen)
                        </p>
                        <p class="description" style="margin-bottom: 10px; color: #FF9800;">
                            <strong>⚠️ Lưu ý:</strong> Nếu "Muted" được bật, bạn cần click vào nút volume của YouTube để bật âm thanh
                        </p>
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
/* Version: 2.7.0 - Fixed videoType bug - Updated: 2025-11-07 19:20 */
jQuery(document).ready(function($) {
    
    var imagesArray = <?php echo json_encode($images_array); ?>;
    
    // DEBUG: Log initial state
    console.log('=== ANMI VIDEO BANNER DEBUG v2.7.0 ===');
    console.log('Initial imagesArray:', imagesArray);
    console.log('Images JSON field value:', $('#images_json').val());
    
    // Video type change - show/hide appropriate fields
    $('#video_type').on('change', function() {
        var type = $(this).val();
        console.log('Video type changed to:', type);
        
        $('.hint-box').removeClass('active');
        $('.hint-' + type).addClass('active');
        
        // Show/hide video URL or embed code fields
        if (type === 'embed') {
            console.log('Showing embed code textarea, hiding URL field');
            $('#video-url-row').hide();
            $('#video-embed-row').show();
            $('#video_url').prop('required', false);
            $('#video_embed_code').prop('required', true);
        } else {
            console.log('Showing URL field, hiding embed code textarea');
            $('#video-url-row').show();
            $('#video-embed-row').hide();
            $('#video_url').prop('required', true);
            $('#video_embed_code').prop('required', false);
        }
        
        console.log('video-url-row display:', $('#video-url-row').css('display'));
        console.log('video-embed-row display:', $('#video-embed-row').css('display'));
    });
    
    // Trigger on page load to set correct initial state
    console.log('Triggering initial video_type change...');
    $('#video_type').trigger('change');
    
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
    
    // ========================================
    // OVERLAY IMAGE UPLOAD
    // ========================================
    
    // Upload overlay image button
    $('#upload_overlay_image_button').on('click', function(e) {
        e.preventDefault();
        
        var overlayImageUploader = wp.media({
            title: 'Chọn Hình Ảnh Lớp Phủ (Overlay)',
            button: {
                text: 'Sử Dụng Hình Này'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        overlayImageUploader.on('select', function() {
            var attachment = overlayImageUploader.state().get('selection').first().toJSON();
            
            // Set hidden input value
            $('#overlay_image').val(attachment.url);
            
            // Show preview image
            $('#overlay_image_preview')
                .attr('src', attachment.url)
                .show();
            
            // Show remove button
            $('#remove_overlay_image_button').show();
            
            console.log('Overlay image selected:', attachment.url);
        });
        
        overlayImageUploader.open();
    });
    
    // Remove overlay image button
    $('#remove_overlay_image_button').on('click', function(e) {
        e.preventDefault();
        
        // Clear hidden input
        $('#overlay_image').val('');
        
        // Hide preview image
        $('#overlay_image_preview')
            .attr('src', '')
            .hide();
        
        // Hide remove button
        $(this).hide();
        
        console.log('Overlay image removed');
    });
    
    // ========================================
    // END OVERLAY IMAGE UPLOAD
    // ========================================
    
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
        
        // Get video URL based on video type
        var videoType = $('#video_type').val();
        var videoUrl = '';
        
        if (videoType === 'embed') {
            var embedCode = $('#video_embed_code').val().trim();
            if (!embedCode) {
                alert('Vui lòng nhập mã nhúng iframe');
                return;
            }
            
            // Lưu toàn bộ embed code thay vì chỉ extract URL
            // Điều này giúp giữ nguyên code khi Edit lại
            videoUrl = embedCode;
        } else {
            videoUrl = $('#video_url').val();
            if (!videoUrl) {
                alert('Vui lòng nhập URL video');
                return;
            }
        }
        
        if (imagesArray.length === 0) {
            alert('Vui lòng tải lên ít nhất một hình ảnh');
            return;
        }
        
        $submitBtn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Đang lưu...');
        
        $.ajax({
            url: abvpBannerAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'abvp_save_banner',
                nonce: abvpBannerAdmin.nonce,
                banner_id: $('#banner_id').val(),
                name: $('#banner_name').val(),
                video_url: videoUrl,
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
                status: $('#status').val(),
                // Video playback settings
                video_autoplay: $('#video_autoplay').is(':checked') ? 1 : 0,
                video_muted: $('#video_muted').is(':checked') ? 1 : 0,
                video_loop: $('#video_loop').is(':checked') ? 1 : 0,
                video_controls: $('#video_controls').is(':checked') ? 1 : 0,
                enable_slider: $('#enable_slider').is(':checked') ? 1 : 0,
                enable_slider_desktop: $('#enable_slider_desktop').is(':checked') ? 1 : 0,
                enable_slider_mobile: $('#enable_slider_mobile').is(':checked') ? 1 : 0,
                enable_overlay: $('#enable_overlay').is(':checked') ? 1 : 0,
                enable_overlay_desktop: $('#enable_overlay_desktop').is(':checked') ? 1 : 0,
                enable_overlay_mobile: $('#enable_overlay_mobile').is(':checked') ? 1 : 0,
                enable_hover: $('#enable_hover').is(':checked') ? 1 : 0,
                enable_hover_desktop: $('#enable_hover_desktop').is(':checked') ? 1 : 0,
                enable_hover_mobile: $('#enable_hover_mobile').is(':checked') ? 1 : 0,
                video_modestbranding: $('#video_modestbranding').is(':checked') ? 1 : 0,
                video_rel: $('#video_rel').is(':checked') ? 1 : 0,
                show_title: $('#show_title').is(':checked') ? 1 : 0,
                show_subtitle: $('#show_subtitle').is(':checked') ? 1 : 0,
                show_button: $('#show_button').is(':checked') ? 1 : 0
            },
            success: function(response) {
                if (response.success) {
                    // Redirect to list page with success message
                    window.location.href = '<?php echo admin_url("admin.php?page=anmi-video-banner&message=saved"); ?>';
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
        update: function() {
            var newOrder = [];
            $('#images_preview_container .image-preview-item').each(function(index) {
                var imageUrl = $(this).find('img').attr('src');
                if (imageUrl) {
                    newOrder.push(imageUrl);
                }
                $(this).attr('data-index', index);
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
        // Get video URL based on type
        var selectedVideoType = $('#video_type').val();
        var videoUrl = '';
        
        if (selectedVideoType === 'embed') {
            var embedCode = $('#video_embed_code').val().trim();
            if (embedCode) {
                // Extract src URL from iframe
                var srcMatch = embedCode.match(/src=["']([^"']+)["']/i);
                if (srcMatch && srcMatch[1]) {
                    videoUrl = srcMatch[1];
                }
            }
        } else {
            videoUrl = $('#video_url').val();
        }
        
        var title = $('#title').val();
        var subtitle = $('#subtitle').val();
        var buttonText = $('#button_text').val();
        var buttonLink = $('#button_link').val();
        var showTitle = $('#show_title').is(':checked') ? '1' : '0';
        var showSubtitle = $('#show_subtitle').is(':checked') ? '1' : '0';
        var showButton = $('#show_button').is(':checked') ? '1' : '0';
        var transition = $('#transition').val();
        var autoplayDelay = $('#autoplay_delay').val();
        var sliderSpeed = $('#slider_speed').val() || '3000';
        var sliderEffect = 'fade';
        
        // Get video playback settings
        var videoAutoplay = $('#video_autoplay').is(':checked') ? 1 : 0;
        var videoMuted = $('#video_muted').is(':checked') ? 1 : 0;
        var videoLoop = $('#video_loop').is(':checked') ? 1 : 0;
        var videoControls = $('#video_controls').is(':checked') ? 1 : 0;
        var videoModestbranding = $('#video_modestbranding').is(':checked') ? 1 : 0;
        var videoRel = $('#video_rel').is(':checked') ? 1 : 0;
        
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
        var derivedVideoType = 'direct';
        var embedUrl = videoUrl;
        var videoId = null;
        
        // YouTube detection
        var youtubeMatch = videoUrl.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i);
        if (youtubeMatch) {
            derivedVideoType = 'youtube';
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
        }
        
        // Vimeo detection
        var vimeoMatch = videoUrl.match(/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)(?:$|\/|\?)/i);
        if (vimeoMatch) {
            derivedVideoType = 'vimeo';
            videoId = vimeoMatch[1];
            // Build Vimeo URL with user settings
            embedUrl = 'https://player.vimeo.com/video/' + videoId + 
                '?autoplay=' + videoAutoplay +
                '&muted=' + videoMuted +
                '&loop=' + videoLoop +
                '&background=' + (videoControls ? 0 : 1) +
                '&controls=' + videoControls;
        }
        
        // Generate unique ID
        var uniqueId = 'anmi-preview-' + Date.now();
        
        // Build HTML with hover + click to play behavior
        var html = '<div class="anmi-video-banner-container ' + uniqueId + ' transition-' + transition + '" ' +
                   'style="height: 300px; position: relative; cursor: pointer;" ' +
                   'data-autoplay-delay="' + autoplayDelay + '" ' +
                   'data-mobile-behavior="both" ' +
                   'data-slider-speed="' + sliderSpeed + '" ' +
                   'data-slider-effect="' + sliderEffect + '" ' +
                   'data-video-type="' + derivedVideoType + '">';
        
        // Image Slider (visible by default)
        imagesArray.forEach(function(imageUrl, index) {
            html += '<div class="anmi-banner-image ' + (index === 0 ? 'active' : '') + '" ' +
                    'style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; ' +
                    'background-image: url(\'' + imageUrl + '\'); background-size: cover; background-position: center; ' +
                    'opacity: ' + (index === 0 ? '1' : '0') + '; transition: opacity 0.8s ease; z-index: 2;">' +
                    '</div>';
        });
        
        // Video/Iframe (hidden by default - use PRODUCTION classes)
        if (derivedVideoType === 'youtube' || derivedVideoType === 'vimeo') {
            html += '<iframe class="anmi-banner-video anmi-banner-iframe" ' +
                    'src="' + embedUrl + '" ' +
                    'frameborder="0" ' +
                    'allow="autoplay; fullscreen; picture-in-picture" ' +
                    'allowfullscreen></iframe>';
        } else {
            html += '<video class="anmi-banner-video" ' +
                    'loop muted playsinline preload="metadata" ' +
                    'poster="' + (imagesArray[0] || '') + '">' +
                    '<source src="' + embedUrl + '" type="video/mp4">' +
                    '</video>';
        }
        
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
        
        // Slider dots (if multiple images)
        if (imagesArray.length > 1) {
            html += '<div class="anmi-banner-dots" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 10;">';
            imagesArray.forEach(function(imageUrl, index) {
                html += '<span class="anmi-banner-dot ' + (index === 0 ? 'active' : '') + '" data-slide="' + index + '" ' +
                        'style="width: 12px; height: 12px; border-radius: 50%; background: ' + (index === 0 ? '#fff' : 'rgba(255,255,255,0.5)') + '; ' +
                        'cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">' +
                        '</span>';
            });
            html += '</div>';
        }
        
        // Play button overlay - Small button at bottom right
        html += '<div class="anmi-play-overlay" style="position: absolute; bottom: 80px; right: 20px; z-index: 15; pointer-events: auto; cursor: pointer;">' +
                '<div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.95); display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.3); transition: transform 0.3s;">' +
                '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ff6600" stroke-width="2">' +
                '<polygon points="5 3 19 12 5 21 5 3"></polygon>' +
                '</svg>' +
                '</div>' +
                '</div>';
        
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
        
        // Initialize hover + click to play behavior
        setTimeout(function() {
            var $container = $('.' + uniqueId);
            
            if ($container.length) {
                var currentSlide = 0;
                var sliderInterval = null;
                var isHovered = false;
                var isVideoPlaying = false;
                
                var $video = $container.find('.anmi-banner-video');
                var $images = $container.find('.anmi-banner-image');
                var $playOverlay = $container.find('.anmi-play-overlay');
                var $dots = $container.find('.anmi-banner-dot');
                
                // Start auto-play slider
                if (imagesArray.length > 1) {
                    sliderInterval = setInterval(function() {
                        if (!isHovered && !isVideoPlaying) {
                            // Fade out current
                            $images.eq(currentSlide).css('opacity', '0');
                            $dots.eq(currentSlide).css('background', 'rgba(255,255,255,0.5)').removeClass('active');
                            
                            // Next slide
                            currentSlide = (currentSlide + 1) % imagesArray.length;
                            
                            // Fade in next
                            $images.eq(currentSlide).css('opacity', '1');
                            $dots.eq(currentSlide).css('background', '#fff').addClass('active');
                        }
                    }, parseInt(sliderSpeed));
                }
                
                // HOVER: Stop slider, show video
                $container.on('mouseenter', function() {
                    isHovered = true;
                    
                    if (sliderInterval) {
                        clearInterval(sliderInterval);
                    }
                    
                    $images.css('opacity', '0');
                    $video.css('opacity', '1');
                    $playOverlay.css('opacity', '1');
                });
                
                // MOUSE LEAVE: Stop video, resume slider
                $container.on('mouseleave', function() {
                    isHovered = false;
                    
                    // Stop video if playing
                    if (isVideoPlaying) {
                        if ($video.is('video')) {
                            $video[0].pause();
                            $video[0].currentTime = 0;
                        }
                        isVideoPlaying = false;
                    }
                    
                    // Hide video
                    $video.css('opacity', '0');
                    $playOverlay.css('opacity', '1').show();
                    
                    // Show current slide
                    $images.css('opacity', '0');
                    $images.eq(currentSlide).css('opacity', '1');
                    
                    // Resume slider
                    if (imagesArray.length > 1) {
                        if (sliderInterval) {
                            clearInterval(sliderInterval);
                        }
                        
                        sliderInterval = setInterval(function() {
                            if (!isHovered && !isVideoPlaying) {
                                $images.eq(currentSlide).css('opacity', '0');
                                $dots.eq(currentSlide).css('background', 'rgba(255,255,255,0.5)').removeClass('active');
                                
                                currentSlide = (currentSlide + 1) % imagesArray.length;
                                
                                $images.eq(currentSlide).css('opacity', '1');
                                $dots.eq(currentSlide).css('background', '#fff').addClass('active');
                            }
                        }, parseInt(sliderSpeed));
                    }
                });
                
                // CLICK: Play video
                $container.on('click', function() {
                    if (!isVideoPlaying) {
                        isVideoPlaying = true;
                        $playOverlay.fadeOut(300);
                        
                        if ($video.length) {
                            if ($video.is('video')) {
                                $video[0].play();
                            }
                        }
                    }
                });
                
                // Dot click handlers
                $dots.on('click', function(e) {
                    e.stopPropagation();
                    
                    if (sliderInterval) {
                        clearInterval(sliderInterval);
                    }
                    
                    var slideIndex = $(this).data('slide');
                    
                    $images.css('opacity', '0');
                    $dots.css('background', 'rgba(255,255,255,0.5)').removeClass('active');
                    
                    $images.eq(slideIndex).css('opacity', '1');
                    $(this).css('background', '#fff').addClass('active');
                    
                    currentSlide = slideIndex;
                });
            }
        }, 100);
    }
    
    // Update status labels for all video settings checkboxes
    function updateCheckboxLabels() {
        // Helper function to update badge
        function updateBadge(checkboxId, statusId) {
            var $checkbox = $('#' + checkboxId);
            var $status = $('#' + statusId);
            
            if ($checkbox.is(':checked')) {
                $status.removeClass('disabled').addClass('enabled')
                    .html('✓ ' + (statusId.includes('_desktop') || statusId.includes('_mobile') ? 'BẬT' : 'ĐANG BẬT'));
            } else {
                $status.removeClass('enabled').addClass('disabled')
                    .html('✗ ' + (statusId.includes('_desktop') || statusId.includes('_mobile') ? 'TẮT' : 'ĐANG TẮT'));
            }
        }
        
        // Video Autoplay
        if ($('#video_autoplay').is(':checked')) {
            $('#video_autoplay_status').html('✓ ĐANG BẬT').css('color', '#46b450');
        } else {
            $('#video_autoplay_status').html('✗ ĐANG TẮT').css('color', '#d63638');
        }
        
        // Video Muted
        if ($('#video_muted').is(':checked')) {
            $('#video_muted_status').html('✓ ĐANG BẬT').css('color', '#d63638');
        } else {
            $('#video_muted_status').html('✗ ĐANG TẮT').css('color', '#46b450');
        }
        
        // Video Loop
        if ($('#video_loop').is(':checked')) {
            $('#video_loop_status').html('✓ ĐANG BẬT').css('color', '#46b450');
        } else {
            $('#video_loop_status').html('✗ ĐANG TẮT').css('color', '#d63638');
        }
        
        // Video Controls
        if ($('#video_controls').is(':checked')) {
            $('#video_controls_status').html('✓ ĐANG BẬT').css('color', '#46b450');
        } else {
            $('#video_controls_status').html('✗ ĐANG TẮT').css('color', '#d63638');
        }
        
        // Enable Slider
        if ($('#enable_slider').is(':checked')) {
            $('#enable_slider_status').html('✓ ĐANG BẬT').css('color', '#46b450');
        } else {
            $('#enable_slider_status').html('✗ ĐANG TẮT').css('color', '#d63638');
        }
        
        // Enable Slider Desktop
        if ($('#enable_slider_desktop').is(':checked')) {
            $('#enable_slider_desktop_status').html('✓ ĐANG BẬT').css('color', '#46b450');
        } else {
            $('#enable_slider_desktop_status').html('✗ ĐANG TẮT').css('color', '#d63638');
        }
        
        // Enable Slider Mobile
        if ($('#enable_slider_mobile').is(':checked')) {
            $('#enable_slider_mobile_status').html('✓ ĐANG BẬT').css('color', '#46b450');
        } else {
            $('#enable_slider_mobile_status').html('✗ ĐANG TẮT').css('color', '#d63638');
        }
        
        // Video Modest Branding
        if ($('#video_modestbranding').is(':checked')) {
            $('#video_modestbranding_status').html('✓ ĐANG BẬT').css('color', '#46b450');
        } else {
            $('#video_modestbranding_status').html('✗ ĐANG TẮT').css('color', '#d63638');
        }
        
        // Video Rel
        if ($('#video_rel').is(':checked')) {
            $('#video_rel_status').html('✓ ĐANG BẬT').css('color', '#46b450');
        } else {
            $('#video_rel_status').html('✗ ĐANG TẮT').css('color', '#d63638');
        }
        
        // Update all device settings badges using helper function
        updateBadge('enable_overlay', 'enable_overlay_status');
        updateBadge('enable_overlay_desktop', 'enable_overlay_desktop_status');
        updateBadge('enable_overlay_mobile', 'enable_overlay_mobile_status');
        updateBadge('enable_hover', 'enable_hover_status');
        updateBadge('enable_hover_desktop', 'enable_hover_desktop_status');
        updateBadge('enable_hover_mobile', 'enable_hover_mobile_status');
    }
    
    // Update labels on change for all checkboxes
    $('#video_autoplay, #video_muted, #video_loop, #video_controls, #enable_slider, #enable_slider_desktop, #enable_slider_mobile, #enable_overlay, #enable_overlay_desktop, #enable_overlay_mobile, #enable_hover, #enable_hover_desktop, #enable_hover_mobile, #video_modestbranding, #video_rel').on('change', updateCheckboxLabels);
    
    // Initial update
    updateCheckboxLabels();
    
    // Trigger preview update on field changes
    $('#video_url, #video_embed_code, #title, #subtitle, #button_text, #button_link, #transition, #autoplay_delay, #slider_speed').on('input change', function() {
        updateLivePreview();
    });
    
    $('#show_title, #show_subtitle, #show_button, #video_autoplay, #video_muted, #video_loop, #video_controls, #enable_slider, #enable_slider_desktop, #enable_slider_mobile, #video_modestbranding, #video_rel').on('change', function() {
        updateLivePreview();
    });
    
    // Initial preview render
    updateLivePreview();
});
</script>
