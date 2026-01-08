#!/usr/bin/env python3
"""
Scraper Profundo para Vivero Los Cocos
Extrae productos de Glacoxan y Plantas Faitful
Genera Excel consolidado con todos los campos
"""

import os
import re
import json
import time
import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin, urlparse
from pathlib import Path
from datetime import datetime

# Configuración
BASE_DIR = Path(__file__).parent.parent
OUTPUT_DIR = BASE_DIR / "scraped_data"
IMAGES_DIR = OUTPUT_DIR / "images"
OUTPUT_DIR.mkdir(exist_ok=True)
IMAGES_DIR.mkdir(exist_ok=True)

HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    'Accept-Language': 'es-AR,es;q=0.9,en;q=0.8',
}

def get_soup(url, retries=3):
    """Obtiene el BeautifulSoup de una URL con reintentos"""
    for attempt in range(retries):
        try:
            response = requests.get(url, headers=HEADERS, timeout=30)
            response.raise_for_status()
            return BeautifulSoup(response.content, 'html.parser')
        except Exception as e:
            print(f"  ⚠️ Intento {attempt+1}/{retries} fallido para {url}: {e}")
            time.sleep(2)
    return None

def download_image(url, filename, subdir=""):
    """Descarga una imagen y la guarda con nombre limpio"""
    try:
        img_dir = IMAGES_DIR / subdir if subdir else IMAGES_DIR
        img_dir.mkdir(exist_ok=True)
        
        # Limpiar nombre de archivo
        clean_name = re.sub(r'[^\w\-.]', '_', filename)
        filepath = img_dir / clean_name
        
        if filepath.exists():
            print(f"  ✓ Imagen ya existe: {clean_name}")
            return str(filepath)
        
        response = requests.get(url, headers=HEADERS, timeout=30)
        response.raise_for_status()
        
        with open(filepath, 'wb') as f:
            f.write(response.content)
        
        print(f"  📷 Imagen descargada: {clean_name}")
        return str(filepath)
    except Exception as e:
        print(f"  ❌ Error descargando imagen {url}: {e}")
        return None

def clean_text(text):
    """Limpia texto de espacios extra y caracteres especiales"""
    if not text:
        return ""
    return re.sub(r'\s+', ' ', text.strip())

# ============================================
# SCRAPER DE GLACOXAN
# ============================================
def scrape_glacoxan():
    """Scraper completo para glacoxan.com"""
    print("\n" + "="*60)
    print("🧪 INICIANDO SCRAPING DE GLACOXAN")
    print("="*60)
    
    products = []
    base_url = "https://www.glacoxan.com"
    
    # Página principal de soluciones
    solutions_url = f"{base_url}/soluciones/"
    print(f"\n📍 Obteniendo categorías de: {solutions_url}")
    
    soup = get_soup(solutions_url)
    if not soup:
        print("❌ No se pudo acceder a la página de soluciones")
        return products
    
    # Buscar todas las categorías de productos
    categories = []
    
    # Método 1: Buscar enlaces a categorías en el menú
    menu_links = soup.select('nav a, .menu a, .nav-link, a[href*="/producto/"], a[href*="categoria"], a[href*="soluciones"]')
    for link in menu_links:
        href = link.get('href', '')
        if href and '/producto/' in href or 'categoria' in href.lower():
            full_url = urljoin(base_url, href)
            if full_url not in [c['url'] for c in categories]:
                categories.append({
                    'name': clean_text(link.get_text()),
                    'url': full_url
                })
    
    # Método 2: Buscar cards de productos
    product_cards = soup.select('.product, .producto, article, .card, [class*="product"], [class*="item"]')
    for card in product_cards:
        link = card.find('a', href=True)
        if link:
            href = link.get('href', '')
            if '/producto/' in href:
                full_url = urljoin(base_url, href)
                if full_url not in [c['url'] for c in categories]:
                    title = card.find(['h2', 'h3', 'h4', '.title', '.name'])
                    categories.append({
                        'name': clean_text(title.get_text()) if title else clean_text(link.get_text()),
                        'url': full_url
                    })
    
    print(f"📦 Encontradas {len(categories)} URLs de productos/categorías")
    
    # Procesar cada producto
    for i, cat in enumerate(categories, 1):
        print(f"\n[{i}/{len(categories)}] Procesando: {cat['name']}")
        product = scrape_glacoxan_product(cat['url'], base_url)
        if product:
            products.append(product)
        time.sleep(1)  # Respetar al servidor
    
    # También buscar directamente productos conocidos
    known_products = [
        "jardin-fungoxan", "jardin-glacoxan-e", "jardin-glacoxan-h",
        "jardin-glacoxan-ciper", "jardin-glacoxan-d-sist", "jardin-glacoxan-avam",
        "jardin-glacoxan-imida", "jardin-glacoxan-oil", "jardin-glacoxan-p",
        "jardin-glacoxan-total", "jardin-glacoxan-mcpa",
        "fertifox-floracion", "fertifox-potenciado", "fertifox-follaje", "fertifox-lustre"
    ]
    
    for slug in known_products:
        url = f"{base_url}/producto/{slug}/"
        if url not in [p.get('url') for p in products]:
            print(f"\n🔍 Intentando producto conocido: {slug}")
            product = scrape_glacoxan_product(url, base_url)
            if product:
                products.append(product)
            time.sleep(1)
    
    print(f"\n✅ Total productos Glacoxan extraídos: {len(products)}")
    return products

