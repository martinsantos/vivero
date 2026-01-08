#!/usr/bin/env python3
"""
Scraper Específico para Glacoxan con headers de navegador real
Bypass de protección anti-bot
"""

import requests
from bs4 import BeautifulSoup
import json
import time
import re
from pathlib import Path
from urllib.parse import urljoin
import openpyxl
from openpyxl.styles import Font, Alignment, PatternFill, Border, Side
from openpyxl.utils import get_column_letter
from datetime import datetime

# Configuración
BASE_URL = "https://www.glacoxan.com"
OUTPUT_DIR = Path("/Applications/um/vivero/scraped_data")
IMAGES_DIR = OUTPUT_DIR / "images" / "glacoxan"

# Headers realistas de navegador
HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'Accept-Language': 'es-AR,es;q=0.9,en;q=0.8',
    'Accept-Encoding': 'gzip, deflate, br',
    'DNT': '1',
    'Connection': 'keep-alive',
    'Upgrade-Insecure-Requests': '1',
    'Sec-Fetch-Dest': 'document',
    'Sec-Fetch-Mode': 'navigate',
    'Sec-Fetch-Site': 'none',
    'Sec-Fetch-User': '?1',
    'Cache-Control': 'max-age=0',
}

# Categorías conocidas del sitio (obtenidas del navegador)
CATEGORIES = {
    'jardin': {
        'name': 'Sanidad Vegetal - Jardín',
        'url': f'{BASE_URL}/jardin/',
        'subcategories': ['Acaricidas', 'Funguicida', 'Herbicidas', 'Hormiguicida', 'Insecticidas', 'Molusquicidas', 'Orgánico']
    },
    'hogar': {
        'name': 'Sanidad Ambiental - Hogar',
        'url': f'{BASE_URL}/hogar/',
        'subcategories': ['Biológico', 'Profesional', 'Insecticidas', 'Rodenticidas']
    },
    'mascotas': {
        'name': 'Sanidad Animal - Mascotas',
        'url': f'{BASE_URL}/mascotas/',
        'subcategories': ['Antiparasitarios', 'Higiene']
    }
}


def get_session():
    """Crear sesión con cookies persistentes"""
    session = requests.Session()
    session.headers.update(HEADERS)
    return session


def get_soup(session, url, retries=3):
    """Obtener BeautifulSoup con reintentos"""
    for attempt in range(retries):
        try:
            # Delay entre peticiones para evitar bloqueo
            if attempt > 0:
                time.sleep(2 ** attempt)
            
            response = session.get(url, timeout=30)
            response.raise_for_status()
            return BeautifulSoup(response.text, 'html.parser')
        except requests.exceptions.RequestException as e:
            print(f"  ⚠️ Intento {attempt + 1}/{retries} fallido para {url}: {e}")
            if attempt == retries - 1:
                return None
    return None


def download_image(session, url, product_name):
    """Descargar imagen del producto"""
    if not url:
        return None
    
    try:
        IMAGES_DIR.mkdir(parents=True, exist_ok=True)
        
        # Limpiar nombre de archivo
        clean_name = re.sub(r'[^\w\s-]', '', product_name)
        clean_name = re.sub(r'\s+', '_', clean_name)[:50]
        
        ext = Path(url).suffix.split('?')[0] or '.jpg'
        filename = f"glacoxan_{clean_name}{ext}"
        filepath = IMAGES_DIR / filename
        
        if filepath.exists():
            return str(filepath)
        
        response = session.get(url, timeout=30)
        response.raise_for_status()
        
        with open(filepath, 'wb') as f:
            f.write(response.content)
        
        return str(filepath)
    except Exception as e:
        print(f"  ⚠️ Error descargando imagen {url}: {e}")
        return None


def extract_product_links(session, category_url):
    """Extraer todos los enlaces de productos de una categoría"""
    soup = get_soup(session, category_url)
    if not soup:
        return []
    
    product_links = []
    
    # Buscar productos en la página
    products = soup.select('.product, .woocommerce-loop-product__link, article.product, .products li a')
    
    for product in products:
        if product.name == 'a':
            href = product.get('href', '')
        else:
            link = product.find('a', href=True)
            href = link.get('href', '') if link else ''
        
        if href and '/producto/' in href:
            if href not in product_links:
                product_links.append(href)
    
    # También buscar en enlaces directos
    for link in soup.find_all('a', href=True):
        href = link.get('href', '')
        if '/producto/' in href and href not in product_links:
            product_links.append(href)
    
    return list(set(product_links))


