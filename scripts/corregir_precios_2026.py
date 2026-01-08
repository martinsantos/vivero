#!/usr/bin/env python3
"""
Script para corregir precios de productos 2026.
Multiplica por 1000 los precios que fueron importados como decimales (ej: 5.4 -> 5400).
"""

import csv
import os
import re
import time
from woocommerce import API
from dotenv import load_dotenv
from pathlib import Path

# Cargar configuración
load_dotenv()

wcapi = API(
    url="https://viveroloscocos.com.ar",
    consumer_key=os.getenv("WC_CONSUMER_KEY"),
    consumer_secret=os.getenv("WC_CONSUMER_SECRET"),
    version="wc/v3",
    timeout=60
)

def parse_price_corrected(price_str):
    """Convierte precio string a float y escala si es necesario"""
    if not price_str or price_str == '-':
        return None
    
    # Limpiar caracteres no numéricos excepto punto y coma
    clean = re.sub(r'[^\d.,]', '', price_str)
    
    # Caso: $5.40 -> queremos 5400
    # Si tiene un punto y el valor es bajo, asumimos que el punto separa miles implícitos
    # o que es un formato $X.YY que debe ser X000 + YY*10
    
    try:
        val = float(clean.replace(',', '.'))
        # Si el valor es menor a 100, es altamente probable que necesite escala
        if val < 500: # Umbral de seguridad, la maceta más barata es 300 o 400
            return int(val * 1000)
        return int(val)
    except:
        return None

def main(dry_run=True):
    csv_path = Path("/Applications/um/vivero/productos2026.csv")
    
    print(f"--- {'MODO SIMULACIÓN' if dry_run else 'MODO REAL'} ---")
    
    # 1. Mapear SKUs del CSV a precios correctos
    correct_prices = {}
    with open(csv_path, 'r', encoding='utf-8') as f:
        reader = csv.reader(f)
        next(reader) # skip header
        for row in reader:
            if len(row) < 6: continue
            sku = row[4].strip()
            if sku and sku != '-':
                price = parse_price_corrected(row[5])
                if price:
                    correct_prices[sku] = price

    # 2. Buscar productos en WooCommerce por SKU
    print(f"Buscando productos para actualizar ({len(correct_prices)} SKUs en CSV)...")
    
    updated = 0
    skipped = 0
    
    for sku, new_price in correct_prices.items():
        try:
            # Buscar el producto por SKU
            res = wcapi.get("products", params={"sku": sku})
            if res.status_code == 200:
                products = res.json()
                if not products:
                    # print(f"  ? SKU {sku} no encontrado en WooCommerce")
                    continue
                
                for p in products:
                    current_price = p.get('regular_price')
                    
                    if str(current_price) == str(new_price):
                        # print(f"  = {p['name']} ya tiene el precio correcto: {new_price}")
                        skipped += 1
                        continue
                        
                    print(f"  -> {p['name']} (SKU: {sku}): {current_price} -> {new_price}")
                    
                    if not dry_run:
                        update_res = wcapi.put(f"products/{p['id']}", {"regular_price": str(new_price)})
                        if update_res.status_code == 200:
                            updated += 1
                        else:
                            print(f"    ✗ Error al actualizar: {update_res.text}")
                    else:
                        updated += 1
            
            # Evitar rate limits
            if not dry_run:
                time.sleep(1)
                
        except Exception as e:
            print(f"  ⚠ Error con SKU {sku}: {e}")

    print(f"\nResumen: {updated} productos {'serían actualizados' if dry_run else 'actualizados'}, {skipped} ya correctos.")

if __name__ == "__main__":
    import sys
    is_dry = "--real" not in sys.argv
    main(dry_run=is_dry)
