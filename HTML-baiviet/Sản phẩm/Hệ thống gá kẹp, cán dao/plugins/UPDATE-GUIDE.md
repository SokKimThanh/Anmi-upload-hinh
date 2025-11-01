# 🎨 Cập Nhật Plugin - An Mi Product Style Injector v2.1.5

## ✨ Tính Năng Mới - Version 2.1.5

### 🖼️ Image Lightbox - Click to Zoom!

Plugin đã được cập nhật với **Image Lightbox** cho phép phóng to hình ảnh sản phẩm.

**Tính năng:**
- ✅ Click vào bất kỳ hình ảnh sản phẩm nào để phóng to full-screen
- ✅ Smooth zoom animation (0.3s)
- ✅ 3 cách đóng lightbox:
  - Click nút ✕ (góc phải trên)
  - Press phím ESC
  - Click vào vùng tối bên ngoài hình
- ✅ Hiển thị caption tự động từ figcaption
- ✅ Cursor pointer với tooltip "Click để phóng to"
- ✅ Mobile responsive (max-width 95%, max-height 70vh)

**Technical Implementation:**
```javascript
// Tự động áp dụng cho tất cả images có class .bordered-img
const productImages = document.querySelectorAll('figure img.bordered-img, .product-images-grid img.bordered-img');
```

**CSS Lightbox Styles:**
```css
.anmi-lightbox {
    position: fixed;
    z-index: 999999;
    background: rgba(0, 0, 0, 0.9);
    animation: zoom 0.3s;
}
```

**Files Added:**
- `js/image-lightbox.js` - Vanilla JavaScript lightbox (no dependencies)
- CSS trong `anmi-holder-products.css` (v1.3.2)

## 📋 Changelog Tổng Hợp

### v2.1.5 (November 1, 2025)
- **CSS v1.3.2:** Added image lightbox functionality
- **JavaScript:** image-lightbox.js for click-to-zoom
- **Feature:** Full-screen image preview with smooth animation
- **UX:** Multiple close methods (X button, ESC, outside click)

### v2.1.4 (November 1, 2025)
- **CSS v1.3.1:** Added `.product-images-grid` for 2-column image layout
- **Feature:** Responsive behavior: 2 columns → 1 column on mobile
- **Applied to:** NT-CK section (NBJ16 head + tool box)

### v2.1.3 (October 31, 2025)
- Fixed: PRESERVE `<p>` tags with actual content
- Only remove `<p>` tags with comments or empty content

### v2.1.2 (October 30, 2025)
- Added JavaScript cleanup for `<p>` tags wrapping HTML comments

### v2.1.1 (October 30, 2025)
- Added wpautop filter to prevent auto-generated `<p>` tags

### v2.1.0 (October 29, 2025)
- **CSS v1.3.0:** WordPress Editor Integration
- CSS hiển thị trong Gutenberg & Classic Editor
- WYSIWYG preview trước khi publish

### v2.0.0
- Changed to single common CSS file
- Better performance and maintainability

## ✅ CSS Hiển Thị Trong WordPress Editor

**Trước đây:**
- ❌ CSS chỉ hiển thị ở frontend
- ❌ Editor không có styling → khó preview
- ❌ Phải publish rồi mới xem được CSS

**Bây giờ:**
- ✅ CSS hiển thị ngay trong editor
- ✅ WYSIWYG - What You See Is What You Get
- ✅ Preview chính xác trước khi publish
- ✅ Tự động áp dụng cho tất cả sản phẩm holder

## 🚀 Cách Cập Nhật

### Bước 1: Tắt Plugin Cũ

1. Vào: **Plugins → Installed Plugins**
2. Tìm: **"An Mi Tools - Product Style Injector"**
3. Click: **"Deactivate"**

### Bước 2: Xóa File Cũ (Nếu Upload Qua FTP)

Nếu bạn upload plugin qua FTP:

```bash
# Xóa thư mục plugin cũ
rm -rf /wp-content/plugins/anmi-product-style-injector/
```

Hoặc dùng FTP client để xóa thư mục `anmi-product-style-injector`

### Bước 3: Upload Plugin Mới

**Cách 1: Qua WordPress Admin**

1. Vào: **Plugins → Add New → Upload Plugin**
2. Choose File: Chọn file ZIP plugin mới
3. Click: **"Install Now"**
4. Click: **"Activate"**

**Cách 2: Qua FTP**

```bash
# Upload thư mục plugin vào
/wp-content/plugins/anmi-product-style-injector/

# Cấp quyền
chmod -R 755 /wp-content/plugins/anmi-product-style-injector/
```

### Bước 4: Kích Hoạt Plugin

1. Vào: **Plugins → Installed Plugins**
2. Tìm: **"An Mi Tools - Product Style Injector"**
3. Click: **"Activate"**

