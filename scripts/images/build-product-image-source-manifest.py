#!/usr/bin/env python3
"""Build an auditable image-source plan for the whole WooCommerce catalog.

The manifest is intentionally conservative. It only marks images as directly
applicable when they come from local, product-specific provider assets already
present in the repository. Everything else becomes a tracked work item with a
recommended next source instead of being applied blindly.
"""

from __future__ import annotations

import argparse
import csv
import json
import re
import shutil
import unicodedata
from pathlib import Path
from typing import Dict, Iterable, List, Tuple


ROOT = Path(__file__).resolve().parents[2]
DEFAULT_INPUT = Path("/private/tmp/vivero-products-all.tsv")
DEFAULT_OUTPUT_DIR = ROOT / "assets" / "product-images" / "source-manifest"
DEFAULT_PROVIDER_DIR = ROOT / "assets" / "product-images" / "provider-official"
GLACOXAN_DIR = ROOT / "Glacoxan"


LOCAL_PROVIDER_IMAGE_MAP: Dict[str, str] = {
    "fertifox floracion": "Fertifox FLORACION.png",
    "fertifox floración": "Fertifox FLORACION.png",
    "fertifox potenciado": "Fertifox HORMONA.png",
    "fertifox hormona": "Fertifox HORMONA.png",
    "fertifox follaje": "Fertifox FOLLAJE.png",
    "fertifox lustre": "Fertifox Lustre vegetal.png",
    "fungoxan": "Fungoxan.png",
    "glacoxan total": "Glacoxan TOTAL.png",
    "glacoxan mcpa": "Glacoxan MCPA.png",
    "glacoxan e": "Glacoxan E.png",
    "glacoxan h": "Glacoxan H.png",
    "glacoxan ciper": "Glacoxan Ciper.png",
    "glacoxan d-sist": "Glacoxan D-sist.png",
    "glacoxan dsist": "Glacoxan D-sist.png",
    "glacoxan avam": "Glacoxan AVAM.png",
    "glacoxan imida": "Glacoxan IMIDA.png",
    "glacoxan oil": "Glacoxan OIL.png",
    "glacoxan p pellet": "Glacoxan P Pellet.png",
    "glacoxan p (pellet)": "Glacoxan P Pellet.png",
    "glacoxan p polvo": "Glacoxan P Polvo seco.png",
    "glacoxan p (polvo)": "Glacoxan P Polvo seco.png",
}

LOCAL_PROVIDER_SKU_MAP: Dict[str, str] = {
    "GLACOH250GR": "Glacoxan H.png",
    "GLACOE60CC": "Glacoxan E.png",
    "GLACOD30CC": "Glacoxan D-sist.png",
    "GLACOHSEL100CC": "Glacoxan MCPA.png",
    "GLACOHTOT100CC": "Glacoxan TOTAL.png",
    "GLACOIM30CC": "Glacoxan IMIDA.png",
    "GLACOAVAM30CC": "Glacoxan AVAM.png",
    "GLACOCIP30CC": "Glacoxan Ciper.png",
    "GLACOFUNG30CC": "Fungoxan.png",
    "GLACOPPELL200GR": "Glacoxan P Pellet.png",
    "GLACOPCEB200GR": "Glacoxan P Polvo seco.png",
    "FERTIFLOR200": "Fertifox FLORACION.png",
    "FERTIFOLL200": "Fertifox FOLLAJE.png",
    "FERTIPOT200": "Fertifox HORMONA.png",
    "FERTULVEG": "Fertifox Lustre vegetal.png",
    "FERTIHORM": "Fertifox HORMONA.png",
}

OFFICIAL_SOURCE_URLS = {
    "Glacoxan": "https://www.glacoxan.com/",
    "Fertifox": "https://www.glacoxan.com/",
    "FungoXAN": "https://www.glacoxan.com/producto/jardin-fungoxan/",
    "Terrafertil": "https://terrafertil.com/productos_profesionales.html",
    "TA Plastic": "https://www.blonia.com.ar/productos/taplastic-rocio/",
    "Plantas Faitful": "https://www.plantasfaitful.com.ar/",
    "Unsplash": "https://unsplash.com/license",
}


