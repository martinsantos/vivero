#!/usr/bin/env python3
"""Download free/licensed representative plant images from Wikimedia Commons.

This covers the live-plant portion of the catalog without using paid stock,
competitor photos, or unlicensed provider images. The output is an auditable
apply manifest compatible with scripts/php/apply-curated-product-images.php.
"""

from __future__ import annotations

import argparse
import csv
import io
import json
import re
import time
import unicodedata
import urllib.parse
import urllib.error
import urllib.request
from dataclasses import dataclass
from pathlib import Path
from typing import Dict, Iterable, List, Optional

from PIL import Image, ImageOps


ROOT = Path(__file__).resolve().parents[2]
DEFAULT_INPUT = Path("/private/tmp/vivero-products-all.tsv")
DEFAULT_OUTPUT_DIR = ROOT / "assets" / "product-images" / "plants-free-source"
DEFAULT_MANIFEST_DIR = ROOT / "assets" / "product-images" / "source-manifest"
USER_AGENT = "ViveroLosCocosCatalogImageBot/1.0 (free image curation)"


@dataclass(frozen=True)
class Rule:
    pattern: str
    common_name: str
    scientific_name: str
    query: str
    confidence: float = 0.78


RULES: List[Rule] = [
    Rule(r"^SANSETRI", "Sansevieria", "Dracaena trifasciata", "Dracaena trifasciata potted plant", 0.9),
    Rule(r"^SANSEZENA", "Sansevieria zeylanica", "Dracaena zeylanica", "Sansevieria zeylanica plant", 0.82),
    Rule(r"^CHAMA", "Palmera chamaedorea", "Chamaedorea elegans", "Chamaedorea elegans potted plant", 0.9),
    Rule(r"^CRO", "Croton", "Codiaeum variegatum", "Codiaeum variegatum croton plant", 0.88),
    Rule(r"^SYN", "Syngonium", "Syngonium podophyllum", "Syngonium podophyllum houseplant", 0.88),
    Rule(r"^DRAC", "Dracena", "Dracaena fragrans", "Dracaena fragrans houseplant", 0.86),
    Rule(r"^ASPL", "Helecho nido de ave", "Asplenium nidus", "Asplenium nidus houseplant", 0.86),
    Rule(r"^DIEF", "Dieffenbachia", "Dieffenbachia seguine", "Dieffenbachia seguine houseplant", 0.84),
    Rule(r"^PEPE", "Peperomia", "Peperomia obtusifolia", "Peperomia obtusifolia potted plant", 0.82),
    Rule(r"^PERUV", "Calathea", "Goeppertia makoyana", "Goeppertia makoyana houseplant", 0.62),
    Rule(r"^WARNE", "Dracena Warneckii", "Dracaena deremensis Warneckii", "Dracaena fragrans Warneckii plant", 0.78),
    Rule(r"^FICBEN", "Ficus benjamina", "Ficus benjamina", "Ficus benjamina potted plant", 0.9),
    Rule(r"^FICVAR", "Ficus variegado", "Ficus elastica variegata", "Ficus elastica variegata plant", 0.84),
    Rule(r"^FICBL", "Ficus elastica", "Ficus elastica", "Ficus elastica houseplant", 0.82),
    Rule(r"^FICN", "Ficus elastica", "Ficus elastica", "Ficus elastica houseplant", 0.76),
    Rule(r"^GOMERO", "Gomero", "Ficus elastica", "Ficus elastica rubber plant", 0.9),
    Rule(r"^MONS|^MONSTA", "Monstera", "Monstera deliciosa", "Monstera deliciosa potted plant", 0.92),
    Rule(r"^AGLA", "Aglaonema", "Aglaonema commutatum", "Aglaonema plant", 0.78),
    Rule(r"^ANTHU", "Anthurium", "Anthurium andraeanum", "Anthurium andraeanum plant", 0.88),
    Rule(r"^YUCA", "Yuca", "Yucca gigantea", "Yucca gigantea houseplant", 0.86),
    Rule(r"^CALAMAC", "Calathea makoyana", "Goeppertia makoyana", "Goeppertia makoyana plant", 0.82),
    Rule(r"^ZAMI", "Zamioculca", "Zamioculcas zamiifolia", "Zamioculcas zamiifolia plant", 0.9),
    Rule(r"^SPAT", "Spathiphyllum", "Spathiphyllum wallisii", "Spathiphyllum wallisii plant", 0.88),
    Rule(r"^PANDU", "Pandanus", "Pandanus veitchii", "Pandanus veitchii plant", 0.76),
    Rule(r"^PHILIMP", "Philodendron Imperial", "Philodendron erubescens", "Philodendron imperial green plant", 0.68),
    Rule(r"^PHILMIS", "Philodendron trepador", "Philodendron hederaceum", "Philodendron hederaceum plant", 0.82),
    Rule(r"^HELE", "Helecho", "Nephrolepis exaltata", "Nephrolepis exaltata fern", 0.88),
    Rule(r"^ARECA", "Areca", "Dypsis lutescens", "Dypsis lutescens palm", 0.9),
    Rule(r"^ARA", "Araucaria", "Araucaria heterophylla", "Norfolk Island pine potted plant", 0.78),
    Rule(r"^PHOROB", "Phormium", "Phormium tenax", "Phormium tenax ornamental plant", 0.82),
    Rule(r"^ALO", "Aloe", "Aloe vera", "Aloe vera plant", 0.9),
    Rule(r"^ASPI", "Aspidistra", "Aspidistra elatior", "Aspidistra elatior plant", 0.9),
    Rule(r"^RAPHIS", "Raphis", "Rhapis excelsa", "Rhapis excelsa palm", 0.92),
    Rule(r"^NANFIREP", "Nandina Firepower", "Nandina domestica", "Nandina domestica firepower", 0.78),
    Rule(r"^NANDOM", "Nandina", "Nandina domestica", "Nandina domestica shrub", 0.88),
    Rule(r"^EVO", "Evónimo", "Euonymus japonicus", "Euonymus japonicus shrub", 0.82),
    Rule(r"^AZA", "Azalea", "Rhododendron simsii", "Rhododendron simsii azalea", 0.82),
    Rule(r"^ABELG", "Abelia", "Abelia grandiflora", "Abelia grandiflora shrub", 0.86),
    Rule(r"^LAU", "Laurel", "Laurus nobilis", "Laurus nobilis laurel shrub", 0.84),
    Rule(r"^BUX", "Buxus", "Buxus sempervirens", "Buxus sempervirens hedge plant", 0.86),
    Rule(r"^COPRO", "Coprosma", "Coprosma repens", "Coprosma repens plant", 0.82),
    Rule(r"^EQUIHYE", "Cola de caballo", "Equisetum hyemale", "Equisetum hyemale plant", 0.88),
    Rule(r"^EUGEMYR", "Eugenia", "Syzygium paniculatum", "Eugenia myrtifolia plant", 0.68),
    Rule(r"^OLETEX", "Laurel de flor", "Nerium oleander", "Nerium oleander shrub", 0.82),
    Rule(r"^DODO", "Dodonaea", "Dodonaea viscosa", "Dodonaea viscosa shrub", 0.86),
    Rule(r"^TEUCRI", "Teucrium", "Teucrium fruticans", "Teucrium fruticans plant", 0.82),
    Rule(r"^BOUGAN", "Bougainvillea", "Bougainvillea glabra", "Bougainvillea glabra plant", 0.9),
    Rule(r"^ROSABANK", "Rosa banksiae", "Rosa banksiae", "Rosa banksiae climbing rose", 0.86),
    Rule(r"^THUJA", "Thuja", "Thuja occidentalis", "Thuja occidentalis shrub", 0.86),
    Rule(r"^CALLIS", "Callistemon", "Callistemon citrinus", "Callistemon citrinus shrub", 0.9),
    Rule(r"^GRATA", "Grevillea", "Grevillea rosmarinifolia", "Grevillea rosmarinifolia plant", 0.6),
    Rule(r"^STRENICO", "Strelitzia nicolai", "Strelitzia nicolai", "Strelitzia nicolai plant", 0.9),
    Rule(r"^CAÑATAC|^CANATAC", "Caña de Indias", "Canna indica", "Canna indica plant", 0.82),
    Rule(r"^FOR", "Formio", "Phormium tenax", "Phormium tenax ornamental plant", 0.78),
    Rule(r"^BRACHI", "Brachychiton", "Brachychiton populneus", "Brachychiton populneus tree", 0.78),
    Rule(r"^AGUA", "Aguaribay", "Schinus molle", "Schinus molle tree", 0.78),
    Rule(r"^EUCACIN", "Eucalipto cinerea", "Eucalyptus cinerea", "Eucalyptus cinerea tree", 0.84),
    Rule(r"^LIQUI", "Liquidambar", "Liquidambar styraciflua", "Liquidambar styraciflua tree", 0.9),
    Rule(r"^PRUN", "Prunus", "Prunus cerasifera", "Prunus cerasifera tree", 0.78),
    Rule(r"^MORAH", "Morera", "Morus alba", "Morus alba leaves tree", 0.7),
    Rule(r"^ARABIA", "Aralia", "Fatsia japonica", "Fatsia japonica plant", 0.56),
    Rule(r"^ABEDUL", "Abedul", "Betula pendula", "Betula pendula tree", 0.9),
    Rule(r"^ACACONS", "Acacia", "Acacia cognata", "Acacia cognata plant", 0.58),
    Rule(r"^JACA", "Jacarandá", "Jacaranda mimosifolia", "Jacaranda mimosifolia flowers tree", 0.86),
    Rule(r"^TILO", "Tilo", "Tilia cordata", "Tilia cordata tree", 0.86),
    Rule(r"^FRESROJ", "Fresno rojo", "Fraxinus pennsylvanica", "Fraxinus pennsylvanica tree", 0.72),
    Rule(r"^FRESAME", "Fresno americano", "Fraxinus americana", "Fraxinus americana tree", 0.72),
    Rule(r"^CRESPON", "Crespón", "Lagerstroemia indica", "Lagerstroemia indica tree", 0.9),
    Rule(r"^OLIVO", "Olivo", "Olea europaea", "Olea europaea olive tree", 0.92),
    Rule(r"^JAZLLUV", "Jazmín lluvia de oro", "Jasminum mesnyi", "Jasminum mesnyi yellow jasmine", 0.72),
    Rule(r"^BIGROJ", "Bignonia roja", "Campsis radicans", "Campsis radicans red trumpet vine", 0.74),
    Rule(r"^BIGJAS", "Bignonia jasminoides", "Pandorea jasminoides", "Pandorea jasminoides vine", 0.74),
    Rule(r"^BIGROS", "Bignonia rosa", "Podranea ricasoliana", "Podranea ricasoliana vine", 0.72),
    Rule(r"^GLICI", "Glicina", "Wisteria sinensis", "Wisteria sinensis vine", 0.9),
    Rule(r"^JAZPLU", "Jazmín del cielo", "Plumbago auriculata", "Plumbago auriculata plant", 0.72),
    Rule(r"^JAZMAD", "Jazmín de Madagascar", "Stephanotis floribunda", "Stephanotis floribunda plant", 0.74),
    Rule(r"^JAZPER", "Jazmín estrella", "Trachelospermum jasminoides", "Trachelospermum jasminoides plant", 0.8),
    Rule(r"^JAZDIA", "Gardenia", "Gardenia jasminoides", "Gardenia jasminoides plant", 0.68),
    Rule(r"^JAZAZO", "Jazmín azórico", "Jasminum azoricum", "Jasminum azoricum plant", 0.78),
]

