#!/usr/bin/env python3
"""
EXTRAER PRECIOS REALES - VIVERO LOS COCOS
==========================================

Extrae precios del inventario original y crea CSV de mapeo
para actualizar productos en WooCommerce.

Uso:
    python3 extraer_precios_reales.py
"""

import csv
import json
import logging
import os
import sys

import requests
from dotenv import load_dotenv

load_dotenv()

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s %(levelname)s: %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S"
)
log = logging.getLogger(__name__)


def get_wc_api():
    """Get WooCommerce API credentials"""
    wordpress_url = os.getenv('WORDPRESS_URL', '').rstrip('/')
    wc_key = os.getenv('WC_CONSUMER_KEY', '')
    wc_secret = os.getenv('WC_CONSUMER_SECRET', '')
    
    if not all([wordpress_url, wc_key, wc_secret]):
        raise ValueError("Missing WooCommerce credentials in .env")
    
    return wordpress_url, wc_key, wc_secret


def cargar_precios_inventario():
    """Carga precios del archivo de inventario original"""
    precios = {}
    
    archivos = [
        'INVENTARIOTODASLASHOJAS_formatted.csv',
        'scripts/data/INVENTARIOTODASLASHOJAS.csv'
    ]
    
    for archivo in archivos:
        if not os.path.exists(archivo):
            continue
        
        log.info(f"Leyendo {archivo}...")
        
        try:
            with open(archivo, 'r', encoding='utf-8') as f:
                reader = csv.DictReader(f)
                for row in reader:
                    sku = row.get('SKU', '').strip()
                    precio = row.get('Regular price', '').strip() or row.get('Precio', '').strip()
                    stock = row.get('Stock', '').strip() or row.get('Cantidad', '').strip()
                    
                    if sku and precio:
                        try:
                            precio_num = float(precio)
                            if precio_num > 0:
                                precios[sku] = {
                                    'precio': precio_num,
                                    'stock': int(stock) if stock else 10
                                }
                        except ValueError:
                            pass
        except Exception as e:
            log.warning(f"Error leyendo {archivo}: {e}")
    
    log.info(f"Precios cargados: {len(precios)} SKUs")
    return precios


def obtener_productos_woocommerce(wordpress_url, wc_key, wc_secret):
    """Obtiene todos los productos de WooCommerce"""
    productos = []
    page = 1
    
    log.info("Obteniendo productos de WooCommerce...")
    
    while page <= 6:
        url = f"{wordpress_url}/wp-json/wc/v3/products"
        params = {"per_page": 100, "page": page, "status": "publish"}
        
        try:
            r = requests.get(url, params=params, auth=(wc_key, wc_secret), timeout=30)
            r.raise_for_status()
            data = r.json()
            
            if not data:
                break
            
            productos.extend(data)
            log.info(f"  Página {page}: {len(data)} productos")
            page += 1
            
        except Exception as e:
            log.error(f"Error obteniendo productos: {e}")
            break
    
    log.info(f"Total productos obtenidos: {len(productos)}")
    return productos


def crear_csv_mapeo(precios_inventario, productos_wc, output_file='precios_reales_mapeo.csv'):
    """Crea CSV con mapeo ID → Precio para productos que tienen match"""
    
    matches = []
    sin_match = []
    
    for producto in productos_wc:
        producto_id = producto['id']
        sku = producto.get('sku', '').strip()
        nombre = producto.get('name', '')
        
        if sku in precios_inventario:
            datos_precio = precios_inventario[sku]
            matches.append({
                'id': producto_id,
                'sku': sku,
                'nombre': nombre,
                'precio': datos_precio['precio'],
                'stock': datos_precio['stock']
            })
        else:
            sin_match.append({
                'id': producto_id,
                'sku': sku,
                'nombre': nombre
            })
    
    # Escribir CSV con matches
    with open(output_file, 'w', encoding='utf-8', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=['id', 'sku', 'nombre', 'precio', 'stock'])
        writer.writeheader()
        writer.writerows(matches)
    
    log.info(f"✅ CSV creado: {output_file}")
    log.info(f"   Productos con precio: {len(matches)}")
    log.info(f"   Productos sin precio: {len(sin_match)}")
    
    # Escribir CSV con productos sin match
    sin_match_file = 'productos_sin_precio.csv'
    with open(sin_match_file, 'w', encoding='utf-8', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=['id', 'sku', 'nombre'])
        writer.writeheader()
        writer.writerows(sin_match)
    
    log.info(f"✅ CSV sin precios: {sin_match_file}")
    
    return len(matches), len(sin_match)


