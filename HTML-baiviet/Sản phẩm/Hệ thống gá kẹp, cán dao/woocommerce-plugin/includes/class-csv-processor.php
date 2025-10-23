<?php
/**
 * CSV Processor Class
 * Parse and process CSV files
 */

if (!defined('ABSPATH')) {
    exit;
}

class AnMi_CSV_Processor {
    
    private $delimiter = ',';
    private $encoding = 'UTF-8';
    
    /**
     * Parse CSV file
     */
    public function parse_csv($filepath, $options = array()) {
        $this->delimiter = $options['delimiter'] ?? ',';
        $this->encoding = $options['encoding'] ?? 'UTF-8';
        
        // Detect encoding if auto
        if ($this->encoding === 'auto') {
            $this->encoding = $this->detect_encoding($filepath);
        }
        
        $products = array();
        $handle = fopen($filepath, 'r');
        
        if (!$handle) {
            throw new Exception('Không thể mở file CSV');
        }
        
        // Read header
        $headers = fgetcsv($handle, 0, $this->delimiter);
        
        if (!$headers) {
            fclose($handle);
            throw new Exception('File CSV không có header');
        }
        
        // Convert encoding
        if (strtoupper($this->encoding) !== 'UTF-8') {
            $headers = $this->convert_encoding($headers);
        }
        
        // Read data rows
        while (($row = fgetcsv($handle, 0, $this->delimiter)) !== false) {
            if (strtoupper($this->encoding) !== 'UTF-8') {
                $row = $this->convert_encoding($row);
            }
            
            $product_data = $this->map_row_to_product($headers, $row);
            
            if ($product_data) {
                $products[] = $product_data;
            }
        }
        
        fclose($handle);
        
        return $products;
    }
    
    /**
     * Map CSV row to product data
     */
    private function map_row_to_product($headers, $row) {
        $data = array();
        
        foreach ($headers as $index => $header) {
            $header = trim(strtolower($header));
            $value = isset($row[$index]) ? trim($row[$index]) : '';
            
            // Map CSV columns to WooCommerce fields
            switch ($header) {
                case 'id':
                case 'product_id':
                    $data['id'] = $value;
                    break;
                    
                case 'sku':
                case 'mã':
                case 'ma':
                    $data['sku'] = $value;
                    break;
                    
                case 'name':
                case 'title':
                case 'tên':
                case 'ten':
                case 'product_name':
                    $data['name'] = $value;
                    break;
                    
                case 'slug':
                case 'url':
                    $data['slug'] = $value;
                    break;
                    
                case 'description':
                case 'mô tả':
                case 'mo_ta':
                    $data['description'] = $value;
                    break;
                    
                case 'short_description':
                case 'mô tả ngắn':
                case 'mo_ta_ngan':
                    $data['short_description'] = $value;
                    break;
                    
                case 'regular_price':
                case 'price':
                case 'giá':
                case 'gia':
                    $data['regular_price'] = $this->parse_price($value);
                    break;
                    
                case 'sale_price':
                case 'giá khuyến mãi':
                case 'gia_khuyen_mai':
                    $data['sale_price'] = $this->parse_price($value);
                    break;
                    
                case 'stock':
                case 'stock_quantity':
                case 'tồn kho':
                case 'ton_kho':
                case 'quantity':
                    $data['stock_quantity'] = (int)$value;
                    break;
                    
                case 'category':
                case 'categories':
                case 'danh mục':
                case 'danh_muc':
                    $data['categories'] = $value;
                    break;
                    
                case 'tags':
                case 'thẻ':
                case 'the':
                    $data['tags'] = $value;
                    break;
                    
                case 'images':
                case 'hình ảnh':
                case 'hinh_anh':
                    $data['images'] = $value;
                    break;
                    
                case 'status':
                case 'trạng thái':
                case 'trang_thai':
                    $data['status'] = $this->parse_status($value);
                    break;
                    
                case 'attributes':
                case 'thuộc tính':
                case 'thuoc_tinh':
                    $data['attributes'] = $value;
                    break;
                    
                default:
                    // Store as meta data
                    if (!empty($value)) {
                        $data['meta'][$header] = $value;
                    }
                    break;
            }
        }
        
        // Validate required fields
        if (empty($data['name'])) {
            return null;
        }
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generate_slug($data['name']);
        }
        
        return $data;
    }
    
    /**
     * Parse price (remove currency symbols, commas, etc.)
     */
    private function parse_price($price_string) {
        if (empty($price_string)) {
            return '';
        }
        
        // Remove currency symbols and spaces
        $price_string = str_replace(array('₫', 'VNĐ', 'VND', '$', '€', ',', ' '), '', $price_string);
        
        return floatval($price_string);
    }
    
    /**
     * Parse status
     */
    private function parse_status($status_string) {
        $status_string = strtolower(trim($status_string));
        
        $status_map = array(
            'publish' => 'publish',
            'published' => 'publish',
            'đăng' => 'publish',
            'công khai' => 'publish',
            'draft' => 'draft',
            'nháp' => 'draft',
            'pending' => 'pending',
            'chờ duyệt' => 'pending',
            'private' => 'private',
            'riêng tư' => 'private'
        );
        
        return $status_map[$status_string] ?? 'publish';
    }
    
    /**
     * Generate slug from name
     */
    private function generate_slug($name) {
        // Convert Vietnamese to Latin
        $name = $this->vietnamese_to_latin($name);
        
        // Sanitize
        $slug = sanitize_title($name);
        
        return $slug;
    }
    
    /**
     * Convert Vietnamese characters to Latin
     */
    private function vietnamese_to_latin($str) {
        $vietnamese = array(
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
        );
        
        $latin = array(
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
        );
        
        return str_replace($vietnamese, $latin, $str);
    }
    
    /**
     * Detect file encoding
     */
    private function detect_encoding($filepath) {
        $content = file_get_contents($filepath, false, null, 0, 10000);
        $encoding = mb_detect_encoding($content, ['UTF-8', 'UTF-16', 'Windows-1252', 'ISO-8859-1'], true);
        return $encoding ?: 'UTF-8';
    }
    
    /**
     * Convert encoding to UTF-8
     */
    private function convert_encoding($data) {
        return array_map(function($value) {
            return mb_convert_encoding($value, 'UTF-8', $this->encoding);
        }, $data);
    }
    
    /**
     * Export CSV template
     */
    public function export_template() {
        $headers = array(
            'sku',
            'name',
            'slug',
            'description',
            'short_description',
            'regular_price',
            'sale_price',
            'stock_quantity',
            'categories',
            'tags',
            'images',
            'status',
            'attributes'
        );
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="anmi-woocommerce-import-template-' . date('Ymd') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers
        fputcsv($output, $headers);
        
        // Sample row
        fputcsv($output, array(
            'BT-SK-001',
            'BT-SK High Speed Tool Holder',
            'bt-sk-high-speed-tool-holder',
            'BT-SK High Speed Tool Holder với độ chính xác 0.003mm, tốc độ 40,000 RPM. Collet SK6-SK20, cân bằng G2.5.',
            'Gá kẹp tốc độ cao BT-SK với độ chính xác vượt trội',
            '1500000',
            '1350000',
            '100',
            'BT SIDE LOCK, Tool Holders',
            'bt-sk, high speed, tool holder',
            'https://anmitools.com/images/bt-sk-001.jpg, https://anmitools.com/images/bt-sk-002.jpg',
            'publish',
            'Size: BT30, BT40, BT50 | Material: Steel'
        ));
        
        fclose($output);
        exit;
    }
}