def normalize(value: str) -> str:
    value = str(value or "")
    value = value.replace("&#215;", "x")
    value = unicodedata.normalize("NFKD", value)
    value = "".join(ch for ch in value if not unicodedata.combining(ch))
    return re.sub(r"\s+", " ", value).strip().lower()


def slugify(value: str) -> str:
    value = normalize(value)
    return re.sub(r"[^a-z0-9]+", "-", value).strip("-")[:90] or "producto"


def read_products(path: Path) -> List[dict]:
    rows: List[dict] = []
    with path.open("r", encoding="utf-8") as fh:
        for raw in fh:
            parts = raw.rstrip("\n").split("\t")
            if len(parts) < 6:
                continue
            rows.append(
                {
                    "product_id": int(parts[0]),
                    "name": parts[1],
                    "sku": parts[2],
                    "categories": [c for c in parts[3].split(",") if c],
                    "image_id": parts[4],
                    "image_url": parts[5],
                }
            )
    return rows


def category_key(product: dict) -> str:
    categories = {normalize(c) for c in product["categories"]}
    if "macetas" in categories:
        return "macetas"
    if "plantas" in categories:
        return "plantas"
    if categories & {"insecticidas", "fertilizantes", "herbicidas", "funguicidas", "molusquicidas"}:
        return "quimicos"
    return "otros"


def provider_for(product: dict) -> str:
    text = normalize(f"{product['name']} {product['sku']}")
    sku = product["sku"].upper()
    if sku in LOCAL_PROVIDER_SKU_MAP:
        filename = LOCAL_PROVIDER_SKU_MAP[sku]
        if filename.startswith("Fertifox"):
            return "Fertifox"
        if filename == "Fungoxan.png":
            return "FungoXAN"
        return "Glacoxan"
    if "glacoxan" in text or "glaco" in text:
        return "Glacoxan"
    if "fertifox" in text:
        return "Fertifox"
    if "fungoxan" in text:
        return "FungoXAN"
    if product["sku"].upper().startswith(("MTAP", "MMAT", "PTAP", "GTAP", "FIB", "JARD", "POT")):
        return "TA Plastic / proveedor de macetas"
    if category_key(product) == "plantas":
        return "Vivero/proveedor de plantas"
    return "No clasificado"


def find_local_provider_asset(product: dict) -> Tuple[Path | None, str | None]:
    sku = product["sku"].upper()
    if sku in LOCAL_PROVIDER_SKU_MAP:
        source = GLACOXAN_DIR / LOCAL_PROVIDER_SKU_MAP[sku]
        if source.exists():
            return source, sku

    text = normalize(product["name"])
    for key, filename in LOCAL_PROVIDER_IMAGE_MAP.items():
        if normalize(key) in text:
            source = GLACOXAN_DIR / filename
            if source.exists():
                return source, key
    return None, None


def decision_for(product: dict) -> dict:
    cat = category_key(product)
    provider = provider_for(product)
    source, matched_key = find_local_provider_asset(product)

    if source:
        return {
            "source_type": "local_provider_asset",
            "provider": provider,
            "source_url": OFFICIAL_SOURCE_URLS.get(provider, OFFICIAL_SOURCE_URLS["Glacoxan"]),
            "source_file": str(source.relative_to(ROOT)),
            "license_status": "provider/product asset already present in project; verify commercial permission before broad reuse",
            "confidence_score": 0.92,
            "review_status": "ready_for_visual_review",
            "recommended_action": "normalize_and_apply",
            "matched_key": matched_key,
        }

    if cat == "macetas":
        return {
            "source_type": "premium_render_required",
            "provider": provider,
            "source_url": OFFICIAL_SOURCE_URLS["TA Plastic"],
            "source_file": "",
            "license_status": "reference-only unless provider grants usage; generated render is owned asset",
            "confidence_score": 0.74,
            "review_status": "needs_render_review",
            "recommended_action": "generate_three_js_render_from_family_color_size",
            "matched_key": "",
        }

    if cat == "plantas":
        return {
            "source_type": "species_or_own_photo_required",
            "provider": provider,
            "source_url": OFFICIAL_SOURCE_URLS["Plantas Faitful"],
            "source_file": "",
            "license_status": "provider/competitor images are reference-only unless permission is granted",
            "confidence_score": 0.58,
            "review_status": "needs_species_mapping",
            "recommended_action": "map_common_and_scientific_species_then_source_free_or_own_photo",
            "matched_key": "",
        }

    return {
        "source_type": "manual_source_required",
        "provider": provider,
        "source_url": "",
        "source_file": "",
        "license_status": "unknown",
        "confidence_score": 0.2,
        "review_status": "needs_research",
        "recommended_action": "manual_provider_research",
        "matched_key": "",
    }


