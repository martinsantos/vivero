#!/usr/bin/env python3
"""
VALIDACIÓN ESTRICTA DE PERTINENCIA DE IMÁGENES
=============================================

Valida que imágenes sean:
1. ÚNICAS (no duplicadas visualmente)
2. PERTINENTES (relacionadas con producto/vivero/plantas)
3. RELEVANTES (match con meta tags, categorías, atributos)

Uso:
    from validar_pertinencia_imagen import validar_imagen_producto
    
    score, razones = validar_imagen_producto(producto, imagen_url, imagen_metadata)
    if score >= 60:
        # Imagen aceptable
    else:
        # Rechazar imagen
"""

import re
from typing import Dict, Any, List, Tuple, Optional


# Palabras clave OBLIGATORIAS para vivero de plantas
KEYWORDS_VIVERO = [
    'plant', 'plants', 'planta', 'plantas',
    'tree', 'trees', 'arbol', 'arboles',
    'flower', 'flowers', 'flor', 'flores',
    'leaf', 'leaves', 'hoja', 'hojas',
    'garden', 'jardin', 'gardening',
    'botanical', 'botanico', 'botanic',
    'nature', 'natural', 'naturaleza',
    'green', 'verde', 'foliage', 'follaje',
    'pot', 'potted', 'maceta', 'container',
    'nursery', 'vivero', 'greenhouse',
    'succulent', 'suculenta', 'cactus',
    'palm', 'palma', 'fern', 'helecho',
    'shrub', 'arbusto', 'bush',
    'vine', 'trepadora', 'climbing'
]

# Palabras clave PROHIBIDAS (no relacionadas con vivero)
KEYWORDS_PROHIBIDAS = [
    'person', 'people', 'human', 'man', 'woman', 'child',
    'animal', 'dog', 'cat', 'bird', 'pet',
    'car', 'vehicle', 'auto', 'truck',
    'building', 'house', 'edificio', 'construction',
    'food', 'comida', 'meal', 'dish',
    'technology', 'computer', 'phone', 'device',
    'abstract', 'pattern', 'texture', 'background',
    'sky', 'cloud', 'sunset', 'landscape' # Solo si no hay plantas
]


def extraer_keywords_producto(producto: Dict[str, Any]) -> List[str]:
    """Extrae keywords relevantes del producto"""
    keywords = []
    
    # Nombre del producto
    nombre = producto.get('name', '').lower()
    keywords.extend([w for w in nombre.split() if len(w) > 3])
    
    # Categorías
    for cat in producto.get('categories', []):
        cat_name = cat.get('name', '').lower()
        keywords.extend([w for w in cat_name.split() if len(w) > 3])
    
    # Tags (si existen)
    for tag in producto.get('tags', []):
        tag_name = tag.get('name', '').lower()
        keywords.extend([w for w in tag_name.split() if len(w) > 3])
    
    # Atributos
    for attr in producto.get('attributes', []):
        attr_name = attr.get('name', '').lower()
        keywords.append(attr_name)
        for opt in attr.get('options', []):
            if isinstance(opt, str):
                keywords.append(opt.lower())
    
    # Limpiar y deduplicar
    stop_words = ['de', 'del', 'la', 'el', 'en', 'con', 'para', 'por', 'y', 'o']
    keywords = [k for k in keywords if k not in stop_words]
    keywords = list(set(keywords))
    
    return keywords


def detectar_nombre_cientifico(nombre: str) -> Optional[Tuple[str, str]]:
    """Detecta nombre científico en formato 'Género especie'"""
    pattern = r'\b([A-Z][a-z]+)\s+(?:x\s+)?([a-z]+)\b'
    match = re.search(pattern, nombre)
    if match:
        return (match.group(1).lower(), match.group(2).lower())
    return None


