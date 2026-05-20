#!/usr/bin/env python3
"""Read-only product image audit for the Catalog Visual Premium goal.

The script reads WooCommerce products, scores the current featured image state,
and writes review artifacts. It never uploads media or updates products.
"""

from __future__ import annotations

import argparse
import csv
import html
import io
import json
import os
import re
import sys
from collections import Counter, defaultdict
from concurrent.futures import ThreadPoolExecutor, as_completed
from dataclasses import asdict, dataclass
from datetime import datetime, timezone
from pathlib import Path
from typing import Any, Dict, Iterable, List, Optional, Tuple
from urllib.parse import urlparse

import requests

try:
    from dotenv import load_dotenv
except Exception:  # pragma: no cover
    load_dotenv = None

try:
    from PIL import Image, ImageFile

    ImageFile.LOAD_TRUNCATED_IMAGES = True
    HAS_PIL = True
except Exception:  # pragma: no cover
    HAS_PIL = False


ROOT = Path(__file__).resolve().parents[2]
DEFAULT_JSON = ROOT / "logs" / "catalog-image-goal-audit.json"
DEFAULT_CSV = ROOT / "logs" / "catalog-image-goal-priority.csv"
DEFAULT_MD = ROOT / "docs" / "reports" / "catalog-image-goal-audit.md"
DEFAULT_HTML = ROOT / "docs" / "reports" / "catalog-image-goal-review.html"
DEFAULT_APPROVALS = ROOT / "data" / "catalog-image-duplicate-approvals.json"

PLANT_TERMS = {
    "abedul", "acacia", "aguaribay", "alamo", "álamo", "algarrobo", "araucaria",
    "arbol", "árbol", "arbusto", "azalea", "bambu", "bambú", "begonia",
    "camellia", "cedro", "cerezo", "cipres", "ciprés", "ciruelo", "crespon",
    "crespón", "duraznero", "eucalipto", "fresno", "ginkgo", "glicina",
    "granado", "hibisco", "hortensia", "jacaranda", "jazmin", "jazmín",
    "laurel", "limonero", "liquidambar", "magnolia", "manzano", "morera",
    "naranjo", "nogal", "olivo", "palmera", "paraiso", "paraíso", "pino",
    "planta", "plantas", "plátano", "platano", "roble", "rosa", "sauce",
    "tilo", "tulipan", "tulipán", "vid", "wisteria",
}
PACKAGED_TERMS = {
    "fertilizante", "fertilizantes", "fungicida", "fungicidas", "herbicida",
    "herbicidas", "insecticida", "insecticidas", "molusquicida", "molusquicidas",
    "glacoxan", "mamboreta", "mamboretá", "fertifox", "terrafertil", "sustrato",
    "tierra", "abono", "fertiliz", "roundup", "veneno", "control",
}
ACCESSORY_TERMS = {
    "maceta", "macetas", "rocio", "rocío", "plastica", "plástica", "plastico",
    "plástico", "barro", "jardinera", "tutor", "aspersor", "manguera", "bandeja",
    "matri", "ta plastic", "gancho", "owen", "cuadrada", "fin", "colgante",
    "cachepot", "contenedor",
}
PLACEHOLDER_TERMS = {"placeholder", "default", "sample", "generic", "sin-imagen", "no-image"}


@dataclass
class ProductFinding:
    product_id: int
    name: str
    sku: str
    slug: str
    categories: str
    product_kind: str
    source_policy: str
    image_id: Optional[int]
    image_url: str
    image_alt: str
    flags: List[str]
    priority: str
    recommendation: str


def norm_text(value: Any) -> str:
    value = re.sub(r"<[^>]+>", " ", str(value or ""))
    value = value.lower()
    value = re.sub(r"\s+", " ", value)
    return value.strip()


def token_hit(text: str, terms: Iterable[str]) -> bool:
    haystack = norm_text(text)
    return any(term in haystack for term in terms)


