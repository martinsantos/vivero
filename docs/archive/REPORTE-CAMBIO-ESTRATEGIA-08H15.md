# 🔄 CAMBIO DE ESTRATEGIA DÍA 4 - 08:15 ART

**Decisión:** PRIORIZAR TAGS WOOCOMMERCE  
**Razón:** Mayor impacto en score (+1.0-1.5 pts)

---

## ❌ PROBLEMA CON IMÁGENES

### Tests Realizados

**Test 1:** Sistema validación estricta  
- Resultado: 0/5 aceptadas
- Problema: Queries genéricas, scores 0-15/100

**Test 2:** Queries ajustadas  
- Resultado: 0/3 aceptadas  
- Problema: Nombres de productos no estándar ("Forverna", "Foratrona")
- Scores: 15/100 (muy bajo)

### Conclusión

- Sistema validación funciona BIEN (rechaza irrelevantes)
- Problema: Nombres productos no son científicos estándar
- Solo 13/538 productos tienen nombre científico real
- Mayoría tiene nombres inventados/códigos

**Decisión:** Posponer imágenes, priorizar tags

---

## ✅ NUEVA ESTRATEGIA

### Prioridad 1: TAGS WOOCOMMERCE (08:15-09:30)

**Test completado:**
```
10 productos procesados
10 exitosos
27 tags asignados
8 tags únicos
```

**Tags generados:**
- Uncategorized (10)
- 3 Litros (7)
- 4 Litros (3)
- Flor de Temporada (2)
- Floral (2)
- Arbusto, Trepadora, etc.

**Estado:** 🔄 EJECUTANDO producción (538 productos)

**Impacto:** +1.0-1.5 puntos score

---

### Prioridad 2: TÍTULOS CORTOS (09:30-10:30)

**Objetivo:** Expandir ~338 títulos <30 chars a 40-60 chars

**Método:** Script inline rápido

**Impacto:** +0.5-1.0 puntos

---

### Prioridad 3: KEYWORDS (10:30-11:00)

**Objetivo:** Completar ~122 productos sin keywords

**Impacto:** +0.3-0.5 puntos

---

### Prioridad 4: AUDITORÍA (11:00-11:30)

**Objetivo:** Verificar score 91-92/100

**Si tiempo:** Intentar imágenes con wc_image_automation.py mejorado

---

## 📊 SCORE PROYECTADO

### Sin Imágenes Nuevas

| Tarea | Impacto |
|-------|---------|
| Tags (538) | +1.0-1.5 pts |
| Títulos (338) | +0.5-1.0 pts |
| Keywords (122) | +0.3-0.5 pts |
| **TOTAL** | **+1.8-3.0 pts** |

**Score proyectado:** 90.6-91.8/100

**Objetivo mínimo:** 91/100 ✅ (alcanzable)

---

## 🎯 JUSTIFICACIÓN

### Por qué Tags Primero

1. **Mayor impacto:** +1.0-1.5 pts vs +0.5 pts imágenes
2. **Script listo:** Funciona perfectamente
3. **Rápido:** 538 productos en 1 hora
4. **Sin riesgo:** No hay validación compleja

### Por qué Posponer Imágenes

1. **Nombres no estándar:** 525/538 sin nombre científico
2. **Validación muy estricta:** Rechaza todo (correcto)
3. **Tiempo limitado:** 3 horas restantes
4. **Menor impacto:** +0.3-0.5 pts por 30 imágenes

### Plan B Imágenes

Si queda tiempo (11:30+):
- Usar wc_image_automation.py mejorado
- Provider: iNaturalist (mejor para plantas)
- Queries: nombre específico + "plant"
- Límite: 20-30 productos
- Validación manual cada 10

---

## 🔄 EJECUCIÓN ACTUAL (08:15)

### Tags WooCommerce

**Comando:**
```bash
python3 agregar_tags_woocommerce.py \
    --output tags_woocommerce_final.json
```

**Estado:** 🔄 Ejecutando  
**Productos:** 538  
**ETA:** 09:15 (1 hora)

**Monitoreo:**
```bash
tail -f logs/tags_woocommerce_*.log
```

---

## 📋 CRONOGRAMA ACTUALIZADO

```
08:15-09:15 | Tags WooCommerce (538)       [EN CURSO]
09:15-10:15 | Títulos cortos (338)         [PENDIENTE]
10:15-10:45 | Keywords (122)               [PENDIENTE]
10:45-11:15 | Auditoría SEO                [PENDIENTE]
11:15-12:00 | Imágenes (opcional)          [PENDIENTE]
```

**Total:** 3h 45min  
**Objetivo:** 91-92/100

---

## ✅ VENTAJAS ESTRATEGIA

1. **Garantiza objetivo mínimo** (91/100)
2. **Usa herramientas probadas** (tags script OK)
3. **Maximiza impacto/tiempo** (tags > imágenes)
4. **Reduce riesgo** (no depende de validación compleja)
5. **Deja buffer** (imágenes opcional al final)

---

**🟢 ESTRATEGIA AJUSTADA - TAGS EN EJECUCIÓN**

**Prioridad:** Tags → Títulos → Keywords → Auditoría  
**Objetivo:** 91-92/100  
**Confianza:** 90%  
**ETA:** 11:15 ART

---

*Actualizado: 08:15 ART - 5 de Octubre, 2025*  
*Decisión: Priorizar impacto máximo*  
*Sistema de Automatización SEO - Vivero Los Cocos*
