from pathlib import Path
from PyPDF2 import PdfReader

pdf_path = Path(r"e:\ANMI_Dự Án bảo trì phần mềm website AnMi\Anmi-upload-hinh\PDF Upload san pham\2.1. Diamond cutting tools (V-E).pdf")
reader = PdfReader(pdf_path)
found = False
for index, page in enumerate(reader.pages, start=1):
    text = page.extract_text() or ""
    if "IDCCGW" in text.upper():
        print(f"--- PAGE {index} ---")
        print(text)
        found = True
if not found:
    print("IDCCGW_NOT_FOUND")
