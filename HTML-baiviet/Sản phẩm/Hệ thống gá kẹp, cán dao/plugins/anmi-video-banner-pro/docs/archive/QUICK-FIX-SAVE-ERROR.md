# 🚨 Quick Fix: "Failed to save banner" Error

## Problem

You're seeing this error when trying to save a banner:
```
Error: Failed to save banner
```

## Root Cause

The database doesn't have the 6 new columns added in v1.6.13. This happens when:
- Plugin was updated but database migration didn't run automatically
- `dbDelta()` failed silently
- Database user lacks ALTER TABLE permissions

---

## ✅ Solution (Choose One)

### Option 1: Run Manual Migration Script (RECOMMENDED)

**Steps:**

1. **Upload** the plugin to your WordPress site

2. **Access migration script** in your browser:
   ```
   http://your-site.com/wp-content/plugins/anmi-video-banner/migrate-v1.6.13.php
   ```

3. **Follow on-screen instructions:**
   - Script will check existing columns
   - Add missing columns automatically
   - Show detailed results

4. **DELETE the migration file** after success:
   ```
   wp-content/plugins/anmi-video-banner/migrate-v1.6.13.php
   ```

5. **Try saving your banner again** - should work now! ✅

---

### Option 2: Run SQL Manually (phpMyAdmin)

**Steps:**

1. Go to **phpMyAdmin** → Select your WordPress database

2. Run this SQL query:
   ```sql
   ALTER TABLE `wp_anmi_video_banners` 
   ADD COLUMN IF NOT EXISTS `video_autoplay` tinyint(1) DEFAULT 1 AFTER `status`,
   ADD COLUMN IF NOT EXISTS `video_muted` tinyint(1) DEFAULT 1 AFTER `video_autoplay`,
   ADD COLUMN IF NOT EXISTS `video_loop` tinyint(1) DEFAULT 1 AFTER `video_muted`,
   ADD COLUMN IF NOT EXISTS `video_controls` tinyint(1) DEFAULT 1 AFTER `video_loop`,
   ADD COLUMN IF NOT EXISTS `video_modestbranding` tinyint(1) DEFAULT 1 AFTER `video_controls`,
   ADD COLUMN IF NOT EXISTS `video_rel` tinyint(1) DEFAULT 0 AFTER `video_modestbranding`;
   ```

3. Verify columns were added:
   ```sql
   SHOW COLUMNS FROM `wp_anmi_video_banners` LIKE 'video_%';
   ```
   
   Should return 6 rows.

4. Try saving your banner again ✅

---

### Option 3: Deactivate & Reactivate Plugin

**Steps:**

1. Go to **Plugins** in WordPress admin

2. **Deactivate** "An Mi Video Banner"

3. **Activate** it again

4. This triggers `create_table()` which runs `dbDelta()`

5. Try saving your banner ✅

**Note:** This may not work if database user lacks ALTER permissions.

---

## 🔍 Verify Database Structure

After migration, check if columns exist:

**SQL Query:**
```sql
DESCRIBE `wp_anmi_video_banners`;
```

**Expected Columns (should include):**
- `video_autoplay` - tinyint(1) - Default: 1
- `video_muted` - tinyint(1) - Default: 1
- `video_loop` - tinyint(1) - Default: 1
- `video_controls` - tinyint(1) - Default: 1
- `video_modestbranding` - tinyint(1) - Default: 1
- `video_rel` - tinyint(1) - Default: 0

---

## 🐛 Debug Information

If you still see errors, check WordPress debug log:

**Enable Debug Logging:**

Edit `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

**Check Log File:**
```
wp-content/debug.log
```

Look for lines starting with:
```
=== ANMI SAVE BANNER DEBUG ===
```

---

## 📋 Common Error Messages

### Error: "Database update failed: Unknown column 'video_autoplay'"

**Cause:** Columns don't exist in database

**Fix:** Run migration script (Option 1) or SQL query (Option 2)

---

### Error: "Permission denied"

**Cause:** Not logged in as admin

**Fix:** Log in as WordPress administrator

---

### Error: "Invalid images format"

**Cause:** Images JSON is malformed

**Fix:** 
1. Remove all images from banner
2. Re-upload them one by one
3. Try saving

---

### Error: "Failed to save banner - unknown error"

**Cause:** Database query failed but no specific error

**Fix:**
1. Check debug.log for detailed error
2. Verify database connection
3. Check database user permissions (needs ALTER TABLE)

---

## 🆘 Still Not Working?

### Step 1: Check Browser Console

1. Press **F12** in browser
2. Go to **Console** tab
3. Look for red errors
4. Share error message with support

### Step 2: Check Network Tab

1. Press **F12** in browser
2. Go to **Network** tab
3. Filter by **XHR**
4. Click on `admin-ajax.php` request
5. Check **Response** tab for error details

### Step 3: Check WordPress Debug Log

1. Enable debug logging (see above)
2. Try saving banner again
3. Check `wp-content/debug.log`
4. Look for ANMI-related errors

### Step 4: Check Database User Permissions

Run this SQL:
```sql
SHOW GRANTS FOR CURRENT_USER();
```

Should include:
```
GRANT ALTER ON `database_name`.* TO ...
```

If missing, contact hosting provider.

---

## ✅ Success Checklist

After migration, verify:

- [ ] Can access Edit Banner page
- [ ] See "🎛️ Cài Đặt Phát Video" section
- [ ] 6 toggle switches visible
- [ ] Can toggle switches ON/OFF
- [ ] Can save banner without errors
- [ ] Banner displays on frontend
- [ ] Video controls visible (if enabled)

---

## 📞 Need Help?

If none of the above works:

1. **Export debug info:**
   - Browser console errors
   - Network tab response
   - WordPress debug.log excerpts
   - Database DESCRIBE output

2. **Contact Support:**
   - Email: support@anmitools.com
   - Include debug info from step 1

---

## 🎯 TL;DR (Quick Steps)

1. Access: `http://your-site.com/wp-content/plugins/anmi-video-banner/migrate-v1.6.13.php`
2. Follow on-screen instructions
3. Delete migration file
4. Try saving banner again
5. ✅ Done!

---

**Last Updated:** November 3, 2025  
**Version:** 1.6.13  
**Issue:** Database columns missing after update
