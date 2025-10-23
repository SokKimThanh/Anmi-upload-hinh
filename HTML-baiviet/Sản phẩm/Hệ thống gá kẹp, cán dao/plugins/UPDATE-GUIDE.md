# 🎨 Cập Nhật Plugin - An Mi Product Style Injector v2.1.0

## ✨ Tính Năng Mới

### ✅ CSS Hiển Thị Trong WordPress Editor

Plugin đã được cập nhật để **tự động load CSS** vào WordPress Editor (cả Gutenberg và Classic Editor).

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
3. Kiểm tra version: **2.1.0**

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