def classify_product(product: Dict[str, Any]) -> Tuple[str, str]:
    name = product.get("name") or ""
    cats = " ".join(c.get("name", "") for c in product.get("categories", []))
    text = f"{name} {cats} {product.get('short_description', '')}"

    if token_hit(text, PACKAGED_TERMS):
        return "packaged_input", "Imagen real del envase/producto comercial; evitar fotos genericas de plantas."
    if token_hit(text, ACCESSORY_TERMS):
        return "pot_or_accessory", "Imagen real del modelo, color y tamano; variantes solo si son visualmente iguales."
    if "planta" in norm_text(cats) or token_hit(text, PLANT_TERMS):
        return "plant_species", "Foto botanicamente correcta de la especie/variedad, idealmente propia o de fuente verificable."
    return "general_catalog", "Imagen real y especifica del producto; fuente y licencia trazables."


def image_basename(url: str) -> str:
    path = urlparse(url or "").path
    return os.path.basename(path).lower()


def flag_product(
    product: Dict[str, Any],
    duplicate_image_ids: set[int],
    duplicate_srcs: set[str],
    perceptual_duplicate_ids: set[int],
) -> ProductFinding:
    product_id = int(product.get("id") or 0)
    name = str(product.get("name") or "").strip()
    sku = str(product.get("sku") or "").strip()
    slug = str(product.get("slug") or "").strip()
    categories = ", ".join(c.get("name", "") for c in product.get("categories", []))
    kind, policy = classify_product(product)

    images = product.get("images") or []
    first = images[0] if images else {}
    image_id = first.get("id")
    image_id = int(image_id) if image_id else None
    image_url = str(first.get("src") or "")
    image_alt = str(first.get("alt") or "").strip()
    file_name = image_basename(image_url)
    flags: List[str] = []

    if not images or not image_url:
        flags.append("missing_featured_image")
    if image_url and token_hit(f"{image_url} {image_alt} {first.get('name', '')}", PLACEHOLDER_TERMS):
        flags.append("placeholder_or_generic")
    if file_name.endswith(".svg"):
        flags.append("synthetic_svg")
    if image_id and image_id in duplicate_image_ids:
        flags.append("duplicate_media_id")
    if image_url and file_name in duplicate_srcs:
        flags.append("duplicate_file")
    if product_id in perceptual_duplicate_ids:
        flags.append("unapproved_perceptual_duplicate")
    if not image_alt:
        flags.append("missing_alt")
    elif len(norm_text(image_alt)) < 8:
        flags.append("weak_alt")
    if kind == "packaged_input" and token_hit(image_url, PLANT_TERMS) and not token_hit(image_url, PACKAGED_TERMS):
        flags.append("packaged_product_may_have_plant_stock_photo")
    if kind == "plant_species" and token_hit(image_url, PACKAGED_TERMS):
        flags.append("plant_may_have_packaged_product_photo")

    if any(f in flags for f in ("missing_featured_image", "placeholder_or_generic", "synthetic_svg")):
        priority = "P0"
    elif any(f in flags for f in ("duplicate_media_id", "duplicate_file", "unapproved_perceptual_duplicate", "packaged_product_may_have_plant_stock_photo", "plant_may_have_packaged_product_photo")):
        priority = "P1"
    elif any(f in flags for f in ("missing_alt", "weak_alt")):
        priority = "P2"
    else:
        priority = "OK"

    recommendation = recommendation_for(kind, flags)

    return ProductFinding(
        product_id=product_id,
        name=name,
        sku=sku,
        slug=slug,
        categories=categories,
        product_kind=kind,
        source_policy=policy,
        image_id=image_id,
        image_url=image_url,
        image_alt=image_alt,
        flags=flags,
        priority=priority,
        recommendation=recommendation,
    )


