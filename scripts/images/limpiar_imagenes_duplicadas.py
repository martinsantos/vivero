#!/usr/bin/env python3
"""
LIMPIEZA DE IMÁGENES DUPLICADAS - VIVERO LOS COCOS
===================================================

Problema:
- Imágenes duplicadas (misma imagen, diferentes IDs)
- Imágenes sin usar (1477 de 2000)
- Asignaciones incoherentes

Solución:
1. Identificar duplicados por hash SHA1
2. Consolidar a una sola imagen por contenido
3. Re-asignar productos a imagen canónica
4. Eliminar duplicados de WordPress y servidor

Uso:
    python3 limpiar_imagenes_duplicadas.py --dry-run
    python3 limpiar_imagenes_duplicadas.py --ejecutar
"""

import argparse
import hashlib
import logging
import os
import sys
from collections import defaultdict
from typing import Dict, List, Set

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


class ImagenCleaner:
    def __init__(self):
        self.wp_url = os.getenv('WORDPRESS_URL', '').rstrip('/')
        self.wc_key = os.getenv('WC_CONSUMER_KEY', '')
        self.wc_secret = os.getenv('WC_CONSUMER_SECRET', '')
        self.wp_user = os.getenv('WP_USERNAME', '')
        self.wp_pass = os.getenv('WP_APP_PASSWORD', '')
        
        if not all([self.wp_url, self.wc_key, self.wc_secret, self.wp_user, self.wp_pass]):
            raise ValueError("Missing credentials in .env")
        
        self.media_items = []
        self.productos = []
        self.hash_to_images = defaultdict(list)  # hash -> [image_ids]
        self.image_to_productos = defaultdict(list)  # image_id -> [producto_ids]
        self.productos_to_images = defaultdict(list)  # producto_id -> [image_ids]
    
    def obtener_media_library(self):
        """Obtiene todas las imágenes del media library"""
        log.info("Obteniendo imágenes del Media Library...")
        
        page = 1
        while page <= 20:
            r = requests.get(
                f'{self.wp_url}/wp-json/wp/v2/media',
                params={'per_page': 100, 'page': page, 'media_type': 'image'},
                auth=(self.wp_user, self.wp_pass),
                timeout=30
            )
            
            if r.status_code != 200:
                break
            
            data = r.json()
            if not data:
                break
            
            self.media_items.extend(data)
            page += 1
        
        log.info(f"Total imágenes: {len(self.media_items)}")
    
    def obtener_productos(self):
        """Obtiene todos los productos"""
        log.info("Obteniendo productos...")
        
        page = 1
        while page <= 6:
            r = requests.get(
                f'{self.wp_url}/wp-json/wc/v3/products',
                auth=(self.wc_key, self.wc_secret),
                params={'per_page': 100, 'page': page},
                timeout=30
            )
            
            if r.status_code != 200:
                break
            
            data = r.json()
            if not data:
                break
            
            self.productos.extend(data)
            page += 1
        
        log.info(f"Total productos: {len(self.productos)}")
    
    def calcular_hash_imagen(self, url: str) -> str:
        """Calcula hash SHA1 de una imagen por URL"""
        try:
            r = requests.get(url, timeout=30)
            r.raise_for_status()
            return hashlib.sha1(r.content).hexdigest()
        except Exception as e:
            log.warning(f"Error calculando hash de {url}: {e}")
            return None
    
    def analizar_duplicados(self):
        """Identifica imágenes duplicadas por hash"""
        log.info("Analizando duplicados por hash SHA1...")
        
        for item in tqdm(self.media_items, desc="Calculando hashes"):
            url = item.get('source_url')
            if not url:
                continue
            
            img_hash = self.calcular_hash_imagen(url)
            if img_hash:
                self.hash_to_images[img_hash].append({
                    'id': item['id'],
                    'url': url,
                    'title': item.get('title', {}).get('rendered', ''),
                    'alt': item.get('alt_text', '')
                })
        
        # Filtrar solo duplicados
        self.duplicados = {k: v for k, v in self.hash_to_images.items() if len(v) > 1}
        
        log.info(f"Hashes únicos: {len(self.hash_to_images)}")
        log.info(f"Duplicados encontrados: {len(self.duplicados)}")
    
    def mapear_uso_imagenes(self):
        """Mapea qué imágenes usan qué productos"""
        log.info("Mapeando uso de imágenes en productos...")
        
        for producto in self.productos:
            prod_id = producto['id']
            
            for img in producto.get('images', []):
                img_id = img.get('id')
                if img_id:
                    self.image_to_productos[img_id].append(prod_id)
                    self.productos_to_images[prod_id].append(img_id)
    
    def generar_plan_consolidacion(self) -> Dict:
        """Genera plan de consolidación: qué duplicados eliminar y cómo re-asignar"""
        log.info("Generando plan de consolidación...")
        
        plan = {
            'consolidaciones': [],
            'eliminaciones': [],
            'reasignaciones': []
        }
        
        for img_hash, imagenes in self.duplicados.items():
            # Elegir imagen canónica (la más usada o la primera)
            uso_por_imagen = [(img, len(self.image_to_productos.get(img['id'], []))) for img in imagenes]
            uso_por_imagen.sort(key=lambda x: x[1], reverse=True)
            
            canonica = uso_por_imagen[0][0]
            duplicados_a_eliminar = [img for img, _ in uso_por_imagen[1:]]
            
            # Recopilar todos los productos que usan cualquier duplicado
            productos_afectados = set()
            for img in imagenes:
                productos_afectados.update(self.image_to_productos.get(img['id'], []))
            
            plan['consolidaciones'].append({
                'hash': img_hash,
                'canonica': canonica,
                'duplicados': duplicados_a_eliminar,
                'productos_afectados': list(productos_afectados)
            })
            
            # Planificar eliminaciones
            for img in duplicados_a_eliminar:
                plan['eliminaciones'].append(img['id'])
            
            # Planificar re-asignaciones
            for prod_id in productos_afectados:
                plan['reasignaciones'].append({
                    'producto_id': prod_id,
                    'imagen_nueva': canonica['id'],
                    'imagenes_viejas': [img['id'] for img in duplicados_a_eliminar]
                })
        
        return plan
    
    def ejecutar_consolidacion(self, plan: Dict, dry_run: bool = True):
        """Ejecuta el plan de consolidación"""
        
        if dry_run:
            log.info("=" * 60)
            log.info("MODO DRY-RUN: No se aplicarán cambios")
            log.info("=" * 60)
        
        log.info(f"\nConsolidaciones planificadas: {len(plan['consolidaciones'])}")
        log.info(f"Imágenes a eliminar: {len(plan['eliminaciones'])}")
        log.info(f"Productos a re-asignar: {len(plan['reasignaciones'])}")
        
        if not plan['consolidaciones']:
            log.info("No hay duplicados para consolidar")
            return
        
        # Mostrar ejemplos
        log.info("\nEjemplos de consolidación:")
        for i, cons in enumerate(plan['consolidaciones'][:5], 1):
            log.info(f"\n{i}. Hash: {cons['hash'][:16]}...")
            log.info(f"   Canónica: ID {cons['canonica']['id']} - {cons['canonica']['title']}")
            log.info(f"   Duplicados: {len(cons['duplicados'])} imágenes")
            log.info(f"   Productos afectados: {len(cons['productos_afectados'])}")
        
        if dry_run:
            return
        
        # Ejecutar re-asignaciones
        log.info("\nRe-asignando productos...")
        for reasig in tqdm(plan['reasignaciones'], desc="Re-asignando"):
            producto_id = reasig['producto_id']
            imagen_nueva = reasig['imagen_nueva']
            
            # Obtener producto actual
            r = requests.get(
                f"{self.wp_url}/wp-json/wc/v3/products/{producto_id}",
                auth=(self.wc_key, self.wc_secret),
                timeout=30
            )
            
            if r.status_code != 200:
                continue
            
            producto = r.json()
            imagenes_actuales = producto.get('images', [])
            
            # Consolidar a imagen canónica única
            imagenes_nuevas = [{'id': imagen_nueva}]
            
            # Actualizar producto
            r = requests.put(
                f"{self.wp_url}/wp-json/wc/v3/products/{producto_id}",
                json={'images': imagenes_nuevas},
                auth=(self.wc_key, self.wc_secret),
                timeout=30
            )
            
            if r.status_code != 200:
                log.error(f"Error re-asignando producto {producto_id}")
        
        # Eliminar duplicados del media library
        log.info("\nEliminando imágenes duplicadas...")
        for img_id in tqdm(plan['eliminaciones'], desc="Eliminando"):
            r = requests.delete(
                f"{self.wp_url}/wp-json/wp/v2/media/{img_id}",
                params={'force': True},
                auth=(self.wp_user, self.wp_pass),
                timeout=30
            )
            
            if r.status_code not in [200, 204]:
                log.warning(f"Error eliminando imagen {img_id}: {r.status_code}")
        
        log.info("Consolidación completada")
    
    def eliminar_imagenes_sin_usar(self, dry_run: bool = True):
        """Elimina imágenes que no están asignadas a ningún producto"""
        log.info("\nIdentificando imágenes sin usar...")
        
        imagenes_usadas = set()
        for prod in self.productos:
            for img in prod.get('images', []):
                if img.get('id'):
                    imagenes_usadas.add(img['id'])
        
        imagenes_sin_usar = [
            item for item in self.media_items 
            if item['id'] not in imagenes_usadas
        ]
        
        log.info(f"Imágenes sin usar: {len(imagenes_sin_usar)}")
        
        if dry_run:
            log.info("DRY-RUN: No se eliminarán")
            return
        
        if not imagenes_sin_usar:
            return
        
        respuesta = input(f"\n¿Eliminar {len(imagenes_sin_usar)} imágenes sin usar? (s/N): ")
        if respuesta.lower() != 's':
            log.info("Cancelado por usuario")
            return
        
        for item in tqdm(imagenes_sin_usar, desc="Eliminando sin usar"):
            r = requests.delete(
                f"{self.wp_url}/wp-json/wp/v2/media/{item['id']}",
                params={'force': True},
                auth=(self.wp_user, self.wp_pass),
                timeout=30
            )


