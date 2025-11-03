# 📱 Mobile Testing Guide - v1.6.11

## ✅ Hoàn thành Implementation

Version 1.6.11 đã fix lỗi video không phát trên mobile devices.

---

## 🎯 Changes Summary

### Files Updated:

1. **`assets/js/video-banner.js`** ✅
   - Separated mobile vs desktop logic
   - Mobile: Tap-to-play (1 tap = show + play)
   - Desktop: Hover + Click (unchanged)
   - Added error handling for play() promise

2. **`assets/css/video-banner.css`** ✅
   - Fixed iframe `pointer-events: none` (allow container clicks)
   - Added desktop-only `pointer-events: auto` when playing

3. **`includes/admin-panel.php`** ✅
   - Changed default `mobile_behavior` từ `'image'` → `'video'`
   - New banners sẽ enable video trên mobile mặc định

4. **`anmi-video-banner.php`** ✅
   - Version bump: 1.6.10 → 1.6.11

5. **`CHANGELOG.md`** ✅
   - Documented all changes

---

## 🧪 Testing Procedure

### **Test 1: iOS Safari (iPhone/iPad)**

**Setup:**
1. Upload plugin lên server
2. Activate plugin
3. Create/Edit banner với video
4. Add banner vào page
5. Open page trên iPhone Safari

**Expected Behavior:**
```
① Slider auto-plays (images rotating)
② Tap anywhere on banner
   → Slider stops
   → Video appears
   → Video plays immediately ✅
③ Tap again
   → Video pauses
   → Video resets to start
   → Slider resumes
```

**Check Console:**
```javascript
// F12 → Console (on Mac with Safari Remote Debug)
// Should see:
"Mobile video playing" ✅

// Should NOT see:
"Mobile video play failed" ❌
"NotAllowedError" ❌
```

**Test Cases:**
- [ ] Direct MP4 video plays
- [ ] YouTube iframe video shows
- [ ] Vimeo iframe video shows
- [ ] Play button hides when video plays
- [ ] No layout shift
- [ ] Portrait orientation works
- [ ] Landscape orientation works
- [ ] Tap outside banner doesn't affect

---

### **Test 2: Android Chrome**

**Setup:**
1. Same setup as iOS
2. Open page trên Android Chrome
3. Enable USB debugging
4. Chrome DevTools → Remote devices

**Expected Behavior:**
```
Same as iOS Safari ✅
```

**Check Console:**
```javascript
// Chrome DevTools → Console
"Mobile video playing" ✅
```

**Test Cases:**
- [ ] Touch events fire correctly
- [ ] Video plays on first tap
- [ ] No `touchstart` vs `click` conflicts
- [ ] Fullscreen button works (if native controls)
- [ ] Landscape mode works

---

### **Test 3: Desktop (Regression Test)**

**Setup:**
1. Open page trên desktop browser (Chrome/Firefox/Safari)
2. Window width > 768px

**Expected Behavior:**
```
Desktop behavior UNCHANGED:
① Hover → Video shows (not playing) ✅
② Click → Video plays ✅
③ Mouse leave → Video stops ✅
```

**Test Cases:**
- [ ] Hover shows video
- [ ] Hover shows play button
- [ ] Click plays video
- [ ] Click hides play button
- [ ] Mouse leave stops video
- [ ] Mouse leave resumes slider
- [ ] No mobile logic triggers

---

### **Test 4: Responsive (Viewport Resize)**

**Setup:**
1. Open page trên desktop
2. Resize browser window from wide → narrow
3. Cross 768px breakpoint

**Expected Behavior:**
```
① Desktop mode (> 768px): Hover + Click ✅
② Resize to < 768px
   → Switch to mobile mode
   → Tap to play ✅
③ Resize back to > 768px
   → Switch back to desktop mode
   → Hover + Click ✅
```

**Test Cases:**
- [ ] Logic switches at 768px breakpoint
- [ ] No JavaScript errors during resize
- [ ] Video resets properly
- [ ] Event handlers update correctly

---

### **Test 5: Different Video Types**

#### **A. Direct MP4 Video:**
```html
<video src="video.mp4" ...>
```
- [ ] Plays on mobile tap
- [ ] Muted by default
- [ ] `playsinline` works (no fullscreen on iOS)
- [ ] Loops correctly

#### **B. YouTube Iframe:**
```html
<iframe src="https://youtube.com/embed/...?autoplay=1&mute=1">
```
- [ ] Shows on mobile tap
- [ ] Autoplay triggers (iframe URL param)
- [ ] No click blocking
- [ ] Controls hidden

#### **C. Vimeo Iframe:**
```html
<iframe src="https://player.vimeo.com/video/...?autoplay=1&muted=1&background=1">
```
- [ ] Shows on mobile tap
- [ ] Background mode works
- [ ] No controls overlay
- [ ] Loops correctly

---

### **Test 6: Mobile Behavior Settings**

#### **Setting: `mobile_behavior = "video"` (Default)**
```
✅ Video enabled on mobile
✅ Tap to play works
```

#### **Setting: `mobile_behavior = "image"`**
```
✅ Video disabled (removed from DOM)
✅ Only slider shows
✅ No tap interaction (just slider)
```

#### **Setting: `mobile_behavior = "both"` (if exists)**
```
Should behave same as "video"
```

---

### **Test 7: Edge Cases**

#### **A. No Internet Connection:**
- [ ] Video doesn't load → Show fallback image
- [ ] No infinite loading spinner
- [ ] Console shows error gracefully

#### **B. Slow 3G:**
- [ ] Video preload="metadata" (minimal data)
- [ ] Poster image shows while loading
- [ ] Tap doesn't crash if video not ready

