#!/usr/bin/env python3
"""
VERIFICACIÓN VISUAL DE IMÁGENES DE PRODUCTOS
===========================================

Script para auditar y detectar:
1. Imágenes no pertinentes (no relacionadas con título/descripción)
2. Duplicación visual (misma imagen en múltiples productos)
3. Imágenes genéricas vs específicas

Genera reporte HTML con miniaturas para revisión manual.

Uso:
    python3 verificar_imagenes_productos.py --output auditoria_imagenes.html
    python3 verificar_imagenes_productos.py --check-duplicates --threshold 5
"""

import argparse
import hashlib
import json
import logging
import os
import sys
from collections import defaultdict
from datetime import datetime
from typing import Any, Dict, List, Optional, Tuple
from urllib.parse import urlparse

import requests
from dotenv import load_dotenv
from PIL import Image
from tqdm import tqdm

try:
    import imagehash
    HAS_IMAGEHASH = True
except ImportError:
    print("⚠️  imagehash no instalado. Instalar con: pip install imagehash")
    HAS_IMAGEHASH = False

# Cargar variables de entorno
load_dotenv()

# Configuración
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s %(levelname)s: %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S"
)
log = logging.getLogger(__name__)


class ImageVerifier:
    """Verificador de imágenes de productos"""
    
    def __init__(self, wordpress_url: str, wc_key: str, wc_secret: str):
        self.wordpress_url = wordpress_url.rstrip('/')
        self.wc_key = wc_key
        self.wc_secret = wc_secret
        self.session = requests.Session()
        
    def get_all_products_with_images(self) -> List[Dict[str, Any]]:
        """Obtiene todos los productos que tienen imágenes"""
        products = []
        page = 1
        
        log.info("Obteniendo productos con imágenes...")
        
        while True:
            url = f"{self.wordpress_url}/wp-json/wc/v3/products"
            params = {
                "per_page": 100,
                "page": page,
                "status": "publish"
            }
            
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
                
                # Filtrar solo productos con imágenes
                for product in data:
                    images = product.get('images', [])
                    if images:
                        products.append(product)
                
                log.info(f"  Página {page}: {len(data)} productos, {len([p for p in data if p.get('images')])} con imágenes")
                page += 1
                
            except Exception as e:
                log.error(f"Error obteniendo productos página {page}: {e}")
                break
        
        log.info(f"✅ Total: {len(products)} productos con imágenes")
        return products
    
    def download_image(self, url: str, temp_dir: str = "/tmp") -> Optional[str]:
        """Descarga imagen temporalmente"""
        try:
            r = requests.get(url, timeout=10)
            r.raise_for_status()
            
            # Generar nombre de archivo temporal
            filename = hashlib.md5(url.encode()).hexdigest() + ".jpg"
            filepath = os.path.join(temp_dir, filename)
            
            with open(filepath, 'wb') as f:
                f.write(r.content)
            
            return filepath
            
        except Exception as e:
            log.warning(f"No se pudo descargar {url}: {e}")
            return None
    
    def compute_perceptual_hash(self, image_path: str) -> Optional[Dict[str, str]]:
        """Calcula hashes perceptuales de una imagen"""
        if not HAS_IMAGEHASH:
            return None
        
        try:
            img = Image.open(image_path)
            
            # Múltiples algoritmos de hash
            ahash = imagehash.average_hash(img)
            phash = imagehash.phash(img)
            dhash = imagehash.dhash(img)
            
            return {
                'ahash': str(ahash),
                'phash': str(phash),
                'dhash': str(dhash)
            }
            
        except Exception as e:
            log.warning(f"Error calculando hash perceptual: {e}")
            return None
    
    def score_image_relevance(self, product: Dict, image_url: str) -> int:
        """
        Calcula un score de relevancia 0-100
        basado en comparación del título del producto con metadata de imagen
        """
        score = 0
        
        product_name = product.get('name', '').lower()
        product_desc = product.get('short_description', '').lower()
        
        # Extraer palabras clave del nombre del producto
        # Remover palabras comunes
        stop_words = ['de', 'del', 'la', 'el', 'en', 'con', 'para', 'por', 'y', 'o']
        keywords = [w for w in product_name.split() if len(w) > 3 and w not in stop_words]
        
        # Análisis simple basado en URL de imagen
        image_name = urlparse(image_url).path.lower()
        
        # Coincidencias en nombre de archivo
        matches = sum(1 for kw in keywords if kw in image_name)
        
        if matches > 0:
            score += min(50, matches * 15)  # Máximo 50 puntos
        
        # Si tiene palabras clave de planta
        plant_keywords = ['plant', 'tree', 'flower', 'leaf', 'garden', 'planta', 'arbol', 'flor']
        if any(pk in image_name for pk in plant_keywords):
            score += 20
        
        # Penalizar imágenes genéricas
        generic_keywords = ['generic', 'placeholder', 'default', 'sample']
        if any(gk in image_name for gk in generic_keywords):
            score -= 30
        
        return max(0, min(100, score))
    
    def find_visual_duplicates(self, products: List[Dict], threshold: int = 5) -> List[Dict]:
        """
        Encuentra imágenes visualmente duplicadas
        threshold: Hamming distance máximo (0-64), menor = más similar
        """
        if not HAS_IMAGEHASH:
            log.warning("imagehash no disponible, saltando detección de duplicados")
            return []
        
        duplicates = []
        all_hashes = {}  # {image_id: {'hash': ..., 'product': ..., 'url': ...}}
        
        log.info("Buscando duplicados visuales...")
        
        for product in tqdm(products, desc="Analizando imágenes"):
            for img in product.get('images', []):
                img_id = img.get('id')
                img_url = img.get('src')
                
                # Descargar y calcular hash
                temp_path = self.download_image(img_url)
                if not temp_path:
                    continue
                
                phash_data = self.compute_perceptual_hash(temp_path)
                if not phash_data:
                    continue
                
                # Comparar con todas las imágenes anteriores
                current_phash = imagehash.hex_to_hash(phash_data['phash'])
                
                for existing_id, existing_data in all_hashes.items():
                    existing_phash = imagehash.hex_to_hash(existing_data['hash']['phash'])
                    
                    # Calcular diferencia (Hamming distance)
                    diff = current_phash - existing_phash
                    
                    if diff <= threshold:
                        duplicates.append({
                            'product_1': {
                                'id': existing_data['product']['id'],
                                'name': existing_data['product']['name'],
                                'image_url': existing_data['url']
                            },
                            'product_2': {
                                'id': product['id'],
                                'name': product['name'],
                                'image_url': img_url
                            },
                            'similarity': f"{100 - (diff / 64 * 100):.1f}%",
                            'hamming_distance': diff
                        })
                
                # Guardar hash para comparaciones futuras
                all_hashes[img_id] = {
                    'hash': phash_data,
                    'product': product,
                    'url': img_url
                }
                
                # Limpiar archivo temporal
                try:
                    os.remove(temp_path)
                except:
                    pass
        
        log.info(f"✅ Encontrados {len(duplicates)} pares de duplicados visuales")
        return duplicates
    
    def analyze_products(self, check_duplicates: bool = False, threshold: int = 5) -> Dict[str, Any]:
        """Análisis completo de productos con imágenes"""
        products = self.get_all_products_with_images()
        
        results = {
            'timestamp': datetime.now().isoformat(),
            'total_products': len(products),
            'products_analyzed': [],
            'statistics': {
                'with_multiple_images': 0,
                'with_single_image': 0,
                'low_relevance': 0,
                'medium_relevance': 0,
                'high_relevance': 0,
                'total_images': 0
            },
            'visual_duplicates': []
        }
        
        # Analizar cada producto
        log.info("Analizando relevancia de imágenes...")
        
        for product in tqdm(products, desc="Productos"):
            images = product.get('images', [])
            num_images = len(images)
            
            # Estadísticas básicas
            results['statistics']['total_images'] += num_images
            if num_images > 1:
                results['statistics']['with_multiple_images'] += 1
            else:
                results['statistics']['with_single_image'] += 1
            
            # Analizar cada imagen
            image_scores = []
            for img in images:
                score = self.score_image_relevance(product, img['src'])
                image_scores.append({
                    'id': img['id'],
                    'src': img['src'],
                    'alt': img.get('alt', ''),
                    'relevance_score': score
                })
                
                # Estadísticas de relevancia
                if score < 40:
                    results['statistics']['low_relevance'] += 1
                elif score < 70:
                    results['statistics']['medium_relevance'] += 1
                else:
                    results['statistics']['high_relevance'] += 1
            
            # Calcular score promedio
            avg_score = sum(img['relevance_score'] for img in image_scores) / len(image_scores) if image_scores else 0
            
            results['products_analyzed'].append({
                'id': product['id'],
                'name': product['name'],
                'num_images': num_images,
                'images': image_scores,
                'avg_relevance_score': round(avg_score, 1),
                'permalink': product.get('permalink', '')
            })
        
        # Buscar duplicados visuales si se solicita
        if check_duplicates and HAS_IMAGEHASH:
            results['visual_duplicates'] = self.find_visual_duplicates(products, threshold)
        
        return results
    
    def generate_html_report(self, results: Dict, output_path: str):
        """Genera reporte HTML visual"""
        html = f"""
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría de Imágenes - Vivero Los Cocos</title>
    <style>
        body {{
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }}
        .header {{
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }}
        .stats {{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }}
        .stat-card {{
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }}
        .stat-value {{
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
        }}
        .stat-label {{
            color: #64748b;
            font-size: 14px;
            margin-top: 5px;
        }}
        .products-grid {{
            display: grid;
            gap: 20px;
        }}
        .product-card {{
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }}
        .product-card.low-relevance {{
            border-left: 4px solid #ef4444;
        }}
        .product-card.medium-relevance {{
            border-left: 4px solid #f59e0b;
        }}
        .product-card.high-relevance {{
            border-left: 4px solid #10b981;
        }}
        .product-header {{
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }}
        .product-name {{
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
        }}
        .relevance-badge {{
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }}
        .relevance-badge.low {{
            background: #fecaca;
            color: #991b1b;
        }}
        .relevance-badge.medium {{
            background: #fed7aa;
            color: #92400e;
        }}
        .relevance-badge.high {{
            background: #bbf7d0;
            color: #166534;
        }}
        .images-grid {{
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }}
        .image-item {{
            position: relative;
        }}
        .image-item img {{
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #e2e8f0;
        }}
        .image-score {{
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }}
        .duplicates-section {{
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }}
        .duplicate-pair {{
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 20px;
            align-items: center;
            padding: 15px;
            background: #f8fafc;
            border-radius: 6px;
            margin-bottom: 15px;
        }}
        .duplicate-item {{
            text-align: center;
        }}
        .duplicate-item img {{
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #e2e8f0;
        }}
        .duplicate-arrow {{
            font-size: 24px;
            color: #ef4444;
        }}
        .filters {{
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }}
        .filter-buttons {{
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }}
        .filter-btn {{
            padding: 8px 16px;
            border: 2px solid #e2e8f0;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }}
        .filter-btn.active {{
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }}
    </style>
</head>
<body>
    <div class="header">
        <h1>🔍 Auditoría de Imágenes de Productos</h1>
        <p>Vivero Los Cocos - {datetime.now().strftime('%d/%m/%Y %H:%M')}</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-value">{results['total_products']}</div>
            <div class="stat-label">Productos con imágenes</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{results['statistics']['total_images']}</div>
            <div class="stat-label">Total de imágenes</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{results['statistics']['with_multiple_images']}</div>
            <div class="stat-label">Con 2+ imágenes</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{results['statistics']['low_relevance']}</div>
            <div class="stat-label">Baja relevancia (&lt;40)</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{results['statistics']['medium_relevance']}</div>
            <div class="stat-label">Media relevancia (40-70)</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{results['statistics']['high_relevance']}</div>
            <div class="stat-label">Alta relevancia (&gt;70)</div>
        </div>
    </div>

    <div class="filters">
        <h3>Filtros:</h3>
        <div class="filter-buttons">
            <button class="filter-btn active" onclick="filterProducts('all')">Todos</button>
            <button class="filter-btn" onclick="filterProducts('low')">Baja relevancia</button>
            <button class="filter-btn" onclick="filterProducts('medium')">Media relevancia</button>
            <button class="filter-btn" onclick="filterProducts('high')">Alta relevancia</button>
        </div>
    </div>

    <div class="products-grid" id="products-grid">
"""
        
        # Ordenar productos por score (menor primero - más problemáticos)
        products_sorted = sorted(results['products_analyzed'], key=lambda x: x['avg_relevance_score'])
        
        for product in products_sorted:
            avg_score = product['avg_relevance_score']
            
            if avg_score < 40:
                relevance_class = 'low-relevance'
                badge_class = 'low'
                badge_text = f'Baja ({avg_score})'
            elif avg_score < 70:
                relevance_class = 'medium-relevance'
                badge_class = 'medium'
                badge_text = f'Media ({avg_score})'
            else:
                relevance_class = 'high-relevance'
                badge_class = 'high'
                badge_text = f'Alta ({avg_score})'
            
            html += f"""
        <div class="product-card {relevance_class}" data-relevance="{relevance_class}">
            <div class="product-header">
                <div class="product-name">{product['name']}</div>
                <div class="relevance-badge {badge_class}">{badge_text}</div>
            </div>
            <div>ID: {product['id']} | {product['num_images']} imagen(es)</div>
            <div class="images-grid">
"""
            
            for img in product['images']:
                score = img['relevance_score']
                html += f"""
                <div class="image-item">
                    <img src="{img['src']}" alt="{img.get('alt', '')}">
                    <div class="image-score">{score}</div>
                </div>
"""
            
            html += """
            </div>
        </div>
"""
        
        html += """
    </div>
"""
        
        # Sección de duplicados visuales
        if results['visual_duplicates']:
            html += """
    <div class="duplicates-section">
        <h2>⚠️ Duplicados Visuales Encontrados</h2>
        <p>Imágenes visualmente similares asignadas a diferentes productos:</p>
"""
            
            for dup in results['visual_duplicates']:
                html += f"""
        <div class="duplicate-pair">
            <div class="duplicate-item">
                <img src="{dup['product_1']['image_url']}" alt="">
                <div>{dup['product_1']['name']}</div>
                <div style="font-size: 12px; color: #64748b;">ID: {dup['product_1']['id']}</div>
            </div>
            <div class="duplicate-arrow">⚠️<br>{dup['similarity']}</div>
            <div class="duplicate-item">
                <img src="{dup['product_2']['image_url']}" alt="">
                <div>{dup['product_2']['name']}</div>
                <div style="font-size: 12px; color: #64748b;">ID: {dup['product_2']['id']}</div>
            </div>
        </div>
"""
            
            html += """
    </div>
"""
        
        html += """
    <script>
        function filterProducts(filter) {
            const products = document.querySelectorAll('.product-card');
            const buttons = document.querySelectorAll('.filter-btn');
            
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            products.forEach(product => {
                if (filter === 'all') {
                    product.style.display = 'block';
                } else if (filter === 'low' && product.classList.contains('low-relevance')) {
                    product.style.display = 'block';
                } else if (filter === 'medium' && product.classList.contains('medium-relevance')) {
                    product.style.display = 'block';
                } else if (filter === 'high' && product.classList.contains('high-relevance')) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
"""
        
        with open(output_path, 'w', encoding='utf-8') as f:
            f.write(html)
        
        log.info(f"✅ Reporte HTML generado: {output_path}")


