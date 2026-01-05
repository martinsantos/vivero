#!/usr/bin/env python3
"""
AGREGAR TAGS AUTOMÁTICOS A PRODUCTOS WOOCOMMERCE
===============================================

Genera y asigna tags automáticamente a productos basándose en:
- Categorías del producto
- Atributos (tamaño, tipo, uso)
- Palabras clave del nombre
- Características del producto

Uso:
    python3 agregar_tags_woocommerce.py --dry-run
    python3 agregar_tags_woocommerce.py --max-products 50
    python3 agregar_tags_woocommerce.py  # Ejecutar en todos
"""

import argparse
import json
import logging
import os
import re
import sys
from typing import Any, Dict, List, Set
from collections import Counter

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


class WooCommerceTagGenerator:
    """Generador de tags para productos WooCommerce"""
    
    def __init__(self, wordpress_url: str, wc_key: str, wc_secret: str):
        self.wordpress_url = wordpress_url.rstrip('/')
        self.wc_key = wc_key
        self.wc_secret = wc_secret
        self.session = requests.Session()
        
        # Cache de tags existentes
        self.existing_tags: Dict[str, int] = {}  # {nombre: id}
        self.load_existing_tags()
        
    def load_existing_tags(self):
        """Carga todos los tags existentes en WooCommerce"""
        log.info("Cargando tags existentes...")
        page = 1
        
        while True:
            url = f"{self.wordpress_url}/wp-json/wc/v3/products/tags"
            params = {"per_page": 100, "page": page}
            
            try:
                r = self.session.get(
                    url,
                    params=params,
                    auth=(self.wc_key, self.wc_secret),
                    timeout=30
                )
                r.raise_for_status()
                
                tags = r.json()
                if not tags:
                    break
                
                for tag in tags:
                    self.existing_tags[tag['name'].lower()] = tag['id']
                
                page += 1
                
            except Exception as e:
                log.error(f"Error cargando tags: {e}")
                break
        
        log.info(f"✅ {len(self.existing_tags)} tags existentes cargados")
    
    def create_tag(self, name: str) -> int:
        """Crea un nuevo tag en WooCommerce"""
        # Verificar si ya existe (case-insensitive)
        name_lower = name.lower()
        if name_lower in self.existing_tags:
            return self.existing_tags[name_lower]
        
        url = f"{self.wordpress_url}/wp-json/wc/v3/products/tags"
        data = {"name": name}
        
        try:
            r = self.session.post(
                url,
                json=data,
                auth=(self.wc_key, self.wc_secret),
                timeout=30
            )
            r.raise_for_status()
            
            tag = r.json()
            tag_id = tag['id']
            
            # Actualizar cache
            self.existing_tags[name_lower] = tag_id
            
            log.debug(f"Tag creado: '{name}' (ID: {tag_id})")
            return tag_id
            
        except Exception as e:
            log.error(f"Error creando tag '{name}': {e}")
            return None
    
    def generate_tags_from_product(self, product: Dict[str, Any]) -> List[str]:
        """
        Genera lista de tags relevantes para un producto
        """
        tags: Set[str] = set()
        
        name = product.get('name', '').strip()
        description = product.get('short_description', '')
        categories = product.get('categories', [])
        attributes = product.get('attributes', [])
        
        # 1. Tags desde categorías
        for cat in categories:
            cat_name = cat.get('name', '').strip()
            if cat_name:
                # Agregar categoría completa
                tags.add(cat_name)
                
                # Agregar palabras individuales si son significativas
                words = [w for w in cat_name.split() if len(w) > 3]
                for word in words:
                    if word.lower() not in ['para', 'plantas', 'planta']:
                        tags.add(word.capitalize())
        
        # 2. Tags desde atributos
        for attr in attributes:
            attr_name = attr.get('name', '').strip()
            attr_options = attr.get('options', [])
            
            # Agregar nombre de atributo
            if attr_name and attr_name.lower() not in ['tamaño', 'tamaño maceta']:
                tags.add(attr_name.capitalize())
            
            # Agregar valores de atributo
            for option in attr_options:
                if isinstance(option, str) and len(option) > 2:
                    # Filtrar valores numéricos puros
                    if not re.match(r'^\d+\s*(cm|ml|litros?|l)$', option.lower()):
                        tags.add(option.capitalize())
        
        # 3. Tags desde nombre del producto
        # Detectar características comunes
        name_lower = name.lower()
        
        # Tamaño/recipiente
        if any(x in name_lower for x in ['maceta', 'pot']):
            tags.add('Maceta')
        
        # Tamaño en litros
        litros_match = re.search(r'(\d+)\s*litros?', name_lower)
        if litros_match:
            litros = litros_match.group(1)
            tags.add(f'{litros} Litros')
        
        # Tipo de planta
        tipos_planta = {
            'arbol': 'Árbol',
            'arboles': 'Árbol',
            'arbusto': 'Arbusto',
            'trepadora': 'Trepadora',
            'trepador': 'Trepadora',
            'palma': 'Palma',
            'palmera': 'Palma',
            'cactus': 'Cactus',
            'suculenta': 'Suculenta',
            'helecho': 'Helecho',
            'flor': 'Flor de Temporada',
            'flores': 'Flor de Temporada'
        }
        
        for key, value in tipos_planta.items():
            if key in name_lower:
                tags.add(value)
        
        # Uso/ubicación
        if any(x in name_lower for x in ['interior', 'indoor']):
            tags.add('Planta de Interior')
        if any(x in name_lower for x in ['exterior', 'outdoor', 'jardin']):
            tags.add('Planta de Exterior')
        
        # Características especiales
        caracteristicas = {
            'floral': 'Floral',
            'flores': 'Floral',
            'ornamental': 'Ornamental',
            'decorativa': 'Decorativa',
            'frutal': 'Frutal',
            'aromatica': 'Aromática',
            'comestible': 'Comestible',
            'nativa': 'Nativa',
            'perenne': 'Perenne'
        }
        
        for key, value in caracteristicas.items():
            if key in name_lower or key in description.lower():
                tags.add(value)
        
        # 4. Extraer nombre científico como tag
        scientific_pattern = r'\b([A-Z][a-z]+)\s+(?:x\s+)?([a-z]+)\b'
        match = re.search(scientific_pattern, name)
        if match:
            genus = match.group(1)
            # Agregar solo género (más útil que nombre completo)
            if len(genus) > 3:
                tags.add(genus)
        
        # 5. Filtrar y limpiar tags
        cleaned_tags = []
        
        # Palabras a excluir como tags
        exclude = {
            'de', 'del', 'la', 'el', 'los', 'las', 'en', 'con', 'para', 'por',
            'y', 'o', 'un', 'una', 'planta', 'plantas', 'vivero'
        }
        
        for tag in tags:
            tag_clean = tag.strip()
            
            # Filtros
            if not tag_clean:
                continue
            if tag_clean.lower() in exclude:
                continue
            if len(tag_clean) < 3:
                continue
            if tag_clean.isdigit():
                continue
            
            cleaned_tags.append(tag_clean)
        
        return list(set(cleaned_tags))  # Eliminar duplicados
    
    def get_all_products(self, max_products: int = None) -> List[Dict[str, Any]]:
        """Obtiene todos los productos publicados"""
        products = []
        page = 1
        
        log.info("Obteniendo productos...")
        
        while True:
            url = f"{self.wordpress_url}/wp-json/wc/v3/products"
            params = {"per_page": 100, "page": page, "status": "publish"}
            
            try:
                r = self.session.get(
                    url,
                    params=params,
                    auth=(self.wc_key, self.wc_secret),
                    timeout=30
                )
                r.raise_for_status()
                
                data = r.json()
                if not data:
                    break
                
                products.extend(data)
                log.info(f"  Página {page}: {len(data)} productos")
                
                if max_products and len(products) >= max_products:
                    products = products[:max_products]
                    break
                
                page += 1
                
            except Exception as e:
                log.error(f"Error obteniendo productos página {page}: {e}")
                break
        
        log.info(f"✅ Total: {len(products)} productos obtenidos")
        return products
    
    def assign_tags_to_product(self, product_id: int, tag_names: List[str], 
                               dry_run: bool = True) -> bool:
        """Asigna tags a un producto"""
        if not tag_names:
            return True
        
        # Crear tags si no existen y obtener IDs
        tag_objects = []
        
        for tag_name in tag_names:
            tag_id = self.create_tag(tag_name)
            if tag_id:
                tag_objects.append({"id": tag_id})
        
        if not tag_objects:
            return False
        
        if dry_run:
            log.info(f"[DRY-RUN] Asignaría {len(tag_objects)} tags al producto {product_id}")
            return True
        
        # Actualizar producto
        url = f"{self.wordpress_url}/wp-json/wc/v3/products/{product_id}"
        data = {"tags": tag_objects}
        
        try:
            r = self.session.put(
                url,
                json=data,
                auth=(self.wc_key, self.wc_secret),
                timeout=30
            )
            r.raise_for_status()
            
            log.info(f"✅ {len(tag_objects)} tags asignados al producto {product_id}")
            return True
            
        except Exception as e:
            log.error(f"Error asignando tags al producto {product_id}: {e}")
            return False
    
    def process_all_products(self, max_products: int = None, 
                           dry_run: bool = True) -> Dict[str, Any]:
        """Procesa todos los productos y asigna tags"""
        products = self.get_all_products(max_products)
        
        stats = {
            'total': len(products),
            'processed': 0,
            'success': 0,
            'failed': 0,
            'tags_created': 0,
            'tags_assigned': 0
        }
        
        # Contador de tags generados (para estadísticas)
        all_tags_generated = []
        
        log.info(f"\n{'='*60}")
        log.info(f"PROCESANDO {len(products)} PRODUCTOS")
        log.info(f"Modo: {'DRY-RUN' if dry_run else 'PRODUCCIÓN'}")
        log.info(f"{'='*60}\n")
        
        for product in tqdm(products, desc="Asignando tags"):
            product_id = product['id']
            product_name = product['name']
            current_tags = product.get('tags', [])
            
            stats['processed'] += 1
            
            # Generar tags
            generated_tags = self.generate_tags_from_product(product)
            
            if not generated_tags:
                log.warning(f"No se generaron tags para: {product_name}")
                continue
            
            all_tags_generated.extend(generated_tags)
            
            # Combinar con tags existentes
            existing_tag_names = [t['name'] for t in current_tags]
            new_tags = [t for t in generated_tags if t not in existing_tag_names]
            
            if not new_tags:
                log.debug(f"Producto {product_id} ya tiene todos los tags necesarios")
                stats['success'] += 1
                continue
            
            # Combinar tags existentes + nuevos
            all_tag_names = existing_tag_names + new_tags
            
            # Asignar tags
            if self.assign_tags_to_product(product_id, all_tag_names, dry_run=dry_run):
                stats['success'] += 1
                stats['tags_assigned'] += len(new_tags)
            else:
                stats['failed'] += 1
        
        # Estadísticas de tags más comunes
        tag_counter = Counter(all_tags_generated)
        stats['top_tags'] = tag_counter.most_common(20)
        stats['unique_tags'] = len(tag_counter)
        
        return stats


