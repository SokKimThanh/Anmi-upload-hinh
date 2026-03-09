# CẤU HÌNH FORM ỨNG TUYỂN – CONTACT FORM 7
> WordPress → Contact → Thêm Form mới

---

## FORM 1 – ỨNG TUYỂN NVBH PHÍA BẮC

**Tên form:** `Ứng tuyển NVBH – Phía Bắc`

---

### Tab "Nội dung Form"

```
<label> Họ và tên *
    [text* your-name autocomplete:name placeholder "Nguyễn Văn A"] </label>

<label> Email *
    [email* your-email id:your-email autocomplete:email placeholder "example@email.com"] </label>

<label> Số điện thoại *
    [tel* your-phone autocomplete:tel placeholder "09x xxx xxxx"] </label>

<label> Khu vực ứng tuyển *
    [select* your-region "-- Chọn khu vực --" "Hà Nội" "Hải Phòng" "Bắc Ninh" "Hưng Yên" "Đà Nẵng"] </label>

<label> Vị trí ứng tuyển *
    [select* your-position "-- Chọn vị trí --" "Nhân viên Kinh doanh Kỹ thuật (NVBH)" "Trợ lý Bán hàng (TLBH)"] </label>

<label> Trình độ học vấn *
    [select* your-education "-- Chọn trình độ --" "Cao đẳng" "Đại học" "Sau đại học"] </label>

<label> Số năm kinh nghiệm Sales
    [select your-experience "-- Chọn kinh nghiệm --" "Chưa có kinh nghiệm" "Dưới 1 năm" "1 – 2 năm" "3 – 5 năm" "Trên 5 năm"] </label>

<label> Tải lên CV (PDF hoặc Word, tối đa 5MB) *
    [file* your-cv limit:5mb filetypes:pdf|doc|docx] </label>

<label> Giới thiệu ngắn (không bắt buộc)
    [textarea your-message placeholder "Chia sẻ kinh nghiệm, điểm mạnh hoặc lý do bạn muốn ứng tuyển tại ANMI Tools..."] </label>

<label> [acceptance your-terms] Tôi đồng ý để ANMI Tools lưu trữ và xử lý thông tin cá nhân của tôi cho mục đích tuyển dụng. </label>

[submit "Nộp hồ sơ ứng tuyển"]
```

> ⚠️ **Lưu ý sau khi lưu form:** Vào **WordPress → Cài đặt → Thư mục tải lên** kiểm tra quyền ghi thư mục `wp-content/uploads`. CF7 cần quyền này để nhận file đính kèm.

---

### Tab "Cấu hình Mail" – Mail nhận về cho Ms. Lan Phương

| Trường | Nội dung điền |
|---|---|
| **Mail nhận (To)** | `admsales7@anmitools.com` |
| **Mail gửi (From)** | `ANMI Tools <wordpress@anmitools.com>` |
| **Tiêu đề (Subject)** | `[NVBH-BẮC] – [your-name] – [your-region]` |
| **Tiêu đề bổ sung** | `Reply-To: [your-email]` |
| **Tệp đính kèm** | `[your-cv]` *(điền vào ô Tệp đính kèm – CF7 tự gửi file CV kèm email)* |

> ⚠️ **Cảnh báo `!` bên cạnh `[your-email]` ở ô Mail nhận:** Đây là thông báo bảo mật **cố ý của CF7 từ bản 5.6+** — **không thể tắt** vì `[your-email]` là giá trị do người dùng nhập. Form **vẫn gửi email tự động đúng về ứng viên** bình thường. **Bỏ qua cảnh báo này.**

**Nội dung tin nhắn:**

```
===== HỒ SƠ ỨNG TUYỂN MỚI – PHÍA BẮC =====

Họ tên      : [your-name]
Email        : [your-email]
Điện thoại  : [your-phone]
Khu vực     : [your-region]
Vị trí       : [your-position]
Học vấn     : [your-education]
Kinh nghiệm : [your-experience]
Link CV      : [your-cv]

Giới thiệu bản thân:
[your-message]

---
Người phụ trách: Ms. Lan Phương – admsales7@anmitools.com
Gửi từ website: anmitools.com
```

- [x] Không bao gồm các dòng chứa mail tag trống

---

### Tab "Cấu hình Mail" – Mail kiểu 2: Trả lời tự động cho ứng viên

> Tích vào **"Bật cấu hình Mail này"**

| Trường | Nội dung điền |
|---|---|
| **Mail nhận (To)** | `[your-email]` |
| **Mail gửi (From)** | `ANMI Tools Tuyển Dụng <wordpress@anmitools.com>` |
| **Tiêu đề (Subject)** | `ANMI Tools đã nhận hồ sơ của bạn – [your-name]` |
| **Tiêu đề bổ sung** | *(để trống)* |

**Nội dung tin nhắn:**

```
Xin chào [your-name],

Cảm ơn bạn đã ứng tuyển vị trí [your-position] tại ANMI Tools – khu vực [your-region].

Chúng tôi đã nhận được hồ sơ và sẽ xem xét trong vòng 2–3 ngày làm việc.
Nếu hồ sơ phù hợp, Ms. Lan Phương sẽ liên hệ với bạn qua email hoặc số điện thoại đã đăng ký.

Trân trọng,
Bộ phận Tuyển dụng – ANMI Tools
Email: admsales7@anmitools.com
Website: https://anmitools.com
```

---
---

## FORM 2 – ỨNG TUYỂN NVBH PHÍA NAM

**Tên form:** `Ứng tuyển NVBH – Phía Nam`

---

### Tab "Nội dung Form"