def main():
    parser = argparse.ArgumentParser(description="Verificar imágenes de productos WooCommerce")
    parser.add_argument("--output", default="auditoria_imagenes_productos.html", help="Archivo HTML de salida")
    parser.add_argument("--check-duplicates", action="store_true", help="Buscar duplicados visuales (requiere imagehash)")
    parser.add_argument("--threshold", type=int, default=5, help="Threshold de similitud para duplicados (0-64, menor=más similar)")
    parser.add_argument("--json", help="Guardar también resultados en JSON")
    
    args = parser.parse_args()
    
    # Obtener credenciales
    wordpress_url = os.getenv('WORDPRESS_URL')
    wc_key = os.getenv('WC_CONSUMER_KEY')
    wc_secret = os.getenv('WC_CONSUMER_SECRET')
    
    if not all([wordpress_url, wc_key, wc_secret]):
        log.error("❌ Faltan variables de entorno: WORDPRESS_URL, WC_CONSUMER_KEY, WC_CONSUMER_SECRET")
        sys.exit(1)
    
    # Verificar imagehash si se solicita duplicados
    if args.check_duplicates and not HAS_IMAGEHASH:
        log.error("❌ --check-duplicates requiere imagehash. Instalar con: pip install imagehash")
        sys.exit(1)
    
    # Ejecutar verificación
    verifier = ImageVerifier(wordpress_url, wc_key, wc_secret)
    
    log.info("🔍 Iniciando auditoría de imágenes...")
    results = verifier.analyze_products(
        check_duplicates=args.check_duplicates,
        threshold=args.threshold
    )
    
    # Generar reporte HTML
    verifier.generate_html_report(results, args.output)
    
    # Guardar JSON si se solicita
    if args.json:
        with open(args.json, 'w', encoding='utf-8') as f:
            json.dump(results, f, ensure_ascii=False, indent=2)
        log.info(f"✅ Resultados guardados en JSON: {args.json}")
    
    # Resumen en consola
    print("\n" + "="*60)
    print("📊 RESUMEN DE AUDITORÍA")
    print("="*60)
    print(f"Total productos: {results['total_products']}")
    print(f"Total imágenes: {results['statistics']['total_images']}")
    print(f"\nRelevancia:")
    print(f"  🔴 Baja (<40):    {results['statistics']['low_relevance']}")
    print(f"  🟡 Media (40-70): {results['statistics']['medium_relevance']}")
    print(f"  🟢 Alta (>70):    {results['statistics']['high_relevance']}")
    
    if args.check_duplicates:
        print(f"\n⚠️  Duplicados visuales: {len(results['visual_duplicates'])} pares")
    
    print(f"\n📄 Reporte HTML: {args.output}")
    print("="*60)


if __name__ == "__main__":
    main()
