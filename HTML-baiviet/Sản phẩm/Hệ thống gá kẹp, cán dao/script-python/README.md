# 📚 HỆ THỐNG QUẢN LÝ NỘI DUNG SẢN PHẨM AN MI TOOLS

## 🎯 Tổng quan
Hệ thống tự động tạo và quản lý bài viết sản phẩm chuẩn SEO cho An Mi Tools, tập trung vào dòng sản phẩm **Holder CNC**. Hệ thống bao gồm:

- **Plugin PHP duy nhất** để inject CSS chung cho tất cả sản phẩm holder
- **File Markdown (.seo.md)** chứa nội dung gốc chuẩn SEO
- **File CSS chung duy nhất** `anmi-holder-products.css` áp dụng cho tất cả 40 sản phẩm
- **Danh sách 40 sản phẩm** từ tri thức nền `holder_knowledge_base.txt`

---

## 📁 Cấu trúc thư mục

```
Hệ thống gá kẹp, cán dao/
├── plugins/
│   └── anmi-product-style-injector.php  # Plugin duy nhất inject CSS chung
├── products/
│   ├── 01-bt-sk-high-speed-tool-holder.seo.md
│   ├── 02-bt-ger-high-speed-er-collet-chuck.seo.md
│   └── ... (40 sản phẩm)
├── css/
│   └── anmi-holder-products.css  # FILE CSS CHUNG DUY NHẤT
├── seo back end/
│   ├── context master/
│   │   ├── holder_knowledge_base.txt  # Tri thức nền
│   │   └── ngữ cảnh.txt  # Đã cập nhật địa chỉ 6 văn phòng + 2 nhà máy
│   └── seo template/
│       └── template.seo.md  # Template mẫu
├── seo html/
│   └── (các file HTML cũ để tham khảo)
├── assets/
│   └── (hình ảnh, tài liệu)
├── products-list.json  # Danh sách 40 sản phẩm
└── README.md  # File này
```

---

## 🚀 Cài đặt Plugin

### Bước 1: Upload plugin vào WordPress
```bash
# Copy file plugin vào thư mục plugins của WordPress
cp plugins/anmi-product-style-injector.php /path/to/wordpress/wp-content/plugins/
```

### Bước 2: Tạo thư mục CSS
```bash
# Tạo thư mục css trong cùng cấp với plugins
mkdir /path/to/wordpress/wp-content/css/
```

### Bước 3: Upload file CSS chung
```bash
# Copy FILE CSS CHUNG vào thư mục css
cp css/anmi-holder-products.css /path/to/wordpress/wp-content/css/
```

**Lưu ý:** Chỉ cần 1 file CSS duy nhất `anmi-holder-products.css`, không cần upload 40 file riêng lẻ.

### Bước 4: Kích hoạt plugin trong WordPress
1. Đăng nhập vào WordPress Admin
2. Vào **Plugins → Installed Plugins**
3. Tìm **An Mi Product Style Injector**
4. Click **Activate**

### Bước 5: Kiểm tra plugin hoạt động
1. Vào **Settings → An Mi Product Styles**
2. Kiểm tra danh sách file CSS đã được phát hiện
3. Nếu không thấy file CSS, kiểm tra lại đường dẫn thư mục

---

## 📝 Quy trình tạo bài viết mới

### Phương pháp 1: Tạo bài trong WordPress Editor
1. **Tạo Post/Page mới** trong WordPress
2. **Copy nội dung** từ file `.seo.md` (bỏ qua metadata YAML)
3. **Paste vào editor** (chế độ HTML/Code)
4. **Publish** bài viết
5. Plugin sẽ **tự động inject CSS** dựa vào class `<section class="slug">`

### Phương pháp 2: Import từ Markdown (nếu có plugin hỗ trợ)
1. Cài đặt plugin **WP Githuber MD** hoặc **Markdown Editor**
2. Import file `.seo.md` trực tiếp
3. Publish

---

## 🎨 Cách hoạt động của Plugin

### Cơ chế tự động nhận diện (Version 2.0)
```php
// Plugin sẽ nhận diện holder products qua 3 cách:

// 1. Kiểm tra content có chứa class holder pattern:
<section class="bt-sk-...">  // bt-, hsk-, nbh, nbj, ewn, rbh, cbh, etc.

// 2. Kiểm tra post thuộc category:
Category slug: "he-thong-ga-kep-can-dao" (slug cha)
hoặc: "bt-", "hsk-" (slug con)

// 3. Kiểm tra post slug:
Post slug bắt đầu bằng: bt-, hsk-, nbh, nbj, v.v.

// Nếu phát hiện là holder product → tự động enqueue:
wp-content/css/anmi-holder-products.css
```

