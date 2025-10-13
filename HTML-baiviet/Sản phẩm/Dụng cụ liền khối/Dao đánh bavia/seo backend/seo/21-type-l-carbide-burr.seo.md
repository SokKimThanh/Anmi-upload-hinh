## SEO BACKEND SPEC – 21 (Rank Math v1.2.0)

### 1. Identification
- Mã bài: 21
- Nhóm: Shape Detail
- Primary Category: Carbide Burr
- Phân loại bài viết: Shape Detail
- Mô tả ngắn: Dao mài hợp kim dạng nón mũi cầu chuyên gia công các bề mặt và hình dạng hẹp với độ chính xác cao.
- H1: Carbide Burr Shape L (Cone 60°): Vát chuẩn góc trung bình
- Slug: type-l-carbide-burr
- Legacy Redirects: (trống)
- Pillar Content?: NO (Supporting)
- Word Count Target: 700–850

### 2. Intent & Persona
- Intent: Informational (vát góc / countersink nhẹ)
- Persona: Người cần vát chuẩn 60° cho mép lỗ / cạnh nghiêng
- Pain: Shape J/K khó giữ góc đều; dạng cầu gây méo biên
- Outcome: Biết khi nào dùng Cone 60° & giới hạn nếu cần góc khác

### 3. Focus Keywords
| Type | Keyword | Use in | Priority | Note |
|------|---------|--------|----------|------|
| Primary | carbide burr shape l | H1, Title | P1 | Core |
| Secondary | burr cone 60 do l | Intro | P2 | VN variant |
| Secondary | ứng dụng burr shape l | Ứng dụng | P2 | Benefit |
| Long-tail | khi nào dùng burr shape l | FAQ / Khi nào dùng | P3 | Decision |
| Semantic | 60 degree cone burr | 1 lần | P3 | Semantic |

### 4. Meta Fields
- Title: Carbide Burr Shape L (Cone 60°): Vát chuẩn góc trung bình | AN MI TOOLS
- Meta Description: Shape L (cone 60°) dùng vát mép lỗ & cạnh nghiêng góc trung bình. Khi nào nên dùng thay taper point hoặc 90° và giới hạn linh hoạt.
- Canonical: https://anmitools.com/carbide-burr/shape-l/
- Robots: index, follow
- Cornerstone/Pillar: FALSE

### 5. Open Graph & Twitter
- OG Title: Carbide Burr Shape L (Cone 60°)
- OG Description: Vát chuẩn góc trung bình
- OG Image: /wp-content/uploads/2025/10/carbide-burr-shape-l-og.webp
- Twitter Card: summary_large_image

### 6. URL & Breadcrumb
```
/carbide-burr/shape-l/
```
Breadcrumb: Trang chủ › Carbide Burr › Shape L

### 7. Content Outline
1. Shape L là gì? (cone 60°)
2. Ứng dụng vát mép lỗ / cạnh nghiêng
3. Bảng tình huống ứng dụng
4. Khi KHÔNG nên dùng (cần bo mềm hoặc góc khác)
5. So với Shape K (nhọn) & M (cone 90°)
6. FAQ
7. CTA

### 8. Internal Linking Plan
| Anchor Text | Target URL | Reason | Placement |
|-------------|------------|--------|-----------|
| so sánh các loại | /carbide-burr/so-sanh/ | Overview | Intro |
| Shape K | /carbide-burr/shape-k/ | Liên kết taper point | So sánh |
| Shape M | /carbide-burr/shape-m/ | Cone 90° đối chứng | So sánh |
| hướng dẫn chọn | /carbide-burr/huong-dan-chon/ | Funnel | CTA |

### 9. External Links
| Anchor | URL | Purpose | Nofollow? |
|--------|-----|---------|----------|
| 60 degree chamfer note | https://example-chamfer.org/ | Chuẩn góc | YES |

### 10. Media / Asset Plan
| # | Type | Purpose | File | Alt Text | Caption? |
|---|------|---------|------|---------|----------|
| 1 | Shape diagram | Hình dạng | shape-l-diagram.webp | Carbide Burr Shape L | Có |
| 2 | Chamfer hole | Vát lỗ | shape-l-chamfer-hole.webp | Vát mép lỗ 60° | Có |
| 3 | Edge chamfer | Vát cạnh | shape-l-edge-chamfer.webp | Vát cạnh bằng Shape L | Có |
| 4 | Comparison | So sánh | shape-l-vs-km.webp | Shape L vs K vs M | Có |
| 5 | Limitation | Giới hạn | shape-l-limitation.webp | Giới hạn góc cố định | Có |
| 6 | CTA | Hỗ trợ | shape-l-cta.webp | Tư vấn chọn shape | Có |

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
| Khi nào dùng Shape L? | Khi cần vát 60° đồng đều | Decision | khi nào dùng burr shape l |
| Khác taper point? | Taper point linh hoạt, L chuẩn góc | Compare | carbide burr shape l |
| Dùng để bo mép mềm? | Không – dùng Shape J | Limit |  |
| Nếu cần góc 90°? | Dùng Shape M | Clarify |  |

FAQ JSON: (viết sau)

### 12. Additional Schema
- Article + FAQPage

### 13. Content Quality Standards
- Tập trung vào đặc tính kỹ thuật và ứng dụng thực tế của type L carbide burr
- Hướng dẫn liên hệ kỹ thuật cho tư vấn thông số cụ thể theo ứng dụng

### 14. CTA Architecture
| CTA Type | Copy | URL/Action | Placement |
|----------|------|-----------|-----------|
| Primary | Gửi góc yêu cầu + vật liệu → gợi ý pattern | /contact-us/ | Sau Ứng dụng |
| Secondary | Xem hướng dẫn chọn Burr | /carbide-burr/huong-dan-chon/ | After FAQ |
| Soft Support | Chat kỹ thuật | Chat Widget | Footer |

### 15. Author & E-E-A-T
- Author: (Điền) Kỹ thuật viên dụng cụ
- Reviewed By: (Reviewer) Chamfer góc trung bình
- Review Date: 2025-10-08
- E-E-A-T Note: Ứng dụng vát chuẩn trong xưởng.
- Bio: Hỗ trợ lựa chọn burr vát mép.

### 16. Human Tone
| Checklist | OK? | Note |
|-----------|-----|------|
| Cụ thể | ✔ | Ứng dụng rõ |
| Minh bạch | ✔ | Giới hạn góc cố định |
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
carbide burr shape l, burr cone 60 độ l, ứng dụng burr shape l, khi nào dùng burr shape l, 60 degree cone burr, vát lỗ 60 độ, countersink nhẹ, so sánh shape k m, dao đánh bavia, mũi mài hợp kim, an mi tools

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