def recommendation_for(kind: str, flags: List[str]) -> str:
    if "missing_featured_image" in flags:
        return "Asignar imagen principal antes de publicar el producto en grillas."
    if "placeholder_or_generic" in flags or "synthetic_svg" in flags:
        return "Reemplazar por imagen real 1200x1200 WebP con fuente trazable."
    if "duplicate_media_id" in flags or "duplicate_file" in flags:
        return "Validar si es variante real; si no, reemplazar por imagen unica."
    if kind == "plant_species":
        return "Validar especie/variedad y usar foto botanica precisa."
    if kind == "packaged_input":
        return "Usar packshot real del envase y conservar galeria si agrega contexto."
    if kind == "pot_or_accessory":
        return "Usar foto del modelo exacto, color y tamano."
    return "Mantener si la imagen representa exactamente el producto."


def load_env() -> None:
    if load_dotenv:
        load_dotenv(ROOT / ".env")


def fetch_products(base_url: str, wc_key: str, wc_secret: str, limit: int = 0) -> List[Dict[str, Any]]:
    products: List[Dict[str, Any]] = []
    session = requests.Session()
    page = 1
    while True:
        params = {"per_page": 100, "page": page, "status": "publish"}
        url = f"{base_url.rstrip('/')}/wp-json/wc/v3/products"
        response = session.get(url, params=params, auth=(wc_key, wc_secret), timeout=45)
        if response.status_code != 200:
            raise RuntimeError(f"WooCommerce products request failed: HTTP {response.status_code} {response.text[:180]}")
        batch = response.json()
        if not batch:
            break
        products.extend(batch)
        if limit and len(products) >= limit:
            return products[:limit]
        page += 1
    return products


def duplicate_sets(products: List[Dict[str, Any]]) -> Tuple[set[int], set[str], Dict[str, Any]]:
    id_to_products: Dict[int, List[int]] = defaultdict(list)
    src_to_products: Dict[str, List[int]] = defaultdict(list)

    for product in products:
        images = product.get("images") or []
        if not images:
            continue
        first = images[0]
        image_id = first.get("id")
        if image_id:
            id_to_products[int(image_id)].append(int(product.get("id") or 0))
        src = image_basename(first.get("src") or "")
        if src:
            src_to_products[src].append(int(product.get("id") or 0))

    duplicate_ids = {image_id for image_id, ids in id_to_products.items() if len(ids) > 1}
    duplicate_srcs = {src for src, ids in src_to_products.items() if len(ids) > 1}
    details = {
        "duplicate_media_ids": {str(k): v for k, v in id_to_products.items() if len(v) > 1},
        "duplicate_files": {k: v for k, v in src_to_products.items() if len(v) > 1},
    }
    return duplicate_ids, duplicate_srcs, details


def dhash_image(image: "Image.Image", hash_size: int = 8) -> int:
    gray = image.convert("L").resize((hash_size + 1, hash_size))
    pixels = list(gray.getdata())
    value = 0
    for row in range(hash_size):
        offset = row * (hash_size + 1)
        for col in range(hash_size):
            left = pixels[offset + col]
            right = pixels[offset + col + 1]
            value = (value << 1) | int(left > right)
    return value


def fetch_dhash(product: Dict[str, Any], timeout: int = 12) -> Tuple[int, Optional[int], str]:
    product_id = int(product.get("id") or 0)
    images = product.get("images") or []
    if not images:
        return product_id, None, "no_image"
    url = images[0].get("src") or ""
    if not url:
        return product_id, None, "no_url"
    try:
        response = requests.get(url, timeout=timeout, headers={"User-Agent": "loscocos-image-audit/1.0"})
        response.raise_for_status()
        with Image.open(io.BytesIO(response.content)) as img:
            return product_id, dhash_image(img), ""
    except Exception as exc:
        return product_id, None, str(exc)[:160]


def hamming(left: int, right: int) -> int:
    return bin(int(left ^ right)).count("1")


