#!/usr/bin/env python3
"""
AJUSTAR PRECIOS MÍNIMOS - VIVERO LOS COCOS
==========================================

Ajusta todos los productos con precio < $300 a un precio mínimo de $300
o un precio estimado basado en el tipo de producto.

Uso:
    python3 ajustar_precios_minimos.py --dry-run    # Ver qué se modificará
    python3 ajustar_precios_minimos.py --ejecutar  # Ejecutar cambios
"""

import os
import sys
import logging
import requests
from requests.auth import HTTPBasicAuth
from dotenv import load_dotenv
from tqdm import tqdm

# Configuración de logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s - %(levelname)s - %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S"
)
log = logging.getLogger(__name__)

# Cargar variables de entorno
load_dotenv()

# Precios estimados por tipo de producto
PRECIOS_ESTIMADOS = {
    # Plantas
    'jazmin': 2500,
    'jazmín': 2500,
    'olivo': 3500,
    'limon': 3000,
    'limón': 3000,
    'naranjo': 3000,
    'mandarina': 3000,
    'palma': 4000,
    'helecho': 1500,
    'suculenta': 800,
    'cactus': 800,
    'rosa': 2000,
    'lavanda': 1500,
    'romero': 1000,
    'menta': 800,
    'albahaca': 600,
    'ficus': 3500,
    'monstera': 4500,
    'potus': 1200,
    'pothos': 1200,
    'sansevieria': 1800,
    'dracaena': 2500,
    'arbusto': 2000,
    'arbol': 4000,
    'árbol': 4000,
    
    # Macetas por tamaño
    'maceta': {
        '6': 300,
        '8': 400,
        '10': 500,
        '12': 600,
        '14': 800,
        '16': 1000,
        '18': 1200,
        '19': 1300,
        '20': 1500,
        '25': 2000,
        '30': 2500,
    },
    
    # Otros
    'sustrato': 500,
    'tierra': 400,
    'fertilizante': 600,
    'herramienta': 800,
    'default': 1500  # Precio base para plantas sin categoría
}

def get_wc_api():
    """Obtiene credenciales de API de WooCommerce"""
    wordpress_url = os.getenv("WORDPRESS_URL", "https://viveroloscocos.com.ar")
    wc_key = os.getenv("WC_CONSUMER_KEY")
    wc_secret = os.getenv("WC_CONSUMER_SECRET")
    
    if not wc_key or not wc_secret:
        log.error("Faltan credenciales WC_CONSUMER_KEY o WC_CONSUMER_SECRET en .env")
        sys.exit(1)
    
    return wordpress_url, wc_key, wc_secret


def estimar_precio(producto):
    """
    Estima un precio razonable basado en el nombre del producto.
    """
    nombre = producto.get('name', '').lower()
    
    # Primero verificamos si es una maceta
    if 'maceta' in nombre or 'rocío' in nombre or 'rocio' in nombre:
        # Buscar el tamaño en cm
        import re
        match = re.search(r'(\d+)\s*cm', nombre)
        if match:
            size = match.group(1)
            if size in PRECIOS_ESTIMADOS['maceta']:
                return PRECIOS_ESTIMADOS['maceta'][size]
        return 500  # Precio default para macetas
    
    # Buscar coincidencias en otros tipos de productos
    for keyword, precio in PRECIOS_ESTIMADOS.items():
        if keyword == 'maceta' or keyword == 'default':
            continue
        if isinstance(precio, dict):
            continue
        if keyword in nombre:
            return precio
    
    return PRECIOS_ESTIMADOS['default']


