#!/usr/bin/env python3
"""
Testing Integral de Optimización SEO
====================================
Valida TODOS los productos en producción
"""

import requests
from requests.auth import HTTPBasicAuth
import sys
from typing import List, Dict
import time


class SEOTester:
    """Tester integral de SEO"""
    
    def __init__(self, url: str, consumer_key: str, consumer_secret: str):
        self.url = url.rstrip('/')
        self.consumer_key = consumer_key
        self.consumer_secret = consumer_secret
        self.session = requests.Session()
        self.session.auth = HTTPBasicAuth(consumer_key, consumer_secret)
        
        self.tests_passed = 0
        self.tests_failed = 0
        self.warnings = 0
    
    def get_all_products(self) -> List[Dict]:
        """Obtiene todos los productos"""
        all_products = []
        page = 1
        
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
                print(f"✓ Página {page}: {len(products)} productos obtenidos")
                page += 1
                time.sleep(0.5)
                
            except Exception as e:
                print(f"✗ Error en página {page}: {e}")
                break
        
        return all_products
    
    def test_title_length(self, title: str) -> tuple:
        """Test 1: Longitud del título (50-70 chars ideal)"""
        length = len(title)
        
        if 50 <= length <= 70:
            return True, f"✓ Longitud óptima: {length} chars"
        elif 40 <= length < 50:
            return True, f"⚠ Longitud aceptable: {length} chars (ideal 50-70)"
        elif 70 < length <= 80:
            return True, f"⚠ Longitud aceptable: {length} chars (ideal 50-70)"
        else:
            return False, f"✗ Longitud no óptima: {length} chars (debe ser 40-80)"
    
    def test_has_keywords(self, title: str) -> tuple:
        """Test 2: Contiene keywords relevantes"""
        keywords = ["mendoza", "argentina", "litros", "cm", "árbol", "arbusto", "planta"]
        found_keywords = [kw for kw in keywords if kw.lower() in title.lower()]
        
        if len(found_keywords) >= 1:
            return True, f"✓ Keywords encontradas: {', '.join(found_keywords)}"
        else:
            return False, f"✗ Sin keywords relevantes"
    
    def test_no_duplicates(self, title: str) -> tuple:
        """Test 3: Sin duplicados de marca"""
        duplicates = ["vivero los cocos vivero los cocos", "mendoza mendoza"]
        
        for dup in duplicates:
            if dup.lower() in title.lower():
                return False, f"✗ Duplicado encontrado: {dup}"
        
        return True, "✓ Sin duplicados"
    
    def test_capitalization(self, title: str) -> tuple:
        """Test 4: Capitalización correcta"""
        if title and title[0].isupper():
            return True, "✓ Capitalización correcta"
        else:
            return False, "✗ Debe empezar con mayúscula"
    
    def test_has_size_info(self, title: str, sku: str) -> tuple:
        """Test 5: Contiene información de tamaño"""
        size_patterns = ["litros", "cm", "l ", "l|"]
        
        for pattern in size_patterns:
            if pattern.lower() in title.lower():
                return True, f"✓ Información de tamaño: {pattern}"
        
        # Si el SKU tiene tamaño pero el título no
        if any(char.isdigit() for char in sku):
            return False, "⚠ SKU tiene tamaño pero título no lo muestra claramente"
        
        return True, "✓ Producto sin tamaño específico"
    
    def test_seo_friendly(self, title: str) -> tuple:
        """Test 6: SEO-friendly (sin caracteres especiales problemáticos)"""
        problematic = ["--", "||", "___", "  "]
        
        for prob in problematic:
            if prob in title:
                return False, f"✗ Caracteres problemáticos: {prob}"
        
        return True, "✓ SEO-friendly"
    
    def test_product(self, product: Dict) -> Dict:
        """Ejecuta todos los tests en un producto"""
        product_id = product['id']
        title = product['name']
        sku = product.get('sku', '')
        
        results = {
            'id': product_id,
            'sku': sku,
            'title': title,
            'tests': [],
            'passed': 0,
            'failed': 0,
            'warnings': 0
        }
        
        # Ejecutar todos los tests
        tests = [
            self.test_title_length(title),
            self.test_has_keywords(title),
            self.test_no_duplicates(title),
            self.test_capitalization(title),
            self.test_has_size_info(title, sku),
            self.test_seo_friendly(title)
        ]
        
        for passed, message in tests:
            results['tests'].append({'passed': passed, 'message': message})
            
            if passed:
                if '⚠' in message:
                    results['warnings'] += 1
                else:
                    results['passed'] += 1
            else:
                results['failed'] += 1
        
        return results
    
    def run_full_test(self) -> Dict:
        """Ejecuta test completo en todos los productos"""
        print("\n" + "="*70)
        print("🧪 TESTING INTEGRAL DE OPTIMIZACIÓN SEO")
        print("="*70 + "\n")
        
        print("📦 Obteniendo productos...")
        products = self.get_all_products()
        total = len(products)
        
        print(f"\n✓ {total} productos obtenidos\n")
        print("🔍 Ejecutando tests...\n")
        
        all_results = []
        products_passed = 0
        products_failed = 0
        
        for i, product in enumerate(products, 1):
            result = self.test_product(product)
            all_results.append(result)
            
            # Producto pasa si no tiene tests fallidos
            if result['failed'] == 0:
                products_passed += 1
            else:
                products_failed += 1
            
            # Mostrar progreso cada 50 productos
            if i % 50 == 0:
                print(f"  Progreso: {i}/{total} productos testeados...")
        
        print(f"\n✓ Testing completado: {total} productos\n")
        
        # Generar resumen
        summary = {
            'total_products': total,
            'products_passed': products_passed,
            'products_failed': products_failed,
            'pass_rate': (products_passed / total * 100) if total > 0 else 0,
            'results': all_results
        }
        
        return summary
    
    def print_summary(self, summary: Dict):
        """Imprime resumen de resultados"""
        print("="*70)
        print("📊 RESUMEN DE TESTING")
        print("="*70)
        print(f"\nTotal de productos testeados: {summary['total_products']}")
        print(f"✓ Productos que pasan todos los tests: {summary['products_passed']}")
        print(f"✗ Productos con tests fallidos: {summary['products_failed']}")
        print(f"📈 Tasa de éxito: {summary['pass_rate']:.1f}%\n")
        
        # Mostrar productos con fallos
        if summary['products_failed'] > 0:
            print("="*70)
            print("⚠️  PRODUCTOS CON TESTS FALLIDOS")
            print("="*70 + "\n")
            
            failed_count = 0
            for result in summary['results']:
                if result['failed'] > 0:
                    failed_count += 1
                    if failed_count <= 20:  # Mostrar solo primeros 20
                        print(f"Producto ID: {result['id']}")
                        print(f"SKU: {result['sku']}")
                        print(f"Título: {result['title']}")
                        print("Tests fallidos:")
                        for test in result['tests']:
                            if not test['passed']:
                                print(f"  {test['message']}")
                        print()
            
            if failed_count > 20:
                print(f"... y {failed_count - 20} productos más con fallos\n")
        
        # Estadísticas de tests individuales
        print("="*70)
        print("📋 ESTADÍSTICAS POR TEST")
        print("="*70 + "\n")
        
        test_names = [
            "Longitud del título",
            "Keywords relevantes",
            "Sin duplicados",
            "Capitalización",
            "Información de tamaño",
            "SEO-friendly"
        ]
        
        for i, test_name in enumerate(test_names):
            passed = sum(1 for r in summary['results'] if r['tests'][i]['passed'])
            total = summary['total_products']
            percentage = (passed / total * 100) if total > 0 else 0
            
            status = "✓" if percentage >= 90 else "⚠" if percentage >= 70 else "✗"
            print(f"{status} {test_name}: {passed}/{total} ({percentage:.1f}%)")
    
    def export_results(self, summary: Dict, filename: str):
        """Exporta resultados a archivo"""
        with open(filename, 'w', encoding='utf-8') as f:
            f.write("# REPORTE DE TESTING INTEGRAL SEO\n\n")
            f.write(f"## Resumen General\n\n")
            f.write(f"- **Total productos:** {summary['total_products']}\n")
            f.write(f"- **Productos OK:** {summary['products_passed']}\n")
            f.write(f"- **Productos con fallos:** {summary['products_failed']}\n")
            f.write(f"- **Tasa de éxito:** {summary['pass_rate']:.1f}%\n\n")
            
            f.write(f"## Productos con Tests Fallidos\n\n")
            for result in summary['results']:
                if result['failed'] > 0:
                    f.write(f"### Producto ID: {result['id']}\n")
                    f.write(f"- **SKU:** {result['sku']}\n")
                    f.write(f"- **Título:** {result['title']}\n")
                    f.write(f"- **Tests fallidos:** {result['failed']}\n")
                    f.write(f"- **Warnings:** {result['warnings']}\n\n")
                    f.write("**Detalles:**\n")
                    for test in result['tests']:
                        f.write(f"- {test['message']}\n")
                    f.write("\n")
        
        print(f"\n💾 Resultados exportados a: {filename}")


def main():
    """Función principal"""
    import argparse
    
    parser = argparse.ArgumentParser(description='Testing integral de SEO')
    parser.add_argument('--url', required=True, help='URL del sitio')
    parser.add_argument('--key', required=True, help='Consumer Key')
    parser.add_argument('--secret', required=True, help='Consumer Secret')
    parser.add_argument('--export', default='seo_test_results.md', help='Archivo de exportación')
    
    args = parser.parse_args()
    
    # Crear tester
    tester = SEOTester(args.url, args.key, args.secret)
    
    # Ejecutar tests
    summary = tester.run_full_test()
    
    # Mostrar resumen
    tester.print_summary(summary)
    
    # Exportar resultados
    tester.export_results(summary, args.export)
    
    # Exit code basado en resultados
    if summary['pass_rate'] >= 95:
        print("\n🎉 ¡EXCELENTE! Tasa de éxito >= 95%")
        sys.exit(0)
    elif summary['pass_rate'] >= 80:
        print("\n✓ BUENO. Tasa de éxito >= 80%")
        sys.exit(0)
    else:
        print("\n⚠️  REQUIERE ATENCIÓN. Tasa de éxito < 80%")
        sys.exit(1)


if __name__ == "__main__":
    main()
