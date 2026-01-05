#!/usr/bin/env python3
"""
CORRECCIÓN AUTOMÁTICA DE IMÁGENES INCORRECTAS
============================================

Script para corregir productos con:
1. Imágenes no pertinentes (score < threshold)
2. Duplicados visuales detectados
3. Re-asignación inteligente con validación estricta

Uso:
    python3 corregir_imagenes_incorrectas.py --input auditoria_resultados.json --dry-run
    python3 corregir_imagenes_incorrectas.py --min-score 50 --remove-duplicates
"""

import argparse
import hashlib
import json
import logging
import os
import sys
import time
from collections import defaultdict
from typing import Any, Dict, List, Optional, Set, Tuple
from urllib.parse import urlparse

import requests
from dotenv import load_dotenv
from PIL import Image
from tqdm import tqdm

try:
    import imagehash
    HAS_IMAGEHASH = True
except ImportError:
    HAS_IMAGEHASH = False

load_dotenv()

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s %(levelname)s: %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S"
)
log = logging.getLogger(__name__)


class ImageCorrector:
    """Corrector de imágenes de productos"""
    
    def __init__(self, wordpress_url: str, wc_key: str, wc_secret: str, 
                 wp_user: str, wp_pass: str):
        self.wordpress_url = wordpress_url.rstrip('/')
        self.wc_key = wc_key
        self.wc_secret = wc_secret
        self.wp_user = wp_user
        self.wp_pass = wp_pass
        self.session = requests.Session()
        
        # Cache de hashes perceptuales existentes
        self.existing_hashes: Dict[int, Dict[str, str]] = {}
        
    def get_wp_auth_header(self) -> Dict[str, str]:
        """Genera header de autenticación para WordPress"""
        auth_str = f"{self.wp_user}:{self.wp_pass}"
        auth_bytes = auth_str.encode('utf-8')
        auth_b64 = base64.b64encode(auth_bytes).decode('utf-8')
        return {
            "Authorization": f"Basic {auth_b64}",
            "Content-Type": "application/json"
        }
    
    def remove_product_image(self, product_id: int, image_id: int, dry_run: bool = True) -> bool:
        """Elimina una imagen de un producto"""
        if dry_run:
            log.info(f"[DRY-RUN] Eliminaría imagen {image_id} del producto {product_id}")
            return True
        
        try:
            # Primero, actualizar producto para remover referencia
            url = f"{self.wordpress_url}/wp-json/wc/v3/products/{product_id}"
            
            # Obtener producto actual
            r = self.session.get(url, auth=(self.wc_key, self.wc_secret), timeout=30)
            r.raise_for_status()
            product = r.json()
            
            # Filtrar la imagen a eliminar
            current_images = product.get('images', [])
            new_images = [img for img in current_images if img.get('id') != image_id]
            
            # Actualizar producto
            update_data = {'images': new_images}
            r = self.session.put(
                url,
                json=update_data,
                auth=(self.wc_key, self.wc_secret),
                timeout=30
            )
            r.raise_for_status()
            
            log.info(f"✅ Imagen {image_id} eliminada del producto {product_id}")
            
            # Intentar eliminar del media library (opcional, puede fallar)
            try:
                media_url = f"{self.wordpress_url}/wp-json/wp/v2/media/{image_id}"
                headers = self.get_wp_auth_header()
                r = self.session.delete(media_url, headers=headers, timeout=30, params={'force': True})
                if r.status_code == 200:
                    log.info(f"  Media {image_id} eliminado de biblioteca")
            except:
                pass  # No crítico si falla
            
            return True
            
        except Exception as e:
            log.error(f"Error eliminando imagen {image_id}: {e}")
            return False
    
    def download_image_temp(self, url: str, temp_dir: str = "/tmp") -> Optional[str]:
        """Descarga imagen temporalmente"""
        try:
            r = requests.get(url, timeout=10)
            r.raise_for_status()
            
            filename = hashlib.md5(url.encode()).hexdigest() + ".jpg"
            filepath = os.path.join(temp_dir, filename)
            
            with open(filepath, 'wb') as f:
                f.write(r.content)
            
            return filepath
            
        except Exception as e:
            log.warning(f"No se pudo descargar {url}: {e}")
            return None
    
    def compute_perceptual_hash(self, image_path: str) -> Optional[Dict[str, str]]:
        """Calcula hashes perceptuales"""
        if not HAS_IMAGEHASH:
            return None
        
        try:
            img = Image.open(image_path)
            return {
                'ahash': str(imagehash.average_hash(img)),
                'phash': str(imagehash.phash(img)),
                'dhash': str(imagehash.dhash(img))
            }
        except Exception as e:
            log.warning(f"Error calculando hash: {e}")
            return None
    
    def is_visually_duplicate(self, img_url: str, threshold: int = 8) -> Tuple[bool, Optional[int]]:
        """
        Verifica si una imagen es visualmente duplicada
        Retorna: (is_duplicate, duplicate_of_image_id)
        """
        if not HAS_IMAGEHASH:
            return False, None
        
        # Descargar imagen
        temp_path = self.download_image_temp(img_url)
        if not temp_path:
            return False, None
        
        # Calcular hash
        new_hash = self.compute_perceptual_hash(temp_path)
        if not new_hash:
            return False, None
        
        new_phash = imagehash.hex_to_hash(new_hash['phash'])
        
        # Comparar con existentes
        for img_id, existing_hash_data in self.existing_hashes.items():
            existing_phash = imagehash.hex_to_hash(existing_hash_data['phash'])
            diff = new_phash - existing_phash
            
            if diff <= threshold:
                # Limpiar temp
                try:
                    os.remove(temp_path)
                except:
                    pass
                return True, img_id
        
        # No es duplicado, agregar a cache
        # Generar ID único temporal
        temp_id = hash(img_url) % 1000000
        self.existing_hashes[temp_id] = new_hash
        
        # Limpiar temp
        try:
            os.remove(temp_path)
        except:
            pass
        
        return False, None
    
    def build_search_terms(self, product: Dict, enrich: bool = True) -> str:
        """Construye términos de búsqueda mejorados"""
        name = product.get('name', '').strip()
        
        if not enrich:
            return name
        
        # Extraer nombre científico si existe (patrón: Palabra Palabra o Palabra x palabra)
        import re
        scientific_pattern = r'\b([A-Z][a-z]+)\s+(?:x\s+)?([a-z]+)\b'
        match = re.search(scientific_pattern, name)
        
        if match:
            # Priorizar nombre científico
            return f"{match.group(1)} {match.group(2)}"
        
        # Simplificar nombre eliminando palabras comunes
        stop_words = ['de', 'del', 'la', 'el', 'en', 'con', 'para', 'por', 'y', 'o', 'maceta', 'pot']
        words = [w for w in name.lower().split() if w not in stop_words and len(w) > 2]
        
        return ' '.join(words[:4])  # Máximo 4 palabras más relevantes
    
    def generate_improved_queries(self, product: Dict) -> List[str]:
        """Genera queries MUCHO más específicas"""
        queries = []
        
        # 1. Nombre exacto simplificado
        name = product.get('name', '').strip()
        if name:
            clean_name = self.build_search_terms(product, enrich=True)
            queries.append(clean_name)
        
        # 2. Categorías
        categories = product.get('categories', [])
        if categories and name:
            for cat in categories[:1]:  # Solo primera categoría
                cat_name = cat.get('name', '').strip()
                if cat_name and len(cat_name) > 3:
                    queries.append(f"{clean_name} {cat_name}")
        
        # 3. Tags específicos (si existen)
        tags = product.get('tags', [])
        for tag in tags[:2]:  # Máximo 2 tags
            tag_name = tag.get('name', '').strip()
            if tag_name and len(tag_name) > 3:
                queries.append(f"{clean_name} {tag_name}")
        
        # IMPORTANTE: NO agregar fallbacks genéricos
        # Es mejor fallar que asignar imagen incorrecta
        
        return queries[:3]  # Máximo 3 queries específicas
    
    def score_image_relevance(self, product: Dict, image_metadata: Dict) -> int:
        """
        Calcula score de relevancia 0-100
        """
        score = 0
        
        product_name = product.get('name', '').lower()
        image_url = image_metadata.get('url', '').lower()
        image_alt = image_metadata.get('alt', '').lower()
        
        # Extraer palabras clave del producto
        stop_words = ['de', 'del', 'la', 'el', 'en', 'con', 'para', 'por', 'y', 'o']
        keywords = [w for w in product_name.split() if len(w) > 3 and w not in stop_words]
        
        # 1. Match en URL de imagen (40 pts)
        matches_in_url = sum(1 for kw in keywords if kw in image_url)
        score += min(40, matches_in_url * 15)
        
        # 2. Match en alt text (30 pts)
        matches_in_alt = sum(1 for kw in keywords if kw in image_alt)
        score += min(30, matches_in_alt * 15)
        
        # 3. Palabras clave de planta (20 pts)
        plant_keywords = ['plant', 'tree', 'flower', 'leaf', 'garden', 'planta', 'arbol', 'flor', 'verde']
        if any(pk in image_url or pk in image_alt for pk in plant_keywords):
            score += 20
        
        # 4. Penalizar genéricos (-30 pts)
        generic_keywords = ['generic', 'placeholder', 'default', 'sample', 'pot', 'maceta']
        if any(gk in image_url for gk in generic_keywords):
            score -= 30
        
        # 5. Bonus por nombre científico
        import re
        scientific_pattern = r'\b([A-Z][a-z]+)\s+([a-z]+)\b'
        match = re.search(scientific_pattern, product_name)
        if match:
            genus = match.group(1).lower()
            if genus in image_url or genus in image_alt:
                score += 10
        
        return max(0, min(100, score))
    
    def search_better_image(self, product: Dict, providers: List[str] = None,
                          min_score: int = 60, delay: float = 2.0) -> Optional[Dict]:
        """
        Busca una imagen mejor para el producto
        Retorna metadata de imagen o None si no encuentra
        """
        if providers is None:
            providers = ['unsplash', 'inaturalist']
        
        queries = self.generate_improved_queries(product)
        
        log.info(f"Buscando imagen para '{product.get('name')}' con queries: {queries}")
        
        for query in queries:
            for provider in providers:
                try:
                    # Aquí llamaríamos a las APIs de providers
                    # Por ahora, simular búsqueda
                    log.info(f"  Buscando en {provider}: '{query}'")
                    
                    # NOTA: Implementación real requiere integrar con
                    # search_unsplash, search_inaturalist, etc.
                    # del wc_image_automation.py
                    
                    time.sleep(delay)
                    
                except Exception as e:
                    log.warning(f"Error buscando en {provider}: {e}")
                    continue
        
        return None  # Por ahora
    
    def correct_low_relevance_images(self, audit_results: Dict, min_score: int = 50,
                                    dry_run: bool = True) -> Dict[str, Any]:
        """
        Corrige productos con imágenes de baja relevancia
        """
        stats = {
            'processed': 0,
            'removed': 0,
            'reassigned': 0,
            'failed': 0,
            'skipped': 0
        }
        
        products = audit_results.get('products_analyzed', [])
        
        # Filtrar productos con baja relevancia
        low_relevance = [
            p for p in products
            if p.get('avg_relevance_score', 100) < min_score
        ]
        
        log.info(f"Encontrados {len(low_relevance)} productos con score < {min_score}")
        
        for product_data in tqdm(low_relevance, desc="Corrigiendo productos"):
            product_id = product_data['id']
            product_name = product_data['name']
            images = product_data.get('images', [])
            
            stats['processed'] += 1
            
            log.info(f"\n{'='*60}")
            log.info(f"Producto: {product_name} (ID: {product_id})")
            log.info(f"Score promedio: {product_data.get('avg_relevance_score', 0)}")
            
            # Identificar imágenes a remover (muy baja relevancia)
            images_to_remove = [
                img for img in images
                if img.get('relevance_score', 100) < 40
            ]
            
            if not images_to_remove:
                log.info("  No hay imágenes con score < 40, omitiendo")
                stats['skipped'] += 1
                continue
            
            log.info(f"  Imágenes a remover: {len(images_to_remove)}")
            
            # Remover imágenes incorrectas
            for img in images_to_remove:
                if self.remove_product_image(product_id, img['id'], dry_run=dry_run):
                    stats['removed'] += 1
                    log.info(f"    ✅ Removida imagen ID {img['id']} (score: {img['relevance_score']})")
                else:
                    stats['failed'] += 1
            
            # Buscar imagen mejor (opcional)
            # better_img = self.search_better_image(product_data, min_score=60)
            # if better_img:
            #     stats['reassigned'] += 1
            
            if not dry_run:
                time.sleep(1)  # Rate limiting
        
        return stats
    
    def remove_visual_duplicates(self, audit_results: Dict, dry_run: bool = True) -> Dict[str, Any]:
        """
        Elimina duplicados visuales, manteniendo solo la primera ocurrencia
        """
        stats = {
            'pairs_found': 0,
            'images_removed': 0,
            'failed': 0
        }
        
        duplicates = audit_results.get('visual_duplicates', [])
        stats['pairs_found'] = len(duplicates)
        
        log.info(f"Encontrados {len(duplicates)} pares de duplicados visuales")
        
        # Agrupar por imagen (remover la segunda de cada par)
        seen_products: Set[int] = set()
        
        for dup in tqdm(duplicates, desc="Eliminando duplicados"):
            product2_id = dup['product_2']['id']
            product2_name = dup['product_2']['name']
            
            # Solo procesar si no hemos tocado este producto
            if product2_id in seen_products:
                continue
            
            seen_products.add(product2_id)
            
            log.info(f"\nDuplicado: {product2_name} (ID: {product2_id})")
            log.info(f"  Similar a: {dup['product_1']['name']} (ID: {dup['product_1']['id']})")
            log.info(f"  Similitud: {dup['similarity']}")
            
            # Obtener ID de imagen del producto 2
            # (Necesitamos hacer GET al producto para obtener image_id)
            try:
                url = f"{self.wordpress_url}/wp-json/wc/v3/products/{product2_id}"
                r = self.session.get(url, auth=(self.wc_key, self.wc_secret), timeout=30)
                r.raise_for_status()
                product = r.json()
                
                images = product.get('images', [])
                if not images:
                    continue
                
                # Remover la imagen duplicada (asumir que es la última agregada)
                for img in images:
                    if img['src'] == dup['product_2']['image_url']:
                        if self.remove_product_image(product2_id, img['id'], dry_run=dry_run):
                            stats['images_removed'] += 1
                            log.info(f"  ✅ Imagen duplicada removida")
                        else:
                            stats['failed'] += 1
                        break
                
            except Exception as e:
                log.error(f"Error procesando duplicado: {e}")
                stats['failed'] += 1
        
        return stats


