# Volume Control Testing Guide

## 🔍 Quick Diagnostic - Why Volume Button Not Showing?

### ✅ Checklist Before Testing

**1. Video Type Check:**
```
✅ Using MP4 video file? → Volume control WILL show
❌ Using YouTube URL? → Volume control WON'T show
❌ Using Vimeo URL? → Volume control WON'T show
```

**2. Video State Check:**
```
Volume button only shows when:
✅ Video is PLAYING (after user clicks play button)
❌ Video is STOPPED (hidden)
❌ Video is PAUSED (hidden)
```

**3. Browser Cache Check:**
```
Clear cache and reload:
- Chrome: Ctrl+Shift+R (Windows) / Cmd+Shift+R (Mac)
- Firefox: Ctrl+F5
- Safari: Cmd+Option+R
```

---

## 🧪 Testing Steps

### Test 1: Production Page (Frontend)

**Setup:**
```php
[anmi_video_banner 
    video_url="https://your-site.com/video.mp4"  // Must be MP4!
    images="image1.jpg,image2.jpg"
]
```

**Steps:**
1. Visit page with banner
2. **Desktop:** Hover over banner → Video appears
3. **Click** play button (large circle in center)
4. **Wait** for video to start playing
5. **Look** bottom-right corner → Volume button should appear
6. **Click** volume button → Should hear sound

**Expected Result:**
- ✅ Volume button appears when video plays
- ✅ Button positioned bottom-right corner
- ✅ Clicking toggles mute/unmute
- ✅ Icon changes (🔇 ↔ 🔊)

---

### Test 2: Admin Preview Modal

**Steps:**
1. Go to WordPress Admin → **Video Banners**
2. Find any banner with **MP4 video**
3. Click **Preview** button
4. Modal opens with 2 sections:

**Section A: Preview Video (Top):**
- Video auto-plays (muted)
- Volume button should appear immediately
- Click volume → Test sound

**Section B: Production Demo (Bottom):**
- Hover banner → Video appears
- Click play button
- Volume button appears
- Click volume → Test sound

**Expected Result:**
- ✅ Both sections show volume button (MP4 only)
- ✅ YouTube/Vimeo: Button hidden, no errors
- ✅ Console: No JavaScript errors

---

### Test 3: Edit Page Preview

**Steps:**
1. Go to **Video Banners** → **Edit** any banner
2. Scroll to **"👁️ Xem Trước Trực Tiếp"** section
3. Make sure Video URL is **MP4** file
4. Hover preview area
5. Click play button
6. Volume button should appear bottom-right

**Expected Result:**
- ✅ Volume button appears when playing
- ✅ Click toggles mute/unmute
- ✅ Real-time testing works

---

## 🐛 Troubleshooting

### Problem 1: Volume Button Never Shows

**Possible Causes:**

**A. Using YouTube/Vimeo (Expected Behavior):**
```javascript
// Check console for:
"Volume control disabled for iframe video (YouTube/Vimeo)"
```
**Solution:** Switch to MP4 video to test volume control.

**B. JavaScript Not Loaded:**
```javascript
// Open browser console (F12)
// Check for errors like:
"AnMiVideoBanner is not defined"
```
**Solution:** Clear cache and reload page.

**C. Element Not Found:**
```javascript
// Check console for:
"Volume control element not found"
```
**Solution:** Check HTML - button should exist with class `anmi-volume-control`.

---

### Problem 2: Button Shows But No Sound

**Possible Causes:**

**A. Video Has No Audio Track:**
```
Some MP4 files are video-only (no sound)
```
**Solution:** 
- Open video in VLC Media Player
- Check: View → Codec Information → Audio Codec
- If "No audio": Video file has no sound track

**B. Browser Sound Muted:**
```
Check browser tab icon for mute symbol 🔇
```
**Solution:** Right-click tab → Unmute site

**C. System Volume Zero:**
```
Computer sound is muted or volume at 0%
```
**Solution:** Check system volume settings

---

### Problem 3: Button Visible But Can't Click

**Possible Causes:**

**A. Z-index Issue:**
```css
/* Check if something covers button */
/* Volume button should have z-index: 20 */
```
**Solution:** Inspect element, check z-index value.

**B. Pointer Events Blocked:**
```css
/* Parent might have pointer-events: none */
```
**Solution:** Check parent containers for pointer-events.

---

## 🔬 Developer Debug

### Check Volume Button CSS

**Open Browser DevTools (F12):**

