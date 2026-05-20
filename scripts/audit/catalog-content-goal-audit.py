#!/usr/bin/env python3
"""Read-only product content audit for Ficha de Producto 360.

The audit flags generic WooCommerce product copy, weak taxonomy, missing
attributes, and unsafe wording. It writes JSON, CSV, Markdown and HTML reports.
"""

from __future__ import annotations

import argparse
import csv
import html
import json
import os
import re
import sys
from collections import Counter
from dataclasses import asdict, dataclass
from datetime import datetime, timezone
from pathlib import Path
from typing import Any, Dict, Iterable, List, Optional, Tuple

import requests

try:
    from dotenv import load_dotenv
except Exception:  # pragma: no cover
    load_dotenv = None


ROOT = Path(__file__).resolve().parents[2]
DEFAULT_JSON = ROOT / "logs" / "catalog-content-goal-audit.json"
DEFAULT_CSV = ROOT / "logs" / "catalog-content-goal-priority.csv"
DEFAULT_MD = ROOT / "docs" / "reports" / "catalog-content-goal-audit.md"
DEFAULT_HTML = ROOT / "docs" / "reports" / "catalog-content-goal-review.html"

GENERIC_PATTERNS = {
    "generic_quality_garden": "Producto de calidad para tu jardín",
    "generic_excellent_option": "es una excelente opción para tu jardín o espacio verde",
    "generic_cultivated_plant": "esta planta ha sido cultivada",
    "generic_comprar_plantas": "Plantas de calidad para tu jardín",
    "generic_best_for_garden": "Tu jardín merece lo mejor",
}

PACKAGED_TERMS = {
    "glacoxan", "fertifox", "fungoxan", "fertilizante", "fungicida",
    "herbicida", "insecticida", "molusquicida", "acaricida", "hormiguicida",
    "oil", "ciper", "imida", "avam", "mcpa", "glaco", "ferti", "fertul",
}
ACCESSORY_TERMS = {
    "maceta", "matri", "ta plastic", "rocio", "rocío", "owen", "gancho",
    "jardinera", "plato", "bandeja", "tutor", "colgante", "mmat", "mtap",
    "jare", "owred",
}
PLANT_TERMS = {
    "planta", "arbusto", "arbol", "árbol", "jazmin", "jazmín", "olivo",
    "glicina", "dracena", "laurel", "tilo", "abedul", "liquidambar",
    "raphis", "palmera", "ficus", "rosa", "crespón", "crespon",
    "bougainvillea", "cañatac", "canatac", "abel",
}


@dataclass
class ContentFinding:
    product_id: int
    name: str
    sku: str
    slug: str
    product_kind: str
    categories: str
    flags: List[str]
    priority: str
    current_short_length: int
    current_long_length: int
    attribute_count: int
    recommendation: str


def load_env() -> None:
    if load_dotenv:
        load_dotenv(ROOT / ".env")


def strip_html(value: str) -> str:
    value = re.sub(r"<br\s*/?>", " ", str(value or ""), flags=re.I)
    value = re.sub(r"</p\s*>", " ", value, flags=re.I)
    value = re.sub(r"<[^>]+>", " ", value)
    value = html.unescape(value)
    return re.sub(r"\s+", " ", value).strip()


def norm(value: str) -> str:
    return strip_html(value).lower()


def contains_any(text: str, terms: Iterable[str]) -> bool:
    normalized = norm(text)
    return any(term in normalized for term in terms)


def classify_product(product: Dict[str, Any]) -> str:
    name = product.get("name", "")
    sku = product.get("sku", "")
    cats = " ".join(c.get("name", "") for c in product.get("categories", []))
    tags = " ".join(t.get("name", "") for t in product.get("tags", []))
    text = f"{name} {sku} {cats} {tags}"

    if contains_any(text, PACKAGED_TERMS):
        return "packaged_input"
    if contains_any(text, ACCESSORY_TERMS):
        return "pot_or_accessory"
    if contains_any(text, PLANT_TERMS):
        return "plant_species"
    return "general_catalog"


def expected_category(kind: str) -> str:
    return {
        "packaged_input": "insumos",
        "pot_or_accessory": "macetas",
        "plant_species": "plantas",
        "general_catalog": "tienda",
    }[kind]


