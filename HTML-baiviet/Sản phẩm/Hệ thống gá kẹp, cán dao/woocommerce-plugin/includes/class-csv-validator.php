<?php
/**
 * CSV Validator Class
 * Validate CSV file before import
 */

if (!defined('ABSPATH')) {
    exit;
}

class AnMi_CSV_Validator {
    
    private $required_fields = array('name');
    private $recommended_fields = array('sku', 'slug', 'regular_price', 'categories');
    
    /**
     * Validate uploaded file
     */
    public function validate_file($file) {
        $errors = array();
        $warnings = array();
        $stats = array(
            'total_rows' => 0,
            'valid_rows' => 0,
            'invalid_rows' => 0
        );
        
        // Check file upload
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return array(
                'valid' => false,
                'errors' => array('File upload không hợp lệ'),
                'warnings' => array(),
                'stats' => $stats
            );
        }
        
        // Check file size
        $max_size = wp_max_upload_size();
        if ($file['size'] > $max_size) {
            return array(
                'valid' => false,
                'errors' => array('File vượt quá kích thước cho phép: ' . size_format($max_size)),
                'warnings' => array(),
                'stats' => $stats
            );
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, array('csv', 'txt'))) {
            return array(
                'valid' => false,
                'errors' => array('File phải có định dạng .csv hoặc .txt'),
                'warnings' => array(),
                'stats' => $stats
            );
        }
        
        // Parse and validate content
        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            return array(
                'valid' => false,
                'errors' => array('Không thể đọc file'),
                'warnings' => array(),
                'stats' => $stats
            );
        }
        
        // Read header
        $headers = fgetcsv($handle, 0, ',');
        if (!$headers) {
            fclose($handle);
            return array(
                'valid' => false,
                'errors' => array('File không có header'),
                'warnings' => array(),
                'stats' => $stats
            );
        }
        
        // Check required fields
        $headers_lower = array_map('strtolower', array_map('trim', $headers));
        $missing_required = array();
        
        foreach ($this->required_fields as $field) {
            $found = false;
            foreach ($headers_lower as $header) {
                if ($this->field_matches($header, $field)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $missing_required[] = $field;
            }
        }
        
        if (!empty($missing_required)) {
            $errors[] = 'Thiếu các cột bắt buộc: ' . implode(', ', $missing_required);
        }
        
        // Check recommended fields
        foreach ($this->recommended_fields as $field) {
            $found = false;
            foreach ($headers_lower as $header) {
                if ($this->field_matches($header, $field)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $warnings[] = 'Khuyến nghị thêm cột: ' . $field;
            }
        }
        
        // Validate data rows
        $seen_skus = array();
        $row_number = 1;
        
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $row_number++;
            $stats['total_rows']++;
            $row_valid = true;
            
            // Map row data
            $data = array();
            foreach ($headers_lower as $index => $header) {
                $data[$header] = isset($row[$index]) ? trim($row[$index]) : '';
            }
            
            // Check name
            $name_value = $this->get_field_value($data, 'name');
            if (empty($name_value)) {
                $errors[] = "Dòng {$row_number}: Thiếu tên sản phẩm";
                $row_valid = false;
            }
            
            // Check SKU duplication
            $sku_value = $this->get_field_value($data, 'sku');
            if (!empty($sku_value)) {
                if (in_array($sku_value, $seen_skus)) {
                    $warnings[] = "Dòng {$row_number}: SKU '{$sku_value}' bị trùng";
                }
                $seen_skus[] = $sku_value;
            }
            
            // Check price format
            $price_value = $this->get_field_value($data, 'price');
            if (!empty($price_value) && !is_numeric(str_replace(array(',', ' ', '₫', 'VND'), '', $price_value))) {
                $warnings[] = "Dòng {$row_number}: Định dạng giá không hợp lệ";
            }
            
            // Check stock
            $stock_value = $this->get_field_value($data, 'stock');
            if (!empty($stock_value) && !is_numeric($stock_value)) {
                $warnings[] = "Dòng {$row_number}: Định dạng tồn kho không hợp lệ";
            }
            
            if ($row_valid) {
                $stats['valid_rows']++;
            } else {
                $stats['invalid_rows']++;
            }
        }
        
        fclose($handle);
        
        return array(
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'stats' => $stats
        );
    }
    
    /**
     * Check if field matches any aliases
     */
    private function field_matches($header, $field) {
        $aliases = array(
            'name' => array('name', 'title', 'tên', 'ten', 'product_name'),
            'sku' => array('sku', 'mã', 'ma', 'product_code'),
            'slug' => array('slug', 'url', 'link'),
            'price' => array('price', 'regular_price', 'giá', 'gia'),
            'categories' => array('category', 'categories', 'danh_muc', 'danh mục'),
            'stock' => array('stock', 'stock_quantity', 'ton_kho', 'tồn kho', 'quantity')
        );
        
        if (!isset($aliases[$field])) {
            return $header === $field;
        }
        
        return in_array($header, $aliases[$field]);
    }
    
    /**
     * Get field value from mapped data
     */
    private function get_field_value($data, $field) {
        foreach ($data as $key => $value) {
            if ($this->field_matches($key, $field)) {
                return $value;
            }
        }
        return '';
    }
}
