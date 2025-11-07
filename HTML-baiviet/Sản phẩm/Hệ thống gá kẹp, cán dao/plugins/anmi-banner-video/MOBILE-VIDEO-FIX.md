# Mobile Video Playback Fix - Implementation Summary

## Problem
Mobile devices showed black screen when trying to play video with no autoplay and no sound, especially on iOS Safari where autoplay restrictions are strict.

## Solution
Integrated WordPress core's `WP_Widget_Media_Video` to leverage WordPress's built-in mobile video handling while maintaining custom banner behavior.

---

## Changes Made

### 1. **PHP Backend (`anmi-banner-video-pro.php`)**

#### Added Method: `render_wp_core_video()`
Location: Lines 132-217

**Purpose**: Wraps WordPress core video widget with custom attributes for mobile interaction.

**Key Features**:
- Uses `the_widget('WP_Widget_Media_Video')` for MP4 videos
- Adds custom data attributes for JS interaction:
  - `data-enable-autoplay`: Autoplay setting
  - `data-enable-muted`: Muted setting
  - `data-poster`: Poster/fallback image URL
  - `data-video-url`: Original video URL
- Fallback to standard `<video>` tag if WP widget unavailable
- Only processes MP4 direct videos (not YouTube/Vimeo)

**Usage**:
```php
$plugin->render_wp_core_video($video_url, $settings, $poster_image);
```

---

### 2. **Template (`templates/banner-output.php`)**

#### Modified Video Rendering Section
Location: Lines 53-95

**Changes**:
- For MP4 videos: calls `render_wp_core_video()` instead of direct `<video>` tag
- Maintains fallback to standard video element
- Preserves YouTube/Vimeo iframe rendering unchanged

**Before**:
```php
<video class="abvp-video-frame anmi-banner-video" ...>
```

**After**:
```php
<?php 
$wp_video_html = $plugin->render_wp_core_video(...);
if (!empty($wp_video_html)) {
    echo $wp_video_html;
} else {
    // Fallback
}
?>
```

---

### 3. **JavaScript (`assets/js/video-banner.js`)**

#### A. Enhanced Constructor
Location: Lines 11-26

**Added**:
- Detection of WP core video wrapper (`.abvp-wp-video-wrapper`)
- Finding actual video element within wrapper
- `isWPCoreVideo` flag for conditional logic

**Code**:
```javascript
this.$wpVideoWrapper = this.$container.find('.abvp-wp-video-wrapper');
if (this.$wpVideoWrapper.length > 0) {
    const $wpVideo = this.$wpVideoWrapper.find('video');
    if ($wpVideo.length > 0) {
        this.$video = $wpVideo;
        this.isWPCoreVideo = true;
    }
}
```

---

#### B. Updated `attemptAutoplay()` Method
Location: Lines 162-198

**Added**:
- Mobile interaction setup for WP core video
- Poster visibility check on autoplay failure
- Better error handling with poster fallback

**Key Logic**:
```javascript
if (this.isWPCoreVideo && this.isMobileDevice) {
    this.setupWPCoreVideoMobileInteraction(video);
}

playPromise.catch(() => {
    // Show overlay and ensure poster visible
    if (this.isWPCoreVideo) {
        this.ensureWPVideoPosterVisible(video);
    }
});
```

---

#### C. New Method: `ensureWPVideoPosterVisible()`
Location: Lines 534-548

**Purpose**: Ensures poster image is shown when autoplay fails on mobile.

**Features**:
- Reads poster URL from wrapper data attribute
- Sets video.poster if not already set
- Reloads video to show poster frame

---

#### D. New Method: `setupWPCoreVideoMobileInteraction()`
Location: Lines 550-600

**Purpose**: Handles mobile tap-to-play interaction.

**Features**:
- Attaches click handler to play overlay
- Attempts video playback on user tap
- Adds `.playing` CSS class to video element
- Stops image slider when video plays
- Handles video ended event to restart slider
- Makes overlay clickable on mobile (`pointer-events: auto`)

**Flow**:
1. User taps play button overlay
2. JS attempts `video.play()`
3. On success: hide overlay, add `.playing` class, stop slider
4. On error: keep overlay visible, log error
5. On video end: remove `.playing`, show overlay, restart slider

---

### 4. **CSS Styling (`assets/css/video-banner.css`)**

#### Added WP Core Video Wrapper Styles
Location: Lines 148-217

**New Selectors**:

1. **`.abvp-wp-video-wrapper`**: 
   - Absolute positioning to fill container
   - z-index: 1 (below slider by default)

2. **`.abvp-wp-video-wrapper .wp-video`, `.mejs-container`**:
   - Remove WordPress default margins/padding
   - Force 100% width/height

3. **`.abvp-wp-video-wrapper video`**:
   - Absolute positioning with object-fit: cover
   - Hidden by default (`opacity: 0`)
   - Shown on hover (`opacity: 1`)

