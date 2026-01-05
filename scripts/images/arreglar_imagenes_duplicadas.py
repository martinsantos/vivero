#!/usr/bin/env python3
"""
ARREGLAR IMÁGENES DUPLICADAS - SOLUCIÓN URGENTE
===============================================
Detecta productos con imágenes duplicadas y les asigna imágenes únicas
desde APIs gratuitas (Unsplash, Pexels, iNaturalist)
"""

import requests
from requests.auth import HTTPBasicAuth
import sys
from typing import List, Dict, Set, Optional
from collections import defaultdict
import time
import os
import hashlib
import base64
from io import BytesIO
from PIL import Image


class ImageDuplicateFixer:
    """Arregla imágenes duplicadas asignando nuevas imágenes únicas"""
    
    def __init__(self, url: str, wc_key: str, wc_secret: str, wp_user: str, wp_pass: str):
        self.url = url.rstrip('/')
        self.wc_session = requests.Session()
        self.wc_session.auth = HTTPBasicAuth(wc_key, wc_secret)
        
        self.wp_user = wp_user
        self.wp_pass = wp_pass
        
        # APIs de imágenes
        self.unsplash_key = os.getenv('UNSPLASH_API_KEY', '')
        self.pexels_key = os.getenv('PEXELS_API_KEY', '')
        
        self.used_images = set()  # Track para evitar re-duplicar
    
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
        
        return all_products
    
    def find_duplicates(self, products: List[Dict]) -> Dict[str, List[Dict]]:
        """Encuentra productos con imágenes duplicadas"""
        url_to_products = defaultdict(list)
        
        for product in products:
            images = product.get('images', [])
            if images:
                img_url = images[0].get('src', '').split('?')[0]
                if img_url:
                    url_to_products[img_url].append(product)
        
        # Solo duplicados (2+ productos)
        return {
            url: prods 
            for url, prods in url_to_products.items() 
            if len(prods) > 1
        }
    
    def search_unique_image(self, product_name: str, sku: str) -> Optional[str]:
        """Busca una imagen única para el producto"""
        
        # Construir query de búsqueda inteligente
        search_terms = []
        
        # Extraer términos útiles del nombre
        name_lower = product_name.lower()
        
        if 'maceta' in name_lower or 'plástica' in name_lower:
            # Para macetas, buscar por color y material
            if 'color:' in name_lower:
                color = name_lower.split('color:')[1].strip().split()[0]
                search_terms.append(f"plastic pot {color}")
            else:
                search_terms.append("colorful plastic garden pot")
        
        elif any(tree in name_lower for tree in ['árbol', 'arbol', 'tree']):
            # Para árboles
            tree_types = {
                'olivo': 'olive tree garden',
                'oletex': 'olive tree',
                'prun': 'plum tree blossom',
                'tilo': 'linden tree',
                'abedul': 'birch tree white bark'
            }
            for key, value in tree_types.items():
                if key in name_lower or key in sku.lower():
                    search_terms.append(value)
                    break
            
            if not search_terms:
                search_terms.append("ornamental tree garden")
        
        elif 'planta' in name_lower:
            # Para plantas
            search_terms.append("houseplant green foliage")
        
        else:
            # Genérico
            search_terms.append(f"{product_name.split()[0]} plant")
        
        # Intentar Unsplash primero
        for term in search_terms:
            img_url = self._search_unsplash(term)
            if img_url and img_url not in self.used_images:
                self.used_images.add(img_url)
                return img_url
        
        # Si falla, intentar Pexels
        for term in search_terms:
            img_url = self._search_pexels(term)
            if img_url and img_url not in self.used_images:
                self.used_images.add(img_url)
                return img_url
        
        return None
    
    def _search_unsplash(self, query: str) -> Optional[str]:
        """Busca en Unsplash"""
        if not self.unsplash_key:
            return None
        
        try:
            url = "https://api.unsplash.com/search/photos"
            params = {
                'query': query,
                'per_page': 5,
                'orientation': 'square'
            }
            headers = {'Authorization': f'Client-ID {self.unsplash_key}'}
            
            response = requests.get(url, params=params, headers=headers, timeout=10)
            response.raise_for_status()
            data = response.json()
            
            if data.get('results'):
                # Tomar la primera imagen que no hayamos usado
                for result in data['results']:
                    img_url = result['urls']['regular']
                    if img_url not in self.used_images:
                        return img_url
            
        except Exception as e:
            print(f"  ⚠️  Error Unsplash: {e}")
        
        return None
    
    def _search_pexels(self, query: str) -> Optional[str]:
        """Busca en Pexels"""
        if not self.pexels_key:
            return None
        
        try:
            url = "https://api.pexels.com/v1/search"
            params = {
                'query': query,
                'per_page': 5,
                'orientation': 'square'
            }
            headers = {'Authorization': self.pexels_key}
            
            response = requests.get(url, params=params, headers=headers, timeout=10)
            response.raise_for_status()
            data = response.json()
            
            if data.get('photos'):
                for photo in data['photos']:
                    img_url = photo['src']['large']
                    if img_url not in self.used_images:
                        return img_url
            
        except Exception as e:
            print(f"  ⚠️  Error Pexels: {e}")
        
        return None
    
    def upload_image_to_wordpress(self, image_url: str, product_name: str) -> Optional[int]:
        """Sube imagen a WordPress y retorna media ID"""
        try:
            # Descargar imagen
            img_response = requests.get(image_url, timeout=15)
            img_response.raise_for_status()
            
            # Procesar imagen
            img = Image.open(BytesIO(img_response.content))
            
            # Resize a 1200x1200
            img.thumbnail((1200, 1200), Image.Resampling.LANCZOS)
            
            # Convertir a bytes
            img_bytes = BytesIO()
            img.save(img_bytes, format='JPEG', quality=90)
            img_bytes.seek(0)
            
            # Generar nombre de archivo
            filename = f"product_{hashlib.md5(product_name.encode()).hexdigest()[:8]}.jpg"
            
            # Subir a WordPress
            wp_endpoint = f"{self.url}/wp-json/wp/v2/media"
            
            headers = {
                'Content-Disposition': f'attachment; filename="{filename}"',
                'Content-Type': 'image/jpeg'
            }
            
            auth = HTTPBasicAuth(self.wp_user, self.wp_pass)
            
            response = requests.post(
                wp_endpoint,
                data=img_bytes.getvalue(),
                headers=headers,
                auth=auth,
                timeout=30
            )
            
            if response.status_code == 201:
                return response.json()['id']
            else:
                print(f"  ✗ Error subiendo: {response.status_code} - {response.text[:100]}")
                return None
                
        except Exception as e:
            print(f"  ✗ Error: {e}")
            return None
    
    def update_product_image(self, product_id: int, media_id: int) -> bool:
        """Actualiza la imagen del producto"""
        endpoint = f"{self.url}/wp-json/wc/v3/products/{product_id}"
        data = {
            'images': [{'id': media_id}]
        }
        
        try:
            response = self.wc_session.put(endpoint, json=data)
            response.raise_for_status()
            return True
        except Exception as e:
            print(f"  ✗ Error actualizando producto: {e}")
            return False
    
    def fix_duplicates(self, dry_run: bool = False):
        """Proceso principal para arreglar duplicados"""
        print("\n" + "="*70)
        print("🔧 ARREGLADOR DE IMÁGENES DUPLICADAS")
        print("="*70 + "\n")
        
        products = self.get_all_products()
        duplicates = self.find_duplicates(products)
        
        print(f"\n📊 Encontrados {len(duplicates)} grupos de imágenes duplicadas")
        print(f"📊 Total de productos afectados: {sum(len(prods) for prods in duplicates.values())}\n")
        
        if not duplicates:
            print("✅ ¡No hay duplicados!")
            return
        
        fixed = 0
        errors = 0
        
        for img_url, products_list in duplicates.items():
            print(f"\n{'='*70}")
            print(f"🔄 Procesando grupo de {len(products_list)} productos")
            print(f"{'='*70}")
            
            # Mantener la imagen en el primer producto
            # Asignar nuevas imágenes a los demás
            for i, product in enumerate(products_list[1:], 1):  # Skip first
                product_id = product['id']
                product_name = product['name']
                sku = product.get('sku', '')
                
                print(f"\n{i}. Producto: {product_name}")
                print(f"   ID: {product_id} | SKU: {sku}")
                
                if dry_run:
                    print(f"   🔍 DRY-RUN: Buscaría nueva imagen...")
                    continue
                
                # Buscar nueva imagen
                print(f"   🔍 Buscando imagen única...")
                new_img_url = self.search_unique_image(product_name, sku)
                
                if not new_img_url:
                    print(f"   ✗ No se encontró imagen")
                    errors += 1
                    continue
                
                print(f"   ✓ Imagen encontrada: {new_img_url[:50]}...")
                
                # Subir a WordPress
                print(f"   📤 Subiendo a WordPress...")
                media_id = self.upload_image_to_wordpress(new_img_url, product_name)
                
                if not media_id:
                    print(f"   ✗ Error subiendo")
                    errors += 1
                    continue
                
                print(f"   ✓ Subida exitosa (Media ID: {media_id})")
                
                # Actualizar producto
                print(f"   🔄 Actualizando producto...")
                if self.update_product_image(product_id, media_id):
                    print(f"   ✅ Producto actualizado correctamente")
                    fixed += 1
                else:
                    errors += 1
                
                # Rate limiting
                time.sleep(2)
        
        # Resumen
        print("\n" + "="*70)
        print("📊 RESUMEN")
        print("="*70)
        print(f"\n✅ Productos arreglados: {fixed}")
        print(f"❌ Errores: {errors}")
        print("\n" + "="*70)


def main():
    import argparse
    
    parser = argparse.ArgumentParser(description='Arreglar imágenes duplicadas')
    parser.add_argument('--url', required=True, help='URL del sitio')
    parser.add_argument('--wc-key', required=True, help='WooCommerce Consumer Key')
    parser.add_argument('--wc-secret', required=True, help='WooCommerce Consumer Secret')
    parser.add_argument('--wp-user', required=True, help='WordPress Username')
    parser.add_argument('--wp-pass', required=True, help='WordPress App Password')
    parser.add_argument('--dry-run', action='store_true', help='Solo simular')
    
    args = parser.parse_args()
    
    # Verificar API keys
    if not os.getenv('UNSPLASH_API_KEY') and not os.getenv('PEXELS_API_KEY'):
        print("⚠️  WARNING: No UNSPLASH_API_KEY ni PEXELS_API_KEY configuradas")
        print("   Las imágenes pueden no encontrarse")
    
    fixer = ImageDuplicateFixer(
        args.url,
        args.wc_key,
        args.wc_secret,
        args.wp_user,
        args.wp_pass
    )
    
    fixer.fix_duplicates(args.dry_run)


if __name__ == "__main__":
    main()