### Bước 5: Clear Cache

**Clear WordPress Cache:**
- Vào: **Settings → Optimization** (nếu có plugin cache)
- Click: **"Clear All Cache"**

**Clear Browser Cache:**
- Chrome: `Ctrl + Shift + Delete`
- Firefox: `Ctrl + Shift + Delete`

## ✅ Kiểm Tra Plugin Hoạt Động

### Test 1: Kiểm Tra Version

1. Vào: **Plugins → Installed Plugins**
2. Tìm: **"An Mi Tools - Product Style Injector"**
3. Kiểm tra version: **2.1.5**

### Test 2: Kiểm Tra Image Lightbox

1. Mở bất kỳ trang sản phẩm holder nào
2. **Click vào hình ảnh sản phẩm**
3. Kiểm tra:
   - ✅ Hình phóng to full-screen với nền đen trong suốt
   - ✅ Animation zoom mượt mà (0.3s)
   - ✅ Caption hiển thị bên dưới hình
   - ✅ Nút ✕ ở góc phải trên
   - ✅ Click ✕ hoặc ESC hoặc outside để đóng
   - ✅ Cursor pointer khi hover vào hình

### Test 3: Kiểm Tra Mobile

1. Mở DevTools (F12) → Device Toolbar
2. Chọn device: iPhone 12 Pro hoặc tương tự
3. Click vào hình ảnh
4. Kiểm tra:
   - ✅ Hình fit màn hình mobile (max-width 95%)
   - ✅ Nút ✕ vẫn click được dễ dàng
   - ✅ Caption dễ đọc

### Test 2: Kiểm Tra CSS Trong Editor

1. Vào: **Products → All Products**
2. Chọn 1 sản phẩm holder (VD: BT-SK High Speed Tool Holder)
3. Click **"Edit"**
4. Trong editor:
   - ✅ Background màu be (#FCF7EC)
   - ✅ Font styling đúng
   - ✅ Section styling hiển thị
   - ✅ Colors, spacing đúng như frontend

### Test 3: Kiểm Tra Frontend

1. View sản phẩm ở frontend
2. Đảm bảo CSS vẫn hoạt động bình thường
3. So sánh với editor - phải giống nhau

## 📋 File Đã Thay Đổi

```
plugins/
└── anmi-product-style-injector.php
    ├── Version: 2.0.1 → 2.1.0
    ├── Added: enqueue_editor_styles() function
    ├── Added: Editor CSS support
    └── Updated: init_hooks() method
```

## 🎯 Sản Phẩm Được Áp Dụng

Plugin tự động phát hiện và load CSS cho:

### ✅ Theo Post Type:
- WooCommerce Products (`post_type = 'product'`)

### ✅ Theo Slug Pattern:
- `bt-*` (BT holders)
- `hsk-*` (HSK holders)  
- `nbh*`, `nbj*` (Boring systems)
- `ewn*`, `rbh*`, `cbh*` (Boring heads)
- `bst*`, `ck-*`, `lbk*` (Boring tools)
- `cbs*`, `sb-*`, `gc-*` (Cutters)
- `er-*`, `sk-*` (Collets)
- `nt-*` (NT system)

### ✅ Theo Category:
- Slug: `he-thong-ga-kep-can-dao`
- Slug chứa: `holder`, `ga-kep`

## 🔍 Debug Mode

Nếu CSS không load trong editor, bật debug mode:

```php
// Thêm vào wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check log tại: `/wp-content/debug.log`

Tìm dòng:
```
An Mi Product Style Injector: Loaded CSS in editor for post ID: XXX
```

## 🐛 Troubleshooting

### CSS không hiển thị trong editor?

**Giải pháp 1: Clear cache**
```bash
# Clear WordPress cache
wp cache flush

# Clear browser cache
Ctrl + Shift + Delete
```

**Giải pháp 2: Kiểm tra file CSS tồn tại**
```bash
# Check file path
ls -la /wp-content/plugins/anmi-product-style-injector/../css/anmi-holder-products.css
```

**Giải pháp 3: Reactivate plugin**
1. Deactivate plugin
2. Activate lại
3. Clear cache
4. Refresh editor

### CSS hiển thị sai trong editor?

**Nguyên nhân:** Theme CSS override

**Giải pháp:** Thêm `!important` vào CSS nếu cần:
```css
.editor-styles-wrapper {
    background-color: #FCF7EC !important;
}
```

## 📞 Hỗ Trợ

Nếu gặp vấn đề:
- Email: support@anmitools.com
- Hotline: 1900-xxxx
- Website: https://anmitools.com

---

**Version:** 2.1.0  
**Date:** October 23, 2025  
**Author:** An Mi Tools Technical Team
