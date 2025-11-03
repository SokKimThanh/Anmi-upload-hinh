# An Mi Video Banner - Changelog

## Version 1.6.6 (2025-11-03)

### 🎯 Critical Fix - Iframe Sizing & Aspect Ratio

#### **Problem:** Iframe không vừa với kích thước container
**Symptoms:**
- Video bị scale quá lớn (300% trong media queries)
- Iframe không fit đúng khung preview
- Video bị crop hoặc overflow

#### **Root Cause:**
CSS media queries scale iframe 300% (design cho background video cover) → Không phù hợp với modal preview

#### **Solutions:**

**1. Separate Modal Iframe from Production**
```css
/* Modal preview: Keep at 100% */
.anmi-modal-iframe {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: contain; /* Fit entire video */
}

/* Production: Only scale non-modal iframes */
@media (min-aspect-ratio: 16/9) {
    .anmi-banner-iframe:not(.anmi-modal-iframe) {
        height: 300%;
        top: -100%;
    }
}
```

**2. Fixed Video Element Sizing**
```css
.anmi-modal-video {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    transform: none !important; /* Override translate(-50%, -50%) */
    object-fit: contain !important;
}
```

**3. Container Constraints**
```css
#anmi-preview-container .anmi-video-banner-container {
    position: relative;
    height: 400px; /* Fixed height */
    background: #000;
}

#anmi-preview-container .anmi-banner-iframe,
#anmi-preview-container .anmi-banner-video {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
}
```

