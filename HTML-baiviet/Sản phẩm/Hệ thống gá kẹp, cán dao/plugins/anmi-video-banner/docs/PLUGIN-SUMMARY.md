# 🚀 AN MI VIDEO BANNER PLUGIN - VERSION 1.2.0
## Complete Summary & Quick Reference

---

## 📊 Plugin Overview

**Version:** 1.2.0  
**Release Date:** November 3, 2025  
**WordPress:** 5.0+  
**PHP:** 7.4+  
**Elementor:** 3.0+ (Optional)

---

## ✨ What's New in v1.2.0

### 🎛️ Admin Panel CRUD
- **WordPress Admin menu** "Video Banners" 
- **Create/Edit/Delete** banners trong dashboard
- **Database storage** - lưu banner vào `wp_anmi_video_banners` table
- **Visual interface** với thumbnails, stats, và quick actions
- **Copy shortcode** với 1 click
- **Status management** (Active/Inactive)

### 🎬 Video Sources Support
- **YouTube** - Embed URL hoặc watch URL
- **Vimeo** - Player URL
- **Direct MP4** - Upload hoặc external URL
- **Any video URL** - Embed từ bất kỳ platform nào

### 🖼️ Image Slider (v1.1.0)
- **Multiple images** auto-rotating slider
- **Drag & drop** reorder trong admin
- **Hover to video** - Slider pause, video play
- **Mouse out** - Video stop, slider resume
- **Navigation dots** với click navigation

### 🎨 Elementor Integration Update
- **Banner selector** dropdown trong widget
- **Database banners** - Chọn từ list có sẵn
- **Manual setup** option vẫn available
- **Auto-load settings** từ database

### 📝 Shortcode Update
- **Simple syntax** - `[anmi_video_banner id="1"]`
- **Database-driven** - Load từ saved banner
- **Backward compatible** - Vẫn hỗ trợ manual parameters

---

## 📁 File Structure

```
anmi-video-banner/
├── anmi-video-banner.php          # Main plugin file (v1.2.0)
├── README.md                      # General documentation
├── ADMIN-PANEL-GUIDE.md          # Admin panel tutorial (NEW)
├── ELEMENTOR-SETUP-GUIDE.md      # Elementor tutorial (NEW)
├── INSTALL.md                     # Installation guide
│
├── assets/
│   ├── css/
│   │   ├── video-banner.css       # Frontend styles
│   │   └── admin-style.css        # Admin panel styles (NEW)
│   └── js/
│       ├── video-banner.js        # Frontend JavaScript
│       └── admin-script.js        # Admin JavaScript (NEW)
│
├── includes/
│   ├── admin-panel.php            # Admin CRUD logic (NEW)
│   ├── elementor-widget.php       # Elementor widget (updated)
│   └── views/
│       ├── admin-list.php         # Banner list page (NEW)
│       └── admin-edit.php         # Banner edit form (NEW)
│
└── demo/
    ├── index.html                 # General demo
    ├── anmi-profile-demo.html     # An Mi video demo
    └── slider-demo.html           # Slider demo (v1.1.0)
```

**Total:** 15+ files, ~6000+ lines of code

---

## 🎯 Usage Methods

### 1️⃣ Shortcode (Simplest)

**With Banner ID (Recommended):**
```php
[anmi_video_banner id="1"]
```

**With Parameters (Manual):**
```php
[anmi_video_banner 
    video_url="https://youtube.com/embed/VIDEO_ID"
    images="img1.jpg,img2.jpg,img3.jpg"
    title="Title Here"
    button_text="Click Me"]
```

### 2️⃣ Elementor Widget

**Database Method:**
1. Add widget "An Mi Video Banner"
2. Select banner from dropdown
3. Publish → Done!

**Manual Method:**
1. Add widget "An Mi Video Banner"
2. Choose "Manual Setup"
3. Configure all settings
4. Publish

### 3️⃣ Admin Panel

