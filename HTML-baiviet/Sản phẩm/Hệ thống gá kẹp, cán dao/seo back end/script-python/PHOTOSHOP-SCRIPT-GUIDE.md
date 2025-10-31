# HƯỚNG DẪN SỬ DỤNG PHOTOSHOP SCRIPT

## 🎨 CreateProductImages.jsx

Script JSX chạy trực tiếp trong Adobe Photoshop để tự động tạo cấu trúc thư mục và file PSD cho 39 sản phẩm.

---

## ✨ Tính năng

✅ **Tạo tự động 39 sản phẩm** với cấu trúc hoàn chỉnh:
```
[tên-sản-phẩm]/
├── anh-thu-vien/
├── anh-bai-viet/
└── [tên-sản-phẩm].psd (1000x1000px, nền trắng, DPI 300)
```

✅ **Xử lý theo batch** (10 sản phẩm/lần) để tránh quá tải

✅ **Progress bar** hiển thị tiến độ real-time

✅ **Kiểm tra quyền ghi** trước khi bắt đầu

✅ **Tên file chuẩn slug** (SEO-friendly)

---

## 🚀 Cách sử dụng

### Bước 1: Mở Adobe Photoshop

Khởi động Adobe Photoshop (bất kỳ phiên bản nào từ CS6 trở lên)

### Bước 2: Chạy script

**Cách 1: Menu File**
```
File > Scripts > Browse...
→ Chọn file: CreateProductImages.jsx
```

**Cách 2: Kéo thả**
```
Kéo file CreateProductImages.jsx vào cửa sổ Photoshop
```

**Cách 3: ExtendScript Toolkit (nếu có)**
```
1. Mở Adobe ExtendScript Toolkit
2. Mở file CreateProductImages.jsx
3. Chọn Target: Adobe Photoshop
4. Nhấn F5 hoặc nút Play
```

### Bước 3: Làm theo hướng dẫn

1. **Dialog 1:** Xác nhận tạo 39 sản phẩm → Nhấn **OK**

2. **Dialog 2:** Chọn thư mục gốc để lưu → Nhấn **Chọn thư mục**

3. **Dialog 3:** Xác nhận đường dẫn → Nhấn **OK**

4. **Progress Bar:** Đợi script chạy (khoảng 2-5 phút)

5. **Dialog cuối:** Hiển thị kết quả → Nhấn **OK**

### Bước 4: Kiểm tra kết quả

Mở thư mục đã chọn, bạn sẽ thấy:
```
Thư mục gốc/
├── bt-sk-high-speed-tool-holder/
│   ├── anh-thu-vien/
│   ├── anh-bai-viet/
│   └── bt-sk-high-speed-tool-holder.psd
├── bt-ger-high-speed-er-collet-chuck/
│   ├── anh-thu-vien/
│   ├── anh-bai-viet/
│   └── bt-ger-high-speed-er-collet-chuck.psd
├── ... (37 sản phẩm khác)
```

---

## 📋 Danh sách 39 sản phẩm

Script sẽ tạo cấu trúc cho các sản phẩm sau:

### BT Series (16)
1. bt-sk-high-speed-tool-holder
2. bt-ger-high-speed-er-collet-chuck
3. bt-hger-high-speed-er-collet-chuck
4. bt-er-collet-chuck-standard
5. bt-c-power-chuck-tool-holder
6. bt-oz-heavy-duty-tool-holder
7. bt-apu-drill-chuck-holder
8. bt-fma-face-milling-arbor
9. bt-fmb-face-milling-arbor
10. bt-sla-weldon-tool-holder
11. bt-mta-mtb-morse-taper
12. bt-slo-ero-oil-feed-tool-holder
13. bt-sdc-high-precision-tool-holder
14. bt-sr-shrink-fit-chuck
15. bt-hs-hydraulic-chuck
16. bt-tension-compression-tapping-holder

### HSK Series (8)
17. hsk-sr-shrink-fit-chuck
18. hsk-er-tool-holder
19. hsk-gsk-tool-holder
20. hsk-hs-hydraulic-chuck
21. hsk-fmb-face-milling-arbor
22. hsk-sla-weldon-tool-holder
23. hsk-apu-drill-chuck-holder
24. hsk-c-power-chuck

### Boring Systems (11)
25. nbh2084-micro-boring-system
26. nbj16-micro-boring-system
27. ewn-micro-boring-head
28. rbh-adjustable-rough-boring-head
29. cbh-large-diameter-fine-boring-head
30. cbs-45-90-boring-tool
31. sb-fixed-diameter-boring-cutter
32. gc-fixed-diameter-boring-cutter
33. ck-lbk-boring-bar
34. tck-bst-boring-system
35. bst-twin-blade-boring-tool

