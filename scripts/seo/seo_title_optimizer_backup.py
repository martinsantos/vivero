#!/usr/bin/env python3
"""
SEO Title Optimizer para Vivero Los Cocos
==========================================
Optimiza títulos de productos para mejorar SEO en Argentina
Especializado en plantas, árboles y productos de vivero
"""

import re
import csv
import json
from typing import Dict, List, Tuple, Optional
from dataclasses import dataclass


@dataclass
class PlantInfo:
    """Información de una planta para SEO"""
    scientific_name: str
    common_names: List[str]
    category: str
    features: List[str]
    seo_keywords: List[str]


# Base de conocimiento ENRIQUECIDA de plantas argentinas
PLANT_DATABASE = {
    # Árboles frutales
    "olivo": PlantInfo(
        scientific_name="Olea europaea",
        common_names=["Olivo", "Olivera", "Olivo Europeo"],
        category="Árbol Frutal Mediterráneo",
        features=["perenne", "resistente sequía", "frutos comestibles aceitunas", "follaje plateado", "ideal clima seco"],
        seo_keywords=["olivo mendoza", "arbol olivo jardin", "olivo frutal", "comprar olivo argentina", "olivo aceitunas"]
    ),
    "jaca": PlantInfo(
        scientific_name="Artocarpus heterophyllus",
        common_names=["Jaca", "Yaca", "Árbol de Jack", "Jackfruit"],
        category="Árbol Frutal Tropical Exótico",
        features=["tropical", "frutos gigantes comestibles", "sombra densa", "crecimiento rápido"],
        seo_keywords=["jaca fruta", "arbol jaca argentina", "yaca tropical", "jackfruit mendoza"]
    ),
    
    # Arbustos florales
    "jazmin": PlantInfo(
        scientific_name="Jasminum",
        common_names=["Jazmín", "Jazmin Blanco", "Jazmín Común"],
        category="Arbusto Floral Perfumado",
        features=["flores blancas aromáticas", "trepadora o arbusto", "perenne", "floración primavera-verano", "ideal cercos"],
        seo_keywords=["jazmin mendoza", "jazmin perfumado jardin", "planta jazmin argentina", "jazmin trepador"]
    ),
    "jazlluv": PlantInfo(
        scientific_name="Jasminum nudiflorum",
        common_names=["Jazmín Lluvia de Oro", "Jazmín de Invierno", "Jazmín Amarillo"],
        category="Arbusto Floral de Invierno",
        features=["flores amarillas brillantes", "floracion invernal única", "perfumado intenso", "resistente heladas", "cascada dorada"],
        seo_keywords=["jazmin lluvia oro mendoza", "jazmin amarillo invierno", "jazmin resistente frio", "jazmin perfumado argentina"]
    ),
    "bougan": PlantInfo(
        scientific_name="Bougainvillea",
        common_names=["Bougainvillea", "Buganvilia", "Santa Rita", "Trinitaria"],
        category="Enredadera Floral Espectacular",
        features=["brácteas coloridas todo el año", "muy resistente sequía", "trepadora vigorosa", "colores intensos", "bajo mantenimiento"],
        seo_keywords=["bougainvillea mendoza", "santa rita argentina", "buganvilia trepadora", "planta resistente sol"]
    ),
    
    # Árboles ornamentales
    "abedul": PlantInfo(
        scientific_name="Betula",
        common_names=["Abedul", "Abedul Blanco"],
        category="Árbol Ornamental",
        features=["corteza blanca", "decorativo", "caduco"],
        seo_keywords=["abedul jardin", "arbol abedul", "abedul corteza blanca"]
    ),
    "tilo": PlantInfo(
        scientific_name="Tilia",
        common_names=["Tilo", "Tila"],
        category="Árbol Ornamental",
        features=["sombra", "flores medicinales", "perfumado"],
        seo_keywords=["tilo para jardin", "arbol de tilo", "tilo medicinal"]
    ),
    "liqui": PlantInfo(
        scientific_name="Liquidambar styraciflua",
        common_names=["Liquidámbar", "Árbol del Ámbar"],
        category="Árbol Ornamental",
        features=["colores otoñales", "sombra", "ornamental"],
        seo_keywords=["liquidambar argentina", "arbol liquidambar", "liquidambar jardin"]
    ),
    "eucacin": PlantInfo(
        scientific_name="Eucalyptus cinerea",
        common_names=["Eucalipto Plateado", "Eucalipto Cinéreo"],
        category="Árbol Ornamental",
        features=["hojas plateadas", "aromático", "rápido crecimiento"],
        seo_keywords=["eucalipto plateado", "eucalyptus cinerea", "eucalipto jardin"]
    ),
    
    # Árboles grandes
    "acacia": PlantInfo(
        scientific_name="Acacia",
        common_names=["Acacia", "Acacia de Constantinopla"],
        category="Árbol de Sombra",
        features=["sombra densa", "flores perfumadas", "resistente"],
        seo_keywords=["acacia para jardin", "arbol de acacia", "acacia mendoza"]
    ),
    "acacons": PlantInfo(
        scientific_name="Albizia julibrissin",
        common_names=["Acacia de Constantinopla", "Acacia Rosada"],
        category="Árbol de Sombra",
        features=["flores rosas", "sombra", "ornamental"],
        seo_keywords=["acacia constantinopla", "acacia rosada", "albizia julibrissin"]
    ),
    "arabia": PlantInfo(
        scientific_name="Acacia arabica",
        common_names=["Acacia Arábiga", "Espina de Cristo"],
        category="Árbol Espinoso",
        features=["resistente", "espinoso", "sombra"],
        seo_keywords=["acacia arabiga", "espina de cristo", "acacia espinosa"]
    ),
    
    # Arbustos y trepadoras
    "glici": PlantInfo(
        scientific_name="Wisteria",
        common_names=["Glicina", "Wisteria"],
        category="Trepadora Floral",
        features=["flores colgantes", "perfumada", "ornamental"],
        seo_keywords=["glicina argentina", "wisteria jardin", "glicina trepadora"]
    ),
    "rosa": PlantInfo(
        scientific_name="Rosa",
        common_names=["Rosa", "Rosal"],
        category="Arbusto Floral",
        features=["flores perfumadas", "ornamental", "variedad de colores"],
        seo_keywords=["rosal para jardin", "rosa argentina", "comprar rosas"]
    ),
    
    # Plantas aromáticas y decorativas
    "lau": PlantInfo(
        scientific_name="Laurus nobilis",
        common_names=["Laurel", "Laurel de Cocina"],
        category="Arbusto Aromático",
        features=["aromático", "medicinal", "culinario"],
        seo_keywords=["laurel planta", "laurel aromatico", "laurel cocina"]
    ),
    "thuja": PlantInfo(
        scientific_name="Thuja",
        common_names=["Tuya", "Thuja", "Cedro"],
        category="Conífera Ornamental",
        features=["perenne", "cerco vivo", "ornamental"],
        seo_keywords=["thuja argentina", "tuya jardin", "cerco vivo thuja"]
    ),
    
    # Frutales
    "fres": PlantInfo(
        scientific_name="Fragaria",
        common_names=["Frutilla", "Fresa"],
        category="Frutal Pequeño",
        features=["frutos comestibles", "rastrera", "ornamental"],
        seo_keywords=["planta frutilla", "fresa jardin", "cultivar frutillas"]
    ),
    "prun": PlantInfo(
        scientific_name="Prunus",
        common_names=["Ciruelo", "Cerezo"],
        category="Árbol Frutal",
        features=["frutos comestibles", "flores primaverales", "ornamental"],
        seo_keywords=["ciruelo frutal", "prunus argentina", "arbol de ciruelas"]
    ),
    
    # Plantas decorativas
    "drac": PlantInfo(
        scientific_name="Dracaena",
        common_names=["Dracena", "Palo de Agua"],
        category="Planta de Interior",
        features=["interior", "purificadora", "ornamental"],
        seo_keywords=["dracena interior", "palo de agua", "dracaena jardin"]
    ),
    "for": PlantInfo(
        scientific_name="Forsythia",
        common_names=["Forsitia", "Campanas de Oro"],
        category="Arbusto Floral",
        features=["flores amarillas", "primaveral", "ornamental"],
        seo_keywords=["forsythia argentina", "campanas de oro", "forsitia jardin"]
    ),
}

