# RANK MATH SEO BACKEND TEMPLATE (Carbide Burr Cluster)

> Mục tiêu: Chuẩn hóa cấu trúc nhập liệu cho Rank Math (Title, Description, Focus Keywords, Schema, FAQ, Social, Internal Link Hub) và hỗ trợ team biên tập / kỹ thuật.
> Áp dụng cho 28 bài: 01–03 (Overview), 04–10 (Cut Type), 11–23 (Shape), 24–28 (Use Case).

---
## 1. Identification
- Mã bài: 00
- Nhóm: Overview | Cut Type | Shape | Use Case
- Primary Category (Rank Math): 
- H1 (Post Title): 
- Slug: 
- Legacy Slugs / Redirect 301 (cũ → mới): 
- Ngôn ngữ: vi-VN
- Pillar Content?: YES / NO (chỉ 01–03 = YES)
- Word Count Target: Overview 1200–1400 / Detail 800–1000 / Shape 650–850 / Use Case 750–950

## 2. Intent & Persona
- Search Intent: Informational | Comparative | How-to | Problem-solution | Transactional Assist
- Primary Persona: (Kỹ thuật viên / Chủ xưởng / Thu mua / Kỹ sư khuôn)
- Pain Points: 
- Desired Outcome: 

## 3. Focus Keywords (Rank Math)
| Type | Keyword | Use in | Priority | Note |
|------|---------|--------|----------|------|
| Primary |  | H1, Title, Intro, Alt | P1 |  |
| Secondary |  | H2, Body | P2 |  |
| Supporting |  | Body | P3 |  |
| Long-tail |  | FAQ / CTA | P3 |  |

Max 5 từ khóa chính/secondary cho Rank Math Free. Tránh nhồi lặp không tự nhiên.

## 4. Meta Fields (Rank Math Title & Description)
- Title (≤ 60 chars, từ khóa đầu): 
- Meta Description (150–160 chars, có CTA nhẹ): 
- Canonical: https://anmitools.com/carbide-burr/.../
- Robots: index, follow (hoặc noindex nếu trang hỗ trợ nội bộ)
- Cornerstone/Pillar: TRUE/FALSE

## 5. Open Graph & Twitter
- OG Title: (thường = Title)
- OG Description: (≤ 110 chars)
- OG Image: /wp-content/uploads/2025/..../<slug>-og.webp (1200x630)
- Twitter Card Type: summary_large_image

## 6. URL & Breadcrumb Strategy
```
/carbide-burr/<slug>/
```
Breadcrumb: Trang chủ › Carbide Burr › (Nhóm) › Tên bài

## 7. Content Outline (H2/H3 Hierarchy)
1. 
2. 
3. 
4. 
5. 

Checklist:
- [ ] Single H1
- [ ] Không nhảy H2→H4 bỏ qua H3
- [ ] 1 H2 mở định hướng (không keyword stuffing)

## 8. Internal Linking Plan
| Anchor Text | Target URL | Reason | Placement |
|-------------|------------|--------|-----------|
|  |  |  |  |

Hub Matrix:
- Overview ↔ Cut Type ↔ Shape ↔ Use Case
- Mỗi bài chi tiết ≥ 3 internal links (1 lên hub, 1 ngang, 1 xuống sâu hơn nếu có)

## 9. External Links (Authority / Optional)
| Anchor | URL | Purpose | Nofollow? |
|--------|-----|---------|----------|
|  |  |  |  |

Nguyên tắc: 0–2 external; không link đối thủ trực tiếp VN.

## 10. Media / Asset Plan
| # | Type | Purpose | File (đề xuất) | Alt Text | Caption? |
|---|------|---------|---------------|----------|----------|
| 1 | Hero | Nhận diện | <slug>-hero.webp |  |  |
| 2 | Diagram | Minh họa chính | <slug>-diagram.webp |  |  |
| 3 | Use Case | Thực tế | <slug>-usecase.webp |  |  |
| 4 | Comparison | So sánh | <slug>-compare.webp |  |  |
| 5 | CTA | Đội hỗ trợ | carbide-burr-support-team.webp |  |  |

Guidelines:
- WebP, width 1000px, lazy, alt mô tả trung thực.
- Không tạo alt bán hàng; ưu tiên chức năng/hành động.

## 11. FAQ Planning (Rank Math FAQ Block)
| Q | A (tóm tắt) | Intent | Keyword Target? |
|---|-------------|--------|-----------------|
|  |  |  |  |

FAQ JSON (mẫu – điền khi finalize):
```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "?",
      "acceptedAnswer": {"@type": "Answer","text": "...}
    }
  ]
}
```