```css
/* Expected CSS for .anmi-volume-control */
.anmi-volume-control {
    position: absolute;
    bottom: 20px;
    right: 20px;
    z-index: 20;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(0,0,0,0.6);
    border: 2px solid rgba(255,255,255,0.8);
    cursor: pointer;
    display: none; /* Changed to block by jQuery when video plays */
    transition: all 0.3s ease;
    opacity: 0.8;
}
```

### Check JavaScript Execution

**Console Commands:**

```javascript
// 1. Check if AnMiVideoBanner class exists
typeof AnMiVideoBanner
// Should return: "function"

// 2. Check volume button exists
$('.anmi-volume-control').length
// Should return: 1 (or more if multiple banners)

// 3. Check button display state
$('.anmi-volume-control').css('display')
// When playing: "block"
// When stopped: "none"

// 4. Check if video is iframe
$('.anmi-banner-video')[0].tagName
// MP4: "VIDEO"
// YouTube/Vimeo: "IFRAME"

// 5. Force show button (for testing)
$('.anmi-volume-control').fadeIn(300)
// Button should appear
```

### Check Video Element

```javascript
// Get video element
var video = $('.anmi-banner-video')[0];

// Check if it's HTML5 video
console.log('Tag:', video.tagName);
// Expected: "VIDEO" (MP4) or "IFRAME" (YouTube/Vimeo)

// For HTML5 video, check muted state
if (video.tagName === 'VIDEO') {
    console.log('Muted:', video.muted);  // true = muted
    console.log('Volume:', video.volume); // 0.0 to 1.0
    
    // Test unmute
    video.muted = false;
    console.log('Unmuted! Now playing with sound.');
}
```

---

## 📊 Expected Behavior Summary

### MP4 Video (HTML5 `<video>`)

| State | Volume Button | Sound | Icon |
|-------|---------------|-------|------|
| **Not playing** | Hidden | N/A | N/A |
| **Playing (default)** | Visible | Muted | 🔇 |
| **After clicking button** | Visible | Unmuted | 🔊 |
| **Click again** | Visible | Muted | 🔇 |
| **Video stops** | Hidden | Reset to muted | N/A |

### YouTube/Vimeo (iframe)

| State | Volume Button | Reason |
|-------|---------------|--------|
| **Always** | Hidden | CORS restriction - cannot control |
| **Workaround** | Use platform's native controls | Click gear icon |

---

## ✅ Success Criteria

**Volume control working correctly if:**

1. ✅ Button appears when MP4 video plays
2. ✅ Button hidden when video stops
3. ✅ Button hidden for YouTube/Vimeo (no errors)
4. ✅ Clicking toggles mute/unmute
5. ✅ Icon changes appropriately
6. ✅ Sound actually plays when unmuted
7. ✅ No console errors
8. ✅ Works in all contexts (production, modal, edit preview)

---

## 🚨 Known Issues

### Issue 1: Volume Button Invisible (Fixed in v1.6.12b)

**Problem:** Inline styles `display: none` couldn't be overridden.

**Solution:** Moved all styling to CSS file.

**Fixed in commit:** `b981b45`

### Issue 2: YouTube/Vimeo No Volume Control

**Problem:** Cannot control iframe video via JavaScript.

**Status:** **Expected behavior** - requires YouTube/Vimeo API integration.

**Workaround:** Use platform's native controls.

**Future:** Implement YouTube IFrame API / Vimeo Player API.

---

## 📞 Support

**If volume control still not working:**

1. Check **VOLUME-CONTROL-LIMITATIONS.md** for detailed explanation
2. Verify video is **MP4 file** (not YouTube/Vimeo)
3. Clear browser cache completely
4. Check browser console for errors
5. Test with simple MP4 file (known working video)

**Test Video URLs:**
```
Sample MP4 (public):
https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4

Or use your own MP4 hosted on same domain.
```

---

## 🎯 Quick Fix Commands

**If button not showing, try these in browser console:**

```javascript
// Force show volume button
$('.anmi-volume-control').show();

// Check video type
console.log($('.anmi-banner-video')[0].tagName);

// Manually unmute (if video playing)
var video = $('.anmi-banner-video')[0];
if (video.tagName === 'VIDEO') {
    video.muted = false;
    console.log('Unmuted!');
}

// Re-initialize AnMiVideoBanner
var container = $('.anmi-video-banner-container')[0];
new AnMiVideoBanner(container);
```

---

**Last Updated:** November 3, 2025  
**Plugin Version:** 1.6.12b  
**Feature:** Volume Control for MP4 Videos
