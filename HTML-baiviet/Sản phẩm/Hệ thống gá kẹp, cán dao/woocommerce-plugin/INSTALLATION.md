# 🚀 HƯỚNG DẪN CÀI ĐẶT NHANH

## Bước 1: Chuẩn Bị

1. Đảm bảo đã cài đặt **WordPress** và **WooCommerce**
2. Tải toàn bộ thư mục `woocommerce-plugin`

## Bước 2: Cài Đặt Plugin

### Cách A: Qua WordPress Admin (Khuyến nghị)

```bash
# 1. Nén thư mục thành ZIP
zip -r anmi-csv-importer.zip woocommerce-plugin/

# 2. Vào WordPress Admin
# → Plugins → Add New → Upload Plugin
# 3. Chọn file anmi-csv-importer.zip
# 4. Click "Install Now" → "Activate"
```

### Cách B: Qua FTP/SSH

```bash
# 1. Upload thư mục qua FTP đến:
/wp-content/plugins/

# 2. Đổi tên thư mục
mv woocommerce-plugin anmi-csv-importer

# 3. Cấp quyền
chmod -R 755 anmi-csv-importer/

# 4. Vào WordPress Admin → Plugins → Activate
```

## Bước 3: Sử Dụng

1. Vào: **WooCommerce → 📊 Nhập CSV**
2. Click **"📥 Tải File Mẫu CSV"**
3. Chuẩn bị file CSV theo mẫu
4. Upload và Import

## 📋 Cấu Trúc Thư Mục Plugin

```
woocommerce-plugin/
├── anmi-csv-importer.php          # File chính
├── README.md                       # Tài liệu đầy đủ
├── INSTALLATION.md                 # File này
├── sample-import.csv               # File CSV mẫu
├── includes/
│   ├── admin-page.php             # Giao diện admin
│   ├── class-product-importer.php # Class import
│   ├── class-csv-processor.php    # Class xử lý CSV
│   └── class-csv-validator.php    # Class validate
└── assets/
    ├── css/
    │   └── admin.css              # Style admin
    └── js/
        └── admin.js               # JavaScript admin
```

## ✅ Kiểm Tra Cài Đặt

Sau khi cài đặt, kiểm tra:

- [ ] Plugin xuất hiện trong danh sách Plugins
- [ ] Có thể Activate plugin
- [ ] Menu **"📊 Nhập CSV"** xuất hiện trong WooCommerce
- [ ] Có thể mở trang import
- [ ] Có thể tải file mẫu CSV

## ⚙️ Yêu Cầu Hệ Thống

- WordPress: 5.8+
- WooCommerce: 5.0+
- PHP: 7.4+
- MySQL: 5.6+
- Memory limit: 256MB+
- Max execution time: 300s+
- Upload max size: 64MB+

## 🔧 Tăng Giới Hạn Upload (Nếu Cần)

### Trong wp-config.php
```php
@ini_set('upload_max_size', '256M');
@ini_set('post_max_size', '256M');
@ini_set('max_execution_time', '300');
@ini_set('memory_limit', '512M');
```

### Trong .htaccess
```apache
php_value upload_max_filesize 256M
php_value post_max_size 256M
php_value max_execution_time 300
php_value memory_limit 512M
```

## 🐛 Xử Lý Lỗi

### Lỗi: Plugin không hiển thị
```bash
# Kiểm tra quyền thư mục
chmod -R 755 /wp-content/plugins/anmi-csv-importer/
chown -R www-data:www-data /wp-content/plugins/anmi-csv-importer/
```

### Lỗi: Cannot activate plugin
```bash
# Kiểm tra log lỗi
tail -f /var/log/apache2/error.log
# hoặc
tail -f /var/log/nginx/error.log
```

### Lỗi: White screen
```php
// Thêm vào wp-config.php để debug
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## 📞 Hỗ Trợ

Nếu gặp vấn đề:
- Email: support@anmitools.com
- Hotline: 1900-xxxx
- Website: https://anmitools.com

---

**Happy Importing! 🎉**