**Create Banner:**
1. Video Banners → Add New
2. Select video type (YouTube/Vimeo/MP4)
3. Paste video URL
4. Upload slider images
5. Add content (title/subtitle/button)
6. Save → Get shortcode

---

## 🗄️ Database Schema

### Table: `wp_anmi_video_banners`

```sql
id              bigint(20)      AUTO_INCREMENT PRIMARY KEY
name            varchar(255)    NOT NULL              -- Internal name
video_url       text            NOT NULL              -- Video URL
video_type      varchar(50)     DEFAULT 'url'         -- url/youtube/vimeo/embed
images          text            NOT NULL              -- JSON array
title           varchar(255)    DEFAULT ''            -- Display title
subtitle        text            DEFAULT ''            -- Subtitle
button_text     varchar(100)    DEFAULT ''            -- CTA text
button_link     varchar(255)    DEFAULT ''            -- CTA URL
height          varchar(50)     DEFAULT '600px'       -- Banner height
transition      varchar(50)     DEFAULT 'fade'        -- fade/zoom/blur/slide
slider_speed    int(11)         DEFAULT 3000          -- Milliseconds
slider_effect   varchar(50)     DEFAULT 'fade'        -- Slider effect
autoplay_delay  int(11)         DEFAULT 0             -- Hover delay (seconds)
mobile_behavior varchar(50)     DEFAULT 'image'       -- image/video/both
status          varchar(20)     DEFAULT 'active'      -- active/inactive
created_at      datetime        DEFAULT CURRENT_TIMESTAMP
updated_at      datetime        ON UPDATE CURRENT_TIMESTAMP
```

**Example Data:**
```sql
INSERT INTO wp_anmi_video_banners 
(name, video_url, video_type, images, title, height, transition, slider_speed)
VALUES 
('Homepage Hero', 
 'https://www.youtube.com/embed/VIDEO_ID',
 'youtube',
 '["img1.jpg","img2.jpg","img3.jpg"]',
 'An Mi Tools - CNC Solutions',
 '600px',
 'fade',
 3000);
```

---

## 🎨 Features Summary

### Core Features
| Feature | Status | Version | Description |
|---------|--------|---------|-------------|
| Hover to Play | ✅ | 1.0.0 | Video plays on hover, stops on mouse out |
| 4 Transitions | ✅ | 1.0.0 | Fade, Zoom, Blur, Slide effects |
| Responsive | ✅ | 1.0.0 | Mobile, tablet, desktop optimized |
| Elementor Widget | ✅ | 1.0.0 | Drag & drop integration |
| Shortcode | ✅ | 1.0.0 | Use anywhere in WordPress |
| Image Slider | ✅ | 1.1.0 | Multiple images auto-rotate |
| Navigation Dots | ✅ | 1.1.0 | Click to change slide |
| Admin Panel | ✅ | 1.2.0 | CRUD interface |
| Database Storage | ✅ | 1.2.0 | Save banners to DB |
| YouTube Support | ✅ | 1.2.0 | Embed YouTube videos |
| Vimeo Support | ✅ | 1.2.0 | Embed Vimeo videos |
| Banner Selector | ✅ | 1.2.0 | Dropdown in Elementor |

### Advanced Features
- ✅ Preload video for smooth playback
- ✅ Lazy loading support
- ✅ Mobile behavior configuration
- ✅ Touch events for mobile
- ✅ Autoplay delay setting
- ✅ Custom CSS support
- ✅ WordPress Media Library integration
- ✅ Drag & drop image sorting
- ✅ AJAX save (no page reload)
- ✅ Copy shortcode button
- ✅ Status management (Active/Inactive)
- ✅ Statistics dashboard

---

## 🎯 Quick Start Guide

### 5-Minute Setup

**Step 1: Install & Activate (1 min)**
```
Plugins → Add New → Upload → anmi-video-banner.zip → Activate
```

