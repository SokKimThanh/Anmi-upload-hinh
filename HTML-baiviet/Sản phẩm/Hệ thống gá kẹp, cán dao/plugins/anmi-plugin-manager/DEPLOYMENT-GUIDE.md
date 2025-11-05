# Anmi Plugin Manager - Deployment & Testing Guide

## 📋 Tổng Quan Hệ Thống

Hệ thống gồm 2 components chính:
1. **Mu-Plugin Watchdog** (`plugins/anmi-watchdog/anmi-watchdog.php` → deploy vào `wp-content/mu-plugins/anmi-watchdog.php`) - Tự động chạy, bảo vệ fatal errors
2. **Plugin Manager** (`plugins/anmi-plugin-manager/` → deploy vào `wp-content/plugins/anmi-plugin-manager/`) - Quản trị plugins với safe operations

---

## 🚀 Deployment Steps

### Bước 1: Deploy Watchdog (MU-Plugin)

```bash
# 1. Copy watchdog vào mu-plugins folder
cp plugins/anmi-watchdog/anmi-watchdog.php [YOUR_SITE]/wp-content/mu-plugins/

# 2. Tạo quarantine directory (optional, watchdog sẽ tự tạo)
mkdir -p wp-content/anmi-quarantine
chmod 755 wp-content/anmi-quarantine

# 3. Verify watchdog active
# Truy cập: wp-admin/admin.php?anmi_watchdog_action=view_logs&_wpnonce=[NONCE]
# Hoặc check mu-plugins list trong wp-admin
```

**Test Watchdog:**
```bash
# Test 1: View logs
wp-admin/admin.php?anmi_watchdog_action=view_logs&_wpnonce=[generate from URL]

# Test 2: Check status
# Should show: 🟢 ACTIVE (killswitch OFF)
```

---

### Bước 2: Deploy Plugin Manager

```bash
# 1. Copy plugin folder
cp -r plugins/anmi-plugin-manager [YOUR_SITE]/wp-content/plugins/

# 2. Activate plugin trong WordPress Admin
# WP Admin → Plugins → Activate "Anmi Plugin Manager"

# 3. Verify directories created:
ls -la wp-content/anmi-staging-plugins
ls -la wp-content/anmi-backups
ls -la wp-content/uploads/anmi-temp
```

**Test Plugin Manager:**
1. Menu "Anmi Plugins" xuất hiện trong admin sidebar
2. Truy cập → list plugins hiển thị
3. Stats boxes hiển thị (Total, Active, Managed)

---

## 🧪 Test Cases

### Test Case 1: Upload Clean Plugin (Success Flow)

**Chuẩn bị:**
```bash
# Tạo test plugin zip
cd plugins/test-plugins/test-clean-plugin
zip -r test-clean-plugin.zip test-clean-plugin.php
```

**Steps:**
1. WP Admin → Anmi Plugins → Upload Plugin
2. Select `test-clean-plugin.zip`
3. KHÔNG check "auto activate"
4. Click "Upload & Stage Plugin"

**Expected:**
- ✅ Upload success message
- ✅ Plugin xuất hiện trong list với badge "✓ Managed"
- ✅ Status: Inactive
- ✅ Log ghi: `upload_success`

**Verify:**
```bash
# Check staging cleaned
ls wp-content/anmi-staging-plugins  # Should be empty or only temp folders

# Check plugin installed
ls wp-content/plugins/test-clean-plugin

# Check metadata
# WP Admin → Anmi Plugins → click plugin name → verify metadata exists
```

---

### Test Case 2: Safe Activate Clean Plugin

**Steps:**
1. Từ plugin list, click "Activate" button
2. Wait 1-2 seconds

**Expected:**
- ✅ Message: "Plugin đã được kích hoạt an toàn (safe-activated)"
- ✅ Badge changes to "Active"
- ✅ Plugin hoạt động bình thường
- ✅ Log ghi: `safe_activate_start`, `activate_success`
- ✅ Watchdog pending cleared (không còn pending)

**Verify Watchdog Logs:**
```
WP Admin → Anmi Plugins → Watchdog Status → Pending should be empty
```

---

### Test Case 3: Upload Dangerous Plugin (Security Scan)

**Chuẩn bị:**
```bash
cd plugins/test-plugins/test-dangerous-code
zip -r test-dangerous-code.zip test-dangerous-code.php
```

**Steps:**
1. Upload `test-dangerous-code.zip`

**Expected:**
- ❌ Error: "Security scan failed"
- ❌ Threats listed: eval, base64_decode, exec, system
- ✅ Log ghi: `upload_rejected` với threats details
- ✅ Plugin KHÔNG được install

---

### Test Case 4: Fatal Error Recovery (Watchdog Integration)

**⚠️ WARNING: Test này sẽ trigger fatal error. Backup site trước!**

**Chuẩn bị:**
```bash
cd plugins/test-plugins/test-fatal-error
zip -r test-fatal-error.zip test-fatal-error.php
```

**Steps:**
1. Upload `test-fatal-error.zip` (success vì chưa activate)
2. Verify plugin trong list, status Inactive
3. Click "Activate" button
4. **Site sẽ crash trong vài giây**

