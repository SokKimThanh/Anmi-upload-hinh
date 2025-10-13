## SEO BACKEND SPEC – 01 (Áp dụng template Rank Math v1.2.0)

### 1. Identification
- Mã bài: 01
- Nhóm: Overview
- Primary Category (Rank Math): Carbide Burr
- Phân loại bài viết: Overview
- Mô tả ngắn: Giới thiệu tổng quan về dao mài hợp kim cứng Carbide Burr, đặc tính vật liệu Tungsten Carbide và ứng dụng chính trong gia công kim loại.
- H1 (Post Title): Carbide Burr là gì? Ứng dụng và cách phân loại trong gia công kim loại
- Slug: carbide-burr-la-gi
- Legacy Slugs / Redirect 301: (trống)
- Ngôn ngữ: vi-VN
- Pillar Content?: YES
- Word Count Target: 1200–1400

### 2. Intent & Persona
- Search Intent: Informational (nền tảng, định nghĩa)
- Primary Persona: Kỹ thuật viên mới, chủ xưởng, nhân viên thu mua
- Pain Points: Bối rối giữa nhiều shape & cut type; không phân biệt với đá mài / end mill
- Desired Outcome: Nắm khái niệm – biết 2 trục phân loại (cut type / shape) – hiểu bước tiếp theo để chọn đúng

### 3. Focus Keywords (Rank Math)
| Type | Keyword | Use in | Priority | Note |
|------|---------|--------|----------|------|
| Primary | carbide burr là gì | H1, Title, Intro, Alt | P1 | Exact intent |
| Secondary | carbide burr ứng dụng | H2, Body | P2 | Ứng dụng chính |
| Secondary | phân loại carbide burr | H2, Body, Anchor | P2 | Liên kết bài 02/03 |
| Long-tail | carbide burr dùng để làm gì | FAQ | P3 | Câu hỏi phổ biến |
| Semantic | tungsten carbide | Body | P3 | Giải thích vật liệu |

### 4. Meta Fields
- Title: Carbide Burr là gì? Ứng dụng & Phân loại | AN MI TOOLS
- Meta Description: Carbide Burr là mũi mài hợp kim dùng để bavia, tạo hình kim loại. Giải thích khái niệm, nhóm cut type & shape. Xem hướng dẫn chọn.
- Canonical: https://anmitools.com/carbide-burr/la-gi/
- Robots: index, follow
- Cornerstone/Pillar: TRUE

### 5. Open Graph & Twitter
- OG Title: Carbide Burr là gì? Ứng dụng & Phân loại
- OG Description: Tổng quan khái niệm, nhóm hình dạng & kiểu cắt.
- OG Image: /wp-content/uploads/2025/10/carbide-burr-la-gi-og.webp (1200x630)
- Twitter Card: summary_large_image

### 6. URL & Breadcrumb
```
/carbide-burr/la-gi/
```
Breadcrumb: Trang chủ › Carbide Burr › Carbide Burr là gì?

### 7. Content Outline (H2/H3)
1. Carbide Burr là gì? (đồng cảm)
2. Cấu trúc & vật liệu (Tungsten Carbide)
3. Phân loại theo Cut Type (tóm tắt 7 nhóm)
4. Phân loại theo Shape A → N
5. Ứng dụng phổ biến (deburring / blending / chamfer)
6. Lợi ích khi chọn đúng
7. Sai lầm thường gặp & cách tránh
8. Checklist chuẩn bị trước khi hỏi tư vấn
9. FAQ

Checklist:
- [x] Single H1
- [x] Không nhảy cấp H2→H4
- [x] H2 mở đầu định hướng thân thiện

### 8. Internal Linking Plan
| Anchor Text | Target URL | Reason | Placement |
|-------------|------------|--------|-----------|
| so sánh các loại | /carbide-burr/so-sanh/ | Điều hướng sang bài 02 | Section 3 kết thúc |
| kiểu cắt Double Cut | /carbide-burr/double-cut/ | Đi sâu Cut Type quan trọng | Section 3 list |
| hình dạng Type A | /carbide-burr/type-a/ | Ví dụ shape thực tế | Section 4 grid |
| hướng dẫn chọn | /carbide-burr/huong-dan-chon/ | Chuyển sang quy trình quyết định | Checklist CTA |

Hub Matrix: Overview (01) trỏ xuống So sánh (02) & Hướng dẫn chọn (03); từ đây lan sang Cut Type & Shape.

