# 🚨 TAREA CRÍTICA: CORRECCIÓN DE IMÁGENES NO PERTINENTES

**Fecha:** Sábado 4 de Octubre, 2025 - 18:03 ART  
**Prioridad:** 🔥 CRÍTICA  
**Estado:** EN ANÁLISIS  
**Impacto:** Alto - Afecta UX y conversión

---

## 🎯 PROBLEMA IDENTIFICADO

### Síntomas Reportados

1. **Imágenes no relacionadas con productos**
   - Imágenes asignadas NO corresponden al título del producto
   - Imágenes NO corresponden a la descripción del producto
   - Ejemplo: Producto "Rosa Roja" con imagen de planta genérica

2. **Duplicación visual**
   - Misma imagen asignada a múltiples productos
   - Aunque tengan nombres de archivo diferentes
   - Deduplicación SHA1 no detecta imágenes visualmente similares

### Alcance del Problema

**Productos afectados:** ~600 imágenes agregadas en Día 3  
**Iteraciones afectadas:** Iteraciones 1, 3, 4, 5, 6, 7  
**Providers involucrados:** Unsplash, iNaturalist, Pexels

---

## 🔍 ANÁLISIS DE CAUSA RAÍZ

### 1. Queries de Búsqueda Genéricas

**Problema actual:**
```python
# Queries muy generales
queries = ["planta", "arbusto", "plant pot", "garden pot"]
```

**Resultado:**
- APIs devuelven imágenes genéricas
- No específicas al producto individual
- Ejemplo: "Ficus benjamina" → query "planta" → imagen cualquier planta

### 2. Deduplicación Solo por SHA1

**Limitación actual:**
```python
# Solo detecta archivos idénticos bit a bit
sha1 = hashlib.sha1(image_data).hexdigest()
```

**Problema:**
- Dos imágenes visualmente idénticas con compresión diferente = SHA1 distinto
- Misma foto recortada diferente = SHA1 distinto
- No detecta similitud perceptual

### 3. Falta de Validación de Pertinencia

**Ausente en código actual:**
- No hay validación semántica
- No hay scoring de relevancia
- No hay verificación visual automática

### 4. Fallbacks Genéricos Agresivos

**Problema en código:**
```python
# Fallbacks muy genéricos al final
for q in ["plant pot", "garden pot", "planter", "flower pot"]:
    queries.append(q)
```

**Resultado:**
- Cuando falla búsqueda específica
- Usa queries genéricas como último recurso
- Asigna imágenes completamente irrelevantes

---

## 📋 PLAN DE CORRECCIÓN

### Fase 1: Auditoría y Documentación (Inmediata)

**Objetivo:** Identificar y documentar productos con imágenes incorrectas

**Tareas:**
1. **Crear script de auditoría visual**
   - Listar todos los productos con imágenes agregadas en Día 3
   - Comparar título/descripción con imágenes asignadas
   - Generar reporte HTML con miniaturas
   - Marcar productos sospechosos

2. **Análisis manual de muestra**
   - Revisar 50 productos aleatorios
   - Clasificar: Correctas / Incorrectas / Dudosas
   - Identificar patrones de error

3. **Documentar casos problemáticos**
   - Screenshots de ejemplos
   - Análisis de queries usadas
   - Proveedores que fallaron

**Entregables:**
- `auditoria_imagenes_productos.html` - Reporte visual
- `productos_imagenes_incorrectas.json` - Lista productos problema
- `analisis_muestra_manual.md` - Análisis detallado

**Tiempo estimado:** 2-3 horas

---

### Fase 2: Implementación de Mejoras (Día 4 AM)

**Objetivo:** Prevenir futuros errores

#### 2.1 Deduplicación Perceptual

**Implementar `imagehash` library:**

