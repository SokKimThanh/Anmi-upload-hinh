# 🔍 Phân tích lỗi không phát video trên thiết bị di động

## 📱 Vấn đề báo cáo

Video không phát trên thiết bị di động (mobile devices).

---

## 🔬 Phân tích nguyên nhân

### 1. **Browser Autoplay Policy trên Mobile** ⚠️

**Vấn đề chính:**
- Hầu hết trình duyệt mobile (Safari iOS, Chrome Android) **CHẶN autoplay video có âm thanh**
- Chỉ cho phép autoplay nếu video:
  - ✅ Có `muted` attribute
  - ✅ Có `playsinline` attribute (đặc biệt iOS)
  - ✅ Được trigger bởi user interaction (tap/click)

**Current code status:**
```php
// ✅ CORRECT - anmi-video-banner.php (line 231-236)
<video class="anmi-banner-video" 
       loop 
       muted           // ✅ OK
       playsinline     // ✅ OK
       preload="metadata"
       poster="...">
```

**Kết luận:** HTML video tag đã đúng chuẩn mobile.

---

### 2. **Hover Events không hoạt động trên Mobile** ⚠️

**Vấn đề:**
- Mobile devices **KHÔNG HỖ TRỢ `mouseenter`/`mouseleave` events**
- Current code dùng hover để show video:

```javascript
// ❌ PROBLEM - video-banner.js (line 155-168)
this.$container.on('mouseenter', function() {
    self.isHovered = true;
    // ... show video
});

this.$container.on('mouseleave', function() {
    self.isHovered = false;
    // ... hide video
});
```

**Tác động:**
- Trên mobile, hover events **KHÔNG BAO GIỜ FIRE**
- Video không bao giờ xuất hiện (vẫn ở `opacity: 0`)
- User tap vào banner nhưng không có gì xảy ra

**Current touch support (line 234-244):**
```javascript
// ⚠️ INCOMPLETE
if (this.mobileBehavior === 'video' || this.mobileBehavior === 'both') {
    this.$container.on('touchstart', function() {
        if (self.$video.css('opacity') === '0') {
            self.isHovered = true;
            self.stopSlider();
            self.$images.css('opacity', '0');
            self.$video.css('opacity', '1');
        }
    });
}
```

**Vấn đề với touch support hiện tại:**
1. ❌ Chỉ handle `touchstart`, không có `touchend`
2. ❌ Không play video sau khi show (cần user tap lần 2)
3. ❌ Không show play button
4. ❌ Logic phức tạp (cần tap nhiều lần)

---

### 3. **Mobile Behavior Setting** ⚠️

**Default behavior:**
```javascript
// video-banner.js (line 34-38)
if (this.isMobile() && this.mobileBehavior === 'image') {
    this.disableVideoOnMobile();  // Remove video completely
    this.startSlider();
    return;
}
```

**CSS enforcement:**
```css
/* video-banner.css (line 413-415) */
@media (max-width: 768px) {
    .anmi-video-banner-container[data-mobile-behavior="image"] .anmi-banner-video {
        display: none !important;
    }
}
```

**Vấn đề:**
- Nếu admin set `mobile_behavior = "image"` (default), video **bị xóa hoàn toàn**
- User không thể xem video dù có muốn
- Không có option để user chủ động play video

---

### 4. **Video Play Policy trên iOS** ⚠️

**iOS Safari đặc biệt nghiêm ngặt:**

```javascript
// Current code (line 219-227)
this.$container.on('click', function() {
    if (!self.isVideoPlaying) {
        self.isVideoPlaying = true;
        self.$playOverlay.fadeOut(300);
        
        const video = self.$video[0];
        if (video && video.tagName === 'VIDEO') {
            video.play();  // ⚠️ Có thể bị reject trên iOS
        }
    }
});
```

**iOS requirements:**
- Video play PHẢI được gọi **TRỰC TIẾP** trong event handler
- Không được async (setTimeout, Promise, animation callback)
- Current code gọi `fadeOut(300)` trước `play()` → **CÓ THỂ BỊ REJECT**

---

### 5. **Pointer Events Conflict** ⚠️

