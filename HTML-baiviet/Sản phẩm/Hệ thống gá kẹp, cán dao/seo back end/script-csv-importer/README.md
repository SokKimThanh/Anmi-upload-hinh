# 📊 Hướng Dẫn Sử Dụng CSV Importer - An Mi Tools

## 🎯 Tổng Quan

CSV Importer là công cụ nhập/cập nhật hàng loạt sản phẩm vào hệ thống An Mi Tools từ file CSV hoặc TXT.

## 🚀 Cách Sử Dụng

### 1. Truy cập công cụ

Mở trình duyệt và truy cập:
```
http://your-domain.com/csv-importer/index.php
```

### 2. Chuẩn bị file CSV

#### Định dạng file CSV chuẩn:

**Cột bắt buộc:**
- `id` - ID sản phẩm (số nguyên, duy nhất)
- `title` - Tên sản phẩm
- `slug` - URL slug (nếu không có sẽ tự động tạo từ title)
- `category` - Danh mục sản phẩm

**Cột tùy chọn:**
- `primary_keyword` - Từ khóa chính SEO
- `status` - Trạng thái (completed/pending/draft)
- `file_md` - Tên file markdown
- `file_html` - Tên file HTML
- `file_css` - Tên file CSS
- `seo_title` - Tiêu đề SEO
- `seo_description` - Mô tả SEO
- `tags` - Các tag, phân cách bằng dấu phẩy
- `price` - Giá sản phẩm
- `stock` - Số lượng tồn kho

#### Ví dụ file CSV:

```csv
id,title,slug,category,primary_keyword,status,price,stock
1,BT-SK High Speed Tool Holder,bt-sk-high-speed-tool-holder,BT SIDE LOCK,bt-sk high speed tool holder,completed,1500000,100
2,BT-GER High Speed ER Collet Chuck,bt-ger-high-speed-er-collet-chuck,BT SIDE LOCK,bt-ger er collet chuck,pending,1800000,80
```

### 3. Import từ máy tính

1. Click nút **"Chọn tập tin"**
2. Chọn file CSV từ máy tính (tối đa 256 MB)
3. Tích chọn **"Cập nhật các sản phẩm hiện có"** nếu muốn cập nhật sản phẩm đã tồn tại
4. Chọn **dấu phân cách** phù hợp (mặc định là dấu phẩy)
5. Chọn **mã hóa ký tự** (khuyến nghị: UTF-8)
6. Click **"Nhập Dữ Liệu"**

### 4. Import từ server

1. Nhập đường dẫn đầy đủ đến file CSV trên server
   ```
   /home1/mangthanhcong/anmitools.com/products.csv
   ```
2. Cấu hình các tùy chọn tương tự như import từ máy tính
3. Click **"Nhập Dữ Liệu"**

## ⚙️ Các Tùy Chọn

### Cập nhật sản phẩm hiện có
- ✅ **Bật**: Sản phẩm có ID hoặc slug trùng sẽ được cập nhật
- ❌ **Tắt**: Sản phẩm trùng sẽ bị bỏ qua

### Dấu phân cách CSV
- `,` (Dấu phẩy) - Mặc định, phổ biến nhất
- `;` (Dấu chấm phẩy) - Phổ biến ở châu Âu
- `Tab` - Dùng cho file TSV
- `|` (Dấu gạch đứng) - Dùng khi dữ liệu có nhiều dấu phẩy

### Mã hóa ký tự
- **UTF-8** - Khuyến nghị cho tiếng Việt
- **UTF-16** - Cho Unicode
- **Windows-1252** - File từ Excel Windows cũ
- **ISO-8859-1** - Latin-1
- **Tự động phát hiện** - Hệ thống tự nhận diện

### Ánh xạ cột trước đó
- Lưu cấu hình ánh xạ cột để sử dụng lại cho lần import tiếp theo

## 📥 Tải File Mẫu

Click nút **"Tải File Mẫu CSV"** để download file CSV mẫu với đầy đủ cấu trúc chuẩn.

## 📊 Kết Quả Import

