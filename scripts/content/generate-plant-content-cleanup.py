#!/usr/bin/env python3
"""Generate clean product copy for every product currently in PLANTAS.

The generated copy removes internal review language from public product
descriptions and separates live plants from accessories and growing inputs.
It emits a manifest compatible with scripts/php/apply-product-content-manifest.php.
"""

from __future__ import annotations

import argparse
import html
import importlib.util
import json
import re
import sys
import unicodedata
from pathlib import Path
from typing import Any, Dict, List


ROOT = Path(__file__).resolve().parents[2]
DEFAULT_INPUT = Path("/private/tmp/vivero-products-all.tsv")
DEFAULT_OUTPUT = ROOT / "data" / "content-manifests" / "plant-content-cleanup-all.json"
IMAGE_SCRIPT = ROOT / "scripts" / "images" / "download-wikimedia-plant-images.py"


def load_image_classifier():
    spec = importlib.util.spec_from_file_location("plant_image_classifier", IMAGE_SCRIPT)
    module = importlib.util.module_from_spec(spec)
    sys.modules["plant_image_classifier"] = module
    assert spec and spec.loader
    spec.loader.exec_module(module)
    return module.classify


def strip_accents(value: str) -> str:
    normalized = unicodedata.normalize("NFKD", str(value or ""))
    return "".join(ch for ch in normalized if not unicodedata.combining(ch))


def normalize(value: str) -> str:
    return re.sub(r"\s+", " ", strip_accents(value).lower()).strip()


def esc(value: str) -> str:
    return html.escape(str(value or ""), quote=False)


def p(text: str) -> str:
    return f"<p>{esc(text)}</p>"


def ul(items: List[str]) -> str:
    return "<ul>" + "".join(f"<li>{esc(item)}</li>" for item in items if item) + "</ul>"


def attr(name: str, value: str) -> Dict[str, Any]:
    return {"name": name, "visible": True, "variation": False, "options": [str(value)]}


def meta(key: str, value: Any) -> Dict[str, Any]:
    return {"key": key, "value": value}


def read_products(path: Path) -> List[dict]:
    rows: List[dict] = []
    with path.open("r", encoding="utf-8") as fh:
        for raw in fh:
            parts = raw.rstrip("\n").split("\t")
            if len(parts) < 6 or parts[3] != "PLANTAS":
                continue
            rows.append(
                {
                    "id": int(parts[0]),
                    "name": parts[1],
                    "sku": parts[2],
                    "categories": parts[3],
                    "image_id": parts[4],
                    "image_url": parts[5],
                }
            )
    return rows


def presentation(product: dict) -> str:
    text = f"{product['name']} {product['sku']}"
    sku = strip_accents(product["sku"]).upper()
    sku_digits = re.search(r"([A-Z]+)(\d+)", sku)
    if sku_digits:
        prefix, digits = sku_digits.groups()
        if prefix.startswith(("FIBJAR", "FIBPIR", "FIB")) and len(digits) == 6:
            return f"{int(digits[0:2])} x {int(digits[2:4])} x {int(digits[4:6])} cm"
        if prefix.startswith(("FIBCIL", "FIBCON", "FIBINC", "FIBCALI", "FIBSALTE")) and len(digits) in {2, 3}:
            return f"{int(digits)} cm"
        if prefix.startswith(("FIBCUB", "FUBCUB", "FIBMAL", "FIBESF", "MENSU", "ARO", "PORTAM", "PIE", "TPREP", "TURBA", "GUANO", "SUST", "HUM", "CHIP")) and len(digits) <= 3:
            return f"{int(digits)} litros"
        if prefix.startswith(("PIENORD", "PIENOR", "PIEBAJO", "PIEALTO", "PORTAM")) and len(digits) == 4:
            return f"{int(digits[0:2])} x {int(digits[2:4])} cm"
        if prefix.startswith("PIEPOR") and len(digits) == 3:
            return f"{int(digits[0])} x {int(digits[1:3])} cm"
        if prefix.startswith(("HIERRO", "NFOSKA", "TRIPLE")):
            return f"{int(digits)} gr"
    match = re.search(r"(\d+(?:[,.]\d+)?)\s*(litros?|lts?|l\b|cm|cc|grs?|g\b)", text, flags=re.I)
    if not match:
        sku_match = re.search(r"(\d+)\s*l?$", product["sku"], flags=re.I)
        return f"{sku_match.group(1)} litros" if sku_match else "presentación indicada"
    value = match.group(1).replace(",", ".")
    unit = match.group(2).lower()
    if unit.startswith("l"):
        unit = "litros"
    elif unit == "g":
        unit = "gr"
    return f"{value} {unit}"


