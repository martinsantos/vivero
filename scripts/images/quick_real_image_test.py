#!/usr/bin/env python3
"""
Quick test to verify we can get products with SVG images
"""

import os
import requests
import base64
from dotenv import load_dotenv

load_dotenv()

# Configuration
WORDPRESS_URL = os.getenv("WORDPRESS_URL")
WP_USERNAME = os.getenv("WP_USERNAME")
WP_APP_PASSWORD = os.getenv("WP_APP_PASSWORD")

# Create auth header
auth = base64.b64encode(f"{WP_USERNAME}:{WP_APP_PASSWORD}".encode()).decode()
headers = {
    "Authorization": f"Basic {auth}",
    "Content-Type": "application/json"
}

print(f"Testing connection to: {WORDPRESS_URL}")
print(f"Username: {WP_USERNAME}")
print(f"App Password: {WP_APP_PASSWORD[:10]}...")

# Test WordPress REST API
url = f"{WORDPRESS_URL}/wp-json/wp/v2/posts"
params = {"per_page": 5}

try:
    response = requests.get(url, headers=headers, params=params, timeout=10)
    print(f"WordPress API Response: {response.status_code}")
    
    if response.status_code == 200:
        posts = response.json()
        print(f"Retrieved {len(posts)} posts")
        
        # Check for products specifically
        url_products = f"{WORDPRESS_URL}/wp-json/wp/v2/product"
        response_products = requests.get(url_products, headers=headers, params=params, timeout=10)
        print(f"Product endpoint response: {response_products.status_code}")
        
        if response_products.status_code == 200:
            products = response_products.json()
            print(f"Retrieved {len(products)} products")
            
            for product in products[:3]:
                print(f"- {product.get('title', {}).get('rendered', 'No title')} (ID: {product.get('id')})")
        
    else:
        print(f"Error: {response.text[:200]}")
        
except Exception as e:
    print(f"Exception: {e}")

# Test WooCommerce API
print("\n" + "="*50)
print("Testing WooCommerce API...")

WC_KEY = os.getenv("WC_CONSUMER_KEY")
WC_SECRET = os.getenv("WC_CONSUMER_SECRET")

url_wc = f"{WORDPRESS_URL}/wp-json/wc/v3/products"
params_wc = {
    "consumer_key": WC_KEY,
    "consumer_secret": WC_SECRET,
    "per_page": 5
}

try:
    response_wc = requests.get(url_wc, params=params_wc, timeout=10)
    print(f"WooCommerce API Response: {response_wc.status_code}")
    
    if response_wc.status_code == 200:
        wc_products = response_wc.json()
        print(f"Retrieved {len(wc_products)} WC products")
        
        svg_count = 0
        for product in wc_products[:10]:
            name = product.get("name", "No name")
            images = product.get("images", [])
            if images:
                image_url = images[0].get("src", "")
                if "placeholder.svg" in image_url:
                    svg_count += 1
                    print(f"  SVG: {name} - {image_url}")
            else:
                svg_count += 1
                print(f"  No image: {name}")
        
        print(f"Found {svg_count} products needing real images")
    else:
        print(f"WC Error: {response_wc.text[:200]}")
        
except Exception as e:
    print(f"WC Exception: {e}")