**Expected Watchdog Behavior:**
- ⚡ Fatal error detected
- 🛡️ Watchdog deactivates plugin
- 🔄 Quarantine plugin vào `wp-content/anmi-quarantine/`
- 🔴 Killswitch enabled (watchdog disabled để ngăn loop)
- 📋 Log ghi: `recovery_success` với actions taken

**Verify:**
```bash
# 1. Check quarantine
ls wp-content/anmi-quarantine/
# Should contain: test-fatal-error_TIMESTAMP

# 2. Check watchdog status
# WP Admin → Watchdog Status
# Should show: 🔴 DISABLED (killswitch ON)

# 3. Check watchdog logs
# Should show recovery_success entry với plugin_file, error details

# 4. Re-enable watchdog
# Click "Enable Watchdog" button
```

---

### Test Case 5: Backup & Restore

**Steps:**
1. Upload plugin version 1.0
2. Note backup created: `wp-content/anmi-backups/plugin-name_TIMESTAMP.zip`
3. Upload same plugin version 2.0 (overwrite)
4. Check new backup created
5. If needed, restore manually:

```bash
cd wp-content/anmi-backups
unzip plugin-name_TIMESTAMP.zip -d ../plugins/
```

---

### Test Case 6: Rename Detection

**Steps:**
1. Install & mark plugin as managed
2. Manually rename plugin folder:
```bash
cd wp-content/plugins
mv test-clean-plugin test-clean-plugin-renamed
```
3. WP Admin → Anmi Plugins → Click "Resync Renamed Plugins"

**Expected:**
- ✅ Message: "Resync thành công: 1 plugins resynced"
- ✅ Plugin metadata updated với new path
- ✅ Log ghi: `plugin_renamed_detected`

---

### Test Case 7: Safe Delete

**Steps:**
1. Deactivate plugin first
2. Click "Delete" button
3. Confirm deletion

**Expected:**
- ✅ Final backup created với suffix `_delete_TIMESTAMP.zip`
- ✅ Plugin files removed
- ✅ Metadata removed
- ✅ Log ghi: `delete_backup_created`, `delete_success`

---

## 📊 Monitoring & Logs

### Xem Watchdog Logs
```
WP Admin → Anmi Plugins → Watchdog Status
```

### Xem Plugin Manager Logs
```
WP Admin → Anmi Plugins → History Logs
```

**Filter logs by action:**
- upload_success
- activate_success
- activate_failed
- upload_rejected
- recovery_success
- plugin_renamed_detected

---

## 🔧 Troubleshooting

### Issue: Watchdog không hoạt động

**Check:**
```bash
# 1. Verify file exists
ls wp-content/mu-plugins/anmi-watchdog.php

# 2. Check PHP errors
tail -f wp-content/debug.log

# 3. Verify killswitch OFF
# WP Admin → Watchdog Status → should be 🟢 ACTIVE
```

### Issue: Upload fails với "staging failed"

**Check:**
```bash
# 1. Verify directories writable
chmod 755 wp-content/anmi-staging-plugins
chmod 755 wp-content/uploads/anmi-temp

# 2. Check disk space
df -h

# 3. Check PHP ZipArchive
php -m | grep zip  # Should show "zip"
```

### Issue: Health check fails ngay cả với clean plugin

**Check:**
```bash
# 1. Test admin-ajax endpoint
curl -X POST https://yoursite.com/wp-admin/admin-ajax.php?action=anmi_pm_health_check

# 2. Check firewall/security plugin blocking requests
# Whitelist admin-ajax.php

# 3. Increase timeout (in class-plugin-activator.php)
const HEALTH_CHECK_TIMEOUT = 20; // Increase to 20s
```

---

## 🔐 Security Notes

1. **Permissions:** Chỉ `manage_options` capability (admin)
2. **Nonce:** Tất cả actions yêu cầu valid nonce
3. **No Credentials:** Không lưu passwords/API keys
4. **No Exec:** Không dùng exec/proc_open/shell commands
5. **Dangerous Patterns:** 14 patterns được scan tự động
6. **Backups:** Auto-created trước mọi destructive operations

---

## 📝 Maintenance

### Clean Old Backups (Manual)

```bash
# Delete backups older than 30 days
find wp-content/anmi-backups -name "*.zip" -mtime +30 -delete
```

### Clean Old Logs (Auto)

- Logs tự động giới hạn 500 entries
- Watchdog logs cap ở 200 entries
- Oldest logs auto-deleted khi exceed limit

---

## 🎯 Production Checklist

Before deploying to production:

- [ ] Backup toàn bộ site
- [ ] Test trên staging environment trước
- [ ] Deploy watchdog trước, test riêng
- [ ] Deploy plugin manager sau
- [ ] Run all test cases
- [ ] Verify watchdog status 🟢 ACTIVE
- [ ] Test upload 1 clean plugin
- [ ] Test safe-activate
- [ ] Document cho team
- [ ] Monitor logs trong 24h đầu

---

## 📞 Support

Nếu gặp issues:
1. Check logs (Watchdog + Plugin Manager)
2. Enable WP_DEBUG trong wp-config.php
3. Check PHP error logs
4. Verify file permissions
5. Test với clean WordPress install

---

**Version:** 1.0.0  
**Last Updated:** November 5, 2025  
**Author:** Anmi Development Team
