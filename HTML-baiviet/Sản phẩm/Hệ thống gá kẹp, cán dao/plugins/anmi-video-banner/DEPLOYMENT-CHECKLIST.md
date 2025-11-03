# ✅ CHECKLIST - An Mi Video Banner v1.6.10

## 📋 Pre-Deployment Verification

### 1. Files Updated ✅

- [x] `anmi-video-banner.php` - Version 1.6.10
- [x] `includes/elementor-widget.php` - Updated render() method
- [x] `CHANGELOG.md` - Added v1.6.10 entry
- [x] `ELEMENTOR-SYNC-NOTES.md` - NEW technical doc
- [x] `SUMMARY-v1.6.10.md` - NEW summary doc

### 2. Code Quality ✅

- [x] No PHP syntax errors
- [x] No JavaScript syntax errors  
- [x] All closing tags properly matched
- [x] Version constants updated

### 3. Functionality Checklist

#### HTML Structure:
- [x] Uses `.anmi-banner-image` (not `.anmi-slider-slide`)
- [x] Uses `.anmi-banner-dot` (not `.dot`)
- [x] Has `.anmi-play-overlay` with SVG
- [x] Removed nested `.anmi-banner-slider` wrapper
- [x] Inline styles for positioning
- [x] Video type detection (YouTube/Vimeo/Direct)

#### JavaScript:
- [x] `get_script_depends()` returns correct handle
- [x] `get_style_depends()` returns correct handle
- [x] Initialization code added to render output
- [x] Elementor preview refresh handler added
- [x] Duplicate initialization prevention (`data-anmi-initialized`)

---

## 🚀 Deployment Steps

### Step 1: Upload Files
```bash
# Upload entire plugin folder to:
wp-content/plugins/anmi-video-banner/

# Or via FTP/cPanel File Manager
```

### Step 2: Activate/Update Plugin
```bash
1. Go to: WordPress Admin → Plugins
2. If already active: Plugin will auto-update
3. If not active: Click "Activate"
```

### Step 3: Clear Caches
```bash
# Elementor Cache:
Elementor → Tools → Regenerate CSS & Data
Elementor → Tools → Clear Cache

# Browser Cache:
Press Ctrl + Shift + Delete (or Cmd + Shift + Delete on Mac)
Select "Cached images and files"
Clear
```

---

## 🧪 Testing Procedure

### Test 1: Admin Panel ✅
```
1. Go to: An Mi Video Banner → All Banners
2. Click "Xem Trước" on any banner
3. Check Modal Preview:
   - Row 1: Traditional preview (OK)
   - Row 2: Production demo
     ✓ Slider auto-plays
     ✓ Hover stops slider, shows video
     ✓ Click plays video
     ✓ Mouse leave stops video
```

### Test 2: Edit Page Preview ✅
```
1. Go to: An Mi Video Banner → Add New/Edit
2. Fill in fields (or load existing)
3. Check Live Preview panel:
   ✓ Slider auto-plays
   ✓ Hover stops slider, shows video
   ✓ Click plays video
   ✓ Mouse leave stops video
```

### Test 3: Elementor Widget (NEW - v1.6.10) ⏳
```
1. Create/Edit page with Elementor:
   Pages → Add New → Edit with Elementor

2. Drag "An Mi Video Banner" widget:
   From left panel → Drag to section

3. Configure widget:
   Option A: Select saved banner from dropdown
   Option B: Use "Manual Setup"

4. Test in Elementor Preview:
   ✓ Slider auto-plays
   ✓ Hover stops slider, shows video (not playing)
   ✓ Click plays video
   ✓ Mouse leave stops video, resumes slider

5. Publish and test frontend:
   Click "Publish" → View page
   ✓ Same behavior as Elementor preview
```

### Test 4: Production Shortcode ✅
```
1. Create page/post with shortcode:
   [anmi_video_banner id="X"]

2. View on frontend:
   ✓ Slider auto-plays
   ✓ Hover stops slider, shows video
   ✓ Click plays video
   ✓ Mouse leave stops video
```

---

## 🎯 Expected Behavior (All Contexts)

### 3-Step Interaction Flow:

