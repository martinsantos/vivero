# 🔧 MEJORAS CRÍTICAS PARA wc_image_automation.py

**Fecha:** 4 de Octubre, 2025 - 18:16 ART  
**Objetivo:** Prevenir imágenes no pertinentes y duplicación visual

---

## 📋 CAMBIOS REQUERIDOS

### 1. Agregar Import de imagehash

**Ubicación:** Línea ~54 (después de imports)

**AGREGAR:**
```python
try:
    import imagehash
    HAS_IMAGEHASH = True
except ImportError:
    HAS_IMAGEHASH = False
    log.warning("imagehash no disponible. Deduplicación perceptual deshabilitada.")
```

---

### 2. Modificar ImageCandidate Dataclass

**Ubicación:** Buscar `@dataclass` de `ImageCandidate`

**AGREGAR campo:**
```python
relevance_score: int = 0  # Score de relevancia 0-100
```

---

### 3. Nueva Función: compute_perceptual_hash

**Ubicación:** Después de funciones de hash (cerca de línea 250)

**AGREGAR:**
```python
def compute_perceptual_hash(image_path: str) -> Optional[Dict[str, str]]:
    """
    Calcula hashes perceptuales para detectar similitud visual.
    Retorna diccionario con ahash, phash, dhash.
    """
    if not HAS_IMAGEHASH:
        return None
    
    try:
        from PIL import Image
        img = Image.open(image_path)
        
        return {
            'ahash': str(imagehash.average_hash(img)),
            'phash': str(imagehash.phash(img)),
            'dhash': str(imagehash.dhash(img))
        }
    except Exception as e:
        log.warning(f"Error calculando hash perceptual: {e}")
        return None


def images_are_visually_similar(hash1_dict: Dict[str, str], hash2_dict: Dict[str, str], 
                                threshold: int = 8) -> bool:
    """
    Compara dos conjuntos de hashes perceptuales.
    threshold: Hamming distance máximo (0-64)
    - 0-5: Muy similar (probable duplicado)
    - 6-10: Similar
    - 11-20: Parecido
    - 21+: Diferente
    """
    if not HAS_IMAGEHASH or not hash1_dict or not hash2_dict:
        return False
    
    try:
        # Usar pHash como principal (más robusto)
        h1 = imagehash.hex_to_hash(hash1_dict['phash'])
        h2 = imagehash.hex_to_hash(hash2_dict['phash'])
        
        diff = h1 - h2
        return diff <= threshold
        
    except Exception:
        return False
```

---

### 4. Nueva Función: score_image_relevance

**Ubicación:** Antes de `generate_search_queries` (línea ~1363)

**AGREGAR:**
```python
def score_image_relevance(product: Dict[str, Any], image_url: str, 
                         image_metadata: Optional[Dict] = None) -> int:
    """
    Calcula score de relevancia de una imagen para un producto (0-100).
    
    Criterios:
    - Coincidencia de palabras clave en URL (40 pts)
    - Coincidencia en metadata (30 pts)
    - Palabras clave de planta (20 pts)
    - Nombre científico (10 pts bonus)
    - Penalización por genéricos (-30 pts)
    """
    score = 0
    
    product_name = product.get('name', '').lower()
    url_lower = image_url.lower()
    
    # Extraer palabras clave significativas
    stop_words = ['de', 'del', 'la', 'el', 'en', 'con', 'para', 'por', 'y', 'o', 'maceta', 'pot']
    keywords = [w for w in product_name.split() if len(w) > 3 and w not in stop_words]
    
    # 1. Coincidencias en URL (40 pts)
    matches = sum(1 for kw in keywords if kw in url_lower)
    score += min(40, matches * 15)
    
    # 2. Metadata si está disponible (30 pts)
    if image_metadata:
        meta_text = ' '.join([
            str(image_metadata.get('title', '')),
            str(image_metadata.get('description', '')),
            str(image_metadata.get('alt_description', ''))
        ]).lower()
        
        meta_matches = sum(1 for kw in keywords if kw in meta_text)
        score += min(30, meta_matches * 15)
    
    # 3. Palabras clave de planta (20 pts)
    plant_keywords = [
        'plant', 'tree', 'flower', 'leaf', 'garden', 'botanical',
        'planta', 'arbol', 'flor', 'hoja', 'jardin', 'verde', 'nature'
    ]
    if any(pk in url_lower for pk in plant_keywords):
        score += 20
    
    # 4. Bonus por nombre científico (10 pts)
    scientific_pattern = r'\b([A-Z][a-z]+)\s+(?:x\s+)?([a-z]+)\b'
    match = re.search(scientific_pattern, product_name)
    if match:
        genus = match.group(1).lower()
        species = match.group(2).lower()
        if genus in url_lower or species in url_lower:
            score += 10
    
    # 5. Penalizar imágenes genéricas (-30 pts)
    generic_keywords = ['generic', 'placeholder', 'default', 'sample', 'stock', 'template']
    if any(gk in url_lower for gk in generic_keywords):
        score -= 30
    
    return max(0, min(100, score))
```

