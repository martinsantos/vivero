#!/usr/bin/env python3
"""Generate clean ecommerce renders for every product in the MACETAS category.

The input TSV is produced from WP-CLI with:
product_id, name, sku, image_id, image_url.

The generated images are original neutral product renders. They are intended to
replace clearly wrong catalog photos such as landscapes, people, plants, or
unrelated objects shown inside the Macetas category.
"""

from __future__ import annotations

import argparse
import html
import json
import re
import unicodedata
from pathlib import Path
from typing import Dict, Iterable, List, Tuple

from PIL import Image, ImageDraw, ImageFilter, ImageFont


ROOT = Path(__file__).resolve().parents[2]
DEFAULT_INPUT = Path("/private/tmp/macetas-products.tsv")
DEFAULT_OUTPUT = ROOT / "assets" / "product-images" / "macetas-generated"


COLORS: Dict[str, str] = {
    "amarillo": "#e6bd35",
    "amarilla": "#e6bd35",
    "a": "#e6bd35",
    "azul": "#406da8",
    "beige": "#c7ad84",
    "blanco": "#f5f3ea",
    "blanca": "#f5f3ea",
    "bl": "#f5f3ea",
    "crema": "#dfd1b8",
    "cre": "#dfd1b8",
    "gris claro": "#bbc1bb",
    "gcl": "#bbc1bb",
    "gris oscuro": "#68706a",
    "gos": "#68706a",
    "l": "#dfd1b8",
    "marron claro": "#9a6a43",
    "marrón claro": "#9a6a43",
    "mc": "#9a6a43",
    "marron oscuro": "#513526",
    "marrón oscuro": "#513526",
    "mo": "#513526",
    "marron terracota": "#a85032",
    "marrón terracota": "#a85032",
    "mt": "#a85032",
    "naranja": "#d87528",
    "negro": "#232624",
    "negra": "#232624",
    "ne": "#232624",
    "rojo": "#b74235",
    "roja": "#b74235",
    "r": "#b74235",
    "rosa": "#d99aaa",
    "ro": "#d99aaa",
    "verde aqua": "#86b8a7",
    "va": "#86b8a7",
    "vaq": "#86b8a7",
    "verde claro": "#91c783",
    "vc": "#91c783",
    "verde oscuro": "#355b42",
    "vo": "#355b42",
    "violeta": "#7d6599",
    "vi": "#7d6599",
    "vol": "#7d6599",
}


def normalize(value: str) -> str:
    value = html.unescape(str(value or ""))
    value = value.replace("&#215;", "x")
    value = unicodedata.normalize("NFKD", value)
    value = "".join(ch for ch in value if not unicodedata.combining(ch))
    return re.sub(r"\s+", " ", value).strip().lower()


def display(value: str) -> str:
    return re.sub(r"\s+", " ", html.unescape(str(value or "")).replace("&#215;", "x")).strip()


def slugify(value: str) -> str:
    value = normalize(value)
    value = re.sub(r"[^a-z0-9]+", "-", value).strip("-")
    return value[:92] or "maceta"


def font(size: int, bold: bool = False) -> ImageFont.FreeTypeFont:
    candidates = [
        "/System/Library/Fonts/Supplemental/Arial Bold.ttf" if bold else "/System/Library/Fonts/Supplemental/Arial.ttf",
        "/System/Library/Fonts/Supplemental/Helvetica.ttc",
        "/Library/Fonts/Arial.ttf",
    ]
    for path in candidates:
        try:
            return ImageFont.truetype(path, size=size)
        except Exception:
            continue
    return ImageFont.load_default()


def rgb(hex_color: str) -> Tuple[int, int, int]:
    value = hex_color.lstrip("#")
    return tuple(int(value[i:i + 2], 16) for i in (0, 2, 4))


def adjust(color: Tuple[int, int, int], amount: int) -> Tuple[int, int, int]:
    return tuple(max(0, min(255, c + amount)) for c in color)


def canvas() -> Image.Image:
    img = Image.new("RGBA", (1200, 1200), "#f6f7f1")
    d = ImageDraw.Draw(img)
    d.rounded_rectangle((62, 62, 1138, 1138), radius=20, outline="#dfe6dc", width=3)
    return img


def add_label(img: Image.Image, title: str, subtitle: str) -> None:
    d = ImageDraw.Draw(img)
    title = display(title)
    subtitle = display(subtitle)
    if len(title) > 44:
        title = title[:41].rstrip() + "..."
    d.text((94, 88), title, fill="#17231d", font=font(38, True))
    d.text((94, 140), subtitle, fill="#66746b", font=font(25))