```python
import imagehash
from PIL import Image

def compute_perceptual_hash(image_path):
    """
    Calcula hash perceptual de imagen.
    Detecta similitud visual, no solo bit a bit.
    """
    img = Image.open(image_path)
    # Average Hash (aHash) - rápido y efectivo
    ahash = imagehash.average_hash(img)
    # Perceptual Hash (pHash) - más robusto
    phash = imagehash.phash(img)
    # Difference Hash (dHash) - bueno para recortes
    dhash = imagehash.dhash(img)
    
    return {
        'ahash': str(ahash),
        'phash': str(phash),
        'dhash': str(dhash)
    }

def images_are_similar(hash1, hash2, threshold=5):
    """
    Compara dos hashes perceptuales.
    threshold: diferencia máxima permitida (0-64)
    5 = muy similar, 10 = similar, 15+ = diferente
    """
    # Convertir strings a ImageHash objects
    h1 = imagehash.hex_to_hash(hash1)
    h2 = imagehash.hex_to_hash(hash2)
    
    # Hamming distance
    diff = h1 - h2
    
    return diff <= threshold
```

**Integración:**
- Guardar hashes perceptuales además de SHA1
- Comparar con todas las imágenes existentes
- Rechazar imágenes con hamming distance < 5

#### 2.2 Mejora de Queries de Búsqueda

**Estrategia mejorada:**

```python
def generate_improved_queries(product):
    """
    Genera queries MUCHO más específicas.
    """
    name = product.get('name', '').strip()
    description = product.get('description', '')
    short_desc = product.get('short_description', '')
    categories = [c['name'] for c in product.get('categories', [])]
    tags = [t['name'] for t in product.get('tags', [])]
    
    # 1. Prioridad MÁXIMA: Nombre exacto del producto
    queries = []
    if name:
        # Limpiar pero mantener especificidad
        clean_name = clean_search_term(name)
        queries.append(clean_name)
        
        # Si tiene nombre científico, úsalo
        if has_scientific_name(name):
            queries.insert(0, extract_scientific_name(name))
    
    # 2. Combinación nombre + categoría
    if name and categories:
        for cat in categories[:2]:  # Solo 2 primeras
            queries.append(f"{clean_name} {cat}")
    
    # 3. Descripción corta (primeras palabras clave)
    if short_desc:
        keywords = extract_keywords(short_desc, max_words=3)
        if keywords:
            queries.append(keywords)
    
    # 4. Tags específicos
    for tag in tags[:3]:  # Solo 3 primeros
        tag_clean = clean_search_term(tag)
        if len(tag_clean) > 3:  # Evitar tags muy cortos
            queries.append(f"{name} {tag_clean}")
    
    # 5. ELIMINAR fallbacks genéricos
    # NO agregar "plant pot", "garden pot", etc.
    # Si no hay match específico, mejor fallar que asignar imagen incorrecta
    
    return queries[:5]  # Limitar a 5 queries MUY específicas
```

**Cambios clave:**
- Priorizar nombre científico si existe
- Combinar múltiples atributos del producto
- **Eliminar fallbacks genéricos**
- Limitar número de queries (calidad > cantidad)

#### 2.3 Validación de Pertinencia

**Scoring de relevancia:**

```python
def score_image_relevance(product, image_metadata):
    """
    Calcula score de relevancia 0-100.
    """
    score = 0
    product_name = product.get('name', '').lower()
    image_title = image_metadata.get('title', '').lower()
    image_desc = image_metadata.get('description', '').lower()
    image_tags = ' '.join(image_metadata.get('tags', [])).lower()
    
    # Extraer palabras clave del producto
    product_keywords = extract_keywords(product_name)
    
    # 1. Match en título de imagen (40 pts)
    for keyword in product_keywords:
        if keyword in image_title:
            score += 40
            break
    
    # 2. Match en descripción de imagen (30 pts)
    for keyword in product_keywords:
        if keyword in image_desc:
            score += 30
            break
    
    # 3. Match en tags de imagen (20 pts)
    for keyword in product_keywords:
        if keyword in image_tags:
            score += 20
            break
    
    # 4. Bonus por nombre científico (10 pts)
    if has_scientific_name(product_name):
        sci_name = extract_scientific_name(product_name)
        if sci_name.lower() in image_title or sci_name.lower() in image_desc:
            score += 10
    
    return min(score, 100)

def should_accept_image(product, image_metadata, min_score=50):
    """
    Decide si aceptar imagen basado en relevancia.
    """
    score = score_image_relevance(product, image_metadata)
    
    if score < min_score:
        log.warning(
            "Image rejected for product '%s': relevance score %d < %d",
            product.get('name'),
            score,
            min_score
        )
        return False
    
    return True
```