def generar_reporte(total_matches, total_sin_match):
    """Genera reporte con estadísticas"""
    
    total = total_matches + total_sin_match
    
    reporte = f"""
╔══════════════════════════════════════════════════════════════╗
║                                                              ║
║     📊 EXTRACCIÓN DE PRECIOS REALES - COMPLETADA           ║
║                                                              ║
╠══════════════════════════════════════════════════════════════╣
║                                                              ║
║  RESULTADOS:                                                 ║
║                                                              ║
║  Total productos WooCommerce: {total:3d}                              ║
║  Con precio en inventario:    {total_matches:3d} ({total_matches/total*100:5.1f}%)              ║
║  Sin precio en inventario:    {total_sin_match:3d} ({total_sin_match/total*100:5.1f}%)              ║
║                                                              ║
╠══════════════════════════════════════════════════════════════╣
║                                                              ║
║  ARCHIVOS GENERADOS:                                         ║
║                                                              ║
║  ✅ precios_reales_mapeo.csv                                 ║
║     → {total_matches} productos con ID, SKU, precio y stock          ║
║     → Listo para usar con configurar_precios_stock.py       ║
║                                                              ║
║  ✅ productos_sin_precio.csv                                 ║
║     → {total_sin_match} productos que necesitan precio manual         ║
║                                                              ║
╠══════════════════════════════════════════════════════════════╣
║                                                              ║
║  PRÓXIMOS PASOS:                                             ║
║                                                              ║
║  1. Aplicar precios reales ({total_matches} productos):                  ║
║     $ python3 configurar_precios_stock.py \\                 ║
║         --modo custom --csv precios_reales_mapeo.csv         ║
║                                                              ║
║  2. Configurar {total_sin_match} productos restantes:                    ║
║     Opción A: Precio por categoría                          ║
║     $ python3 configurar_precios_stock.py --modo categoria  ║
║                                                              ║
║     Opción B: Completar CSV manualmente                     ║
║     Editar productos_sin_precio.csv y agregar precios       ║
║                                                              ║
╚══════════════════════════════════════════════════════════════╝
"""
    
    print(reporte)
    
    # Guardar reporte
    with open('REPORTE_EXTRACCION_PRECIOS.txt', 'w', encoding='utf-8') as f:
        f.write(reporte)


def main():
    log.info("=" * 60)
    log.info("EXTRACCIÓN DE PRECIOS REALES - VIVERO LOS COCOS")
    log.info("=" * 60)
    
    # 1. Cargar precios del inventario
    precios_inventario = cargar_precios_inventario()
    
    if not precios_inventario:
        log.error("❌ No se encontraron precios en el inventario")
        sys.exit(1)
    
    # 2. Obtener productos de WooCommerce
    wordpress_url, wc_key, wc_secret = get_wc_api()
    productos_wc = obtener_productos_woocommerce(wordpress_url, wc_key, wc_secret)
    
    if not productos_wc:
        log.error("❌ No se encontraron productos en WooCommerce")
        sys.exit(1)
    
    # 3. Crear CSV de mapeo
    total_matches, total_sin_match = crear_csv_mapeo(precios_inventario, productos_wc)
    
    # 4. Generar reporte
    generar_reporte(total_matches, total_sin_match)
    
    log.info("=" * 60)
    log.info("✅ Proceso completado exitosamente")
    log.info("=" * 60)


if __name__ == '__main__':
    main()
