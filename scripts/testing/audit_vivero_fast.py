#!/usr/bin/env python3
"""
Auditoría rápida de productos WooCommerce usando consultas SQL directas
"""

import json
import subprocess
import sys
from collections import defaultdict
import re

SSH_CMD = "sshpass -p 'gsiB%s@0yD' ssh -o StrictHostKeyChecking=no root@23.105.176.45"
WP_PATH = "/home/viveroloscocos.com.ar/public_html"

def run_wp_sql(query: str) -> str:
    """Ejecuta consulta SQL en la base de datos de WordPress"""
    escaped_query = query.replace('"', '\\"').replace('$', '\\$')
    full_cmd = f'{SSH_CMD} "cd {WP_PATH} && wp db query \\"{escaped_query}\\" --allow-root 2>/dev/null"'
    result = subprocess.run(full_cmd, shell=True, capture_output=True, text=True)
    if result.returncode != 0:
        print(f"Error SQL: {result.stderr}", file=sys.stderr)
        return ""
    return result.stdout.strip()

def get_all_product_data():
    """Obtiene todos los datos de productos en una consulta"""
    query = """
    SELECT 
        p.ID,
        p.post_title,
        p.post_content as long_description,
        MAX(CASE WHEN pm.meta_key = '_thumbnail_id' THEN pm.meta_value END) as thumbnail_id,
        MAX(CASE WHEN pm.meta_key = '_product_short_description' THEN pm.meta_value END) as short_description,
        MAX(CASE WHEN pm.meta_key = '_sku' THEN pm.meta_value END) as sku
    FROM wp_posts p
    LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id
    WHERE p.post_type = 'product' 
    AND p.post_status = 'publish'
    GROUP BY p.ID, p.post_title, p.post_content
    ORDER BY p.ID
    """
    
    output = run_wp_sql(query)
    if not output:
        return []
    
    # Parsear resultado tabular
    lines = output.strip().split('\n')
    if len(lines) < 2:
        return []
    
    # Saltar header
    products = []
    for line in lines[1:]:  # Skip header
        if not line.strip() or line.startswith('+'):
            continue
        
        parts = [p.strip() for p in line.split('\t')]
        if len(parts) >= 6:
            products.append({
                'ID': parts[0],
                'post_title': parts[1],
                'long_description': parts[2] if parts[2] != 'NULL' else '',
                'thumbnail_id': parts[3] if parts[3] != 'NULL' else '',
                'short_description': parts[4] if parts[4] != 'NULL' else '',
                'sku': parts[5] if parts[5] != 'NULL' else ''
            })
    
    return products

def analyze_seo_title(title: str) -> dict:
    """Analiza calidad SEO del título"""
    issues = []
    score = 100
    
    if len(title) < 30:
        issues.append("Título muy corto (< 30 chars)")
        score -= 20
    elif len(title) > 70:
        issues.append("Título muy largo (> 70 chars)")
        score -= 15
    
    generic_words = ['producto de vivero', 'planta genérica', 'árbol sin especificar']
    if any(word in title.lower() for word in generic_words):
        issues.append("Contiene palabras genéricas")
        score -= 15
    
    if not re.search(r'\d+\s*(litros?|cm|l\b)', title.lower()):
        issues.append("No especifica tamaño")
        score -= 10
    
    return {'score': max(0, score), 'issues': issues, 'length': len(title)}

def analyze_seo_description(short_desc: str, long_desc: str) -> dict:
    """Analiza calidad SEO de las descripciones"""
    issues = []
    score = 100
    
    if not short_desc:
        issues.append("Sin descripción corta")
        score -= 30
    elif len(short_desc) < 50:
        issues.append("Descripción corta muy breve")
        score -= 15
    
    if not long_desc:
        issues.append("Sin descripción larga")
        score -= 40
    elif len(long_desc) < 150:
        issues.append("Descripción larga muy breve")
        score -= 20
    
    keywords = ['planta', 'cultivo', 'cuidado', 'riego', 'sol', 'sombra', 'suelo']
    combined = f"{short_desc} {long_desc}".lower()
    keyword_count = sum(1 for kw in keywords if kw in combined)
    
    if keyword_count < 2:
        issues.append(f"Pocas keywords ({keyword_count}/7)")
        score -= 15
    
    return {
        'score': max(0, score),
        'issues': issues,
        'short_length': len(short_desc),
        'long_length': len(long_desc)
    }