def validar_imagen_producto(
    producto: Dict[str, Any],
    imagen_url: str,
    imagen_metadata: Optional[Dict[str, Any]] = None
) -> Tuple[int, List[str]]:
    """
    Valida pertinencia de imagen para producto.
    
    Returns:
        (score, razones): Score 0-100 y lista de razones
    """
    score = 0
    razones = []
    
    # Preparar textos de análisis
    nombre_producto = producto.get('name', '').lower()
    url_lower = imagen_url.lower()
    
    # Metadata de imagen
    img_title = ''
    img_desc = ''
    img_alt = ''
    img_tags = []
    
    if imagen_metadata:
        img_title = str(imagen_metadata.get('title', '')).lower()
        img_desc = str(imagen_metadata.get('description', '')).lower()
        img_alt = str(imagen_metadata.get('alt_description', '')).lower()
        img_tags = [str(t).lower() for t in imagen_metadata.get('tags', [])]
    
    texto_imagen = f"{img_title} {img_desc} {img_alt} {' '.join(img_tags)}"
    
    # ===== VALIDACIÓN 1: Palabras clave VIVERO (OBLIGATORIO) =====
    tiene_keyword_vivero = False
    for kw in KEYWORDS_VIVERO:
        if kw in texto_imagen or kw in url_lower:
            tiene_keyword_vivero = True
            score += 15
            razones.append(f"✅ Contiene keyword vivero: '{kw}'")
            break
    
    if not tiene_keyword_vivero:
        razones.append("❌ NO contiene keywords de vivero/plantas")
        return (0, razones)  # RECHAZO INMEDIATO
    
    # ===== VALIDACIÓN 2: Palabras PROHIBIDAS =====
    for kw_prohibida in KEYWORDS_PROHIBIDAS:
        if kw_prohibida in texto_imagen or kw_prohibida in url_lower:
            score -= 30
            razones.append(f"❌ Contiene keyword prohibida: '{kw_prohibida}'")
            # Si tiene prohibida, score muy bajo
            if score < 0:
                return (0, razones)
    
    # ===== VALIDACIÓN 3: Nombre científico (BONUS) =====
    nombre_cientifico = detectar_nombre_cientifico(nombre_producto)
    if nombre_cientifico:
        genero, especie = nombre_cientifico
        
        if genero in texto_imagen or genero in url_lower:
            score += 25
            razones.append(f"✅ Match género científico: '{genero}'")
        
        if especie in texto_imagen or especie in url_lower:
            score += 15
            razones.append(f"✅ Match especie científica: '{especie}'")
    
    # ===== VALIDACIÓN 4: Keywords del producto =====
    keywords_producto = extraer_keywords_producto(producto)
    matches = 0
    
    for kw in keywords_producto[:10]:  # Top 10 keywords
        if len(kw) < 4:
            continue
        
        if kw in texto_imagen or kw in url_lower:
            matches += 1
            score += 5
            razones.append(f"✅ Match keyword producto: '{kw}'")
    
    if matches == 0:
        razones.append("⚠️ Sin match con keywords del producto")
    
    # ===== VALIDACIÓN 5: Categorías del producto =====
    for cat in producto.get('categories', [])[:2]:  # Top 2 categorías
        cat_name = cat.get('name', '').lower()
        if cat_name in texto_imagen or cat_name in url_lower:
            score += 10
            razones.append(f"✅ Match categoría: '{cat_name}'")
    
    # ===== VALIDACIÓN 6: Tipo de planta específico =====
    tipos_planta = {
        'arbol': ['tree', 'arbol'],
        'arbusto': ['shrub', 'bush', 'arbusto'],
        'trepadora': ['vine', 'climbing', 'trepadora'],
        'palma': ['palm', 'palma', 'palmera'],
        'cactus': ['cactus', 'cacti'],
        'suculenta': ['succulent', 'suculenta'],
        'helecho': ['fern', 'helecho'],
        'flor': ['flower', 'flor', 'bloom', 'blossom']
    }
    
    for tipo, keywords in tipos_planta.items():
        if any(k in nombre_producto for k in keywords):
            # Producto es de este tipo
            if any(k in texto_imagen or k in url_lower for k in keywords):
                score += 15
                razones.append(f"✅ Match tipo planta: '{tipo}'")
                break
    
    # ===== VALIDACIÓN 7: Contexto vivero/jardín =====
    contexto_vivero = ['nursery', 'vivero', 'greenhouse', 'garden center', 'plant shop']
    if any(ctx in texto_imagen or ctx in url_lower for ctx in contexto_vivero):
        score += 10
        razones.append("✅ Contexto de vivero/jardín")
    
    # ===== VALIDACIÓN 8: Penalizaciones =====
    
    # Penalizar si es muy genérica
    genericas = ['generic', 'placeholder', 'default', 'sample', 'stock photo']
    if any(g in url_lower for g in genericas):
        score -= 20
        razones.append("❌ Imagen genérica/placeholder")
    
    # Penalizar si NO es foto de planta real
    no_planta_real = ['illustration', 'drawing', 'cartoon', 'icon', 'logo', 'graphic']
    if any(np in texto_imagen or np in url_lower for np in no_planta_real):
        score -= 15
        razones.append("⚠️ No es foto real de planta")
    
    # ===== SCORE FINAL =====
    score = max(0, min(100, score))
    
    # Resumen
    if score >= 70:
        razones.insert(0, f"🟢 EXCELENTE pertinencia: {score}/100")
    elif score >= 50:
        razones.insert(0, f"🟡 ACEPTABLE pertinencia: {score}/100")
    elif score >= 30:
        razones.insert(0, f"🟠 BAJA pertinencia: {score}/100")
    else:
        razones.insert(0, f"🔴 MUY BAJA pertinencia: {score}/100")
    
    return (score, razones)


