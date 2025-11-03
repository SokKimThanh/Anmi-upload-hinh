# An Mi Video Banner - Changelog

## Version 1.6.10 (2025-11-03)

### 🎨 CRITICAL FIX - Synchronized Elementor Widget with Production

#### **Problem:**
Elementor widget đang sử dụng **HTML structure CŨ**, không giống với preview và production:
- ❌ Class cũ: `.anmi-slider-slide`, `.dot`, `.anmi-banner-slider`
- ❌ Không có `.anmi-play-overlay` (nút play)
- ❌ Không có `.anmi-banner-image` (ảnh riêng lẻ)
- ❌ Auto-play behavior cũ (không có hover + click logic)
- ❌ Không khởi tạo AnMiVideoBanner class

#### **Solution:**
Đồng bộ **Elementor widget** với production shortcode:

1. **Updated HTML Structure** (`elementor-widget.php` - render method):
   - ✅ Dùng `.anmi-banner-image` cho từng slide
   - ✅ Dùng `.anmi-banner-dot` cho slider dots
   - ✅ Thêm `.anmi-play-overlay` với SVG play button
   - ✅ Support YouTube/Vimeo iframe với `.anmi-banner-iframe`
   - ✅ Inline styles cho positioning (giống production)

2. **Added Script Dependencies**:
   ```php
   public function get_script_depends() {
       return ['anmi-video-banner-script'];
   }
   
   public function get_style_depends() {
       return ['anmi-video-banner-style'];
   }
   ```

3. **Added JavaScript Initialization**:
   ```javascript
   // Initialize AnMiVideoBanner class after render
   jQuery(document).ready(function($) {
       if (typeof AnMiVideoBanner !== 'undefined') {
           new AnMiVideoBanner(container[0]);
       }
   });
   
   // Re-initialize on Elementor preview refresh
   elementorFrontend.hooks.addAction('frontend/element_ready/anmi_video_banner.default', ...);
   ```

#### **Result:**
✅ Elementor widget giờ có **SAME BEHAVIOR** với production và preview:
- ① Slider auto-plays
- ② Hover stops slider, shows video (not playing)
- ③ Click plays video
- ④ Mouse leave stops video, resumes slider

---

## Version 1.6.9 (2025-11-03)

### 🎯 NEW FEATURE - Applied Hover + Click Logic to Edit Preview

#### **What's New:**
Áp dụng **hover + click to play behavior** vào **Live Preview** trong trang Add/Edit Banner.

#### **Problem:**
Trước đây, Live Preview trong admin-edit chỉ dùng `AnMiVideoBanner` class cơ bản:
- Không có slider auto-play
- Không có hover effect
- Không có click to play
- Không giống với modal preview và production demo

#### **Solution:**
Implement **SAME LOGIC** như production demo vào `updateLivePreview()`:

```javascript
// 3-Step Flow in Edit Preview:
① Slider auto-play (tiếp tục rotating)
② Hover → Slider stops, video shows
③ Click → Video plays
④ Mouse leave → Video stops, slider resumes
```

---

### **Implementation Details:**

#### **1. HTML Structure Updated**
```javascript
// Before: Simple video + slider
<iframe class="anmi-banner-video">
<div class="anmi-banner-slider">
  <div class="anmi-slider-slide">

// After: Production-style with play button
<div class="anmi-banner-image"> (separate slides)
<iframe class="anmi-banner-video"> (production classes)
<div class="anmi-play-overlay"> (play button)
<div class="anmi-banner-dots"> (slider dots)
```

#### **2. JavaScript Logic Added**
```javascript
var isHovered = false;
var isVideoPlaying = false;
var sliderInterval = null;

// Auto-play slider
sliderInterval = setInterval(...);

// Hover: Stop slider, show video
$container.on('mouseenter', function() {
    clearInterval(sliderInterval);
    $images.css('opacity', '0');
    $video.css('opacity', '1');
});

// Mouse leave: Stop video, resume slider
$container.on('mouseleave', function() {
    if ($video.is('video')) {
        $video[0].pause();
        $video[0].currentTime = 0;
    }
    $video.css('opacity', '0');
    $images.eq(currentSlide).css('opacity', '1');
    sliderInterval = setInterval(...);
});

// Click: Play video
$container.on('click', function() {
    isVideoPlaying = true;
    $playOverlay.fadeOut(300);
    $video[0].play();
});
```

#### **3. Auto-Update on Field Changes**
Preview **automatically updates** khi user thay đổi:
```javascript
// Triggers updateLivePreview():
$('#video_url, #banner_title, #subtitle, ...').on('input change', updateLivePreview);
$('#show_title, #show_subtitle, ...').on('change', updateLivePreview);

// After add/remove images:
updateLivePreview(); // Refresh preview

// After reorder images:
updateLivePreview(); // Refresh preview
```

---

### **Benefits:**

