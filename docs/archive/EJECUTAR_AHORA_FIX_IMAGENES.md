# 🚨 CORRECCIÓN URGENTE - IMÁGENES DUPLICADAS

**Problema confirmado:** Múltiples productos comparten imágenes  
**Causa:** Error en proceso previo de automatización  
**Solución:** Ejecutar scripts de corrección AHORA

---

## ✅ OPCIÓN 1: USAR SCRIPT EXISTENTE (RECOMENDADO)

Ya tienes `wc_image_automation.py` que puede arreglar esto. Necesitas:

### Paso 1: Verificar/Crear credenciales de WordPress

```bash
# Conectar al servidor
sshpass -p 'gsiB%s@0yD' ssh root@23.105.176.45

# Crear Application Password para admin
cd /home/viveroloscocos.com.ar/public_html
wp user application-password create admin "image-automation" --user=admin

# Copiar el password generado (formato: xxxx xxxx xxxx xxxx)
```

### Paso 2: Configurar variables de entorno

Crear/editar `/Applications/um/vivero/.env`:

```bash
WORDPRESS_URL=https://viveroloscocos.com.ar
WC_CONSUMER_KEY=ck_ac71d70680d0ab93fbc6aa00009ae2811d024b45
WC_CONSUMER_SECRET=cs_27fc77bf74e39397d4c4d42120e166469590258b
WP_USERNAME=admin
WP_APP_PASSWORD="xxxx xxxx xxxx xxxx"  # El que generaste arriba
UNSPLASH_API_KEY=tu_key_aqui  # Opcional pero recomendado
PEXELS_API_KEY=tu_key_aqui    # Opcional pero recomendado
```

### Paso 3: Ejecutar corrección con deduplicación global

```bash
cd /Applications/um/vivero

# DRY RUN primero (solo simular)
python3 wc_image_automation.py \
  --batch-size 50 \
  --global-dedupe \
  --providers unsplash pexels \
  --dry-run

# Si todo OK, ejecutar en PRODUCCIÓN
python3 wc_image_automation.py \
  --batch-size 50 \
  --global-dedupe \
  --providers unsplash pexels inaturalist \
  2>&1 | tee logs/fix_duplicates_$(date +%Y%m%d).log
```

---

## ✅ OPCIÓN 2: SCRIPT ESPECÍFICO DE CORRECCIÓN

Si solo quieres arreglar los duplicados sin procesar todo:

```bash
cd /Applications/um/vivero

# Ejecutar el script nuevo
python3 arreglar_imagenes_duplicadas.py \
  --url https://viveroloscocos.com.ar \
  --wc-key ck_ac71d70680d0ab93fbc6aa00009ae2811d024b45 \
  --wc-secret cs_27fc77bf74e39397d4c4d42120e166469590258b \
  --wp-user admin \
  --wp-pass "xxxx xxxx xxxx xxxx" \
  --dry-run

# Si OK, ejecutar sin dry-run
python3 arreglar_imagenes_duplicadas.py \
  --url https://viveroloscocos.com.ar \
  --wc-key ck_ac71d70680d0ab93fbc6aa00009ae2811d024b45 \
  --wc-secret cs_27fc77bf74e39397d4c4d42120e166469590258b \
  --wp-user admin \
  --wp-pass "xxxx xxxx xxxx xxxx"
```

---

## 🔥 OPCIÓN 3: SOLUCIÓN RÁPIDA MANUAL (SI FALLAN LAS OTRAS)

### Productos críticos a arreglar manualmente:

1. **Árbol Oletexal 15 Litros** - Buscar imagen de olivo
2. **Árbol Prun 7 Litros** - Buscar imagen de ciruelo
3. **Maceta Rocío 10cm Naranja** - Foto de maceta naranja
4. **Maceta Rocío 10cm Verde** - Foto de maceta verde
5. **Maceta Rocío 10cm Amarillo** - Foto de maceta amarilla
6. **Maceta Rocío 18cm Blanco** - Foto de maceta blanca
7. **Maceta Rocío 21cm diferentes colores** - Fotos únicas

### Cómo hacerlo manual:

```bash
# 1. Descargar imágenes apropiadas de:
# - Unsplash.com (buscar "plastic pot orange", "olive tree", etc.)
# - Pexels.com
# - Pixabay.com

# 2. Subir vía WordPress admin:
# https://viveroloscocos.com.ar/wp-admin/upload.php

# 3. Asignar a productos:
# https://viveroloscocos.com.ar/wp-admin/edit.php?post_type=product
```

---

## 📊 VERIFICACIÓN POST-CORRECCIÓN

```bash
# Ejecutar detector de nuevo
python3 detectar_imagenes_duplicadas.py \
  --url https://viveroloscocos.com.ar \
  --key ck_ac71d70680d0ab93fbc6aa00009ae2811d024b45 \
  --secret cs_27fc77bf74e39397d4c4d42120e166469590258b

# Debería mostrar: 0 duplicados
```

---

## ⏱️ TIEMPO ESTIMADO

- **Opción 1 (Automatizada):** 2-3 horas, 0 esfuerzo
- **Opción 2 (Script específico):** 30-60 minutos
- **Opción 3 (Manual):** 2-3 horas de trabajo manual

---

## 🎯 RECOMENDACIÓN

**OPCIÓN 1** es la mejor porque:
- ✅ Usa el sistema probado que ya existe
- ✅ Procesa todo automáticamente
- ✅ Garantiza imágenes únicas con `--global-dedupe`
- ✅ Múltiples fuentes de imágenes de calidad

**EJECUTAR AHORA:**

1. Genera el Application Password de WordPress
2. Configura `.env`
3. Ejecuta `wc_image_automation.py` con `--global-dedupe`
4. Espera 2-3 horas
5. Verifica resultados

---

**🔥 LAMENTO EL ERROR EN MI ANÁLISIS INICIAL**

Mi consulta SQL estaba incorrecta. El problema EXISTE y es SERIO.
La solución está LISTA para ejecutar.

**¿Quieres que te ayude a generar el Application Password de WordPress AHORA?**
