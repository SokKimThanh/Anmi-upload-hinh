<?php
/**
 * Plugin Uploader - Upload, staging, scan, backup
 */

defined('ABSPATH') || exit;

class Anmi_PM_Plugin_Uploader {
    
    // Dangerous patterns to scan for
    const DANGEROUS_PATTERNS = [
        'eval\s*\(',
        'base64_decode\s*\(',
        'exec\s*\(',
        'passthru\s*\(',
        'shell_exec\s*\(',
        'system\s*\(',
        'proc_open\s*\(',
        'popen\s*\(',
        'create_function\s*\(',
        'preg_replace\s*\(.*["\']\/.*e["\']',
        'gzuncompress\s*\(',
        'gzinflate\s*\(',
        'str_rot13\s*\(',
        'assert\s*\('
    ];
    
    public function __construct() {
        add_action('admin_post_anmi_pm_handle_upload', [$this, 'handle_upload']);
    }
    
    /**
     * Render upload form
     */
    public function render() {
        ?>
        <div class="wrap">
            <h1><?php _e('Upload Plugin', 'anmi-plugin-manager'); ?></h1>
            
            <?php $this->display_messages(); ?>
            
            <div class="anmi-upload-form">
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
                    <?php wp_nonce_field('anmi_pm_upload', 'anmi_pm_upload_nonce'); ?>
                    <input type="hidden" name="action" value="anmi_pm_handle_upload">
                    
                    <div class="form-field">
                        <label for="plugin_zip">
                            <?php _e('Select Plugin ZIP File', 'anmi-plugin-manager'); ?>
                        </label>
                        <input type="file" name="plugin_zip" id="plugin_zip" accept=".zip" required>
                        <span class="description">
                            <?php printf(__('Maximum file size: %s', 'anmi-plugin-manager'), size_format(wp_max_upload_size())); ?>
                        </span>
                    </div>
                    
                    <div class="form-field">
                        <label>
                            <input type="checkbox" name="auto_activate" value="1">
                            <?php _e('Tự động kích hoạt sau khi upload (không khuyến nghị)', 'anmi-plugin-manager'); ?>
                        </label>
                        <span class="description">
                            <?php _e('Nên kiểm tra và dùng Safe Activate thay vì tự động kích hoạt.', 'anmi-plugin-manager'); ?>
                        </span>
                    </div>
                    
                    <p>
                        <button type="submit" class="button button-primary button-large">
                            <?php _e('Upload & Stage Plugin', 'anmi-plugin-manager'); ?>
                        </button>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=anmi-plugins')); ?>" class="button button-large">
                            <?php _e('Cancel', 'anmi-plugin-manager'); ?>
                        </a>
                    </p>
                </form>
            </div>
            
            <div class="anmi-upload-info">
                <h3><?php _e('Upload Process', 'anmi-plugin-manager'); ?></h3>
                <ol>
                    <li><?php _e('Upload ZIP file → temporary storage', 'anmi-plugin-manager'); ?></li>
                    <li><?php _e('Security scan → detect dangerous code patterns', 'anmi-plugin-manager'); ?></li>
                    <li><?php _e('Extract to staging folder → not yet active', 'anmi-plugin-manager'); ?></li>
                    <li><?php _e('Backup existing plugin (if exists)', 'anmi-plugin-manager'); ?></li>
                    <li><?php _e('Move from staging to plugins folder', 'anmi-plugin-manager'); ?></li>
                    <li><?php _e('Save metadata → ready for Safe Activate', 'anmi-plugin-manager'); ?></li>
                </ol>
            </div>
        </div>
        <?php
    }
    
