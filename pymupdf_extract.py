import fitz

PDF_PATH = "PDF Upload san pham/2.1. Diamond cutting tools (V-E).pdf"
OUTPUT_PATH = "pdf_text_fitz.txt"


def extract_pdf_text(pdf_path: str, output_path: str) -> None:
    doc = fitz.open(pdf_path)
    try:
        with open(output_path, "w", encoding="utf-8") as f:
            for page_num, page in enumerate(doc, start=1):
                text = page.get_text("text")
                f.write(f"\n--- Page {page_num} ---\n")
                f.write(text)
                f.write("\n")
    finally:
        doc.close()


if __name__ == "__main__":
    extract_pdf_text(PDF_PATH, OUTPUT_PATH)
    print(f"Text exported to {OUTPUT_PATH}")
