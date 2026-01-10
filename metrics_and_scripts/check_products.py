import os
from woocommerce import API
from dotenv import load_dotenv

load_dotenv()
wcapi = API(
    url="https://viveroloscocos.com.ar",
    consumer_key=os.getenv("WC_CONSUMER_KEY"),
    consumer_secret=os.getenv("WC_CONSUMER_SECRET"),
    version="wc/v3",
    timeout=30
)

# Chequear unos específicos de la captura del usuario
search_list = ["Maceta Plastico TA N 18", "Maceta Cemento Cubo Mediano", "Maceta Fibrocemento N 2"]
for s in search_list:
    res = wcapi.get("products", params={"search": s})
    if res.status_code == 200:
        for p in res.json():
            if p['name'].lower() in [x.lower() for x in search_list]:
                print(f"VERIFY: {p['name']} -> {p['regular_price']}")
