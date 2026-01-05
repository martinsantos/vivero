#!/usr/bin/env python3
"""
Auditoría completa de productos WooCommerce en producción
- Verifica unicidad de imágenes
- Analiza títulos y descripciones para SEO
- Genera reporte de optimizaciones necesarias
"""

import json
import subprocess
import sys
from collections import defaultdict
from typing import Dict, List, Tuple
import re

SSH_CMD = "sshpass -p 'gsiB%s@0yD' ssh -o StrictHostKeyChecking=no root@23.105.176.45"
WP_PATH = "/home/viveroloscocos.com.ar/public_html"

def run_wp_cli(command: str) -> str:
    """Ejecuta comando WP-CLI en producción"""
    full_cmd = f'{SSH_CMD} "cd {WP_PATH} && {command} --allow-root 2>/dev/null"'
    result = subprocess.run(full_cmd, shell=True, capture_output=True, text=True)
    if result.returncode != 0:
        return ""
    return result.stdout.strip()

def get_all_products() -> List[Dict]:
    """Obtiene todos los productos publicados"""
    print("📦 Obteniendo productos...")
    output = run_wp_cli('wp post list --post_type=product --post_status=publish --fields=ID,post_title --format=json --posts_per_page=-1')
    if not output:
        return []
    return json.loads(output)

def get_product_details(product_id: int) -> Dict:
    """Obtiene detalles completos de un producto"""
    # Imagen destacada
    thumbnail_id = run_wp_cli(f'wp post meta get {product_id} _thumbnail_id')
    
    # Descripción corta y larga
    short_desc = run_wp_cli(f'wp post meta get {product_id} _product_short_description')
    long_desc = run_wp_cli(f'wp post get {product_id} --field=post_content')
    
    # SKU
    sku = run_wp_cli(f'wp post meta get {product_id} _sku')
    
    # Categorías
    categories = run_wp_cli(f'wp post term list {product_id} product_cat --fields=name --format=json')
    cats = json.loads(categories) if categories else []
    
    return {
        'thumbnail_id': thumbnail_id,
        'short_description': short_desc,
        'long_description': long_desc,
        'sku': sku,
        'categories': [c['name'] for c in cats] if cats else []
    }

def analyze_seo_title(title: str, categories: List[str]) -> Dict:
    """Analiza calidad SEO del título"""
    issues = []
    score = 100
    
    # Longitud óptima: 50-60 caracteres
    if len(title) < 30:
        issues.append("Título muy corto (< 30 chars)")
        score -= 20
    elif len(title) > 70:
        issues.append("Título muy largo (> 70 chars)")
        score -= 15
    
    # Palabras genéricas
    generic_words = ['producto de vivero', 'planta', 'árbol']
    if any(word in title.lower() for word in generic_words):
        issues.append("Contiene palabras genéricas poco descriptivas")
        score -= 15
    
    # Falta de información de tamaño/variedad
    if not re.search(r'\d+\s*(litros?|cm|metros?)', title.lower()):
        issues.append("No especifica tamaño claramente")
        score -= 10
    
    # Nombre científico o común
    if title.count(' ') < 2:
        issues.append("Título muy simple, falta descripción")
        score -= 10
    
    return {
        'score': max(0, score),
        'issues': issues,
        'length': len(title)
    }

def analyze_seo_description(short_desc: str, long_desc: str, title: str) -> Dict:
    """Analiza calidad SEO de las descripciones"""
    issues = []
    score = 100
    
    # Descripción corta
    if not short_desc or short_desc == "":
        issues.append("Sin descripción corta")
        score -= 30
    elif len(short_desc) < 50:
        issues.append("Descripción corta muy breve (< 50 chars)")
        score -= 15
    elif len(short_desc) > 160:
        issues.append("Descripción corta muy larga (> 160 chars)")
        score -= 10
    
    # Descripción larga
    if not long_desc or long_desc == "":
        issues.append("Sin descripción larga")
        score -= 40
    elif len(long_desc) < 150:
        issues.append("Descripción larga muy breve (< 150 chars)")
        score -= 20
    
    # Keywords importantes para viveros
    keywords = ['planta', 'cultivo', 'cuidado', 'riego', 'sol', 'sombra', 'suelo', 'clima', 'argentina']
    combined = f"{short_desc} {long_desc}".lower()
    keyword_count = sum(1 for kw in keywords if kw in combined)
    
    if keyword_count < 2:
        issues.append(f"Pocas keywords relevantes ({keyword_count}/9)")
        score -= 15
    
    return {
        'score': max(0, score),
        'issues': issues,
        'short_length': len(short_desc) if short_desc else 0,
        'long_length': len(long_desc) if long_desc else 0,
        'keyword_count': keyword_count
    }