NON_PLANT_PREFIXES = (
    "FIB",
    "FUB",
    "PIENOR",
    "MENSU",
    "ARO",
    "PORTAM",
    "PIE",
    "TPREP",
    "TURBA",
    "GUANO",
    "SUST",
    "HUM",
    "CHIP",
    "PERLITA",
    "HIERRO",
    "NFOSKA",
    "TRIPLE",
)

ALLOWED_LICENSE_HINTS = (
    "cc0",
    "cc by",
    "cc-by",
    "public domain",
    "gfdl",
    "attribution",
    "free",
)

BAD_TITLE_HINTS = (
    "map",
    "range",
    "diagram",
    "drawing",
    "illustration",
    "icon",
    "logo",
    "seed",
    "fruit",
    "herbarium",
)


def normalize(value: str) -> str:
    value = str(value or "").replace("&#215;", "x")
    value = unicodedata.normalize("NFKD", value)
    value = "".join(ch for ch in value if not unicodedata.combining(ch))
    return re.sub(r"\s+", " ", value).strip().lower()


def slugify(value: str) -> str:
    return re.sub(r"[^a-z0-9]+", "-", normalize(value)).strip("-")[:90] or "plant"


def read_products(path: Path) -> List[dict]:
    rows: List[dict] = []
    with path.open("r", encoding="utf-8") as fh:
        for raw in fh:
            parts = raw.rstrip("\n").split("\t")
            if len(parts) < 6 or parts[3] != "PLANTAS":
                continue
            rows.append(
                {
                    "product_id": int(parts[0]),
                    "name": parts[1],
                    "sku": parts[2],
                    "categories": parts[3],
                    "image_id": parts[4],
                    "image_url": parts[5],
                }
            )
    return rows


