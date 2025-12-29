# Worklog BT-C Power Chuck Tool Holder

## Mục tiêu chuẩn hoá
- Chuẩn hoá nội dung, bố cục và SEO cho BT-C Power Chuck Tool Holder.
- Đồng bộ cấu trúc với các mã chuẩn (BT-OZ, HM192, BT-ER) về tabs, hình ảnh, thông số, ứng dụng, liên hệ.
- Tối ưu cho Rank Math SEO: internal link giữa các mã holder, outbound link kỹ thuật, schema Product/Breadcrumb.

## Thay đổi hôm nay (HTML .seo.html)
- Chuẩn hoá lại toàn bộ layout BT-C thành dạng **tabs** rõ ràng:
  - Tabs: Tổng Quan, Thông Số, Hộp Tool BT-C, Ứng Dụng, Liên Hệ.
  - Loại bỏ cấu trúc tab lồng nhau và các section cũ, chỉ giữ một `section.bt-c-power-chuck-tool-holder` duy nhất.

- **Tab Tổng Quan**
  - Thêm `h2` "Tổng Quan BT-C Power Chuck Tool Holder" bên trong `section.product-intro`.
  - Giữ lại 2 đoạn mô tả chính về thiết kế 16 rãnh, C collet C20/25/32/42, heavy-duty machining.
  - Gộp block tải catalog vào trong `product-intro` với cấu trúc `div.catalog-download` giống HM192:
    - Nút `a.download-btn` kèm icon `img.download-icon` và text "Download Catalog: BT-C Power Chuck Tool Holder".

- **Tab Thông Số**
  - Giữ `div.specs-grid` với các thông số chính: C collet, dải kẹp Φ3~42, 16 rãnh, lực kẹp, vật liệu, taper BT30/40/50, ứng dụng, độ bền.
  - Thêm `figure.product-image` ngay dưới tiêu đề để hiển thị ảnh bản vẽ/bảng thông số:
    - Ảnh: `BT-C-Power-Chuck-Tool-Holder-thongsokythuat.webp`.
    - Alt/figcaption mô tả đây là bản vẽ và thông số chi tiết C20/C25/C32/C42.

- **Tab Hộp Tool BT-C**
  - Mô tả rõ cấu hình hộp tool BT-C: holder theo taper, bộ C collet C20/C25/C32/C42, phụ kiện (khóa siết, chìa, pull stud...), hộp tool thép/inox với foam định hình.
  - Thêm `div.product-images-grid` với `figure.product-image`:
    - Ảnh: `BT-C-Power-Chuck-Tool-Holder-Toolbox.webp`.
    - Figcaption giải thích hộp tool BT-C gồm holder, C collet, phụ kiện được sắp xếp gọn gàng để quản lý và setup nhanh.

- **Tab Ứng Dụng**
  - Giữ 3 performance cards: Lực kẹp cực mạnh, Chống rung tối đa, Heavy-duty (ap>5mm, HRC>50).
  - Section `use-cases applications` trình bày 3 nhóm ứng dụng:
    - Rough Milling (phay thô) với dao Φ20–42, ap=5–10mm.
    - Heavy Cutting trên thép cứng, titan, Inconel.
    - Ngành công nghiệp (khuôn lớn, ô tô hạng nặng, đóng tàu, năng lượng).
  - Thêm outbound link kỹ thuật:
    - Trong đoạn kết luận ngành công nghiệp, chèn link tới tài liệu heavy-duty machining của Sandvik Coromant.
  - Bảng so sánh BT-C vs BT-ER vs BT-OZ:
    - Giữ các tiêu chí: lực kẹp, chống trượt, ứng dụng, chiều sâu cắt, giá thành.
    - Đoạn "Gợi ý chọn lựa" bổ sung internal link:
      - Tới BT-ER Collet Chuck Standard.
      - Tới BT-OZ Heavy Duty Tool Holder.
  - FAQ:
    - Cập nhật câu hỏi 1 (BT-C khác BT-ER như thế nào?) với outbound link tới Wikipedia `Tool_holder`.

