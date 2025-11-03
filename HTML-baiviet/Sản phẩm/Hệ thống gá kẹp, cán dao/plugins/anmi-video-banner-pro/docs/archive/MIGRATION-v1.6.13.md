# Migration Guide: v1.6.13 - Video Settings Feature

## 🎯 Overview

Version 1.6.13 adds 6 new database columns for video playback settings. This migration is **AUTOMATIC** when you activate/reload the plugin, but this guide explains the changes.

---

## 📋 Database Changes

### New Columns Added

```sql
ALTER TABLE `wp_anmi_video_banners` 
ADD COLUMN `video_autoplay` tinyint(1) DEFAULT 1,
ADD COLUMN `video_muted` tinyint(1) DEFAULT 1,
ADD COLUMN `video_loop` tinyint(1) DEFAULT 1,
ADD COLUMN `video_controls` tinyint(1) DEFAULT 1,
ADD COLUMN `video_modestbranding` tinyint(1) DEFAULT 1,
ADD COLUMN `video_rel` tinyint(1) DEFAULT 0;
```

### Default Values

| Column | Default | Description |
|--------|---------|-------------|
| `video_autoplay` | `1` (ON) | Video tự động phát khi trang tải |
| `video_muted` | `1` (ON) | Video bắt đầu ở chế độ tắt tiếng |
| `video_loop` | `1` (ON) | Video tự động phát lại khi kết thúc |
| `video_controls` | `1` (ON) | Hiển thị thanh điều khiển (play/pause/volume) |
| `video_modestbranding` | `1` (ON) | Ẩn logo YouTube trong player |
| `video_rel` | `0` (OFF) | Hiện video gợi ý khi kết thúc (YouTube) |

---

## 🔄 Automatic Migration

The plugin uses WordPress `dbDelta()` which:
- ✅ Adds missing columns automatically
- ✅ Keeps existing data safe
- ✅ Sets default values for new columns
- ✅ No manual SQL required

**Action Required:** None! Just reload the plugin or visit the admin page.

---

## 🆕 What's New

### 1. Admin Settings UI

**Location:** Edit Banner Page → "🎛️ Cài Đặt Phát Video (YouTube/Vimeo)" section

**Features:**
- 6 toggle switches with ON/OFF visual feedback
- Real-time preview updates
- Warning notes about browser autoplay policies
- Recommendations for best practices

### 2. Dynamic URL Generation

**Before (v1.6.12):**
```
YouTube: ?autoplay=1&mute=1&loop=1&controls=1 (hardcoded)
Vimeo: ?autoplay=1&muted=1&loop=1&controls=1 (hardcoded)
```

**After (v1.6.13):**
```php
YouTube: ?autoplay={$autoplay}&mute={$muted}&loop={$loop}&controls={$controls}...
Vimeo: ?autoplay={$autoplay}&muted={$muted}&loop={$loop}&controls={$controls}
```

### 3. Frontend Application

Settings are applied in 3 places:
1. **Production Shortcode** (`anmi-video-banner.php`)
2. **Admin Preview** (`admin-edit.php`)
3. **Elementor Widget** (if used)

---

## 🔧 Testing After Migration

### Step 1: Verify Database
```sql
SHOW COLUMNS FROM wp_anmi_video_banners LIKE 'video_%';
```

Expected output: 6 columns starting with `video_`

### Step 2: Check Existing Banners

1. Go to **Video Banners → All Banners**
2. Edit any existing banner
3. Scroll to **"🎛️ Cài Đặt Phát Video"** section
4. All toggles should be **ON** by default (except "Gợi Ý Video")

### Step 3: Test YouTube Video

1. Edit a banner with YouTube URL
2. Try different toggle combinations:
   - **Muted OFF** + **Autoplay ON** → May be blocked by browser
   - **Controls ON** → Volume button visible
   - **Controls OFF** → No volume button
3. Save and preview

### Step 4: Check Frontend

