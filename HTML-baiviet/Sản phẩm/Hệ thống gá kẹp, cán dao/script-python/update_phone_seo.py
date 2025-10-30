#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script cập nhật số điện thoại trong tất cả file .seo.md
Thay đổi: 0909 927 274 → 091 519 2325
"""

import os
import re
from pathlib import Path

# Đường dẫn thư mục chứa file markdown
MD_DIR = r"e:\ANMI_Dự Án bảo trì phần mềm website AnMi\Anmi-upload-hinh\HTML-baiviet\Sản phẩm\Hệ thống gá kẹp, cán dao\seo back end\products"

# Số cũ và số mới
OLD_PHONE = "0909 927 274"
NEW_PHONE = "091 519 2325"

def update_file(file_path):
    """Cập nhật số điện thoại trong một file markdown"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Kiểm tra xem có số cũ không
        if OLD_PHONE not in content:
            return f"[SKIP] {file_path.name} - Không có số {OLD_PHONE}"
        
        # Thay thế số cũ bằng số mới
        new_content = content.replace(OLD_PHONE, NEW_PHONE)
        
        # Đếm số lần thay thế
        count = content.count(OLD_PHONE)
        
        # Ghi lại file
        with open(file_path, 'w', encoding='utf-8', newline='\n') as f:
            f.write(new_content)
        
        return f"[OK] {file_path.name} - Đã thay {count} lần"
            
    except Exception as e:
        return f"[ERROR] {file_path.name} - {str(e)}"

def main():
    """Main function"""
    md_dir = Path(MD_DIR)
    
    # Lấy tất cả file .seo.md
    md_files = list(md_dir.glob("*.seo.md"))
    
    print(f"Tìm thấy {len(md_files)} file .seo.md cần kiểm tra...\n")
    print(f"Thay đổi: {OLD_PHONE} → {NEW_PHONE}\n")
    
    processed = 0
    skipped = 0
    errors = 0
    
    for file_path in sorted(md_files):
        result = update_file(file_path)
        print(result)
        
        if "[OK]" in result:
            processed += 1
        elif "[SKIP]" in result:
            skipped += 1
        elif "[ERROR]" in result:
            errors += 1
    
    print(f"\n{'='*60}")
    print(f"✅ Hoàn thành!")
    print(f"   - Đã cập nhật: {processed} files")
    print(f"   - Bỏ qua: {skipped} files")
    print(f"   - Lỗi: {errors} files")
    print(f"{'='*60}")

if __name__ == "__main__":
    main()
