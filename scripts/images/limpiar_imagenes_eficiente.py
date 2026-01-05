#!/usr/bin/env python3
"""
LIMPIEZA EFICIENTE DE IMÁGENES - VIVERO LOS COCOS
==================================================

Estrategia optimizada:
1. Identificar y eliminar imágenes SIN USAR primero (1477 de 2000)
2. Luego analizar duplicados solo en las 523 restantes
3. Consolidar duplicados por hash
4. Re-asignar productos correctamente

Uso:
    python3 limpiar_imagenes_eficiente.py --paso 1 --dry-run    # Ver imágenes sin usar
    python3 limpiar_imagenes_eficiente.py --paso 1 --ejecutar   # Eliminar sin usar
    python3 limpiar_imagenes_eficiente.py --paso 2 --dry-run    # Analizar duplicados
    python3 limpiar_imagenes_eficiente.py --paso 2 --ejecutar   # Consolidar duplicados
"""

import argparse
import hashlib
import logging
import os
import sys
from collections import defaultdict

import requests
from dotenv import load_dotenv
from tqdm import tqdm

load_dotenv()

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s %(levelname)s: %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S"
)
log = logging.getLogger(__name__)


def get_credentials():
    wp_url = os.getenv('WORDPRESS_URL', '').rstrip('/')
    wc_key = os.getenv('WC_CONSUMER_KEY', '')
    wc_secret = os.getenv('WC_CONSUMER_SECRET', '')
    wp_user = os.getenv('WP_USERNAME', '')
    wp_pass = os.getenv('WP_APP_PASSWORD', '')
    
    if not all([wp_url, wc_key, wc_secret, wp_user, wp_pass]):
        raise ValueError("Missing credentials in .env")
    
    return wp_url, wc_key, wc_secret, wp_user, wp_pass


def obtener_productos(wp_url, wc_key, wc_secret):
    """Obtiene todos los productos publicados (sin límite inferior al real)."""
    log.info("Obteniendo productos...")
    productos = []
    page = 1
    total_pages = None

    while True:
        r = requests.get(
            f'{wp_url}/wp-json/wc/v3/products',
            auth=(wc_key, wc_secret),
            params={'per_page': 100, 'page': page},
            timeout=30
        )

        if r.status_code != 200:
            log.warning(f"WooCommerce API devolvió {r.status_code} en la página {page}")
            break

        if total_pages is None:
            try:
                total_pages = int(r.headers.get('X-WP-TotalPages') or 0)
            except (TypeError, ValueError):
                total_pages = 0

        data = r.json() or []
        if not data:
            break

        productos.extend(data)

        if total_pages and page >= total_pages:
            break

        page += 1

    log.info(f"Total productos: {len(productos)}")
    return productos


def obtener_media(wp_url, wp_user, wp_pass):
    """Obtiene todas las imágenes del Media Library sin limitarse a 20 páginas."""
    log.info("Obteniendo imágenes del Media Library...")
    media_items = []
    page = 1
    total_pages = None

    while True:
        try:
            r = requests.get(
                f'{wp_url}/wp-json/wp/v2/media',
                params={'per_page': 100, 'page': page, 'media_type': 'image'},
                auth=(wp_user, wp_pass),
                timeout=60
            )
        except requests.RequestException as e:
            log.warning(f"Error obteniendo media página {page}: {e}")
            break

        if r.status_code == 400 and 'rest_post_invalid_page_number' in r.text:
            break
        if r.status_code != 200:
            log.warning(f"Media API devolvió {r.status_code} en la página {page}")
            break

        if total_pages is None:
            try:
                total_pages = int(r.headers.get('X-WP-TotalPages') or 0)
            except (TypeError, ValueError):
                total_pages = 0

        data = r.json() or []
        if not data:
            break

        media_items.extend(data)

        if page % 25 == 0:
            log.info(f"  Página {page}{f'/{total_pages}' if total_pages else ''} -> {len(media_items)} acumuladas")

        if total_pages and page >= total_pages:
            break

        page += 1

    log.info(f"Total imágenes: {len(media_items)}")
    return media_items


