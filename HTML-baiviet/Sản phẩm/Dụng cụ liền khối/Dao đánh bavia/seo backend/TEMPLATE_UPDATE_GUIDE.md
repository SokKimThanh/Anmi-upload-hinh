# HƯỚNG DẪN CẬP NHẬT TEMPLATE SEO BACKEND v1.4.0

## Thay đổi chính trong Template v1.4.0 (2025-10-13)

### 1. Thêm trường "Mô tả ngắn" vào phần Identification

**Vị trí:** Mục 1. Identification
**Thêm dòng:** `- Mô tả ngắn: `

**Thứ tự mới trong Identification:**
```markdown
## 1. Identification
- Mã bài: 00
- Nhóm: Overview | Cut Type | Shape | Use Case
- Primary Category (Rank Math): 
- Phân loại bài viết: Overview | Cut Type Detail | Shape Detail | Use Case
- Mô tả ngắn: [MÔ TẢ NGẮN GỌN SẢN PHẨM]
- H1 (Post Title): 
- Slug: 
- Legacy Slugs / Redirect 301 (cũ → mới): 
- Ngôn ngữ: vi-VN
- Pillar Content?: YES / NO
- Word Count Target: Overview 1200–1400 / Detail 800–1000 / Shape 650–850 / Use Case 750–950
```

### 2. Hướng dẫn viết "Mô tả ngắn"

**Nguyên tắc:**
- 1 câu duy nhất, dài 15-25 từ
- Mô tả chức năng chính của sản phẩm
- Không dùng ngôn ngữ marketing/bán hàng
- Phù hợp với persona kỹ thuật viên
- Dựa trên thông tin từ ngữ cảnh CarbideBurr.txt

**Ví dụ chuẩn:**
- **Shape A**: "Dao mài hợp kim dạng trụ phẳng chuyên gia công bề mặt, loại bỏ bavia và tạo biên dạng mặt phẳng trên các chi tiết kim loại."
- **Double Cut**: "Carbide burr răng cắt đôi chuyên gia công thép dưới 60 HRC, inox và vật liệu chịu nhiệt với hiệu suất cao."
- **Type D**: "Dao mài hợp kim dạng cầu chuyên xử lý điểm hàn, loại bỏ bavia đường kính và gia công biên dạng cong."

**Tránh:**
- Ngôn ngữ quảng cáo: "tốt nhất", "hoàn hảo", "độc quyền"
- Số liệu không có trong ngữ cảnh: "tăng 90% hiệu suất"
- Mô tả quá chung chung: "dụng cụ chất lượng cao"

## 3. Cập nhật các file .seo.md hiện có

### Checklist cho từng file:

- [ ] **Kiểm tra Identification:** Có trường "Mô tả ngắn" chưa?
- [ ] **Viết mô tả ngắn:** Dựa theo nguyên tắc trên
- [ ] **Kiểm tra vị trí:** Mô tả ngắn phải ở sau "Phân loại bài viết" và trước "H1"
- [ ] **Cập nhật Changelog:** Thêm dòng v1.4.0 nếu có cập nhật

### Danh sách 28 file cần cập nhật:

#### Overview (01-03):
- [ ] 01-carbide-burr-la-gi.seo.md
- [ ] 02-so-sanh-cac-loai-carbide-burr.seo.md  
- [ ] 03-huong-dan-chon-carbide-burr.seo.md

#### Cut Type (04-10):
- [ ] 04-double-cut-carbide-burr.seo.md
- [ ] 05-single-cut-carbide-burr.seo.md
- [ ] 06-aluminium-cut-carbide-burr.seo.md
- [ ] 07-fine-cut-carbide-burr.seo.md
- [ ] 08-diamond-cut-carbide-burr.seo.md
- [ ] 09-coarse-cut-carbide-burr.seo.md
- [ ] 10-chipbreaker-cut-carbide-burr.seo.md

