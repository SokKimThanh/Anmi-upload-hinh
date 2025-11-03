# 🔌 An Mi Tools - WordPress Plugins# 🎨 An Mi Product Style Injector Plugin



Collection of custom WordPress plugins for An Mi Tools website.**Version:** 2.1.6 🆕  

**CSS Version:** 1.3.2  

## 📦 Available Plugins**Last Updated:** November 2, 2025



### 1. **An Mi Video Banner** (v1.3.0)## 🚨 CRITICAL UPDATE v2.1.6

📍 **Folder:** `anmi-video-banner/`

### 🔧 Fixed: Vấn Đề Mất Thẻ `<p>` Trong WordPress Editor

**Description:** Professional video banner with image slider, hover-to-play video, and content visibility controls.

**Vấn đề:** HTML paste vào WordPress editor bị mất tất cả thẻ `<p>`, gây ra văn bản không có cấu trúc.

**Features:**

- ✅ Image slider with auto-advance**Giải pháp:**

- ✅ Hover-triggered video playback (YouTube/Vimeo/MP4)- ✅ **Giữ wpautop enabled** - WordPress có thể tự tạo `<p>` tags

- ✅ Admin CRUD panel with database storage- ✅ **JavaScript chỉ xóa `<p>` trong grid containers** - Không touch `<p>` ở nội dung thường

- ✅ Elementor widget integration- ✅ **Logic kiểm tra cải tiến** - Preserve `<p>` có text hoặc child elements

- ✅ Content visibility toggles (Title/Subtitle/Button)

- ✅ 4 transition effects (fade/zoom/blur/slide)**Chi tiết:** Xem [FIX-P-TAG-ISSUE.md](FIX-P-TAG-ISSUE.md)

- ✅ Mobile responsive

---

**Documentation:** See `anmi-video-banner/README.md`

## 📁 Cấu Trúc Plugin

---

```

### 2. **An Mi Product Style Injector** (v2.1.6)anmi-product-style-injector/

📍 **Folder:** `anmi-product-style-injector/`├── anmi-product-style-injector.php  ← Main plugin file (v2.1.6) 🆕

├── css/

**Description:** Automatically inject unified CSS styling for all holder product pages.│   └── anmi-holder-products.css     ← Product styling (v1.3.2)

├── js/

**Features:**│   ├── grid-cleanup.js              ← Grid cleanup script (v1.2.0) 🆕

- ✅ Auto-detect holder products by slug pattern│   └── image-lightbox.js            ← Image lightbox

- ✅ Unified CSS for consistent styling├── README.md                         ← This file

- ✅ WordPress Editor support (Gutenberg + Classic)├── FIX-P-TAG-ISSUE.md               ← Fix documentation 🆕

- ✅ Image lightbox (click-to-zoom)├── UPDATE-GUIDE.md                   ← Update instructions

- ✅ Grid layout cleanup (removes empty `<p>` tags)└── WORDPRESS-FIX-README.md          ← WordPress fixes

- ✅ 2-column product image grid```



**Documentation:** See `anmi-product-style-injector/README.md`## 🆕 Version 2.1.5 - What's New



---### 🖼️ Image Lightbox - Click to Zoom!



## 📁 Folder Structure**NEW Feature:** Click vào bất kỳ hình ảnh sản phẩm nào để phóng to!



```**Tính năng:**

plugins/- ✅ **Click to Zoom:** Click vào hình để xem full-screen

├── README.md                          ← This file- ✅ **Smooth Animation:** Zoom animation mượt mà

├── anmi-video-banner/- ✅ **Multiple Close Methods:** 

│   ├── anmi-video-banner.php         ← Main plugin file  - Click nút ✕ góc phải

│   ├── assets/  - Press ESC key

│   │   ├── css/  - Click vào vùng tối bên ngoài

│   │   │   ├── video-banner.css- ✅ **Mobile Friendly:** Responsive, hoạt động tốt trên mobile

│   │   │   └── admin-style.css- ✅ **Auto Caption:** Hiển thị caption từ figcaption

│   │   └── js/- ✅ **Cursor Hint:** Cursor pointer + tooltip "Click để phóng to"

│   │       ├── video-banner.js

│   │       └── admin-script.js**Applied to:**

│   ├── includes/- Tất cả images có class `.bordered-img`

│   │   ├── admin-panel.php- Images trong `figure` tags

│   │   ├── elementor-widget.php- Images trong `.product-images-grid`

│   │   └── views/

│   │       ├── admin-list.php**Technical:**

│   │       └── admin-edit.php- **CSS:** `.anmi-lightbox` với full-screen overlay (z-index: 999999)

│   ├── demo/- **JavaScript:** `image-lightbox.js` - Vanilla JS, no dependencies

│   │   ├── index.html- **Performance:** Lazy initialization, no impact on page load

│   │   ├── anmi-profile-demo.html

│   │   └── slider-demo.html### CSS v1.3.2 Features:

│   └── *.md                          ← Documentation files```css

│.anmi-lightbox {

└── anmi-product-style-injector/    background: rgba(0, 0, 0, 0.9);

    ├── anmi-product-style-injector.php ← Main plugin file    animation: zoom 0.3s;

    ├── assets/}

    │   ├── css/```

    │   │   └── anmi-holder-products.css

    │   └── js/## 📋 Previous Updates

    │       ├── grid-cleanup.js

    │       └── image-lightbox.js### v1.3.1 (2025-11-01):

    └── *.md                            ← Documentation files- ✅ **Product Images Grid:** `.product-images-grid` class for 2-column image layout

```- ✅ **Responsive:** 2 columns (desktop) → 1 column (mobile)

