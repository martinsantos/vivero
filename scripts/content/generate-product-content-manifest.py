#!/usr/bin/env python3
"""Generate curated product-content update manifests.

The script reads the content audit output and current WooCommerce products,
then emits a JSON manifest. It does not update WordPress.
"""

from __future__ import annotations

import argparse
import html
import json
import os
import re
import sys
from datetime import datetime, timezone
from pathlib import Path
from typing import Any, Dict, Iterable, List, Optional, Set, Tuple

import requests

try:
    from dotenv import load_dotenv
except Exception:  # pragma: no cover
    load_dotenv = None


ROOT = Path(__file__).resolve().parents[2]
DEFAULT_AUDIT = ROOT / "logs" / "catalog-content-goal-audit.json"
DEFAULT_OUTPUT = ROOT / "data" / "content-manifests" / "product-content-pilot.json"


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
    text = " ".join([
        str(product.get("name", "")),
        str(product.get("sku", "")),
        " ".join(c.get("name", "") for c in product.get("categories", [])),
        " ".join(t.get("name", "") for t in product.get("tags", [])),
    ])
    if contains_any(text, PACKAGED_TERMS):
        return "packaged_input"
    if contains_any(text, ACCESSORY_TERMS):
        return "pot_or_accessory"
    if contains_any(text, PLANT_TERMS):
        return "plant_species"
    return "general_catalog"


def fetch_products(base_url: str, wc_key: str, wc_secret: str) -> Dict[int, Dict[str, Any]]:
    products: Dict[int, Dict[str, Any]] = {}
    page = 1
    session = requests.Session()
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
        for product in batch:
            products[int(product["id"])] = product
        page += 1
    return products


def parse_product_ids(raw: str) -> Set[int]:
    ids: Set[int] = set()
    for part in re.split(r"[\s,]+", raw.strip()):
        if part:
            ids.add(int(part))
    return ids


def extract_size(product: Dict[str, Any]) -> str:
    name = strip_html(product.get("name", ""))
    sku = product.get("sku", "")
    text = f"{name} {sku}"
    match = re.search(r"(\d+(?:[,.]\d+)?)\s*(litros?|lts?|l\b|cm|cc|grs?|g\b)", text, flags=re.I)
    if not match:
        return "presentacion indicada"
    value = match.group(1).replace(",", ".")
    unit = match.group(2).lower()
    if unit.startswith("l"):
        unit = "litros"
    elif unit == "g":
        unit = "gr"
    return f"{value} {unit}"


def attr_option(product: Dict[str, Any], key: str) -> str:
    key = key.lower()
    for attr in product.get("attributes", []):
        name = str(attr.get("name", "")).lower()
        if key in name:
            options = attr.get("options") or []
            return str(options[0]) if options else ""
    return ""


def infer_color(product: Dict[str, Any]) -> str:
    color = attr_option(product, "color")
    if color:
        return color
    name = strip_html(product.get("name", ""))
    match = re.search(r"color:\s*([^-.]+)", name, flags=re.I)
    return match.group(1).strip() if match else ""


def infer_measure(product: Dict[str, Any]) -> str:
    measure = attr_option(product, "paño") or attr_option(product, "medida")
    if measure:
        return measure
    name = strip_html(product.get("name", ""))
    match = re.search(r"(\d+(?:x\d+)?\s*cm)", name, flags=re.I)
    return match.group(1).strip() if match else extract_size(product)


def paragraph(text: str) -> str:
    return f"<p>{html.escape(text)}</p>"


def list_html(items: List[str]) -> str:
    return "<ul>" + "".join(f"<li>{html.escape(item)}</li>" for item in items) + "</ul>"


