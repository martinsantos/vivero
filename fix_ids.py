import os, time
from woocommerce import API
from dotenv import load_dotenv

load_dotenv()
wcapi = API(url="https://viveroloscocos.com.ar", consumer_key=os.getenv("WC_CONSUMER_KEY"), consumer_secret=os.getenv("WC_CONSUMER_SECRET"), version="wc/v3", timeout=60)

# IDs obtenidos de inspeccion visual y logs
ids = {
    61103: 2400, # TA N18
    61112: 6800, # Cemento Cubo Mediano
    61115: 8900, # Fibrocemento N2
    61118: 600,  # Toceto N0
    61025: 5400, # Glacoxan H 60
    61026: 9300, # Glacoxan H 120
    61044: 8900  # Follaje Chico
}

for pid, price in ids.items():
    print(f"ID {pid} -> {price}")
    wcapi.put(f"products/{pid}", {"regular_price": str(price)})
    time.sleep(1)