**CSS cho iframe:**
```css
/* video-banner.css (line 40) */
.anmi-banner-iframe {
    pointer-events: auto;  // ⚠️ Allow iframe to capture clicks
    z-index: 10;
}
```

**Vấn đề:**
- Khi video (iframe) show (`opacity: 1`), iframe có `pointer-events: auto`
- User click vào video → Click đi vào iframe, KHÔNG vào container
- JavaScript click handler **KHÔNG FIRE**
- Video không play

**Đặc biệt với YouTube/Vimeo iframe:**
- Iframe block tất cả clicks từ parent
- Cần set `pointer-events: none` cho iframe background

---

## 🎯 Tóm tắt các vấn đề chính

| Vấn đề | Mức độ | Tác động | Nền tảng |
|--------|--------|----------|----------|
| **Hover events không hoạt động** | 🔴 Critical | Video không show trên mobile | iOS, Android |
| **Touch events chưa hoàn chỉnh** | 🔴 Critical | Cần tap nhiều lần | iOS, Android |
| **Mobile behavior default = "image"** | 🟡 Medium | Video bị disable mặc định | iOS, Android |
| **iOS autoplay policy** | 🟡 Medium | Video không play tự động | iOS only |
| **Iframe pointer-events** | 🟠 High | Click không trigger play | YouTube/Vimeo |

---

## 🔧 Root Causes Summary

### Nguyên nhân chính video không phát trên mobile:

1. **Desktop-first design:**
   - Logic dựa trên `mouseenter`/`mouseleave`
   - Touch events chỉ là afterthought
   - Chưa test kỹ trên mobile

2. **Incomplete touch support:**
   - Chỉ có `touchstart`, thiếu `touchend`
   - Không integrate với existing play logic
   - Cần user interaction 2 lần (show video → play video)

3. **Conservative default:**
   - `mobile_behavior = "image"` → disable video
   - Giả định mobile không cần video
   - Không có fallback cho user muốn xem video

4. **CSS conflicts:**
   - `opacity: 0` trên video (chờ hover)
   - Hover không xảy ra → video không bao giờ visible
   - Click handler không fire nếu element invisible

5. **iOS-specific issues:**
   - Play() phải synchronous trong user event
   - Current code có animation delay
   - Có thể bị browser reject

---

## 💡 Giải pháp đề xuất

### Option 1: Simple Mobile Experience (Recommended)

**Approach:** Tap anywhere → Play video ngay

```javascript
// Mobile-first logic
if (this.isMobile()) {
    this.$container.on('touchstart click', function(e) {
        e.preventDefault();
        
        // Stop slider
        self.stopSlider();
        
        // Hide images
        self.$images.css('opacity', '0');
        
        // Show + Play video immediately
        self.$video.css('opacity', '1');
        self.$playOverlay.hide();
        
        const video = self.$video[0];
        if (video && video.tagName === 'VIDEO') {
            video.play().catch(err => {
                console.error('Play failed:', err);
                // Fallback: show play button
                self.$playOverlay.show();
            });
        }
        
        self.isVideoPlaying = true;
    });
}
```

**Pros:**
- ✅ Simple UX (1 tap to play)
- ✅ Synchronous play (iOS compliant)
- ✅ No hover dependency
- ✅ Works on all mobile browsers

**Cons:**
- ❌ No preview (tap blind)
- ❌ Different from desktop UX

---

### Option 2: Two-Tap Experience (Match Desktop)

**Approach:** Tap 1 → Show video + play button, Tap 2 → Play

```javascript
if (this.isMobile()) {
    this.$container.on('touchstart', function(e) {
        e.preventDefault();
        
        if (self.$video.css('opacity') === '0') {
            // First tap: Show video
            self.stopSlider();
            self.$images.css('opacity', '0');
            self.$video.css('opacity', '1');
            self.$playOverlay.css('opacity', '1').show();
        } else if (!self.isVideoPlaying) {
            // Second tap: Play video
            self.$playOverlay.hide();
            
            const video = self.$video[0];
            if (video && video.tagName === 'VIDEO') {
                video.play().catch(err => {
                    console.error('Play failed:', err);
                });
            }
            
            self.isVideoPlaying = true;
        }
    });
}
```

