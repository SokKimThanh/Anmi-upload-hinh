# YouTube & Vimeo Support - v1.4.0

## 🎉 Tính năng mới

Plugin **An Mi Video Banner** hiện hỗ trợ đầy đủ 3 loại video:

### ✅ Các định dạng được hỗ trợ:

1. **YouTube** - `https://www.youtube.com/watch?v=...` hoặc `https://youtu.be/...`
2. **Vimeo** - `https://vimeo.com/...`
3. **Direct Video (MP4)** - Link trực tiếp file `.mp4`

---

## 🔧 Cách sử dụng

### Ví dụ với YouTube:

```html
<!-- URL thông thường -->
https://www.youtube.com/watch?v=egbA1RHO8MY

<!-- URL rút gọn -->
https://youtu.be/egbA1RHO8MY?si=9KqRKfT78y1Bm2us

<!-- Cả hai đều hoạt động! -->
```

### Ví dụ với Vimeo:

```html
https://vimeo.com/123456789
```

### Ví dụ với MP4:

```html
https://example.com/videos/banner-video.mp4
```

---

## 🎯 Cách plugin xử lý

### Auto-Detection Logic:

```php
// Plugin tự động nhận diện loại video
parse_video_url($url) {
    // 1. Check YouTube pattern
    if (youtube.com || youtu.be) → YouTube iframe
    
    // 2. Check Vimeo pattern  
    if (vimeo.com) → Vimeo iframe
    
    // 3. Default
    else → Direct video tag
}
```

### Render khác nhau theo loại:

#### YouTube/Vimeo → `<iframe>`
```html
<iframe src="https://www.youtube.com/embed/VIDEO_ID?autoplay=1&mute=1&loop=1&controls=0">
```

**Lợi ích:**
- ✅ **Không cần tải video** (YouTube/Vimeo CDN)
- ✅ **Hiển thị ngay lập tức** (không buffering)
- ✅ **Tiết kiệm băng thông** server
- ✅ **Auto quality** (YouTube tự chọn quality phù hợp)

#### Direct MP4 → `<video>`
```html
<video preload="metadata" poster="...">
    <source src="video.mp4">
</video>
```

**Lợi ích:**
- ✅ **Toàn quyền kiểm soát**
- ✅ **Không phụ thuộc** dịch vụ bên thứ 3
- ✅ **SEO friendly** hơn

---

## 🚀 Cải tiến kỹ thuật

### 1. **Regex Pattern Matching**

```php
// YouTube detection
preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $match)

// Vimeo detection
preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)(?:$|\/|\?)/i', $url, $match)
```

**Hỗ trợ các format:**
- ✅ `youtube.com/watch?v=VIDEO_ID`
- ✅ `youtube.com/embed/VIDEO_ID`
- ✅ `youtu.be/VIDEO_ID`
- ✅ `youtu.be/VIDEO_ID?si=...`
- ✅ `vimeo.com/VIDEO_ID`
- ✅ `vimeo.com/channels/...`

### 2. **Embed URL với tối ưu params**

#### YouTube Embed:
```
https://www.youtube.com/embed/{VIDEO_ID}?
  autoplay=1          // Tự phát khi hiện
  &mute=1             // Bắt buộc muted (browser policy)
  &loop=1             // Lặp vô hạn
  &playlist={VIDEO_ID} // Cần cho loop
  &controls=0         // Ẩn controls
  &showinfo=0         // Ẩn title
  &rel=0              // Không hiện related videos
  &modestbranding=1   // Ẩn logo YouTube
  &playsinline=1      // iOS inline play
```

#### Vimeo Embed:
```
https://player.vimeo.com/video/{VIDEO_ID}?
  autoplay=1
  &muted=1
  &loop=1
  &background=1       // Background mode
  &controls=0
```

### 3. **CSS Responsive iframe**