## 12. Additional Schema (nếu áp dụng)
- Type: Article | Product | HowTo | FAQPage | BreadcrumbList
- Special Notes: (Ví dụ: Product schema defer do thiếu giá / offers)
- Product fields tạm bỏ nếu chưa có SKU/price.

## 13. Data Boundaries & Transparency
Thiếu (nếu có): RPM / D-L-d đầy đủ / Coating / Giá.  
Nguyên tắc: Luôn ghi rõ “Chưa có dữ liệu trong catalogue dạng text – liên hệ đội kỹ thuật”.

## 13b. Rủi Ro & Giảm Thiểu (Use Case Only)
| Rủi ro | Khi nào xảy ra | Hậu quả | Biện pháp giảm thiểu | Ghi chú |
|--------|----------------|---------|----------------------|---------|
| Quá nhiệt bề mặt | Tốc độ quá cao / giữ tại 1 điểm | Cháy xém / đổi màu | Di chuyển liên tục, giảm RPM | Không ước lượng % cải thiện |
| Mẻ cạnh / sứt mép | Ấn lực ngang lớn trên vật liệu giòn | Bavia lớn hơn / nứt | Nhiều pass nhẹ, chọn hình dạng phù hợp | Tránh hứa giảm 100% |
| Rung (chatter) | Cán dài / kẹp lỏng | Mặt nhám xấu, mòn sớm | Giảm nhô dao, kiểm tra collet | Ghi rõ nếu chưa có dữ liệu độ đảo |
| Bám phoi | Gia công vật liệu mềm / không làm sạch | Nhiệt, kẹt dao | Thổi khí nhẹ, dừng làm sạch | Không nêu hóa chất nếu chưa kiểm chứng |
| Bụi mịn nguy hại | Mài composite / hợp kim đặc biệt | Ảnh hưởng hô hấp | Khẩu trang đạt chuẩn, hút bụi cục bộ | Không khuyến nghị PPE cụ thể nếu thiếu chuẩn |


## 14. CTA Architecture
| CTA Type | Copy | URL/Action | Placement |
|----------|------|-----------|-----------|
| Primary |  | /contact-us/ | Giữa hoặc cuối |
| Secondary |  | /carbide-burr/huong-dan-chon/ | Sau checklist |
| Soft Support |  | Form/Chat | Sidebar/After FAQ |

Tone: Trợ giúp, không “Mua ngay”, tránh capslock.

## 15. Author & E-E-A-T Block
- Author Name: 
- Author Role: (Kỹ thuật viên / Biên tập / Chuyên gia dụng cụ)
- Years Experience: 
- Reviewed By (nếu có): 
- Review Date (yyyy-mm-dd): 
- E-E-A-T Note (1 câu giá trị thực tế): 
- Bio Snippet (≤ 160 chars): 

## 16. Human Tone (A1 Compliance)
| Checklist | OK? | Note |
|-----------|-----|------|
| Mở bài có đồng cảm |  |  |
| Có trao quyền (hành động cụ thể) |  |  |
| Cảnh báo dữ liệu thiếu minh bạch |  |  |
| CTA không gây áp lực |  |  |
| Không phóng đại tuyệt đối |  |  |
| Tránh lặp từ khóa vô lý |  |  |

## 17. Technical SEO Execution
| Hạng mục | Trạng thái | Ghi chú |
|----------|-----------|---------|
| H1 unique |  |  |
| Meta Title length ok |  |  |
| Meta Description length ok |  |  |
| 3–5 Internal links |  |  |
| FAQ block valid |  |  |
| Schema loại chính |  |  |
| OG Image đúng size |  |  |
| Canonical set |  |  |
| No duplicate intent |  |  |
| Alt text đầy đủ |  |  |
| Redirects active (nếu có) |  |  |

## 18. Performance & Core Web Vitals (Ghi sau khi publish)
| Metric | Target | Current | Note |
|--------|--------|---------|------|
| LCP | < 2.5s |  |  |
| CLS | < 0.1 |  |  |
| INP | < 200ms |  |  |
| Total Size | < 1.0MB |  |  |

## 19. Success Metrics (90 days)
| Metric | Target |
|--------|--------|
| Clicks |  |
| Impressions |  |
| Avg Position |  |
| CTR |  |
| Leads |  |

## 20. Revision / Changelog
- v1.0.0 (yyyy-mm-dd): Khởi tạo spec theo template Rank Math
- v1.1.0 (yyyy-mm-dd): Cập nhật keyword cluster / schema / CTA
- v1.2.0 (yyyy-mm-dd): Thêm Primary Category / Redirects / E-E-A-T