def has_correct_category(product: Dict[str, Any], kind: str) -> bool:
    categories = {norm(c.get("name", "")) for c in product.get("categories", [])}
    slugs = {norm(c.get("slug", "")) for c in product.get("categories", [])}
    merged = categories | slugs
    if kind == "packaged_input":
        return bool(merged & {"fertilizantes", "fungicidas", "funguicidas", "herbicidas", "insecticidas", "molusquicidas"})
    if kind == "pot_or_accessory":
        return bool(merged & {"macetas", "maceta", "jarrones", "accesorios"})
    if kind == "plant_species":
        return bool(merged & {"plantas", "planta"})
    return bool(merged - {"uncategorized", "sin categoria", "sin categoría"})


def recommended_attributes(kind: str) -> List[str]:
    if kind == "plant_species":
        return ["presentacion", "luz", "riego", "cuidado"]
    if kind == "packaged_input":
        return ["tipo", "presentacion", "modo de uso"]
    if kind == "pot_or_accessory":
        return ["material", "medida", "color"]
    return ["presentacion"]


def flag_product(product: Dict[str, Any]) -> ContentFinding:
    product_id = int(product.get("id") or 0)
    name = str(product.get("name") or "").strip()
    sku = str(product.get("sku") or "").strip()
    slug = str(product.get("slug") or "").strip()
    kind = classify_product(product)
    categories = ", ".join(c.get("name", "") for c in product.get("categories", []))
    short = strip_html(product.get("short_description", ""))
    long = strip_html(product.get("description", ""))
    all_text = f"{short} {long}"
    attrs = product.get("attributes") or []
    flags: List[str] = []

    if not categories or "uncategorized" in norm(categories):
        flags.append("category_uncategorized")
    elif not has_correct_category(product, kind):
        flags.append("category_mismatch")

    if len(short) < 80:
        flags.append("short_description_too_short")
    if len(short) > 260:
        flags.append("short_description_too_long")
    if len(long) < 240:
        flags.append("long_description_too_short")

    for flag, pattern in GENERIC_PATTERNS.items():
        if pattern.lower() in all_text.lower():
            flags.append(flag)

    if kind != "plant_species" and "esta planta ha sido cultivada" in all_text.lower():
        flags.append("nonplant_described_as_plant")
    if kind == "packaged_input" and "leer la etiqueta" not in all_text.lower() and "leé la etiqueta" not in all_text.lower():
        flags.append("packaged_input_missing_label_warning")

    if len(attrs) == 0:
        flags.append("missing_attributes")
    elif len(attrs) < min(2, len(recommended_attributes(kind))):
        flags.append("thin_attributes")

    if any(f in flags for f in ("nonplant_described_as_plant", "category_uncategorized", "category_mismatch")):
        priority = "P0"
    elif any(f.startswith("generic_") for f in flags) or "short_description_too_short" in flags or "missing_attributes" in flags:
        priority = "P1"
    elif flags:
        priority = "P2"
    else:
        priority = "OK"

    return ContentFinding(
        product_id=product_id,
        name=name,
        sku=sku,
        slug=slug,
        product_kind=kind,
        categories=categories,
        flags=flags,
        priority=priority,
        current_short_length=len(short),
        current_long_length=len(long),
        attribute_count=len(attrs),
        recommendation=recommendation(kind, flags),
    )


def recommendation(kind: str, flags: List[str]) -> str:
    if "category_uncategorized" in flags or "category_mismatch" in flags:
        return f"Asignar categoria correcta para {expected_category(kind)} antes de publicar mejoras."
    if "nonplant_described_as_plant" in flags:
        return "Reescribir descripcion: el producto no debe presentarse como planta."
    if kind == "packaged_input":
        return "Completar tipo, presentacion, uso general y advertencia de leer etiqueta."
    if kind == "pot_or_accessory":
        return "Completar material, medida, color, uso recomendado y compatibilidad."
    if kind == "plant_species":
        return "Completar especie/variedad si es verificable, presentacion, luz, riego y cuidados."
    return "Reemplazar texto generico por una ficha comercial especifica."