### 9. External Links
| Anchor | URL | Purpose | Nofollow? |
|--------|-----|---------|----------|
| tungsten carbide material | https://en.wikipedia.org/wiki/Tungsten_carbide | Xác thực vật liệu | NO |

### 10. Media / Asset Plan
| # | Type | Purpose | File | Alt Text | Caption? |
|---|------|---------|------|---------|----------|
| 1 | Hero | Nhận diện chủ đề | carbide-burr-overview-hero.webp | Carbide Burr overview – shape A đến N | (Có) |
| 2 | Diagram | Phân nhóm shape | carbide-burr-shape-chart.webp | Sơ đồ 14 hình dạng Carbide Burr | (Có) |
| 3 | Cut Type Tile | Nhóm cut type | carbide-burr-cut-type-grid.webp | Các kiểu cắt Double / Single / Aluminium... | (Có) |
| 4 | Use Case | Thực tế deburring | carbide-burr-application-deburring.webp | Dùng Burr loại bỏ bavia thép | (Có) |
| 5 | CTA | Đội hỗ trợ | trang-30_tools_diachi-editbyAI.webp | Đội ngũ kỹ thuật ANMI TOOLS | (Có) |

Guidelines: WebP ≥1000px, lazy, alt mô tả hành động; không dùng tính từ phóng đại.

### 10b. Technical Support Image (Footer, required)
- Placement: Cuối bài (sau CTA)
- Source: https://anmitools.com/wp-content/uploads/2025/09/trang-30_tools_diachi-editbyAI.webp
- Requirements: width="1000", loading="lazy", alt trung thực, có figcaption ngắn
- HTML pattern:

```html
<figure class="wp-block-image">
	<img src="https://anmitools.com/wp-content/uploads/2025/09/trang-30_tools_diachi-editbyAI.webp"
			 alt="Đội ngũ kỹ thuật AN MI TOOLS hỗ trợ tư vấn và bảo dưỡng dụng cụ"
			 width="1000" height="560" loading="lazy">
	<figcaption>AN MI TOOLS đồng hành cùng xưởng từ tư vấn lựa chọn đến bảo dưỡng dụng cụ.</figcaption>
</figure>
```

### 11. FAQ Planning
| Q | A (tóm tắt) | Intent | Keyword Target? |
|---|-------------|--------|-----------------|
| Carbide Burr dùng để làm gì? | Dùng để loại bavia, tạo hình, làm mịn mép kim loại | Clarify | Long-tail |
| Có bao nhiêu loại Carbide Burr? | 2 trục chính: Cut Type (7) & Shape (A→N ~14) | Inform | phân loại |
| Khác gì đá mài hoặc end mill? | Burr để hoàn thiện linh hoạt, không thay thế phay chính xác | Differentiate | carbide burr là gì |
| Vì sao phải chọn đúng kiểu cắt? | Ảnh hưởng kiểm soát phoi, rung và tuổi thọ | Pain-solution | carbide burr ứng dụng |

FAQ JSON (draft):
```json
{
	"@context": "https://schema.org",
	"@type": "FAQPage",
	"mainEntity": [
		{"@type":"Question","name":"Carbide Burr dùng để làm gì?","acceptedAnswer":{"@type":"Answer","text":"Carbide Burr dùng để loại bỏ bavia, bo mép và tinh chỉnh chi tiết kim loại nhanh ở giai đoạn hoàn thiện."}},
		{"@type":"Question","name":"Có bao nhiêu loại Carbide Burr?","acceptedAnswer":{"@type":"Answer","text":"Phân loại theo 7 kiểu cắt (Double, Single, Aluminium, Fine, Diamond, Coarse, Chip Breaker) và 14 shape từ Type A đến Type N."}},
		{"@type":"Question","name":"Carbide Burr khác gì đá mài?","acceptedAnswer":{"@type":"Answer","text":"Đá mài thường dùng mài phẳng hoặc loại bỏ vật liệu rộng; Burr linh hoạt ở bavia, rãnh, biên dạng nhỏ – không thay thế end mill chính xác."}},
		{"@type":"Question","name":"Vì sao phải chọn đúng kiểu cắt?","acceptedAnswer":{"@type":"Answer","text":"Chọn đúng giúp phoi ngắn, giảm rung, kéo dài tuổi thọ dụng cụ và đạt bề mặt mong muốn."}}
	]
}
```

