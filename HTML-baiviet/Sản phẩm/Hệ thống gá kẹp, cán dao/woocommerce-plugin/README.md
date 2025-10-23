# An Mi Tools - CSV Product Importer for WooCommerce

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.8+-green.svg)
![WooCommerce](https://img.shields.io/badge/WooCommerce-5.0+-purple.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4.svg)

## 📋 Mô Tả

Plugin WordPress cho phép nhập/cập nhật hàng loạt sản phẩm WooCommerce từ file CSV với hỗ trợ đầy đủ tiếng Việt.

## ✨ Tính Năng

- ✅ **Nhập hàng loạt** sản phẩm từ CSV
- ✅ **Cập nhật** sản phẩm hiện có
- ✅ **Hỗ trợ tiếng Việt** đầy đủ (tên cột, dữ liệu)
- ✅ **Auto-detect encoding** (UTF-8, UTF-16, Windows-1252, ISO-8859-1)
- ✅ **Validate** file trước khi import
- ✅ **Tạo danh mục** tự động
- ✅ **Upload hình ảnh** từ URL
- ✅ **Attributes** và variations
- ✅ **Progress tracking** real-time
- ✅ **Chi tiết thống kê** sau import
- ✅ **Export template** CSV chuẩn

## 📦 Cài Đặt

### Cách 1: Upload qua WordPress Admin

1. Tải toàn bộ thư mục `woocommerce-plugin`
2. Nén thành file ZIP: `anmi-csv-importer.zip`
3. Vào WordPress Admin → Plugins → Add New → Upload Plugin
4. Chọn file ZIP và click "Install Now"
5. Sau khi cài đặt, click "Activate"

### Cách 2: Upload qua FTP

1. Upload thư mục `woocommerce-plugin` vào `/wp-content/plugins/`
2. Đổi tên thư mục thành `anmi-csv-importer`
3. Vào WordPress Admin → Plugins
4. Tìm "An Mi Tools - CSV Product Importer"
5. Click "Activate"

### Cách 3: Giải nén trực tiếp trên server

```bash
cd /path/to/wordpress/wp-content/plugins/
# Upload và giải nén
unzip anmi-csv-importer.zip
# Hoặc copy thư mục
cp -r /path/to/woocommerce-plugin ./anmi-csv-importer
# Cấp quyền
chmod -R 755 anmi-csv-importer/
```

## 🚀 Sử Dụng

### Bước 1: Truy cập Plugin

Sau khi kích hoạt plugin, vào:
```
WooCommerce → 📊 Nhập CSV
```

### Bước 2: Tải File Mẫu

Click nút **"📥 Tải File Mẫu CSV"** để download template với cấu trúc chuẩn.

### Bước 3: Chuẩn Bị File CSV

#### Cấu trúc file CSV:

```csv
sku,name,slug,description,short_description,regular_price,sale_price,stock_quantity,categories,tags,images,status,attributes
BT-SK-001,BT-SK High Speed Tool Holder,bt-sk-high-speed-tool-holder,"Mô tả chi tiết...","Mô tả ngắn",1500000,1350000,100,"BT SIDE LOCK, Tool Holders","bt-sk, high speed",https://example.com/image1.jpg,publish,"Size:BT30,BT40,BT50|Material:Steel"
```

#### Các cột bắt buộc:
- `name` - Tên sản phẩm

#### Các cột khuyến nghị:
- `sku` - Mã sản phẩm (duy nhất)
- `slug` - URL slug
- `regular_price` - Giá gốc
- `sale_price` - Giá khuyến mãi
- `stock_quantity` - Số lượng tồn kho
- `categories` - Danh mục (phân cách bằng dấu phẩy)
- `tags` - Thẻ (phân cách bằng dấu phẩy)
- `description` - Mô tả chi tiết
- `short_description` - Mô tả ngắn
- `images` - URL hình ảnh (phân cách bằng dấu phẩy, ảnh đầu tiên là ảnh chính)
- `status` - Trạng thái (publish/draft/pending/private)
- `attributes` - Thuộc tính (format: Name:Value1,Value2|Name2:Value1)

#### Hỗ trợ tên cột tiếng Việt:
```csv
mã,tên,giá,tồn kho,danh mục,thẻ
BT-SK-001,Gá Kẹp BT-SK,1500000,100,Gá Kẹp,bt-sk
```

### Bước 4: Import

1. Chọn file CSV
2. Cấu hình tùy chọn:
   - ✅ Cập nhật sản phẩm hiện có
   - ✅ Tự động tạo danh mục
   - ✅ Cập nhật giá
   - ✅ Cập nhật tồn kho
3. Chọn dấu phân cách (mặc định: dấu phẩy)
4. Chọn mã hóa (khuyến nghị: UTF-8)
5. Click **"🔍 Kiểm Tra File"** để validate (khuyến nghị)
6. Click **"🚀 Bắt Đầu Import"**

### Bước 5: Kiểm Tra Kết Quả

Sau khi import, plugin sẽ hiển thị:
- ✅ Số sản phẩm nhập mới
- ✅ Số sản phẩm cập nhật
- ✅ Số sản phẩm bỏ qua
- ⚠️ Cảnh báo (nếu có)
- ❌ Lỗi (nếu có)

## 🔧 Cấu Hình Nâng Cao

### Tăng giới hạn upload

Nếu file CSV lớn, thêm vào `wp-config.php`:

```php
@ini_set('upload_max_size', '256M');
@ini_set('post_max_size', '256M');
@ini_set('max_execution_time', '300');
@ini_set('memory_limit', '512M');
```

Hoặc trong `.htaccess`:

```apache
php_value upload_max_filesize 256M
php_value post_max_size 256M
php_value max_execution_time 300
php_value memory_limit 512M
```

### Custom delimiter

Plugin tự động phát hiện các delimiter phổ biến:
- `,` (Dấu phẩy) - Mặc định
- `;` (Dấu chấm phẩy)
- `\t` (Tab)
- `|` (Dấu gạch đứng)

## 📊 Format Đặc Biệt

### Categories (Danh mục)
```csv
categories
"BT SIDE LOCK, Tool Holders, High Speed"
```

### Tags (Thẻ)
```csv
tags
"bt-sk, high speed, tool holder, cnc"
```

### Images (Hình ảnh)
```csv
images
"https://example.com/image1.jpg, https://example.com/image2.jpg, https://example.com/image3.jpg"
```
- Ảnh đầu tiên = Ảnh chính
- Các ảnh sau = Ảnh gallery

### Attributes (Thuộc tính)
```csv
attributes
"Size:BT30,BT40,BT50|Material:Steel,Aluminum|Color:Black,Silver"
```
Format: `Name:Value1,Value2|Name2:Value1,Value2`

### Price (Giá)
Các format được hỗ trợ:
```csv
regular_price
1500000
1,500,000
1.500.000
1500000₫
1500000 VND
```

### Status (Trạng thái)
```csv
status
publish      # Công khai
draft        # Nháp
pending      # Chờ duyệt
private      # Riêng tư
```

## 🛡️ Bảo Mật

- ✅ Nonce verification cho mọi AJAX request
- ✅ Capability checking (require `manage_woocommerce`)
- ✅ File type validation
- ✅ File size validation
- ✅ SQL injection prevention (sử dụng WooCommerce API)
- ✅ XSS prevention (sanitize tất cả input)

## 🐛 Troubleshooting

### Lỗi: "WooCommerce missing"
**Giải pháp**: Cài đặt và kích hoạt WooCommerce plugin

### Lỗi: "Maximum upload size exceeded"
**Giải pháp**: Tăng giới hạn upload trong `wp-config.php` hoặc `.htaccess`

### Lỗi: "Maximum execution time exceeded"
**Giải pháp**: 
- Tăng `max_execution_time` trong PHP config
- Chia nhỏ file CSV thành nhiều file nhỏ hơn

### Lỗi: Encoding (hiển thị ký tự lỗi)
**Giải pháp**: 
- Chọn encoding phù hợp (UTF-8 cho tiếng Việt)
- Hoặc chọn "Tự động phát hiện"

### Sản phẩm không hiển thị ảnh
**Giải pháp**:
- Đảm bảo URL hình ảnh là đường dẫn đầy đủ (http://...)
- Kiểm tra quyền truy cập URL
- Kiểm tra thư mục uploads có quyền write

### Danh mục không được tạo
**Giải pháp**: Tích chọn "Tự động tạo danh mục"

## 📝 Changelog

### Version 1.0.0 (2025-10-23)
- 🎉 Initial release
- ✅ CSV import with Vietnamese support
- ✅ Update existing products
- ✅ Auto-create categories
- ✅ Image upload from URL
- ✅ Attributes support
- ✅ Validation before import
- ✅ Export CSV template

## 🤝 Hỗ Trợ

- **Website**: https://anmitools.com
- **Email**: support@anmitools.com
- **Hotline**: 1900-xxxx

## 📄 License

GPL v2 or later

## 👨‍💻 Author

An Mi Tools Technical Team

---

**Made with ❤️ for An Mi Tools**
