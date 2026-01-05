#!/usr/bin/env python3
"""
Actualización Masiva de Títulos SEO en Producción
=================================================
Optimiza todos los títulos de productos en viveroloscocos.com.ar
"""

import os
import sys
import json
import requests
from requests.auth import HTTPBasicAuth
import time
from typing import List, Dict
import argparse

# Importar el optimizador V3
try:
    from seo_title_optimizer import generate_seo_title_v3, generate_seo_description_v3, find_plant_info, decode_sku
    generate_seo_title = generate_seo_title_v3
    generate_seo_description = generate_seo_description_v3
except ImportError:
    from seo_title_optimizer import generate_seo_title, generate_seo_description, find_plant_info, decode_sku


class WooCommerceAPI:
    """Cliente para API de WooCommerce"""
    
    def __init__(self, url: str, consumer_key: str, consumer_secret: str):
        self.url = url.rstrip('/')
        self.consumer_key = consumer_key
        self.consumer_secret = consumer_secret
        self.session = requests.Session()
        self.session.auth = HTTPBasicAuth(consumer_key, consumer_secret)
    
    def get_products(self, per_page: int = 100, page: int = 1) -> List[Dict]:
        """Obtiene productos de WooCommerce"""
        endpoint = f"{self.url}/wp-json/wc/v3/products"
        params = {
            "per_page": per_page,
            "page": page
        }
        
        try:
            response = self.session.get(endpoint, params=params)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            print(f"❌ Error obteniendo productos: {e}")
            return []
    
    def get_all_products(self) -> List[Dict]:
        """Obtiene todos los productos paginando"""
        all_products = []
        page = 1
        
        while True:
            products = self.get_products(per_page=100, page=page)
            if not products:
                break
            
            all_products.extend(products)
            print(f"📦 Página {page}: {len(products)} productos obtenidos")
            page += 1
            time.sleep(0.5)  # Rate limiting
        
        return all_products
    
    def update_product(self, product_id: int, data: Dict) -> bool:
        """Actualiza un producto"""
        endpoint = f"{self.url}/wp-json/wc/v3/products/{product_id}"
        
        try:
            response = self.session.put(endpoint, json=data)
            response.raise_for_status()
            return True
        except requests.exceptions.RequestException as e:
            print(f"❌ Error actualizando producto {product_id}: {e}")
            return False


def optimize_product_title(product: Dict, dry_run: bool = False) -> Dict:
    """
    Optimiza el título de un producto
    
    Returns:
        Dict con información de la optimización
    """
    product_id = product['id']
    current_title = product['name']
    sku = product.get('sku', '')
    slug = product.get('slug', '')
    
    # Usar SKU o slug como base
    code = sku if sku else slug
    
    # Generar título optimizado
    optimized_title = generate_seo_title(code, current_title)
    
    # Generar descripción y keywords
    try:
        # V3 retorna 3 valores
        plant_code, _, _ = decode_sku(code)
    except ValueError:
        # V2 retorna 2 valores
        plant_code, _ = decode_sku(code)
    
    plant_info = find_plant_info(plant_code) if plant_code else None
    
    seo_description = generate_seo_description(code, optimized_title, plant_info)
    
    # generate_seo_keywords solo existe en V2
    try:
        seo_keywords = generate_seo_keywords(plant_info, optimized_title)
    except NameError:
        seo_keywords = []
    
    optimization = {
        'id': product_id,
        'sku': sku,
        'slug': slug,
        'current_title': current_title,
        'optimized_title': optimized_title,
        'changed': current_title != optimized_title,
        'seo_description': seo_description,
        'seo_keywords': seo_keywords[:10],  # Top 10 keywords
        'plant_info_found': plant_info is not None
    }
    
    return optimization


