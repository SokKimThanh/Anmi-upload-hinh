<?php
/**
 * CSV Importer Interface
 * An Mi Tools Product Import System
 */

session_start();
require_once 'product-csv-importer.php';

$importer = new ProductCSVImporter();
$message = '';
$message_type = '';
$show_stats = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Export template
    if (isset($_POST['export_template'])) {
        $importer->exportTemplate();
        exit;
    }
    
    // Import from upload
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $options = [
            'delimiter' => $_POST['delimiter'] ?? ',',
            'encoding' => $_POST['encoding'] ?? 'UTF-8',
            'update_existing' => isset($_POST['update_existing']),
            'use_previous_mapping' => isset($_POST['use_previous_mapping'])
        ];
        
        $result = $importer->importFromUpload($_FILES['csv_file'], $options);
        
        if ($result) {
            $message_type = 'success';
            $stats = $importer->getStats();
            $message = "Import thành công! ";
            $message .= "Tổng: {$stats['total']}, ";
            $message .= "Nhập mới: {$stats['imported']}, ";
            $message .= "Cập nhật: {$stats['updated']}, ";
            $message .= "Bỏ qua: {$stats['skipped']}";
            $show_stats = true;
        } else {
            $message_type = 'error';
            $errors = $importer->getErrors();
            $message = "Lỗi: " . implode(', ', $errors);
        }
    }
    
    // Import from server path
    elseif (!empty($_POST['server_path'])) {
        $options = [
            'delimiter' => $_POST['delimiter'] ?? ',',
            'encoding' => $_POST['encoding'] ?? 'UTF-8',
            'update_existing' => isset($_POST['update_existing']),
            'use_previous_mapping' => isset($_POST['use_previous_mapping'])
        ];
        
        $result = $importer->importFromServerPath($_POST['server_path'], $options);
        
        if ($result) {
            $message_type = 'success';
            $stats = $importer->getStats();
            $message = "Import thành công! ";
            $message .= "Tổng: {$stats['total']}, ";
            $message .= "Nhập mới: {$stats['imported']}, ";
            $message .= "Cập nhật: {$stats['updated']}, ";
            $message .= "Bỏ qua: {$stats['skipped']}";
            $show_stats = true;
        } else {
            $message_type = 'error';
            $errors = $importer->getErrors();
            $message = "Lỗi: " . implode(', ', $errors);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập Sản Phẩm từ CSV - An Mi Tools</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            line-height: 1.6;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 24px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        input[type="file"],
        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        input[type="file"] {
            padding: 8px;
        }
        
        .file-info {
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
        }
        
        .checkbox-group label {
            margin: 0;
            font-weight: normal;
        }
        
        .help-text {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        button {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #0066cc;
            color: white;
            flex: 1;
        }
        
        .btn-primary:hover {
            background: #0052a3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .message {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .stats-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        
        .stats-table th,
        .stats-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .stats-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .divider {
            height: 1px;
            background: #e0e0e0;
            margin: 30px 0;
        }
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }
        
        .info-box h3 {
            margin-bottom: 10px;
            font-size: 16px;
            color: #0066cc;
        }
        
        .info-box ul {
            margin-left: 20px;
        }
        
        .info-box li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Nhập Sản Phẩm từ File CSV</h1>
        <p class="subtitle">Công cụ này cho phép bạn nhập (hoặc hợp nhất) dữ liệu sản phẩm vào cửa hàng của mình từ tệp CSV hoặc TXT.</p>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($show_stats): ?>
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>Thống kê</th>
                        <th>Số lượng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stats = $importer->getStats(); ?>
                    <tr>
                        <td>Tổng số dòng đã xử lý</td>
                        <td><?php echo $stats['total']; ?></td>
                    </tr>
                    <tr>
                        <td>Sản phẩm mới được nhập</td>
                        <td><?php echo $stats['imported']; ?></td>
                    </tr>
                    <tr>
                        <td>Sản phẩm được cập nhật</td>
                        <td><?php echo $stats['updated']; ?></td>
                    </tr>
                    <tr>
                        <td>Sản phẩm bị bỏ qua</td>
                        <td><?php echo $stats['skipped']; ?></td>
                    </tr>
                </tbody>
            </table>
            
            <?php if (count($importer->getWarnings()) > 0): ?>
                <div class="info-box">
                    <h3>⚠️ Cảnh báo:</h3>
                    <ul>
                        <?php foreach ($importer->getWarnings() as $warning): ?>
                            <li><?php echo htmlspecialchars($warning); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="csv_file">Chọn tập tin CSV từ máy tính của bạn:</label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt">
                <div class="file-info">Kích thước tối đa: 256 MB</div>
            </div>
            
            <div class="checkbox-group">
                <input type="checkbox" id="update_existing" name="update_existing">
                <label for="update_existing">Cập nhật các sản phẩm hiện có</label>
            </div>
            <div class="help-text">Sản phẩm đã có khớp ID hoặc mã sản phẩm sẽ được cập nhật. Sản phẩm không tồn tại sẽ được bỏ qua.</div>
            
            <div class="divider"></div>
            
            <div class="form-group">
                <label for="server_path">Cách khác, là nhập đường dẫn đến tệp CSV trên máy chủ của bạn:</label>
                <input type="text" id="server_path" name="server_path" placeholder="/home1/mangthanhcong/anmitools.com/products.csv">
            </div>
            
            <div class="form-group">
                <label for="delimiter">Dấu phân cách CSV:</label>
                <select id="delimiter" name="delimiter">
                    <option value=",">Dấu phẩy (,)</option>
                    <option value=";">Dấu chấm phẩy (;)</option>
                    <option value="\t">Tab</option>
                    <option value="|">Dấu gạch đứng (|)</option>
                </select>
            </div>
            
            <div class="checkbox-group">
                <input type="checkbox" id="use_previous_mapping" name="use_previous_mapping">
                <label for="use_previous_mapping">Sử dụng tùy chọn ánh xạ cột trước đó?</label>
            </div>
            
            <div class="form-group">
                <label for="encoding">Mã hóa ký tự của tệp:</label>
                <select id="encoding" name="encoding">
                    <option value="UTF-8">UTF-8</option>
                    <option value="UTF-16">UTF-16</option>
                    <option value="Windows-1252">Windows-1252</option>
                    <option value="ISO-8859-1">ISO-8859-1</option>
                    <option value="auto">Tự động phát hiện</option>
                </select>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-primary">🚀 Nhập Dữ Liệu</button>
                <button type="submit" name="export_template" class="btn-success">📥 Tải File Mẫu CSV</button>
            </div>
        </form>
        
        <div class="info-box">
            <h3>📋 Hướng dẫn sử dụng:</h3>
            <ul>
                <li><strong>File CSV phải có header</strong> (dòng đầu tiên chứa tên cột)</li>
                <li>Các cột bắt buộc: <code>id</code>, <code>title</code>, <code>slug</code>, <code>category</code></li>
                <li>Các cột tùy chọn: <code>primary_keyword</code>, <code>status</code>, <code>file_md</code>, <code>file_html</code>, <code>seo_title</code>, <code>seo_description</code>, <code>tags</code>, <code>price</code>, <code>stock</code></li>
                <li>Nếu không có cột <code>slug</code>, hệ thống sẽ tự động tạo từ <code>title</code></li>
                <li>Sử dụng nút "Tải File Mẫu CSV" để xem cấu trúc file chuẩn</li>
            </ul>
        </div>
    </div>
</body>
</html>
