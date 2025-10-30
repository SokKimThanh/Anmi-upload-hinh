"""
Script để cập nhật tất cả file HTML với cấu trúc hotline responsive mới
- Desktop: Hiển thị ảnh địa chỉ đầy đủ
- Mobile: Hiển thị ảnh hotline số to rõ ràng

Version: 1.0.0
Date: October 30, 2025
Author: An Mi Tools Technical Team
"""

import os
import re
from pathlib import Path

# Thư mục chứa các file HTML cần cập nhật
SEO_HTML_DIR = Path("seo html")

# Pattern cũ: ảnh contact đơn giản
OLD_PATTERN_1 = r'<figure class="contact-image">\s*<img src="https://anmitools\.com/wp-content/uploads/2025/09/trang-30_tools_diachi-editbyAI\.webp"[^>]*>\s*</figure>'

OLD_PATTERN_2 = r'<figure class="contact-image">\s*<img src="https://anmitools\.com/wp-content/uploads/2025/09/trang-30_tools_diachi-editbyAI\.webp"[^>]*>\s*<figcaption>[^<]*</figcaption>\s*</figure>'

# Template mới với responsive hotline
NEW_TEMPLATE = '''<!-- Desktop: Ảnh địa chỉ đầy đủ -->
    <figure class="contact-image contact-image-desktop">
      <img src="https://anmitools.com/wp-content/uploads/2025/09/trang-30_tools_diachi-editbyAI.webp" 
           alt="An Mi Tools - Hotline & Địa chỉ liên hệ" 
           loading="lazy" 
           width="1200" 
           height="400">
      <figcaption>Liên hệ An Mi Tools để được tư vấn chi tiết về {{PRODUCT_NAME}} và các giải pháp gia công chính xác</figcaption>
    </figure>
    
    <!-- Mobile: Ảnh hotline số to rõ ràng -->
    <figure class="contact-image contact-image-mobile">
      <img src="https://anmitools.com/wp-content/uploads/2025/10/HOTLINE-1900x1200-copy.webp" 
           alt="An Mi Tools - Hotline 091 519 2325 - Số điện thoại tư vấn {{PRODUCT_NAME}}" 
           loading="lazy" 
           width="1900" 
           height="1200">
      <figcaption>Gọi ngay 091 519 2325 để được tư vấn {{PRODUCT_NAME}}</figcaption>
    </figure>'''


def extract_product_name(file_path):
    """Trích xuất tên sản phẩm từ nội dung file HTML"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            # Tìm H1 tag
            h1_match = re.search(r'<h1[^>]*>([^<]+)</h1>', content)
            if h1_match:
                # Lấy phần trước dấu – hoặc - hoặc |
                product_name = h1_match.group(1)
                product_name = re.split(r'[–\-|]', product_name)[0].strip()
                return product_name
    except Exception as e:
        print(f"  ⚠️ Lỗi khi đọc tên sản phẩm: {e}")
    
    # Fallback: lấy từ tên file
    filename = os.path.basename(file_path)
    # Loại bỏ số thứ tự và .seo.html
    product_name = re.sub(r'^\d+-', '', filename)
    product_name = product_name.replace('.seo.html', '')
    product_name = product_name.replace('-', ' ').title()
    return product_name


def update_html_file(file_path):
    """Cập nhật một file HTML với cấu trúc hotline responsive mới"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        
        # Trích xuất tên sản phẩm
        product_name = extract_product_name(file_path)
        
        # Tạo template với tên sản phẩm cụ thể
        new_html = NEW_TEMPLATE.replace('{{PRODUCT_NAME}}', product_name)
        
        # Thay thế pattern 1 (không có figcaption)
        content = re.sub(OLD_PATTERN_1, new_html, content, flags=re.DOTALL | re.IGNORECASE)
        
        # Thay thế pattern 2 (có figcaption)
        content = re.sub(OLD_PATTERN_2, new_html, content, flags=re.DOTALL | re.IGNORECASE)
        
        # Kiểm tra xem có thay đổi không
        if content != original_content:
            # Ghi lại file
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
            return True, product_name
        else:
            return False, product_name
            
    except Exception as e:
        print(f"  ❌ Lỗi: {e}")
        return False, None


def main():
    """Main function"""
    print("=" * 80)
    print("🚀 BẮT ĐẦU CẬP NHẬT HOTLINE RESPONSIVE CHO TẤT CẢ FILE HTML")
    print("=" * 80)
    print()
    
    if not SEO_HTML_DIR.exists():
        print(f"❌ Không tìm thấy thư mục: {SEO_HTML_DIR}")
        return
    
    # Lấy danh sách tất cả file .seo.html
    html_files = sorted(SEO_HTML_DIR.glob("*.seo.html"))
    
    if not html_files:
        print(f"❌ Không tìm thấy file .seo.html nào trong {SEO_HTML_DIR}")
        return
    
    print(f"📂 Tìm thấy {len(html_files)} file HTML\n")
    
    updated_count = 0
    skipped_count = 0
    error_count = 0
    
    for i, file_path in enumerate(html_files, 1):
        filename = file_path.name
        print(f"[{i}/{len(html_files)}] Đang xử lý: {filename}")
        
        success, product_name = update_html_file(file_path)
        
        if success:
            print(f"  ✅ Đã cập nhật - Sản phẩm: {product_name}")
            updated_count += 1
        else:
            if product_name:
                print(f"  ⏭️ Đã có cấu trúc mới hoặc không tìm thấy pattern cũ")
                skipped_count += 1
            else:
                print(f"  ❌ Lỗi khi xử lý")
                error_count += 1
        
        print()
    
    # Tổng kết
    print("=" * 80)
    print("📊 KẾT QUẢ CẬP NHẬT")
    print("=" * 80)
    print(f"✅ Đã cập nhật:     {updated_count} file")
    print(f"⏭️ Đã bỏ qua:       {skipped_count} file")
    print(f"❌ Lỗi:             {error_count} file")
    print(f"📁 Tổng số file:    {len(html_files)} file")
    print()
    
    if updated_count > 0:
        print("🎉 CẬP NHẬT THÀNH CÔNG!")
        print()
        print("📱 Cấu trúc mới:")
        print("   - Desktop (>768px): Hiển thị ảnh địa chỉ đầy đủ")
        print("   - Mobile (≤768px): Hiển thị ảnh hotline số to (HOTLINE-1900x1200-copy.webp)")
        print()
        print("🎨 CSS đã được cập nhật trong anmi-holder-products.css v1.1.7")
        print()
        print("✅ Các file đã sẵn sàng upload lên WordPress!")
    else:
        print("ℹ️ Không có file nào cần cập nhật.")
    
    print("=" * 80)


if __name__ == "__main__":
    main()