def main():
    parser = argparse.ArgumentParser(description='Limpiar imágenes duplicadas')
    parser.add_argument('--dry-run', action='store_true', help='Simular sin aplicar cambios')
    parser.add_argument('--ejecutar', action='store_true', help='Ejecutar limpieza real')
    parser.add_argument('--sin-usar', action='store_true', help='También eliminar imágenes sin usar')
    args = parser.parse_args()
    
    if not args.dry_run and not args.ejecutar:
        log.error("Especifica --dry-run o --ejecutar")
        sys.exit(1)
    
    cleaner = ImagenCleaner()
    
    # Fase 1: Obtener datos
    cleaner.obtener_media_library()
    cleaner.obtener_productos()
    
    # Fase 2: Analizar duplicados
    cleaner.analizar_duplicados()
    cleaner.mapear_uso_imagenes()
    
    # Fase 3: Generar plan
    plan = cleaner.generar_plan_consolidacion()
    
    # Fase 4: Ejecutar
    cleaner.ejecutar_consolidacion(plan, dry_run=args.dry_run)
    
    # Fase 5: Eliminar sin usar (opcional)
    if args.sin_usar:
        cleaner.eliminar_imagenes_sin_usar(dry_run=args.dry_run)
    
    log.info("=" * 60)
    log.info("Proceso completado")


if __name__ == '__main__':
    main()
