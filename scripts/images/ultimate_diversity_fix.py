#!/usr/bin/env python3
"""
Ultimate Diversity Fix - Eliminación definitiva de duplicados por contenido
Usa paginación profunda y variación de queries para máxima diversidad
"""

import os
import sys
import time
import hashlib
import random
import requests
from collections import defaultdict
from typing import Dict, List, Set, Tuple, Optional
from tqdm import tqdm

# WooCommerce API
try:
    from woocommerce import API as WooCommerceAPI
except ImportError:
    print("Installing woocommerce...")
    os.system("pip3 install woocommerce")
    from woocommerce import API as WooCommerceAPI

# WordPress REST API
WP_URL = "https://viveroloscocos.com.ar"
WP_USER = os.getenv("WP_USERNAME", "admin")
WP_PASS = os.getenv("WP_APP_PASSWORD", "oKCY MdFE PrHc CWZ4 Wzdh 67kw")

# WooCommerce API
WC_KEY = os.getenv("WC_CONSUMER_KEY", "ck_8a33eaa5c57e3cbd90f88a7ce58ea19ce76e9e1b")
WC_SECRET = os.getenv("WC_CONSUMER_SECRET", "cs_78cb61b3e2e7bb13f1ae653f03fc2ad3d5dd1094")

# Image APIs
UNSPLASH_KEY = "YjiiXP_kb4z7yhpBMrK3OWeWx1jf_VQrzl37VfFssLY"
PEXELS_KEY = "Ru0qn9ob5D5XqNyzacPTNoZiaTzfbmjRrHduXFisV4G97BADZ4EDBNiw"

# Query variations para agregar diversidad
QUERY_SUFFIXES = [
    "plant", "nature", "botanical", "green", "garden",
    "flora", "foliage", "leaf", "outdoor", "natural",
    "vivero", "nursery", "greenhouse", "potted", "growing"
]

def get_file_md5(filepath: str) -> str:
    """Calculate MD5 hash of file"""
    with open(filepath, 'rb') as f:
        return hashlib.md5(f.read()).hexdigest()

def find_duplicate_hashes() -> Dict[str, List[Tuple[int, str]]]:
    """Find products with duplicate image content (by hash)"""
    print("🔍 Analizando duplicados por contenido...")
    
    # Get all products with images
    wcapi = WooCommerceAPI(
        url=WP_URL,
        consumer_key=WC_KEY,
        consumer_secret=WC_SECRET,
        version="wc/v3",
        timeout=30
    )
    
    page = 1
    all_products = []
    
    while True:
        products = wcapi.get("products", params={"per_page": 100, "page": page}).json()
        if not products:
            break
        all_products.extend(products)
        page += 1
        time.sleep(0.5)
    
    print(f"📦 Total productos: {len(all_products)}")
    
    # Download and hash all current images
    hash_to_products = defaultdict(list)
    
    for product in tqdm(all_products, desc="Hasheando imágenes"):
        pid = product['id']
        pname = product['name']
        
        if not product.get('images'):
            continue
            
        image_url = product['images'][0]['src']
        
        try:
            # Download image
            response = requests.get(image_url, timeout=10)
            if response.status_code == 200:
                import tempfile
                temp_file = tempfile.NamedTemporaryFile(delete=False, suffix='.jpg')
                temp_file.write(response.content)
                temp_file.close()
                
                # Calculate hash
                img_hash = get_file_md5(temp_file.name)
                hash_to_products[img_hash].append((pid, pname))
                
                os.unlink(temp_file.name)
        except Exception as e:
            continue
    
    # Find duplicates
    duplicates = {h: prods for h, prods in hash_to_products.items() if len(prods) > 1}
    
    print(f"\n📊 Hashes únicos: {len(hash_to_products)}")
    print(f"🔴 Hashes duplicados: {len(duplicates)}")
    
    return duplicates

def search_diverse_image(query: str, provider: str, page: int = 1) -> Optional[str]:
    """Search for image with specific page for diversity"""
    try:
        if provider == 'unsplash':
            url = "https://api.unsplash.com/search/photos"
            params = {
                'query': query,
                'page': page,
                'per_page': 30,
                'client_id': UNSPLASH_KEY
            }
            response = requests.get(url, params=params, timeout=10)
            data = response.json()
            
            if data.get('results'):
                # Pick random from results
                img = random.choice(data['results'])
                return img['urls']['regular']
                
        elif provider == 'pexels':
            url = "https://api.pexels.com/v1/search"
            headers = {'Authorization': PEXELS_KEY}
            params = {
                'query': query,
                'page': page,
                'per_page': 30
            }
            response = requests.get(url, headers=headers, params=params, timeout=10)
            data = response.json()
            
            if data.get('photos'):
                img = random.choice(data['photos'])
                return img['src']['large']
    except Exception as e:
        pass
    
    return None