def accessory_label(product: dict) -> str:
    text = normalize(f"{product['name']} {product['sku']}")
    if "pienor" in text or "pie" in text:
        return "pie o soporte decorativo para macetas"
    if "aro" in text:
        return "aro o estructura de soporte para plantas"
    if "porta" in text:
        return "porta maceta"
    if "fib" in text or "fub" in text:
        return "contenedor liviano para plantación"
    return "accesorio de vivero"


def input_label(product: dict) -> str:
    text = normalize(f"{product['name']} {product['sku']}")
    if "turba" in text:
        return "turba"
    if "guano" in text:
        return "guano"
    if "sust" in text:
        return "sustrato"
    if "hum" in text:
        return "humus"
    if "chip" in text:
        return "chip decorativo o cobertura"
    if "perlita" in text:
        return "perlita expandida"
    if "hierro" in text:
        return "aporte de hierro"
    if "nfoska" in text or "triple" in text:
        return "fertilizante"
    return "insumo de cultivo"


def input_article(label: str) -> str:
    feminine = {"turba", "perlita expandida"}
    return "una" if label in feminine else "un"


INTERIOR_TERMS = {
    "Sansevieria", "Sansevieria zeylanica", "Palmera chamaedorea", "Croton", "Syngonium",
    "Dracena", "Dracena Warneckii", "Dieffenbachia", "Peperomia", "Calathea", "Ficus elastica",
    "Ficus benjamina", "Monstera", "Aglaonema", "Anthurium", "Yuca", "Zamioculca",
    "Spathiphyllum", "Pandanus", "Philodendron Imperial", "Philodendron trepador",
    "Helecho", "Areca", "Raphis", "Aspidistra", "Aloe", "Helecho nido de ave",
}

TREE_TERMS = {
    "Brachychiton", "Aguaribay", "Eucalipto cinerea", "Liquidambar", "Prunus", "Morera",
    "Abedul", "Jacarandá", "Tilo", "Fresno rojo", "Fresno americano", "Crespón", "Olivo",
}

FLOWER_TERMS = {
    "Azalea", "Bougainvillea", "Rosa banksiae", "Callistemon", "Caña de Indias",
    "Jazmín lluvia de oro", "Bignonia roja", "Bignonia jasminoides", "Bignonia rosa",
    "Glicina", "Jazmín del cielo", "Jazmín de Madagascar", "Jazmín estrella", "Gardenia",
    "Jazmín azórico",
}


def plant_profile(common_name: str) -> Dict[str, str]:
    if common_name in TREE_TERMS:
        return {
            "ambiente": "exterior",
            "luz": "sol directo o media sombra luminosa",
            "riego": "riego de establecimiento y luego moderado",
            "uso": "jardín, vereda, patio amplio o proyecto paisajístico",
            "cuidado": "medio",
        }
    if common_name in FLOWER_TERMS:
        return {
            "ambiente": "exterior luminoso",
            "luz": "sol suave o media sombra según la estación",
            "riego": "regular, sin encharcar",
            "uso": "canteros, cercos, pérgolas, patios o macetones",
            "cuidado": "medio",
        }
    if common_name in INTERIOR_TERMS:
        return {
            "ambiente": "interior luminoso o galería protegida",
            "luz": "luz natural brillante sin sol fuerte permanente",
            "riego": "moderado, dejando orear el sustrato",
            "uso": "living, oficina, galería, entrada o patio reparado",
            "cuidado": "fácil",
        }
    return {
        "ambiente": "jardín, patio o maceta",
        "luz": "buena luz natural",
        "riego": "moderado, ajustado a temperatura y drenaje",
        "uso": "espacios verdes, patios o composiciones de vivero",
        "cuidado": "fácil a medio",
    }