Sau khi import, hệ thống sẽ hiển thị thống kê:
- **Tổng số dòng đã xử lý** - Tổng số sản phẩm trong file
- **Sản phẩm mới được nhập** - Số sản phẩm mới thêm vào
- **Sản phẩm được cập nhật** - Số sản phẩm đã cập nhật
- **Sản phẩm bị bỏ qua** - Số sản phẩm không được xử lý

## ⚠️ Lưu Ý Quan Trọng

### 1. Backup dữ liệu
- Hệ thống tự động backup file `products-list.json` trước khi import
- File backup có tên: `products-list.json.backup.YYYYMMDDHHmmss`

### 2. File CSV chuẩn
- **Phải có dòng header** (dòng đầu tiên)
- Dữ liệu bắt đầu từ dòng thứ 2
- Không để trống các cột bắt buộc

### 3. Định dạng dữ liệu
- **ID**: Số nguyên dương, duy nhất
- **Slug**: Chữ thường, dấu gạch ngang, không dấu
- **Price**: Số thực (VD: 1500000 hoặc 1500000.00)
- **Stock**: Số nguyên
- **Tags**: Phân cách bằng dấu phẩy

### 4. Xử lý tiếng Việt
- Sử dụng encoding **UTF-8** cho tiếng Việt
- Nếu file từ Excel, save as CSV với encoding UTF-8

### 5. Dữ liệu lớn
- File tối đa: 256 MB
- Nếu import nhiều sản phẩm, có thể mất vài phút
- Không đóng trình duyệt trong quá trình import

## 🔧 Xử Lý Lỗi Thường Gặp

### Lỗi: "File upload không hợp lệ"
**Nguyên nhân**: File không được upload đúng cách
**Giải pháp**: 
- Kiểm tra kích thước file (< 256 MB)
- Thử lại hoặc sử dụng import từ server path

### Lỗi: "Định dạng file không được hỗ trợ"
**Nguyên nhân**: File không phải .csv hoặc .txt
**Giải pháp**: Đổi đuôi file thành .csv hoặc .txt

### Lỗi: "Thiếu cột bắt buộc: xxx"
**Nguyên nhân**: File CSV thiếu cột bắt buộc
**Giải pháp**: Thêm cột bị thiếu vào header của file CSV

### Lỗi: "File không tồn tại trên server"
**Nguyên nhân**: Đường dẫn server không đúng
**Giải pháp**: 
- Kiểm tra đường dẫn đầy đủ
- Kiểm tra quyền truy cập file

### Cảnh báo: "Sản phẩm ID xxx đã tồn tại, bỏ qua"
**Nguyên nhân**: Sản phẩm đã có trong hệ thống
**Giải pháp**: 
- Tích chọn "Cập nhật các sản phẩm hiện có" để cập nhật
- Hoặc đổi ID sản phẩm trong file CSV

## 📝 Workflow Khuyến Nghị

1. **Tải file mẫu** để xem cấu trúc chuẩn
2. **Chuẩn bị dữ liệu** theo đúng format
3. **Test với ít sản phẩm** (3-5 sản phẩm) trước
4. **Kiểm tra kết quả** trong `products-list.json`
5. **Import toàn bộ** sau khi đã test thành công
6. **Verify dữ liệu** sau khi import

## 🔐 Bảo Mật

- Công cụ chỉ nên được truy cập bởi admin
- Không public đường dẫn `/csv-importer/` ra bên ngoài
- Khuyến nghị thêm authentication trước khi sử dụng
- Thêm file `.htaccess` để bảo vệ thư mục:

```apache
AuthType Basic
AuthName "Restricted Area"
AuthUserFile /path/to/.htpasswd
Require valid-user
```

## 📞 Hỗ Trợ

Nếu gặp vấn đề, vui lòng liên hệ:
- **Email**: support@anmitools.com
- **Hotline**: 1900-xxxx
- **Website**: https://anmitools.com

---

**Phiên bản**: 1.0.0  
**Ngày cập nhật**: 2025-10-23  
**Tác giả**: An Mi Tools Technical Team
