#!/usr/bin/env python3
"""
CONFIGURAR PRECIOS Y STOCK - VIVERO LOS COCOS
==============================================

Solución al problema crítico: 538/538 productos sin precio ni stock

Este script configura precios y stock para habilitar la funcionalidad
de agregar productos al carrito y realizar compras.

Opciones:
  1. Precios de prueba uniformes ($1000 para todos)
  2. Precios por categoría (diferenciados)
  3. Precio personalizado por producto

Uso:
    python3 configurar_precios_stock.py --modo prueba --dry-run
    python3 configurar_precios_stock.py --modo categoria
    python3 configurar_precios_stock.py --modo custom --csv precios.csv
"""

import argparse
import csv
import logging
import os
import sys
from typing import Dict, List

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


# Configuración de precios por categoría
PRECIOS_CATEGORIA = {
    'plantas': 1500,
    'macetas': 800,
    'herramientas': 500,
    'sustratos': 300,
    'default': 1000
}


def get_wc_api():
    """Get WooCommerce API credentials"""
    wordpress_url = os.getenv('WORDPRESS_URL', '').rstrip('/')
    wc_key = os.getenv('WC_CONSUMER_KEY', '')
    wc_secret = os.getenv('WC_CONSUMER_SECRET', '')
    
    if not all([wordpress_url, wc_key, wc_secret]):
        raise ValueError("Missing WooCommerce credentials in .env")
    
    return wordpress_url, wc_key, wc_secret


def get_all_products(wordpress_url: str, wc_key: str, wc_secret: str) -> List[Dict]:
    """Obtiene todos los productos"""
    all_products = []
    page = 1
    
    log.info("Obteniendo productos de WooCommerce...")
    
    while True:
        url = f"{wordpress_url}/wp-json/wc/v3/products"
        params = {"per_page": 100, "page": page, "status": "publish"}
        
        try:
            r = requests.get(url, params=params, auth=(wc_key, wc_secret), timeout=30)
            r.raise_for_status()
            products = r.json()
            
            if not products:
                break
            
            all_products.extend(products)
            log.info(f"  Página {page}: {len(products)} productos")
            page += 1
            
        except Exception as e:
            log.error(f"Error obteniendo productos: {e}")
            break
    
    log.info(f"Total productos obtenidos: {len(all_products)}")
    return all_products


def determinar_precio_categoria(producto: Dict) -> int:
    """Determina precio basado en categoría del producto"""
    categorias = producto.get('categories', [])
    
    if not categorias:
        return PRECIOS_CATEGORIA['default']
    
    # Buscar coincidencia de categoría
    for categoria in categorias:
        nombre = categoria['name'].lower()
        for key, precio in PRECIOS_CATEGORIA.items():
            if key in nombre:
                return precio
    
    return PRECIOS_CATEGORIA['default']


def configurar_precio_prueba(producto: Dict, wordpress_url: str, wc_key: str, wc_secret: str, dry_run: bool = False) -> bool:
    """Configura precio de prueba uniforme ($1000)"""
    producto_id = producto['id']
    precio = 1000
    
    datos = {
        'regular_price': str(precio),
        'stock_status': 'instock',
        'manage_stock': True,
        'stock_quantity': 10
    }
    
    if dry_run:
        log.info(f"[DRY-RUN] Producto {producto_id}: precio ${precio}, stock 10")
        return True
    
    try:
        url = f"{wordpress_url}/wp-json/wc/v3/products/{producto_id}"
        r = requests.put(url, json=datos, auth=(wc_key, wc_secret), timeout=30)
        r.raise_for_status()
        return True
    except Exception as e:
        log.error(f"Error en producto {producto_id}: {e}")
        return False


def configurar_precio_categoria(producto: Dict, wordpress_url: str, wc_key: str, wc_secret: str, dry_run: bool = False) -> bool:
    """Configura precio basado en categoría"""
    producto_id = producto['id']
    precio = determinar_precio_categoria(producto)
    
    datos = {
        'regular_price': str(precio),
        'stock_status': 'instock',
        'manage_stock': True,
        'stock_quantity': 10
    }
    
    if dry_run:
        categorias = [c['name'] for c in producto.get('categories', [])]
        log.info(f"[DRY-RUN] Producto {producto_id} ({', '.join(categorias)}): precio ${precio}, stock 10")
        return True
    
    try:
        url = f"{wordpress_url}/wp-json/wc/v3/products/{producto_id}"
        r = requests.put(url, json=datos, auth=(wc_key, wc_secret), timeout=30)
        r.raise_for_status()
        return True
    except Exception as e:
        log.error(f"Error en producto {producto_id}: {e}")
        return False