```css
/* Scale iframe to cover container như video */
.anmi-banner-iframe {
    width: 100%;
    height: 100%;
    pointer-events: none; /* Không bị click can thiệp */
}

/* Tỷ lệ 16:9 - scale chiều cao */
@media (min-aspect-ratio: 16/9) {
    .anmi-banner-iframe {
        height: 300%;
        top: -100%;
    }
}

/* Tỷ lệ 4:3 - scale chiều rộng */
@media (max-aspect-ratio: 16/9) {
    .anmi-banner-iframe {
        width: 300%;
        left: -100%;
    }
}
```

### 4. **JavaScript xử lý khác biệt**

```javascript
// Init - Detect video type
this.videoType = this.$container.data('video-type'); // 'youtube' | 'vimeo' | 'direct'

// YouTube/Vimeo: Sẵn sàng ngay (không cần preload)
if (this.videoType === 'youtube' || this.videoType === 'vimeo') {
    this.isVideoReady = true;
    this.$loader.removeClass('active'); // Tắt spinner ngay
}

// Play video
playVideo() {
    if (this.videoType === 'youtube' || this.videoType === 'vimeo') {
        this.$video.css('opacity', '1'); // Chỉ cần show iframe
        this.$slider.css('opacity', '0'); // Ẩn slider
    } else {
        video.play(); // Play MP4 tag
    }
}
```

---

## 📊 So sánh hiệu suất

| Metric | YouTube/Vimeo | Direct MP4 |
|--------|---------------|------------|
| **Load time** | 0-1s (instant) | 1-3s (metadata) |
| **Bandwidth** | 0 (CDN) | ~500KB-5MB |
| **Quality** | Auto adapt | Fixed |
| **Buffering** | Rare (CDN) | Possible |
| **Controls** | Hidden via params | Native |
| **SEO** | Moderate | Better |
| **Privacy** | External | Private |

---

## 🎨 User Experience

### Timeline với YouTube:

```
0s:   User vào trang
      ├─ Slider hiển thị ngay
      ├─ YouTube iframe load trong background
      └─ KHÔNG CÓ SPINNER (instant ready)

1s:   User hover
      ├─ Slider fade out
      ├─ YouTube iframe fade in
      └─ Video tự phát ngay (autoplay=1)

Exit: User mouse leave
      ├─ YouTube iframe fade out
      └─ Slider fade in trở lại
```

### So với MP4:

```
0s:   User vào trang
      ├─ Slider hiển thị
      ├─ Video load metadata
      └─ Spinner (tối đa 3s)

3s:   Video ready
      └─ Spinner tắt

5s:   User hover
      ├─ Play MP4
      └─ Có thể buffer 0.5-1s
```

**→ YouTube/Vimeo nhanh hơn và mượt hơn!**

---

## 🧪 Test Cases

### Test 1: YouTube URL thông thường
```
Input: https://www.youtube.com/watch?v=egbA1RHO8MY
Expected: 
  ✅ Detect type: "youtube"
  ✅ Extract ID: "egbA1RHO8MY"
  ✅ Render iframe với autoplay
  ✅ Video hiển thị ngay khi hover
```

### Test 2: YouTube URL rút gọn
```
Input: https://youtu.be/egbA1RHO8MY?si=9KqRKfT78y1Bm2us
Expected:
  ✅ Detect type: "youtube"
  ✅ Extract ID: "egbA1RHO8MY" (bỏ query params)
  ✅ Hoạt động giống URL thông thường
```

### Test 3: Vimeo URL
```
Input: https://vimeo.com/123456789
Expected:
  ✅ Detect type: "vimeo"
  ✅ Extract ID: "123456789"
  ✅ Render Vimeo player với background mode
```

### Test 4: Direct MP4 (fallback)
```
Input: https://example.com/video.mp4
Expected:
  ✅ Detect type: "direct"
  ✅ Render <video> tag
  ✅ Preload metadata + poster
```

---

## 🔍 Debugging

### Console logs để kiểm tra:

```javascript
// Khi init
console.log('Video type:', this.videoType); 
// → "youtube" | "vimeo" | "direct"

// Khi hover (YouTube/Vimeo)
console.log('Showing iframe video:', this.videoType);
// → "Showing iframe video: youtube"

// Khi mouse leave
console.log('Hiding iframe video');
// → iframe fades out
```

### Inspect element:

```html
<!-- YouTube render -->
<div class="anmi-video-banner-container" data-video-type="youtube">
    <iframe class="anmi-banner-video anmi-banner-iframe" 
            src="https://www.youtube.com/embed/egbA1RHO8MY?autoplay=1&mute=1...">
    </iframe>
    ...
</div>

<!-- MP4 render -->
<div class="anmi-video-banner-container" data-video-type="direct">
    <video class="anmi-banner-video" preload="metadata" poster="...">
        <source src="video.mp4">
    </video>
    ...
</div>
```

---

## ⚠️ Lưu ý quan trọng

### 1. **Autoplay Policy**
- YouTube/Vimeo iframe **phải muted** mới autoplay được (browser policy)
- Plugin tự động set `mute=1` trong embed URL

### 2. **Loop YouTube**
- Cần param `&playlist={VIDEO_ID}` để loop hoạt động
- Plugin tự động thêm param này

### 3. **Privacy YouTube**
- Có thể dùng `youtube-nocookie.com` nếu cần:
```php
// Tùy chọn trong future version
$result['embed_url'] = 'https://www.youtube-nocookie.com/embed/' . $match[1];
```

### 4. **Vimeo Background Mode**
- Param `background=1` ẩn hết controls và logo
- Tương đương "Vimeo Background Video" feature

### 5. **iframe pointer-events**
- CSS set `pointer-events: none` để click không bị iframe chặn
- User vẫn click được buttons/links overlay

---

## 📝 Admin Panel Update

Trong admin form, giờ có thể paste bất kỳ URL nào:

```
Video URL: [https://youtu.be/egbA1RHO8MY?si=...]
           ↓
           Plugin tự động detect và xử lý đúng!
```

**Không cần chọn loại** - Plugin tự nhận diện thông minh!

---

## 🚀 Future Improvements (Optional)

### 1. **YouTube Playlist Support**
```php
// Detect playlist
if (preg_match('/[?&]list=([^&]+)/', $url, $match)) {
    $playlist_id = $match[1];
}
```

### 2. **Video Quality Selector**
```php
// Admin option: Auto, 720p, 1080p
$quality = get_option('anmi_vb_youtube_quality', 'auto');
```

### 3. **Custom Thumbnail**
```php
// Override YouTube thumbnail
if ($custom_poster) {
    // Use custom image instead of YouTube thumb
}
```

### 4. **Analytics Integration**
```javascript
// Track video plays
gtag('event', 'video_play', {
    'video_type': 'youtube',
    'video_id': 'egbA1RHO8MY'
});
```

---

## 📦 Files Changed in v1.4.0

```
✏️ anmi-video-banner.php
   - Added parse_video_url() method
   - Updated render_video_banner() to support iframe
   - Added data-video-type attribute

✏️ assets/css/video-banner.css
   - Added .anmi-banner-iframe styles
   - Added responsive scaling media queries

✏️ assets/js/video-banner.js
   - Added videoType detection
   - Updated init() to skip preload for YouTube/Vimeo
   - Updated playVideo() and pauseVideo() for iframe
```

---

## ✨ Benefits Summary

### Cho User:
- ✅ **Load nhanh hơn** (không tải video về)
- ✅ **Không buffering** (YouTube CDN)
- ✅ **Tiết kiệm data** (mobile friendly)
- ✅ **Quality tự động** adapt theo mạng

### Cho Admin:
- ✅ **Dễ dùng hơn** (paste link YouTube là xong)
- ✅ **Tiết kiệm hosting** (không upload video lên server)
- ✅ **Quản lý video** trên YouTube (edit video không cần re-upload)

### Cho Developer:
- ✅ **Code clean** (tách biệt logic cho từng loại)
- ✅ **Dễ maintain** (regex pattern rõ ràng)
- ✅ **Extensible** (dễ thêm platform khác: Dailymotion, TikTok...)

---

**Version:** 1.4.0  
**Release Date:** November 3, 2025  
**Author:** An Mi Tools Technical Team  

🎉 **Giờ đây bạn có thể dùng YouTube URL trực tiếp!**