# Patrones de decodificación de SKUs
SKU_PATTERNS = {
    # Plantas con tamaño en litros
    r"^([A-Za-z]+?)(\d+)l$": lambda m: (m.group(1).lower(), f"{m.group(2)} Litros"),
    # Plantas con variedad y tamaño
    r"^([A-Za-z]+?)([a-z]{2,4})(\d+)l$": lambda m: (m.group(1).lower(), f"{m.group(2).title()} {m.group(3)} Litros"),
}


def decode_sku(sku: str) -> Tuple[Optional[str], Optional[str]]:
    """
    Decodifica un SKU de producto
    
    Returns:
        Tuple de (nombre_base, info_adicional)
    """
    sku_clean = sku.strip().lower()
    
    for pattern, extractor in SKU_PATTERNS.items():
        match = re.match(pattern, sku_clean)
        if match:
            return extractor(match)
    
    return None, None


def find_plant_info(plant_code: str) -> Optional[PlantInfo]:
    """Busca información de la planta en la base de datos"""
    plant_code = plant_code.lower()
    
    # Búsqueda exacta
    if plant_code in PLANT_DATABASE:
        return PLANT_DATABASE[plant_code]
    
    # Búsqueda por coincidencia parcial
    for key, info in PLANT_DATABASE.items():
        if plant_code.startswith(key) or key in plant_code:
            return info
    
    return None


