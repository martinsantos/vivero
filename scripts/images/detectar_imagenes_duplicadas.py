#!/usr/bin/env python3
"""
DETECTOR DE IMÁGENES DUPLICADAS - CORRECCIÓN URGENTE
=====================================================
Detecta productos que comparten la MISMA imagen visual
(no solo mismo ID de media, sino mismo archivo)
"""

import requests
from requests.auth import HTTPBasicAuth
import sys
from typing import List, Dict, Set
from collections import defaultdict
import hashlib
import time


class ImageDuplicateDetector:
    """Detecta imágenes duplicadas por URL/contenido"""
    
    def __init__(self, url: str, consumer_key: str, consumer_secret: str):
        self.url = url.rstrip('/')
        self.consumer_key = consumer_key
        self.consumer_secret = consumer_secret
        self.session = requests.Session()
        self.session.auth = HTTPBasicAuth(consumer_key, consumer_secret)
    
    def get_all_products(self) -> List[Dict]:
        """Obtiene todos los productos con imágenes"""
        all_products = []
        page = 1
        
        print("📦 Obteniendo productos...")
        
        while True:
            endpoint = f"{self.url}/wp-json/wc/v3/products"
            params = {"per_page": 100, "page": page}
            
            try:
                response = self.session.get(endpoint, params=params)
                response.raise_for_status()
                products = response.json()
                
                if not products:
                    break
                
                all_products.extend(products)
                print(f"  Página {page}: {len(products)} productos")
                page += 1
                time.sleep(0.5)
                
            except Exception as e:
                print(f"✗ Error: {e}")
                break
        
        print(f"\n✓ Total: {len(all_products)} productos\n")
        return all_products
    
    def detect_duplicates(self, products: List[Dict]) -> Dict:
        """Detecta imágenes duplicadas por URL"""
        
        # Mapear URL de imagen -> lista de productos que la usan
        url_to_products = defaultdict(list)
        
        print("🔍 Analizando imágenes...\n")
        
        # Primero, deduplicar productos por ID (API puede devolver duplicados)
        unique_products = {}
        for product in products:
            product_id = product['id']
            if product_id not in unique_products:
                unique_products[product_id] = product
        
        print(f"📊 Productos únicos: {len(unique_products)} (de {len(products)} totales)\n")
        
        for product in unique_products.values():
            product_id = product['id']
            product_name = product['name']
            images = product.get('images', [])
            
            if images:
                # Imagen principal
                main_image = images[0]
                image_url = main_image.get('src', '')
                
                if image_url:
                    # Normalizar URL (remover parámetros de query)
                    clean_url = image_url.split('?')[0]
                    
                    url_to_products[clean_url].append({
                        'id': product_id,
                        'name': product_name,
                        'sku': product.get('sku', ''),
                        'full_url': image_url
                    })
        
        # Encontrar duplicados REALES (misma imagen, productos diferentes)
        real_duplicates = {}
        for url, prods in url_to_products.items():
            if len(prods) > 1:
                # Verificar que son productos DIFERENTES (no duplicados de API)
                unique_ids = set(p['id'] for p in prods)
                if len(unique_ids) > 1:
                    real_duplicates[url] = prods
        
        return {
            'duplicates': real_duplicates,
            'total_duplicate_urls': len(real_duplicates),
            'total_affected_products': sum(len(prods) for prods in real_duplicates.values())
        }
    
    def print_report(self, results: Dict):
        """Imprime reporte de duplicados"""
        duplicates = results['duplicates']
        
        print("\n" + "="*70)
        print("🚨 REPORTE DE IMÁGENES DUPLICADAS")
        print("="*70 + "\n")
        
        print(f"📊 RESUMEN:")
        print(f"  Total de URLs duplicadas: {results['total_duplicate_urls']}")
        print(f"  Total de productos afectados: {results['total_affected_products']}")
        print()
        
        if not duplicates:
            print("✅ ¡No se encontraron imágenes duplicadas!")
            return
        
        print("="*70)
        print("DETALLE DE DUPLICADOS")
        print("="*70 + "\n")
        
        for i, (url, products) in enumerate(sorted(duplicates.items(), key=lambda x: len(x[1]), reverse=True), 1):
            print(f"\n{i}. IMAGEN DUPLICADA ({len(products)} productos):")
            print(f"   URL: {url}")
            print(f"   \n   Productos que la usan:")
            
            for prod in products:
                print(f"     - ID: {prod['id']} | SKU: {prod['sku']} | {prod['name']}")
            
            print()
    
    def export_report(self, results: Dict, filename: str):
        """Exporta reporte a archivo"""
        duplicates = results['duplicates']
        
        with open(filename, 'w', encoding='utf-8') as f:
            f.write("# 🚨 REPORTE DE IMÁGENES DUPLICADAS\n\n")
            f.write(f"**Fecha:** 2025-10-03\n\n")
            f.write(f"## 📊 RESUMEN\n\n")
            f.write(f"- **URLs duplicadas:** {results['total_duplicate_urls']}\n")
            f.write(f"- **Productos afectados:** {results['total_affected_products']}\n\n")
            
            f.write("---\n\n")
            f.write("## 🔍 DETALLE DE DUPLICADOS\n\n")
            
            for i, (url, products) in enumerate(sorted(duplicates.items(), key=lambda x: len(x[1]), reverse=True), 1):
                f.write(f"### {i}. Imagen compartida por {len(products)} productos\n\n")
                f.write(f"**URL:** `{url}`\n\n")
                f.write(f"**Productos:**\n\n")
                
                for prod in products:
                    f.write(f"- **ID {prod['id']}** | SKU: `{prod['sku']}` | {prod['name']}\n")
                
                f.write("\n")
        
        print(f"\n💾 Reporte exportado a: {filename}")


def main():
    import argparse
    
    parser = argparse.ArgumentParser(description='Detectar imágenes duplicadas')
    parser.add_argument('--url', required=True, help='URL del sitio')
    parser.add_argument('--key', required=True, help='Consumer Key')
    parser.add_argument('--secret', required=True, help='Consumer Secret')
    parser.add_argument('--export', default='IMAGENES_DUPLICADAS.md', help='Archivo de exportación')
    
    args = parser.parse_args()
    
    detector = ImageDuplicateDetector(args.url, args.key, args.secret)
    products = detector.get_all_products()
    results = detector.detect_duplicates(products)
    
    detector.print_report(results)
    detector.export_report(results, args.export)


if __name__ == "__main__":
    main()
