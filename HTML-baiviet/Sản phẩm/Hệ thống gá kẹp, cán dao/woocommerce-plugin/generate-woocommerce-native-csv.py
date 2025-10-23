#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Tạo CSV tương thích với WooCommerce Native Importer
Format chuẩn WooCommerce để import trực tiếp
"""

import os
import re
import csv
from pathlib import Path

class WooCommerceNativeCSV:
    def __init__(self, input_dir, output_csv):
        self.input_dir = input_dir
        self.output_csv = output_csv
        self.products = []
        
    def parse_frontmatter(self, content):
        """Parse YAML frontmatter"""
        frontmatter = {}
        match = re.search(r'^---\s*\n(.*?)\n---', content, re.DOTALL)
        if not match:
            return frontmatter
        
        yaml_content = match.group(1)
        for line in yaml_content.split('\n'):
            line = line.strip()
            if ':' in line:
                key, value = line.split(':', 1)
                key = key.strip()
                value = value.strip().strip('"').strip("'")
                frontmatter[key] = value
        
        return frontmatter
    
    def extract_description(self, content):
        """Extract description"""
        content = re.sub(r'^---\s*\n.*?\n---', '', content, flags=re.DOTALL)
        content = re.sub(r'<[^>]+>', '', content)
        lines = [line.strip() for line in content.split('\n') if line.strip()]
        
        description = ''
        for line in lines:
            if len(line) > 50 and not line.startswith('#'):
                description = line
                break
        
        return description[:1000] if description else ''
    
    def generate_sku(self, filename, index):
        """Generate SKU"""
        match = re.match(r'(\d+)-([\w-]+)\.seo\.md', filename)
        if match:
            number, code = match.groups()
            code_upper = code.upper().replace('-', '')[:10]
            return f"ANMI-{code_upper}-{number.zfill(3)}"
        return f"ANMI-PROD-{str(index).zfill(3)}"
    
    def estimate_price(self, category, title):
        """Estimate price"""
        title_lower = title.lower()
        
        if 'shrink fit' in title_lower or 'co nhiệt' in title_lower:
            return 3500000
        elif 'hydraulic' in title_lower or 'thủy lực' in title_lower:
            return 3200000
        elif 'boring' in title_lower or 'khoét' in title_lower:
            return 2800000
        elif 'hsk' in title_lower:
            return 2500000
        elif 'collet' in title_lower and 'er' not in title_lower:
            return 800000
        elif 'bt' in title_lower and 'er' in title_lower:
            return 1800000
        elif 'bt' in title_lower:
            return 1500000
        else:
            return 1200000
    
    def process_file(self, filepath, index):
        """Process single file"""
        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            frontmatter = self.parse_frontmatter(content)
            if not frontmatter:
                return None
            
            filename = filepath.name
            title = frontmatter.get('title', '').split('|')[0].strip()
            if not title:
                return None
            
            slug = frontmatter.get('slug', '')
            category = frontmatter.get('category', 'Uncategorized')
            tags = frontmatter.get('tags', '')
            seo_description = frontmatter.get('seo_description', '')
            
            sku = self.generate_sku(filename, index)
            regular_price = self.estimate_price(category, title)
            sale_price = int(regular_price * 0.9)
            
            description = self.extract_description(content)
            if not description and seo_description:
                description = seo_description
            
            short_description = seo_description[:200] if seo_description else title
            
            # WooCommerce native format
            product = {
                'ID': '',  # Empty for new products
                'Type': 'simple',
                'SKU': sku,
                'Name': title,
                'Published': '1',
                'Is featured?': '0',
                'Visibility in catalog': 'visible',
                'Short description': short_description,
                'Description': description,
                'Date sale price starts': '',
                'Date sale price ends': '',
                'Tax status': 'taxable',
                'Tax class': '',
                'In stock?': '1',
                'Stock': str(100 if index % 2 == 0 else 80),
                'Backorders allowed?': '0',
                'Sold individually?': '0',
                'Weight (kg)': '2',
                'Length (cm)': '',
                'Width (cm)': '',
                'Height (cm)': '',
                'Allow customer reviews?': '1',
                'Purchase note': '',
                'Sale price': str(sale_price),
                'Regular price': str(regular_price),
                'Categories': category,
                'Tags': tags,
                'Shipping class': '',
                'Images': f"https://anmitools.com/wp-content/uploads/products/{slug}-01.jpg",
                'Download limit': '',
                'Download expiry days': '',
                'Parent': '',
                'Grouped products': '',
                'Upsells': '',
                'Cross-sells': '',
                'External URL': '',
                'Button text': '',
                'Position': '0',
                'Attribute 1 name': 'Size',
                'Attribute 1 value(s)': 'BT30 | BT40 | BT50' if 'BT' in title.upper() and 'HSK' not in title.upper() else 'Standard',
                'Attribute 1 visible': '1',
                'Attribute 1 global': '0',
                'Attribute 2 name': 'Material',
                'Attribute 2 value(s)': 'Steel | Alloy Steel',
                'Attribute 2 visible': '1',
                'Attribute 2 global': '0',
            }
            
            return product
            
        except Exception as e:
            print(f"❌ Error: {filepath.name}: {str(e)}")
            return None
    
    def process_all_files(self):
        """Process all files"""
        input_path = Path(self.input_dir)
        
        if not input_path.exists():
            print(f"❌ Directory not found: {self.input_dir}")
            return False
        
        seo_files = sorted(input_path.glob('*.seo.md'))
        
        if not seo_files:
            print(f"❌ No .seo.md files found")
            return False
        
        print(f"📚 Found {len(seo_files)} files")
        print("🔄 Processing...\n")
        
        for index, filepath in enumerate(seo_files, start=1):
            print(f"  [{index:2d}/{len(seo_files)}] {filepath.name}")
            
            product = self.process_file(filepath, index)
            if product:
                self.products.append(product)
        
        print(f"\n✅ Processed {len(self.products)} products")
        return True
    
    def write_csv(self):
        """Write CSV in WooCommerce native format"""
        if not self.products:
            return False
        
        # Get all keys
        headers = list(self.products[0].keys())
        
        try:
            with open(self.output_csv, 'w', encoding='utf-8-sig', newline='') as f:
                writer = csv.DictWriter(f, fieldnames=headers)
                writer.writeheader()
                writer.writerows(self.products)
            
            print(f"\n✅ CSV created!")
            print(f"📄 File: {self.output_csv}")
            print(f"📊 Products: {len(self.products)}")
            print("\n" + "="*60)
            print("🎯 HƯỚNG DẪN IMPORT:")
            print("="*60)
            print("1. Vào: WooCommerce → Products")
            print("2. Click: Import ở đầu trang")
            print("3. Choose File → Select file CSV này")
            print("4. Click 'Continue'")
            print("5. Map các cột (tự động)")
            print("6. Click 'Run the importer'")
            print("7. Đợi hoàn thành!")
            print("="*60)
            
            return True
            
        except Exception as e:
            print(f"❌ Error: {str(e)}")
            return False


def main():
    print("="*60)
    print("🚀 WooCommerce Native CSV Generator")
    print("="*60)
    print()
    
    base_dir = Path(__file__).parent.parent
    input_dir = base_dir / "seo back end" / "products"
    output_csv = base_dir / "woocommerce-plugin" / "woocommerce-native-import.csv"
    
    print(f"📁 Input: {input_dir}")
    print(f"📄 Output: {output_csv}")
    print()
    
    converter = WooCommerceNativeCSV(str(input_dir), str(output_csv))
    
    if converter.process_all_files():
        converter.write_csv()
    else:
        print("\n❌ Failed")


if __name__ == '__main__':
    main()
