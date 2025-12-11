# AnMi News Styling Plugin

**Version:** 1.0.0  
**Author:** Thanh - Content Marketing / Nội dung Kỹ thuật  
**Requires WordPress:** 5.0 or higher  
**Tested up to:** 6.4  
**License:** GPL v2 or later

## Mô tả

Plugin tự động inject CSS và JavaScript vào các trang tin tức thuộc category:
- **Truyền thông** (`truyen-thong`)
- **Tin nội bộ** (`tin-noi-bo`)
- **Báo chí** (`bao-chi`)

Plugin cung cấp styling chuyên nghiệp với:
- CSS Variables system cho easy customization
- Responsive design (Mobile-first approach)
- Lightbox gallery cho hình ảnh
- Reading progress indicator
- Social share buttons
- Print functionality
- Analytics tracking
- Accessibility support (WCAG 2.1)

## Cài đặt

### Cách 1: Upload qua WordPress Admin
1. Zip thư mục `anmi-news-styling`
2. Vào **WordPress Admin > Plugins > Add New**
3. Click **Upload Plugin**
4. Chọn file zip và click **Install Now**
5. Click **Activate Plugin**

### Cách 2: FTP Upload
1. Upload thư mục `anmi-news-styling` vào `/wp-content/plugins/`
2. Vào **WordPress Admin > Plugins**
3. Tìm **AnMi News Styling** và click **Activate**

### Cách 3: Copy trực tiếp
```bash
# Copy plugin vào WordPress
cp -r anmi-news-styling /path/to/wordpress/wp-content/plugins/

# Hoặc tạo symlink
ln -s /path/to/anmi-news-styling /path/to/wordpress/wp-content/plugins/
```

## Cấu trúc thư mục

```
anmi-news-styling/
├── anmi-news-styling.php    # Main plugin file
├── assets/
│   ├── css/
│   │   └── anmi-news-style.css    # Styling cho tin tức
│   └── js/
│       └── anmi-news-script.js    # JavaScript features
├── README.md                # Tài liệu này
└── screenshot.png           # Screenshot (optional)
```

## Tính năng

### 1. Tự động detect news categories
Plugin tự động nhận biết các trang thuộc category tin tức và chỉ load CSS/JS trên những trang đó.

### 2. CSS Variables System
Dễ dàng customize màu sắc, font, spacing thông qua CSS variables:

```css
:root {
    --anmi-primary-color: #003087;
    --anmi-secondary-color: #e31e24;
    --anmi-spacing-lg: 2rem;
    /* ... và nhiều variables khác */
}
```

### 3. Responsive Design
- Desktop: Full 2-column gallery
- Tablet (≤1024px): Optimized layout
- Mobile (≤768px): Single column, stacked images

### 4. Image Gallery với Lightbox
- 2-column grid cho hình ảnh cặp đôi
- Full-width cho hình ảnh quan trọng
- Lightbox zoom với navigation
- Lazy loading cho performance

### 5. Interactive Features
- **Reading Progress Bar**: Thanh tiến trình đọc ở top
- **Back to Top**: Nút scroll về đầu trang
- **Print Button**: In bài viết dễ dàng
- **Share Buttons**: Facebook, LinkedIn, Email
- **Smooth Scroll**: Cuộn mượt đến anchor links

### 6. Analytics Tracking
- Track article views
- Track image clicks
- Track time spent on page
- Compatible với Google Analytics và Facebook Pixel

## Customization

### Thay đổi màu sắc
Edit file `assets/css/anmi-news-style.css`:

```css
:root {
    --anmi-primary-color: #YOUR_COLOR;
    --anmi-secondary-color: #YOUR_COLOR;
}
```

### Thêm category mới
Edit file `anmi-news-styling.php`:

```php
private function is_news_category() {
    // ...
    $news_slugs = array('truyen-thong', 'tin-noi-bo', 'bao-chi', 'your-new-category');
    // ...
}
```

### Disable một feature
Edit file `assets/js/anmi-news-script.js`:

```javascript
init: function() {
    // Comment out feature bạn muốn disable
    // this.setupPrintButton();
    // this.setupShareButtons();
}
```

## Yêu cầu hệ thống

- **WordPress:** 5.0+
- **PHP:** 7.2+
- **jQuery:** Included with WordPress
- **Modern browsers:** Chrome 90+, Firefox 88+, Safari 14+, Edge 90+

## Browser Support

| Browser | Version |
|---------|---------|
| Chrome  | 90+     |
| Firefox | 88+     |
| Safari  | 14+     |
| Edge    | 90+     |
| Mobile Safari | iOS 13+ |
| Chrome Mobile | Android 8+ |

## Performance

- **CSS file size:** ~15KB (minified: ~10KB)
- **JS file size:** ~8KB (minified: ~5KB)
- **Lightbox library:** ~30KB (CDN)
- **Total overhead:** <50KB
- **Load time impact:** <200ms

## Compatibility

### Tested with themes:
- Astra
- GeneratePress
- OceanWP
- Kadence
- Default WordPress themes (Twenty Twenty-Four, etc.)

### Compatible plugins:
- Yoast SEO
- Rank Math
- WooCommerce
- Contact Form 7
- Elementor
- WPBakery

## Troubleshooting

### CSS không load?
1. Clear WordPress cache
2. Clear browser cache
3. Check if category slug matches: `truyen-thong`, `tin-noi-bo`, `bao-chi`
4. Check Console for errors

### Lightbox không hoạt động?
1. Kiểm tra jQuery đã load chưa
2. Check Console for JavaScript errors
3. Ensure images have proper `data-lightbox` attributes
4. Try disabling other lightbox plugins (conflict)

### Responsive không đúng?
1. Check viewport meta tag trong header
2. Clear CSS cache
3. Test với browser DevTools
4. Kiểm tra theme CSS conflicts

## Changelog

### Version 1.0.0 (2025-11-18)
- Initial release
- CSS Variables system
- Responsive design
- Lightbox gallery
- Reading progress indicator
- Social share buttons
- Print functionality
- Analytics tracking
- Accessibility support

## Roadmap

### Version 1.1.0 (Coming soon)
- [ ] Admin settings page
- [ ] Color picker for customization
- [ ] Enable/disable individual features
- [ ] Custom CSS editor
- [ ] Template system

### Version 1.2.0 (Future)
- [ ] Dark mode support
- [ ] More share options (Twitter, WhatsApp, Telegram)
- [ ] Table of contents generator
- [ ] Estimated reading time
- [ ] Related posts widget

## Support

- **Email:** info@anmitools.com
- **Website:** https://anmitools.com
- **Documentation:** https://anmitools.com/docs/anmi-news-styling

## License

This plugin is licensed under GPL v2 or later.

## Credits

- **Developer:** Thanh - Content Marketing / Nội dung Kỹ thuật
- **Lightbox2:** Lokesh Dhakar (https://lokeshdhakar.com/projects/lightbox2/)
- **Icons:** System emoji (no external dependencies)

---

**© 2025 An Mi Tools - Ghi danh công nghiệp Việt trên bản đồ thế giới**