✅ **Consistent UX:** Edit preview = Modal preview = Production demo  
✅ **Real-time Testing:** See exact behavior while editing  
✅ **Auto-refresh:** Preview updates on ANY field change  
✅ **Full Interaction:** Hover, click, mouse leave all work  
✅ **Better Workflow:** No need to save → view → edit cycle  

---

### **Comparison:**

| Feature | v1.6.8 (Old Edit) | v1.6.9 (New Edit) |
|---------|-------------------|-------------------|
| **Slider auto-play** | ❌ No | ✅ Yes |
| **Hover effect** | ❌ No | ✅ Yes (stop slider, show video) |
| **Click to play** | ❌ No | ✅ Yes (manual play) |
| **Mouse leave** | ❌ N/A | ✅ Yes (stop video, resume slider) |
| **Play button** | ❌ No | ✅ Yes (visible overlay) |
| **Slider dots** | ✅ Yes | ✅ Yes (with click navigation) |
| **Auto-update** | ✅ Yes | ✅ Yes (maintained) |
| **Production-like** | ❌ No | ✅ Yes (identical behavior) |

---

### **User Experience:**

#### **Before (v1.6.8):**
```
Edit Page Live Preview:
- Static preview
- No interaction
- Basic slider
- Can't test hover effect
```

#### **After (v1.6.9):**
```
Edit Page Live Preview:
✅ Slider auto-plays
✅ Hover to see video
✅ Click to play video
✅ Mouse leave stops video
✅ Exact production behavior
✅ Test everything while editing!
```

---

### **Auto-Update Triggers:**

Preview refreshes when:
1. ✅ Video URL changes
2. ✅ Title/subtitle/button text changes
3. ✅ Show/hide toggles change
4. ✅ Transition/speed/effect changes
5. ✅ Images added via uploader
6. ✅ Images removed (click X)
7. ✅ Images reordered (drag & drop)

**Result:** Always see up-to-date preview!

---

### **Files Modified:**
- `admin-edit.php` - Rewrote `updateLivePreview()` with hover logic (150+ lines)
- `admin-edit.php` - Updated HTML structure (production-style)
- `anmi-video-banner.php` - Version bump to 1.6.9
- `video-banner.css` - Version comment update
- CHANGELOG.md - Documented new feature

---

### **Technical Notes:**

**Removed:**
```javascript
// Old: Simple AnMiVideoBanner initialization
if (typeof AnMiVideoBanner !== 'undefined') {
    previewInstance = new AnMiVideoBanner($container[0]);
}
```

**Added:**
```javascript
// New: Custom hover + click logic (same as production demo)
var isHovered = false;
var isVideoPlaying = false;
var sliderInterval = setInterval(...);
$container.on('mouseenter/mouseleave/click', ...);
```

**Why?**
- More control over preview behavior
- Consistent with modal preview (admin-list.php)
- Better for testing real interactions

---

## Version 1.6.8 (2025-11-03)

### 🎯 NEW FEATURE - Production Demo Preview (Hover + Click to Play)

#### **What's New:**
Thêm **HÀNG MỚI** trong modal preview để demo **hover effect + click to play** giống production (website thực tế).

#### **User Flow (3 Steps):**

```
Step 1: Initial State
┌─────────────────────────┐
│  [Slider Auto-Playing]  │
│  Image 1 → Image 2 → 3  │
│  🔄 Slides rotating     │
└─────────────────────────┘

Step 2: Hover (Mouse Enter)
┌─────────────────────────┐
│  [Slider STOPS]         │
│  [Video/Iframe SHOWS]   │
│  ▶️ Play Button Visible │
│  ⏸️ Waiting for click   │
└─────────────────────────┘

Step 3: Click Play Button
┌─────────────────────────┐
│  [Video PLAYING]        │
│  Play button fades out  │
│  🎬 Video active        │
└─────────────────────────┘

Step 4: Mouse Leave (ALWAYS)
┌─────────────────────────┐
│  [Video STOPS]          │
│  🛑 video.pause()       │
│  🔄 Reset to start      │
│  ✅ Slider resumes      │
└─────────────────────────┘
```

---

### **Behavior Logic:**

#### **1. Before Hover (Initial State)**
```javascript
✅ Slider: Auto-play (continues rotating)
❌ Video: Hidden (opacity: 0)
✅ Play Button: Visible on slider
⏱️ Slider Speed: Based on banner settings
```

#### **2. On Hover (Mouse Enter)**
```javascript
🛑 Slider: STOP (clearInterval)
✅ Video/Iframe: SHOW (opacity: 1)
✅ Play Button: VISIBLE (waiting for click)
❌ Video: NOT playing yet (paused state)

console.log('Hover detected - slider stopped, video ready');
```