def scrape_glacoxan_product(url, base_url):
    """Extrae información detallada de un producto Glacoxan"""
    soup = get_soup(url)
    if not soup:
        return None
    
    product = {
        'source': 'Glacoxan',
        'url': url,
        'name': '',
        'category': '',
        'subcategory': '',
        'short_description': '',
        'full_description': '',
        'composition': '',
        'benefits': '',
        'pests_controlled': '',
        'dose': '',
        'application': '',
        'presentation': '',
        'warnings': '',
        'image_url': '',
        'image_local': '',
        'pdf_url': '',
        'technical_table': {}
    }
    
    # Nombre del producto
    title = soup.find(['h1', '.product-title', '.entry-title', '[class*="title"]'])
    if title:
        product['name'] = clean_text(title.get_text())
    
    # Imagen principal
    img = soup.find('img', {'class': lambda x: x and any(k in str(x).lower() for k in ['product', 'main', 'featured'])})
    if not img:
        img = soup.select_one('.product-image img, .woocommerce-product-gallery img, article img, .entry-content img')
    if img:
        img_url = img.get('src') or img.get('data-src')
        if img_url:
            product['image_url'] = urljoin(base_url, img_url)
            # Descargar imagen
            ext = Path(urlparse(img_url).path).suffix or '.jpg'
            filename = f"glacoxan_{product['name'].replace(' ', '_')}{ext}"
            product['image_local'] = download_image(product['image_url'], filename, "glacoxan")
    
    # Descripción
    desc_selectors = [
        '.product-description', '.description', '.entry-content',
        '.woocommerce-product-details__short-description', 'article p',
        '[class*="description"]', '[class*="content"]'
    ]
    for selector in desc_selectors:
        desc = soup.select_one(selector)
        if desc:
            product['full_description'] = clean_text(desc.get_text())
            break
    
    # Buscar tablas técnicas
    tables = soup.find_all('table')
    for table in tables:
        rows = table.find_all('tr')
        for row in rows:
            cells = row.find_all(['td', 'th'])
            if len(cells) >= 2:
                key = clean_text(cells[0].get_text()).lower()
                value = clean_text(cells[1].get_text())
                product['technical_table'][key] = value
                
                # Mapear a campos específicos
                if 'composici' in key:
                    product['composition'] = value
                elif 'dosis' in key or 'dose' in key:
                    product['dose'] = value
                elif 'beneficio' in key:
                    product['benefits'] = value
                elif 'plaga' in key or 'control' in key:
                    product['pests_controlled'] = value
                elif 'aplicaci' in key or 'uso' in key:
                    product['application'] = value
                elif 'present' in key or 'envase' in key:
                    product['presentation'] = value
                elif 'precauci' in key or 'advertenc' in key:
                    product['warnings'] = value
    
    # Buscar PDFs
    pdf_links = soup.find_all('a', href=lambda x: x and '.pdf' in x.lower())
    if pdf_links:
        product['pdf_url'] = urljoin(base_url, pdf_links[0]['href'])
    
    # Categoría desde breadcrumbs o URL
    breadcrumb = soup.select_one('.breadcrumb, .breadcrumbs, [class*="breadcrumb"]')
    if breadcrumb:
        crumbs = breadcrumb.find_all('a')
        if len(crumbs) >= 2:
            product['category'] = clean_text(crumbs[-2].get_text())
        if len(crumbs) >= 3:
            product['subcategory'] = clean_text(crumbs[-1].get_text())
    
    if not product['category']:
        # Inferir de la URL
        path_parts = urlparse(url).path.strip('/').split('/')
        if len(path_parts) >= 2:
            product['category'] = path_parts[-2].replace('-', ' ').title()
    
    if product['name']:
        print(f"  ✅ Extraído: {product['name']}")
        return product
    return None

