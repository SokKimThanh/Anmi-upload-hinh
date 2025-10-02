from pathlib import Path
from pdfminer.high_level import extract_text

pdf_path = Path(r"e:\ANMI_Dự Án bảo trì phần mềm website AnMi\Anmi-upload-hinh\PDF Upload san pham\2.1. Diamond cutting tools (V-E).pdf")
text = extract_text(str(pdf_path))
output_path = Path("pdf_text.txt")
output_path.write_text(text, encoding="utf-8")
