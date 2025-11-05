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
    
    const SCAN_TRANSIENT_PREFIX = 'anmi_pm_scan_';
    const SCAN_SESSION_TTL      = 1800; // 30 minutes
    
    public function __construct() {
        add_action('admin_post_anmi_pm_scan_zip', [$this, 'handle_scan_request']);
        add_action('admin_post_anmi_pm_extract_staging', [$this, 'handle_extract_request']);
        add_action('admin_post_anmi_pm_discard_scan', [$this, 'handle_discard_request']);
    }
    
    /**
     * Render upload form
     */
    public function render() {
        $scan_token   = isset($_GET['scan_token']) ? sanitize_text_field($_GET['scan_token']) : '';
        $scan_session = $scan_token ? $this->get_scan_session($scan_token) : null;
        $messages     = $this->collect_messages();
        $max_upload   = size_format(wp_max_upload_size());

        $view = [
            'scan_token'   => $scan_token,
            'scan_session' => $scan_session,
            'messages'     => $messages,
            'max_upload'   => $max_upload,
        ];

        include ANMI_PM_DIR . 'admin/templates/upload.php';
    }
    
    /**
     * Handle initial scan request
     */
    public function handle_scan_request() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'anmi-plugin-manager'));
        }

        check_admin_referer('anmi_pm_scan_zip', 'anmi_pm_scan_nonce');

        if (!isset($_FILES['plugin_zip']) || $_FILES['plugin_zip']['error'] !== UPLOAD_ERR_OK) {
            $this->redirect_with_status('error', 'upload_failed', __('File upload error', 'anmi-plugin-manager'));
        }

        $file        = $_FILES['plugin_zip'];
        $validation  = $this->validate_upload($file);

        if (!$validation['valid']) {
            $this->redirect_with_status('error', 'validation_failed', $validation['message']);
        }

        $logger = new Anmi_PM_Logger();

        try {
            $temp_file = $this->move_to_temp($file);

            $logger->log('upload_received', [
                'file' => $file['name'],
                'size' => (int) $file['size'],
            ]);

            $scan_result = $this->security_scan($temp_file);

            if (!$scan_result['safe']) {
                $logger->log('upload_rejected', [
                    'file'    => $file['name'],
                    'reason'  => 'security_scan_failed',
                    'threats' => $scan_result['threats'],
                ]);

                @unlink($temp_file);

                $this->redirect_with_status('error', 'security_scan_failed', implode(', ', $scan_result['threats']));
            }

            $metadata = $this->inspect_zip_metadata($temp_file);

            $token   = wp_generate_uuid4();
            $session = [
                'token'        => $token,
                'file_name'    => $file['name'],
                'file_size'    => (int) $file['size'],
                'temp_path'    => $temp_file,
                'scan'         => $scan_result,
                'metadata'     => $metadata,
                'created_at'   => time(),
                'auto_activate'=> isset($_POST['auto_activate']) ? '1' : '0',
                'user_id'      => get_current_user_id(),
            ];

            $this->persist_scan_session($session);

            $redirect = add_query_arg([
                'page'        => 'anmi-plugins-upload',
                'scan_token'  => rawurlencode($token),
                'notice'      => 'success',
                'code'        => 'scan_ready',
            ], admin_url('admin.php'));

            wp_safe_redirect($redirect);
            exit;

        } catch (Exception $e) {
            $logger->log('upload_exception', ['error' => $e->getMessage()]);
            $this->redirect_with_status('error', 'exception', $e->getMessage());
        }
    }

    /**
     * Handle extraction step
     */
    public function handle_extract_request() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'anmi-plugin-manager'));
        }

        check_admin_referer('anmi_pm_extract', 'anmi_pm_extract_nonce');

        if (Anmi_PM_Settings::is_kill_switch_enabled()) {
            $this->redirect_with_status('warning', 'kill_switch_active', __('Kill-switch is active. Extraction blocked.', 'anmi-plugin-manager'));
        }

        $token   = isset($_POST['scan_token']) ? sanitize_text_field($_POST['scan_token']) : '';
        $session = $this->get_scan_session($token);

        if (!$session) {
            $this->redirect_with_status('error', 'session_missing', __('Scan session expired or missing.', 'anmi-plugin-manager'));
        }

        $logger = new Anmi_PM_Logger();
        $auto_activate = (isset($_POST['auto_activate']) && $_POST['auto_activate'] === '1') || !empty($session['auto_activate']);

        try {
            $staging_result = $this->extract_to_staging($session['temp_path']);

            if (!$staging_result['success']) {
                $logger->log('staging_failed', [
                    'file'  => $session['file_name'],
                    'error' => $staging_result['error'],
                ]);
                $this->redirect_with_status('error', 'staging_failed', $staging_result['error'], ['scan_token' => rawurlencode($token)]);
            }

            $staging_dir = $staging_result['staging_dir'];
            $plugin_info = $staging_result['plugin_info'];

            $backup_zip = null;
            $existing_path = WP_PLUGIN_DIR . '/' . $plugin_info['plugin_dir'];
            if (is_dir($existing_path)) {
                $backup_zip = $this->backup_existing_plugin($plugin_info['plugin_dir']);
                $logger->log('backup_created', [
                    'plugin_dir' => $plugin_info['plugin_dir'],
                    'backup_zip' => $backup_zip,
                ]);
            }

            $move_result = $this->move_to_plugins($staging_dir, $plugin_info['plugin_dir']);
            if (!$move_result['success']) {
                $logger->log('move_failed', ['error' => $move_result['error']]);
                $this->redirect_with_status('error', 'move_failed', $move_result['error'], ['scan_token' => rawurlencode($token)]);
            }

            $checksum = sha1_file(WP_PLUGIN_DIR . '/' . $plugin_info['plugin_file']);

            Anmi_PM_Metadata_Manager::save_plugin_meta([
                'plugin_file'    => $plugin_info['plugin_file'],
                'plugin_dir'     => $plugin_info['plugin_dir'],
                'name'           => $plugin_info['name'],
                'version'        => $plugin_info['version'],
                'author'         => $plugin_info['author'],
                'checksum'       => $checksum,
                'installed_date' => current_time('mysql'),
                'active_status'  => '0',
                'managed'        => true,
                'backup_zip'     => $backup_zip,
            ]);

            $logger->log('upload_success', [
                'plugin_file' => $plugin_info['plugin_file'],
                'version'     => $plugin_info['version'],
                'backup_zip'  => $backup_zip,
            ]);

            if ($auto_activate) {
                $activate_result = Anmi_PM_Plugin_Activator::safe_activate($plugin_info['plugin_file']);
                if ($activate_result['success']) {
                    $logger->log('auto_activated_safe', ['plugin_file' => $plugin_info['plugin_file']]);
                } else {
                    $logger->log('auto_activate_failed', [
                        'plugin_file' => $plugin_info['plugin_file'],
                        'error'       => $activate_result['message'],
                    ]);
                }
            }

            @unlink($session['temp_path']);
            $this->delete_scan_session($token);

            wp_safe_redirect(add_query_arg([
                'page'   => 'anmi-plugins',
                'upload' => 'success',
                'plugin' => rawurlencode($plugin_info['plugin_file']),
            ], admin_url('admin.php')));
            exit;

        } catch (Exception $e) {
            $logger->log('upload_exception', ['error' => $e->getMessage()]);
            $this->redirect_with_status('error', 'exception', $e->getMessage(), ['scan_token' => rawurlencode($token)]);
        }
    }

    /**
     * Handle discard request
     */
    public function handle_discard_request() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'anmi-plugin-manager'));
        }

        check_admin_referer('anmi_pm_discard', 'anmi_pm_discard_nonce');

        $token   = isset($_POST['scan_token']) ? sanitize_text_field($_POST['scan_token']) : '';
        $session = $this->get_scan_session($token);

        if ($session) {
            @unlink($session['temp_path']);
            $this->delete_scan_session($token);
        }

        $redirect = add_query_arg([
            'page'   => 'anmi-plugins-upload',
            'notice' => 'success',
            'code'   => 'discarded',
        ], admin_url('admin.php'));

        wp_safe_redirect($redirect);
        exit;
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
     * Persist scan session in transient cache
     */
    private function persist_scan_session(array $session) {
        set_transient(self::SCAN_TRANSIENT_PREFIX . $session['token'], $session, self::SCAN_SESSION_TTL);
    }

    /**
     * Retrieve scan session
     */
    private function get_scan_session($token) {
        if (empty($token)) {
            return null;
        }

        $session = get_transient(self::SCAN_TRANSIENT_PREFIX . $token);
        if (!$session) {
            return null;
        }

        // Extend TTL while active
        set_transient(self::SCAN_TRANSIENT_PREFIX . $token, $session, self::SCAN_SESSION_TTL);

        return $session;
    }

    /**
     * Delete scan session
     */
    private function delete_scan_session($token) {
        if (empty($token)) {
            return;
        }
        delete_transient(self::SCAN_TRANSIENT_PREFIX . $token);
    }

    /**
     * Collect UI messages for template
     */
    private function collect_messages() {
        $messages = [];

        if (!isset($_GET['notice'])) {
            return $messages;
        }

    $type  = sanitize_text_field($_GET['notice']);
    $code  = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : '';
    $detail = isset($_GET['detail']) ? sanitize_text_field(rawurldecode($_GET['detail'])) : '';

        $map = [
            'scan_ready'          => __('Security scan completed. Review details below.', 'anmi-plugin-manager'),
            'security_scan_failed'=> __('Security scan failed. Threats detected and upload rejected.', 'anmi-plugin-manager'),
            'validation_failed'   => __('The uploaded file is not a valid ZIP archive.', 'anmi-plugin-manager'),
            'upload_failed'       => __('Unable to process the uploaded file.', 'anmi-plugin-manager'),
            'staging_failed'      => __('Extraction into staging failed. Please review the error and try again.', 'anmi-plugin-manager'),
            'move_failed'         => __('Failed to move plugin into the plugins directory.', 'anmi-plugin-manager'),
            'session_missing'     => __('The scan session is no longer available. Please upload the ZIP file again.', 'anmi-plugin-manager'),
            'discarded'           => __('Scan session discarded.', 'anmi-plugin-manager'),
            'exception'           => __('An unexpected error occurred during processing.', 'anmi-plugin-manager'),
            'kill_switch_active'  => __('Kill-switch is active. Extraction blocked until it is disabled.', 'anmi-plugin-manager'),
        ];

        $text = $map[$code] ?? __('Operation completed.', 'anmi-plugin-manager');

        if (!empty($detail)) {
            $text .= ' ' . $detail;
        }

        $messages[] = [
            'type' => $type,
            'text' => $text,
        ];

        return $messages;
    }

    /**
     * Redirect helper for upload workflow
     */
    private function redirect_with_status($type, $code, $detail = '', $extra = []) {
        $query = array_merge([
            'page'  => 'anmi-plugins-upload',
            'notice'=> $type,
            'code'  => $code,
        ], $extra);

        if (!empty($detail)) {
            $query['detail'] = rawurlencode($detail);
        }

        wp_safe_redirect(add_query_arg($query, admin_url('admin.php')));
        exit;
    }

    /**
     * Inspect zip file to extract metadata without staging
     */
    private function inspect_zip_metadata($zip_file) {
        if (!class_exists('ZipArchive')) {
            return [];
        }

        $zip = new ZipArchive();
        if ($zip->open($zip_file) !== true) {
            return [];
        }

        $metadata = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $filename = $stat['name'];

            if (substr($filename, -4) !== '.php') {
                continue;
            }

            if (substr($filename, -1) === '/') {
                continue;
            }

            $content = $zip->getFromIndex($i);
            if ($content === false || stripos($content, 'Plugin Name:') === false) {
                continue;
            }

            if (!function_exists('get_plugin_data')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $temp_file = wp_tempnam($filename);
            if (!$temp_file) {
                continue;
            }

            file_put_contents($temp_file, $content);
            $plugin_data = get_plugin_data($temp_file, false, false);
            @unlink($temp_file);

            if (!empty($plugin_data['Name'])) {
                $metadata = [
                    'plugin_file' => str_replace('\', '/', $filename),
                    'name'        => $plugin_data['Name'],
                    'version'     => $plugin_data['Version'],
                    'author'      => $plugin_data['Author'],
                    'description' => $plugin_data['Description'],
                ];
                break;
            }
        }

        $zip->close();

        return $metadata;
    }
}
