#!/usr/bin/env python3
"""
ASIGNACIÓN DE IMÁGENES CON VALIDACIÓN ESTRICTA
=============================================

Asigna imágenes a productos con validación METICULOSA:
1. Búsqueda específica por nombre científico/categoría
2. Validación de pertinencia (score mínimo 60)
3. Verificación manual cada 5 productos
4. Solo imágenes relacionadas con vivero/plantas

Uso:
    python3 asignar_imagenes_validadas.py --max-productos 20 --dry-run
    python3 asignar_imagenes_validadas.py --max-productos 50
"""

import argparse
import json
import logging
import os
import sys
import time
from typing import Any, Dict, List, Optional

import requests
from dotenv import load_dotenv
from tqdm import tqdm

# Importar validador
from validar_pertinencia_imagen import validar_imagen_producto, es_imagen_pertinente

load_dotenv()

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s %(levelname)s: %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S"
)
log = logging.getLogger(__name__)


class AsignadorImagenesValidadas:
    """Asigna imágenes con validación estricta de pertinencia"""
    
    def __init__(self, wordpress_url: str, wc_key: str, wc_secret: str,
                 unsplash_key: Optional[str] = None):
        self.wordpress_url = wordpress_url.rstrip('/')
        self.wc_key = wc_key
        self.wc_secret = wc_secret
        self.unsplash_key = unsplash_key
        self.session = requests.Session()
        
    def buscar_imagen_unsplash(self, query: str, orientacion: str = 'portrait') -> Optional[Dict]:
        """Busca imagen en Unsplash con query específica"""
        if not self.unsplash_key:
            return None
        
        url = "https://api.unsplash.com/search/photos"
        headers = {"Authorization": f"Client-ID {self.unsplash_key}"}
        params = {
            "query": query,
            "per_page": 10,
            "orientation": orientacion,
            "content_filter": "high"
        }
        
        try:
            r = self.session.get(url, headers=headers, params=params, timeout=15)
            r.raise_for_status()
            
            results = r.json().get('results', [])
            if not results:
                return None
            
            # Tomar primera imagen
            img = results[0]
            return {
                'url': img['urls']['regular'],
                'url_download': img['links']['download_location'],
                'title': img.get('description') or img.get('alt_description', ''),
                'description': img.get('description', ''),
                'alt_description': img.get('alt_description', ''),
                'tags': [t['title'] for t in img.get('tags', [])],
                'author': img['user']['name'],
                'author_url': img['user']['links']['html']
            }
            
        except Exception as e:
            log.warning(f"Error buscando en Unsplash '{query}': {e}")
            return None
    
    def generar_queries_especificas(self, producto: Dict) -> List[str]:
        """Genera queries MUY específicas para vivero de plantas"""
        queries = []
        nombre = producto.get('name', '').strip()
        
        # 1. Nombre científico (PRIORIDAD MÁXIMA)
        import re
        pattern = r'\b([A-Z][a-z]+)\s+(?:x\s+)?([a-z]+)\b'
        match = re.search(pattern, nombre)
        
        if match:
            genero = match.group(1)
            especie = match.group(2)
            
            # Queries con nombre científico
            queries.append(f"{genero} {especie} plant")
            queries.append(f"{genero} plant potted")
            return queries[:2]  # Solo científicas si existe
        
        # 2. Extraer nombre ESPECÍFICO de planta (primera palabra significativa)
        stop_words = ['planta', 'plant', 'de', 'del', 'la', 'el', 'litros', 'litro', 'cm', 'ml']
        palabras = [w for w in nombre.split() if len(w) > 3 and w.lower() not in stop_words and not w.isdigit()]
        
        nombre_especifico = palabras[0] if palabras else None
        
        if nombre_especifico:
            # Queries con nombre específico
            queries.append(f"{nombre_especifico} plant pot")
            queries.append(f"{nombre_especifico} potted plant")
            
            # Si es tipo conocido, agregar contexto
            tipos_planta = {
                'arbol': 'tree', 'arboles': 'tree',
                'arbusto': 'shrub', 'arbustos': 'shrub',
                'trepadora': 'climbing vine', 'palma': 'palm',
                'cactus': 'cactus', 'suculenta': 'succulent',
                'helecho': 'fern', 'flor': 'flower'
            }
            
            nombre_lower = nombre.lower()
            for tipo, tipo_en in tipos_planta.items():
                if tipo in nombre_lower:
                    queries.append(f"{nombre_especifico} {tipo_en} plant")
                    break
        
        # 3. Si aún no hay queries, usar tipo de planta genérico
        if not queries:
            nombre_lower = nombre.lower()
            tipos_planta = {
                'arbol': 'tree plant nursery',
                'arbusto': 'shrub plant pot',
                'trepadora': 'climbing plant vine',
                'palma': 'palm plant potted',
                'cactus': 'cactus plant pot',
                'suculenta': 'succulent plant pot',
                'helecho': 'fern plant indoor',
                'flor': 'flowering plant pot'
            }
            
            for tipo, query in tipos_planta.items():
                if tipo in nombre_lower:
                    queries.append(query)
                    break
        
        # 4. Último recurso: categoría específica
        if not queries:
            for cat in producto.get('categories', [])[:1]:
                cat_name = cat.get('name', '').strip()
                # Solo si categoría es específica
                if cat_name and 'interior' in cat_name.lower():
                    queries.append('indoor plant pot')
                elif cat_name and 'exterior' in cat_name.lower():
                    queries.append('outdoor plant pot')
        
        return queries[:2] if queries else ['potted plant nursery']
    
    def asignar_imagen_producto(self, producto: Dict, dry_run: bool = True,
                                threshold_score: int = 60) -> Optional[Dict]:
        """Asigna imagen a producto con validación estricta"""
        producto_id = producto['id']
        producto_nombre = producto['name']
        
        log.info(f"\n{'='*60}")
        log.info(f"Producto: {producto_nombre} (ID: {producto_id})")
        
        # Generar queries específicas
        queries = self.generar_queries_especificas(producto)
        log.info(f"Queries generadas: {queries}")
        
        mejor_imagen = None
        mejor_score = 0
        
        # Buscar en cada query
        for query in queries:
            log.info(f"  Buscando: '{query}'")
            
            imagen = self.buscar_imagen_unsplash(query)
            if not imagen:
                log.info(f"    No se encontraron resultados")
                continue
            
            # VALIDAR PERTINENCIA
            score, razones = validar_imagen_producto(
                producto,
                imagen['url'],
                imagen
            )
            
            log.info(f"    Imagen encontrada: {imagen['title'][:50]}")
            log.info(f"    Score pertinencia: {score}/100")
            
            for razon in razones[:5]:  # Top 5 razones
                log.info(f"      {razon}")
            
            if score > mejor_score:
                mejor_score = score
                mejor_imagen = imagen
            
            # Si encontramos una excelente, usar esa
            if score >= 80:
                break
            
            time.sleep(1)  # Rate limiting
        
        # Verificar threshold
        if mejor_score < threshold_score:
            log.warning(f"  ❌ RECHAZADA: Score {mejor_score} < {threshold_score}")
            log.warning(f"  Mejor imagen encontrada no cumple threshold mínimo")
            return None
        
        # Imagen aceptada
        log.info(f"  ✅ ACEPTADA: Score {mejor_score}/100")
        log.info(f"  URL: {mejor_imagen['url'][:80]}")
        
        if dry_run:
            log.info(f"  [DRY-RUN] No se asigna imagen")
            return {
                'producto_id': producto_id,
                'imagen_url': mejor_imagen['url'],
                'score': mejor_score,
                'dry_run': True
            }
        
        # Asignar imagen (implementar upload a WordPress)
        # Por ahora solo retornar info
        return {
            'producto_id': producto_id,
            'imagen_url': mejor_imagen['url'],
            'score': mejor_score,
            'asignada': False  # Cambiar cuando se implemente upload
        }
    
    def procesar_productos(self, max_productos: int = 20, dry_run: bool = True,
                          threshold: int = 60) -> Dict[str, Any]:
        """Procesa productos asignando imágenes validadas"""
        
        # Obtener productos sin imagen
        url = f"{self.wordpress_url}/wp-json/wc/v3/products"
        params = {"per_page": 100, "status": "publish"}
        
        try:
            r = self.session.get(
                url,
                params=params,
                auth=(self.wc_key, self.wc_secret),
                timeout=30
            )
            r.raise_for_status()
            productos = r.json()
        except Exception as e:
            log.error(f"Error obteniendo productos: {e}")
            return {'error': str(e)}
        
        # Filtrar productos sin imagen
        sin_imagen = [p for p in productos if not p.get('images')]
        log.info(f"Productos sin imagen: {len(sin_imagen)}")
        
        # Limitar
        sin_imagen = sin_imagen[:max_productos]
        log.info(f"Procesando: {len(sin_imagen)} productos")
        
        stats = {
            'total': len(sin_imagen),
            'procesados': 0,
            'aceptadas': 0,
            'rechazadas': 0,
            'score_promedio': 0,
            'resultados': []
        }
        
        scores = []
        
        for i, producto in enumerate(sin_imagen, 1):
            log.info(f"\n{'#'*60}")
            log.info(f"PRODUCTO {i}/{len(sin_imagen)}")
            
            resultado = self.asignar_imagen_producto(
                producto,
                dry_run=dry_run,
                threshold_score=threshold
            )
            
            stats['procesados'] += 1
            
            if resultado:
                stats['aceptadas'] += 1
                scores.append(resultado['score'])
                stats['resultados'].append(resultado)
            else:
                stats['rechazadas'] += 1
            
            # Pausa cada 5 productos para verificación manual
            if i % 5 == 0 and i < len(sin_imagen):
                log.info(f"\n{'='*60}")
                log.info(f"PAUSA VERIFICACIÓN - {i} productos procesados")
                log.info(f"Aceptadas: {stats['aceptadas']}, Rechazadas: {stats['rechazadas']}")
                log.info(f"{'='*60}")
                input("Presiona ENTER para continuar o Ctrl+C para detener...")
        
        # Calcular promedio
        if scores:
            stats['score_promedio'] = sum(scores) / len(scores)
        
        return stats


