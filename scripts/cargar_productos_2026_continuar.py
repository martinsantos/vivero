#!/usr/bin/env python3
"""
Carga de Productos 2026 - CONTINUACIÓN desde fila 8
Carga productos restantes desde CSV a WooCommerce
"""

import csv
import os
import re
import time
import requests
from woocommerce import API
from dotenv import load_dotenv
from pathlib import Path

# Cargar configuración
load_dotenv()

# Configuración WooCommerce con timeout aumentado
wcapi = API(
    url="https://viveroloscocos.com.ar",
    consumer_key=os.getenv("WC_CONSUMER_KEY"),
    consumer_secret=os.getenv("WC_CONSUMER_SECRET"),
    version="wc/v3",
    timeout=120  # 2 minutos
)

# Credenciales WordPress
WP_USER = os.getenv("WP_USERNAME", "admin")
WP_APP_PASS = os.getenv("WP_APP_PASSWORD")

# Imagen placeholder ya subida
PLACEHOLDER_IMAGE_ID = 61016

# Cache de categorías
category_cache = {}

# Fila desde la que continuar (0-indexed, sin contar header)
START_ROW = 7  # Ya se crearon 7 productos (filas 0-6)


def parse_price(price_str):
    """Convierte precio string a float"""
    if not price_str or price_str == '-':
        return None
    clean = re.sub(r'[^\d.,]', '', price_str)
    if '.' in clean and ',' in clean:
        clean = clean.replace('.', '').replace(',', '.')
    elif ',' in clean:
        clean = clean.replace(',', '.')
    try:
        return float(clean)
    except:
        return None


def get_or_create_category(cat_name, parent_id=None):
    """Obtiene o crea una categoría en WooCommerce"""
    cache_key = f"{parent_id}_{cat_name}"
    if cache_key in category_cache:
        return category_cache[cache_key]
    
    try:
        params = {"search": cat_name, "per_page": 100}
        if parent_id:
            params["parent"] = parent_id
        
        response = wcapi.get("products/categories", params=params)
        if response.status_code == 200:
            categories = response.json()
            for cat in categories:
                if cat["name"].lower() == cat_name.lower():
                    category_cache[cache_key] = cat["id"]
                    print(f"  ✓ Categoría: {cat_name} (ID: {cat['id']})")
                    return cat["id"]
        
        # Crear categoría
        data = {"name": cat_name}
        if parent_id:
            data["parent"] = parent_id
        
        response = wcapi.post("products/categories", data)
        if response.status_code == 201:
            cat_id = response.json()["id"]
            category_cache[cache_key] = cat_id
            print(f"  + Categoría nueva: {cat_name} (ID: {cat_id})")
            return cat_id
    except Exception as e:
        print(f"  ⚠ Error categoría {cat_name}: {e}")
    
    return None


def improve_description(short_desc, long_desc, name, variant):
    """Mejora la descripción si es insuficiente"""
    if long_desc and len(long_desc) > 50:
        return long_desc
    
    product_name = f"{name} {variant}".strip() if variant and variant != '-' else name
    
    if "Glacoxan" in name:
        return f"{product_name} - Producto profesional para sanidad vegetal. Aplicar según indicaciones del marbete."
    elif "Hormiguicida" in name.lower():
        return f"{product_name} para control efectivo de hormigas. Producto de alta eficacia."
    elif "Humus" in name:
        return f"{product_name} - Fertilizante orgánico de primera calidad. Mejora la estructura del suelo."
    elif "Perlita" in name:
        return f"{product_name} - Mineral volcánico para aireación y drenaje del sustrato."
    elif "Leca" in name:
        return f"{product_name} - Arcilla expandida ideal para drenaje en macetas."
    elif "Maceta" in name:
        material = "plástico" if "Plástico" in name else "cemento" if "Cemento" in name else "fibrocemento" if "Fibrocemento" in name else "barro"
        return f"{product_name} - Contenedor de {material} para tus plantas."
    elif "Plato" in name:
        return f"{product_name} - Accesorio para recolección de agua."
    else:
        return short_desc or f"{product_name} - Producto de calidad para tu jardín."


