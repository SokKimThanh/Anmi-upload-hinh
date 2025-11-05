# Anmi Plugin Manager - README

## 🎯 Giới Thiệu

**Anmi Plugin Manager** là hệ thống quản lý plugins WordPress với khả năng:
- 🛡️ **Watchdog Protection**: Tự động phát hiện và khôi phục fatal errors
- 📦 **Safe Upload**: Security scan trước khi install
- ✅ **Safe Activate**: Health check và auto rollback
- 💾 **Auto Backup**: Backup trước mọi thay đổi
- 📊 **Complete Logging**: Track mọi hành động
- 🔄 **Rename Detection**: Tự động phát hiện plugins đổi tên

---

## 🏗️ Kiến Trúc

### Component 1: Mu-Plugin Watchdog
**Source:** `plugins/anmi-watchdog/anmi-watchdog.php` → deploy vào `wp-content/mu-plugins/anmi-watchdog.php`

**Chức năng:**
- Tự động chạy (must-use plugin)
- `register_shutdown_function()` bắt fatal errors
- Phát hiện errors trong 60s window sau activation
- Auto deactivate + restore/quarantine
- Killswitch protection ngăn loop

### Component 2: Plugin Manager
**Source:** `plugins/anmi-plugin-manager/` → deploy vào `wp-content/plugins/anmi-plugin-manager/`

**Structure:**
```
anmi-plugin-manager/
├── anmi-plugin-manager.php     # Main file
├── inc/
│   ├── class-metadata-manager.php    # CPT storage
│   ├── class-plugin-list.php         # Admin UI list
│   ├── class-plugin-uploader.php     # Upload + scan
│   ├── class-plugin-activator.php    # Safe activate
│   ├── class-logger.php              # Logging system
│   └── class-rename-detector.php     # Resync renamed
└── assets/
    ├── admin.css                     # Admin styles
    └── admin.js                      # Admin scripts
```

---

## 🔧 Yêu Cầu Hệ Thống

- WordPress 6.0+
- PHP 7.4+
- PHP Extensions: `zip`, `json`
- Permissions: writable `wp-content` folder
- Memory: 512MB+ recommended
- Max Upload: 256MB+ recommended

---

## 📥 Installation

### Quick Install

```bash
# 1. Deploy Watchdog
cp plugins/anmi-watchdog/anmi-watchdog.php /path/to/wp-content/mu-plugins/

# 2. Deploy Plugin Manager
cp -r plugins/anmi-plugin-manager /path/to/wp-content/plugins/

# 3. Activate Plugin Manager
# WP Admin → Plugins → Activate "Anmi Plugin Manager"
```

### Verify Installation

✅ Check mu-plugins: `wp-admin/plugins.php?plugin_status=mustuse`  
✅ Check menu: "Anmi Plugins" trong sidebar  
✅ Check watchdog: Truy cập Watchdog Status page  

---

## 🎮 Usage

### Upload Plugin

1. **Anmi Plugins → Upload Plugin**
2. Select .zip file
3. (Optional) Check "auto activate" - **NOT RECOMMENDED**
4. Click "Upload & Stage Plugin"

**Process:**
- Upload → Temp storage
- Security scan (14 dangerous patterns)
- Extract to staging
- Backup existing (if exists)
- Move to plugins folder
- Save metadata
- Ready for safe activate

### Safe Activate

1. **Anmi Plugins → Plugin List**
2. Find plugin (status: Inactive)
3. Click **"Activate"** button

**Process:**
- Set pending trong watchdog
- Activate plugin
- Health check (10s timeout)
- If success: Clear pending
- If fail: Auto deactivate + restore backup

### Mark as Managed

Plugins có "Anmi" trong Author tự động hiển thị.  
Để track plugins khác:

1. Click **"Mark"** button
2. Plugin được track với metadata
3. Badge: **✓ Managed**

### Resync Renamed Plugins

Nếu manually rename plugin folder:

1. Click **"Resync Renamed Plugins"**
2. System scan by checksum + name
3. Metadata auto-updated

### View Logs

**Plugin Manager Logs:**  
`Anmi Plugins → History Logs`

**Watchdog Logs:**  
`Anmi Plugins → Watchdog Status`

---

## 🔒 Security Features

### Upload Security Scan

Scan 14 dangerous patterns:
- `eval()`
- `base64_decode()`
- `exec()`, `system()`, `shell_exec()`
- `proc_open()`, `popen()`
- `passthru()`
- `create_function()`
- `preg_replace` với `/e` flag
- `gzuncompress()`, `gzinflate()`
- `str_rot13()`
- `assert()`