### Collet Systems (4)
36. er-collet
37. sk-collet
38. c-power-chuck-collet
39. oz-collet

---

## ⚙️ Cấu hình (có thể tùy chỉnh trong script)

```javascript
var CONFIG = {
    imageWidth: 1000,        // Chiều rộng (px)
    imageHeight: 1000,       // Chiều cao (px)
    resolution: 300,         // DPI
    colorMode: RGB,          // Color mode
    batchSize: 10,           // Số sản phẩm xử lý mỗi lần
    subfolders: [            // Tên thư mục con
        "anh-thu-vien", 
        "anh-bai-viet"
    ]
};
```

### Cách thay đổi cấu hình:

1. Mở file `CreateProductImages.jsx` bằng text editor
2. Tìm đến phần `// CẤU HÌNH`
3. Chỉnh sửa các giá trị trong `var CONFIG = {...}`
4. Lưu lại và chạy script

**Ví dụ:** Thay đổi kích thước ảnh thành 2000x2000px:
```javascript
imageWidth: 2000,
imageHeight: 2000,
```

---

## 🔧 Xử lý lỗi

### Lỗi: "Không có quyền ghi vào thư mục"
**Giải pháp:**
- Chọn thư mục khác (ví dụ: Desktop, Documents)
- Hoặc chạy Photoshop với quyền Administrator

### Lỗi: "Script không chạy"
**Giải pháp:**
1. Kiểm tra phiên bản Photoshop (cần CS6 trở lên)
2. File > Scripts > Browse... (không dùng File > Open)
3. Thử chạy lại Photoshop

### Lỗi: "Out of memory"
**Giải pháp:**
- Giảm `batchSize` từ 10 xuống 5 hoặc 3
- Đóng các document khác trong Photoshop
- Tăng RAM allocation cho Photoshop:
  ```
  Edit > Preferences > Performance
  → Tăng "Memory Usage" lên 70-80%
  ```

### Script chạy quá chậm
**Giải pháp:**
- Đóng các ứng dụng khác
- Tắt antivirus tạm thời
- Chọn thư mục trên ổ SSD thay vì HDD

---

## 📊 Thời gian xử lý ước tính

| Số lượng | Thời gian | RAM khuyến nghị |
|----------|-----------|-----------------|
| 39 sản phẩm | 2-5 phút | 8GB+ |
| 100 sản phẩm | 5-12 phút | 16GB+ |
| 200 sản phẩm | 10-25 phút | 32GB+ |

*Thời gian phụ thuộc vào cấu hình máy, tốc độ ổ đĩa, và batch size*

---

## 💡 Tips & Tricks

### 1. Thêm sản phẩm mới

Mở file JSX và thêm vào mảng `PRODUCTS`:
```javascript
var PRODUCTS = [
    "BT-SK High Speed Tool Holder",
    "Tên Sản Phẩm Mới",  // ← Thêm ở đây
    // ... các sản phẩm khác
];
```

### 2. Thay đổi tên thư mục con

Sửa trong `CONFIG`:
```javascript
subfolders: ["thu-vien", "bai-viet", "featured"]
```

### 3. Tạo ảnh với background khác

Sửa trong hàm `createWhitePSD()`:
```javascript
// Thay vì DocumentFill.WHITE
DocumentFill.TRANSPARENT  // Nền trong suốt
DocumentFill.BACKGROUNDCOLOR  // Màu background hiện tại
```

### 4. Tự động đặt tên Layer

Thêm sau dòng tạo document:
```javascript
doc.artLayers[0].name = "Background";
```

---

## 📞 Hỗ trợ

**An Mi Tools**
- 🌐 Website: https://anmitools.com
- 📧 Email: sales@anmitools.com
- 📱 Hotline: 091 519 2325
- 📍 Địa chỉ HCM: KCN Tân Bình, TP.HCM

---

## 📝 Changelog

### Version 1.0 (2025-10-29)
- ✅ Tạo script JSX cho Photoshop
- ✅ Xử lý 39 sản phẩm tự động
- ✅ Batch processing để tối ưu hiệu suất
- ✅ Progress bar real-time
- ✅ Kiểm tra quyền ghi
- ✅ Cấu trúc thư mục linh hoạt

---

## 📄 License

Copyright © 2025 An Mi Tools. All rights reserved.