def generate_seo_title(
    sku: str,
    current_title: str,
    use_scientific: bool = False,
    include_location: bool = True
) -> str:
    """
    Genera un título optimizado para SEO
    
    Estrategia SEO:
    1. Nombre descriptivo y natural
    2. Tamaño del producto (importante para viveros)
    3. Palabras clave relevantes
    4. Ubicación geográfica (Mendoza/Argentina)
    5. Largo óptimo: 50-60 caracteres
    """
    
    # Decodificar SKU
    plant_code, size_info = decode_sku(sku)
    
    if not plant_code:
        # Si no se puede decodificar, mejorar título actual
        return improve_current_title(current_title)
    
    # Buscar información de la planta
    plant_info = find_plant_info(plant_code)
    
    if not plant_info:
        # Título genérico mejorado
        base_name = plant_code.title()
        if size_info:
            return f"{base_name} en Maceta {size_info} | Vivero Los Cocos"
        return f"{base_name} | Planta de Vivero Los Cocos"
    
    # Construir título SEO-optimizado
    common_name = plant_info.common_names[0]
    category = plant_info.category
    
    # Título base
    if size_info:
        title = f"{common_name} {size_info}"
    else:
        title = common_name
    
    # Agregar categoría si es relevante y no está en el nombre
    if len(title) < 40 and category:
        title = f"{title} - {category}"
    
    # Agregar ubicación si se solicita
    if include_location and len(title) < 50:
        title = f"{title} | Mendoza"
    
    # Agregar marca del vivero si hay espacio
    if len(title) < 55:
        title = f"{title} | Vivero Los Cocos"
    
    return title


def improve_current_title(title: str) -> str:
    """Mejora un título existente sin información adicional"""
    # Eliminar guiones y normalizar
    title = title.replace("–", "-").replace("—", "-")
    
    # Capitalizar correctamente
    words = title.split()
    improved = []
    
    for word in words:
        if word.lower() in ["de", "del", "la", "el", "y", "o", "para"]:
            improved.append(word.lower())
        else:
            improved.append(word.capitalize())
    
    title = " ".join(improved)
    
    # Agregar contexto si es muy corto
    if len(title) < 30:
        title = f"{title} | Vivero Los Cocos Mendoza"
    
    return title


def generate_seo_description(sku: str, title: str, plant_info: Optional[PlantInfo] = None) -> str:
    """
    Genera una descripción SEO optimizada
    
    Meta description ideal: 150-160 caracteres
    """
    if not plant_info:
        plant_code, size_info = decode_sku(sku)
        if plant_code:
            plant_info = find_plant_info(plant_code)
    
    if plant_info:
        features_str = ", ".join(plant_info.features[:3])
        description = f"Comprá {plant_info.common_names[0]} en Vivero Los Cocos Mendoza. {plant_info.category} ideal para jardines. Características: {features_str}. ¡Envío el mismo día!"
    else:
        description = f"Comprá {title} en Vivero Los Cocos Mendoza. Plantas de calidad para tu jardín. Stock permanente y envío rápido en Mendoza, Argentina."
    
    # Truncar a 160 caracteres
    if len(description) > 160:
        description = description[:157] + "..."
    
    return description


