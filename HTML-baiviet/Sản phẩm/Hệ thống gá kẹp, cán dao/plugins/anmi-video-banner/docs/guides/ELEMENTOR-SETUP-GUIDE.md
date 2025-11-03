# 🎨 ELEMENTOR SETUP GUIDE - An Mi Video Banner
## Hướng dẫn chi tiết setup banner trong Elementor

---

## 📋 Mục lục

1. [Chuẩn bị](#chuẩn-bị)
2. [Phương pháp 1: Select Banner từ Database](#phương-pháp-1-select-banner-từ-database-recommended)
3. [Phương pháp 2: Manual Setup](#phương-pháp-2-manual-setup)
4. [Tùy chỉnh Style](#tùy-chỉnh-style)
5. [Templates & Examples](#templates--examples)
6. [Troubleshooting](#troubleshooting)

---

## 🎯 Chuẩn bị

### Yêu cầu

- ✅ WordPress 5.0+
- ✅ Elementor 3.0+ (Free hoặc Pro)
- ✅ Plugin An Mi Video Banner đã activate

### Trước khi bắt đầu

**Nếu dùng Database Method (Recommended):**
1. Vào **WordPress Admin → Video Banners**
2. Tạo ít nhất 1 banner (xem [ADMIN-PANEL-GUIDE.md](ADMIN-PANEL-GUIDE.md))

**Nếu dùng Manual Method:**
1. Chuẩn bị video URL (YouTube/Vimeo/MP4)
2. Chuẩn bị 3-5 ảnh cho slider (1920x1080 recommended)

---

## 🚀 Phương pháp 1: Select Banner từ Database (RECOMMENDED)

### Bước 1: Mở Elementor Editor

**Cách 1: Edit existing page**
```
Pages → All Pages → Hover page → Click "Edit with Elementor"
```

**Cách 2: Create new page**
```
Pages → Add New → Enter title → Click "Edit with Elementor"
```

### Bước 2: Thêm Widget

**Trong Elementor Editor:**

1. **Mở Widget Panel**
   - Panel bên trái tự động mở
   - Hoặc click icon "+" ở góc trái

2. **Search Widget**
   - Gõ "An Mi" hoặc "Video Banner" trong search box
   - Widget sẽ xuất hiện với icon 🎥

3. **Drag & Drop**
   - Click giữ widget
   - Kéo vào section mong muốn
   - Thả chuột để place widget

### Bước 3: Select Banner

**Tab: Select Banner** (Mở mặc định)

```
┌─────────────────────────────────────┐
│ Banner Source                       │
│ ┌─────────────────────────────────┐ │
│ │ Homepage Hero Banner        ▼   │ │ <- Dropdown list
│ └─────────────────────────────────┘ │
│                                     │
│ ℹ️ Create New Banner in Admin Panel │ <- Link to admin
└─────────────────────────────────────┘
```

**Chọn banner từ dropdown:**
- Tất cả banner đã tạo sẽ hiển thị
- Format: "Banner Name" (from database)
- Chọn 1 banner → **Xong!**

**Tất cả settings tự động load:**
- ✅ Video URL
- ✅ Slider images
- ✅ Title, subtitle, button
- ✅ Height, transition, speed
- ✅ Mobile behavior

### Bước 4: (Optional) Override Settings

**Nếu muốn tùy chỉnh thêm:**

**Tab: Style**
- Title color, typography, shadow
- Button colors (normal + hover)
- Custom CSS nếu cần

**Tab: Advanced**
- Margin, padding
- Custom CSS ID/Class
- Z-index, animations

### Bước 5: Preview & Publish

**Preview:**
1. Click **"Preview"** button (bottom left)
2. Hover vào banner để test video
3. Test responsive (mobile/tablet icons)

**Publish:**
1. Click **"Publish"** hoặc **"Update"**
2. Xác nhận publish
3. View page trên frontend

---

## 🛠️ Phương pháp 2: Manual Setup

### Bước 1-2: Như phương pháp 1

(Mở Elementor, thêm widget)

### Bước 3: Chọn Manual Setup

**Tab: Select Banner**

```
Banner Source: Manual Setup ▼
```

### Bước 4: Configure Video & Images

**Tab: Video & Images** (xuất hiện khi chọn Manual)

#### 4.1. Video URL

```
┌─────────────────────────────────────┐
│ Video URL                           │
│ ┌─────────────────────────────────┐ │
│ │ https://yourdomain.com/video.mp4│ │
│ └─────────────────────────────────┘ │
│ 💡 YouTube, Vimeo, or direct MP4   │
└─────────────────────────────────────┘
```

**Supported formats:**
- YouTube: `https://www.youtube.com/watch?v=VIDEO_ID`
- Vimeo: `https://vimeo.com/VIDEO_ID`
- Direct: `https://cdn.com/video.mp4`

#### 4.2. Slider Images

```
┌─────────────────────────────────────┐
│ Slider Images                       │
│ ┌───────┐ ┌───────┐ ┌───────┐     │
│ │ IMG 1 │ │ IMG 2 │ │ IMG 3 │  +  │
│ └───────┘ └───────┘ └───────┘     │
│ 📤 Upload multiple images for slider│
└─────────────────────────────────────┘
```

**Upload process:**
1. Click **"+"** button hoặc image placeholder
2. Chọn từ Media Library hoặc Upload Files
3. Ctrl/Cmd + Click để chọn nhiều ảnh
4. Click **"Insert gallery"**
5. Drag để reorder nếu cần

**Image specs:**
- Resolution: 1920x1080 (16:9)
- Format: WebP, JPEG, or PNG
- Size: < 500KB per image
- Quantity: 3-5 images optimal

### Bước 5: Add Content

**Tab: Content**

```
┌─────────────────────────────────────┐
│ Title                               │
│ ┌─────────────────────────────────┐ │
│ │ An Mi Tools - CNC Solutions     │ │
│ └─────────────────────────────────┘ │
│                                     │
│ Subtitle                            │
│ ┌─────────────────────────────────┐ │
│ │ Over 20 years of experience...  │ │
│ │                                 │ │
│ └─────────────────────────────────┘ │
│                                     │
│ Button Text                         │
│ ┌─────────────────────────────────┐ │
│ │ Learn More                      │ │
│ └─────────────────────────────────┘ │
│                                     │
│ Button Link                         │
│ ┌─────────────────────────────────┐ │
│ │ https://anmitools.com           │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

### Bước 6: Configure Settings

**Tab: Settings**

```
┌─────────────────────────────────────┐
│ Height               [600] px ▼     │ <- Slider with units
│                                     │
│ Transition           [Fade] ▼      │ <- Fade/Zoom/Blur/Slide
│                                     │
│ Slider Speed         [3000] ms     │ <- Time per slide
│                                     │
│ Autoplay Delay       [0] seconds   │ <- Delay before video
│                                     │
│ Mobile Behavior      [Image] ▼     │ <- Image/Video/Both
└─────────────────────────────────────┘
```

**Settings explained:**

**Height:**
- Default: `600px`
- Options: `px`, `vh`, `%`
- Examples: `100vh` (full screen), `50%` (half)

**Transition:**
- **Fade:** Smooth opacity (default)
- **Zoom:** Scale image 1.1x while fading
- **Blur:** Blur effect to video
- **Slide:** Slide left animation

**Slider Speed:**
- Milliseconds between slides
- Default: `3000` (3 seconds)
- Range: 1000-10000ms
- Example: `5000` = 5s per slide

**Autoplay Delay:**
- Delay before video plays on hover
- Default: `0` (instant)
- Range: 0-10 seconds
- Example: `0.5` = play after 0.5s

**Mobile Behavior:**
- **Image Only:** Show slider, no video (saves data)
- **Video Only:** Show video, no slider
- **Both:** Touch to play video

### Bước 7: Publish

(Như phương pháp 1)

---

## 🎨 Tùy chỉnh Style

### Tab: Style

#### 1. Title Styling

```
┌─────────────────────────────────────┐
│ Title                               │
│ ┌─────────────────────────────────┐ │
│ │ Color        [#ffffff]  ■       │ │ <- Color picker
│ │ Typography   [Customize...]     │ │ <- Font settings
│ │   Family     [Poppins] ▼        │ │
│ │   Size       [48] px            │ │
│ │   Weight     [700] ▼            │ │
│ │   Transform  [None] ▼           │ │
│ │ Text Shadow  [0 2px 8px...]     │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

**Typography options:**
- Font Family: Google Fonts integration
- Size: Responsive (Desktop/Tablet/Mobile)
- Weight: 300-900
- Style: Normal/Italic
- Decoration: None/Underline
- Line Height: 1.0-3.0
- Letter Spacing: -2 to 10px

#### 2. Subtitle Styling

(Same as Title, but smaller defaults)

#### 3. Button Styling

```
┌─────────────────────────────────────┐
│ Button                              │
│ ┌─────────────────────────────────┐ │
│ │ Background Color [#ff6600] ■    │ │
│ │ Hover BG Color   [#ff8533] ■    │ │
│ │ Typography       [Customize...] │ │
│ │ Border Radius    [5] px         │ │
│ │ Padding          ☐ 15px 40px    │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

**Button customization:**
- Background color (normal + hover)
- Text color (normal + hover)
- Typography (family, size, weight)
- Border (width, style, color, radius)
- Padding (top, right, bottom, left)
- Box shadow

### Tab: Advanced

```
┌─────────────────────────────────────┐
│ Margin                              │
│ ┌─────────────────────────────────┐ │
│ │ Top    [0] px                   │ │
│ │ Right  [0] px                   │ │
│ │ Bottom [60] px                  │ │
│ │ Left   [0] px                   │ │
│ └─────────────────────────────────┘ │
│                                     │
│ Custom CSS                          │
│ ┌─────────────────────────────────┐ │
│ │ selector {                      │ │
│ │   /* your custom CSS */         │ │
│ │ }                               │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

---

## 📐 Templates & Examples

### Template 1: Homepage Hero Banner

**Settings:**
```yaml
Height: 100vh
Transition: Fade
Slider Speed: 4000ms
Autoplay Delay: 0s
Mobile: Image Only

Content:
  Title: "An Mi Tools - Giải Pháp CNC Toàn Diện"
  Subtitle: "Hơn 20 năm kinh nghiệm"
  Button: "Khám phá ngay"
  
Style:
  Title: 64px, Bold, White, Shadow
  Button: #ff6600, White text, 5px radius
```

### Template 2: Product Showcase

**Settings:**
```yaml
Height: 600px
Transition: Zoom
Slider Speed: 3000ms
Autoplay Delay: 0.5s
Mobile: Both (Touch to play)

Content:
  Title: "Hệ Thống Gá Kẹp Cán Dao"
  Subtitle: "Công nghệ Nhật Bản"
  Button: "Xem chi tiết"
  
Style:
  Title: 48px, Semi-Bold, White
  Button: #0073aa, White text
```

### Template 3: About Us Section

**Settings:**
```yaml
Height: 500px
Transition: Blur
Slider Speed: 5000ms
Autoplay Delay: 1s
Mobile: Video Only

Content:
  Title: "Nhà Máy Sản Xuất Hiện Đại"
  Subtitle: "Quy trình chuẩn quốc tế ISO 9001"
  Button: "Về chúng tôi"
  
Style:
  Title: 40px, Bold, #333
  Subtitle: 20px, Regular, #666
  Button: Gradient background
```

---

## 🔧 Troubleshooting

### Widget không xuất hiện trong Elementor

**Nguyên nhân:**
- Plugin chưa activate
- Elementor cache chưa clear

**Giải pháp:**
```
1. Plugins → Installed Plugins
2. Verify "An Mi Video Banner" = Active
3. Elementor → Tools → Regenerate CSS
4. Clear browser cache (Ctrl + Shift + R)
```

### Banner không hiển thị trong dropdown

**Nguyên nhân:**
- Chưa tạo banner nào
- Banner status = Inactive

**Giải pháp:**
```
1. Video Banners → Add New
2. Tạo ít nhất 1 banner
3. Set Status = Active
4. Refresh Elementor Editor
```

### Video không phát khi hover

**Nguyên nhân:**
- Video URL không valid
- Browser autoplay policy
- Video format không support

**Giải pháp:**
```
1. Check video URL còn accessible
2. Video phải là MP4 (H.264 codec)
3. Browser must allow muted autoplay
4. Open browser console (F12) để xem errors
```

### Slider không chạy

**Nguyên nhân:**
- Chỉ upload 1 ảnh
- jQuery không load
- JavaScript error

**Giải pháp:**
```
1. Upload ít nhất 2 ảnh
2. Check jQuery loaded (F12 Console)
3. Check browser console for errors
4. Disable other plugins to test conflict
```

### Images bị vỡ layout

**Nguyên nhân:**
- Ảnh khác aspect ratio
- File size quá lớn
- CSS conflict

**Giải pháp:**
```
1. Use same aspect ratio (16:9)
2. Resize tất cả ảnh về 1920x1080
3. Compress images (< 500KB)
4. Check CSS inspector (F12)
```

### Banner chậm load

**Nguyên nhân:**
- Video file quá lớn
- Nhiều ảnh chưa optimize
- Server chậm

**Giải pháp:**
```
1. Compress video (< 10MB)
2. Use CDN for video hosting
3. Optimize images (WebP format)
4. Enable lazy loading
5. Use YouTube/Vimeo embed
```

---

## 📱 Responsive Design Tips

### Desktop (> 1024px)
```
Height: 600-800px hoặc 100vh
Title: 48-64px
Subtitle: 18-24px
Button: 16-18px
```

### Tablet (768-1024px)
```
Height: 500-600px hoặc 70vh
Title: 36-48px
Subtitle: 16-20px
Button: 14-16px
```

### Mobile (< 768px)
```
Height: 400-500px hoặc 60vh
Title: 28-36px
Subtitle: 14-16px
Button: 14px
Mobile Behavior: Image Only (recommended)
```

**Elementor Responsive Controls:**
- Click icon desktop/tablet/mobile
- Adjust values for each breakpoint
- Test preview on each device

---

## 🎯 Best Practices

### Do's ✅

- ✅ Use database method (easier to manage)
- ✅ Optimize images before upload (WebP)
- ✅ Use YouTube/Vimeo for large videos
- ✅ Test on real mobile devices
- ✅ Use consistent image aspect ratio
- ✅ Set mobile behavior = Image Only
- ✅ Add descriptive alt text to images
- ✅ Test hover effect trước khi publish

### Don'ts ❌

- ❌ Upload videos > 50MB directly
- ❌ Use different aspect ratios
- ❌ Forget to optimize images
- ❌ Ignore mobile preview
- ❌ Use too many images (> 10)
- ❌ Set slider speed < 1000ms
- ❌ Forget to test video playback
- ❌ Skip browser compatibility testing

---

## 📞 Support

**Need help?**
- Email: contact@anmitools.com
- Documentation: [README.md](README.md)
- Admin Guide: [ADMIN-PANEL-GUIDE.md](ADMIN-PANEL-GUIDE.md)

**Version:** 1.2.0  
**Last Updated:** November 3, 2025
