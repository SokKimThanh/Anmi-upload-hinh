# An Mi Video Banner - Troubleshooting Guide

## ❌ Video Không Chạy Trong Modal Preview

### 🔍 Triệu chứng:
- Modal hiển thị OK
- AJAX load thành công
- Console log: "Video preload timeout - showing slider"
- Video không xuất hiện hoặc không play

### ✅ Giải pháp:

#### 1. Kiểm tra Console Log
Mở Developer Tools (F12) → Console tab → Nhấn Preview button

**Console log chuẩn phải có:**
```javascript
Original video_url: https://youtu.be/egbA1RHO8MY?si=...
video_type: youtube
Not an iframe code, using URL directly
YouTube detected - Video ID: egbA1RHO8MY
Final video detection - Type: youtube, Embed URL: https://www.youtube.com/embed/...
Creating iframe for youtube with URL: https://www.youtube.com/embed/...
```

**Nếu thiếu dòng "YouTube detected":**
- URL không đúng format → Xem phần [URL Formats](#url-formats) bên dưới
- Regex không match → Cần update plugin lên v1.6.3+

#### 2. Xóa Cache Browser
Plugin v1.6.3 có asset version mới. Xóa cache:
- **Chrome/Edge:** Ctrl + Shift + R (Windows) / Cmd + Shift + R (Mac)
- **Firefox:** Ctrl + F5
- **Safari:** Cmd + Option + R

#### 3. Kiểm tra Plugin Version
```
Trang Admin → An Mi Video Banners → Kiểm tra footer: "Version 1.6.3"
```

Nếu không phải v1.6.3:
1. Upload lại toàn bộ thư mục plugin
2. Vào WordPress Admin → Plugins → Deactivate → Activate lại

---

## 📺 URL Formats (YouTube)

### ✅ Formats được hỗ trợ (v1.6.3+):

**1. YouTube Share URL với query parameter:**
```
https://youtu.be/VIDEO_ID?si=SHARE_CODE
https://youtu.be/egbA1RHO8MY?si=-RXA2ksd67pHdDq6
```

**2. YouTube Watch URL:**
```
https://www.youtube.com/watch?v=VIDEO_ID
https://www.youtube.com/watch?v=egbA1RHO8MY
```

**3. YouTube Embed URL:**
```
https://www.youtube.com/embed/VIDEO_ID
https://www.youtube.com/embed/egbA1RHO8MY
```

**4. Iframe Embed Code (Khuyến nghị):**
```html
<iframe width="560" height="315" 
  src="https://www.youtube.com/embed/egbA1RHO8MY?si=-RXA2ksd67pHdDq6" 
  title="YouTube video player" frameborder="0" 
  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
  referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
</iframe>
```

### 🎯 Cách lấy Iframe Code từ YouTube:

1. Mở video trên YouTube
2. Click nút **"Share"** (Chia sẻ)
3. Click **"Embed"** (Nhúng)
4. Copy toàn bộ code `<iframe>...</iframe>`
5. Paste vào field **"Mã Nhúng Video"** trong plugin

---

## 🚫 WebFontLoader Error

### 🔍 Triệu chứng:
```
webfontloader.js?ver=3.0.28:10 Uncaught TypeError: d[b].on is not a function
```

### ✅ Giải pháp:
Plugin v1.6.3 đã fix lỗi này với 2-layer dequeue:
1. `admin_init` hook (priority 9999)
2. `admin_enqueue_scripts` (priority 999)

**Nếu vẫn thấy error:**
1. Clear browser cache (Ctrl + Shift + R)
2. Upload lại `includes/admin-panel.php`
3. Deactivate → Activate plugin

---

## 🎬 Modal Preview Không Hiển Thị

### 🔍 Triệu chứng:
- Click "Preview" → Không có gì xảy ra
- Console error: "AnMiVideoBanner is not defined"

### ✅ Giải pháp:
Plugin v1.6.1+ đã expose class ra global scope.

**Kiểm tra:**
```javascript
// Trong Console, gõ:
typeof AnMiVideoBanner
// Phải return: "function"
```

**Nếu return "undefined":**
1. Upload lại `assets/js/video-banner.js`
2. Clear cache (Ctrl + Shift + R)
3. Reload trang admin

---

## 📋 Debug Checklist

Khi có vấn đề, làm theo thứ tự:

- [ ] **Step 1:** Mở Developer Tools (F12) → Console tab
- [ ] **Step 2:** Click nút "Preview" banner
- [ ] **Step 3:** Check console logs:
  - [ ] "Preview button clicked!"
  - [ ] "showPreviewModal called with ID: X"
  - [ ] "AJAX Success"
  - [ ] "Original video_url: ..."
  - [ ] "YouTube detected - Video ID: ..."
  - [ ] "Creating iframe for youtube..."
- [ ] **Step 4:** Nếu thiếu bất kỳ log nào → Copy TOÀN BỘ console → Gửi cho dev
- [ ] **Step 5:** Check plugin version (phải là 1.6.3+)
- [ ] **Step 6:** Clear cache (Ctrl + Shift + R)
- [ ] **Step 7:** Test lại

---

## 🆘 Liên hệ Support

Nếu sau khi làm theo tất cả bước trên vẫn không được:

**Thông tin cần gửi:**
1. Plugin version (vd: 1.6.3)
2. WordPress version
3. Browser đang dùng (Chrome/Firefox/Safari)
4. Screenshot của Console (F12)
5. Video URL đang test (vd: `https://youtu.be/...`)
6. Screenshot của Modal Preview (nếu có)

**Liên hệ:**
- Email: support@anmitools.com
- Website: https://anmitools.com

---

## 📚 Các Guide Khác

- [README.md](README.md) - Tổng quan plugin
- [CHANGELOG.md](CHANGELOG.md) - Lịch sử cập nhật
- [docs/QUICK-START.md](docs/QUICK-START.md) - Hướng dẫn nhanh
- [docs/IFRAME-EMBED-GUIDE.md](docs/IFRAME-EMBED-GUIDE.md) - Guide mã nhúng

---

**Last Updated:** 2025-11-03 | **Version:** 1.6.3
