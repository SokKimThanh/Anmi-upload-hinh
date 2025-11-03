# 🔊 Hướng Dẫn Debug Volume Control - Từng Bước

## 📋 Tổng Quan

File này hướng dẫn chi tiết cách kiểm tra và khắc phục lỗi **video không phát âm thanh** theo các bước bạn đã đề xuất.

---

## 🔍 Bước 1: Kiểm tra phần tử video trong DOM

### ❓ Vấn Đề

Video có thuộc tính `muted` → Âm thanh bị tắt

### ✅ Cách Kiểm Tra

**Mở Browser DevTools (F12):**

```javascript
// 1. Tìm video element
const video = document.querySelector('.anmi-banner-video');
console.log('Video element:', video);

// 2. Kiểm tra thuộc tính muted
console.log('Is muted:', video.muted);
// Output: true = muted, false = có âm thanh

// 3. Kiểm tra HTML attribute
console.log('Has muted attribute:', video.hasAttribute('muted'));
```

**Kiểm tra trong HTML:**

```html
<!-- ❌ BAD: Video bị muted -->
<video muted>
    <source src="video.mp4" type="video/mp4">
</video>

<!-- ✅ GOOD: Video có âm thanh -->
<video>
    <source src="video.mp4" type="video/mp4">
</video>
```

### 💡 Cách Khắc Phục

**Option 1: Xóa attribute `muted` khỏi HTML**

```html
<!-- Trước -->
<video muted playsinline>

<!-- Sau -->
<video playsinline>
```

**Option 2: Set `muted = false` qua JavaScript**

```javascript
const video = document.querySelector('.anmi-banner-video');
video.muted = false;
console.log('Unmuted! Now:', video.muted); // false
```

**Option 3: Unmute khi user click play**

```javascript
video.addEventListener('play', function() {
    video.muted = false;
    console.log('Video playing with sound!');
});
```

---

## ⚡ Bước 2: Kiểm tra thuộc tính volume và autoplay

### ❓ Vấn Đề

Trình duyệt **tắt âm thanh tự động** nếu video có `autoplay` (theo chính sách autoplay policy)

### ✅ Cách Kiểm Tra

```javascript
const video = document.querySelector('.anmi-banner-video');

// 1. Kiểm tra autoplay attribute
console.log('Has autoplay:', video.hasAttribute('autoplay'));

// 2. Kiểm tra volume level
console.log('Volume level:', video.volume);
// Output: 0.0 to 1.0 (0 = silent, 1 = max)

// 3. Kiểm tra cả muted và volume
console.log('Muted:', video.muted, '| Volume:', video.volume);
```

**Kiểm tra HTML:**

```html
<!-- ❌ BAD: Autoplay buộc phải muted -->
<video autoplay muted>
    <source src="video.mp4" type="video/mp4">
</video>

<!-- ✅ GOOD: Không autoplay, có thể có âm thanh -->
<video>
    <source src="video.mp4" type="video/mp4">
</video>
```

### 💡 Cách Khắc Phục

**Browser Autoplay Policy:**

| Browser | Policy |
|---------|--------|
| Chrome | `autoplay` → Must be `muted` |
| Firefox | `autoplay` → Must be `muted` |
| Safari | `autoplay` → Must be `muted` |
| Edge | `autoplay` → Must be `muted` |

**Solution 1: Bỏ autoplay, dùng user interaction**

```javascript
// User click vào banner
container.addEventListener('click', function() {
    video.muted = false;  // Unmute
    video.volume = 1.0;    // Max volume
    video.play();          // Play với âm thanh
});
```

**Solution 2: Autoplay muted → User unmute sau**

```javascript
// Video autoplay (muted)
video.autoplay = true;
video.muted = true;
video.play();

// User click volume button
volumeButton.addEventListener('click', function() {
    video.muted = false;  // NOW has sound!
});
```

**Solution 3: Kiểm tra autoplay support**

```javascript
video.play().then(() => {
    console.log('✅ Autoplay works!');
}).catch(error => {
    console.warn('❌ Autoplay blocked:', error);
    // Show play button for user
    showPlayButton();
});
```

---

## 🎨 Bước 3: Kiểm tra CSS có ảnh hưởng không

### ❓ Vấn Đề

CSS `display: none` hoặc `visibility: hidden` có thể ảnh hưởng playback

### ✅ Cách Kiểm Tra

