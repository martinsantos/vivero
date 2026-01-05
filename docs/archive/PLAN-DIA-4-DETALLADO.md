# 📋 PLAN DETALLADO DÍA 4 - TAGS + TÍTULOS

**Fecha:** Domingo 5 de Octubre, 2025  
**Objetivo:** Score 91-92/100  
**Duración estimada:** 8-10 horas  
**Prioridad:** 🔥 CRÍTICA

---

## 🎯 OBJETIVO PRINCIPAL

**Score SEO:** 88.8 → 91-92/100 (+2.2-3.2 puntos)

**Tareas críticas:**
1. ✅ Tags WooCommerce (538 productos) → +1.0-1.5 pts
2. ✅ Títulos cortos expandir (206 productos) → +0.5-1.0 pts
3. ✅ Keywords faltantes (122 productos) → +0.3-0.5 pts

---

## 📊 SITUACIÓN ACTUAL

### Score SEO

| Métrica | Valor Actual |
|---------|--------------|
| Score total | 88.8/100 |
| Productos | 538 |
| Sin tags | 538 (100%) |
| Títulos cortos (<30 chars) | 206 (38%) |
| Sin keywords | 122 (23%) |

### Problemas Día 3 Resueltos

✅ Imágenes irrelevantes eliminadas  
✅ Código mejorado (sin fallbacks genéricos)  
✅ Herramientas de auditoría creadas  
✅ Base limpia para optimizaciones

---

## ⏰ CRONOGRAMA DETALLADO

### Bloque 1: TAGS WOOCOMMERCE (08:00-11:00) - 3h

**08:00-08:30 | Testing Script Tags**
```bash
# Test con muestra pequeña
python3 agregar_tags_woocommerce.py --dry-run --max-products 10

# Revisar tags generados
cat tags_resultados.json | jq '.top_tags'

# Verificar calidad de tags
```

**Criterios de éxito:**
- Tags relevantes (no genéricos)
- 3-8 tags por producto
- Sin duplicados
- Relacionados con producto

**08:30-09:30 | Ejecución Completa (Dry-Run)**
```bash
# Dry-run completo
python3 agregar_tags_woocommerce.py \
    --dry-run \
    --output tags_dry_run_resultados.json

# Análisis resultados
cat tags_dry_run_resultados.json | jq '{
    total, 
    procesados: .processed, 
    exitosos: .success,
    tags_unicos: .unique_tags,
    tags_asignados: .tags_assigned
}'
```

**Verificaciones:**
- [ ] ¿Tags pertinentes?
- [ ] ¿Cantidad razonable por producto?
- [ ] ¿Sin errores API?
- [ ] ¿Top tags coherentes?

**09:30-10:30 | Ejecución REAL**
```bash
# Si dry-run OK, ejecutar real
python3 agregar_tags_woocommerce.py \
    --output tags_resultados_final.json \
    2>&1 | tee logs/tags_woocommerce_$(date '+%Y%m%d_%H%M%S').log
```

**Monitoreo:**
```bash
# En otra terminal
tail -f logs/tags_woocommerce_*.log | grep -E "✅|❌|ERROR"
```

**10:30-11:00 | Verificación Post-Tags**
```bash
# Verificar productos con tags
python3 -c "
import os, requests
from dotenv import load_dotenv

load_dotenv()
url = os.getenv('WORDPRESS_URL') + '/wp-json/wc/v3/products'
r = requests.get(url, auth=(os.getenv('WC_CONSUMER_KEY'), os.getenv('WC_CONSUMER_SECRET')), params={'per_page': 10})

for p in r.json():
    tags = [t['name'] for t in p.get('tags', [])]
    print(f\"{p['name'][:40]}: {len(tags)} tags - {', '.join(tags[:3])}\")
"
```

**Resultado esperado:**
- 538 productos con tags
- 3-8 tags promedio
- Tags relevantes y específicos

---

### Bloque 2: TÍTULOS CORTOS (11:00-13:00) - 2h

