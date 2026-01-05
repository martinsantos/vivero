#!/usr/bin/env python3
"""
ASIGNADOR AUTOMÁTICO DE TAGS
============================
Asigna tags relevantes a cada producto basado en:
- Categoría
- Tamaño
- Características
- Uso
- Tipo de planta
"""

import requests
from requests.auth import HTTPBasicAuth
import sys
from typing import List, Dict, Set, Optional
import time
import re
import argparse


class TagGenerator:
    """Generador de tags automáticos"""
    
    def __init__(self, url: str, consumer_key: str, consumer_secret: str):
        self.url = url.rstrip('/')
        self.consumer_key = consumer_key
        self.consumer_secret = consumer_secret
        self.session = requests.Session()
        self.session.auth = HTTPBasicAuth(consumer_key, consumer_secret)
        
        # Cache de tags existentes
        self.tag_cache = {}
    
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
    
    def get_or_create_tag(self, tag_name: str) -> Optional[int]:
        """Obtiene o crea un tag y retorna su ID"""
        # Verificar cache
        if tag_name in self.tag_cache:
            return self.tag_cache[tag_name]
        
        # Buscar tag existente
        endpoint = f"{self.url}/wp-json/wc/v3/products/tags"
        params = {"search": tag_name}
        
        try:
            response = self.session.get(endpoint, params=params)
            response.raise_for_status()
            tags = response.json()
            
            if tags:
                # Tag existe
                tag_id = tags[0]['id']
                self.tag_cache[tag_name] = tag_id
                return tag_id
            
            # Crear nuevo tag
            data = {"name": tag_name}
            response = self.session.post(endpoint, json=data)
            
            if response.status_code in [200, 201]:
                tag_id = response.json()['id']
                self.tag_cache[tag_name] = tag_id
                return tag_id
            
        except Exception as e:
            print(f"  ⚠️  Error con tag '{tag_name}': {e}")
        
        return None
    
    def generate_tags(self, product: Dict) -> Set[str]:
        """Genera tags relevantes para un producto"""
        title = product['name'].lower()
        sku = product.get('sku', '').lower()
        categories = product.get('categories', [])
        
        tags = set()
        
        # TAGS POR TIPO DE PRODUCTO
        if any(x in title or x in sku for x in ['maceta', 'mta', 'jardinera']):
            tags.add('Macetas')
            
            # Material
            if 'plástica' in title or 'plastic' in title:
                tags.add('Plástico')
            
            # Color
            colors = {
                'negro': 'Negro', 'blanco': 'Blanco', 'rojo': 'Rojo',
                'verde': 'Verde', 'azul': 'Azul', 'amarillo': 'Amarillo',
                'naranja': 'Naranja', 'violeta': 'Violeta', 'rosa': 'Rosa',
                'gris': 'Gris', 'marron': 'Marrón', 'terracota': 'Terracota'
            }
            for color_key, color_tag in colors.items():
                if color_key in title:
                    tags.add(color_tag)
        
        elif 'plato' in title:
            tags.add('Accesorios')
            tags.add('Platos')
        
        elif 'árbol' in title or 'arbol' in title:
            tags.add('Árboles')
            
            # Tipos específicos
            tree_types = {
                'olivo': 'Olivos',
                'tilo': 'Tilos',
                'abedul': 'Abedules',
                'liquidámbar': 'Liquidámbar',
                'eucalip': 'Eucaliptos',
                'ciruelo': 'Frutales',
                'prun': 'Frutales'
            }
            for key, tag in tree_types.items():
                if key in title or key in sku:
                    tags.add(tag)
            
            # Uso
            if any(x in title for x in ['sombra', 'ornamental']):
                tags.add('Ornamental')
            
            if 'frutal' in title:
                tags.add('Frutales')
        
        elif 'planta' in title:
            tags.add('Plantas')
            
            # Plantas específicas
            plant_types = {
                'dracena': 'Plantas de Interior',
                'interior': 'Plantas de Interior',
                'bougain': 'Enredaderas',
                'glicina': 'Enredaderas',
                'jazmin': 'Aromáticas',
                'jazmín': 'Aromáticas',
                'rosa': 'Rosales'
            }
            for key, tag in plant_types.items():
                if key in title or key in sku:
                    tags.add(tag)
        
        # TAGS POR TAMAÑO
        size_match = re.search(r'(\d+)\s*[Ll]', title + ' ' + sku)
        if size_match:
            litros = int(size_match.group(1))
            
            if litros <= 3:
                tags.add('Pequeño')
            elif litros <= 10:
                tags.add('Mediano')
            else:
                tags.add('Grande')
            
            # Tag específico de tamaño
            tags.add(f'{litros} Litros')
        
        # Buscar cm
        cm_match = re.search(r'(\d+)\s*cm', title, re.IGNORECASE)
        if cm_match:
            cm = int(cm_match.group(1))
            
            if cm <= 10:
                tags.add('Pequeño')
            elif cm <= 20:
                tags.add('Mediano')
            else:
                tags.add('Grande')
        
        # TAGS POR USO
        if any(x in title for x in ['exterior', 'jardín', 'jardin']):
            tags.add('Exterior')
        
        if any(x in title for x in ['interior', 'hogar']):
            tags.add('Interior')
        
        # TAG GENÉRICO
        tags.add('Vivero Mendoza')
        
        # TAG DE DISPONIBILIDAD
        if product.get('stock_status') == 'instock':
            tags.add('Stock Disponible')
        
        return tags
    
    def update_product_tags(self, product_id: int, tags: List[Dict], dry_run: bool = False) -> bool:
        """Actualiza los tags del producto"""
        if dry_run:
            return True
        
        endpoint = f"{self.url}/wp-json/wc/v3/products/{product_id}"
        data = {' tags': tags}
        
        try:
            response = self.session.put(endpoint, json=data)
            response.raise_for_status()
            return True
        except Exception as e:
            print(f"  ✗ Error actualizando producto {product_id}: {e}")
            return False
    
    def run(self, batch_size: int = 50, dry_run: bool = False, force: bool = False):
        """Ejecuta el generador de tags"""
        print("\n" + "="*70)
        print("🏷️  GENERADOR DE TAGS AUTOMÁTICOS")
        print("="*70 + "\n")
        
        products = self.get_all_products()
        total = len(products)
        
        if force:
            print(f"📊 Modo FORCE: Actualizando TODOS los {total} productos\n")
        
        updated = 0
        errors = 0
        total_tags_created = 0
        
        print(f"🔄 Procesando {total} productos...\n")
        
        for i, product in enumerate(products, 1):
            product_id = product['id']
            title = product['name']
            current_tags = product.get('tags', [])
            
            print(f"{'='*70}")
            print(f"Producto {i}/{total}")
            print(f"{'='*70}")
            print(f"ID: {product_id}")
            print(f"Título: {title}")
            print(f"Tags actuales: {len(current_tags)}")
            
            # Generar nuevos tags
            tag_names = self.generate_tags(product)
            
            print(f"\n🏷️  Tags generados ({len(tag_names)}):")
            for tag_name in sorted(tag_names):
                print(f"  - {tag_name}")
            
            if not dry_run:
                # Obtener IDs de tags
                tag_objects = []
                for tag_name in tag_names:
                    tag_id = self.get_or_create_tag(tag_name)
                    if tag_id:
                        tag_objects.append({'id': tag_id})
                
                if tag_objects:
                    total_tags_created += len(tag_objects)
                    
                    # Actualizar en WooCommerce
                    print(f"\n🔄 Actualizando con {len(tag_objects)} tags...")
                    if self.update_product_tags(product_id, tag_objects, dry_run):
                        print(f"✅ Actualizado correctamente")
                        updated += 1
                    else:
                        print(f"❌ Error al actualizar")
                        errors += 1
                else:
                    print(f"\n⚠️  No se pudieron crear tags")
                    errors += 1
                
                # Rate limiting
                time.sleep(1)
            else:
                print(f"\n⏭️  DRY-RUN - No se actualizó")
                updated += 1
            
            print()
            
            # Pausa cada batch
            if i % batch_size == 0 and i < total:
                print(f"\n⏸️  Pausa de 5 segundos... ({i}/{total} completados)")
                time.sleep(5)
        
        # Resumen
        print("\n" + "="*70)
        print("📊 RESUMEN FINAL")
        print("="*70)
        print(f"\nTotal procesados: {total}")
        print(f"✅ Actualizados: {updated}")
        print(f"❌ Errores: {errors}")
        print(f"🏷️  Tags únicos creados: {len(self.tag_cache)}")
        print(f"📈 Tasa de éxito: {(updated/total*100):.1f}%")
        print("\n" + "="*70)


def main():
    parser = argparse.ArgumentParser(description='Generar tags automáticos')
    parser.add_argument('--url', required=True, help='URL del sitio')
    parser.add_argument('--key', required=True, help='Consumer Key')
    parser.add_argument('--secret', required=True, help='Consumer Secret')
    parser.add_argument('--batch-size', type=int, default=50, help='Tamaño del batch')
    parser.add_argument('--dry-run', action='store_true', help='Solo simular')
    parser.add_argument('--force', action='store_true', help='Actualizar todos')
    
    args = parser.parse_args()
    
    generator = TagGenerator(args.url, args.key, args.secret)
    generator.run(args.batch_size, args.dry_run, args.force)


if __name__ == "__main__":
    main()