def classify(product: dict) -> dict:
    sku = normalize(product["sku"]).upper().replace("Ñ", "N")
    if sku.startswith(NON_PLANT_PREFIXES):
        kind = "substrate_or_input" if sku.startswith(("TPREP", "TURBA", "GUANO", "SUST", "HUM", "CHIP", "PERLITA", "HIERRO", "NFOSKA", "TRIPLE")) else "misclassified_container_or_accessory"
        return {
            "entity_type": kind,
            "common_name": "",
            "scientific_name": "",
            "query": "",
            "confidence_score": 0.96,
            "recommended_action": "do_not_assign_plant_photo",
        }

    for rule in RULES:
        if re.search(rule.pattern, sku, re.IGNORECASE):
            return {
                "entity_type": "live_plant",
                "common_name": rule.common_name,
                "scientific_name": rule.scientific_name,
                "query": rule.query,
                "confidence_score": rule.confidence,
                "recommended_action": "download_free_wikimedia_image",
            }

    return {
        "entity_type": "needs_manual_species_review",
        "common_name": "",
        "scientific_name": "",
        "query": "",
        "confidence_score": 0.1,
        "recommended_action": "manual_review_before_image_assignment",
    }


def request_json(url: str, retries: int = 5) -> dict:
    last_error: Exception | None = None
    for attempt in range(retries):
        req = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
        try:
            with urllib.request.urlopen(req, timeout=45) as response:
                return json.load(response)
        except urllib.error.HTTPError as exc:
            last_error = exc
            if exc.code not in {429, 500, 502, 503, 504}:
                raise
            time.sleep(5 + attempt * 8)
        except urllib.error.URLError as exc:
            last_error = exc
            time.sleep(3 + attempt * 5)
    raise RuntimeError(f"metadata request failed after retries: {url}") from last_error