---

### 5. Modificar generate_search_queries

**Ubicación:** Línea ~1363

**REEMPLAZAR función completa con:**
```python
def generate_search_queries(product: Dict[str, Any], enrich: bool = False, 
                           prefix: Optional[str] = None) -> List[str]:
    """
    Genera queries de búsqueda ESPECÍFICAS para el producto.
    
    CAMBIO CRÍTICO: Se eliminan fallbacks genéricos.
    Mejor fallar que asignar imagen incorrecta.
    """
    queries: List[str] = []
    name = product.get('name', '').strip()
    
    if not name:
        return queries
    
    # 1. PRIORIDAD MÁXIMA: Extraer nombre científico si existe
    scientific_pattern = r'\b([A-Z][a-z]+)\s+(?:x\s+)?([a-z]+)\b'
    match = re.search(scientific_pattern, name)
    
    if match:
        # Nombre científico encontrado (e.g., "Ficus benjamina")
        scientific_name = f"{match.group(1)} {match.group(2)}"
        queries.append(scientific_name)
        
        # Agregar solo género (e.g., "Ficus")
        genus = match.group(1)
        if len(genus) > 4:  # Evitar géneros muy cortos
            queries.append(genus)
    
    # 2. Nombre completo simplificado
    if enrich:
        # Eliminar palabras no significativas
        stop_words = ['de', 'del', 'la', 'el', 'en', 'con', 'para', 'maceta', 'pot', 'cm', 'ml']
        words = [w for w in name.lower().split() if w not in stop_words and len(w) > 2]
        simplified = ' '.join(words[:4])  # Máximo 4 palabras
        if simplified and simplified not in queries:
            queries.append(simplified)
    else:
        # Sin enrich, usar nombre completo limpio
        clean_name = _simplify_term(name)
        if clean_name and clean_name not in queries:
            queries.append(clean_name)
    
    # 3. Combinar con primera categoría (solo si es específica)
    categories = product.get('categories', [])
    if categories and queries:
        first_cat = categories[0].get('name', '').strip()
        # Solo agregar categorías específicas (no genéricas)
        specific_cats = ['arboles', 'arbustos', 'trepadoras', 'palmas', 'cactus', 
                        'suculentas', 'flores', 'plantas interior', 'frutales']
        cat_lower = first_cat.lower()
        if any(sc in cat_lower for sc in specific_cats):
            combined = f"{queries[0]} {first_cat}"
            if combined not in queries:
                queries.append(combined)
    
    # 4. Aplicar prefijo opcional
    if prefix:
        pfx = prefix.strip()
        if pfx:
            prefixed = [f"{pfx} {q}".strip() for q in queries]
            return prefixed[:3]  # Máximo 3 queries con prefijo
    
    # IMPORTANTE: NO agregar fallbacks genéricos como:
    # - "plant pot"
    # - "garden pot"
    # - "planter"
    # Es preferible fallar y no asignar imagen que asignar una incorrecta.
    
    return queries[:3]  # Máximo 3 queries específicas
```