# ============================================
# SCRAPER DE PLANTAS FAITFUL
# ============================================
def scrape_plantas_faitful():
    """Scraper completo para plantasfaitful.com.ar"""
    print("\n" + "="*60)
    print("🌿 INICIANDO SCRAPING DE PLANTAS FAITFUL")
    print("="*60)
    
    products = []
    base_url = "https://www.plantasfaitful.com.ar"
    
    # Obtener página principal
    soup = get_soup(base_url)
    if not soup:
        print("❌ No se pudo acceder al sitio")
        return products
    
    # Buscar todas las categorías de productos
    categories = []
    
    # Buscar en el menú y páginas de productos
    nav_links = soup.select('nav a, .menu a, .nav-item a, a[href*="producto"], a[href*="categoria"]')
    for link in nav_links:
        href = link.get('href', '')
        if href and ('producto' in href.lower() or 'categoria' in href.lower() or 'shop' in href.lower()):
            full_url = urljoin(base_url, href)
            if full_url not in [c['url'] for c in categories] and base_url in full_url:
                categories.append({
                    'name': clean_text(link.get_text()),
                    'url': full_url
                })
    
    # También buscar la tienda directamente
    shop_urls = [
        f"{base_url}/productos/",
        f"{base_url}/tienda/",
        f"{base_url}/shop/",
        f"{base_url}/catalogo/"
    ]
    
    for shop_url in shop_urls:
        soup_shop = get_soup(shop_url)
        if soup_shop:
            print(f"📍 Encontrada página de productos: {shop_url}")
            # Buscar productos en esta página
            product_links = soup_shop.select('a[href*="/producto"], .product a, article a, .item a')
            for link in product_links:
                href = link.get('href', '')
                if href:
                    full_url = urljoin(base_url, href)
                    if full_url not in [c['url'] for c in categories]:
                        categories.append({
                            'name': clean_text(link.get_text()) or urlparse(full_url).path.split('/')[-2],
                            'url': full_url
                        })
    
    print(f"📦 Encontradas {len(categories)} URLs de productos")
    
    # Procesar cada producto
    for i, cat in enumerate(categories, 1):
        print(f"\n[{i}/{len(categories)}] Procesando: {cat['name']}")
        product = scrape_faitful_product(cat['url'], base_url)
        if product:
            products.append(product)
        time.sleep(1)
    
    print(f"\n✅ Total productos Plantas Faitful extraídos: {len(products)}")
    return products

def scrape_faitful_product(url, base_url):
    """Extrae información detallada de un producto Plantas Faitful"""
    soup = get_soup(url)
    if not soup:
        return None
    
    product = {
        'source': 'Plantas Faitful',
        'url': url,
        'name': '',
        'category': '',
        'subcategory': '',
        'short_description': '',
        'full_description': '',
        'composition': '',
        'benefits': '',
        'application': '',
        'presentation': '',
        'price': '',
        'image_url': '',
        'image_local': '',
        'technical_table': {}
    }
    
    # Nombre del producto
    title = soup.find(['h1', '.product-title', '.entry-title'])
    if title:
        product['name'] = clean_text(title.get_text())
    
    # Imagen principal
    img = soup.select_one('.product-image img, .woocommerce-product-gallery img, article img, .main-image img')
    if img:
        img_url = img.get('src') or img.get('data-src')
        if img_url:
            product['image_url'] = urljoin(base_url, img_url)
            ext = Path(urlparse(img_url).path).suffix or '.jpg'
            filename = f"faitful_{product['name'].replace(' ', '_')}{ext}"
            product['image_local'] = download_image(product['image_url'], filename, "faitful")
    
    # Descripción
    desc = soup.select_one('.product-description, .description, .entry-content, [class*="description"]')
    if desc:
        product['full_description'] = clean_text(desc.get_text())
    
    # Precio
    price = soup.select_one('.price, .woocommerce-Price-amount, [class*="price"]')
    if price:
        product['price'] = clean_text(price.get_text())
    
    # Tablas técnicas
    tables = soup.find_all('table')
    for table in tables:
        rows = table.find_all('tr')
        for row in rows:
            cells = row.find_all(['td', 'th'])
            if len(cells) >= 2:
                key = clean_text(cells[0].get_text()).lower()
                value = clean_text(cells[1].get_text())
                product['technical_table'][key] = value
    
    # Categoría
    breadcrumb = soup.select_one('.breadcrumb, .breadcrumbs')
    if breadcrumb:
        crumbs = breadcrumb.find_all('a')
        if len(crumbs) >= 2:
            product['category'] = clean_text(crumbs[-2].get_text())
    
    if product['name']:
        print(f"  ✅ Extraído: {product['name']}")
        return product
    return None

