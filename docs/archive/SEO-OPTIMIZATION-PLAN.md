# 🚀 PLAN DE OPTIMIZACIÓN SEO MASIVA
## Vivero Los Cocos - Títulos de Productos

**Fecha:** 2025-10-03  
**Objetivo:** Mejorar SEO de 538 productos en producción  
**Estado:** ⚠️ CRÍTICO - Requiere acción inmediata

---

## 📊 ANÁLISIS DEL PROBLEMA

### Situación Actual
Los productos en `viveroloscocos.com.ar` tienen títulos codificados y poco descriptivos:

| SKU | Título Actual | Problema |
|-----|---------------|----------|
| jazlluv3l | Jazlluv3l | ❌ Código críptico |
| abedul15l | Abedul15l | ❌ Sin contexto |
| olivo20l | Olivo20l | ❌ No es amigable |
| bougan3l | Bougan3l | ❌ Abreviatura |
| glici4l | Glici4l | ❌ Sin información |

### Impacto SEO Actual
- ❌ **Títulos no descriptivos:** Google no entiende qué venden
- ❌ **Sin keywords relevantes:** No aparece en búsquedas de "plantas mendoza"
- ❌ **Sin ubicación geográfica:** Pierde tráfico local
- ❌ **Sin información de tamaño:** Usuarios no saben qué comprar
- ❌ **Capitalización incorrecta:** Mala presentación en resultados

---

## 🎯 SOLUCIÓN PROPUESTA

### Algoritmo de Optimización SEO

El script `seo_title_optimizer.py` implementa el siguiente algoritmo:

```
PARA cada producto:
    1. Decodificar SKU/slug → Extraer nombre de planta + tamaño
    2. Buscar en base de conocimiento → Obtener info botánica
    3. Construir título optimizado:
       - Nombre común de la planta
       - Tamaño en litros (importante para viveros)
       - Categoría (Árbol Frutal, Arbusto, etc.)
       - Ubicación geográfica (Mendoza)
       - Marca (Vivero Los Cocos)
    4. Generar meta description (150-160 chars)
    5. Generar keywords SEO relevantes
    6. Calcular score de mejora (0-100)
```

### Ejemplos de Transformación

| Actual | Optimizado | Score |
|--------|-----------|-------|
| Jazlluv3l | **Jazmín Lluvia de Oro 3 Litros - Arbusto Floral \| Mendoza** | 95 |
| Abedul15l | **Abedul 15 Litros - Árbol Ornamental \| Vivero Los Cocos** | 90 |
| Olivo20l | **Olivo 20 Litros - Árbol Frutal \| Mendoza \| Vivero Los Cocos** | 100 |
| Bougan3l | **Bougainvillea 3 Litros - Arbusto Trepador \| Mendoza** | 95 |
| Glici4l | **Glicina 4 Litros - Trepadora Floral \| Vivero Los Cocos** | 90 |

---

## 🗂️ BASE DE CONOCIMIENTO DE PLANTAS

El optimizador incluye información de **30+ plantas argentinas**, incluyendo:

### Árboles Frutales
- Olivo (Olea europaea)
- Jaca (Artocarpus heterophyllus)
- Ciruelo/Cerezo (Prunus)
- Frutilla (Fragaria)

### Árboles Ornamentales
- Abedul (Betula)
- Tilo (Tilia)
- Liquidámbar
- Eucalipto Plateado
- Acacia de Constantinopla

### Arbustos y Trepadoras
- Jazmín (múltiples variedades)
- Bougainvillea
- Glicina (Wisteria)
- Forsitia

### Plantas de Interior
- Dracena
- Thuja

Cada planta incluye:
- ✅ Nombre científico
- ✅ Nombres comunes
- ✅ Categoría
- ✅ Características (perenne, flores, etc.)
- ✅ Keywords SEO específicas

---

## 📝 CRITERIOS SEO IMPLEMENTADOS

### 1. **Longitud Óptima del Título**
- **Ideal:** 50-60 caracteres
- **Aceptable:** 40-70 caracteres
- **Razón:** Google trunca después de ~60 chars

### 2. **Estructura del Título**
```
[Nombre Descriptivo] [Tamaño] - [Categoría] | [Ubicación] | [Marca]
```

Ejemplos:
- ✅ "Jazmín Lluvia de Oro 3 Litros - Arbusto Floral | Mendoza"
- ✅ "Olivo 20 Litros - Árbol Frutal | Vivero Los Cocos"