def scrape_product_detail(session, url, category_name, subcategory=''):
    """Scrapear detalles de un producto específico"""
    soup = get_soup(session, url)
    if not soup:
        return None
    
    product = {
        'url': url,
        'categoria': category_name,
        'subcategoria': subcategory,
        'nombre': '',
        'descripcion_corta': '',
        'descripcion_larga': '',
        'principio_activo': '',
        'clase': '',
        'formulacion': '',
        'plagas': '',
        'presentaciones': '',
        'recomendaciones_uso': '',
        'imagen_url': '',
        'imagen_local': '',
        'pdf_hoja_seguridad': '',
        'pdf_senasa': ''
    }
    
    # Nombre del producto
    title = soup.select_one('h1.product_title, h1.entry-title, .product-title h1')
    if title:
        product['nombre'] = title.get_text(strip=True)
    
    # Descripción corta
    short_desc = soup.select_one('.woocommerce-product-details__short-description, .short-description')
    if short_desc:
        product['descripcion_corta'] = short_desc.get_text(strip=True)
    
    # Descripción larga
    description = soup.select_one('#tab-description, .woocommerce-Tabs-panel--description')
    if description:
        product['descripcion_larga'] = description.get_text(strip=True)
    
    # Imagen principal
    img = soup.select_one('.woocommerce-product-gallery__image img, .product-images img, img.wp-post-image')
    if img:
        img_url = img.get('src') or img.get('data-src')
        if img_url:
            product['imagen_url'] = img_url
            product['imagen_local'] = download_image(session, img_url, product['nombre'] or 'producto')
    
    # Tabla de información adicional
    info_tab = soup.select_one('#tab-additional_information, .woocommerce-Tabs-panel--additional_information')
    if info_tab:
        rows = info_tab.select('tr, .woocommerce-product-attributes-item')
        for row in rows:
            label = row.select_one('th, .woocommerce-product-attributes-item__label')
            value = row.select_one('td, .woocommerce-product-attributes-item__value')
            if label and value:
                label_text = label.get_text(strip=True).lower()
                value_text = value.get_text(strip=True)
                
                if 'clase' in label_text:
                    product['clase'] = value_text
                elif 'formulación' in label_text or 'formulacion' in label_text:
                    product['formulacion'] = value_text
                elif 'principio' in label_text or 'activo' in label_text:
                    product['principio_activo'] = value_text
                elif 'plaga' in label_text:
                    product['plagas'] = value_text
                elif 'presentaci' in label_text:
                    product['presentaciones'] = value_text
    
    # Recomendaciones de uso
    reco_tab = soup.select_one('#tab-recomendaciones, .tab-recomendaciones')
    if reco_tab:
        product['recomendaciones_uso'] = reco_tab.get_text(strip=True)
    
    # Buscar PDFs
    for link in soup.find_all('a', href=True):
        href = link.get('href', '').lower()
        text = link.get_text(strip=True).lower()
        
        if '.pdf' in href:
            if 'seguridad' in text or 'hoja' in text:
                product['pdf_hoja_seguridad'] = link.get('href')
            elif 'senasa' in text or 'anmat' in text:
                product['pdf_senasa'] = link.get('href')
    
    return product


def scrape_category(session, category_key, category_info):
    """Scrapear todos los productos de una categoría"""
    products = []
    
    print(f"\n📂 Categoría: {category_info['name']}")
    print(f"   URL: {category_info['url']}")
    
    # Obtener enlaces de productos
    product_links = extract_product_links(session, category_info['url'])
    print(f"   Productos encontrados: {len(product_links)}")
    
    for i, url in enumerate(product_links, 1):
        print(f"\n   [{i}/{len(product_links)}] Procesando: {url.split('/')[-2]}")
        
        time.sleep(0.5)  # Delay entre productos
        
        product = scrape_product_detail(session, url, category_info['name'])
        if product:
            products.append(product)
            print(f"      ✅ {product['nombre']}")
        else:
            print(f"      ❌ Error extrayendo producto")
    
    return products


