#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Find images WITHOUT alt attribute at all
"""

import re
from pathlib import Path

ROOT_DIR = Path(__file__).parent

def find_no_alt_attribute():
    """Find images that don't have alt attribute at all"""
    files_with_no_alt = {}
    
    for html_file in ROOT_DIR.rglob('*.html'):
        try:
            with open(html_file, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
            
            # Find img tags
            img_pattern = r'<img\s+([^>]*)>'
            matches = list(re.finditer(img_pattern, content, re.IGNORECASE | re.DOTALL))
            
            no_alt_tags = []
            for match in matches:
                attrs = match.group(1)
                
                # Check if NO alt attribute at all
                has_alt_attr = bool(re.search(r'\salt=', attrs, re.IGNORECASE))
                
                if not has_alt_attr:
                    img_tag = match.group(0)
                    
                    # Extract src
                    src_match = re.search(r'src\s*=\s*(["\'])([^"\']*?)\1', attrs, re.IGNORECASE | re.DOTALL)
                    src = src_match.group(2) if src_match else 'N/A'
                    
                    tag_display = re.sub(r'\s+', ' ', img_tag)[:150]
                    
                    no_alt_tags.append({
                        'src': src,
                        'line_snippet': tag_display + ('...' if len(tag_display) > 145 else '')
                    })
            
            if no_alt_tags:
                relative_path = html_file.relative_to(ROOT_DIR)
                files_with_no_alt[str(relative_path)] = no_alt_tags
        
        except Exception as e:
            pass
    
    return files_with_no_alt

result = find_no_alt_attribute()
print(f"Files with images WITHOUT alt attribute: {len(result)}")
print(f"Total images without alt attribute: {sum(len(imgs) for imgs in result.values())}")
print()

for file_path, imgs in sorted(result.items())[:10]:  # Show first 10
    print(f"File: {file_path}")
    for i, img in enumerate(imgs[:3], 1):
        print(f"  {i}. {img['src'][:80]}")
    print()