def main():
    print("=" * 80)
    print("🌱 AUDITORÍA VIVERO LOS COCOS - PRODUCCIÓN")
    print("=" * 80)
    
    products = get_all_products()
    total = len(products)
    print(f"✅ {total} productos encontrados\n")
    
    # Estructuras de análisis
    image_usage = defaultdict(list)  # thumbnail_id -> [product_ids]
    seo_issues = []
    products_without_images = []
    
    print("🔍 Analizando productos...")
    for i, product in enumerate(products, 1):
        product_id = int(product['ID'])
        title = product['post_title']
        
        if i % 50 == 0:
            print(f"   Procesados {i}/{total}...")
        
        # Obtener detalles
        details = get_product_details(product_id)
        
        # Verificar imagen
        thumb_id = details['thumbnail_id']
        if thumb_id and thumb_id != "":
            image_usage[thumb_id].append(product_id)
        else:
            products_without_images.append(product_id)
        
        # Análisis SEO
        title_analysis = analyze_seo_title(title, details['categories'])
        desc_analysis = analyze_seo_description(
            details['short_description'],
            details['long_description'],
            title
        )
        
        # Calcular score combinado
        combined_score = (title_analysis['score'] * 0.4 + desc_analysis['score'] * 0.6)
        
        if combined_score < 70:  # Umbral de calidad
            seo_issues.append({
                'id': product_id,
                'title': title,
                'score': combined_score,
                'title_issues': title_analysis['issues'],
                'desc_issues': desc_analysis['issues'],
                'categories': details['categories']
            })
    
    # REPORTE
    print("\n" + "=" * 80)
    print("📊 RESULTADOS DE AUDITORÍA")
    print("=" * 80)
    
    # 1. Imágenes duplicadas
    duplicated_images = {img_id: prods for img_id, prods in image_usage.items() if len(prods) > 1}
    print(f"\n🖼️  IMÁGENES DUPLICADAS: {len(duplicated_images)}")
    if duplicated_images:
        print(f"   ⚠️  {sum(len(prods) for prods in duplicated_images.values())} productos afectados")
        for img_id, prods in sorted(duplicated_images.items(), key=lambda x: len(x[1]), reverse=True)[:10]:
            print(f"   - Imagen #{img_id}: usada en {len(prods)} productos")
    
    # 2. Productos sin imagen
    print(f"\n📷 PRODUCTOS SIN IMAGEN: {len(products_without_images)}")
    if products_without_images:
        print(f"   ⚠️  IDs: {products_without_images[:20]}{'...' if len(products_without_images) > 20 else ''}")
    
    # 3. Problemas SEO
    print(f"\n🔍 PRODUCTOS CON PROBLEMAS SEO: {len(seo_issues)}")
    print(f"   ({len(seo_issues)/total*100:.1f}% del total)")
    
    # Top 20 peores scores
    seo_issues_sorted = sorted(seo_issues, key=lambda x: x['score'])[:20]
    print("\n   📉 Top 20 productos con peor SEO:")
    for issue in seo_issues_sorted:
        print(f"\n   ID {issue['id']}: {issue['title'][:60]}")
        print(f"   Score: {issue['score']:.1f}/100")
        if issue['title_issues']:
            print(f"   Título: {', '.join(issue['title_issues'])}")
        if issue['desc_issues']:
            print(f"   Descripción: {', '.join(issue['desc_issues'])}")
    
    # 4. Resumen general
    print("\n" + "=" * 80)
    print("📈 RESUMEN EJECUTIVO")
    print("=" * 80)
    
    avg_seo = sum(p['score'] for p in seo_issues) / len(seo_issues) if seo_issues else 100
    
    print(f"""
    Total productos:           {total}
    Productos sin imagen:      {len(products_without_images)} ({len(products_without_images)/total*100:.1f}%)
    Imágenes duplicadas:       {len(duplicated_images)}
    Productos con SEO < 70:    {len(seo_issues)} ({len(seo_issues)/total*100:.1f}%)
    Score SEO promedio:        {avg_seo:.1f}/100
    
    ⚠️  ACCIONES REQUERIDAS:
    1. Asignar imágenes únicas a {sum(len(prods)-1 for prods in duplicated_images.values())} productos
    2. Agregar imágenes a {len(products_without_images)} productos
    3. Optimizar títulos y descripciones de {len(seo_issues)} productos
    """)
    
    # Guardar reporte JSON
    report = {
        'total_products': total,
        'duplicated_images': {k: v for k, v in duplicated_images.items()},
        'products_without_images': products_without_images,
        'seo_issues': seo_issues
    }
    
    with open('/tmp/vivero_audit_report.json', 'w', encoding='utf-8') as f:
        json.dump(report, f, indent=2, ensure_ascii=False)
    
    print("\n💾 Reporte completo guardado en: /tmp/vivero_audit_report.json")
    print("=" * 80)

if __name__ == '__main__':
    main()
