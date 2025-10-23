/**
 * Ẩn các cột không cần thiết trong WooCommerce Products List
 * Thêm code này vào functions.php của theme
 */

// Ẩn cột Featured Image trong Products List
add_filter('manage_product_posts_columns', 'anmi_remove_product_columns', 999);
function anmi_remove_product_columns($columns) {
    // Xóa cột Featured Image
    if (isset($columns['featured_image'])) {
        unset($columns['featured_image']);
    }
    
    // Xóa cột Thumbnail (nếu có)
    if (isset($columns['thumb'])) {
        unset($columns['thumb']);
    }
    
    // Xóa cột SEO (từ Yoast hoặc RankMath)
    if (isset($columns['wpseo-score'])) {
        unset($columns['wpseo-score']);
    }
    
    if (isset($columns['wpseo-title'])) {
        unset($columns['wpseo-title']);
    }
    
    if (isset($columns['wpseo-metadesc'])) {
        unset($columns['wpseo-metadesc']);
    }
    
    if (isset($columns['wpseo-focuskw'])) {
        unset($columns['wpseo-focuskw']);
    }
    
    // RankMath SEO
    if (isset($columns['rank_math_seo_details'])) {
        unset($columns['rank_math_seo_details']);
    }
    
    return $columns;
}

// Optional: Tùy chỉnh thứ tự cột
add_filter('manage_product_posts_columns', 'anmi_reorder_product_columns', 1000);
function anmi_reorder_product_columns($columns) {
    $new_columns = array();
    
    // Checkbox
    if (isset($columns['cb'])) {
        $new_columns['cb'] = $columns['cb'];
    }
    
    // Tên sản phẩm
    if (isset($columns['name'])) {
        $new_columns['name'] = $columns['name'];
    }
    
    // SKU
    if (isset($columns['sku'])) {
        $new_columns['sku'] = $columns['sku'];
    }
    
    // Giá
    if (isset($columns['price'])) {
        $new_columns['price'] = $columns['price'];
    }
    
    // Tồn kho
    if (isset($columns['is_in_stock'])) {
        $new_columns['is_in_stock'] = $columns['is_in_stock'];
    }
    
    // Danh mục
    if (isset($columns['product_cat'])) {
        $new_columns['product_cat'] = $columns['product_cat'];
    }
    
    // Tags
    if (isset($columns['product_tag'])) {
        $new_columns['product_tag'] = $columns['product_tag'];
    }
    
    // Date
    if (isset($columns['date'])) {
        $new_columns['date'] = $columns['date'];
    }
    
    // Các cột còn lại
    foreach ($columns as $key => $value) {
        if (!isset($new_columns[$key])) {
            $new_columns[$key] = $value;
        }
    }
    
    return $new_columns;
}
