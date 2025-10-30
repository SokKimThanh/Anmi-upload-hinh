# 🚀 HƯỚNG DẪN UPLOAD PLUGIN - NHANH

## ⚡ Bước 1: Xóa Plugin Cũ

1. Vào WordPress Admin: **Plugins → Installed Plugins**
2. Tìm: **"An Mi Tools - Product Style Injector"**
3. Click: **"Deactivate"** → **"Delete"**
4. Xác nhận xóa

## 📦 Bước 2: Upload Plugin Mới

### Qua WordPress Admin (KHUYÊN DÙNG)

1. Vào: **Plugins → Add New → Upload Plugin**
2. Click: **"Choose File"**
3. Chọn file: **`anmi-product-style-injector.zip`** (11KB)
4. Click: **"Install Now"**
5. Chờ upload hoàn tất
6. Click: **"Activate Plugin"**

### Qua FTP (Nếu upload WordPress bị lỗi)

```bash
# Upload qua FTP/sFTP
Local:  anmi-product-style-injector.zip
Remote: /wp-content/plugins/

# Giải nén trên server
cd /wp-content/plugins/
unzip anmi-product-style-injector.zip
rm anmi-product-style-injector.zip

# Set permissions
chmod -R 755 anmi-product-style-injector/
```

## ✅ Bước 3: Kiểm Tra

### Test 1: View Source

1. Mở trang sản phẩm: https://anmitools.com/san-pham/er-high-precision-collet/
2. **View Page Source** (Ctrl + U)
3. Tìm dòng CSS:

```html
<link rel='stylesheet' id='anmi-holder-products-css'
      href='https://anmitools.com/wp-content/plugins/anmi-product-style-injector/css/anmi-holder-products.css?ver=2.1.0'
      media='all' />
```

4. **Click vào URL CSS** - Phải thấy nội dung CSS (không phải 404)

### Test 2: Check Network Tab

1. Mở **DevTools** (F12)
2. Tab **Network**
3. Filter: **CSS**
4. Refresh trang (F5)
5. Tìm: `anmi-holder-products.css?ver=2.1.0`
6. **Status phải là: 200 OK** (KHÔNG PHẢI 301 hay 404)

### Test 3: Xem Frontend

1. Mở sản phẩm: https://anmitools.com/san-pham/er-high-precision-collet/
2. Kiểm tra styling:
   - ✅ Background màu be (#FCF7EC)
   - ✅ Sections có border, padding
   - ✅ Font, colors đúng

### Test 4: Xem Editor

1. **Edit** sản phẩm trong WordPress
2. **Gutenberg Editor** hoặc **Classic Editor**
3. Kiểm tra:
   - ✅ Background màu be trong editor
   - ✅ Preview giống frontend
   - ✅ WYSIWYG styling

## 🎯 Expected Results

### ✅ URL CSS Đúng

**Frontend:**
```
https://anmitools.com/wp-content/plugins/anmi-product-style-injector/css/anmi-holder-products.css?ver=2.1.0
Status: 200 OK
```

**Editor (Same file):**
```
https://anmitools.com/wp-content/plugins/anmi-product-style-injector/css/anmi-holder-products.css?ver=2.1.0
Status: 200 OK
```

### ❌ URL CSS Sai (Trước Khi Fix)

```
https://anmitools.com/wp-content/plugins/css/anmi-holder-products.css?ver=2.1.0
Status: 301 Moved Permanently (REDIRECT TO HOMEPAGE)
```

## 🐛 Nếu Vẫn Lỗi

### CSS vẫn 404?

```bash
# SSH vào server, check file tồn tại
ls -la /path/to/wp-content/plugins/anmi-product-style-injector/css/

# Output phải có:
# -rw-r--r-- 1 user group 15234 Oct 23 09:00 anmi-holder-products.css
```

### CSS vẫn redirect 301?

1. **Clear cache:**
   - WordPress cache
   - CDN cache (Cloudflare, etc)
   - Browser cache

2. **Check .htaccess:**
   ```apache
   # Đảm bảo không có rule redirect /plugins/
   ```

3. **Hard refresh:**
   ```
   Ctrl + Shift + R (Chrome/Edge)
   Ctrl + F5 (Firefox)
   ```

## 📞 Done!

Sau khi hoàn thành các bước trên:

✅ CSS load đúng URL  
✅ Frontend có styling  
✅ Editor có styling  
✅ WYSIWYG hoàn hảo

---

**File:** `anmi-product-style-injector.zip` (11KB)  
**Version:** 2.1.0  
**Date:** October 23, 2025