def main():
    parser = argparse.ArgumentParser(description="Agregar tags automáticos a productos WooCommerce")
    parser.add_argument("--dry-run", action="store_true", help="Simular sin aplicar cambios")
    parser.add_argument("--max-products", type=int, help="Limitar número de productos a procesar")
    parser.add_argument("--output", default="tags_resultados.json", help="Archivo de resultados JSON")
    
    args = parser.parse_args()
    
    # Cargar credenciales
    wordpress_url = os.getenv('WORDPRESS_URL')
    wc_key = os.getenv('WC_CONSUMER_KEY')
    wc_secret = os.getenv('WC_CONSUMER_SECRET')
    
    if not all([wordpress_url, wc_key, wc_secret]):
        log.error("❌ Faltan variables de entorno: WORDPRESS_URL, WC_CONSUMER_KEY, WC_CONSUMER_SECRET")
        sys.exit(1)
    
    # Crear generador
    generator = WooCommerceTagGenerator(wordpress_url, wc_key, wc_secret)
    
    # Procesar productos
    stats = generator.process_all_products(
        max_products=args.max_products,
        dry_run=args.dry_run
    )
    
    # Guardar resultados
    with open(args.output, 'w', encoding='utf-8') as f:
        json.dump(stats, f, ensure_ascii=False, indent=2)
    
    # Resumen
    print("\n" + "="*60)
    print("📊 RESUMEN DE TAGS")
    print("="*60)
    print(f"Total productos: {stats['total']}")
    print(f"Procesados: {stats['processed']}")
    print(f"Exitosos: {stats['success']}")
    print(f"Fallidos: {stats['failed']}")
    print(f"Tags únicos generados: {stats['unique_tags']}")
    print(f"Tags asignados: {stats['tags_assigned']}")
    
    if stats.get('top_tags'):
        print(f"\n🏷️  Top 10 tags más comunes:")
        for tag, count in stats['top_tags'][:10]:
            print(f"  {tag}: {count} productos")
    
    print(f"\n📄 Resultados: {args.output}")
    print("="*60)
    
    if args.dry_run:
        print("\n⚠️  Ejecutar sin --dry-run para aplicar cambios")


if __name__ == "__main__":
    main()
