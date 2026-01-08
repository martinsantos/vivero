#!/usr/bin/env python3
"""
Carga de Productos 2026 - Vivero Los Cocos
Carga productos desde CSV a WooCommerce con imagen placeholder
"""

import csv
import os
import re
import time
import requests
from woocommerce import API
from dotenv import load_dotenv
from pathlib import Path
from requests.adapters import HTTPAdapter
from urllib3.util.retry import Retry

# Cargar configuración
load_dotenv()

# Configuración WooCommerce con timeout aumentado
wcapi = API(
    url="https://viveroloscocos.com.ar",
    consumer_key=os.getenv("WC_CONSUMER_KEY"),
    consumer_secret=os.getenv("WC_CONSUMER_SECRET"),
    version="wc/v3",
    timeout=90  # Aumentado de 30 a 90 segundos
)

# Credenciales WordPress para subir imágenes
WP_USER = os.getenv("WP_USERNAME", "admin")
WP_APP_PASS = os.getenv("WP_APP_PASSWORD")
WP_MEDIA_URL = "https://viveroloscocos.com.ar/wp-json/wp/v2/media"

# Imagen placeholder
PLACEHOLDER_IMAGE = Path("/Users/santosma/.gemini/antigravity/brain/86b0a12a-effc-4397-a861-0bc93e412f0e/producto_2026_placeholder_1767707957318.png")

# Cache de categorías e imagen
category_cache = {}
placeholder_image_id = None


def parse_price(price_str):
    """Convierte precio string a float"""
    if not price_str or price_str == '-':
        return None
    # Remover $ y espacios, manejar formato argentino
    clean = re.sub(r'[^\d.,]', '', price_str)
    # Si tiene punto como separador de miles
    if '.' in clean and ',' in clean:
        clean = clean.replace('.', '').replace(',', '.')
    elif ',' in clean:
        clean = clean.replace(',', '.')
    try:
        return float(clean)
    except:
        return None


def retry_api_call(func, *args, max_retries=3, delay=5, **kwargs):
    """Ejecuta una llamada API con reintentos"""
    for attempt in range(max_retries):
        try:
            result = func(*args, **kwargs)
            return result
        except Exception as e:
            if attempt < max_retries - 1:
                wait_time = delay * (attempt + 1)
                print(f"    ⏳ Reintentando en {wait_time}s... (intento {attempt + 2}/{max_retries})")
                time.sleep(wait_time)
            else:
                raise e
    return None


def get_or_create_category(cat_name, parent_id=None):
    """Obtiene o crea una categoría en WooCommerce"""
    cache_key = f"{parent_id}_{cat_name}"
    if cache_key in category_cache:
        return category_cache[cache_key]
    
    # Buscar categoría existente
    params = {"search": cat_name, "per_page": 100}
    if parent_id:
        params["parent"] = parent_id
    
    response = wcapi.get("products/categories", params=params)
    if response.status_code == 200:
        categories = response.json()
        for cat in categories:
            if cat["name"].lower() == cat_name.lower():
                category_cache[cache_key] = cat["id"]
                print(f"  ✓ Categoría existente: {cat_name} (ID: {cat['id']})")
                return cat["id"]
    
    # Crear categoría
    data = {"name": cat_name}
    if parent_id:
        data["parent"] = parent_id
    
    response = wcapi.post("products/categories", data)
    if response.status_code == 201:
        cat_id = response.json()["id"]
        category_cache[cache_key] = cat_id
        print(f"  + Categoría creada: {cat_name} (ID: {cat_id})")
        return cat_id
    else:
        print(f"  ✗ Error creando categoría {cat_name}: {response.text}")
        return None


def upload_placeholder_image():
    """Sube la imagen placeholder y retorna su ID"""
    global placeholder_image_id
    if placeholder_image_id:
        return placeholder_image_id
    
    print("📷 Subiendo imagen placeholder...")
    
    with open(PLACEHOLDER_IMAGE, 'rb') as img:
        files = {
            'file': ('producto-2026-vivero-los-cocos.png', img, 'image/png')
        }
        headers = {
            'Content-Disposition': 'attachment; filename=producto-2026-vivero-los-cocos.png'
        }
        
        response = requests.post(
            WP_MEDIA_URL,
            auth=(WP_USER, WP_APP_PASS),
            files=files,
            headers=headers
        )
        
        if response.status_code == 201:
            placeholder_image_id = response.json()["id"]
            print(f"  ✓ Imagen subida (ID: {placeholder_image_id})")
            return placeholder_image_id
        else:
            print(f"  ✗ Error subiendo imagen: {response.status_code} - {response.text[:200]}")
            return None


def generate_slug(name, variant):
    """Genera un slug único para el producto"""
    text = f"{name}-{variant}" if variant and variant != '-' else name
    slug = text.lower()
    slug = re.sub(r'[^a-z0-9]+', '-', slug)
    slug = slug.strip('-')
    return slug


