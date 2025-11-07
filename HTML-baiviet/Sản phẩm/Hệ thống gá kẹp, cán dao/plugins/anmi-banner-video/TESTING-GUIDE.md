# Quick Testing Guide - Mobile Video Fix

## Test Setup

### Create a Test Banner
1. Go to WordPress admin → AnMi Video Banner
2. Create new banner with:
   - **Video**: Upload or use MP4 URL (e.g., sample.mp4)
   - **Images**: Add at least 1 image for poster/slider
   - **Settings**: Enable autoplay, enable muted, enable loop

### Test Page Setup
Add shortcode to any page/post:
```
[anmi_banner_video_pro id="1"]
```

Or use Elementor widget: "An Mi Video Banner"

---

## Desktop Testing (Chrome/Firefox)

### Expected Behavior
1. ✅ Page loads → Image slider visible
2. ✅ Hover over banner → Play button appears
3. ✅ Hover continues → Video plays (muted)
4. ✅ Video transitions smoothly (opacity fade)
5. ✅ Volume button appears (bottom right)
6. ✅ Mouse leaves → Video stops, resets, slider resumes

### Debug Console
Open browser DevTools (F12), check for:
- ❌ No JavaScript errors
- ✅ Console log: "Video ready" or similar
- ✅ Network tab: video file loaded

---

## Mobile Testing (iOS Safari - CRITICAL)

### Device Setup
- iPhone/iPad with iOS 12+ (Safari)
- Enable "Request Desktop Website" OFF
- Clear browser cache

### Expected Behavior
1. ✅ Page loads → Poster image visible (first slider image)
2. ✅ Play button overlay visible and centered
3. ✅ NO BLACK SCREEN (this was the bug!)
4. ✅ Tap play button → Video starts playing
5. ✅ Video plays WITH SOUND (not muted by default)
6. ✅ Slider stops while video plays
7. ✅ Video ends → Play button reappears, slider resumes

### Debug on iOS
Use Safari Remote Debugging:
1. Mac: Safari → Develop → [Your iPhone]
2. Open console
3. Check for errors:
   - ❌ "Mobile video play failed" = ISSUE
   - ✅ No errors = GOOD

### Common iOS Issues
**Black screen appears**:
- Check: `data-poster` attribute set in HTML
- Check: Poster image URL is valid
- Solution: Verify `ensureWPVideoPosterVisible()` running

**Play button doesn't respond**:
- Check: CSS `pointer-events: auto` on `.abvp-play-icon`
- Check: JS event handler attached (line 561)
- Solution: Clear cache, reload page

**Video plays but no sound**:
- Check: iOS restrictions require user gesture
- Check: Video not force-muted on mobile
- Solution: Ensure `setupWPCoreVideoMobileInteraction()` called

---

## Mobile Testing (Android Chrome)

### Expected Behavior
Similar to iOS, but less restrictive:
1. ✅ Poster image visible
2. ✅ Tap play → Video plays
3. ✅ Sound works if unmuted
4. ✅ Browser controls may appear (if enabled)

### Android-Specific
- May autoplay muted automatically (less restricted than iOS)
- Play button should still work if autoplay fails

---

## Test Cases Checklist

### ✅ Basic Functionality
- [ ] Video loads without errors
- [ ] Poster image displays (no black screen)
- [ ] Play button is visible
- [ ] Play button is clickable
- [ ] Video plays on interaction
- [ ] Slider stops during video
- [ ] Slider resumes after video

### ✅ Mobile-Specific
- [ ] iOS Safari: No autoplay, tap required ✅
- [ ] iOS Safari: Poster visible before tap ✅
- [ ] iOS Safari: Sound plays on tap ✅
- [ ] Android Chrome: Similar behavior ✅
- [ ] Mobile landscape: Video fills screen
- [ ] Mobile portrait: Video maintains aspect ratio

### ✅ Edge Cases
- [ ] Slow connection: Poster shows while buffering
- [ ] Video file missing: Error handled gracefully
- [ ] Multiple banners on same page: Each works independently
- [ ] Elementor preview: Works in editor
- [ ] Admin preview: Desktop/mobile toggle works

### ✅ Fallback Testing
- [ ] Without WP widget class: Standard video works
- [ ] YouTube URL: Iframe rendering unchanged
- [ ] Vimeo URL: Iframe rendering unchanged
- [ ] No JavaScript: Poster image still shows

---

## Debug Commands

### Browser Console (Desktop)
```javascript
// Check if video wrapper detected
jQuery('.abvp-wp-video-wrapper').length > 0

// Check video element
jQuery('.abvp-wp-video-wrapper video')[0]

// Force mobile simulation
jQuery('.anmi-video-banner-container').attr('data-preview-device', 'mobile')
window.location.reload()

// Check play overlay event
jQuery('.anmi-play-overlay').data('events')
```

### iOS Safari Console (Remote Debug)
```javascript
// Check poster URL
jQuery('.abvp-wp-video-wrapper').data('poster')

// Check if mobile detected
navigator.userAgent

// Manual play test
const video = jQuery('.abvp-wp-video-wrapper video')[0];
video.play().then(() => console.log('OK')).catch(e => console.error(e))
```

---

## Performance Testing

### Metrics to Check
- **Page load time**: < 3 seconds
- **Video buffer time**: < 2 seconds on 4G
- **Memory usage**: < 100MB increase
- **No layout shift**: Poster maintains size

### Tools
- Chrome DevTools → Performance tab
- Lighthouse audit (mobile)
- Network throttling (Slow 3G)

---

## Comparison: Before vs After

### BEFORE (v1.6.12)
❌ Mobile shows black screen  
❌ No poster fallback  
❌ Autoplay blocked silently  
❌ No user interaction handler  
❌ No sound on mobile  

### AFTER (v2.3.0)
✅ Poster image always visible  
✅ Play button works on tap  
✅ User gesture bypasses restrictions  
✅ Sound enabled on mobile tap  
✅ Graceful autoplay failure handling  
✅ WordPress core compatibility  

---

## Expected Console Output

### Desktop (Hover to Play)
```
[AnMi Video Banner] Initialized
[AnMi Video Banner] WP Core Video detected: true
[AnMi Video Banner] Video ready
[AnMi Video Banner] Autoplay attempt...
[AnMi Video Banner] Video playing
```

### Mobile (Tap to Play)
```
[AnMi Video Banner] Initialized
[AnMi Video Banner] Mobile device detected
[AnMi Video Banner] WP Core Video mobile interaction setup
[AnMi Video Banner] Autoplay attempt...
[AnMi Video Banner] Autoplay blocked (expected on mobile)
[AnMi Video Banner] Poster visible, waiting for user tap...
[User taps play button]
[AnMi Video Banner] User tap detected
[AnMi Video Banner] Video playing
```

---

## Reporting Issues

If testing fails, provide:
1. **Device/Browser**: "iOS 15.2 Safari" or "Android 12 Chrome"
2. **Issue**: "Black screen appears" or "Play button doesn't work"
3. **Console errors**: Copy full error message
4. **Screenshot**: Show the issue visually
5. **Steps to reproduce**: 1, 2, 3...

---

## Success Criteria

✅ **Fix Verified** when:
- No black screen on iOS Safari mobile
- Play button responds to tap
- Video plays with sound after user gesture
- Slider integration works smoothly
- No JavaScript console errors
- Performance remains acceptable

---

**Last Updated**: 2025-01-XX  
**Plugin Version**: v2.3.0  
**Testing Status**: ⏳ Pending Real Device Testing