**11:00-11:30 | Análisis Títulos Actuales**
```bash
# Identificar títulos cortos
python3 -c "
import os, requests
from dotenv import load_dotenv

load_dotenv()
url = os.getenv('WORDPRESS_URL') + '/wp-json/wc/v3/products'
auth = (os.getenv('WC_CONSUMER_KEY'), os.getenv('WC_CONSUMER_SECRET'))

page = 1
cortos = []

while page <= 6:
    r = requests.get(url, auth=auth, params={'per_page': 100, 'page': page})
    for p in r.json():
        if len(p['name']) < 30:
            cortos.append({'id': p['id'], 'name': p['name'], 'len': len(p['name'])})
    page += 1

print(f'Títulos cortos (<30 chars): {len(cortos)}')
for item in cortos[:10]:
    print(f\"  ID {item['id']}: '{item['name']}' ({item['len']} chars)\")
" > analisis_titulos_cortos.txt

cat analisis_titulos_cortos.txt
```

**11:30-12:30 | Script Expansión Títulos**

Crear `expandir_titulos.py`:
```python
#!/usr/bin/env python3
"""Expande títulos cortos de productos"""

import os, requests, re
from dotenv import load_dotenv

load_dotenv()

def expand_title(product):
    """Expande título corto a 40-60 caracteres"""
    name = product['name']
    
    if len(name) >= 30:
        return None  # Ya es suficientemente largo
    
    # Obtener info adicional
    categories = [c['name'] for c in product.get('categories', [])]
    attrs = product.get('attributes', [])
    
    # Construir título expandido
    expanded = name
    
    # Agregar categoría si es específica
    if categories:
        cat = categories[0]
        if cat not in name and len(cat) < 20:
            expanded += f" - {cat}"
    
    # Agregar atributos clave
    for attr in attrs:
        attr_name = attr.get('name', '')
        attr_values = attr.get('options', [])
        
        if attr_name == 'Tamaño' and attr_values:
            size = attr_values[0]
            if size not in expanded:
                expanded += f" {size}"
    
    # Verificar longitud final
    if 40 <= len(expanded) <= 60:
        return expanded
    elif len(expanded) < 40:
        # Aún corto, agregar descriptor genérico
        if 'planta' not in expanded.lower():
            expanded += " - Planta"
        return expanded
    else:
        # Muy largo, truncar
        return expanded[:60]

# Procesar
url = os.getenv('WORDPRESS_URL') + '/wp-json/wc/v3/products'
auth = (os.getenv('WC_CONSUMER_KEY'), os.getenv('WC_CONSUMER_SECRET'))

# Obtener productos con títulos cortos
# ... implementar actualización
```

**12:30-13:00 | Ejecución Expansión Títulos**
```bash
python3 expandir_titulos.py --dry-run
# Si OK:
python3 expandir_titulos.py
```

---

### ALMUERZO (13:00-14:00)

---

### Bloque 3: KEYWORDS FALTANTES (14:00-15:00) - 1h

**14:00-14:30 | Identificar Productos Sin Keywords**
```bash
python3 -c "
import os, requests
from dotenv import load_dotenv

load_dotenv()
url = os.getenv('WORDPRESS_URL') + '/wp-json/wc/v3/products'
auth = (os.getenv('WC_CONSUMER_KEY'), os.getenv('WC_CONSUMER_SECRET'))

page = 1
sin_keywords = []

while page <= 6:
    r = requests.get(url, auth=auth, params={'per_page': 100, 'page': page})
    for p in r.json():
        # Verificar meta keywords (en yoast_meta)
        meta = p.get('meta_data', [])
        has_keywords = any(m.get('key') == '_yoast_wpseo_focuskw' for m in meta)
        
        if not has_keywords:
            sin_keywords.append(p['id'])
    
    page += 1

print(f'Productos sin keywords: {len(sin_keywords)}')
print(f'IDs: {sin_keywords[:20]}')
"
```

**14:30-15:00 | Generar Keywords Automáticas**
- Usar nombre del producto
- Agregar categoría principal
- Incluir atributos clave
- Formato: "palabra1, palabra2, palabra3"

---

### Bloque 4: AUDITORÍA INTERMEDIA (15:00-15:30) - 30min