**Pros:**
- ✅ Giống desktop UX (preview trước)
- ✅ User control (chọn khi nào play)

**Cons:**
- ❌ Cần 2 taps (phức tạp hơn)
- ❌ First tap vẫn synchronous (tốt cho iOS)

---

### Option 3: Native Video Controls

**Approach:** Trên mobile, dùng native `<video controls>`

```php
<video class="anmi-banner-video" 
       loop 
       muted 
       playsinline 
       <?php echo $is_mobile ? 'controls' : ''; ?>
       preload="metadata">
```

```javascript
if (this.isMobile()) {
    // Show video immediately with controls
    this.$video.css('opacity', '1');
    this.$video.attr('controls', 'controls');
    this.$playOverlay.hide();
    this.stopSlider();
}
```

**Pros:**
- ✅ Native UX (familiar to users)
- ✅ Built-in play/pause/fullscreen
- ✅ Accessibility compliant
- ✅ No JavaScript play() needed

**Cons:**
- ❌ Mất custom styling
- ❌ Khác biệt lớn với desktop
- ❌ Controls chiếm space

---

## 🧪 Test Cases Needed

### Desktop (Working):
- [x] ✅ Hover → Video shows (not playing)
- [x] ✅ Click → Video plays
- [x] ✅ Mouse leave → Video stops

### Mobile (TO FIX):
- [ ] ❌ Tap → Video shows/plays
- [ ] ❌ Video visible và playable
- [ ] ❌ Native controls work (if using Option 3)
- [ ] ❌ Slider stops when video plays
- [ ] ❌ Video resets on navigation away

### iOS Safari specific:
- [ ] ❌ Video plays without user gesture error
- [ ] ❌ `playsinline` prevents fullscreen
- [ ] ❌ Muted video autoplays
- [ ] ❌ Unmute works after autoplay

### Android Chrome specific:
- [ ] ❌ Touch events fire correctly
- [ ] ❌ Video plays on tap
- [ ] ❌ No layout shift

---

## 📊 Browser Compatibility Matrix

| Browser | Autoplay Muted | Autoplay Unmuted | User Gesture Required | playsinline Required |
|---------|----------------|------------------|----------------------|---------------------|
| Chrome Android | ✅ Yes | ❌ No | ✅ Yes | ❌ No |
| Safari iOS | ✅ Yes (with playsinline) | ❌ No | ✅ Yes | ✅ Yes |
| Firefox Android | ✅ Yes | ❌ No | ✅ Yes | ❌ No |
| Samsung Internet | ✅ Yes | ❌ No | ✅ Yes | ❌ No |

**Kết luận:**
- Video tag hiện tại đã đúng (`muted playsinline`)
- Vấn đề là **JavaScript logic**, không phải HTML attributes

---

## 🚀 Recommended Solution: Option 1

**Lý do chọn Option 1:**

1. **Simplest UX:** 1 tap = play (intuitive)
2. **iOS compliant:** Synchronous play in event handler
3. **Least code changes:** Add mobile-specific handler
4. **Best performance:** No multi-step logic
5. **Matches user expectation:** Mobile users expect tap-to-play

**Implementation priority:**
1. Add mobile detection
2. Add touch event handler (Option 1)
3. Disable hover logic on mobile
4. Test on iOS Safari + Android Chrome
5. Add fallback for play() rejection

---

## 📝 Next Steps

1. **Implement Option 1 solution**
2. **Update `mobile_behavior` default** to `"video"` instead of `"image"`
3. **Add error handling** for play() promise rejection
4. **Fix iframe pointer-events** for YouTube/Vimeo
5. **Test thoroughly** on real devices (not just emulator)
6. **Update documentation** for mobile behavior

---

**Status:** 🔴 **Critical Issue - Needs Immediate Fix**  
**Priority:** P0 (Blocking mobile users)  
**Estimated effort:** 2-3 hours  
**Testing required:** iOS Safari, Android Chrome, Firefox Mobile