#### **C. Autoplay Blocked:**
- [ ] Play() promise catches error
- [ ] Fallback: Show play button again
- [ ] Console error logged
- [ ] User can retry

#### **D. Multiple Banners:**
- [ ] Each banner works independently
- [ ] Tapping one doesn't affect others
- [ ] No variable leakage between instances

---

## 🔍 Debugging Tools

### **iOS Safari (Mac Required):**

1. Enable Web Inspector:
   - iPhone: Settings → Safari → Advanced → Web Inspector
   - Mac: Safari → Preferences → Advanced → Show Develop menu

2. Connect iPhone via USB

3. Mac Safari → Develop → [Your iPhone] → [Your Page]

4. Console will show:
   ```javascript
   "Mobile video playing" // ✅ Success
   "Mobile video play failed: ..." // ❌ Error
   ```

### **Android Chrome:**

1. Enable USB Debugging:
   - Settings → About Phone → Tap "Build Number" 7 times
   - Settings → Developer Options → USB Debugging

2. Connect Android via USB

3. Chrome on PC → `chrome://inspect`

4. Find your device → Inspect

### **Browser Console Commands:**

```javascript
// Check if mobile detected
jQuery('.anmi-video-banner-container').data('mobile-behavior');

// Check video element
jQuery('.anmi-banner-video').length; // Should be 1

// Check video opacity
jQuery('.anmi-banner-video').css('opacity'); // "0" initially, "1" after tap

// Check if video playing
var video = jQuery('.anmi-banner-video')[0];
video.paused; // false if playing

// Manual play test
video.play().then(() => console.log('OK')).catch(e => console.error(e));
```

---

## 📊 Expected Results

### **Success Criteria:**

| Platform | Browser | Status | Notes |
|----------|---------|--------|-------|
| iOS 14+ | Safari | ✅ Should work | playsinline required |
| iOS 14+ | Chrome | ✅ Should work | Uses Safari engine |
| Android 10+ | Chrome | ✅ Should work | Native support |
| Android 10+ | Firefox | ✅ Should work | Native support |
| Android 10+ | Samsung Internet | ✅ Should work | Chromium-based |
| Desktop | All modern browsers | ✅ Should work | Unchanged |

### **Known Limitations:**

- ❌ iOS < 10: No `playsinline` support (will fullscreen)
- ❌ Android < 5: Limited autoplay support
- ⚠️ Data Saver Mode: Video may not autoplay (user tap required)
- ⚠️ Low Power Mode (iOS): Video performance may degrade

---

## 🐛 Troubleshooting

### **Issue 1: Video doesn't play on tap**

**Check:**
```javascript
// Console should show:
"Mobile video playing" ✅

// If shows:
"Mobile video play failed: NotAllowedError"
// → Video not muted or no user gesture
```

**Fix:**
1. Verify `<video muted playsinline>`
2. Verify tap is direct (not async)
3. Check autoplay policy

### **Issue 2: Tap doesn't trigger anything**

**Check:**
```javascript
// Video opacity should change
jQuery('.anmi-banner-video').css('opacity'); 
// Before tap: "0"
// After tap: "1"

// If doesn't change:
// → Event handler not attached
```

**Fix:**
1. Check console for JS errors
2. Verify `AnMiVideoBanner` class initialized
3. Check if `isMobile()` returns true

### **Issue 3: Desktop behavior on mobile**

**Check:**
```javascript
// Test mobile detection
/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
// Should return true on mobile

window.innerWidth <= 768;
// Should return true on mobile
```

**Fix:**
1. Clear browser cache
2. Test on real device (not emulator)
3. Check viewport meta tag

---

## ✅ Final Checklist

Before marking as complete:

**Code Quality:**
- [x] ✅ No JavaScript errors
- [x] ✅ No PHP errors
- [x] ✅ Version updated (1.6.11)
- [x] ✅ CHANGELOG documented

**Mobile Testing:**
- [ ] iOS Safari tested
- [ ] Android Chrome tested
- [ ] Direct MP4 works
- [ ] YouTube iframe works
- [ ] Vimeo iframe works
- [ ] Tap to play works
- [ ] Tap to pause works
- [ ] No console errors

**Desktop Testing (Regression):**
- [ ] Hover shows video
- [ ] Click plays video
- [ ] Mouse leave stops video
- [ ] No mobile logic triggers

**Cross-browser:**
- [ ] Safari iOS ≥ 14
- [ ] Chrome Android ≥ 10
- [ ] Firefox Android
- [ ] Samsung Internet

**Edge Cases:**
- [ ] Multiple banners on page
- [ ] Slow network
- [ ] Autoplay blocked
- [ ] Viewport resize

---

## 🚀 Deployment

1. **Upload files:**
   ```
   assets/js/video-banner.js (updated)
   assets/css/video-banner.css (updated)
   includes/admin-panel.php (updated)
   anmi-video-banner.php (version bump)
   ```

2. **Database migration (optional):**
   ```sql
   -- Update existing banners to use video on mobile
   UPDATE wp_anmi_video_banners 
   SET mobile_behavior = 'video' 
   WHERE mobile_behavior = 'image';
   ```

3. **Clear caches:**
   - WordPress object cache
   - Browser cache
   - CDN cache (if any)

4. **Test on staging first:**
   - Upload to staging
   - Test all platforms
   - Verify no regressions
   - Deploy to production

---

**Version:** 1.6.11  
**Status:** ✅ Ready for Testing  
**Priority:** P0 (Critical mobile fix)  
**Estimated Testing Time:** 1-2 hours
