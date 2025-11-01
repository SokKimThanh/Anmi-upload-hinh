#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script để thêm contact section vào các file HTML chưa có
"""

import os
import re

# Template contact section chuẩn
CONTACT_SECTION_TEMPLATE = '''
  <div class="section support-contact">
    <h2>📞 Liên Hệ Tư Vấn & Đặt Hàng</h2>
    <p><strong>An Mi Tools</strong> cung cấp sản phẩm chính hãng, bảo hành 24 tháng. Đội ngũ kỹ sư của chúng tôi sẵn sàng tư vấn lựa chọn sản phẩm phù hợp với ứng dụng của bạn.</p>
    
    <div class="contact-cta cta-buttons">
      <a href="https://anmitools.com/contact-us/" class="btn btn-primary cta-button">💬 Báo Giá Sản Phẩm</a>
      <a href="https://anmitools.com/catalog-anmi-tools/tai-xuong/catalog-san-pham-an-mi-tools/" class="btn btn-primary cta-button">📄 Tải Catalog</a>
    </div>
    
    <!-- Desktop: Ảnh hotline số to -->
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
    
    <!-- Mobile: Ảnh hotline số to rõ ràng -->
    <figure class="contact-image contact-image-mobile">
      <img src="https://anmitools.com/wp-content/uploads/2025/10/HOTLINE-1900x1200-copy.webp" 
           alt="An Mi Tools - Hotline tư vấn sản phẩm" 
           loading="lazy" 
           width="1900" 
           height="1200">
      <figcaption>Gọi ngay hotline để được tư vấn chuyên sâu về sản phẩm</figcaption>
    </figure>
  </div>
'''

def add_contact_section(filepath):
    """Thêm contact section vào file HTML"""
    try:
        # Đọc file
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Kiểm tra xem đã có contact section chưa
        if 'support-contact' in content or 'contact-image' in content:
            print(f"⏭️  Đã có: {os.path.basename(filepath)}")
            return False
        
        # Tìm vị trí trước </section> cuối cùng để chèn contact section
        # Tìm tất cả các script JSON-LD (thường ở cuối)
        script_pattern = r'(<script type="application/ld\+json">.*?</script>\s*)+'
        
        # Tìm vị trí script JSON-LD cuối cùng
        scripts = list(re.finditer(script_pattern, content, re.DOTALL))
        
        if scripts:
            # Chèn contact section trước các script JSON-LD
            last_script_start = scripts[-1].start()
            
            # Tìm section cuối cùng trước script
            before_script = content[:last_script_start]
            
            # Chèn contact section
            new_content = before_script + CONTACT_SECTION_TEMPLATE + '\n' + content[last_script_start:]
        else:
            # Nếu không tìm thấy script, chèn trước </section> cuối
            section_close = content.rfind('</section>')
            if section_close != -1:
                new_content = content[:section_close] + CONTACT_SECTION_TEMPLATE + '\n' + content[section_close:]
            else:
                print(f"❌ Không tìm thấy </section>: {os.path.basename(filepath)}")
                return False
        
        # Ghi lại file
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        
        print(f"✅ Đã thêm: {os.path.basename(filepath)}")
        return True
        
    except Exception as e:
        print(f"❌ Lỗi {os.path.basename(filepath)}: {str(e)}")
        return False

def main():
    """Main function"""
    # Danh sách các file cần thêm contact section
    files_to_update = [
        "01-bt-sk-high-speed-tool-holder.seo.html",
        "38-er-high-precision-collet.seo.html",
        "39-sk-high-precision-collet.seo.html",
        "41-bt-er-extension-holder.seo.html",
        "42-ewe-digital-boring-head.seo.html",
        "43-ewb-round-bit-boring-head.seo.html",
        "44-accessories-tooling-support.seo.html"
    ]
    
    html_dir = r"e:\ANMI_Dự Án bảo trì phần mềm website AnMi\Anmi-upload-hinh\HTML-baiviet\Sản phẩm\Hệ thống gá kẹp, cán dao\seo html"
    
    print(f"🔧 Bắt đầu thêm contact section vào {len(files_to_update)} file")
    print("=" * 60)
    
    updated_count = 0
    for filename in files_to_update:
        filepath = os.path.join(html_dir, filename)
        if os.path.exists(filepath):
            if add_contact_section(filepath):
                updated_count += 1
        else:
            print(f"⚠️  Không tìm thấy: {filename}")
    
    print("=" * 60)
    print(f"🎉 Hoàn thành! Đã thêm contact section vào {updated_count}/{len(files_to_update)} file")

if __name__ == "__main__":
    main()
