import json
from pathlib import Path

try:
    from PyPDF2 import PdfReader
except ImportError:
    raise SystemExit("PyPDF2 module missing")

pdf_path = Path(r"e:\ANMI_Dự Án bảo trì phần mềm website AnMi\Anmi-upload-hinh\PDF Upload san pham\2.1. Diamond cutting tools (V-E).pdf")
reader = PdfReader(pdf_path)
output = []
for index, page in enumerate(reader.pages, start=1):
    text = page.extract_text() or ""
    output.append({"page": index, "text": text})

print(json.dumps(output, ensure_ascii=False))
