# Changelog - AnMi Banner Video Pro

## Version 2.6.0 (November 7, 2025)

### 🎯 Major Features
- **Device-Specific Controls**: Added independent settings for Desktop and Mobile
  - Slider: Can enable/disable separately for desktop and mobile
  - Overlay: Can show/hide separately for desktop and mobile  
  - Hover Effect: Can enable/disable separately for desktop and mobile

### 🔧 Admin UI Improvements
- **Responsive Device Settings Table**: New 2-column layout (Desktop | Mobile)
- **Consolidated Video Settings**: All video-related settings in one section
- **Real-time Status Badges**: Show enabled/disabled status for each control
- **Better Organization**: Grouped master switches with device-specific settings

### 🐛 Bug Fixes
- **Mobile Video Positioning**: Fixed video being pushed to bottom on mobile devices
  - Changed iframe scaling from 300% to 100% on mobile
  - Removed transform centering on mobile for better positioning
  - Video now fills container correctly on small screens

- **Pagination Dots Hiding**: Fixed dots still showing when slider is disabled
  - Added CSS rules for master slider OFF
  - Added CSS rules for desktop slider OFF
  - Added CSS rules for mobile slider OFF

- **Overlay Behavior**: Fixed overlay not hiding when hover is disabled
  - Overlay now hides immediately when hover is turned off
  - Video shows immediately without needing hover
  - Works correctly on both desktop and mobile

### 📦 Database Changes
- Added 6 new columns to `wp_anmi_video_banners` table:
  - `enable_slider_desktop` (tinyint, default 1)
  - `enable_slider_mobile` (tinyint, default 1)
  - `enable_overlay_desktop` (tinyint, default 1)
  - `enable_overlay_mobile` (tinyint, default 1)
  - `enable_hover_desktop` (tinyint, default 1)
  - `enable_hover_mobile` (tinyint, default 1)

### 🎨 CSS Updates
- **Responsive Media Queries**: Improved desktop (≥769px) and mobile (≤768px) breakpoints
- **Device-Specific Styles**: Separate CSS rules for desktop and mobile controls
- **Mobile Optimization**: Better video sizing and positioning on mobile devices
- **Hover Disabled State**: Proper CSS for when hover effect is turned off

### 💻 JavaScript Enhancements
- **Device Detection**: Added methods to detect current device type
- **Device Logic**: Implemented `applyDeviceSliderLogic()`, `applyDeviceOverlayLogic()`, `applyDeviceHoverLogic()`
- **Data Attributes**: Reading 9 device-specific settings from container

### 📝 Files Modified
- `anmi-banner-video-pro.php` - Main plugin file, version bump
- `assets/css/video-banner.css` - Mobile fixes, device-specific CSS
- `assets/css/admin-device-settings.css` - New responsive table styles
- `assets/js/video-banner.js` - Device detection and logic
- `includes/admin-panel.php` - Database migration, ensure_columns_exist()
- `includes/views/admin-edit.php` - New device settings UI
- `templates/banner-output.php` - Device-specific data attributes

---

## Version 2.5.0 (Previous Release)

### Features
- Basic slider enable/disable functionality
- Overlay and hover controls (global only)
- YouTube/Vimeo/MP4 video support
- Image slider with navigation dots
- Modal preview in admin panel

### Known Issues Fixed in 2.6.0
- Video positioning issues on mobile
- Overlay not hiding when hover disabled
- Pagination dots showing when slider disabled
- No device-specific controls

---

## Upgrade Notes

### From 2.5.0 to 2.6.0
1. **Automatic Database Migration**: Plugin will automatically add 6 new columns on activation
2. **Default Values**: All device-specific settings default to enabled (1) for backward compatibility
3. **No Breaking Changes**: Existing banners will work exactly as before
4. **New Features Available**: Edit any banner to access device-specific controls

### Compatibility
- WordPress: 5.0+
- PHP: 7.2+
- Tested up to: WordPress 6.4
- Browser Support: Chrome, Firefox, Safari, Edge (latest 2 versions)

---

## Support
For issues or questions, contact: An Mi Tools Technical Team
Website: https://anmitools.com