def build_packaged(product: Dict[str, Any]) -> Dict[str, Any]:
    name = strip_html(product.get("name", ""))
    presentation = extract_size(product)
    lower = name.lower()
    if "herbicida" in lower or "mcpa" in lower or "total" in lower or "glacoh" in lower or "hsel" in lower or "htot" in lower:
        kind = "herbicida"
    elif "fungo" in lower or "fungicida" in lower or "fung" in lower:
        kind = "fungicida"
    elif "fertifox" in lower or "fertiliz" in lower or "ferti" in lower or "fertul" in lower:
        kind = "fertilizante"
    elif "molus" in lower or "pell" in lower:
        kind = "molusquicida"
    elif "insect" in lower or "d-sist" in lower or "ciper" in lower or "cip" in lower or "imida" in lower or "glacoim" in lower or "avam" in lower or "oil" in lower or "cebo" in lower or "ceb" in lower:
        kind = "insecticida"
    else:
        kind = "insecticida"

    short = f"{name}: {kind} en presentación {presentation}, para uso responsable en jardín, huerta o vivero según indicaciones de etiqueta."
    description = "\n".join([
        paragraph(f"{name} es un {kind} para tareas de cuidado y mantenimiento de espacios verdes. La presentación {presentation} facilita su uso en trabajos puntuales de jardín, vivero o huerta familiar."),
        paragraph("Antes de aplicar, leer la etiqueta del fabricante y respetar dosis, compatibilidades, elementos de protección y tiempos de seguridad indicados para el producto."),
        "<h3>Uso recomendado</h3>",
        list_html([
            "Aplicar únicamente sobre el objetivo indicado por el fabricante.",
            "Evitar derivas, excesos de producto y aplicaciones en horarios de alta temperatura.",
            "Mantener fuera del alcance de niños, mascotas y alimentos.",
        ]),
        paragraph("En Vivero Los Cocos podemos orientarte para elegir el producto adecuado según el problema a resolver, sin reemplazar las instrucciones oficiales del envase."),
    ])
    return {
        "short_description": short,
        "description": description,
        "attributes": [
            attr("Tipo", kind.title()),
            attr("Presentación", presentation),
            attr("Uso", "Segun etiqueta del fabricante"),
        ],
        "meta_data": [
            meta("_loscocos_tipo_accion", kind),
            meta("_loscocos_modo_uso", "pulverizacion"),
        ],
        "tags": ["Insumos de jardin", kind.title(), presentation],
        "category_hint": kind,
    }


def build_accessory(product: Dict[str, Any]) -> Dict[str, Any]:
    name = strip_html(product.get("name", ""))
    measure = infer_measure(product)
    color = infer_color(product)
    is_hook = "gancho" in name.lower()
    material = "Plastico"
    product_label = "gancho trenzado" if is_hook else "maceta o contenedor"
    short = f"{name}: {product_label} de {material.lower()} en medida {measure}" + (f" y color {color}" if color else "") + "."
    description = "\n".join([
        paragraph(f"{name} es un accesorio pensado para ordenar, sostener o presentar plantas de manera practica en espacios interiores y exteriores."),
        "<h3>Caracteristicas principales</h3>",
        list_html([
            f"Medida: {measure}.",
            f"Material: {material}.",
            f"Color: {color or 'segun variante seleccionada'}.",
            "Uso recomendado: jardin, balcon, patio, interior luminoso o vivero.",
        ]),
        paragraph("Combiná este producto con plantas de tamaño compatible para lograr una presentación prolija, estable y fácil de mantener."),
    ])
    return {
        "short_description": short,
        "description": description,
        "attributes": [
            attr("Material", material),
            attr("Medida", measure),
            attr("Color", color or "Segun variante"),
        ],
        "meta_data": [
            meta("_loscocos_material_maceta", "plastico"),
            meta("_loscocos_drenaje", "con_agujero" if not is_hook else "sin_agujero"),
        ],
        "tags": ["Macetas y accesorios", material, color] if color else ["Macetas y accesorios", material],
        "category_hint": "macetas",
    }


def build_plant(product: Dict[str, Any]) -> Dict[str, Any]:
    name = strip_html(product.get("name", ""))
    presentation = extract_size(product)
    needs_source = "Producto de Vivero" in name or re.search(r"\b[A-Z]{4,}\d", product.get("sku", ""))
    short = f"{name}: planta en presentación {presentation}, seleccionada para jardín, patio o espacios verdes de Mendoza."
    description = "\n".join([
        paragraph(f"{name} se entrega en presentación {presentation}, lista para trasplante o mantenimiento en maceta según el uso previsto."),
        paragraph("Es una opción de vivero para sumar verde real a patios, jardines, galerías o espacios interiores luminosos según la adaptación de cada ejemplar."),
        "<h3>Cuidados generales</h3>",
        list_html([
            "Ubicar en un lugar con buena luz y adaptar gradualmente si cambia de ambiente.",
            "Regar cuando el sustrato empiece a perder humedad, evitando encharcamientos.",
            "Revisar drenaje, viento y exposición solar según la respuesta de la planta.",
        ]),
        paragraph("Consultanos antes de comprar si querés combinarla con maceta, sustrato u otras plantas del vivero."),
    ])
    return {
        "short_description": short,
        "description": description,
        "attributes": [
            attr("Presentación", presentation),
            attr("Luz", "Buena luz natural"),
            attr("Riego", "Moderado, sin encharcar"),
        ],
        "meta_data": [
            meta("_loscocos_luminosidad", "luz_parcial"),
            meta("_loscocos_riego", "moderado"),
            meta("_loscocos_nivel_cuidado", "facil"),
            meta("_loscocos_content_review_status", "Needs Source" if needs_source else "Generated Review"),
        ],
        "tags": ["Plantas", presentation],
        "category_hint": "plantas",
    }


