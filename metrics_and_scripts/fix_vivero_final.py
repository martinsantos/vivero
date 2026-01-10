import csv
import re
import time
import os
from woocommerce import API
from dotenv import load_dotenv

load_dotenv()
wcapi = API(
    url="https://viveroloscocos.com.ar",
    consumer_key=os.getenv("WC_CONSUMER_KEY"),
    consumer_secret=os.getenv("WC_CONSUMER_SECRET"),
    version="wc/v3",
    timeout=60
)

def get_price(p):
    if not p or p == '-': return None
    c = re.sub(r'[^\d.,]', '', p)
    if '.' in c and ',' in c: c = c.replace('.', '').replace(',', '.')
    elif ',' in c: c = c.replace(',', '.')
    try:
        v = float(c)
        return int(v * 1000) if v < 100 else int(v)
    except: return None

# 1. Cargar mapeo del CSV
mapping = {}
with open("productos2026.csv", 'r', encoding='utf-8') as f:
    reader = csv.reader(f)
    next(reader)
    for row in reader:
        name = f"{row[2].strip()} {row[3].strip()}".strip() if row[3].strip() != '-' else row[2].strip()
        price = get_price(row[5])
        if price: mapping[name.lower()] = price

# 2. Buscar y actualizar cada uno
for name, price in mapping.items():
    res = wcapi.get("products", params={"search": name, "per_page": 5})
    if res.status_code == 200:
        for p in res.json():
            if p['name'].lower().strip() == name:
                if str(p.get('regular_price')) != str(price):
                    print(f"UPDATING {p['name']}: {p['regular_price']} -> {price}")
                    wcapi.put(f"products/{p['id']}", {"regular_price": str(price)})
                    time.sleep(1)