def paso_1_eliminar_sin_usar(dry_run=True):
    """Paso 1: Eliminar imágenes no usadas en productos"""
    log.info("=" * 60)
    log.info("PASO 1: ELIMINAR IMÁGENES SIN USAR")
    log.info("=" * 60)
    
    wp_url, wc_key, wc_secret, wp_user, wp_pass = get_credentials()
    
    # Obtener datos
    productos = obtener_productos(wp_url, wc_key, wc_secret)
    media_items = obtener_media(wp_url, wp_user, wp_pass)
    
    # Identificar imágenes usadas
    imagenes_usadas = set()
    for prod in productos:
        for img in prod.get('images', []):
            if img.get('id'):
                imagenes_usadas.add(img['id'])
    
    # Identificar sin usar
    imagenes_sin_usar = [
        item for item in media_items 
        if item['id'] not in imagenes_usadas
    ]
    
    total_imagenes = len(media_items)
    sin_usar = len(imagenes_sin_usar)
    porcentaje = (sin_usar / total_imagenes * 100) if total_imagenes else 0

    log.info(f"\n📊 Estadísticas:")
    log.info(f"   Total imágenes: {total_imagenes}")
    log.info(f"   Usadas en productos: {len(imagenes_usadas)}")
    log.info(f"   Sin usar: {sin_usar} ({porcentaje:.1f}%)")
    
    if dry_run:
        log.info("\n🔍 DRY-RUN: No se eliminarán imágenes")
        log.info(f"   Se eliminarían {len(imagenes_sin_usar)} imágenes")
        
        # Mostrar ejemplos
        log.info(f"\n   Ejemplos de imágenes sin usar:")
        for i, item in enumerate(imagenes_sin_usar[:10], 1):
            title = item.get('title', {}).get('rendered', 'Sin título')
            log.info(f"   {i}. ID {item['id']}: {title}")
        
        return
    
    # Ejecutar eliminación
    log.info(f"\n🗑️  Eliminando {len(imagenes_sin_usar)} imágenes sin usar...")
    
    errores = 0
    for item in tqdm(imagenes_sin_usar, desc="Eliminando"):
        r = requests.delete(
            f"{wp_url}/wp-json/wp/v2/media/{item['id']}",
            params={'force': True},
            auth=(wp_user, wp_pass),
            timeout=30
        )
        
        if r.status_code not in [200, 204]:
            errores += 1
    
    log.info(f"\n✅ Eliminación completada:")
    log.info(f"   Eliminadas: {len(imagenes_sin_usar) - errores}")
    log.info(f"   Errores: {errores}")


def calcular_hash(url):
    """Calcula hash SHA1 de imagen"""
    try:
        r = requests.get(url, timeout=30)
        r.raise_for_status()
        return hashlib.sha1(r.content).hexdigest()
    except:
        return None


