# 📦 HƯỚNG DẪN CÀI ĐẶT & TRIỂN KHAI

## An Mi Video Banner Plugin v1.0.0

---

## 🚀 CÁCH 1: CÀI ĐẶT TRỰC TIẾP QUA WORDPRESS ADMIN

### Bước 1: Chuẩn bị file

1. Nén folder `anmi-video-banner` thành file `.zip`:
   ```
   anmi-video-banner.zip
   ```

2. Đảm bảo cấu trúc trong file zip:
   ```
   anmi-video-banner/
   ├── anmi-video-banner.php
   ├── assets/
   │   ├── css/
   │   │   └── video-banner.css
   │   └── js/
   │       └── video-banner.js
   ├── includes/
   │   └── elementor-widget.php
   ├── README.md
   └── demo/
       └── index.html
   ```

### Bước 2: Upload qua WordPress

1. Đăng nhập **WordPress Admin**
2. Vào **Plugins** → **Add New**
3. Click **Upload Plugin**
4. Chọn file `anmi-video-banner.zip`
5. Click **Install Now**
6. Đợi upload hoàn tất
7. Click **Activate Plugin**

### Bước 3: Kiểm tra

✅ Vào **Plugins** → **Installed Plugins**  
✅ Tìm "An Mi Video Banner" - trạng thái **Active**  
✅ Nếu dùng Elementor: Vào Elementor editor, tìm widget "An Mi Video Banner" trong panel

---

## 📁 CÁCH 2: CÀI ĐẶT QUA FTP/CPANEL

### Bước 1: Upload qua FTP

1. Kết nối FTP đến server (dùng FileZilla, WinSCP...)
2. Đi đến thư mục:
   ```
   /public_html/wp-content/plugins/
   ```
3. Upload toàn bộ folder `anmi-video-banner`
4. Đảm bảo permissions:
   - Folders: `755`
   - Files: `644`

### Bước 2: Activate

1. Đăng nhập WordPress Admin
2. Vào **Plugins** → **Installed Plugins**
3. Tìm "An Mi Video Banner"
4. Click **Activate**

---

## 🎬 CÁCH 3: UPLOAD VIDEO & HÌNH ẢNH

### Upload video

1. Vào **Media** → **Add New**
2. Upload file video `.mp4` (khuyến nghị < 5MB)
3. Copy URL video:
   ```
   https://anmitools.com/wp-content/uploads/2025/11/banner.mp4
   ```

### Upload ảnh

1. Vào **Media** → **Add New**
2. Upload ảnh `.jpg`, `.png`, hoặc `.webp`
3. Copy URL ảnh:
   ```
   https://anmitools.com/wp-content/uploads/2025/11/banner-cover.jpg
   ```

### Optimize video (Optional)

**Dùng HandBrake hoặc FFmpeg:**

```bash
ffmpeg -i input.mp4 -vcodec h264 -acodec aac -b:v 2M -preset slow -crf 23 output.mp4
```

**Settings khuyến nghị:**
- **Resolution:** 1920x1080 hoặc 1280x720
- **Bitrate:** 2-3 Mbps
- **Format:** MP4 (H.264)
- **Duration:** 10-30 seconds (loop)
- **Audio:** Muted (không cần thiết)

---

## ✅ CÁCH 4: SỬ DỤNG SHORTCODE

### Trên Post/Page

1. Tạo hoặc chỉnh sửa Post/Page
2. Thêm **Shortcode Block** (Gutenberg) hoặc paste trực tiếp
3. Nhập shortcode:

```
[anmi_video_banner 
    video_url="https://anmitools.com/wp-content/uploads/2025/11/video.mp4" 
    image_url="https://anmitools.com/wp-content/uploads/2025/11/image.jpg"
    height="600px"
    title="An Mi Tools"
    subtitle="CNC Tooling Solutions"
    button_text="Explore"
    button_link="/products"
    transition="fade"
]
```

4. **Update** hoặc **Publish**
5. Xem trang để kiểm tra

### Trong Widget

1. Vào **Appearance** → **Widgets**
2. Thêm widget **Custom HTML** hoặc **Shortcode**
3. Paste shortcode
4. **Save**

### Trong PHP Template

```php
<?php echo do_shortcode('[anmi_video_banner video_url="..." image_url="..."]'); ?>
```

---

## 🎨 CÁCH 5: SỬ DỤNG VỚI ELEMENTOR

### Bước 1: Mở Elementor Editor

1. Tạo hoặc chỉnh sửa trang
2. Click **Edit with Elementor**

### Bước 2: Thêm Widget

1. Tìm widget **"An Mi Video Banner"** trong panel bên trái
   - Icon: 📹 (video camera)
   - Category: **General**
2. Kéo thả widget vào section mong muốn

### Bước 3: Cấu hình Content

**Tab: Video & Image**
- **Video URL:** Click **Choose Video** → Upload hoặc chọn từ Media Library
- **Image Overlay:** Click **Choose Image** → Upload hoặc chọn ảnh

