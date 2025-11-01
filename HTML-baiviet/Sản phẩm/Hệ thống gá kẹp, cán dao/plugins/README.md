# 🎨 An Mi Product Style Injector Plugin

**Version:** 2.1.5  
**CSS Version:** 1.3.2  
**Last Updated:** November 1, 2025

## 📁 Cấu Trúc Plugin

```
anmi-product-style-injector/
├── anmi-product-style-injector.php  ← Main plugin file (v2.1.5)
├── css/
│   └── anmi-holder-products.css     ← Product styling (v1.3.2)
├── js/
│   ├── grid-cleanup.js              ← Grid cleanup script
│   └── image-lightbox.js            ← Image lightbox (NEW!)
├── README.md                         ← This file
├── UPDATE-GUIDE.md                   ← Update instructions
└── WORDPRESS-FIX-README.md          ← WordPress fixes
```

## 🆕 Version 2.1.5 - What's New

### 🖼️ Image Lightbox - Click to Zoom!

**NEW Feature:** Click vào bất kỳ hình ảnh sản phẩm nào để phóng to!

**Tính năng:**
- ✅ **Click to Zoom:** Click vào hình để xem full-screen
- ✅ **Smooth Animation:** Zoom animation mượt mà
- ✅ **Multiple Close Methods:** 
  - Click nút ✕ góc phải
  - Press ESC key
  - Click vào vùng tối bên ngoài
- ✅ **Mobile Friendly:** Responsive, hoạt động tốt trên mobile
- ✅ **Auto Caption:** Hiển thị caption từ figcaption
- ✅ **Cursor Hint:** Cursor pointer + tooltip "Click để phóng to"

**Applied to:**
- Tất cả images có class `.bordered-img`
- Images trong `figure` tags
- Images trong `.product-images-grid`

**Technical:**
- **CSS:** `.anmi-lightbox` với full-screen overlay (z-index: 999999)
- **JavaScript:** `image-lightbox.js` - Vanilla JS, no dependencies
- **Performance:** Lazy initialization, no impact on page load

### CSS v1.3.2 Features:
```css
.anmi-lightbox {
    background: rgba(0, 0, 0, 0.9);
    animation: zoom 0.3s;
}
```

## 📋 Previous Updates

### v1.3.1 (2025-11-01):
- ✅ **Product Images Grid:** `.product-images-grid` class for 2-column image layout
- ✅ **Responsive:** 2 columns (desktop) → 1 column (mobile)
- ✅ **Applied to:** NT-CK section (NBJ16 head + tool box set)

## 📦 Upload Lên WordPress

### Cách 1: Upload Qua FTP (Khuyên Dùng)

1. **Kết nối FTP** đến server WordPress
2. **Navigate** đến: `/wp-content/plugins/`
3. **Upload toàn bộ thư mục** `anmi-product-style-injector/`
4. Cấu trúc sau khi upload:

```
/wp-content/plugins/
└── anmi-product-style-injector/
    ├── anmi-product-style-injector.php
    ├── css/
    │   └── anmi-holder-products.css
    ├── README.md
    └── UPDATE-GUIDE.md
```

5. **Set permissions:**
   ```bash
   chmod -R 755 /wp-content/plugins/anmi-product-style-injector/
   chmod 644 /wp-content/plugins/anmi-product-style-injector/*.php
   chmod 644 /wp-content/plugins/anmi-product-style-injector/css/*.css
   ```

6. **Activate plugin:**
   - Vào WordPress Admin: **Plugins → Installed Plugins**
   - Tìm: **"An Mi Tools - Product Style Injector"**
   - Click: **"Activate"**

### Cách 2: Upload Qua WordPress Admin

1. **Nén plugin thành ZIP:**
   - Nén thư mục `anmi-product-style-injector/`
   - Tên file: `anmi-product-style-injector.zip`

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