def build_general(product: Dict[str, Any]) -> Dict[str, Any]:
    name = strip_html(product.get("name", ""))
    presentation = extract_size(product)
    return {
        "short_description": f"{name}: producto de vivero en presentación {presentation}, disponible para compra online y entrega coordinada.",
        "description": "\n".join([
            paragraph(f"{name} forma parte del catálogo de Vivero Los Cocos y está disponible para resolver necesidades de jardín, patio, vivero o mantenimiento de espacios verdes."),
            paragraph("La ficha fue normalizada para evitar contenido genérico y mantener información clara hasta contar con una fuente técnica más específica."),
        ]),
        "attributes": [attr("Presentación", presentation)],
        "meta_data": [meta("_loscocos_content_review_status", "Generated Review")],
        "tags": ["Vivero"],
        "category_hint": "tienda",
    }


def attr(name: str, value: str) -> Dict[str, Any]:
    return {"name": name, "visible": True, "variation": False, "options": [str(value)]}


def meta(key: str, value: Any) -> Dict[str, Any]:
    return {"key": key, "value": value}


def build_update(product: Dict[str, Any]) -> Dict[str, Any]:
    kind = classify_product(product)
    if kind == "packaged_input":
        generated = build_packaged(product)
    elif kind == "pot_or_accessory":
        generated = build_accessory(product)
    elif kind == "plant_species":
        generated = build_plant(product)
    else:
        generated = build_general(product)

    generated["product_id"] = int(product["id"])
    generated["sku"] = product.get("sku", "")
    generated["name"] = strip_html(product.get("name", ""))
    generated["product_kind"] = kind
    generated["previous"] = {
        "short_description": product.get("short_description", ""),
        "description": product.get("description", ""),
        "attributes": product.get("attributes", []),
        "categories": product.get("categories", []),
        "tags": product.get("tags", []),
    }
    generated["review_status"] = "Generated Review"
    generated["source_policy"] = "Curado por reglas de catalogo; sin datos tecnicos sensibles inventados."
    return generated


def select_product_ids(audit: Dict[str, Any], explicit_ids: Set[int], limit: int, kinds: Set[str]) -> List[int]:
    if explicit_ids:
        return sorted(explicit_ids)
    findings = audit.get("findings", [])
    selected = []
    for finding in sorted(findings, key=lambda f: ({"P0": 0, "P1": 1, "P2": 2, "OK": 3}.get(f["priority"], 9), f["product_id"])):
        if finding["priority"] == "OK":
            continue
        if kinds and finding.get("product_kind") not in kinds:
            continue
        selected.append(int(finding["product_id"]))
        if limit and len(selected) >= limit:
            break
    return selected


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Generate a product content update manifest.")
    parser.add_argument("--audit", default=str(DEFAULT_AUDIT))
    parser.add_argument("--output", default=str(DEFAULT_OUTPUT))
    parser.add_argument("--product-ids", default="", help="Comma/space separated product IDs")
    parser.add_argument("--limit", type=int, default=30)
    parser.add_argument("--kinds", default="", help="Comma separated product kinds to include")
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    load_env()
    base_url = os.getenv("WORDPRESS_URL", "").strip()
    wc_key = os.getenv("WC_CONSUMER_KEY", "").strip()
    wc_secret = os.getenv("WC_CONSUMER_SECRET", "").strip()
    if not base_url or not wc_key or not wc_secret:
        print("Missing WORDPRESS_URL, WC_CONSUMER_KEY or WC_CONSUMER_SECRET", file=sys.stderr)
        return 2
    audit = json.loads(Path(args.audit).read_text(encoding="utf-8"))
    explicit_ids = parse_product_ids(args.product_ids) if args.product_ids else set()
    kinds = {k.strip() for k in args.kinds.split(",") if k.strip()}
    selected_ids = select_product_ids(audit, explicit_ids, args.limit, kinds)
    products = fetch_products(base_url, wc_key, wc_secret)
    updates = [build_update(products[pid]) for pid in selected_ids if pid in products]
    payload = {
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "mode": "manifest_only",
        "count": len(updates),
        "product_ids": [u["product_id"] for u in updates],
        "updates": updates,
    }
    output = Path(args.output)
    output.parent.mkdir(parents=True, exist_ok=True)
    output.write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")
    print(json.dumps({"output": str(output), "count": len(updates), "product_ids": payload["product_ids"]}, ensure_ascii=False, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
