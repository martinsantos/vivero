#!/usr/bin/env python3
"""
Asigna imágenes relevantes a productos sin imagen usando APIs de imágenes gratuitas
"""

import os
import sys
import requests
import hashlib
from dotenv import load_dotenv
from woocommerce import API
import time

load_dotenv()

# Configuración
WORDPRESS_URL = os.getenv('WORDPRESS_URL')
WC_KEY = os.getenv('WC_CONSUMER_KEY')
WC_SECRET = os.getenv('WC_CONSUMER_SECRET')
WP_USER = os.getenv('WP_USERNAME')
WP_PASS = os.getenv('WP_APP_PASSWORD')
UNSPLASH_KEY = os.getenv('UNSPLASH_API_KEY')
PEXELS_KEY = os.getenv('PEXELS_API_KEY')

# Inicializar WooCommerce API
wcapi = API(
    url=WORDPRESS_URL,
    consumer_key=WC_KEY,
    consumer_secret=WC_SECRET,
    version="wc/v3",
    timeout=30
)

# Productos sin imagen y sus búsquedas optimizadas
PRODUCTS_TO_FIX = {
    "36": "orange plastic pot planter",
    "42": "terracotta brown pot planter",
    "43": "yellow plastic pot planter",
    "45": "light green plastic pot planter",
    "132": "green monaco bowl planter",
    "238": "dark green plant saucer",
    "239": "green plant saucer tray",
    "240": "white plant saucer tray",
    "241": "brown plant saucer tray",
    "255": "black plant saucer tray",
    "337": "andean terracotta pot traditional",
    "338": "decorated andean pottery planter",
    "339": "conical terracotta pot tall",
    "353": "wooden plant stand nordic",
    "354": "wooden plant stand scandinavian"
}

def search_unsplash(query: str) -> str:
    """Busca imagen en Unsplash"""
    if not UNSPLASH_KEY:
        return None
    
    url = "https://api.unsplash.com/search/photos"
    params = {
        'query': query,
        'per_page': 1,
        'orientation': 'squarish',
        'client_id': UNSPLASH_KEY
    }
    
    try:
        response = requests.get(url, params=params, timeout=10)
        if response.status_code == 200:
            data = response.json()
            if data['results']:
                return data['results'][0]['urls']['regular']
    except Exception as e:
        print(f"   ⚠️  Error Unsplash: {e}")
    
    return None

def search_pexels(query: str) -> str:
    """Busca imagen en Pexels"""
    if not PEXELS_KEY:
        return None
    
    url = "https://api.pexels.com/v1/search"
    headers = {'Authorization': PEXELS_KEY}
    params = {
        'query': query,
        'per_page': 1,
        'orientation': 'square'
    }
    
    try:
        response = requests.get(url, headers=headers, params=params, timeout=10)
        if response.status_code == 200:
            data = response.json()
            if data['photos']:
                return data['photos'][0]['src']['large']
    except Exception as e:
        print(f"   ⚠️  Error Pexels: {e}")
    
    return None

def download_image(url: str) -> bytes:
    """Descarga imagen desde URL"""
    try:
        response = requests.get(url, timeout=15)
        if response.status_code == 200:
            return response.content
    except Exception as e:
        print(f"   ❌ Error descargando: {e}")
    return None

def upload_to_wordpress(image_data: bytes, filename: str) -> int:
    """Sube imagen a WordPress Media Library"""
    upload_url = f"{WORDPRESS_URL}/wp-json/wp/v2/media"
    
    headers = {
        'Content-Disposition': f'attachment; filename="{filename}"',
        'Content-Type': 'image/jpeg'
    }
    
    try:
        response = requests.post(
            upload_url,
            headers=headers,
            data=image_data,
            auth=(WP_USER, WP_PASS),
            timeout=30
        )
        
        if response.status_code == 201:
            return response.json()['id']
        else:
            print(f"   ❌ Error upload: {response.status_code} - {response.text[:200]}")
    except Exception as e:
        print(f"   ❌ Error subiendo imagen: {e}")
    
    return None

def assign_image_to_product(product_id: str, image_id: int):
    """Asigna imagen destacada a producto"""
    try:
        data = {'images': [{'id': image_id}]}
        response = wcapi.put(f"products/{product_id}", data)
        
        if response.status_code == 200:
            return True
        else:
            print(f"   ❌ Error asignando imagen: {response.status_code}")
    except Exception as e:
        print(f"   ❌ Error: {e}")
    
    return False

def process_product(product_id: str, search_query: str):
    """Procesa un producto: busca, descarga y asigna imagen"""
    print(f"\n🖼️  Producto #{product_id}: {search_query}")
    
    # Buscar imagen
    image_url = search_unsplash(search_query)
    if not image_url:
        image_url = search_pexels(search_query)
    
    if not image_url:
        print(f"   ❌ No se encontró imagen")
        return False
    
    print(f"   ✓ Imagen encontrada")
    
    # Descargar
    image_data = download_image(image_url)
    if not image_data:
        return False
    
    print(f"   ✓ Imagen descargada ({len(image_data)} bytes)")
    
    # Generar nombre único
    hash_name = hashlib.md5(image_data).hexdigest()[:12]
    filename = f"product-{product_id}-{hash_name}.jpg"
    
    # Subir a WordPress
    media_id = upload_to_wordpress(image_data, filename)
    if not media_id:
        return False
    
    print(f"   ✓ Subida a WordPress (Media ID: {media_id})")
    
    # Asignar a producto
    if assign_image_to_product(product_id, media_id):
        print(f"   ✅ Imagen asignada exitosamente")
        return True
    
    return False

def main():
    print("=" * 80)
    print("🖼️  ASIGNACIÓN DE IMÁGENES - VIVERO LOS COCOS")
    print("=" * 80)
    print(f"\nProductos a procesar: {len(PRODUCTS_TO_FIX)}")
    
    success_count = 0
    failed_count = 0
    
    for product_id, search_query in PRODUCTS_TO_FIX.items():
        if process_product(product_id, search_query):
            success_count += 1
        else:
            failed_count += 1
        
        # Pausa para no saturar APIs
        time.sleep(2)
    
    print("\n" + "=" * 80)
    print("📊 RESUMEN")
    print("=" * 80)
    print(f"""
    Total procesados:  {len(PRODUCTS_TO_FIX)}
    Exitosos:          {success_count}
    Fallidos:          {failed_count}
    
    ✅ Proceso completado
    """)

if __name__ == '__main__':
    main()