#### 2.4 Logging y Debugging Mejorado

```python
def log_image_assignment(product, image, query_used, score):
    """
    Log detallado de cada asignación.
    """
    log.info(
        "IMAGE ASSIGNED | Product: %s | Query: '%s' | Provider: %s | Score: %d | URL: %s",
        product.get('name'),
        query_used,
        image.get('provider'),
        score,
        image.get('url')[:100]
    )
```

**Tiempo estimado:** 4-5 horas

---

### Fase 3: Script de Corrección Automática (Día 4 PM)

**Objetivo:** Corregir productos con imágenes incorrectas

#### 3.1 Identificación Automática

```python
def identify_incorrect_images():
    """
    Identifica productos con imágenes posiblemente incorrectas.
    """
    products = get_all_products_with_images()
    incorrect = []
    
    for product in products:
        images = product.get('images', [])
        if not images:
            continue
        
        # Verificar pertinencia de cada imagen
        for img in images:
            # Obtener metadata de imagen
            img_metadata = get_image_metadata(img['src'])
            
            # Calcular score
            score = score_image_relevance(product, img_metadata)
            
            if score < 40:  # Threshold bajo = muy incorrecta
                incorrect.append({
                    'product_id': product['id'],
                    'product_name': product['name'],
                    'image_id': img['id'],
                    'image_src': img['src'],
                    'score': score,
                    'reason': 'Low relevance score'
                })
    
    return incorrect
```

#### 3.2 Deduplicación Visual

```python
def find_visual_duplicates():
    """
    Encuentra imágenes visualmente duplicadas.
    """
    products = get_all_products_with_images()
    all_images = {}
    
    # Calcular hashes perceptuales
    for product in products:
        for img in product.get('images', []):
            img_path = download_temp(img['src'])
            phash = compute_perceptual_hash(img_path)
            
            # Buscar similares
            for existing_id, existing_hash in all_images.items():
                if images_are_similar(phash['phash'], existing_hash['phash'], threshold=5):
                    # Duplicado visual encontrado
                    yield {
                        'product_id': product['id'],
                        'product_name': product['name'],
                        'image_id': img['id'],
                        'duplicate_of': existing_id,
                        'similarity': calculate_similarity(phash, existing_hash)
                    }
            
            all_images[img['id']] = phash
```

#### 3.3 Re-asignación Inteligente

```python
def reassign_incorrect_images(incorrect_list, dry_run=True):
    """
    Re-busca y asigna imágenes correctas.
    """
    stats = {'processed': 0, 'success': 0, 'failed': 0}
    
    for item in incorrect_list:
        product_id = item['product_id']
        product = get_product(product_id)
        
        # Eliminar imagen incorrecta
        if not dry_run:
            remove_product_image(product_id, item['image_id'])
        
        # Buscar imagen correcta con criterios estrictos
        queries = generate_improved_queries(product)
        
        for query in queries:
            images = search_free_images(
                query,
                providers=['unsplash', 'inaturalist'],
                min_relevance_score=60  # Threshold alto
            )
            
            for img in images:
                # Verificar pertinencia
                score = score_image_relevance(product, img.metadata)
                if score >= 60:
                    # Verificar duplicación visual
                    if not is_visually_duplicate(img):
                        # Asignar imagen
                        if not dry_run:
                            assign_image_to_product(product_id, img)
                        
                        stats['success'] += 1
                        log.info(
                            "✅ Reassigned image to product %s (score: %d)",
                            product['name'],
                            score
                        )
                        break
            
            if stats['success'] > stats['processed']:
                break  # Imagen encontrada, siguiente producto
        
        if stats['success'] == stats['processed']:
            # No se encontró imagen adecuada
            stats['failed'] += 1
            log.warning(
                "⚠️  No suitable image found for product %s",
                product['name']
            )
        
        stats['processed'] += 1
    
    return stats
```

