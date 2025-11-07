# AnMi Banner Video Pro - Installation Guide

## 🚀 Quick Installation

### Method 1: Via WordPress Admin (Recommended)

1. **Deactivate old plugin (if exists)**
   ```
   WordPress Admin → Plugins → Deactivate "AnMi Banner Video Pro"
   ```

2. **Delete old plugin**
   ```
   Click "Delete" (this won't delete your banners - data is in database)
   ```

3. **Upload new plugin**
   ```
   Plugins → Add New → Upload Plugin
   Select: anmi-banner-video.zip
   Click: Install Now → Activate
   ```

4. **Done!** ✅
   ```
   Database auto-migrates on activation
   Check: An Mi Video Banner menu appears
   ```

---

### Method 2: FTP/File Manager

1. **Backup old plugin (optional)**
   ```
   Rename: /wp-content/plugins/anmi-banner-video/
   To: /wp-content/plugins/anmi-banner-video-backup/
   ```

2. **Upload new plugin**
   ```
   Upload folder: anmi-banner-video/
   To: /wp-content/plugins/
   ```

3. **Activate via WordPress**
   ```
   WordPress Admin → Plugins
   Find: "AnMi Banner Video Pro"
   Click: Activate
   ```

4. **Migration runs automatically!** ✅

---

### Method 3: Copy & Paste (Direct Replace)

1. **Navigate to plugins folder**
   ```
   /wp-content/plugins/
   ```

2. **Replace plugin folder**
   ```
   Delete: anmi-banner-video/ (or rename to backup)
   Paste: New anmi-banner-video/ folder
   ```

3. **Deactivate → Activate**
   ```
   WordPress Admin → Plugins
   Deactivate → Activate "AnMi Banner Video Pro"
   ```

---

## ⚙️ How Migration Works

### Triple Safety System:

**1. On Activation** (Primary)
```php
register_activation_hook()
→ setup_database_table()
→ dbDelta() adds missing columns
→ Sets abvp_version = 2.5.0
```

**2. On Version Change** (Backup)
```php
plugins_loaded hook
→ Check abvp_version option
→ If version < 2.5.0 → migrate
→ Update abvp_version
```

**3. On Save/Update** (Emergency)
```php
Before INSERT/UPDATE banner
→ ensure_columns_exist()
→ Check columns in database
→ If missing → setup_database_table()
```

---

## 🔍 Verify Installation

### Check Migration Success:

1. **Check WordPress Debug Log**
   ```
   /wp-content/debug.log
   Look for: "AnMi Banner Video Pro v2.5.0 activated - Database updated"
   ```

2. **Check Database**
   ```sql
   SHOW COLUMNS FROM wp_anmi_video_banners;
   
   Should see:
   - enable_slider
   - enable_slider_desktop
   - enable_slider_mobile
   ```

3. **Check Admin Panel**
   ```
   Edit any banner
   Look for new checkboxes:
   🖼️ Bật Slider Hình Ảnh
   🖥️ Slider trên Desktop
   📱 Slider trên Mobile
   ```

---

## 🐛 Troubleshooting

### Issue: "Unknown column 'enable_slider'"

**Solution 1: Deactivate → Activate**
```
WordPress Admin → Plugins
Deactivate → Activate plugin
```

**Solution 2: Manual Column Check**
```php
// Check if columns exist
SELECT * FROM wp_anmi_video_banners LIMIT 1;

// If columns missing, deactivate/activate plugin
```

**Solution 3: Edit & Save Banner**
```
Open any banner in admin
Click "Save Banner"
Emergency migration will run
```

---

### Issue: Plugin not appearing

**Check:**
1. PHP version ≥ 7.2
2. WordPress version ≥ 5.0
3. File permissions (755 for folders, 644 for files)
4. No PHP errors in debug.log

---

### Issue: Database table not created

**Solution:**
```sql
-- Check if table exists
SHOW TABLES LIKE 'wp_anmi_video_banners';

-- If not exists, activate plugin again
-- Or run manually (not recommended):
-- Use WordPress Admin → Plugins → Deactivate → Activate
```

---

## 📝 What's New in v2.5.0

### Features:
- ✅ Device-Specific Slider Control
- ✅ Dual Migration System
- ✅ Emergency Column Check
- ✅ WordPress Core Video Integration
- ✅ Elementor-Style Overlay

### Database Changes:
```sql
ALTER TABLE wp_anmi_video_banners
ADD COLUMN enable_slider tinyint(1) DEFAULT 1,
ADD COLUMN enable_slider_desktop tinyint(1) DEFAULT 1,
ADD COLUMN enable_slider_mobile tinyint(1) DEFAULT 1;
```

---

## 📞 Support

**Documentation:** Check plugin folder for detailed docs
**Issues:** Contact An Mi Tools Technical Team
**Website:** https://anmitools.com

---

## ⚡ Quick Start After Installation

1. Go to: **An Mi Video Banner → All Banners**
2. Click: **Edit** on any banner
3. Scroll to: **Video Settings**
4. See new options:
   - 🖼️ Bật Slider Hình Ảnh
   - 🖥️ Slider trên Desktop
   - 📱 Slider trên Mobile
5. Configure & Save!

---

**Version:** 2.5.0  
**Release Date:** November 7, 2025  
**Author:** An Mi Tools Technical Team
