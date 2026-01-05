#!/usr/bin/env python3
"""
AGREGAR KEYWORDS RÁPIDO
=======================

Genera y asigna meta keywords a productos que no los tienen.

Uso:
    python3 agregar_keywords_rapido.py --dry-run
    python3 agregar_keywords_rapido.py
"""

import argparse
import json
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


def generar_keywords(producto: Dict) -> str:
    """Genera keywords desde nombre, categorías y tags"""
    keywords = []
    
    # 1. Palabras del nombre (sin stop words)
    nombre = producto.get('name', '').lower()
    stop_words = ['de', 'del', 'la', 'el', 'en', 'con', 'para', 'por', 'y', 'o', '-']
    palabras = [w for w in nombre.split() if len(w) > 2 and w not in stop_words]
    keywords.extend(palabras[:5])  # Top 5
    
    # 2. Categorías
    for cat in producto.get('categories', [])[:2]:
        cat_name = cat.get('name', '').strip().lower()
        if cat_name and cat_name not in keywords:
            keywords.append(cat_name)
    
    # 3. Tags (si existen)
    for tag in producto.get('tags', [])[:3]:
        tag_name = tag.get('name', '').strip().lower()
        if tag_name and tag_name not in keywords:
            keywords.append(tag_name)
    
    # 4. Agregar "vivero", "plantas" como keywords genéricas
    if 'vivero' not in keywords:
        keywords.append('vivero')
    if 'plantas' not in keywords and 'planta' not in keywords:
        keywords.append('plantas')
    
    # Limitar a 10 keywords
    keywords = keywords[:10]
    
    # Retornar como string separado por comas
    return ', '.join(keywords)


def procesar_productos(wordpress_url: str, wc_key: str, wc_secret: str,
                      wp_user: str, wp_pass: str, dry_run: bool = True) -> Dict:
    """Procesa productos agregando keywords"""
    
    session = requests.Session()
    stats = {
        'total': 0,
        'procesados': 0,
        'con_keywords': 0,
        'sin_keywords': 0,
        'agregados': 0,
        'errores': 0
    }
    
    # Obtener productos
    log.info("Obteniendo productos...")
    productos = []
    page = 1
    
    while True:
        url = f"{wordpress_url}/wp-json/wc/v3/products"
        params = {"per_page": 100, "page": page, "status": "publish"}
        
        try:
            r = session.get(url, params=params, auth=(wc_key, wc_secret), timeout=30)
            r.raise_for_status()
            data = r.json()
            
            if not data:
                break
            
            productos.extend(data)
            log.info(f"  Página {page}: {len(data)} productos")
            page += 1
            
        except Exception as e:
            log.error(f"Error: {e}")
            break
    
    log.info(f"✅ Total: {len(productos)}")
    stats['total'] = len(productos)
    
    # Procesar
    for producto in tqdm(productos, desc="Agregando keywords"):
        producto_id = producto['id']
        stats['procesados'] += 1
        
        # Verificar si ya tiene keywords (en meta_data)
        meta_data = producto.get('meta_data', [])
        tiene_keywords = any(
            m.get('key') == '_yoast_wpseo_focuskw' and m.get('value')
            for m in meta_data
        )
        
        if tiene_keywords:
            stats['con_keywords'] += 1
            continue
        
        stats['sin_keywords'] += 1
        
        # Generar keywords
        keywords = generar_keywords(producto)
        
        log.info(f"\nProducto {producto_id}: {producto['name'][:40]}")
        log.info(f"  Keywords: {keywords}")
        
        if dry_run:
            log.info(f"  [DRY-RUN] No se agregan")
            stats['agregados'] += 1
            continue
        
        # Agregar keywords via WP REST API
        # Nota: Yoast SEO guarda en meta _yoast_wpseo_focuskw
        try:
            url = f"{wordpress_url}/wp-json/wc/v3/products/{producto_id}"
            
            # Agregar a meta_data
            meta_nueva = {
                'key': '_yoast_wpseo_focuskw',
                'value': keywords.split(',')[0].strip()  # Focus keyword (primera)
            }
            
            # WooCommerce API permite actualizar meta_data
            data = {
                'meta_data': meta_data + [meta_nueva]
            }
            
            r = session.put(url, json=data, auth=(wc_key, wc_secret), timeout=30)
            r.raise_for_status()
            
            log.info(f"  ✅ Keywords agregadas")
            stats['agregados'] += 1
            
        except Exception as e:
            log.error(f"  ❌ Error: {e}")
            stats['errores'] += 1
    
    return stats


def main():
    parser = argparse.ArgumentParser(description="Agregar keywords a productos")
    parser.add_argument("--dry-run", action="store_true", help="Simular")
    parser.add_argument("--output", default="keywords_resultados.json", help="Resultados")
    
    args = parser.parse_args()
    
    # Credenciales
    wordpress_url = os.getenv('WORDPRESS_URL')
    wc_key = os.getenv('WC_CONSUMER_KEY')
    wc_secret = os.getenv('WC_CONSUMER_SECRET')
    wp_user = os.getenv('WP_USERNAME')
    wp_pass = os.getenv('WP_APP_PASSWORD')
    
    if not all([wordpress_url, wc_key, wc_secret]):
        log.error("❌ Faltan variables de entorno")
        sys.exit(1)
    
    # Procesar
    log.info(f"\n{'='*60}")
    log.info(f"AGREGAR KEYWORDS")
    log.info(f"{'='*60}")
    log.info(f"Modo: {'DRY-RUN' if args.dry_run else 'PRODUCCIÓN'}")
    log.info(f"{'='*60}\n")
    
    stats = procesar_productos(
        wordpress_url.rstrip('/'),
        wc_key, wc_secret,
        wp_user or '', wp_pass or '',
        dry_run=args.dry_run
    )
    
    # Guardar
    with open(args.output, 'w', encoding='utf-8') as f:
        json.dump(stats, f, ensure_ascii=False, indent=2)
    
    # Resumen
    print(f"\n{'='*60}")
    print(f"RESUMEN")
    print(f"{'='*60}")
    print(f"Total: {stats['total']}")
    print(f"Con keywords: {stats['con_keywords']}")
    print(f"Sin keywords: {stats['sin_keywords']}")
    print(f"Keywords agregadas: {stats['agregados']}")
    print(f"Errores: {stats['errores']}")
    print(f"\nResultados: {args.output}")
    print(f"{'='*60}")
    
    if args.dry_run:
        print("\n⚠️ Ejecutar sin --dry-run para aplicar")


if __name__ == "__main__":
    main()
