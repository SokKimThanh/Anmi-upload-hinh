Kế hoạch học tập 15 ngày: Xây dựng Trung tâm Tải tài liệu trên WordPress
========================================================================

Giới thiệu
----------

Tài liệu này hướng dẫn bạn từng bước từ khái niệm cơ bản đến triển khai một Download Center sẵn sàng vận hành trên WordPress. Trong 15 ngày, bạn sẽ cân bằng giữa lý thuyết và thực hành, với mỗi buổi chỉ tập trung vào một chủ đề cụ thể để đảm bảo tiếp thu hiệu quả.

Điều kiện tiên quyết
--------------------

-   Đã cài đặt WordPress (bản mới nhất) trên máy chủ hoặc môi trường local.

-   Quyền quản trị đầy đủ (Dashboard, FTP/SSH).

-   Hiểu biết cơ bản về PHP, HTML, CSS.

-   Trình soạn thảo mã (VS Code, PhpStorm...) và Git (khuyến khích).

Tổng quan kế hoạch
------------------

Mỗi ngày chia làm hai phần:

1.  Lý thuyết & khái niệm

2.  Bài tập thực hành

Dành 1--2 giờ mỗi ngày và giữ phạm vi nhỏ để nắm vững trước khi chuyển sang chủ đề tiếp theo.

Lịch trình chi tiết
-------------------

### Ngày 1: Cơ bản về WordPress

Lý thuyết

-   Kiến trúc CMS: theme, plugin, hook, template hierarchy

-   Cách WordPress xử lý nội dung và URL

Thực hành

1.  Cài WordPress local (Local by Flywheel, XAMPP...).

2.  Khám phá thư mục wp-content, wp-includes, wp-admin.

### Ngày 2: Child Theme & Tùy chỉnh

Lý thuyết

-   Mục đích và cấu trúc child theme

-   Đăng ký và enqueue file CSS/JS

Thực hành

1.  Tạo child theme của Twenty Twenty-Three.

2.  Enqueue stylesheet và script tùy chỉnh.

### Ngày 3: Custom Post Type & Taxonomy

Lý thuyết

-   Hàm register_post_type() và register_taxonomy()

-   Khi nào nên dùng CPT thay vì category/tag

Thực hành

1.  Đăng ký CPT "Download".

2.  Tạo taxonomy "Loại tài liệu" và "Chủ đề".

### Ngày 4: Advanced Custom Fields (ACF)

Lý thuyết

-   Giới thiệu plugin ACF và các nhóm trường (Field Group)

-   Loại trường phù hợp cho file, lựa chọn, văn bản

Thực hành

1.  Cài và kích hoạt ACF.

2.  Thêm nhóm trường chứa file, text, select cho CPT Download.

### Ngày 5: Thư viện Phương tiện & Quản lý tệp

Lý thuyết

-   So sánh Media Library và upload thủ công qua FTP

-   Cấu trúc URL và mime type

Thực hành

1.  Upload PDF và hình ảnh qua Media Library.

2.  Gắn kết tệp vào bài Download.

### Ngày 6: Khám phá plugin Download Center

Lý thuyết

-   Ưu/nhược điểm của plugin chuyên dụng

-   Tổng quan Download Monitor, WP File Download, Easy Digital Downloads

Thực hành

1.  Cài plugin Download Monitor.

2.  Tạo mục tải mẫu và kiểm thử liên kết download.

### Ngày 7: Tùy chỉnh Download Monitor

Lý thuyết

-   Shortcode, template override, hook của plugin

-   Theo dõi và giới hạn lượt tải

Thực hành

1.  Hiển thị danh sách download qua shortcode.

2.  Kích hoạt và xem thống kê download.

### Ngày 8: Xây dựng Download Center thủ công

Lý thuyết

-   So sánh cách tự code và plugin

-   Các hàm add_meta_box(), save_post(), wp_nonce_field()

Thực hành

1.  Thêm meta box nhập URL tệp cho CPT Download.

2.  Lưu và hiển thị URL trên front-end.

### Ngày 9: Metadata & File Attachment

Lý thuyết

-   Hàm wp_handle_upload()

-   Lưu ID attachment so với URL thô

Thực hành

1.  Tạo form upload file trong admin.

2.  Validate, lưu và attach file vào bài Download.

### Ngày 10: Template & Danh sách hiển thị

Lý thuyết

-   WP_Query cho CPT loop

-   Tên file template archive-download.php

Thực hành

1.  Xây dựng template archive hiển thị tất cả download.

2.  Hiển thị tiêu đề, mô tả ngắn và nút download.

### Ngày 11: Bộ lọc & Tìm kiếm

Lý thuyết

-   Tham số tax_query, meta_query

-   Tích hợp AJAX search hoặc FacetWP

Thực hành

1.  Thêm dropdown lọc theo "Loại tài liệu" và "Chủ đề".

2.  Xây dựng tìm kiếm AJAX cho downloads.

### Ngày 12: Phân quyền & Kiểm soát truy cập

Lý thuyết

-   current_user_can(), custom capability

-   Chặn tải theo vai trò

Thực hành

1.  Chỉ cho phép user đã đăng nhập tải tài liệu.

2.  Tạo role "Subscriber" với quyền download.

### Ngày 13: Bảo mật & Hiệu suất

Lý thuyết

-   Nonce, sanitization, capability check

-   Caching kết quả bằng Transients API

Thực hành

1.  Bổ sung nonce cho handler download.

2.  Cache danh sách downloads với transient.

### Ngày 14: UX, Styling & Responsive

Lý thuyết

-   Thiết kế mobile-first

-   Accessibility: ARIA labels, button rõ ràng

Thực hành

1.  Tùy chỉnh giao diện Download Center với CSS hoặc framework.

2.  Kiểm thử trên thiết bị di động và điều chỉnh.

### Ngày 15: Kiểm thử, Triển khai & Tài liệu

Lý thuyết

-   Cross-browser test, backup, migration

-   Viết hướng dẫn sử dụng và changelog

Thực hành

1.  Chạy thử toàn bộ chức năng và kiểm tra phân quyền.

2.  Viết README.md tóm tắt cách cài đặt, tính năng và bảo trì.

Các bước kế tiếp
----------------

-   Tích hợp thông báo email khi có download mới.

-   Triển khai tính năng download trả phí với Easy Digital Downloads.
