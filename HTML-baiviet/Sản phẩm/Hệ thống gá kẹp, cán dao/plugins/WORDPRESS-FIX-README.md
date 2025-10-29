# An Mi Product Style Injector v2.1.2

## 🎯 Giải quyết vấn đề WordPress Editor tự tạo thẻ `<p>`

### ⚠️ Vấn đề

WordPress Editor (Gutenberg/Classic Editor) có chức năng `wpautop` tự động thêm thẻ `<p>` vào HTML, gây ra vấn đề:

1. **Thẻ `<p>` sau mỗi `<div>` con của grid**
   ```html
   <div class="application-grid">
       <div class="application-card">...</div>
       <p></p>  <!-- ❌ WordPress tự thêm -->
       <div class="application-card">...</div>
       <p></p>  <!-- ❌ WordPress tự thêm -->
   </div>
   ```

2. **Thẻ `<p>` wrap HTML comments**
   ```html
   <div class="application-grid">
       <p><!-- Card 1: Gia công tốc độ cao --></p>  <!-- ❌ WordPress wrap comment -->
       <div class="application-card">...</div>
   </div>
   ```

3. **Kết quả:** Grid layout bị loạn, khoảng trống lạ, thứ tự sai

---

## ✅ Giải pháp 3 lớp bảo vệ

### **Layer 1: PHP - Tắt wpautop**
```php
// Tự động phát hiện trang sản phẩm và tắt wpautop
add_filter('the_content', 'disable_wpautop_for_products', 9);
remove_filter('the_content', 'wpautop');
```
- **Ưu điểm:** Ngăn chặn từ gốc, không tạo `<p>` từ đầu
- **Nhược điểm:** Có thể không áp dụng với một số theme/plugin khác

### **Layer 2: CSS - Ẩn thẻ `<p>` không mong muốn**
```css
/* Ẩn TẤT CẢ <p> là con trực tiếp của grid */
.application-grid > p,
.instruction-grid > p,
.feature-grid > p {
    display: none !important;
    margin: 0 !important;
    height: 0 !important;
}

/* Nhưng GIỮ LẠI <p> bên trong card (nội dung thật) */
.application-card p {
    display: block !important;
}
```
- **Ưu điểm:** Hoạt động 100%, không cần config
- **Nhược điểm:** Thẻ `<p>` vẫn tồn tại trong DOM (chỉ ẩn đi)

### **Layer 3: JavaScript - Xóa thẻ `<p>` khỏi DOM**
```javascript
// Tự động quét và xóa <p> rỗng hoặc chỉ chứa comment trong grid
function cleanupGridParagraphs() {
    document.querySelectorAll('.application-grid > p').forEach(p => {
        if (onlyContainsComments(p) || isParagraphRemovable(p)) {
            p.remove();
        }
    });
}
```
- **Ưu điểm:** DOM sạch sẽ, SEO tốt hơn, không còn dấu vết
- **Nhược điểm:** Chạy sau khi page load (vài millisecond)

---

## 📦 Cấu trúc Plugin

```
wp-content/plugins/anmi-product-style-injector/
├── anmi-product-style-injector.php  (v2.1.2)
├── css/
│   └── anmi-holder-products.css     (v1.1.5)
├── js/
│   └── grid-cleanup.js              (v1.0.0)
└── README.md
```

---

## 🚀 Cài đặt

1. **Upload plugin** vào `wp-content/plugins/anmi-product-style-injector/`
2. **Activate** trong WordPress Admin → Plugins
3. **Không cần config** - tự động hoạt động!

---

## 🔍 Cách Plugin phát hiện trang sản phẩm

Plugin tự động load CSS/JS khi phát hiện:

### **Method 1: Slug patterns trong content**
```php
'bt-', 'hsk-', 'nbh', 'nbj', 'ewn', 'rbh', 'cbh', ...
```

### **Method 2: Grid classes trong content**
```php
'.feature-grid', '.application-grid', '.instruction-grid', ...
```

### **Method 3: Category slug**
```php
'he-thong-ga-kep-can-dao'
```

---

## 🛠️ Debug Mode

Mở console và bật debug:
```javascript
window.anmiDebug = true;
```

Xem log:
```
[An Mi Grid Cleanup] Removing auto-generated <p>: <p><!-- comment --></p>
[An Mi Grid Cleanup] Removed 8 auto-generated <p> tags from grids
```

---

## 📊 Performance Impact

- **CSS:** ~50KB (cached sau lần đầu)
- **JavaScript:** ~4KB (minified: ~2KB)
- **Execution time:** < 10ms
- **HTTP requests:** +1 CSS, +1 JS
- **Overall impact:** Negligible ✅

---

## ✨ Tính năng

✅ Tự động phát hiện trang sản phẩm holder  
✅ Load CSS chung cho 40 sản phẩm (1 file duy nhất)  
✅ Tắt wpautop cho trang sản phẩm  
✅ CSS ẩn thẻ `<p>` không mong muốn  
✅ JavaScript cleanup DOM  
✅ Hỗ trợ Gutenberg & Classic Editor  
✅ Responsive: Desktop/Tablet/Mobile  
✅ Cache-friendly (file version = modification time)  
✅ Debug mode cho developers  

---

## 📝 Changelog

### v2.1.2 (2025-10-29)
- ✨ Added JavaScript cleanup to remove `<p>` tags wrapping HTML comments
- 🔧 Enhanced CSS rules with more aggressive `!important` flags
- 📚 Comprehensive documentation about WordPress Editor quirks

### v2.1.1 (2025-10-29)
- 🛠️ Added wpautop filter to prevent auto `<p>` tag generation
- 🎯 Smart detection of holder product pages

### v2.1.0 (2025-10-29)
- 🎨 Added CSS support for WordPress Editor preview

### v2.0.0 (2025-10-29)
- 🚀 Changed to single common CSS file for all 40 products
- ⚡ Improved performance with unified stylesheet

### v1.0.0 (Initial release)
- 📦 Individual CSS files per product

---

## 🤝 Hỗ trợ

**An Mi Tools Technical Team**  
📧 Email: technical@anmitools.com  
🌐 Website: https://anmitools.com  
📞 Hotline: 1900-xxxx

---

## 📄 License

GPL v2 or later  
https://www.gnu.org/licenses/gpl-2.0.html