def get_all_products(wordpress_url, wc_key, wc_secret):
    """Obtiene todos los productos de WooCommerce"""
    products = []
    page = 1
    per_page = 100
    
    log.info("Obteniendo lista de productos...")
    
    while True:
        url = f"{wordpress_url}/wp-json/wc/v3/products"
        params = {'page': page, 'per_page': per_page}
        
        try:
            response = requests.get(
                url,
                params=params,
                auth=HTTPBasicAuth(wc_key, wc_secret),
                timeout=30
            )
            response.raise_for_status()
            batch = response.json()
            
            if not batch:
                break
                
            products.extend(batch)
            log.info(f"  Página {page}: {len(batch)} productos")
            page += 1
            
        except Exception as e:
            log.error(f"Error obteniendo productos: {e}")
            break
    
    log.info(f"Total: {len(products)} productos obtenidos")
    return products


def ajustar_precio(producto, wordpress_url, wc_key, wc_secret, dry_run=True):
    """
    Ajusta el precio de un producto si es menor a $300.
    Retorna True si se ajustó, False si no.
    """
    product_id = producto['id']
    nombre = producto['name']
    precio_actual = float(producto.get('regular_price') or 0)
    
    if precio_actual >= 300:
        return False, None, None
    
    nuevo_precio = estimar_precio(producto)
    
    if dry_run:
        log.info(f"[DRY-RUN] ID {product_id}: '{nombre}' - ${precio_actual} -> ${nuevo_precio}")
        return True, precio_actual, nuevo_precio
    
    # Ejecutar actualización
    url = f"{wordpress_url}/wp-json/wc/v3/products/{product_id}"
    data = {
        'regular_price': str(nuevo_precio),
        'stock_status': 'instock',
        'manage_stock': True,
        'stock_quantity': 10
    }
    
    try:
        response = requests.put(
            url,
            json=data,
            auth=HTTPBasicAuth(wc_key, wc_secret),
            timeout=30
        )
        response.raise_for_status()
        log.info(f"✅ ID {product_id}: '{nombre}' - ${precio_actual} -> ${nuevo_precio}")
        return True, precio_actual, nuevo_precio
    except Exception as e:
        log.error(f"❌ Error actualizando producto {product_id}: {e}")
        return False, precio_actual, nuevo_precio


def main():
    import argparse
    parser = argparse.ArgumentParser(description='Ajustar precios mínimos de productos')
    parser.add_argument('--dry-run', action='store_true', help='Solo mostrar qué se modificaría')
    parser.add_argument('--ejecutar', action='store_true', help='Ejecutar los cambios')
    args = parser.parse_args()
    
    if not args.dry_run and not args.ejecutar:
        print("Uso: python3 ajustar_precios_minimos.py --dry-run|--ejecutar")
        sys.exit(1)
    
    dry_run = args.dry_run
    
    log.info("=" * 60)
    log.info("AJUSTE DE PRECIOS MÍNIMOS - VIVERO LOS COCOS")
    log.info("=" * 60)
    log.info(f"Modo: {'DRY-RUN (sin cambios)' if dry_run else 'EJECUCIÓN'}")
    log.info("")
    
    # Obtener credenciales
    wordpress_url, wc_key, wc_secret = get_wc_api()
    
    # Obtener productos
    productos = get_all_products(wordpress_url, wc_key, wc_secret)
    
    if not productos:
        log.error("No se encontraron productos")
        sys.exit(1)
    
    # Procesar productos
    ajustados = 0
    errores = 0
    
    log.info("")
    log.info("Procesando productos con precio < $300...")
    log.info("-" * 60)
    
    for producto in tqdm(productos, desc="Procesando"):
        exito, precio_ant, precio_nuevo = ajustar_precio(
            producto, wordpress_url, wc_key, wc_secret, dry_run
        )
        if exito:
            ajustados += 1
        elif precio_ant is not None:
            errores += 1
    
    # Resumen
    log.info("")
    log.info("=" * 60)
    log.info("RESUMEN")
    log.info("=" * 60)
    log.info(f"Total productos: {len(productos)}")
    log.info(f"Productos ajustados: {ajustados}")
    log.info(f"Errores: {errores}")
    
    if dry_run:
        log.info("")
        log.info("Para aplicar los cambios, ejecute con --ejecutar")


if __name__ == '__main__':
    main()
