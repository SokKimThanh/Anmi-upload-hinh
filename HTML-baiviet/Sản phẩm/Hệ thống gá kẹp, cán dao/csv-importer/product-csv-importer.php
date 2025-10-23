<?php
/**
 * Product CSV Importer for An Mi Tools
 * Version: 1.0.0
 * Date: 2025-10-23
 * 
 * Công cụ nhập sản phẩm từ file CSV vào hệ thống
 */

class ProductCSVImporter {
    
    private $max_file_size = 268435456; // 256 MB
    private $allowed_extensions = ['csv', 'txt'];
    private $default_delimiter = ',';
    private $default_encoding = 'UTF-8';
    private $products_json_path = '../products-list.json';
    private $seo_backend_path = '../seo back end/products/';
    private $seo_html_path = '../seo html/';
    
    private $errors = [];
    private $warnings = [];
    private $stats = [
        'total' => 0,
        'imported' => 0,
        'updated' => 0,
        'skipped' => 0,
        'failed' => 0
    ];
    
    /**
     * Constructor
     */
    public function __construct() {
        // Set PHP configurations
        ini_set('auto_detect_line_endings', true);
        ini_set('max_execution_time', 300); // 5 minutes
        ini_set('memory_limit', '512M');
    }
    
    /**
     * Import from uploaded file
     */
    public function importFromUpload($file, $options = []) {
        // Validate file upload
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $this->errors[] = "File upload không hợp lệ";
            return false;
        }
        
