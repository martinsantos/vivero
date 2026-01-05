#!/usr/bin/env python3
"""
Simple test for Ultimate Image Automation
"""

import os
import requests
import base64
from dotenv import load_dotenv

def test_wc_api():
    load_dotenv()
    
    base_url = os.getenv("WORDPRESS_URL", "http://localhost:8080").rstrip('/')
    username = os.getenv("WP_USERNAME", "admin")
    password = os.getenv("WP_APP_PASSWORD", "")
    
    if not password:
        print("❌ WP_APP_PASSWORD not set")
        return False
    
    # Test WooCommerce API with Basic Auth
    auth = base64.b64encode(f"{username}:{password}".encode()).decode()
    headers = {"Authorization": f"Basic {auth}"}
    
    try:
        url = f"{base_url}/wp-json/wc/v3/products?per_page=5"
        print(f"🔍 Testing WC API: {url}")
        
        response = requests.get(url, headers=headers, timeout=10)
        print(f"   Status: {response.status_code}")
        
        if response.status_code == 200:
            products = response.json()
            print(f"   ✅ Retrieved {len(products)} products")
            
            # Count products without images
            no_images = 0
            for product in products:
                images = product.get("images", [])
                if not images:
                    no_images += 1
                    print(f"      - {product['name']}: NO IMAGES")
                else:
                    print(f"      - {product['name']}: {len(images)} images")
            
            print(f"   📊 Products without images: {no_images}/{len(products)}")
            return True
        else:
            print(f"   ❌ API Error: {response.text[:200]}")
            return False
            
    except Exception as e:
        print(f"   ❌ Exception: {e}")
        return False

if __name__ == "__main__":
    print("🌱 Ultimate Image Automation - Simple Test")
    print("=" * 50)
    
    success = test_wc_api()
    
    if success:
        print("\n✅ Test passed! The API is working correctly.")
        print("   Ready to run: python3 ultimate_image_automation.py --target missing")
    else:
        print("\n❌ Test failed! Check configuration.")