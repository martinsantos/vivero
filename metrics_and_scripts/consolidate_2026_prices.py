import csv
import re
import os

def clean_price(p):
    if not p or p == '-': return None
    # Remove currency symbol and other chars except digits, dot and comma
    c = re.sub(r'[^\d.,]', '', p)
    if '.' in c and ',' in c: 
        # Assume dot is thousands and comma is decimal
        c = c.replace('.', '').replace(',', '.')
    elif ',' in c:
        c = c.replace(',', '.')
    try:
        v = float(c)
        # Logic from fix_all_2026.py: if < 100, it's likely in thousands (or needing multiplication)
        # However, looking at productos2026.csv, $5.40 likely means 5400
        if v < 100:
            return v * 1000
        return v
    except:
        return None

master_data_sku = {} # sku -> price
master_data_name = {} # name -> price

def add_price(sku, name, price):
    if sku: master_data_sku[sku] = price
    if name: master_data_name[name.lower().strip()] = price

# 1. precios_reales_mapeo.csv
try:
    with open('data/precios_reales_mapeo.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            sku = row.get('sku')
            name = row.get('nombre')
            price = clean_price(row.get('precio'))
            if price:
                add_price(sku, name, price)
except FileNotFoundError:
    pass

# 2. INVENTARIOTODASLASHOJAS_formatted.csv
try:
    with open('data/INVENTARIOTODASLASHOJAS_formatted.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            sku = row.get('SKU')
            name = row.get('Name')
            price = clean_price(row.get('Regular price'))
            if price:
                add_price(sku, name, price)
except FileNotFoundError:
    pass

# 3. productos2026.csv
try:
    with open('productos2026.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            sku = row.get('SKU (Cód. Interno)')
            # In this file, name is often Jerarquía 2 + Nombre + Variante
            name = f"{row.get('Nombre del Producto (Familia)')} {row.get('Variante / Presentación')}"
            price = clean_price(row.get('Precio (ARS)'))
            if price:
                add_price(sku, name, price)
except FileNotFoundError:
    pass

# 4. productos2026quimicos.csv
try:
    with open('productos2026quimicos.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            name = f"{row.get('PRODUCTO')} {row.get('PRESENTACIÓN')}"
            price = clean_price(row.get('PRECIO'))
            if price:
                add_price(None, name, price)
except FileNotFoundError:
    pass

# Write master CSVs
os.makedirs('data', exist_ok=True)
with open('data/master_prices_sku_2026.csv', 'w', encoding='utf-8', newline='') as f:
    writer = csv.writer(f)
    writer.writerow(['sku', 'precio'])
    for sku, price in master_data_sku.items():
        writer.writerow([sku, price])

with open('data/master_prices_name_2026.csv', 'w', encoding='utf-8', newline='') as f:
    writer = csv.writer(f)
    writer.writerow(['name', 'precio'])
    for name, price in master_data_name.items():
        writer.writerow([name, price])

print(f"Consolidated {len(master_data_sku)} SKU prices and {len(master_data_name)} Name prices")
