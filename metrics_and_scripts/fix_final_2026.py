import os, time, csv, re
from woocommerce import API
from dotenv import load_dotenv

load_dotenv()
wcapi = API(url="https://viveroloscocos.com.ar", consumer_key=os.getenv("WC_CONSUMER_KEY"), consumer_secret=os.getenv("WC_CONSUMER_SECRET"), version="wc/v3", timeout=60)

def parse_p(p):
    c = re.sub(r'[^\d.,]', '', p)
    if '.' in c and ',' in c: c = c.replace('.','').replace(',','.')
    elif ',' in c: c = c.replace(',','.')
    try:
        v = float(c)
        return int(v*1000) if v < 100 else int(v)
    except: return None

m = {}
with open("productos2026.csv", 'r') as f:
    r = csv.reader(f); next(r)
    for row in r:
        n = f"{row[2]} {row[3]}".strip() if row[3] != '-' else row[2].strip()
        m[n.lower()] = parse_p(row[5])

# Busqueda directa por nombres para evitar problemas de paginación
count = 0
for name, target in m.items():
    res = wcapi.get("products", params={"search": name})
    if res.status_code == 200:
        for p in res.json():
            if p['name'].lower().strip() == name:
                if str(p['regular_price']) != str(target):
                    print(f"FIX: {p['name']} -> {target}")
                    wcapi.put(f"products/{p['id']}", {"regular_price": str(target)})
                    time.sleep(1)
    count += 1
    if count % 10 == 0: print(f"Procesados {count}/84")