def fetch_products(base_url: str, wc_key: str, wc_secret: str, limit: int = 0) -> List[Dict[str, Any]]:
    products: List[Dict[str, Any]] = []
    session = requests.Session()
    page = 1
    while True:
        response = session.get(
            f"{base_url.rstrip('/')}/wp-json/wc/v3/products",
            params={"per_page": 100, "page": page, "status": "publish"},
            auth=(wc_key, wc_secret),
            timeout=45,
        )
        if response.status_code != 200:
            raise RuntimeError(f"WooCommerce request failed: HTTP {response.status_code} {response.text[:180]}")
        batch = response.json()
        if not batch:
            break
        products.extend(batch)
        if limit and len(products) >= limit:
            return products[:limit]
        page += 1
    return products


def build_summary(findings: List[ContentFinding]) -> Dict[str, Any]:
    return {
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "total_products": len(findings),
        "priority_counts": dict(Counter(f.priority for f in findings)),
        "product_kind_counts": dict(Counter(f.product_kind for f in findings)),
        "flag_counts": dict(Counter(flag for f in findings for flag in f.flags)),
        "products_ok": sum(1 for f in findings if f.priority == "OK"),
        "products_with_findings": sum(1 for f in findings if f.priority != "OK"),
    }


def priority_rank(priority: str) -> int:
    return {"P0": 0, "P1": 1, "P2": 2, "OK": 3}.get(priority, 9)


