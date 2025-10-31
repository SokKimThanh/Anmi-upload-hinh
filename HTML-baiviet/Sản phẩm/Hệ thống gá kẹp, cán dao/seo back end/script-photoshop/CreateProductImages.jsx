/*
================================================================================
ANMI TOOLS - PRODUCT IMAGE CREATOR FOR PHOTOSHOP
Tạo cấu trúc thư mục và file PSD cho sản phẩm
================================================================================

Chức năng:
- Tạo thư mục theo tên sản phẩm
- Tạo file PSD 1000x1000px nền trắng
- Tạo 2 thư mục con: anh-thu-vien, anh-bai-viet
- Xử lý theo batch để tránh quá tải

Cách sử dụng:
1. Mở Photoshop
2. File > Scripts > Browse...
3. Chọn file này và chạy
4. Làm theo hướng dẫn trên màn hình

Version: 1.0
Date: 2025-10-29
================================================================================
*/

// ============================================================================
// CẤU HÌNH
// ============================================================================

var CONFIG = {
    // Kích thước ảnh (pixels)
    imageWidth: 1000,
    imageHeight: 1000,
    
    // DPI
    resolution: 300,
    
    // Color mode (RGB)
    colorMode: NewDocumentMode.RGB,
    
    // Bit depth
    bitsPerChannel: BitsPerChannelType.EIGHT,
    
    // Batch size (số sản phẩm xử lý mỗi lần)
    batchSize: 10,
    
    // Tên thư mục con
    subfolders: ["anh-thu-vien", "anh-bai-viet"]
};

// ============================================================================
// DANH SÁCH SẢN PHẨM
// ============================================================================

var PRODUCTS = [
    "BT-SK High Speed Tool Holder",
    "BT-GER High Speed ER Collet Chuck",
    "BT-HGER High Speed ER Collet Chuck",
    "BT-ER Collet Chuck Standard",
    "BT-C Power Chuck Tool Holder",
    "BT-OZ Heavy Duty Tool Holder",
    "BT-APU Drill Chuck Holder",
    "BT-FMA Face Milling Arbor",
    "BT-FMB Face Milling Arbor",
    "BT-SLA Weldon Tool Holder",
    "BT-MTA-MTB Morse Taper",
    "BT-SLO-ERO Oil-Feed Tool Holder",
    "BT-SDC High Precision Tool Holder",
    "BT-SR Shrink Fit Chuck",
    "BT-HS Hydraulic Chuck",
    "BT Tension-Compression Tapping Holder",
    "HSK-SR Shrink Fit Chuck",
    "HSK-ER Tool Holder",
    "HSK-GSK Tool Holder",
    "HSK-HS Hydraulic Chuck",
    "HSK-FMB Face Milling Arbor",
    "HSK-SLA Weldon Tool Holder",
    "HSK-APU Drill Chuck Holder",
    "HSK-C Power Chuck",
    "NBH2084 Micro Boring System",
    "NBJ16 Micro Boring System",
    "EWN Micro Boring Head",
    "RBH Adjustable Rough Boring Head",
    "CBH Large-Diameter Fine Boring Head",
    "CBS 45-90 Boring Tool",
    "SB Fixed Diameter Boring Cutter",
    "GC Fixed Diameter Boring Cutter",
    "CK-LBK Boring Bar",
    "TCK-BST Boring System",
    "BST Twin-Blade Boring Tool",
    "ER Collet",
    "SK Collet",
    "C Power Chuck Collet",
    "OZ Collet"
];

// ============================================================================
// HÀM TIỆN ÍCH
// ============================================================================

/**
 * Chuyển đổi tên sản phẩm thành tên file hợp lệ
 */
