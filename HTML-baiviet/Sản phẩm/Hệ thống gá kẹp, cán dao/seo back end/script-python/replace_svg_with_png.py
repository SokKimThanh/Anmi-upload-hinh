#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script: replace_svg_with_png.py

Replace the inline SVG with a PNG image for elements with class="download-icon"
Searches .html and .md files under the repo subfolders 'seo html' and 'seo back end' and updates them in-place.

Usage:
  python replace_svg_with_png.py

This script will create a .bak backup for each file it modifies.
"""

import re
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent.parent.parent

# Directories to scan (relative to ROOT_DIR)
SCAN_DIRS = [ROOT_DIR / 'seo html', ROOT_DIR / 'seo back end']

# Regex to find the <svg ...class="download-icon"...>...</svg> block (non-greedy)
SVG_RE = re.compile(r'<svg\b(?:(?!</svg>).)*?class="download-icon"(?:(?!</svg>).)*?</svg>', re.IGNORECASE | re.DOTALL)

PNG_TAG = '<img class="download-icon" src="https://anmitools.com/wp-content/uploads/2025/11/download.png" alt="Download catalog" width="20" height="20" loading="lazy">'

def process_file(path: Path):
    try:
        text = path.read_text(encoding='utf-8')
    except Exception as e:
        return False, f'Failed to read: {e}'

    if 'class="download-icon"' not in text:
        return None, 'no download-icon'

    new_text, n = SVG_RE.subn(PNG_TAG, text)

    if n == 0:
        return None, 'no svg match'

    # backup
    bak = path.with_suffix(path.suffix + '.bak')
    bak.write_text(text, encoding='utf-8')

    # write new
    path.write_text(new_text, encoding='utf-8')
    return True, f'replaced {n} svg(s)'

def main():
    total = 0
    updated = 0
    skipped = 0
    errors = 0

    for d in SCAN_DIRS:
        if not d.exists():
            print(f'⏭️  Skipping missing dir: {d}')
            continue

        for path in sorted(d.rglob('*')):
            if not path.is_file():
                continue
            if path.suffix.lower() not in ('.html', '.htm', '.md'):
                continue

            total += 1
            result, msg = process_file(path)
            if result is True:
                print(f'✅ {path.relative_to(ROOT_DIR)} - {msg}')
                updated += 1
            elif result is None:
                print(f'⏭️  {path.relative_to(ROOT_DIR)} - {msg}')
                skipped += 1
            else:
                print(f'❌ {path.relative_to(ROOT_DIR)} - {msg}')
                errors += 1

    print('\nSummary:')
    print(f'  Scanned files: {total}')
    print(f'  Updated: {updated}')
    print(f'  Skipped: {skipped}')
    print(f'  Errors: {errors}')

if __name__ == '__main__':
    main()