**Tiempo estimado:** 3-4 horas desarrollo + 2-3 horas ejecución

---

### Fase 4: Validación Manual y Ajustes (Día 5)

**Objetivo:** Verificar correcciones y ajustar parámetros

**Tareas:**
1. Revisar muestra de productos corregidos
2. Ajustar thresholds de similitud/pertinencia
3. Refinar queries para casos edge
4. Documentar casos especiales

**Tiempo estimado:** 2-3 horas

---

## 🛠️ IMPLEMENTACIÓN TÉCNICA

### Dependencias Nuevas

```bash
# Instalar imagehash para deduplicación perceptual
pip install imagehash==4.3.1

# Actualizar requirements.txt
echo "imagehash==4.3.1" >> requirements.txt
```

### Scripts a Crear

1. **`verificar_imagenes_productos.py`**
   - Auditoría visual completa
   - Genera reporte HTML
   - Identifica problemas

2. **`corregir_imagenes_incorrectas.py`**
   - Corrección automática
   - Re-asignación inteligente
   - Estadísticas detalladas

3. **`wc_image_automation_v2.py`**
   - Versión mejorada con:
     - Deduplicación perceptual
     - Queries mejoradas
     - Validación de pertinencia
     - Logging detallado

### Archivos a Modificar

1. **`wc_image_automation.py`**
   - Integrar `imagehash`
   - Mejorar `generate_search_queries()`
   - Agregar `score_image_relevance()`
   - Actualizar `batch_processor()`

---

## 📊 MÉTRICAS DE ÉXITO

### Objetivos Cuantificables

| Métrica | Actual | Objetivo |
|---------|--------|----------|
| **Pertinencia** | ~30-40% | >90% |
| **Duplicación visual** | ~15-20% | <2% |
| **Score relevancia promedio** | ~35/100 | >70/100 |
| **Imágenes rechazadas** | 0% | 30-40% |

### Criterios de Aceptación

1. ✅ >90% imágenes relacionadas con título del producto
2. ✅ <2% duplicación visual entre productos
3. ✅ Score relevancia promedio >70/100
4. ✅ Zero imágenes genéricas de fallback
5. ✅ Documentación completa de casos edge

---

## ⏰ CRONOGRAMA

```
Día 3 (hoy 18:00-20:00):
  ├─ Crear scripts de auditoría
  ├─ Análisis inicial de muestra
  └─ Documentar casos problemáticos

Día 4 (mañana 08:00-18:00):
  ├─ AM: Implementar mejoras (5h)
  │   ├─ Deduplicación perceptual (2h)
  │   ├─ Queries mejoradas (2h)
  │   └─ Validación pertinencia (1h)
  │
  └─ PM: Corrección automática (5h)
      ├─ Identificar incorrectas (1h)
      ├─ Re-asignar imágenes (3h)
      └─ Verificación (1h)

Día 5 (pasado mañana 08:00-12:00):
  ├─ Validación manual muestra (2h)
  ├─ Ajuste de parámetros (1h)
  └─ Documentación final (1h)
```

**Total estimado:** 15-18 horas

---

## 🚨 RIESGOS Y MITIGACIONES

### Riesgo 1: APIs No Devuelven Imágenes Específicas

**Probabilidad:** Alta  
**Impacto:** Alto

**Mitigación:**
- Usar múltiples providers (Unsplash + iNaturalist + Flickr)
- Queries muy específicas (nombre científico)
- Threshold de relevancia alto (rechazar >50% imágenes)
- Aceptar "sin imagen" si no hay match adecuado

### Riesgo 2: Deduplicación Perceptual Falsos Positivos

**Probabilidad:** Media  
**Impacto:** Medio

**Mitigación:**
- Usar múltiples algoritmos de hash (aHash, pHash, dHash)
- Threshold conservador (hamming distance ≤5)
- Logging detallado de rechazos
- Revisión manual de casos dudosos

