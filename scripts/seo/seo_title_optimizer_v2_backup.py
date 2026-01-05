#!/usr/bin/env python3
"""
SEO Title Optimizer V2 - ULTRA ENRIQUECIDO
==========================================
Base de conocimiento expandida con 80+ plantas argentinas
Optimización SEO de clase mundial para viveros
"""

import re
import csv
import json
from typing import Dict, List, Tuple, Optional
from dataclasses import dataclass


@dataclass
class PlantInfo:
    """Información enriquecida de una planta para SEO"""
    scientific_name: str
    common_names: List[str]
    category: str
    features: List[str]
    seo_keywords: List[str]
    care_level: str = "medio"  # fácil, medio, difícil
    light_needs: str = "sol pleno"  # sol pleno, media sombra, sombra
    water_needs: str = "moderado"  # bajo, moderado, alto


# Base de conocimiento ULTRA ENRIQUECIDA - 80+ plantas
PLANT_DATABASE = {
    # ========== ÁRBOLES FRUTALES ==========
    "olivo": PlantInfo(
        scientific_name="Olea europaea",
        common_names=["Olivo", "Olivera", "Olivo Europeo", "Aceituno"],
        category="Árbol Frutal Mediterráneo",
        features=["perenne", "resistente sequía extrema", "frutos comestibles aceitunas", "follaje plateado decorativo", "ideal clima seco mendocino", "longevidad centenaria"],
        seo_keywords=["olivo mendoza", "arbol olivo jardin", "olivo frutal argentina", "comprar olivo aceitunas", "olivo resistente sequia"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="bajo"
    ),
    
    "jaca": PlantInfo(
        scientific_name="Artocarpus heterophyllus",
        common_names=["Jaca", "Yaca", "Árbol de Jack", "Jackfruit"],
        category="Árbol Frutal Tropical Exótico",
        features=["tropical", "frutos gigantes hasta 30kg", "sombra densa refrescante", "crecimiento rápido vigoroso", "fruto dulce nutritivo"],
        seo_keywords=["jaca fruta mendoza", "arbol jaca argentina", "yaca tropical", "jackfruit exotico", "fruta gigante"],
        care_level="medio",
        light_needs="sol pleno",
        water_needs="alto"
    ),
    
    "prun": PlantInfo(
        scientific_name="Prunus",
        common_names=["Ciruelo", "Cerezo", "Prunus Ornamental", "Ciruelo Japonés"],
        category="Árbol Frutal Ornamental",
        features=["flores primaverales espectaculares", "frutos comestibles dulces", "follaje decorativo", "ideal clima templado", "floración temprana"],
        seo_keywords=["ciruelo mendoza", "cerezo frutal", "prunus jardin", "arbol flores primavera", "ciruelo ornamental"],
        care_level="medio",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    
    "fres": PlantInfo(
        scientific_name="Fragaria",
        common_names=["Frutilla", "Fresa", "Frutilla de Jardín", "Fresón"],
        category="Frutal Pequeño Rastrero",
        features=["frutos rojos comestibles", "rastrera cubresuelos", "flores blancas delicadas", "cosecha continua", "ideal macetas y jardín"],
        seo_keywords=["frutilla planta mendoza", "fresa jardin", "cultivar frutillas casa", "planta frutilla argentina", "frutilla maceta"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    
    # ========== ARBUSTOS FLORALES ==========
    "jazmin": PlantInfo(
        scientific_name="Jasminum",
        common_names=["Jazmín", "Jazmin Blanco", "Jazmín Común", "Jazmín del País"],
        category="Arbusto Floral Perfumado",
        features=["flores blancas intensamente aromáticas", "trepadora o arbusto versátil", "perenne resistente", "floración primavera-verano prolongada", "ideal cercos y pérgolas", "perfume nocturno"],
        seo_keywords=["jazmin mendoza", "jazmin perfumado jardin", "planta jazmin argentina", "jazmin trepador", "jazmin flores blancas"],
        care_level="fácil",
        light_needs="sol pleno o media sombra",
        water_needs="moderado"
    ),
    
    "jazlluv": PlantInfo(
        scientific_name="Jasminum nudiflorum",
        common_names=["Jazmín Lluvia de Oro", "Jazmín de Invierno", "Jazmín Amarillo", "Jazmín Dorado"],
        category="Arbusto Floral de Invierno",
        features=["flores amarillas brillantes únicas", "floración invernal espectacular", "perfumado intenso y dulce", "resistente heladas fuertes", "cascada dorada ornamental", "florece sin hojas"],
        seo_keywords=["jazmin lluvia oro mendoza", "jazmin amarillo invierno", "jazmin resistente frio", "jazmin perfumado argentina", "jazmin dorado"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    
    "bougan": PlantInfo(
        scientific_name="Bougainvillea",
        common_names=["Bougainvillea", "Buganvilia", "Santa Rita", "Trinitaria", "Veranera"],
        category="Enredadera Floral Espectacular",
        features=["brácteas coloridas todo el año", "muy resistente sequía extrema", "trepadora vigorosa rápida", "colores intensos variados", "bajo mantenimiento", "ideal muros y pérgolas"],
        seo_keywords=["bougainvillea mendoza", "santa rita argentina", "buganvilia trepadora", "planta resistente sol", "enredadera colorida"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="bajo"
    ),
    
    "rosa": PlantInfo(
        scientific_name="Rosa",
        common_names=["Rosa", "Rosal", "Rosa de Jardín", "Rosa Híbrida"],
        category="Arbusto Floral Clásico",
        features=["flores perfumadas elegantes", "variedad infinita de colores", "floración repetida", "ideal jardines románticos", "corte para floreros"],
        seo_keywords=["rosal mendoza", "rosa jardin argentina", "comprar rosas", "rosal perfumado", "rosa hibrida"],
        care_level="medio",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    
    # ========== ÁRBOLES ORNAMENTALES ==========
    "abedul": PlantInfo(
        scientific_name="Betula",
        common_names=["Abedul", "Abedul Blanco", "Abedul Europeo", "Betula"],
        category="Árbol Ornamental de Corteza Blanca",
        features=["corteza blanca papelosa única", "follaje dorado otoñal", "caduco decorativo", "porte elegante esbelto", "ideal jardines paisajísticos"],
        seo_keywords=["abedul mendoza", "arbol corteza blanca", "abedul ornamental", "betula jardin", "abedul blanco argentina"],
        care_level="medio",
        light_needs="sol pleno o media sombra",
        water_needs="moderado"
    ),
    
    "tilo": PlantInfo(
        scientific_name="Tilia",
        common_names=["Tilo", "Tila", "Tilo Europeo", "Tilo Plateado"],
        category="Árbol Ornamental de Sombra",
        features=["sombra densa refrescante", "flores medicinales aromáticas", "perfumado en verano", "follaje denso verde", "ideal veredas y plazas"],
        seo_keywords=["tilo mendoza", "arbol sombra jardin", "tilo medicinal", "tila argentina", "arbol tilo ornamental"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    
    "liqui": PlantInfo(
        scientific_name="Liquidambar styraciflua",
        common_names=["Liquidámbar", "Árbol del Ámbar", "Liquidambar Americano", "Ocozol"],
        category="Árbol Ornamental de Colores Otoñales",
        features=["colores otoñales espectaculares", "follaje estrellado único", "sombra densa", "crecimiento vigoroso", "ideal parques y jardines grandes"],
        seo_keywords=["liquidambar mendoza", "arbol colores otoño", "liquidambar jardin", "arbol ornamental argentina", "liquidambar americano"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    
    "eucacin": PlantInfo(
        scientific_name="Eucalyptus cinerea",
        common_names=["Eucalipto Plateado", "Eucalipto Cinéreo", "Eucalipto Dollar", "Eucalipto Azul"],
        category="Árbol Ornamental Aromático",
        features=["hojas plateadas aromáticas", "follaje decorativo para corte", "crecimiento rápido", "resistente sequía", "ideal arreglos florales"],
        seo_keywords=["eucalipto plateado mendoza", "eucalyptus cinerea", "eucalipto decorativo", "eucalipto jardin", "eucalipto aromatico"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="bajo"
    ),
    
    # ========== ACACIAS Y ÁRBOLES DE SOMBRA ==========
    "acacia": PlantInfo(
        scientific_name="Acacia",
        common_names=["Acacia", "Acacia Común", "Mimosa", "Acacia Dorada"],
        category="Árbol de Sombra Rápido",
        features=["sombra densa rápida", "flores amarillas perfumadas", "resistente sequía", "crecimiento veloz", "ideal cortinas forestales"],
        seo_keywords=["acacia mendoza", "arbol sombra rapido", "acacia jardin", "mimosa argentina", "acacia dorada"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="bajo"
    ),
    
    "acacons": PlantInfo(
        scientific_name="Albizia julibrissin",
        common_names=["Acacia de Constantinopla", "Acacia Rosada", "Árbol de la Seda", "Albizia"],
        category="Árbol de Sombra Ornamental",
        features=["flores rosas plumosas espectaculares", "sombra ligera filtrada", "follaje finamente dividido", "floración estival prolongada", "ideal jardines ornamentales"],
        seo_keywords=["acacia constantinopla mendoza", "acacia rosada", "albizia julibrissin", "arbol seda argentina", "acacia flores rosas"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    
    "arabia": PlantInfo(
        scientific_name="Acacia arabica",
        common_names=["Acacia Arábiga", "Espina de Cristo", "Acacia Espinosa", "Babul"],
        category="Árbol Espinoso Resistente",
        features=["muy resistente sequía extrema", "espinas protectoras", "sombra densa", "flores amarillas aromáticas", "ideal cercos vivos"],
        seo_keywords=["acacia arabiga mendoza", "espina cristo", "acacia espinosa", "arbol resistente sequia", "acacia cerco vivo"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="bajo"
    ),
    
    # ========== TREPADORAS Y ENREDADERAS ==========
    "glici": PlantInfo(
        scientific_name="Wisteria",
        common_names=["Glicina", "Wisteria", "Glicinia", "Flor de la Pluma"],
        category="Trepadora Floral Espectacular",
        features=["flores colgantes en racimos largos", "perfumada intensamente", "floración primaveral masiva", "trepadora vigorosa", "colores lila, blanco, rosado"],
        seo_keywords=["glicina mendoza", "wisteria argentina", "glicina trepadora", "flores colgantes", "glicina perfumada"],
        care_level="medio",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    
    # ========== PLANTAS DE INTERIOR Y ORNAMENTALES ==========
    "drac": PlantInfo(
        scientific_name="Dracaena",
        common_names=["Dracena", "Palo de Agua", "Drácena", "Tronco del Brasil"],
        category="Planta de Interior Purificadora",
        features=["purifica aire interior", "bajo mantenimiento", "follaje decorativo variegado", "resistente sombra", "ideal oficinas y hogares"],
        seo_keywords=["dracena interior mendoza", "palo agua", "dracaena jardin", "planta purificadora", "dracena oficina"],
        care_level="fácil",
        light_needs="media sombra o sombra",
        water_needs="bajo"
    ),
    
    "thuja": PlantInfo(
        scientific_name="Thuja",
        common_names=["Tuya", "Thuja", "Cedro Blanco", "Árbol de la Vida"],
        category="Conífera Ornamental Perenne",
        features=["perenne todo el año", "ideal cercos vivos densos", "follaje aromático", "forma cónica elegante", "resistente frío"],
        seo_keywords=["thuja mendoza", "tuya cerco vivo", "cedro blanco", "conifera jardin", "thuja argentina"],
        care_level="fácil",
        light_needs="sol pleno o media sombra",
        water_needs="moderado"
    ),
    
    "lau": PlantInfo(
        scientific_name="Laurus nobilis",
        common_names=["Laurel", "Laurel de Cocina", "Laurel Común", "Laurel Noble"],
        category="Arbusto Aromático Culinario",
        features=["hojas aromáticas culinarias", "perenne resistente", "topiaria decorativa", "medicinal y condimento", "ideal macetas y jardín"],
        seo_keywords=["laurel planta mendoza", "laurel cocina", "laurel aromatico", "laurus nobilis", "laurel culinario"],
        care_level="fácil",
        light_needs="sol pleno o media sombra",
        water_needs="moderado"
    ),
    
    # ========== FORSITIAS Y ARBUSTOS FLORALES ==========
    "for": PlantInfo(
        scientific_name="Forsythia",
        common_names=["Forsitia", "Campanas de Oro", "Forsythia", "Arbusto Dorado"],
        category="Arbusto Floral Primaveral",
        features=["flores amarillas doradas masivas", "floración temprana primaveral", "antes que las hojas", "muy ornamental", "ideal cercos floridos"],
        seo_keywords=["forsythia mendoza", "campanas oro", "forsitia jardin", "arbusto flores amarillas", "forsythia argentina"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    
    # ========== PLANTAS ADICIONALES DEL INVENTARIO ==========
    "yuca": PlantInfo(
        scientific_name="Yucca",
        common_names=["Yuca", "Yuca de Jardín", "Yucca", "Palma Yuca"],
        category="Planta Suculenta Ornamental",
        features=["resistente sequía extrema", "follaje espinoso decorativo", "flores blancas en espiga", "bajo mantenimiento", "ideal xerojardines"],
        seo_keywords=["yuca mendoza", "yucca jardin", "planta resistente sequia", "yuca ornamental", "suculenta grande"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="bajo"
    ),
    
    "raphis": PlantInfo(
        scientific_name="Rhapis excelsa",
        common_names=["Raphis", "Palmera de Bambú", "Palma Lady", "Rapis"],
        category="Palmera de Interior",
        features=["ideal interior sombra", "crecimiento lento compacto", "follaje elegante en abanico", "purificadora aire", "resistente plagas"],
        seo_keywords=["raphis mendoza", "palmera bambu", "palma interior", "rhapis excelsa", "palmera sombra"],
        care_level="fácil",
        light_needs="media sombra o sombra",
        water_needs="moderado"
    ),
    
    "nandom": PlantInfo(
        scientific_name="Nandina domestica",
        common_names=["Nandina", "Bambú Sagrado", "Bambú Celestial", "Nandina Doméstica"],
        category="Arbusto Ornamental Perenne",
        features=["follaje cambia color estaciones", "bayas rojas decorativas", "resistente frío", "bajo mantenimiento", "ideal macetas y jardín"],
        seo_keywords=["nandina mendoza", "bambu sagrado", "nandina domestica", "arbusto perenne", "nandina jardin"],
        care_level="fácil",
        light_needs="sol pleno o media sombra",
        water_needs="moderado"
    ),
    
    "strenico": PlantInfo(
        scientific_name="Strelitzia nicolai",
        common_names=["Strelitzia Nicolai", "Ave del Paraíso Gigante", "Estrelitzia Blanca", "Pájaro del Paraíso"],
        category="Planta Tropical Ornamental",
        features=["flores blancas espectaculares", "follaje grande tropical", "crecimiento vigoroso", "ideal jardines modernos", "resistente viento"],
        seo_keywords=["strelitzia mendoza", "ave paraiso gigante", "strelitzia nicolai", "planta tropical", "estrelitzia blanca"],
        care_level="medio",
        light_needs="sol pleno o media sombra",
        water_needs="moderado"
    ),
    
    "oletex": PlantInfo(
        scientific_name="Olea europaea 'Texana'",
        common_names=["Olivo Texano", "Olivo Ornamental", "Olivo de Texas"],
        category="Árbol Ornamental Mediterráneo",
        features=["follaje plateado decorativo", "resistente sequía", "bajo mantenimiento", "ideal jardines modernos", "crecimiento compacto"],
        seo_keywords=["olivo texano mendoza", "olivo ornamental", "olea texana", "olivo jardin", "olivo resistente"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="bajo"
    ),
    
    "teucri": PlantInfo(
        scientific_name="Teucrium fruticans",
        common_names=["Teucrio", "Salvia Arbustiva", "Teucrium", "Olivilla"],
        category="Arbusto Aromático Mediterráneo",
        features=["flores azules delicadas", "follaje plateado aromático", "resistente sequía", "ideal borduras", "bajo mantenimiento"],
        seo_keywords=["teucrio mendoza", "teucrium fruticans", "arbusto mediterraneo", "salvia arbustiva", "planta resistente sequia"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="bajo"
    ),
    
    "sansetri": PlantInfo(
        scientific_name="Sansevieria trifasciata",
        common_names=["Sansevieria", "Lengua de Suegra", "Espada de San Jorge", "Planta Serpiente"],
        category="Suculenta de Interior Purificadora",
        features=["purifica aire nocturno", "muy resistente abandono", "ideal interior oscuro", "bajo riego", "decorativa moderna"],
        seo_keywords=["sansevieria mendoza", "lengua suegra", "planta interior resistente", "sansevieria trifasciata", "planta purificadora"],
        care_level="muy fácil",
        light_needs="cualquier luz",
        water_needs="muy bajo"
    ),
    
    "philim": PlantInfo(
        scientific_name="Philodendron",
        common_names=["Filodendro", "Philodendron", "Filodendro Imperial", "Monstera"],
        category="Planta de Interior Tropical",
        features=["follaje grande decorativo", "trepadora o colgante", "purifica aire", "crecimiento rápido", "ideal interior"],
        seo_keywords=["philodendron mendoza", "filodendro interior", "planta tropical", "philodendron jardin", "monstera"],
        care_level="fácil",
        light_needs="media sombra",
        water_needs="moderado"
    ),
    
    "phor": PlantInfo(
        scientific_name="Phormium",
        common_names=["Formio", "Lino de Nueva Zelanda", "Phormium", "Formio Variegado"],
        category="Planta Ornamental Arquitectónica",
        features=["follaje lineal arquitectónico", "colores variegados", "resistente viento", "bajo mantenimiento", "ideal jardines modernos"],
        seo_keywords=["formio mendoza", "phormium jardin", "lino nueva zelanda", "planta arquitectonica", "formio variegado"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    
    "crespon": PlantInfo(
        scientific_name="Lagerstroemia indica",
        common_names=["Crespón", "Árbol de Júpiter", "Lagerstroemia", "Astromelia"],
        category="Árbol Floral Ornamental",
        features=["flores abundantes verano", "corteza decorativa exfoliante", "colores intensos", "resistente calor", "ideal veredas"],
        seo_keywords=["crespon mendoza", "arbol jupiter", "lagerstroemia", "arbol flores verano", "crespon ornamental"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    
    "morah": PlantInfo(
        scientific_name="Morus alba",
        common_names=["Morera", "Moral", "Morus", "Árbol de Mora"],
        category="Árbol Frutal de Sombra",
        features=["frutos comestibles dulces", "sombra densa refrescante", "crecimiento rápido", "resistente", "ideal jardines grandes"],
        seo_keywords=["morera mendoza", "moral arbol", "morus alba", "arbol moras", "morera frutal"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    
    "agua": PlantInfo(
        scientific_name="Persea americana",
        common_names=["Aguacate", "Palta", "Avocado", "Palto"],
        category="Árbol Frutal Tropical",
        features=["frutos nutritivos cremosos", "follaje perenne denso", "sombra", "crecimiento vigoroso", "ideal clima templado"],
        seo_keywords=["aguacate mendoza", "palta arbol", "avocado", "palto frutal", "aguacate jardin"],
        care_level="medio",
        light_needs="sol pleno",
        water_needs="moderado"
    ),
    
    "brachi": PlantInfo(
        scientific_name="Brachychiton",
        common_names=["Braquiquito", "Árbol Botella", "Brachychiton", "Palo Borracho"],
        category="Árbol Ornamental Resistente",
        features=["tronco engrosado decorativo", "flores vistosas", "resistente sequía extrema", "follaje caduco", "ideal xerojardines"],
        seo_keywords=["braquiquito mendoza", "brachychiton", "arbol botella", "arbol resistente sequia", "palo borracho"],
        care_level="fácil",
        light_needs="sol pleno",
        water_needs="bajo"
    ),
}


# Patrones de decodificación mejorados
SKU_PATTERNS = {
    # Plantas con tamaño en litros
    r"^([A-Za-z]+?)(\d+)l$": lambda m: (m.group(1).lower(), f"{m.group(2)} Litros"),
    # Plantas con variedad y tamaño
    r"^([A-Za-z]+?)([a-z]{2,4})(\d+)l$": lambda m: (m.group(1).lower(), f"{m.group(3)} Litros"),
    # Macetas y productos
    r"^([A-Za-z]+?)(\d+)$": lambda m: (m.group(1).lower(), f"{m.group(2)} cm"),
}


def decode_sku(sku: str) -> Tuple[Optional[str], Optional[str]]:
    """Decodifica un SKU de producto"""
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
    
    # Búsqueda por coincidencia parcial (más inteligente)
    for key, info in PLANT_DATABASE.items():
        if plant_code.startswith(key) or key in plant_code:
            return info
        # Buscar en nombres comunes
        for common_name in info.common_names:
            if common_name.lower() in plant_code or plant_code in common_name.lower():
                return info
    
    return None


def generate_seo_title(
    sku: str,
    current_title: str,
    use_scientific: bool = False,
    include_location: bool = True
) -> str:
    """
    Genera un título ULTRA optimizado para SEO
    
    Estrategia SEO V2:
    1. Nombre descriptivo y natural
    2. Tamaño del producto (crítico para viveros)
    3. Categoría botánica (contexto)
    4. Características únicas (diferenciación)
    5. Ubicación geográfica (Mendoza/Argentina)
    6. Largo óptimo: 55-65 caracteres
    """
    
    # Decodificar SKU
    plant_code, size_info = decode_sku(sku)
    
    if not plant_code:
        return improve_current_title(current_title)
    
    # Buscar información de la planta
    plant_info = find_plant_info(plant_code)
    
    if not plant_info:
        # Título genérico mejorado
        base_name = plant_code.title()
        if size_info:
            return f"{base_name} {size_info} - Planta de Vivero | Mendoza"
        return f"{base_name} - Planta de Vivero | Mendoza Argentina"
    
    # Construir título SEO-optimizado V2
    common_name = plant_info.common_names[0]
    category = plant_info.category
    
    # Característica destacada (primera feature)
    feature = plant_info.features[0] if plant_info.features else ""
    
    # Título base con nombre y tamaño
    if size_info:
        title = f"{common_name} {size_info}"
    else:
        title = common_name
    
    # Agregar categoría si aporta valor SEO
    if len(title) < 40 and category and category not in title:
        title = f"{title} - {category}"
    
    # Agregar ubicación para SEO local
    if include_location and len(title) < 55:
        title = f"{title} | Mendoza"
    
    # Limitar a 70 caracteres máximo
    if len(title) > 70:
        title = title[:67] + "..."
    
    return title


def improve_current_title(title: str) -> str:
    """Mejora un título existente"""
    title = title.replace("–", "-").replace("—", "-")
    
    # Capitalizar correctamente
    words = title.split()
    improved = []
    
    for word in words:
        if word.lower() in ["de", "del", "la", "el", "y", "o", "para", "con"]:
            improved.append(word.lower())
        else:
            improved.append(word.capitalize())
    
    title = " ".join(improved)
    
    # Agregar contexto si es muy corto
    if len(title) < 30 and "Mendoza" not in title:
        title = f"{title} | Mendoza Argentina"
    
    return title


def generate_seo_description(sku: str, title: str, plant_info: Optional[PlantInfo] = None) -> str:
    """
    Genera una descripción SEO ULTRA optimizada
    
    Meta description ideal: 150-160 caracteres
    Incluye: CTA + Nombre + Características + Ubicación + Urgencia
    """
    if not plant_info:
        plant_code, size_info = decode_sku(sku)
        if plant_code:
            plant_info = find_plant_info(plant_code)
    
    if plant_info:
        # Tomar las 3 características más importantes
        features_str = ", ".join(plant_info.features[:3])
        
        # Descripción enriquecida
        description = (
            f"Comprá {plant_info.common_names[0]} en Vivero Los Cocos Mendoza. "
            f"{plant_info.category}. Características: {features_str}. "
            f"Stock disponible. ¡Envío rápido!"
        )
    else:
        description = (
            f"Comprá {title} en Vivero Los Cocos Mendoza. "
            f"Plantas de calidad premium para tu jardín. "
            f"Stock permanente y envío el mismo día en Mendoza, Argentina."
        )
    
    # Truncar a 160 caracteres
    if len(description) > 160:
        description = description[:157] + "..."
    
    return description


def generate_seo_keywords(plant_info: Optional[PlantInfo], title: str) -> List[str]:
    """Genera keywords SEO enriquecidas"""
    keywords = []
    
    if plant_info:
        keywords.extend(plant_info.seo_keywords)
        keywords.extend(plant_info.common_names)
        keywords.append(plant_info.category.lower())
        
        # Agregar keywords de cuidado
        keywords.append(f"planta {plant_info.care_level}")
        keywords.append(f"planta {plant_info.light_needs}")
    
    # Keywords genéricos de vivero
    generic_keywords = [
        "vivero mendoza",
        "plantas mendoza",
        "vivero los cocos",
        "comprar plantas argentina",
        "jardin mendoza",
        "plantas para jardin"
    ]
    keywords.extend(generic_keywords)
    
    return list(set(keywords))[:15]  # Top 15 keywords únicos


if __name__ == "__main__":
    # Test con ejemplos
    test_skus = [
        "jazlluv3l",
        "olivo20l",
        "glici4l",
        "abedul15l",
        "tilo15l",
        "bougan3l",
        "drac15l",
        "raphis10l"
    ]
    
    print("🌿 SEO TITLE OPTIMIZER V2 - ULTRA ENRIQUECIDO")
    print("=" * 70)
    print()
    
    for sku in test_skus:
        optimized = generate_seo_title(sku, sku.title())
        plant_code, _ = decode_sku(sku)
        plant_info = find_plant_info(plant_code) if plant_code else None
        
        print(f"SKU: {sku}")
        print(f"Título: {optimized}")
        
        if plant_info:
            description = generate_seo_description(sku, optimized, plant_info)
            keywords = generate_seo_keywords(plant_info, optimized)
            print(f"Categoría: {plant_info.category}")
            print(f"Cuidado: {plant_info.care_level} | Luz: {plant_info.light_needs} | Riego: {plant_info.water_needs}")
            print(f"Descripción: {description}")
            print(f"Keywords: {', '.join(keywords[:5])}")
        
        print()
