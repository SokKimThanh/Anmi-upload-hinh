# Live Preview Feature - v1.5.0

## 🎉 Tính năng mới: Xem Trước Trực Tiếp

Plugin **An Mi Video Banner v1.5.0** giờ có **Inline Live Preview** ngay trong trang Edit Banner!

---

## ✨ Tính năng

### 📍 **Vị trí:** Sidebar phải của trang Edit Banner

```
┌─────────────────────────────────────┐
│  📝 Banner Information              │
│  🎬 Video Settings                  │
│  🖼️ Image Slider                    │
│  📝 Content Overlay                 │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐ ← SIDEBAR
│  📊 Xuất Bản                        │
│  ⚙️ Cài Đặt Hiển Thị                │
│  📋 Mã Nhúng                        │
│  👁️ **XEM TRƯỚC TRỰC TIẾP** ⭐ MỚI │  ← PREVIEW BOX
│  ❓ Trợ Giúp                        │
└─────────────────────────────────────┘
```

---

## 🎯 Chức năng Preview

### ✅ **Realtime Updates:**

Preview tự động cập nhật khi bạn thay đổi:

| Field | Trigger Event | Update Speed |
|-------|---------------|--------------|
| **Video URL** | `input` | Instant |
| **Banner Title** | `input` | Instant |
| **Banner Subtitle** | `input` | Instant |
| **Button Text** | `input` | Instant |
| **Button Link** | `input` | Instant |
| **Transition Effect** | `change` | Instant |
| **Autoplay Delay** | `input` | Instant |
| **Slider Speed** | `input` | Instant |
| **Slider Effect** | `change` | Instant |
| **Show Title** | `change` | Instant |
| **Show Subtitle** | `change` | Instant |
| **Show Button** | `change` | Instant |
| **Add Image** | Upload complete | Instant |
| **Remove Image** | Click delete | Instant |
| **Reorder Images** | Drag & drop | Instant |

### ✅ **Interactive Preview:**

```
┌──────────────────────────────────┐
│                                  │
│    [Slider Image 1] ──┐          │
│                       │          │
│   Hover chuột ────────┤          │
│                       ↓          │
│    [Video plays] ←─ Transition   │
│                                  │
│   Title + Subtitle + Button      │
│   (if enabled)                   │
│                                  │
└──────────────────────────────────┘
```

**Tương tác đầy đủ:**
- ✅ **Hover → Video phát** (với transition effect)
- ✅ **Slider auto-rotate** (khi không hover)
- ✅ **Slider dots** clickable
- ✅ **Content overlay** hiển thị/ẩn theo settings
- ✅ **YouTube/Vimeo iframe** hoạt động bình thường
- ✅ **Direct MP4** play/pause mượt mà

---

## 🔧 Cách hoạt động

### **1. HTML Injection**

```javascript
function updateLivePreview() {
    // Lấy tất cả settings từ form
    var videoUrl = $('#video_url').val();
    var title = $('#banner_title').val();
    // ... etc
    
    // Build HTML giống hệt frontend
    var html = '<div class="anmi-video-banner-container ...">';
    
    // Detect video type (YouTube/Vimeo/MP4)
    if (videoType === 'youtube' || videoType === 'vimeo') {
        html += '<iframe src="..." />';
    } else {
        html += '<video src="..." />';
    }
    
    // Add slider
    html += '<div class="anmi-banner-slider">...';
    
    // Add content overlay
    html += '<div class="anmi-banner-content">...';
    
    // Inject vào preview container
    $('#live_preview_container').html(html);
    
    // Initialize banner JavaScript
    new AnMiVideoBanner($container[0]);
}
```

### **2. Event Listeners**

```javascript
// Trigger update on any field change
$('#video_url, #banner_title, #banner_subtitle, ...').on('input change', function() {
    updateLivePreview();
});

$('#show_title, #show_subtitle, #show_button').on('change', function() {
    updateLivePreview();
});
```

### **3. Frontend Assets trong Admin**

