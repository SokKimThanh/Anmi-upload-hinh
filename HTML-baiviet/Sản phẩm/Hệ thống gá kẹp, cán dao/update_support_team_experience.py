"""
Script để cập nhật thông tin đội hỗ trợ kỹ thuật
Từ "10+ năm" hoặc "10 năm" → "15+ năm kinh nghiệm"

Version: 1.0.0
Date: October 30, 2025
Author: An Mi Tools Technical Team
"""

import os
import re
from pathlib import Path

# Thư mục chứa các file HTML cần cập nhật
SEO_HTML_DIR = Path("seo html")

# Patterns cần thay thế
PATTERNS = [
    (r'(\bkỹ sư\s+)10\+?\s+năm kinh nghiệm', r'\g<1>15+ năm kinh nghiệm'),
    (r'(\bkỹ thuật\s+)10\+?\s+năm', r'\g<1>15+ năm'),
    (r'(\bkinh nghiệm\s+)10\+?\s+năm', r'\g<1>15+ năm'),
]


def update_html_file(file_path):
    """Cập nhật một file HTML với thông tin 15+ năm kinh nghiệm"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        changes_made = []
        
        # Áp dụng các pattern thay thế
        for pattern, replacement in PATTERNS:
            matches = re.findall(pattern, content, flags=re.IGNORECASE)
            if matches:
                content = re.sub(pattern, replacement, content, flags=re.IGNORECASE)
                changes_made.append(f"Thay '{pattern}' → '{replacement}' ({len(matches)} lần)")
        
        # Kiểm tra xem có thay đổi không
        if content != original_content:
            # Ghi lại file
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
            return True, changes_made
        else:
            return False, []
            
    except Exception as e:
        print(f"  ❌ Lỗi: {e}")
        return False, []


def main():
    """Main function"""
    print("=" * 80)
    print("🚀 CẬP NHẬT THÔNG TIN ĐỘI HỖ TRỢ KỸ THUẬT: 15+ NĂM KINH NGHIỆM")
    print("=" * 80)
    print()
    
    if not SEO_HTML_DIR.exists():
        print(f"❌ Không tìm thấy thư mục: {SEO_HTML_DIR}")
        return
    
    # Lấy danh sách tất cả file .html
    html_files = sorted(list(SEO_HTML_DIR.glob("*.html")))
    
    if not html_files:
        print(f"❌ Không tìm thấy file HTML nào trong {SEO_HTML_DIR}")
        return
    
    print(f"📂 Tìm thấy {len(html_files)} file HTML\n")
    
    updated_count = 0
    skipped_count = 0
    
    for i, file_path in enumerate(html_files, 1):
        filename = file_path.name
        print(f"[{i}/{len(html_files)}] Đang xử lý: {filename}")
        
        success, changes = update_html_file(file_path)
        
        if success:
            print(f"  ✅ Đã cập nhật:")
            for change in changes:
                print(f"     - {change}")
            updated_count += 1
        else:
            print(f"  ⏭️ Không có thay đổi")
            skipped_count += 1
        
        print()
    
    # Tổng kết
    print("=" * 80)
    print("📊 KẾT QUẢ CẬP NHẬT")
    print("=" * 80)
    print(f"✅ Đã cập nhật:     {updated_count} file")
    print(f"⏭️ Đã bỏ qua:       {skipped_count} file")
    print(f"📁 Tổng số file:    {len(html_files)} file")
    print()
    
    if updated_count > 0:
        print("🎉 CẬP NHẬT THÀNH CÔNG!")
        print()
        print("📝 Thông tin mới:")
        print("   - Đội ngũ kỹ sư: 15+ năm kinh nghiệm")
        print("   - Hỗ trợ kỹ thuật: 15+ năm chuyên môn")
        print()
        print("✅ Các file đã sẵn sàng upload lên WordPress!")
    else:
        print("ℹ️ Không có file nào cần cập nhật.")
    
    print("=" * 80)


if __name__ == "__main__":
    main()
