# 📱 Hướng Dẫn Sử Dụng Ảnh Hotline Responsive (Desktop & Mobile)

## 📋 Tổng Quan

Từ **anmi-holder-products.css v1.1.7**, hệ thống hỗ trợ hiển thị 2 ảnh contact khác nhau cho Desktop và Mobile:

- **Desktop (>768px):** Hiển thị ảnh địa chỉ đầy đủ (bản đồ, địa chỉ chi tiết, hotline)
- **Mobile (≤768px):** Hiển thị ảnh hotline với số điện thoại TO, RÕ, DỄ NHÌN để khách hàng gọi ngay

## 🎯 Mục Đích

- **Mobile:** Người dùng thường xuyên cuộn nhanh → cần số hotline BỰ để nhìn thấy ngay và gọi điện
- **Desktop:** Người dùng có màn hình lớn → hiển thị đầy đủ thông tin địa chỉ, bản đồ, email

## 📝 Cấu Trúc HTML

```html
<!-- Desktop: Ảnh địa chỉ đầy đủ -->
<figure class="contact-image contact-image-desktop">
  <img src="https://anmitools.com/wp-content/uploads/2025/09/trang-30_tools_diachi-editbyAI.webp" 
       alt="An Mi Tools - Hotline & Địa chỉ liên hệ" 
       loading="lazy" 
       width="1200" 
       height="400">
  <figcaption>Liên hệ An Mi Tools để được tư vấn chi tiết về [TÊN SẢN PHẨM]</figcaption>
</figure>

<!-- Mobile: Ảnh hotline số to rõ ràng -->
<figure class="contact-image contact-image-mobile">
  <img src="https://anmitools.com/wp-content/uploads/2025/10/hotline-mobile-large.webp" 
       alt="An Mi Tools - Hotline 091 519 2325 - Số điện thoại tư vấn [TÊN SẢN PHẨM]" 
       loading="lazy" 
       width="800" 
       height="600">
  <figcaption>Gọi ngay 091 519 2325 để được tư vấn [TÊN SẢN PHẨM]</figcaption>
</figure>
```

## 🎨 CSS Responsive Logic

### Desktop (>768px)
```css
.contact-image-desktop {
    display: block;  /* Hiển thị ảnh địa chỉ đầy đủ */
}

.contact-image-mobile {
    display: none;   /* Ẩn ảnh hotline mobile */
}
```

### Mobile (≤768px)
```css
@media (max-width: 768px) {
    .contact-image-desktop {
        display: none;   /* Ẩn ảnh địa chỉ đầy đủ */
    }

    .contact-image-mobile {
        display: block;  /* Hiển thị ảnh hotline số to */
    }
}
```

## 🖼️ Yêu Cầu Hình Ảnh

### 📍 Ảnh Desktop (contact-image-desktop)
- **Kích thước:** 1200x400px (tỉ lệ 3:1)
- **Nội dung:** Địa chỉ đầy đủ, bản đồ, hotline, email
- **Format:** WebP (tối ưu tốc độ)
- **File name example:** `trang-30_tools_diachi-editbyAI.webp`

### 📞 Ảnh Mobile (contact-image-mobile)
- **Kích thước:** 800x600px (tỉ lệ 4:3)
- **Nội dung:** 
  - ✅ Số hotline **091 519 2325** với font size CỰC LỚN (≥80px)
  - ✅ Màu sắc tương phản cao (ví dụ: chữ trắng nền xanh, hoặc chữ đỏ nền trắng)
  - ✅ Icon điện thoại ☎️ hoặc 📞 bên cạnh số
  - ✅ Text "GỌI NGAY" hoặc "TƯ VẤN 24/7"
  - ❌ KHÔNG thêm quá nhiều thông tin phụ (địa chỉ, email) → tập trung vào SỐ HOTLINE
- **Format:** WebP
- **File name example:** `hotline-mobile-large.webp`

## 🎯 Hướng Dẫn Thiết Kế Ảnh Mobile Hotline