1. View page with `[anmi_video_banner id="X"]`
2. Inspect iframe src:
```html
<!-- Should show dynamic parameters -->
<iframe src="https://www.youtube.com/embed/VIDEO_ID?autoplay=1&mute=1&loop=1&controls=1...">
```

---

## ⚠️ Known Issues & Solutions

### Issue 1: Browser Blocks Autoplay with Sound

**Symptom:** Video doesn't autoplay when `muted=0` + `autoplay=1`

**Solution:**
- Keep `muted=1` (default)
- Enable `controls=1` so users can manually unmute

**Why:** Modern browsers block autoplay with sound to prevent spam.

### Issue 2: Existing Banners Show Muted

**Symptom:** Old banners created before v1.6.13 are muted

**Expected:** This is correct! Default is `video_muted=1` for browser compatibility.

**Action:** Edit banner and toggle "Tắt Tiếng" OFF if you want sound by default.

### Issue 3: Migration Doesn't Run

**Symptom:** New columns not added

**Solution:**
```php
// Manually trigger in wp-admin/plugins.php
// Deactivate and Reactivate the plugin
```

Or run SQL manually:
```sql
ALTER TABLE `wp_anmi_video_banners` 
ADD COLUMN IF NOT EXISTS `video_autoplay` tinyint(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS `video_muted` tinyint(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS `video_loop` tinyint(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS `video_controls` tinyint(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS `video_modestbranding` tinyint(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS `video_rel` tinyint(1) DEFAULT 0;
```

---

## 📊 Impact Assessment

### Backward Compatibility: ✅ SAFE

- Old shortcodes work without changes
- Existing banners get default settings (muted + controls)
- No breaking changes

### Performance: ✅ NO IMPACT

- Database: +6 columns (minimal storage)
- Frontend: URL generation is lightweight
- No additional HTTP requests

### User Experience: ✅ IMPROVED

- **Before:** Users had no control over video behavior
- **After:** Full control via visual toggle switches
- **Benefit:** Can enable sound when desired

---

## 🎓 Best Practices

### Recommended Settings

**For Background Videos (most common):**
```
✅ Auto Play: ON
✅ Muted: ON
✅ Loop: ON
✅ Show Controls: ON (allows unmute)
✅ Hide YouTube Logo: ON
❌ Show Related Videos: OFF
```

**For Feature Videos (with sound):**
```
❌ Auto Play: OFF
❌ Muted: OFF
✅ Loop: OFF
✅ Show Controls: ON
✅ Hide YouTube Logo: ON
❌ Show Related Videos: OFF
```

**For Pure Background (no interaction):**
```
✅ Auto Play: ON
✅ Muted: ON
✅ Loop: ON
❌ Show Controls: OFF
✅ Hide YouTube Logo: ON
❌ Show Related Videos: OFF
```

---

## 📝 Changelog Summary

### Added
- 6 video settings toggles in admin UI
- Database columns for settings persistence
- Dynamic URL generation based on settings
- Real-time preview updates

### Changed
- `parse_video_url()` now accepts `$banner` parameter
- YouTube/Vimeo URLs now use user settings instead of hardcoded values
- Admin form saves 6 additional fields

### Fixed
- `mute=1` always present in YouTube iframe (now configurable)
- No volume control for iframe videos (now has native controls option)
- Users couldn't enable sound (now have full control)

---

## 🆘 Support

If you encounter issues:

1. **Check Console:**
   ```
   F12 → Console → Look for errors
   ```

2. **Verify Settings:**
   ```
   Admin → Edit Banner → Check toggle states
   ```

3. **Test URL:**
   ```
   Inspect iframe src="..." → Check parameters
   ```

4. **Database Check:**
   ```sql
   SELECT video_autoplay, video_muted, video_controls 
   FROM wp_anmi_video_banners 
   WHERE id = YOUR_BANNER_ID;
   ```

---

## ✅ Migration Complete!

Your plugin is now updated to v1.6.13 with full video playback control! 🎉