### Ví dụ cụ thể
```html
<!-- TRƯỚC ĐÂY (Version 1.0 - 40 file CSS riêng) -->
<section class="bt-sk-high-speed-tool-holder">
  → Load: bt-sk-high-speed-tool-holder.css
  
<section class="bt-ger-high-speed-er-collet-chuck">
  → Load: bt-ger-high-speed-er-collet-chuck.css

<!-- BÂY GIỜ (Version 2.0 - 1 file CSS chung) -->
<section class="bt-sk-high-speed-tool-holder">
  → Load: anmi-holder-products.css (file chung)
  
<section class="bt-ger-high-speed-er-collet-chuck">
  → Load: anmi-holder-products.css (file chung)

<section class="hsk-er-tool-holder">
  → Load: anmi-holder-products.css (file chung)

→ TẤT CẢ 40 sản phẩm holder dùng CHUNG 1 FILE CSS
```

### URL tham chiếu
- **Category cha**: https://anmitools.com/product-category/he-thong-ga-kep-can-dao/
- **Các sản phẩm con**: Tự động nhận diện nếu có slug pattern holder

---

## 📊 Danh sách 40 sản phẩm

Xem file `products-list.json` để biết chi tiết. Tóm tắt:

| ID | Sản phẩm | Category | Status |
|----|----------|----------|--------|
| 1  | BT-SK High Speed Tool Holder | BT SIDE LOCK | ✅ Completed |
| 2  | BT-GER High Speed ER Collet Chuck | BT SIDE LOCK | ⏳ Pending |
| 3  | BT-HGER High Speed ER Collet Chuck | BT SIDE LOCK | ⏳ Pending |
| ... | ... | ... | ... |
| 40 | NT Tool Holder System | NT | ⏳ Pending |

### Thống kê theo danh mục
- **BT SIDE LOCK**: 17 sản phẩm
- **HSK**: 8 sản phẩm
- **BT-SHR**: 1 sản phẩm
- **COLLET**: 2 sản phẩm
- **Boring System**: 10 sản phẩm
- **NT**: 1 sản phẩm
- **Hệ thống gá kẹp, cán dao**: 1 sản phẩm

---

## 📋 Template Metadata

Mỗi file `.seo.md` phải có metadata YAML ở đầu file:

```yaml
---
title: "Tên sản phẩm đầy đủ"
slug: "ten-san-pham-khong-dau"
primary_keyword: "từ khóa chính duy nhất"
tags: "tag1, tag2, tag3"
category: "BT SIDE LOCK"
seo_title: "Tiêu đề SEO chứa từ khóa chính"
seo_description: "Mô tả ngắn 150-160 ký tự"
social_image_facebook: "link_ảnh_fb.jpg"
social_image_twitter: "link_ảnh_x.jpg"
seo_score_target: ">=70"
author: "An Mi Tools Technical Team"
date_published: "2025-01-20"
date_modified: "2025-01-20"
schema_type: "Product"
---
```

---

## 🎨 Quy tắc CSS (Version 2.0 - Single CSS File)

### 1. MỘT file CSS cho tất cả holder products
```
css/anmi-holder-products.css  ← FILE DUY NHẤT cho 40 sản phẩm
```

### 2. CSS sử dụng attribute selectors
```css
/* Target TẤT CẢ sản phẩm có class chứa "bt-" */
section[class*="bt-"] h1 {
  font-size: clamp(1.75rem, 4vw, 2.5rem);
}

/* Target TẤT CẢ sản phẩm có class chứa "hsk-" */
section[class*="hsk-"] h1 {
  font-size: clamp(1.75rem, 4vw, 2.5rem);
}

/* Target NHIỀU patterns cùng lúc */
section[class*="nbh"] h1,
section[class*="nbj"] h1,
section[class*="ewn"] h1,
section[class*="rbh"] h1 {
  font-size: clamp(1.75rem, 4vw, 2.5rem);
}
```

### 3. CSS phải scope trong section với attribute selector
```css
/* ✅ Đúng - Target specific holder product sections */
section[class*="bt-"] h1 {
  font-size: 2.5rem;
}

/* ❌ Sai - Áp dụng toàn site */
h1 {
  font-size: 2.5rem;
}
```

### 4. Màu sắc chuẩn An Mi Tools
```css
/* Nền */
background-color: #FCF7EC;

/* Chữ chính */
color: #000000;  /* Contrast ratio 18.96:1 */

/* Caption ảnh */
color: #333333;

/* Link */
color: #0055AA;

/* Highlight */
border-color: #0055AA;
```

---

## 📐 Cấu trúc nội dung chuẩn

Mỗi bài viết phải có các phần sau:

