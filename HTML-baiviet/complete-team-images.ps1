# Script to add team support section to remaining HTML files
$remainingFiles = @(
    "03-huong-dan-chon-carbide-burr.html",
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
    "27-carbide-burr-xu-ly-diem-han.html"
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

$processedCount = 0
$errorCount = 0

foreach ($fileName in $remainingFiles) {
    $filePath = Join-Path $basePath $fileName
    
    if (Test-Path $filePath) {
        try {
            Write-Host "Processing: $fileName" -ForegroundColor Green
            
            # Read file content
            $content = Get-Content $filePath -Raw -Encoding UTF8
            
            # Check if team image already exists
            if ($content -match "trang-30_tools_diachi-editbyAI") {
                Write-Host "  - Team image already exists, skipping..." -ForegroundColor Yellow
                continue
            }
            
            # Find and replace pattern before footer
            $patterns = @(
                '(\s+)</section>(\s+)<footer class="article-footer">',
                '(\s+)<footer class="article-footer">',
                '(\s+)</div>(\s+)<!--'
            )
            
            $replaced = $false
            foreach ($pattern in $patterns) {
                if ($content -match $pattern) {
                    if ($pattern -eq '(\s+)</section>(\s+)<footer class="article-footer">') {
                        $newContent = $content -replace $pattern, ('$1</section>' + $teamImageSection + '$2<footer class="article-footer">')
                    } elseif ($pattern -eq '(\s+)<footer class="article-footer">') {
                        $newContent = $content -replace $pattern, ($teamImageSection + '$1<footer class="article-footer">')
                    } else {
                        $newContent = $content -replace $pattern, ('$1</section>' + $teamImageSection + '$2<!--')
                    }
                    
                    # Write back to file
                    [System.IO.File]::WriteAllText($filePath, $newContent, [System.Text.Encoding]::UTF8)
                    Write-Host "  - Successfully added team section" -ForegroundColor Green
                    $processedCount++
                    $replaced = $true
                    break
                }
            }
            
            if (-not $replaced) {
                Write-Host "  - No suitable pattern found" -ForegroundColor Red
                $errorCount++
            }
            
        } catch {
            Write-Host "  - Error processing file: $($_.Exception.Message)" -ForegroundColor Red
            $errorCount++
        }
    } else {
        Write-Host "File not found: $fileName" -ForegroundColor Red
        $errorCount++
    }
}

Write-Host "`nSummary:" -ForegroundColor Cyan
Write-Host "Processed: $processedCount files" -ForegroundColor Green
Write-Host "Errors: $errorCount files" -ForegroundColor Red
Write-Host "Total files: $($remainingFiles.Count)" -ForegroundColor White