def clean_name(product: dict, classification: dict) -> str:
    name = product["name"]
    if classification["entity_type"] == "live_plant" and classification.get("common_name"):
        return classification["common_name"]
    name = re.sub(r"\s*-\s*Producto de Vivero\s*$", "", name).strip()
    if classification["entity_type"] != "live_plant":
        name = re.sub(r"^Planta\s+", "", name, flags=re.I).strip()
    return name


def title_presentation(value: str) -> str:
    value = re.sub(r"\blitros\b", "Litros", value)
    value = re.sub(r"\bgr\b", "g", value)
    return value


def product_title(display: str, pres: str) -> str:
    pretty = title_presentation(pres)
    if normalize(pretty) in normalize(display):
        return display
    return f"{display} {pretty}".strip()


def accessory_title(product: dict, label: str, pres: str) -> str:
    text = normalize(f"{product['name']} {product['sku']}")
    pretty = title_presentation(pres)
    if "fibjar" in text:
        return f"Jardinera Fibjar {pretty}"
    if "fibcub" in text or "fubcub" in text:
        return f"Contenedor Fibcub {pretty}"
    if "fibcil" in text:
        return f"Maceta cilíndrica Fibcil {pretty}"
    if "fibpir" in text:
        return f"Maceta piramidal Fibpir {pretty}"
    if "fibmal" in text:
        return f"Maceta Malva {pretty}"
    if "aro" in text:
        return f"Aro soporte para plantas {pretty}"
    if "porta" in text:
        return f"Porta maceta {pretty}"
    if "pie" in text:
        return f"Soporte para maceta {pretty}"
    return product_title(clean_name(product, {"entity_type": "accessory"}), pres)


def build_live_plant(product: dict, classification: dict) -> Dict[str, Any]:
    display = clean_name(product, classification)
    sci = classification.get("scientific_name", "")
    pres = presentation(product)
    profile = plant_profile(display)
    public_name = product_title(display, pres)
    title = f"{display} en {pres}"
    short = f"{title}: planta seleccionada para {profile['uso']}, disponible para compra online y entrega coordinada en Mendoza."
    species_line = f"Nombre botánico de referencia: {sci}." if sci else f"Producto publicado como {display}."
    description = "\n".join(
        [
            p(f"{title} es una opción de vivero para sumar verde real a {profile['uso']}. Se entrega en {pres}, con stock visible y compra directa desde la tienda online."),
            p(f"{species_line} La selección prioriza ejemplares aptos para adaptación en {profile['ambiente']}."),
            "<h3>Cuidados recomendados</h3>",
            ul(
                [
                    f"Luz: {profile['luz']}.",
                    f"Riego: {profile['riego']}.",
                    "Trasplante: usar maceta con buen drenaje o suelo preparado.",
                    "Adaptación: evitar cambios bruscos de sol, viento o temperatura durante los primeros días.",
                ]
            ),
            p("Si necesitás confirmar ubicación ideal, tamaño de maceta o combinación con otras especies, consultanos antes de comprar."),
        ]
    )
    return {
        "short_description": short,
        "product_name": public_name,
        "description": description,
        "attributes": [
            attr("Presentación", pres),
            attr("Ambiente", profile["ambiente"]),
            attr("Luz", profile["luz"]),
            attr("Riego", profile["riego"]),
            attr("Cuidado", profile["cuidado"]),
        ],
        "meta_data": [
            meta("_loscocos_luminosidad", profile["luz"]),
            meta("_loscocos_riego", profile["riego"]),
            meta("_loscocos_nivel_cuidado", profile["cuidado"]),
            meta("_loscocos_nombre_botanico", sci),
        ],
        "tags": ["Plantas", display, pres],
        "category_hint": "plantas",
        "source_policy": "Contenido curado por reglas de catálogo; sin frases internas de revisión ni notas operativas.",
    }