def paso_2_consolidar_duplicados(dry_run=True):
    """Paso 2: Consolidar imágenes duplicadas por hash"""
    log.info("=" * 60)
    log.info("PASO 2: CONSOLIDAR DUPLICADOS POR HASH")
    log.info("=" * 60)
    
    wp_url, wc_key, wc_secret, wp_user, wp_pass = get_credentials()
    
    # Obtener datos
    productos = obtener_productos(wp_url, wc_key, wc_secret)
    media_items = obtener_media(wp_url, wp_user, wp_pass)
    
    # Solo analizar imágenes usadas
    imagenes_usadas_ids = set()
    for prod in productos:
        for img in prod.get('images', []):
            if img.get('id'):
                imagenes_usadas_ids.add(img['id'])
    
    imagenes_usadas = [m for m in media_items if m['id'] in imagenes_usadas_ids]
    
    log.info(f"\n📊 Analizando {len(imagenes_usadas)} imágenes usadas...")
    
    # Calcular hashes
    hash_to_images = defaultdict(list)
    
    for item in tqdm(imagenes_usadas, desc="Calculando hashes"):
        url = item.get('source_url')
        if not url:
            continue
        
        img_hash = calcular_hash(url)
        if img_hash:
            hash_to_images[img_hash].append(item)
    
    # Identificar duplicados
    duplicados = {k: v for k, v in hash_to_images.items() if len(v) > 1}
    
    log.info(f"\n📊 Resultados:")
    log.info(f"   Hashes únicos: {len(hash_to_images)}")
    log.info(f"   Duplicados: {len(duplicados)}")
    
    if not duplicados:
        log.info("   ✅ No hay duplicados para consolidar")
        return
    
    # Mapear uso en productos
    image_to_productos = defaultdict(list)
    for prod in productos:
        for img in prod.get('images', []):
            if img.get('id'):
                image_to_productos[img['id']].append(prod['id'])
    
    # Generar plan de consolidación
    total_a_eliminar = 0
    plan = []
    
    for img_hash, imagenes in duplicados.items():
        # Elegir canónica (la más usada)
        uso = [(img, len(image_to_productos.get(img['id'], []))) for img in imagenes]
        uso.sort(key=lambda x: x[1], reverse=True)
        
        canonica = uso[0][0]
        a_eliminar = [img for img, _ in uso[1:]]
        total_a_eliminar += len(a_eliminar)
        
        # Productos afectados
        prods_afectados = set()
        for img in imagenes:
            prods_afectados.update(image_to_productos.get(img['id'], []))
        
        plan.append({
            'hash': img_hash,
            'canonica': canonica,
            'duplicados': a_eliminar,
            'productos': list(prods_afectados)
        })
    
    log.info(f"\n📋 Plan de consolidación:")
    log.info(f"   Grupos de duplicados: {len(plan)}")
    log.info(f"   Imágenes a eliminar: {total_a_eliminar}")
    log.info(f"   Productos a actualizar: {sum(len(p['productos']) for p in plan)}")
    
    # Mostrar ejemplos
    log.info(f"\n   Ejemplos:")
    for i, p in enumerate(plan[:5], 1):
        log.info(f"   {i}. Hash {p['hash'][:16]}...")
        log.info(f"      Canónica: ID {p['canonica']['id']}")
        log.info(f"      Duplicados: {len(p['duplicados'])} imágenes")
        log.info(f"      Productos: {len(p['productos'])}")
    
    if dry_run:
        log.info("\n🔍 DRY-RUN: No se aplicarán cambios")
        return
    
    # Ejecutar consolidación
    log.info(f"\n🔄 Consolidando duplicados...")
    
    for p in tqdm(plan, desc="Consolidando"):
        canonica_id = p['canonica']['id']
        
        for prod_id in p['productos']:
            # Actualizar producto con solo la imagen canónica
            r = requests.put(
                f"{wp_url}/wp-json/wc/v3/products/{prod_id}",
                json={'images': [{'id': canonica_id}]},
                auth=(wc_key, wc_secret),
                timeout=30
            )
        
        # Eliminar duplicados
        for img in p['duplicados']:
            requests.delete(
                f"{wp_url}/wp-json/wp/v2/media/{img['id']}",
                params={'force': True},
                auth=(wp_user, wp_pass),
                timeout=30
            )
    
    log.info(f"\n✅ Consolidación completada")


def main():
    parser = argparse.ArgumentParser(description='Limpieza eficiente de imágenes')
    parser.add_argument('--paso', type=int, choices=[1, 2], required=True,
                        help='1=Eliminar sin usar, 2=Consolidar duplicados')
    parser.add_argument('--dry-run', action='store_true', help='Simular sin cambios')
    parser.add_argument('--ejecutar', action='store_true', help='Ejecutar cambios reales')
    args = parser.parse_args()
    
    if not args.dry_run and not args.ejecutar:
        log.error("Especifica --dry-run o --ejecutar")
        sys.exit(1)
    
    if args.paso == 1:
        paso_1_eliminar_sin_usar(dry_run=args.dry_run)
    elif args.paso == 2:
        paso_2_consolidar_duplicados(dry_run=args.dry_run)
    
    log.info("=" * 60)
    log.info("Proceso completado")


if __name__ == '__main__':
    main()
