# 📚 CÁCH WORDPRESS CÀI ĐẶT VÀ NHẬN DIỆN PLUGIN

## 🎯 Quy trình WordPress quét và nhận diện Plugin

### Bước 1: WordPress quét thư mục plugins

Khi bạn vào trang **Plugins** trong WordPress Admin, WordPress sẽ:

```php
// WordPress core code (wp-admin/includes/plugin.php)
function get_plugins($plugin_folder = '') {
    $cache_plugins = wp_cache_get('plugins', 'plugins');
    
    // Quét thư mục wp-content/plugins/
    $plugins_dir = @ opendir(WP_PLUGIN_DIR);
    
    while (($file = readdir($plugins_dir)) !== false) {
        // Bỏ qua . và ..
        if ('.' === $file || '..' === $file)
            continue;
            
        // Kiểm tra nếu là thư mục
        if (is_dir(WP_PLUGIN_DIR . '/' . $file)) {
            $plugins_subdir = @ opendir(WP_PLUGIN_DIR . '/' . $file);
            
            // Tìm file .php trong thư mục
            while (($subfile = readdir($plugins_subdir)) !== false) {
                if (substr($subfile, -4) == '.php')
                    $plugin_files[] = "$file/$subfile";
            }
        }
    }
}
```

### Bước 2: Đọc Plugin Header

WordPress đọc phần comment ở đầu file PHP để lấy thông tin:

```php
/**
 * Plugin Name: AnMi Video Banner Pro          ← Tên hiển thị
 * Plugin URI: https://anmitools.com            ← URL trang plugin
 * Description: Video banner với slider...      ← Mô tả ngắn
 * Version: 2.0.0                               ← Phiên bản
 * Author: An Mi Tools Technical Team           ← Tác giả
 * Author URI: https://anmitools.com            ← URL tác giả
 * License: GPL v2 or later                     ← Giấy phép
 * Text Domain: anmi-video-banner-pro          ← Domain cho i18n
 * Requires at least: 5.0                       ← WP tối thiểu
 * Requires PHP: 7.2                            ← PHP tối thiểu
 * Network: false                               ← Multisite support
 */
```

**WordPress sử dụng hàm `get_file_data()` để parse:**

```php
function get_plugin_data($plugin_file) {
    $default_headers = array(
        'Name'        => 'Plugin Name',
        'PluginURI'   => 'Plugin URI',
        'Version'     => 'Version',
        'Description' => 'Description',
        'Author'      => 'Author',
        'AuthorURI'   => 'Author URI',
        'TextDomain'  => 'Text Domain',
        'DomainPath'  => 'Domain Path',
        'Network'     => 'Network',
        'RequiresWP'  => 'Requires at least',
        'RequiresPHP' => 'Requires PHP',
    );
    
    return get_file_data($plugin_file, $default_headers, 'plugin');
}
```

### Bước 3: Xác định Plugin Path (QUAN TRỌNG!)

WordPress tạo **Plugin ID** = `thư-mục/file-chính.php`

**Ví dụ:**

✅ **ĐÚNG - Plugin được nhận diện:**
```
wp-content/plugins/
└── anmi-banner-video-pro/
    └── anmi-banner-video-pro.php

Plugin ID: anmi-banner-video-pro/anmi-banner-video-pro.php
```

❌ **SAI - Plugin KHÔNG được nhận diện:**
```
wp-content/plugins/
└── anmi-banner-video-pro/
    └── anmi-video-banner.php         ← Tên file khác với thư mục

Plugin ID: anmi-banner-video-pro/anmi-video-banner.php
                                  ↑
                           Tên không khớp!
```

### Bước 4: Lưu vào Database

Khi bạn **Activate** plugin, WordPress lưu vào database:

```sql
-- Bảng: wp_options
-- option_name: active_plugins

option_value: 
a:2:{
  i:0;s:35:"akismet/akismet.php";
  i:1;s:51:"anmi-banner-video-pro/anmi-banner-video-pro.php";
}
```

**Format:** Serialized PHP array
```php
array(
    0 => 'akismet/akismet.php',
    1 => 'anmi-banner-video-pro/anmi-banner-video-pro.php'
)
```

## 🔧 Quy trình cài đặt Plugin chi tiết

### Cách 1: Upload qua WordPress Admin

#### Step 1: Upload file ZIP
```php
// WordPress tải file lên wp-content/uploads/
$uploaded_file = $_FILES['pluginzip'];
move_uploaded_file($uploaded_file['tmp_name'], 
                   WP_CONTENT_DIR . '/uploads/temp-plugin.zip');
```

