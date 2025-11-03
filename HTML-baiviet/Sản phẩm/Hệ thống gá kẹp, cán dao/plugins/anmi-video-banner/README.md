# AN MI VIDEO BANNER PLUGIN

**Version:** 1.0.0  
**Author:** An Mi Tools Technical Team  
**License:** GPL v2 or later

## 📖 Mô tả

Plugin tạo video banner với hiệu ứng hover transition chuyên nghiệp - tự động chuyển từ ảnh tĩnh sang video khi người dùng rê chuột vào.

### ✨ Tính năng chính:

- ✅ **Hover to Play**: Video tự động phát khi hover, dừng khi rời chuột
- ✅ **4 Transition Effects**: Fade, Zoom, Blur, Slide
- ✅ **Responsive Design**: Tự động tối ưu cho mobile, tablet, desktop
- ✅ **Elementor Widget**: Drag & drop dễ dàng trong Elementor
- ✅ **Shortcode Support**: Sử dụng ở bất kỳ đâu với shortcode
- ✅ **Mobile Optimization**: Tùy chọn hiển thị video hoặc ảnh trên mobile
- ✅ **Performance**: Preload video, lazy loading, optimized code
- ✅ **Customizable**: Tùy chỉnh màu sắc, typography, spacing

---

## 📥 Cài đặt

### Cách 1: Upload Plugin

1. Download folder `anmi-video-banner`
2. Upload vào `/wp-content/plugins/`
3. Vào **WordPress Admin** → **Plugins** → **Installed Plugins**
4. Tìm "An Mi Video Banner" và click **Activate**

### Cách 2: Upload qua Admin

1. Nén folder `anmi-video-banner` thành file `.zip`
2. Vào **WordPress Admin** → **Plugins** → **Add New** → **Upload Plugin**
3. Chọn file `.zip` và click **Install Now**
4. Click **Activate Plugin**

---

## 🚀 Sử dụng

### 1️⃣ Sử dụng với Elementor (Khuyến nghị)

1. Mở trang với **Elementor Editor**
2. Tìm widget **"An Mi Video Banner"** trong panel (icon camera)
3. Kéo thả widget vào vị trí mong muốn
4. Cấu hình trong panel:

#### **Tab Content:**

**Video & Image:**
- **Video URL**: Upload video .mp4 (khuyến nghị < 5MB)
- **Image Overlay**: Upload ảnh đại diện (sẽ hiện ban đầu)

**Content:**
- **Title**: Tiêu đề chính
- **Subtitle**: Mô tả phụ
- **Button Text**: Text nút CTA
- **Button Link**: URL khi click nút

**Settings:**
- **Height**: Chiều cao banner (px hoặc vh)
- **Transition Effect**: 
  - `Fade` - Mờ dần
  - `Zoom` - Phóng to ảnh khi hover
  - `Blur` - Làm mờ ảnh
  - `Slide` - Trượt ngang
- **Autoplay Delay**: Delay (giây) trước khi video phát (0 = ngay lập tức)
- **Mobile Behavior**: 
  - `Show Image Only` - Chỉ hiện ảnh trên mobile
  - `Show Video Only` - Chỉ hiện video
  - `Allow Video on Touch` - Cho phép tap để xem video

#### **Tab Style:**

**Title:**
- Color, Typography, Text Shadow

**Button:**
- Text Color, Background Color, Hover Color, Typography

### 2️⃣ Sử dụng với Shortcode

Copy shortcode này vào bất kỳ đâu (Post, Page, Widget...):

```
[anmi_video_banner 
    video_url="https://anmitools.com/videos/banner.mp4" 
    image_url="https://anmitools.com/images/banner.jpg"
    height="600px"
    title="Welcome to An Mi Tools"
    subtitle="Premium CNC Tooling Solutions"
    button_text="Explore Products"
    button_link="https://anmitools.com/products"
    transition="fade"
    mobile_behavior="image"
    autoplay_delay="0"
]
```

#### **Shortcode Parameters:**

| Parameter | Required | Default | Description |
|-----------|----------|---------|-------------|
| `video_url` | ✅ Yes | - | URL video .mp4 |
| `image_url` | ✅ Yes | - | URL ảnh overlay |
| `height` | ❌ No | 600px | Chiều cao banner (px, vh, %) |
| `title` | ❌ No | - | Tiêu đề |
| `subtitle` | ❌ No | - | Mô tả phụ |
| `button_text` | ❌ No | - | Text button |
| `button_link` | ❌ No | # | URL button |
| `transition` | ❌ No | fade | Effect: fade, zoom, blur, slide |
| `mobile_behavior` | ❌ No | image | Mobile: image, video, both |
| `autoplay_delay` | ❌ No | 0 | Delay (seconds) |

---

## 📋 Ví dụ thực tế

### Ví dụ 1: Banner trang chủ với Fade effect