        // Check file size
        if ($file['size'] > $this->max_file_size) {
            $this->errors[] = "File vượt quá kích thước cho phép (256 MB)";
            return false;
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowed_extensions)) {
            $this->errors[] = "Định dạng file không được hỗ trợ. Chỉ chấp nhận CSV hoặc TXT";
            return false;
        }
        
        // Process the file
        return $this->processCSVFile($file['tmp_name'], $options);
    }
    
    /**
     * Import from server path
     */
    public function importFromServerPath($filepath, $options = []) {
        if (!file_exists($filepath)) {
            $this->errors[] = "File không tồn tại trên server: " . $filepath;
            return false;
        }
        
        if (!is_readable($filepath)) {
            $this->errors[] = "Không có quyền đọc file: " . $filepath;
            return false;
        }
        
        return $this->processCSVFile($filepath, $options);
    }
    
    /**
     * Process CSV file
     */
    private function processCSVFile($filepath, $options) {
        $delimiter = $options['delimiter'] ?? $this->default_delimiter;
        $encoding = $options['encoding'] ?? $this->default_encoding;
        $update_existing = $options['update_existing'] ?? false;
        $use_previous_mapping = $options['use_previous_mapping'] ?? false;
        
        // Detect encoding if needed
        if ($encoding === 'auto') {
            $encoding = $this->detectEncoding($filepath);
        }
        
        // Open file
        $handle = fopen($filepath, 'r');
        if (!$handle) {
            $this->errors[] = "Không thể mở file CSV";
            return false;
        }
        
        // Read header row
        $headers = fgetcsv($handle, 0, $delimiter);
        if (!$headers) {
            $this->errors[] = "File CSV không có header";
            fclose($handle);
            return false;
        }
        
        // Convert encoding if needed
        if (strtoupper($encoding) !== 'UTF-8') {
            $headers = array_map(function($value) use ($encoding) {
                return mb_convert_encoding($value, 'UTF-8', $encoding);
            }, $headers);
        }
        
        // Validate required columns
        $required_columns = ['id', 'title', 'slug', 'category'];
        $column_mapping = $this->mapColumns($headers, $use_previous_mapping);
        
        foreach ($required_columns as $req_col) {
            if (!isset($column_mapping[$req_col])) {
                $this->errors[] = "Thiếu cột bắt buộc: " . $req_col;
                fclose($handle);
                return false;
            }
        }
        
        // Load existing products
        $existing_products = $this->loadExistingProducts();
        $products_by_id = [];
        $products_by_slug = [];
        
        foreach ($existing_products as $idx => $product) {
            if (isset($product['id'])) {
                $products_by_id[$product['id']] = $idx;
            }
            if (isset($product['slug'])) {
                $products_by_slug[$product['slug']] = $idx;
            }
        }
        
        // Process each row
        $row_number = 1;
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $row_number++;
            $this->stats['total']++;
            
            // Convert encoding if needed
            if (strtoupper($encoding) !== 'UTF-8') {
                $row = array_map(function($value) use ($encoding) {
                    return mb_convert_encoding($value, 'UTF-8', $encoding);
                }, $row);
            }
            
            // Map row data to product structure
            $product_data = $this->mapRowToProduct($row, $headers, $column_mapping);
            
            if (!$product_data) {
                $this->warnings[] = "Dòng {$row_number}: Dữ liệu không hợp lệ, bỏ qua";
                $this->stats['skipped']++;
                continue;
            }
            
            // Check if product exists
            $existing_index = null;
            if (isset($product_data['id']) && isset($products_by_id[$product_data['id']])) {
                $existing_index = $products_by_id[$product_data['id']];
            } elseif (isset($product_data['slug']) && isset($products_by_slug[$product_data['slug']])) {
                $existing_index = $products_by_slug[$product_data['slug']];
            }
            
            if ($existing_index !== null) {
                if ($update_existing) {
                    // Update existing product
                    $existing_products[$existing_index] = array_merge(
                        $existing_products[$existing_index],
                        $product_data
                    );
                    $this->stats['updated']++;
                } else {
                    $this->warnings[] = "Dòng {$row_number}: Sản phẩm ID {$product_data['id']} đã tồn tại, bỏ qua";
                    $this->stats['skipped']++;
                }
            } else {
                // Add new product
                $existing_products[] = $product_data;
                $this->stats['imported']++;
            }
        }
        
        fclose($handle);
        
        // Save updated products list
        if ($this->stats['imported'] > 0 || $this->stats['updated'] > 0) {
            $this->saveProducts($existing_products);
        }
        
        return true;
    }
    
    /**
     * Detect file encoding
     */
    private function detectEncoding($filepath) {
        $content = file_get_contents($filepath, false, null, 0, 10000);
        $encoding = mb_detect_encoding($content, ['UTF-8', 'UTF-16', 'Windows-1252', 'ISO-8859-1'], true);
        return $encoding ?: 'UTF-8';
    }
    
    /**
     * Map CSV columns to product fields
     */
    private function mapColumns($headers, $use_previous = false) {
        // If using previous mapping, load from session or config
        if ($use_previous && isset($_SESSION['csv_column_mapping'])) {
            return $_SESSION['csv_column_mapping'];
        }
        
        // Auto-detect column mapping
        $mapping = [];
        $headers_lower = array_map('strtolower', $headers);
        
        $field_patterns = [
            'id' => ['id', 'product_id', 'mã', 'ma'],
            'title' => ['title', 'name', 'tên', 'ten', 'product_name'],
            'slug' => ['slug', 'url', 'link'],
            'category' => ['category', 'danh mục', 'danh_muc', 'loại', 'loai'],
            'primary_keyword' => ['primary_keyword', 'keyword', 'từ khóa', 'tu_khoa'],
            'status' => ['status', 'trạng thái', 'trang_thai'],
            'file_md' => ['file_md', 'md_file', 'markdown'],
            'file_html' => ['file_html', 'html_file'],
            'file_css' => ['file_css', 'css_file'],
            'seo_title' => ['seo_title', 'meta_title'],
            'seo_description' => ['seo_description', 'meta_description'],
            'tags' => ['tags', 'thẻ', 'the'],
            'price' => ['price', 'giá', 'gia'],
            'stock' => ['stock', 'tồn kho', 'ton_kho', 'quantity']
        ];
        
        foreach ($field_patterns as $field => $patterns) {
            foreach ($patterns as $pattern) {
                $index = array_search($pattern, $headers_lower);
                if ($index !== false) {
                    $mapping[$field] = $index;
                    break;
                }
            }
        }
        
        // Save mapping to session
        $_SESSION['csv_column_mapping'] = $mapping;
        
        return $mapping;
    }
    
    /**
     * Map CSV row to product data structure
     */
    private function mapRowToProduct($row, $headers, $mapping) {
        $product = [];
        
        foreach ($mapping as $field => $column_index) {
            if (isset($row[$column_index])) {
                $value = trim($row[$column_index]);
                
                // Type casting
                if ($field === 'id') {
                    $product[$field] = (int)$value;
                } elseif (in_array($field, ['price', 'stock'])) {
                    $product[$field] = is_numeric($value) ? (float)$value : 0;
                } else {
                    $product[$field] = $value;
                }
            }
        }
        
        // Validate required fields
        if (empty($product['id']) || empty($product['title'])) {
            return null;
        }
        
        // Generate slug if not provided
        if (empty($product['slug'])) {
            $product['slug'] = $this->generateSlug($product['title']);
        }
        
        // Set default values
        $product['status'] = $product['status'] ?? 'pending';
        
        return $product;
    }
    
    /**
     * Generate slug from title
     */
    private function generateSlug($title) {
        // Convert Vietnamese characters
        $title = $this->convertVietnameseToLatin($title);
        
        // Convert to lowercase and replace spaces with hyphens
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Convert Vietnamese characters to Latin
     */
    private function convertVietnameseToLatin($str) {
        $vietnamese = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
            'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
            'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
            'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
            'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
            'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
            'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
            'Đ'
        ];
        
        $latin = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
            'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A',
            'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E',
            'I', 'I', 'I', 'I', 'I',
            'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O',
            'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U',
            'Y', 'Y', 'Y', 'Y', 'Y',
            'D'
        ];
        
        return str_replace($vietnamese, $latin, $str);
    }
    
    /**
     * Load existing products from JSON
     */
    private function loadExistingProducts() {
        if (!file_exists($this->products_json_path)) {
            return [];
        }
        
        $json_content = file_get_contents($this->products_json_path);
        $data = json_decode($json_content, true);
        
        return $data['products'] ?? [];
    }
    
    /**
     * Save products to JSON
     */
    private function saveProducts($products) {
        // Load existing JSON structure
        $json_data = [
            'metadata' => [
                'title' => 'Danh sách sản phẩm An Mi Tools - Hệ thống Holder CNC',
                'version' => '1.0.0',
                'date_created' => date('Y-m-d'),
                'date_modified' => date('Y-m-d H:i:s'),
                'description' => 'Danh sách sản phẩm được cập nhật từ CSV import',
                'total_products' => count($products)
            ],
            'products' => $products
        ];
        
        $json_content = json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        // Backup existing file
        if (file_exists($this->products_json_path)) {
            $backup_path = $this->products_json_path . '.backup.' . date('YmdHis');
            copy($this->products_json_path, $backup_path);
        }
        
        // Save new file
        return file_put_contents($this->products_json_path, $json_content) !== false;
    }
    
    /**
     * Get import statistics
     */
    public function getStats() {
        return $this->stats;
    }
    
    /**
     * Get errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get warnings
     */
    public function getWarnings() {
        return $this->warnings;
    }
    
    /**
     * Export products to CSV template
     */
    public function exportTemplate() {
        $headers = [
            'id',
            'title',
            'slug',
            'category',
            'primary_keyword',
            'status',
            'file_md',
            'file_html',
            'file_css',
            'seo_title',
            'seo_description',
            'tags',
            'price',
            'stock'
        ];
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="anmi-products-template-' . date('Ymd') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Write UTF-8 BOM
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Write headers
        fputcsv($output, $headers);
        
        // Write sample row
        fputcsv($output, [
            '1',
            'BT-SK High Speed Tool Holder',
            'bt-sk-high-speed-tool-holder',
            'BT SIDE LOCK',
            'bt-sk high speed tool holder',
            'completed',
            '01-bt-sk-high-speed-tool-holder.seo.md',
            '01-bt-sk-high-speed-tool-holder.seo.html',
            'bt-sk-high-speed-tool-holder.css',
            'BT-SK High Speed Tool Holder - Chính Xác 0.003mm | An Mi Tools 2025',
            'BT-SK High Speed Tool Holder với độ chính xác 0.003mm, tốc độ 40,000 RPM',
            'bt-sk holder, sk collet, high speed holder',
            '1500000',
            '100'
        ]);
        
        fclose($output);
        exit;
    }
}
