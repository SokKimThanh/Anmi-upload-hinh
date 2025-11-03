# An Mi Video Banner - Changelog

## Version 1.6.8 (2025-11-03)

### 🎯 NEW FEATURE - Production Demo Preview (Hover Effect)

#### **What's New:**
Thêm **HÀNG MỚI** trong modal preview để demo **hover effect giống production** (website thực tế).

#### **Layout Structure:**

```
┌─────────────────────────────────────────────────────┐
│  Modal Preview                                      │
├─────────────────────────────────────────────────────┤
│  ROW 1: Split Layout (Existing - Không thay đổi)   │
│  ┌──────────────────┬──────────────────┐           │
│  │ 🎬 Video Preview │ 🖼️ Image Slider │           │
│  │ (Clean view)     │ (Standalone)     │           │
│  └──────────────────┴──────────────────┘           │
├─────────────────────────────────────────────────────┤
│  ROW 2: Production Demo (NEW!)                     │
│  ┌─────────────────────────────────────┐           │
│  │ 🎯 Production Preview (Hover Effect)│           │
│  │                                     │           │
│  │  [Slider with Play Button]         │           │
│  │  Hover → Video plays               │           │
│  │                                     │           │
│  └─────────────────────────────────────┘           │
│  ⚡ Hover Effect: Slider fade out → Video play      │
└─────────────────────────────────────────────────────┘
```

---

### **Features Added:**

#### **1. Production Demo Container**
```javascript
// New section added below existing preview
html += '<div class="anmi-preview-production-demo">';
html += '<h3>🎯 Production Preview (Hover Effect)</h3>';
html += '<p>Đây là cách banner hoạt động trên website thực tế</p>';
```

#### **2. Full Production Behavior**
- ✅ **Image slider** visible by default (auto-play)
- ✅ **Video hidden** (opacity: 0) until hover
- ✅ **Play button overlay** centered on banner
- ✅ **Hover effect:** Slider fade out → Video fade in & play
- ✅ **Slider dots** with click navigation
- ✅ **Uses PRODUCTION CSS classes** (.anmi-banner-video, .anmi-banner-iframe)

#### **3. Play Button Overlay**
```html
<div class="anmi-play-overlay">
    <div> <!-- 80px white circle with play icon -->
        <svg> ... play triangle ... </svg>
    </div>
</div>
```

- Centered overlay với play icon
- Fade out khi click/hover
- Pointer-events: none để không block interactions

#### **4. Interactive Features**
```javascript
// Click to play
$productionContainer.on('click', function() {
    $images.css('opacity', '0');      // Hide slider
    $playOverlay.fadeOut(300);        // Hide play button
    $video.css('opacity', '1');       // Show video
    $video[0].play();                 // Start playback
});

// Auto-play slider
setInterval(function() {
    // Cycle through images
}, banner.slider_speed);

// Dot navigation
$('.anmi-banner-dot').on('click', function() {
    // Jump to specific slide
});
```

---

### **CSS Added:**

```css
/* Play button overlay animations */
.anmi-play-overlay {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.anmi-video-banner-container:hover .anmi-play-overlay {
    transform: translate(-50%, -50%) scale(1.1); /* Grow on hover */
    opacity: 0.8;
}

/* Hide when video playing */
.anmi-video-banner-container.video-playing .anmi-play-overlay {
    opacity: 0;
    pointer-events: none;
}

/* Production demo styling */
.anmi-preview-production-demo .anmi-video-banner-container {
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    border-radius: 8px;
    overflow: hidden;
}
```

---

### **User Experience:**

#### **Before (v1.6.7):**
```
Modal Preview:
├─ Video Preview (clean, no context)
└─ Image Slider (standalone)

❌ User không thấy được "hover effect" hoạt động thế nào
```

#### **After (v1.6.8):**
```
Modal Preview:
├─ Video Preview (clean view)
├─ Image Slider (standalone)
└─ 🆕 Production Demo (hover effect)
    ├─ Shows slider with play button
    ├─ Hover → Video plays (like real website)
    └─ Full interactive experience

✅ User thấy CHÍNH XÁC cách banner hoạt động trên website
```

---

### **Technical Details:**

