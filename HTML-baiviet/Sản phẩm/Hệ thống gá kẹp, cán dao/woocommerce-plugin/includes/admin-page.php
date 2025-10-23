<?php
/**
 * Admin Page - CSV Importer Interface
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap anmi-csv-importer">
    <h1>
        <span class="dashicons dashicons-upload"></span>
        Nhập Sản Phẩm từ CSV
    </h1>
    
    <div class="anmi-intro">
        <p>Công cụ này cho phép bạn nhập hoặc cập nhật hàng loạt sản phẩm WooCommerce từ file CSV. Hỗ trợ đầy đủ tiếng Việt.</p>
    </div>
    
    <?php settings_errors('anmi_csv_importer'); ?>
    
    <div class="anmi-container">
        <!-- Left Panel: Import Form -->
        <div class="anmi-panel anmi-panel-main">
            <div class="anmi-card">
                <h2>📁 Chọn File CSV</h2>
                
                <form id="anmi-import-form" enctype="multipart/form-data">
                    <?php wp_nonce_field('anmi_csv_importer', 'anmi_nonce'); ?>
                    
                    <div class="anmi-form-group">
                        <label for="csv_file">File CSV/TXT:</label>
                        <input type="file" 
                               id="csv_file" 
                               name="csv_file" 
                               accept=".csv,.txt" 
                               required>
                        <p class="description">
                            Kích thước tối đa: <?php echo size_format(wp_max_upload_size()); ?>
                        </p>
                    </div>
                    
                    <div class="anmi-divider"></div>
                    
                    <h3>⚙️ Tùy Chọn Import</h3>
                    
                    <div class="anmi-form-group">
                        <label>
                            <input type="checkbox" name="update_existing" value="1" checked>
                            <strong>Cập nhật sản phẩm hiện có</strong>
                        </label>
                        <p class="description">
                            Sản phẩm đã có (khớp SKU hoặc slug) sẽ được cập nhật. Nếu không chọn, sản phẩm trùng sẽ bị bỏ qua.
                        </p>
                    </div>
                    
                    <div class="anmi-form-group">
                        <label>
                            <input type="checkbox" name="create_categories" value="1" checked>
                            <strong>Tự động tạo danh mục</strong>
                        </label>
                        <p class="description">
                            Tự động tạo danh mục nếu chưa tồn tại.
                        </p>
                    </div>
                    
                    <div class="anmi-form-group">
                        <label>
                            <input type="checkbox" name="update_price" value="1" checked>
                            <strong>Cập nhật giá</strong>
                        </label>
                        <p class="description">
                            Cập nhật giá cho sản phẩm đã tồn tại.
                        </p>
                    </div>
                    
                    <div class="anmi-form-group">
                        <label>
                            <input type="checkbox" name="update_stock" value="1" checked>
                            <strong>Cập nhật tồn kho</strong>
                        </label>
                        <p class="description">
                            Cập nhật số lượng tồn kho cho sản phẩm đã tồn tại.
                        </p>
                    </div>
                    
                    <div class="anmi-divider"></div>
                    
                    <h3>🔧 Cấu Hình CSV</h3>
                    
                    <div class="anmi-form-row">
                        <div class="anmi-form-group">
                            <label for="delimiter">Dấu phân cách:</label>
                            <select id="delimiter" name="delimiter">
                                <option value=",">Dấu phẩy (,)</option>
                                <option value=";">Dấu chấm phẩy (;)</option>
                                <option value="\t">Tab</option>
                                <option value="|">Dấu gạch đứng (|)</option>
                            </select>
                        </div>
                        
                        <div class="anmi-form-group">
                            <label for="encoding">Mã hóa:</label>
                            <select id="encoding" name="encoding">
                                <option value="UTF-8">UTF-8</option>
                                <option value="UTF-16">UTF-16</option>
                                <option value="Windows-1252">Windows-1252</option>
                                <option value="ISO-8859-1">ISO-8859-1</option>
                                <option value="auto">Tự động phát hiện</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="anmi-divider"></div>
                    
                    <div class="anmi-actions">
                        <button type="button" id="validate-btn" class="button button-secondary">
                            🔍 Kiểm Tra File
                        </button>
                        <button type="submit" id="import-btn" class="button button-primary button-large">
                            🚀 Bắt Đầu Import
                        </button>
                    </div>
                </form>
                
                <!-- Progress Bar -->
                <div id="import-progress" class="anmi-progress" style="display:none;">
                    <div class="anmi-progress-bar">
                        <div class="anmi-progress-fill"></div>
                    </div>
                    <p class="anmi-progress-text">Đang xử lý...</p>
                </div>
                
                <!-- Results -->
                <div id="import-results" class="anmi-results" style="display:none;">
                    <!-- Results will be inserted here via JavaScript -->
                </div>
            </div>
        </div>
        
        <!-- Right Panel: Help & Info -->
        <div class="anmi-panel anmi-panel-sidebar">
            
            <!-- Quick Actions -->
            <div class="anmi-card">
                <h3>⚡ Thao Tác Nhanh</h3>
                <div class="anmi-quick-actions">
                    <a href="#" id="export-template-btn" class="button button-secondary button-block">
                        📥 Tải File Mẫu CSV
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="button button-block">
                        📦 Xem Sản Phẩm
                    </a>
                    <a href="<?php echo admin_url('edit-tags.php?taxonomy=product_cat&post_type=product'); ?>" class="button button-block">
                        📂 Quản Lý Danh Mục
                    </a>
                </div>
            </div>
            
            <!-- Help Box -->
            <div class="anmi-card anmi-card-info">
                <h3>📋 Cấu Trúc File CSV</h3>
                
                <h4>Các cột bắt buộc:</h4>
                <ul>
                    <li><code>name</code> - Tên sản phẩm</li>
                </ul>
                
                <h4>Các cột khuyến nghị:</h4>
                <ul>
                    <li><code>sku</code> - Mã sản phẩm (duy nhất)</li>
                    <li><code>slug</code> - URL slug</li>
                    <li><code>regular_price</code> - Giá gốc</li>
                    <li><code>sale_price</code> - Giá khuyến mãi</li>
                    <li><code>stock_quantity</code> - Số lượng tồn</li>
                    <li><code>categories</code> - Danh mục (phân cách bằng dấu phẩy)</li>
                    <li><code>tags</code> - Thẻ (phân cách bằng dấu phẩy)</li>
                    <li><code>description</code> - Mô tả chi tiết</li>
                    <li><code>short_description</code> - Mô tả ngắn</li>
                    <li><code>images</code> - URL hình ảnh (phân cách bằng dấu phẩy)</li>
                    <li><code>status</code> - Trạng thái (publish/draft/pending)</li>
                    <li><code>attributes</code> - Thuộc tính (Size:S,M,L|Color:Red,Blue)</li>
                </ul>
                
                <h4>Hỗ trợ tiếng Việt:</h4>
                <p class="description">
                    Bạn có thể dùng tên cột tiếng Việt như: 
                    <code>tên</code>, <code>mã</code>, <code>giá</code>, 
                    <code>tồn kho</code>, <code>danh mục</code>, v.v.
                </p>
            </div>
            
            <!-- Tips Box -->
            <div class="anmi-card anmi-card-warning">
                <h3>💡 Mẹo Sử Dụng</h3>
                <ul>
                    <li>Luôn <strong>backup database</strong> trước khi import</li>
                    <li>Dùng nút <strong>"Kiểm Tra File"</strong> trước khi import</li>
                    <li>Test với file nhỏ (5-10 sản phẩm) trước</li>
                    <li>Sử dụng <strong>UTF-8</strong> cho file có tiếng Việt</li>
                    <li>Để trống SKU nếu không muốn cập nhật sản phẩm cũ</li>
                    <li>URL hình ảnh phải là đường dẫn đầy đủ (http://...)</li>
                </ul>
            </div>
            
            <!-- Stats Box -->
            <div class="anmi-card anmi-card-stats">
                <h3>📊 Thống Kê</h3>
                <?php
                $product_count = wp_count_posts('product');
                $category_count = wp_count_terms(array('taxonomy' => 'product_cat', 'hide_empty' => false));
                ?>
                <div class="anmi-stat-item">
                    <span class="anmi-stat-label">Tổng sản phẩm:</span>
                    <span class="anmi-stat-value"><?php echo number_format_i18n($product_count->publish); ?></span>
                </div>
                <div class="anmi-stat-item">
                    <span class="anmi-stat-label">Nháp:</span>
                    <span class="anmi-stat-value"><?php echo number_format_i18n($product_count->draft); ?></span>
                </div>
                <div class="anmi-stat-item">
                    <span class="anmi-stat-label">Danh mục:</span>
                    <span class="anmi-stat-value"><?php echo number_format_i18n($category_count); ?></span>
                </div>
            </div>
            
        </div>
    </div>
</div>
