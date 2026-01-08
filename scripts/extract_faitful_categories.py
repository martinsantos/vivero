#!/usr/bin/env python3
"""
Extrae SOLO las categorías únicas de Faitful de forma rápida.
Va por el sitemap y hace sampling rápido para obtener todas las categorías.
"""

import requests
import json
import time
import re
from xml.etree import ElementTree as ET
from bs4 import BeautifulSoup
from collections import defaultdict

HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
}

BASE_URL = "https://www.plantasfaitful.com.ar"
SITEMAP_URL = f"{BASE_URL}/sitemap.xml"


def get_product_urls():
    """Obtiene URLs de productos del sitemap"""
    print("📍 Obteniendo sitemap...")
    response = requests.get(SITEMAP_URL, headers=HEADERS, timeout=30)
    root = ET.fromstring(response.content)
    ns = {'sm': 'http://www.sitemaps.org/schemas/sitemap/0.9'}
    
    urls = []
    for url_elem in root.findall('.//sm:url', ns):
        loc = url_elem.find('sm:loc', ns)
        if loc is not None and '/productos/' in loc.text and '?' not in loc.text:
            urls.append(loc.text)
    
    print(f"✅ {len(urls)} productos encontrados")
    return urls


def extract_category_from_url(url):
    """Extrae categoría desde una página de producto"""
    try:
        response = requests.get(url, headers=HEADERS, timeout=15)
        soup = BeautifulSoup(response.content, 'html.parser')
        
        # Método 1: Breadcrumbs a.crumb
        breadcrumbs = soup.select('a.crumb')
        if breadcrumbs:
            crumbs = [b.get_text().strip() for b in breadcrumbs]
            crumbs = [c for c in crumbs if c.lower() not in ['inicio', 'home', '']]
            if crumbs:
                return crumbs
        
        # Método 2: JSON-LD
        for script in soup.find_all('script', type='application/ld+json'):
            try:
                data = json.loads(script.string)
                if isinstance(data, dict):
                    if data.get('@type') == 'BreadcrumbList':
                        items = data.get('itemListElement', [])
                        names = [i.get('item', {}).get('name', i.get('name', '')) for i in items]
                        names = [n for n in names if n.lower() not in ['inicio', 'home', '']]
                        if names:
                            return names
            except:
                continue
        
        return []
    except Exception as e:
        print(f"  ⚠️ Error: {e}")
        return []


def main():
    print("\n" + "🌿"*30)
    print("  EXTRACTOR DE CATEGORÍAS - PLANTAS FAITFUL")
    print("🌿"*30 + "\n")
    
    urls = get_product_urls()
    
    # Categorías encontradas
    categories = defaultdict(set)  # categoria -> set de subcategorias
    all_paths = set()  # Rutas completas de categorías
    
    total = len(urls)
    
    for i, url in enumerate(urls, 1):
        print(f"\r[{i}/{total}] Procesando...", end="", flush=True)
        
        crumbs = extract_category_from_url(url)
        if crumbs:
            # Guardar ruta completa
            path = " > ".join(crumbs)
            all_paths.add(path)
            
            # Guardar categoría y subcategoría
            if len(crumbs) >= 1:
                cat = crumbs[0]
                if len(crumbs) >= 2:
                    categories[cat].add(crumbs[1])
                else:
                    categories[cat]  # Solo categoría principal
        
        time.sleep(0.3)  # Delay cortés
    
    print("\n\n" + "="*60)
    print("📋 CATEGORÍAS ENCONTRADAS EN FAITFUL")
    print("="*60 + "\n")
    
    # Mostrar categorías ordenadas
    for cat in sorted(categories.keys()):
        subcats = sorted(categories[cat]) if categories[cat] else []
        print(f"\n🏷️  {cat.upper()}")
        if subcats:
            for sub in subcats:
                print(f"    └─ {sub}")
        else:
            print(f"    └─ (sin subcategorías)")
    
    # Lista de rutas completas
    print("\n\n" + "="*60)
    print("📍 RUTAS COMPLETAS DE CATEGORÍAS")
    print("="*60)
    for path in sorted(all_paths):
        print(f"  • {path}")
    
    # Guardar a archivo
    output = {
        "categorias": {k: list(v) for k, v in categories.items()},
        "rutas_completas": sorted(list(all_paths)),
        "total_productos": total
    }
    
    with open("/Applications/um/vivero/scraped_data/faitful_categorias.json", "w", encoding="utf-8") as f:
        json.dump(output, f, ensure_ascii=False, indent=2)
    
    print(f"\n\n💾 Guardado en: scraped_data/faitful_categorias.json")
    print(f"📊 Total categorías principales: {len(categories)}")
    print(f"📊 Total rutas únicas: {len(all_paths)}")
    
    return output


if __name__ == "__main__":
    main()
