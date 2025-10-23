# 📊 Hướng Dẫn Import CSV Vào WooCommerce

## ✅ File CSV Đã Sẵn Sàng

File **`anmi-products-full-import.csv`** đã được tạo thành công với:
- ✅ **40 sản phẩm** từ tất cả file SEO markdown
- ✅ Đầy đủ thông tin: SKU, tên, mô tả, giá, tồn kho
- ✅ Danh mục, tags, hình ảnh, attributes
- ✅ Encoding UTF-8 với BOM (tương thích WooCommerce)

## 📋 Thông Tin CSV

### Tổng Quan
```
📦 Tổng số sản phẩm: 40
📂 Danh mục:
   • BT SIDE LOCK: 18 sản phẩm
   • HSK: 8 sản phẩm  
   • Hệ thống gá kẹp, cán dao: 10 sản phẩm
   • COLLET: 2 sản phẩm
   • NT: 1 sản phẩm
   • BT-SHR: 1 sản phẩm

💰 Giá:
   • Thấp nhất: 800,000 VNĐ
   • Cao nhất: 3,500,000 VNĐ
   • Trung bình: 2,240,000 VNĐ

📦 Tồn kho: 3,780 sản phẩm
```

### Cấu Trúc CSV

```csv
sku,name,slug,description,short_description,regular_price,sale_price,stock_quantity,categories,tags,images,status,attributes
```

**Các cột:**
- `sku` - Mã sản phẩm duy nhất (ANMI-XXX-001)
- `name` - Tên sản phẩm đầy đủ
- `slug` - URL slug
- `description` - Mô tả chi tiết (từ file SEO)
- `short_description` - Mô tả ngắn (200 ký tự)
- `regular_price` - Giá gốc (VNĐ)
- `sale_price` - Giá khuyến mãi (giảm 10%)
- `stock_quantity` - Số lượng tồn kho
- `categories` - Danh mục (từ file SEO)
- `tags` - Tags/từ khóa (từ file SEO)
- `images` - URL hình ảnh (2 ảnh mỗi sản phẩm)
- `status` - Trạng thái (publish)
- `attributes` - Thuộc tính sản phẩm

## 🚀 Cách Import Vào WooCommerce

### Bước 1: Cài Đặt Plugin

1. Nén thư mục `woocommerce-plugin` thành `anmi-csv-importer.zip`
2. Vào **WordPress Admin → Plugins → Add New → Upload**
3. Upload file ZIP và kích hoạt plugin

### Bước 2: Truy Cập Trang Import

Vào: **WooCommerce → 📊 Nhập CSV**

### Bước 3: Upload File CSV

1. Click **"Chọn tập tin"**
2. Chọn file `anmi-products-full-import.csv`

### Bước 4: Cấu Hình Import

**Tùy chọn khuyến nghị:**
- ✅ **Cập nhật sản phẩm hiện có** - Nếu muốn cập nhật sản phẩm có SKU trùng
- ✅ **Tự động tạo danh mục** - Tạo danh mục nếu chưa có
- ✅ **Cập nhật giá** - Cập nhật giá cho sản phẩm đã tồn tại
- ✅ **Cập nhật tồn kho** - Cập nhật số lượng tồn kho

**Cấu hình CSV:**
- **Dấu phân cách:** Dấu phẩy (,)
- **Mã hóa:** UTF-8

### Bước 5: Import

1. Click **"🔍 Kiểm Tra File"** để validate (khuyến nghị)
2. Xem kết quả validation
3. Click **"🚀 Bắt Đầu Import"**
4. Chờ quá trình hoàn tất (có thể mất 2-5 phút)

### Bước 6: Kiểm Tra Kết Quả

Sau khi import xong, kiểm tra:
- ✅ Số sản phẩm đã import
- ✅ Số sản phẩm cập nhật
- ✅ Cảnh báo/lỗi (nếu có)

## 📝 Lưu Ý Quan Trọng

### 1. Backup Database
```bash
# Backup WordPress database trước khi import
mysqldump -u username -p database_name > backup.sql
```

Hoặc dùng plugin: **UpdraftPlus** hoặc **BackWPup**

### 2. Test Import Nhỏ Trước

Để test, bạn có thể:
1. Mở file CSV
2. Chỉ giữ lại 5 dòng đầu tiên (+ header)
3. Save as `test-import.csv`
4. Import file test trước
5. Kiểm tra kết quả
6. Import toàn bộ nếu OK

### 3. Hình Ảnh

**URL hình ảnh trong CSV:**
```
https://anmitools.com/wp-content/uploads/products/bt-sk-high-speed-tool-holder-01.jpg
https://anmitools.com/wp-content/uploads/products/bt-sk-high-speed-tool-holder-02.jpg
```

**Để hình ảnh hiển thị:**
- Upload hình ảnh vào thư mục `/wp-content/uploads/products/`
- Hoặc thay URL bằng đường dẫn hình ảnh thực tế
- Hoặc để trống nếu chưa có hình (thêm sau)

**Cập nhật URL hình ảnh:**
```bash
# Tìm và thay thế trong CSV
# Thay "https://anmitools.com" bằng domain thực của bạn
```