**Step 2: Create First Banner (2 min)**
```
Video Banners → Add New
↓
Video Type: YouTube
Video URL: https://www.youtube.com/watch?v=dQw4w9WgXcQ
↓
Upload 3 Images (drag & drop)
↓
Title: "Welcome to An Mi Tools"
Button: "Learn More"
↓
Click "Create Banner"
```

**Step 3: Use in Page (2 min)**

**Option A: Shortcode**
```
Copy: [anmi_video_banner id="1"]
Paste into any page/post
```

**Option B: Elementor**
```
Open page with Elementor
Search "An Mi Video Banner"
Drag to page
Select banner from dropdown
Publish
```

**Done!** 🎉

---

## 📋 Parameters Reference

### Shortcode Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `id` | int | - | Banner ID from database |
| `video_url` | URL | required | Video URL (YouTube/Vimeo/MP4) |
| `images` | string | required | Comma-separated image URLs |
| `height` | string | 600px | Banner height (px, vh, %) |
| `title` | string | empty | Display title |
| `subtitle` | string | empty | Display subtitle |
| `button_text` | string | empty | Button text |
| `button_link` | URL | # | Button URL |
| `transition` | string | fade | fade, zoom, blur, slide |
| `slider_speed` | int | 3000 | Milliseconds per slide |
| `autoplay_delay` | int | 0 | Seconds before video plays |
| `mobile_behavior` | string | image | image, video, both |

### Elementor Widget Controls

**Tab: Select Banner**
- Banner Source (dropdown)
- Link to create new banner

**Tab: Video & Images** (Manual only)
- Video URL (text input)
- Slider Images (gallery)

**Tab: Content** (Manual only)
- Title (text)
- Subtitle (textarea)
- Button Text (text)
- Button Link (URL)

**Tab: Settings**
- Height (slider + unit)
- Transition (select)
- Slider Speed (number)
- Autoplay Delay (number)
- Mobile Behavior (select)

**Tab: Style**
- Title typography, color, shadow
- Subtitle typography, color
- Button colors (normal + hover)
- Button typography

**Tab: Advanced**
- Margin, padding
- Custom CSS
- Z-index, animations

---

## 🎥 Video Sources

### YouTube

**Formats accepted:**
```
✅ https://www.youtube.com/embed/VIDEO_ID
✅ https://www.youtube.com/watch?v=VIDEO_ID
✅ https://youtu.be/VIDEO_ID
```

**Get embed URL:**
1. Open video on YouTube
2. Click "Share" → "Embed"
3. Copy URL from `<iframe src="...">`

### Vimeo

**Formats accepted:**
```
✅ https://player.vimeo.com/video/VIDEO_ID
✅ https://vimeo.com/VIDEO_ID (auto-convert)
```

**Get embed URL:**
1. Open video on Vimeo
2. Click "Share" button
3. Copy embed URL

### Direct MP4

**Upload to WordPress:**
```
Media → Add New → Upload video → Copy URL
```

**Or use external URL:**
```
https://cdn.yoursite.com/videos/banner.mp4
```

**Video specs:**
- Format: MP4 (H.264 + AAC)
- Resolution: 1920x1080 or 1280x720
- Bitrate: 2-4 Mbps
- File size: < 10MB (optimal), < 50MB (max)

---

## 🔧 Common Tasks

### Change Video URL
```
Method 1: Admin Panel
Video Banners → Find banner → Edit → Update Video URL → Save

Method 2: Shortcode
Update video_url parameter in shortcode

Method 3: Elementor
Edit page → Select widget → Update Video URL → Publish
```

### Add More Images to Slider
```
Admin Panel:
Video Banners → Edit banner → Upload Images → Drag to reorder → Save
```

### Change Slider Speed
```
Admin Panel:
Video Banners → Edit banner → Slider Speed: 5000ms → Save

Shortcode:
[anmi_video_banner id="1" slider_speed="5000"]

Elementor:
Settings tab → Slider Speed: 5000
```

### Disable Banner Temporarily
```
Admin Panel:
Video Banners → Edit banner → Status: Inactive → Save
```

