"""
Design Standards Validation Script
Kiểm tra CSS và HTML structure theo DESIGN-RULES.md
"""

import os
import re
from pathlib import Path
from bs4 import BeautifulSoup

# Base directory
BASE_DIR = Path(r"e:\ANMI_Dự Án bảo trì phần mềm website AnMi\Anmi-upload-hinh\HTML-baiviet\Sản phẩm\Hệ thống gá kẹp, cán dao")
HTML_DIR = BASE_DIR / "seo html"
CSS_FILE = BASE_DIR / "plugins" / "plugins" / "css" / "anmi-holder-products.css"

def check_css_standards(css_path):
    """Kiểm tra CSS có đúng standards không"""
    print("\n" + "="*80)
    print("CSS VALIDATION")
    print("="*80)
    
    issues = []
    
    with open(css_path, 'r', encoding='utf-8') as f:
        css_content = f.read()
    
    # Check 1: H1 typography scale
    if 'clamp(1.84rem, 4.2vw, 2.625rem)' not in css_content:
        issues.append("❌ H1 font-size không đúng clamp(1.84rem, 4.2vw, 2.625rem)")
    else:
        print("✅ H1 typography scale: clamp(1.84rem, 4.2vw, 2.625rem)")
    
    # Check 2: H2 typography scale
    if 'clamp(1.575rem, 3.15vw, 1.97rem)' not in css_content:
        issues.append("❌ H2 font-size không đúng clamp(1.575rem, 3.15vw, 1.97rem)")
    else:
        print("✅ H2 typography scale: clamp(1.575rem, 3.15vw, 1.97rem)")
    
    # Check 3: H2 border-bottom
    h2_border_pattern = r'\.section\s+h2[^}]*border-bottom[^}]*3px'
    if not re.search(h2_border_pattern, css_content, re.IGNORECASE):
        issues.append("❌ H2 không có border-bottom 3px")
    else:
        print("✅ H2 border-bottom: 3px")
    
    # Check 4: Body line-height
    if 'line-height: 1.7' not in css_content and 'line-height:1.7' not in css_content:
        issues.append("❌ Body text không có line-height 1.7")
    else:
        print("✅ Body line-height: 1.7")
    
    # Check 5: Contact email section styles
    if '.contact-email-section' not in css_content:
        issues.append("❌ Thiếu .contact-email-section styles")
    else:
        print("✅ Contact email section: có CSS")
    
    # Check 6: Responsive breakpoints
    breakpoints = ['@media (max-width: 1024px)', '@media (max-width: 768px)', '@media (max-width: 480px)']
    found_breakpoints = []
    for bp in breakpoints:
        if bp in css_content:
            found_breakpoints.append(bp.split('max-width: ')[1].split(')')[0])
    
    if len(found_breakpoints) < 2:
        issues.append(f"❌ Thiếu responsive breakpoints (found: {found_breakpoints})")
    else:
        print(f"✅ Responsive breakpoints: {', '.join(found_breakpoints)}")
    
    if issues:
        print("\n⚠️  CSS ISSUES:")
        for issue in issues:
            print(f"  {issue}")
    else:
        print("\n✅ CSS đạt chuẩn 100%")
    
    return len(issues) == 0

