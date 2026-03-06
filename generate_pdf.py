#!/usr/bin/env python3
"""Generate PDF report from HTML using WeasyPrint."""
import sys
import os

# Ensure WeasyPrint can find fonts / Pango on Linux
os.environ.setdefault('FONTCONFIG_PATH', '/etc/fonts')

try:
    from weasyprint import HTML, CSS
    from weasyprint.text.fonts import FontConfiguration
except ImportError:
    print("WeasyPrint not found. Install with: pip3 install weasyprint", file=sys.stderr)
    sys.exit(1)

BASE_DIR   = os.path.dirname(os.path.abspath(__file__))
HTML_FILE  = os.path.join(BASE_DIR, 'rapport_securite.html')
PDF_FILE   = os.path.join(BASE_DIR, 'Rapport_SecureCoding_Talel_Chaanbi.pdf')

print(f"→ Lecture de : {HTML_FILE}")

font_config = FontConfiguration()

HTML(filename=HTML_FILE).write_pdf(
    PDF_FILE,
    font_config=font_config,
)

print(f"✅ PDF généré : {PDF_FILE}")