def clean_download_url(url: str) -> str:
    parsed = urllib.parse.urlsplit(url)
    return urllib.parse.urlunsplit((parsed.scheme, parsed.netloc, parsed.path, "", ""))


def request_bytes(url: str, retries: int = 4) -> bytes:
    last_error: Exception | None = None
    clean_url = clean_download_url(url)
    for attempt in range(retries):
        req = urllib.request.Request(clean_url, headers={"User-Agent": USER_AGENT})
        try:
            with urllib.request.urlopen(req, timeout=60) as response:
                return response.read()
        except urllib.error.HTTPError as exc:
            last_error = exc
            if exc.code not in {429, 500, 502, 503, 504}:
                raise
            time.sleep(4 + attempt * 6)
        except urllib.error.URLError as exc:
            last_error = exc
            time.sleep(3 + attempt * 4)
    raise RuntimeError(f"download failed after retries: {clean_url}") from last_error


def ext(meta: dict, key: str) -> str:
    return str(meta.get(key, {}).get("value", "") or "")


def license_allowed(meta: dict) -> bool:
    text = normalize(" ".join([ext(meta, "LicenseShortName"), ext(meta, "UsageTerms"), ext(meta, "License")]))
    return any(hint in text for hint in ALLOWED_LICENSE_HINTS)


