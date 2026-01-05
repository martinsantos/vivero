#!/usr/bin/env python3
"""
EXPANSIÓN RÁPIDA DE TÍTULOS CORTOS
==================================

Expande títulos <30 caracteres a 40-60 caracteres
agregando categoría, atributos o descriptores.

Uso:
    python3 expandir_titulos_rapido.py --dry-run
    python3 expandir_titulos_rapido.py
"""

import argparse
import json
import logging
import os
import sys
from typing import Dict, List, Optional

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


def expandir_titulo(producto: Dict) -> Optional[str]:
    """Expande título corto a 40-60 caracteres"""
    nombre_actual = producto.get('name', '').strip()
    
    if len(nombre_actual) >= 30:
        return None  # Ya es suficientemente largo
    
    # Construir título expandido
    titulo_nuevo = nombre_actual
    
    # 1. Agregar categoría principal si es específica
    categorias = producto.get('categories', [])
    if categorias:
        cat_name = categorias[0].get('name', '').strip()
        # Solo agregar si no está ya en el título y es específica
        if cat_name and cat_name not in nombre_actual and len(cat_name) < 25:
            # Evitar categorías muy genéricas
            if cat_name.lower() not in ['productos', 'uncategorized', 'sin categoría']:
                titulo_nuevo = f"{titulo_nuevo} - {cat_name}"
    
    # 2. Si aún es corto, agregar atributo principal (tamaño)
    if len(titulo_nuevo) < 40:
        atributos = producto.get('attributes', [])
        for attr in atributos:
            attr_name = attr.get('name', '').lower()
            if 'tamaño' in attr_name or 'litros' in attr_name:
                opciones = attr.get('options', [])
                if opciones and isinstance(opciones[0], str):
                    valor = opciones[0]
                    if valor not in titulo_nuevo:
                        titulo_nuevo = f"{titulo_nuevo} {valor}"
                        break
    
    # 3. Si aún es corto y no tiene descriptor, agregar genérico
    if len(titulo_nuevo) < 40:
        # Detectar tipo de producto
        nombre_lower = nombre_actual.lower()
        if 'planta' not in nombre_lower and 'arbol' not in nombre_lower:
            if any(cat.get('name', '').lower().find('planta') >= 0 for cat in categorias):
                titulo_nuevo = f"{titulo_nuevo} - Planta"
    
    # Verificar longitud final
    if len(titulo_nuevo) < 30:
        # Aún muy corto, forzar a 30+ agregando "- Producto de Vivero"
        titulo_nuevo = f"{titulo_nuevo} - Producto de Vivero"
    
    # Truncar si es muy largo
    if len(titulo_nuevo) > 70:
        titulo_nuevo = titulo_nuevo[:67] + "..."
    
    # Solo retornar si cambió
    if titulo_nuevo != nombre_actual:
        return titulo_nuevo
    
    return None


def procesar_productos(wordpress_url: str, wc_key: str, wc_secret: str,
                      dry_run: bool = True) -> Dict:
    """Procesa productos expandiendo títulos cortos"""
    
    session = requests.Session()
    stats = {
        'total': 0,
        'procesados': 0,
        'expandidos': 0,
        'sin_cambios': 0,
        'errores': 0
    }
    
    # Obtener todos los productos
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
            log.error(f"Error obteniendo productos: {e}")
            break
    
    log.info(f"✅ Total productos: {len(productos)}")
    
    # Filtrar títulos cortos
    cortos = [p for p in productos if len(p.get('name', '')) < 30]
    log.info(f"Títulos cortos (<30 chars): {len(cortos)}")
    
    stats['total'] = len(cortos)
    
    # Procesar
    for producto in tqdm(cortos, desc="Expandiendo títulos"):
        producto_id = producto['id']
        nombre_actual = producto['name']
        
        stats['procesados'] += 1
        
        # Generar título expandido
        titulo_nuevo = expandir_titulo(producto)
        
        if not titulo_nuevo:
            stats['sin_cambios'] += 1
            continue
        
        log.info(f"\nProducto {producto_id}:")
        log.info(f"  Actual: '{nombre_actual}' ({len(nombre_actual)} chars)")
        log.info(f"  Nuevo:  '{titulo_nuevo}' ({len(titulo_nuevo)} chars)")
        
        if dry_run:
            log.info(f"  [DRY-RUN] No se actualiza")
            stats['expandidos'] += 1
            continue
        
        # Actualizar producto
        try:
            url = f"{wordpress_url}/wp-json/wc/v3/products/{producto_id}"
            data = {"name": titulo_nuevo}
            
            r = session.put(url, json=data, auth=(wc_key, wc_secret), timeout=30)
            r.raise_for_status()
            
            log.info(f"  ✅ Actualizado")
            stats['expandidos'] += 1
            
        except Exception as e:
            log.error(f"  ❌ Error: {e}")
            stats['errores'] += 1
    
    return stats


def main():
    parser = argparse.ArgumentParser(description="Expandir títulos cortos")
    parser.add_argument("--dry-run", action="store_true", help="Simular sin actualizar")
    parser.add_argument("--output", default="titulos_expandidos.json", help="Archivo resultados")
    
    args = parser.parse_args()
    
    # Credenciales
    wordpress_url = os.getenv('WORDPRESS_URL')
    wc_key = os.getenv('WC_CONSUMER_KEY')
    wc_secret = os.getenv('WC_CONSUMER_SECRET')
    
    if not all([wordpress_url, wc_key, wc_secret]):
        log.error("❌ Faltan variables de entorno")
        sys.exit(1)
    
    # Procesar
    log.info(f"\n{'='*60}")
    log.info(f"EXPANSIÓN DE TÍTULOS CORTOS")
    log.info(f"{'='*60}")
    log.info(f"Modo: {'DRY-RUN' if args.dry_run else 'PRODUCCIÓN'}")
    log.info(f"{'='*60}\n")
    
    stats = procesar_productos(
        wordpress_url.rstrip('/'),
        wc_key,
        wc_secret,
        dry_run=args.dry_run
    )
    
    # Guardar resultados
    with open(args.output, 'w', encoding='utf-8') as f:
        json.dump(stats, f, ensure_ascii=False, indent=2)
    
    # Resumen
    print(f"\n{'='*60}")
    print(f"RESUMEN")
    print(f"{'='*60}")
    print(f"Total procesados: {stats['procesados']}")
    print(f"Títulos expandidos: {stats['expandidos']}")
    print(f"Sin cambios: {stats['sin_cambios']}")
    print(f"Errores: {stats['errores']}")
    print(f"\nResultados: {args.output}")
    print(f"{'='*60}")
    
    if args.dry_run:
        print("\n⚠️ Ejecutar sin --dry-run para aplicar cambios")


if __name__ == "__main__":
    main()
