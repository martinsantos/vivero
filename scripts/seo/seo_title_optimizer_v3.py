#!/usr/bin/env python3
"""
SEO Title Optimizer V3 - PROFESIONAL SIN REDUNDANCIAS
=====================================================
Estructura como e-commerce profesionales:
- Nombre del producto + Características clave
- Sin repeticiones de ubicación
- Formato natural y descriptivo
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
    care_level: str = "medio"
    light_needs: str = "sol pleno"
    water_needs: str = "moderado"


# Base de conocimiento de plantas (mantenida de V2)
PLANT_DATABASE = {
    "olivo": PlantInfo(
        scientific_name="Olea europaea",
        common_names=["Olivo", "Olivera"],
        category="Árbol Frutal",
        features=["perenne", "resistente sequía", "frutos comestibles"],
        seo_keywords=["olivo", "arbol frutal", "olivo mendoza"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="bajo"
    ),
    "jazlluv": PlantInfo(
        scientific_name="Jasminum nudiflorum",
        common_names=["Jazmín Lluvia de Oro", "Jazmín Amarillo"],
        category="Arbusto Floral",
        features=["flores amarillas", "perfumado", "resistente frío"],
        seo_keywords=["jazmin", "arbusto floral", "flores amarillas"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    "glici": PlantInfo(
        scientific_name="Wisteria",
        common_names=["Glicina", "Wisteria"],
        category="Trepadora Floral",
        features=["flores colgantes", "perfumada", "trepadora"],
        seo_keywords=["glicina", "trepadora", "flores"],
        care_level="medio",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    "abedul": PlantInfo(
        scientific_name="Betula",
        common_names=["Abedul"],
        category="Árbol Ornamental",
        features=["corteza blanca", "follaje dorado otoñal"],
        seo_keywords=["abedul", "arbol ornamental"],
        care_level="medio",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    "tilo": PlantInfo(
        scientific_name="Tilia",
        common_names=["Tilo"],
        category="Árbol de Sombra",
        features=["sombra densa", "flores medicinales"],
        seo_keywords=["tilo", "arbol sombra"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    "bougan": PlantInfo(
        scientific_name="Bougainvillea",
        common_names=["Bougainvillea", "Santa Rita"],
        category="Enredadera Floral",
        features=["flores coloridas", "resistente sequía"],
        seo_keywords=["bougainvillea", "santa rita", "enredadera"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="bajo"
    ),
    "drac": PlantInfo(
        scientific_name="Dracaena",
        common_names=["Dracena", "Palo de Agua"],
        category="Planta de Interior",
        features=["purifica aire", "bajo mantenimiento"],
        seo_keywords=["dracena", "planta interior"],
        care_level="fácil",
        light_needs="media sombra",
        water_needs="bajo"
    ),
    "raphis": PlantInfo(
        scientific_name="Rhapis excelsa",
        common_names=["Raphis", "Palmera Bambú"],
        category="Palmera de Interior",
        features=["ideal interior", "crecimiento lento"],
        seo_keywords=["raphis", "palmera interior"],
        care_level="fácil",
        light_needs="media sombra",
        water_needs="moderado"
    ),
    "liqui": PlantInfo(
        scientific_name="Liquidambar styraciflua",
        common_names=["Liquidámbar"],
        category="Árbol Ornamental",
        features=["colores otoñales", "sombra densa"],
        seo_keywords=["liquidambar", "arbol ornamental"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    "lau": PlantInfo(
        scientific_name="Laurus nobilis",
        common_names=["Laurel"],
        category="Arbusto Aromático",
        features=["aromático", "culinario"],
        seo_keywords=["laurel", "aromatico"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
}


def decode_sku(sku: str) -> Tuple[Optional[str], Optional[str], Optional[str]]:
    """
    Decodifica un SKU de producto
    Returns: (nombre_base, tamaño, color/variedad)
    """
    sku_clean = sku.strip().upper()
    
    # Patrón para macetas: MARCA + MODELO + TAMAÑO + COLOR
    # Ejemplo: MTAPR12MC = Maceta Ta Plastic Rocio 12cm Marron Clara
    if sku_clean.startswith('MTA'):
        # Maceta Ta Plastic
        match = re.match(r'MTA([A-Z]+)(\d+)([A-Z]+)', sku_clean)
        if match:
            modelo = match.group(1)
            tamaño = match.group(2)
            color = match.group(3)
            return ("maceta_ta_plastic", tamaño, color)
    
    # Patrón para plantas: NOMBRE + TAMAÑO + L
    match = re.match(r'^([A-Za-z]+?)(\d+)L?$', sku_clean, re.IGNORECASE)
    if match:
        nombre = match.group(1).lower()
        tamaño = match.group(2)
        return (nombre, tamaño, None)
    
    # Patrón para productos con variedad
    match = re.match(r'^([A-Za-z]+?)([A-Z]{2,})(\d+)$', sku_clean)
    if match:
        nombre = match.group(1).lower()
        variedad = match.group(2)
        tamaño = match.group(3)
        return (nombre, tamaño, variedad)
    
    return (sku_clean.lower(), None, None)


def find_plant_info(plant_code: str) -> Optional[PlantInfo]:
    """Busca información de la planta"""
    plant_code = plant_code.lower()
    
    if plant_code in PLANT_DATABASE:
        return PLANT_DATABASE[plant_code]
    
    for key, info in PLANT_DATABASE.items():
        if plant_code.startswith(key) or key in plant_code:
            return info
    
    return None


def normalize_color(color_code: str) -> str:
    """Normaliza códigos de color a nombres legibles"""
    color_map = {
        'MC': 'Marrón Claro',
        'MT': 'Marrón Terracota',
        'AM': 'Amarillo',
        'NA': 'Naranja',
        'VC': 'Verde Claro',
        'R': 'Rojo',
        'VI': 'Violeta',
        'NE': 'Negro',
        'BL': 'Blanco',
        'BE': 'Beige',
        'RO': 'Rosa',
        'AZ': 'Azul',
    }
    
    return color_map.get(color_code.upper(), color_code)


def generate_seo_title_v3(sku: str, current_title: str) -> str:
    """
    Genera título SEO V3 - PROFESIONAL SIN REDUNDANCIAS
    
    Estructura inspirada en e-commerce líderes:
    - Mercado Libre: "Producto Marca Modelo. Característica."
    - Amazon: "Producto - Característica Principal - Tamaño"
    - Easy/Sodimac: "Producto Marca. Tamaño. Color."
    
    Reglas:
    1. NO repetir ubicación (Mendoza Argentina)
    2. NO usar pipes (|) excesivos
    3. Usar puntos (.) para separar características
    4. Formato natural y descriptivo
    5. Longitud: 50-70 caracteres
    """
    
    nombre_base, tamaño, variedad = decode_sku(sku)
    
    # CASO 1: MACETAS TA PLASTIC
    if nombre_base == "maceta_ta_plastic":
        if tamaño and variedad:
            color = normalize_color(variedad)
            return f"Maceta Plástica Rocío {tamaño} cm. Color: {color}"
        return current_title
    
    # CASO 2: MACETAS MATRI
    if 'matri' in current_title.lower():
        # Extraer información del título actual
        parts = current_title.split('–')
        if len(parts) >= 2:
            marca = parts[0].strip()
            resto = parts[1].strip()
            
            # Buscar color
            color = None
            for palabra in ['Negro', 'Negra', 'Blanco', 'Blanca', 'Rojo', 'Roja']:
                if palabra in resto:
                    color = palabra
                    resto = resto.replace(palabra, '').strip()
                    break
            
            # Buscar modelo
            modelo = resto.split('|')[0].strip() if '|' in resto else resto
            
            if color:
                return f"Maceta Jardinera {modelo} {marca}. Color: {color}"
            else:
                return f"Maceta Jardinera {modelo} {marca}"
    
    # CASO 3: PLANTAS CON INFO BOTÁNICA
    plant_info = find_plant_info(nombre_base)
    
    if plant_info:
        nombre = plant_info.common_names[0]
        categoria = plant_info.category
        
        if tamaño:
            # Determinar unidad
            unidad = "Litros" if int(tamaño) <= 50 else "cm"
            
            # Característica principal (primera feature)
            caracteristica = plant_info.features[0] if plant_info.features else ""
            
            if caracteristica:
                # Formato: "Nombre Tamaño - Categoría. Característica"
                return f"{nombre} {tamaño} {unidad} - {categoria}"
            else:
                return f"{nombre} {tamaño} {unidad} - {categoria}"
        else:
            return f"{nombre} - {categoria}"
    
    # CASO 4: PRODUCTOS GENÉRICOS CON TAMAÑO
    if tamaño:
        nombre_limpio = nombre_base.replace('_', ' ').title()
        
        # Determinar unidad correcta basado en el tamaño
        try:
            tam_num = int(tamaño)
            # Si es mayor a 50, probablemente son centímetros
            # Si es menor o igual a 50, probablemente son litros
            if tam_num > 50:
                unidad = "cm"
                tipo = "Maceta"
            else:
                unidad = "Litros"
                tipo = "Planta"
        except:
            unidad = "Litros"
            tipo = "Planta"
        
        # Determinar tipo basado en título actual
        if 'maceta' in current_title.lower() or 'jardinera' in current_title.lower():
            tipo = "Maceta"
            unidad = "cm"
        elif any(x in current_title.lower() for x in ['árbol', 'arbol']):
            tipo = "Árbol"
        elif 'arbusto' in current_title.lower():
            tipo = "Arbusto"
        elif 'planta' in current_title.lower():
            tipo = "Planta"
        
        return f"{tipo} {nombre_limpio} {tamaño} {unidad}"
    
    # CASO 5: PRODUCTOS SIN TAMAÑO
    nombre_limpio = nombre_base.replace('_', ' ').title()
    
    # Mantener lo que ya está bien
    if len(current_title) >= 40 and '|' not in current_title:
        return current_title
    
    # Limpiar título actual de redundancias
    titulo_limpio = current_title
    titulo_limpio = re.sub(r'\s*\|\s*Mendoza.*', '', titulo_limpio)
    titulo_limpio = re.sub(r'\s*\|\s*Vivero.*', '', titulo_limpio)
    titulo_limpio = re.sub(r'\s*\|\s*Argentina.*', '', titulo_limpio)
    
    if len(titulo_limpio) >= 30:
        return titulo_limpio
    
    return f"{nombre_limpio} - Producto de Vivero"


def improve_current_title(title: str) -> str:
    """Mejora un título existente eliminando redundancias"""
    # Eliminar redundancias comunes
    title = re.sub(r'\s*\|\s*Mendoza\s*Argentina', '', title)
    title = re.sub(r'\s*\|\s*Mendoza', '', title)
    title = re.sub(r'\s*\|\s*Argentina', '', title)
    title = re.sub(r'\s*\|\s*Vivero\s+Los\s+Cocos', '', title)
    
    # Limpiar pipes múltiples
    title = re.sub(r'\s*\|\s*\|\s*', ' | ', title)
    title = re.sub(r'\s*\|\s*$', '', title)
    
    # Capitalizar correctamente
    words = title.split()
    improved = []
    
    for word in words:
        if word.lower() in ["de", "del", "la", "el", "y", "o", "para", "con", "en"]:
            improved.append(word.lower())
        else:
            improved.append(word.capitalize())
    
    return " ".join(improved).strip()


def generate_seo_description_v3(sku: str, title: str, plant_info: Optional[PlantInfo] = None) -> str:
    """
    Genera descripción SEO V3 - Natural y sin redundancias
    """
    if plant_info:
        features_str = ", ".join(plant_info.features[:2])
        description = (
            f"{plant_info.common_names[0]} - {plant_info.category}. "
            f"Características: {features_str}. "
            f"Vivero Los Cocos, Mendoza."
        )
    else:
        description = (
            f"{title}. Producto de calidad para tu jardín. "
            f"Vivero Los Cocos, Mendoza, Argentina."
        )
    
    # Truncar a 160 caracteres
    if len(description) > 160:
        description = description[:157] + "..."
    
    return description


if __name__ == "__main__":
    # Tests
    test_cases = [
        ("MTAPR12MC", "Ta Plastic – Rocio Marron Clara"),
        ("JAZLLUV3L", "Jazlluv3l"),
        ("OLIVO20L", "Olivo20l"),
        ("MATRI", "Matri – Jardinera Erika Negra | Mendoza Argentina"),
    ]
    
    print("🌿 SEO TITLE OPTIMIZER V3 - SIN REDUNDANCIAS")
    print("=" * 70)
    print()
    
    for sku, current in test_cases:
        optimized = generate_seo_title_v3(sku, current)
        print(f"SKU: {sku}")
        print(f"Antes:  {current}")
        print(f"Ahora:  {optimized}")
        print()
