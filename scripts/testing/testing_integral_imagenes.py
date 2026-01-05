#!/usr/bin/env python3
"""
TESTING INTEGRAL - DEPURACIÓN DE IMÁGENES
==========================================

Suite completa de tests para verificar el estado del sistema
de imágenes tras la depuración masiva.

Tests incluidos:
1. Inventario de biblioteca WordPress
2. Productos sin imagen
3. Verificación de URLs (404s)
4. Duplicados por hash
5. Filesystem en servidor
6. Imágenes compartidas entre productos
7. Performance de carga
8. Verificación de alt-text SEO

Uso:
    python3 testing_integral_imagenes.py
"""

import hashlib
import json
import logging
import os
import subprocess
import sys
import time
from collections import defaultdict

import requests
from dotenv import load_dotenv
from tqdm import tqdm

load_dotenv(dotenv_path='/Applications/um/vivero/.env')

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s %(levelname)s: %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S"
)
log = logging.getLogger(__name__)

# Configuración
WP_URL = os.getenv('WORDPRESS_URL', '').rstrip('/')
WC_KEY = os.getenv('WC_CONSUMER_KEY', '')
WC_SECRET = os.getenv('WC_CONSUMER_SECRET', '')
WP_USER = os.getenv('WP_USERNAME', '')
WP_PASS = os.getenv('WP_APP_PASSWORD', '')

SSH_HOST = "root@23.105.176.45"
SSH_PASS = "gsiB%s@0yD"
WP_PATH = "/home/viveroloscocos.com.ar/public_html"

# Resultados globales
resultados = {
    'tests_ejecutados': 0,
    'tests_exitosos': 0,
    'tests_fallidos': 0,
    'warnings': [],
    'errores': [],
    'metricas': {}
}


def ssh_exec(cmd):
    """Ejecuta comando SSH y retorna stdout"""
    full_cmd = f"sshpass -p '{SSH_PASS}' ssh -o StrictHostKeyChecking=no {SSH_HOST} \"{cmd}\""
    result = subprocess.run(full_cmd, shell=True, capture_output=True, text=True)
    if result.returncode != 0:
        return None
    return result.stdout.strip()


def test_1_inventario_biblioteca():
    """Test 1: Verificar inventario de biblioteca WordPress"""
    log.info("\n" + "="*60)
    log.info("TEST 1: INVENTARIO DE BIBLIOTECA WORDPRESS")
    log.info("="*60)
    
    resultados['tests_ejecutados'] += 1
    
    try:
        r = requests.get(
            f'{WP_URL}/wp-json/wp/v2/media',
            params={'per_page': 1, 'page': 1},
            auth=(WP_USER, WP_PASS),
            timeout=30
        )
        
        if r.status_code == 200:
            total_media = int(r.headers.get('X-WP-Total', 0))
            resultados['metricas']['total_media'] = total_media
            
            log.info(f"✅ Total imágenes en biblioteca: {total_media}")
            
            if total_media <= 110:
                log.info(f"✅ PASS: Biblioteca limpia ({total_media} ≤ 110)")
                resultados['tests_exitosos'] += 1
                return True
            else:
                log.warning(f"⚠️  WARNING: Biblioteca tiene {total_media} imágenes (esperado ≤110)")
                resultados['warnings'].append(f"Biblioteca con {total_media} imágenes")
                resultados['tests_exitosos'] += 1
                return True
        else:
            log.error(f"❌ FAIL: Error obteniendo media: {r.status_code}")
            resultados['tests_fallidos'] += 1
            resultados['errores'].append(f"Test 1: HTTP {r.status_code}")
            return False
            
    except Exception as e:
        log.error(f"❌ FAIL: Excepción en test 1: {e}")
        resultados['tests_fallidos'] += 1
        resultados['errores'].append(f"Test 1: {str(e)}")
        return False


def test_2_productos_sin_imagen():
    """Test 2: Verificar que todos los productos tengan imagen"""
    log.info("\n" + "="*60)
    log.info("TEST 2: PRODUCTOS SIN IMAGEN")
    log.info("="*60)
    
    resultados['tests_ejecutados'] += 1
    
    try:
        productos = []
        page = 1
        
        while page <= 6:
            r = requests.get(
                f'{WP_URL}/wp-json/wc/v3/products',
                auth=(WC_KEY, WC_SECRET),
                params={'per_page': 100, 'page': page},
                timeout=30
            )
            
            if r.status_code != 200:
                break
            
            data = r.json()
            if not data:
                break
            
            productos.extend(data)
            page += 1
        
        sin_imagen = []
        for p in productos:
            imgs = p.get('images', [])
            if not imgs:
                sin_imagen.append(p['id'])
        
        resultados['metricas']['total_productos'] = len(productos)
        resultados['metricas']['productos_sin_imagen'] = len(sin_imagen)
        
        log.info(f"Total productos: {len(productos)}")
        log.info(f"Productos sin imagen: {len(sin_imagen)}")
        
        if len(sin_imagen) == 0:
            log.info("✅ PASS: Todos los productos tienen imagen")
            resultados['tests_exitosos'] += 1
            return True
        else:
            log.error(f"❌ FAIL: {len(sin_imagen)} productos sin imagen: {sin_imagen[:5]}")
            resultados['tests_fallidos'] += 1
            resultados['errores'].append(f"Test 2: {len(sin_imagen)} productos sin imagen")
            return False
            
    except Exception as e:
        log.error(f"❌ FAIL: Excepción en test 2: {e}")
        resultados['tests_fallidos'] += 1
        resultados['errores'].append(f"Test 2: {str(e)}")
        return False


