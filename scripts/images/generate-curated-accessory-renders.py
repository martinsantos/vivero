#!/usr/bin/env python3
"""Generate curated interim product renders for products with wrong images.

These are clean ecommerce renders used only where the current image is clearly
wrong and no local supplier photo exists. They are keyed by SKU/product id so
the replacement batch is explicit and reversible.
"""

from __future__ import annotations

import json
from pathlib import Path
from typing import Dict, Tuple

from PIL import Image, ImageDraw, ImageFilter, ImageFont


ROOT = Path(__file__).resolve().parents[2]
OUT_DIR = ROOT / "assets" / "product-images" / "curated"
MANIFEST = OUT_DIR / "curated-accessory-manifest.json"


def font(size: int, bold: bool = False) -> ImageFont.FreeTypeFont:
    candidates = [
        "/System/Library/Fonts/Supplemental/Arial Bold.ttf" if bold else "/System/Library/Fonts/Supplemental/Arial.ttf",
        "/System/Library/Fonts/Supplemental/Helvetica.ttc",
    ]
    for path in candidates:
        try:
            return ImageFont.truetype(path, size=size)
        except Exception:
            continue
    return ImageFont.load_default()


def base_canvas() -> Image.Image:
    img = Image.new("RGB", (1200, 1200), "#f6f7f3")
    draw = ImageDraw.Draw(img)
    draw.rectangle((72, 72, 1128, 1128), outline="#dfe6dc", width=2)
    return img


def add_label(img: Image.Image, title: str, subtitle: str) -> None:
    draw = ImageDraw.Draw(img)
    draw.text((96, 88), title, fill="#17231d", font=font(38, True))
    draw.text((96, 138), subtitle, fill="#657268", font=font(25))


def shadow_layer(box: Tuple[int, int, int, int], radius: int = 44) -> Image.Image:
    layer = Image.new("RGBA", (1200, 1200), (0, 0, 0, 0))
    d = ImageDraw.Draw(layer)
    d.ellipse(box, fill=(0, 0, 0, 42))
    return layer.filter(ImageFilter.GaussianBlur(radius))


