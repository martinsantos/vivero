#!/usr/bin/env python3
"""
Force Diversity - Diversificación agresiva de imágenes duplicadas
Ejecutar DESPUÉS de wc_image_automation.py para corregir duplicados restantes
"""

import os
import sys
import time
import hashlib
import requests
from typing import Dict, List, Set, Optional
from tqdm import tqdm

# WooCommerce API
from woocommerce import API as WooCommerceAPI

# WordPress
import mysql.connector

# Config
WP_URL = os.getenv("WORDPRESS_URL", "https://viveroloscocos.com.ar")
WC_KEY = os.getenv("WC_CONSUMER_KEY", "")
WC_SECRET = os.getenv("WC_CONSUMER_SECRET", "")
WP_USER = os.getenv("WP_USERNAME", "admin")
WP_PASS = os.getenv("WP_APP_PASSWORD", "")

# APIs
UNSPLASH_KEY = os.getenv("UNSPLASH_ACCESS_KEY", "YjiiXP_kb4z7yhpBMrK3OWeWx1jf_VQrzl37VfFssLY")
PEXELS_KEY = os.getenv("PEXELS_API_KEY", "Ru0qn9ob5D5XqNyzacPTNoZiaTzfbmjRrHduXFisV4G97BADZ4EDBNiw")

def get_md5_hash(filepath: str) -> str:
    """Calculate MD5 hash of file"""
    with open(filepath, 'rb') as f:
        return hashlib.md5(f.read()).hexdigest()

def find_duplicates_in_db() -> Dict[str, List[int]]:
    """Find products with duplicate images via direct DB query"""
    conn = mysql.connector.connect(
        host="localhost",
        port=3306,
        user="7WDqKtuc94yCN7",
        password="6FJOJIMznmEh7Y",
        database="7WDqKtuc94yCN7"
    )
    
    cursor = conn.cursor()
    
    # Query to find duplicate image URLs
    query = """
    SELECT att.guid, GROUP_CONCAT(p.ID) as product_ids, COUNT(*) as count
    FROM wp_posts p
    INNER JOIN wp_postmeta pm ON p.ID = pm.post_id
    INNER JOIN wp_posts att ON pm.meta_value = att.ID
    WHERE p.post_type = 'product'
      AND p.post_status = 'publish'
      AND pm.meta_key = '_thumbnail_id'
    GROUP BY att.guid
    HAVING COUNT(*) > 1
    ORDER BY count DESC
    """
    
    cursor.execute(query)
    duplicates = {}
    
    for row in cursor.fetchall():
        image_url = row[0]
        product_ids = [int(pid) for pid in row[1].split(',')]
        count = row[2]
        
        if count > 1:
            duplicates[image_url] = product_ids
    
    cursor.close()
    conn.close()
    
    return duplicates

def search_diverse_image(query: str, page: int = 1, provider: str = 'unsplash') -> Optional[str]:
    """Search for image with pagination for diversity"""
    try:
        if provider == 'unsplash':
            url = f"https://api.unsplash.com/search/photos"
            params = {
                'query': query,
                'page': page,
                'per_page': 30,
                'client_id': UNSPLASH_KEY
            }
            response = requests.get(url, params=params, timeout=10)
            data = response.json()
            
            if data.get('results'):
                # Pick a random image from results
                import random
                img = random.choice(data['results'])
                return img['urls']['regular']
                
        elif provider == 'pexels':
            url = f"https://api.pexels.com/v1/search"
            headers = {'Authorization': PEXELS_KEY}
            params = {
                'query': query,
                'page': page,
                'per_page': 30
            }
            response = requests.get(url, headers=headers, params=params, timeout=10)
            data = response.json()
            
            if data.get('photos'):
                import random
                img = random.choice(data['photos'])
                return img['src']['large']
    except Exception as e:
        print(f"  Error searching {provider}: {e}")
    
    return None