def configurar_desde_csv(producto: Dict, precios_sku: Dict, precios_nombre: Dict, wordpress_url: str, wc_key: str, wc_secret: str, dry_run: bool = False) -> bool:
    """Configura precio desde archivos CSV (SKU o Nombre)"""
    producto_id = producto['id']
    sku = (producto.get('sku') or '').strip()
    name = (producto.get('name') or '').lower().strip()
    
    # Buscar precio: 1. SKU, 2. Nombre exacto
    precio = precios_sku.get(sku) or precios_nombre.get(name)
    
    if not precio:
        # Intento 3: Buscar si el nombre del producto contiene alguna de las llaves del diccionario de nombres
        # (Búsqueda parcial para casos donde el título en WC es más largo)
        for key_name, p in precios_nombre.items():
            if key_name in name:
                precio = p
                break

    if not precio:
        log.warning(f"Producto {producto_id} ({name}) no encontrado en CSVs, usando precio categoria")
        precio = determinar_precio_categoria(producto)
    
    datos = {
        'regular_price': str(int(precio)),
        'stock_status': 'instock',
        'manage_stock': True,
        'stock_quantity': 10
    }
    
    if dry_run:
        log.info(f"[DRY-RUN] Producto {producto_id}: precio ${precio}, stock 10")
        return True
    
    try:
        url = f"{wordpress_url}/wp-json/wc/v3/products/{producto_id}"
        r = requests.put(url, json=datos, auth=(wc_key, wc_secret), timeout=30)
        r.raise_for_status()
        return True
    except Exception as e:
        log.error(f"Error en producto {producto_id}: {e}")
        return False


def cargar_precios_dict(csv_path: str, key_col: str) -> Dict:
    """Carga precios desde archivo CSV a un diccionario"""
    precios = {}
    if not os.path.exists(csv_path):
        log.warning(f"Archivo no encontrado: {csv_path}")
        return precios
        
    with open(csv_path, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            if key_col in row and 'precio' in row:
                key = row[key_col].lower().strip()
                try:
                    precios[key] = float(row['precio'])
                except:
                    continue
    
    log.info(f"Cargados {len(precios)} precios desde {csv_path}")
    return precios


def main():
    parser = argparse.ArgumentParser(description='Configurar precios y stock en WooCommerce')
    parser.add_argument('--modo', choices=['prueba', 'categoria', 'custom', '2026'], required=True,
                        help='Modo de configuración de precios')
    parser.add_argument('--csv', help='Archivo CSV con precios (para modo custom)')
    parser.add_argument('--csv-sku', help='CSV SKU (para modo 2026)', default='data/master_prices_sku_2026.csv')
    parser.add_argument('--csv-name', help='CSV Nombre (para modo 2026)', default='data/master_prices_name_2026.csv')
    parser.add_argument('--dry-run', action='store_true', help='Simular sin aplicar cambios')
    parser.add_argument('--limite', type=int, help='Limitar cantidad de productos a procesar')
    args = parser.parse_args()
    
    # Validaciones
    if args.modo == 'custom' and not args.csv:
        log.error("Modo custom requiere --csv con archivo de precios")
        sys.exit(1)
    
    # Obtener credenciales
    wordpress_url, wc_key, wc_secret = get_wc_api()
    
    # Obtener productos
    productos = get_all_products(wordpress_url, wc_key, wc_secret)
    
    if not productos:
        log.error("No se encontraron productos")
        sys.exit(1)
    
    # Aplicar límite si se especificó
    if args.limite:
        productos = productos[:args.limite]
        log.info(f"Limitando a {args.limite} productos")
    
    # Cargar precios CSV si aplica
    precios_sku = {}
    precios_nombre = {}
    if args.modo == 'custom':
        precios_sku = cargar_precios_dict(args.csv, 'sku')
    elif args.modo == '2026':
        precios_sku = cargar_precios_dict(args.csv_sku, 'sku')
        precios_nombre = cargar_precios_dict(args.csv_name, 'name')
    
    # Procesar productos
    log.info(f"Configurando precios y stock en modo: {args.modo}")
    if args.dry_run:
        log.info("MODO DRY-RUN: No se aplicarán cambios")
    
    stats = {'total': len(productos), 'exitosos': 0, 'errores': 0}
    
    for producto in tqdm(productos, desc="Procesando"):
        if args.modo == 'prueba':
            exito = configurar_precio_prueba(producto, wordpress_url, wc_key, wc_secret, args.dry_run)
        elif args.modo == 'categoria':
            exito = configurar_precio_categoria(producto, wordpress_url, wc_key, wc_secret, args.dry_run)
        elif args.modo == 'custom':
            exito = configurar_desde_csv(producto, precios_sku, {}, wordpress_url, wc_key, wc_secret, args.dry_run)
        elif args.modo == '2026':
            exito = configurar_desde_csv(producto, precios_sku, precios_nombre, wordpress_url, wc_key, wc_secret, args.dry_run)
        
        if exito:
            stats['exitosos'] += 1
        else:
            stats['errores'] += 1
    
    # Resumen
    log.info("=" * 60)
    log.info("RESUMEN:")
    log.info(f"  Total procesados: {stats['total']}")
    log.info(f"  Exitosos: {stats['exitosos']}")
    log.info(f"  Errores: {stats['errores']}")
    log.info("=" * 60)
    
    if not args.dry_run:
        log.info("✅ Configuración completada")
        log.info("➡️  Próximo paso: Verificar funcionalidad de carrito en sitio web")


if __name__ == '__main__':
    main()