def update_products_batch(
    api: WooCommerceAPI,
    products: List[Dict],
    dry_run: bool = False,
    force: bool = False
) -> Dict:
    """
    Actualiza un lote de productos
    
    Returns:
        Estadísticas de la actualización
    """
    stats = {
        'total': len(products),
        'updated': 0,
        'skipped': 0,
        'errors': 0,
        'optimizations': []
    }
    
    for i, product in enumerate(products, 1):
        print(f"\n{'='*60}")
        print(f"Producto {i}/{stats['total']}")
        print(f"{'='*60}")
        
        # Optimizar título
        optimization = optimize_product_title(product, dry_run)
        stats['optimizations'].append(optimization)
        
        print(f"ID: {optimization['id']}")
        print(f"SKU: {optimization['sku']}")
        print(f"Actual: {optimization['current_title']}")
        print(f"Optimizado: {optimization['optimized_title']}")
        print(f"Cambio: {'✅ SÍ' if optimization['changed'] else '⏭️  NO'}")
        
        if optimization['plant_info_found']:
            print(f"🌿 Info de planta encontrada")
            print(f"📝 Descripción: {optimization['seo_description'][:80]}...")
            print(f"🔑 Keywords: {', '.join(optimization['seo_keywords'][:5])}")
        
        # Actualizar solo si cambió y no es dry-run
        if optimization['changed'] and not dry_run:
            if not force:
                response = input("¿Actualizar este producto? (s/n/q para salir): ").lower()
                if response == 'q':
                    print("🛑 Proceso cancelado por el usuario")
                    break
                elif response != 's':
                    stats['skipped'] += 1
                    continue
            
            # Preparar datos de actualización
            update_data = {
                'name': optimization['optimized_title'],
                'short_description': optimization['seo_description'],
                'meta_data': [
                    {
                        'key': '_yoast_wpseo_title',
                        'value': optimization['optimized_title']
                    },
                    {
                        'key': '_yoast_wpseo_metadesc',
                        'value': optimization['seo_description']
                    },
                    {
                        'key': '_seo_keywords',
                        'value': ','.join(optimization['seo_keywords'])
                    }
                ]
            }
            
            # Actualizar
            print("🔄 Actualizando...")
            if api.update_product(optimization['id'], update_data):
                stats['updated'] += 1
                print("✅ Actualizado correctamente")
            else:
                stats['errors'] += 1
                print("❌ Error al actualizar")
            
            time.sleep(1)  # Rate limiting
        elif not optimization['changed']:
            stats['skipped'] += 1
    
    return stats


def generate_report(stats: Dict, output_file: str):
    """Genera reporte de la optimización"""
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write("# REPORTE DE OPTIMIZACIÓN SEO - VIVERO LOS COCOS\n\n")
        f.write(f"## Estadísticas\n\n")
        f.write(f"- **Total de productos:** {stats['total']}\n")
        f.write(f"- **Actualizados:** {stats['updated']}\n")
        f.write(f"- **Omitidos:** {stats['skipped']}\n")
        f.write(f"- **Errores:** {stats['errors']}\n\n")
        
        f.write(f"## Detalle de Optimizaciones\n\n")
        
        for opt in stats['optimizations']:
            if opt['changed']:
                f.write(f"### Producto ID: {opt['id']}\n")
                f.write(f"- **SKU:** {opt['sku']}\n")
                f.write(f"- **Título anterior:** {opt['current_title']}\n")
                f.write(f"- **Título optimizado:** {opt['optimized_title']}\n")
                f.write(f"- **Descripción SEO:** {opt['seo_description']}\n")
                f.write(f"- **Keywords:** {', '.join(opt['seo_keywords'])}\n")
                f.write(f"- **Info de planta:** {'✅' if opt['plant_info_found'] else '❌'}\n\n")
    
    print(f"\n📄 Reporte generado: {output_file}")


def main():
    parser = argparse.ArgumentParser(description='Optimiza títulos SEO de productos')
    parser.add_argument('--url', required=True, help='URL del sitio WordPress')
    parser.add_argument('--key', required=True, help='Consumer Key de WooCommerce')
    parser.add_argument('--secret', required=True, help='Consumer Secret de WooCommerce')
    parser.add_argument('--dry-run', action='store_true', help='Simular sin actualizar')
    parser.add_argument('--force', action='store_true', help='Actualizar sin confirmación')
    parser.add_argument('--limit', type=int, help='Limitar número de productos')
    parser.add_argument('--report', default='seo_optimization_report.md', help='Archivo de reporte')
    
    args = parser.parse_args()
    
    print("🌿 OPTIMIZACIÓN SEO MASIVA - VIVERO LOS COCOS")
    print("=" * 60)
    print(f"🌐 URL: {args.url}")
    print(f"🔍 Modo: {'DRY-RUN (simulación)' if args.dry_run else 'PRODUCCIÓN'}")
    print(f"⚡ Auto-confirm: {'SÍ' if args.force else 'NO'}")
    print("=" * 60)
    
    if not args.dry_run and not args.force:
        confirm = input("\n⚠️  ¿Proceder con la actualización? (escriba 'SI' para confirmar): ")
        if confirm != 'SI':
            print("🛑 Operación cancelada")
            return
    
    # Inicializar API
    api = WooCommerceAPI(args.url, args.key, args.secret)
    
    # Obtener productos
    print("\n📦 Obteniendo productos...")
    products = api.get_all_products()
    
    if args.limit:
        products = products[:args.limit]
    
    print(f"✅ {len(products)} productos obtenidos\n")
    
    # Actualizar productos
    stats = update_products_batch(api, products, args.dry_run, args.force)
    
    # Generar reporte
    generate_report(stats, args.report)
    
    # Resumen final
    print("\n" + "=" * 60)
    print("📊 RESUMEN FINAL")
    print("=" * 60)
    print(f"Total: {stats['total']}")
    print(f"✅ Actualizados: {stats['updated']}")
    print(f"⏭️  Omitidos: {stats['skipped']}")
    print(f"❌ Errores: {stats['errors']}")
    print(f"📄 Reporte: {args.report}")
    print("=" * 60)


if __name__ == "__main__":
    main()
