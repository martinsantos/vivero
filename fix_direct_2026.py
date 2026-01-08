import os, time
from woocommerce import API
from dotenv import load_dotenv

load_dotenv()
wcapi = API(url="https://viveroloscocos.com.ar", consumer_key=os.getenv("WC_CONSUMER_KEY"), consumer_secret=os.getenv("WC_CONSUMER_SECRET"), version="wc/v3", timeout=60)

# Lista manual de correcciones urgentes basadas en la captura
corrections = {
    "Maceta Plástico TA N 18": 2400,
    "Maceta Cemento Cubo Mediano": 6800,
    "Maceta Fibrocemento N 2": 8900,
    "Maceta Toceto (Barro) N 0": 600,
    "Glacoxan H (Líquido) 60 cc": 5400,
    "Glacoxan H (Líquido) 120 cc": 9300,
    "Fertifox Activador de Follaje Chico": 8900
}

for name, price in corrections.items():
    res = wcapi.get("products", params={"search": name})
    if res.status_code == 200:
        for p in res.json():
            if p['name'].lower().strip() == name.lower().strip():
                print(f"Updating {p['name']} to {price}")
                wcapi.put(f"products/{p['id']}", {"regular_price": str(price)})
                time.sleep(1)