def copy_provider_images(rows: Iterable[dict], provider_dir: Path) -> List[dict]:
    provider_dir.mkdir(parents=True, exist_ok=True)
    apply_rows: List[dict] = []
    for row in rows:
        if row["source_type"] != "local_provider_asset":
            continue
        source = ROOT / row["source_file"]
        suffix = source.suffix.lower()
        filename = f"{row['product_id']}-{slugify(row['sku'] or row['name'])}{suffix}"
        target = provider_dir / filename
        shutil.copy2(source, target)
        apply_rows.append(
            {
                "product_id": row["product_id"],
                "file": filename,
                "alt": f"{row['name']} - imagen oficial de producto en Vivero Los Cocos",
                "source_type": row["source_type"],
                "source_url": row["source_url"],
            }
        )
    return apply_rows


def write_csv(path: Path, rows: List[dict]) -> None:
    fields = [
        "product_id",
        "name",
        "sku",
        "categories",
        "provider",
        "source_type",
        "confidence_score",
        "review_status",
        "recommended_action",
        "source_url",
        "source_file",
        "image_url",
    ]
    with path.open("w", encoding="utf-8", newline="") as fh:
        writer = csv.DictWriter(fh, fieldnames=fields)
        writer.writeheader()
        for row in rows:
            writer.writerow({field: row.get(field, "") for field in fields})


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--input", type=Path, default=DEFAULT_INPUT)
    parser.add_argument("--output-dir", type=Path, default=DEFAULT_OUTPUT_DIR)
    parser.add_argument("--provider-dir", type=Path, default=DEFAULT_PROVIDER_DIR)
    args = parser.parse_args()

    args.output_dir.mkdir(parents=True, exist_ok=True)
    products = read_products(args.input)

    rows: List[dict] = []
    for product in products:
        decision = decision_for(product)
        rows.append(
            {
                **product,
                **decision,
                "categories": ",".join(product["categories"]),
            }
        )

    apply_rows = copy_provider_images(rows, args.provider_dir)

    manifest_path = args.output_dir / "catalog-image-source-manifest.json"
    summary_path = args.output_dir / "catalog-image-source-summary.csv"
    apply_manifest_path = args.provider_dir / "provider-official-apply-manifest.json"

    manifest_path.write_text(json.dumps(rows, ensure_ascii=False, indent=2), encoding="utf-8")
    write_csv(summary_path, rows)
    apply_manifest_path.write_text(json.dumps(apply_rows, ensure_ascii=False, indent=2), encoding="utf-8")

    counts: Dict[str, int] = {}
    for row in rows:
        counts[row["source_type"]] = counts.get(row["source_type"], 0) + 1

    print(
        json.dumps(
            {
                "products": len(rows),
                "source_type_counts": counts,
                "directly_applicable_provider_assets": len(apply_rows),
                "manifest": str(manifest_path),
                "summary": str(summary_path),
                "apply_manifest": str(apply_manifest_path),
                "provider_dir": str(args.provider_dir),
            },
            ensure_ascii=False,
            indent=2,
        )
    )


if __name__ == "__main__":
    main()
