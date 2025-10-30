#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script cập nhật class cho CTA buttons trong các file HTML
Thêm các class: cta-buttons, btn, btn-primary để cải thiện styling
"""

import os
import re
from pathlib import Path

# Thư mục chứa các file HTML
HTML_DIR = Path(r"seo html")

def update_cta_buttons(content):
    """Cập nhật CTA buttons với các class mới"""
    changes = 0
    
    # Pattern 1: Thêm class "cta-buttons" vào div.contact-cta nếu chưa có
    pattern1 = r'<div class="contact-cta">'
    replacement1 = r'<div class="contact-cta cta-buttons">'
    if pattern1 in content and replacement1 not in content:
        content = content.replace(pattern1, replacement1)
        changes += 1
    
    # Pattern 2: Cập nhật nút báo giá chính (thêm btn btn-primary)
    # Tìm các patterns như: class="cta-button">💬 Báo Giá
    pattern2 = r'<a href="https://anmitools\.com/contact-us/" class="cta-button">'
    if re.search(pattern2, content):
        content = re.sub(
            pattern2,
            r'<a href="https://anmitools.com/contact-us/" class="btn btn-primary cta-button">',
            content
        )
        changes += 1
    
    # Pattern 3: Cập nhật nút tải catalog phụ (thêm btn btn-primary, đổi secondary thành btn-primary)
    # Tìm: class="cta-button secondary">📄 Tải Catalog
    pattern3 = r'<a href="https://anmitools\.com/catalog-anmi-tools/tai-xuong/catalog-san-pham-an-mi-tools/" class="cta-button secondary">'
    if re.search(pattern3, content):
        content = re.sub(
            pattern3,
            r'<a href="https://anmitools.com/catalog-anmi-tools/tai-xuong/catalog-san-pham-an-mi-tools/" class="btn btn-primary cta-button">',
            content
        )
        changes += 1
    
    # Pattern 4: Nếu không có class secondary, chỉ thêm btn btn-primary
    pattern4 = r'<a href="https://anmitools\.com/catalog-anmi-tools/tai-xuong/catalog-san-pham-an-mi-tools/" class="cta-button">'
    if re.search(pattern4, content):
        content = re.sub(
            pattern4,
            r'<a href="https://anmitools.com/catalog-anmi-tools/tai-xuong/catalog-san-pham-an-mi-tools/" class="btn btn-primary cta-button">',
            content
        )
        changes += 1
    
    return content, changes

def update_html_file(file_path):
    """Cập nhật một file HTML"""
    try:
        # Đọc nội dung file
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Kiểm tra có contact-cta không
        if 'class="contact-cta' not in content:
            return None, "Không có contact-cta"
        
        # Kiểm tra đã có class đầy đủ chưa
        if 'class="btn btn-primary cta-button"' in content and 'class="contact-cta cta-buttons"' in content:
            return None, "Đã có class đầy đủ"
        
        # Cập nhật
        new_content, changes = update_cta_buttons(content)
        
        # Kiểm tra có thay đổi không
        if changes == 0:
            return None, "Không cần cập nhật"
        
        # Ghi lại file
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        
        return True, f"Đã cập nhật {changes} thay đổi"
    
    except Exception as e:
        return False, f"Lỗi: {str(e)}"

def main():
    """Hàm chính"""
    print("=" * 70)
    print("SCRIPT CẬP NHẬT CLASS CHO CTA BUTTONS")
    print("=" * 70)
    
    # Tìm tất cả file .html
    html_files = sorted(HTML_DIR.glob("*.html"))
    
    if not html_files:
        print(f"❌ Không tìm thấy file HTML trong {HTML_DIR}")
        return
    
    print(f"Tìm thấy {len(html_files)} file HTML...\n")
    
    # Thống kê
    updated = 0
    skipped = 0
    errors = 0
    
    # Xử lý từng file
    for file_path in html_files:
        file_name = file_path.name
        result, message = update_html_file(file_path)
        
        if result is True:
            print(f"✅ {file_name} - {message}")
            updated += 1
        elif result is None:
            print(f"⏭️  {file_name} - {message}")
            skipped += 1
        else:
            print(f"❌ {file_name} - {message}")
            errors += 1
    
    # Tổng kết
    print("\n" + "=" * 70)
    print("✅ HOÀN THÀNH!")
    print(f"   - Đã cập nhật: {updated} files")
    print(f"   - Bỏ qua: {skipped} files")
    print(f"   - Lỗi: {errors} files")
    print("=" * 70)
    print("\nCác class đã được thêm:")
    print("   • <div class=\"contact-cta cta-buttons\">")
    print("   • <a class=\"btn btn-primary cta-button\">")

if __name__ == "__main__":
    main()
