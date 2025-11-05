# 🚀 Checklist Vận Hành - Anmi Plugin Manager

## ✅ Pre-Deployment Checklist

### 1. Environment Check
- [ ] WordPress 6.0+ installed
- [ ] PHP 7.4+ running
- [ ] PHP extension `zip` available (`php -m | grep zip`)
- [ ] PHP extension `json` available
- [ ] `wp-content/` folder writable
- [ ] Memory limit ≥ 512MB
- [ ] Upload max filesize ≥ 256MB
- [ ] Full site backup completed

### 2. Staging Test (REQUIRED)
- [ ] Deploy to staging environment first
- [ ] Run all 7 test cases (DEPLOYMENT-GUIDE.md)
- [ ] Verify watchdog recovery works
- [ ] Test security scan rejection
- [ ] Monitor logs for 24 hours
- [ ] Document any issues found

---

## 📦 Deployment Steps

### Step 1: Deploy Watchdog (5 mins)

```bash
# 1. Copy watchdog file
cp plugins/anmi-watchdog/anmi-watchdog.php /path/to/site/wp-content/mu-plugins/

# 2. Verify file exists
ls -la /path/to/site/wp-content/mu-plugins/anmi-watchdog.php

# 3. Verify loaded
# WP Admin → Plugins → Must-Use → "Anmi Watchdog" visible
```

**Verification:**
- [ ] Watchdog visible trong mu-plugins list
- [ ] No PHP errors trong debug.log
- [ ] Directories created: `wp-content/anmi-quarantine/`

**Test Watchdog:**
```bash
# Generate nonce và test view logs
# URL: wp-admin/admin.php?anmi_watchdog_action=view_logs&_wpnonce=[NONCE]
```

- [ ] Logs page loads
- [ ] Status shows: 🟢 ACTIVE
- [ ] No pending activations
- [ ] Killswitch OFF

---

### Step 2: Deploy Plugin Manager (10 mins)

```bash
# 1. Copy plugin folder
cp -r plugins/anmi-plugin-manager /path/to/site/wp-content/plugins/

# 2. Set permissions
chmod -R 755 /path/to/site/wp-content/plugins/anmi-plugin-manager

# 3. Activate via WP Admin
# Plugins → Activate "Anmi Plugin Manager"
```

**Verification:**
- [ ] Plugin activated successfully
- [ ] Menu "Anmi Plugins" visible trong sidebar
- [ ] No PHP errors
- [ ] Directories auto-created:
  - [ ] `wp-content/anmi-staging-plugins/`
  - [ ] `wp-content/anmi-backups/`
  - [ ] `wp-content/uploads/anmi-temp/`
- [ ] `.htaccess` files created trong directories (deny access)

**Test Plugin Manager:**
- [ ] Anmi Plugins → Plugin List loads
- [ ] Stats boxes hiển thị (Total: 0, Active: 0, Managed: 0)
- [ ] Upload Plugin page loads
- [ ] History Logs page loads
- [ ] Watchdog Status redirects correctly

---

### Step 3: Initial Test (15 mins)

#### Test 1: Upload Clean Plugin

```bash
# Create test plugin
cd plugins/test-plugins/test-clean-plugin
zip -r test-clean-plugin.zip test-clean-plugin.php
```

**Upload:**
- [ ] Navigate: Anmi Plugins → Upload Plugin
- [ ] Select `test-clean-plugin.zip`
- [ ] Do NOT check "auto activate"
- [ ] Click "Upload & Stage Plugin"

**Expected:**
- [ ] Success message: "Plugin uploaded successfully"
- [ ] Plugin visible trong list
- [ ] Badge: "✓ Managed"
- [ ] Status: Inactive
- [ ] Log entry: `upload_success`

#### Test 2: Safe Activate

