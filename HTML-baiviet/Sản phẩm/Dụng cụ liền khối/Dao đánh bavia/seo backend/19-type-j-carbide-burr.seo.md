## SEO BACKEND SPEC – 19 (Rank Math v1.2.0)

### 1. Identification
- Mã bài: 19
- Nhóm: Shape Detail
- Primary Category: Carbide Burr
- Phân loại bài viết: Shape Detail
- Mô tả ngắn: Dao mài hợp kim countersink 60 độ chuyên gia công vát mép lỗ và tạo cung doa ngược 60° trên phôi.
- H1: Carbide Burr Shape J (Taper Radius End): Vát bo mềm kiểm soát
- Slug: type-j-carbide-burr
- Legacy Redirects: (trống)
- Pillar Content?: NO (Supporting)
- Word Count Target: 750–900

### 2. Intent & Persona
- Intent: Informational (hoàn thiện vát bo)
- Persona: Người cần vát mép có bo nhẹ hoặc chuyển tiếp góc nghiêng
- Pain: Shape F/G quá thuôn sâu; Shape A/B phẳng tạo gờ
- Outcome: Biết khi nào dùng taper radius để vừa vát vừa bo mép

### 3. Focus Keywords
| Type | Keyword | Use in | Priority | Note |
|------|---------|--------|----------|------|
| Primary | carbide burr shape j | H1, Title | P1 | Core |
| Secondary | burr taper radius j | Intro | P2 | VN variant |
| Secondary | ứng dụng burr shape j | Ứng dụng | P2 | Benefit |
| Long-tail | khi nào dùng burr shape j | FAQ / Khi nào dùng | P3 | Decision |
| Semantic | taper radius burr | 1 lần | P3 | Semantic |

### 4. Meta Fields
- Title: Carbide Burr Shape J (Taper Radius End): Vát bo mềm kiểm soát | AN MI TOOLS
- Meta Description: Shape J vát thuôn với đầu bo giúp tạo vát bo mềm, giảm cạnh sắc. Khi nào chọn thay Shape K hoặc L.
- Canonical: https://anmitools.com/carbide-burr/shape-j/
- Robots: index, follow
- Cornerstone/Pillar: FALSE

### 5. Open Graph & Twitter
- OG Title: Carbide Burr Shape J (Taper Radius End)
- OG Description: Vát bo mềm kiểm soát?
- OG Image: /wp-content/uploads/2025/10/carbide-burr-shape-j-og.webp
- Twitter Card: summary_large_image

### 6. URL & Breadcrumb
```
/carbide-burr/shape-j/
```
Breadcrumb: Trang chủ › Carbide Burr › Shape J

### 7. Content Outline
1. Shape J là gì? (taper radius)
2. Hình học vát + bo → phân bổ áp lực
3. Ứng dụng vát bo mềm (bảng)
4. Khi KHÔNG nên dùng (cần vát sắc / góc nhọn sâu)
5. So với Shape K (point) & L (cone 60°)
6. FAQ
7. CTA

### 8. Internal Linking Plan
| Anchor Text | Target URL | Reason | Placement |
|-------------|------------|--------|-----------|
| so sánh các loại | /carbide-burr/so-sanh/ | Overview | Intro |
| Shape K | /carbide-burr/shape-k/ | Liên kết đối chứng | So sánh |
| Shape L | /carbide-burr/shape-l/ | Góc côn khác | So sánh |
| hướng dẫn chọn | /carbide-burr/huong-dan-chon/ | Funnel | CTA |

### 9. External Links
| Anchor | URL | Purpose | Nofollow? |
|--------|-----|---------|----------|
| taper burr radius note | https://example-taper.org/ | Cơ sở hình học | YES |

### 10. Media / Asset Plan
| # | Type | Purpose | File | Alt Text | Caption? |
|---|------|---------|------|---------|----------|
| 1 | Shape diagram | Hình dạng | shape-j-diagram.webp | Carbide Burr Shape J | Có |
| 2 | Chamfer blend | Vát bo | shape-j-chamfer-blend.webp | Vát bo mềm Shape J | Có |
| 3 | Edge refinement | Làm mượt mép | shape-j-edge-refine.webp | Làm mượt cạnh bằng Shape J | Có |
| 4 | Comparison | So sánh | shape-j-vs-k.webp | Shape J vs K | Có |
| 5 | Limitation | Giới hạn | shape-j-limitation.webp | Giới hạn vát sắc sâu | Có |
| 6 | CTA | Hỗ trợ | shape-j-cta.webp | Tư vấn chọn shape | Có |

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
| Khi nào dùng Shape J? | Khi cần vát bo mềm kiểm soát | Decision | khi nào dùng burr shape j |
| Khác Shape K thế nào? | J bo mềm; K nhọn cho vát sắc | Compare | carbide burr shape j |
| Có thay thế cone 60°? | Không nếu cần góc chính xác | Limit |  |
| Pattern đề xuất? | Double / Fine | Clarify |  |

FAQ JSON: (viết sau)

### 12. Additional Schema
- Article + FAQPage

### 13. Data Boundaries
- Không góc độ chính xác
- Không % cải thiện mép

### 14. CTA Architecture
| CTA Type | Copy | URL/Action | Placement |
|----------|------|-----------|-----------|
| Primary | Gửi biên dạng + vật liệu → gợi ý shape | /contact-us/ | Sau Ứng dụng |
| Secondary | Xem hướng dẫn chọn Burr | /carbide-burr/huong-dan-chon/ | After FAQ |
| Soft Support | Chat kỹ thuật | Chat Widget | Footer |

### 15. Author & E-E-A-T
- Author: (Điền) Kỹ thuật viên dụng cụ
- Reviewed By: (Reviewer) Hoàn thiện mép
- Review Date: 2025-10-08
- E-E-A-T Note: Ứng dụng vát bo mềm thực tế.
- Bio: Hỗ trợ lựa chọn burr xử lý cạnh.

### 16. Human Tone
| Checklist | OK? | Note |
|-----------|-----|------|
| Cụ thể | ✔ | Ứng dụng rõ |
| Minh bạch | ✔ | Giới hạn vát sắc |
| Không phóng đại | ✔ | Không % |
| Hỗ trợ | ✔ | CTA mềm |

### 17. Technical SEO Execution
| Hạng mục | Trạng thái | Ghi chú |
|----------|-----------|---------|
| H1 unique | ✔ | 1 H1 |
| Meta Title | ✔ | OK |
| Meta Description | ✔ | ~156 chars |
| Internal links | ✔ | 4 |
| FAQ block | Pending | Rank Math |
| Schema | ✔ | Article+FAQ |
| OG Image | Pending | 1200x630 |
| Canonical | ✔ | Set |
| Alt text | Pending | Khi upload |
| Redirects | N/A | None |

### Tags
carbide burr shape j, burr taper radius j, ứng dụng burr shape j, khi nào dùng burr shape j, taper radius burr, vát bo mềm, chamfer blend, so sánh shape k l, dao đánh bavia, mũi mài hợp kim, an mi tools

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
| Clicks | 76 |
| Impressions | 1,520 |
| Avg Position | < 26 |
| CTR | > 3.9% |
| Leads | 3 |

### 20. Changelog
- v1.0.0 (2025-10-08): Skeleton tạo theo template v1.2.0
