# 🎨 Elementor Widget Synchronization - v1.6.10

## Tổng quan

Version 1.6.10 đồng bộ **Elementor widget** với **production shortcode** và **admin previews**, đảm bảo **behavior nhất quán** trên toàn bộ plugin.

---

## 🔴 Vấn đề đã khắc phục

### Trước v1.6.10:

**Elementor widget sử dụng HTML structure CŨ:**

```html
<!-- OLD HTML (INCORRECT) -->
<div class="anmi-video-banner-container">
    <video class="anmi-banner-video">...</video>
    
    <div class="anmi-banner-slider">  ❌ Nested slider wrapper
        <div class="anmi-slider-slide active">  ❌ Old class
            <!-- Slide content -->
        </div>
        
        <div class="anmi-slider-dots">  ❌ Old structure
            <span class="dot active"></span>  ❌ Old class
        </div>
    </div>
</div>
```

**Thiếu các element quan trọng:**
- ❌ Không có `.anmi-play-overlay` (nút play)
- ❌ Không có JavaScript initialization
- ❌ Không có hover + click to play logic
- ❌ Behavior khác với production

---

## ✅ Giải pháp v1.6.10

### 1. Cập nhật HTML Structure

**NEW HTML (CORRECT) - giống production:**

```html
<div class="anmi-video-banner-container">
    <!-- Individual image slides (NO wrapper) -->
    <div class="anmi-banner-image active" 
         style="position: absolute; opacity: 1; ...">
        <!-- First slide -->
    </div>
    <div class="anmi-banner-image" 
         style="position: absolute; opacity: 0; ...">
        <!-- Second slide -->
    </div>
    
    <!-- Video (YouTube/Vimeo iframe OR direct video) -->
    <iframe class="anmi-banner-video anmi-banner-iframe" 
            src="https://youtube.com/embed/..."
            style="opacity: 0; ..."></iframe>
    <!-- OR -->
    <video class="anmi-banner-video" 
           style="opacity: 0; ...">
        <source src="video.mp4">
    </video>
    
    <!-- Play overlay button (NEW!) -->
    <div class="anmi-play-overlay" 
         style="opacity: 0; pointer-events: none;">
        <svg><!-- Play icon --></svg>
    </div>
    
    <!-- Slider dots (updated classes) -->
    <div class="anmi-banner-dots">
        <span class="anmi-banner-dot active"></span>
        <span class="anmi-banner-dot"></span>
    </div>
    
    <!-- Content overlay -->
    <div class="anmi-banner-content">...</div>
</div>
```

### 2. Thêm Video Type Detection

```php
// Auto-detect YouTube/Vimeo and convert to embed URL
$video_type = 'direct';
$video_embed_url = $video_url;

if (strpos($video_url, 'youtube.com') !== false) {
    $video_type = 'youtube';
    // Convert to embed URL with autoplay params
    $video_embed_url = 'https://youtube.com/embed/...?autoplay=1&mute=1&loop=1';
}
elseif (strpos($video_url, 'vimeo.com') !== false) {
    $video_type = 'vimeo';
    // Convert to player URL with background mode
    $video_embed_url = 'https://player.vimeo.com/video/...?autoplay=1&muted=1&loop=1&background=1';
}
```

### 3. Thêm Script Dependencies

```php
class AnMi_Video_Banner_Elementor_Widget extends \Elementor\Widget_Base {
    
    // Ensure scripts are loaded
    public function get_script_depends() {
        return ['anmi-video-banner-script'];
    }
    
    // Ensure styles are loaded
    public function get_style_depends() {
        return ['anmi-video-banner-style'];
    }
}
```

### 4. Thêm JavaScript Initialization

```javascript
// Initialize after render
jQuery(document).ready(function($) {
    if (typeof AnMiVideoBanner !== 'undefined') {
        var container = $('.anmi-vb-XXXXX'); // Widget unique ID
        if (container.length && !container.data('anmi-initialized')) {
            new AnMiVideoBanner(container[0]);
            container.data('anmi-initialized', true);
        }
    }
});

// Re-initialize on Elementor preview refresh
elementorFrontend.hooks.addAction('frontend/element_ready/anmi_video_banner.default', function($scope) {
    var container = $scope.find('.anmi-video-banner-container');
    if (container.length && !container.data('anmi-initialized')) {
        new AnMiVideoBanner(container[0]);
        container.data('anmi-initialized', true);
    }
});
```

---

## 🎯 Kết quả

### Behavior nhất quán trên 3 contexts:

| Context | HTML Structure | JS Behavior | Status |
|---------|---------------|-------------|--------|
| **Production Shortcode** | ✅ New classes | ✅ Hover + Click | ✅ Working |
| **Admin Modal Preview** | ✅ New classes | ✅ Hover + Click | ✅ Working |
| **Admin Edit Preview** | ✅ New classes | ✅ Hover + Click | ✅ Working |
| **Elementor Widget** | ✅ New classes (v1.6.10) | ✅ Hover + Click (v1.6.10) | ✅ Fixed! |

