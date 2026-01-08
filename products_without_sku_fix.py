import csv
import os
import re
import time
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

def parse_p(p):
    if not p or p == '-': return None
    c = re.sub(r'[^\d.,]', '', p)
    if '.' in c and ',' in c: c = c.replace('.', '').replace(',', '.')
    elif ',' in c: c = c.replace(',', '.')
    try:
        v = float(c)
        return int(v * 1000) if v < 50 else int(v)
    except: return None

csv_map = {}
with open("productos2026.csv", 'r', encoding='utf-8') as f:
    r = csv.reader(f)
    next(r)
    for row in r:
        n = f"{row[2].strip()} {row[3].strip()}".strip() if row[3].strip() != '-' else row[2].strip()
        pr = parse_p(row[5])
        if pr: csv_map[n.lower().strip()] = pr

# ACTUALIZACION MANUAL POR RANGO DE IDS (los cargados hoy son 61xxx)
for pid in range(61017, 61150):
    try:
        res = wcapi.get(f"products/{pid}")
        if res.status_code == 200:
            p = res.json()
            name = p['name'].lower().strip()
            if name in csv_map:
                t = csv_map[name]
                cur = p.get('regular_price')
                if str(cur) != str(t):
                    print(f"FIX {pid}: {p['name']} {cur} -> {t}")
                    wcapi.put(f"products/{pid}", {"regular_price": str(t)})
                    time.sleep(0.5)
                else:
                    print(f"OK {pid}: {p['name']}")
    except:
        pass