def check_html_structure(html_path):
    """Kiểm tra HTML structure theo DESIGN-RULES.md"""
    with open(html_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    soup = BeautifulSoup(content, 'html.parser')
    issues = []
    
    # Check 1: Có 1 H1 duy nhất
    h1_tags = soup.find_all('h1')
    if len(h1_tags) == 0:
        issues.append("❌ Thiếu H1 tag")
    elif len(h1_tags) > 1:
        issues.append(f"❌ Có {len(h1_tags)} H1 tags (chỉ nên có 1)")
    
    # Check 2: H2 có trong các section
    h2_tags = soup.find_all('h2')
    if len(h2_tags) < 3:
        issues.append(f"⚠️  Chỉ có {len(h2_tags)} H2 tags (nên có ít nhất 3 sections)")
    
    # Check 3: Không dùng H4-H6
    h4_tags = soup.find_all(['h4', 'h5', 'h6'])
    if h4_tags:
        issues.append(f"❌ Có {len(h4_tags)} H4/H5/H6 tags (không nên dùng)")
    
    # Check 4: Product image có figcaption
    product_image = soup.find('figure', class_='product-image')
    if product_image:
        figcaption = product_image.find('figcaption')
        if not figcaption:
            issues.append("❌ Product image thiếu figcaption")
    else:
        issues.append("❌ Thiếu figure.product-image")
    
    # Check 5: Section structure
    sections = soup.find_all('div', class_='section')
    if len(sections) < 5:
        issues.append(f"⚠️  Chỉ có {len(sections)} sections (nên có ít nhất 5)")
    
    # Check 6: Contact info có 4 offices
    contact_offices = soup.find_all('div', class_='office')
    if len(contact_offices) != 4:
        issues.append(f"⚠️  Contact có {len(contact_offices)} offices (cần 4: HN, HCM, HP, DN)")
    
    # Check 7: Contact email section
    email_section = soup.find('div', class_='contact-email-section')
    if not email_section:
        issues.append("❌ Thiếu contact-email-section")
    else:
        email_link = email_section.find('a', href=re.compile(r'mailto:'))
        if not email_link or 'admsales7@anmitools.com' not in email_link.get('href', ''):
            issues.append("⚠️  Email section không đúng (cần admsales7@anmitools.com)")
    
    # Check 8: Table có thead/tbody
    tables = soup.find_all('table')
    for table in tables:
        thead = table.find('thead')
        tbody = table.find('tbody')
        if not thead or not tbody:
            issues.append(f"⚠️  Table thiếu thead/tbody structure")
            break
    
    # Check 9: Lists có bold titles
    list_items = soup.find_all('li')
    bold_count = sum(1 for li in list_items if li.find('strong'))
    if list_items and bold_count < len(list_items) * 0.3:  # At least 30% should have bold
        issues.append("⚠️  List items thiếu <strong> cho titles")
    
    # Check 10: CTA buttons
    cta_buttons = soup.find_all('a', class_=re.compile(r'btn|cta-button|download-btn'))
    if len(cta_buttons) < 2:
        issues.append(f"⚠️  Chỉ có {len(cta_buttons)} CTA buttons (nên có ít nhất 2)")
    
    return issues

def validate_all_html_files():
    """Validate tất cả HTML files"""
    print("\n" + "="*80)
    print("HTML FILES VALIDATION")
    print("="*80)
    
    html_files = sorted(HTML_DIR.glob("*.seo.html"))
    
    compliant_files = []
    non_compliant_files = []
    
    for html_file in html_files:
        file_name = html_file.name
        
        # Skip FIXED files
        if 'FIXED' in file_name:
            continue
        
        issues = check_html_structure(html_file)
        
        if not issues:
            compliant_files.append(file_name)
        else:
            non_compliant_files.append({
                'file': file_name,
                'issues': issues
            })
    
    # Print compliant files
    print(f"\n✅ FILES ĐẠT CHUẨN ({len(compliant_files)} files):")
    for file_name in compliant_files:
        print(f"  ✓ {file_name}")
    
    # Print non-compliant files
    if non_compliant_files:
        print(f"\n❌ FILES CHƯA ĐẠT CHUẨN ({len(non_compliant_files)} files):")
        for item in non_compliant_files:
            print(f"\n  📄 {item['file']}")
            for issue in item['issues']:
                print(f"     {issue}")
    else:
        print("\n🎉 TẤT CẢ HTML FILES ĐẠT CHUẨN!")
    
    return compliant_files, non_compliant_files

def main():
    print("🔍 DESIGN STANDARDS VALIDATION")
    print("Kiểm tra CSS và HTML theo DESIGN-RULES.md")
    
    # Check CSS
    css_compliant = check_css_standards(CSS_FILE)
    
    # Check HTML files
    compliant_files, non_compliant_files = validate_all_html_files()
    
    # Summary
    print("\n" + "="*80)
    print("SUMMARY")
    print("="*80)
    print(f"CSS: {'✅ Đạt chuẩn' if css_compliant else '❌ Chưa đạt chuẩn'}")
    print(f"HTML Files: {len(compliant_files)}/{len(compliant_files) + len(non_compliant_files)} files đạt chuẩn")
    
    if non_compliant_files:
        print("\n📋 DANH SÁCH FILES CẦN SỬA:")
        for item in non_compliant_files:
            print(f"  • {item['file']} ({len(item['issues'])} issues)")
    
    print("\n✅ Validation hoàn tất!")

if __name__ == "__main__":
    main()