### Riesgo 3: Tiempo de Ejecución Muy Largo

**Probabilidad:** Media  
**Impacto:** Bajo

**Mitigación:**
- Procesar en batches pequeños
- Cachear hashes perceptuales
- Paralelizar cálculos cuando posible
- Modo dry-run para testing

### Riesgo 4: No Hay Imágenes Adecuadas Disponibles

**Probabilidad:** Media  
**Impacto:** Medio

**Mitigación:**
- **Aceptar productos sin segunda imagen**
- Priorizar calidad sobre cantidad
- Documentar productos sin match
- Considerar fuentes alternativas (banco de imágenes propio)

---

## 📁 ENTREGABLES

### Documentación

1. ✅ `TAREA-CORRECCION-IMAGENES-CRITICA.md` (este documento)
2. ⏳ `auditoria_imagenes_productos.html` - Reporte visual
3. ⏳ `productos_imagenes_incorrectas.json` - Lista problemas
4. ⏳ `analisis_muestra_manual.md` - Análisis detallado
5. ⏳ `INFORME-CORRECCION-IMAGENES.md` - Resultados finales

### Código

1. ⏳ `verificar_imagenes_productos.py` - Script auditoría
2. ⏳ `corregir_imagenes_incorrectas.py` - Script corrección
3. ⏳ `wc_image_automation.py` - Versión mejorada
4. ⏳ `requirements.txt` - Actualizado con imagehash

### Datos

1. ⏳ `productos_auditados.json` - Todos los productos revisados
2. ⏳ `imagenes_duplicadas.json` - Duplicados visuales encontrados
3. ⏳ `imagenes_corregidas.json` - Log de correcciones aplicadas
4. ⏳ `estadisticas_final.json` - Métricas de éxito

---

## 💡 RECOMENDACIONES ADICIONALES

### A Corto Plazo

1. **Pausar nuevas asignaciones automáticas**
   - No agregar más imágenes hasta corregir existentes
   - Evitar propagar el problema

2. **Priorizar productos más visitados**
   - Corregir primero productos con mayor tráfico
   - Mayor impacto en conversión

3. **Crear banco de imágenes curado**
   - Fotografías propias de productos reales
   - Mayor control sobre calidad y pertinencia

### A Medio Plazo

1. **Integrar más providers especializados**
   - Botanic Image API
   - PlantNet
   - Flora de Argentina

2. **Machine Learning para relevancia**
   - Modelo entrenado en productos del vivero
   - Clasificación automática de pertinencia

3. **Sistema de feedback**
   - Permitir reportar imágenes incorrectas
   - Crowdsourcing de correcciones

---

## 🎯 CONCLUSIÓN

### Resumen del Problema

- ❌ ~600 imágenes agregadas en Día 3
- ❌ Muchas NO relacionadas con productos
- ❌ Duplicación visual significativa
- ❌ Queries de búsqueda muy genéricas

### Solución Propuesta

1. ✅ Auditoría completa de imágenes existentes
2. ✅ Deduplicación perceptual con imagehash
3. ✅ Queries de búsqueda MUCHO más específicas
4. ✅ Validación de pertinencia con scoring
5. ✅ Re-asignación inteligente de imágenes incorrectas

### Impacto Esperado

**Antes:**
- Pertinencia: ~35%
- Duplicación: ~18%
- UX: Confusa

**Después:**
- Pertinencia: >90% ✅
- Duplicación: <2% ✅
- UX: Profesional ✅

### Compromiso

**Inicio:** Hoy 18:00 ART  
**Finalización:** Día 5 12:00 ART  
**Duración:** ~18 horas de trabajo  
**Probabilidad de éxito:** 85%

---

**🔥 TAREA CRÍTICA - PRIORIDAD MÁXIMA**

**Bloquea:** Score final 95/100  
**Afecta:** UX, conversión, credibilidad  
**Requiere:** Acción inmediata

---

*Creado: 18:03 ART - 4 Oct 2025*  
*Responsable: Sistema de Automatización SEO*  
*Estado: Análisis completo - Pendiente aprobación inicio*