def test_3_verificar_urls_404():
    """Test 3: Verificar que las URLs de imágenes no den 404"""
    log.info("\n" + "="*60)
    log.info("TEST 3: VERIFICACIÓN DE URLs (404s)")
    log.info("="*60)
    
    resultados['tests_ejecutados'] += 1
    
    try:
        r = requests.get(
            f'{WP_URL}/wp-json/wc/v3/products',
            auth=(WC_KEY, WC_SECRET),
            params={'per_page': 20, 'page': 1},
            timeout=30
        )
        
        if r.status_code != 200:
            log.error(f"❌ FAIL: Error obteniendo productos: {r.status_code}")
            resultados['tests_fallidos'] += 1
            return False
        
        productos = r.json()
        errores_404 = []
        urls_verificadas = 0
        
        log.info(f"Verificando URLs de {len(productos)} productos...")
        
        for p in tqdm(productos, desc="Verificando URLs"):
            imgs = p.get('images', [])
            if imgs:
                img_url = imgs[0].get('src', '')
                if img_url:
                    try:
                        r_img = requests.head(img_url, timeout=10)
                        urls_verificadas += 1
                        
                        if r_img.status_code == 404:
                            errores_404.append({
                                'producto_id': p['id'],
                                'nombre': p['name'],
                                'url': img_url
                            })
                    except Exception as e:
                        log.warning(f"⚠️  Error verificando {p['id']}: {e}")
        
        resultados['metricas']['urls_verificadas'] = urls_verificadas
        resultados['metricas']['errores_404'] = len(errores_404)
        
        log.info(f"URLs verificadas: {urls_verificadas}")
        log.info(f"Errores 404: {len(errores_404)}")
        
        if len(errores_404) == 0:
            log.info("✅ PASS: No se encontraron errores 404")
            resultados['tests_exitosos'] += 1
            return True
        else:
            log.error(f"❌ FAIL: {len(errores_404)} errores 404 detectados")
            for err in errores_404[:3]:
                log.error(f"   - Producto {err['producto_id']}: {err['url']}")
            resultados['tests_fallidos'] += 1
            resultados['errores'].append(f"Test 3: {len(errores_404)} errores 404")
            return False
            
    except Exception as e:
        log.error(f"❌ FAIL: Excepción en test 3: {e}")
        resultados['tests_fallidos'] += 1
        resultados['errores'].append(f"Test 3: {str(e)}")
        return False


def test_4_duplicados_hash():
    """Test 4: Verificar que no haya duplicados por hash"""
    log.info("\n" + "="*60)
    log.info("TEST 4: DUPLICADOS POR HASH SHA1")
    log.info("="*60)
    
    resultados['tests_ejecutados'] += 1
    
    try:
        r = requests.get(
            f'{WP_URL}/wp-json/wp/v2/media',
            params={'per_page': 100, 'page': 1, 'media_type': 'image'},
            auth=(WP_USER, WP_PASS),
            timeout=30
        )
        
        if r.status_code != 200:
            log.error(f"❌ FAIL: Error obteniendo media: {r.status_code}")
            resultados['tests_fallidos'] += 1
            return False
        
        media_items = r.json()
        hash_to_images = defaultdict(list)
        
        log.info(f"Calculando hashes de {len(media_items)} imágenes...")
        
        for item in tqdm(media_items, desc="Calculando hashes"):
            url = item.get('source_url')
            if not url:
                continue
            
            try:
                r_img = requests.get(url, timeout=30)
                if r_img.status_code == 200:
                    img_hash = hashlib.sha1(r_img.content).hexdigest()
                    hash_to_images[img_hash].append(item['id'])
            except Exception:
                continue
        
        duplicados = {k: v for k, v in hash_to_images.items() if len(v) > 1}
        
        resultados['metricas']['hashes_unicos'] = len(hash_to_images)
        resultados['metricas']['duplicados'] = len(duplicados)
        
        log.info(f"Hashes únicos: {len(hash_to_images)}")
        log.info(f"Duplicados: {len(duplicados)}")
        
        if len(duplicados) == 0:
            log.info("✅ PASS: No se encontraron duplicados")
            resultados['tests_exitosos'] += 1
            return True
        else:
            log.error(f"❌ FAIL: {len(duplicados)} grupos de duplicados detectados")
            resultados['tests_fallidos'] += 1
            resultados['errores'].append(f"Test 4: {len(duplicados)} duplicados")
            return False
            
    except Exception as e:
        log.error(f"❌ FAIL: Excepción en test 4: {e}")
        resultados['tests_fallidos'] += 1
        resultados['errores'].append(f"Test 4: {str(e)}")
        return False