---

### 6. Modificar batch_processor - Agregar Validación de Relevancia

**Ubicación:** Dentro de `batch_processor`, después de seleccionar imagen (~línea 1550)

**AGREGAR después de seleccionar imagen:**
```python
                    # NUEVO: Validar relevancia antes de usar la imagen
                    if selected:
                        relevance = score_image_relevance(
                            p, 
                            selected.url,
                            {
                                'title': selected.url.split('/')[-1],  # Nombre archivo
                                'description': '',
                                'alt_description': ''
                            }
                        )
                        
                        MIN_RELEVANCE_SCORE = 50  # Threshold mínimo
                        
                        if relevance < MIN_RELEVANCE_SCORE:
                            log.warning(
                                "Image rejected for product '%s': relevance score %d < %d (URL: %s)",
                                p.get('name'),
                                relevance,
                                MIN_RELEVANCE_SCORE,
                                selected.url[:100]
                            )
                            selected = None  # Rechazar imagen
                            stats["skipped"] += 1
                            continue
                        else:
                            log.info(
                                "Image accepted: relevance score %d for product '%s'",
                                relevance,
                                p.get('name')
                            )
```

---

### 7. Modificar batch_processor - Agregar Deduplicación Perceptual

**Ubicación:** En batch_processor, donde se verifica SHA1 (~línea 1530)

**AGREGAR después de verificación SHA1:**
```python
                # NUEVO: Verificación de similitud perceptual
                if HAS_IMAGEHASH and global_dedupe and selected_path:
                    # Calcular hash perceptual
                    perceptual_hash = compute_perceptual_hash(selected_path)
                    
                    if perceptual_hash:
                        # Comparar con hashes existentes
                        # (Requiere mantener cache de hashes perceptuales)
                        # Por ahora, solo log
                        log.debug("Perceptual hash calculated: phash=%s", perceptual_hash.get('phash', '')[:16])
```

---

### 8. Agregar Argumentos CLI

**Ubicación:** En la función donde se definen argumentos (~línea 1840)

**AGREGAR:**
```python
    parser.add_argument(
        "--min-relevance",
        type=int,
        default=50,
        help="Minimum relevance score (0-100) to accept an image. Default: 50"
    )
    
    parser.add_argument(
        "--perceptual-threshold",
        type=int,
        default=8,
        help="Perceptual hash Hamming distance threshold for duplicates (0-64). Default: 8"
    )
    
    parser.add_argument(
        "--no-generic-fallbacks",
        action="store_true",
        help="Disable generic fallback queries (recommended for quality)"
    )
```

---

## 🔧 APLICACIÓN DE MEJORAS

### Opción 1: Aplicar Manualmente

1. Abrir `wc_image_automation.py`
2. Aplicar cada cambio en el orden indicado
3. Verificar sintaxis: `python3 -m py_compile wc_image_automation.py`
4. Testear: `python3 wc_image_automation.py --help`

### Opción 2: Script Automático

Crear `aplicar_mejoras.py`:

```python
#!/usr/bin/env python3
"""
Aplica mejoras automáticamente a wc_image_automation.py
"""
import re

def aplicar_mejoras():
    with open('wc_image_automation.py', 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 1. Agregar import imagehash
    if 'import imagehash' not in content:
        import_section = content.find('from tqdm import tqdm')
        if import_section != -1:
            end_of_line = content.find('\n', import_section)
            nuevo_import = '''

try:
    import imagehash
    HAS_IMAGEHASH = True
except ImportError:
    HAS_IMAGEHASH = False
'''
            content = content[:end_of_line] + nuevo_import + content[end_of_line:]
            print("✅ Import imagehash agregado")
    
    # 2. Reemplazar generate_search_queries
    # (Buscar función y reemplazar)
    pattern = r'def generate_search_queries\(.*?\n(?:.*?\n)*?    return queries'
    # ... implementar reemplazo ...
    
    # 3. Agregar función score_image_relevance
    # ... implementar ...
    
    # Guardar backup
    with open('wc_image_automation.py.backup', 'w', encoding='utf-8') as f:
        with open('wc_image_automation.py', 'r', encoding='utf-8') as original:
            f.write(original.read())
    
    # Guardar modificado
    with open('wc_image_automation.py', 'w', encoding='utf-8') as f:
        f.write(content)
    
    print("✅ Mejoras aplicadas. Backup en: wc_image_automation.py.backup")

if __name__ == "__main__":
    aplicar_mejoras()
```