# ============================================
# GENERADOR DE EXCEL
# ============================================
def generate_excel(glacoxan_products, faitful_products):
    """Genera archivo Excel con todos los productos"""
    print("\n" + "="*60)
    print("📊 GENERANDO EXCEL CONSOLIDADO")
    print("="*60)
    
    try:
        import openpyxl
        from openpyxl.styles import Font, Fill, PatternFill, Alignment, Border, Side
        from openpyxl.utils import get_column_letter
    except ImportError:
        print("⚠️ Instalando openpyxl...")
        os.system("pip install openpyxl")
        import openpyxl
        from openpyxl.styles import Font, PatternFill, Alignment, Border, Side
        from openpyxl.utils import get_column_letter
    
    wb = openpyxl.Workbook()
    
    # Estilos
    header_font = Font(bold=True, color="FFFFFF", size=11)
    header_fill = PatternFill(start_color="2D5A3D", end_color="2D5A3D", fill_type="solid")
    alt_fill = PatternFill(start_color="F5F5F5", end_color="F5F5F5", fill_type="solid")
    border = Border(
        left=Side(style='thin'),
        right=Side(style='thin'),
        top=Side(style='thin'),
        bottom=Side(style='thin')
    )
    
    # Columnas
    columns = [
        'Fuente', 'Nombre', 'Categoría', 'Subcategoría', 
        'Descripción Corta', 'Descripción Completa',
        'Composición', 'Beneficios', 'Plagas Controladas',
        'Dosis', 'Aplicación', 'Presentación', 'Precio',
        'Advertencias', 'URL Imagen', 'Imagen Local', 'URL PDF', 'URL Producto'
    ]
    
    # Hoja de Glacoxan
    ws_glacoxan = wb.active
    ws_glacoxan.title = "Glacoxan"
    
    # Headers
    for col, header in enumerate(columns, 1):
        cell = ws_glacoxan.cell(row=1, column=col, value=header)
        cell.font = header_font
        cell.fill = header_fill
        cell.alignment = Alignment(horizontal='center', vertical='center')
        cell.border = border
    
    # Datos Glacoxan
    for row, product in enumerate(glacoxan_products, 2):
        data = [
            product.get('source', ''),
            product.get('name', ''),
            product.get('category', ''),
            product.get('subcategory', ''),
            product.get('short_description', ''),
            product.get('full_description', ''),
            product.get('composition', ''),
            product.get('benefits', ''),
            product.get('pests_controlled', ''),
            product.get('dose', ''),
            product.get('application', ''),
            product.get('presentation', ''),
            product.get('price', ''),
            product.get('warnings', ''),
            product.get('image_url', ''),
            product.get('image_local', ''),
            product.get('pdf_url', ''),
            product.get('url', '')
        ]
        for col, value in enumerate(data, 1):
            cell = ws_glacoxan.cell(row=row, column=col, value=str(value) if value else '')
            cell.border = border
            cell.alignment = Alignment(vertical='top', wrap_text=True)
            if row % 2 == 0:
                cell.fill = alt_fill
    
    # Hoja de Plantas Faitful
    ws_faitful = wb.create_sheet("Plantas Faitful")
    
    # Headers
    for col, header in enumerate(columns, 1):
        cell = ws_faitful.cell(row=1, column=col, value=header)
        cell.font = header_font
        cell.fill = PatternFill(start_color="4A7C59", end_color="4A7C59", fill_type="solid")
        cell.alignment = Alignment(horizontal='center', vertical='center')
        cell.border = border
    
    # Datos Faitful
    for row, product in enumerate(faitful_products, 2):
        data = [
            product.get('source', ''),
            product.get('name', ''),
            product.get('category', ''),
            product.get('subcategory', ''),
            product.get('short_description', ''),
            product.get('full_description', ''),
            product.get('composition', ''),
            product.get('benefits', ''),
            '',  # pests_controlled
            '',  # dose
            product.get('application', ''),
            product.get('presentation', ''),
            product.get('price', ''),
            '',  # warnings
            product.get('image_url', ''),
            product.get('image_local', ''),
            '',  # pdf_url
            product.get('url', '')
        ]
        for col, value in enumerate(data, 1):
            cell = ws_faitful.cell(row=row, column=col, value=str(value) if value else '')
            cell.border = border
            cell.alignment = Alignment(vertical='top', wrap_text=True)
            if row % 2 == 0:
                cell.fill = alt_fill
    
    # Hoja consolidada
    ws_all = wb.create_sheet("TODOS LOS PRODUCTOS")
    all_products = glacoxan_products + faitful_products
    
    # Headers
    for col, header in enumerate(columns, 1):
        cell = ws_all.cell(row=1, column=col, value=header)
        cell.font = header_font
        cell.fill = PatternFill(start_color="1E3A5F", end_color="1E3A5F", fill_type="solid")
        cell.alignment = Alignment(horizontal='center', vertical='center')
        cell.border = border
    
    # Todos los datos
    for row, product in enumerate(all_products, 2):
        data = [
            product.get('source', ''),
            product.get('name', ''),
            product.get('category', ''),
            product.get('subcategory', ''),
            product.get('short_description', ''),
            product.get('full_description', ''),
            product.get('composition', ''),
            product.get('benefits', ''),
            product.get('pests_controlled', ''),
            product.get('dose', ''),
            product.get('application', ''),
            product.get('presentation', ''),
            product.get('price', ''),
            product.get('warnings', ''),
            product.get('image_url', ''),
            product.get('image_local', ''),
            product.get('pdf_url', ''),
            product.get('url', '')
        ]
        for col, value in enumerate(data, 1):
            cell = ws_all.cell(row=row, column=col, value=str(value) if value else '')
            cell.border = border
            cell.alignment = Alignment(vertical='top', wrap_text=True)
            if row % 2 == 0:
                cell.fill = alt_fill
    
    # Ajustar anchos de columna
    for ws in [ws_glacoxan, ws_faitful, ws_all]:
        for col in range(1, len(columns) + 1):
            ws.column_dimensions[get_column_letter(col)].width = 20
        ws.column_dimensions['F'].width = 50  # Descripción completa
        ws.column_dimensions['B'].width = 30  # Nombre
    
    # Guardar
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    filename = OUTPUT_DIR / f"catalogo_vivero_completo_{timestamp}.xlsx"
    wb.save(filename)
    
    print(f"\n✅ Excel generado: {filename}")
    print(f"   📄 Glacoxan: {len(glacoxan_products)} productos")
    print(f"   📄 Plantas Faitful: {len(faitful_products)} productos")
    print(f"   📄 Total consolidado: {len(all_products)} productos")
    
    return str(filename)

# ============================================
# MAIN
# ============================================
def main():
    print("\n" + "🌿"*30)
    print("    SCRAPER PROFUNDO - VIVERO LOS COCOS")
    print("    Extracción de catálogo completo")
    print("🌿"*30)
    
    start_time = time.time()
    
    # Scraping
    glacoxan_products = scrape_glacoxan()
    faitful_products = scrape_plantas_faitful()
    
    # Guardar JSON de respaldo
    json_file = OUTPUT_DIR / "productos_raw.json"
    with open(json_file, 'w', encoding='utf-8') as f:
        json.dump({
            'glacoxan': glacoxan_products,
            'plantas_faitful': faitful_products,
            'timestamp': datetime.now().isoformat()
        }, f, ensure_ascii=False, indent=2)
    print(f"\n💾 JSON de respaldo guardado: {json_file}")
    
    # Generar Excel
    excel_file = generate_excel(glacoxan_products, faitful_products)
    
    elapsed = time.time() - start_time
    print(f"\n⏱️ Tiempo total: {elapsed/60:.1f} minutos")
    print(f"📁 Archivos generados en: {OUTPUT_DIR}")
    
    return excel_file

if __name__ == "__main__":
    main()
