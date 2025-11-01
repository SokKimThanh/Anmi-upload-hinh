#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script để cập nhật phần contact images trong tất cả các file HTML
Thay đổi từ 1 ảnh desktop + 1 ảnh mobile sang 2 ảnh desktop + 1 ảnh mobile
"""

import os
import re
import glob

def update_contact_section(content):
    """
    Cập nhật phần contact images theo tiêu chuẩn mới:
    - Desktop: Hiển thị 2 ảnh (Hotline + Địa chỉ)
    - Mobile: Hiển thị 1 ảnh (Hotline)
    """
    
    # Pattern tìm phần contact images cũ (có thể có nhiều biến thể)
    # Tìm từ <!-- Desktop --> đến <!-- Mobile --> và thay thế toàn bộ
    pattern = r'(<!-- Desktop:.*?-->.*?<figure class="contact-image contact-image-desktop">.*?</figure>.*?)(<!-- Mobile:.*?-->)'
    
    # Nội dung mới theo template chuẩn
    new_section = '''<!-- Desktop: Ảnh hotline số to -->
    <figure class="contact-image contact-image-desktop">
      <img src="https://anmitools.com/wp-content/uploads/2025/10/HOTLINE-1900x1200-copy.webp" 
           alt="An Mi Tools - Hotline tư vấn sản phẩm" 
           loading="lazy" 
           width="1900" 
           height="1200">
      <figcaption>Gọi ngay hotline để được tư vấn chuyên sâu về sản phẩm</figcaption>
    </figure>
    
    <!-- Desktop: Ảnh địa chỉ đầy đủ -->
    <figure class="contact-image contact-image-desktop">
      <img src="https://anmitools.com/wp-content/uploads/2025/09/trang-30_tools_diachi-editbyAI.webp" 
           alt="An Mi Tools - Thông tin liên hệ và hỗ trợ kỹ thuật 24/7" 
           loading="lazy" 
           width="1200" 
           height="400">
      <figcaption>Thông tin liên hệ <strong>An Mi Tools</strong> - Hỗ trợ kỹ thuật 24/7 và các giải pháp gá kẹp công cụ CNC</figcaption>
    </figure>
    
    <!-- Mobile: Ảnh hotline số to rõ ràng -->'''
    
    # Thay thế
    updated_content = re.sub(pattern, new_section, content, flags=re.DOTALL)
    
    return updated_content

def process_file(filepath):
    """Xử lý một file HTML"""
    try:
        # Đọc file
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Kiểm tra xem file có phần contact-image-desktop không
        if 'contact-image-desktop' not in content:
            print(f"⏭️  Bỏ qua: {os.path.basename(filepath)} (không có contact section)")
            return False
        
        # Kiểm tra xem đã có 2 ảnh desktop chưa
        desktop_count = content.count('contact-image-desktop')
        if desktop_count >= 2:
            print(f"✅ Đã cập nhật: {os.path.basename(filepath)} (đã có {desktop_count} ảnh desktop)")
            return False
        
        # Cập nhật content
        updated_content = update_contact_section(content)
        
        # Kiểm tra xem có thay đổi không
        if updated_content == content:
            print(f"⚠️  Không thay đổi: {os.path.basename(filepath)}")
            return False
        
        # Ghi lại file
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(updated_content)
        
        print(f"✅ Cập nhật: {os.path.basename(filepath)}")
        return True
        
    except Exception as e:
        print(f"❌ Lỗi {os.path.basename(filepath)}: {str(e)}")
        return False

def main():
    """Main function"""
    # Đường dẫn thư mục chứa các file HTML
    html_dir = r"e:\ANMI_Dự Án bảo trì phần mềm website AnMi\Anmi-upload-hinh\HTML-baiviet\Sản phẩm\Hệ thống gá kẹp, cán dao\seo html"
    
    # Tìm tất cả file .seo.html
    pattern = os.path.join(html_dir, "*.seo.html")
    html_files = glob.glob(pattern)
    
    print(f"🔍 Tìm thấy {len(html_files)} file HTML")
    print("=" * 60)
    
    # Xử lý từng file
    updated_count = 0
    for filepath in sorted(html_files):
        if process_file(filepath):
            updated_count += 1
    
    print("=" * 60)
    print(f"🎉 Hoàn thành! Đã cập nhật {updated_count}/{len(html_files)} file")

if __name__ == "__main__":
    main()