```markdown
<section class="slug">

  <!-- 1. TIÊU ĐỀ CHÍNH -->
  <h1>Tên sản phẩm đầy đủ</h1>

  <!-- 2. MÔ TẢ NGẮN -->
  <div class="section">
    <h2>Mô tả sản phẩm</h2>
    <p>...</p>
  </div>

  <!-- 3. ẢNH ĐẠI DIỆN -->
  <figure>
    <img src="..." alt="...">
    <figcaption>Caption liên quan đến mô tả ngắn</figcaption>
  </figure>

  <!-- 4. THÔNG SỐ KỸ THUẬT -->
  <div class="section">
    <h2>Thông số kỹ thuật</h2>
    <div class="card">
      <ul class="spec-list">...</ul>
    </div>
  </div>

  <!-- 5. ẢNH THÔNG SỐ -->
  <figure>
    <img src="..." alt="...">
    <figcaption>Caption giải thích bảng thông số</figcaption>
  </figure>

  <!-- 6. ỨNG DỤNG -->
  <div class="section">
    <h2>Ứng dụng thực tế</h2>
    ...
  </div>

  <!-- 7. ẢNH ỨNG DỤNG -->
  <figure>
    <img src="..." alt="..." class="bordered-img">
    <figcaption>Caption ví dụ ứng dụng</figcaption>
  </figure>

  <!-- 8. HỖ TRỢ KỸ THUẬT -->
  <div class="section">
    <h2>Hỗ trợ kỹ thuật</h2>
    ...
  </div>

</section>
```

---

## 🖼️ Quy tắc hình ảnh

### 1. Kích thước tiêu chuẩn
```html
<figure>
  <img 
    src="https://anmitools.com/images/product-name.jpg" 
    alt="Mô tả đầy đủ chứa từ khóa"
    loading="lazy"
  >
  <figcaption>Caption chi tiết liên quan nội dung</figcaption>
</figure>
```

### 2. Ảnh có border (dùng cho ảnh ứng dụng)
```html
<img 
  src="..." 
  alt="..."
  class="bordered-img"
  loading="lazy"
>
```

CSS tương ứng:
```css
.slug .bordered-img {
  border: 1px solid rgba(0,0,0,0.08);
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}
```

### 3. Vị trí hình ảnh
- **Ảnh đại diện**: sau phần "Mô tả ngắn"
- **Ảnh thông số**: sau phần "Thông số kỹ thuật"
- **Ảnh ứng dụng**: sau phần "Ứng dụng thực tế"
- **Ảnh hướng dẫn**: sau phần "Hướng dẫn sử dụng"
- **Ảnh liên hệ**: sau phần "Hỗ trợ kỹ thuật"

---

## ✅ Checklist trước khi publish

- [ ] Metadata YAML đầy đủ và chính xác
- [ ] Slug không dấu, viết thường, phân cách bằng `-`
- [ ] File CSS đã tồn tại trong thư mục `css/`
- [ ] Tên file CSS khớp với slug
- [ ] Nội dung bọc trong `<section class="slug">`
- [ ] Có đầy đủ các section: Mô tả, Thông số, Ứng dụng, Hỗ trợ
- [ ] Mỗi section có ít nhất 1 hình ảnh minh họa
- [ ] Caption hình ảnh liên quan trực tiếp đến nội dung
- [ ] Alt text hình ảnh chứa từ khóa chính
- [ ] Độ dài SEO description: 150-160 ký tự
- [ ] Rank Math SEO score: ≥70
- [ ] Kiểm tra responsive trên mobile
- [ ] Kiểm tra accessibility (contrast ratio, alt text)

---

## 🔄 Quy trình viết bài mới

### Bước 1: Chọn sản phẩm từ `products-list.json`
```json
{
  "id": 2,
  "title": "BT-GER High Speed ER Collet Chuck",
  "slug": "bt-ger-high-speed-er-collet-chuck",
  "category": "BT SIDE LOCK",
  "status": "pending"
}
```

### Bước 2: Tra cứu thông tin trong `holder_knowledge_base.txt`
- Tìm phần **"BT-GER High Speed ER Collet Chuck"**
- Trích xuất:
  - Định nghĩa
  - Đặc điểm kỹ thuật
  - Ứng dụng
  - Ưu/nhược điểm

### Bước 3: Tạo file `.seo.md`
```bash
products/02-bt-ger-high-speed-er-collet-chuck.seo.md
```

### Bước 4: Tạo file CSS
```bash
css/bt-ger-high-speed-er-collet-chuck.css
```

### Bước 5: Viết nội dung theo template
- Copy từ `01-bt-sk-high-speed-tool-holder.seo.md`
- Thay thế nội dung theo sản phẩm mới
- Giữ nguyên cấu trúc HTML và class

