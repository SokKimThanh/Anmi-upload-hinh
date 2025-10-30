$htmlFiles = @(
    "03-huong-dan-chon-carbide-burr.html",
    "07-fine-cut-carbide-burr.html", 
    "08-diamond-cut-carbide-burr.html",
    "09-coarse-cut-carbide-burr.html",
    "10-chip-breaker-cut-carbide-burr.html",
    "11-carbide-burr-type-a-dang-tru.html",
    "12-carbide-burr-type-b-tru-co-dau-cat.html",
    "13-carbide-burr-type-c-tru-mui-cau.html",
    "14-carbide-burr-type-d-dang-cau.html",
    "15-carbide-burr-type-e-dang-oval.html",
    "16-carbide-burr-type-f-cay-mui-cau.html",
    "17-carbide-burr-type-g-cay-nhon.html",
    "18-carbide-burr-type-h-dang-ngon-lua.html",
    "19-carbide-burr-type-j-vat-mep-60-do.html",
    "20-carbide-burr-type-k-vat-mep-90-do.html",
    "21-carbide-burr-type-l-non-mui-cau.html",
    "22-carbide-burr-type-m-dang-non.html",
    "23-carbide-burr-type-n-non-nguoc.html",
    "24-carbide-burr-gia-cong-thep.html",
    "25-carbide-burr-gia-cong-inox.html",
    "26-carbide-burr-gia-cong-nhom.html",
    "27-carbide-burr-xu-ly-diem-han.html",
    "28-carbide-burr-vat-mep-loai-bo-bavia.html"
)

$basePath = "e:\ANMI_Dự Án bảo trì phần mềm website AnMi\Anmi-upload-hinh\HTML-baiviet\Sản phẩm\Dụng cụ liền khối\Dao đánh bavia\Seo website"

$teamImageSection = @"

    <section class="team-support">
        <figure class="wp-block-image">
            <img src="https://anmitools.com/wp-content/uploads/2025/09/trang-30_tools_diachi-editbyAI.webp" alt="Đội ngũ kỹ thuật ANMI TOOLS" width="1000" height="560" loading="lazy">
            <figcaption>Đội ngũ kỹ thuật ANMI TOOLS luôn sẵn sàng hỗ trợ bạn chọn đúng loại Carbide Burr.</figcaption>
        </figure>
    </section>
"@

foreach ($file in $htmlFiles) {
    $filePath = Join-Path $basePath $file
    if (Test-Path $filePath) {
        $content = Get-Content $filePath -Raw -Encoding UTF8
        
        # Check if team image already exists
        if ($content -notmatch "trang-30_tools_diachi-editbyAI") {
            Write-Host "Adding team image to: $file"
            
            # Replace footer pattern
            $newContent = $content -replace '(\s+)<footer class="article-footer">', "$teamImageSection`$1<footer class=`"article-footer`""
            
            # Write back to file
            Set-Content $filePath -Value $newContent -Encoding UTF8 -NoNewline
        } else {
            Write-Host "Team image already exists in: $file"
        }
    } else {
        Write-Host "File not found: $file"
    }
}

Write-Host "Completed adding team images to HTML files."