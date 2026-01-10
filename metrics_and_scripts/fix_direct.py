import time
import requests
from woocommerce import API
import os
from dotenv import load_dotenv

load_dotenv()
wcapi = API(
    url="https://viveroloscocos.com.ar",
    consumer_key=os.getenv("WC_CONSUMER_KEY"),
    consumer_secret=os.getenv("WC_CONSUMER_SECRET"),
    version="wc/v3",
    timeout=60
)

# MACETAS PLASTICO TA
ids = {
    61099: 1000, 61100: 1200, 61101: 1500, 61102: 2100, 61103: 2400,
    61104: 3100, 61105: 4000, 61106: 5400, 61107: 7500, 61108: 10800,
    61111: 4400, 61112: 6800, 61113: 8800, # Cemento Cubo
    61114: 6900, 61115: 8900, 61116: 12500 # Fibrocemento
}

for pid, price in ids.items():
    print(f"Updating {pid} to {price}...")
    res = wcapi.put(f"products/{pid}", {"regular_price": str(price)})
    print(res.status_code)
    time.sleep(1)
