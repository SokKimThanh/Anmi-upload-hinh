#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Script: add_support_team_section.py
Mục đích: Thêm phần "Đội ngũ kỹ sư 15+ năm kinh nghiệm" vào section "Tại Sao Chọn...Từ An Mi Tools?"
"""

import os
import re
from pathlib import Path

def extract_product_name(file_path):
    """Trích xuất tên sản phẩm từ H1 hoặc filename"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            
        # Tìm H1 tag
        h1_match = re.search(r'<h1[^>]*>(.*?)</h1>', content, re.DOTALL)
        if h1_match:
            h1_text = re.sub(r'<[^>]+>', '', h1_match.group(1))
            h1_text = h1_text.split('–')[0].split('-')[0].strip()
            return h1_text
        
        # Fallback: dùng filename
        filename = Path(file_path).stem
        filename = re.sub(r'^\d+-', '', filename)
        filename = filename.replace('-', ' ').replace('.seo', '').title()
        return filename
    except Exception as e:
        print(f"  ⚠️ Lỗi extract product name: {e}")
        return "sản phẩm"

def add_support_team_section(file_path):
    """Thêm section Hỗ Trợ Kỹ Thuật với đội ngũ 15+ năm kinh nghiệm"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Kiểm tra đã có "15+ năm kinh nghiệm" chưa
        if re.search(r'15\+?\s*năm kinh nghiệm', content):
            print(f"  ✅ Đã có '15+ năm kinh nghiệm'")
            return False
        
        # Kiểm tra đã có "10+ năm kinh nghiệm" - cần thay thế
        if re.search(r'10\+?\s*năm kinh nghiệm', content):
            content = re.sub(
                r'(\bĐội ngũ kỹ sư\s+)10\+?\s*năm kinh nghiệm',
                r'\g<1>15+ năm kinh nghiệm',
                content
            )
            print(f"  ✅ Đã thay '10+ năm' → '15+ năm'")
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
            return True
        
        # Tìm section hỗ trợ kỹ thuật
        product_name = extract_product_name(file_path)
        
        # Pattern tìm section với nhiều biến thể
        section_patterns = [
            # Pattern 1: <div class="section">...<h2>🌟 Tại Sao Chọn...
            (r'(<div class="section">\s*<h2>🌟\s*Tại Sao Chọn\s+.*?\s+Từ An Mi Tools\?</h2>\s*<div class="support-grid">)', 'tai-sao-chon'),
            
            # Pattern 2: <h2>Hỗ trợ kỹ thuật và liên hệ</h2>...<div class="support-grid">
            (r'(<div class="section">\s*<h2>Hỗ trợ kỹ thuật và liên hệ</h2>\s*<p>.*?</p>\s*<div class="support-grid">)', 'ho-tro-ky-thuat'),
            
            # Pattern 3: <h2>📞 Liên Hệ Tư Vấn...</h2>...<div class="support-grid">
            (r'(<div class="section[^"]*">\s*<h2>📞\s*Liên Hệ.*?</h2>\s*<p>.*?</p>\s*<div class="support-grid">)', 'lien-he-tu-van'),
        ]
        
        support_section_match = None
        section_type = None
        for pattern, stype in section_patterns:
            support_section_match = re.search(pattern, content, re.DOTALL)
            if support_section_match:
                section_type = stype
                break
        
        if not support_section_match:
            print(f"  ⏭️ Không tìm thấy section hỗ trợ kỹ thuật")
            return False
        
        # Template card Hỗ Trợ Kỹ Thuật dựa theo section type
        if section_type == 'tai-sao-chon':
            support_card_template = f'''
      <div class="support-card">
        <div class="support-icon">🔧</div>
        <h3>Hỗ Trợ Kỹ Thuật</h3>
        <p>Đội ngũ kỹ sư 15+ năm kinh nghiệm sẵn sàng tư vấn lựa chọn {product_name} phù hợp, hướng dẫn lắp đặt và tối ưu hóa tham số gia công.</p>
      </div>'''
        elif section_type == 'lien-he-tu-van':
            support_card_template = f'''
    <div class="support-card">
      <div class="support-icon">👨‍🔧</div>
      <h3>Đội ngũ kỹ sư 15+ năm kinh nghiệm</h3>
      <p>Tư vấn chuyên sâu lựa chọn {product_name} phù hợp với ứng dụng của bạn, hỗ trợ lắp đặt và tối ưu hóa quy trình gia công.</p>
    </div>'''
        else:  # ho-tro-ky-thuat
            support_card_template = f'''
    <div class="support-card">
      <div class="support-icon">🔧</div>
      <h3>Đội ngũ kỹ sư 15+ năm kinh nghiệm</h3>
      <p>Tư vấn chuyên sâu lựa chọn {product_name} phù hợp, hướng dẫn lắp đặt và tối ưu hóa tham số gia công cho từng ứng dụng cụ thể.</p>
    </div>'''
        
        # Tìm vị trí insert (sau opening tag của support-grid)
        insert_pos = support_section_match.end()
        
        # Kiểm tra xem đã có card nào chưa - tìm đến khi gặp </div> đầu tiên sau support-grid
        # Đọc content từ insert_pos để tìm các card hiện có
        remaining_content = content[insert_pos:]
        
        # Tìm tất cả support-card trong section này
        cards_match = re.search(r'(.*?)</div>\s*</div>', remaining_content, re.DOTALL)
        if cards_match:
            cards_content = cards_match.group(1)
            
            # Kiểm tra đã có card với "15+ năm kinh nghiệm" hoặc "Hỗ Trợ Kỹ Thuật" chưa
            if re.search(r'15\+?\s*năm kinh nghiệm', cards_content):
                print(f"  ✅ Đã có card với '15+ năm kinh nghiệm'")
                return False
            
            # Kiểm tra và update nếu có card "10+ năm kinh nghiệm"
            if re.search(r'10\+?\s*năm kinh nghiệm', cards_content):
                # Thay thế trong phạm vi section này
                section_start = insert_pos
                section_end = insert_pos + cards_match.end()
                section_content = content[section_start:section_end]
                
                updated_section = re.sub(
                    r'10\+?\s*năm kinh nghiệm',
                    '15+ năm kinh nghiệm',
                    section_content
                )
                
                content = content[:section_start] + updated_section + content[section_end:]
                print(f"  ✅ Đã update card từ '10+ năm' → '15+ năm'")
                
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(content)
                return True
            
            # Kiểm tra có card "Hỗ Trợ Kỹ Thuật" hoặc "Đội ngũ kỹ sư" chưa
            has_support_card = re.search(r'<h3>(Hỗ Trợ Kỹ Thuật|Đội ngũ kỹ sư)', cards_content)
            
            if has_support_card:
                # Đã có card nhưng chưa có thông tin năm kinh nghiệm, update nội dung
                if section_type == 'tai-sao-chon':
                    # Update card "Hỗ Trợ Kỹ Thuật"
                    pattern = r'(<h3>Hỗ Trợ Kỹ Thuật</h3>\s*<p>)(.*?)(</p>)'
                    replacement = rf'\g<1>Đội ngũ kỹ sư 15+ năm kinh nghiệm sẵn sàng tư vấn lựa chọn {product_name} phù hợp, hướng dẫn lắp đặt và tối ưu hóa tham số gia công.\g<3>'
                else:
                    # Update card đội ngũ
                    pattern = r'(<h3>Đội ngũ kỹ sư[^<]*</h3>\s*<p>)(.*?)(</p>)'
                    replacement = rf'\g<1>Tư vấn chuyên sâu lựa chọn {product_name} phù hợp, hướng dẫn lắp đặt và tối ưu hóa tham số gia công cho từng ứng dụng cụ thể.\g<3>'
                
                # Apply update trong phạm vi section
                section_start = insert_pos
                section_end = insert_pos + cards_match.end()
                section_content = content[section_start:section_end]
                
                updated_section = re.sub(pattern, replacement, section_content, flags=re.DOTALL)
                
                if updated_section != section_content:
                    content = content[:section_start] + updated_section + content[section_end:]
                    print(f"  ✅ Đã update nội dung card với '15+ năm kinh nghiệm'")
                    
                    with open(file_path, 'w', encoding='utf-8') as f:
                        f.write(content)
                    return True
                else:
                    print(f"  ⏭️ Card đã tồn tại nhưng không cần update")
                    return False
            else:
                # Chưa có card, thêm vào
                new_content = content[:insert_pos] + support_card_template + content[insert_pos:]
                content = new_content
                print(f"  ✅ Đã thêm card mới với '15+ năm kinh nghiệm'")
                
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(content)
                return True
        else:
            print(f"  ⏭️ Không tìm thấy vị trí insert phù hợp")
            return False
        
    except Exception as e:
        print(f"  ❌ Lỗi: {e}")
        return False

def main():
    """Main function"""
    seo_html_dir = Path("seo html")
    
    if not seo_html_dir.exists():
        print(f"❌ Thư mục 'seo html' không tồn tại!")
        return
    
    # Lấy tất cả file .html
    html_files = sorted(list(seo_html_dir.glob("*.html")))
    
    print(f"\n{'='*60}")
    print(f"🚀 BẮT ĐẦU THÊM PHẦN 'ĐỘI NGŨ KỸ SƯ 15+ NĂM KINH NGHIỆM'")
    print(f"{'='*60}\n")
    print(f"📁 Tìm thấy {len(html_files)} file HTML\n")
    
    updated_count = 0
    skipped_count = 0
    
    for i, file_path in enumerate(html_files, 1):
        print(f"[{i}/{len(html_files)}] Đang xử lý: {file_path.name}")
        
        if add_support_team_section(file_path):
            updated_count += 1
        else:
            skipped_count += 1
        
        print()
    
    print(f"{'='*60}")
    print(f"✅ Đã cập nhật:     {updated_count} file")
    print(f"⏭️ Đã bỏ qua:       {skipped_count} file")
    print(f"📊 Tổng số:         {len(html_files)} file")
    print(f"{'='*60}\n")

if __name__ == "__main__":
    main()