def scan_perceptual_duplicates(
    products: List[Dict[str, Any]],
    threshold: int,
    workers: int,
) -> Tuple[set[int], List[Dict[str, Any]], Dict[str, str]]:
    if not HAS_PIL:
        return set(), [], {"dependency": "Pillow is not installed"}

    hashes: Dict[int, int] = {}
    errors: Dict[str, str] = {}
    with ThreadPoolExecutor(max_workers=max(1, workers)) as executor:
        futures = [executor.submit(fetch_dhash, product) for product in products]
        for future in as_completed(futures):
            product_id, image_hash, error = future.result()
            if image_hash is None:
                errors[str(product_id)] = error
            else:
                hashes[product_id] = image_hash

    parent: Dict[int, int] = {product_id: product_id for product_id in hashes}

    def find(item: int) -> int:
        while parent[item] != item:
            parent[item] = parent[parent[item]]
            item = parent[item]
        return item

    def union(left: int, right: int) -> None:
        root_left = find(left)
        root_right = find(right)
        if root_left != root_right:
            parent[root_right] = root_left

    items = list(hashes.items())
    for index, (left_id, left_hash) in enumerate(items):
        for right_id, right_hash in items[index + 1 :]:
            if hamming(left_hash, right_hash) <= threshold:
                union(left_id, right_id)

    grouped: Dict[int, List[int]] = defaultdict(list)
    for product_id in hashes:
        grouped[find(product_id)].append(product_id)

    clusters = []
    duplicate_ids: set[int] = set()
    name_by_id = {int(p.get("id") or 0): str(p.get("name") or "") for p in products}
    for ids in grouped.values():
        if len(ids) < 2:
            continue
        ids = sorted(ids)
        duplicate_ids.update(ids)
        clusters.append({
            "product_ids": ids,
            "products": [{"id": pid, "name": name_by_id.get(pid, "")} for pid in ids],
        })

    clusters.sort(key=lambda group: (-len(group["product_ids"]), group["product_ids"][0]))
    return duplicate_ids, clusters, errors


def load_approved_duplicate_sets(path: Path) -> List[set[int]]:
    if not path.exists():
        return []
    payload = json.loads(path.read_text(encoding="utf-8"))
    groups = payload.get("approved_groups", [])
    approved: List[set[int]] = []
    for group in groups:
        ids = group.get("product_ids", [])
        id_set = {int(pid) for pid in ids if str(pid).strip()}
        if len(id_set) > 1:
            approved.append(id_set)
    return approved


def is_approved_cluster(product_ids: Iterable[int], approved_sets: List[set[int]]) -> bool:
    id_set = {int(pid) for pid in product_ids}
    return any(id_set == approved for approved in approved_sets)


def apply_perceptual_approvals(
    clusters: List[Dict[str, Any]],
    approved_sets: List[set[int]],
) -> Tuple[set[int], List[Dict[str, Any]], List[Dict[str, Any]]]:
    unapproved_clusters: List[Dict[str, Any]] = []
    approved_clusters: List[Dict[str, Any]] = []
    unapproved_ids: set[int] = set()
    for cluster in clusters:
        ids = [int(pid) for pid in cluster.get("product_ids", [])]
        if is_approved_cluster(ids, approved_sets):
            cluster = dict(cluster)
            cluster["approved_reason"] = "variant_same_model_color_or_size_family"
            approved_clusters.append(cluster)
            continue
        unapproved_clusters.append(cluster)
        unapproved_ids.update(ids)
    return unapproved_ids, unapproved_clusters, approved_clusters


