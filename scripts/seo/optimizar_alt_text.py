#!/usr/bin/env python3
"""
OPTIMIZAR ALT TEXT DE IMÁGENES
==============================
Optimiza el alt text de todas las imágenes del catálogo
Formato: [descripción producto] - Vivero Los Cocos Mendoza
"""

import requests
from requests.auth import HTTPBasicAuth
import sys
from typing import List, Dict, Optional
import time
import re
import argparse


class AltTextOptimizer:
    """Optimizador de alt text de imágenes"""
    
    def __init__(self, url: str, consumer_key: str, consumer_secret: str, wp_user: str, wp_pass: str):
        self.url = url.rstrip('/')
        self.consumer_key = consumer_key
        self.consumer_secret = consumer_secret
        self.wp_user = wp_user
        self.wp_pass = wp_pass
        self.wc_session = requests.Session()
        self.wc_session.auth = HTTPBasicAuth(consumer_key, consumer_secret)
        self.wp_session = requests.Session()
        self.wp_session.auth = HTTPBasicAuth(wp_user, wp_pass)
    
    def get_all_products(self) -> List[Dict]:
        """Obtiene todos los productos"""
        all_products = []
        page = 1
        
        print("📦 Obteniendo productos...")
        
        while True:
            endpoint = f"{self.url}/wp-json/wc/v3/products"
            params = {"per_page": 100, "page": page}
            
            try:
                response = self.wc_session.get(endpoint, params=params)
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
    
    def extract_product_features(self, product: Dict) -> Dict[str, Optional[str]]:
        """Extrae características del producto para el alt text"""
        title = product['name']
        sku = product.get('sku', '')
        
        features = {
            'type': None,
            'name': None,
            'size': None,
            'color': None
        }
        
        # Tipo de producto
        title_lower = title.lower()
        if 'maceta' in title_lower:
            features['type'] = 'maceta'
        elif 'árbol' in title_lower or 'arbol' in title_lower:
            features['type'] = 'árbol'
        elif 'planta' in title_lower:
            features['type'] = 'planta'
        elif 'plato' in title_lower:
            features['type'] = 'plato'
        
        # Tamaño
        size_match = re.search(r'(\d+)\s*(cm|litros?|l)', title, re.IGNORECASE)
        if size_match:
            num = size_match.group(1)
            unit = size_match.group(2).lower()
            if unit in ['litros', 'litro', 'l']:
                features['size'] = f"{num} litros"
            else:
                features['size'] = f"{num}cm"
        
        # Color
        if 'Color:' in title:
            color_match = re.search(r'Color:\s*(\w+(?:\s+\w+)?)', title)
            if color_match:
                features['color'] = color_match.group(1).lower()
        
        # Nombre de planta (después de "Planta" o "Árbol")
        if features['type'] in ['planta', 'árbol']:
            words = title.split()
            for i, word in enumerate(words):
                if word.lower() in ['planta', 'árbol', 'arbol']:
                    if i + 1 < len(words):
                        name = words[i + 1]
                        # Limpiar
                        name = re.sub(r'[^\w]', '', name)
                        if name and len(name) > 2:
                            features['name'] = name.lower()
                        break
        
        return features
    
    def generate_alt_text(self, product: Dict) -> str:
        """Genera alt text optimizado para el producto"""
        title = product['name']
        features = self.extract_product_features(product)
        
        parts = []
        
        # Macetas
        if features['type'] == 'maceta':
            parts.append('Maceta plástica')
            if features['color']:
                parts.append(f"color {features['color']}")
            if features['size']:
                parts.append(features['size'])
            parts.append('para plantas')
        
        # Árboles
        elif features['type'] == 'árbol':
            if features['name']:
                parts.append(f"Árbol {features['name']}")
            else:
                parts.append('Árbol')
            if features['size']:
                parts.append(f"presentación {features['size']}")
            parts.append('para jardín')
        
        # Plantas
        elif features['type'] == 'planta':
            if features['name']:
                parts.append(f"Planta {features['name']}")
            else:
                parts.append('Planta')
            if features['size']:
                parts.append(features['size'])
            parts.append('de vivero')
        
        # Accesorios
        elif features['type'] == 'plato':
            parts.append('Plato para maceta')
            if features['size']:
                parts.append(features['size'])
        
        # Genérico
        else:
            # Usar título simplificado
            simple_title = title.split('-')[0].split('.')[0].strip()
            if len(simple_title) > 50:
                simple_title = simple_title[:50]
            parts.append(simple_title)
        
        # Agregar ubicación
        parts.append('Vivero Los Cocos Mendoza')
        
        # Construir alt text
        alt_text = ' - '.join(parts)
        
        # Limitar longitud (125 caracteres es óptimo para SEO)
        if len(alt_text) > 125:
            alt_text = alt_text[:122] + '...'
        
        return alt_text
    
    def update_image_alt_text(self, image_id: int, alt_text: str, dry_run: bool = False) -> bool:
        """Actualiza el alt text de una imagen vía WordPress API"""
        if dry_run:
            return True
        
        endpoint = f"{self.url}/wp-json/wp/v2/media/{image_id}"
        data = {
            'alt_text': alt_text
        }
        
        try:
            response = self.wp_session.post(endpoint, json=data)
            response.raise_for_status()
            return True
        except Exception as e:
            print(f"  ⚠️  Error actualizando imagen {image_id}: {e}")
            return False
    
    def run(self, batch_size: int = 50, dry_run: bool = False, force: bool = False):
        """Ejecuta el optimizador de alt text"""
        print("\n" + "="*70)
        print("🖼️  OPTIMIZADOR DE ALT TEXT DE IMÁGENES")
        print("="*70 + "\n")
        
        products = self.get_all_products()
        total = len(products)
        
        if force:
            print(f"📊 Modo FORCE: Procesando TODOS los {total} productos\n")
        
        updated = 0
        errors = 0
        skipped = 0
        images_updated = 0
        
        print(f"🔄 Procesando {total} productos...\n")
        
        for i, product in enumerate(products, 1):
            product_id = product['id']
            title = product['name']
            images = product.get('images', [])
            
            if not images:
                skipped += 1
                continue
            
            print(f"{'='*70}")
            print(f"Producto {i}/{total} (ID: {product_id})")
            print(f"{'='*70}")
            print(f"Título: {title}")
            print(f"Imágenes: {len(images)}")
            
            # Generar alt text optimizado
            new_alt = self.generate_alt_text(product)
            
            print(f"\n🏷️  Alt text generado:")
            print(f"   {new_alt}")
            
            # Actualizar cada imagen del producto
            product_updated = False
            for img in images:
                image_id = img.get('id')
                current_alt = img.get('alt', '')
                
                if not image_id:
                    continue
                
                # Decidir si actualizar
                should_update = force or not current_alt or len(current_alt) < 20
                
                if should_update:
                    if not dry_run:
                        print(f"\n   🔄 Actualizando imagen {image_id}...")
                        if self.update_image_alt_text(image_id, new_alt, dry_run):
                            images_updated += 1
                            product_updated = True
                        else:
                            errors += 1
                        time.sleep(0.5)
                    else:
                        print(f"\n   ⏭️  DRY-RUN - Imagen {image_id}")
                        images_updated += 1
                        product_updated = True
            
            if product_updated:
                print(f"\n✅ Producto actualizado")
                updated += 1
            else:
                print(f"\n⏭️  Producto sin cambios")
                skipped += 1
            
            print()
            
            # Pausa cada batch
            if i % batch_size == 0 and i < total:
                print(f"\n⏸️  Pausa de 5 segundos... ({i}/{total} completados)")
                time.sleep(5)
        
        # Resumen
        print("\n" + "="*70)
        print("📊 RESUMEN FINAL")
        print("="*70)
        print(f"\nTotal productos: {total}")
        print(f"✅ Actualizados: {updated}")
        print(f"⏭️  Sin cambios: {skipped}")
        print(f"❌ Errores: {errors}")
        print(f"🖼️  Imágenes actualizadas: {images_updated}")
        print(f"📈 Tasa de éxito: {(updated/total*100):.1f}%")
        print("\n" + "="*70)


def main():
    parser = argparse.ArgumentParser(description='Optimizar alt text de imágenes')
    parser.add_argument('--url', required=True, help='URL del sitio')
    parser.add_argument('--key', required=True, help='Consumer Key')
    parser.add_argument('--secret', required=True, help='Consumer Secret')
    parser.add_argument('--wp-user', required=True, help='WordPress Username')
    parser.add_argument('--wp-pass', required=True, help='WordPress App Password')
    parser.add_argument('--batch-size', type=int, default=50, help='Tamaño del batch')
    parser.add_argument('--dry-run', action='store_true', help='Solo simular')
    parser.add_argument('--force', action='store_true', help='Actualizar todos')
    
    args = parser.parse_args()
    
    optimizer = AltTextOptimizer(args.url, args.key, args.secret, args.wp_user, args.wp_pass)
    optimizer.run(args.batch_size, args.dry_run, args.force)


if __name__ == "__main__":
    main()