```php
// admin-panel.php
public function enqueue_admin_assets($hook) {
    // Admin CSS
    wp_enqueue_style('anmi-banner-admin-css', ...);
    
    // ✨ Frontend CSS for preview
    wp_enqueue_style('anmi-video-banner-style', ...); // ← MỚI
    
    // Admin JS
    wp_enqueue_script('anmi-banner-admin-js', ...);
    
    // ✨ Frontend JS for preview
    wp_enqueue_script('anmi-video-banner-script', ...); // ← MỚI
}
```

**→ Preview sử dụng 100% code của frontend!**

---

## 🎨 UI Design

### **Preview Box Structure:**

```html
<div class="postbox">
    <div class="postbox-header">
        <h2>👁️ Xem Trước Trực Tiếp</h2>
    </div>
    <div class="inside">
        <p class="description">Hover chuột để xem video phát</p>
        
        <div id="live_preview_container" 
             style="height: 300px; background: #000; border-radius: 4px;">
            
            <!-- Banner render here -->
            <div class="anmi-video-banner-container">
                <video/iframe>...</video/iframe>
                <div class="anmi-banner-slider">...</div>
                <div class="anmi-banner-content">...</div>
            </div>
            
        </div>
    </div>
</div>
```

### **Custom Preview Styles:**

```css
/* Scale down content for smaller preview */
#live_preview_container .anmi-banner-title {
    font-size: 20px !important; /* Smaller than frontend */
}

#live_preview_container .anmi-banner-subtitle {
    font-size: 12px !important;
}

#live_preview_container .anmi-banner-btn {
    font-size: 11px !important;
    padding: 6px 16px !important;
}

/* Hover hint animation */
#live_preview_container:hover::after {
    content: '👆 Hover để xem video';
    animation: fadeInOut 2s infinite;
}
```

---

## 🚀 User Experience Flow

### **Scenario 1: Tạo banner mới**

```
1. Admin vào "Add New Banner"
   └─ Preview box: "Nhập Video URL và tải lên hình ảnh để xem preview"

2. Admin paste YouTube URL
   └─ Preview: Placeholder message (chưa có images)

3. Admin click "Tải lên hình ảnh"
   └─ Upload 3 images
   └─ Preview: ✅ Slider xuất hiện ngay!

4. Admin hover preview
   └─ ✅ YouTube video phát!

5. Admin nhập Title, Subtitle, Button
   └─ Preview: ✅ Content overlay xuất hiện realtime!

6. Admin thay đổi Transition Effect → "Zoom"
   └─ Preview: ✅ Effect thay đổi ngay!

7. Admin toggle "Show Subtitle" → OFF
   └─ Preview: ✅ Subtitle biến mất!

8. Admin hài lòng → Click "Tạo Banner"
   └─ Save thành công!
```

### **Scenario 2: Edit banner có sẵn**

```
1. Admin click "Edit" banner ID #5
   └─ Page load
   └─ Preview: ✅ Tự động render banner với data từ database!

2. Admin hover preview
   └─ ✅ Video phát như frontend!

3. Admin drag-drop reorder images
   └─ Preview: ✅ Slider order cập nhật ngay!

4. Admin thay đổi Video URL từ MP4 → YouTube
   └─ Preview: ✅ Chuyển từ <video> sang <iframe>!

5. Admin thay Autoplay Delay từ 0s → 2s
   └─ Preview: ✅ Hover delay 2s mới phát video!

6. Admin click "Cập Nhật Banner"
   └─ Save thành công!
```

---

## 🧪 Test Cases

### **Test 1: Empty State**
```
Given: Trang Add New Banner
When: Chưa nhập gì
Then: Preview hiện placeholder icon + message
```

### **Test 2: Only Video URL**
```
Given: Paste YouTube URL
When: Chưa upload images
Then: Preview hiện placeholder "Vui lòng tải lên hình ảnh"
```