4. **Mobile-specific rules** (`@media (max-width: 768px)`):
   - Force play overlay visibility and interaction
   - Keep video hidden until playing
   - `.playing` class shows video (`opacity: 1`)

---

## How It Works

### Desktop Flow
1. Image slider displays by default
2. User hovers → video loads in background (poster visible)
3. User hovers again → video plays automatically (muted)
4. Video shown with opacity transition
5. User leaves → video pauses, resets, slider resumes

### Mobile Flow (NEW)
1. Image slider displays by default
2. Play button overlay is visible and clickable
3. User taps play button:
   - JS detects tap on overlay
   - Calls `video.play()` (user-initiated, bypasses autoplay restrictions)
   - On success: video plays with sound, overlay hides, slider stops
   - On failure: overlay stays visible, poster shown
4. Video ends → overlay reappears, slider resumes

### Autoplay Failure Handling
- **Desktop**: Show overlay, allow retry on next hover
- **Mobile**: 
  - Ensure poster image is visible (not black screen)
  - Keep play button overlay visible
  - Enable tap-to-play interaction
  - User gesture required to start playback

---

## Benefits

✅ **Uses WordPress Core Widget**: Leverages WP's built-in mobile video handling (MediaElement.js)
✅ **No Black Screen**: Poster image always visible when video not playing
✅ **Tap to Play**: User gesture bypasses mobile autoplay restrictions
✅ **Maintains Custom Behavior**: Slider integration, hover effects preserved
✅ **Graceful Fallback**: Falls back to standard `<video>` if WP widget unavailable
✅ **Z-index Fix**: Video shown above slider when playing, avoids visibility issues
✅ **Sound Support**: Mobile tap allows audio playback (not forced muted)

---

## Testing Checklist

### Desktop
- [ ] Video autoplays on hover (muted)
- [ ] Video hidden when not hovering
- [ ] Slider works normally
- [ ] Volume control appears when video plays

### Mobile (iOS Safari)
- [ ] Poster image visible by default (no black screen)
- [ ] Play button overlay visible and tappable
- [ ] Video plays with sound when tapped
- [ ] Slider stops when video plays
- [ ] Video overlay reappears after video ends
- [ ] Slider resumes after video ends

### Mobile (Chrome Android)
- [ ] Same behavior as iOS Safari
- [ ] Video controls (if enabled) accessible

### Fallback Scenarios
- [ ] Works without WP_Widget_Media_Video class
- [ ] YouTube/Vimeo iframes unchanged
- [ ] No JavaScript errors in console

---

## Files Modified

1. ✏️ `anmi-banner-video-pro.php` - Added `render_wp_core_video()` method
2. ✏️ `templates/banner-output.php` - Updated video rendering to use WP core widget
3. ✏️ `assets/js/video-banner.js` - Added mobile interaction handlers
4. ✏️ `assets/css/video-banner.css` - Added WP wrapper and mobile styles

---

## Future Enhancements

🔮 **Potential Improvements**:
- Add loading spinner while video buffers on mobile
- Preload video metadata on mobile for faster playback
- Custom controls for better mobile UX
- Analytics tracking for mobile video plays
- Progressive enhancement for older browsers

---

## Developer Notes

### WordPress Core Video Widget
The `WP_Widget_Media_Video` class is part of WordPress core (since WP 4.8). It uses:
- **MediaElement.js** for cross-browser video playback
- **Built-in poster handling** with proper fallback
- **Mobile-optimized player** with touch controls
- **Accessibility features** (ARIA labels, keyboard nav)

### Why Not MediaElement.js Directly?
Using the WordPress widget ensures:
- Version compatibility with WordPress core
- No need to enqueue additional scripts
- Automatic updates when WordPress updates
- WordPress best practices for media handling

### Data Attributes Reference
```html
<div class="abvp-wp-video-wrapper"
     data-enable-autoplay="1"
     data-enable-muted="1"
     data-poster="https://example.com/poster.jpg"
     data-video-url="https://example.com/video.mp4">
    <!-- WordPress widget output here -->
</div>
```

---

## Troubleshooting

### Issue: Black screen still appears
**Solution**: Check that poster image URL is valid and accessible

### Issue: Play button doesn't work on mobile
**Solution**: Verify `.abvp-play-icon` has `pointer-events: auto` in CSS

### Issue: Video doesn't stop when slider should resume
**Solution**: Check `setupWPCoreVideoMobileInteraction()` ended event listener

### Issue: WP widget not loading
**Solution**: Ensure WordPress core is up-to-date (4.8+), check for theme conflicts

---

## Version History

- **v2.3.0** (Current): Added WordPress core video widget integration for mobile
- **v1.6.12**: Previous version with custom video handling

---

**Implementation Date**: 2025-01-XX  
**Developer**: AnMi Tools Technical Team  
**Plugin**: AnMi Banner Video Pro  