#### **3. On Click (Play Button)**
```javascript
▶️ Video: START playing
💨 Play Button: Fade out
🎬 Video State: isVideoPlaying = true

if (video element):
    video.play()
if (iframe):
    autoplay URL parameter handles it
```

#### **4. On Mouse Leave**
```javascript
// ALWAYS stop video and return to slider
if (isVideoPlaying):
    video.pause()              // Stop playback
    video.currentTime = 0      // Reset to start
    isVideoPlaying = false

// Hide video
$video.css('opacity', '0')

// Show play button again
$playOverlay.show()

// Resume slider
$images.eq(current).css('opacity', '1')
productionSliderInterval = setInterval(...)

console.log('Mouse leave - video stopped, slider resumed');
```

---

### **JavaScript Implementation:**

```javascript
var isHovered = false;
var isVideoPlaying = false;
var productionSliderInterval = null;

// Auto-play slider on load
productionSliderInterval = setInterval(function() {
    if (!isHovered && !isVideoPlaying) {
        // Rotate slides
    }
}, banner.slider_speed);

// HOVER: Stop slider, show video
$container.on('mouseenter', function() {
    isHovered = true;
    clearInterval(productionSliderInterval); // Stop slider
    $images.css('opacity', '0');            // Hide slider
    $video.css('opacity', '1');             // Show video
    $playOverlay.css('opacity', '1');       // Keep play button
});

// MOUSE LEAVE: Resume slider if video not playing
$container.on('mouseleave', function() {
    isHovered = false;
    
    if (!isVideoPlaying) {
        $video.css('opacity', '0');         // Hide video
        $images.eq(current).css('opacity', '1'); // Show current slide
        productionSliderInterval = setInterval(...); // Resume slider
    }
});

// CLICK: Play video
$container.on('click', function() {
    isVideoPlaying = true;
    $playOverlay.fadeOut(300);              // Hide play button
    $video[0].play();                       // Start video
});
```

---

### **HTML Structure:**

```html
<div class="anmi-preview-production-demo">
    <h3>🎯 Production Preview (Hover + Click Effect)</h3>
    <p>
        <strong>Bước 1:</strong> Slider tự động chạy →
        <strong>Bước 2:</strong> Hover vào banner (slider dừng, video hiện) →
        <strong>Bước 3:</strong> Click play để phát video
    </p>
    
    <div class="anmi-video-banner-container">
        <!-- Slider images (visible by default) -->
        <div class="anmi-banner-image active" style="opacity: 1;"></div>
        <div class="anmi-banner-image" style="opacity: 0;"></div>
        
        <!-- Video/iframe (hidden by default) -->
        <video class="anmi-banner-video" style="opacity: 0;"></video>
        
        <!-- Play button overlay -->
        <div class="anmi-play-overlay">
            <svg>play icon</svg>
        </div>
        
        <!-- Slider dots -->
        <div class="anmi-banner-dots">
            <span class="anmi-banner-dot active"></span>
        </div>
    </div>
    
    <div style="background: #f0f0f0;">
        <p>
            <strong>⚡ Flow:</strong>
            <span style="color: #2196F3;">①</span> Slider auto-play →
            <span style="color: #FF9800;">②</span> Hover (slider dừng, video hiện) →
            <span style="color: #4CAF50;">③</span> Click play button
        </p>
    </div>
</div>
```

---

### **CSS (No Changes Needed):**

Existing CSS already supports this behavior:
- `.anmi-banner-video { opacity: 0; }` - Hidden by default
- `.anmi-banner-image { opacity: 1; }` - Visible by default
- Transitions handled by JavaScript opacity changes

---

### **Key Differences from v1.6.7:**

| Aspect | v1.6.7 (Old) | v1.6.8 (New) |
|--------|--------------|--------------|
| **Hover behavior** | Video plays immediately | Video shows, waits for click |
| **Play trigger** | Hover = auto-play | Click = manual play |
| **Slider control** | Always stops on hover | Stops on hover, resumes on leave |
| **Mouse leave** | N/A | **ALWAYS stops video + resets** |
| **Video state** | N/A | Resets to beginning (currentTime = 0) |
| **User control** | Less | More (3-step flow) |
| **Repeatability** | N/A | Can hover again to replay |

---

### **Benefits:**

✅ **Clearer UX:** 3-step flow is intuitive (auto → hover → click)  
✅ **User Control:** Video doesn't autoplay on hover (wait for click)  
✅ **Auto Stop:** Mouse leave ALWAYS stops video and resets slider  
✅ **Repeatable:** Can hover again to replay (video resets to start)  
✅ **Production-like:** Mimics real website behavior accurately  
✅ **Better Demo:** Shows full interaction pattern  

---

### **Files Modified:**
- `admin-list.php` - Rewrote production demo JavaScript logic (150+ lines)
- `admin-list.php` - Updated HTML descriptions for 3-step flow
- CHANGELOG.md - Documented new behavior
- Version: 1.6.8

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