```javascript
const video = document.querySelector('.anmi-banner-video');

// 1. Kiểm tra display
const display = window.getComputedStyle(video).display;
console.log('Display:', display);
// ❌ "none" = hidden, video không play đúng

// 2. Kiểm tra visibility
const visibility = window.getComputedStyle(video).visibility;
console.log('Visibility:', visibility);
// ❌ "hidden" = invisible, có thể ảnh hưởng

// 3. Kiểm tra opacity
const opacity = window.getComputedStyle(video).opacity;
console.log('Opacity:', opacity);
// "0" = transparent, nhưng vẫn play OK

// 4. Kiểm tra parent container
const parent = video.parentElement;
const parentDisplay = window.getComputedStyle(parent).display;
console.log('Parent display:', parentDisplay);
```

### 💡 Cách Khắc Phục

**Fix CSS hiding:**

```css
/* ❌ BAD: Video bị ẩn hoàn toàn */
.anmi-banner-video {
    display: none;
}

/* ✅ GOOD: Video ẩn nhưng vẫn play */
.anmi-banner-video {
    opacity: 0;
    pointer-events: none;
}

/* ✅ BETTER: Position off-screen */
.anmi-banner-video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.3s;
}

/* Show khi hover */
.anmi-video-banner-container:hover .anmi-banner-video {
    opacity: 1;
}
```

---

## 💻 Bước 4: Kiểm tra JavaScript

### ❓ Vấn Đề

JavaScript code có thể đang:
- Set `video.muted = true`
- Override volume
- Block sound playback

### ✅ Cách Kiểm Tra

**Mở Console và test:**

```javascript
// 1. Tìm video
const video = document.querySelector('.anmi-banner-video');

// 2. Force unmute
video.muted = false;
video.volume = 1.0;

// 3. Play
video.play().then(() => {
    console.log('✅ Playing with sound!');
    console.log('Muted:', video.muted);
    console.log('Volume:', video.volume);
}).catch(err => {
    console.error('❌ Failed:', err);
});

// 4. Kiểm tra event listeners
getEventListeners(video);
// Shows all event listeners attached to video
```

**Kiểm tra code có mute video không:**

```javascript
// ❌ BAD: Code này mute video
video.addEventListener('play', function() {
    video.muted = true;  // ← Đây là nguyên nhân!
});

// ✅ GOOD: Giữ unmuted state
video.addEventListener('play', function() {
    // Không touch vào muted property
    console.log('Playing...');
});
```

### 💡 Cách Khắc Phục

**Tìm và fix code đang mute:**

```bash
# Search trong project
grep -r "video.muted = true" .
grep -r ".muted = true" .
```

**Override trong console để test:**

```javascript
// Backup original muted setter
const originalMutedSetter = Object.getOwnPropertyDescriptor(
    HTMLMediaElement.prototype, 
    'muted'
).set;

// Override để log khi nào muted được set
Object.defineProperty(HTMLMediaElement.prototype, 'muted', {
    set: function(value) {
        console.trace('🔍 video.muted set to:', value);
        originalMutedSetter.call(this, value);
    },
    get: function() {
        return this._muted;
    }
});
```

---

## 🔊 Bước 5: Kiểm tra thiết bị và trình duyệt

### ❓ Vấn Đề

- Loa/tai nghe bị tắt
- Volume hệ thống = 0
- Browser tab bị mute
- Codec không support

### ✅ Cách Kiểm Tra

**1. System Volume:**

```javascript
// Không thể check system volume qua JS
// Phải check manual:
// - Windows: Click speaker icon → Check volume slider
// - Mac: Check menu bar volume icon
// - Linux: Check sound settings
```

**2. Browser Tab Muted:**

```javascript
// Chrome: Right-click tab
// - If shows "Unmute tab" → Tab is muted!
// - Click "Unmute tab"

// Check qua JS (limited):
console.log('Page visible:', !document.hidden);
console.log('Page has focus:', document.hasFocus());
```

**3. Audio Codec Support:**

```javascript
const video = document.createElement('video');

// Check supported formats
const formats = [
    { type: 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"', name: 'MP4 H.264 + AAC' },
    { type: 'video/webm; codecs="vp8, vorbis"', name: 'WebM VP8' },
    { type: 'video/ogg; codecs="theora, vorbis"', name: 'Ogg Theora' }
];

console.log('📊 Supported formats:');
formats.forEach(format => {
    const support = video.canPlayType(format.type);
    console.log(`${format.name}: ${support || 'No'}`);
    // "probably" = best support
    // "maybe" = might work
    // "" = not supported
});
```

**4. Audio Context (Web Audio API):**

```javascript
// Check if audio is allowed
const AudioContext = window.AudioContext || window.webkitAudioContext;
const ctx = new AudioContext();

console.log('Audio context state:', ctx.state);
// "running" = OK
// "suspended" = Need user interaction

// Resume if suspended
if (ctx.state === 'suspended') {
    ctx.resume().then(() => {
        console.log('✅ Audio context resumed');
    });
}
```

### 💡 Cách Khắc Phục