def shadow(img: Image.Image, box: Tuple[int, int, int, int], blur: int = 36) -> None:
    layer = Image.new("RGBA", img.size, (0, 0, 0, 0))
    d = ImageDraw.Draw(layer)
    d.ellipse(box, fill=(0, 0, 0, 42))
    img.alpha_composite(layer.filter(ImageFilter.GaussianBlur(blur)))


def infer_color(name: str, sku: str) -> Tuple[str, str]:
    text = normalize(f"{name} {sku}")
    match = re.search(r"color:\s*([a-záéíóúñ ]+)", text)
    candidates = [match.group(1).strip()] if match else []
    candidates.extend(COLORS.keys())
    for key in candidates:
        if key and re.search(rf"\b{re.escape(normalize(key))}\b", text):
            label = key.title().replace("Mc", "Marron Claro").replace("Mt", "Marron Terracota")
            return COLORS[key], label
    suffix = re.search(r"(bl|ne|mc|mt|be|gcl|gos|vc|vaq|vi|vol|az|cre|ro|vo|am|na|r)$", normalize(sku))
    if suffix and suffix.group(1) in COLORS:
        key = suffix.group(1)
        return COLORS[key], key.upper()
    return "#9a6a43", "Natural"


def infer_measure(name: str, sku: str) -> str:
    text = normalize(f"{name} {sku}")
    match = re.search(r"(\d{1,3})\s*x\s*(\d{1,3})", text)
    if match:
        return f"{match.group(1)}x{match.group(2)} cm"
    match = re.search(r"(\d{1,3})\s*cm", text)
    if match:
        return f"{match.group(1)} cm"
    match = re.search(r"(\d{1,3})\s*litros?", text)
    if match:
        return f"{match.group(1)} litros"
    match = re.search(r"(\d{2})(?:bl|ne|mc|mt|be|gcl|gos|vc|vaq|vi|vol|az|cre|ro|vo|am|na|r)$", normalize(sku))
    if match:
        return f"{match.group(1)} cm"
    return "medida indicada"


def product_family(name: str, sku: str) -> str:
    text = normalize(f"{name} {sku}")
    if "gancho" in text or text.startswith("gtap"):
        return "hook"
    if "regadera" in text:
        return "watering_can"
    if "pie nordico" in text or "pienord" in text:
        return "stand"
    if "plato cuadrado" in text or "ptapcuad" in text:
        return "square_saucer"
    if "plato" in text or "ptap" in text:
        return "saucer"
    if "cono" in text or "fibcon" in text:
        return "cone_pot"
    if "andina" in text or "fiband" in text:
        return "decorated_pot"
    if "jardinera" in text or "jare" in text or "jard" in text:
        return "planter"
    if "cuadrada" in text or "owcu" in text or "matri" in text and "red" not in text:
        return "square_pot"
    if "bols" in text or "bm" in text or "owred" in text:
        return "bowl_pot"
    if "pot" in text:
        return "round_pot"
    return "round_pot"