- [ ] Click "Activate" button cho test-clean-plugin
- [ ] Wait 2-3 seconds
- [ ] Success message: "Plugin đã được kích hoạt an toàn"
- [ ] Badge changes to "Active"
- [ ] Admin notice: "Test Clean Plugin is active!"
- [ ] Log entries: `safe_activate_start`, `activate_success`
- [ ] Watchdog pending cleared

#### Test 3: Safe Deactivate & Delete

- [ ] Click "Deactivate" button
- [ ] Success message
- [ ] Status: Inactive
- [ ] Click "Delete" button
- [ ] Confirm deletion
- [ ] Backup created in `wp-content/anmi-backups/`
- [ ] Plugin removed from list
- [ ] Log: `delete_backup_created`, `delete_success`

---

## 🧪 Production Testing (30 mins)

### Test 4: Security Scan (Dangerous Code)

```bash
cd plugins/test-plugins/test-dangerous-code
zip -r test-dangerous-code.zip test-dangerous-code.php
```

- [ ] Upload `test-dangerous-code.zip`
- [ ] Expected: Error "Security scan failed"
- [ ] Threats listed: eval, base64_decode, exec, system
- [ ] Plugin NOT installed
- [ ] Log: `upload_rejected`

### Test 5: Watchdog Recovery (CAREFUL!)

⚠️ **WARNING:** This will crash site temporarily!

```bash
cd plugins/test-plugins/test-fatal-error
zip -r test-fatal-error.zip test-fatal-error.php
```

**Before test:**
- [ ] Full backup completed
- [ ] Team notified
- [ ] Low traffic time

**Test:**
- [ ] Upload `test-fatal-error.zip` (success)
- [ ] Plugin visible, inactive
- [ ] Click "Activate"
- [ ] **Site crashes for 1-2 seconds**

**Expected Recovery:**
- [ ] Watchdog detects fatal error
- [ ] Plugin auto-deactivated
- [ ] Plugin moved to `wp-content/anmi-quarantine/test-fatal-error_TIMESTAMP/`
- [ ] Killswitch enabled (🔴 DISABLED)
- [ ] Log: `recovery_success`
- [ ] Site accessible again

**After Recovery:**
- [ ] Navigate: Watchdog Status
- [ ] Verify recovery log entry
- [ ] Click "Enable Watchdog" button
- [ ] Status returns to 🟢 ACTIVE
- [ ] Delete quarantined plugin manually if needed

### Test 6: Mark Existing Plugin

- [ ] Find any existing plugin with "Anmi" trong author
- [ ] Verify auto-visible trong list
- [ ] Find plugin WITHOUT "Anmi" trong author
- [ ] Click "Mark" button
- [ ] Badge shows "✓ Managed"
- [ ] Plugin now tracked with metadata

### Test 7: Rename Detection

```bash
# Manually rename plugin
cd wp-content/plugins
mv test-clean-plugin test-clean-plugin-renamed
```

- [ ] Anmi Plugins → List shows plugin missing
- [ ] Click "Resync Renamed Plugins"
- [ ] Success: "1 plugins resynced"
- [ ] Metadata updated với new path
- [ ] Log: `plugin_renamed_detected`

---

## 🔍 Post-Deployment Monitoring (24 hours)

### Hour 1-4: Active Monitoring
- [ ] Check logs every 30 mins
- [ ] Monitor PHP error logs
- [ ] Test 2-3 real plugin uploads
- [ ] Verify no performance degradation

### Day 1: Regular Monitoring
- [ ] Check logs morning/afternoon/evening
- [ ] Review any error entries
- [ ] Check backup sizes reasonable
- [ ] Monitor disk space usage

### Week 1: Periodic Checks
- [ ] Daily log review
- [ ] Weekly backup cleanup
- [ ] Document any issues
- [ ] Tune configurations if needed

---

## 🛠️ Configuration Tuning (Optional)

### If health check timeout too short:

**File:** `wp-content/plugins/anmi-plugin-manager/inc/class-plugin-activator.php`

```php
// Change line ~8
const HEALTH_CHECK_TIMEOUT = 10; // Increase to 15 or 20
```