### Option 1: Photoshop/Figma
1. Tạo canvas 800x600px
2. Nền: Gradient xanh dương (#0055AA → #003d7a)
3. Icon điện thoại: 📞 (100x100px, màu trắng)
4. Số hotline: **091 519 2325** (font size 100px, font weight bold, màu trắng)
5. Text "GỌI NGAY TƯ VẤN 24/7" (font size 40px, màu vàng #FFD700)
6. Export WebP với chất lượng 85%

### Option 2: Canva (Dễ hơn)
1. Vào Canva → Tạo design tùy chỉnh 800x600px
2. Chọn template "Phone Banner" hoặc "Call to Action"
3. Thêm text box với số **091 519 2325** → font size MAX, bold
4. Thêm icon điện thoại từ Elements
5. Màu nền: Xanh dương hoặc gradient
6. Download → WebP

### Option 3: Online Tools
- **Remove.bg:** Xóa nền ảnh cũ
- **Canva:** Thiết kế ảnh mới
- **TinyPNG:** Nén ảnh WebP

## 📂 Vị Trí Upload Hình

1. **WordPress Media Library:**
   - Upload ảnh mobile hotline vào: `wp-content/uploads/2025/10/`
   - URL: `https://anmitools.com/wp-content/uploads/2025/10/hotline-mobile-large.webp`

2. **Cập nhật HTML:**
   - Thay đổi `src=""` trong thẻ `<img class="contact-image-mobile">`
   - Cập nhật `alt=""` với từ khóa sản phẩm

## ✅ Checklist Khi Thêm Ảnh Mới

- [ ] Ảnh desktop: 1200x400px, WebP, địa chỉ đầy đủ
- [ ] Ảnh mobile: 800x600px, WebP, hotline số to (≥80px)
- [ ] Alt text có chứa từ khóa sản phẩm + "hotline 091 519 2325"
- [ ] Figcaption có CTA rõ ràng "Gọi ngay 091 519 2325"
- [ ] Test responsive trên Chrome DevTools (Mobile S/M/L)
- [ ] Kiểm tra load speed: ảnh ≤100KB

## 🧪 Test Responsive

### Chrome DevTools
1. Mở trang product
2. F12 → Toggle device toolbar (Ctrl+Shift+M)
3. Chọn device: iPhone SE, iPhone 12, Samsung Galaxy S20
4. Kiểm tra:
   - Desktop: Thấy ảnh địa chỉ đầy đủ
   - Mobile: Thấy ảnh hotline số to
   - Số hotline dễ đọc không?
   - CTA "Gọi ngay" rõ ràng không?

## 📊 Thống Kê Ứng Dụng

File đã áp dụng responsive hotline image:
- ✅ `03-bt-hger-high-speed-er-collet-chuck.seo.FIXED.html`

Sẽ cập nhật cho 39 files còn lại sau khi test thành công.

## 🔄 Migration Plan

### Phase 1: Test (1 file)
- [x] File 03 BT-HGER (đã cập nhật)
- [ ] Upload ảnh hotline mobile thật
- [ ] Test trên mobile thật (iPhone, Android)
- [ ] Thu thập feedback từ team/khách hàng

### Phase 2: Rollout (39 files)
- [ ] Thiết kế 1 ảnh hotline mobile chung cho tất cả sản phẩm
- [ ] Hoặc: Thiết kế ảnh hotline riêng cho từng category (BT, HSK, NBH, v.v.)
- [ ] Cập nhật script Python để tự động thêm 2 ảnh vào HTML template
- [ ] Apply cho 39 files còn lại

## 💡 Tips & Best Practices

1. **Font size hotline mobile:** Tối thiểu 80px, tốt nhất 100-120px
2. **Màu sắc:** Tương phản cao (ví dụ: #0055AA nền + #FFFFFF chữ)
3. **CTA:** "GỌI NGAY", "TƯ VẤN 24/7", "MIỄN PHÍ TƯ VẤN"
4. **Icon:** 📞 ☎️ 📱 (chọn 1, kích thước lớn)
5. **Nền:** Gradient hoặc solid color, tránh ảnh phức tạp
6. **File size:** ≤100KB để load nhanh trên 3G/4G

## 🚀 Kết Quả Mong Đợi

- **Tăng 30-50% tỷ lệ gọi điện từ mobile**
- Khách hàng dễ dàng nhìn thấy số hotline ngay khi cuộn đến phần liên hệ
- UX tốt hơn: Desktop xem đầy đủ thông tin, Mobile tập trung vào hành động (gọi điện)

---

**Version:** 1.0.0  
**Last Updated:** October 30, 2025  
**Author:** An Mi Tools Technical Team