def main():
    print("=" * 80)
    print("🌱 AUDITORÍA RÁPIDA VIVERO LOS COCOS")
    print("=" * 80)
    
    print("\n📊 Obteniendo datos de productos...")
    products = get_all_product_data()
    
    if not products:
        print("❌ No se pudieron obtener productos")
        return
    
    total = len(products)
    print(f"✅ {total} productos encontrados\n")
    
    # Análisis
    image_usage = defaultdict(list)
    products_without_images = []
    seo_issues = []
    
    print("🔍 Analizando...")
    for product in products:
        pid = product['ID']
        title = product['post_title']
        thumb_id = product['thumbnail_id']
        
        # Imágenes
        if thumb_id:
            image_usage[thumb_id].append(pid)
        else:
            products_without_images.append(pid)
        
        # SEO
        title_analysis = analyze_seo_title(title)
        desc_analysis = analyze_seo_description(
            product['short_description'],
            product['long_description']
        )
        
        combined_score = title_analysis['score'] * 0.4 + desc_analysis['score'] * 0.6
        
        if combined_score < 70:
            seo_issues.append({
                'id': pid,
                'title': title,
                'score': combined_score,
                'title_issues': title_analysis['issues'],
                'desc_issues': desc_analysis['issues']
            })
    
    # REPORTE
    print("\n" + "=" * 80)
    print("📊 RESULTADOS")
    print("=" * 80)
    
    # Imágenes duplicadas
    duplicated = {img: prods for img, prods in image_usage.items() if len(prods) > 1}
    print(f"\n🖼️  IMÁGENES DUPLICADAS: {len(duplicated)}")
    if duplicated:
        total_affected = sum(len(prods) for prods in duplicated.values())
        print(f"   ⚠️  {total_affected} productos usan imágenes duplicadas")
        print("\n   Top 10 imágenes más reutilizadas:")
        for img_id, prods in sorted(duplicated.items(), key=lambda x: len(x[1]), reverse=True)[:10]:
            print(f"   - Imagen #{img_id}: {len(prods)} productos")
    
    # Sin imagen
    print(f"\n📷 SIN IMAGEN: {len(products_without_images)} productos")
    if products_without_images:
        print(f"   IDs: {', '.join(products_without_images[:30])}")
    
    # SEO
    print(f"\n🔍 PROBLEMAS SEO: {len(seo_issues)} productos ({len(seo_issues)/total*100:.1f}%)")
    
    if seo_issues:
        print("\n   📉 Top 15 peores scores:")
        for issue in sorted(seo_issues, key=lambda x: x['score'])[:15]:
            print(f"\n   #{issue['id']}: {issue['title'][:65]}")
            print(f"   Score: {issue['score']:.0f}/100")
            if issue['title_issues']:
                print(f"   • {' | '.join(issue['title_issues'])}")
            if issue['desc_issues']:
                print(f"   • {' | '.join(issue['desc_issues'])}")
    
    # Resumen
    print("\n" + "=" * 80)
    print("📈 RESUMEN EJECUTIVO")
    print("=" * 80)
    
    avg_seo = sum(p['score'] for p in seo_issues) / len(seo_issues) if seo_issues else 100
    
    print(f"""
Total productos:           {total}
Sin imagen:                {len(products_without_images)} ({len(products_without_images)/total*100:.1f}%)
Imágenes duplicadas:       {len(duplicated)}
Productos afectados:       {sum(len(prods) for prods in duplicated.values())}
Con problemas SEO:         {len(seo_issues)} ({len(seo_issues)/total*100:.1f}%)
Score SEO promedio:        {avg_seo:.1f}/100

⚠️  ACCIONES CRÍTICAS:
1. 🖼️  Asignar imágenes únicas a {sum(len(prods)-1 for prods in duplicated.values())} productos
2. 📷 Agregar imágenes a {len(products_without_images)} productos
3. ✍️  Optimizar SEO de {len(seo_issues)} productos
""")
    
    # Guardar reporte
    report = {
        'total_products': total,
        'duplicated_images': {k: v for k, v in duplicated.items()},
        'products_without_images': products_without_images,
        'seo_issues': sorted(seo_issues, key=lambda x: x['score'])
    }
    
    with open('/tmp/vivero_audit.json', 'w', encoding='utf-8') as f:
        json.dump(report, f, indent=2, ensure_ascii=False)
    
    print("💾 Reporte guardado: /tmp/vivero_audit.json")
    print("=" * 80)

if __name__ == '__main__':
    main()
