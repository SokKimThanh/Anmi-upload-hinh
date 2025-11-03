# Volume Control Limitations - An Mi Video Banner

## 🔊 Volume Control Feature Overview

Version 1.6.12 đã thêm volume control button cho video banner. **NHƯNG** feature này chỉ hoạt động với **HTML5 video files (MP4)**, KHÔNG hoạt động với **YouTube/Vimeo iframe videos**.

---

## ✅ What Works

### HTML5 Video (MP4 Files)

**Supported:**
```html
<!-- ✅ WORKS: Direct MP4 video file -->
<video src="https://example.com/video.mp4" muted playsinline>
    <source src="video.mp4" type="video/mp4">
</video>
```

**Features:**
- ✅ Volume control button hiển thị
- ✅ Click button → Toggle mute/unmute
- ✅ Icon thay đổi (🔇 ↔️ 🔊)
- ✅ Sound plays/stops correctly
- ✅ Works on desktop & mobile

**Testing:**
```javascript
// JavaScript can access video element directly
const video = document.querySelector('video');
video.muted = false; // ✅ Works!
video.volume = 0.5;  // ✅ Works!
```

---

## ❌ What Doesn't Work

### YouTube/Vimeo Iframe Videos

**Not Supported:**
```html
<!-- ❌ DOESN'T WORK: YouTube iframe -->
<iframe src="https://www.youtube.com/embed/VIDEO_ID"></iframe>

<!-- ❌ DOESN'T WORK: Vimeo iframe -->
<iframe src="https://player.vimeo.com/video/VIDEO_ID"></iframe>
```

**Behavior:**
- ❌ Volume control button **HIDDEN**
- 💡 Use YouTube/Vimeo **native player controls**
- ⚠️ JavaScript **cannot access** iframe internals

**Why it doesn't work:**
```javascript
// Problem: iframe creates isolated environment
const iframe = document.querySelector('iframe');
const video = iframe.contentWindow.document.querySelector('video');
// ❌ SecurityError: Blocked by CORS policy!

// Cannot do:
video.muted = false;  // ❌ Cannot access!
video.volume = 0.5;   // ❌ Cannot access!
```

---

## 🔍 Technical Explanation

### Why YouTube/Vimeo Can't Be Controlled

**1. Cross-Origin Security (CORS):**
```
Your site:        https://anmi.vn
YouTube iframe:   https://www.youtube.com
                  ↑
                  Different origin = CORS blocked
```

**2. Iframe Isolation:**
- Iframe content loaded from **different domain**
- Browser **blocks** JavaScript access (security feature)
- Cannot read/modify iframe's internal elements

**3. Example of Blocked Access:**
```javascript
// Your JavaScript (on anmi.vn)
const iframe = document.querySelector('iframe');

// Try to access video inside YouTube iframe
const youtubeVideo = iframe.contentWindow.document.querySelector('video');
// ❌ DOMException: Blocked a frame with origin "https://anmi.vn" 
//    from accessing a cross-origin frame.

// Try to control volume
youtubeVideo.muted = false;
// ❌ Cannot even reach this line - access denied!
```

---

## 💡 Current Workaround

### For YouTube/Vimeo Videos

**Option 1: Use Native Player Controls**
```php
// Enable YouTube controls in embed URL
$embed_url = 'https://www.youtube.com/embed/VIDEO_ID?controls=1';
//                                                      ↑
//                                                   Show controls
```

**Option 2: Switch to MP4 Video**
```php
// Instead of YouTube URL:
'video_url' => 'https://youtube.com/watch?v=xxx',  // ❌ No volume control

// Use direct MP4:
'video_url' => 'https://anmi.vn/videos/product.mp4',  // ✅ Volume control works!
```

---

## 🚀 Future Enhancement

### Implement YouTube/Vimeo API

**To enable volume control for iframes, we need:**

**1. YouTube IFrame API:**
```javascript
// Load YouTube API
<script src="https://www.youtube.com/iframe_api"></script>

// Create player instance
const player = new YT.Player('player', {
    videoId: 'VIDEO_ID',
    events: {
        'onReady': function(event) {
            // Now we can control volume!
            player.unMute();           // ✅ Works!
            player.setVolume(50);      // ✅ Works!
        }
    }
});
```

**2. Vimeo Player API:**
```javascript
// Load Vimeo API
<script src="https://player.vimeo.com/api/player.js"></script>

// Create player instance
const player = new Vimeo.Player('player', {
    id: VIDEO_ID
});

// Control volume
player.setVolume(0.5);  // ✅ Works!
player.setMuted(false); // ✅ Works!
```

**Implementation Requirements:**
- [ ] Load external API scripts
- [ ] Replace `<iframe>` with API-managed players
- [ ] Handle player lifecycle (ready, play, pause)
- [ ] Sync volume button with API methods
- [ ] Test across browsers/devices
- [ ] Handle API load failures

**Complexity:**
- 🔴 **HIGH** - Requires major refactoring
- ⏱️ ~2-3 days development + testing
- 📊 Increased file size (~50KB per API)
- 🐛 More potential bugs/edge cases

---

## 📋 Summary

| Video Type | Volume Control | Workaround |
|-----------|----------------|------------|
| **HTML5 MP4** | ✅ **WORKS** | Use directly |
| **YouTube iframe** | ❌ **HIDDEN** | Use native controls or API |
| **Vimeo iframe** | ❌ **HIDDEN** | Use native controls or API |

### Current Implementation (v1.6.12)

```javascript
// Detect video type
const video = this.$video[0];
const isIframe = video && video.tagName === 'IFRAME';

if (isIframe) {
    // Hide volume control for YouTube/Vimeo
    this.$volumeControl.hide();
    console.log('Volume control disabled for iframe video');
} else {
    // Enable volume control for HTML5 video
    this.$volumeControl.on('click', function() {
        video.muted = !video.muted;  // ✅ Works!
    });
}
```

### Recommended Usage

**For products with important audio (voice-over, music):**
```php
[anmi_video_banner 
    video_url="https://anmi.vn/videos/product-demo.mp4"
    ↑ Use MP4 for volume control
]
```

**For background ambiance (no audio needed):**
```php
[anmi_video_banner 
    video_url="https://youtube.com/watch?v=xxx"
    ↑ YouTube OK - no audio anyway
]
```

---

## 🎯 Testing Checklist

**HTML5 Video (MP4):**
- [x] ✅ Volume button appears when video plays
- [x] ✅ Click button → Video unmutes
- [x] ✅ Icon changes to 🔊
- [x] ✅ Sound actually plays
- [x] ✅ Click again → Video mutes
- [x] ✅ Icon changes to 🔇

**YouTube/Vimeo:**
- [x] ✅ Volume button hidden
- [x] ✅ No console errors
- [x] ✅ Native controls available (if enabled)

---

## 📞 Questions?

Nếu bạn:
- ✅ Dùng **MP4 video** → Volume control hoạt động tốt
- ❌ Dùng **YouTube/Vimeo** → Volume button sẽ ẩn, dùng native controls

Để enable volume control cho YouTube/Vimeo → Cần implement API (future update).