def score_candidate(page: dict, query: str) -> float:
    title = normalize(page.get("title", ""))
    info = (page.get("imageinfo") or [{}])[0]
    meta = info.get("extmetadata") or {}
    categories = normalize(ext(meta, "Categories"))
    score = 0.0
    for term in normalize(query).split():
        if len(term) > 3 and term in title:
            score += 3.0
        if len(term) > 3 and term in categories:
            score += 1.5
    if "potted" in title or "plant" in title or "shrub" in title or "tree" in title:
        score += 2.0
    if not license_allowed(meta):
        score -= 100.0
    if not str(info.get("mime", "")).startswith("image/"):
        score -= 100.0
    for bad in BAD_TITLE_HINTS:
        if bad in title:
            score -= 4.0
    width = int(info.get("width") or 0)
    height = int(info.get("height") or 0)
    if min(width, height) >= 900:
        score += 2.0
    elif min(width, height) < 500:
        score -= 5.0
    return score


def search_commons(query: str, limit: int = 15) -> Optional[dict]:
    params = {
        "action": "query",
        "generator": "search",
        "gsrsearch": query,
        "gsrnamespace": "6",
        "gsrlimit": str(limit),
        "prop": "imageinfo",
        "iiprop": "url|extmetadata|mime|size",
        "iiurlwidth": "1600",
        "format": "json",
    }
    url = "https://commons.wikimedia.org/w/api.php?" + urllib.parse.urlencode(params)
    data = request_json(url)
    pages = list((data.get("query", {}).get("pages") or {}).values())
    if not pages:
        return None
    pages.sort(key=lambda page: score_candidate(page, query), reverse=True)
    best = pages[0]
    if score_candidate(best, query) < 0:
        return None
    return best


def normalize_image(raw: bytes, target: Path) -> None:
    image = Image.open(io.BytesIO(raw))
    image = ImageOps.exif_transpose(image).convert("RGB")
    fitted = ImageOps.fit(image, (1200, 1200), method=Image.Resampling.LANCZOS, centering=(0.5, 0.5))
    fitted.save(target, "WEBP", quality=88, method=6)


def candidate_metadata(page: dict) -> dict:
    info = (page.get("imageinfo") or [{}])[0]
    meta = info.get("extmetadata") or {}
    return {
        "commons_title": page.get("title", ""),
        "source_url": info.get("descriptionurl", ""),
        "download_url": info.get("url", ""),
        "thumbnail_url": info.get("thumburl", ""),
        "license": ext(meta, "LicenseShortName") or ext(meta, "UsageTerms"),
        "author": re.sub(r"<[^>]+>", "", ext(meta, "Artist")),
        "credit": re.sub(r"<[^>]+>", "", ext(meta, "Credit")),
        "width": info.get("width"),
        "height": info.get("height"),
    }