### Delete Banner
```
Admin Panel:
Video Banners → Find banner → Delete → Confirm
(Shortcodes will show error, remove manually)
```

---

## 🐛 Troubleshooting

### Video không phát
1. Check video URL còn valid
2. Verify video format (MP4 H.264)
3. Check browser console (F12) for errors
4. Ensure browser allows muted autoplay

### Slider không chạy
1. Upload ít nhất 2 ảnh
2. Check slider_speed > 0
3. Verify jQuery loaded
4. Check browser console for errors

### Admin panel không hiển thị
1. Re-activate plugin
2. Check WordPress version (5.0+)
3. Check PHP version (7.4+)
4. Verify database table created

### Elementor widget không có dropdown
1. Tạo ít nhất 1 banner trong admin
2. Set status = Active
3. Refresh Elementor editor
4. Clear Elementor cache (Tools → Regenerate CSS)

---

## 📚 Documentation Files

| File | Purpose | Size |
|------|---------|------|
| `README.md` | General overview & features | 365 lines |
| `ADMIN-PANEL-GUIDE.md` | Admin panel tutorial | 650 lines |
| `ELEMENTOR-SETUP-GUIDE.md` | Elementor tutorial | 572 lines |
| `INSTALL.md` | Installation guide | 400 lines |
| `THIS FILE` | Quick reference | You're here! |

**Total Documentation:** 2000+ lines

---

## 🎓 Learning Resources

### For Beginners
1. Read `README.md` - General overview
2. Follow `INSTALL.md` - Installation
3. Try `ADMIN-PANEL-GUIDE.md` - Create first banner
4. Use shortcode in a page

### For Elementor Users
1. Read `ELEMENTOR-SETUP-GUIDE.md`
2. Follow "Phương pháp 1: Database"
3. Customize in Style tab

### For Advanced Users
1. Read all documentation
2. Explore database schema
3. Custom CSS in Elementor Advanced tab
4. Modify source code if needed

---

## 📊 Statistics

### Code Metrics
- **PHP Files:** 5 files (~2500 lines)
- **CSS Files:** 2 files (~700 lines)
- **JavaScript Files:** 2 files (~400 lines)
- **HTML Files:** 3 demo files (~1400 lines)
- **Documentation:** 4 MD files (~2000 lines)
- **Total:** 15+ files, ~7000+ lines

### Features
- ✅ 12 major features
- ✅ 4 transition effects
- ✅ 3 usage methods (Shortcode/Elementor/Admin)
- ✅ 3 video sources (YouTube/Vimeo/Direct)
- ✅ 3 mobile behaviors
- ✅ 15+ shortcode parameters
- ✅ 20+ Elementor controls

---

## 🚀 Future Roadmap

### v1.3.0 (Planned)
- [ ] Preview modal in admin list
- [ ] Bulk actions (delete multiple)
- [ ] Banner templates library
- [ ] Import/Export functionality

### v1.4.0 (Future)
- [ ] Analytics (views, clicks tracking)
- [ ] A/B testing support
- [ ] Video playlist support
- [ ] Advanced animations

### v2.0.0 (Vision)
- [ ] Pro version with premium features
- [ ] AI-powered video optimization
- [ ] Cloud storage integration
- [ ] Multi-language support

---

## 💼 Support & Contact

**Email:** contact@anmitools.com  
**Website:** https://anmitools.com  
**Documentation:** All `.md` files in plugin folder  

**Office:**
- **Hanoi:** 50 Trần Quốc Hoàn, Dịch Vọng Hậu, Cầu Giấy
- **HCM:** 110/11 Lê Lợi, Phường 4, Quận Gò Vấp

---

## 📄 License

GPL v2 or later

---

**Plugin Version:** 1.2.0  
**Last Updated:** November 3, 2025  
**Created by:** An Mi Tools Technical Team

---

🎉 **Thank you for using An Mi Video Banner Plugin!**