```
[anmi_video_banner 
    video_url="https://anmitools.com/wp-content/uploads/videos/home-banner.mp4" 
    image_url="https://anmitools.com/wp-content/uploads/images/home-cover.jpg"
    height="80vh"
    title="An Mi Tools - Giải pháp CNC toàn diện"
    subtitle="Hơn 20 năm kinh nghiệm cung cấp công cụ cắt gọt chất lượng cao"
    button_text="Xem sản phẩm"
    button_link="/san-pham"
    transition="fade"
]
```

### Ví dụ 2: Product showcase với Zoom effect

```
[anmi_video_banner 
    video_url="/videos/bt-holder-demo.mp4" 
    image_url="/images/bt-holder-cover.jpg"
    height="500px"
    title="BT Tool Holder Series"
    button_text="Tìm hiểu thêm"
    button_link="/san-pham/bt-tool-holder"
    transition="zoom"
    autoplay_delay="0.5"
]
```

### Ví dụ 3: Landing page banner với Blur effect

```
[anmi_video_banner 
    video_url="/videos/factory-tour.mp4" 
    image_url="/images/factory.jpg"
    height="600px"
    title="Nhà máy sản xuất hiện đại"
    subtitle="Quy trình sản xuất khép kín, đảm bảo chất lượng cao nhất"
    button_text="Đặt lịch tham quan"
    button_link="/lien-he"
    transition="blur"
    mobile_behavior="image"
]
```

---

## 🎨 Tùy chỉnh CSS

Thêm CSS tùy chỉnh vào **Customizer** → **Additional CSS**:

### Tùy chỉnh màu nút CTA:

```css
.anmi-banner-btn {
    background: #0066cc !important;
    border-radius: 25px;
}

.anmi-banner-btn:hover {
    background: #0052a3 !important;
    transform: scale(1.05);
}
```

### Tùy chỉnh title font:

```css
.anmi-banner-title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 800;
    letter-spacing: 2px;
}
```

### Thêm gradient overlay:

```css
.anmi-video-banner-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.7));
    z-index: 2;
}

.anmi-banner-content {
    z-index: 4 !important;
}
```

---

## 📱 Mobile Optimization

Plugin tự động tối ưu cho mobile:

- **≤ 768px**: 
  - Chiều cao giảm xuống 400px
  - Font size nhỏ hơn
  - Button padding nhỏ hơn
  
- **≤ 480px**: 
  - Chiều cao 300px
  - Layout compact

### Tắt video hoàn toàn trên mobile:

Thêm vào CSS:

```css
@media (max-width: 768px) {
    .anmi-banner-video {
        display: none !important;
    }
    .anmi-banner-image {
        opacity: 1 !important;
    }
}
```

---

## ⚙️ Performance Tips

### 1. Tối ưu video:

- Format: `.mp4` (H.264 codec)
- Resolution: 1920x1080 max
- Bitrate: 2-3 Mbps
- File size: < 5MB

**Tools:** HandBrake, FFmpeg

```bash
# FFmpeg command to optimize video
ffmpeg -i input.mp4 -vcodec h264 -acodec aac -b:v 2M -preset slow -crf 23 output.mp4
```

### 2. Lazy loading:

Video chỉ load khi scroll đến phần banner (plugin tự động xử lý)

### 3. CDN:

Upload video lên CDN (Cloudflare, AWS CloudFront) để giảm tải server

---

## 🐛 Troubleshooting

### Video không phát trên Chrome/Safari:

**Nguyên nhân:** Browser policy yêu cầu muted để autoplay

**Giải pháp:** Video đã có thuộc tính `muted` mặc định. Check console log.

### Video bị lag/stutter:

**Nguyên nhân:** File quá nặng hoặc bitrate cao

**Giải pháp:** 
- Nén video xuống < 5MB
- Giảm resolution về 1280x720
- Dùng CDN

### Ảnh overlay không ẩn khi hover:

**Nguyên nhân:** CSS conflict với theme

**Giải pháp:** Thêm `!important`:

```css
.anmi-video-banner-container:hover .anmi-banner-image {
    opacity: 0 !important;
}
```

### Video không hiện trong Elementor preview:

**Nguyên nhân:** Cần reload iframe

**Giải pháp:** Save và refresh preview. Plugin tự động hook vào Elementor.

---

## 🔧 Advanced Customization

### Hook vào JavaScript:

```javascript
jQuery(document).on('anmi_video_banner_loaded', function(e, container) {
    console.log('Banner loaded:', container);
});

jQuery(document).on('anmi_video_playing', function(e, video) {
    console.log('Video playing:', video);
});
```

### Filter PHP:

```php
// Thay đổi default height
add_filter('anmi_video_banner_default_height', function($height) {
    return '700px';
});

// Thay đổi mobile breakpoint
add_filter('anmi_video_banner_mobile_breakpoint', function($breakpoint) {
    return 992; // Default: 768
});
```

---

## 📞 Support

- **Email:** sales.hn@anmitools.com
- **Phone:** +84 24 3556 2635
- **Website:** https://anmitools.com

---

## 📝 Changelog

### Version 1.0.0 (2025-11-03)
- ✅ Initial release
- ✅ 4 transition effects
- ✅ Elementor widget
- ✅ Shortcode support
- ✅ Mobile optimization
- ✅ Performance optimized

---

## 📄 License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html
