import os, time
from woocommerce import API
from dotenv import load_dotenv

load_dotenv()
wcapi = API(url="https://viveroloscocos.com.ar", consumer_key=os.getenv("WC_CONSUMER_KEY"), consumer_secret=os.getenv("WC_CONSUMER_SECRET"), version="wc/v3", timeout=60)

# Batch update masivo para todos los IDs de hoy (61017 a 61150)
data = {"update": []}
for pid in range(61017, 61150):
    try:
        res = wcapi.get(f"products/{pid}")
        if res.status_code == 200:
            p = res.json()
            curr = p.get('regular_price')
            if curr and '.' in curr or float(curr or 0) < 100:
                new_price = int(float(curr) * 1000)
                data["update"].append({"id": pid, "regular_price": str(new_price)})
                print(f"ADD: {p['name']} ({curr} -> {new_price})")
    except: pass

if data["update"]:
    print(f"Enviando batch de {len(data['update'])} productos...")
    res = wcapi.post("products/batch", data)
    print(f"Status: {res.status_code}")