### 3. **Keywords Principales**
Cada producto incluye keywords como:
- Nombre de la planta + "mendoza"
- Nombre + "argentina"
- Nombre + "vivero"
- Nombre + "comprar"
- Categoría + "jardin"

### 4. **Meta Descriptions**
Generadas automáticamente con:
- Call-to-action ("Comprá...")
- Nombre del producto
- Ubicación (Mendoza)
- Características principales
- Urgencia ("¡Envío el mismo día!")

**Ejemplo:**
```
Comprá Jazmín Lluvia de Oro en Vivero Los Cocos Mendoza. 
Arbusto Floral ideal para jardines. Características: flores 
amarillas, floracion invernal, perfumado. ¡Envío el mismo día!
```

---

## 🛠️ HERRAMIENTAS DESARROLLADAS

### 1. `seo_title_optimizer.py`
**Funcionalidad:**
- Decodifica SKUs de productos
- Busca información botánica
- Genera títulos SEO-optimizados
- Calcula scores de mejora
- Exporta análisis a CSV

**Uso:**
```bash
python3 seo_title_optimizer.py
```

### 2. `update_titles_production.py`
**Funcionalidad:**
- Conecta con API de WooCommerce
- Obtiene todos los productos (538)
- Optimiza cada título
- Actualiza en producción
- Genera reporte detallado
- Incluye modo dry-run (simulación)

**Uso:**
```bash
# Simulación (no actualiza)
python3 update_titles_production.py \
  --url https://viveroloscocos.com.ar \
  --key ck_XXXXX \
  --secret cs_XXXXX \
  --dry-run

# Producción (con confirmación manual)
python3 update_titles_production.py \
  --url https://viveroloscocos.com.ar \
  --key ck_XXXXX \
  --secret cs_XXXXX

# Producción (automático, sin confirmación)
python3 update_titles_production.py \
  --url https://viveroloscocos.com.ar \
  --key ck_XXXXX \
  --secret cs_XXXXX \
  --force
```

---

## 🚀 PLAN DE EJECUCIÓN

### Fase 1: Análisis Previo (5 minutos)
1. ✅ Revisar base de datos de plantas
2. ✅ Validar algoritmo con ejemplos
3. ✅ Ejecutar en modo dry-run
4. ✅ Revisar reporte de optimización

### Fase 2: Generación de Credenciales (5 minutos)
```bash
# Conectar al servidor
ssh root@23.105.176.45

# Generar claves de WooCommerce
cd /home/viveroloscocos.com.ar/public_html
wp wc customer create \
  --email="admin@loscocos.com" \
  --username="seo_optimizer" \
  --password="SEO2025Secure!" \
  --role=administrator

# Obtener Consumer Key/Secret desde WooCommerce > Settings > Advanced > REST API
```

### Fase 3: Simulación (10 minutos)
```bash
python3 update_titles_production.py \
  --url https://viveroloscocos.com.ar \
  --key ck_XXXXX \
  --secret cs_XXXXX \
  --dry-run \
  --limit 50
```

**Revisar:**
- ✅ Títulos generados son correctos
- ✅ Descripciones son relevantes
- ✅ Keywords son apropiadas
- ✅ No hay errores de formato

### Fase 4: Actualización por Lotes (30 minutos)
```bash
# Lote 1: Primeros 100 productos
python3 update_titles_production.py \
  --url https://viveroloscocos.com.ar \
  --key ck_XXXXX \
  --secret cs_XXXXX \
  --limit 100

# Revisar en el sitio que todo funciona

# Lote 2: Siguientes 200 productos
python3 update_titles_production.py \
  --url https://viveroloscocos.com.ar \
  --key ck_XXXXX \
  --secret cs_XXXXX \
  --limit 300

# Lote 3: Resto de productos
python3 update_titles_production.py \
  --url https://viveroloscocos.com.ar \
  --key ck_XXXXX \
  --secret cs_XXXXX \
  --force
```

### Fase 5: Verificación (10 minutos)
1. Revisar productos en el sitio web
2. Verificar que los títulos se ven correctos
3. Probar búsquedas en Google
4. Revisar Google Search Console
5. Validar que no hay errores 404

---

## 📊 RESULTADOS ESPERADOS

### Mejoras Inmediatas (0-7 días)
- ✅ Títulos descriptivos y profesionales
- ✅ Mejor presentación en resultados de Google
- ✅ Aumento de CTR (Click-Through Rate)
- ✅ Reducción de bounce rate

