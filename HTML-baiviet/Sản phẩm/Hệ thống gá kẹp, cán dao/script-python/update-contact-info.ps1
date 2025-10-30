# Script để cập nhật thông tin liên hệ vào tất cả file HTML
# Thêm thông tin 2 văn phòng (Hà Nội + TP.HCM) vào sau CTA buttons

$contactInfoBlock = @"
    
    <div class="contact-info">
      <div class="office">
        <h3>🏢 Hà Nội</h3>
        <p>Suite 409, CT4 Building, Song Da Urban Area, Me Tri Street, Nam Tu Liem District</p>
        <p>☎️ Tel: <a href="tel:+842435562635">+84 24 3556 2635</a></p>
      </div>
      <div class="office">
        <h3>🏢 TP. Hồ Chí Minh</h3>
        <p>75 Do Xuan Hop, W. Phuoc Long B, Thu Duc</p>
        <p>☎️ Tel: <a href="tel:+842862623959">+84 28 6262 3959</a></p>
      </div>
    </div>
"@

$htmlDir = "e:\ANMI_Dự Án bảo trì phần mềm website AnMi\Anmi-upload-hinh\HTML-baiviet\Sản phẩm\Hệ thống gá kẹp, cán dao\seo html"

# Lấy danh sách file từ 17-40
$files = @()
17..40 | ForEach-Object {
    $fileNum = "{0:D2}" -f $_  # Format thành 2 chữ số: 17 -> "17", 9 -> "09"
    $foundFiles = Get-ChildItem -Path $htmlDir -Filter "$fileNum-*.html" -ErrorAction SilentlyContinue
    if ($foundFiles) {
        $files += $foundFiles
    }
}

$totalFiles = $files.Count
$processedFiles = 0

Write-Host "Tìm thấy $totalFiles files cần cập nhật..." -ForegroundColor Green

foreach ($file in $files) {
    try {
        $content = Get-Content -Path $file.FullName -Raw -Encoding UTF8
        
        # Kiểm tra xem đã có contact-info chưa
        if ($content -match '<div class="contact-info">') {
            Write-Host "[SKIP] $($file.Name) - Đã có contact-info" -ForegroundColor Yellow
            continue
        }
        
        # Tìm và thay thế: chèn contact-info sau </div> của contact-cta và trước <figure class="contact-image">
        $pattern = '(</div>\s*)\n(\s*<figure class="contact-image">)'
        
        if ($content -match $pattern) {
            $newContent = $content -replace $pattern, "`$1$contactInfoBlock`n`$2"
            Set-Content -Path $file.FullName -Value $newContent -Encoding UTF8 -NoNewline
            
            $processedFiles++
            Write-Host "[OK] $($file.Name) - Đã thêm contact-info" -ForegroundColor Green
        } else {
            Write-Host "[ERROR] $($file.Name) - Không tìm thấy pattern phù hợp" -ForegroundColor Red
        }
        
    } catch {
        Write-Host "[ERROR] $($file.Name) - $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "`n✅ Hoàn thành! Đã cập nhật $processedFiles/$totalFiles files" -ForegroundColor Green
