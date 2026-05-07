#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from pathlib import Path
from html.parser import HTMLParser
from datetime import datetime

ROOT = Path(__file__).resolve().parent
OUT_DIR = ROOT / "Chỉnh sửa ảnh thiếu alt"
OUT_FILE = OUT_DIR / "KIEM_TRA_LAN_2_HTMLPARSER.txt"

TARGETS = [
    "Sản phẩm",
    "Menu-Contact",
    "Giải pháp theo ngành",
    "Tải về",
    "Tin tức và truyền thông",
    "trang chu",
    "Tuyển dụng nhân viên",
    "Về chúng tôi",
]

class ImgAltParser(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.missing = []

    def handle_starttag(self, tag, attrs):
        if tag.lower() != "img":
            return
        d = {k.lower(): (v if v is not None else "") for k, v in attrs}
        src = d.get("src", "N/A")
        if "alt" not in d:
            self.missing.append((src, "KHONG_CO_ALT"))
        elif not d.get("alt", "").strip():
            self.missing.append((src, "ALT_RONG"))


def html_files():
    files = []
    for t in TARGETS:
        p = ROOT / t
        if p.exists():
            files.extend([f for f in p.rglob("*") if f.is_file() and f.suffix.lower() in {".html", ".htm"}])
    return sorted(set(files), key=lambda x: str(x).lower())


def main():
    files = html_files()
    by_file = {}
    total_missing = 0

    for f in files:
        try:
            text = f.read_text(encoding="utf-8", errors="ignore")
            p = ImgAltParser()
            p.feed(text)
            p.close()
        except Exception:
            continue

        if p.missing:
            rel = f.relative_to(ROOT).as_posix()
            by_file[rel] = p.missing
            total_missing += len(p.missing)

    lines = []
    lines.append("KIEM TRA LAN 2 BANG HTML PARSER")
    lines.append(f"Ngay tao: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    lines.append(f"Tong file HTML/HTM da quet: {len(files)}")
    lines.append(f"So file co anh thieu alt/alt rong: {len(by_file)}")
    lines.append(f"Tong so anh thieu alt/alt rong: {total_missing}")
    lines.append("")

    for fp in sorted(by_file.keys(), key=lambda s: s.lower()):
        items = by_file[fp]
        lines.append(f"[{fp}] - {len(items)} anh")
        for i, (src, state) in enumerate(items, 1):
            lines.append(f" - #{i} | {src} | {state}")
        lines.append("")

    OUT_DIR.mkdir(parents=True, exist_ok=True)
    OUT_FILE.write_text("\n".join(lines), encoding="utf-8")

    print(f"Created: {OUT_FILE}")
    print(f"Scanned files: {len(files)}")
    print(f"Missing-alt files: {len(by_file)}")
    print(f"Missing-alt images: {total_missing}")

if __name__ == "__main__":
    main()