function sanitizeFilename(name) {
    var filename = name;
    
    // Thay thế các ký tự đặc biệt
    filename = filename.replace(/\//g, "-");
    filename = filename.replace(/\(/g, "");
    filename = filename.replace(/\)/g, "");
    filename = filename.replace(/,/g, "");
    filename = filename.replace(/\s+/g, "-");
    filename = filename.replace(/°/g, "");
    
    // Chuyển thành chữ thường
    filename = filename.toLowerCase();
    
    // Loại bỏ dấu gạch ngang kép
    filename = filename.replace(/-+/g, "-");
    
    // Loại bỏ dấu gạch ngang ở đầu và cuối
    filename = filename.replace(/^-|-$/g, "");
    
    return filename;
}

/**
 * Tạo thư mục (nếu chưa tồn tại)
 */
function createFolder(folderPath) {
    var folder = new Folder(folderPath);
    if (!folder.exists) {
        folder.create();
    }
    return folder;
}

/**
 * Kiểm tra quyền ghi vào thư mục
 */
function checkWritePermission(folderPath) {
    try {
        var testFile = new File(folderPath + "/test_write_permission.tmp");
        testFile.open("w");
        testFile.write("test");
        testFile.close();
        testFile.remove();
        return true;
    } catch (e) {
        return false;
    }
}

/**
 * Tạo file PSD với nền trắng
 */
function createWhitePSD(savePath, productName) {
    try {
        // Tạo document mới
        var doc = app.documents.add(
            UnitValue(CONFIG.imageWidth, "px"),
            UnitValue(CONFIG.imageHeight, "px"),
            CONFIG.resolution,
            productName,
            CONFIG.colorMode,
            DocumentFill.WHITE,
            1.0,
            CONFIG.bitsPerChannel
        );
        
        // Lưu file PSD
        var psdFile = new File(savePath);
        var psdSaveOptions = new PhotoshopSaveOptions();
        psdSaveOptions.alphaChannels = true;
        psdSaveOptions.layers = true;
        psdSaveOptions.embedColorProfile = true;
        
        doc.saveAs(psdFile, psdSaveOptions, true);
        
        // Đóng document
        doc.close(SaveOptions.DONOTSAVECHANGES);
        
        return true;
    } catch (e) {
        alert("Lỗi khi tạo file PSD: " + e.message);
        return false;
    }
}

/**
 * Tạo cấu trúc thư mục và file cho một sản phẩm
 */
function createProductStructure(baseFolder, productName) {
    try {
        // Tên thư mục sản phẩm
        var productFolderName = sanitizeFilename(productName);
        var productFolderPath = baseFolder + "/" + productFolderName;
        
        // Tạo thư mục chính của sản phẩm
        var productFolder = createFolder(productFolderPath);
        
        // Tạo 2 thư mục con
        for (var i = 0; i < CONFIG.subfolders.length; i++) {
            createFolder(productFolderPath + "/" + CONFIG.subfolders[i]);
        }
        
        // Tạo file PSD trong thư mục chính
        var psdPath = productFolderPath + "/" + productFolderName + ".psd";
        var success = createWhitePSD(psdPath, productName);
        
        return success;
    } catch (e) {
        alert("Lỗi khi tạo cấu trúc cho sản phẩm '" + productName + "': " + e.message);
        return false;
    }
}

/**
 * Hiển thị hộp thoại chọn thư mục
 */
function selectBaseFolder() {
    var folder = Folder.selectDialog(
        "Chọn thư mục gốc để lưu các sản phẩm:\n" +
        "Script sẽ tạo cấu trúc thư mục và file PSD cho " + PRODUCTS.length + " sản phẩm."
    );
    return folder;
}

/**
 * Hiển thị progress bar
 */
function createProgressBar(title, maxValue) {
    var win = new Window("palette", title, undefined, {closeButton: false});
    win.progressBar = win.add("progressbar", undefined, 0, maxValue);
    win.progressBar.preferredSize = [400, 20];
    win.text = win.add("statictext", undefined, "Đang chuẩn bị...");
    win.text.preferredSize = [400, 20];
    win.show();
    return win;
}

/**
 * Cập nhật progress bar
 */
function updateProgressBar(win, value, text) {
    win.progressBar.value = value;
    win.text.text = text;
    win.update();
}

// ============================================================================
// HÀM CHÍNH
// ============================================================================

/**
 * Xử lý batch sản phẩm
 */
function processBatch(baseFolder, products, startIndex, batchSize, progressWin, totalProcessed) {
    var endIndex = Math.min(startIndex + batchSize, products.length);
    var successCount = 0;
    var errorCount = 0;
    
    for (var i = startIndex; i < endIndex; i++) {
        var productName = products[i];
        var progress = totalProcessed + (i - startIndex) + 1;
        
        // Cập nhật progress bar
        updateProgressBar(
            progressWin,
            progress,
            "[" + progress + "/" + products.length + "] Đang tạo: " + productName
        );
        
        // Tạo cấu trúc cho sản phẩm
        var success = createProductStructure(baseFolder, productName);
        
        if (success) {
            successCount++;
        } else {
            errorCount++;
        }
    }
    
    return {
        processed: endIndex - startIndex,
        success: successCount,
        error: errorCount
    };
}

/**
 * Hàm main
 */
function main() {
    // Hiển thị thông tin
    var introMessage = 
        "═══════════════════════════════════════════════════════\n" +
        "   ANMI TOOLS - PRODUCT IMAGE CREATOR\n" +
        "   Tạo cấu trúc thư mục và file PSD cho sản phẩm\n" +
        "═══════════════════════════════════════════════════════\n\n" +
        "Script này sẽ tạo:\n" +
        "• " + PRODUCTS.length + " thư mục sản phẩm\n" +
        "• " + PRODUCTS.length + " file PSD (1000x1000px, nền trắng)\n" +
        "• " + (PRODUCTS.length * CONFIG.subfolders.length) + " thư mục con (" + CONFIG.subfolders.join(", ") + ")\n\n" +
        "Tổng: " + PRODUCTS.length + " sản phẩm\n" +
        "Batch size: " + CONFIG.batchSize + " sản phẩm/lần\n\n" +
        "Bạn có muốn tiếp tục?";
    
    var userConfirm = confirm(introMessage);
    if (!userConfirm) {
        alert("Đã hủy!");
        return;
    }
    
    // Chọn thư mục gốc
    var baseFolder = selectBaseFolder();
    if (!baseFolder) {
        alert("Chưa chọn thư mục. Đã hủy!");
        return;
    }
    
    var baseFolderPath = baseFolder.fsName;
    
    // Kiểm tra quyền ghi
    if (!checkWritePermission(baseFolderPath)) {
        alert("Không có quyền ghi vào thư mục này!\nVui lòng chọn thư mục khác hoặc chạy Photoshop với quyền Administrator.");
        return;
    }
    
    // Xác nhận lần cuối
    var confirmMessage = 
        "Chuẩn bị tạo " + PRODUCTS.length + " sản phẩm tại:\n\n" +
        baseFolderPath + "\n\n" +
        "Tiếp tục?";
    
    if (!confirm(confirmMessage)) {
        alert("Đã hủy!");
        return;
    }
    
    // Tạo progress bar
    var progressWin = createProgressBar("Đang tạo sản phẩm...", PRODUCTS.length);
    
    // Xử lý theo batch
    var totalSuccess = 0;
    var totalError = 0;
    var totalProcessed = 0;
    var currentIndex = 0;
    
    while (currentIndex < PRODUCTS.length) {
        var result = processBatch(
            baseFolderPath,
            PRODUCTS,
            currentIndex,
            CONFIG.batchSize,
            progressWin,
            totalProcessed
        );
        
        totalSuccess += result.success;
        totalError += result.error;
        totalProcessed += result.processed;
        currentIndex += CONFIG.batchSize;
        
        // Nghỉ ngắn giữa các batch
        $.sleep(100);
    }
    
    // Đóng progress bar
    progressWin.close();
    
    // Hiển thị kết quả
    var resultMessage = 
        "═══════════════════════════════════════════════════════\n" +
        "   HOÀN THÀNH!\n" +
        "═══════════════════════════════════════════════════════\n\n" +
        "Đã xử lý: " + totalProcessed + "/" + PRODUCTS.length + " sản phẩm\n" +
        "Thành công: " + totalSuccess + "\n" +
        (totalError > 0 ? "Lỗi: " + totalError + "\n" : "") +
        "\nĐường dẫn:\n" + baseFolderPath + "\n\n" +
        "Cấu trúc:\n" +
        "├── [tên-sản-phẩm]/\n" +
        "│   ├── anh-thu-vien/\n" +
        "│   ├── anh-bai-viet/\n" +
        "│   └── [tên-sản-phẩm].psd (1000x1000px)\n\n" +
        "🎉 Hoàn thành!";
    
    alert(resultMessage);
}

// ============================================================================
// CHẠY SCRIPT
// ============================================================================

try {
    // Tắt dialog boxes của Photoshop
    app.displayDialogs = DialogModes.NO;
    
    // Chạy hàm main
    main();
    
    // Bật lại dialog boxes
    app.displayDialogs = DialogModes.ALL;
    
} catch (e) {
    app.displayDialogs = DialogModes.ALL;
    alert("Lỗi: " + e.message + "\n\nLine: " + e.line);
}
