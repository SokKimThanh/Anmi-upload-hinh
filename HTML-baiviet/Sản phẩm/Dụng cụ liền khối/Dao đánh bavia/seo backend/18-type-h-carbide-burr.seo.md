## SEO BACKEND SPEC – 18 (Rank Math v1.2.0)

### 1. Identification
- Mã bài: 18
- Nhóm: Shape Detail
- Primary Category: Carbide Burr
- H1: Carbide Burr Shape H (Flame): Chuyển tiếp cong thuôn dài
- Slug: type-h-carbide-burr
- Legacy Redirects: (trống)
- Pillar Content?: NO (Supporting)
- Word Count Target: 750–900

### 2. Intent & Persona
- Intent: Informational hình học ứng dụng
- Persona: Người cần hoàn thiện chuyển tiếp cong dài / bo kéo dài
- Pain: Shape D (cầu) quá tròn, tạo điểm; Shape F/G quá nhọn hoặc bó hẹp
- Outcome: Hiểu khi nào dùng Flame shape và giới hạn phẳng

### 3. Focus Keywords
| Type | Keyword | Use in | Priority | Note |
|------|---------|--------|----------|------|
| Primary | carbide burr shape h | H1, Title | P1 | Core |
| Secondary | burr ngọn lửa h | Intro | P2 | VN variant |
| Secondary | ứng dụng burr shape h | Ứng dụng | P2 | Benefit |
| Long-tail | khi nào dùng burr shape h | FAQ / Khi nào dùng | P3 | Decision |
| Semantic | flame carbide burr | 1 lần | P3 | Semantic |

### 4. Meta Fields
- Title: Carbide Burr Shape H (Flame): Chuyển tiếp cong thuôn dài | AN MI TOOLS
- Meta Description: Shape H (Flame) tối ưu cho chuyển tiếp cong kéo dài & bo mềm sâu vừa. Khi nào dùng thay cầu / tree & giới hạn diện phẳng.
- Canonical: https://anmitools.com/carbide-burr/shape-h/
- Robots: index, follow
- Cornerstone/Pillar: FALSE

### 5. Open Graph & Twitter
- OG Title: Carbide Burr Shape H (Flame)
- OG Description: Chuyển tiếp cong thuôn dài?
- OG Image: /wp-content/uploads/2025/10/carbide-burr-shape-h-og.webp
- Twitter Card: summary_large_image

### 6. URL & Breadcrumb
```
/carbide-burr/shape-h/
```
Breadcrumb: Trang chủ › Carbide Burr › Shape H

### 7. Content Outline
1. Shape H là gì? (flame thuôn)
2. Hình học thuôn và tiếp xúc tuyến
3. Ứng dụng chuyển tiếp cong dài (bảng)
4. Khi KHÔNG nên dùng (phẳng / khe rất hẹp)
5. So với Shape F / G / D
6. FAQ
7. CTA

### 8. Internal Linking Plan
| Anchor Text | Target URL | Reason | Placement |
|-------------|------------|--------|-----------|
| so sánh các loại | /carbide-burr/so-sanh/ | Overview | Intro |
| Shape F | /carbide-burr/shape-f/ | Liên kết tree radius | So sánh |
| Shape G | /carbide-burr/shape-g/ | Liên kết tree point | So sánh |
| hướng dẫn chọn | /carbide-burr/huong-dan-chon/ | Funnel | CTA |

### 9. External Links
| Anchor | URL | Purpose | Nofollow? |
|--------|-----|---------|----------|
| flame burr contour note | https://example-contour.org/ | Contour hoàn thiện | YES |

### 10. Media / Asset Plan
| # | Type | Purpose | File | Alt Text | Caption? |
|---|------|---------|------|---------|----------|
| 1 | Shape diagram | Hình dạng | shape-h-diagram.webp | Carbide Burr Shape H | Có |
| 2 | Long contour | Chuyển tiếp dài | shape-h-long-contour.webp | Hoàn thiện đường cong dài | Có |
| 3 | Edge blending | Bo kéo dài | shape-h-edge-long-blend.webp | Bo mép kéo dài Shape H | Có |
| 4 | Comparison | So sánh | shape-h-vs-fg.webp | Shape H vs F/G | Có |
| 5 | Limitation | Giới hạn | shape-h-limitation.webp | Giới hạn diện phẳng | Có |
| 6 | CTA | Hỗ trợ | shape-h-cta.webp | Tư vấn chọn shape | Có |

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
| Khi nào dùng Shape H? | Khi cần hoàn thiện chuyển tiếp cong dài | Decision | khi nào dùng burr shape h |
| Khác Shape F/G? | Flame tiếp xúc mềm hơn & không quá nhọn | Compare | carbide burr shape h |
| Dùng diện phẳng? | Không tối ưu – nên dùng A/B | Limit |  |
| Pattern đề xuất? | Double / Fine cho hoàn thiện | Clarify |  |

FAQ JSON: (viết sau)

### 12. Additional Schema
- Article + FAQPage

### 13. Data Boundaries
- Không % cải thiện bề mặt
- Không kích thước chiều dài tiếp xúc

### 14. CTA Architecture
| CTA Type | Copy | URL/Action | Placement |
|----------|------|-----------|-----------|
| Primary | Gửi hình dạng chi tiết + vật liệu → gợi ý shape | /contact-us/ | Sau Ứng dụng |
| Secondary | Xem hướng dẫn chọn Burr | /carbide-burr/huong-dan-chon/ | After FAQ |
| Soft Support | Chat kỹ thuật | Chat Widget | Footer |

### 15. Author & E-E-A-T
- Author: (Điền) Kỹ thuật viên dụng cụ
- Reviewed By: (Reviewer) Hoàn thiện contour
- Review Date: 2025-10-08
- E-E-A-T Note: Dựa ứng dụng hoàn thiện đường cong dài.
- Bio: Hỗ trợ chọn burr cho contour & chuyển tiếp.

### 16. Human Tone
| Checklist | OK? | Note |
|-----------|-----|------|
| Cụ thể | ✔ | Ứng dụng rõ |
| Minh bạch | ✔ | Giới hạn phẳng |
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
carbide burr shape h, burr ngọn lửa h, ứng dụng burr shape h, khi nào dùng burr shape h, flame carbide burr, chuyển tiếp cong dài, bo kéo dài, so sánh shape f g d, dao đánh bavia, mũi mài hợp kim, an mi tools

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
| Clicks | 74 |
| Impressions | 1,480 |
| Avg Position | < 26 |
| CTR | > 3.9% |
| Leads | 3 |

### 20. Changelog
- v1.0.0 (2025-10-08): Skeleton tạo theo template v1.2.0