def generate_seo_keywords(plant_info: Optional[PlantInfo], title: str) -> List[str]:
    """Genera keywords SEO"""
    keywords = []
    
    if plant_info:
        keywords.extend(plant_info.seo_keywords)
        keywords.extend(plant_info.common_names)
    
    # Keywords genéricos de vivero
    generic_keywords = [
        "vivero mendoza",
        "plantas mendoza",
        "vivero los cocos",
        "comprar plantas",
        "jardin mendoza",
        "vivero argentina"
    ]
    keywords.extend(generic_keywords)
    
    return list(set(keywords))  # Eliminar duplicados


def analyze_product_batch(products: List[Dict]) -> Dict:
    """Analiza un lote de productos y genera recomendaciones"""
    analysis = {
        "total": len(products),
        "decodified": 0,
        "with_plant_info": 0,
        "needs_optimization": 0,
        "optimizations": []
    }
    
    for product in products:
        sku = product.get("sku", "")
        current_title = product.get("title", "")
        
        plant_code, size_info = decode_sku(sku)
        if plant_code:
            analysis["decodified"] += 1
            
            plant_info = find_plant_info(plant_code)
            if plant_info:
                analysis["with_plant_info"] += 1
        
        # Generar título optimizado
        optimized_title = generate_seo_title(sku, current_title)
        
        if optimized_title != current_title:
            analysis["needs_optimization"] += 1
            analysis["optimizations"].append({
                "id": product.get("id"),
                "sku": sku,
                "current_title": current_title,
                "optimized_title": optimized_title,
                "improvement_score": calculate_improvement_score(current_title, optimized_title)
            })
    
    return analysis


def calculate_improvement_score(current: str, optimized: str) -> float:
    """Calcula un score de mejora (0-100)"""
    score = 0
    
    # Longitud óptima (50-60 chars)
    if 50 <= len(optimized) <= 60:
        score += 20
    elif 40 <= len(optimized) <= 70:
        score += 10
    
    # Contiene ubicación
    if "mendoza" in optimized.lower() or "argentina" in optimized.lower():
        score += 15
    
    # Contiene marca
    if "vivero" in optimized.lower() or "los cocos" in optimized.lower():
        score += 15
    
    # Contiene información de tamaño
    if re.search(r"\d+\s*(litros?|cm|l\b)", optimized.lower()):
        score += 20
    
    # Capitalización correcta
    if optimized[0].isupper():
        score += 10
    
    # No tiene caracteres especiales problemáticos
    if not re.search(r"[–—_\|]{2,}", optimized):
        score += 10
    
    # Más descriptivo que el anterior
    if len(optimized) > len(current):
        score += 10
    
    return min(score, 100)


def export_to_csv(analysis: Dict, output_file: str):
    """Exporta resultados a CSV para revisión"""
    with open(output_file, 'w', newline='', encoding='utf-8') as f:
        writer = csv.DictWriter(f, fieldnames=[
            'id', 'sku', 'current_title', 'optimized_title', 'improvement_score'
        ])
        writer.writeheader()
        writer.writerows(analysis['optimizations'])
    
    print(f"✅ Análisis exportado a: {output_file}")
    print(f"📊 Total productos: {analysis['total']}")
    print(f"🔍 Decodificados: {analysis['decodified']}")
    print(f"🌿 Con información: {analysis['with_plant_info']}")
    print(f"🚀 Necesitan optimización: {analysis['needs_optimization']}")


if __name__ == "__main__":
    # Ejemplos de uso
    test_skus = [
        "jazlluv3l",
        "abedul15l",
        "olivo20l",
        "bougan3l",
        "glici4l",
        "tilo15l"
    ]
    
    print("🌿 SEO TITLE OPTIMIZER - VIVERO LOS COCOS")
    print("=" * 60)
    print()
    
    for sku in test_skus:
        optimized = generate_seo_title(sku, sku.title())
        plant_code, _ = decode_sku(sku)
        plant_info = find_plant_info(plant_code) if plant_code else None
        
        print(f"SKU: {sku}")
        print(f"Título optimizado: {optimized}")
        
        if plant_info:
            description = generate_seo_description(sku, optimized, plant_info)
            keywords = generate_seo_keywords(plant_info, optimized)
            print(f"Descripción: {description}")
            print(f"Keywords: {', '.join(keywords[:5])}")
        
        print()
