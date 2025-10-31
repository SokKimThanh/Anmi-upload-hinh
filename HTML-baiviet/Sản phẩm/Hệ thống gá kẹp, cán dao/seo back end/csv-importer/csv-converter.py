#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
CSV Product Converter & Validator
An Mi Tools - Product Data Management
Version: 1.0.0
"""

import csv
import json
import sys
import os
from datetime import datetime
import re

class ProductCSVConverter:
    """Convert and validate product data between formats"""
    
    def __init__(self):
        self.required_fields = ['id', 'title', 'slug', 'category']
        self.optional_fields = [
            'primary_keyword', 'status', 'file_md', 'file_html', 
            'file_css', 'seo_title', 'seo_description', 'tags', 
            'price', 'stock'
        ]
        self.all_fields = self.required_fields + self.optional_fields
        
    def json_to_csv(self, json_file, csv_file):
        """Convert products-list.json to CSV format"""
        try:
            # Read JSON
            with open(json_file, 'r', encoding='utf-8') as f:
                data = json.load(f)
            
            products = data.get('products', [])
            
            if not products:
                print("❌ No products found in JSON file")
                return False
            
            # Write CSV
            with open(csv_file, 'w', encoding='utf-8-sig', newline='') as f:
                writer = csv.DictWriter(f, fieldnames=self.all_fields, extrasaction='ignore')
                writer.writeheader()
                
                for product in products:
                    # Fill missing fields with empty string
                    row = {field: product.get(field, '') for field in self.all_fields}
                    writer.writerow(row)
            
            print(f"✅ Converted {len(products)} products to CSV")
            print(f"📄 Output file: {csv_file}")
            return True
            
        except Exception as e:
            print(f"❌ Error converting JSON to CSV: {str(e)}")
            return False
    
    def csv_to_json(self, csv_file, json_file):
        """Convert CSV to products-list.json format"""
        try:
            products = []
            
            # Read CSV
            with open(csv_file, 'r', encoding='utf-8-sig') as f:
                reader = csv.DictReader(f)
                
                for row in reader:
                    # Clean and validate data
                    product = {}
                    
                    # Convert ID to integer
                    if 'id' in row and row['id']:
                        product['id'] = int(row['id'])
                    
                    # Add other fields if not empty
                    for field in self.all_fields:
                        if field != 'id' and field in row and row[field]:
                            value = row[field].strip()
                            
                            # Convert numeric fields
                            if field in ['price', 'stock']:
                                try:
                                    product[field] = float(value) if field == 'price' else int(value)
                                except:
                                    product[field] = value
                            else:
                                product[field] = value
                    
                    products.append(product)
            
            # Create JSON structure
            json_data = {
                "metadata": {
                    "title": "Danh sách sản phẩm An Mi Tools - Hệ thống Holder CNC",
                    "version": "1.0.0",
                    "date_created": datetime.now().strftime("%Y-%m-%d"),
                    "date_modified": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
                    "description": "Danh sách sản phẩm được chuyển đổi từ CSV",
                    "total_products": len(products)
                },
                "products": products
            }
            
            # Write JSON
            with open(json_file, 'w', encoding='utf-8') as f:
                json.dump(json_data, f, ensure_ascii=False, indent=2)
            
            print(f"✅ Converted {len(products)} products to JSON")
            print(f"📄 Output file: {json_file}")
            return True
            
        except Exception as e:
            print(f"❌ Error converting CSV to JSON: {str(e)}")
            return False
    
    def validate_csv(self, csv_file):
        """Validate CSV file structure and data"""
        errors = []
        warnings = []
        stats = {'total': 0, 'valid': 0, 'invalid': 0}
        
        try:
            with open(csv_file, 'r', encoding='utf-8-sig') as f:
                reader = csv.DictReader(f)
                
                # Check headers
                headers = reader.fieldnames
                if not headers:
                    errors.append("CSV file has no headers")
                    return False, errors, warnings, stats
                
                # Check required fields
                missing_fields = [field for field in self.required_fields if field not in headers]
                if missing_fields:
                    errors.append(f"Missing required fields: {', '.join(missing_fields)}")
                    return False, errors, warnings, stats
                
                # Validate each row
                seen_ids = set()
                seen_slugs = set()
                
                for idx, row in enumerate(reader, start=2):  # Start at 2 (header is line 1)
                    stats['total'] += 1
                    row_errors = []
                    
                    # Check ID
                    if not row.get('id'):
                        row_errors.append(f"Line {idx}: Missing ID")
                    else:
                        try:
                            product_id = int(row['id'])
                            if product_id in seen_ids:
                                row_errors.append(f"Line {idx}: Duplicate ID {product_id}")
                            seen_ids.add(product_id)
                        except ValueError:
                            row_errors.append(f"Line {idx}: Invalid ID (must be integer)")
                    
                    # Check title
                    if not row.get('title') or not row['title'].strip():
                        row_errors.append(f"Line {idx}: Missing or empty title")
                    
                    # Check slug
                    if row.get('slug'):
                        slug = row['slug'].strip()
                        if not re.match(r'^[a-z0-9-]+$', slug):
                            warnings.append(f"Line {idx}: Slug contains invalid characters (should be lowercase letters, numbers, hyphens only)")
                        if slug in seen_slugs:
                            row_errors.append(f"Line {idx}: Duplicate slug '{slug}'")
                        seen_slugs.add(slug)
                    
                    # Check category
                    if not row.get('category') or not row['category'].strip():
                        warnings.append(f"Line {idx}: Missing category")
                    
                    # Check numeric fields
                    if row.get('price'):
                        try:
                            price = float(row['price'])
                            if price < 0:
                                warnings.append(f"Line {idx}: Price is negative")
                        except ValueError:
                            warnings.append(f"Line {idx}: Invalid price format")
                    
                    if row.get('stock'):
                        try:
                            stock = int(row['stock'])
                            if stock < 0:
                                warnings.append(f"Line {idx}: Stock is negative")
                        except ValueError:
                            warnings.append(f"Line {idx}: Invalid stock format")
                    
                    if row_errors:
                        errors.extend(row_errors)
                        stats['invalid'] += 1
                    else:
                        stats['valid'] += 1
                
                is_valid = len(errors) == 0
                return is_valid, errors, warnings, stats
                
        except Exception as e:
            errors.append(f"Error reading CSV file: {str(e)}")
            return False, errors, warnings, stats
    
    def generate_slug(self, title):
        """Generate URL-friendly slug from title"""
        # Vietnamese to Latin conversion
        vietnamese_map = {
            'à': 'a', 'á': 'a', 'ạ': 'a', 'ả': 'a', 'ã': 'a',
            'â': 'a', 'ầ': 'a', 'ấ': 'a', 'ậ': 'a', 'ẩ': 'a', 'ẫ': 'a',
            'ă': 'a', 'ằ': 'a', 'ắ': 'a', 'ặ': 'a', 'ẳ': 'a', 'ẵ': 'a',
            'è': 'e', 'é': 'e', 'ẹ': 'e', 'ẻ': 'e', 'ẽ': 'e',
            'ê': 'e', 'ề': 'e', 'ế': 'e', 'ệ': 'e', 'ể': 'e', 'ễ': 'e',
            'ì': 'i', 'í': 'i', 'ị': 'i', 'ỉ': 'i', 'ĩ': 'i',
            'ò': 'o', 'ó': 'o', 'ọ': 'o', 'ỏ': 'o', 'õ': 'o',
            'ô': 'o', 'ồ': 'o', 'ố': 'o', 'ộ': 'o', 'ổ': 'o', 'ỗ': 'o',
            'ơ': 'o', 'ờ': 'o', 'ớ': 'o', 'ợ': 'o', 'ở': 'o', 'ỡ': 'o',
            'ù': 'u', 'ú': 'u', 'ụ': 'u', 'ủ': 'u', 'ũ': 'u',
            'ư': 'u', 'ừ': 'u', 'ứ': 'u', 'ự': 'u', 'ử': 'u', 'ữ': 'u',
            'ỳ': 'y', 'ý': 'y', 'ỵ': 'y', 'ỷ': 'y', 'ỹ': 'y',
            'đ': 'd', 'Đ': 'D'
        }
        
        slug = title.lower()
        for viet, latin in vietnamese_map.items():
            slug = slug.replace(viet, latin)
        
        # Remove special characters and replace spaces with hyphens
        slug = re.sub(r'[^a-z0-9\s-]', '', slug)
        slug = re.sub(r'[\s-]+', '-', slug)
        slug = slug.strip('-')
        
        return slug
    
    def auto_fix_csv(self, input_csv, output_csv):
        """Auto-fix common issues in CSV file"""
        fixed_count = 0
        
        try:
            products = []
            
            with open(input_csv, 'r', encoding='utf-8-sig') as f:
                reader = csv.DictReader(f)
                
                for row in reader:
                    # Generate slug if missing
                    if not row.get('slug') or not row['slug'].strip():
                        if row.get('title'):
                            row['slug'] = self.generate_slug(row['title'])
                            fixed_count += 1
                    
                    # Set default status
                    if not row.get('status') or not row['status'].strip():
                        row['status'] = 'pending'
                        fixed_count += 1
                    
                    # Generate file names if missing
                    if row.get('slug') and row.get('id'):
                        slug = row['slug']
                        product_id = row['id'].zfill(2)
                        
                        if not row.get('file_md'):
                            row['file_md'] = f"{product_id}-{slug}.seo.md"
                            fixed_count += 1
                        
                        if not row.get('file_html'):
                            row['file_html'] = f"{product_id}-{slug}.seo.html"
                            fixed_count += 1
                        
                        if not row.get('file_css'):
                            row['file_css'] = f"{slug}.css"
                            fixed_count += 1
                    
                    products.append(row)
            
            # Write fixed CSV
            with open(output_csv, 'w', encoding='utf-8-sig', newline='') as f:
                writer = csv.DictWriter(f, fieldnames=self.all_fields, extrasaction='ignore')
                writer.writeheader()
                writer.writerows(products)
            
            print(f"✅ Fixed {fixed_count} issues")
            print(f"📄 Output file: {output_csv}")
            return True
            
        except Exception as e:
            print(f"❌ Error fixing CSV: {str(e)}")
            return False


def main():
    """Main function"""
    converter = ProductCSVConverter()
    
    if len(sys.argv) < 2:
        print("📚 CSV Product Converter & Validator")
        print("\nUsage:")
        print("  python csv-converter.py validate <csv_file>")
        print("  python csv-converter.py json2csv <json_file> <output_csv>")
        print("  python csv-converter.py csv2json <csv_file> <output_json>")
        print("  python csv-converter.py fix <input_csv> <output_csv>")
        print("\nExamples:")
        print("  python csv-converter.py validate sample-products.csv")
        print("  python csv-converter.py json2csv ../products-list.json products.csv")
        print("  python csv-converter.py csv2json products.csv ../products-list.json")
        print("  python csv-converter.py fix input.csv output.csv")
        return
    
    command = sys.argv[1].lower()
    
    if command == 'validate':
        if len(sys.argv) < 3:
            print("❌ Missing CSV file argument")
            return
        
        csv_file = sys.argv[2]
        print(f"🔍 Validating {csv_file}...")
        
        is_valid, errors, warnings, stats = converter.validate_csv(csv_file)
        
        print(f"\n📊 Statistics:")
        print(f"  Total rows: {stats['total']}")
        print(f"  Valid rows: {stats['valid']}")
        print(f"  Invalid rows: {stats['invalid']}")
        
        if warnings:
            print(f"\n⚠️  Warnings ({len(warnings)}):")
            for warning in warnings[:10]:  # Show first 10
                print(f"  - {warning}")
            if len(warnings) > 10:
                print(f"  ... and {len(warnings) - 10} more")
        
        if errors:
            print(f"\n❌ Errors ({len(errors)}):")
            for error in errors[:10]:  # Show first 10
                print(f"  - {error}")
            if len(errors) > 10:
                print(f"  ... and {len(errors) - 10} more")
            print("\n❌ Validation FAILED")
        else:
            print("\n✅ Validation PASSED")
    
    elif command == 'json2csv':
        if len(sys.argv) < 4:
            print("❌ Missing arguments")
            print("Usage: python csv-converter.py json2csv <json_file> <output_csv>")
            return
        
        json_file = sys.argv[2]
        csv_file = sys.argv[3]
        converter.json_to_csv(json_file, csv_file)
    
    elif command == 'csv2json':
        if len(sys.argv) < 4:
            print("❌ Missing arguments")
            print("Usage: python csv-converter.py csv2json <csv_file> <output_json>")
            return
        
        csv_file = sys.argv[2]
        json_file = sys.argv[3]
        converter.csv_to_json(csv_file, json_file)
    
    elif command == 'fix':
        if len(sys.argv) < 4:
            print("❌ Missing arguments")
            print("Usage: python csv-converter.py fix <input_csv> <output_csv>")
            return
        
        input_csv = sys.argv[2]
        output_csv = sys.argv[3]
        print(f"🔧 Auto-fixing {input_csv}...")
        converter.auto_fix_csv(input_csv, output_csv)
    
    else:
        print(f"❌ Unknown command: {command}")
        print("Valid commands: validate, json2csv, csv2json, fix")


if __name__ == '__main__':
    main()