- ✅ **Applied to:** NT-CK section (NBJ16 head + tool box set)

## 🚀 Installation

## 📦 Upload Lên WordPress

### Method 1: FTP Upload (Recommended)

### Cách 1: Upload Qua FTP (Khuyên Dùng)

1. **Connect FTP** to WordPress server

2. **Navigate** to `/wp-content/plugins/`1. **Kết nối FTP** đến server WordPress

3. **Upload entire folder** (e.g., `anmi-video-banner/`)2. **Navigate** đến: `/wp-content/plugins/`

4. **Go to:** WordPress Admin → Plugins3. **Upload toàn bộ thư mục** `anmi-product-style-injector/`

5. **Click:** "Activate"4. Cấu trúc sau khi upload:



### Method 2: ZIP Upload```

/wp-content/plugins/

1. **Compress** plugin folder to `.zip`└── anmi-product-style-injector/

2. **Go to:** Plugins → Add New → Upload Plugin    ├── anmi-product-style-injector.php

3. **Upload** and activate    ├── css/

    │   └── anmi-holder-products.css

## 📊 Plugin Status    ├── README.md

    └── UPDATE-GUIDE.md

| Plugin | Version | Status | Last Updated |```

|--------|---------|--------|--------------|

| Video Banner | v1.3.0 | ✅ Active | Nov 3, 2025 |5. **Set permissions:**

| Style Injector | v2.1.6 | ✅ Active | Nov 2, 2025 |   ```bash

   chmod -R 755 /wp-content/plugins/anmi-product-style-injector/

## 🔗 Quick Links   chmod 644 /wp-content/plugins/anmi-product-style-injector/*.php

   chmod 644 /wp-content/plugins/anmi-product-style-injector/css/*.css

- **Video Banner Docs:** [anmi-video-banner/README.md](anmi-video-banner/README.md)   ```

- **Style Injector Docs:** [anmi-product-style-injector/README.md](anmi-product-style-injector/README.md)

- **Website:** https://anmitools.com6. **Activate plugin:**

   - Vào WordPress Admin: **Plugins → Installed Plugins**

## 📞 Support   - Tìm: **"An Mi Tools - Product Style Injector"**

   - Click: **"Activate"**

- **Email:** support@anmitools.com

- **Website:** https://anmitools.com/contact-us/### Cách 2: Upload Qua WordPress Admin



---1. **Nén plugin thành ZIP:**

   - Nén thư mục `anmi-product-style-injector/`

**Last Updated:** November 3, 2025   - Tên file: `anmi-product-style-injector.zip`


2. **Upload ZIP:**
   - Vào: **Plugins → Add New → Upload Plugin**
   - Choose file: `anmi-product-style-injector.zip`
   - Click: **"Install Now"**
   - Click: **"Activate"**

## ✅ Kiểm Tra Plugin Hoạt Động

### Test CSS Path

1. **View source** bất kỳ trang sản phẩm holder nào
2. Tìm dòng:
   ```html
   <link rel='stylesheet' id='anmi-holder-products-css'
         href='https://anmitools.com/wp-content/plugins/anmi-product-style-injector/css/anmi-holder-products.css?ver=2.1.5'
         media='all' />
   <script src='https://anmitools.com/wp-content/plugins/anmi-product-style-injector/js/image-lightbox.js?ver=2.1.5'></script>
   ```

3. **Click vào URL CSS** - phải load được file (status 200)

### Test Frontend Styling

1. Mở sản phẩm holder bất kỳ (VD: `er-high-precision-collet`)
2. Kiểm tra:
   - ✅ Background màu be (#FCF7EC)
   - ✅ Sections có styling
   - ✅ Typography đúng
   - ✅ Colors, spacing đúng

### Test Editor Styling

1. Edit sản phẩm holder trong WordPress
2. Kiểm tra editor:
   - ✅ Background màu be
   - ✅ Preview giống frontend
   - ✅ WYSIWYG - What You See Is What You Get

## 🐛 Troubleshooting

### CSS không load (404 Not Found)

**Nguyên nhân:** File path sai

**Giải pháp:**
```bash
# Kiểm tra file tồn tại
ls -la /wp-content/plugins/anmi-product-style-injector/css/anmi-holder-products.css

# Nếu không có, upload lại file CSS
```

### CSS không hiển thị

**Nguyên nhân:** Cache

**Giải pháp:**
```bash
# Clear WordPress cache
wp cache flush

# Clear browser cache
Ctrl + Shift + Delete

# Hard refresh
Ctrl + Shift + R
```

### CSS bị override bởi theme

**Nguyên nhân:** Theme CSS có higher specificity

**Giải pháp:** Thêm `!important` vào CSS:
```css
.editor-styles-wrapper {
    background-color: #FCF7EC !important;
}
```

## 📝 Plugin Info

- **Version:** 2.1.0
- **Author:** An Mi Tools
- **Requires:** WordPress 5.0+
- **Requires PHP:** 7.2+
- **License:** GPL v2 or later

## 🔗 URLs Đúng

**Frontend CSS:**
```
https://anmitools.com/wp-content/plugins/anmi-product-style-injector/css/anmi-holder-products.css?ver=2.1.0
```

**Editor CSS (Same file, loaded via different hook):**
```
https://anmitools.com/wp-content/plugins/anmi-product-style-injector/css/anmi-holder-products.css?ver=2.1.0
```

## 📞 Support

- Email: support@anmitools.com
- Hotline: 1900-xxxx
- Website: https://anmitools.com

---

**Last Updated:** October 23, 2025