#### Step 2: Giải nén
```php
// WordPress giải nén vào wp-content/plugins/
$zip = new ZipArchive;
$zip->open(WP_CONTENT_DIR . '/uploads/temp-plugin.zip');
$zip->extractTo(WP_PLUGIN_DIR);
$zip->close();

// Kết quả:
// wp-content/plugins/anmi-banner-video-pro/
```

#### Step 3: Xác thực Plugin Header
```php
// WordPress kiểm tra file có header hợp lệ không
$plugin_data = get_plugin_data(
    WP_PLUGIN_DIR . '/anmi-banner-video-pro/anmi-banner-video-pro.php'
);

if (empty($plugin_data['Name'])) {
    wp_die('Plugin không có header hợp lệ!');
}
```

#### Step 4: Hiển thị trong danh sách
```php
// Plugin xuất hiện trong Plugins > Installed Plugins
// với trạng thái "Inactive"
```

### Cách 2: Upload qua FTP

#### Step 1: Kết nối FTP
```
Host: ftp.yoursite.com
Username: your_username
Password: your_password
```

#### Step 2: Upload thư mục
```
Local:  C:\xampp\htdocs\wp-content\plugins\anmi-banner-video-pro\
Remote: /public_html/wp-content/plugins/anmi-banner-video-pro/
```

#### Step 3: WordPress tự động phát hiện
- Lần tiếp theo vào **Plugins**, WordPress tự động quét và hiển thị

## ⚡ Activation Hook - Kích hoạt Plugin

### Code trong plugin:

```php
// File: anmi-banner-video-pro.php

register_activation_hook(__FILE__, 'anmi_video_banner_activate');

function anmi_video_banner_activate() {
    // 1. Tạo database tables
    global $wpdb;
    $table_name = $wpdb->prefix . 'anmi_video_banners';
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        video_url text NOT NULL,
        PRIMARY KEY  (id)
    )";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // 2. Set default options
    add_option('anmi_video_banner_version', '2.0.0');
    add_option('anmi_video_banner_settings', array(
        'autoplay' => true,
        'muted' => true
    ));
    
    // 3. Clear cache
    wp_cache_flush();
    
    // 4. Create rewrite rules
    flush_rewrite_rules();
    
    // 5. Log activation
    error_log('AnMi Video Banner Pro activated');
}
```

### WordPress thực thi:

```php
// WordPress core: wp-admin/includes/plugin.php

function activate_plugin($plugin) {
    $plugin = plugin_basename($plugin);
    
    // 1. Include file plugin
    include_once(WP_PLUGIN_DIR . '/' . $plugin);
    
    // 2. Chạy activation hook
    do_action('activate_' . $plugin);
    
    // 3. Thêm vào active_plugins
    $current = get_option('active_plugins', array());
    $current[] = $plugin;
    update_option('active_plugins', $current);
    
    // 4. Chạy activated hook
    do_action('activated_plugin', $plugin);
}
```

## 🛠️ Deactivation Hook - Vô hiệu hóa Plugin

```php
register_deactivation_hook(__FILE__, 'anmi_video_banner_deactivate');

function anmi_video_banner_deactivate() {
    // 1. Xóa scheduled tasks
    wp_clear_scheduled_hook('anmi_video_banner_daily_cleanup');
    
    // 2. Xóa transients
    delete_transient('anmi_video_banner_cache');
    
    // 3. Clear rewrite rules
    flush_rewrite_rules();
    
    // 4. Log deactivation
    error_log('AnMi Video Banner Pro deactivated');
    
    // LƯU Ý: KHÔNG xóa database hoặc user data ở đây!
}
```

## 🗑️ Uninstall Hook - Gỡ cài đặt Plugin

### Cách 1: File uninstall.php

```php
// File: uninstall.php (trong thư mục plugin)

// Kiểm tra WordPress gọi đúng
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Xóa database tables
global $wpdb;
$table_name = $wpdb->prefix . 'anmi_video_banners';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Xóa options
delete_option('anmi_video_banner_version');
delete_option('anmi_video_banner_settings');

// Xóa user meta
delete_metadata('user', 0, 'anmi_video_banner_preferences', '', true);

// Xóa uploaded files
$upload_dir = wp_upload_dir();
$plugin_upload_dir = $upload_dir['basedir'] . '/anmi-video-banners';
if (is_dir($plugin_upload_dir)) {
    // Xóa thư mục và files
}
```

### Cách 2: Register uninstall hook

```php
// File: anmi-banner-video-pro.php

register_uninstall_hook(__FILE__, 'anmi_video_banner_uninstall');

function anmi_video_banner_uninstall() {
    // Tương tự như uninstall.php
}
```