    /**
     * Handle upload POST
     */
    public function handle_upload() {
        // Security checks
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'anmi-plugin-manager'));
        }
        
        if (!isset($_POST['anmi_pm_upload_nonce']) || !wp_verify_nonce($_POST['anmi_pm_upload_nonce'], 'anmi_pm_upload')) {
            wp_die(__('Nonce verification failed', 'anmi-plugin-manager'));
        }
        
        // Check file upload
        if (!isset($_FILES['plugin_zip']) || $_FILES['plugin_zip']['error'] !== UPLOAD_ERR_OK) {
            $this->redirect_with_error('upload_failed', 'File upload error');
        }
        
        $file = $_FILES['plugin_zip'];
        
        // Validate file
        $validation = $this->validate_upload($file);
        if (!$validation['valid']) {
            $this->redirect_with_error('validation_failed', $validation['message']);
        }
        
        $logger = new Anmi_PM_Logger();
        
        try {
            // Step 1: Move to temp directory
            $temp_file = $this->move_to_temp($file);
            $logger->log('upload_received', ['file' => $file['name'], 'size' => $file['size']]);
            
            // Step 2: Security scan
            $scan_result = $this->security_scan($temp_file);
            if (!$scan_result['safe']) {
                @unlink($temp_file);
                $logger->log('upload_rejected', [
                    'file' => $file['name'],
                    'reason' => 'security_scan_failed',
                    'threats' => $scan_result['threats']
                ]);
                $this->redirect_with_error('security_scan_failed', implode(', ', $scan_result['threats']));
            }
            
            // Step 3: Extract to staging
            $staging_result = $this->extract_to_staging($temp_file);
            if (!$staging_result['success']) {
                @unlink($temp_file);
                $logger->log('staging_failed', ['file' => $file['name'], 'error' => $staging_result['error']]);
                $this->redirect_with_error('staging_failed', $staging_result['error']);
            }
            
            $staging_dir = $staging_result['staging_dir'];
            $plugin_info = $staging_result['plugin_info'];
            
            // Step 4: Backup existing plugin if exists
            $backup_zip = null;
            $existing_path = WP_PLUGIN_DIR . '/' . $plugin_info['plugin_dir'];
            if (is_dir($existing_path)) {
                $backup_zip = $this->backup_existing_plugin($plugin_info['plugin_dir']);
                $logger->log('backup_created', [
                    'plugin_dir' => $plugin_info['plugin_dir'],
                    'backup_zip' => $backup_zip
                ]);
            }
            
            // Step 5: Move from staging to plugins
            $move_result = $this->move_to_plugins($staging_dir, $plugin_info['plugin_dir']);
            if (!$move_result['success']) {
                $logger->log('move_failed', ['error' => $move_result['error']]);
                $this->redirect_with_error('move_failed', $move_result['error']);
            }
            
            // Step 6: Save metadata
            $checksum = sha1_file(WP_PLUGIN_DIR . '/' . $plugin_info['plugin_file']);
            Anmi_PM_Metadata_Manager::save_plugin_meta([
                'plugin_file' => $plugin_info['plugin_file'],
                'plugin_dir' => $plugin_info['plugin_dir'],
                'name' => $plugin_info['name'],
                'version' => $plugin_info['version'],
                'author' => $plugin_info['author'],
                'checksum' => $checksum,
                'installed_date' => current_time('mysql'),
                'active_status' => '0',
                'managed' => true,
                'backup_zip' => $backup_zip
            ]);
            
            $logger->log('upload_success', [
                'plugin_file' => $plugin_info['plugin_file'],
                'version' => $plugin_info['version'],
                'backup_zip' => $backup_zip
            ]);
            
            // Clean up temp file
            @unlink($temp_file);
            
            // Auto activate if requested (not recommended)
            if (isset($_POST['auto_activate']) && $_POST['auto_activate'] == '1') {
                $activate_result = Anmi_PM_Plugin_Activator::safe_activate($plugin_info['plugin_file']);
                if ($activate_result['success']) {
                    $logger->log('auto_activated_safe', ['plugin_file' => $plugin_info['plugin_file']]);
                } else {
                    $logger->log('auto_activate_failed', [
                        'plugin_file' => $plugin_info['plugin_file'],
                        'error' => $activate_result['message']
                    ]);
                }
            }
            
            // Redirect success
            wp_redirect(add_query_arg([
                'page' => 'anmi-plugins',
                'upload' => 'success',
                'plugin' => urlencode($plugin_info['plugin_file'])
            ], admin_url('admin.php')));
            exit;
            
        } catch (Exception $e) {
            $logger->log('upload_exception', ['error' => $e->getMessage()]);
            $this->redirect_with_error('exception', $e->getMessage());
        }
    }
    
    /**
     * Validate uploaded file
     */
    private function validate_upload($file) {
        // Check extension
        $filename = $file['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($ext !== 'zip') {
            return ['valid' => false, 'message' => 'Only .zip files allowed'];
        }
        
        // Check mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mimes = ['application/zip', 'application/x-zip-compressed'];
        if (!in_array($mime, $allowed_mimes)) {
            return ['valid' => false, 'message' => 'Invalid file type: ' . $mime];
        }
        
        // Check size
        if ($file['size'] > wp_max_upload_size()) {
            return ['valid' => false, 'message' => 'File too large'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Move uploaded file to temp directory
     */
    private function move_to_temp($file) {
        if (!is_dir(ANMI_PM_TEMP_DIR)) {
            @mkdir(ANMI_PM_TEMP_DIR, 0755, true);
        }
        
        $unique_name = uniqid('plugin_', true) . '.zip';
        $temp_file = ANMI_PM_TEMP_DIR . '/' . $unique_name;
        
        if (!move_uploaded_file($file['tmp_name'], $temp_file)) {
            throw new Exception('Failed to move uploaded file');
        }
        
        return $temp_file;
    }
    
    /**
     * Security scan for dangerous patterns
     */
    private function security_scan($zip_file) {
        if (!class_exists('ZipArchive')) {
            return ['safe' => true, 'message' => 'ZipArchive not available, skipping scan'];
        }
        
        $zip = new ZipArchive();
        if ($zip->open($zip_file) !== true) {
            return ['safe' => false, 'threats' => ['Cannot open zip file']];
        }
        
        $threats = [];
        
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $filename = $stat['name'];
            
            // Only scan PHP files
            if (substr($filename, -4) !== '.php') {
                continue;
            }
            
            // Skip if directory
            if (substr($filename, -1) === '/') {
                continue;
            }
            
            $content = $zip->getFromIndex($i);
            if ($content === false) {
                continue;
            }
            
            // Scan for dangerous patterns
            foreach (self::DANGEROUS_PATTERNS as $pattern) {
                if (preg_match('/' . $pattern . '/i', $content)) {
                    $threats[] = "File: {$filename} - Pattern: {$pattern}";
                }
            }
        }
        
        $zip->close();
        
        return [
            'safe' => empty($threats),
            'threats' => $threats
        ];
    }
    
    /**
     * Extract zip to staging directory
     */
    private function extract_to_staging($zip_file) {
        if (!class_exists('ZipArchive')) {
            return ['success' => false, 'error' => 'ZipArchive not available'];
        }
        
        $zip = new ZipArchive();
        if ($zip->open($zip_file) !== true) {
            return ['success' => false, 'error' => 'Cannot open zip file'];
        }
        
        // Create unique staging directory
        $staging_id = uniqid('staging_', true);
        $staging_dir = ANMI_PM_STAGING_DIR . '/' . $staging_id;
        
        if (!is_dir($staging_dir)) {
            @mkdir($staging_dir, 0755, true);
        }
        
        // Extract
        if (!$zip->extractTo($staging_dir)) {
            $zip->close();
            return ['success' => false, 'error' => 'Failed to extract zip'];
        }
        
        $zip->close();
        
        // Find plugin main file
        $plugin_info = $this->find_plugin_file($staging_dir);
        if (!$plugin_info) {
            $this->recursive_delete($staging_dir);
            return ['success' => false, 'error' => 'Plugin main file not found'];
        }
        
        return [
            'success' => true,
            'staging_dir' => $staging_dir,
            'plugin_info' => $plugin_info
        ];
    }
    
    /**
     * Find plugin main file in directory
     */
    private function find_plugin_file($dir) {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                
                // Check for plugin header
                if (preg_match('/Plugin Name:/i', $content)) {
                    $plugin_data = get_plugin_data($file->getPathname(), false, false);
                    
                    if (!empty($plugin_data['Name'])) {
                        // Determine relative path from staging dir
                        $relative_path = str_replace($dir . '/', '', $file->getPathname());
                        $plugin_dir = dirname($relative_path);
                        
                        if ($plugin_dir === '.') {
                            // Single file plugin
                            $plugin_dir = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                            $plugin_file = $plugin_dir . '/' . $file->getFilename();
                        } else {
                            $plugin_file = $relative_path;
                        }
                        
                        return [
                            'plugin_file' => str_replace('\\', '/', $plugin_file),
                            'plugin_dir' => str_replace('\\', '/', $plugin_dir),
                            'name' => $plugin_data['Name'],
                            'version' => $plugin_data['Version'],
                            'author' => $plugin_data['Author']
                        ];
                    }
                }
            }
        }
        
        return null;
    }
    
    /**
     * Backup existing plugin
     */
    private function backup_existing_plugin($plugin_dir) {
        if (!class_exists('ZipArchive')) {
            return null;
        }
        
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_dir;
        if (!is_dir($plugin_path)) {
            return null;
        }
        
        // Create backup directory
        if (!is_dir(ANMI_PM_BACKUP_DIR)) {
            @mkdir(ANMI_PM_BACKUP_DIR, 0755, true);
        }
        
        $timestamp = date('Ymd_His');
        $backup_file = ANMI_PM_BACKUP_DIR . '/' . basename($plugin_dir) . '_' . $timestamp . '.zip';
        
        $zip = new ZipArchive();
        if ($zip->open($backup_file, ZipArchive::CREATE) !== true) {
            return null;
        }
        
        // Add files to zip
        $this->add_dir_to_zip($zip, $plugin_path, $plugin_dir);
        $zip->close();
        
        return $backup_file;
    }
    
    /**
     * Add directory to zip recursively
     */
    private function add_dir_to_zip($zip, $source_dir, $local_dir) {
        $items = @scandir($source_dir);
        if (!$items) {
            return;
        }
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $source_path = $source_dir . '/' . $item;
            $local_path = $local_dir . '/' . $item;
            
            if (is_dir($source_path)) {
                $zip->addEmptyDir($local_path);
                $this->add_dir_to_zip($zip, $source_path, $local_path);
            } else {
                $zip->addFile($source_path, $local_path);
            }
        }
    }
    
    /**
     * Move plugin from staging to plugins directory
     */
    private function move_to_plugins($staging_dir, $plugin_dir) {
        $source = $staging_dir . '/' . $plugin_dir;
        $destination = WP_PLUGIN_DIR . '/' . $plugin_dir;
        
        // Remove existing if present
        if (is_dir($destination)) {
            $this->recursive_delete($destination);
        }
        
        // Move
        if (!@rename($source, $destination)) {
            // If rename fails, try copy
            if (!$this->recursive_copy($source, $destination)) {
                return ['success' => false, 'error' => 'Failed to move plugin to destination'];
            }
            $this->recursive_delete($source);
        }
        
        // Clean up staging directory
        $this->recursive_delete($staging_dir);
        
        return ['success' => true];
    }
    
    /**
     * Recursive copy
     */
    private function recursive_copy($src, $dst) {
        if (!is_dir($src)) {
            return @copy($src, $dst);
        }
        
        if (!is_dir($dst)) {
            @mkdir($dst, 0755, true);
        }
        
        $items = @scandir($src);
        if (!$items) {
            return false;
        }
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $src_path = $src . '/' . $item;
            $dst_path = $dst . '/' . $item;
            
            if (!$this->recursive_copy($src_path, $dst_path)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Recursive delete
     */
    private function recursive_delete($dir) {
        if (!is_dir($dir)) {
            return @unlink($dir);
        }
        
        $items = @scandir($dir);
        if (!$items) {
            return false;
        }
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->recursive_delete($path);
            } else {
                @unlink($path);
            }
        }
        
        return @rmdir($dir);
    }
    
    /**
     * Redirect with error
     */
    private function redirect_with_error($code, $message) {
        wp_redirect(add_query_arg([
            'page' => 'anmi-plugins-upload',
            'error' => $code,
            'message' => urlencode($message)
        ], admin_url('admin.php')));
        exit;
    }
    
    /**
     * Display messages
     */
    private function display_messages() {
        if (isset($_GET['error'])) {
            $error_code = sanitize_text_field($_GET['error']);
            $message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
            
            echo '<div class="notice notice-error is-dismissible"><p>';
            echo '<strong>Error:</strong> ' . esc_html($error_code);
            if ($message) {
                echo ' - ' . esc_html($message);
            }
            echo '</p></div>';
        }
    }
}