def draw_round_pot(path: Path, color: str, title: str, subtitle: str, scale: float = 1.0) -> None:
    img = base_canvas().convert("RGBA")
    img.alpha_composite(shadow_layer((330, 865, 870, 1000), 34))
    d = ImageDraw.Draw(img)
    cx = 600
    top_w = int(430 * scale)
    top_h = int(128 * scale)
    body_w = int(350 * scale)
    body_h = int(455 * scale)
    top_y = int(380 + (1 - scale) * 120)
    bottom_y = top_y + body_h
    rim = tuple(int(color.lstrip("#")[i:i + 2], 16) for i in (0, 2, 4))
    body = tuple(max(0, c - 28) for c in rim)
    hi = tuple(min(255, c + 38) for c in rim)
    d.ellipse((cx - top_w // 2, top_y - top_h // 2, cx + top_w // 2, top_y + top_h // 2), fill=hi + (255,), outline=(36, 50, 42, 80), width=4)
    d.polygon([
        (cx - top_w // 2 + 24, top_y),
        (cx + top_w // 2 - 24, top_y),
        (cx + body_w // 2, bottom_y),
        (cx - body_w // 2, bottom_y),
    ], fill=body + (255,))
    d.ellipse((cx - body_w // 2, bottom_y - top_h // 3, cx + body_w // 2, bottom_y + top_h // 3), fill=tuple(max(0, c - 46) for c in rim) + (255,))
    d.ellipse((cx - top_w // 2, top_y - top_h // 2, cx + top_w // 2, top_y + top_h // 2), outline=tuple(max(0, c - 58) for c in rim) + (255,), width=7)
    d.arc((cx - top_w // 2 + 45, top_y - top_h // 2 + 20, cx + top_w // 2 - 45, top_y + top_h // 2 - 10), 190, 350, fill=(255, 255, 255, 95), width=9)
    add_label(img, title, subtitle)
    img.convert("RGB").save(path, "WEBP", quality=92)


def draw_square_pot(path: Path, color: str, title: str, subtitle: str) -> None:
    img = base_canvas().convert("RGBA")
    img.alpha_composite(shadow_layer((342, 842, 858, 1010), 36))
    d = ImageDraw.Draw(img)
    rim = tuple(int(color.lstrip("#")[i:i + 2], 16) for i in (0, 2, 4))
    body = tuple(max(0, c - 30) for c in rim)
    dark = tuple(max(0, c - 70) for c in rim)
    points_top = [(390, 380), (810, 380), (895, 474), (305, 474)]
    points_front = [(305, 474), (895, 474), (790, 875), (410, 875)]
    points_left = [(305, 474), (410, 875), (360, 810), (270, 505)]
    d.polygon(points_top, fill=tuple(min(255, c + 38) for c in rim) + (255,), outline=dark + (255,))
    d.polygon(points_front, fill=body + (255,), outline=dark + (255,))
    d.polygon(points_left, fill=tuple(max(0, c - 48) for c in rim) + (255,), outline=dark + (255,))
    d.line(points_top + [points_top[0]], fill=dark + (255,), width=7)
    d.line([(330, 500), (870, 500)], fill=(255, 255, 255, 50), width=9)
    add_label(img, title, subtitle)
    img.convert("RGB").save(path, "WEBP", quality=92)


def draw_hook(path: Path) -> None:
    img = base_canvas().convert("RGBA")
    img.alpha_composite(shadow_layer((385, 850, 815, 980), 34))
    d = ImageDraw.Draw(img)
    green = (102, 181, 116, 255)
    dark = (42, 112, 62, 255)
    # Braided hanging hook: two mirrored cords plus product hook.
    for offset in (-20, 20):
        d.line([(600 + offset, 280), (600 + offset, 760)], fill=dark, width=34)
        d.line([(600 + offset, 280), (600 + offset, 760)], fill=green, width=22)
    for y in range(320, 735, 58):
        d.arc((510, y - 40, 690, y + 80), 205, 335, fill=(220, 255, 221, 255), width=10)
        d.arc((510, y - 40, 690, y + 80), 25, 155, fill=(44, 135, 66, 255), width=10)
    d.arc((452, 710, 748, 980), 18, 322, fill=dark, width=52)
    d.arc((452, 710, 748, 980), 18, 322, fill=green, width=34)
    d.ellipse((548, 218, 652, 322), outline=dark, width=30)
    d.ellipse((548, 218, 652, 322), outline=green, width=18)
    add_label(img, "Gancho trenzado chico", "Verde claro · Ta Plastic")
    img.convert("RGB").save(path, "WEBP", quality=92)


def draw_shrub(path: Path, title: str, subtitle: str, foliage: str, accent: str) -> None:
    img = base_canvas().convert("RGBA")
    img.alpha_composite(shadow_layer((360, 865, 840, 1000), 34))
    d = ImageDraw.Draw(img)
    pot = (92, 70, 52, 255)
    d.ellipse((445, 770, 755, 855), fill=(130, 96, 66, 255), outline=(54, 42, 34, 255), width=5)
    d.polygon([(470, 810), (730, 810), (680, 1015), (520, 1015)], fill=pot, outline=(54, 42, 34, 255))
    d.ellipse((520, 970, 680, 1030), fill=(58, 43, 34, 255))
    trunk = tuple(int(accent.lstrip("#")[i:i + 2], 16) for i in (0, 2, 4)) + (255,)
    leaf = tuple(int(foliage.lstrip("#")[i:i + 2], 16) for i in (0, 2, 4)) + (245,)
    light = tuple(min(255, int(foliage.lstrip("#")[i:i + 2], 16) + 44) for i in (0, 2, 4)) + (235,)
    for x2, y2, width in [(545, 675, 12), (490, 610, 9), (625, 595, 10), (700, 675, 8), (590, 520, 8)]:
        d.line([(600, 800), (x2, y2)], fill=trunk, width=width)
    leaves = [
        (420, 540, 610, 700), (520, 450, 720, 640), (610, 540, 805, 710),
        (470, 610, 665, 780), (575, 620, 780, 800), (520, 360, 665, 500),
        (375, 645, 535, 790), (700, 640, 850, 785),
    ]
    for i, box in enumerate(leaves):
        d.ellipse(box, fill=leaf if i % 2 else light, outline=(39, 90, 53, 120), width=3)
    for box in [(475, 500, 535, 555), (655, 485, 718, 545), (575, 390, 635, 445), (735, 690, 792, 742)]:
        d.ellipse(box, fill=(236, 243, 224, 210))
    add_label(img, title, subtitle)
    img.convert("RGB").save(path, "WEBP", quality=92)


def draw_columnar_shrub(path: Path, title: str, subtitle: str) -> None:
    img = base_canvas().convert("RGBA")
    img.alpha_composite(shadow_layer((360, 865, 840, 1000), 34))
    d = ImageDraw.Draw(img)
    d.ellipse((445, 775, 755, 855), fill=(128, 93, 62, 255), outline=(54, 42, 34, 255), width=5)
    d.polygon([(475, 812), (725, 812), (675, 1010), (525, 1010)], fill=(80, 58, 43, 255), outline=(54, 42, 34, 255))
    d.ellipse((525, 965, 675, 1022), fill=(50, 38, 31, 255))
    trunk = (84, 59, 42, 255)
    for x in (545, 590, 635):
        d.line([(600, 805), (x, 440)], fill=trunk, width=10)
    for x, y, rx, ry, color in [
        (540, 650, 95, 140, "#4e8b5d"),
        (650, 620, 96, 150, "#6aa56b"),
        (585, 515, 82, 128, "#3f7a50"),
        (610, 405, 66, 96, "#79ad72"),
        (490, 545, 70, 105, "#629b62"),
        (705, 520, 62, 96, "#4f8959"),
    ]:
        rgb = tuple(int(color.lstrip("#")[i:i + 2], 16) for i in (0, 2, 4))
        d.ellipse((x - rx, y - ry, x + rx, y + ry), fill=rgb + (242,), outline=(35, 83, 49, 140), width=3)
    for x, y in [(515, 500), (610, 370), (690, 485), (585, 600), (655, 640), (465, 620)]:
        d.ellipse((x - 16, y - 16, x + 16, y + 16), fill=(230, 226, 244, 230), outline=(128, 100, 168, 190), width=2)
    add_label(img, title, subtitle)
    img.convert("RGB").save(path, "WEBP", quality=92)


def draw_dsist_60(path: Path) -> None:
    source = ROOT / "Glacoxan" / "Glacoxan D-sist.png"
    img = Image.open(source).convert("RGBA")
    edit = ImageDraw.Draw(img)
    width, height = img.size
    # Source image shows the 30 cc variant in the bottom-right face. Cover that
    # marking on the package itself before placing it in the catalog canvas.
    edit.rounded_rectangle(
        (int(width * 0.70), int(height * 0.78), int(width * 0.97), int(height * 0.96)),
        radius=12,
        fill=(19, 22, 19, 255),
    )
    edit.text((int(width * 0.725), int(height * 0.79)), "60", fill=(174, 213, 121, 255), font=font(58, True))
    edit.text((int(width * 0.725), int(height * 0.895)), "Cont. Neto", fill=(224, 224, 208, 255), font=font(13, True))
    edit.text((int(width * 0.912), int(height * 0.905)), "cm3", fill=(224, 224, 208, 255), font=font(12, True))
    canvas = Image.new("RGBA", (1200, 1200), "#f6f7f3")
    img.thumbnail((900, 900))
    canvas.alpha_composite(img, ((1200 - img.width) // 2, 135))
    add_label(canvas, "GlacoXAN D-SIST", "60 cc · insecticida acaricida")
    canvas.convert("RGB").save(path, "WEBP", quality=92)


def main() -> int:
    OUT_DIR.mkdir(parents=True, exist_ok=True)
    specs: Dict[str, Dict[str, str]] = {
        "MTAPCAC10VC": {
            "file": "MTAPCAC10VC-maceta-rocio-10-verde-claro.webp",
            "title": "Maceta Rocio 10 cm",
            "subtitle": "Verde claro · plastica",
            "product_id": "22",
            "alt": "Maceta Plastica Rocio 10 cm color Verde Claro en Vivero Los Cocos",
        },
        "MTAPR30NE": {
            "file": "MTAPR30NE-maceta-rocio-30-negro.webp",
            "title": "Maceta Rocio 30 cm",
            "subtitle": "Negro · plastica",
            "product_id": "89",
            "alt": "Maceta Plastica Rocio 30 cm color Negro en Vivero Los Cocos",
        },
        "GTAPVC": {
            "file": "GTAPVC-gancho-trenzado-verde-claro.webp",
            "title": "Gancho trenzado chico",
            "subtitle": "Verde claro · Ta Plastic",
            "product_id": "303",
            "alt": "Gancho trenzado chico verde claro Ta Plastic en Vivero Los Cocos",
        },
        "MMATOWCU15MC": {
            "file": "MMATOWCU15MC-matri-owen-cuadrada-marron-claro.webp",
            "title": "Matri Owen cuadrada",
            "subtitle": "15x15 cm · marron claro",
            "product_id": "185",
            "alt": "Matri Owen cuadrada 15x15 cm marron claro en Vivero Los Cocos",
        },
        "MMATOWCU15MO": {
            "file": "MMATOWCU15MO-matri-owen-cuadrada-marron-oscuro.webp",
            "title": "Matri Owen cuadrada",
            "subtitle": "15x15 cm · marron oscuro",
            "product_id": "189",
            "alt": "Matri Owen cuadrada 15x15 cm marron oscuro en Vivero Los Cocos",
        },
        "FORAU10L": {
            "file": "FORAU10L-arbusto-vivero-10-litros.webp",
            "title": "Arbusto Forau",
            "subtitle": "10 litros · vivero",
            "product_id": "511",
            "alt": "Arbusto Forau 10 Litros en Vivero Los Cocos",
        },
        "FORATRONA10L": {
            "file": "FORATRONA10L-arbusto-vivero-10-litros.webp",
            "title": "Arbusto Foratrona",
            "subtitle": "10 litros · vivero",
            "product_id": "516",
            "alt": "Arbusto Foratrona 10 Litros en Vivero Los Cocos",
        },
        "GLACOXAN-DSIST-60": {
            "file": "GLACOXAN-DSIST-60cc.webp",
            "title": "GlacoXAN D-SIST",
            "subtitle": "60 cc · insecticida acaricida",
            "product_id": "61112",
            "alt": "GlacoXAN D-SIST 60 cc insecticida acaricida en Vivero Los Cocos",
        },
    }
    draw_round_pot(OUT_DIR / specs["MTAPCAC10VC"]["file"], "#9fd98f", specs["MTAPCAC10VC"]["title"], specs["MTAPCAC10VC"]["subtitle"], 0.68)
    draw_round_pot(OUT_DIR / specs["MTAPR30NE"]["file"], "#1d211f", specs["MTAPR30NE"]["title"], specs["MTAPR30NE"]["subtitle"], 1.0)
    draw_hook(OUT_DIR / specs["GTAPVC"]["file"])
    draw_square_pot(OUT_DIR / specs["MMATOWCU15MC"]["file"], "#9a7656", specs["MMATOWCU15MC"]["title"], specs["MMATOWCU15MC"]["subtitle"])
    draw_square_pot(OUT_DIR / specs["MMATOWCU15MO"]["file"], "#3b2b22", specs["MMATOWCU15MO"]["title"], specs["MMATOWCU15MO"]["subtitle"])
    draw_shrub(OUT_DIR / specs["FORAU10L"]["file"], specs["FORAU10L"]["title"], specs["FORAU10L"]["subtitle"], "#6f9b48", "#6a4a32")
    draw_columnar_shrub(OUT_DIR / specs["FORATRONA10L"]["file"], specs["FORATRONA10L"]["title"], specs["FORATRONA10L"]["subtitle"])
    draw_dsist_60(OUT_DIR / specs["GLACOXAN-DSIST-60"]["file"])
    manifest = []
    for sku, spec in specs.items():
        manifest.append({
            "sku": sku,
            "product_id": int(spec["product_id"]),
            "file": spec["file"],
            "alt": spec["alt"],
        })
    MANIFEST.write_text(json.dumps(manifest, ensure_ascii=False, indent=2), encoding="utf-8")
    print(MANIFEST)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