def build_summary(products: List[Dict[str, Any]], findings: List[ProductFinding], duplicates: Dict[str, Any]) -> Dict[str, Any]:
    priorities = Counter(f.priority for f in findings)
    kinds = Counter(f.product_kind for f in findings)
    flags = Counter(flag for f in findings for flag in f.flags)
    with_images = sum(1 for p in products if p.get("images"))
    unique_featured_ids = {f.image_id for f in findings if f.image_id}

    return {
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "total_products": len(products),
        "products_with_featured_image": with_images,
        "products_without_featured_image": len(products) - with_images,
        "unique_featured_media_ids": len(unique_featured_ids),
        "priority_counts": dict(priorities),
        "product_kind_counts": dict(kinds),
        "flag_counts": dict(flags),
        "duplicate_media_id_groups": len(duplicates["duplicate_media_ids"]),
        "duplicate_file_groups": len(duplicates["duplicate_files"]),
        "perceptual_duplicate_groups": len(duplicates.get("perceptual_clusters", [])),
        "approved_perceptual_duplicate_groups": len(duplicates.get("approved_perceptual_clusters", [])),
        "unapproved_perceptual_duplicate_groups": len(duplicates.get("unapproved_perceptual_clusters", duplicates.get("perceptual_clusters", []))),
        "products_in_duplicate_media_id_groups": sum(len(v) for v in duplicates["duplicate_media_ids"].values()),
        "products_in_perceptual_duplicate_groups": sum(len(v["product_ids"]) for v in duplicates.get("perceptual_clusters", [])),
        "products_in_unapproved_perceptual_duplicate_groups": sum(len(v["product_ids"]) for v in duplicates.get("unapproved_perceptual_clusters", duplicates.get("perceptual_clusters", []))),
    }


