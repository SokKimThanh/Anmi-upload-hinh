# QUY TẮC THIẾT KẾ VISUAL
## Hierarchy, Consistency, Negative Space, Contrast Ratio

Version: 1.0.0  
Last Updated: November 11, 2025  
Author: An Mi Tools Technical Team

---

## I. HIERARCHY (Phân cấp trực quan)

### 1. TYPOGRAPHY SCALE (Thang chữ)

```css
H1: clamp(1.84rem, 4.2vw, 2.625rem)     /* Desktop: 2.625rem (42px), Mobile: 1.84rem (29.44px) */
    Font-weight: 700 (bold)
    Line-height: 1.2
    Margin-bottom: 1.5rem
    Color: --anmi-color-text (#000000)
    
H2: clamp(1.575rem, 3.15vw, 1.97rem)    /* Desktop: 1.97rem (31.52px), Mobile: 1.575rem (25.2px) */
    Font-weight: 600 (semi-bold)
    Line-height: 1.3
    Margin-bottom: 1rem
    Border-bottom: 3px solid --anmi-color-primary
    Padding-bottom: 0.5rem
    Display: inline-block (border chỉ dưới text)
    
H3: clamp(1.18rem, 2.1vw, 1.31rem)      /* Desktop: 1.31rem (20.96px), Mobile: 1.18rem (18.88px) */
    Font-weight: 600 (semi-bold)
    Line-height: 1.4
    Margin-bottom: 0.75rem
    
Body (P): clamp(1.05rem, 1.575vw, 1.18rem) /* Desktop: 1.18rem (18.88px), Mobile: 1.05rem (16.8px) */
    Line-height: 1.7 (dễ đọc trên mobile)
    Margin-bottom: 1rem
    Color: --anmi-color-text
```

**Nguyên tắc:**
- ✅ Tỷ lệ scale: H1 = 2.22x Body, H2 = 1.67x Body, H3 = 1.11x Body (desktop)
- ✅ Line-height tăng dần cho body text (1.7) để dễ đọc
- ✅ H2 có visual emphasis: border-bottom 3px (tách section rõ ràng)
- ✅ Clamp() responsive: tự điều chỉnh theo viewport (không break trên màn hình nhỏ)
- ✅ Contrast tối đa: #000000 trên #FCF7EC = 18.96:1 (WCAG AAA)

### 2. CONTENT STRUCTURE (Cấu trúc nội dung)

```html
<section class="[product-slug]">
├── <figure class="product-image"> (ảnh đại diện)
├── <h1> (tiêu đề chính - 1 lần duy nhất)
└── <div class="section [section-name]"> (mỗi phần content)
    ├── <h2> (tiêu đề section)
    ├── <p> hoặc <ul>/<ol> (nội dung chính)
    ├── <h3> (sub-section - nếu cần)
    ├── <figure> hoặc <table> (visual support)
    └── <div class="[component]"> (buttons, cards, grids)
```

**Nguyên tắc phân cấp:**
- ✅ **H1**: Chỉ 1 lần, dùng cho title chính (product name)
- ✅ **H2**: Mở đầu mỗi section (product-intro, why-choose, specifications, applications, FAQ, contact)
- ✅ **H3**: Dùng trong section có nhiều sub-topics (6 tính năng, 4 ứng dụng)
- ❌ **Không dùng H4-H6**: Giữ hierarchy đơn giản, dễ scan

### 3. VISUAL WEIGHT (Trọng lượng trực quan)

**Thứ tự ưu tiên (top → bottom):**
1. **H1** (lớn nhất, bold nhất) → First impression
2. **Product Image** (hero image với figcaption)
3. **H2 với border-bottom** → Section dividers  
4. **CTA Buttons** (màu primary, có icon, hover effects)
5. **H3** → Sub-sections
6. **Body text, lists, tables** → Content chi tiết
7. **Figcaption, notes** (text nhỏ hơn, màu secondary)

---

## II. CONSISTENCY (Đồng bộ)

### 1. SPACING SYSTEM (Hệ thống khoảng cách)

