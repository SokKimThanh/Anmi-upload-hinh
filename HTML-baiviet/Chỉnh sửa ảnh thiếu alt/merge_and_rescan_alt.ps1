$ErrorActionPreference = 'Stop'

$root = "h:\Dự Án bảo trì phần mềm website\Anmi-upload-hinh\HTML-baiviet"
$outDir = Join-Path $root "Chỉnh sửa ảnh thiếu alt"
$final = Join-Path $outDir "BAO_CAO_TONG_HOP_HTML_ALT_HOAN_CHINH.txt"

$targets = @(
    "Sản phẩm",
    "Menu-Contact",
    "Giải pháp theo ngành",
    "Tải về",
    "Tin tức và truyền thông",
    "trang chu",
    "Tuyển dụng nhân viên",
    "Về chúng tôi"
)

$targetPaths = @()
foreach ($t in $targets) {
    $p = Join-Path $root $t
    if (Test-Path $p) { $targetPaths += $p }
}

$files = foreach ($tp in $targetPaths) {
    Get-ChildItem -Path $tp -Recurse -File -ErrorAction SilentlyContinue |
        Where-Object { $_.Extension -in '.html', '.htm' }
}
$files = $files | Sort-Object FullName -Unique

$imgRegex = [regex]::new('<img\s+([^>]*?)>', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase -bor [System.Text.RegularExpressions.RegexOptions]::Singleline)

$missingByFile = @{}
$missingTotal = 0

foreach ($f in $files) {
    $content = Get-Content -Path $f.FullName -Raw -Encoding UTF8
    $imgMatches = $imgRegex.Matches($content)
    $items = @()

    foreach ($m in $imgMatches) {
        $attrs = $m.Groups[1].Value
        $hasAltAttr = $attrs -match '(?is)\salt\s*='
        $hasMeaningfulAlt = $false
        $altVal = $null

        if ($hasAltAttr) {
            if ($attrs -match '(?is)\salt\s*=\s*"([^"]*)"') {
                $altVal = $Matches[1]
            } elseif ($attrs -match "(?is)\salt\s*=\s*'([^']*)'") {
                $altVal = $Matches[1]
            } elseif ($attrs -match '(?is)\salt\s*=\s*([^\s>]+)') {
                $altVal = $Matches[1]
            }

            if ($null -ne $altVal -and $altVal.Trim().Length -gt 0) {
                $hasMeaningfulAlt = $true
            }
        }

        if (-not $hasMeaningfulAlt) {
            $src = 'N/A'
            if ($attrs -match '(?is)\ssrc\s*=\s*"([^"]*)"') {
                $src = $Matches[1]
            } elseif ($attrs -match "(?is)\ssrc\s*=\s*'([^']*)'") {
                $src = $Matches[1]
            }
            $items += [pscustomobject]@{
                Src = $src
                Alt = $altVal
            }
        }
    }

    if ($items.Count -gt 0) {
        $rel = $f.FullName.Replace($root + '\', '')
        $missingByFile[$rel] = $items
        $missingTotal += $items.Count
    }
}

$groups = $files | Group-Object DirectoryName | Sort-Object Name
$seoWebsiteCount = ($files | Where-Object { $_.FullName -match '(?i)seo\s+website' }).Count
$seoHtmlCount = ($files | Where-Object { $_.FullName -match '(?i)seo\s+html' }).Count

$lines = @()
$lines += "BAO CAO TONG HOP HTML VA ANH THIEU ALT"
$lines += "Ngay tao: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
$lines += ""
$lines += "I. PHAM VI QUET"
foreach ($tp in $targetPaths) {
    $lines += " - " + $tp.Replace($root + '\', '')
}

$lines += ""
$lines += "II. THONG KE"
$lines += " - Tong file HTML/HTM: $($files.Count)"
$lines += " - File trong thu muc SEO WEBSITE: $seoWebsiteCount"
$lines += " - File trong thu muc SEO HTML: $seoHtmlCount"
$lines += " - So file co anh can sua alt: $($missingByFile.Keys.Count)"
$lines += " - Tong anh can sua alt: $missingTotal"

$lines += ""
$lines += "III. DANH SACH FILE HTML THEO THU MUC"
foreach ($g in $groups) {
    $relDir = $g.Name.Replace($root + '\', '')
    $lines += ""
    $lines += "[$relDir] - $($g.Count) file"
    foreach ($f in ($g.Group | Sort-Object Name)) {
        $lines += " - $($f.Name)"
    }
}

$lines += ""
$lines += "IV. DANH SACH ANH CAN SUA ALT"
if ($missingByFile.Keys.Count -eq 0) {
    $lines += " - Khong tim thay anh can sua alt trong pham vi quet."
} else {
    foreach ($k in ($missingByFile.Keys | Sort-Object)) {
        $items = $missingByFile[$k]
        $lines += ""
        $lines += "[$k] - $($items.Count) anh"

        $i = 1
        foreach ($it in $items) {
            if ($null -eq $it.Alt) {
                $altText = "KHONG CO ALT"
            } elseif (($it.Alt).Trim().Length -eq 0) {
                $altText = "alt rong"
            } else {
                $altText = "alt co noi dung"
            }
            $lines += " - #$i | $($it.Src) | $altText"
            $i++
        }
    }
}

$legacyCandidates = @(
    (Join-Path $root "Danh_sach_anh_thieu_alt_TOAN_DIEN.txt"),
    (Join-Path $root "Danh_sach_file_html_theo_thu_muc.txt"),
    (Join-Path $outDir "Danh_sach_anh_thieu_alt.txt"),
    (Join-Path $outDir "chỉnh sửa ảnh thiếu alt.txt")
)
$legacyExisting = $legacyCandidates | Where-Object { Test-Path $_ }

$lines += ""
$lines += "V. NOI DUNG GOM TU BAO CAO TRUOC"
if ($legacyExisting.Count -eq 0) {
    $lines += " - Khong co file bao cao cu de gom."
} else {
    foreach ($lf in $legacyExisting) {
        $lines += ""
        $lines += "----- BAT DAU FILE: $($lf.Replace($root + '\', '')) -----"
        $lines += (Get-Content -Path $lf -Encoding UTF8)
        $lines += "----- KET THUC FILE: $($lf.Replace($root + '\', '')) -----"
    }
}

Set-Content -Path $final -Value $lines -Encoding UTF8

# Xoa cac file bao cao rieng le sau khi da gom
$toDelete = @(
    (Join-Path $root "Danh_sach_anh_thieu_alt_TOAN_DIEN.txt"),
    (Join-Path $root "Danh_sach_file_html_theo_thu_muc.txt"),
    (Join-Path $outDir "Danh_sach_anh_thieu_alt.txt")
)

$deleted = @()
foreach ($d in $toDelete) {
    if (Test-Path $d) {
        Remove-Item -Path $d -Force
        $deleted += $d
    }
}

Write-Output "Created: $final"
Write-Output "HTML files: $($files.Count)"
Write-Output "Missing-alt files: $($missingByFile.Keys.Count)"
Write-Output "Missing-alt images: $missingTotal"
Write-Output "Legacy merged files: $($legacyExisting.Count)"
Write-Output "Deleted old report files: $($deleted.Count)"
foreach ($d in $deleted) { Write-Output " - $d" }