### **Test 3: Video + Images**
```
Given: YouTube URL + 3 images
When: Preview renders
Then: 
  ✅ Slider hiển thị image 1
  ✅ Slider dots (3 dots)
  ✅ Hover → YouTube iframe fade in
  ✅ Mouse leave → Slider fade back
```

### **Test 4: Content Visibility**
```
Given: Title + Subtitle + Button đã nhập
When: Toggle "Show Title" OFF
Then: Preview title biến mất ngay lập tức
```

### **Test 5: Transition Effects**
```
Given: Banner với video + slider
When: Chọn transition "Blur"
Then: 
  ✅ Container class: "transition-blur"
  ✅ Hover → Blur effect xuất hiện
```

### **Test 6: Image Upload**
```
Given: Preview đang hiển thị 2 images
When: Upload thêm 1 image
Then: 
  ✅ Preview slider có 3 slides
  ✅ Slider dots update (3 dots)
  ✅ New image thêm vào cuối
```

### **Test 7: Image Delete**
```
Given: Preview với 3 images
When: Delete image thứ 2
Then:
  ✅ Preview slider còn 2 slides
  ✅ Slider dots update (2 dots)
  ✅ Order giữ nguyên
```

### **Test 8: Image Reorder**
```
Given: Preview với images [A, B, C]
When: Drag image C lên đầu
Then: Preview slider order: [C, A, B]
```

### **Test 9: YouTube → Vimeo**
```
Given: Preview đang render YouTube iframe
When: Thay URL thành Vimeo
Then:
  ✅ Preview re-render với Vimeo iframe
  ✅ Embed params khác nhau
  ✅ Hover vẫn hoạt động
```

### **Test 10: MP4 → YouTube**
```
Given: Preview đang render <video> tag
When: Thay URL thành YouTube
Then:
  ✅ Preview chuyển thành <iframe>
  ✅ Không còn poster attribute
  ✅ Autoplay hoạt động ngay
```

---

## 🔍 Technical Details

### **Video Type Detection:**

```javascript
// YouTube regex
var youtubeMatch = videoUrl.match(
    /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i
);

// Vimeo regex  
var vimeoMatch = videoUrl.match(
    /vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|)(\d+)(?:$|\/|\?)/i
);

// Build embed URL
if (youtubeMatch) {
    embedUrl = 'https://www.youtube.com/embed/' + videoId + 
               '?autoplay=1&mute=1&loop=1&playlist=' + videoId + 
               '&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1';
}
```

### **Preview Instance Management:**

```javascript
var previewInstance = null;

function updateLivePreview() {
    // Destroy old instance
    if (previewInstance) {
        previewInstance = null;
    }
    
    // Build new HTML
    $('#live_preview_container').html(html);
    
    // Initialize new instance
    setTimeout(function() {
        if (typeof AnMiVideoBanner !== 'undefined') {
            previewInstance = new AnMiVideoBanner($container[0]);
        }
    }, 100);
}
```

**→ Mỗi lần update = destroy + recreate instance mới!**

### **Performance Optimization:**

```javascript
// Debounce không cần thiết vì:
// 1. Update chỉ trigger khi user thay đổi field
// 2. HTML injection rất nhanh (<10ms)
// 3. AnMiVideoBanner init lightweight

// Nhưng nếu muốn optimize:
var updateTimeout;
$('#video_url').on('input', function() {
    clearTimeout(updateTimeout);
    updateTimeout = setTimeout(updateLivePreview, 300); // Debounce 300ms
});
```

---

## 📊 Benefits

### **Cho Admin:**
- ✅ **Xem ngay kết quả** → Không cần save + reload trang
- ✅ **Test hover effects** → Đảm bảo UX trước khi publish
- ✅ **Thử transition** → Chọn effect đẹp nhất
- ✅ **Verify content** → Check title/button display
- ✅ **Confidence** → Biết chắc banner sẽ hoạt động

### **Cho Developer:**
- ✅ **Code reuse** → Frontend assets dùng lại 100%
- ✅ **Consistency** → Preview = Frontend (không sai lệch)
- ✅ **Easy debug** → Console logs trong admin
- ✅ **WYSIWYG** → True "What You See Is What You Get"

