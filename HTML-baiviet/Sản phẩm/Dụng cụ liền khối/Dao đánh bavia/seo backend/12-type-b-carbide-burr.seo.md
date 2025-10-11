## SEO BACKEND SPEC – 12 (Rank Math v1.2.0)

### 1. Identification
- Mã bài: 12
- Nhóm: Shape Detail
- Primary Category: Carbide Burr
- H1: Carbide Burr Shape B (Cylinder End Rounded): Xử lý mép bo & chuyển tiếp phẳng
- Slug: type-b-carbide-burr
- Legacy Redirects: (trống)
- Pillar Content?: NO (Supporting)
- Word Count Target: 700–850

### 2. Intent & Persona
- Intent: Informational hình học ứng dụng
- Persona: Người cần phá bavia mép phẳng nhưng giữ bo mượt
- Pain: Mép bị cắt sắc hoặc tạo rãnh khi dùng Shape A
- Outcome: Biết khi nào chọn Cylinder đầu tròn Shape B

### 3. Focus Keywords
| Type | Keyword | Use in | Priority | Note |
|------|---------|--------|----------|------|
| Primary | carbide burr shape b | H1, Title | P1 | Core |
| Secondary | burr trụ đầu tròn b | Intro | P2 | VN variant |
| Secondary | ứng dụng burr shape b | Ứng dụng | P2 | Benefit |
| Long-tail | khi nào dùng burr shape b | FAQ / Khi nào dùng | P3 | Decision |
| Semantic | cylinder end rounded burr | 1 lần | P3 | Semantic |

### 4. Meta Fields
- Title: Carbide Burr Shape B (Cylinder End Rounded): Xử lý mép bo & chuyển tiếp phẳng | AN MI TOOLS
- Meta Description: Shape B (Cylinder đầu tròn) giúp gọt phẳng kèm chuyển tiếp bo mượt, giảm tạo rãnh sắc. Khi nào nên chọn thay Shape A.
- Canonical: https://anmitools.com/carbide-burr/shape-b/
- Robots: index, follow
- Cornerstone/Pillar: FALSE

### 5. Open Graph & Twitter
- OG Title: Carbide Burr Shape B (Cylinder End Rounded)
- OG Description: Xử lý mép bo & chuyển tiếp phẳng?
- OG Image: /wp-content/uploads/2025/10/carbide-burr-shape-b-og.webp
- Twitter Card: summary_large_image

### 6. URL & Breadcrumb
```
/carbide-burr/shape-b/
```
Breadcrumb: Trang chủ › Carbide Burr › Shape B

### 7. Content Outline
1. Shape B là gì? (đầu bo)
2. Ưu thế xử lý mép & chuyển tiếp
3. Ứng dụng tiêu biểu (bảng)
4. Khi KHÔNG nên dùng (góc sắc cần giữ cạnh)
5. So với Shape A / D
6. FAQ
7. CTA

### 8. Internal Linking Plan
| Anchor Text | Target URL | Reason | Placement |
|-------------|------------|--------|-----------|
| so sánh các loại | /carbide-burr/so-sanh/ | Overview | Intro |
| Shape A | /carbide-burr/shape-a/ | Liên kết liền kề | So sánh |
| hướng dẫn chọn | /carbide-burr/huong-dan-chon/ | Funnel | CTA |
| Double Cut | /carbide-burr/double-cut/ | Pattern phổ biến | Ứng dụng |

### 9. External Links
| Anchor | URL | Purpose | Nofollow? |
|--------|-----|---------|----------|
| edge rounding burr note | https://example-edge.org/ | Xác nhận ứng dụng bo | YES |

### 10. Media / Asset Plan
| # | Type | Purpose | File | Alt Text | Caption? |
|---|------|---------|------|---------|----------|
| 1 | Shape diagram | Hình dạng | shape-b-diagram.webp | Carbide Burr Shape B | Có |
| 2 | Edge rounding | Mép bo | shape-b-edge-rounding.webp | Bo mép bằng Shape B | Có |
| 3 | Transition | Chuyển tiếp mượt | shape-b-transition.webp | Chuyển tiếp phẳng bo | Có |
| 4 | Comparison | So sánh | shape-b-vs-a.webp | Shape B vs A | Có |
| 5 | Limitation | Giới hạn | shape-b-limitation.webp | Hạn chế giữ cạnh sắc | Có |
| 6 | CTA | Hỗ trợ | shape-b-cta.webp | Tư vấn chọn shape | Có |