def generate_slug(name, variant):
    """Genera un slug único para el producto"""
    text = f"{name}-{variant}" if variant and variant != '-' else name
    slug = text.lower()
    slug = re.sub(r'[^a-z0-9]+', '-', slug)
    slug = slug.strip('-')
    return slug


def create_product(row, row_num, max_retries=3):
    """Crea un producto en WooCommerce con reintentos"""
    cat_principal = row[0].strip()
    cat_secundaria = row[1].strip()
    nombre = row[2].strip()
    variante = row[3].strip()
    sku = row[4].strip() if row[4] != '-' else None
    precio = parse_price(row[5])
    desc_corta = row[6].strip()
    desc_larga = row[7].strip()
    
    product_name = f"{nombre} {variante}".strip() if variante and variante != '-' else nombre
    print(f"\n[{row_num}] 📦 {product_name}")
    
    # Obtener/crear categorías
    cat_principal_id = get_or_create_category(cat_principal)
    cat_secundaria_id = get_or_create_category(cat_secundaria, cat_principal_id) if cat_secundaria else None
    
    categories = []
    if cat_principal_id:
        categories.append({"id": cat_principal_id})
    if cat_secundaria_id:
        categories.append({"id": cat_secundaria_id})
    
    description = improve_description(desc_corta, desc_larga, nombre, variante)
    
    product_data = {
        "name": product_name,
        "type": "simple",
        "status": "publish",
        "catalog_visibility": "visible",
        "description": description,
        "short_description": desc_corta,
        "sku": sku,
        "regular_price": str(precio) if precio else "",
        "manage_stock": False,
        "stock_status": "instock",
        "categories": categories,
        "slug": generate_slug(nombre, variante),
        "images": [{"id": PLACEHOLDER_IMAGE_ID}]
    }
    
    for attempt in range(max_retries):
        try:
            response = wcapi.post("products", product_data)
            
            if response.status_code == 201:
                prod = response.json()
                print(f"  ✓ Creado (ID: {prod['id']}, ${precio})")
                return True
            else:
                error = response.json() if response.status_code < 500 else response.text[:100]
                print(f"  ✗ Error API: {error}")
                return False
                
        except Exception as e:
            if attempt < max_retries - 1:
                wait = 10 * (attempt + 1)
                print(f"  ⏳ Timeout, retry en {wait}s...")
                time.sleep(wait)
            else:
                print(f"  ✗ Falló tras {max_retries} intentos: {e}")
                return False
    
    return False


def main():
    print("=" * 60)
    print("🌿 CARGA PRODUCTOS 2026 - CONTINUACIÓN")
    print(f"   Continuando desde fila {START_ROW + 1}")
    print("=" * 60)
    
    # Verificar conexión
    print("\n🔌 Verificando conexión...")
    try:
        test = wcapi.get("products", params={"per_page": 1})
        if test.status_code != 200:
            print(f"✗ Error de conexión: {test.status_code}")
            return
        print("  ✓ Conectado")
    except Exception as e:
        print(f"✗ Error: {e}")
        return
    
    csv_file = Path("/Applications/um/vivero/productos2026.csv")
    success = 0
    errors = 0
    
    with open(csv_file, 'r', encoding='utf-8') as f:
        reader = csv.reader(f)
        next(reader)  # Saltar encabezado
        
        for row_num, row in enumerate(reader):
            if row_num < START_ROW:
                continue  # Saltar filas ya procesadas
                
            if len(row) >= 8 and row[0].strip():
                if create_product(row, row_num + 1):
                    success += 1
                else:
                    errors += 1
                
                # Pausa entre productos para no sobrecargar
                time.sleep(2)
    
    print("\n" + "=" * 60)
    print(f"📊 RESUMEN: {success} creados, {errors} errores")
    print("=" * 60)


if __name__ == "__main__":
    main()