def write_csv(path: Path, rows: Iterable[dict]) -> None:
    rows = list(rows)
    fields = [
        "product_id",
        "sku",
        "name",
        "entity_type",
        "common_name",
        "scientific_name",
        "confidence_score",
        "recommended_action",
        "file",
        "source_url",
        "license",
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
    parser.add_argument("--manifest-dir", type=Path, default=DEFAULT_MANIFEST_DIR)
    parser.add_argument("--sleep", type=float, default=0.25)
    args = parser.parse_args()

    args.output_dir.mkdir(parents=True, exist_ok=True)
    args.manifest_dir.mkdir(parents=True, exist_ok=True)

    rows = []
    products = read_products(args.input)
    classified = [{**product, **classify(product)} for product in products]

    assets_by_query: Dict[str, dict] = {}
    for query in sorted({row["query"] for row in classified if row["entity_type"] == "live_plant" and row["query"]}):
        asset_key = slugify(query)
        target = args.output_dir / f"{asset_key}.webp"
        if not target.exists():
            try:
                page = search_commons(query)
            except Exception as exc:
                assets_by_query[query] = {"status": "failed", "reason": str(exc)}
                time.sleep(args.sleep * 4)
                continue
            if not page:
                assets_by_query[query] = {"status": "failed", "reason": "no_licensed_candidate"}
                continue
            metadata = candidate_metadata(page)
            try:
                raw = request_bytes(metadata.get("thumbnail_url") or metadata["download_url"])
                normalize_image(raw, target)
            except Exception as exc:
                assets_by_query[query] = {"status": "failed", "reason": str(exc)}
                time.sleep(args.sleep * 4)
                continue
            time.sleep(args.sleep)
        else:
            metadata = {
                "commons_title": "",
                "source_url": "",
                "download_url": "",
                "thumbnail_url": "",
                "license": "metadata previously downloaded; rerun with empty output dir to refresh",
                "author": "",
                "credit": "",
                "width": 1200,
                "height": 1200,
            }

        assets_by_query[query] = {
            "status": "ready",
            "file": target.name,
            **metadata,
        }
        time.sleep(args.sleep)

    apply_rows = []
    for row in classified:
        asset = assets_by_query.get(row["query"], {})
        merged = {**row, **asset}
        if row["entity_type"] == "live_plant" and asset.get("status") == "ready":
            merged["alt"] = f"{row['name']} - imagen representativa de {row['common_name']} en Vivero Los Cocos"
            apply_rows.append(
                {
                    "product_id": row["product_id"],
                    "file": asset["file"],
                    "alt": merged["alt"],
                    "source_type": "wikimedia_commons_free_license",
                    "source_url": asset.get("source_url", ""),
                    "license": asset.get("license", ""),
                    "species": row["scientific_name"],
                }
            )
        rows.append(merged)

    plan_path = args.manifest_dir / "plant-image-plan.json"
    csv_path = args.manifest_dir / "plant-image-plan-summary.csv"
    apply_path = args.output_dir / "plants-free-source-apply-manifest.json"
    attribution_path = args.output_dir / "plants-free-source-attribution.json"

    plan_path.write_text(json.dumps(rows, ensure_ascii=False, indent=2), encoding="utf-8")
    apply_path.write_text(json.dumps(apply_rows, ensure_ascii=False, indent=2), encoding="utf-8")
    attribution_path.write_text(json.dumps(assets_by_query, ensure_ascii=False, indent=2), encoding="utf-8")
    write_csv(csv_path, rows)

    counts: Dict[str, int] = {}
    for row in rows:
        counts[row["entity_type"]] = counts.get(row["entity_type"], 0) + 1

    print(
        json.dumps(
            {
                "products": len(products),
                "entity_type_counts": counts,
                "unique_live_plant_queries": len(assets_by_query),
                "downloaded_or_ready_assets": sum(1 for item in assets_by_query.values() if item.get("status") == "ready"),
                "apply_rows": len(apply_rows),
                "plan": str(plan_path),
                "summary": str(csv_path),
                "apply_manifest": str(apply_path),
                "attribution": str(attribution_path),
                "output_dir": str(args.output_dir),
            },
            ensure_ascii=False,
            indent=2,
        )
    )


if __name__ == "__main__":
    main()