```css
/* Vertical Rhythm - Nhịp dọc đồng bộ */
--anmi-section-margin: 1.5rem          /* Giữa các section */
--anmi-heading-margin-bottom:
  - H1: 1.5rem
  - H2: 1rem
  - H3: 0.75rem
--anmi-paragraph-margin-bottom: 1rem   /* Giữa các đoạn văn */
--anmi-figure-margin: 1.5rem auto      /* Giữa các hình ảnh */

/* Component Padding */
--anmi-section-padding-y: 1rem         /* Desktop vertical padding */
--anmi-section-padding-x: 0.5rem      /* Desktop horizontal padding */
--mobile-section-padding: 0.5rem 0.15rem /* Mobile: giảm để tận dụng không gian */

/* Component Gaps */
--anmi-grid-gap: 1rem                  /* Grid spacing (feature-grid, application-grid) */
--anmi-card-padding: 1.5rem           /* Card/box internal spacing */
```

**Nguyên tắc:**
- ✅ **Base unit: 0.5rem (8px)** → Mọi spacing là bội số của 8px (8, 16, 24, 32...)
- ✅ **Consistent spacing**: Giữa sections luôn 1.5rem, giữa paragraphs luôn 1rem
- ✅ **Margin-top > margin-bottom cho headings**: H2 margin-top tự nhiên từ section class
- ✅ **Mobile compression**: Giảm spacing 25-33% trên mobile

### 2. COLOR PALETTE (Bảng màu đồng bộ)

```css
/* Brand Colors */
--anmi-color-primary: #0055AA         /* Primary blue - CTA buttons, links, H2 borders */
--anmi-color-primary-hover: #003d7a   /* Hover state */
--anmi-color-primary-strong: #004080  /* Strong emphasis */
--anmi-color-primary-tint-soft: #E6F2FF /* Soft background (cards, tables) */
--anmi-color-primary-tint-strong: #B3D9FF /* Strong tint (borders) */

/* Neutral Colors */
--anmi-color-text: #000000            /* Body text - maximum contrast */
--anmi-color-text-secondary: #333333  /* Secondary text (figcaption, notes) */
--anmi-color-text-muted: #666666      /* Muted text (labels, meta) */
--anmi-color-surface: #FFFFFF         /* Card backgrounds */
--anmi-color-surface-warm: #FCF7EC    /* Warm background (notes, highlights) */

/* Semantic Colors */
--anmi-color-success: #28a745         /* Success state (checkmarks) */
--anmi-color-warning: #ffc107         /* Warning state */
--anmi-color-error: #dc3545           /* Error state */

/* Border & Shadow */
--anmi-border-color-soft: #E0E0E0     /* Soft borders (tables, cards) */
--anmi-box-shadow-soft: 0 2px 8px rgba(0, 0, 0, 0.06) /* Light shadow */
--anmi-box-shadow-medium: 0 4px 12px rgba(0, 0, 0, 0.1) /* Medium shadow */
```