### If logs growing too fast:

**File:** `wp-content/plugins/anmi-plugin-manager/inc/class-logger.php`

```php
// Change line ~8
const MAX_LOGS = 500; // Reduce to 200 or increase to 1000
```

**File:** `wp-content/mu-plugins/anmi-watchdog.php`

```php
// Change line ~11
const MAX_LOG_ENTRIES = 200; // Adjust as needed
```

---

## 🚨 Troubleshooting Common Issues

### Issue: "Upload failed: staging failed"

**Solution:**
```bash
chmod -R 755 wp-content/anmi-staging-plugins
chmod -R 755 wp-content/uploads/anmi-temp
```

### Issue: Health check always fails

**Solution:**
1. Test admin-ajax manually:
```bash
curl -X POST https://yoursite.com/wp-admin/admin-ajax.php?action=anmi_pm_health_check
```
2. Check firewall/security plugin blocking
3. Whitelist admin-ajax.php
4. Increase timeout (see Configuration Tuning)

### Issue: Watchdog not triggering

**Solution:**
1. Verify killswitch OFF
2. Check mu-plugin loaded: WP Admin → Plugins → Must-Use
3. Verify pending data set: check `wp_options` table for `anmi_pm_pending_activation`
4. Test fatal error dentro 60s window

### Issue: Permissions errors

**Solution:**
```bash
# Fix all permissions
chown -R www-data:www-data wp-content/
chmod -R 755 wp-content/
chmod -R 755 wp-content/plugins/
chmod -R 755 wp-content/mu-plugins/
```

---

## 📊 Success Metrics

After 1 week of production use, verify:

- [ ] Zero unhandled fatal errors
- [ ] All uploads processed correctly
- [ ] Security scan blocked dangerous code
- [ ] Backups created before destructive operations
- [ ] Logs complete and accurate
- [ ] No performance degradation
- [ ] Team comfortable using system
- [ ] Documentation clear and sufficient

---

## 🎯 Go/No-Go Decision

**GO to Production if:**
- ✅ All pre-deployment checks passed
- ✅ Staging tests 100% successful
- ✅ Watchdog recovery tested and works
- ✅ Team trained on usage
- ✅ Backup strategy in place
- ✅ Rollback plan documented

**NO-GO if:**
- ❌ Any staging test failed
- ❌ Watchdog not working
- ❌ Permission issues unresolved
- ❌ Team not trained
- ❌ No backup available

---

## 🔄 Rollback Plan

### If issues detected in production:

**Immediate (< 5 mins):**
```bash
# 1. Deactivate Plugin Manager
wp plugin deactivate anmi-plugin-manager

# 2. Remove mu-plugin
rm wp-content/mu-plugins/anmi-watchdog.php

# 3. Restore from backup if needed
# Use your backup solution
```

**Complete Rollback (< 15 mins):**
```bash
# 1. Remove all files
rm -rf wp-content/plugins/anmi-plugin-manager
rm wp-content/mu-plugins/anmi-watchdog.php

# 2. Clean up directories
rm -rf wp-content/anmi-*
rm -rf wp-content/uploads/anmi-temp

# 3. Clean database (optional)
# Delete CPT posts: anmi_plugin, anmi_plugin_log
# Delete options: anmi_pm_*

# 4. Restore site from backup
```

---

## 📝 Final Sign-Off

**Deployment Date:** _______________  
**Deployed By:** _______________  
**Verified By:** _______________  

**All checks passed:**
- [ ] Watchdog deployed and tested
- [ ] Plugin Manager deployed and tested
- [ ] All 7 test cases passed
- [ ] Documentation reviewed
- [ ] Team notified
- [ ] Monitoring active

**Production approval:** ☐ APPROVED ☐ HOLD

**Notes:**
```
_________________________________________
_________________________________________
_________________________________________
```

---

**Version:** 1.0.0  
**Last Updated:** November 5, 2025  
**Document Owner:** Anmi Development Team
