# 🎬 AN MI VIDEO BANNER - ADMIN PANEL GUIDE
## Version 1.2.0 - CRUD Interface & Database Management

---

## 📋 Mục lục

1. [Admin Panel Overview](#admin-panel-overview)
2. [Tạo Banner Mới](#tạo-banner-mới)
3. [Quản lý Banner](#quản-lý-banner)
4. [Sử dụng trong Elementor](#sử-dụng-trong-elementor)
5. [Sử dụng Shortcode](#sử-dụng-shortcode)
6. [Video Sources Support](#video-sources-support)
7. [Tips & Best Practices](#tips--best-practices)

---

## 🎛️ Admin Panel Overview

### Truy cập Admin Panel

1. Đăng nhập WordPress Admin
2. Trong sidebar, tìm menu **"Video Banners"** (icon video 🎬)
3. Click để xem danh sách tất cả banner

### Dashboard Features

**Video Banners Dashboard** hiển thị:
- 📊 **Statistics:** Tổng số banner, Active, Inactive
- 📋 **Banner List:** Bảng danh sách tất cả banner với:
  - Preview thumbnail
  - Tên banner và title
  - Video source (YouTube/Vimeo/Direct URL)
  - Số lượng ảnh trong slider
  - Shortcode (click to copy)
  - Status (Active/Inactive)
  - Action buttons (Edit/Delete/Preview)

---

## ➕ Tạo Banner Mới

### Bước 1: Click "Add New"

Từ menu **Video Banners → Add New** hoặc click nút **"Add New"** trên dashboard.

### Bước 2: Banner Information

```
Banner Name*: Homepage Hero Banner
(Tên nội bộ để quản lý - không hiển thị public)
```

### Bước 3: Video Settings

#### 3.1. Chọn Video Type

- **Direct URL (MP4):** File video trực tiếp (.mp4, .webm)
- **YouTube:** Video từ YouTube
- **Vimeo:** Video từ Vimeo
- **Embed URL:** Bất kỳ video embed URL nào

#### 3.2. Nhập Video URL

**Direct MP4:**
```
https://anmitools.com/videos/banner-video.mp4
```

**YouTube (2 formats):**
```
https://www.youtube.com/embed/VIDEO_ID
https://www.youtube.com/watch?v=VIDEO_ID
```

**Vimeo:**
```
https://player.vimeo.com/video/VIDEO_ID
```

**Embed URL:**
```
https://any-video-platform.com/embed/video-url
```

### Bước 4: Image Slider

#### 4.1. Upload Images

1. Click nút **"Upload Images"**
2. Chọn nhiều ảnh cùng lúc (Ctrl/Cmd + Click)
3. Click **"Add to Slider"**

#### 4.2. Quản lý Images

- **Drag & Drop:** Kéo thả để sắp xếp lại thứ tự
- **Remove:** Click nút [X] để xóa ảnh
- **Preview:** Xem trước các ảnh đã upload

#### 4.3. Slider Settings

```
Slider Speed: 3000 ms (3 seconds per slide)
Min: 1000ms (1s)
Max: 10000ms (10s)

Transition Effect:
- Fade (smooth opacity transition)
- Zoom (scale effect)
- Blur (blur to video)
- Slide (slide left animation)
```

### Bước 5: Content Overlay (Optional)

```
Title: An Mi Tools - Giải Pháp CNC Toàn Diện
Subtitle: Hơn 20 năm kinh nghiệm trong lĩnh vực công cụ cắt gọt
Button Text: Tìm hiểu thêm
Button Link: https://anmitools.com
```

### Bước 6: Display Settings (Sidebar)

```
Status: Active / Inactive
Height: 600px (có thể dùng vh, %)
Autoplay Delay: 0s (delay before video plays on hover)
Mobile Behavior:
  - Image Only (hiển thị slider, không video)
  - Video Only (chỉ video, không slider)
  - Both (touch to play)
```

### Bước 7: Save

Click **"Create Banner"** hoặc **"Update Banner"**

---

## 📋 Quản lý Banner

### Banner List

**Xem tất cả banner:** Menu → Video Banners → All Banners

### Actions Available

#### 1. Edit Banner
- Click nút **"Edit"** hoặc tên banner
- Sửa bất kỳ thông tin nào
- Click **"Update Banner"** để lưu

#### 2. Delete Banner
- Click nút **"Delete"**
- Confirm xóa (không thể undo)
- Banner sẽ bị xóa khỏi database

#### 3. Copy Shortcode
- Click vào ô shortcode hoặc nút **"Copy"**
- Shortcode được copy vào clipboard
- Paste vào page/post bất kỳ

#### 4. Preview Banner (Coming Soon)
- Click nút **"Preview"**
- Xem trước banner trong popup

### Filter & Search

**Filter by Status:**
- Active banners only
- Inactive banners only
- All banners

**Search:**
- Tìm theo tên banner
- Tìm theo title

---

## 🎨 Sử dụng trong Elementor

### Phương pháp 1: Chọn từ Database (Recommended)

#### Bước 1: Mở Elementor Editor
- Edit page/post với Elementor
- Click **"Edit with Elementor"**

#### Bước 2: Thêm Widget
- Trong panel bên trái, search **"An Mi Video Banner"**
- Drag widget vào vị trí mong muốn

#### Bước 3: Chọn Banner
**Tab: Select Banner**
```
Banner Source: [Dropdown list of all banners]
- Homepage Hero Banner
- Product Showcase Banner
- About Us Banner
- Manual Setup
```

Chọn banner đã tạo từ database → **Xong!**

#### Bước 4: (Optional) Customize
- Tất cả settings được load tự động từ database
- Có thể override trong Elementor nếu cần
- Style tab để tùy chỉnh màu sắc, font chữ

### Phương pháp 2: Manual Setup

#### Nếu chọn "Manual Setup"

**Tab: Video & Images**
```
Video URL: https://yourdomain.com/video.mp4
Slider Images: [Upload gallery]
```

**Tab: Content**
```
Title: Your Title
Subtitle: Your Subtitle
Button Text: Learn More
Button Link: https://link.com
```

**Tab: Settings**
```
Height: 600px
Transition: Fade
Slider Speed: 3000ms
Autoplay Delay: 0s
Mobile Behavior: Image Only
```

**Tab: Style**
- Customize title typography, color
- Button colors (normal & hover)
- Text shadow, spacing

#### Bước 5: Publish
- Click **"Update"** để lưu
- Xem preview trên frontend

### Live Preview

- **Desktop:** Hover vào banner để xem video
- **Mobile:** Touch to play (nếu bật mobile behavior)
- **Slider:** Auto-rotate theo tốc độ cấu hình

---

## 📝 Sử dụng Shortcode

### Shortcode với Banner ID (Recommended)

```php
[anmi_video_banner id="1"]
```

**ID là gì?**
- ID của banner trong database
- Xem trong cột "Shortcode" ở banner list
- Tự động load tất cả settings

### Shortcode với Parameters (Manual)

```php
[anmi_video_banner 
    video_url="https://yourdomain.com/video.mp4"
    images="image1.jpg,image2.jpg,image3.jpg"
    slider_speed="3000"
    height="600px"
    title="An Mi Tools"
    subtitle="Over 20 years experience"
    button_text="Learn More"
    button_link="https://anmitools.com"
    transition="fade"
    autoplay_delay="0"
    mobile_behavior="image"]
```

### Sử dụng trong WordPress

#### 1. Classic Editor
- Paste shortcode vào content
- Publish page

#### 2. Gutenberg (Block Editor)
- Add **Shortcode Block**
- Paste shortcode
- Preview & Publish

#### 3. Widget Area
- Appearance → Widgets
- Add **Shortcode Widget**
- Paste shortcode

#### 4. Theme Template (PHP)
```php
<?php echo do_shortcode('[anmi_video_banner id="1"]'); ?>
```

---

## 🎥 Video Sources Support

### 1. YouTube

#### Lấy YouTube Video URL

**Cách 1: Embed URL (Recommended)**
1. Mở video trên YouTube
2. Click **"Share"** → **"Embed"**
3. Copy URL trong `<iframe src="...">`
4. Format: `https://www.youtube.com/embed/VIDEO_ID`

**Cách 2: Watch URL**
1. Copy URL từ address bar
2. Format: `https://www.youtube.com/watch?v=VIDEO_ID`
3. Plugin tự động convert sang embed URL

**Ví dụ:**
```
✅ https://www.youtube.com/embed/dQw4w9WgXcQ
✅ https://www.youtube.com/watch?v=dQw4w9WgXcQ
✅ https://youtu.be/dQw4w9WgXcQ
```

### 2. Vimeo

#### Lấy Vimeo Video URL

1. Mở video trên Vimeo
2. Click **"Share"** button
3. Copy **Embed** URL
4. Format: `https://player.vimeo.com/video/VIDEO_ID`

**Ví dụ:**
```
✅ https://player.vimeo.com/video/123456789
✅ https://vimeo.com/123456789 (plugin auto-convert)
```

### 3. Direct MP4 URL

#### Upload video lên server

**Cách 1: WordPress Media Library**
1. Media → Add New
2. Upload video file (.mp4 recommended)
3. Copy URL từ media library
4. Paste vào Video URL field

**Cách 2: FTP/Hosting**
1. Upload video qua FTP vào `/wp-content/uploads/videos/`
2. Get public URL: `https://yourdomain.com/wp-content/uploads/videos/banner.mp4`
3. Paste vào Video URL field

**Ví dụ:**
```
✅ https://anmitools.com/videos/banner-video.mp4
✅ https://cdn.anmitools.com/media/promo.mp4
```

**Lưu ý:**
- Optimize video trước khi upload (compress, resize)
- Recommended format: MP4 (H.264 codec)
- Max file size: 50MB (tùy hosting)

### 4. Other Video Platforms

**Wistia:**
```
https://fast.wistia.net/embed/iframe/VIDEO_ID
```

**Dailymotion:**
```
https://www.dailymotion.com/embed/video/VIDEO_ID
```

**Custom CDN:**
```
https://cdn.yourcdn.com/path/to/video.mp4
```

---

## 💡 Tips & Best Practices

### 1. Video Optimization

#### Compress Video
```bash
# FFmpeg command (Linux/Mac)
ffmpeg -i input.mp4 -vcodec h264 -acodec aac -b:v 2M output.mp4
```

#### Recommended Specs
- **Format:** MP4 (H.264 + AAC)
- **Resolution:** 1920x1080 (Full HD) or 1280x720 (HD)
- **Bitrate:** 2-4 Mbps
- **Frame Rate:** 24-30 fps
- **File Size:** < 10MB optimal, < 50MB max

#### Online Tools
- HandBrake (free desktop app)
- CloudConvert (online)
- Adobe Media Encoder

### 2. Image Slider Best Practices

#### Image Specs
- **Resolution:** 1920x1080 (same as video)
- **Format:** WebP (best) or JPEG/PNG
- **File Size:** < 500KB per image
- **Quantity:** 3-5 images optimal

#### Image Optimization
- Use WebP format for smaller file size
- Compress images with TinyPNG, ImageOptim
- Use same aspect ratio for all images

### 3. Performance Tips

#### Lazy Load Videos
- Videos automatically preload="auto"
- Consider preload="metadata" for large files
- Use poster image for initial display

#### CDN Usage
- Host videos on CDN for faster delivery
- YouTube/Vimeo automatically uses CDN
- Direct URLs: use Cloudflare, AWS CloudFront

#### Mobile Optimization
- Set Mobile Behavior to "Image Only" to save data
- Use smaller video files for mobile (responsive video)
- Test on real devices

### 4. SEO Considerations

#### Video SEO
- Add video to sitemap
- Use schema markup (VideoObject)
- Add transcript/caption

#### Image Alt Text
- Add descriptive alt text to slider images
- Use WordPress media library alt field
- Improves accessibility & SEO

### 5. Accessibility

#### WCAG Compliance
- Provide text alternatives for video content
- Use high contrast text on slider
- Add captions/subtitles to videos

#### Keyboard Navigation
- Ensure banner is keyboard accessible
- Tab navigation support
- Skip link option

### 6. Common Issues & Solutions

#### Issue: Video không phát
**Solutions:**
- Check video URL còn valid
- Verify video format (MP4 + H.264)
- Check browser autoplay policy (must be muted)
- Clear browser cache

#### Issue: Slider không chạy
**Solutions:**
- Check jQuery loaded
- Verify images đã upload đúng
- Check browser console for errors
- Ensure slider_speed > 0

#### Issue: Mobile không hiển thị
**Solutions:**
- Check responsive settings
- Verify mobile behavior setting
- Test on real device (not just browser resize)

#### Issue: Slow loading
**Solutions:**
- Compress video (use FFmpeg)
- Use CDN for video hosting
- Enable lazy loading
- Reduce slider image sizes

---

## 📞 Support & Documentation

### Plugin Info
- **Version:** 1.2.0
- **WordPress:** 5.0+
- **PHP:** 7.4+
- **Elementor:** Optional (3.0+)

### Features
✅ Admin CRUD Panel
✅ Database Management
✅ YouTube/Vimeo Support
✅ Direct MP4 Upload
✅ Image Slider (multiple images)
✅ Elementor Widget
✅ Shortcode Support
✅ Mobile Responsive
✅ 4 Transition Effects
✅ Touch Events
✅ Lazy Loading

### Quick Links
- **Admin Panel:** Dashboard → Video Banners
- **Add New:** Video Banners → Add New
- **Documentation:** This file (ADMIN-PANEL-GUIDE.md)
- **Demo Files:** `/demo/` folder
- **Support:** contact@anmitools.com

---

## 🎓 Video Tutorials (Coming Soon)

1. **Getting Started:** Tạo banner đầu tiên (5 phút)
2. **YouTube Integration:** Sử dụng YouTube video (3 phút)
3. **Elementor Setup:** Thêm banner vào Elementor (4 phút)
4. **Advanced Customization:** Tùy chỉnh styles & effects (10 phút)

---

**Created by:** An Mi Tools Technical Team  
**Last Updated:** November 3, 2025  
**Plugin Version:** 1.2.0
