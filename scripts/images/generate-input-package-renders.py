#!/usr/bin/env python3
"""Generate owned, neutral package renders for substrate/input products.

These are not official brand photos. They are clean catalog placeholders for
items that are currently miscategorized under PLANTAS but are actually inputs
such as soil, compost, chips, perlite, or fertilizers.
"""

from __future__ import annotations

import argparse
import json
import re
import unicodedata
from pathlib import Path

from PIL import Image, ImageDraw, ImageFilter, ImageFont


ROOT = Path(__file__).resolve().parents[2]
DEFAULT_INPUT = Path("/private/tmp/plant-input-products.tsv")
DEFAULT_OUTPUT = ROOT / "assets" / "product-images" / "plant-input-renders"


PALETTES = {
    "turba": ("#6f4b36", "#d8b98e", "TURBA"),
    "guano": ("#5e4a33", "#d9c39b", "GUANO"),
    "sustrato": ("#315c45", "#cad9b8", "SUSTRATO"),
    "humus": ("#46392b", "#c8bda4", "HUMUS"),
    "chip": ("#8a5a32", "#e3c18c", "CHIP"),
    "perlita": ("#d8d7cf", "#7b8882", "PERLITA"),
    "hierro": ("#913d2c", "#e0b09e", "HIERRO"),
    "fertilizante": ("#315f7d", "#c7d8df", "FERTILIZANTE"),
    "tierra": ("#644330", "#ddc3a0", "TIERRA"),
}


def normalize(value: str) -> str:
    value = unicodedata.normalize("NFKD", str(value or ""))
    value = "".join(ch for ch in value if not unicodedata.combining(ch))
    return re.sub(r"\s+", " ", value).strip().lower()


def slugify(value: str) -> str:
    return re.sub(r"[^a-z0-9]+", "-", normalize(value)).strip("-")[:90] or "insumo"


def infer_kind(name: str, sku: str) -> tuple[str, str, str]:
    text = normalize(f"{name} {sku}")
    if "turba" in text:
        return PALETTES["turba"]
    if "guano" in text:
        return PALETTES["guano"]
    if "sust" in text or "sustrato" in text:
        return PALETTES["sustrato"]
    if "hum" in text or "humus" in text:
        return PALETTES["humus"]
    if "chip" in text:
        return PALETTES["chip"]
    if "perlita" in text:
        return PALETTES["perlita"]
    if "hierro" in text:
        return PALETTES["hierro"]
    if "nfoska" in text or "triple" in text:
        return PALETTES["fertilizante"]
    return PALETTES["tierra"]


def infer_measure(name: str, sku: str) -> str:
    text = normalize(f"{name} {sku}")
    match = re.search(r"(\d{1,3})\s*litros?", text)
    if match:
        return f"{match.group(1)} L"
    match = re.search(r"(\d{1,4})\s*(?:cm|gr|g|kg)?", text)
    if match:
        return match.group(1)
    return "vivero"


def font(size: int, bold: bool = False) -> ImageFont.FreeTypeFont:
    candidates = [
        "/System/Library/Fonts/Supplemental/Arial Bold.ttf" if bold else "/System/Library/Fonts/Supplemental/Arial.ttf",
        "/System/Library/Fonts/Supplemental/Helvetica Bold.ttf" if bold else "/System/Library/Fonts/Supplemental/Helvetica.ttf",
        "/Library/Fonts/Arial Bold.ttf" if bold else "/Library/Fonts/Arial.ttf",
    ]
    for candidate in candidates:
        if candidate and Path(candidate).exists():
            return ImageFont.truetype(candidate, size)
    return ImageFont.load_default()


def centered(draw: ImageDraw.ImageDraw, box: tuple[int, int, int, int], text: str, fill: str, size: int, bold: bool = False) -> None:
    fnt = font(size, bold)
    bbox = draw.multiline_textbbox((0, 0), text, font=fnt, spacing=8, align="center")
    x = box[0] + (box[2] - box[0] - (bbox[2] - bbox[0])) / 2
    y = box[1] + (box[3] - box[1] - (bbox[3] - bbox[1])) / 2
    draw.multiline_text((x, y), text, fill=fill, font=fnt, spacing=8, align="center")