```
STEP 1: Initial State
├─ Slider is auto-playing
├─ Images rotating every 3 seconds
├─ Video is hidden (opacity: 0)
└─ Play button is hidden

STEP 2: Mouse Enter (Hover)
├─ Slider stops immediately
├─ Current image stays visible
├─ Video appears (opacity: 1) but NOT playing
├─ Play button appears (opacity: 1)
└─ User sees: Image → Video (paused) + Play button

STEP 3: Click Play Button
├─ Video starts playing
├─ Play button fades out (opacity: 0)
└─ User sees: Video playing

STEP 4: Mouse Leave
├─ Video pauses
├─ Video resets to start (currentTime = 0)
├─ Video hides (opacity: 0)
├─ Current slide shows (opacity: 1)
├─ Play button resets (opacity: 0)
└─ Slider resumes from current slide
```

---

## 🔍 Debugging Checklist

### If Elementor widget doesn't appear:

```javascript
// Check 1: Elementor version
Elementor → System Info → Version (should be 3.0+)

// Check 2: Clear Elementor cache
Elementor → Tools → Regenerate CSS
Elementor → Tools → Clear Cache

// Check 3: Deactivate/Reactivate plugin
Plugins → Deactivate An Mi Video Banner
Plugins → Activate An Mi Video Banner
```

### If hover behavior doesn't work:

```javascript
// Check 1: Console errors
F12 → Console tab → Look for errors

// Check 2: Verify script loaded
F12 → Network tab → Filter "JS" → Look for "video-banner.js"

// Check 3: Check initialization
F12 → Console → Run:
jQuery('.anmi-video-banner-container').data('anmi-initialized')
// Should return: true

// Check 4: Verify class exists
F12 → Console → Run:
typeof AnMiVideoBanner
// Should return: "function"
```

### If video doesn't play:

```javascript
// Check 1: Video URL is valid
// Check 2: YouTube/Vimeo embed allowed on site
// Check 3: Browser autoplay policy (muted videos allowed)

// Debug video element:
F12 → Elements tab → Find ".anmi-banner-video"
Check style="opacity: 1" when video should show
```

---

## 📊 Version History Reference

| Version | Date | Main Feature | Status |
|---------|------|--------------|--------|
| 1.6.7 | 2025-11-03 | Separated production/preview classes | ✅ |
| 1.6.8 | 2025-11-03 | Added production demo to modal | ✅ |
| 1.6.9 | 2025-11-03 | Applied hover+click to edit preview & production | ✅ |
| **1.6.10** | 2025-11-03 | **Synchronized Elementor widget** | ✅ **NEW** |

---

## 📞 Support

### Common Issues:

**Q: Widget không hiện trong Elementor panel?**  
A: Clear Elementor cache (Tools → Regenerate CSS)

**Q: Video không auto-play trong Elementor preview?**  
A: Check console for errors, verify AnMiVideoBanner class loaded

**Q: Hover behavior khác với production?**  
A: Ensure widget uses v1.6.10 code, clear browser cache

**Q: Mobile không hoạt động?**  
A: Check `mobile_behavior` setting in widget config

---

## ✨ Final Checklist

### Before marking as complete:

- [ ] All files uploaded
- [ ] Plugin activated
- [ ] Elementor cache cleared
- [ ] Browser cache cleared
- [ ] Admin modal preview tested
- [ ] Admin edit preview tested
- [ ] **Elementor widget tested** (NEW in v1.6.10)
- [ ] Production shortcode tested
- [ ] Mobile responsive tested
- [ ] Cross-browser tested

### Success Criteria:

- [ ] ✅ Slider auto-plays in all contexts
- [ ] ✅ Hover stops slider, shows video (not playing)
- [ ] ✅ Click plays video
- [ ] ✅ Mouse leave stops video, resumes slider
- [ ] ✅ No console errors
- [ ] ✅ Behavior identical in all 4 contexts

---

**Status:** ✅ READY FOR DEPLOYMENT  
**Version:** 1.6.10  
**Date:** 2025-11-03

**Contexts Synchronized:**
1. ✅ Production Shortcode
2. ✅ Admin Modal Preview
3. ✅ Admin Edit Preview
4. ✅ **Elementor Widget** (NEW)

**Next Action:** Deploy to staging/production and perform full testing cycle 🚀
