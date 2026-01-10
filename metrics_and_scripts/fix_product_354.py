#!/usr/bin/env python3
import os
import requests
import hashlib
from dotenv import load_dotenv
from woocommerce import API
import time

load_dotenv()

WORDPRESS_URL = os.getenv('WORDPRESS_URL')
WC_KEY = os.getenv('WC_CONSUMER_KEY')
WC_SECRET = os.getenv('WC_CONSUMER_SECRET')
WP_USER = os.getenv('WP_USERNAME')
WP_PASS = os.getenv('WP_APP_PASSWORD')
UNSPLASH_KEY = os.getenv('UNSPLASH_API_KEY')

wcapi = API(url=WORDPRESS_URL, consumer_key=WC_KEY, consumer_secret=WC_SECRET, version="wc/v3", timeout=30)

def search_unsplash(query):
    url = "https://api.unsplash.com/search/photos"
    params = {'query': query, 'per_page': 1, 'orientation': 'squarish', 'client_id': UNSPLASH_KEY}
    response = requests.get(url, params=params, timeout=10)
    if response.status_code == 200:
        data = response.json()
        if data['results']:
            return data['results'][0]['urls']['regular']
    return None

def download_image(url):
    response = requests.get(url, timeout=15)
    if response.status_code == 200:
        return response.content
    return None

def upload_to_wordpress(image_data, filename):
    upload_url = f"{WORDPRESS_URL}/wp-json/wp/v2/media"
    headers = {'Content-Disposition': f'attachment; filename="{filename}"', 'Content-Type': 'image/jpeg'}
    
    for attempt in range(3):
        try:
            response = requests.post(upload_url, headers=headers, data=image_data, auth=(WP_USER, WP_PASS), timeout=30, verify=True)
            if response.status_code == 201:
                return response.json()['id']
            print(f"Intento {attempt+1}: {response.status_code}")
            time.sleep(2)
        except Exception as e:
            print(f"Intento {attempt+1} error: {e}")
            time.sleep(3)
    return None

def assign_image_to_product(product_id, image_id):
    data = {'images': [{'id': image_id}]}
    response = wcapi.put(f"products/{product_id}", data)
    return response.status_code == 200

print("🔧 Reparando producto #354...")
image_url = search_unsplash("wooden plant stand scandinavian")
if image_url:
    print("✓ Imagen encontrada")
    image_data = download_image(image_url)
    if image_data:
        print(f"✓ Descargada ({len(image_data)} bytes)")
        hash_name = hashlib.md5(image_data).hexdigest()[:12]
        filename = f"product-354-{hash_name}.jpg"
        media_id = upload_to_wordpress(image_data, filename)
        if media_id:
            print(f"✓ Subida (Media ID: {media_id})")
            if assign_image_to_product("354", media_id):
                print("✅ Producto #354 reparado exitosamente")
            else:
                print("❌ Error asignando imagen")
        else:
            print("❌ Error subiendo imagen")