def draw_round_pot(img: Image.Image, color: str, scale: float = 1.0) -> None:
    d = ImageDraw.Draw(img)
    c = rgb(color)
    shadow(img, (330, 850, 870, 1005))
    cx, top_y = 600, int(360 + (1 - scale) * 110)
    top_w, top_h = int(430 * scale), int(120 * scale)
    body_w, body_h = int(340 * scale), int(460 * scale)
    bottom_y = top_y + body_h
    d.ellipse((cx - top_w // 2, top_y - top_h // 2, cx + top_w // 2, top_y + top_h // 2), fill=adjust(c, 40) + (255,), outline=adjust(c, -80) + (255,), width=7)
    d.polygon([(cx - top_w // 2 + 22, top_y), (cx + top_w // 2 - 22, top_y), (cx + body_w // 2, bottom_y), (cx - body_w // 2, bottom_y)], fill=adjust(c, -20) + (255,), outline=adjust(c, -82) + (255,))
    d.ellipse((cx - body_w // 2, bottom_y - top_h // 3, cx + body_w // 2, bottom_y + top_h // 3), fill=adjust(c, -45) + (255,), outline=adjust(c, -90) + (255,), width=4)
    d.arc((cx - top_w // 2 + 44, top_y - top_h // 2 + 18, cx + top_w // 2 - 44, top_y + top_h // 2 - 12), 190, 350, fill=(255, 255, 255, 110), width=10)


def draw_bowl_pot(img: Image.Image, color: str) -> None:
    d = ImageDraw.Draw(img)
    c = rgb(color)
    shadow(img, (315, 820, 885, 980))
    d.ellipse((300, 382, 900, 555), fill=adjust(c, 42) + (255,), outline=adjust(c, -78) + (255,), width=8)
    d.polygon([(330, 460), (870, 460), (750, 850), (450, 850)], fill=adjust(c, -18) + (255,), outline=adjust(c, -82) + (255,))
    d.ellipse((450, 815, 750, 900), fill=adjust(c, -48) + (255,))
    d.arc((370, 405, 830, 520), 190, 350, fill=(255, 255, 255, 105), width=10)


def draw_square_pot(img: Image.Image, color: str) -> None:
    d = ImageDraw.Draw(img)
    c = rgb(color)
    shadow(img, (330, 840, 870, 1000))
    top = [(380, 360), (820, 360), (905, 468), (295, 468)]
    front = [(295, 468), (905, 468), (790, 890), (410, 890)]
    left = [(295, 468), (410, 890), (350, 820), (262, 504)]
    d.polygon(top, fill=adjust(c, 42) + (255,), outline=adjust(c, -76) + (255,))
    d.polygon(front, fill=adjust(c, -18) + (255,), outline=adjust(c, -76) + (255,))
    d.polygon(left, fill=adjust(c, -45) + (255,), outline=adjust(c, -80) + (255,))
    d.line(top + [top[0]], fill=adjust(c, -90) + (255,), width=7)
    d.line([(330, 500), (870, 500)], fill=(255, 255, 255, 56), width=9)


def draw_planter(img: Image.Image, color: str) -> None:
    d = ImageDraw.Draw(img)
    c = rgb(color)
    shadow(img, (250, 840, 950, 1000))
    d.rounded_rectangle((255, 420, 945, 800), radius=28, fill=adjust(c, -18) + (255,), outline=adjust(c, -78) + (255,), width=7)
    d.rounded_rectangle((215, 360, 985, 500), radius=36, fill=adjust(c, 40) + (255,), outline=adjust(c, -78) + (255,), width=7)
    d.line((285, 455, 915, 455), fill=(255, 255, 255, 74), width=10)
    d.rounded_rectangle((310, 765, 890, 855), radius=42, fill=adjust(c, -45) + (255,))


def draw_saucer(img: Image.Image, color: str, square: bool = False) -> None:
    d = ImageDraw.Draw(img)
    c = rgb(color)
    shadow(img, (290, 770, 910, 970), 42)
    if square:
        d.rounded_rectangle((310, 430, 890, 760), radius=36, fill=adjust(c, -18) + (255,), outline=adjust(c, -82) + (255,), width=8)
        d.rounded_rectangle((380, 500, 820, 690), radius=28, fill=adjust(c, 36) + (255,), outline=adjust(c, -62) + (255,), width=5)
    else:
        d.ellipse((250, 410, 950, 760), fill=adjust(c, -20) + (255,), outline=adjust(c, -82) + (255,), width=8)
        d.ellipse((355, 485, 845, 690), fill=adjust(c, 36) + (255,), outline=adjust(c, -62) + (255,), width=5)
        d.arc((335, 490, 865, 700), 190, 350, fill=(255, 255, 255, 100), width=10)


def draw_hook(img: Image.Image, color: str) -> None:
    d = ImageDraw.Draw(img)
    c = rgb(color)
    dark = adjust(c, -70)
    shadow(img, (385, 850, 815, 980))
    for offset in (-22, 22):
        d.line([(600 + offset, 260), (600 + offset, 760)], fill=dark + (255,), width=34)
        d.line([(600 + offset, 260), (600 + offset, 760)], fill=c + (255,), width=22)
    for y in range(320, 735, 58):
        d.arc((510, y - 40, 690, y + 80), 205, 335, fill=adjust(c, 82) + (255,), width=10)
        d.arc((510, y - 40, 690, y + 80), 25, 155, fill=adjust(c, -30) + (255,), width=10)
    d.arc((452, 710, 748, 980), 18, 322, fill=dark + (255,), width=52)
    d.arc((452, 710, 748, 980), 18, 322, fill=c + (255,), width=34)
    d.ellipse((548, 218, 652, 322), outline=dark + (255,), width=30)
    d.ellipse((548, 218, 652, 322), outline=c + (255,), width=18)


def draw_cone_pot(img: Image.Image, color: str) -> None:
    d = ImageDraw.Draw(img)
    c = rgb(color)
    shadow(img, (365, 850, 835, 990))
    d.ellipse((350, 345, 850, 490), fill=adjust(c, 42) + (255,), outline=adjust(c, -80) + (255,), width=8)
    d.polygon([(380, 420), (820, 420), (690, 900), (510, 900)], fill=adjust(c, -18) + (255,), outline=adjust(c, -80) + (255,))
    d.ellipse((510, 865, 690, 930), fill=adjust(c, -45) + (255,))


def draw_decorated_pot(img: Image.Image, color: str) -> None:
    draw_round_pot(img, color, 0.95)
    d = ImageDraw.Draw(img)
    for x, y in [(500, 610), (575, 665), (650, 610), (695, 705), (515, 735)]:
        d.ellipse((x - 18, y - 18, x + 18, y + 18), fill=(238, 218, 160, 220), outline=(83, 71, 50, 120), width=2)
    d.arc((465, 570, 735, 790), 190, 350, fill=(238, 218, 160, 210), width=6)


def draw_watering_can(img: Image.Image, color: str) -> None:
    d = ImageDraw.Draw(img)
    c = rgb(color)
    shadow(img, (320, 820, 880, 990))
    d.rounded_rectangle((390, 480, 760, 820), radius=45, fill=adjust(c, -18) + (255,), outline=adjust(c, -80) + (255,), width=7)
    d.ellipse((460, 395, 700, 510), fill=adjust(c, 38) + (255,), outline=adjust(c, -80) + (255,), width=7)
    d.arc((690, 500, 930, 780), 250, 80, fill=adjust(c, -70) + (255,), width=42)
    d.polygon([(380, 560), (220, 510), (225, 560), (385, 615)], fill=adjust(c, -20) + (255,), outline=adjust(c, -80) + (255,))


def draw_stand(img: Image.Image) -> None:
    d = ImageDraw.Draw(img)
    shadow(img, (305, 880, 895, 1010))
    wood = (154, 105, 63, 255)
    dark = (94, 62, 36, 255)
    for x1, x2 in [(420, 520), (780, 680)]:
        d.line((x1, 460, x2, 930), fill=dark, width=34)
        d.line((x1, 460, x2, 930), fill=wood, width=22)
    d.rounded_rectangle((360, 430, 840, 500), radius=18, fill=wood, outline=dark, width=5)
    d.rounded_rectangle((430, 670, 770, 730), radius=16, fill=wood, outline=dark, width=5)


def render(row: Dict[str, str], out_dir: Path) -> Dict[str, str]:
    name = row["name"]
    sku = row["sku"]
    product_id = int(row["product_id"])
    color, color_label = infer_color(name, sku)
    measure = infer_measure(name, sku)
    family = product_family(name, sku)
    img = canvas()
    if family == "square_pot":
        draw_square_pot(img, color)
    elif family == "planter":
        draw_planter(img, color)
    elif family == "saucer":
        draw_saucer(img, color, False)
    elif family == "square_saucer":
        draw_saucer(img, color, True)
    elif family == "hook":
        draw_hook(img, color)
    elif family == "cone_pot":
        draw_cone_pot(img, color)
    elif family == "decorated_pot":
        draw_decorated_pot(img, color)
    elif family == "bowl_pot":
        draw_bowl_pot(img, color)
    elif family == "watering_can":
        draw_watering_can(img, color)
    elif family == "stand":
        draw_stand(img)
    else:
        scale = 0.72 if re.search(r"\b(?:6|8|10|12|14)\s*cm\b", normalize(name)) else 0.92
        draw_round_pot(img, color, scale)

    filename = f"{product_id}-{slugify(sku or name)}.webp"
    img.convert("RGB").save(out_dir / filename, "WEBP", quality=92)
    return {
        "product_id": product_id,
        "file": filename,
        "alt": f"{display(name)} - imagen representativa de maceta o accesorio en Vivero Los Cocos",
        "family": family,
        "color": color_label,
        "measure": measure,
    }


def load_rows(path: Path) -> List[Dict[str, str]]:
    rows = []
    for line in path.read_text(encoding="utf-8").splitlines():
        parts = line.split("\t")
        if len(parts) < 5:
            continue
        rows.append({
            "product_id": parts[0],
            "name": parts[1],
            "sku": parts[2],
            "image_id": parts[3],
            "image_url": parts[4],
        })
    return rows


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Generate MACETAS product image renders.")
    parser.add_argument("--input", default=str(DEFAULT_INPUT))
    parser.add_argument("--output-dir", default=str(DEFAULT_OUTPUT))
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    input_path = Path(args.input)
    out_dir = Path(args.output_dir)
    out_dir.mkdir(parents=True, exist_ok=True)
    rows = load_rows(input_path)
    manifest = [render(row, out_dir) for row in rows]
    manifest_path = out_dir / "macetas-generated-manifest.json"
    manifest_path.write_text(json.dumps(manifest, ensure_ascii=False, indent=2), encoding="utf-8")
    family_counts: Dict[str, int] = {}
    for row in manifest:
        family_counts[row["family"]] = family_counts.get(row["family"], 0) + 1
    print(json.dumps({
        "output_dir": str(out_dir),
        "manifest": str(manifest_path),
        "count": len(manifest),
        "family_counts": family_counts,
    }, ensure_ascii=False, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
