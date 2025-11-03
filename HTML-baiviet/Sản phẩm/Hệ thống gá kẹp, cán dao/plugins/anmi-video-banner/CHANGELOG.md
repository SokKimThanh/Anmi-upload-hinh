# An Mi Video Banner - Changelog

## Version 1.6.3 (2025-11-03)

### 🐛 Critical Bug Fixes
- **Fixed YouTube URL Detection:** Updated regex pattern to support `youtu.be` URLs with query parameters (e.g., `?si=...`)
  - Old pattern: `/youtu\.be\/)([^"&?\/ ]{11})/` ❌ Failed on URLs with `?si=` parameter
  - New pattern: `/youtu\.be\/)([a-zA-Z0-9_-]{11})(?:[?&]|$)/` ✅ Works with all YouTube share URL formats
- **Enhanced WebFontLoader Dequeue:** Added early `admin_init` hook with priority 9999 to prevent script conflicts
  - Prevents "d[b].on is not a function" error more reliably
  - Added late priority (999) to `admin_enqueue_scripts`
- **Improved Modal Preview Debugging:** Added comprehensive console logs for video URL parsing:
  - Original video_url logging
  - video_type detection logging
  - Iframe extraction tracking
  - YouTube/Vimeo detection confirmation
  - Final embed URL verification

### 🔍 Debug Improvements
- Console logs now show complete video processing pipeline:
  ```javascript
  Original video_url: https://youtu.be/egbA1RHO8MY?si=...
  video_type: youtube
  Not an iframe code, using URL directly
  YouTube detected - Video ID: egbA1RHO8MY
  Final video detection - Type: youtube, Embed URL: https://www.youtube.com/embed/...
  Creating iframe for youtube with URL: https://www.youtube.com/embed/...
  ```

### 📝 Code Quality
- Updated version to 1.6.3 across all plugin files
- Enhanced comments in regex patterns explaining query parameter support
- Added null-safety check for video_url before indexOf operation

---

## Version 1.6.1 (2025-11-03)

### 🐛 Bug Fixes
- **Fixed Modal Preview:** Exposed `AnMiVideoBanner` class to global scope (`window.AnMiVideoBanner`) to enable admin modal preview initialization
- **Fixed JavaScript Error:** Dequeued `webfontloader` script on admin pages to prevent "d[b].on is not a function" error
- **Improved Iframe Embed Field:** Added debug console logs, better styling (dashed border, monospace font), and clearer Vietnamese instructions

### 🎨 UI Improvements
- Enhanced textarea styling for iframe embed code with distinctive visual design
- Added yellow instruction box with step-by-step guide for YouTube/Vimeo embed codes
- Improved field visibility toggle with console debugging

### 📝 Code Quality
- Updated all version numbers to 1.6.1 across plugin files
- Added comprehensive changelogs in file headers
- Improved asset cache busting with updated version numbers

---

## Version 1.6.0 (2025-11-03)

### ✨ New Features
- **Iframe Embed Support:** Added dedicated textarea field for pasting YouTube/Vimeo iframe embed codes
- **Automatic URL Extraction:** Plugin automatically extracts `src` URL from iframe code
- **Live Preview for Embeds:** Real-time preview updates when pasting iframe codes

### 🔧 Technical Changes
- Added `video-embed-row` field with show/hide logic based on video type selection
- Implemented iframe `src` attribute extraction via regex
- Enhanced form validation for embed codes

---

## Version 1.5.1 (Previous)

### ✨ Features
- Player mode with visible YouTube controls
- Improved video UX with clickable iframe controls

---

## Version 1.5.0 (Previous)

### ✨ Features
- Live inline preview in Edit page
- Modal preview in List page
- Real-time content updates

---

## Version 1.4.0 (Previous)

### ✨ Features
- YouTube/Vimeo auto-detection
- Iframe embed rendering
- Background video mode

---

## Version 1.3.2 (Previous)

### 🐛 Bug Fixes
- Video preload optimization (metadata + 3s timeout)
- Added poster image for better loading experience

---

## Version 1.3.1 (Previous)

### 🌐 Localization
- Vietnamese UI translation
- Image upload bugfix

---

## Version 1.3.0 (Previous)

### ✨ Features
- Content visibility controls (show/hide title, subtitle, button)
- Enhanced admin interface

---

## Upgrade Notes

### From 1.6.0 to 1.6.1
- No database changes required
- Clear browser cache to load updated JavaScript
- Modal preview should now work without console errors

### From 1.5.x to 1.6.x
- New database field: `video_type` (automatically added)
- Existing banners will work without modification
- Recommended: Review and update to use new iframe embed feature if needed

---

## Support

For issues or questions:
- Website: https://anmitools.com
- Plugin URI: https://anmitools.com/plugins/anmi-video-banner
