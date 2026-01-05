# 🚨 SOLUCIÓN URGENTE - IMÁGENES DUPLICADAS

**Problema identificado:** Múltiples productos comparten las mismas imágenes
**Impacto:** Confusión del cliente, pérdida de conversiones, SEO negativo
**Solución:** Asignar imágenes únicas usando el sistema automático existente

---

## 📊 PRODUCTOS AFECTADOS (De las screenshots)

### Grupo 1: Árboles
- Árbol Oletexal 15 Litros
- Árbol Prun 7 Litros
→ Comparten imagen de césped con pájaro

- Árbol Oletexal 3 Litros  
- Árbol Oletexver 3 Litros
→ Comparten imagen de riego por goteo

### Grupo 2: Macetas Rocío 10 cm
- Color: Naranja
- Color: Verde Claro
- Color: Amarillo
→ Comparten imagen textura amarilla/dorada

### Grupo 3: Macetas Rocío 18 cm
- Color: Blanco (múltiples)
- Color: L
→ Comparten imagen de tronco de árbol

### Grupo 4: Macetas Rocío 21 cm
- Color: Marrón Terracota
- Color: Naranja
→ Comparten imágenes de flores y mariposas

---

## ✅ SOLUCIÓN INMEDIATA

### OPCIÓN 1: Usar el sistema automático existente

Ya tienes `wc_image_automation.py` funcional. Vamos a:

1. **Eliminar imágenes actuales de productos afectados**
2. **Ejecutar automation con búsquedas específicas por producto**
3. **Verificar que cada producto tenga imagen única**

### Comando para ejecutar:

```bash
cd /Applications/um/vivero

# Ejecutar con global deduplication ACTIVADO
python3 wc_image_automation.py \
  --url https://viveroloscocos.com.ar \
  --key ck_ac71d70680d0ab93fbc6aa00009ae2811d024b45 \
  --secret cs_27fc77bf74e39397d4c4d42120e166469590258b \
  --batch-size 50 \
  --global-dedupe \
  --providers unsplash pexels inaturalist \
  --force-replace
```

**Flags importantes:**
- `--global-dedupe`: Evita usar la misma imagen para diferentes productos
- `--force-replace`: Reemplaza imágenes existentes
- Múltiples providers para máxima variedad

---

## 🔧 OPCIÓN 2: Script Específico para Productos Afectados

Crear lista de productos con imágenes problemáticas y procesarlos individualmente:

```python
# productos_afectados.txt
Árbol Oletexal 15 Litros
Árbol Prun 7 Litros
Árbol Oletexal 3 Litros
Árbol Oletexver 3 Litros
Maceta Plástica Rocío 10 cm. Color: Naranja
Maceta Plástica Rocío 10 cm. Color: Verde Claro
Maceta Plástica Rocío 10 cm. Color: Amarillo
Maceta Plástica Rocío 18 cm. Color: Blanco
Maceta Plástica Rocío 18 cm. Color: L
Maceta Plástica Rocío 18 cm. Color: Marrón Claro
Maceta Plástica Rocío 21 cm. Color: Marrón Terracota
Maceta Plástica Rocío 21 cm. Color: Naranja
```

---

## 📋 PLAN DE EJECUCIÓN

### Paso 1: Backup
```bash
# Crear backup de imágenes actuales
sshpass -p 'gsiB%s@0yD' ssh root@23.105.176.45 \
  "cd /home/viveroloscocos.com.ar/public_html && \
   wp db export /tmp/backup_imagenes_$(date +%Y%m%d).sql"
```

### Paso 2: Limpiar imágenes duplicadas
```bash
# Script SQL para identificar y marcar duplicados
sshpass -p 'gsiB%s@0yD' ssh root@23.105.176.45 \
  "cd /home/viveroloscocos.com.ar/public_html && \
   wp db query \"
   SELECT p.ID, p.post_title, pm.meta_value as image_id, 
          (SELECT guid FROM wp_posts WHERE ID = pm.meta_value) as image_url
   FROM wp_posts p
   JOIN wp_postmeta pm ON p.ID = pm.post_id
   WHERE p.post_type = 'product' 
     AND pm.meta_key = '_thumbnail_id'
   ORDER BY pm.meta_value;
   \""
```

### Paso 3: Ejecutar automation con deduplicación
```bash
cd /Applications/um/vivero
python3 wc_image_automation.py \
  --batch-size 20 \
  --global-dedupe \
  --force-replace \
  --providers unsplash pexels inaturalist flickr
```

### Paso 4: Verificar resultados
```bash
# Ver productos actualizados
sshpass -p 'gsiB%s@0yD' ssh root@23.105.176.45 \
  "cd /home/viveroloscocos.com.ar/public_html && \
   wp post list --post_type=product --posts_per_page=20 \
   --orderby=modified --order=DESC --format=table"
```

---

## ⚡ EJECUCIÓN INMEDIATA RECOMENDADA

### Comando único para solucionar TODO:

```bash
cd /Applications/um/vivero && \
python3 wc_image_automation.py \
  --batch-size 50 \
  --delay 2 \
  --global-dedupe \
  --force-replace \
  --providers unsplash pexels inaturalist \
  2>&1 | tee logs/image_fix_$(date +%Y%m%d_%H%M%S).log
```

**Tiempo estimado:** 2-3 horas para 538 productos  
**Resultado:** Cada producto con imagen única y relevante

---

## 🎯 VERIFICACIÓN POST-EJECUCIÓN

1. **Visual:** Navegar https://viveroloscocos.com.ar/tienda/
2. **Por categoría:** Verificar macetas de diferentes colores
3. **Por producto:** Spot check 50 productos aleatorios
4. **SQL:** Ejecutar query de duplicados nuevamente

---

## 📊 MÉTRICAS DE ÉXITO

- ✅ 0 productos compartiendo imágenes
- ✅ Cada maceta de color diferente con imagen única
- ✅ Árboles diferentes con imágenes diferentes
- ✅ Productos similares visualmente diferenciados

---

**🔥 EJECUTAR AHORA PARA CORREGIR URGENTEMENTE**