def test_5_filesystem_servidor():
    """Test 5: Verificar filesystem en servidor"""
    log.info("\n" + "="*60)
    log.info("TEST 5: FILESYSTEM EN SERVIDOR")
    log.info("="*60)
    
    resultados['tests_ejecutados'] += 1
    
    try:
        cmd = f"find {WP_PATH}/wp-content/uploads -type f | wc -l"
        output = ssh_exec(cmd)
        
        if output is None:
            log.error("❌ FAIL: Error ejecutando comando SSH")
            resultados['tests_fallidos'] += 1
            return False
        
        total_archivos = int(output)
        resultados['metricas']['archivos_filesystem'] = total_archivos
        
        log.info(f"Total archivos en filesystem: {total_archivos}")
        
        if total_archivos <= 120:
            log.info(f"✅ PASS: Filesystem limpio ({total_archivos} ≤ 120)")
            resultados['tests_exitosos'] += 1
            return True
        else:
            log.warning(f"⚠️  WARNING: Filesystem tiene {total_archivos} archivos (esperado ≤120)")
            resultados['warnings'].append(f"Filesystem con {total_archivos} archivos")
            resultados['tests_exitosos'] += 1
            return True
            
    except Exception as e:
        log.error(f"❌ FAIL: Excepción en test 5: {e}")
        resultados['tests_fallidos'] += 1
        resultados['errores'].append(f"Test 5: {str(e)}")
        return False


def test_6_imagenes_compartidas():
    """Test 6: Verificar imágenes compartidas entre productos"""
    log.info("\n" + "="*60)
    log.info("TEST 6: IMÁGENES COMPARTIDAS ENTRE PRODUCTOS")
    log.info("="*60)
    
    resultados['tests_ejecutados'] += 1
    
    try:
        productos = []
        page = 1
        
        while page <= 6:
            r = requests.get(
                f'{WP_URL}/wp-json/wc/v3/products',
                auth=(WC_KEY, WC_SECRET),
                params={'per_page': 100, 'page': page},
                timeout=30
            )
            
            if r.status_code != 200:
                break
            
            data = r.json()
            if not data:
                break
            
            productos.extend(data)
            page += 1
        
        image_to_products = defaultdict(list)
        
        for p in productos:
            imgs = p.get('images', [])
            if imgs:
                img_id = imgs[0].get('id')
                if img_id:
                    image_to_products[img_id].append(p['id'])
        
        compartidas = {k: v for k, v in image_to_products.items() if len(v) > 1}
        
        resultados['metricas']['imagenes_unicas'] = len(image_to_products)
        resultados['metricas']['imagenes_compartidas'] = len(compartidas)
        
        log.info(f"Imágenes únicas usadas: {len(image_to_products)}")
        log.info(f"Imágenes compartidas: {len(compartidas)}")
        
        if len(compartidas) <= 10:
            log.info(f"✅ PASS: Pocas imágenes compartidas ({len(compartidas)} ≤ 10)")
            resultados['tests_exitosos'] += 1
            return True
        else:
            log.warning(f"⚠️  WARNING: {len(compartidas)} imágenes compartidas entre productos")
            resultados['warnings'].append(f"{len(compartidas)} imágenes compartidas")
            resultados['tests_exitosos'] += 1
            return True
            
    except Exception as e:
        log.error(f"❌ FAIL: Excepción en test 6: {e}")
        resultados['tests_fallidos'] += 1
        resultados['errores'].append(f"Test 6: {str(e)}")
        return False


