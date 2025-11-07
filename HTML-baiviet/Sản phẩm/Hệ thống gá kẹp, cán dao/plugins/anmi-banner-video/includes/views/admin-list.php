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

    <?php if (isset($_GET['message']) && $_GET['message'] === 'saved'): ?>
        <div class="notice notice-success is-dismissible">
            <p>Banner saved successfully!</p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'deleted'): ?>
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
            <div class="stat-number"><?php echo count(array_filter($banners, function ($b) { return $b->banner_status === 'active'; })); ?></div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?php echo count(array_filter($banners, function ($b) { return $b->banner_status === 'inactive'; })); ?></div>
            <div class="stat-label">Inactive</div>
        </div>
    </div>

    <?php if (empty($banners)): ?>

        <div class="anmi-empty-state">
            <div class="empty-icon">[Preview]</div>
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
                    <li><strong>Or Elementor:</strong> Drag &amp; drop widget with banner selector</li>
                </ol>
            </div>
        </div>

    <?php else: ?>

        <div class="anmi-panel anmi-column-settings" aria-label="Column display settings">
            <div class="anmi-panel__title">Tùy chỉnh cột hiển thị</div>
            <p class="anmi-column-settings__description">Chọn các cột bạn muốn hiển thị trong danh sách banner.</p>
            <div class="anmi-column-toggle-list" role="group" aria-label="Column toggles">
                <label class="anmi-column-toggle">
                    <input type="checkbox" data-column="column-thumbnail" checked>
                    <span>Xem trước</span>
                </label>
                <label class="anmi-column-toggle">
                    <input type="checkbox" data-column="column-name" checked>
                    <span>Tên banner</span>
                </label>
                <label class="anmi-column-toggle">
                    <input type="checkbox" data-column="column-video" checked>
                    <span>Video</span>
                </label>
                <label class="anmi-column-toggle">
                    <input type="checkbox" data-column="column-images" checked>
                    <span>Ảnh nền</span>
                </label>
                <label class="anmi-column-toggle">
                    <input type="checkbox" data-column="column-shortcode" checked>
                    <span>Shortcode</span>
                </label>
                <label class="anmi-column-toggle">
                    <input type="checkbox" data-column="column-status" checked>
                    <span>Trạng thái</span>
                </label>
                <label class="anmi-column-toggle">
                    <input type="checkbox" data-column="column-date" checked>
                    <span>Ngày tạo</span>
                </label>
                <label class="anmi-column-toggle">
                    <input type="checkbox" data-column="column-actions" checked>
                    <span>Thao tác</span>
                </label>
            </div>
            <div class="anmi-column-settings__status" role="status" aria-live="polite">Cài đặt hiển thị cột sẽ được lưu trong trình duyệt.</div>
            <button type="button" class="button anmi-column-settings__reset">Khôi phục mặc định</button>
        </div>

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

                    $video_display_source = $banner->video_url_value;
                    if ($banner->video_input_type === 'embed' && preg_match('/src=["\']([^"\']+)["\']/', $banner->video_url_value, $matches)) {
                        $video_display_source = $matches[1];
                    }

                    $video_type = 'Direct URL';
                    if ($video_display_source && (strpos($video_display_source, 'youtube.com') !== false || strpos($video_display_source, 'youtu.be') !== false)) {
                        $video_type = 'YouTube';
                    } elseif ($video_display_source && strpos($video_display_source, 'vimeo.com') !== false) {
                        $video_type = 'Vimeo';
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
                        <span class="video-type-badge"><?php echo esc_html($video_type); ?></span>
                        <br><small class="video-url" title="<?php echo esc_attr($video_display_source); ?>">
                            <?php echo $video_display_source ? esc_html($video_label) : '&mdash;'; ?>
                        </small>
                    </td>

                    <td class="column-images">
                        <span class="image-count-badge">
                            <span class="dashicons dashicons-images-alt2"></span>
                            <?php echo $image_count; ?> images
                        </span>
                        <br><small>Slider: <?php echo $banner->image_slider_speed ? intval($banner->image_slider_speed / 1000) : 0; ?>s/slide</small>
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
                        <?php if ($banner->banner_status === 'active'): ?>
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

    <div class="anmi-info-box">
        <h3>How to Use Your Banners</h3>
        <div class="info-grid">
            <div class="info-item">
                <h4>In Pages/Posts (Shortcode)</h4>
                <p>Copy the shortcode from the list above and paste it into any page, post, or widget.</p>
                <code>[anmi_video_banner id="1"]</code>
            </div>

            <div class="info-item">
                <h4>In Elementor</h4>
                <p>1. Open page in Elementor<br>
                   2. Search "An Mi Video Banner" widget<br>
                   3. Select banner from dropdown<br>
                   4. Customize and publish</p>
            </div>

            <div class="info-item">
                <h4>Video Sources</h4>
                <p>Supported video types:<br>
                   • YouTube (embed URL)<br>
                   • Vimeo (embed URL)<br>
                   • Direct MP4 link<br>
                   • Any video URL</p>
            </div>

            <div class="info-item">
                <h4>Mobile Responsive</h4>
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
jQuery(function($) {
    var activePreviewIntervals = [];
    var previewSliderInterval = null;

    $('.copy-shortcode').on('click', function(e) {
        e.preventDefault();
        var shortcode = $(this).data('shortcode');
        var $input = $(this).prev('.shortcode-input');

        $input.trigger('focus');
        $input[0].setSelectionRange(0, $input.val().length);
        document.execCommand('copy');

        $(this).html('<span class="dashicons dashicons-yes"></span> Copied!');
        setTimeout(function() {
            $(this).html('<span class="dashicons dashicons-clipboard"></span> Copy');
        }.bind(this), 2000);
    });

    $('.delete-banner').on('click', function(e) {
        e.preventDefault();

        var bannerId = $(this).data('banner-id');
        var bannerName = $(this).data('banner-name');
        var $row = $(this).closest('tr');

        if (!confirm('Bạn chắc chắn muốn xóa "' + bannerName + '"?\n\nHành động này không thể hoàn tác.')) {
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

                        if ($('.anmi-banner-table tbody tr').length === 0) {
                            location.reload();
                        }
                    });

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

    $('.preview-banner').on('click', function(e) {
        e.preventDefault();
        var bannerId = $(this).data('banner-id');
        showPreviewModal(bannerId);
    });

    function showPreviewModal(bannerId) {
        if ($('#anmi-preview-modal').length === 0) {
            var modalHtml = '' +
                '<div id="anmi-preview-modal" class="anmi-modal">' +
                    '<div class="anmi-modal-overlay"></div>' +
                    '<div class="anmi-modal-content">' +
                        '<div class="anmi-modal-header">' +
                            '<h2>Xem trước banner</h2>' +
                            '<button class="anmi-modal-close" type="button">&times;</button>' +
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

            $('.anmi-modal-close, .anmi-modal-overlay').on('click', function() {
                closePreviewModal();
            });

            $(document).on('keyup.abvpPreview', function(event) {
                if (event.key === 'Escape') {
                    closePreviewModal();
                }
            });
        }

        $('#anmi-preview-modal').fadeIn(300);
        $('body').addClass('anmi-modal-open');

        $.ajax({
            url: abvpBannerAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'abvp_get_banner_preview',
                banner_id: bannerId,
                nonce: abvpBannerAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    renderPreview(response.data);
                } else {
                    showPreviewError(response.data || 'Không thể tải banner.');
                }
            },
            error: function(xhr) {
                showPreviewError('Lỗi ' + xhr.status + ': ' + xhr.statusText + '. Server có thể đang bận, vui lòng thử lại.');
            }
        });
    }

    function renderPreview(banner) {
        $('.anmi-modal-loading').hide();

        if (!banner || !banner.render_html) {
            showPreviewError('Không tìm thấy dữ liệu preview.');
            return;
        }

        clearPreviewIntervals();

        var images = parsePreviewImages(banner.image_list);
        var metaHtml = buildMetaRows(banner, images);

        var previewHtml = '' +
            '<div class="abvp-preview-container">' +
                '<div class="abvp-preview-toolbar" role="group" aria-label="Chế độ thiết bị">' +
                    '<button type="button" class="button button-secondary abvp-preview-mode is-active" data-mode="desktop">Desktop</button>' +
                    '<button type="button" class="button button-secondary abvp-preview-mode" data-mode="mobile">Mobile</button>' +
                    '<span class="abvp-preview-hint">Mobile preview mô phỏng autoplay mute trên thiết bị thật.</span>' +
                '</div>' +
                '<div class="abvp-preview-stage" data-preview-mode="desktop">' +
                    '<div class="abvp-preview-device abvp-preview-device--desktop">' +
                        '<div class="abvp-preview-frame" data-device="desktop">' +
                            banner.render_html +
                        '</div>' +
                    '</div>' +
                    '<div class="abvp-preview-device abvp-preview-device--mobile">' +
                        '<div class="abvp-preview-frame" data-device="mobile">' +
                            banner.render_html +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="abvp-preview-details">' +
                    '<h3>' + escapeHtml(banner.banner_name || 'Cấu hình banner') + '</h3>' +
                    '<ul class="abvp-preview-meta-list">' + metaHtml + '</ul>' +
                '</div>' +
            '</div>';

        $('#anmi-preview-container').html(previewHtml).fadeIn(200);
        initializePreviewInteractions($('#anmi-preview-container'));
    }

    function clearPreviewIntervals() {
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
    }

    function initializePreviewInteractions($root) {
        var $stage = $root.find('.abvp-preview-stage');
        var $desktopFrame = $stage.find('.abvp-preview-frame[data-device="desktop"]');
        var $mobileFrame = $stage.find('.abvp-preview-frame[data-device="mobile"]');

        bootPreviewFrame($desktopFrame, 'desktop');

        $root.find('.abvp-preview-mode')
            .off('click.previewToggle')
            .on('click.previewToggle', function(event) {
                event.preventDefault();

                var $button = $(this);
                var mode = $button.data('mode');

                if (!mode || $button.hasClass('is-active')) {
                    return;
                }

                $root.find('.abvp-preview-mode').removeClass('is-active');
                $button.addClass('is-active');

                $stage.attr('data-preview-mode', mode);

                if (mode === 'mobile' && !$mobileFrame.data('abvp-booted')) {
                    bootPreviewFrame($mobileFrame, 'mobile');
                }
            });
    }

    function bootPreviewFrame($frame, device) {
        if (!$frame.length) {
            return;
        }

        var $container = $frame.find('.anmi-video-banner-container').first();

        if (!$container.length) {
            return;
        }

        $container.attr('data-preview-device', device);
        $container.removeData('anmi-initialized');
        $container.data('anmi-initialized', false);

        if (typeof AnMiVideoBanner === 'function') {
            new AnMiVideoBanner($container[0]);
        }

        $frame.data('abvp-booted', true);
    }

    function parsePreviewImages(imageJson) {
        if (!imageJson) {
            return [];
        }

        try {
            var parsed = JSON.parse(imageJson);

            if (!Array.isArray(parsed)) {
                return [];
            }

            return parsed
                .filter(function(url) {
                    return typeof url === 'string' && url.trim() !== '';
                })
                .map(function(url) {
                    return url.trim();
                });
        } catch (error) {
            console.warn('Không thể parse danh sách hình ảnh preview:', error);
            return [];
        }
    }

    function describeVideoSource(value, inputType) {
        var source = (value || '').toString();

        switch ((inputType || '').toString()) {
            case 'youtube':
                return 'YouTube';
            case 'vimeo':
                return 'Vimeo';
            case 'embed':
                if (/youtube\.com|youtu\.be/i.test(source)) {
                    return 'YouTube iframe';
                }
                if (/vimeo\.com/i.test(source)) {
                    return 'Vimeo iframe';
                }
                return 'Iframe tùy chỉnh';
            case 'url':
            default:
                if (/youtube\.com|youtu\.be/i.test(source)) {
                    return 'YouTube URL';
                }
                if (/vimeo\.com/i.test(source)) {
                    return 'Vimeo URL';
                }
                if (source) {
                    return 'Direct / MP4';
                }
                return 'Chưa thiết lập';
        }
    }

    function describeSlider(images, rawSpeed) {
        if (!images.length) {
            return 'Chưa có hình ảnh';
        }

        if (images.length === 1) {
            return '1 hình ảnh';
        }

        var speed = parseInt(rawSpeed, 10);
        if (isNaN(speed) || speed <= 0) {
            speed = 3000;
        }

        var seconds = (speed / 1000).toFixed(1).replace(/\.0$/, '');
        return images.length + ' hình ảnh · ' + seconds + 's';
    }

    function describeMobileBehavior(value) {
        switch ((value || '').toString()) {
            case 'video':
                return 'Chỉ video (slider ẩn)';
            case 'both':
                return 'Slider + video (chạm để phát)';
            case 'image':
            default:
                return 'Chỉ slider hình ảnh';
        }
    }

    function formatToggle(value) {
        return parseInt(value, 10) === 1 ? 'Bật' : 'Tắt';
    }

    function formatAutoplayDelay(rawDelay) {
        var parsed = parseInt(rawDelay, 10);
        if (isNaN(parsed) || parsed <= 0) {
            return '';
        }
        return ' · trễ ' + parsed + 's';
    }

    function escapeHtml(value) {
        if (value === undefined || value === null) {
            return '';
        }

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function buildMetaRows(banner, images) {
        var rows = [
            { label: 'Nguồn video', value: describeVideoSource(banner.video_url_value, banner.video_input_type) },
            { label: 'Autoplay', value: formatToggle(banner.video_autoplay) + formatAutoplayDelay(banner.video_autoplay_delay) },
            { label: 'Muted', value: formatToggle(banner.video_muted) },
            { label: 'Loop', value: formatToggle(banner.video_loop) },
            { label: 'Controls', value: formatToggle(banner.video_controls) },
            { label: 'Slider', value: describeSlider(images, banner.image_slider_speed) },
            { label: 'Mobile', value: describeMobileBehavior(banner.mobile_display_mode) }
        ];

        if ((banner.mobile_display_mode || '') === 'video') {
            rows.push({
                label: 'Ghi chú mobile',
                value: 'Video hiển thị toàn màn hình, slider bị ẩn và nút phát luôn hiển thị nếu autoplay bị chặn.'
            });
        }

        return rows.map(function(row) {
            return '<li><span class="abvp-preview-meta-label">' + escapeHtml(row.label) + '</span>' +
                '<span class="abvp-preview-meta-value">' + escapeHtml(row.value) + '</span></li>';
        }).join('');
    }

    function showPreviewError(message) {
        $('.anmi-modal-loading').hide();
        $('#anmi-preview-container').html(
            '<div class="anmi-preview-error">' +
            '<span class="dashicons dashicons-warning"></span>' +
            '<p>' + escapeHtml(message || 'Không thể tải preview') + '</p>' +
            '</div>'
        ).fadeIn(300);
    }

    function closePreviewModal() {
        $('#anmi-preview-modal').fadeOut(300);
        $('body').removeClass('anmi-modal-open');

        clearPreviewIntervals();

        setTimeout(function() {
            $('#anmi-preview-container').html('').hide();
            $('.anmi-modal-loading').show();
        }, 300);
    }
});
</script>