def write_json(path: Path, summary: Dict[str, Any], findings: List[ProductFinding], duplicates: Dict[str, Any]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    payload = {
        "summary": summary,
        "duplicates": duplicates,
        "findings": [asdict(f) for f in findings],
    }
    path.write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")


def write_csv(path: Path, findings: List[ProductFinding]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    ordered = sorted(findings, key=lambda f: ("P0", "P1", "P2", "OK").index(f.priority))
    with path.open("w", newline="", encoding="utf-8") as fh:
        writer = csv.writer(fh)
        writer.writerow([
            "priority", "product_id", "name", "sku", "kind", "image_id",
            "flags", "recommendation", "image_url",
        ])
        for finding in ordered:
            writer.writerow([
                finding.priority,
                finding.product_id,
                finding.name,
                finding.sku,
                finding.product_kind,
                finding.image_id or "",
                "|".join(finding.flags),
                finding.recommendation,
                finding.image_url,
            ])


def write_markdown(path: Path, summary: Dict[str, Any], findings: List[ProductFinding]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    top = [f for f in sorted(findings, key=lambda f: ("P0", "P1", "P2", "OK").index(f.priority)) if f.priority != "OK"][:40]
    lines = [
        "# Catalogo Visual Premium - Auditoria de Imagenes",
        "",
        f"Generado: `{summary['generated_at']}`",
        "",
        "## Resumen",
        "",
        f"- Productos auditados: `{summary['total_products']}`",
        f"- Con imagen principal: `{summary['products_with_featured_image']}`",
        f"- Sin imagen principal: `{summary['products_without_featured_image']}`",
        f"- Imagenes destacadas unicas por media ID: `{summary['unique_featured_media_ids']}`",
        f"- Grupos duplicados por media ID: `{summary['duplicate_media_id_groups']}`",
        f"- Productos dentro de grupos duplicados: `{summary['products_in_duplicate_media_id_groups']}`",
        f"- Grupos duplicados por apariencia visual: `{summary.get('perceptual_duplicate_groups', 0)}`",
        f"- Productos dentro de grupos visuales: `{summary.get('products_in_perceptual_duplicate_groups', 0)}`",
        f"- Grupos visuales aprobados como variantes: `{summary.get('approved_perceptual_duplicate_groups', 0)}`",
        f"- Grupos visuales no aprobados: `{summary.get('unapproved_perceptual_duplicate_groups', 0)}`",
        f"- Productos en grupos visuales no aprobados: `{summary.get('products_in_unapproved_perceptual_duplicate_groups', 0)}`",
        "",
        "## Prioridades",
        "",
    ]
    for key in ("P0", "P1", "P2", "OK"):
        lines.append(f"- `{key}`: `{summary['priority_counts'].get(key, 0)}`")
    lines.extend([
        "",
        "## Flags",
        "",
    ])
    for flag, count in sorted(summary["flag_counts"].items(), key=lambda item: (-item[1], item[0])):
        lines.append(f"- `{flag}`: `{count}`")
    lines.extend([
        "",
        "## Primeros Productos A Revisar",
        "",
        "| Prioridad | Producto | SKU | Tipo | Flags | Recomendacion |",
        "|---|---|---|---|---|---|",
    ])
    for f in top:
        lines.append(
            f"| {f.priority} | #{f.product_id} {escape_md(f.name)} | {escape_md(f.sku)} | "
            f"{f.product_kind} | `{', '.join(f.flags)}` | {escape_md(f.recommendation)} |"
        )
    lines.extend([
        "",
        "## Politica de Reemplazo",
        "",
        "- Plantas: imagen botanicamente correcta de la especie o variedad.",
        "- Insumos comerciales: foto real del envase, no stock generico.",
        "- Macetas/accesorios: modelo, color y tamano correctos.",
        "- Variantes: compartir imagen solo si la diferencia no es visible o esta aprobada.",
        "- Siempre: 1200x1200, WebP/JPEG optimizado, alt text descriptivo y fuente trazable.",
    ])
    path.write_text("\n".join(lines) + "\n", encoding="utf-8")


def escape_md(value: str) -> str:
    return str(value or "").replace("|", "\\|").replace("\n", " ")


def write_html(path: Path, summary: Dict[str, Any], findings: List[ProductFinding], max_cards: int) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    ordered = [f for f in sorted(findings, key=lambda f: ("P0", "P1", "P2", "OK").index(f.priority)) if f.priority != "OK"][:max_cards]
    cards = []
    for f in ordered:
        img = f'<img src="{html.escape(f.image_url)}" alt="">' if f.image_url else '<div class="empty">Sin imagen</div>'
        cards.append(f"""
        <article class="card {html.escape(f.priority.lower())}">
          <div class="thumb">{img}</div>
          <div class="body">
            <div class="priority">{html.escape(f.priority)} · #{f.product_id}</div>
            <h2>{html.escape(f.name)}</h2>
            <p><strong>SKU:</strong> {html.escape(f.sku or "-")}</p>
            <p><strong>Tipo:</strong> {html.escape(f.product_kind)}</p>
            <p><strong>Flags:</strong> {html.escape(", ".join(f.flags) or "OK")}</p>
            <p>{html.escape(f.recommendation)}</p>
          </div>
        </article>
        """)

    document = f"""<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Catalogo Visual Premium - Revision</title>
  <style>
    body {{ margin: 0; font-family: Inter, system-ui, -apple-system, sans-serif; color: #17231d; background: #f5f7f2; }}
    header {{ padding: 32px clamp(16px, 4vw, 56px); background: #244735; color: #fff; }}
    h1 {{ margin: 0 0 10px; font-size: clamp(28px, 4vw, 52px); }}
    .stats {{ display: flex; flex-wrap: wrap; gap: 12px; margin-top: 22px; }}
    .stat {{ border: 1px solid rgba(255,255,255,.22); padding: 12px 14px; border-radius: 8px; min-width: 150px; }}
    .stat b {{ display: block; font-size: 24px; }}
    main {{ padding: 28px clamp(16px, 4vw, 56px); }}
    .grid {{ display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px; }}
    .card {{ background: #fff; border: 1px solid #dce4d8; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 24px rgba(23,35,29,.06); }}
    .thumb {{ aspect-ratio: 1 / 1; background: #edf1ea; display: grid; place-items: center; }}
    .thumb img {{ width: 100%; height: 100%; object-fit: cover; display: block; }}
    .empty {{ color: #68766d; }}
    .body {{ padding: 14px; }}
    h2 {{ margin: 4px 0 8px; font-size: 17px; line-height: 1.25; }}
    p {{ margin: 7px 0; color: #4a574f; font-size: 14px; line-height: 1.45; }}
    .priority {{ font-size: 12px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }}
    .p0 {{ border-color: #a73524; }}
    .p1 {{ border-color: #b68221; }}
    .p2 {{ border-color: #768aa1; }}
  </style>
</head>
<body>
  <header>
    <h1>Catalogo Visual Premium</h1>
    <p>Revision priorizada de imagenes de producto. Este reporte es solo lectura.</p>
    <div class="stats">
      <div class="stat"><b>{summary['total_products']}</b> productos</div>
      <div class="stat"><b>{summary['priority_counts'].get('P0', 0)}</b> P0</div>
      <div class="stat"><b>{summary['priority_counts'].get('P1', 0)}</b> P1</div>
      <div class="stat"><b>{summary['duplicate_media_id_groups']}</b> grupos por ID</div>
      <div class="stat"><b>{summary.get('perceptual_duplicate_groups', 0)}</b> grupos visuales</div>
    </div>
  </header>
  <main>
    <div class="grid">
      {''.join(cards)}
    </div>
  </main>
</body>
</html>
"""
    path.write_text(document, encoding="utf-8")


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Audit WooCommerce product images for Catalog Visual Premium.")
    parser.add_argument("--url", default="", help="WordPress base URL. Defaults to WORDPRESS_URL from .env.")
    parser.add_argument("--limit", type=int, default=0, help="Limit products for testing. 0 means all.")
    parser.add_argument("--json-output", default=str(DEFAULT_JSON))
    parser.add_argument("--csv-output", default=str(DEFAULT_CSV))
    parser.add_argument("--md-output", default=str(DEFAULT_MD))
    parser.add_argument("--html-output", default=str(DEFAULT_HTML))
    parser.add_argument("--html-max-cards", type=int, default=120)
    parser.add_argument("--perceptual", action="store_true", help="Download featured images and detect visual duplicate clusters using dHash.")
    parser.add_argument("--perceptual-threshold", type=int, default=5, help="dHash Hamming distance threshold. Lower is stricter.")
    parser.add_argument("--perceptual-workers", type=int, default=12, help="Concurrent image downloads for perceptual scan.")
    parser.add_argument("--duplicate-approvals", default=str(DEFAULT_APPROVALS), help="JSON registry of approved duplicate product groups.")
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    load_env()
    base_url = (args.url or os.getenv("WORDPRESS_URL") or "").strip()
    wc_key = (os.getenv("WC_CONSUMER_KEY") or "").strip()
    wc_secret = (os.getenv("WC_CONSUMER_SECRET") or "").strip()

    if not base_url or not wc_key or not wc_secret:
        print("Missing WORDPRESS_URL, WC_CONSUMER_KEY or WC_CONSUMER_SECRET in environment/.env", file=sys.stderr)
        return 2

    products = fetch_products(base_url, wc_key, wc_secret, limit=args.limit)
    duplicate_ids, duplicate_srcs, duplicates = duplicate_sets(products)
    perceptual_duplicate_ids: set[int] = set()
    approved_sets = load_approved_duplicate_sets(Path(args.duplicate_approvals))
    if args.perceptual:
        _all_perceptual_ids, clusters, errors = scan_perceptual_duplicates(
            products,
            threshold=args.perceptual_threshold,
            workers=args.perceptual_workers,
        )
        perceptual_duplicate_ids, unapproved_clusters, approved_clusters = apply_perceptual_approvals(clusters, approved_sets)
        duplicates["perceptual_clusters"] = clusters
        duplicates["unapproved_perceptual_clusters"] = unapproved_clusters
        duplicates["approved_perceptual_clusters"] = approved_clusters
        duplicates["perceptual_errors"] = errors
    else:
        duplicates["perceptual_clusters"] = []
        duplicates["unapproved_perceptual_clusters"] = []
        duplicates["approved_perceptual_clusters"] = []
        duplicates["perceptual_errors"] = {}
    findings = [flag_product(product, duplicate_ids, duplicate_srcs, perceptual_duplicate_ids) for product in products]
    summary = build_summary(products, findings, duplicates)

    write_json(Path(args.json_output), summary, findings, duplicates)
    write_csv(Path(args.csv_output), findings)
    write_markdown(Path(args.md_output), summary, findings)
    write_html(Path(args.html_output), summary, findings, args.html_max_cards)

    print(json.dumps(summary, ensure_ascii=False, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
