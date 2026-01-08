#!/usr/bin/env python3
"""
Carga de Productos Químicos 2026 desde productos2026quimicos.csv
Con imágenes reales de la carpeta Glacoxan/
"""
import csv
import os
import re
import time
import requests
from woocommerce import API
from dotenv import load_dotenv
from pathlib import Path

load_dotenv()

wcapi = API(
    url="https://viveroloscocos.com.ar",
    consumer_key=os.getenv("WC_CONSUMER_KEY"),
    consumer_secret=os.getenv("WC_CONSUMER_SECRET"),
    version="wc/v3",
    timeout=120
)

WP_USER = os.getenv("WP_USERNAME", "admin")
WP_APP_PASS = os.getenv("WP_APP_PASSWORD")

# Mapeo de nombres de productos a archivos de imagen
IMAGE_MAP = {
    "fertifox floración": "Fertifox FLORACION.png",
    "fertifox potenciado": "Fertifox HORMONA.png",
    "fertifox follaje": "Fertifox FOLLAJE.png",
    "fertifox lustre": "Fertifox Lustre vegetal.png",
    "fungoxan": "Fungoxan.png",
    "glacoxan total": "Glacoxan TOTAL.png",
    "glacoxan mcpa": "Glacoxan MCPA.png",
    "glacoxan e": "Glacoxan E.png",
    "glacoxan h": "Glacoxan H.png",
    "glacoxan ciper": "Glacoxan Ciper.png",
    "glacoxan d-sist": "Glacoxan D-sist.png",
    "glacoxan avam": "Glacoxan AVAM.png",
    "glacoxan imida": "Glacoxan IMIDA.png",
    "glacoxan oil": "Glacoxan OIL.png",
    "glacoxan p (pellet)": "Glacoxan P Pellet.png",
    "glacoxan p (polvo)": "Glacoxan P Polvo seco.png",
}

IMAGE_DIR = Path("/Applications/um/vivero/Glacoxan")
uploaded_images = {}

def parse_price(price_str):
    """Convierte $7.76 a 7760"""
    if not price_str:
        return None
    clean = re.sub(r'[^\d.,]', '', price_str)
    clean = clean.replace(',', '.')
    try:
        val = float(clean)
        return int(val * 1000)  # Escalar a pesos argentinos
    except:
        return None

def upload_image(image_path, product_name):
    """Sube imagen a WordPress y retorna el ID"""
    if image_path in uploaded_images:
        return uploaded_images[image_path]
    
    if not image_path.exists():
        print(f"  ⚠ Imagen no encontrada: {image_path}")
        return None
    
    try:
        url = "https://viveroloscocos.com.ar/wp-json/wp/v2/media"
        headers = {
            "Content-Disposition": f'attachment; filename="{image_path.name}"',
            "Content-Type": "image/png"
        }
        with open(image_path, 'rb') as img:
            response = requests.post(
                url,
                headers=headers,
                data=img,
                auth=(WP_USER, WP_APP_PASS),
                timeout=60
            )
        
        if response.status_code == 201:
            img_id = response.json()['id']
            uploaded_images[image_path] = img_id
            print(f"  ✓ Imagen subida: {image_path.name} (ID: {img_id})")
            return img_id
        else:
            print(f"  ✗ Error subiendo imagen: {response.status_code}")
            return None
    except Exception as e:
        print(f"  ✗ Error: {e}")
        return None

def get_or_create_category(cat_name):
    """Obtiene o crea categoría"""
    try:
        res = wcapi.get("products/categories", params={"search": cat_name, "per_page": 100})
        if res.status_code == 200:
            for cat in res.json():
                if cat["name"].lower() == cat_name.lower():
                    return cat["id"]
        
        # Crear
        res = wcapi.post("products/categories", {"name": cat_name})
        if res.status_code == 201:
            return res.json()["id"]
    except Exception as e:
        print(f"  ⚠ Error categoría: {e}")
    return None

def main():
    print("=" * 60)
    print("🧪 CARGA PRODUCTOS QUÍMICOS 2026")
    print("=" * 60)
    
    csv_file = Path("/Applications/um/vivero/productos2026quimicos.csv")
    success = 0
    errors = 0
    
    with open(csv_file, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            categoria = row["CATEGORÍA (Deducida)"].strip()
            producto = row["PRODUCTO"].strip()
            precio = parse_price(row["PRECIO"])
            presentacion = row["PRESENTACIÓN"].strip()
            descripcion = row["GENERALIDADES / USO"].strip()
            
            product_name = f"{producto} {presentacion}"
            print(f"\n📦 {product_name}")
            
            # Categoría
            cat_id = get_or_create_category(categoria)
            
            # Imagen
            product_key = producto.lower()
            image_file = IMAGE_MAP.get(product_key)
            image_id = None
            if image_file:
                image_path = IMAGE_DIR / image_file
                image_id = upload_image(image_path, product_name)
            
            # Crear producto
            product_data = {
                "name": product_name,
                "type": "simple",
                "status": "publish",
                "regular_price": str(precio) if precio else "",
                "description": descripcion,
                "short_description": f"{producto} - {presentacion}",
                "categories": [{"id": cat_id}] if cat_id else [],
                "manage_stock": False,
                "stock_status": "instock",
            }
            
            if image_id:
                product_data["images"] = [{"id": image_id}]
            
            try:
                res = wcapi.post("products", product_data)
                if res.status_code == 201:
                    prod = res.json()
                    print(f"  ✓ Creado ID {prod['id']} - ${precio}")
                    success += 1
                else:
                    print(f"  ✗ Error: {res.status_code} - {res.text[:100]}")
                    errors += 1
            except Exception as e:
                print(f"  ✗ Error: {e}")
                errors += 1
            
            time.sleep(2)
    
    print("\n" + "=" * 60)
    print(f"📊 RESUMEN: {success} creados, {errors} errores")
    print("=" * 60)

if __name__ == "__main__":
    main()