#### Shape (11-23):
- [x] 11-type-a-carbide-burr.seo.md ✅ (Đã cập nhật)
- [ ] 12-type-b-carbide-burr.seo.md
- [ ] 13-type-c-carbide-burr.seo.md
- [ ] 14-type-d-carbide-burr.seo.md
- [ ] 15-type-e-carbide-burr.seo.md
- [ ] 16-type-f-carbide-burr.seo.md
- [ ] 17-type-g-carbide-burr.seo.md
- [ ] 18-type-h-carbide-burr.seo.md
- [ ] 19-type-j-carbide-burr.seo.md
- [ ] 20-type-k-carbide-burr.seo.md
- [ ] 21-type-l-carbide-burr.seo.md
- [ ] 22-type-m-carbide-burr.seo.md
- [ ] 23-type-n-carbide-burr.seo.md

#### Use Case (24-28):
- [ ] 24-gia-cong-thep-do-cung-cao-carbide-burr.seo.md
- [ ] 25-xu-ly-moi-han-va-inox-carbide-burr.seo.md
- [ ] 26-tao-hinh-chi-tiet-khuon-tinh-carbide-burr.seo.md
- [ ] 27-xu-ly-composite-carbon-carbide-burr.seo.md
- [ ] 28-chuan-bi-be-mat-truoc-phu-carbide-burr.seo.md

## 4. Tham chiếu ngữ cảnh khi viết mô tả

### Mapping từ ngữ cảnh CarbideBurr.txt:

**Cut Types:**
- Double Cut: "Gia công thép < 60 HRC, Inox, Vật liệu chịu nhiệt"
- Cut MX (Single): "Gia công gang, thép, inox < 60 HRC"  
- Cut W (Aluminium): "Gia công nhôm, hợp kim nhôm, kim loại màu, nhựa"
- Cut F (Fine): "Gia công tinh xảo gang, thép < 60 HRC, inox"
- Cut L (Diamond): "Gia công chính xác mọi vật liệu < 60 HRC"
- Cut C (Coarse): "Gia công hoàn thiện kim loại mềm, thép, gang"
- Cut MR (Chip Breaker): "Gia công thép không gỉ, thép và gang"

**Shapes:**
- Type A: "Gia công bề mặt của chi tiết (surface profile)"
- Type B: "Gia công bề mặt và hoán đổi hai mặt vuông góc"
- Type C: "Gia công bề mặt và hình dạng cung tròn"
- Type D: "Gia công cung tròn, loại bỏ burr đường kính, gia công điểm hàn"
- Type E: "Gia công hình dạng cung tròn"
- Type F: "Gia công cung tròn trong không gian hạn chế"
- Type G: "Gia công cung tròn trong không gian hạn chế và hình dạng góc nhọn"
- Type H: "Gia công hình dạng cung tròn"
- Type J: "Gia công vát mép cung doa ngược 60° của phôi"
- Type K: "Gia công hình dạng cung tròn (dạng 90°)"
- Type L: "Gia công các bề mặt và hình dạng hẹp"
- Type M: "Gia công các bề mặt và hình dạng hẹp"
- Type N: "Gia công làm tôn vát bên trong chi tiết (chamfering of inside)"

## 5. Validation checklist cuối cùng

Sau khi cập nhật tất cả file, kiểm tra:

- [ ] **Consistency:** Tất cả file đều có "Mô tả ngắn" ở đúng vị trí
- [ ] **Quality:** Mô tả phù hợp với ngữ cảnh kỹ thuật
- [ ] **No duplication:** Không trùng lặp với Meta Description
- [ ] **Length:** 15-25 từ, 1 câu hoàn chỉnh
- [ ] **Technical accuracy:** Dựa trên thông tin từ CarbideBurr.txt

---
**Cập nhật bởi:** Template v1.4.0  
**Ngày:** 2025-10-13  
**Mục tiêu:** Chuẩn hóa mô tả sản phẩm across 28 bài viết SEO