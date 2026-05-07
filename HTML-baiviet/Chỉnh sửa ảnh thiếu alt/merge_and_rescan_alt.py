#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from pathlib import Path
from datetime import datetime
import re

root = Path(__file__).resolve().parent
out_dir = root / "Chỉnh sửa ảnh thiếu alt"
out_dir.mkdir(parents=True, exist_ok=True)
final_file = out_dir / "BAO_CAO_TONG_HOP_HTML_ALT_HOAN_CHINH.txt"

target_names = [
    "Sản phẩm",
    "Menu-Contact",
    "Giải pháp theo ngành",
    "Tải về",
    "Tin tức và truyền thông",
    "trang chu",
    "Tuyển dụng nhân viên",
    "Về chúng tôi",
]

target_paths = [root / name for name in target_names if (root / name).exists()]

html_files = []
for tp in target_paths:
    html_files.extend([p for p in tp.rglob("*") if p.is_file() and p.suffix.lower() in {".html", ".htm"}])

# unique and sorted
html_files = sorted(set(html_files), key=lambda p: str(p).lower())

img_re = re.compile(r"<img\s+([^>]*?)>", re.IGNORECASE | re.DOTALL)

missing_by_file = {}
missing_total = 0

for f in html_files:
    try:
        content = f.read_text(encoding="utf-8", errors="ignore")
    except Exception:
        continue

    items = []
    for m in img_re.finditer(content):
        attrs = m.group(1)

        alt_val = None
        has_alt = re.search(r"\salt\s*=", attrs, re.IGNORECASE | re.DOTALL) is not None
        if has_alt:
            m1 = re.search(r'\salt\s*=\s*"([^"]*)"', attrs, re.IGNORECASE | re.DOTALL)
            m2 = re.search(r"\salt\s*=\s*'([^']*)'", attrs, re.IGNORECASE | re.DOTALL)
            m3 = re.search(r"\salt\s*=\s*([^\s>]+)", attrs, re.IGNORECASE | re.DOTALL)
            if m1:
                alt_val = m1.group(1)
            elif m2:
                alt_val = m2.group(1)
            elif m3:
                alt_val = m3.group(1)

        has_meaningful_alt = bool(alt_val and alt_val.strip())

        if not has_meaningful_alt:
            src = "N/A"
            s1 = re.search(r'\ssrc\s*=\s*"([^"]*)"', attrs, re.IGNORECASE | re.DOTALL)
            s2 = re.search(r"\ssrc\s*=\s*'([^']*)'", attrs, re.IGNORECASE | re.DOTALL)
            if s1:
                src = s1.group(1)
            elif s2:
                src = s2.group(1)
            items.append({"src": src, "alt": alt_val})

    if items:
        rel = f.relative_to(root).as_posix()
        missing_by_file[rel] = items
        missing_total += len(items)

seo_website_count = sum(1 for p in html_files if re.search(r"seo\s+website", str(p), re.IGNORECASE))
seo_html_count = sum(1 for p in html_files if re.search(r"seo\s+html", str(p), re.IGNORECASE))

# group by directory
by_dir = {}
for f in html_files:
    rel_dir = f.parent.relative_to(root).as_posix()
    by_dir.setdefault(rel_dir, []).append(f.name)

for k in by_dir:
    by_dir[k] = sorted(by_dir[k], key=lambda s: s.lower())

lines = []
lines.append("BAO CAO TONG HOP HTML VA ANH THIEU ALT")
lines.append(f"Ngay tao: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
lines.append("")
lines.append("I. PHAM VI QUET")
for tp in target_paths:
    lines.append(f" - {tp.relative_to(root).as_posix()}")

lines.append("")
lines.append("II. THONG KE")
lines.append(f" - Tong file HTML/HTM: {len(html_files)}")
lines.append(f" - File trong thu muc SEO WEBSITE: {seo_website_count}")
lines.append(f" - File trong thu muc SEO HTML: {seo_html_count}")
lines.append(f" - So file co anh can sua alt: {len(missing_by_file)}")
lines.append(f" - Tong anh can sua alt: {missing_total}")

lines.append("")
lines.append("III. DANH SACH FILE HTML THEO THU MUC")
for d in sorted(by_dir.keys(), key=lambda s: s.lower()):
    lines.append("")
    lines.append(f"[{d}] - {len(by_dir[d])} file")
    for name in by_dir[d]:
        lines.append(f" - {name}")

lines.append("")
lines.append("IV. DANH SACH ANH CAN SUA ALT")
if not missing_by_file:
    lines.append(" - Khong tim thay anh can sua alt trong pham vi quet.")
else:
    for file_path in sorted(missing_by_file.keys(), key=lambda s: s.lower()):
        items = missing_by_file[file_path]
        lines.append("")
        lines.append(f"[{file_path}] - {len(items)} anh")
        for idx, it in enumerate(items, start=1):
            if it["alt"] is None:
                alt_state = "KHONG CO ALT"
            elif not it["alt"].strip():
                alt_state = "alt rong"
            else:
                alt_state = "alt co noi dung"
            lines.append(f" - #{idx} | {it['src']} | {alt_state}")

legacy_candidates = [
    root / "Danh_sach_anh_thieu_alt_TOAN_DIEN.txt",
    root / "Danh_sach_file_html_theo_thu_muc.txt",
    out_dir / "Danh_sach_anh_thieu_alt.txt",
    out_dir / "chỉnh sửa ảnh thiếu alt.txt",
]
legacy_existing = [p for p in legacy_candidates if p.exists()]

lines.append("")
lines.append("V. NOI DUNG GOM TU BAO CAO TRUOC")
if not legacy_existing:
    lines.append(" - Khong co file bao cao cu de gom.")
else:
    for lf in legacy_existing:
        lines.append("")
        lines.append(f"----- BAT DAU FILE: {lf.relative_to(root).as_posix()} -----")
        try:
            lines.extend(lf.read_text(encoding="utf-8", errors="ignore").splitlines())
        except Exception:
            lines.append("(Khong doc duoc noi dung file)")
        lines.append(f"----- KET THUC FILE: {lf.relative_to(root).as_posix()} -----")

final_file.write_text("\n".join(lines), encoding="utf-8")

# delete old separate report files
to_delete = [
    root / "Danh_sach_anh_thieu_alt_TOAN_DIEN.txt",
    root / "Danh_sach_file_html_theo_thu_muc.txt",
    out_dir / "Danh_sach_anh_thieu_alt.txt",
]
deleted = []
for p in to_delete:
    if p.exists():
        p.unlink()
        deleted.append(p)

print(f"Created: {final_file}")
print(f"HTML files: {len(html_files)}")
print(f"Missing-alt files: {len(missing_by_file)}")
print(f"Missing-alt images: {missing_total}")
print(f"Legacy merged files: {len(legacy_existing)}")
print(f"Deleted old report files: {len(deleted)}")
for d in deleted:
    print(f" - {d}")