- **Tab Liên Hệ**
  - Cấu trúc lại cho giống HM192:
    - `section.support-contact` chứa CTA (Yêu Cầu Báo Giá, Tải Catalog).
    - `div.contact-info-wrapper` bọc `div.contact-info` (4 văn phòng: Hà Nội, TP.HCM, Hải Phòng, Đà Nẵng) và `div.contact-slider-dots`.
    - `div.contact-email-section` (email chung admsales7@anmitools.com).
    - 3 figure hình: hotline desktop, banner địa chỉ, hotline mobile.

- **Schema JSON-LD**
  - Giữ `Product` schema với name, description chứa keyword "bt-c power chuck tool holder", brand An Mi Tools, offers InStock VND.
  - Giữ `BreadcrumbList` với 3 bước: Trang chủ → Tooling Systems → BT-C Power Chuck Tool Holder.

## Thay đổi hôm nay (SEO .seo.md)
- Mở rộng `tags` để tăng khả năng tìm kiếm và nhận diện BT-C:
  - Từ: `"BT-C, power chuck, tool holder, heavy duty machining"`.
  - Thành: `"BT-C, BT C, bt-c power chuck tool holder, power chuck, tool holder, BT side lock, dau kep BT-C, dau kep power chuck, heavy duty machining, rough milling, BT30, BT40, BT50, C collet"`.

- Kiểm tra và bổ sung outbound links:
  - Thêm link tới Sandvik Coromant cho phần heavy-duty machining.
  - Thêm link tới Wikipedia `Tool_holder` cho phần so sánh BT-C vs BT-ER.

- Đảm bảo cấu trúc nội dung chính:
  - Intro: mô tả BT-C, thiết kế 16 rãnh, C collet, tải nặng.
  - 5 tính năng chính.
  - Bảng thông số kỹ thuật (collet, dải kẹp, vật liệu, taper, ứng dụng).
  - Ứng dụng thực tế (rough milling, heavy cutting, ngành công nghiệp hạng nặng).
  - FAQ: so sánh với BT-ER, khuyến nghị dùng BT-GER/HGER cho finishing, loại dao phù hợp.
  - CTA liên hệ + link danh mục hệ thống gá kẹp.

## Liên kết nội bộ giữa BT-C và các mã khác
- Từ BT-C → các mã khác:
  - Trong bảng so sánh và đoạn gợi ý chọn lựa, link tới:
    - BT-ER Collet Chuck Standard.
    - BT-OZ Heavy Duty Tool Holder.

- Từ BT-OZ → BT-C:
  - BT-OZ đã có link sang BT-C trong bảng so sánh và phần "Sản phẩm liên quan".

- Từ BT-ER → BT-C/BT-OZ:
  - Thêm đoạn cuối phần Ứng Dụng của BT-ER với gợi ý dùng BT-C cho rough heavy-duty và BT-OZ cho dao vươn dài chống rung.

## Ghi chú dùng làm chuẩn cho các holder khác
- Dùng layout tabs đồng bộ: Tổng Quan, Thông Số, (Hộp Tool/Phụ Kiện nếu có), Ứng Dụng, Liên Hệ.
- Trong Tổng Quan: luôn có H2 + intro + 5–7 bullet tính năng + block `catalog-download` với icon.
- Thông Số: specs-grid + (nếu có) hình bản vẽ/bảng thông số.
- Hộp Tool/Phụ Kiện: mô tả cấu hình bộ và ảnh hộp/bộ phụ kiện.
- Ứng Dụng: performance cards + use-cases + so sánh với mã liên quan + 1–2 outbound links kỹ thuật.
- Liên Hệ: CTA + 4 văn phòng + email + hình, tái sử dụng pattern `contact-info-wrapper` như HM192.
- SEO markdown: front matter đầy đủ, nội dung có phần intro, 5 tính năng, thông số, ứng dụng, FAQ, CTA, internal + external links.
