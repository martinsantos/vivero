# Automatización de Imágenes para WooCommerce

Este módulo (`wc_image_automation.py`) busca, descarga, procesa y asigna imágenes libres de derechos a productos de WooCommerce que no tienen imagen. Funciona en el entorno local de WordPress y respeta tus preferencias: código simple, sin duplicación, `.env` solo lectura, batching, logging, reintentos y reanudación.

## Requisitos
- Python 3.9+ y dependencias instaladas (ver `requirements.txt`).
- WordPress local accesible (p.ej. `http://localhost:8080` o vía Nginx `http://localhost:8082`).
- Credenciales para subir media mediante Application Password:
  - `WP_USERNAME`
  - `WP_APP_PASSWORD` (WordPress permite espacios en la clave)
- Al menos una API key de proveedor de imágenes:
  - `UNSPLASH_API_KEY` (recomendado)
  - Opcional: `PIXABAY_API_KEY`, `PEXELS_API_KEY`
- Variables de entorno principales:
  - `WORDPRESS_URL`, `WP_USERNAME`, `WP_APP_PASSWORD`
  - `UNSPLASH_API_KEY`, `PIXABAY_API_KEY`, `PEXELS_API_KEY`

Notas:
- `.env` (si existe) es leído vía `python-dotenv`, no se sobrescribe.
- Si usas Nginx como proxy, asegúrate de reenviar el header de Authorization:
  `proxy_set_header Authorization $http_authorization;`

## Comandos rápidos (Makefile)
El repositorio incluye un `Makefile` con targets listos.

- Ver ayuda:
```bash
make wc-images-help
```

- Dry-run (no sube ni asigna en WordPress):
```bash
make wc-images-dry-run PROVIDERS=unsplash BATCH=10 DELAY=1 LOG=INFO SIZE=1200x1200 QUALITY=85 MINRES=800x600
```

- Ejecución real (sube y asigna):
```bash
make wc-images-run PROVIDERS=unsplash BATCH=10 DELAY=1 LOG=INFO SIZE=1200x1200 QUALITY=85 MINRES=800x600
```

- Reanudar (solo pendientes/fallidos, usa estado en `.wc_image_automation_state.json`):
```bash
make wc-images-resume PROVIDERS=unsplash BATCH=10 DELAY=1 LOG=INFO
```

Parámetros personalizables (vía variables Make):
- `PROVIDERS`: orden de proveedores, p.ej. `unsplash,pixabay,pexels`
- `BATCH`: tamaño de lote
- `DELAY`: segundos entre operaciones (evita rate limits/timeouts)
- `SIZE`: tamaño objetivo de salida, p.ej. `1200x1200`
- `QUALITY`: 1-100
- `MINRES`: resolución mínima buscada, p.ej. `800x600`
- `WATERMARK`: texto de marca opcional (se muestra en la imagen)
- `TARGET`: qué productos procesar: `missing` (sin imágenes), `with-images` (ya tienen), `all` (todos)
- `ASSIGN`: cómo asignar: `featured` (reemplaza) o `append-gallery` (agrega sin quitar las existentes)
- `ENRICH`: `1/true/yes/on` para enriquecer queries usando descripción, tags y atributos
- `PREFIX`: prefijo opcional para todas las búsquedas, p.ej. `VIVERO DE PLANTAS`

Ejemplo con varios proveedores y marca de agua:
```bash
make wc-images-run PROVIDERS=unsplash,pixabay,pexels WATERMARK="Vivero" BATCH=12 DELAY=2
```

Ejemplo: agregar nuevas imágenes a productos que ya tienen (sin reemplazar), enriqueciendo búsquedas:
```bash
make wc-images-run TARGET=with-images ASSIGN=append-gallery ENRICH=1 PROVIDERS=unsplash
```

Ejemplo: reemplazar solo la imagen destacada manteniendo la galería, con prefijo temático en búsquedas:
```bash
make wc-images-run PREFIX='VIVERO DE PLANTAS' ASSIGN=replace-featured ENRICH=1 PROVIDERS=unsplash
```

## Logs y estado
- Logs: `logs/wc_image_automation.log`
- Estado de reanudación: `.wc_image_automation_state.json`

## Funcionamiento interno (resumen)
- `get_products_by_target()`: selecciona productos según `TARGET` (`missing`, `with-images`, `all`).
- `search_free_images()`: consulta Unsplash/Pixabay/Pexels; genera múltiples queries normalizadas con sinónimos.
- `download_to_cache()`: descarga en streaming con reintentos y tope de 15MB.
- `process_image()`: redimensiona, opcional watermark, optimiza y guarda WebP/JPEG.
- `upload_to_wordpress()`: sube a la librería de medios (requiere Application Password).
- `assign_image_to_product()`: asigna la imagen al producto. Soporta `featured` (reemplaza) y `append-gallery` (agrega manteniendo existentes).
  Además, `replace-featured`: reemplaza solo la destacada y conserva la galería.
- `batch_processor()`: batching, progreso, logging, errores y reanudación.
- Enriquecimiento de queries: si se activa, se usan también descripción, tags y atributos (además de nombre y categorías).

## Solución de problemas
- Timeouts o rate limits: aumenta `DELAY`, reduce `BATCH`, o agrega más proveedores.
- Imágenes muy grandes: el sistema prioriza URLs más manejables y limita descargas a 15MB.
- Errores de imagen truncada: se toleran automáticamente (`LOAD_TRUNCATED_IMAGES`).
- Autenticación fallida: revisa `WORDPRESS_URL` y que Nginx reenvíe `Authorization` si aplica.

## Seguridad
- Mantén tus API Keys en variables de entorno. No las comprometas en repositorios públicos.
- No se escribe en `.env` de forma automática.

## Ejecución directa (sin Make)
```bash
python3 wc_image_automation.py \
  --providers unsplash,pixabay,pexels \
  --batch-size 10 \
  --delay 1 \
  --log-level INFO \
  --image-size 1200x1200 \
  --quality 85 \
  --min-resolution 800x600 \
  --target with-images \
  --assign-mode replace-featured \
  --enrich-queries \
  --query-prefix "VIVERO DE PLANTAS" \
  --resume
```