def download_and_upload_unique(product_id: int, product_name: str, used_hashes: Set[str], max_attempts: int = 30) -> Optional[int]:
    """Download and upload a UNIQUE image (verified by hash)"""
    
    providers = ['unsplash', 'pexels']
    
    # Generate diverse search queries
    base_words = product_name.lower().split()[:3]
    queries = []
    
    # Original query
    queries.append(' '.join(base_words))
    
    # With random suffixes
    for _ in range(3):
        suffix = random.choice(QUERY_SUFFIXES)
        queries.append(f"{base_words[0]} {suffix}")
    
    # Generic fallbacks
    queries.extend(['garden plant', 'potted plant', 'green plant', 'nursery plant'])
    
    for attempt in range(max_attempts):
        try:
            # Rotate provider and query
            provider = providers[attempt % len(providers)]
            query = queries[attempt % len(queries)]
            
            # Use deep pagination (pages 1-30)
            page = random.randint(1, 30)
            
            print(f"  [{attempt+1}/{max_attempts}] {provider} | {query} | p{page}", end=" ")
            
            image_url = search_diverse_image(query, provider, page)
            
            if not image_url:
                print("❌ sin resultados")
                continue
            
            # Download
            response = requests.get(image_url, timeout=15)
            if response.status_code != 200:
                print("❌ download falló")
                continue
            
            # Check hash
            import tempfile
            temp_download = tempfile.NamedTemporaryFile(delete=False, suffix='.jpg')
            temp_download.write(response.content)
            temp_download.close()
            
            new_hash = get_file_md5(temp_download.name)
            
            if new_hash in used_hashes:
                os.unlink(temp_download.name)
                print("⚠️  duplicado")
                continue
            
            # ✅ UNIQUE image found! Upload to WordPress
            print("✅ único → subiendo...", end=" ")
            
            wp_url = f"{WP_URL}/wp-json/wp/v2/media"
            
            with open(temp_download.name, 'rb') as f:
                files = {
                    'file': (f'wcimg_{int(time.time()*1000)}_{product_id}.webp', f, 'image/webp')
                }
                
                response = requests.post(
                    wp_url,
                    files=files,
                    auth=(WP_USER, WP_PASS),
                    timeout=30
                )
            
            os.unlink(temp_download.name)
            
            if response.status_code in [200, 201]:
                media_data = response.json()
                media_id = media_data.get('id')
                used_hashes.add(new_hash)
                print(f"ÉXITO (media {media_id})")
                return media_id
            else:
                print(f"❌ upload error {response.status_code}")
                
        except Exception as e:
            print(f"❌ error: {e}")
            continue
        
        time.sleep(1)  # Rate limiting
    
    return None

def main():
    print("="*60)
    print(" ULTIMATE DIVERSITY FIX - Eliminación Definitiva")
    print("="*60)
    print()
    
    # Find duplicates
    duplicates = find_duplicate_hashes()
    
    if not duplicates:
        print("\n✅ No se encontraron duplicados!")
        return
    
    # Count total products to fix
    total_to_fix = sum(len(prods) - 1 for prods in duplicates.values())
    print(f"\n🎯 Productos a corregir: {total_to_fix}")
    print()
    
    wcapi = WooCommerceAPI(
        url=WP_URL,
        consumer_key=WC_KEY,
        consumer_secret=WC_SECRET,
        version="wc/v3",
        timeout=30
    )
    
    used_hashes = set(duplicates.keys())  # Hashes únicos actuales
    stats = {'success': 0, 'failed': 0, 'skipped': 0}
    
    for img_hash, products in tqdm(duplicates.items(), desc="Grupos duplicados"):
        # Keep first product, replace the rest
        products_to_fix = products[1:]
        
        print(f"\n🔄 Hash {img_hash[:16]}... → {len(products)} productos")
        print(f"   Manteniendo: #{products[0][0]} {products[0][1][:40]}")
        
        for pid, pname in products_to_fix:
            print(f"\n   🔧 #{pid}: {pname[:50]}")
            
            # Try to get unique image
            media_id = download_and_upload_unique(pid, pname, used_hashes, max_attempts=30)
            
            if media_id:
                # Assign to product
                try:
                    wcapi.put(f"products/{pid}", {
                        "images": [{"id": media_id}]
                    })
                    stats['success'] += 1
                except Exception as e:
                    print(f"      ❌ Error asignando: {e}")
                    stats['failed'] += 1
            else:
                print(f"      ❌ No se encontró imagen única después de 30 intentos")
                stats['failed'] += 1
            
            time.sleep(2)
    
    print("\n" + "="*60)
    print(" RESULTADOS FINALES")
    print("="*60)
    print(f"✅ Éxitos:   {stats['success']}")
    print(f"❌ Fallos:   {stats['failed']}")
    print(f"⏭️  Saltados: {stats['skipped']}")
    print()
    print("✅ Proceso completado!")

if __name__ == "__main__":
    main()