### Bước 6: Cập nhật `products-list.json`
```json
{
  "id": 2,
  "status": "completed"
}
```

### Bước 7: Test trên WordPress
1. Tạo post mới
2. Copy nội dung từ `.seo.md`
3. Publish
4. Kiểm tra CSS đã load đúng
5. Kiểm tra Rank Math SEO score

---

## 🛠️ Troubleshooting

### CSS không load
**Nguyên nhân:**
- File CSS chung không tồn tại: `anmi-holder-products.css`
- Plugin chưa được kích hoạt
- Không phát hiện được holder product pattern

**Giải pháp:**
1. Kiểm tra file CSS trong `wp-content/css/anmi-holder-products.css`
2. Kiểm tra section có đúng class pattern: `<section class="bt-...">`
3. Kiểm tra post thuộc category "he-thong-ga-kep-can-dao"
4. Xem log trong **Settings → An Mi Product Styles**
5. Kiểm tra DevTools → Network tab xem CSS có load không

**Debug log mẫu:**
```
An Mi Product Style Injector: Enqueued common CSS for holder product (Post ID: 123)
An Mi Product Style Injector: Common CSS file not found: /wp-content/css/anmi-holder-products.css
```

### Nội dung bị lỗi format
**Nguyên nhân:**
- Sử dụng Visual Editor thay vì Code Editor
- Copy/paste từ Word/Google Docs

**Giải pháp:**
1. Chuyển sang **Text/HTML Editor**
2. Copy từ file `.seo.md` gốc
3. Không sửa trong Visual Editor

### CSS không áp dụng cho sản phẩm cụ thể
**Nguyên nhân:**
- Section class không chứa holder pattern (bt-, hsk-, nbh, v.v.)
- CSS selector quá chặt chẽ

**Giải pháp:**
1. Kiểm tra section class: `<section class="bt-sk-...">` (phải có bt-, hsk-, etc.)
2. Kiểm tra CSS có selector tương ứng: `section[class*="bt-"]`
3. Sử dụng browser DevTools → Elements tab kiểm tra CSS rules applied

### Rank Math SEO score thấp
**Nguyên nhân:**
- Thiếu từ khóa chính trong tiêu đề/mô tả
- Nội dung quá ngắn (<300 từ)
- Thiếu internal/external links
- Thiếu alt text hình ảnh

**Giải pháp:**
1. Thêm từ khóa chính vào H1, H2
2. Mở rộng nội dung "Ứng dụng thực tế"
3. Thêm link đến sản phẩm liên quan
4. Thêm alt text cho tất cả hình ảnh

---

## 📞 Hỗ trợ

**Kỹ thuật:**
- Email: tech@anmitools.com
- Tel: +84 24 3556 2635

**Nội dung:**
- Email: content@anmitools.com

---

## 📝 Changelog

### Version 2.0.0 (2025-01-20)
- 🎉 **MAJOR UPDATE**: Chuyển từ 40 file CSS riêng → 1 file CSS chung
- ✅ Tạo `anmi-holder-products.css` (file CSS duy nhất cho tất cả holder products)
- ✅ Cập nhật plugin: 3-tier detection system (content → category → post slug)
- ✅ Thêm `$holder_slug_patterns` array (16 patterns: bt-, hsk-, nbh, nbj, v.v.)
- ✅ Plugin tự động nhận diện via parent slug: "he-thong-ga-kep-can-dao"
- ✅ CSS sử dụng attribute selectors: `section[class*="bt-"]`
- ✅ Cập nhật README với kiến trúc mới
- ✅ Xóa 5 file CSS riêng lẻ cũ (bt-sk, bt-ger, bt-hger, bt-er, bt-c-power-chuck)

### Version 1.0.0 (2025-01-20)
- ✅ Tạo cấu trúc thư mục dự án
- ✅ Tạo plugin PHP `anmi-product-style-injector.php`
- ✅ Tách CSS từ `bt_sk_high_speed_html.html`
- ✅ Tạo file template sạch `01-bt-sk-high-speed-tool-holder.seo.md`
- ✅ Tạo danh sách 40 sản phẩm `products-list.json`
- ✅ Tạo file README hướng dẫn
- ✅ Cập nhật contact info với 6 văn phòng + 2 nhà máy

### Kế hoạch tiếp theo
- ⏳ Viết 39 sản phẩm còn lại
- ⏳ Tối ưu SEO cho từng bài
- ⏳ Thêm Schema.org markup
- ⏳ Tạo breadcrumb navigation

---

## 📜 License
Copyright © 2025 An Mi Tools. All rights reserved.