### 4. Attributes (Thuộc tính)

Format trong CSV:
```
Size:BT30,BT40,BT50|Material:Steel,Alloy Steel|Balance Grade:G2.5,G6.3
```

- Phân cách attributes: `|`
- Phân cách values: `,`
- Format: `Name:Value1,Value2|Name2:Value1,Value2`

### 5. Categories (Danh mục)

Danh mục được tạo tự động nếu chọn "Tự động tạo danh mục"

**Danh mục trong CSV:**
- BT SIDE LOCK
- HSK
- Hệ thống gá kẹp, cán dao
- COLLET
- NT
- BT-SHR

### 6. SKU Duy Nhất

Mỗi sản phẩm có SKU duy nhất:
```
ANMI-BTSKHIGHSP-001
ANMI-BTGERHIGHS-002
ANMI-BTHGERHIGH-003
...
```

Nếu import lại, sản phẩm có SKU trùng sẽ được cập nhật (nếu chọn "Cập nhật sản phẩm hiện có")

## 🔧 Tùy Chỉnh CSV

### Chỉnh Sửa Giá

Mở file CSV và tìm cột `regular_price` và `sale_price`:

```csv
1500000,1350000    # Giá gốc: 1.5 triệu, Giá sale: 1.35 triệu
```

### Chỉnh Sửa Tồn Kho

Cột `stock_quantity`:
```csv
100    # 100 sản phẩm trong kho
```

### Chỉnh Sửa Mô Tả

Cột `description` và `short_description`:
- Mô tả chi tiết: Lấy từ file SEO
- Mô tả ngắn: 200 ký tự đầu tiên

### Thêm/Sửa Hình Ảnh

Cột `images`:
```csv
"https://your-domain.com/image1.jpg, https://your-domain.com/image2.jpg"
```

- Ảnh đầu tiên = Featured Image
- Các ảnh sau = Gallery
- Phân cách bằng dấu phẩy

## 📊 Sau Khi Import

### 1. Kiểm Tra Sản Phẩm

Vào: **Products → All Products**

Kiểm tra:
- ✅ Tên sản phẩm
- ✅ Giá
- ✅ Tồn kho
- ✅ Danh mục
- ✅ Hình ảnh (có thể chưa có - cần upload riêng)

### 2. Cập Nhật Hình Ảnh

**Cách 1: Thủ công**
- Vào từng sản phẩm
- Upload hình ảnh
- Set Featured Image

**Cách 2: Bulk update**
- Sử dụng plugin: **WP All Import**
- Hoặc viết script custom

### 3. Kiểm Tra Danh Mục

Vào: **Products → Categories**

Đảm bảo tất cả danh mục đã được tạo đúng.

### 4. Kiểm Tra Attributes

Vào: **Products → Attributes**

Kiểm tra các attributes như:
- Size
- Material
- Balance Grade
- Collet Size
- Type

### 5. SEO

Sau khi import, có thể cần:
- Cài đặt **Yoast SEO** hoặc **Rank Math**
- Cập nhật meta description
- Tối ưu tiêu đề SEO

## 🐛 Xử Lý Lỗi

### Lỗi: "Maximum execution time exceeded"

**Giải pháp:**
```php
// Thêm vào wp-config.php
@ini_set('max_execution_time', '300');
```

Hoặc chia file CSV thành nhiều file nhỏ hơn (10-15 sản phẩm/file).

### Lỗi: "Memory limit exceeded"

**Giải pháp:**
```php
// Thêm vào wp-config.php
define('WP_MEMORY_LIMIT', '512M');
```

### Lỗi: Hình ảnh không hiển thị

**Giải pháp:**
1. Kiểm tra URL hình ảnh có đúng không
2. Upload hình ảnh vào đúng thư mục
3. Hoặc để trống cột images, thêm hình sau

### Lỗi: Danh mục không được tạo

**Giải pháp:**
- Đảm bảo chọn "Tự động tạo danh mục"
- Hoặc tạo danh mục thủ công trước khi import

### Lỗi: Attributes không hiển thị

**Giải pháp:**
- Kiểm tra format attributes trong CSV
- Đảm bảo dùng dấu `|` và `,` đúng
- Hoặc thêm attributes thủ công sau

## 🎯 Tái Tạo CSV (Nếu Cần)

Nếu muốn tạo lại CSV với thông tin mới:

```bash
# Chạy lại script Python
cd "e:\ANMI_Dự Án bảo trì phần mềm website AnMi\Anmi-upload-hinh\HTML-baiviet\Sản phẩm\Hệ thống gá kẹp, cán dao\woocommerce-plugin"
python generate-csv-from-seo.py
```

Script sẽ:
- Đọc lại tất cả 40 file SEO markdown
- Tạo CSV mới với dữ liệu cập nhật
- Overwrite file cũ

## 📞 Hỗ Trợ

Nếu gặp vấn đề:
- Email: support@anmitools.com
- Hotline: 1900-xxxx
- Website: https://anmitools.com

---

**Chúc bạn import thành công! 🎉**