**Tab: Content**
- **Title:** Nhập tiêu đề chính
- **Subtitle:** Nhập mô tả phụ
- **Button Text:** Text nút CTA
- **Button Link:** URL đích (có thể chọn internal link)

**Tab: Settings**
- **Height:** Drag slider hoặc nhập số (px, vh)
- **Transition Effect:** Chọn fade/zoom/blur/slide
- **Autoplay Delay:** 0-10 giây
- **Mobile Behavior:** Chọn cách hiển thị trên mobile

### Bước 4: Style Customization

**Tab: Title Style**
- **Color:** Chọn màu text
- **Typography:** Font, size, weight, line height
- **Text Shadow:** Thêm shadow cho text

**Tab: Button Style**
- **Text Color:** Màu chữ button
- **Background Color:** Màu nền button
- **Hover Background Color:** Màu hover
- **Typography:** Font settings

### Bước 5: Preview & Publish

1. Click **Preview** để xem trước
2. Hover vào banner để test video
3. Check responsive trên mobile/tablet
4. Click **Update** hoặc **Publish**

---

## 🔧 CÁCH 6: TÙY CHỈNH CSS

### Thêm Custom CSS

Vào **Customizer** → **Additional CSS** hoặc **Appearance** → **Customize** → **Additional CSS**:

### Tùy chỉnh màu button:

```css
.anmi-banner-btn {
    background: #0066cc !important;
    border-radius: 50px;
    padding: 18px 50px;
}

.anmi-banner-btn:hover {
    background: #0052a3 !important;
    transform: scale(1.1);
    box-shadow: 0 10px 30px rgba(0,102,204,0.5);
}
```

### Thêm gradient overlay:

```css
.anmi-video-banner-container::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        135deg, 
        rgba(255,102,0,0.3) 0%, 
        rgba(0,0,0,0.7) 100%
    );
    z-index: 2;
    pointer-events: none;
}
```

### Custom animation cho title:

```css
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.anmi-banner-title {
    animation: slideInLeft 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
```

---

## 📱 RESPONSIVE SETTINGS

### Tắt video hoàn toàn trên mobile:

```css
@media (max-width: 768px) {
    .anmi-banner-video {
        display: none !important;
    }
    
    .anmi-banner-image {
        opacity: 1 !important;
    }
    
    .anmi-video-banner-container {
        height: 350px !important;
    }
}
```

### Ẩn button trên mobile:

```css
@media (max-width: 480px) {
    .anmi-banner-btn {
        display: none;
    }
}
```

---

## 🐛 TROUBLESHOOTING

### Lỗi: Plugin không xuất hiện trong danh sách

**Nguyên nhân:** Sai cấu trúc folder hoặc permissions

**Giải pháp:**
```bash
# Kiểm tra permissions
chmod 755 anmi-video-banner/
chmod 644 anmi-video-banner/anmi-video-banner.php
```

### Lỗi: Video không phát

**Nguyên nhân:** File không tồn tại hoặc URL sai

**Giải pháp:**
1. Check URL video trong Media Library
2. Đảm bảo file là `.mp4`
3. Test URL trực tiếp trong browser

### Lỗi: CSS không load

**Nguyên nhân:** Cache hoặc conflict

**Giải pháp:**
1. Clear cache (WP Super Cache, W3 Total Cache...)
2. Hard refresh: `Ctrl+Shift+R` (Windows) hoặc `Cmd+Shift+R` (Mac)
3. Disable cache plugin tạm thời

### Lỗi: Elementor widget không hiện

**Nguyên nhân:** Elementor chưa refresh

**Giải pháp:**
1. Vào **Elementor** → **Tools** → **Regenerate CSS**
2. Clear Elementor cache
3. Deactivate và Activate lại plugin

---

## 📊 PERFORMANCE CHECKLIST

### Trước khi đưa vào production:

- [ ] Video đã optimize (< 5MB)
- [ ] Video format: MP4 (H.264)
- [ ] Ảnh đã compress (< 500KB)
- [ ] Ảnh format: WebP hoặc JPG
- [ ] Test trên Chrome, Firefox, Safari
- [ ] Test responsive trên mobile
- [ ] Test với slow 3G connection
- [ ] Enable lazy loading
- [ ] Sử dụng CDN (Cloudflare, AWS...)
- [ ] Minify CSS/JS (WP Rocket, Autoptimize...)

---

## 🔒 SECURITY CHECKLIST

- [ ] Không upload video nhạy cảm lên public media
- [ ] Set correct file permissions (755/644)
- [ ] Update WordPress & plugins thường xuyên
- [ ] Sử dụng SSL (HTTPS) cho video URLs
- [ ] Sanitize user inputs (nếu custom code)

---

## 📞 HỖ TRỢ

**Cần trợ giúp?**

- 📧 Email: sales.hn@anmitools.com
- ☎️ Phone: +84 24 3556 2635
- 🌐 Website: https://anmitools.com
- 📖 Documentation: [README.md](../README.md)

---

**Chúc bạn triển khai thành công! 🚀**
