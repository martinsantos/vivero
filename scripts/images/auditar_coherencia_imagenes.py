#!/usr/bin/env python3
"""
AUDITORÍA DE COHERENCIA DE IMÁGENES - VIVERO LOS COCOS
=======================================================

Analiza todas las imágenes asignadas a productos para detectar:
1. Imágenes repetidas entre productos
2. Imágenes incoherentes con el nombre/descripción del producto
3. Productos con la misma imagen (posibles duplicados)
4. Análisis de contenido visual mediante URLs

Uso:
    python3 auditar_coherencia_imagenes.py
"""

import hashlib
import json
import logging
import os
import re
from collections import defaultdict
from urllib.parse import urlparse

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

# Resultados
auditoria = {
    'total_productos': 0,
    'productos_con_imagen': 0,
    'productos_sin_imagen': 0,
    'imagenes_unicas': 0,
    'imagenes_compartidas': {},
    'productos_por_imagen': {},
    'incoherencias': [],
    'recomendaciones': []
}


def obtener_productos():
    """Obtiene todos los productos con sus imágenes"""
    log.info("Obteniendo productos desde WooCommerce...")
    
    productos = []
    page = 1
    
    while page <= 10:
        try:
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
            
        except Exception as e:
            log.error(f"Error obteniendo productos página {page}: {e}")
            break
    
    log.info(f"Total productos obtenidos: {len(productos)}")
    return productos


def extraer_terminos_clave(texto):
    """Extrae términos clave de un texto para análisis de coherencia"""
    if not texto:
        return set()
    
    # Convertir a minúsculas y limpiar
    texto = texto.lower()
    
    # Palabras clave comunes en productos de vivero
    terminos_plantas = [
        'abedul', 'acacia', 'aguaribay', 'alamo', 'algarrobo', 'araucaria',
        'arbol', 'arbusto', 'azalea', 'bambu', 'begonia', 'bigros', 'brachi',
        'camellia', 'cedro', 'cerezo', 'ciprés', 'ciruelo', 'crespón',
        'duraznero', 'eucalipto', 'fresno', 'ginkgo', 'glicina', 'granado',
        'hibisco', 'hortensia', 'jacaranda', 'jazmin', 'laurel', 'limonero',
        'liquidambar', 'magnolia', 'manzano', 'morera', 'naranjo', 'nogal',
        'olivo', 'palmera', 'paraiso', 'pino', 'platano', 'roble', 'rosa',
        'sauce', 'tilo', 'tulipan', 'vid', 'wisteria'
    ]
    
    terminos_encontrados = set()
    
    for termino in terminos_plantas:
        if termino in texto:
            terminos_encontrados.add(termino)
    
    return terminos_encontrados


def analizar_coherencia_nombre_imagen(producto):
    """Analiza si la imagen es coherente con el nombre del producto"""
    nombre = producto.get('name', '').lower()
    imgs = producto.get('images', [])
    
    if not imgs:
        return None
    
    img_url = imgs[0].get('src', '')
    img_alt = imgs[0].get('alt', '').lower()
    
    # Extraer términos clave del nombre
    terminos_nombre = extraer_terminos_clave(nombre)
    terminos_alt = extraer_terminos_clave(img_alt)
    
    # Verificar coherencia
    if terminos_nombre and terminos_alt:
        coincidencias = terminos_nombre.intersection(terminos_alt)
        if coincidencias:
            return {
                'coherente': True,
                'coincidencias': list(coincidencias),
                'confianza': 'alta'
            }
        else:
            return {
                'coherente': False,
                'terminos_nombre': list(terminos_nombre),
                'terminos_alt': list(terminos_alt),
                'confianza': 'baja'
            }
    
    # Si no hay términos reconocibles, verificar si el alt está vacío
    if not img_alt or img_alt == nombre.lower():
        return {
            'coherente': 'desconocido',
            'razon': 'alt-text genérico o vacío',
            'confianza': 'media'
        }
    
    return {
        'coherente': 'desconocido',
        'razon': 'no se pudieron extraer términos clave',
        'confianza': 'baja'
    }