def improve_description(short_desc, long_desc, name, variant):
    """Mejora la descripción si es insuficiente"""
    if long_desc and len(long_desc) > 50:
        return long_desc
    
    # Generar descripción mejorada basada en el nombre
    product_name = f"{name} {variant}".strip() if variant and variant != '-' else name
    
    # Descripciones por tipo de producto
    if "Glacoxan" in name:
        return f"{product_name} - Producto profesional para sanidad vegetal. Aplicar según indicaciones del marbete. Consulte con nuestros expertos para uso correcto."
    elif "Hormiguicida" in name or "hormiguicida" in name.lower():
        return f"{product_name} para control efectivo de hormigas. Producto de alta eficacia para uso doméstico y jardín."
    elif "Humus" in name:
        return f"{product_name} - Fertilizante orgánico de primera calidad. Mejora la estructura del suelo y aporta nutrientes esenciales para tus plantas."
    elif "Perlita" in name:
        return f"{product_name} - Mineral volcánico expandido que mejora la aireación y drenaje del sustrato. Evita la compactación del suelo."
    elif "Leca" in name:
        return f"{product_name} - Arcilla expandida ideal para drenaje en macetas o decoración de superficie. Material duradero y reutilizable."
    elif "Maceta" in name:
        material = "plástico resistente" if "Plástico" in name else "cemento" if "Cemento" in name else "fibrocemento" if "Fibrocemento" in name else "barro cocido terracota"
        return f"{product_name} - Contenedor de {material} para tus plantas. Ideal para cultivo y decoración."
    elif "Plato" in name:
        return f"{product_name} - Accesorio para recolección de agua. Protege superficies y mantiene la humedad."
    else:
        return short_desc or f"{product_name} - Producto de calidad para tu jardín. Consulta disponibilidad en Vivero Los Cocos."


def create_product(row, image_id):
    """Crea un producto en WooCommerce"""
    cat_principal = row[0].strip()
    cat_secundaria = row[1].strip()
    nombre = row[2].strip()
    variante = row[3].strip()
    sku = row[4].strip() if row[4] != '-' else None
    precio = parse_price(row[5])
    desc_corta = row[6].strip()
    desc_larga = row[7].strip()
    
    # Generar nombre completo
    product_name = f"{nombre} {variante}".strip() if variante and variante != '-' else nombre
    
    # Obtener/crear categorías
    print(f"\n📦 Procesando: {product_name}")
    
    cat_principal_id = get_or_create_category(cat_principal)
    cat_secundaria_id = get_or_create_category(cat_secundaria, cat_principal_id) if cat_secundaria else None
    
    categories = []
    if cat_principal_id:
        categories.append({"id": cat_principal_id})
    if cat_secundaria_id:
        categories.append({"id": cat_secundaria_id})
    
    # Mejorar descripción
    description = improve_description(desc_corta, desc_larga, nombre, variante)
    
    # Datos del producto
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
        "slug": generate_slug(nombre, variante)
    }
    
    # Agregar imagen
    if image_id:
        product_data["images"] = [{"id": image_id}]
    
    # Crear producto
    response = wcapi.post("products", product_data)
    
    if response.status_code == 201:
        prod = response.json()
        print(f"  ✓ Creado: {product_name} (ID: {prod['id']}, Precio: ${precio})")
        return True
    else:
        error = response.json() if response.status_code < 500 else response.text[:200]
        print(f"  ✗ Error: {error}")
        return False


def main():
    print("=" * 60)
    print("🌿 CARGA DE PRODUCTOS 2026 - VIVERO LOS COCOS")
    print("=" * 60)
    
    # Verificar conexión
    print("\n🔌 Verificando conexión WooCommerce...")
    test = wcapi.get("products", params={"per_page": 1})
    if test.status_code != 200:
        print(f"✗ Error de conexión: {test.status_code}")
        return
    print("  ✓ Conexión establecida")
    
    # Subir imagen placeholder
    image_id = upload_placeholder_image()
    
    # Leer CSV
    csv_file = Path("/Applications/um/vivero/productos2026.csv")
    
    success = 0
    errors = 0
    
    with open(csv_file, 'r', encoding='utf-8') as f:
        reader = csv.reader(f)
        header = next(reader)  # Saltar encabezado
        
        for row in reader:
            if len(row) >= 8 and row[0].strip():  # Verificar fila válida
                try:
                    if create_product(row, image_id):
                        success += 1
                    else:
                        errors += 1
                except Exception as e:
                    print(f"  ✗ Excepción: {e}")
                    errors += 1
    
    print("\n" + "=" * 60)
    print(f"📊 RESUMEN: {success} productos creados, {errors} errores")
    print("=" * 60)


if __name__ == "__main__":
    main()
