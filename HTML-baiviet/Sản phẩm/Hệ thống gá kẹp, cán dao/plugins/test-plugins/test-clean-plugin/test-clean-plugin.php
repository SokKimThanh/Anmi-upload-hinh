<?php
/**
 * Plugin Name: Test Clean Plugin
 * Description: Plugin test sạch không có lỗi - dùng để test upload thành công
 * Version: 1.0.0
 * Author: Anmi Test
 */

// Simple clean plugin
add_action('admin_notices', function() {
    echo '<div class="notice notice-info"><p>Test Clean Plugin is active!</p></div>';
});