### 3-Step Interaction Flow (giống nhau tất cả contexts):

```
① Initial State: Slider auto-plays (images rotating every 3s)

② Hover Event: 
   - Slider stops immediately
   - Video shows (opacity: 1) but NOT playing
   - Play button appears (opacity: 1)

③ Click Event:
   - Video starts playing
   - Play button fades out (opacity: 0)

④ Mouse Leave Event:
   - Video stops and resets (currentTime = 0)
   - Video hides (opacity: 0)
   - Current slide shows (opacity: 1)
   - Slider resumes from current slide
```

---

## 🧪 Testing Checklist

### Test trong Elementor Editor:

1. **Mở page với Elementor:**
   - Pages → Edit with Elementor
   
2. **Drag widget vào page:**
   - Tìm "An Mi Video Banner" trong panel
   - Drag vào section
   
3. **Select banner:**
   - Chọn banner từ dropdown
   - HOẶC dùng "Manual Setup"
   
4. **Test trong Editor Preview:**
   - ✅ Slider có auto-play không?
   - ✅ Hover → slider dừng, video hiện?
   - ✅ Click → video chạy?
   - ✅ Mouse leave → video dừng, slider tiếp tục?
   
5. **Publish và test frontend:**
   - Click "Update" hoặc "Publish"
   - View page trên frontend
   - Test lại 4 bước trên

### Test với các video types:

- ✅ YouTube URL (regular, youtu.be, embed)
- ✅ Vimeo URL
- ✅ Direct MP4 URL

### Test responsive:

- ✅ Desktop (hover works)
- ✅ Mobile (check `mobile_behavior` setting)
  - "image" → show image only
  - "video" → show video with autoplay

---

## 📝 Notes cho Developer

### CSS Classes Mapping:

| Old Class (v1.6.8) | New Class (v1.6.10) | Purpose |
|---------------------|---------------------|---------|
| `.anmi-slider-slide` | `.anmi-banner-image` | Individual slide |
| `.dot` | `.anmi-banner-dot` | Slider pagination |
| `.anmi-banner-slider` | (removed) | No wrapper needed |
| (N/A) | `.anmi-play-overlay` | Play button (NEW) |
| `.anmi-banner-video` | `.anmi-banner-video` | Same |
| (N/A) | `.anmi-banner-iframe` | YouTube/Vimeo (NEW) |

### JavaScript Selectors:

```javascript
// OLD (v1.6.8)
this.$slides = this.$container.find('.anmi-slider-slide');
this.$dots = this.$container.find('.anmi-slider-dots .dot');

// NEW (v1.6.10)
this.$images = this.$container.find('.anmi-banner-image');
this.$dots = this.$container.find('.anmi-banner-dot');
this.$playOverlay = this.$container.find('.anmi-play-overlay');
```

### State Management:

```javascript
// NEW state variables (v1.6.9+)
this.isHovered = false;        // Mouse inside banner?
this.isVideoPlaying = false;   // Video currently playing?
```

---

## 🚀 Deployment

1. ✅ Upload plugin files to server
2. ✅ Clear Elementor cache:
   - Elementor → Tools → Regenerate CSS
   - Elementor → Tools → Clear Cache
3. ✅ Clear browser cache
4. ✅ Test widget in Elementor editor
5. ✅ Test published page on frontend

---

## 🐛 Troubleshooting

### Widget không hiện trong Elementor panel:

**Solution:**
```bash
1. Vào Elementor → Tools → Regenerate CSS
2. Deactivate/Activate plugin
3. Clear browser cache
```

### Video không play trong Elementor preview:

**Cause:** JavaScript chưa initialize

**Solution:**
```javascript
// Check console for errors
// Verify AnMiVideoBanner class is loaded
if (typeof AnMiVideoBanner === 'undefined') {
    console.error('AnMiVideoBanner class not found!');
}
```

### Hover behavior không hoạt động:

**Check:**
1. ✅ `video-banner.js` có được enqueue không?
2. ✅ Container có class `.anmi-video-banner-container` không?
3. ✅ `AnMiVideoBanner` constructor có được gọi không?

**Debug:**
```javascript
// Check if initialized
jQuery('.anmi-video-banner-container').data('anmi-initialized'); // should be true
```

---

## 📚 Related Documentation

- [CHANGELOG.md](./CHANGELOG.md) - Version history
- [ELEMENTOR-SETUP-GUIDE.md](./docs/ELEMENTOR-SETUP-GUIDE.md) - User guide
- [INSTALL.md](./docs/INSTALL.md) - Installation guide
- [README.md](./README.md) - Plugin overview

---

**Version:** 1.6.10  
**Date:** 2025-11-03  
**Status:** ✅ Production Ready
