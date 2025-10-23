#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
SEO Markdown to WooCommerce CSV Converter
Đọc tất cả file .seo.md và tạo CSV cho WooCommerce import
"""

import os
import re
import csv
from pathlib import Path

class SEOMarkdownToCSV:
    def __init__(self, input_dir, output_csv):
        self.input_dir = input_dir
        self.output_csv = output_csv
        self.products = []
        
    def parse_frontmatter(self, content):
        """Parse YAML frontmatter từ markdown"""
        frontmatter = {}
        
        # Extract frontmatter between ---
        match = re.search(r'^---\s*\n(.*?)\n---', content, re.DOTALL)
        if not match:
            return frontmatter
        
        yaml_content = match.group(1)
        
        # Parse YAML fields
        for line in yaml_content.split('\n'):
            line = line.strip()
            if ':' in line:
                key, value = line.split(':', 1)
                key = key.strip()
                value = value.strip().strip('"').strip("'")
                frontmatter[key] = value
        
        return frontmatter
    
    def extract_description(self, content):
        """Extract mô tả từ nội dung markdown"""
        # Remove frontmatter
        content = re.sub(r'^---\s*\n.*?\n---', '', content, flags=re.DOTALL)
        
        # Remove HTML tags
        content = re.sub(r'<[^>]+>', '', content)
        
        # Get first meaningful paragraph
        lines = [line.strip() for line in content.split('\n') if line.strip()]
        
        # Find first paragraph with substantial content
        description = ''
        for line in lines:
            if len(line) > 50 and not line.startswith('#'):
                description = line
                break
        
        return description[:500] if description else ''
    
    def generate_sku(self, filename, index):
        """Generate SKU from filename"""
        # Extract product code from filename
        match = re.match(r'(\d+)-([\w-]+)\.seo\.md', filename)
        if match:
            number, code = match.groups()
            # Create SKU format: ANMI-CODE-NUM
            code_upper = code.upper().replace('-', '')[:10]
            return f"ANMI-{code_upper}-{number.zfill(3)}"
        return f"ANMI-PROD-{str(index).zfill(3)}"
    
    def estimate_price(self, category, title):
        """Estimate price based on category and product type"""
        title_lower = title.lower()
        
        # Price ranges based on product type
        if 'shrink fit' in title_lower or 'co nhiệt' in title_lower:
            return 3500000  # High precision shrink fit
        elif 'hydraulic' in title_lower or 'thủy lực' in title_lower:
            return 3200000  # Hydraulic chuck
        elif 'boring' in title_lower or 'khoét' in title_lower:
            return 2800000  # Boring systems
        elif 'hsk' in title_lower:
            return 2500000  # HSK holders
        elif 'collet' in title_lower and 'er' not in title_lower:
            return 800000   # Collets
        elif 'bt' in title_lower and 'er' in title_lower:
            return 1800000  # BT-ER holders
        elif 'bt' in title_lower:
            return 1500000  # Standard BT holders
        else:
            return 1200000  # Default price
    
    def estimate_stock(self, index):
        """Estimate stock quantity"""
        # Vary stock quantities realistically
        if index % 5 == 0:
            return 150
        elif index % 3 == 0:
            return 80
        elif index % 2 == 0:
            return 100
        else:
            return 60
    
    def process_file(self, filepath, index):
        """Process single SEO markdown file"""
        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Parse frontmatter
            frontmatter = self.parse_frontmatter(content)
            
            if not frontmatter:
                print(f"⚠️  No frontmatter found in {filepath.name}")
                return None
            
            filename = filepath.name
            
            # Extract data
            title = frontmatter.get('title', '').split('|')[0].strip()
            if not title:
                print(f"⚠️  No title in {filepath.name}")
                return None
            
            slug = frontmatter.get('slug', '')
            category = frontmatter.get('category', 'Tool Holders')
            tags = frontmatter.get('tags', frontmatter.get('primary_keyword', ''))
            seo_title = frontmatter.get('seo_title', title)
            seo_description = frontmatter.get('seo_description', '')
            
            # Generate or extract additional data
            sku = self.generate_sku(filename, index)
            regular_price = self.estimate_price(category, title)
            sale_price = int(regular_price * 0.9)  # 10% discount
            stock_quantity = self.estimate_stock(index)
            
            # Extract description from content
            description = self.extract_description(content)
            if not description and seo_description:
                description = seo_description
            
            # Short description (first 200 chars of SEO description)
            short_description = seo_description[:200] if seo_description else title
            
            # Images (placeholder - bạn có thể thêm logic tìm ảnh thật)
            image_base = f"https://anmitools.com/wp-content/uploads/products/{slug}"
            images = f"{image_base}-01.jpg, {image_base}-02.jpg"
            
            # Attributes based on product type
            attributes = self.generate_attributes(title, category)
            
            # Product data
            product = {
                'sku': sku,
                'name': title,
                'slug': slug,
                'description': description,
                'short_description': short_description,
                'regular_price': regular_price,
                'sale_price': sale_price,
                'stock_quantity': stock_quantity,
                'categories': category,
                'tags': tags,
                'images': images,
                'status': 'publish',
                'attributes': attributes
            }
            
            return product
            
        except Exception as e:
            print(f"❌ Error processing {filepath.name}: {str(e)}")
            return None
    
    def generate_attributes(self, title, category):
        """Generate product attributes based on title and category"""
        attributes = []
        title_lower = title.lower()
        
        # Size/Type attributes
        if 'bt' in title_lower and 'hsk' not in title_lower:
            attributes.append("Size:BT30,BT40,BT50")
        elif 'hsk' in title_lower:
            attributes.append("Type:HSK-A63,HSK-A100,HSK-E63")
        
        # Collet sizes
        if 'er' in title_lower and 'collet' in title_lower:
            attributes.append("Collet Size:ER8,ER11,ER16,ER20,ER25,ER32,ER40")
        elif 'sk' in title_lower and 'collet' in title_lower:
            attributes.append("Collet Size:SK6,SK10,SK16,SK20")
        
        # Material
        attributes.append("Material:Steel,Alloy Steel")
        
        # Balance grade for high speed
        if 'high speed' in title_lower or 'tốc độ cao' in title_lower:
            attributes.append("Balance Grade:G2.5,G6.3")
        
        # Precision level
        if 'precision' in title_lower or 'chính xác' in title_lower:
            attributes.append("Precision:±0.003mm,±0.005mm")
        
        return '|'.join(attributes) if attributes else "Type:Standard|Material:Steel"
    
    def process_all_files(self):
        """Process all SEO markdown files"""
        input_path = Path(self.input_dir)
        
        if not input_path.exists():
            print(f"❌ Directory not found: {self.input_dir}")
            return False
        
        # Get all .seo.md files
        seo_files = sorted(input_path.glob('*.seo.md'))
        
        if not seo_files:
            print(f"❌ No .seo.md files found in {self.input_dir}")
            return False
        
        print(f"📚 Found {len(seo_files)} SEO markdown files")
        print("🔄 Processing files...\n")
        
        # Process each file
        for index, filepath in enumerate(seo_files, start=1):
            print(f"  [{index:2d}/{len(seo_files)}] Processing: {filepath.name}")
            
            product = self.process_file(filepath, index)
            if product:
                self.products.append(product)
        
        print(f"\n✅ Successfully processed {len(self.products)} products")
        return True
    
    def write_csv(self):
        """Write products to CSV file"""
        if not self.products:
            print("❌ No products to write")
            return False
        
        # CSV headers
        headers = [
            'sku', 'name', 'slug', 'description', 'short_description',
            'regular_price', 'sale_price', 'stock_quantity',
            'categories', 'tags', 'images', 'status', 'attributes'
        ]
        
        try:
            with open(self.output_csv, 'w', encoding='utf-8-sig', newline='') as f:
                writer = csv.DictWriter(f, fieldnames=headers)
                writer.writeheader()
                writer.writerows(self.products)
            
            print(f"\n✅ CSV file created successfully!")
            print(f"📄 File: {self.output_csv}")
            print(f"📊 Total products: {len(self.products)}")
            
            # Display summary
            self.print_summary()
            
            return True
            
        except Exception as e:
            print(f"❌ Error writing CSV: {str(e)}")
            return False
    
    def print_summary(self):
        """Print summary statistics"""
        if not self.products:
            return
        
        print("\n" + "="*60)
        print("📊 SUMMARY STATISTICS")
        print("="*60)
        
        # Count by category
        categories = {}
        for product in self.products:
            cat = product['categories']
            categories[cat] = categories.get(cat, 0) + 1
        
        print("\n📂 Products by Category:")
        for cat, count in sorted(categories.items()):
            print(f"   • {cat}: {count} products")
        
        # Price range
        prices = [p['regular_price'] for p in self.products]
        print(f"\n💰 Price Range:")
        print(f"   • Min: {min(prices):,.0f} VND")
        print(f"   • Max: {max(prices):,.0f} VND")
        print(f"   • Average: {sum(prices)/len(prices):,.0f} VND")
        
        # Stock total
        total_stock = sum(p['stock_quantity'] for p in self.products)
        print(f"\n📦 Total Stock: {total_stock:,} units")
        
        print("\n" + "="*60)


def main():
    """Main function"""
    print("="*60)
    print("🚀 SEO Markdown to WooCommerce CSV Converter")
    print("="*60)
    print()
    
    # Paths
    base_dir = Path(__file__).parent.parent
    input_dir = base_dir / "seo back end" / "products"
    output_csv = base_dir / "woocommerce-plugin" / "anmi-products-full-import.csv"
    
    print(f"📁 Input directory: {input_dir}")
    print(f"📄 Output CSV: {output_csv}")
    print()
    
    # Create converter
    converter = SEOMarkdownToCSV(str(input_dir), str(output_csv))
    
    # Process files
    if converter.process_all_files():
        # Write CSV
        converter.write_csv()
        
        print("\n✅ Done! You can now import this CSV into WooCommerce.")
        print("💡 Go to: WooCommerce → 📊 Nhập CSV")
    else:
        print("\n❌ Failed to process files")


if __name__ == '__main__':
    main()