**Action:** Reject upload nếu phát hiện

### Watchdog Protection

- Detects: `E_ERROR`, `E_PARSE`, `E_CORE_ERROR`, `E_COMPILE_ERROR`
- Window: 60 seconds sau activation
- Actions:
  1. Deactivate plugin
  2. Restore backup hoặc quarantine
  3. Enable killswitch
  4. Log incident

### Permissions

- All actions: `manage_options` capability only
- Nonce verification required
- No exec/shell commands
- No credential storage

---

## 📊 Logging & Monitoring

### Log Events

- `upload_received`
- `upload_rejected` (security scan fail)
- `upload_success`
- `safe_activate_start`
- `activate_success`
- `activate_failed`
- `health_check_failed`
- `rollback_restore`
- `deactivate`
- `delete_backup_created`
- `delete_success`
- `plugin_renamed_detected`
- `recovery_success` (watchdog)

### Storage

- **Plugin Manager:** Custom Post Type `anmi_plugin_log` (cap 500)
- **Watchdog:** Option `anmi_pm_watchdog_logs` (cap 200)
- **Metadata:** Custom Post Type `anmi_plugin`

---

## 🧪 Testing

See **DEPLOYMENT-GUIDE.md** for complete test cases:

1. Upload clean plugin
2. Safe activate
3. Upload dangerous plugin (reject)
4. Fatal error recovery
5. Backup & restore
6. Rename detection
7. Safe delete

---

## 🐛 Troubleshooting

### Plugin upload fails

**Check:**
- File permissions: `wp-content/` writable
- PHP `upload_max_filesize` and `post_max_size`
- ZipArchive extension installed
- Disk space available

### Safe activate fails immediately

**Check:**
- Plugin compatible với PHP version
- No syntax errors
- Dependencies installed
- Check logs cho error details

### Watchdog not triggering

**Check:**
- Killswitch status (should be OFF)
- Pending data set correctly
- Fatal error trong 60s window
- Mu-plugin loaded (check mu-plugins list)

### Health check timeout

**Solutions:**
- Increase `HEALTH_CHECK_TIMEOUT` trong `class-plugin-activator.php`
- Check firewall/security plugin blocking admin-ajax
- Verify site reachable via `wp_remote_post()`

---

## 🔄 Backup & Restore

### Auto Backups

Backups tự động tạo:
- Trước upload (nếu plugin exists)
- Trước delete
- Stored: `wp-content/anmi-backups/`

### Manual Restore

```bash
cd wp-content/anmi-backups
unzip plugin-name_TIMESTAMP.zip -d ../plugins/
```

### Quarantine Folder

Plugins gây fatal error → `wp-content/anmi-quarantine/`

---

## 📈 Performance

- Upload scan: ~1-2s per plugin
- Safe activate: ~2-3s (includes health check)
- Resync: ~1-2s cho 50 plugins
- Logs pagination: 50 entries/page
- Zero exec/shell commands

---

## 🛠️ Configuration

### Watchdog Constants

```php
const WINDOW_SECONDS = 60;      // Pending window
const MAX_LOG_ENTRIES = 200;    // Log cap
```

### Plugin Manager Constants

```php
const HEALTH_CHECK_TIMEOUT = 10;  // Health check timeout
const MAX_LOGS = 500;             // Logger cap
```

### Dangerous Patterns

Edit in `class-plugin-uploader.php`:

```php
const DANGEROUS_PATTERNS = [
    'eval\s*\(',
    // Add more patterns...
];
```

---

## 🤝 Contributing

Development workflow:
1. Test trên local/staging trước
2. Follow WordPress coding standards
3. Add logs cho mọi critical actions
4. Update tests và docs
5. Create pull request

---

## 📜 License

Proprietary - Anmi Development Team

---

## 📞 Support

**Issues:** Check logs first (Plugin Manager + Watchdog)  
**Debug:** Enable `WP_DEBUG` trong `wp-config.php`  
**Email:** support@anmi.com.vn  

---

## 🎯 Roadmap

Future enhancements:
- [ ] Schedule auto-backups
- [ ] Email notifications cho fatal errors
- [ ] Plugin version comparison
- [ ] Bulk operations
- [ ] Export/import metadata
- [ ] Multi-site support
- [ ] API endpoints

---

**Version:** 1.0.0  
**Released:** November 5, 2025  
**Author:** Anmi Development Team  
**Website:** https://anmi.com.vn
