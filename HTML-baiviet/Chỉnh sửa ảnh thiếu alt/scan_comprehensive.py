#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Comprehensive HTML scan - includes deep nested directories and seo html folders
"""

import os
import re
from pathlib import Path
from datetime import datetime
from collections import defaultdict

ROOT_DIR = Path(__file__).parent

def extract_img_tags_comprehensive(html_content):
    """Extract all img tags and check for missing/empty alt text"""
    img_pattern = r'<img\s+([^>]*)>'
    
    results = []
    for match in re.finditer(img_pattern, html_content, re.IGNORECASE | re.DOTALL):
        img_tag = match.group(0)
        attrs = match.group(1)
        
        # Check if has alt attribute with actual content
        alt_match = re.search(r'\salt=\s*(["\'])([^"\']*)\1|\salt=([^\s>]+)', attrs, re.IGNORECASE)
        
        if alt_match:
            alt_value = alt_match.group(2) if alt_match.group(2) is not None else alt_match.group(3)
            has_meaningful_alt = bool(alt_value.strip())
        else:
            has_meaningful_alt = False
            alt_value = None
        
        # Extract src
        src_match = re.search(r'src\s*=\s*(["\'])([^"\']*?)\1', attrs, re.IGNORECASE | re.DOTALL)
        src = src_match.group(2) if src_match else 'N/A'
        
        # Clean up tag for display
        tag_display = re.sub(r'\s+', ' ', img_tag)[:150]
        
        results.append({
            'img_tag': img_tag,
            'src': src,
            'has_meaningful_alt': has_meaningful_alt,
            'alt_value': alt_value,
            'line_snippet': tag_display + ('...' if len(tag_display) > 145 else '')
        })
    
    return results

def scan_all_html_files():
    """Scan ALL HTML files in all subdirectories"""
    files_with_missing_alt = {}
    total_html_files = 0
    total_images = 0
    seo_html_count = 0
    
    # Find all HTML files recursively
    all_html_files = list(ROOT_DIR.rglob('*.html'))
    print(f"🔍 Found {len(all_html_files)} HTML files total")
    
    for html_file in all_html_files:
        total_html_files += 1
        try:
            with open(html_file, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
            
            img_tags = extract_img_tags_comprehensive(content)
            total_images += len(img_tags)
            
            missing_alt_tags = [tag for tag in img_tags if not tag['has_meaningful_alt']]
            
            if missing_alt_tags:
                relative_path = html_file.relative_to(ROOT_DIR)
                files_with_missing_alt[str(relative_path)] = {
                    'images': missing_alt_tags,
                    'is_seo_html': 'seo html' in str(relative_path).lower() or 'seo backend' in str(relative_path).lower()
                }
                
                if 'seo html' in str(relative_path).lower():
                    seo_html_count += 1
        
        except Exception as e:
            pass
    
    return files_with_missing_alt, total_html_files, total_images, seo_html_count

def generate_comprehensive_report(files_data):
    """Generate detailed report"""
    report_lines = []
    report_lines.append("=" * 90)
    report_lines.append("BÁOCÁO TOÀN DIỆN - SCAN ẢNH THIẾU ALT TEXT")
    report_lines.append("=" * 90)
    report_lines.append(f"Ngày tạo: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    report_lines.append(f"Phạm vi: Tất cả thư mục con (bao gồm thư mục 'seo html' sâu)")
    report_lines.append("")
    
    total_files = len(files_data['files'])
    total_images = sum(len(f['images']) for f in files_data['files'].values())
    seo_html_files = sum(1 for f in files_data['files'].values() if f['is_seo_html'])
    seo_html_images = sum(len(f['images']) for f in files_data['files'].values() if f['is_seo_html'])
    
    report_lines.append(f"📊 THỐNG KÊ TỔNG QUAN:")
    report_lines.append(f"   • Tổng file HTML scanned: {files_data['total_html_files']}")
    report_lines.append(f"   • Tổng ảnh trong tất cả HTML: {files_data['total_images']}")
    report_lines.append(f"   • File HTML có ảnh cần sửa: {total_files}")
    report_lines.append(f"   • Tổng ảnh cần sửa: {total_images}")
    report_lines.append("")
    report_lines.append(f"🔧 THỐNG KÊ SỬA CHỮA:")
    report_lines.append(f"   • File 'seo html' cần sửa: {seo_html_files}")
    report_lines.append(f"   • Ảnh trong 'seo html' cần sửa: {seo_html_images}")
    report_lines.append("")
    report_lines.append("=" * 90)
    report_lines.append("")
    
    # Separate seo html and others
    seo_files = {k: v for k, v in files_data['files'].items() if v['is_seo_html']}
    other_files = {k: v for k, v in files_data['files'].items() if not v['is_seo_html']}
    
    # SEO HTML files first
    if seo_files:
        report_lines.append("🔴 THỨ NHẤT - THỨ MỤC 'SEO HTML' (ưu tiên cao)")
        report_lines.append("-" * 90)
        report_lines.append("")
        
        for file_idx, (file_path, file_info) in enumerate(sorted(seo_files.items()), 1):
            missing_imgs = file_info['images']
            report_lines.append(f"[{file_idx}] 📄 {file_path}")
            report_lines.append(f"     Ảnh cần sửa: {len(missing_imgs)}")
            report_lines.append("")
            
            for img_idx, img in enumerate(missing_imgs, 1):
                report_lines.append(f"     ✗ Ảnh #{img_idx}")
                report_lines.append(f"       📷 URL: {img['src']}")
                if img['alt_value'] is not None:
                    report_lines.append(f"       ⚠️  Alt: alt=\"\" (rỗng - cần bổ sung)")
                else:
                    report_lines.append(f"       ⚠️  Alt: KHÔNG CÓ (cần thêm)")
                report_lines.append("")
            
            report_lines.append("-" * 90)
            report_lines.append("")
    
    # Other files
    if other_files:
        report_lines.append("🟡 THỨ HAI - CÁC FILE KHÁC")
        report_lines.append("-" * 90)
        report_lines.append("")
        
        for file_idx, (file_path, file_info) in enumerate(sorted(other_files.items()), 1):
            missing_imgs = file_info['images']
            report_lines.append(f"[{file_idx}] 📄 {file_path}")
            report_lines.append(f"     Ảnh cần sửa: {len(missing_imgs)}")
            report_lines.append("")
            
            for img_idx, img in enumerate(missing_imgs, 1):
                report_lines.append(f"     ✗ Ảnh #{img_idx}")
                report_lines.append(f"       📷 URL: {img['src']}")
                if img['alt_value'] is not None:
                    report_lines.append(f"       ⚠️  Alt: alt=\"\" (rỗng - cần bổ sung)")
                else:
                    report_lines.append(f"       ⚠️  Alt: KHÔNG CÓ (cần thêm)")
                report_lines.append("")
            
            report_lines.append("-" * 90)
            report_lines.append("")
    
    return "\n".join(report_lines)

if __name__ == "__main__":
    print("🔍 Quét toàn bộ thư mục HTML...")
    files_with_missing_alt, total_html, total_img, seo_count = scan_all_html_files()
    
    print(f"✓ Tìm thấy {len(files_with_missing_alt)} file cần sửa")
    print(f"✓ Trong đó {seo_count} file trong thư mục 'seo html'")
    
    # Prepare data
    data = {
        'files': files_with_missing_alt,
        'total_html_files': total_html,
        'total_images': total_img
    }
    
    # Generate report
    report = generate_comprehensive_report(data)
    
    # Save report
    output_file = ROOT_DIR / "Danh_sach_anh_thieu_alt_TOAN_DIEN.txt"
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(report)
    
    print(f"✓ Báo cáo đã lưu: {output_file}")
    print("\n" + report[:1500] + "\n...\n(Xem file để chi tiết đầy đủ)")
