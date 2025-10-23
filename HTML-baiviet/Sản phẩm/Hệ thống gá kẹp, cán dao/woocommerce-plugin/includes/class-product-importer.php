<?php
/**
 * Product Importer Class
 * Import products into WooCommerce
 */

if (!defined('ABSPATH')) {
    exit;
}

class AnMi_Product_Importer {
    
    private $stats = array(
        'total' => 0,
        'imported' => 0,
        'updated' => 0,
        'skipped' => 0,
        'failed' => 0
    );
    
    private $errors = array();
    private $warnings = array();
    
    /**
     * Import from uploaded file
     */
    public function import_from_upload($file, $options = array()) {
        // Validate upload
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return array(
                'success' => false,
                'message' => 'File upload không hợp lệ'
            );
        }
        
        // Process CSV
        $processor = new AnMi_CSV_Processor();
        $products = $processor->parse_csv($file['tmp_name'], $options);
        
        if (empty($products)) {
            return array(
                'success' => false,
                'message' => 'Không có dữ liệu sản phẩm trong file CSV'
            );
        }
        
        // Import products
        foreach ($products as $product_data) {
            $this->stats['total']++;
            $this->import_product($product_data, $options);
        }
        
        return array(
            'success' => true,
            'message' => 'Import hoàn tất',
            'stats' => $this->stats,
            'errors' => $this->errors,
            'warnings' => $this->warnings
        );
    }
    
    /**
     * Import single product
     */
    private function import_product($data, $options) {
        try {
            // Check if product exists
            $product_id = $this->find_product($data);
            
            if ($product_id && !$options['update_existing']) {
                $this->stats['skipped']++;
                $this->warnings[] = sprintf('Sản phẩm "%s" đã tồn tại, bỏ qua', $data['name']);
                return;
            }
            
            // Create or update product
            if ($product_id) {
                $product = wc_get_product($product_id);
                $this->stats['updated']++;
                $message_type = 'cập nhật';
            } else {
                $product = new WC_Product_Simple();
                $this->stats['imported']++;
                $message_type = 'nhập mới';
            }
            
            // Set basic data
            $product->set_name($data['name']);
            $product->set_slug($data['slug']);
            $product->set_description($data['description'] ?? '');
            $product->set_short_description($data['short_description'] ?? '');
            
            // Set SKU
            if (!empty($data['sku'])) {
                $product->set_sku($data['sku']);
            }
            
            // Set price
            if ($options['update_price'] || !$product_id) {
                if (!empty($data['regular_price'])) {
                    $product->set_regular_price($data['regular_price']);
                }
                if (!empty($data['sale_price'])) {
                    $product->set_sale_price($data['sale_price']);
                }
            }
            
            // Set stock
            if ($options['update_stock'] || !$product_id) {
                if (isset($data['stock_quantity'])) {
                    $product->set_manage_stock(true);
                    $product->set_stock_quantity($data['stock_quantity']);
                    $product->set_stock_status($data['stock_quantity'] > 0 ? 'instock' : 'outofstock');
                }
            }
            
            // Set categories
            if (!empty($data['categories'])) {
                $category_ids = $this->get_category_ids($data['categories'], $options['create_categories']);
                $product->set_category_ids($category_ids);
            }
            
            // Set tags
            if (!empty($data['tags'])) {
                $tag_ids = $this->get_tag_ids($data['tags']);
                $product->set_tag_ids($tag_ids);
            }
            
            // Set images
            if (!empty($data['images'])) {
                $this->set_product_images($product, $data['images']);
            }
            
            // Set attributes
            if (!empty($data['attributes'])) {
                $this->set_product_attributes($product, $data['attributes']);
            }
            
            // Set meta data
            if (!empty($data['meta'])) {
                foreach ($data['meta'] as $key => $value) {
                    $product->update_meta_data($key, $value);
                }
            }
            
            // Set status
            $product->set_status($data['status'] ?? 'publish');
            
            // Save product
            $product->save();
            
        } catch (Exception $e) {
            $this->stats['failed']++;
            $this->errors[] = sprintf('Lỗi sản phẩm "%s": %s', $data['name'] ?? 'N/A', $e->getMessage());
        }
    }
    
    /**
     * Find existing product by SKU or name
     */
    private function find_product($data) {
        // Try by SKU first
        if (!empty($data['sku'])) {
            $product_id = wc_get_product_id_by_sku($data['sku']);
            if ($product_id) {
                return $product_id;
            }
        }
        
        // Try by slug
        if (!empty($data['slug'])) {
            $post = get_page_by_path($data['slug'], OBJECT, 'product');
            if ($post) {
                return $post->ID;
            }
        }
        
        // Try by name
        if (!empty($data['name'])) {
            $posts = get_posts(array(
                'post_type' => 'product',
                'title' => $data['name'],
                'posts_per_page' => 1,
                'fields' => 'ids'
            ));
            if (!empty($posts)) {
                return $posts[0];
            }
        }
        
        return false;
    }
    
    /**
     * Get category IDs from names
     */
    private function get_category_ids($categories, $create_if_not_exists = true) {
        $category_ids = array();
        $category_names = array_map('trim', explode(',', $categories));
        
        foreach ($category_names as $category_name) {
            if (empty($category_name)) continue;
            
            $term = get_term_by('name', $category_name, 'product_cat');
            
            if (!$term && $create_if_not_exists) {
                $result = wp_insert_term($category_name, 'product_cat');
                if (!is_wp_error($result)) {
                    $category_ids[] = $result['term_id'];
                }
            } elseif ($term) {
                $category_ids[] = $term->term_id;
            }
        }
        
        return $category_ids;
    }
    
    /**
     * Get tag IDs from names
     */
    private function get_tag_ids($tags) {
        $tag_ids = array();
        $tag_names = array_map('trim', explode(',', $tags));
        
        foreach ($tag_names as $tag_name) {
            if (empty($tag_name)) continue;
            
            $term = get_term_by('name', $tag_name, 'product_tag');
            
            if (!$term) {
                $result = wp_insert_term($tag_name, 'product_tag');
                if (!is_wp_error($result)) {
                    $tag_ids[] = $result['term_id'];
                }
            } else {
                $tag_ids[] = $term->term_id;
            }
        }
        
        return $tag_ids;
    }
    
    /**
     * Set product images
     */
    private function set_product_images($product, $images_string) {
        $image_urls = array_map('trim', explode(',', $images_string));
        $image_ids = array();
        
        foreach ($image_urls as $idx => $image_url) {
            if (empty($image_url)) continue;
            
            $image_id = $this->upload_image($image_url);
            if ($image_id) {
                $image_ids[] = $image_id;
            }
        }
        
        if (!empty($image_ids)) {
            // First image is featured
            $product->set_image_id($image_ids[0]);
            
            // Rest are gallery
            if (count($image_ids) > 1) {
                $product->set_gallery_image_ids(array_slice($image_ids, 1));
            }
        }
    }
    
    /**
     * Upload image from URL
     */
    private function upload_image($image_url) {
        // Check if it's a local URL or external
        if (filter_var($image_url, FILTER_VALIDATE_URL)) {
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            
            $image_id = media_sideload_image($image_url, 0, null, 'id');
            
            if (!is_wp_error($image_id)) {
                return $image_id;
            }
        }
        
        return false;
    }
    
    /**
     * Set product attributes
     */
    private function set_product_attributes($product, $attributes_string) {
        $attributes_array = array();
        $attributes_pairs = explode('|', $attributes_string);
        
        foreach ($attributes_pairs as $pair) {
            if (strpos($pair, ':') === false) continue;
            
            list($name, $values) = explode(':', $pair, 2);
            $name = trim($name);
            $values = array_map('trim', explode(',', $values));
            
            $attribute = new WC_Product_Attribute();
            $attribute->set_name($name);
            $attribute->set_options($values);
            $attribute->set_visible(true);
            $attribute->set_variation(false);
            
            $attributes_array[] = $attribute;
        }
        
        if (!empty($attributes_array)) {
            $product->set_attributes($attributes_array);
        }
    }
}