### 10b. Technical Support Image (Footer, required)
- Placement: Khối ảnh cuối bài (sau CTA), để tăng độ tin cậy và hỗ trợ liên hệ.
- Source (absolute URL):
	https://anmitools.com/wp-content/uploads/2025/09/trang-30_tools_diachi-editbyAI.webp
- CSS compliance: Dùng cấu trúc figure.wp-block-image + figcaption (không cần class riêng). `style-carbide-burr.css` sẽ tự áp.
- Requirements:
	- width="1000", height khai báo theo ảnh thực tế, loading="lazy"
	- Alt mô tả trung thực (không nhồi từ khóa)
	- Có figcaption, giọng tự nhiên, ngắn gọn

HTML pattern (chèn ở cuối bài):

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
| Khi nào dùng Shape B? | Khi cần gọt phẳng kết hợp bo nhẹ | Decision | khi nào dùng burr shape b |
| Có thay Shape A được không? | Trong ứng dụng cần giữ cạnh sắc thì không | Limit | carbide burr shape b |
| Có gây mất nhiều vật liệu mép? | Ít – kiểm soát bằng áp lực | Clarify |  |
| Pattern nào hợp? | Double cho inox/thép, Aluminium cho nhôm | Clarify |  |

FAQ JSON: (viết sau)

### 12. Additional Schema
- Article + FAQPage

### 13. Data Boundaries
- Không % giảm bavia
- Không kích thước radius bo

### 14. CTA Architecture
| CTA Type | Copy | URL/Action | Placement |
|----------|------|-----------|-----------|
| Primary | Gửi vật liệu + yêu cầu mép → gợi ý shape | /contact-us/ | Sau Ứng dụng |
| Secondary | Xem hướng dẫn chọn Burr | /carbide-burr/huong-dan-chon/ | After FAQ |
| Soft Support | Chat kỹ thuật | Chat Widget | Footer |

### 15. Author & E-E-A-T
- Author: (Điền) Kỹ thuật viên dụng cụ
- Reviewed By: (Reviewer) Mép & hoàn thiện
- Review Date: 2025-10-08
- E-E-A-T Note: Dựa nhu cầu bo mép sản phẩm.
- Bio: Hỗ trợ kiểm soát mép & bavia.

### 16. Human Tone
| Checklist | OK? | Note |
|-----------|-----|------|
| Cụ thể | ✔ | Ứng dụng rõ |
| Minh bạch | ✔ | Giới hạn cạnh sắc |
| Không phóng đại | ✔ | Không % |
| Hỗ trợ | ✔ | CTA mềm |

### 17. Technical SEO Execution
| Hạng mục | Trạng thái | Ghi chú |
|----------|-----------|---------|
| H1 unique | ✔ | 1 H1 |
| Meta Title | ✔ | OK |
| Meta Description | ✔ | ~158 chars |
| Internal links | ✔ | 4 |
| FAQ block | Pending | Rank Math |
| Schema | ✔ | Article+FAQ |
| OG Image | Pending | 1200x630 |
| Canonical | ✔ | Set |
| Alt text | Pending | Khi upload |
| Redirects | N/A | None |

### Tags
carbide burr shape b, burr trụ đầu tròn b, ứng dụng burr shape b, khi nào dùng burr shape b, cylinder end rounded burr, chuyển tiếp phẳng bo, xử lý mép bo, so sánh shape a d, dao đánh bavia, mũi mài hợp kim, an mi tools

### 18. Performance (Post-publish)
| Metric | Target | Current | Note |
|--------|--------|---------|------|
| LCP | <2.5s |  |  |
| CLS | <0.1 |  |  |
| INP | <200ms |  |  |
| Total Size | <0.8MB |  |  |

### 19. Success Metrics (90d)
| Metric | Target |
|--------|--------|
| Clicks | 70 |
| Impressions | 1,400 |
| Avg Position | < 26 |
| CTR | > 3.8% |
| Leads | 3 |

### 20. Changelog
- v1.0.0 (2025-10-08): Skeleton tạo theo template v1.2.0
