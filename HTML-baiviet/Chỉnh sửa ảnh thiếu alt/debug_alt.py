#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Debug script to check a specific HTML file
"""

import re
from pathlib import Path

html_file = Path(r"h:\Dự Án bảo trì phần mềm website\Anmi-upload-hinh\HTML-baiviet\trang chu\dichvu.html")

with open(html_file, 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

print(f"File size: {len(content)} bytes")
print(f"Total <img tags (simple count): {content.count('<img')}")

# Find all img tags
img_pattern = r'<img\s+([^>]*)>'
matches = list(re.finditer(img_pattern, content, re.IGNORECASE | re.DOTALL))
print(f"Found {len(matches)} img tags using regex")

for i, match in enumerate(matches[:5], 1):
    img_tag = match.group(0)
    attrs = match.group(1)
    
    print(f"\n[IMG {i}]")
    print(f"Tag (first 150 chars): {img_tag[:150]}")
    
    # Check for alt
    has_alt = bool(re.search(r'\salt=\s*(["\'])([^"\']*)\1|\salt=([^\s>]+)', attrs, re.IGNORECASE))
    print(f"Has alt: {has_alt}")
    
    # Extract src
    src_match = re.search(r'src\s*=\s*(["\'])([^"\']*?)\1', attrs, re.IGNORECASE | re.DOTALL)
    src = src_match.group(2) if src_match else 'NOT FOUND'
    print(f"Src: {src}")