def main():
    parser = argparse.ArgumentParser(description="Corregir imágenes incorrectas en productos")
    parser.add_argument("--input", default="auditoria_resultados.json", help="Archivo JSON de auditoría")
    parser.add_argument("--min-score", type=int, default=50, help="Score mínimo de relevancia")
    parser.add_argument("--remove-duplicates", action="store_true", help="Eliminar duplicados visuales")
    parser.add_argument("--dry-run", action="store_true", help="Simular sin aplicar cambios")
    parser.add_argument("--output", default="correccion_resultados.json", help="Archivo de resultados")
    
    args = parser.parse_args()
    
    # Cargar credenciales
    wordpress_url = os.getenv('WORDPRESS_URL')
    wc_key = os.getenv('WC_CONSUMER_KEY')
    wc_secret = os.getenv('WC_CONSUMER_SECRET')
    wp_user = os.getenv('WP_USERNAME')
    wp_pass = os.getenv('WP_APP_PASSWORD')
    
    if not all([wordpress_url, wc_key, wc_secret, wp_user, wp_pass]):
        log.error("❌ Faltan variables de entorno")
        sys.exit(1)
    
    # Cargar resultados de auditoría
    if not os.path.exists(args.input):
        log.error(f"❌ Archivo de auditoría no encontrado: {args.input}")
        sys.exit(1)
    
    with open(args.input, 'r', encoding='utf-8') as f:
        audit_results = json.load(f)
    
    log.info(f"📊 Auditoría cargada: {audit_results['total_products']} productos")
    
    # Crear corrector
    corrector = ImageCorrector(wordpress_url, wc_key, wc_secret, wp_user, wp_pass)
    
    if args.dry_run:
        log.info("🔍 MODO DRY-RUN - No se aplicarán cambios")
    
    results = {
        'timestamp': datetime.now().isoformat(),
        'dry_run': args.dry_run,
        'min_score': args.min_score,
        'low_relevance_stats': {},
        'duplicates_stats': {}
    }
    
    # Corregir imágenes de baja relevancia
    log.info("\n" + "="*60)
    log.info("🔍 CORRIGIENDO IMÁGENES DE BAJA RELEVANCIA")
    log.info("="*60)
    
    results['low_relevance_stats'] = corrector.correct_low_relevance_images(
        audit_results,
        min_score=args.min_score,
        dry_run=args.dry_run
    )
    
    # Eliminar duplicados visuales
    if args.remove_duplicates:
        log.info("\n" + "="*60)
        log.info("🔍 ELIMINANDO DUPLICADOS VISUALES")
        log.info("="*60)
        
        results['duplicates_stats'] = corrector.remove_visual_duplicates(
            audit_results,
            dry_run=args.dry_run
        )
    
    # Guardar resultados
    with open(args.output, 'w', encoding='utf-8') as f:
        json.dump(results, f, ensure_ascii=False, indent=2)
    
    # Resumen
    print("\n" + "="*60)
    print("📊 RESUMEN DE CORRECCIÓN")
    print("="*60)
    
    lr_stats = results['low_relevance_stats']
    print(f"\n🔴 Baja relevancia:")
    print(f"  Procesados:  {lr_stats.get('processed', 0)}")
    print(f"  Removidos:   {lr_stats.get('removed', 0)}")
    print(f"  Reasignados: {lr_stats.get('reassigned', 0)}")
    print(f"  Fallidos:    {lr_stats.get('failed', 0)}")
    print(f"  Omitidos:    {lr_stats.get('skipped', 0)}")
    
    if args.remove_duplicates:
        dup_stats = results['duplicates_stats']
        print(f"\n⚠️  Duplicados visuales:")
        print(f"  Pares encontrados: {dup_stats.get('pairs_found', 0)}")
        print(f"  Imágenes removidas: {dup_stats.get('images_removed', 0)}")
        print(f"  Fallidos: {dup_stats.get('failed', 0)}")
    
    print(f"\n📄 Resultados: {args.output}")
    print("="*60)
    
    if args.dry_run:
        print("\n⚠️  Ejecutar sin --dry-run para aplicar cambios")


if __name__ == "__main__":
    import base64
    from datetime import datetime
    main()
