#!/usr/bin/env python3
"""
Script to map product names from seo.md files to catalog page numbers
"""
import os
import re
from pathlib import Path

# Map of product titles to catalog pages (verified from catalog files)
CATALOG_MAPPING = {
    # File 01-17 - BT Series
    "BT-SK High Speed Tool Holder": "1",
    "BT-GER High Speed ER Collet Chuck": "2",
    "BT-HGER High Speed ER Collet Chuck": "3",
    "BT-ER Collet Chuck Standard": "4-5",
    "BT ER Extension Holder": "5",
    "BT-C Power Chuck Tool Holder": "6",
    "BT-OZ Heavy Duty Tool Holder": "7",
    "BT-APU Drill Chuck Holder": "8",
    "BT-FMA Face Milling Arbor": "9",
    "BT-FMB Face Milling Arbor": "10",
    "BT-SLA Weldon Tool Holder": "11",
    "BT-MTA Morse Taper": "12",
    "BT-MTB Morse Taper": "12",
    "BT-SLO Oil-Feed Side Lock": "18",
    "BT-SLO": "18",
    "BT-ERO Oil-Feed ER Collet": "18",
    "BT-ERO": "18",
    "BT-SDC High Precision Tool Holder": "15",
    "BT-SR Shrink Fit Chuck": "15",
    "BT-HS Hydraulic Chuck": "16",
    
    # File 18-25 - HSK Series
    "HSK-SR Shrink Fit Chuck": "19",
    "HSK-ER Tool Holder": "20",
    "HSK-GSK Tool Holder": "21",
    "HSK-HS Hydraulic Chuck": "22",
    "HSK-FMB Face Milling Arbor": "23",
    "HSK-SLA Weldon Tool Holder": "24",
    "HSK-APU Drill Chuck Holder": "25",
    "HSK-C Power Chuck": "26",
    
    # File 26-27 - BT Tapping
    "BT Tension-Compression Tapping Holder": "27",
    "BT Tension Compression Tapping": "27",
    "BT Rigid Tapping": "27",
    
    # File 28-36 - Boring Systems
    "NBH2084 Micro Boring System": "28",
    "NBJ16 Micro Boring System": "29",
    "EWN Micro Boring Head": "30",
    "RBH Adjustable Rough Boring Head": "31",
    "CBH Large-Diameter Fine Boring Head": "32-33",
    "CBH Large Diameter Fine Boring Head": "32-33",
    "BST Twin Blade Boring Tool": "34",
    "CK/LBK Boring Bar System": "35",
    "CBS Boring Tool": "36",
    "CBS": "36",
    "SB Fixed Diameter Boring Cutter": "37",
    "GC Fixed Diameter Boring Cutter": "38",
    "CAT Tool Holder System": "39",
    
    # File 38-39 - Collets
    "ER High Precision Collet": "43",
    "SK High Precision Collet": "42",
    
    # File 40-43 - NT & Special
    "NT Tool Holder System": "13-14",
    "EWE Digital Boring Head": "40",
    "EWB Round Bit Boring Head": "41",
    
    # File 44 - Accessories
    "Accessories": "44-50",
    "Tooling Support": "44-50",
}

def get_seo_files():
    """Get all seo.md files"""
    seo_dir = Path("seo back end/seo-products")
    return sorted(seo_dir.glob("*.seo.md"))

def extract_title_from_file(filepath):
    """Extract title from markdown frontmatter"""
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
        match = re.search(r'^title:\s*["\']?(.+?)["\']?\s*$', content, re.MULTILINE)
        if match:
            return match.group(1).strip('"\'')
    return None

def has_catalog_page(filepath):
    """Check if file already has catalog_page field"""
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
        return 'catalog_page:' in content

def main():
    """Main function to list files and their mapping status"""
    print("="*80)
    print("CATALOG PAGE MAPPING STATUS")
    print("="*80)
    
    files = get_seo_files()
    total = len(files)
    has_page = 0
    needs_page = 0
    
    needs_update = []
    
    for filepath in files:
        title = extract_title_from_file(filepath)
        has_page_field = has_catalog_page(filepath)
        
        filename = filepath.name
        
        if has_page_field:
            status = "✅ HAS PAGE"
            has_page += 1
        else:
            status = "❌ NEEDS PAGE"
            needs_page += 1
            needs_update.append((filename, title))
        
        # Try to find catalog page
        catalog_page = "?"
        if title:
            for key, page in CATALOG_MAPPING.items():
                if key.lower() in title.lower() or title.lower() in key.lower():
                    catalog_page = page
                    break
        
        print(f"{status} | {filename:50} | Page {catalog_page:8} | {title}")
    
    print("\n" + "="*80)
    print(f"SUMMARY: {total} total | {has_page} with pages | {needs_page} need pages")
    print("="*80)
    
    if needs_update:
        print("\n📋 FILES THAT NEED catalog_page ADDED:")
        print("-"*80)
        for filename, title in needs_update:
            catalog_page = "?"
            if title:
                for key, page in CATALOG_MAPPING.items():
                    if key.lower() in title.lower() or title.lower() in key.lower():
                        catalog_page = page
                        break
            print(f"  {filename:50} → PAGE {catalog_page}")

if __name__ == "__main__":
    main()