def write_json(path: Path, summary: Dict[str, Any], findings: List[ContentFinding]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(
        json.dumps({"summary": summary, "findings": [asdict(f) for f in findings]}, ensure_ascii=False, indent=2),
        encoding="utf-8",
    )


def write_csv(path: Path, findings: List[ContentFinding]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    ordered = sorted(findings, key=lambda f: (priority_rank(f.priority), f.product_id))
    with path.open("w", newline="", encoding="utf-8") as handle:
        writer = csv.writer(handle)
        writer.writerow([
            "priority", "product_id", "name", "sku", "kind", "categories",
            "flags", "short_len", "long_len", "attribute_count", "recommendation",
        ])
        for f in ordered:
            writer.writerow([
                f.priority, f.product_id, f.name, f.sku, f.product_kind, f.categories,
                "|".join(f.flags), f.current_short_length, f.current_long_length,
                f.attribute_count, f.recommendation,
            ])


def write_markdown(path: Path, summary: Dict[str, Any], findings: List[ContentFinding]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    rows = [f for f in sorted(findings, key=lambda f: (priority_rank(f.priority), f.product_id)) if f.priority != "OK"][:60]
    lines = [
        "# Ficha de Producto 360 - Auditoria de Contenido",
        "",
        f"Generado: `{summary['generated_at']}`",
        "",
        "## Resumen",
        "",
        f"- Productos auditados: `{summary['total_products']}`",
        f"- Productos OK: `{summary['products_ok']}`",
        f"- Productos con hallazgos: `{summary['products_with_findings']}`",
        "",
        "## Prioridades",
        "",
    ]
    for key in ("P0", "P1", "P2", "OK"):
        lines.append(f"- `{key}`: `{summary['priority_counts'].get(key, 0)}`")
    lines.extend(["", "## Flags", ""])
    for flag, count in sorted(summary["flag_counts"].items(), key=lambda item: (-item[1], item[0])):
        lines.append(f"- `{flag}`: `{count}`")
    lines.extend([
        "",
        "## Primeros Productos A Corregir",
        "",
        "| Prioridad | Producto | Tipo | Flags | Recomendacion |",
        "|---|---|---|---|---|",
    ])
    for f in rows:
        lines.append(
            f"| {f.priority} | #{f.product_id} {escape_md(f.name)} | {f.product_kind} | "
            f"`{', '.join(f.flags)}` | {escape_md(f.recommendation)} |"
        )
    path.write_text("\n".join(lines) + "\n", encoding="utf-8")


def write_html(path: Path, summary: Dict[str, Any], findings: List[ContentFinding], max_rows: int) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    rows = [f for f in sorted(findings, key=lambda f: (priority_rank(f.priority), f.product_id)) if f.priority != "OK"][:max_rows]
    cards = []
    for f in rows:
        cards.append(f"""
        <article class="card {html.escape(f.priority.lower())}">
          <div class="meta">{html.escape(f.priority)} · #{f.product_id} · {html.escape(f.product_kind)}</div>
          <h2>{html.escape(f.name)}</h2>
          <p><strong>SKU:</strong> {html.escape(f.sku or "-")}</p>
          <p><strong>Categorias:</strong> {html.escape(f.categories or "-")}</p>
          <p><strong>Flags:</strong> {html.escape(", ".join(f.flags))}</p>
          <p>{html.escape(f.recommendation)}</p>
        </article>
        """)
    document = f"""<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ficha de Producto 360 - Auditoria</title>
  <style>
    body {{ margin: 0; font-family: Inter, system-ui, -apple-system, sans-serif; color: #17231d; background: #f5f7f2; }}
    header {{ padding: 30px clamp(16px, 4vw, 56px); background: #244735; color: white; }}
    h1 {{ margin: 0 0 8px; font-size: clamp(28px, 4vw, 48px); }}
    .stats {{ display: flex; flex-wrap: wrap; gap: 12px; margin-top: 20px; }}
    .stat {{ border: 1px solid rgba(255,255,255,.24); border-radius: 8px; padding: 12px 14px; min-width: 140px; }}
    .stat b {{ display: block; font-size: 25px; }}
    main {{ padding: 28px clamp(16px, 4vw, 56px); }}
    .grid {{ display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 16px; }}
    .card {{ background: white; border: 1px solid #dce4d8; border-radius: 8px; padding: 16px; box-shadow: 0 10px 22px rgba(23,35,29,.06); }}
    .p0 {{ border-color: #a73524; }}
    .p1 {{ border-color: #b68221; }}
    .p2 {{ border-color: #768aa1; }}
    .meta {{ font-size: 12px; font-weight: 800; text-transform: uppercase; color: #53645a; }}
    h2 {{ font-size: 18px; line-height: 1.25; margin: 8px 0 10px; }}
    p {{ font-size: 14px; line-height: 1.45; color: #4a574f; }}
  </style>
</head>
<body>
  <header>
    <h1>Ficha de Producto 360</h1>
    <p>Auditoria de contenido, categorias, atributos y textos genericos.</p>
    <div class="stats">
      <div class="stat"><b>{summary['total_products']}</b> productos</div>
      <div class="stat"><b>{summary['priority_counts'].get('P0', 0)}</b> P0</div>
      <div class="stat"><b>{summary['priority_counts'].get('P1', 0)}</b> P1</div>
      <div class="stat"><b>{summary['products_ok']}</b> OK</div>
    </div>
  </header>
  <main><div class="grid">{''.join(cards)}</div></main>
</body>
</html>
"""
    path.write_text(document, encoding="utf-8")


def escape_md(value: str) -> str:
    return str(value or "").replace("|", "\\|").replace("\n", " ")


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Audit WooCommerce product content quality.")
    parser.add_argument("--url", default="", help="WordPress base URL. Defaults to WORDPRESS_URL from .env.")
    parser.add_argument("--limit", type=int, default=0)
    parser.add_argument("--json-output", default=str(DEFAULT_JSON))
    parser.add_argument("--csv-output", default=str(DEFAULT_CSV))
    parser.add_argument("--md-output", default=str(DEFAULT_MD))
    parser.add_argument("--html-output", default=str(DEFAULT_HTML))
    parser.add_argument("--html-max-rows", type=int, default=160)
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    load_env()
    base_url = (args.url or os.getenv("WORDPRESS_URL") or "").strip()
    wc_key = (os.getenv("WC_CONSUMER_KEY") or "").strip()
    wc_secret = (os.getenv("WC_CONSUMER_SECRET") or "").strip()
    if not base_url or not wc_key or not wc_secret:
        print("Missing WORDPRESS_URL, WC_CONSUMER_KEY or WC_CONSUMER_SECRET", file=sys.stderr)
        return 2
    products = fetch_products(base_url, wc_key, wc_secret, limit=args.limit)
    findings = [flag_product(product) for product in products]
    summary = build_summary(findings)
    write_json(Path(args.json_output), summary, findings)
    write_csv(Path(args.csv_output), findings)
    write_markdown(Path(args.md_output), summary, findings)
    write_html(Path(args.html_output), summary, findings, args.html_max_rows)
    print(json.dumps(summary, ensure_ascii=False, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