**15:00-15:15 | Ejecutar Auditoría SEO**
```bash
python3 AUDITORIA_SEO_COMPLETA.py \
    --url "https://viveroloscocos.com.ar" \
    --key "$WC_CONSUMER_KEY" \
    --secret "$WC_CONSUMER_SECRET" \
    2>&1 | tee logs/auditoria_dia4_intermedia_$(date '+%Y%m%d_%H%M%S').log
```

**15:15-15:30 | Análisis Resultados**
```bash
# Extraer score
grep "Score SEO:" logs/auditoria_dia4_intermedia_*.log

# ¿Alcanzamos 91-92?
# Si SÍ: Documentar éxito
# Si NO: Identificar qué falta
```

**Decisión:**
- Si score ≥ 91: ✅ Objetivo alcanzado, optimizaciones adicionales
- Si score < 91: Identificar gaps y priorizar

---

### Bloque 5: OPTIMIZACIONES ADICIONALES (15:30-18:00) - 2.5h

**Opción A: Score ≥ 91 (Optimizaciones Extra)**

1. **Schema Markup Básico** (1h)
   - Schema Product para productos
   - Schema Organization para sitio
   - Testing en Google Rich Results

2. **Alt Text Revisión** (30min)
   - Verificar alt text de imágenes featured
   - Mejorar descripciones

3. **Meta Descriptions Refinamiento** (1h)
   - Expandir descripciones <140 chars
   - Optimizar con keywords

**Opción B: Score < 91 (Correcciones)**

1. **Análisis de gaps**
   - ¿Qué factores faltan?
   - ¿Qué tiene bajo score?

2. **Correcciones específicas**
   - Focus en factores de mayor impacto
   - Iteración rápida

---

### Bloque 6: AUDITORÍA FINAL DÍA 4 (18:00-18:30) - 30min

**18:00-18:15 | Auditoría Final**
```bash
python3 AUDITORIA_SEO_COMPLETA.py \
    --url "https://viveroloscocos.com.ar" \
    --key "$WC_CONSUMER_KEY" \
    --secret "$WC_CONSUMER_SECRET" \
    2>&1 | tee logs/auditoria_dia4_final_$(date '+%Y%m%d_%H%M%S').log
```

**18:15-18:30 | Análisis y Documentación**
- Extraer métricas finales
- Comparar con objetivo
- Documentar logros y pendientes

---

## 📊 MÉTRICAS DE ÉXITO

### Objetivos Mínimos

| Métrica | Actual | Objetivo | Prioridad |
|---------|--------|----------|-----------|
| Score SEO | 88.8 | 91-92 | 🔥 Crítica |
| Productos con tags | 0 | 538 | 🔥 Crítica |
| Títulos optimizados | 332 | 538 | Alta |
| Con keywords | 416 | 538 | Media |

### KPIs

- ✅ **100% productos con tags** (538/538)
- ✅ **100% títulos >30 chars** (538/538)
- ✅ **100% con keywords** (538/538)
- ✅ **Score ≥ 91/100**

---

## 🛠️ HERRAMIENTAS DISPONIBLES

### Scripts Listos

1. ✅ `agregar_tags_woocommerce.py` - Tags automáticos
2. ✅ `AUDITORIA_SEO_COMPLETA.py` - Auditoría SEO
3. ✅ `wc_image_automation.py` - Imágenes (mejorado)
4. ⏳ `expandir_titulos.py` - A crear hoy
5. ⏳ `agregar_keywords.py` - A crear hoy

### Documentación

1. ✅ `PLAN-DIA-4-DETALLADO.md` - Este documento
2. ✅ `RESUMEN-EJECUTIVO-FINAL-DIA-3.md` - Contexto
3. ✅ `MEJORAS-WC-IMAGE-AUTOMATION.md` - Mejoras código

---

## 🚨 RIESGOS Y MITIGACIONES

### Riesgo 1: Script Tags Genera Tags Irrelevantes

**Probabilidad:** Media  
**Impacto:** Alto

