#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script cập nhật nút download catalog trong các file HTML
Chuyển từ <p><strong>Download catalog...</strong></p>
Thành <div class="catalog-download">...</div> với icon SVG
"""

import os
import re
from pathlib import Path

# Thư mục chứa các file HTML
HTML_DIR = Path(r"seo html")

# Pattern cũ cần thay thế
OLD_PATTERN = r'<p><strong>Download catalog chi tiết:</strong>\s*<a href="([^"]+)" target="_blank" rel="noopener">([^<]+)</a></p>'

# Template mới
NEW_TEMPLATE = '''
    <div class="catalog-download">
      <a href="{url}" target="_blank" rel="noopener" class="download-btn">
        <svg class="download-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
          <polyline points="7 10 12 15 17 10"></polyline>
          <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        <span>Download Catalog: {filename}</span>
      </a>
    </div>'''

def update_html_file(file_path):
    """Cập nhật một file HTML"""
    try:
        # Đọc nội dung file
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Kiểm tra xem đã có catalog-download chưa
        if 'class="catalog-download"' in content:
            return None, "Đã có catalog-download"
        
        # Kiểm tra có pattern cũ không
        if '<strong>Download catalog chi tiết:</strong>' not in content:
            return None, "Không có download link"
        
        # Tìm và thay thế
        def replace_func(match):
            url = match.group(1)
            filename = match.group(2)
            return NEW_TEMPLATE.format(url=url, filename=filename)
        
        new_content = re.sub(OLD_PATTERN, replace_func, content)
        
        # Kiểm tra có thay đổi không
        if new_content == content:
            return None, "Không tìm thấy pattern"
        
        # Ghi lại file
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        
        return True, "Đã cập nhật"
    
    except Exception as e:
        return False, f"Lỗi: {str(e)}"

def main():
    """Hàm chính"""
    print("=" * 60)
    print("SCRIPT CẬP NHẬT NÚT DOWNLOAD CATALOG")
    print("=" * 60)
    
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
    print("\n" + "=" * 60)
    print("✅ HOÀN THÀNH!")
    print(f"   - Đã cập nhật: {updated} files")
    print(f"   - Bỏ qua: {skipped} files")
    print(f"   - Lỗi: {errors} files")
    print("=" * 60)

if __name__ == "__main__":
    main()
