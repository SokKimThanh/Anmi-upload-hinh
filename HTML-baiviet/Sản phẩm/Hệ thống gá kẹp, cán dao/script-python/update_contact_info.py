#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script cập nhật thông tin liên hệ vào tất cả file HTML
Thêm thông tin 2 văn phòng (Hà Nội + TP.HCM) sau CTA buttons
"""

import os
import re
from pathlib import Path

# Block HTML cần chèn vào
CONTACT_INFO_BLOCK = '''    
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
'''

# Đường dẫn thư mục chứa file HTML
HTML_DIR = r"e:\ANMI_Dự Án bảo trì phần mềm website AnMi\Anmi-upload-hinh\HTML-baiviet\Sản phẩm\Hệ thống gá kẹp, cán dao\seo html"

def update_file(file_path):
    """Cập nhật một file HTML với thông tin liên hệ"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Kiểm tra xem đã có contact-info chưa
        if '<div class="contact-info">' in content:
            return f"[SKIP] {file_path.name} - Đã có contact-info"
        
        # Pattern: tìm vị trí sau </div> của contact-cta và trước <figure class="contact-image">
        pattern = r'(</div>\s*\n)(\s*<figure class="contact-image">)'
        
        if re.search(pattern, content):
            new_content = re.sub(pattern, r'\1' + CONTACT_INFO_BLOCK + r'\n\2', content)
            
            with open(file_path, 'w', encoding='utf-8', newline='\n') as f:
                f.write(new_content)
            
            return f"[OK] {file_path.name} - Đã thêm contact-info"
        else:
            return f"[ERROR] {file_path.name} - Không tìm thấy pattern phù hợp"
            
    except Exception as e:
        return f"[ERROR] {file_path.name} - {str(e)}"

def main():
    """Main function"""
    html_dir = Path(HTML_DIR)
    
    # Lấy danh sách file từ 18-40 (đã làm 16, 17, 39)
    files_to_process = []
    for num in range(18, 41):
        # Tìm file có pattern: {num}-*.html
        matching_files = list(html_dir.glob(f"{num:02d}-*.html"))
        files_to_process.extend(matching_files)
    
    print(f"Tìm thấy {len(files_to_process)} files cần cập nhật...\n")
    
    processed = 0
    for file_path in sorted(files_to_process):
        result = update_file(file_path)
        print(result)
        if "[OK]" in result:
            processed += 1
    
    print(f"\n✅ Hoàn thành! Đã cập nhật {processed}/{len(files_to_process)} files")

if __name__ == "__main__":
    main()