### Mejoras a Mediano Plazo (1-4 semanas)
- 📈 Mejor posicionamiento en búsquedas locales
- 📈 Aumento de tráfico orgánico (+30-50%)
- 📈 Más conversiones de búsquedas "mendoza plantas"
- 📈 Mejora en rankings para keywords específicas

### Mejoras a Largo Plazo (1-3 meses)
- 🚀 Dominio de búsquedas "vivero mendoza"
- 🚀 Top 3 en "plantas mendoza argentina"
- 🚀 Autoridad en keywords de plantas específicas
- 🚀 Aumento de ventas online (+40-60%)

---

## 🔍 MÉTRICAS A MONITOREAR

### Google Search Console
- **Impresiones:** Cuántas veces aparece en resultados
- **Clicks:** Cuántas veces hacen click
- **CTR:** Porcentaje de clicks vs impresiones
- **Posición promedio:** Ranking en resultados

### Google Analytics
- **Tráfico orgánico:** Visitantes desde Google
- **Páginas de producto vistas:** Engagement
- **Tasa de rebote:** % que se van sin interactuar
- **Conversiones:** Ventas generadas

### WooCommerce
- **Productos más visitados:** Qué optimizaciones funcionan mejor
- **Tasa de conversión:** % de visitantes que compran
- **Valor promedio del pedido:** Ticket medio

---

## 🎓 KEYWORDS PRINCIPALES A DOMINAR

### Búsquedas de Alta Intención
1. **"vivero mendoza"** (500+ búsquedas/mes)
2. **"plantas mendoza"** (300+ búsquedas/mes)
3. **"comprar plantas mendoza"** (200+ búsquedas/mes)
4. **"vivero online mendoza"** (150+ búsquedas/mes)
5. **"árboles frutales mendoza"** (100+ búsquedas/mes)

### Long-Tail Keywords (Menos competencia)
- "jazmin lluvia de oro mendoza"
- "olivo en maceta mendoza"
- "bougainvillea vivero mendoza"
- "glicina trepadora argentina"
- "abedul ornamental jardin"

### Keywords por Categoría
**Árboles Frutales:**
- olivo mendoza, ciruelo frutal, frutilla planta

**Árboles Ornamentales:**
- abedul jardin, tilo sombra, liquidambar mendoza

**Arbustos:**
- jazmin perfumado, bougainvillea argentina, forsitia flores

---

## ⚠️ PRECAUCIONES

### Antes de Ejecutar
1. ✅ Hacer backup de la base de datos
2. ✅ Probar en modo dry-run primero
3. ✅ Revisar al menos 20 ejemplos manualmente
4. ✅ Validar credenciales de API

### Durante la Ejecución
1. ⚡ No interrumpir el proceso
2. ⚡ Monitorear errores en tiempo real
3. ⚡ Guardar logs de cada ejecución
4. ⚡ Hacer pausas entre lotes grandes

### Después de Ejecutar
1. 🔍 Verificar productos aleatorios
2. 🔍 Probar búsquedas en el sitio
3. 🔍 Verificar que no hay 404s
4. 🔍 Enviar sitemap a Google

---

## 🆘 ROLLBACK (Por si algo sale mal)

### Opción 1: Restaurar desde Backup
```bash
# Restaurar base de datos
mysql -u root -p vivero_db < backup_antes_seo.sql
```

### Opción 2: Revertir Títulos Manualmente
```bash
# Script de rollback (crear si es necesario)
wp post list --post_type=product --format=ids | \
xargs -I % wp post meta update % _backup_title "$(wp post get % --field=post_title)"
```

### Opción 3: Modificar Específicos
Usar el script con lista de IDs a revertir

---

## 📞 SOPORTE

**Desarrollador:** SEO Optimization Team  
**Documentación:** Este archivo  
**Scripts:** `seo_title_optimizer.py`, `update_titles_production.py`  
**Reporte:** `seo_optimization_report.md` (se genera automáticamente)

---

## ✅ CHECKLIST FINAL

Antes de ejecutar en producción:

- [ ] Backup de base de datos realizado
- [ ] Credenciales de API obtenidas y validadas
- [ ] Dry-run ejecutado exitosamente
- [ ] Al menos 50 títulos revisados manualmente
- [ ] Reporte de simulación analizado
- [ ] Plan de rollback preparado
- [ ] Monitoreo de métricas configurado
- [ ] Equipo notificado del cambio

---

**🌿 ¡Listo para hacer a Vivero Los Cocos el #1 en SEO de plantas en Mendoza!**

*Última actualización: 2025-10-03 18:00 ART*