| Aspect | Implementation |
|--------|---------------|
| **HTML** | Separate `<div class="anmi-preview-production-demo">` section |
| **CSS Classes** | Uses `.anmi-banner-video`, `.anmi-banner-iframe` (production classes) |
| **JavaScript** | Full `AnMiVideoBanner` initialization + custom slider + click handlers |
| **Position** | Below existing split layout (Row 2) |
| **Height** | 500px (larger than preview for better demo) |
| **Behavior** | Click to play + hover effect + auto-slider |

---

### **Files Modified:**
- `admin-list.php` - Added production demo HTML section (100+ lines)
- `admin-list.php` - Added JS initialization for production demo
- `video-banner.css` - Added play overlay animations
- Version: 1.6.8

### **Why This Matters:**

**Problem:** Users couldn't see hover effect in preview  
**Solution:** Added full production demo section  
**Result:** Complete preview of ALL banner features in one modal!

---

### **Benefits:**

✅ **Complete Preview:** See both clean view AND production behavior  
✅ **Better UX:** Users understand how banner works on real website  
✅ **No Conflicts:** Production demo uses separate container  
✅ **Reuses Code:** Leverages existing CSS and JS (AnMiVideoBanner class)  
✅ **Clean Separation:** Row 1 = clean previews, Row 2 = production demo  

---

## Version 1.6.7 (2025-11-03)

### 🎯 CRITICAL FIX - Separate Production & Preview Classes

#### **Problem:**
Production (frontend) và Modal Preview (admin panel) đang **SHARE CLASSES** → Gây conflict:
- `anmi-banner-video` được dùng cho cả production và preview
- `anmi-banner-iframe` được dùng cho cả production và preview  
- CSS production (opacity: 0, transform, scaling) ảnh hưởng lên preview
- `:not(.anmi-modal-iframe)` selector phức tạp và dễ lỗi

#### **Root Cause:**
```html
<!-- PRODUCTION (Frontend) -->
<video class="anmi-banner-video">...</video>
<iframe class="anmi-banner-iframe">...</iframe>

<!-- PREVIEW (Admin) - TRƯỚC ĐÂY -->
<video class="anmi-banner-video anmi-modal-video">...</video>
<iframe class="anmi-banner-iframe anmi-modal-iframe">...</iframe>
```
→ Cùng base classes → CSS conflicts!

---

### **Solution: Complete Separation**

#### **New Class Structure:**

| Context | Video Element | Iframe Element |
|---------|---------------|----------------|
| **Production (Frontend)** | `.anmi-banner-video` | `.anmi-banner-iframe` |
| **Preview (Admin)** | `.anmi-preview-video` | `.anmi-preview-iframe` |

**Zero shared classes = Zero conflicts!**

---

### **HTML Changes:**

#### **Before (v1.6.6) - Shared classes:**
```html
<!-- Modal Preview -->
<iframe class="anmi-banner-video anmi-banner-iframe anmi-modal-iframe">
<video class="anmi-banner-video anmi-modal-video">
```

#### **After (v1.6.7) - Separate classes:**
```html
<!-- Modal Preview -->
<iframe class="anmi-preview-iframe">
<video class="anmi-preview-video">
```

---

### **CSS Changes:**

#### **Production (Unchanged):**
```css
/* Frontend video background effect */
.anmi-banner-video {
    position: absolute;
    opacity: 0;
    transform: translate(-50%, -50%);
    object-fit: cover;
}

.anmi-banner-iframe {
    position: absolute;
    width: 100%;
    height: 100%;
}

/* Scaling for cover effect */
@media (min-aspect-ratio: 16/9) {
    .anmi-banner-iframe {
        height: 300%;
        top: -100%;
    }
}
```

#### **Preview (New Simple Rules):**
```css
/* Admin modal preview - Natural sizing */
.anmi-preview-video {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background: #000;
}

.anmi-preview-iframe {
    width: 100%;
    height: 100%;
    border: none;
    background: #000;
}

/* No transforms, no scaling, no positioning */
/* Just clean natural browser behavior */
```

---

### **JavaScript Changes:**

