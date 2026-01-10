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

def clean_p(p):
    if not p or p == '-': return None
    c = re.sub(r'[^\d.,]', '', p)
    if '.' in c and ',' in c: c = c.replace('.', '').replace(',', '.')
    elif ',' in c: c = c.replace(',', '.')
    try:
        v = float(c)
        return int(v * 1000) if v < 100 else int(v)
    except: return None

csv_data = []
with open("productos2026.csv", 'r', encoding='utf-8') as f:
    r = csv.reader(f)
    next(r)
    for row in r:
        name = f"{row[2].strip()} {row[3].strip()}".strip() if row[3].strip() != '-' else row[2].strip()
        price = clean_p(row[5])
        if price: csv_data.append((name.lower(), price))

print(f"CSV items: {len(csv_data)}")

# Obtener productos de hoy
all_p = []
for page in range(1, 4):
    res = wcapi.get("products", params={"page": page, "per_page": 100, "orderby": "id", "order": "desc"})
    if res.status_code == 200:
        batch = res.json()
        if not batch: break
        all_p.extend(batch)

updated = 0
for name, price in csv_data:
    for p in all_p:
        if p['name'].lower().strip() == name:
            if str(p.get('regular_price')) != str(price):
                print(f"FIX: {p['name']} -> {price}")
                wcapi.put(f"products/{p['id']}", {"regular_price": str(price)})
                updated += 1
                time.sleep(0.5)

print(f"Done. {updated} fixed.")
