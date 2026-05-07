#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script to find all HTML files with images missing alt text
"""

import os
import re
from pathlib import Path
from datetime import datetime

# Root directory to scan
ROOT_DIR = Path(__file__).parent

def extract_img_tags(html_content):
    """
    Extract all <img> tags and check for missing or empty alt text
    Returns list of img info dicts
    """
    # Pattern to match img tags (including multiline)
    img_pattern = r'<img\s+([^>]*)>'
    
    results = []
    for match in re.finditer(img_pattern, html_content, re.IGNORECASE | re.DOTALL):
        img_tag = match.group(0)
        attrs = match.group(1)
        
        # Check if has alt attribute with actual content
        alt_match = re.search(r'\salt=\s*(["\'])([^"\']*)\1|\salt=([^\s>]+)', attrs, re.IGNORECASE)
        
        if alt_match:
            # Has alt attribute - check if it's empty
            alt_value = alt_match.group(2) if alt_match.group(2) is not None else alt_match.group(3)
            has_meaningful_alt = bool(alt_value.strip())
        else:
            # No alt attribute at all
            has_meaningful_alt = False
            alt_value = None
        
        # Extract src - handle multiline cases
        src_match = re.search(r'src\s*=\s*(["\'])([^"\']*?)\1', attrs, re.IGNORECASE | re.DOTALL)
        src = src_match.group(2) if src_match else 'N/A'
        
        # Clean up the tag for display
        tag_display = re.sub(r'\s+', ' ', img_tag)[:150]
        
        results.append({
            'img_tag': img_tag,
            'src': src,
            'has_meaningful_alt': has_meaningful_alt,
            'alt_value': alt_value,
            'line_snippet': tag_display + ('...' if len(tag_display) > 145 else '')
        })
    
    return results

def scan_html_files():
    """
    Scan all HTML files in subdirectories and find images without meaningful alt text
    """
    files_with_missing_alt = {}
    
    # Find all HTML files
    for html_file in ROOT_DIR.rglob('*.html'):
        try:
            with open(html_file, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
            
            img_tags = extract_img_tags(content)
            missing_alt_tags = [tag for tag in img_tags if not tag['has_meaningful_alt']]
            
            if missing_alt_tags:
                relative_path = html_file.relative_to(ROOT_DIR)
                files_with_missing_alt[str(relative_path)] = missing_alt_tags
        
        except Exception as e:
            print(f"Error processing {html_file}: {e}")
    
    return files_with_missing_alt

def generate_report(files_with_missing_alt):
    """
    Generate a detailed report
    """
    report_lines = []
    report_lines.append("=" * 80)
    report_lines.append("DANH SÁCH CÁC FILE HTML CHỨA LINK ẢNH KHÔNG CÓ ALT TEXT")
    report_lines.append("=" * 80)
    report_lines.append(f"Ngày tạo: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    report_lines.append("")
    
    total_files = len(files_with_missing_alt)
    total_images = sum(len(imgs) for imgs in files_with_missing_alt.values())
    
    report_lines.append(f"📋 THỐNG KÊ:")
    report_lines.append(f"   - Tổng file HTML có ảnh cần chỉnh sửa: {total_files}")
    report_lines.append(f"   - Tổng số ảnh cần chỉnh sửa: {total_images}")
    report_lines.append("")
    report_lines.append("=" * 80)
    report_lines.append("")
    
    # Group by file
    for file_idx, file_path in enumerate(sorted(files_with_missing_alt.keys()), 1):
        missing_imgs = files_with_missing_alt[file_path]
        
        report_lines.append(f"[{file_idx}] 📄 FILE: {file_path}")
        report_lines.append(f"     Số ảnh cần chỉnh: {len(missing_imgs)}")
        report_lines.append("")
        
        for img_idx, img in enumerate(missing_imgs, 1):
            report_lines.append(f"     ✗ Ảnh #{img_idx}")
            report_lines.append(f"       📷 URL: {img['src']}")
            if img['alt_value'] is not None:
                report_lines.append(f"       ⚠️  Alt hiện tại: alt=\"{img['alt_value']}\" (rỗng, cần bổ sung)")
            else:
                report_lines.append(f"       ⚠️  Alt hiện tại: KHÔNG CÓ (cần thêm)")
            report_lines.append(f"       HTML: {img['line_snippet']}")
            report_lines.append("")
        
        report_lines.append("-" * 80)
        report_lines.append("")
    
    return "\n".join(report_lines)

if __name__ == "__main__":
    print("🔍 Scanning HTML files...")
    files_with_missing_alt = scan_html_files()
    
    print(f"✓ Found {len(files_with_missing_alt)} files with missing alt text")
    
    # Generate report
    report = generate_report(files_with_missing_alt)
    
    # Save report
    output_file = ROOT_DIR / "Danh_sach_anh_thieu_alt.txt"
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(report)
    
    print(f"✓ Report saved to: {output_file}")
    print("\n" + report)