def render_product(product: dict, output: Path) -> dict:
    primary, accent, label = infer_kind(product["name"], product["sku"])
    measure = infer_measure(product["name"], product["sku"])

    image = Image.new("RGB", (1200, 1200), "#f3f0e8")
    shadow = Image.new("RGBA", (1200, 1200), (0, 0, 0, 0))
    sdraw = ImageDraw.Draw(shadow)
    sdraw.ellipse((270, 900, 940, 1035), fill=(34, 30, 24, 75))
    shadow = shadow.filter(ImageFilter.GaussianBlur(34))
    image = Image.alpha_composite(image.convert("RGBA"), shadow)

    layer = Image.new("RGBA", (1200, 1200), (0, 0, 0, 0))
    draw = ImageDraw.Draw(layer)
    bag = [(380, 245), (820, 245), (880, 900), (325, 900)]
    side = [(820, 245), (910, 300), (930, 850), (880, 900)]
    top = [(380, 245), (820, 245), (910, 300), (455, 315)]
    draw.polygon(side, fill="#4b392c")
    draw.polygon(bag, fill=primary)
    draw.polygon(top, fill=accent)
    draw.line([(382, 248), (325, 900), (880, 900), (820, 245), (382, 248)], fill=(255, 255, 255, 46), width=4)
    draw.rounded_rectangle((405, 390, 795, 720), radius=18, fill="#f4efe5")
    draw.rectangle((405, 390, 795, 465), fill=accent)
    centered(draw, (405, 390, 795, 465), "VIVERO LOS COCOS", "#24372e", 24, True)
    centered(draw, (435, 485, 765, 620), label, "#24372e", 54, True)
    centered(draw, (455, 635, 745, 705), measure, primary, 38, True)
    draw.rounded_rectangle((470, 770, 735, 825), radius=27, fill="#24372e")
    centered(draw, (470, 770, 735, 825), "INSUMO PARA CULTIVO", "#f8f6ee", 20, True)

    for x, y, r, c in [(480, 315, 18, "#d8e1c2"), (530, 335, 12, "#b6c58d"), (735, 325, 16, "#e8d1a0")]:
        draw.ellipse((x - r, y - r, x + r, y + r), fill=c)

    image = Image.alpha_composite(image, layer)
    image.convert("RGB").save(output, "WEBP", quality=90, method=6)

    return {
        "product_id": product["product_id"],
        "file": output.name,
        "alt": f"{product['name']} - imagen referencial de insumo para cultivo en Vivero Los Cocos",
        "source_type": "owned_neutral_package_render",
        "kind": label.lower(),
    }


def read_products(path: Path) -> list[dict]:
    rows = []
    with path.open("r", encoding="utf-8") as fh:
        for raw in fh:
            product_id, name, sku = raw.rstrip("\n").split("\t")[:3]
            rows.append({"product_id": int(product_id), "name": name, "sku": sku})
    return rows


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--input", type=Path, default=DEFAULT_INPUT)
    parser.add_argument("--output-dir", type=Path, default=DEFAULT_OUTPUT)
    args = parser.parse_args()
    args.output_dir.mkdir(parents=True, exist_ok=True)

    manifest = []
    for product in read_products(args.input):
        filename = f"{product['product_id']}-{slugify(product['sku'] or product['name'])}.webp"
        manifest.append(render_product(product, args.output_dir / filename))

    manifest_path = args.output_dir / "plant-input-renders-manifest.json"
    manifest_path.write_text(json.dumps(manifest, ensure_ascii=False, indent=2), encoding="utf-8")
    print(json.dumps({"count": len(manifest), "manifest": str(manifest_path), "output_dir": str(args.output_dir)}, indent=2))


if __name__ == "__main__":
    main()