### 🎨 Visual Improvements
- ✅ Video/iframe fit đúng trong khung 400px
- ✅ `object-fit: contain` giữ aspect ratio đúng
- ✅ Không bị crop hay overflow
- ✅ Background đen (#000) giống video player thực tế

### 📝 Technical Details
**Key CSS Properties:**
- `object-fit: contain` - Fit toàn bộ video trong container (có letterbox nếu cần)
- `object-fit: cover` - Fill toàn bộ container (có crop nếu cần) - Dùng cho production
- `:not(.anmi-modal-iframe)` - Selector exclude modal để avoid conflict

**Files Modified:**
- `video-banner.css` - Added `.anmi-modal-iframe` và `.anmi-modal-video` rules
- `admin-style.css` - Enhanced container positioning & sizing
- Version bumped to 1.6.6

### ✅ Testing Checklist
- [x] Iframe fit đúng khung 400px height
- [x] Video không bị scale quá lớn
- [x] Aspect ratio được giữ nguyên (16:9)
- [x] Letterbox xuất hiện nếu video không đúng tỷ lệ
- [x] Production (frontend) vẫn hoạt động bình thường

---

## Version 1.6.5 (2025-11-03)

### 🐛 Critical Bug Fixes - Video Display & Interaction

#### **Problem:** Video iframe không hiển thị hoặc không tương tác được
Nguyên nhân phân tích từ user:
1. ✅ Loader không được ẩn → che mất iframe
2. ✅ `pointer-events` bị chặn → không click được video
3. ✅ z-index không đúng → video bị nằm dưới các layer khác

#### **Solutions Implemented:**

**1. Auto-hide Loader when Iframe/Video Loads**
```javascript
// Event listener for iframe load
$('.anmi-modal-iframe').on('load', function() {
    $(this).closest('.anmi-video-banner-container').find('.anmi-banner-loader').fadeOut(300);
});

// Event listener for video loadeddata
$('.anmi-modal-video').on('loadeddata', function() {
    $(this).closest('.anmi-video-banner-container').find('.anmi-banner-loader').fadeOut(300);
});

// Fallback: Force hide after 3 seconds
setTimeout(() => $('.anmi-banner-loader').fadeOut(300), 3000);
```

**2. Fixed Loader CSS - Don't Block Clicks**
```css
.anmi-banner-loader {
    pointer-events: none; /* CRITICAL: Don't block clicks when hidden */
    z-index: 10; /* Increased for better visibility */
}

.anmi-banner-loader.active {
    pointer-events: auto; /* Only block when visible */
}
```

**3. Ensure Video/Iframe Can Be Clicked**
```css
#anmi-preview-container .anmi-video-banner-container {
    pointer-events: auto !important;
}

#anmi-preview-container .anmi-banner-iframe,
#anmi-preview-container .anmi-banner-video {
    pointer-events: auto !important;
    cursor: pointer;
}
```

**4. Inline Styles for Z-index & Position**
```html
<iframe style="pointer-events: auto; position: absolute; top: 0; left: 0; 
               width: 100%; height: 100%; z-index: 2;">
```

### 🔍 Debug Improvements
- Added console logs for loader hide events
- Added `.anmi-modal-iframe` and `.anmi-modal-video` classes for targeted event binding
- Fallback timeout ensures loader always disappears

### 📝 Code Quality
- Version updated to 1.6.5 across all files
- Enhanced CSS specificity with `!important` for modal context
- Better separation of concerns: modal preview vs production usage

### ✅ Testing Checklist
- [x] Iframe loads và loader tự động ẩn
- [x] Video element loads và loader tự động ẩn
- [x] Fallback timeout (3s) hoạt động
- [x] Click vào video iframe → Có thể tương tác
- [x] Pointer-events không bị chặn
- [x] Console logs hiển thị "Iframe loaded - hiding loader"

---

## Version 1.6.4 (2025-11-03)

### ✨ New Features - Split Preview Layout
- **🎬 Separated Video & Slider Preview:** Modal preview now shows video and slider **side-by-side** in a 2-column layout
  - **LEFT COLUMN:** Video preview with content overlay (if exists)
  - **RIGHT COLUMN:** Image slider showcase with auto-play
- **🖼️ Interactive Slider Preview:** Standalone slider with:
  - Auto-advance based on `slider_speed` setting
  - Clickable dots to navigate slides
  - Effect transitions (fade/slide) matching banner settings
  - Info badge showing: slide count, speed, and effect type
- **📱 Responsive Design:** Stacks to single column on smaller screens (<1200px)

### 🎨 UI/UX Improvements
- **Gradient Headers:** Beautiful purple gradient headings for each preview section
- **Better Visual Separation:** Each column has its own card-style container
- **Wider Modal:** Increased from 1200px to 1400px to accommodate split layout
- **Enhanced Styling:** Clean backgrounds, shadows, and rounded corners
- **Video Interaction:** Changed iframe `pointer-events` to `auto` for better preview interaction

### 🔧 Technical Changes
- **New CSS Classes:**
  - `.anmi-preview-split-layout` - Grid container for 2-column layout
  - `.anmi-preview-video-column` - Left column wrapper
  - `.anmi-preview-slider-column` - Right column wrapper
  - `.anmi-preview-slide` - Individual slider preview slide
  - `.anmi-preview-dot` - Slider navigation dots
- **New JavaScript Function:** `initPreviewSlider()` for standalone slider auto-play
- **Slider Logic:** Independent from main AnMiVideoBanner class for preview-only functionality

### 📝 Code Quality
- Version updated to 1.6.4 across all files
- Asset cache busting with new version numbers
- Clean separation of concerns between video and slider previews

---

## Version 1.6.3 (2025-11-03)

### 🐛 Critical Bug Fixes
- **Fixed YouTube URL Detection:** Updated regex pattern to support `youtu.be` URLs with query parameters (e.g., `?si=...`)
  - Old pattern: `/youtu\.be\/)([^"&?\/ ]{11})/` ❌ Failed on URLs with `?si=` parameter
  - New pattern: `/youtu\.be\/)([a-zA-Z0-9_-]{11})(?:[?&]|$)/` ✅ Works with all YouTube share URL formats
- **Enhanced WebFontLoader Dequeue:** Added early `admin_init` hook with priority 9999 to prevent script conflicts
  - Prevents "d[b].on is not a function" error more reliably
  - Added late priority (999) to `admin_enqueue_scripts`
- **Improved Modal Preview Debugging:** Added comprehensive console logs for video URL parsing:
  - Original video_url logging
  - video_type detection logging
  - Iframe extraction tracking
  - YouTube/Vimeo detection confirmation
  - Final embed URL verification

### 🔍 Debug Improvements
- Console logs now show complete video processing pipeline:
  ```javascript
  Original video_url: https://youtu.be/egbA1RHO8MY?si=...
  video_type: youtube
  Not an iframe code, using URL directly
  YouTube detected - Video ID: egbA1RHO8MY
  Final video detection - Type: youtube, Embed URL: https://www.youtube.com/embed/...
  Creating iframe for youtube with URL: https://www.youtube.com/embed/...
  ```

### 📝 Code Quality
- Updated version to 1.6.3 across all plugin files
- Enhanced comments in regex patterns explaining query parameter support
- Added null-safety check for video_url before indexOf operation

---

## Version 1.6.1 (2025-11-03)

### 🐛 Bug Fixes
- **Fixed Modal Preview:** Exposed `AnMiVideoBanner` class to global scope (`window.AnMiVideoBanner`) to enable admin modal preview initialization
- **Fixed JavaScript Error:** Dequeued `webfontloader` script on admin pages to prevent "d[b].on is not a function" error
- **Improved Iframe Embed Field:** Added debug console logs, better styling (dashed border, monospace font), and clearer Vietnamese instructions

### 🎨 UI Improvements
- Enhanced textarea styling for iframe embed code with distinctive visual design
- Added yellow instruction box with step-by-step guide for YouTube/Vimeo embed codes
- Improved field visibility toggle with console debugging

### 📝 Code Quality
- Updated all version numbers to 1.6.1 across plugin files
- Added comprehensive changelogs in file headers
- Improved asset cache busting with updated version numbers

---

## Version 1.6.0 (2025-11-03)

### ✨ New Features
- **Iframe Embed Support:** Added dedicated textarea field for pasting YouTube/Vimeo iframe embed codes
- **Automatic URL Extraction:** Plugin automatically extracts `src` URL from iframe code
- **Live Preview for Embeds:** Real-time preview updates when pasting iframe codes

### 🔧 Technical Changes
- Added `video-embed-row` field with show/hide logic based on video type selection
- Implemented iframe `src` attribute extraction via regex
- Enhanced form validation for embed codes

---

## Version 1.5.1 (Previous)

### ✨ Features
- Player mode with visible YouTube controls
- Improved video UX with clickable iframe controls

---

## Version 1.5.0 (Previous)

### ✨ Features
- Live inline preview in Edit page
- Modal preview in List page
- Real-time content updates

---

## Version 1.4.0 (Previous)

### ✨ Features
- YouTube/Vimeo auto-detection
- Iframe embed rendering
- Background video mode

---

## Version 1.3.2 (Previous)

### 🐛 Bug Fixes
- Video preload optimization (metadata + 3s timeout)
- Added poster image for better loading experience

---

## Version 1.3.1 (Previous)

### 🌐 Localization
- Vietnamese UI translation
- Image upload bugfix

---

## Version 1.3.0 (Previous)

### ✨ Features
- Content visibility controls (show/hide title, subtitle, button)
- Enhanced admin interface

---

## Upgrade Notes

### From 1.6.0 to 1.6.1
- No database changes required
- Clear browser cache to load updated JavaScript
- Modal preview should now work without console errors

### From 1.5.x to 1.6.x
- New database field: `video_type` (automatically added)
- Existing banners will work without modification
- Recommended: Review and update to use new iframe embed feature if needed

---

## Support

For issues or questions:
- Website: https://anmitools.com
- Plugin URI: https://anmitools.com/plugins/anmi-video-banner