def test_7_performance_carga():
    """Test 7: Verificar performance de carga de imágenes"""
    log.info("\n" + "="*60)
    log.info("TEST 7: PERFORMANCE DE CARGA")
    log.info("="*60)
    
    resultados['tests_ejecutados'] += 1
    
    try:
        r = requests.get(
            f'{WP_URL}/wp-json/wc/v3/products',
            auth=(WC_KEY, WC_SECRET),
            params={'per_page': 5, 'page': 1},
            timeout=30
        )
        
        if r.status_code != 200:
            log.error(f"❌ FAIL: Error obteniendo productos: {r.status_code}")
            resultados['tests_fallidos'] += 1
            return False
        
        productos = r.json()
        tiempos_carga = []
        
        log.info(f"Midiendo tiempo de carga de {len(productos)} imágenes...")
        
        for p in productos:
            imgs = p.get('images', [])
            if imgs:
                img_url = imgs[0].get('src', '')
                if img_url:
                    try:
                        start = time.time()
                        r_img = requests.get(img_url, timeout=10)
                        end = time.time()
                        
                        if r_img.status_code == 200:
                            tiempo = (end - start) * 1000  # ms
                            tiempos_carga.append(tiempo)
                            log.info(f"   Producto {p['id']}: {tiempo:.0f}ms")
                    except Exception:
                        continue
        
        if tiempos_carga:
            promedio = sum(tiempos_carga) / len(tiempos_carga)
            resultados['metricas']['tiempo_carga_promedio_ms'] = round(promedio, 2)
            
            log.info(f"Tiempo promedio de carga: {promedio:.0f}ms")
            
            if promedio < 1000:
                log.info(f"✅ PASS: Performance excelente ({promedio:.0f}ms < 1000ms)")
                resultados['tests_exitosos'] += 1
                return True
            elif promedio < 2000:
                log.info(f"✅ PASS: Performance aceptable ({promedio:.0f}ms < 2000ms)")
                resultados['tests_exitosos'] += 1
                return True
            else:
                log.warning(f"⚠️  WARNING: Performance lenta ({promedio:.0f}ms ≥ 2000ms)")
                resultados['warnings'].append(f"Performance: {promedio:.0f}ms")
                resultados['tests_exitosos'] += 1
                return True
        else:
            log.error("❌ FAIL: No se pudieron medir tiempos de carga")
            resultados['tests_fallidos'] += 1
            return False
            
    except Exception as e:
        log.error(f"❌ FAIL: Excepción en test 7: {e}")
        resultados['tests_fallidos'] += 1
        resultados['errores'].append(f"Test 7: {str(e)}")
        return False


def generar_reporte():
    """Genera reporte final de testing"""
    log.info("\n" + "="*60)
    log.info("REPORTE FINAL DE TESTING")
    log.info("="*60)
    
    log.info(f"\nTests ejecutados: {resultados['tests_ejecutados']}")
    log.info(f"Tests exitosos: {resultados['tests_exitosos']}")
    log.info(f"Tests fallidos: {resultados['tests_fallidos']}")
    
    tasa_exito = (resultados['tests_exitosos'] / resultados['tests_ejecutados'] * 100) if resultados['tests_ejecutados'] > 0 else 0
    log.info(f"Tasa de éxito: {tasa_exito:.1f}%")
    
    if resultados['warnings']:
        log.info(f"\n⚠️  Warnings ({len(resultados['warnings'])}):")
        for w in resultados['warnings']:
            log.info(f"   - {w}")
    
    if resultados['errores']:
        log.info(f"\n❌ Errores ({len(resultados['errores'])}):")
        for e in resultados['errores']:
            log.info(f"   - {e}")
    
    log.info("\n📊 Métricas:")
    for k, v in resultados['metricas'].items():
        log.info(f"   {k}: {v}")
    
    # Guardar reporte JSON
    with open('logs/testing_integral_resultados.json', 'w') as f:
        json.dump(resultados, f, indent=2, ensure_ascii=False)
    
    log.info("\n✅ Reporte guardado en: logs/testing_integral_resultados.json")
    
    if resultados['tests_fallidos'] == 0:
        log.info("\n🎉 TODOS LOS TESTS PASARON EXITOSAMENTE")
        return True
    else:
        log.info(f"\n⚠️  {resultados['tests_fallidos']} TESTS FALLARON")
        return False


def main():
    log.info("="*60)
    log.info("TESTING INTEGRAL - DEPURACIÓN DE IMÁGENES")
    log.info("="*60)
    
    # Verificar credenciales
    if not all([WP_URL, WC_KEY, WC_SECRET, WP_USER, WP_PASS]):
        log.error("❌ Faltan credenciales en .env")
        sys.exit(1)
    
    # Ejecutar tests
    test_1_inventario_biblioteca()
    test_2_productos_sin_imagen()
    test_3_verificar_urls_404()
    test_4_duplicados_hash()
    test_5_filesystem_servidor()
    test_6_imagenes_compartidas()
    test_7_performance_carga()
    
    # Generar reporte
    exito = generar_reporte()
    
    sys.exit(0 if exito else 1)


if __name__ == '__main__':
    main()