def build_accessory(product: dict) -> Dict[str, Any]:
    label = accessory_label(product)
    pres = presentation(product)
    name = clean_name(product, {"entity_type": "accessory"})
    public_name = accessory_title(product, label, pres)
    short = f"{name}: {label} en {pres}, pensado para presentar y organizar plantas en interior, patio o jardín."
    description = "\n".join(
        [
            p(f"{name} corresponde a la línea de {label}. Sirve para acompañar plantas, ordenar el espacio y mejorar la presentación de macetas o composiciones de vivero."),
            "<h3>Uso recomendado</h3>",
            ul(
                [
                    f"Presentación o medida: {pres}.",
                    "Combinar con plantas y macetas de tamaño compatible.",
                    "Ubicar sobre una superficie estable y revisar drenaje cuando se use con contenedores.",
                    "Apto para patios, balcones, galerías e interiores según el uso elegido.",
                ]
            ),
            p("Si necesitás armar un conjunto completo, podemos ayudarte a combinar soporte, maceta y planta."),
        ]
    )
    return {
        "short_description": short,
        "product_name": public_name,
        "description": description,
        "attributes": [
            attr("Tipo", label),
            attr("Presentación", pres),
            attr("Uso", "macetas, plantas y ambientación"),
        ],
        "meta_data": [
            meta("_loscocos_tipo_producto", "accesorio"),
            meta("_loscocos_uso_recomendado", "presentación de plantas"),
        ],
        "tags": ["Accesorios de vivero", "Macetas", pres],
        "category_hint": "plantas",
        "source_policy": "Contenido corregido para evitar tratar accesorios como especies vegetales.",
    }


def build_input(product: dict) -> Dict[str, Any]:
    label = input_label(product)
    article = input_article(label)
    pres = presentation(product)
    name = clean_name(product, {"entity_type": "input"})
    public_name = product_title(name, pres)
    short = f"{name}: {label} en {pres}, para preparar, mejorar o mantener sustratos de plantas."
    description = "\n".join(
        [
            p(f"{name} es {article} {label} de vivero para tareas de preparación, mantenimiento o mejora del sustrato. La presentación {pres} facilita su uso en macetas, canteros o trabajos puntuales."),
            "<h3>Aplicación orientativa</h3>",
            ul(
                [
                    "Usar en mezclas, cobertura o mantenimiento según el tipo de insumo.",
                    "Evitar excesos y ajustar la cantidad al tamaño de maceta o cantero.",
                    "Conservar en lugar seco y protegido.",
                    "Consultar compatibilidad si se combina con fertilizantes u otros productos.",
                ]
            ),
            p("Este producto se compra online y se coordina para retiro o entrega junto con plantas, macetas y otros insumos."),
        ]
    )
    return {
        "short_description": short,
        "product_name": public_name,
        "description": description,
        "attributes": [
            attr("Tipo", label),
            attr("Presentación", pres),
            attr("Uso", "sustratos y mantenimiento de plantas"),
        ],
        "meta_data": [
            meta("_loscocos_tipo_producto", "insumo"),
            meta("_loscocos_uso_recomendado", "sustrato"),
        ],
        "tags": ["Insumos de cultivo", label, pres],
        "category_hint": "plantas",
        "source_policy": "Contenido corregido para describir insumos sin tratarlos como especies vegetales.",
    }


def build_update(product: dict, classification: dict) -> Dict[str, Any]:
    entity = classification["entity_type"]
    if entity == "live_plant":
        generated = build_live_plant(product, classification)
    elif entity == "substrate_or_input":
        generated = build_input(product)
    else:
        generated = build_accessory(product)

    generated.update(
        {
            "product_id": product["id"],
            "sku": product["sku"],
            "name": product["name"],
            "product_kind": entity,
            "review_status": "Content Cleanup 2026-05-17",
        }
    )
    return generated


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--input", type=Path, default=DEFAULT_INPUT)
    parser.add_argument("--output", type=Path, default=DEFAULT_OUTPUT)
    args = parser.parse_args()

    classify = load_image_classifier()
    products = read_products(args.input)
    updates = [build_update(product, classify(product)) for product in products]
    payload = {
        "mode": "plant_content_cleanup",
        "count": len(updates),
        "product_ids": [update["product_id"] for update in updates],
        "updates": updates,
    }
    args.output.parent.mkdir(parents=True, exist_ok=True)
    args.output.write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")
    kinds: Dict[str, int] = {}
    for update in updates:
        kinds[update["product_kind"]] = kinds.get(update["product_kind"], 0) + 1
    print(json.dumps({"output": str(args.output), "count": len(updates), "kinds": kinds}, ensure_ascii=False, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