#### **Updated Event Listeners:**
```javascript
// BEFORE - Used production classes
$('.anmi-modal-iframe').on('load', ...);
$('.anmi-modal-video').on('loadeddata', ...);

// AFTER - Use preview-specific classes
$('.anmi-preview-iframe').on('load', ...);
$('.anmi-preview-video').on('loadeddata', ...);
```

---

### **Benefits:**

✅ **Zero Conflicts:** Production và preview hoàn toàn độc lập  
✅ **Simpler CSS:** Không cần `:not()` selectors phức tạp  
✅ **Cleaner Code:** Rõ ràng class nào cho context nào  
✅ **Easier Maintenance:** Sửa production không ảnh hưởng preview  
✅ **Better Performance:** Ít CSS specificity wars  

---

### **Files Modified:**
- `admin-list.php` - Changed preview HTML classes from `anmi-banner-*` → `anmi-preview-*`
- `video-banner.css` - Added separate `.anmi-preview-video` and `.anmi-preview-iframe` rules
- `video-banner.css` - Removed `.anmi-modal-*` rules and `:not()` selectors
- Version: 1.6.7

### **Migration Notes:**
- **Production (frontend):** No changes - works exactly as before
- **Preview (admin):** Uses new dedicated classes
- **No breaking changes:** All existing banners work unchanged

---

## Version 1.6.6 (2025-11-03)

### 🎯 Critical Fix - Iframe Sizing (Final Solution)

#### **Problem Discovery:** 
Testing revealed that when **ALL CSS rules were commented out**, iframe worked perfectly and filled container naturally. This proved our CSS was **CAUSING THE PROBLEM**, not solving it.

#### **Root Cause:**
Over-engineered CSS with excessive `!important` rules were **FIGHTING** against natural browser behavior instead of working with it.

#### **Philosophy Change:**
❌ **Before:** "Force everything with !important"  
✅ **After:** "Let browser do its job, only override what's necessary"

---

### **Final Minimal Solution:**

#### **For Production (Frontend):**
```css
.anmi-banner-video {
    /* Center and cover - unchanged */
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    object-fit: cover;
    opacity: 0;
}

.anmi-banner-iframe {
    /* Natural positioning */
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
}

/* Scale only production (exclude modal) */
@media (min-aspect-ratio: 16/9) {
    .anmi-banner-iframe:not(.anmi-modal-iframe) {
        height: 300%;
        top: -100%;
    }
}
```

#### **For Modal Preview:**
```css
.anmi-modal-video {
    opacity: 1 !important;      /* Show video */
    transform: none !important; /* Remove centering */
}

/* .anmi-modal-iframe - NO CSS NEEDED! */
/* Browser naturally fills container with iframe */
/* :not(.anmi-modal-iframe) prevents media query scaling */
```

---

### **Key Insight:**

**HTML `<iframe>` default behavior:**
```html
<div style="width: 100%; height: 400px;">
    <iframe src="..."></iframe>
</div>
```
→ Iframe **NATURALLY** fills parent container (100% width/height by default)  
→ We were **BREAKING** this with unnecessary CSS!

---

### **What Was Removed:**

```css
/* ❌ REMOVED - Caused conflicts */
.anmi-modal-iframe {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
}

/* ✅ RESULT: Browser handles sizing naturally */
```

---

### **CSS Architecture - Simplified:**

| Element | Production | Modal Preview |
|---------|-----------|---------------|
| **Video** | Custom positioning | `opacity: 1`, `transform: none` |
| **Iframe** | Custom scaling | **NO CSS** (natural behavior) |
| **Why?** | Need cover effect | Browser does it right |

---

### **Files Modified:**
- `video-banner.css` - Removed 90% of modal CSS, kept only 2 properties
- `admin-style.css` - Minimal container rules only
- Version: 1.6.6

### **Testing Confirmed:**
- [x] Iframe fills container naturally
- [x] No CSS conflicts
- [x] Production scaling still works (`:not(.anmi-modal-iframe)`)
- [x] Modal preview clean and simple
- [x] Works exactly like when all CSS was commented out

---

### **Lesson Learned:**

> "Sometimes the best code is NO code. Let the browser do what it does best."

**Before:** 50+ lines of CSS fighting browser  
**After:** 2 lines working WITH browser

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
