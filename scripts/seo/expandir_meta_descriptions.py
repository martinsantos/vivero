#!/usr/bin/env python3
"""
EXPANDIR META DESCRIPTIONS SEO
==============================
Expande meta descriptions cortas a longitud óptima (150-160 caracteres)
Incluye: nombre producto, característica principal, ubicación, CTA
"""

import requests
from requests.auth import HTTPBasicAuth
import sys
from typing import List, Dict, Optional
import time
import re
import argparse


class MetaDescriptionExpander:
    """Expandidor de meta descriptions"""
    
    def __init__(self, url: str, consumer_key: str, consumer_secret: str):
        self.url = url.rstrip('/')
        self.consumer_key = consumer_key
        self.consumer_secret = consumer_secret
        self.session = requests.Session()
        self.session.auth = HTTPBasicAuth(consumer_key, consumer_secret)
    
    def get_all_products(self) -> List[Dict]:
        """Obtiene todos los productos"""
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
    
    def get_current_meta_description(self, product: Dict) -> Optional[str]:
        """Obtiene la meta description actual del producto"""
        meta_data = product.get('meta_data', [])
        
        for meta in meta_data:
            key = meta.get('key', '')
            if key in ['_yoast_wpseo_metadesc', '_rank_math_description']:
                return meta.get('value', '')
        
        return None
    
    def extract_main_feature(self, product: Dict) -> Optional[str]:
        """Extrae la característica principal del producto"""
        title = product['name']
        
        # Extraer tamaño
        size_match = re.search(r'(\d+\s*(?:cm|litros?|l))', title, re.IGNORECASE)
        if size_match:
            return size_match.group(1).lower()
        
        # Extraer color
        if 'Color:' in title:
            color_match = re.search(r'Color:\s*(\w+(?:\s+\w+)?)', title)
            if color_match:
                return f"color {color_match.group(1).lower()}"
        
        # Tipo de planta
        if 'árbol' in title.lower() or 'arbol' in title.lower():
            return 'árbol'
        elif 'planta' in title.lower():
            return 'planta'
        elif 'maceta' in title.lower():
            return 'maceta'
        
        return None
    
    def get_product_type(self, product: Dict) -> str:
        """Determina el tipo de producto"""
        title = product['name'].lower()
        sku = product.get('sku', '').lower()
        
        if any(x in title or x in sku for x in ['maceta', 'mta', 'jardinera']):
            return 'maceta'
        elif 'plato' in title:
            return 'accesorio'
        elif 'árbol' in title or 'arbol' in title:
            return 'arbol'
        elif 'planta' in title:
            return 'planta'
        
        return 'producto'
    
    def generate_cta(self, product_type: str) -> str:
        """Genera un Call-to-Action según el tipo de producto"""
        ctas = {
            'maceta': '¡Comprá online ahora!',
            'arbol': '¡Consultá stock disponible!',
            'planta': '¡Pedí la tuya hoy!',
            'accesorio': '¡Envío a domicilio!',
            'producto': '¡Visitanos en Mendoza!'
        }
        return ctas.get(product_type, '¡Comprá online!')
    
    def generate_meta_description(self, product: Dict) -> str:
        """Genera una meta description óptima"""
        title = product['name']
        product_type = self.get_product_type(product)
        feature = self.extract_main_feature(product)
        cta = self.generate_cta(product_type)
        
        # Base: nombre del producto
        parts = []
        
        # Simplificar título si es muy largo
        if len(title) > 60:
            # Extraer lo esencial
            base_name = title.split('.')[0].split('-')[0].strip()
            parts.append(base_name)
        else:
            parts.append(title)
        
        # Agregar contexto según tipo
        if product_type == 'maceta':
            parts.append('de alta calidad para tus plantas')
        elif product_type == 'arbol':
            parts.append('para tu jardín o espacio verde')
        elif product_type == 'planta':
            if feature:
                parts.append(f'en {feature}')
            else:
                parts.append('de vivero profesional')
        
        # Agregar ubicación
        parts.append('en Vivero Los Cocos Mendoza')
        
        # Agregar CTA
        parts.append(cta)
        
        # Construir descripción
        meta = '. '.join(parts)
        
        # Ajustar longitud a 150-160 caracteres
        if len(meta) > 160:
            # Acortar manteniendo lo esencial
            meta = f"{title[:80]}... Vivero Los Cocos Mendoza. {cta}"
        
        # Asegurar mínimo 150 caracteres
        if len(meta) < 150:
            meta += ' Calidad garantizada y envíos a todo el país.'
        
        # Truncar a 160 si es necesario
        if len(meta) > 160:
            meta = meta[:157] + '...'
        
        return meta
    
    def update_meta_description(self, product_id: int, meta_description: str, dry_run: bool = False) -> bool:
        """Actualiza la meta description en WooCommerce"""
        if dry_run:
            return True
        
        endpoint = f"{self.url}/wp-json/wc/v3/products/{product_id}"
        
        # Actualizar meta_data para Yoast y RankMath
        data = {
            'meta_data': [
                {
                    'key': '_yoast_wpseo_metadesc',
                    'value': meta_description
                },
                {
                    'key': '_rank_math_description',
                    'value': meta_description
                }
            ]
        }
        
        try:
            response = self.session.put(endpoint, json=data)
            response.raise_for_status()
            return True
        except Exception as e:
            print(f"  ✗ Error actualizando producto {product_id}: {e}")
            return False
    
    def run(self, min_length: int = 150, batch_size: int = 50, dry_run: bool = False, force: bool = False):
        """Ejecuta el expandidor de meta descriptions"""
        print("\n" + "="*70)
        print("📝 EXPANDIDOR DE META DESCRIPTIONS SEO")
        print("="*70 + "\n")
        
        products = self.get_all_products()
        total = len(products)
        
        # Filtrar productos que necesitan expansión
        to_process = []
        for product in products:
            current_meta = self.get_current_meta_description(product)
            
            if force:
                to_process.append(product)
            elif not current_meta or len(current_meta) < min_length:
                to_process.append(product)
        
        print(f"📊 Productos a procesar: {len(to_process)}/{total}")
        print(f"   Criterio: Meta description < {min_length} caracteres\n")
        
        if not to_process:
            print("✅ Todos los productos ya tienen meta descriptions óptimas!")
            return
        
        updated = 0
        errors = 0
        
        print(f"🔄 Procesando {len(to_process)} productos...\n")
        
        for i, product in enumerate(to_process, 1):
            product_id = product['id']
            title = product['name']
            current_meta = self.get_current_meta_description(product)
            
            print(f"{'='*70}")
            print(f"Producto {i}/{len(to_process)} (ID: {product_id})")
            print(f"{'='*70}")
            print(f"Título: {title}")
            
            if current_meta:
                print(f"\n📝 Meta actual ({len(current_meta)} chars):")
                print(f"   {current_meta[:100]}...")
            else:
                print(f"\n📝 Meta actual: (ninguna)")
            
            # Generar nueva meta description
            new_meta = self.generate_meta_description(product)
            
            print(f"\n✨ Meta nueva ({len(new_meta)} chars):")
            print(f"   {new_meta}")
            
            if not dry_run:
                # Actualizar en WooCommerce
                print(f"\n🔄 Actualizando...")
                if self.update_meta_description(product_id, new_meta, dry_run):
                    print(f"✅ Actualizado correctamente")
                    updated += 1
                else:
                    print(f"❌ Error al actualizar")
                    errors += 1
                
                # Rate limiting
                time.sleep(1)
            else:
                print(f"\n⏭️  DRY-RUN - No se actualizó")
                updated += 1
            
            print()
            
            # Pausa cada batch
            if i % batch_size == 0 and i < len(to_process):
                print(f"\n⏸️  Pausa de 5 segundos... ({i}/{len(to_process)} completados)")
                time.sleep(5)
        
        # Resumen
        print("\n" + "="*70)
        print("📊 RESUMEN FINAL")
        print("="*70)
        print(f"\nTotal procesados: {len(to_process)}")
        print(f"✅ Actualizados: {updated}")
        print(f"❌ Errores: {errors}")
        print(f"📈 Tasa de éxito: {(updated/len(to_process)*100):.1f}%")
        print("\n" + "="*70)


def main():
    parser = argparse.ArgumentParser(description='Expandir meta descriptions SEO')
    parser.add_argument('--url', required=True, help='URL del sitio')
    parser.add_argument('--key', required=True, help='Consumer Key')
    parser.add_argument('--secret', required=True, help='Consumer Secret')
    parser.add_argument('--min-length', type=int, default=150, help='Longitud mínima')
    parser.add_argument('--batch-size', type=int, default=50, help='Tamaño del batch')
    parser.add_argument('--dry-run', action='store_true', help='Solo simular')
    parser.add_argument('--force', action='store_true', help='Actualizar todos')
    
    args = parser.parse_args()
    
    expander = MetaDescriptionExpander(args.url, args.key, args.secret)
    expander.run(args.min_length, args.batch_size, args.dry_run, args.force)


if __name__ == "__main__":
    main()