## 🔍 Cách WordPress load Plugin

### 1. Khi WordPress khởi động:

```php
// File: wp-settings.php

// Load active plugins
if (is_multisite()) {
    foreach (wp_get_active_network_plugins() as $network_plugin) {
        include_once($network_plugin);
    }
}

foreach (wp_get_active_and_valid_plugins() as $plugin) {
    include_once($plugin);
}

// Fire action sau khi load plugins
do_action('plugins_loaded');
```

### 2. Thứ tự load:

```
1. Must-Use Plugins (wp-content/mu-plugins/)
   ↓
2. Network-activated plugins (multisite)
   ↓
3. Site-specific plugins (active_plugins option)
   ↓
4. Themes (active theme functions.php)
```

### 3. Trong plugin file:

```php
<?php
/**
 * Plugin Name: AnMi Banner Video Pro
 */

// 1. Prevent direct access
if (!defined('ABSPATH')) {
    exit; // WordPress chưa load, exit ngay
}

// 2. Define constants
define('PLUGIN_VERSION', '2.0.0');
define('PLUGIN_PATH', plugin_dir_path(__FILE__));

// 3. Load dependencies
require_once PLUGIN_PATH . 'includes/class-admin.php';
require_once PLUGIN_PATH . 'includes/class-frontend.php';

// 4. Initialize plugin
function anmi_banner_init() {
    // Plugin logic here
}
add_action('plugins_loaded', 'anmi_banner_init');

// 5. Activation/Deactivation hooks
register_activation_hook(__FILE__, 'anmi_banner_activate');
register_deactivation_hook(__FILE__, 'anmi_banner_deactivate');
```

## ✅ Checklist Plugin hợp lệ

- [ ] File PHP chính có **Plugin Header** đầy đủ
- [ ] **Tên file chính** trùng với **tên thư mục** (recommended)
- [ ] Code có `if (!defined('ABSPATH')) exit;` để prevent direct access
- [ ] Có **activation hook** để setup (nếu cần)
- [ ] Có **deactivation hook** để cleanup (nếu cần)
- [ ] Có **uninstall.php** hoặc **uninstall hook** để xóa dữ liệu
- [ ] Text Domain đúng cho i18n
- [ ] Code tuân theo **WordPress Coding Standards**
- [ ] Sanitize input, escape output (security)

## 🐛 Debug Plugin

### Enable WordPress Debug Mode:

```php
// File: wp-config.php

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);

// Log file: wp-content/debug.log
```

### Check Plugin Info:

```php
// Trong theme functions.php hoặc plugin khác

// Lấy tất cả plugins
$all_plugins = get_plugins();
print_r($all_plugins);

// Lấy active plugins
$active_plugins = get_option('active_plugins');
print_r($active_plugins);

// Check plugin có active không
if (is_plugin_active('anmi-banner-video-pro/anmi-banner-video-pro.php')) {
    echo 'Plugin is active!';
}
```

## 📝 Best Practices

1. **Naming Convention:**
   ```
   Thư mục: my-awesome-plugin
   File chính: my-awesome-plugin.php
   Text Domain: my-awesome-plugin
   Function prefix: myplugin_
   Class prefix: MyPlugin_
   ```

2. **Security:**
   ```php
   // Nonce verification
   wp_verify_nonce($_POST['_wpnonce'], 'action_name');
   
   // Capability check
   if (!current_user_can('manage_options')) {
       wp_die('Unauthorized');
   }
   
   // Sanitize input
   $data = sanitize_text_field($_POST['data']);
   
   // Escape output
   echo esc_html($data);
   ```

3. **Database Operations:**
   ```php
   global $wpdb;
   
   // Use prepare() for SQL
   $wpdb->query($wpdb->prepare(
       "INSERT INTO {$wpdb->prefix}table (col1, col2) VALUES (%s, %d)",
       $value1, $value2
   ));
   ```

## 🎓 Tóm tắt

WordPress nhận diện plugin qua:
1. ✅ **Plugin Header** trong file PHP chính
2. ✅ **Cấu trúc thư mục** đúng: `ten-plugin/ten-plugin.php`
3. ✅ **File hợp lệ** trong `wp-content/plugins/`

Quy trình kích hoạt:
1. Upload/Extract → `wp-content/plugins/`
2. WordPress quét → Đọc header
3. Hiển thị trong danh sách plugins
4. Click Activate → Chạy activation hook → Lưu vào DB

---

**Tài liệu tham khảo:**
- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [Plugin API](https://codex.wordpress.org/Plugin_API)
- [Writing a Plugin](https://developer.wordpress.org/plugins/intro/)