def generate_excel(products):
    """Generar Excel profesional con los productos"""
    wb = openpyxl.Workbook()
    ws = wb.active
    ws.title = "Glacoxan Productos"
    
    # Estilos
    header_font = Font(bold=True, color="FFFFFF", size=11)
    header_fill = PatternFill(start_color="1a472a", end_color="1a472a", fill_type="solid")
    header_alignment = Alignment(horizontal="center", vertical="center", wrap_text=True)
    cell_alignment = Alignment(vertical="top", wrap_text=True)
    thin_border = Border(
        left=Side(style='thin'),
        right=Side(style='thin'),
        top=Side(style='thin'),
        bottom=Side(style='thin')
    )
    
    # Headers
    headers = [
        'Nombre', 'Categoría', 'Subcategoría', 'Principio Activo', 'Clase',
        'Formulación', 'Plagas/Usos', 'Presentaciones', 'Descripción Corta',
        'Descripción Larga', 'Recomendaciones', 'URL', 'Imagen URL',
        'PDF Seguridad', 'PDF SENASA'
    ]
    
    for col, header in enumerate(headers, 1):
        cell = ws.cell(row=1, column=col, value=header)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = header_alignment
        cell.border = thin_border
    
    # Datos
    for row, product in enumerate(products, 2):
        data = [
            product.get('nombre', ''),
            product.get('categoria', ''),
            product.get('subcategoria', ''),
            product.get('principio_activo', ''),
            product.get('clase', ''),
            product.get('formulacion', ''),
            product.get('plagas', ''),
            product.get('presentaciones', ''),
            product.get('descripcion_corta', ''),
            product.get('descripcion_larga', '')[:500] if product.get('descripcion_larga') else '',
            product.get('recomendaciones_uso', '')[:500] if product.get('recomendaciones_uso') else '',
            product.get('url', ''),
            product.get('imagen_url', ''),
            product.get('pdf_hoja_seguridad', ''),
            product.get('pdf_senasa', '')
        ]
        
        for col, value in enumerate(data, 1):
            cell = ws.cell(row=row, column=col, value=value)
            cell.alignment = cell_alignment
            cell.border = thin_border
    
    # Ajustar anchos de columna
    column_widths = [30, 25, 20, 20, 15, 15, 30, 20, 40, 50, 50, 40, 40, 40, 40]
    for i, width in enumerate(column_widths, 1):
        ws.column_dimensions[get_column_letter(i)].width = width
    
    # Congelar primera fila
    ws.freeze_panes = 'A2'
    
    # Guardar
    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)
    timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
    filename = OUTPUT_DIR / f'glacoxan_productos_{timestamp}.xlsx'
    wb.save(filename)
    
    return str(filename)


def main():
    print("\n" + "🧪" * 30)
    print("    SCRAPER GLACOXAN V2")
    print("    Con bypass anti-bot")
    print("🧪" * 30 + "\n")
    
    start_time = time.time()
    
    # Crear sesión
    session = get_session()
    
    # Primero, hacer una petición inicial para obtener cookies
    print("🔐 Inicializando sesión...")
    initial_soup = get_soup(session, BASE_URL)
    if not initial_soup:
        print("❌ No se pudo acceder al sitio base")
        return
    
    print("✅ Sesión iniciada correctamente\n")
    time.sleep(1)
    
    all_products = []
    
    # Scrapear cada categoría
    for category_key, category_info in CATEGORIES.items():
        products = scrape_category(session, category_key, category_info)
        all_products.extend(products)
        print(f"\n   📊 Total categoría {category_key}: {len(products)} productos")
        time.sleep(2)  # Pausa entre categorías
    
    print(f"\n{'='*60}")
    print(f"📊 RESUMEN TOTAL")
    print(f"{'='*60}")
    print(f"   Total productos extraídos: {len(all_products)}")
    
    if all_products:
        # Guardar JSON
        json_path = OUTPUT_DIR / 'glacoxan_productos.json'
        with open(json_path, 'w', encoding='utf-8') as f:
            json.dump(all_products, f, ensure_ascii=False, indent=2)
        print(f"   💾 JSON guardado: {json_path}")
        
        # Generar Excel
        excel_path = generate_excel(all_products)
        print(f"   📊 Excel generado: {excel_path}")
    
    elapsed = (time.time() - start_time) / 60
    print(f"\n⏱️ Tiempo total: {elapsed:.1f} minutos")


if __name__ == '__main__':
    main()