def download_and_upload_image(image_url: str, product_id: int, product_name: str) -> Optional[int]:
    """Download image and upload to WordPress"""
    try:
        # Download
        response = requests.get(image_url, timeout=15)
        if response.status_code != 200:
            return None
        
        # Save temp
        import tempfile
        temp_file = tempfile.NamedTemporaryFile(delete=False, suffix='.jpg')
        temp_file.write(response.content)
        temp_file.close()
        
        # Upload to WordPress via REST API
        wp_url = f"{WP_URL}/wp-json/wp/v2/media"
        
        with open(temp_file.name, 'rb') as f:
            files = {
                'file': (f'wcimg_{int(time.time()*1000)}_{product_id}.jpg', f, 'image/jpeg')
            }
            
            headers = {
                'Content-Disposition': f'attachment; filename="product_{product_id}.jpg"'
            }
            
            response = requests.post(
                wp_url,
                files=files,
                headers=headers,
                auth=(WP_USER, WP_PASS),
                timeout=30
            )
        
        os.unlink(temp_file.name)
        
        if response.status_code in [200, 201]:
            media_data = response.json()
            return media_data.get('id')
    
    except Exception as e:
        print(f"  Error uploading: {e}")
    
    return None

def assign_image_to_product(wcapi, product_id: int, media_id: int):
    """Assign image to product via WooCommerce API"""
    try:
        wcapi.put(f"products/{product_id}", {
            "images": [{"id": media_id}]
        })
        return True
    except Exception as e:
        print(f"  Error assigning: {e}")
        return False

def main():
    print("=== FORCE DIVERSITY - Diversificación Agresiva ===\n")
    
    # Initialize WC API
    wcapi = WooCommerceAPI(
        url=WP_URL,
        consumer_key=WC_KEY,
        consumer_secret=WC_SECRET,
        version="wc/v3",
        timeout=30
    )
    
    print("🔍 Buscando duplicados en base de datos...")
    duplicates = find_duplicates_in_db()
    
    print(f"📊 Encontrados {len(duplicates)} grupos de duplicados\n")
    
    used_hashes = set()
    stats = {'success': 0, 'failed': 0, 'skipped': 0}
    
    providers = ['unsplash', 'pexels']
    provider_idx = 0
    
    for image_url, product_ids in tqdm(duplicates.items(), desc="Grupos duplicados"):
        # Keep first product with original image, update the rest
        product_ids_to_update = product_ids[1:]  # Skip first one
        
        for product_id in product_ids_to_update:
            # Get product info
            try:
                product = wcapi.get(f"products/{product_id}").json()
                product_name = product.get('name', '')
                
                print(f"\n🔄 Producto {product_id}: {product_name[:50]}")
                
                # Try multiple search strategies
                queries = [
                    product_name.split()[0] if product_name else 'plant',
                    'garden plant nursery',
                    'potted plant product',
                    'houseplant vivero'
                ]
                
                new_image_url = None
                for attempt in range(5):  # 5 attempts
                    query = queries[attempt % len(queries)]
                    provider = providers[provider_idx % len(providers)]
                    page = (attempt // len(queries)) + 1
                    
                    provider_idx += 1
                    
                    print(f"  → {provider} | query: {query} | page: {page}")
                    new_image_url = search_diverse_image(query, page=page, provider=provider)
                    
                    if new_image_url:
                        # Check if unique
                        temp_hash = hashlib.md5(new_image_url.encode()).hexdigest()
                        if temp_hash not in used_hashes:
                            used_hashes.add(temp_hash)
                            break
                        else:
                            new_image_url = None
                    
                    time.sleep(0.5)
                
                if not new_image_url:
                    print(f"  ❌ No se encontró imagen única")
                    stats['failed'] += 1
                    continue
                
                # Upload and assign
                media_id = download_and_upload_image(new_image_url, product_id, product_name)
                
                if media_id:
                    if assign_image_to_product(wcapi, product_id, media_id):
                        print(f"  ✅ Éxito: media {media_id}")
                        stats['success'] += 1
                    else:
                        stats['failed'] += 1
                else:
                    stats['failed'] += 1
                
                time.sleep(2)  # Rate limiting
                
            except Exception as e:
                print(f"  ❌ Error: {e}")
                stats['failed'] += 1
    
    print("\n" + "="*50)
    print("📊 RESULTADOS FINALES")
    print("="*50)
    print(f"Éxitos:   {stats['success']}")
    print(f"Fallos:   {stats['failed']}")
    print(f"Saltados: {stats['skipped']}")
    print("\n✅ Proceso completado")

if __name__ == "__main__":
    main()