def analizar_productos():
    """Análisis completo de productos e imágenes"""
    log.info("\n" + "="*60)
    log.info("AUDITORÍA DE COHERENCIA DE IMÁGENES")
    log.info("="*60)
    
    productos = obtener_productos()
    auditoria['total_productos'] = len(productos)
    
    # Mapeo de imágenes a productos
    imagen_a_productos = defaultdict(list)
    productos_sin_imagen = []
    productos_con_imagen = []
    
    log.info("\nAnalizando asignación de imágenes...")
    
    for p in tqdm(productos, desc="Procesando productos"):
        imgs = p.get('images', [])
        
        if not imgs:
            productos_sin_imagen.append({
                'id': p['id'],
                'nombre': p['name'],
                'sku': p.get('sku', 'N/A')
            })
        else:
            productos_con_imagen.append(p)
            img_id = imgs[0].get('id')
            img_url = imgs[0].get('src', '')
            
            if img_id:
                imagen_a_productos[img_id].append({
                    'id': p['id'],
                    'nombre': p['name'],
                    'sku': p.get('sku', 'N/A'),
                    'url': img_url
                })
    
    auditoria['productos_con_imagen'] = len(productos_con_imagen)
    auditoria['productos_sin_imagen'] = len(productos_sin_imagen)
    auditoria['imagenes_unicas'] = len(imagen_a_productos)
    
    # Identificar imágenes compartidas
    log.info("\nIdentificando imágenes compartidas...")
    
    imagenes_compartidas = {}
    for img_id, prods in imagen_a_productos.items():
        if len(prods) > 1:
            imagenes_compartidas[img_id] = prods
    
    auditoria['imagenes_compartidas'] = imagenes_compartidas
    auditoria['productos_por_imagen'] = dict(imagen_a_productos)
    
    # Analizar coherencia
    log.info("\nAnalizando coherencia nombre-imagen...")
    
    incoherencias = []
    coherentes = 0
    incoherentes = 0
    desconocidos = 0
    
    for p in tqdm(productos_con_imagen, desc="Analizando coherencia"):
        resultado = analizar_coherencia_nombre_imagen(p)
        
        if resultado:
            if resultado.get('coherente') == True:
                coherentes += 1
            elif resultado.get('coherente') == False:
                incoherentes += 1
                incoherencias.append({
                    'producto_id': p['id'],
                    'nombre': p['name'],
                    'sku': p.get('sku', 'N/A'),
                    'imagen_url': p['images'][0].get('src', ''),
                    'imagen_alt': p['images'][0].get('alt', ''),
                    'analisis': resultado
                })
            else:
                desconocidos += 1
    
    auditoria['incoherencias'] = incoherencias
    
    # Generar reporte
    log.info("\n" + "="*60)
    log.info("RESULTADOS DE AUDITORÍA")
    log.info("="*60)
    
    log.info(f"\n📊 Estadísticas Generales:")
    log.info(f"   Total productos: {auditoria['total_productos']}")
    log.info(f"   Con imagen: {auditoria['productos_con_imagen']} ({auditoria['productos_con_imagen']/auditoria['total_productos']*100:.1f}%)")
    log.info(f"   Sin imagen: {auditoria['productos_sin_imagen']}")
    log.info(f"   Imágenes únicas: {auditoria['imagenes_unicas']}")
    
    log.info(f"\n🔄 Imágenes Compartidas:")
    log.info(f"   Total imágenes compartidas: {len(imagenes_compartidas)}")
    log.info(f"   Productos afectados: {sum(len(prods) for prods in imagenes_compartidas.values())}")
    
    if imagenes_compartidas:
        log.info(f"\n   Top 10 imágenes más compartidas:")
        sorted_shared = sorted(imagenes_compartidas.items(), key=lambda x: len(x[1]), reverse=True)
        for i, (img_id, prods) in enumerate(sorted_shared[:10], 1):
            log.info(f"   {i}. Imagen ID {img_id}: {len(prods)} productos")
            for prod in prods[:3]:
                log.info(f"      - {prod['nombre']} (ID: {prod['id']})")
            if len(prods) > 3:
                log.info(f"      ... y {len(prods)-3} más")
    
    log.info(f"\n🔍 Análisis de Coherencia:")
    log.info(f"   Coherentes: {coherentes} ({coherentes/len(productos_con_imagen)*100:.1f}%)")
    log.info(f"   Incoherentes: {incoherentes} ({incoherentes/len(productos_con_imagen)*100:.1f}%)")
    log.info(f"   Desconocidos: {desconocidos} ({desconocidos/len(productos_con_imagen)*100:.1f}%)")
    
    if incoherencias:
        log.info(f"\n   ⚠️  Top 10 incoherencias detectadas:")
        for i, inc in enumerate(incoherencias[:10], 1):
            log.info(f"   {i}. {inc['nombre']} (ID: {inc['producto_id']})")
            log.info(f"      Términos nombre: {inc['analisis'].get('terminos_nombre', [])}")
            log.info(f"      Términos alt: {inc['analisis'].get('terminos_alt', [])}")
    
    # Generar recomendaciones
    generar_recomendaciones(imagenes_compartidas, incoherencias)
    
    # Guardar reporte JSON
    with open('logs/auditoria_coherencia_imagenes.json', 'w') as f:
        json.dump(auditoria, f, indent=2, ensure_ascii=False)
    
    log.info("\n✅ Reporte guardado en: logs/auditoria_coherencia_imagenes.json")


