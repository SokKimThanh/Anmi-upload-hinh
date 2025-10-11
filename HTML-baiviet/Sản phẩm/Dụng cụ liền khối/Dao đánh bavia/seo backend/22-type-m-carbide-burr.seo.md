## SEO BACKEND SPEC – 22 (Rank Math v1.2.0)

### 1. Identification
- Mã bài: 22
- Nhóm: Shape Detail
- Primary Category: Carbide Burr
- H1: Carbide Burr Shape M (Cone 90°): Vát góc vuông & mép lỗ lớn
- Slug: type-m-carbide-burr
- Legacy Redirects: (trống)
- Pillar Content?: NO (Supporting)
- Word Count Target: 700–850

### 2. Intent & Persona
- Intent: Informational (vát 90° / miệng lỗ lớn)
- Persona: Người cần vát mép lỗ / cạnh theo 90° chính xác hơn
- Pain: Shape L không đúng góc; taper point không đều
- Outcome: Biết khi nào chọn Cone 90° & giới hạn linh hoạt

### 3. Focus Keywords
| Type | Keyword | Use in | Priority | Note |
|------|---------|--------|----------|------|
| Primary | carbide burr shape m | H1, Title | P1 | Core |
| Secondary | burr cone 90 do m | Intro | P2 | VN variant |
| Secondary | ứng dụng burr shape m | Ứng dụng | P2 | Benefit |
| Long-tail | khi nào dùng burr shape m | FAQ / Khi nào dùng | P3 | Decision |
| Semantic | 90 degree cone burr | 1 lần | P3 | Semantic |

### 4. Meta Fields
- Title: Carbide Burr Shape M (Cone 90°): Vát góc vuông & mép lỗ lớn | AN MI TOOLS
- Meta Description: Shape M (cone 90°) để vát mép lỗ & cạnh góc vuông tiêu chuẩn. Khi nào nên chọn thay 60° hoặc taper và giới hạn ứng dụng bo mềm.
- Canonical: https://anmitools.com/carbide-burr/shape-m/
- Robots: index, follow
- Cornerstone/Pillar: FALSE

### 5. Open Graph & Twitter
- OG Title: Carbide Burr Shape M (Cone 90°)
- OG Description: Vát góc vuông & mép lỗ lớn
- OG Image: /wp-content/uploads/2025/10/carbide-burr-shape-m-og.webp
- Twitter Card: summary_large_image

### 6. URL & Breadcrumb
```
/carbide-burr/shape-m/
```
Breadcrumb: Trang chủ › Carbide Burr › Shape M

### 7. Content Outline
1. Shape M là gì? (cone 90°)
2. Ứng dụng vát mép lỗ & cạnh vuông
3. Bảng tình huống ứng dụng
4. Khi KHÔNG nên dùng (cần bo / góc trung bình)
5. So với Shape L / K
6. FAQ
7. CTA

### 8. Internal Linking Plan
| Anchor Text | Target URL | Reason | Placement |
|-------------|------------|--------|-----------|
| so sánh các loại | /carbide-burr/so-sanh/ | Overview | Intro |
| Shape L | /carbide-burr/shape-l/ | Góc 60° đối chứng | So sánh |
| Shape K | /carbide-burr/shape-k/ | Taper point sắc | So sánh |
| hướng dẫn chọn | /carbide-burr/huong-dan-chon/ | Funnel | CTA |

### 9. External Links
| Anchor | URL | Purpose | Nofollow? |
|--------|-----|---------|----------|
| 90 degree chamfer note | https://example-chamfer.org/ | Chuẩn góc | YES |

### 10. Media / Asset Plan
| # | Type | Purpose | File | Alt Text | Caption? |
|---|------|---------|------|---------|----------|
| 1 | Shape diagram | Hình dạng | shape-m-diagram.webp | Carbide Burr Shape M | Có |
| 2 | Chamfer hole | Vát mép lỗ | shape-m-chamfer-hole.webp | Vát mép lỗ 90° | Có |
| 3 | Edge chamfer | Vát cạnh | shape-m-edge-chamfer.webp | Vát cạnh 90° | Có |
| 4 | Comparison | So sánh | shape-m-vs-l.webp | Shape M vs L | Có |
| 5 | Limitation | Giới hạn | shape-m-limitation.webp | Giới hạn bo mềm | Có |
| 6 | CTA | Hỗ trợ | shape-m-cta.webp | Tư vấn chọn shape | Có |

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
| Khi nào dùng Shape M? | Khi cần vát chuẩn 90° | Decision | khi nào dùng burr shape m |
| Khác 60° thế nào? | 60° cho góc trung bình; 90° chuẩn vuông | Compare | carbide burr shape m |
| Có dùng để bo? | Không – dùng Shape J | Limit |  |
| Pattern đề xuất? | Single / Double tùy vật liệu | Clarify |  |

FAQ JSON: (viết sau)

### 12. Additional Schema
- Article + FAQPage

### 13. Data Boundaries
- Không độ sâu vát cụ thể
- Không % độ chính xác

### 14. CTA Architecture
| CTA Type | Copy | URL/Action | Placement |
|----------|------|-----------|-----------|
| Primary | Gửi thông tin góc + vật liệu → gợi ý shape | /contact-us/ | Sau Ứng dụng |
| Secondary | Xem hướng dẫn chọn Burr | /carbide-burr/huong-dan-chon/ | After FAQ |
| Soft Support | Chat kỹ thuật | Chat Widget | Footer |

### 15. Author & E-E-A-T
- Author: (Điền) Kỹ thuật viên dụng cụ
- Reviewed By: (Reviewer) Chamfer góc vuông
- Review Date: 2025-10-08
- E-E-A-T Note: Ứng dụng vát lỗ chuẩn.
- Bio: Hỗ trợ lựa chọn burr vát cạnh.

### 16. Human Tone
| Checklist | OK? | Note |
|-----------|-----|------|
| Cụ thể | ✔ | Ứng dụng rõ |
| Minh bạch | ✔ | Giới hạn bo |
| Không phóng đại | ✔ | Không % |
| Hỗ trợ | ✔ | CTA mềm |

### 17. Technical SEO Execution
| Hạng mục | Trạng thái | Ghi chú |
|----------|-----------|---------|
| H1 unique | ✔ | 1 H1 |
| Meta Title | ✔ | OK |
| Meta Description | ✔ | ~157 chars |
| Internal links | ✔ | 4 |
| FAQ block | Pending | Rank Math |
| Schema | ✔ | Article+FAQ |
| OG Image | Pending | 1200x630 |
| Canonical | ✔ | Set |
| Alt text | Pending | Khi upload |
| Redirects | N/A | None |

### Tags
carbide burr shape m, burr cone 90 độ m, ứng dụng burr shape m, khi nào dùng burr shape m, 90 degree cone burr, vát lỗ 90 độ, vát cạnh vuông, so sánh shape l k, dao đánh bavia, mũi mài hợp kim, an mi tools

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
| Impressions | 1,430 |
| Avg Position | < 26 |
| CTR | > 3.8% |
| Leads | 3 |

### 20. Changelog
- v1.0.0 (2025-10-08): Skeleton tạo theo template v1.2.0