```
<label> Họ và tên *
    [text* your-name autocomplete:name placeholder "Nguyễn Văn A"] </label>

<label> Email *
    [email* your-email id:your-email autocomplete:email placeholder "example@email.com"] </label>

<label> Số điện thoại *
    [tel* your-phone autocomplete:tel placeholder "09x xxx xxxx"] </label>

<label> Khu vực ứng tuyển *
    [select* your-region "-- Chọn khu vực --" "TP. Hồ Chí Minh" "Đồng Nai" "Vũng Tàu"] </label>

<label> Vị trí ứng tuyển *
    [select* your-position "-- Chọn vị trí --" "Nhân viên Kinh doanh Kỹ thuật (NVBH)" "Trợ lý Bán hàng (TLBH)"] </label>

<label> Trình độ học vấn *
    [select* your-education "-- Chọn trình độ --" "Cao đẳng" "Đại học" "Sau đại học"] </label>

<label> Số năm kinh nghiệm Sales
    [select your-experience "-- Chọn kinh nghiệm --" "Chưa có kinh nghiệm" "Dưới 1 năm" "1 – 2 năm" "3 – 5 năm" "Trên 5 năm"] </label>

<label> Tải lên CV (PDF hoặc Word, tối đa 5MB) *
    [file* your-cv limit:5mb filetypes:pdf|doc|docx] </label>

<label> Giới thiệu ngắn (không bắt buộc)
    [textarea your-message placeholder "Chia sẻ kinh nghiệm, điểm mạnh hoặc lý do bạn muốn ứng tuyển tại ANMI Tools..."] </label>

<label> [acceptance your-terms] Tôi đồng ý để ANMI Tools lưu trữ và xử lý thông tin cá nhân của tôi cho mục đích tuyển dụng. </label>

[submit "Nộp hồ sơ ứng tuyển"]
```

> ⚠️ **Lưu ý sau khi lưu form:** Vào **WordPress → Cài đặt → Thư mục tải lên** kiểm tra quyền ghi thư mục `wp-content/uploads`. CF7 cần quyền này để nhận file đính kèm.

---

### Tab "Cấu hình Mail" – Mail nhận về cho Ms. Thảo

| Trường | Nội dung điền |
|---|---|
| **Mail nhận (To)** | `admsales14@anmitools.com` |
| **Mail gửi (From)** | `ANMI Tools <wordpress@anmitools.com>` |
| **Tiêu đề (Subject)** | `[NVBH-NAM] – [your-name] – [your-region]` |
| **Tiêu đề bổ sung** | `Reply-To: [your-email]` |
| **Tệp đính kèm** | `[your-cv]` *(điền vào ô Tệp đính kèm – CF7 tự gửi file CV kèm email)* |

> ⚠️ **Cảnh báo `!` bên cạnh `[your-email]` ở ô Mail nhận:** Đây là thông báo bảo mật **cố ý của CF7 từ bản 5.6+** — **không thể tắt** vì `[your-email]` là giá trị do người dùng nhập. Form **vẫn gửi email tự động đúng về ứng viên** bình thường. **Bỏ qua cảnh báo này.**

**Nội dung tin nhắn:**

```
===== HỒ SƠ ỨNG TUYỂN MỚI – PHÍA NAM =====

Họ tên      : [your-name]
Email        : [your-email]
Điện thoại  : [your-phone]
Khu vực     : [your-region]
Vị trí       : [your-position]
Học vấn     : [your-education]
Kinh nghiệm : [your-experience]
Link CV      : [your-cv]

Giới thiệu bản thân:
[your-message]

---
Người phụ trách: Ms. Thảo – admsales14@anmitools.com
Gửi từ website: anmitools.com
```

- [x] Không bao gồm các dòng chứa mail tag trống

---

### Tab "Cấu hình Mail" – Mail kiểu 2: Trả lời tự động cho ứng viên

> Tích vào **"Bật cấu hình Mail này"**

| Trường | Nội dung điền |
|---|---|
| **Mail nhận (To)** | `[your-email]` |
| **Mail gửi (From)** | `ANMI Tools Tuyển Dụng <wordpress@anmitools.com>` |
| **Tiêu đề (Subject)** | `ANMI Tools đã nhận hồ sơ của bạn – [your-name]` |
| **Tiêu đề bổ sung** | *(để trống)* |

**Nội dung tin nhắn:**

```
Xin chào [your-name],

Cảm ơn bạn đã ứng tuyển vị trí [your-position] tại ANMI Tools – khu vực [your-region].

Chúng tôi đã nhận được hồ sơ và sẽ xem xét trong vòng 2–3 ngày làm việc.
Nếu hồ sơ phù hợp, Ms. Thảo sẽ liên hệ với bạn qua email hoặc số điện thoại đã đăng ký.

Trân trọng,
Bộ phận Tuyển dụng – ANMI Tools
Email: admsales14@anmitools.com
Website: https://anmitools.com
```

---

## NHÚNG FORM VÀO TRANG /UNG-TUYEN/

Sau khi lưu 2 form, WordPress cấp shortcode. Dán vào trang `/ung-tuyen/`:

```
<h3>Khu vực Phía Bắc – Hà Nội, Hải Phòng, Bắc Ninh, Hưng Yên, Đà Nẵng</h3>
[contact-form-7 id="1f70f3a" title="Ứng tuyển NVBH – Phía Bắc"]

<h3>Khu vực Phía Nam – TP. HCM, Đồng Nai, Vũng Tàu</h3>
[contact-form-7 id="YY" title="Ứng tuyển NVBH – Phía Nam"]
```

> Thay `YY` bằng ID thực tế của Form 2 sau khi lưu.