def main():
    parser = argparse.ArgumentParser(
        description="Asignar imágenes con validación estricta de pertinencia"
    )
    parser.add_argument("--max-productos", type=int, default=20,
                       help="Máximo de productos a procesar")
    parser.add_argument("--threshold", type=int, default=60,
                       help="Score mínimo de pertinencia (0-100)")
    parser.add_argument("--dry-run", action="store_true",
                       help="Simular sin asignar imágenes")
    parser.add_argument("--output", default="imagenes_validadas_resultados.json",
                       help="Archivo de resultados")
    
    args = parser.parse_args()
    
    # Cargar credenciales
    wordpress_url = os.getenv('WORDPRESS_URL')
    wc_key = os.getenv('WC_CONSUMER_KEY')
    wc_secret = os.getenv('WC_CONSUMER_SECRET')
    unsplash_key = os.getenv('UNSPLASH_API_KEY')
    
    if not all([wordpress_url, wc_key, wc_secret]):
        log.error("❌ Faltan variables de entorno")
        sys.exit(1)
    
    if not unsplash_key:
        log.warning("⚠️ UNSPLASH_API_KEY no configurada")
    
    # Crear asignador
    asignador = AsignadorImagenesValidadas(
        wordpress_url, wc_key, wc_secret, unsplash_key
    )
    
    # Procesar
    log.info(f"\n{'='*60}")
    log.info(f"ASIGNACIÓN DE IMÁGENES CON VALIDACIÓN ESTRICTA")
    log.info(f"{'='*60}")
    log.info(f"Máximo productos: {args.max_productos}")
    log.info(f"Threshold score: {args.threshold}/100")
    log.info(f"Modo: {'DRY-RUN' if args.dry_run else 'PRODUCCIÓN'}")
    log.info(f"{'='*60}\n")
    
    stats = asignador.procesar_productos(
        max_productos=args.max_productos,
        dry_run=args.dry_run,
        threshold=args.threshold
    )
    
    # Guardar resultados
    with open(args.output, 'w', encoding='utf-8') as f:
        json.dump(stats, f, ensure_ascii=False, indent=2)
    
    # Resumen
    print(f"\n{'='*60}")
    print(f"RESUMEN")
    print(f"{'='*60}")
    print(f"Total procesados: {stats['procesados']}")
    print(f"Imágenes aceptadas: {stats['aceptadas']}")
    print(f"Imágenes rechazadas: {stats['rechazadas']}")
    print(f"Score promedio: {stats['score_promedio']:.1f}/100")
    print(f"\nResultados: {args.output}")
    print(f"{'='*60}")
    
    if args.dry_run:
        print("\n⚠️ Ejecutar sin --dry-run para asignar imágenes")


if __name__ == "__main__":
    main()