def generar_recomendaciones(imagenes_compartidas, incoherencias):
    """Genera recomendaciones basadas en el análisis"""
    log.info("\n" + "="*60)
    log.info("RECOMENDACIONES")
    log.info("="*60)
    
    recomendaciones = []
    
    # Recomendación 1: Imágenes compartidas
    if len(imagenes_compartidas) > 10:
        rec = {
            'prioridad': 'alta',
            'categoria': 'imagenes_compartidas',
            'descripcion': f'{len(imagenes_compartidas)} imágenes compartidas entre múltiples productos',
            'accion': 'Asignar imágenes únicas a cada producto usando wc_image_automation.py',
            'comando': 'python3 wc_image_automation.py --target with-images --providers pexels,pixabay,flickr --global-dedupe --commit'
        }
        recomendaciones.append(rec)
        log.info(f"\n⚠️  PRIORIDAD ALTA:")
        log.info(f"   {rec['descripcion']}")
        log.info(f"   Acción: {rec['accion']}")
    
    # Recomendación 2: Incoherencias
    if len(incoherencias) > 20:
        rec = {
            'prioridad': 'media',
            'categoria': 'incoherencias',
            'descripcion': f'{len(incoherencias)} productos con imágenes potencialmente incoherentes',
            'accion': 'Revisar manualmente y reasignar imágenes específicas',
            'comando': 'Revisar logs/auditoria_coherencia_imagenes.json sección "incoherencias"'
        }
        recomendaciones.append(rec)
        log.info(f"\n⚠️  PRIORIDAD MEDIA:")
        log.info(f"   {rec['descripcion']}")
        log.info(f"   Acción: {rec['accion']}")
    
    # Recomendación 3: Productos sin imagen
    if auditoria['productos_sin_imagen'] > 0:
        rec = {
            'prioridad': 'alta',
            'categoria': 'sin_imagen',
            'descripcion': f'{auditoria["productos_sin_imagen"]} productos sin imagen',
            'accion': 'Asignar imágenes inmediatamente',
            'comando': 'python3 wc_image_automation.py --target without-images --commit'
        }
        recomendaciones.append(rec)
        log.info(f"\n⚠️  PRIORIDAD ALTA:")
        log.info(f"   {rec['descripcion']}")
        log.info(f"   Acción: {rec['accion']}")
    
    # Recomendación 4: Optimización general
    if len(imagenes_compartidas) > 0 or len(incoherencias) > 0:
        rec = {
            'prioridad': 'baja',
            'categoria': 'optimizacion',
            'descripcion': 'Optimización general del catálogo de imágenes',
            'accion': 'Ejecutar auditoría mensual y mantener imágenes únicas',
            'comando': 'python3 auditar_coherencia_imagenes.py'
        }
        recomendaciones.append(rec)
        log.info(f"\n💡 RECOMENDACIÓN:")
        log.info(f"   {rec['descripcion']}")
        log.info(f"   Acción: {rec['accion']}")
    
    auditoria['recomendaciones'] = recomendaciones


def main():
    log.info("="*60)
    log.info("AUDITORÍA DE COHERENCIA DE IMÁGENES - VIVERO LOS COCOS")
    log.info("="*60)
    
    # Verificar credenciales
    if not all([WP_URL, WC_KEY, WC_SECRET]):
        log.error("❌ Faltan credenciales en .env")
        return
    
    # Ejecutar análisis
    analizar_productos()
    
    log.info("\n" + "="*60)
    log.info("AUDITORÍA COMPLETADA")
    log.info("="*60)


if __name__ == '__main__':
    main()