### 12. Additional Schema
- Type: Article + FAQPage + BreadcrumbList
- Product schema: BỎ (không giá / SKU) – tránh dữ liệu thiếu
- Notes: Có thể thêm HowTo nếu sau bổ sung quy trình chi tiết

### 13. Data Boundaries & Transparency
- Thiếu: RPM chi tiết, bảng kích thước D-L-d đầy đủ, coating nếu có phiên bản phủ.
- Nguyên tắc: Không suy đoán tốc độ, không gán kích thước chưa xác thực, không mô tả coating nếu catalogue chưa xuất.

### 14. CTA Architecture
| CTA Type | Copy | URL/Action | Placement |
|----------|------|-----------|-----------|
| Primary | Gửi 3 thông tin ứng dụng → nhận gợi ý burr phù hợp | /contact-us/ | Sau Checklist |
| Secondary | Khám phá 14 hình dạng burr phổ biến | /carbide-burr/shape-overview/ (hoặc trang shape hub) | Sau Section 4 |
| Soft Support | Chat nhanh: gửi ảnh chi tiết để nhận gợi ý | Chat Widget | After FAQ |

Tone: Hỗ trợ, trung lập – tránh hứa hẹn tuyệt đối.

### 15. Author & E-E-A-T Block
- Author Name: (Điền) – ví dụ: Nguyễn Văn A
- Author Role: Kỹ thuật viên dụng cụ cắt
- Years Experience: 7
- Reviewed By: (Điền nếu có) – Trưởng bộ phận kỹ thuật
- Review Date: 2025-10-08
- E-E-A-T Note: Nội dung tổng hợp từ catalogue & kinh nghiệm hỗ trợ xưởng thực tế.
- Bio Snippet: 7 năm hỗ trợ tối ưu quy trình hoàn thiện kim loại & lựa chọn dụng cụ mài hợp kim.

### 16. Human Tone (A1 Compliance)
| Checklist | OK? | Note |
|-----------|-----|------|
| Mở bài có đồng cảm | ✔ | “Bạn có thể bối rối...” |
| Có trao quyền (Checklist) | ✔ | Section 8 |
| Cảnh báo dữ liệu thiếu minh bạch | ✔ | Section 13 |
| CTA không gây áp lực | ✔ | Ngôn ngữ trung lập |
| Không phóng đại tuyệt đối | ✔ | Không dùng “tốt nhất” |
| Tránh lặp keyword vô lý | ✔ | Mật độ tự nhiên |

### 17. Technical SEO Execution
| Hạng mục | Trạng thái | Ghi chú |
|----------|-----------|---------|
| H1 unique | ✔ | 1 H1 |
| Meta Title length ok | ✔ | ~55 chars |
| Meta Description length ok | ✔ | ~153 chars |
| 3–5 Internal links | ✔ | 4 anchors |
| FAQ block valid | Pending | Thêm block Rank Math trong WP |
| Schema loại chính | ✔ | Article + FAQ |
| OG Image đúng size | Pending | Cần tạo 1200x630 |
| Canonical set | ✔ | Khai báo rõ |
| No duplicate intent | ✔ | Khác bài 02/03 |
| Alt text đầy đủ | Pending | Khi upload media |
| Redirects active | N/A | Không có slug cũ |

### Tags
carbide burr, dao đánh bavia, mũi mài hợp kim, rotary burr, tungsten carbide, an mi tools, carbide burr là gì, ứng dụng carbide burr, phân loại carbide burr, deburring tool, metal finishing

### 18. Performance & Core Web Vitals (Post-Publish)
| Metric | Target | Current | Note |
|--------|--------|---------|------|
| LCP | < 2.5s |  |  |
| CLS | < 0.1 |  |  |
| INP | < 200ms |  |  |
| Total Size | < 1.0MB |  |  |

### 19. Success Metrics (90 days)
| Metric | Target |
|--------|--------|
| Clicks | 250 |
| Impressions | 5,000 |
| Avg Position | < 18 |
| CTR | > 4% |
| Leads | 15 |

### 20. Revision / Changelog
- v1.0.0 (2025-10-08): Khởi tạo spec ban đầu
- v1.1.0 (2025-10-08): Cập nhật theo template Rank Math (Primary Category, Author, FAQ JSON, CTA architecture)