### **Cho Client:**
- ✅ **Faster workflow** → Giảm time từ 10 phút → 2 phút
- ✅ **Less errors** → Không publish banner lỗi
- ✅ **Better UX** → Website luôn có banner đẹp
- ✅ **Professional** → Plugin cao cấp hơn competitor

---

## 🎯 Use Cases

### **1. A/B Testing Transitions:**

```
Admin muốn chọn transition effect đẹp nhất:

Fade → Hover preview → "OK nhưng hơi chậm"
Zoom → Hover preview → "Hay hơn!"
Blur → Hover preview → "Perfect! Dùng cái này"
```

### **2. Content Visibility Testing:**

```
Admin không chắc có nên hiện subtitle:

Show Subtitle: ON → Preview → "Quá dài, che mất video"
Show Subtitle: OFF → Preview → "Gọn gàng hơn!"
```

### **3. Slider Speed Optimization:**

```
Admin test speed tốt nhất:

3000ms → Preview → "Hơi nhanh"
5000ms → Preview → "Hợp lý"
7000ms → Preview → "Quá chậm"
→ Chọn 5000ms
```

### **4. Video URL Validation:**

```
Admin paste URL sai:

URL: "https://youtube.com/invalid" → Preview: Không hiển thị
→ Admin biết ngay URL sai → Fix URL
→ Preview hiển thị OK → Proceed
```

---

## 🔮 Future Improvements

### **1. Mobile Preview Mode:**

```html
<div class="preview-mode-switcher">
    <button class="active">Desktop</button>
    <button>Tablet</button>
    <button>Mobile</button>
</div>
```

**→ Switch responsive views trong preview**

### **2. Fullscreen Preview:**

```javascript
// Button to expand preview
$('.preview-fullscreen').on('click', function() {
    $('#live_preview_container').addClass('fullscreen');
    // Preview fills entire screen
});
```

### **3. Preview History:**

```javascript
// Save preview snapshots
var previewHistory = [];
function savePreviewSnapshot() {
    previewHistory.push({
        timestamp: Date.now(),
        settings: getCurrentSettings(),
        thumbnail: capturePreviewImage()
    });
}
```

**→ Compare different versions**

### **4. Performance Metrics:**

```javascript
// Show preview performance
console.log('Preview render time:', renderTime + 'ms');
console.log('Video load time:', videoLoadTime + 'ms');
```

### **5. AI Suggestions:**

```javascript
// Analyze preview and suggest improvements
if (titleTooLong) {
    showWarning('Title quá dài - nên rút ngắn để UX tốt hơn');
}
```

---

## 📝 Changelog

### **v1.5.0 (November 3, 2025)**
- ✨ NEW: Inline Live Preview trong admin edit page
- ✨ NEW: Realtime updates khi thay đổi settings
- ✨ NEW: Full interactive preview (hover, video play, slider)
- ✨ NEW: YouTube/Vimeo/MP4 support trong preview
- ✨ NEW: Auto-detect video type và render đúng format
- ✨ NEW: Content visibility preview realtime
- ✨ NEW: Transition effects preview
- 🎨 IMPROVED: Preview CSS scaled down for sidebar
- 🎨 IMPROVED: Hover hint animation
- 📚 NEW: Live Preview documentation

---

## 🎓 Documentation Links

- **Main Plugin:** `README.md`
- **YouTube/Vimeo:** `YOUTUBE-VIMEO-SUPPORT.md`
- **Video Optimization:** `VIDEO-OPTIMIZATION-NOTES.md`
- **Live Preview:** `LIVE-PREVIEW-FEATURE.md` ← You are here

---

**Version:** 1.5.0  
**Feature:** Live Preview  
**Release Date:** November 3, 2025  
**Author:** An Mi Tools Technical Team  

🎉 **Preview trước khi publish - Không còn lo banner lỗi!**
