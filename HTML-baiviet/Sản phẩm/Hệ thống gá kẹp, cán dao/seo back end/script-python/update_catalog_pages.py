#!/usr/bin/env python3
"""
Script to automatically add catalog_page to all seo.md files
"""
import os
import re
from pathlib import Path

# Catalog mapping (same as map_catalog_pages.py)
CATALOG_MAPPING = {
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
    "HSK-SR Shrink Fit Chuck": "19",
    "HSK-ER Tool Holder": "20",
    "HSK-GSK Tool Holder": "21",
    "HSK-HS Hydraulic Chuck": "22",
    "HSK-FMB Face Milling Arbor": "23",
    "HSK-SLA Weldon Tool Holder": "24",
    "HSK-APU Drill Chuck Holder": "25",
    "HSK-C Power Chuck": "26",
    "BT Tension-Compression Tapping Holder": "27",
    "BT Tension Compression Tapping": "27",
    "BT Rigid Tapping": "27",
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
    "ER High Precision Collet": "43",
    "SK High Precision Collet": "42",
    "NT Tool Holder System": "13-14",
    "EWE Digital Boring Head": "40",
    "EWB Round Bit Boring Head": "41",
    "Accessories": "44-50",
    "Tooling Support": "44-50",
}

def find_catalog_page(title):
    """Find catalog page for a given title using fuzzy matching"""
    if not title:
        return None
    
    # Direct match first
    if title in CATALOG_MAPPING:
        return CATALOG_MAPPING[title]
    
    # Fuzzy match - check if any catalog key is in the title
    title_lower = title.lower()
    matches = []
    
    for key, page in CATALOG_MAPPING.items():
        key_lower = key.lower()
        
        # Check for substring match
        if key_lower in title_lower or title_lower in key_lower:
            # Prioritize longer matches
            matches.append((len(key), page, key))
    
    if matches:
        # Sort by match length (descending) and return the best match
        matches.sort(reverse=True)
        return matches[0][1]
    
    return None

def extract_title(filepath):
    """Extract title from markdown frontmatter"""
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
        match = re.search(r'^title:\s*["\']?(.+?)["\']?\s*$', content, re.MULTILINE)
        if match:
            return match.group(1).strip('"\'')
    return None

def has_catalog_page(content):
    """Check if content already has catalog_page field"""
    return 'catalog_page:' in content

def add_catalog_page(filepath, page_number):
    """Add catalog_page field to the markdown file after category field"""
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Check if already has catalog_page
    if has_catalog_page(content):
        print(f"  ⏭️  SKIP: Already has catalog_page")
        return False
    
    # Find the category line and add catalog_page after it
    pattern = r'(category:\s*"[^"]*")'
    replacement = r'\1\ncatalog_page: "' + page_number + '"'
    
    new_content = re.sub(pattern, replacement, content, count=1)
    
    if new_content == content:
        print(f"  ❌ ERROR: Could not find category field to insert after")
        return False
    
    # Write back
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(new_content)
    
    print(f"  ✅ ADDED: catalog_page: \"{page_number}\"")
    return True

def main():
    """Main function to update all files"""
    print("="*80)
    print("AUTO-UPDATE CATALOG PAGES")
    print("="*80)
    
    seo_dir = Path("seo back end/seo-products")
    files = sorted(seo_dir.glob("*.seo.md"))
    
    updated = 0
    skipped = 0
    not_found = 0
    
    for filepath in files:
        filename = filepath.name
        title = extract_title(filepath)
        
        print(f"\n📄 {filename}")
        print(f"   Title: {title}")
        
        if not title:
            print(f"  ❌ ERROR: Could not extract title")
            not_found += 1
            continue
        
        page = find_catalog_page(title)
        
        if not page:
            print(f"  ⚠️  WARNING: No catalog page found for this product")
            not_found += 1
            continue
        
        print(f"   Found: PAGE {page}")
        
        if add_catalog_page(filepath, page):
            updated += 1
        else:
            skipped += 1
    
    print("\n" + "="*80)
    print(f"SUMMARY: {updated} updated | {skipped} skipped | {not_found} not found")
    print("="*80)

if __name__ == "__main__":
    main()
