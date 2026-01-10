import csv, re, time, os
from woocommerce import API
from dotenv import load_dotenv

load_dotenv()
wcapi = API(url="https://viveroloscocos.com.ar", consumer_key=os.getenv("WC_CONSUMER_KEY"), consumer_secret=os.getenv("WC_CONSUMER_SECRET"), version="wc/v3", timeout=60)

def parse_val(p):
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
        n = f"{row[2]} {row[3]}".replace(" - "," ").strip().lower()
        pr = parse_val(row[5])
        if pr: m[n] = pr

res = wcapi.get("products", params={"per_page": 100, "orderby": "id", "order": "desc"})
if res.status_code == 200:
    for p in res.json():
        pn = p['name'].lower().strip()
        if pn in m:
            if str(p['regular_price']) != str(m[pn]):
                print(f"FIX {p['id']}: {m[pn]}")
                wcapi.put(f"products/{p['id']}", {"regular_price": str(m[pn])})
                time.sleep(1)
