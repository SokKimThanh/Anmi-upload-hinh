# Worklog BT-OZ Heavy Duty Tool Holder

## Mục tiêu chuẩn hoá
- Chuẩn hoá nội dung, bố cục và SEO cho BT-OZ Heavy Duty Tool Holder.
- Đồng bộ với chuẩn chung của hệ BT (BT-ER, BT-C, BT-HGER...) về: tabs, hình ảnh, thông số, FAQ/Ứng dụng, Liên hệ.
- Đảm bảo dễ đọc cho kỹ thuật, thân thiện Rank Math SEO.

## Commits chính (HTML .seo.html)
- `d5a1063` – Enhance SEO and content for BT-OZ Heavy Duty Tool Holder page  
  - Viết lại H1, intro, bullet features theo chuẩn nội dung holder.  
  - Bổ sung nội dung kỹ thuật: OZ25/OZ32, 16 rãnh, run-out, ứng dụng boring/reaming/lỗ sâu.  
  - Chuẩn hoá JSON-LD Product + Breadcrumb.

- `cca3dab` – feat: enhance BT-OZ Heavy Duty Tool Holder page with new tab structure and additional content  
  - Chuyển layout sang dạng **tabs**: Tổng Quan, Thông Số, Ứng Dụng, Liên Hệ.  
  - Tab Tổng Quan: hình sản phẩm chính, intro, 5–6 bullet tính năng, nút tải catalog.  
  - Tab Thông Số: block thông số nhanh (`specs-grid`) + các bảng chi tiết (`comparison-table`).  
  - Tab Ứng Dụng: use case thực tế, performance cards, bảng so sánh BT-OZ vs BT-ER vs BT-C.  
  - Tab Liên Hệ: CTA báo giá/catalog, thông tin 4 chi nhánh + email.

- `a2db2bf` – Add accessory section and detailed specifications for BT-OZ Heavy Duty Tool Holder  
  - Bổ sung bảng kích thước holder OZ25/OZ32 (size, range, L, D2, loại collet).  
  - Bổ sung bảng dải collet mm/inch và bảng đai ốc OZ (nut).  
  - Thêm phần **phụ kiện BT-OZ**: collet OZ25/OZ32, đai ốc/cờ lê siết, kèm hình minh hoạ.

- `51fbfe2` – feat: enhance BT-OZ Heavy Duty Tool Holder page with new image, detailed features, and specifications  
  - Cập nhật hình sản phẩm chính độ phân giải cao.  
  - Tinh chỉnh lại đoạn giới thiệu và danh sách đặc điểm nổi bật.  
  - Đồng bộ lại cụm thông số chính cho khớp với catalog.

- `53ee878` – feat: enhance BT-OZ Heavy Duty Tool Holder page with updated specifications, improved layout, and accessory section  
  - Tinh chỉnh layout: đưa hình sản phẩm chính vào tab Tổng Quan, hình bảng thông số vào tab Thông Số.  
  - Sắp xếp lại phần phụ kiện để dễ đọc, nhóm theo loại (collet, nut/cờ lê).

- `795aced` – feat: add accessories section with detailed information for BT-OZ Heavy Duty Tool Holder  
  - Mở rộng nội dung phụ kiện: mô tả rõ dải kẹp, ứng dụng của bộ collet và đai ốc/cờ lê.  
  - Cải thiện alt/figcaption cho hình để tốt hơn cho SEO hình ảnh.

- `500b73a` – feat: add accessories section and improve layout for BT-OZ Heavy Duty Tool Holder page  
  - Tách phụ kiện sang **tab riêng Phụ Kiện**, mỗi phụ kiện có tiêu đề riêng và ảnh ngay bên dưới.  
  - Dọn layout, tránh trùng lặp nội dung giữa Thông Số và Phụ Kiện.

## Commits chính (SEO .seo.md)
- `f7cf0bc` – convert structure folder name  
  - Khởi tạo file seo.md cho BT-OZ với cấu trúc front matter chuẩn (slug, title, meta...).

- `097857a` – META: Add catalog_page to all 44 product metadata files  
  - Thêm `catalog_page`/metadata liên quan để đồng bộ SEO toàn bộ sản phẩm.

- `51fbfe2` – feat: enhance BT-OZ Heavy Duty Tool Holder page with new image, detailed features, and specifications  
  - Thêm nội dung cơ bản cho seo.md: giới thiệu, tính năng, thông số, ứng dụng.  
  - Gắn kết với nội dung HTML (H1/H2, thông số chính).

- `1a08fee` – feat: update BT-OZ Heavy Duty Tool Holder page with enhanced content, features, and specifications  
  - Chuẩn hoá cấu trúc seo.md theo mẫu các mã holder khác (BT-FMA, BT-ER...).  
  - Bổ sung mục: 5 tính năng, thông số kỹ thuật, ứng dụng thực tế, FAQ, CTA liên hệ.  
  - Thêm internal link sang các sản phẩm liên quan (BT-ER, BT-C...).

- `53ee878` – feat: enhance BT-OZ Heavy Duty Tool Holder page with updated specifications, improved layout, and accessory section  
  - Cập nhật lại thông số, từ ngữ kỹ thuật để khớp với catalogs mới.  
  - Nhấn mạnh thêm ứng dụng boring/reaming/lỗ sâu và dao vươn dài.

## Tóm tắt chuẩn thay đổi BT-OZ (dùng làm prompt)
- Chuẩn hoá H1, intro, bullet features theo phong cách holder (BT-ER, BT-C...).
- Dùng layout **tabs**: Tổng Quan, Thông Số, Ứng Dụng, Phụ Kiện (nếu có), Liên Hệ.
- Tab Tổng Quan: 1 ảnh sản phẩm chính + intro + 5–7 bullet tính năng + nút tải catalog.
- Tab Thông Số: block thông số nhanh (`specs-grid`) + bảng chi tiết (`comparison-table`) + hình bảng thông số nếu cần.
- Tab Ứng Dụng: use case thực tế, performance cards, bảng so sánh với mã khác (BT-ER, BT-C...).
- Tab Phụ Kiện: tách phụ kiện (collet, nut, wrench...) sang tab riêng; mỗi phụ kiện có H3 + ảnh ngay bên dưới + mô tả ngắn.
- Tab Liên Hệ: CTA báo giá/catalog + thông tin 4 chi nhánh + email, không trùng lặp ảnh contact.
- Chuẩn hoá seo.md: front matter đầy đủ (slug, primary_keyword, seo_title, seo_description, catalog_page...), nội dung bài dài có phần: giới thiệu, 5 tính năng, thông số, ứng dụng, FAQ, CTA, internal link.
- Đồng bộ keyword chính ("bt-oz heavy duty tool holder") giữa slug, title, meta description, H2 đầu, nội dung và JSON-LD Product.