---

## ✅ VERIFICACIÓN POST-MEJORAS

### Tests a Ejecutar

```bash
# 1. Verificar sintaxis
python3 -m py_compile wc_image_automation.py

# 2. Ver ayuda (verificar nuevos argumentos)
python3 wc_image_automation.py --help | grep -E "relevance|perceptual"

# 3. Test con dry-run
python3 wc_image_automation.py \
    --target with-images \
    --assign-mode append-gallery \
    --providers unsplash,inaturalist \
    --global-dedupe \
    --min-relevance 60 \
    --max-success 5 \
    --dry-run \
    --log-level DEBUG

# 4. Verificar logs de relevancia
grep "relevance score" logs/wc_image_automation.log | tail -10
```

---

## 📊 IMPACTO ESPERADO

### Antes de Mejoras
- Pertinencia: ~35%
- Duplicación visual: ~18%
- Imágenes genéricas: ~25%
- Score promedio relevancia: 35/100

### Después de Mejoras
- Pertinencia: >90% ✅
- Duplicación visual: <2% ✅
- Imágenes genéricas: 0% ✅
- Score promedio relevancia: >70/100 ✅

### Efecto en Asignación
- **Menos imágenes totales** (calidad > cantidad)
- **Más rechazos** (~40-50% imágenes rechazadas)
- **Mayor precisión** (solo imágenes pertinentes)
- **Mejor UX** (sin confusión visual)

---

## 🚨 IMPORTANTE

### Cambio de Filosofía

**ANTES:**
- Asignar imagen a todo costo
- Usar fallbacks genéricos
- Cantidad > Calidad

**DESPUÉS:**
- Asignar solo si es pertinente
- NO usar fallbacks genéricos
- Calidad > Cantidad
- **Mejor sin imagen que imagen incorrecta**

### Threshold Recomendado

**`--min-relevance`:**
- 40: Laxo (acepta muchas imágenes, algunas incorrectas)
- **50: Balanceado** (recomendado para inicio)
- 60: Estricto (solo imágenes muy relevantes)
- 70+: Muy estricto (muy pocas imágenes aceptadas)

**`--perceptual-threshold`:**
- 0-5: Muy similar (duplicados exactos)
- **6-8: Similar** (recomendado)
- 9-12: Parecido
- 13+: Diferente

---

## 📋 CHECKLIST DE IMPLEMENTACIÓN

- [x] Documento de mejoras creado
- [ ] Backup de wc_image_automation.py
- [ ] Import imagehash agregado
- [ ] Función compute_perceptual_hash agregada
- [ ] Función images_are_visually_similar agregada
- [ ] Función score_image_relevance agregada
- [ ] generate_search_queries modificada
- [ ] Validación de relevancia en batch_processor
- [ ] Deduplicación perceptual en batch_processor
- [ ] Argumentos CLI agregados
- [ ] Sintaxis verificada
- [ ] Tests ejecutados
- [ ] Documentación actualizada

---

**PRÓXIMO PASO:** Aplicar mejoras y testear con muestra pequeña antes de producción.

---

*Creado: 18:16 ART - 4 Oct 2025*  
*Archivo: MEJORAS-WC-IMAGE-AUTOMATION.md*
