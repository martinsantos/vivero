import os
import requests
from dotenv import load_dotenv

load_dotenv()

def get_wc_products():
    url = f"{os.getenv('WORDPRESS_URL')}/wp-json/wc/v3/products"
    auth = (os.getenv('WC_CONSUMER_KEY'), os.getenv('WC_CONSUMER_SECRET'))
    params = {"per_page": 100}
    r = requests.get(url, auth=auth, params=params)
    if r.status_code == 200:
        return r.json()
    else:
        print(f"Error: {r.status_code} - {r.text}")
        return []

products = get_wc_products()
print(f"{'ID':<6} | {'SKU':<15} | {'Title'}")
print("-" * 50)
for p in products:
    print(f"{p['id']:<6} | {p.get('sku', ''):<15} | {p['name']}")
