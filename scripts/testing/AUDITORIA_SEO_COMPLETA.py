#!/usr/bin/env python3
"""
AUDITORÍA SEO COMPLETA - VIVERO LOS COCOS
=========================================
Analiza TODOS los productos y determina qué falta para tener
EL MEJOR SEO DE VIVEROS EN E-COMMERCE DEL MUNDO
"""

import requests
from requests.auth import HTTPBasicAuth
import sys
from typing import List, Dict
import time
from collections import defaultdict
import re


class SEOAuditComplete:
    """Auditoría completa de SEO para e-commerce"""
    
    def __init__(self, url: str, consumer_key: str, consumer_secret: str):
        self.url = url.rstrip('/')
        self.consumer_key = consumer_key
        self.consumer_secret = consumer_secret
        self.session = requests.Session()
        self.session.auth = HTTPBasicAuth(consumer_key, consumer_secret)
        
        self.issues = defaultdict(list)
        self.stats = {}
    
    def get_all_products(self) -> List[Dict]:
        """Obtiene todos los productos"""
        all_products = []
        page = 1
        
        print("📦 Obteniendo todos los productos...")
        
        while True:
            endpoint = f"{self.url}/wp-json/wc/v3/products"
            params = {"per_page": 100, "page": page}
            
            try:
                response = self.session.get(endpoint, params=params)
                response.raise_for_status()
                products = response.json()
                
                if not products:
                    break
                
                all_products.extend(products)
                print(f"  Página {page}: {len(products)} productos")
                page += 1
                time.sleep(0.5)
                
            except Exception as e:
                print(f"✗ Error: {e}")
                break
        
        print(f"\n✓ Total: {len(all_products)} productos\n")
        return all_products
    
    def audit_title(self, product: Dict) -> Dict:
        """Audita el título del producto"""
        title = product['name']
        product_id = product['id']
        sku = product.get('sku', '')
        
        issues = []
        score = 100
        
        # Longitud del título
        length = len(title)
        if length < 30:
            issues.append(f"Título muy corto ({length} chars) - Ideal: 40-60")
            score -= 20
        elif length > 70:
            issues.append(f"Título muy largo ({length} chars) - Ideal: 40-60")
            score -= 10
        
        # Keywords relevantes
        keywords = ['planta', 'maceta', 'árbol', 'arbusto', 'litros', 'cm', 'color']
        has_keyword = any(kw.lower() in title.lower() for kw in keywords)
        if not has_keyword:
            issues.append("Sin keywords relevantes de producto")
            score -= 15
        
        # Capitalización
        if not title[0].isupper():
            issues.append("Debe empezar con mayúscula")
            score -= 5
        
        # Redundancias
        redundancies = ['mendoza mendoza', 'argentina argentina', 'vivero vivero']
        for red in redundancies:
            if red.lower() in title.lower():
                issues.append(f"Redundancia: '{red}'")
                score -= 10
        
        # Caracteres especiales problemáticos
        if '--' in title or '||' in title or '  ' in title:
            issues.append("Caracteres especiales duplicados")
            score -= 5
        
        # Información de tamaño (para productos con SKU que indica tamaño)
        if re.search(r'\d+[Ll]', sku):
            if not re.search(r'\d+\s*(litros?|cm|l\b)', title, re.IGNORECASE):
                issues.append("SKU tiene tamaño pero título no lo muestra")
                score -= 10
        
        return {
            'title': title,
            'length': length,
            'score': max(0, score),
            'issues': issues,
            'has_issues': len(issues) > 0
        }
    
    def audit_description(self, product: Dict) -> Dict:
        """Audita la descripción del producto"""
        description = product.get('description', '')
        short_description = product.get('short_description', '')
        product_id = product['id']
        
        issues = []
        score = 100
        
        # Descripción principal
        if not description or len(description.strip()) == 0:
            issues.append("Sin descripción larga - CRÍTICO para SEO")
            score -= 50
        elif len(description) < 100:
            issues.append(f"Descripción muy corta ({len(description)} chars) - Ideal: 200+")
            score -= 30
        
        # Descripción corta (excerpt)
        if not short_description or len(short_description.strip()) == 0:
            issues.append("Sin descripción corta (excerpt)")
            score -= 20
        elif len(short_description) < 50:
            issues.append(f"Descripción corta muy breve ({len(short_description)} chars)")
            score -= 10
        
        # Keywords en descripción
        title_words = set(product['name'].lower().split())
        desc_words = set(description.lower().split()) if description else set()
        
        keyword_overlap = len(title_words & desc_words)
        if keyword_overlap < 2:
            issues.append("Poca relación entre título y descripción")
            score -= 15
        
        return {
            'has_description': len(description) > 0,
            'has_short_description': len(short_description) > 0,
            'description_length': len(description),
            'short_description_length': len(short_description),
            'score': max(0, score),
            'issues': issues,
            'has_issues': len(issues) > 0
        }
    
    def audit_images(self, product: Dict) -> Dict:
        """Audita las imágenes del producto"""
        images = product.get('images', [])
        product_id = product['id']
        
        issues = []
        score = 100
        
        # Imagen principal
        if not images or len(images) == 0:
            issues.append("Sin imagen principal - CRÍTICO")
            score = 0
        else:
            main_image = images[0]
            
            # Alt text
            if not main_image.get('alt'):
                issues.append("Imagen sin alt text - CRÍTICO para SEO")
                score -= 40
            elif len(main_image['alt']) < 10:
                issues.append("Alt text muy corto")
                score -= 20
            
            # Nombre del archivo
            image_name = main_image.get('name', '')
            if not image_name or re.match(r'^[a-f0-9\-]+$', image_name):
                issues.append("Nombre de imagen no descriptivo (hash)")
                score -= 15
        
        # Múltiples imágenes
        if len(images) < 2:
            issues.append("Solo 1 imagen - Ideal: 3-5 imágenes")
            score -= 10
        
        # Galería de imágenes
        if len(images) > 1:
            for i, img in enumerate(images[1:], 2):
                if not img.get('alt'):
                    issues.append(f"Imagen {i} sin alt text")
                    score -= 5
        
        return {
            'has_images': len(images) > 0,
            'image_count': len(images),
            'has_alt_text': images[0].get('alt') if images else False,
            'score': max(0, score),
            'issues': issues,
            'has_issues': len(issues) > 0
        }
    
    def audit_meta_seo(self, product: Dict) -> Dict:
        """Audita metadatos SEO"""
        meta = product.get('meta_data', [])
        
        issues = []
        score = 100
        
        # Meta description (Yoast/RankMath)
        meta_desc = next((m['value'] for m in meta if m['key'] in ['_yoast_wpseo_metadesc', '_rank_math_description']), None)
        if not meta_desc:
            issues.append("Sin meta description personalizada")
            score -= 30
        elif len(meta_desc) < 120:
            issues.append("Meta description muy corta (ideal: 150-160)")
            score -= 15
        
        # Focus keyword
        focus_kw = next((m['value'] for m in meta if m['key'] in ['_yoast_wpseo_focuskw', '_rank_math_focus_keyword']), None)
        if not focus_kw:
            issues.append("Sin focus keyword definida")
            score -= 20
        
        # Canonical URL
        canonical = next((m['value'] for m in meta if m['key'] == '_yoast_wpseo_canonical'), None)
        
        return {
            'has_meta_description': meta_desc is not None,
            'has_focus_keyword': focus_kw is not None,
            'meta_description_length': len(meta_desc) if meta_desc else 0,
            'score': max(0, score),
            'issues': issues,
            'has_issues': len(issues) > 0
        }
    
    def audit_product_data(self, product: Dict) -> Dict:
        """Audita datos del producto (precio, stock, etc.)"""
        issues = []
        score = 100
        
        # Precio
        price = product.get('price')
        regular_price = product.get('regular_price')
        
        if not price or price == '' or price == '0':
            issues.append("Sin precio definido")
            score -= 40
        
        # Stock
        stock_status = product.get('stock_status')
        if stock_status == 'outofstock':
            issues.append("Producto sin stock")
            score -= 10
        
        # SKU
        sku = product.get('sku', '')
        if not sku:
            issues.append("Sin SKU - dificulta gestión")
            score -= 20
        
        # Categorías
        categories = product.get('categories', [])
        if len(categories) == 0:
            issues.append("Sin categorías asignadas")
            score -= 25
        
        # Tags
        tags = product.get('tags', [])
        if len(tags) == 0:
            issues.append("Sin etiquetas (tags) - Mejora SEO interno")
            score -= 10
        
        return {
            'has_price': price and price != '0',
            'in_stock': stock_status == 'instock',
            'has_sku': len(sku) > 0,
            'has_categories': len(categories) > 0,
            'has_tags': len(tags) > 0,
            'category_count': len(categories),
            'tag_count': len(tags),
            'score': max(0, score),
            'issues': issues,
            'has_issues': len(issues) > 0
        }
    
    def run_full_audit(self) -> Dict:
        """Ejecuta auditoría completa"""
        print("\n" + "="*70)
        print("🔍 AUDITORÍA SEO COMPLETA - VIVERO LOS COCOS")
        print("="*70 + "\n")
        
        products = self.get_all_products()
        total = len(products)
        
        results = []
        
        print("🔍 Analizando productos...\n")
        
        for i, product in enumerate(products, 1):
            if i % 50 == 0:
                print(f"  Progreso: {i}/{total} productos...")
            
            product_id = product['id']
            
            # Ejecutar todas las auditorías
            title_audit = self.audit_title(product)
            desc_audit = self.audit_description(product)
            images_audit = self.audit_images(product)
            meta_audit = self.audit_meta_seo(product)
            data_audit = self.audit_product_data(product)
            
            # Score global
            global_score = (
                title_audit['score'] * 0.25 +
                desc_audit['score'] * 0.25 +
                images_audit['score'] * 0.25 +
                meta_audit['score'] * 0.15 +
                data_audit['score'] * 0.10
            )
            
            # Recopilar todos los issues
            all_issues = (
                title_audit['issues'] +
                desc_audit['issues'] +
                images_audit['issues'] +
                meta_audit['issues'] +
                data_audit['issues']
            )
            
            result = {
                'id': product_id,
                'name': product['name'],
                'sku': product.get('sku', ''),
                'url': product['permalink'],
                'global_score': round(global_score, 1),
                'title_audit': title_audit,
                'description_audit': desc_audit,
                'images_audit': images_audit,
                'meta_audit': meta_audit,
                'data_audit': data_audit,
                'total_issues': len(all_issues),
                'all_issues': all_issues,
                'needs_attention': global_score < 70
            }
            
            results.append(result)
        
        print(f"\n✓ Análisis completado: {total} productos\n")
        
        return {
            'total_products': total,
            'results': results,
            'summary': self.generate_summary(results)
        }
    
    def generate_summary(self, results: List[Dict]) -> Dict:
        """Genera resumen estadístico"""
        total = len(results)
        
        # Scores
        scores = [r['global_score'] for r in results]
        avg_score = sum(scores) / len(scores) if scores else 0
        
        excellent = len([s for s in scores if s >= 90])
        good = len([s for s in scores if 70 <= s < 90])
        needs_improvement = len([s for s in scores if 50 <= s < 70])
        critical = len([s for s in scores if s < 50])
        
        # Issues comunes
        all_issues = []
        for r in results:
            all_issues.extend(r['all_issues'])
        
        from collections import Counter
        common_issues = Counter(all_issues).most_common(10)
        
        # Por categoría
        without_description = len([r for r in results if not r['description_audit']['has_description']])
        without_images = len([r for r in results if not r['images_audit']['has_images']])
        without_alt_text = len([r for r in results if r['images_audit']['has_images'] and not r['images_audit']['has_alt_text']])
        without_meta_desc = len([r for r in results if not r['meta_audit']['has_meta_description']])
        without_categories = len([r for r in results if not r['data_audit']['has_categories']])
        
        return {
            'total_products': total,
            'average_score': round(avg_score, 1),
            'score_distribution': {
                'excellent': excellent,
                'good': good,
                'needs_improvement': needs_improvement,
                'critical': critical
            },
            'common_issues': common_issues,
            'critical_stats': {
                'without_description': without_description,
                'without_images': without_images,
                'without_alt_text': without_alt_text,
                'without_meta_description': without_meta_desc,
                'without_categories': without_categories
            }
        }
    
    def export_results(self, audit_results: Dict, filename: str):
        """Exporta resultados a Markdown"""
        summary = audit_results['summary']
        results = audit_results['results']
        
        with open(filename, 'w', encoding='utf-8') as f:
            f.write("# 🔍 AUDITORÍA SEO COMPLETA - VIVERO LOS COCOS\n\n")
            f.write(f"**Fecha:** 2025-10-03\n")
            f.write(f"**Productos analizados:** {summary['total_products']}\n\n")
            
            f.write("---\n\n")
            f.write("## 📊 RESUMEN EJECUTIVO\n\n")
            
            f.write(f"### Score Promedio: {summary['average_score']}/100\n\n")
            
            f.write("### Distribución de Calidad\n\n")
            dist = summary['score_distribution']
            f.write(f"- ✅ **Excelente** (90-100): {dist['excellent']} productos ({dist['excellent']/summary['total_products']*100:.1f}%)\n")
            f.write(f"- ✅ **Bueno** (70-89): {dist['good']} productos ({dist['good']/summary['total_products']*100:.1f}%)\n")
            f.write(f"- ⚠️ **Necesita mejora** (50-69): {dist['needs_improvement']} productos ({dist['needs_improvement']/summary['total_products']*100:.1f}%)\n")
            f.write(f"- ❌ **Crítico** (<50): {dist['critical']} productos ({dist['critical']/summary['total_products']*100:.1f}%)\n\n")
            
            f.write("---\n\n")
            f.write("## 🚨 PROBLEMAS CRÍTICOS\n\n")
            
            critical = summary['critical_stats']
            f.write(f"| Problema | Cantidad | % |\n")
            f.write(f"|----------|----------|---|\n")
            f.write(f"| Sin descripción | {critical['without_description']} | {critical['without_description']/summary['total_products']*100:.1f}% |\n")
            f.write(f"| Sin imágenes | {critical['without_images']} | {critical['without_images']/summary['total_products']*100:.1f}% |\n")
            f.write(f"| Sin alt text | {critical['without_alt_text']} | {critical['without_alt_text']/summary['total_products']*100:.1f}% |\n")
            f.write(f"| Sin meta description | {critical['without_meta_description']} | {critical['without_meta_description']/summary['total_products']*100:.1f}% |\n")
            f.write(f"| Sin categorías | {critical['without_categories']} | {critical['without_categories']/summary['total_products']*100:.1f}% |\n\n")
            
            f.write("---\n\n")
            f.write("## 📋 PROBLEMAS MÁS COMUNES\n\n")
            
            for issue, count in summary['common_issues']:
                percentage = (count / summary['total_products']) * 100
                f.write(f"- **{issue}**: {count} productos ({percentage:.1f}%)\n")
            
            f.write("\n---\n\n")
            f.write("## 🎯 PRODUCTOS QUE NECESITAN ATENCIÓN URGENTE\n\n")
            
            critical_products = [r for r in results if r['global_score'] < 50]
            critical_products.sort(key=lambda x: x['global_score'])
            
            for product in critical_products[:20]:
                f.write(f"### Producto ID: {product['id']}\n")
                f.write(f"- **Nombre:** {product['name']}\n")
                f.write(f"- **SKU:** {product['sku']}\n")
                f.write(f"- **Score:** {product['global_score']}/100\n")
                f.write(f"- **Problemas:** {product['total_issues']}\n\n")
                f.write("**Issues:**\n")
                for issue in product['all_issues'][:5]:
                    f.write(f"  - {issue}\n")
                f.write("\n")
            
            if len(critical_products) > 20:
                f.write(f"\n*... y {len(critical_products) - 20} productos más con score < 50*\n\n")
        
        print(f"\n💾 Resultados exportados a: {filename}")


