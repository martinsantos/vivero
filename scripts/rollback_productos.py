#!/usr/bin/env python3
"""
Rollback: Elimina todos los productos cargados hoy (ID >= 61017)
"""
import os
import time
from woocommerce import API
from dotenv import load_dotenv

load_dotenv()
wcapi = API(
    url="https://viveroloscocos.com.ar",
    consumer_key=os.getenv("WC_CONSUMER_KEY"),
    consumer_secret=os.getenv("WC_CONSUMER_SECRET"),
    version="wc/v3",
    timeout=120
)

# Obtener productos recientes y eliminarlos
deleted = 0
errors = 0
for page in range(1, 5):
    print(f"Página {page}...")
    try:
        res = wcapi.get("products", params={"page": page, "per_page": 100, "orderby": "id", "order": "desc"})
        if res.status_code == 200:
            batch = res.json()
            if not batch:
                break
            for p in batch:
                if p['id'] >= 61017:
                    for attempt in range(3):
                        try:
                            print(f"Eliminando ID {p['id']}: {p['name']}")
                            wcapi.delete(f"products/{p['id']}", params={"force": True})
                            deleted += 1
                            time.sleep(1)
                            break
                        except Exception as e:
                            if attempt < 2:
                                print(f"  Reintentando ({attempt+1}/3)...")
                                time.sleep(5)
                            else:
                                print(f"  ✗ Error: {e}")
                                errors += 1
        else:
            print(f"Error API: {res.status_code}")
            break
    except Exception as e:
        print(f"Error en página {page}: {e}")
        time.sleep(10)

print(f"\n✓ Total eliminados: {deleted}, errores: {errors}")