**Contrast Ratios (WCAG AAA):**
- ✅ Body text (#000) on warm bg (#FCF7EC): **18.96:1** (AAA Large)
- ✅ Links (#0055AA) on white (#FFF): **8.59:1** (AA Normal)
- ✅ CTA buttons (white) on primary (#0055AA): **8.59:1** (AA Normal)
- ✅ Secondary text (#333) on white: **12.63:1** (AAA Normal)
- ✅ Muted text (#666) on white: **5.74:1** (AA Normal)

**Nguyên tắc:**
- ✅ **High contrast ratio**: Text #000000 trên background #FCF7EC = 18.96:1
- ✅ **Primary color usage**: Links, CTA buttons, H2 borders, active states
- ✅ **Neutral grays**: 3 levels (#000, #333, #666) cho hierarchy
- ✅ **Soft tints**: Background không white thuần (warm #FCF7EC) giảm mỏi mắt

### 3. COMPONENT PATTERNS (Mẫu component nhất quán)

**CTA Buttons:**
```html
<div class="cta-buttons">
  <a href="[URL]" class="btn btn-primary cta-button">[Icon] [Text]</a>
</div>
```
- Background: `--anmi-color-primary`
- Padding: `1rem 2rem`
- Border-radius: `8px`
- Hover: `transform translateY(-2px) + shadow`
- Icon: SVG inline, 20x20px

**Feature Cards:**
```html
<div class="feature-grid">
  <div class="feature-card">
    <h3>[Title]</h3>
    <p>[Description]</p>
  </div>
</div>
```
- Grid: `repeat(auto-fit, minmax(280px, 1fr))`
- Padding: `1.5rem`
- Border-radius: `12px`
- Shadow: `--anmi-box-shadow-soft`
- Background: `--anmi-color-surface`

**Tables:**
```html
<div class="table-responsive">
  <table>
    <thead><tr><th>...</th></tr></thead>
    <tbody><tr><td>...</td></tr></tbody>
  </table>
</div>
```
- Border-collapse: `collapse`
- Thead background: `--anmi-color-primary-tint-soft`
- Tbody rows: alternate với hover state
- Font-size: `1rem` (desktop), `0.92rem` (mobile)

---

## III. NEGATIVE SPACE (Không gian âm)

### 1. BREATHING ROOM (Không gian thở)

```css
/* Section spacing (không gian giữa các section) */
.section {
  margin-bottom: 1.5rem;  /* Desktop */
}
@media (max-width: 768px) {
  .section {
    margin-bottom: 1rem;  /* Mobile: giảm 33% */
  }
}

/* Figure spacing (hình ảnh cần không gian) */
figure {
  margin: 1.5rem auto;  /* Desktop: top/bottom 1.5rem */
}
@media (max-width: 768px) {
  figure {
    margin: 1rem 0;      /* Mobile: giảm, sát mép */
  }
}

/* Paragraph spacing */
p {
  margin-bottom: 1rem;
  max-width: 70ch;        /* Optimal reading length: 70 characters */
}
```

**Nguyên tắc:**
- ✅ **70ch rule**: Paragraph width tối đa 70 ký tự (optimal readability)
- ✅ **Margin-top tự nhiên**: Headings không cần margin-top vì section đã có margin-bottom
- ✅ **Mobile compression**: Giảm spacing 25-33% trên mobile để tận dụng màn hình nhỏ
- ✅ **Consistent rhythm**: Section spacing đồng nhất (1.5rem desktop, 1rem mobile)

### 2. DENSITY CONTROL (Kiểm soát mật độ)

```css
/* Low density (sparse) - Hero sections */
.product-intro {
  padding: 2rem 1rem;
  margin-bottom: 2rem;
}

/* Medium density - Content sections */
.section {
  padding: 1rem 0.5rem;
  margin-bottom: 1.5rem;
}

/* High density - Data tables */
table {
  font-size: 1rem;
  line-height: 1.5;
}
table td {
  padding: 0.75rem;
}
```

**Nguyên tắc:**
- ✅ **Hero sections**: Sparse (nhiều whitespace) → Focus attention
- ✅ **Content sections**: Medium density → Balance readability
- ✅ **Data tables**: High density → Fit information
- ✅ **Progressive disclosure**: Từ sparse → dense theo content type

### 3. GRID GAPS (Khoảng cách grid)

```css
/* Feature grid */
.feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1rem;  /* Consistent gap */
}

/* Application grid */
.application-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}
@media (max-width: 768px) {
  .application-grid {
    grid-template-columns: 1fr;  /* Stack on mobile */
  }
}

/* Contact info grid */
.contact-info {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1rem;
}
```

**Nguyên tắc:**
- ✅ **Uniform gaps**: Tất cả grids dùng `1rem` gap (consistency)
- ✅ **Auto-fit with minmax**: Responsive tự động không cần media queries
- ✅ **Mobile stacking**: 1 column trên mobile (<768px)
- ✅ **Minimum card width**: 240-280px (không quá nhỏ trên mobile)

---

## IV. CONTRAST RATIO (Tỷ lệ tương phản)

### 1. TEXT CONTRAST (WCAG AAA Standard)

| Element | Color | Background | Ratio | WCAG |
|---------|-------|------------|-------|------|
| Body text | #000000 | #FCF7EC | 18.96:1 | AAA ✅ |
| Headings | #000000 | #FFFFFF | 21:1 | AAA ✅ |
| Links | #0055AA | #FFFFFF | 8.59:1 | AA ✅ |
| Secondary text | #333333 | #FFFFFF | 12.63:1 | AAA ✅ |
| Muted text | #666666 | #FFFFFF | 5.74:1 | AA ✅ |
| CTA button text | #FFFFFF | #0055AA | 8.59:1 | AA ✅ |

**Test tools:**
- WebAIM Contrast Checker: https://webaim.org/resources/contrastchecker/
- Chrome DevTools: Lighthouse Accessibility

### 2. VISUAL CONTRAST (Phân biệt elements)

**Size contrast:**
- H1 (2.625rem) vs Body (1.18rem) = **2.22x difference**
- H2 (1.97rem) vs H3 (1.31rem) = **1.5x difference**
- Clear hierarchy: không confuse giữa levels

**Weight contrast:**
- H1: **700** (bold) → Strong emphasis
- H2/H3: **600** (semi-bold) → Medium emphasis
- Body: **400** (regular) → Easy reading
- Labels: **600** uppercase → Distinguished

**Color contrast:**
- Primary blue (#0055AA) vs black text (#000) → Clear CTAs
- Border-bottom H2 (3px blue) → Visual separator
- Card backgrounds (soft tint) vs white → Depth

### 3. INTERACTIVE STATES (Hover/Focus/Active)

```css
/* Link hover */
a {
  color: #0055AA;
  text-decoration: none;
  transition: color 0.2s ease;
}
a:hover {
  text-decoration: underline;
  color: #003d7a;  /* Darker on hover */
}

/* Button hover */
.btn-primary {
  background: #0055AA;
  color: #FFFFFF;
  transform: translateY(0);
  transition: all 0.3s ease;
}
.btn-primary:hover {
  background: #003d7a;
  transform: translateY(-2px);  /* Lift effect */
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Focus state (accessibility) */
*:focus {
  outline: 2px solid #0055AA;
  outline-offset: 2px;
}
```

**Nguyên tắc:**
- ✅ **Subtle hover**: Không quá dramatic (underline + color change)
- ✅ **Lift effect**: Buttons transform translateY(-2px) on hover
- ✅ **Focus visible**: 2px outline cho keyboard navigation (WCAG 2.4.7)
- ✅ **Smooth transitions**: 0.2-0.3s ease

---

## V. READABILITY OPTIMIZATION (Tối ưu khả năng đọc)

### 1. LINE LENGTH & LINE HEIGHT

```css
/* Optimal reading measure */
p, ul, ol {
  max-width: 70ch;  /* 45-75ch optimal, 70ch ideal */
  line-height: 1.7; /* 1.5-1.8 optimal for body text */
}

/* Headings tighter line-height */
h1 { line-height: 1.2; }  /* Large text can be tighter */
h2 { line-height: 1.3; }
h3 { line-height: 1.4; }
```

**Nguyên tắc:**
- ✅ **70 characters/line**: Optimal reading speed (không quá rộng)
- ✅ **1.7 line-height**: Comfortable cho body text 16-18px
- ✅ **Headings tighter**: Large text (42px) không cần line-height cao
- ✅ **Mobile adjustment**: Line-height giữ nguyên (không giảm)

### 2. PARAGRAPH STRUCTURE

```html
<!-- ✅ Good: Short paragraphs (2-3 sentences max) -->
<p><strong>Product name</strong> là description. Key benefit.</p>
<p>Detail sentence 1. Detail sentence 2.</p>

<!-- ❌ Bad: Long paragraphs (wall of text) -->
<p>Long sentence 1. Long sentence 2. Long sentence 3. Long sentence 4. Long sentence 5. Long sentence 6...</p>
```

**Nguyên tắc:**
- ✅ **2-3 sentences/paragraph**: Easy scanning
- ✅ **Break up walls of text**: Add whitespace
- ✅ **Bold important terms**: `<strong>` for keywords
- ✅ **One idea per paragraph**: Focus clarity

### 3. LIST FORMATTING

```html
<!-- Ordered list (steps, rankings) -->
<ol>
  <li><strong>Title:</strong> Description với details cụ thể.</li>
</ol>

<!-- Unordered list (features, specs) -->
<ul>
  <li><strong>Spec name:</strong> Value và unit</li>
</ul>
```

**Nguyên tắc:**
- ✅ **Bold list item titles**: Easy scanning
- ✅ **Colon separator**: Clear structure
- ✅ **Consistent format**: Title: Description pattern
- ✅ **Whitespace between items**: Margin-bottom list items

---

## VI. RESPONSIVE DESIGN RULES

### 1. BREAKPOINTS

```css
/* Desktop (default) - ≥1025px */
Default styles apply

/* Tablet (≤1024px) */
@media (max-width: 1024px) {
  /* Font sizes giảm 5-10% */
  /* Grid columns: 3 → 2 */
}

/* Mobile (≤768px) */
@media (max-width: 768px) {
  /* Stack grids to 1 column */
  /* Reduce spacing 25-33% */
  /* Increase touch targets to 44x44px */
  /* Show mobile images, hide desktop images */
}

/* Small mobile (≤480px) */
@media (max-width: 480px) {
  /* Force 1 column everything */
  /* Minimum font-size: 16px (prevent zoom) */
}
```

### 2. MOBILE OPTIMIZATIONS

```css
/* Reduce padding/margin on mobile */
@media (max-width: 768px) {
  section {
    padding: 0.5rem 0.15rem;  /* Tràn viền hơn */
  }
  figure {
    margin: 1rem 0;  /* Sát mép */
  }
  .section {
    margin-bottom: 1rem;  /* Giảm spacing 33% */
  }
}

/* Stack grids */
@media (max-width: 768px) {
  .feature-grid,
  .application-grid,
  .contact-info {
    grid-template-columns: 1fr;  /* 1 column */
  }
}

/* Hide/show images */
@media (max-width: 768px) {
  .contact-image-desktop {
    display: none;  /* Ẩn ảnh desktop */
  }
  .contact-image-mobile {
    display: block;  /* Hiện ảnh hotline số to */
  }
}

/* Touch targets */
@media (max-width: 768px) {
  .btn, .cta-button {
    min-height: 44px;  /* iOS/Android touch target */
    padding: 0.75rem 1.5rem;
  }
}
```

**Nguyên tắc:**
- ✅ **Mobile-first mindset**: Design for mobile, enhance for desktop
- ✅ **Touch targets**: Minimum 44x44px (Apple HIG, Material Design)
- ✅ **Reduce spacing**: 25-33% less whitespace on mobile
- ✅ **Stack grids**: 1 column on mobile (<768px)
- ✅ **Font size minimum**: 16px body text (prevent auto-zoom iOS)

---

## VII. CHECKLIST ÁP DỤNG

### ✅ Trước khi publish:

**Hierarchy:**
- [ ] H1 chỉ 1 lần (product name)
- [ ] H2 mở đầu mỗi section
- [ ] H3 cho sub-sections (không dùng H4-H6)
- [ ] Typography scale đúng (H1: 2.625rem, H2: 1.97rem, H3: 1.31rem, Body: 1.18rem)

**Spacing:**
- [ ] Section margin-bottom: 1.5rem (desktop), 1rem (mobile)
- [ ] Paragraph margin-bottom: 1rem
- [ ] Figure margin: 1.5rem auto (desktop), 1rem 0 (mobile)
- [ ] Grid gap: 1rem uniform

**Colors:**
- [ ] Body text: #000000 on #FCF7EC (contrast 18.96:1 ✅)
- [ ] Links: #0055AA (contrast 8.59:1 ✅)
- [ ] CTA buttons: white on #0055AA
- [ ] H2 border-bottom: 3px solid #0055AA

**Negative Space:**
- [ ] Paragraph max-width: 70ch
- [ ] Line-height: 1.7 for body, 1.2-1.4 for headings
- [ ] Mobile spacing reduced 25-33%

**Interactive States:**
- [ ] Link hover: underline + darker color
- [ ] Button hover: lift effect (-2px) + shadow
- [ ] Focus visible: 2px outline #0055AA

**Responsive:**
- [ ] Mobile: stack grids to 1 column
- [ ] Mobile: hide desktop images, show mobile images
- [ ] Mobile: touch targets ≥44px
- [ ] Font-size mobile: minimum 16px body

**Accessibility:**
- [ ] Alt text cho tất cả images
- [ ] Focus visible cho keyboard navigation
- [ ] Contrast ratio ≥4.5:1 (AA) hoặc ≥7:1 (AAA)
- [ ] Schema markup (Product, Breadcrumb, FAQ)

---

## VIII. TOOLS ĐỂ TEST

### Contrast & Color:
- **WebAIM Contrast Checker**: https://webaim.org/resources/contrastchecker/
- **Coolors Contrast Checker**: https://coolors.co/contrast-checker

### Readability:
- **Hemingway Editor**: http://www.hemingwayapp.com/
- **Readable**: https://readable.com/

### Responsive:
- **Chrome DevTools**: Responsive mode (Ctrl+Shift+M)
- **Responsively App**: https://responsively.app/

### Accessibility:
- **WAVE**: https://wave.webaim.org/
- **axe DevTools**: Chrome extension
- **Lighthouse**: Chrome DevTools Audit tab

### Performance:
- **GTmetrix**: https://gtmetrix.com/
- **PageSpeed Insights**: https://pagespeed.web.dev/

---

## IX. EXAMPLES (Ví dụ áp dụng)

### Example 1: Product Intro Section
```html
<div class="section product-intro">
  <h2>NBJ16 Micro Boring – 12 Boring Bars, Compact & Versatile</h2>
  <p><strong>NBJ16 micro boring</strong> system là giải pháp boring bar compact chuyên dụng cho precision boring lỗ nhỏ-trung Φ6-51mm.</p>
  
  <p><strong>Ưu điểm vượt trội:</strong> Tiết kiệm 60% tool magazine space, độ chính xác IT7-IT8, tương thích BT/NT/SK holders.</p>
  
  <div class="catalog-download">
    <a href="[URL]" class="download-btn">📄 Download Catalog</a>
  </div>
</div>
```

**Áp dụng quy tắc:**
- ✅ H2 có border-bottom (visual separator)
- ✅ Paragraph ngắn (2-3 câu)
- ✅ Bold keywords (`<strong>`)
- ✅ CTA button rõ ràng
- ✅ Max-width 70ch cho paragraphs

### Example 2: Feature Grid
```html
<div class="section product-features">
  <h2>6 Tính Năng Vượt Trội</h2>
  
  <h3>1. Coverage Φ6-51mm (12 Bars)</h3>
  <p><strong>NBJ16</strong> bao gồm 12 boring bars phủ 100% range.</p>
  <ul>
    <li><strong>SBJ-1606:</strong> Φ6-9mm (bearing 6200 series)</li>
    <li><strong>SBJ-1608:</strong> Φ8-12mm (bearing 6201-6202 series)</li>
  </ul>
  
  <h3>2. Precision 0.01mm Dial Indicator</h3>
  <p>Dial adjustment → đạt IT7-IT8 direct, không cần grinding.</p>
</div>
```

**Áp dụng quy tắc:**
- ✅ H3 numbered list (clear hierarchy)
- ✅ Bold list item titles (easy scanning)
- ✅ Colon separator (consistent format)
- ✅ Technical terms bold (keywords)

### Example 3: Responsive Table
```html
<div class="table-responsive">
  <table>
    <thead>
      <tr>
        <th>Boring Bar Model</th>
        <th>Boring Range (mm)</th>
        <th>Insert Type</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>SBJ-1606</strong></td>
        <td>Φ6 - 9</td>
        <td>CCMT060204</td>
      </tr>
    </tbody>
  </table>
</div>
```

**Áp dụng quy tắc:**
- ✅ Thead/tbody structure
- ✅ Thead background tint (#E6F2FF)
- ✅ Mobile: horizontal scroll (table-responsive)
- ✅ Font-size: 1rem desktop, 0.92rem mobile

---

## X. SUMMARY (Tóm tắt)

### Key Principles:
1. **Hierarchy**: H1 (1x) → H2 (sections) → H3 (sub-sections), không dùng H4-H6
2. **Consistency**: Base unit 8px (0.5rem), uniform spacing, color palette đồng bộ
3. **Negative Space**: Max-width 70ch paragraphs, section margin 1.5rem, mobile giảm 25-33%
4. **Contrast**: Text #000 on #FCF7EC = 18.96:1 (WCAG AAA), links #0055AA = 8.59:1 (AA)
5. **Readability**: Line-height 1.7 body text, 2-3 sentences/paragraph, bold keywords
6. **Responsive**: Mobile stack grids, touch targets 44px, font-size minimum 16px


### Design Philosophy:
> **"Less is more. Clear hierarchy, consistent spacing, high contrast, ample whitespace."**

- ✅ **Simplicity**: Không phức tạp hóa (H1-H2-H3 là đủ)
- ✅ **Consistency**: Đồng bộ spacing, colors, components
- ✅ **Accessibility**: WCAG AAA contrast, focus visible, alt text
- ✅ **Performance**: Lazy loading images, optimized CSS
- ✅ **Mobile-first**: Design for small screens, enhance for large

---

## XI. APPLICATIONS SECTION STRUCTURE (Cấu trúc ứng dụng thực tế)

### 1. HTML STRUCTURE (Cấu trúc HTML)

```html
<div class="section use-cases">
  <h2>4 Ứng Dụng Thực Tế</h2>
  <div class="application-grid">
    <div class="application-item">
      <div class="application-header">
        <span class="application-number">01</span>
        <h3>[Tiêu đề ứng dụng]</h3>
      </div>
      <div class="application-content">
        <div class="problem-box">
          <strong class="label">Vấn đề:</strong>
          <p>[Mô tả vấn đề kỹ thuật]</p>
        </div>
        <div class="example-box">
          <strong class="label">Ví dụ thực tế:</strong>
          <p>[Case study cụ thể]</p>
        </div>
        <div class="solution-box">
          <strong class="label">Giải pháp:</strong>
          <ul class="solution-steps">
            <li>[Bước 1]</li>
            <li>[Bước 2]</li>
            <li>[Bước 3]</li>
          </ul>
        </div>
        <div class="result-box">
          <strong class="label">Kết quả:</strong>
          <p>[Kết quả đo lường được - số liệu cụ thể]</p>
        </div>
      </div>
    </div>
    <!-- Repeat for applications 02, 03, 04 -->
  </div>
</div>
```

### 2. DESIGN PRINCIPLES APPLIED

**Hierarchy (Phân cấp):**
- ✅ **Number badges (01-04)**: Circular 2.5rem, white overlay, clear visual order
- ✅ **H3 headings**: Bold, clear application title
- ✅ **4-box structure**: Problem → Example → Solution → Result (logical flow)
- ✅ **Bold labels**: Strong emphasis for box types (font-size: 1.05rem)

**Color-Coded Boxes (Contrast & Consistency):**
- ✅ **Problem box**: Orange tint (#FFF3E0), warning border-left 4px
- ✅ **Example box**: Purple tint (#F3E5F5), purple border-left 4px (#9C27B0)
- ✅ **Solution box**: Blue tint (#E3F2FD), primary border-left 4px
- ✅ **Result box**: Green tint (#E8F5E9), success border-left 4px

**Negative Space:**
- ✅ **Grid gap**: 1rem between application items
- ✅ **Content padding**: 1.5rem inside each application-content
- ✅ **Box spacing**: 1rem gap between problem/example/solution/result boxes
- ✅ **Card breathing room**: Margin-bottom 2rem for section

**Responsive:**
- ✅ **Desktop**: 2-column grid `repeat(auto-fit, minmax(320px, 1fr))`
- ✅ **Mobile (<768px)**: 1-column stack
- ✅ **Reduced padding**: 1.5rem → 1.25rem on mobile
- ✅ **Smaller fonts**: 1.05rem → 1rem labels on mobile
- ✅ **Number badges**: 2.5rem → 2.25rem on mobile

### 3. CSS CLASSES REFERENCE

```css
/* Section wrapper */
.use-cases {
  margin-bottom: 2rem;
}

/* Responsive grid */
.application-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 1rem;
}

/* Application card */
.application-item {
  background: var(--anmi-color-surface);
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid var(--anmi-border-color-soft);
  transition: all 0.3s ease;
}
.application-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border-color: var(--anmi-color-primary);
}

/* Header with gradient */
.application-header {
  background: linear-gradient(135deg, 
    var(--anmi-color-primary), 
    var(--anmi-color-primary-strong));
  color: white;
  padding: 1.25rem;
  border-radius: 12px 12px 0 0;
}

/* Circular number badge */
.application-number {
  display: inline-block;
  width: 2.5rem;
  height: 2.5rem;
  background: rgba(255, 255, 255, 0.2);
  border: 2px solid rgba(255, 255, 255, 0.6);
  border-radius: 50%;
  text-align: center;
  line-height: 2.3rem;
  font-weight: 700;
  font-size: 1.1rem;
}

/* Content area */
.application-content {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1.5rem;
}

/* Color-coded boxes */
.problem-box {
  background: var(--anmi-color-problem-bg);  /* #FFF3E0 */
  border-left: 4px solid var(--anmi-color-warning);
  padding: 1rem;
  border-radius: 4px;
}

.example-box {
  background: var(--anmi-color-example-bg);  /* #F3E5F5 */
  border-left: 4px solid #9C27B0;
  padding: 1rem;
  border-radius: 4px;
}

.solution-box {
  background: var(--anmi-color-solution-bg);  /* #E3F2FD */
  border-left: 4px solid var(--anmi-color-primary);
  padding: 1rem;
  border-radius: 4px;
}

.result-box {
  background: var(--anmi-color-result-bg);  /* #E8F5E9 */
  border-left: 4px solid var(--anmi-color-success);
  padding: 1rem;
  border-radius: 4px;
}

/* Labels */
.label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  font-size: 1.05rem;
}
.problem-box .label { color: var(--anmi-color-warning); }
.example-box .label { color: #9C27B0; }
.solution-box .label { color: var(--anmi-color-primary); }
.result-box .label { color: var(--anmi-color-success); }

/* Solution steps list */
.solution-steps {
  margin: 0;
  padding-left: 1.25rem;
  line-height: 1.7;
}

/* Numbered steps (alternative) */
.numbered-steps {
  list-style: none;
  counter-reset: step-counter;
  padding-left: 0;
}
.numbered-steps li {
  counter-increment: step-counter;
  margin-bottom: 0.75rem;
  padding-left: 2.5rem;
  position: relative;
}
.numbered-steps li::before {
  content: counter(step-counter);
  position: absolute;
  left: 0;
  top: 0;
  width: 1.75rem;
  height: 1.75rem;
  background: var(--anmi-color-primary);
  color: white;
  border-radius: 50%;
  text-align: center;
  line-height: 1.75rem;
  font-weight: 600;
  font-size: 0.9rem;
}

/* Highlight success metrics */
.highlight-success {
  color: var(--anmi-color-success);
  font-weight: 700;
}
```

### 4. CONTENT WRITING GUIDELINES

**Problem Box:**
- Mô tả vấn đề kỹ thuật cụ thể mà khách hàng gặp phải
- Sử dụng thuật ngữ chuyên môn nhưng giải thích rõ ràng
- 2-3 câu ngắn gọn

**Example Box:**
- Case study thực tế từ khách hàng (ví dụ: "Nhà máy ô tô X tại Bắc Ninh")
- Điều kiện cụ thể: loại máy, vật liệu, yêu cầu
- Vấn đề trước khi dùng sản phẩm

**Solution Box:**
- Liệt kê các bước giải pháp rõ ràng
- Dùng bullet list hoặc numbered steps
- Nhấn mạnh tính năng sản phẩm giải quyết vấn đề như thế nào

**Result Box:**
- Kết quả đo lường được: số liệu cụ thể
- Lợi ích: tiết kiệm chi phí, tăng năng suất, giảm downtime
- Dùng `.highlight-success` cho metrics (ví dụ: <span class="highlight-success">giảm 40% breakage</span>)

### 5. EXAMPLE IMPLEMENTATION

```html
<div class="application-item">
  <div class="application-header">
    <span class="application-number">01</span>
    <h3>Taro Lỗ Mù – Bảo Vệ Mũi Taro Không Bị Gãy</h3>
  </div>
  <div class="application-content">
    <div class="problem-box">
      <strong class="label">Vấn đề:</strong>
      <p>Khi taro lỗ mù (blind hole), mũi taro dễ va chạm đáy lỗ nếu độ sâu không chính xác. Điều này gây gãy taro, đặc biệt với vật liệu cứng như thép hợp kim hoặc inox 304.</p>
    </div>
    <div class="example-box">
      <strong class="label">Ví dụ thực tế:</strong>
      <p>Nhà máy cơ khí Hà Nội taro M16×2.0 sâu 35mm trên block động cơ gang xám GG25. Sử dụng rigid holder → gãy taro 3-4 cây/tuần, mất 1.2 triệu/tháng chi phí tool.</p>
    </div>
    <div class="solution-box">
      <strong class="label">Giải pháp:</strong>
      <ul class="solution-steps">
        <li>Thay rigid holder bằng <strong>BT40 Tension-Compression Tapping Holder</strong></li>
        <li>Thiết lập floating ±0.3mm để taro tự động "nhả" khi chạm đáy</li>
        <li>Giảm torque CNC xuống 70% (từ 50Nm → 35Nm) vì holder có torque protection</li>
      </ul>
    </div>
    <div class="result-box">
      <strong class="label">Kết quả:</strong>
      <p>Sau 3 tháng sử dụng: <span class="highlight-success">giảm 90% tỷ lệ gãy taro</span> (từ 3-4 cây/tuần → 0-1 cây/tháng), tiết kiệm 900k/tháng chi phí tool, tăng uptime máy 15%.</p>
    </div>
  </div>
</div>
```

---

**Version History:**
- v1.1.0 (2025-11-14): Added Applications Section Structure (Section XI)
- v1.0.0 (2025-11-11): Initial design rules documentation

**References:**
- WCAG 2.1 Guidelines: https://www.w3.org/WAI/WCAG21/quickref/
- Material Design: https://material.io/design
- Apple Human Interface Guidelines: https://developer.apple.com/design/human-interface-guidelines/
- Refactoring UI: https://www.refactoringui.com/