**Fix Browser Tab Mute:**

```
Chrome/Edge:
1. Right-click on tab
2. Click "Unmute site"
3. Reload page

Firefox:
1. Click speaker icon in address bar
2. Click "Unmute"
3. Reload page
```

**Fix System Volume:**

```
Windows:
1. Click speaker icon in system tray
2. Drag volume slider up
3. Check "Mute" is not enabled

Mac:
1. Click volume icon in menu bar
2. Increase volume
3. Check "Mute" is OFF

Linux:
1. Open sound settings
2. Check output device
3. Increase volume
```

**Test with Different Browser:**

```
Try these browsers in order:
1. Chrome/Edge (Chromium) - Best support
2. Firefox - Good support
3. Safari - Mac only, good support
4. Opera - Chromium-based
```

---

## 🧪 Sử Dụng Test Page

### Mở Test Page

```bash
# 1. Navigate to test page
file:///E:/ANMI_Dự%20Án%20bảo%20trì%20phần%20mềm%20website%20AnMi/Anmi-upload-hinh/HTML-baiviet/Sản%20phẩm/Hệ%20thống%20gá%20kẹp,%20cán%20dao/plugins/anmi-video-banner/VOLUME-DEBUG-TEST.html

# 2. Or run local server
cd "plugins/anmi-video-banner"
python -m http.server 8000
# Open: http://localhost:8000/VOLUME-DEBUG-TEST.html
```

### Test Cases

**Test 1: Basic Video**
1. Click "▶ Play Video"
2. Volume button should appear
3. Click "🔊 Toggle Mute"
4. **Should hear sound!** 🔊

**Test 2: Autoplay Video**
1. Video auto-plays (muted)
2. Click "🔊 Toggle Mute"
3. **Should hear sound!** 🔊

**Test 3: Console Commands**
1. Click "🔧 Force Unmute Test 1"
2. Check console log
3. **Should hear sound!** 🔊

### Debug Commands

```javascript
// Run in browser console:

// 1. Check video state
const video = document.getElementById('video1');
console.log({
    muted: video.muted,
    volume: video.volume,
    paused: video.paused,
    duration: video.duration,
    currentTime: video.currentTime
});

// 2. Force unmute
video.muted = false;
video.volume = 1.0;

// 3. Play
video.play();

// 4. Listen to events
video.addEventListener('volumechange', (e) => {
    console.log('Volume changed:', video.volume, 'Muted:', video.muted);
});
```

---

## ✅ Checklist Tổng Hợp

### Trước Khi Test

- [ ] Clear browser cache (`Ctrl+Shift+R`)
- [ ] Check system volume > 0
- [ ] Check browser tab not muted
- [ ] Check speakers/headphones connected
- [ ] Open browser DevTools (F12)

### Trong Khi Test

- [ ] Video element exists in DOM
- [ ] Video has `<source>` tag with MP4
- [ ] No `muted` attribute in HTML
- [ ] `video.muted === false`
- [ ] `video.volume > 0`
- [ ] Video is playing (not paused)
- [ ] No CSS hiding video (`display: none`)
- [ ] No JavaScript setting `muted = true`

### Sau Khi Test

- [ ] Volume button visible
- [ ] Volume button clickable
- [ ] Clicking toggles mute/unmute
- [ ] Icon changes correctly
- [ ] **ACTUALLY HEAR SOUND** 🔊

---

## 🎯 Quick Solutions

### Giải Pháp Nhanh Nhất

```javascript
// Copy-paste vào console:
(function() {
    const video = document.querySelector('.anmi-banner-video');
    if (!video) {
        console.error('❌ Video not found!');
        return;
    }
    
    // Force unmute
    video.muted = false;
    video.volume = 1.0;
    
    // Play if not playing
    if (video.paused) {
        video.play().then(() => {
            console.log('✅ Video playing with sound!');
        }).catch(err => {
            console.error('❌ Play failed:', err.message);
        });
    } else {
        console.log('✅ Video already playing - sound enabled!');
    }
    
    // Log state
    console.log('State:', {
        muted: video.muted,
        volume: video.volume,
        playing: !video.paused
    });
})();
```

---

## 📞 Hỗ Trợ

Nếu sau tất cả các bước trên vẫn không có âm thanh:

1. ✅ Check video file có audio track không (mở bằng VLC)
2. ✅ Test với video khác (sample video từ internet)
3. ✅ Test trên browser khác
4. ✅ Test trên máy khác
5. ✅ Check VOLUME-CONTROL-TESTING.md (comprehensive guide)

**Sample Test Video:**
```
https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4
```

---

**Last Updated:** November 3, 2025  
**Plugin Version:** 1.6.12b  
**Purpose:** Debug volume control issues step-by-step