def main():
    """Función principal"""
    import argparse
    
    parser = argparse.ArgumentParser(description='Auditoría SEO completa')
    parser.add_argument('--url', required=True, help='URL del sitio')
    parser.add_argument('--key', required=True, help='Consumer Key')
    parser.add_argument('--secret', required=True, help='Consumer Secret')
    parser.add_argument('--export', default='AUDITORIA_SEO_RESULTADOS.md', help='Archivo de exportación')
    
    args = parser.parse_args()
    
    auditor = SEOAuditComplete(args.url, args.key, args.secret)
    results = auditor.run_full_audit()
    auditor.export_results(results, args.export)
    
    # Mostrar resumen en consola
    summary = results['summary']
    print("\n" + "="*70)
    print("📊 RESUMEN FINAL")
    print("="*70)
    print(f"\n✓ Productos analizados: {summary['total_products']}")
    print(f"✓ Score promedio: {summary['average_score']}/100")
    print(f"\n📈 Distribución:")
    print(f"  Excelentes: {summary['score_distribution']['excellent']}")
    print(f"  Buenos: {summary['score_distribution']['good']}")
    print(f"  Necesitan mejora: {summary['score_distribution']['needs_improvement']}")
    print(f"  Críticos: {summary['score_distribution']['critical']}")
    
    print(f"\n🚨 Problemas críticos:")
    critical = summary['critical_stats']
    print(f"  Sin descripción: {critical['without_description']}")
    print(f"  Sin imágenes: {critical['without_images']}")
    print(f"  Sin alt text: {critical['without_alt_text']}")
    print(f"  Sin meta description: {critical['without_meta_description']}")
    
    print("\n" + "="*70)


if __name__ == "__main__":
    main()