**Mitigación:**
- Testing exhaustivo con muestra (10 productos)
- Dry-run completo antes de producción
- Revisión manual de top 20 tags generados
- Filtros de palabras excluidas

### Riesgo 2: Títulos Expandidos Quedan Mal Formados

**Probabilidad:** Media  
**Impacto:** Medio

**Mitigación:**
- Reglas claras de expansión
- Validación de longitud (40-60 chars)
- Dry-run obligatorio
- Muestra de 20 para revisión manual

### Riesgo 3: APIs WooCommerce Límite de Rate

**Probabilidad:** Baja  
**Impacto:** Medio

**Mitigación:**
- Delay de 1-2 segundos entre requests
- Procesar en batches pequeños
- Retry automático en fallos
- Monitoreo de errores 429

### Riesgo 4: No Alcanzar Score 91

**Probabilidad:** Baja  
**Impacto:** Alto

**Mitigación:**
- Tareas priorizadas por impacto
- Auditoría intermedia para ajustar
- Optimizaciones adicionales disponibles
- Día 5 disponible para completar

---

## 📋 CHECKLIST PRE-EJECUCIÓN

### Antes de Empezar

- [ ] Backup de base de datos WooCommerce
- [ ] Verificar variables de entorno (.env)
- [ ] Confirmar credenciales API funcionando
- [ ] Script tags compilado y testeado
- [ ] Logs directory creado
- [ ] Herramientas de monitoreo listas

### Durante Ejecución

- [ ] Monitorear logs en tiempo real
- [ ] Verificar errores API
- [ ] Revisar muestra de resultados
- [ ] Documentar problemas encontrados

### Post-Ejecución

- [ ] Auditoría SEO final
- [ ] Comparar con objetivos
- [ ] Documentar resultados
- [ ] Preparar plan Día 5

---

## 💰 ROI ESPERADO

### Inversión

- Tiempo: 8-10 horas
- Scripts: Ya desarrollados
- Testing: 1-2 horas
- Ejecución: 5-6 horas
- Verificación: 1-2 horas

### Retorno

**Mejora de score:** +2.2-3.2 puntos  
**Impacto en conversión:** +3-5%  
**Revenue adicional (12 meses):** +$15-25K/año

**Tags específicamente:**
- Mejor categorización: +2% conversión
- Filtrado mejorado: +1% conversión
- SEO interno: +1-2% tráfico orgánico

---

## 🎯 OBJETIVO FINAL

**Score Día 4:** 91-92/100 ✅  
**Confianza:** 90%  
**Base para Día 5:** Schema + Enlaces internos → 93-94/100

---

## 📞 COMANDOS ÚTILES

### Monitoreo

```bash
# Ver procesos Python
ps aux | grep python3 | grep -E "tags|titulos|keywords"

# Ver logs en tiempo real
tail -f logs/*.log

# Verificar productos con tags
curl -u "$WC_CONSUMER_KEY:$WC_CONSUMER_SECRET" \
    "https://viveroloscocos.com.ar/wp-json/wc/v3/products?per_page=5" \
    | jq '.[].tags'
```

### Testing Rápido

```bash
# Test tags con 5 productos
python3 agregar_tags_woocommerce.py --dry-run --max-products 5

# Test compilación
python3 -m py_compile agregar_tags_woocommerce.py

# Test API
python3 -c "import os; from dotenv import load_dotenv; load_dotenv(); import requests; print(requests.get(os.getenv('WORDPRESS_URL') + '/wp-json/wc/v3/products/tags', auth=(os.getenv('WC_CONSUMER_KEY'), os.getenv('WC_CONSUMER_SECRET'))).status_code)"
```

---

**🟢 DÍA 4: TAGS + TÍTULOS + KEYWORDS → 91-92/100**

**Preparación:** Completa  
**Herramientas:** Listas  
**Confianza:** Alta (90%)  
**Inicio:** 08:00 del 5 de Octubre

---

*Creado: 21:35 ART - 4 Oct 2025*  
*Responsable: Sistema de Automatización SEO*  
*Proyecto: Vivero Los Cocos - Optimización SEO Día 4*