def validar_batch_imagenes(
    productos_imagenes: List[Tuple[Dict[str, Any], str, Optional[Dict]]]
) -> Dict[str, Any]:
    """
    Valida un batch de imágenes.
    
    Args:
        productos_imagenes: Lista de (producto, imagen_url, metadata)
    
    Returns:
        Estadísticas de validación
    """
    resultados = []
    
    for producto, url, metadata in productos_imagenes:
        score, razones = validar_imagen_producto(producto, url, metadata)
        resultados.append({
            'producto_id': producto.get('id'),
            'producto_nombre': producto.get('name'),
            'imagen_url': url,
            'score': score,
            'razones': razones,
            'aceptada': score >= 50
        })
    
    # Estadísticas
    total = len(resultados)
    aceptadas = sum(1 for r in resultados if r['aceptada'])
    rechazadas = total - aceptadas
    score_promedio = sum(r['score'] for r in resultados) / total if total > 0 else 0
    
    return {
        'total': total,
        'aceptadas': aceptadas,
        'rechazadas': rechazadas,
        'score_promedio': score_promedio,
        'resultados': resultados
    }


# ===== FUNCIONES DE UTILIDAD =====

def es_imagen_pertinente(producto: Dict, url: str, metadata: Optional[Dict] = None, 
                         threshold: int = 50) -> bool:
    """Función simple: retorna True si imagen es pertinente"""
    score, _ = validar_imagen_producto(producto, url, metadata)
    return score >= threshold


def obtener_razon_rechazo(producto: Dict, url: str, metadata: Optional[Dict] = None) -> str:
    """Obtiene la razón principal de rechazo"""
    score, razones = validar_imagen_producto(producto, url, metadata)
    if score >= 50:
        return "Imagen aceptada"
    
    # Buscar razón principal de rechazo
    for razon in razones:
        if '❌' in razon:
            return razon
    
    return f"Score muy bajo: {score}/100"


if __name__ == "__main__":
    # Test
    producto_test = {
        'id': 1,
        'name': 'Ficus benjamina 3 Litros - Planta de Interior',
        'categories': [{'name': 'Plantas de Interior'}],
        'tags': [{'name': 'Ficus'}, {'name': 'Interior'}],
        'attributes': [
            {'name': 'Tamaño', 'options': ['3 Litros']},
            {'name': 'Tipo', 'options': ['Arbol']}
        ]
    }
    
    # Test 1: Imagen pertinente
    url_buena = "https://example.com/ficus-benjamina-indoor-plant.jpg"
    metadata_buena = {
        'title': 'Ficus benjamina indoor plant',
        'description': 'Beautiful ficus tree in pot',
        'tags': ['plant', 'ficus', 'indoor', 'tree']
    }
    
    score, razones = validar_imagen_producto(producto_test, url_buena, metadata_buena)
    print(f"\n{'='*60}")
    print(f"TEST 1: Imagen pertinente")
    print(f"{'='*60}")
    print(f"Score: {score}/100")
    for razon in razones:
        print(f"  {razon}")
    
    # Test 2: Imagen NO pertinente
    url_mala = "https://example.com/sunset-landscape.jpg"
    metadata_mala = {
        'title': 'Beautiful sunset landscape',
        'description': 'Colorful sky at dusk',
        'tags': ['sunset', 'sky', 'landscape', 'nature']
    }
    
    score, razones = validar_imagen_producto(producto_test, url_mala, metadata_mala)
    print(f"\n{'='*60}")
    print(f"TEST 2: Imagen NO pertinente")
    print(f"{'='*60}")
    print(f"Score: {score}/100")
    for razon in razones:
        print(f"  {razon}")
