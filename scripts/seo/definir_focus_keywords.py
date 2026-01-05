#!/usr/bin/env python3
"""
DEFINIDOR AUTOMÁTICO DE FOCUS KEYWORDS SEO
==========================================
Asigna focus keywords únicas y relevantes a cada producto
Basado en categoría, características, ubicación y búsquedas comunes
"""

import requests
from requests.auth import HTTPBasicAuth
import sys
from typing import List, Dict, Optional
import time
import re
import argparse


class FocusKeywordGenerator:
    """Generador de focus keywords SEO"""
    
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
    
    def extract_product_type(self, title: str, sku: str, categories: List[Dict]) -> str:
        """Extrae el tipo de producto"""
        title_lower = title.lower()
        sku_lower = sku.lower()
        
        # Macetas
        if any(x in title_lower or x in sku_lower for x in ['maceta', 'mta', 'jardinera', 'matri']):
            return 'maceta'
        
        # Accesorios
        if any(x in title_lower for x in ['plato', 'platillo', 'base']):
            return 'accesorio'
        
        # Árboles
        if 'árbol' in title_lower or 'arbol' in title_lower:
            return 'arbol'
        
        # Plantas
        if 'planta' in title_lower:
            return 'planta'
        
        # Por categoría
        for cat in categories:
            cat_name = cat.get('name', '').lower()
            if 'maceta' in cat_name:
                return 'maceta'
            elif 'arbol' in cat_name or 'árbol' in cat_name:
                return 'arbol'
        
        return 'planta'
    
    def extract_size(self, title: str, sku: str) -> Optional[str]:
        """Extrae el tamaño del producto"""
        # Buscar litros
        match = re.search(r'(\d+)\s*[Ll]', title + ' ' + sku)
        if match:
            return f"{match.group(1)} litros"
        
        # Buscar cm
        match = re.search(r'(\d+)\s*cm', title, re.IGNORECASE)
        if match:
            return f"{match.group(1)} cm"
        
        return None
    
    def extract_plant_name(self, title: str) -> Optional[str]:
        """Extrae el nombre de la planta"""
        # Palabras clave que indican el inicio del nombre
        keywords = ['árbol', 'arbol', 'planta']
        
        for keyword in keywords:
            if keyword in title.lower():
                # Extraer lo que viene después
                parts = title.split()
                try:
                    idx = next(i for i, word in enumerate(parts) if keyword in word.lower())
                    if idx + 1 < len(parts):
                        name = parts[idx + 1]
                        # Limpiar
                        name = re.sub(r'[^\w\s]', '', name)
                        return name.lower()
                except StopIteration:
                    pass
        
        return None
    
    def extract_color(self, title: str) -> Optional[str]:
        """Extrae el color del producto"""
        if 'Color:' in title:
            match = re.search(r'Color:\s*(\w+(?:\s+\w+)?)', title)
            if match:
                return match.group(1).lower()
        
        # Colores comunes
        colors = ['negro', 'blanco', 'rojo', 'verde', 'azul', 'amarillo', 
                  'naranja', 'violeta', 'rosa', 'gris', 'marron', 'terracota']
        
        for color in colors:
            if color in title.lower():
                return color
        
        return None
    
    def generate_focus_keyword(self, product: Dict) -> str:
        """Genera el focus keyword ideal para un producto"""
        title = product['name']
        sku = product.get('sku', '')
        categories = product.get('categories', [])
        
        product_type = self.extract_product_type(title, sku, categories)
        size = self.extract_size(title, sku)
        plant_name = self.extract_plant_name(title)
        color = self.extract_color(title)
        
        # Construir keyword según tipo
        if product_type == 'maceta':
            # Maceta plástica [color] [tamaño] mendoza
            parts = ['maceta plástica']
            
            if color:
                parts.append(color)
            
            if size:
                parts.append(size)
            
            parts.append('mendoza')
            
            return ' '.join(parts)
        
        elif product_type == 'arbol':
            # [nombre] árbol [tamaño] mendoza
            parts = []
            
            if plant_name:
                parts.append(plant_name)
                parts.append('árbol')
            else:
                parts.append('árbol')
            
            if size:
                parts.append(size)
            
            parts.append('mendoza')
            
            return ' '.join(parts)
        
        elif product_type == 'planta':
            # planta [nombre] [tamaño] vivero mendoza
            parts = ['planta']
            
            if plant_name:
                parts.append(plant_name)
            
            if size:
                parts.append(size)
            
            parts.extend(['vivero', 'mendoza'])
            
            return ' '.join(parts)
        
        elif product_type == 'accesorio':
            # plato maceta [tamaño] mendoza
            parts = []
            
            if 'plato' in title.lower():
                parts.append('plato maceta')
            else:
                parts.append('accesorio jardín')
            
            if size:
                parts.append(size)
            
            parts.append('mendoza')
            
            return ' '.join(parts)
        
        else:
            # Genérico
            return f"vivero plantas mendoza"
    
    def update_product_keywords(self, product_id: int, focus_keyword: str, dry_run: bool = False) -> bool:
        """Actualiza el focus keyword en WooCommerce"""
        if dry_run:
            return True
        
        endpoint = f"{self.url}/wp-json/wc/v3/products/{product_id}"
        
        # Actualizar meta_data para Yoast y RankMath
        data = {
            'meta_data': [
                {
                    'key': '_yoast_wpseo_focuskw',
                    'value': focus_keyword
                },
                {
                    'key': '_rank_math_focus_keyword',
                    'value': focus_keyword
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
    
    def run(self, batch_size: int = 50, dry_run: bool = False, force: bool = False):
        """Ejecuta el generador de keywords"""
        print("\n" + "="*70)
        print("🎯 GENERADOR DE FOCUS KEYWORDS SEO")
        print("="*70 + "\n")
        
        products = self.get_all_products()
        total = len(products)
        
        if force:
            print(f"📊 Modo FORCE: Actualizando TODOS los {total} productos\n")
        
        updated = 0
        errors = 0
        
        print(f"🔄 Procesando {total} productos...\n")
        
        for i, product in enumerate(products, 1):
            product_id = product['id']
            title = product['name']
            
            print(f"{'='*70}")
            print(f"Producto {i}/{total}")
            print(f"{'='*70}")
            print(f"ID: {product_id}")
            print(f"Título: {title}")
            
            # Generar focus keyword
            focus_keyword = self.generate_focus_keyword(product)
            
            print(f"\n🎯 Focus Keyword: '{focus_keyword}'")
            
            if not dry_run:
                # Actualizar en WooCommerce
                print(f"\n🔄 Actualizando...")
                if self.update_product_keywords(product_id, focus_keyword, dry_run):
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
        print(f"📈 Tasa de éxito: {(updated/total*100):.1f}%")
        print("\n" + "="*70)


def main():
    parser = argparse.ArgumentParser(description='Generar focus keywords SEO')
    parser.add_argument('--url', required=True, help='URL del sitio')
    parser.add_argument('--key', required=True, help='Consumer Key')
    parser.add_argument('--secret', required=True, help='Consumer Secret')
    parser.add_argument('--batch-size', type=int, default=50, help='Tamaño del batch')
    parser.add_argument('--dry-run', action='store_true', help='Solo simular')
    parser.add_argument('--force', action='store_true', help='Actualizar todos')
    
    args = parser.parse_args()
    
    generator = FocusKeywordGenerator(args.url, args.key, args.secret)
    generator.run(args.batch_size, args.dry_run, args.force)


if __name__ == "__main__":
    main()
