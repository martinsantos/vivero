# 🖼️ INSTRUCCIONES - GALERÍA DE IMÁGENES DÍA 3

**Objetivo:** Agregar 2-3 imágenes más por producto  
**Impacto esperado:** +2-3 puntos SEO → Score 91-92/100  
**Productos objetivo:** 538 (todos con imágenes)

---

## 📋 COMANDO PRINCIPAL

```bash
cd /Applications/um/vivero

# Ejecutar con proveedores múltiples y deduplicación
python3 wc_image_automation.py \
  --target with-images \
  --assign-mode append-gallery \
  --providers unsplash,pexels,inaturalist,flickr \
  --global-dedupe \
  --batch-size 25 \
  --delay 3 \
  --max-success 538 \
  --enrich-queries \
  --log-level INFO \
  2>&1 | tee logs/galeria_imagenes_$(date +%Y%m%d_%H%M%S).log
```

---

## ⚙️ PARÁMETROS EXPLICADOS

| Parámetro | Valor | Explicación |
|-----------|-------|-------------|
| `--target` | `with-images` | Solo productos que ya tienen imágenes |
| `--assign-mode` | `append-gallery` | **Agregar a galería sin reemplazar** |
| `--providers` | `unsplash,pexels,inaturalist,flickr` | Múltiples fuentes |
| `--global-dedupe` | flag | Evita duplicados entre productos |
| `--batch-size` | `25` | Procesar 25 productos por vez |
| `--delay` | `3` | 3 segundos entre requests |
| `--max-success` | `538` | Máximo 538 productos exitosos |
| `--enrich-queries` | flag | Mejores búsquedas usando descripción |

---

## 🎯 ESTRATEGIA

### Ejecución Iterativa
Para agregar múltiples imágenes, ejecutar el comando **2-3 veces**:

```bash
# Primera ejecución - Agrega imagen #2
python3 wc_image_automation.py \
  --target with-images \
  --assign-mode append-gallery \
  --providers unsplash,inaturalist \
  --global-dedupe \
  --batch-size 25 \
  --delay 3 \
  --max-success 538 \
  2>&1 | tee logs/galeria_img2_$(date +%Y%m%d_%H%M%S).log

# Esperar 5 minutos para cooldown de APIs

# Segunda ejecución - Agrega imagen #3
python3 wc_image_automation.py \
  --target with-images \
  --assign-mode append-gallery \
  --providers pexels,flickr \
  --global-dedupe \
  --batch-size 25 \
  --delay 3 \
  --max-success 538 \
  2>&1 | tee logs/galeria_img3_$(date +%Y%m%d_%H%M%S).log
```

---

## 📊 MONITOREO

### Ver progreso en tiempo real:
```bash
tail -f logs/galeria_imagenes_*.log
```

### Verificar productos con múltiples imágenes:
```bash
# Después de ejecutar
grep -c "Successfully assigned" logs/galeria_imagenes_*.log
```

---

## ⏱️ TIEMPO ESTIMADO

- **Primera ejecución:** 2-3 horas (538 productos)
- **Segunda ejecución:** 2-3 horas (538 productos)
- **Total:** 4-6 horas

**Recomendación:** Ejecutar durante la noche o tarde

---

## 🚨 IMPORTANTE

### Antes de Ejecutar
1. ✅ Verificar que .env tiene las API keys:
   - UNSPLASH_API_KEY
   - PEXELS_API_KEY
   - FLICKR_API_KEY (opcional)

2. ✅ Verificar credenciales WordPress:
   - WP_USERNAME
   - WP_APP_PASSWORD

### Verificación .env:
```bash
cat /Applications/um/vivero/.env | grep -E "(UNSPLASH|PEXELS|FLICKR|WP_)"
```

---

## 🎯 DESPUÉS DE EJECUTAR

### Auditoría Día 3:
```bash
python3 AUDITORIA_SEO_COMPLETA.py \
  --url https://viveroloscocos.com.ar \
  --key ck_ac71d70680d0ab93fbc6aa00009ae2811d024b45 \
  --secret cs_27fc77bf74e39397d4c4d42120e166469590258b \
  --export AUDITORIA_DIA_3_FINAL.md
```

**Score esperado:** 91-92/100

---

## 💡 ALTERNATIVA: DRY-RUN PRIMERO

Para probar sin hacer cambios:

```bash
python3 wc_image_automation.py \
  --target with-images \
  --assign-mode append-gallery \
  --providers unsplash,pexels \
  --global-dedupe \
  --batch-size 5 \
  --dry-run \
  --log-level DEBUG
```

Revisar output y luego ejecutar sin `--dry-run`.

---

## 📈 IMPACTO ESPERADO

### Con 2-3 imágenes por producto:
- Mejor SEO de imágenes
- Mayor engagement visual
- Mejor ranking en Google Images
- +2-3 puntos en score SEO

### Proyección:
- Score actual: 88.8
- Score después: 91-92
- Productos excelentes: 250-300 (de 212)

---

*Guía creada: 4-Oct-2025 09:50 ART*  
*Ejecutar cuando: Tarde/noche Día 3*  
*Duración: 4-6 horas total*
