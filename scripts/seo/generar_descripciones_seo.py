#!/usr/bin/env python3
"""
GENERADOR AUTOMÁTICO DE DESCRIPCIONES SEO
==========================================
Genera descripciones únicas de 200-300 palabras para TODOS los productos
Basado en SKU, categoría, características y base de conocimiento de plantas
"""

import requests
from requests.auth import HTTPBasicAuth
import sys
from typing import List, Dict, Optional
import time
import re
import argparse


# Base de conocimiento de plantas (expandida)
PLANT_KNOWLEDGE = {
    "olivo": {
        "nombre_cientifico": "Olea europaea",
        "tipo": "árbol frutal mediterráneo",
        "caracteristicas": [
            "resistente a la sequía",
            "follaje plateado ornamental",
            "produce aceitunas comestibles",
            "longevidad excepcional",
            "ideal para clima seco mendocino"
        ],
        "cuidados": "Requiere exposición solar directa, riego moderado una vez establecido, tolera suelos pobres.",
        "altura": "3-8 metros en madurez",
        "beneficios": "Sombra moderada, frutos para consumo o producción de aceite, bajo mantenimiento"
    },
    "jazmin": {
        "nombre_cientifico": "Jasminum",
        "tipo": "arbusto floral perfumado",
        "caracteristicas": [
            "flores blancas intensamente aromáticas",
            "floración primavera-verano",
            "trepadora o arbusto versátil",
            "perenne",
            "ideal para cercos y pérgolas"
        ],
        "cuidados": "Necesita sol pleno o media sombra, riego regular, poda anual para mantener forma.",
        "altura": "2-4 metros",
        "beneficios": "Perfume natural del jardín, atrae polinizadores, privacidad visual"
    },
    "jazlluv": {
        "nombre_cientifico": "Jasminum nudiflorum",
        "tipo": "arbusto floral de invierno",
        "caracteristicas": [
            "flores amarillas brillantes en invierno",
            "florece sin hojas",
            "perfumado intenso",
            "resistente a heladas",
            "cascada dorada ornamental"
        ],
        "cuidados": "Sol pleno, riego moderado, muy resistente al frío.",
        "altura": "1.5-3 metros",
        "beneficios": "Color en invierno cuando otros jardines están grises, bajo mantenimiento"
    },
    "rosa": {
        "nombre_cientifico": "Rosa",
        "tipo": "arbusto floral clásico",
        "caracteristicas": [
            "flores perfumadas en variedad de colores",
            "floración repetida",
            "elegancia atemporal",
            "ideal para corte",
            "símbolo de belleza"
        ],
        "cuidados": "Sol pleno, riego regular, fertilización mensual, poda invernal.",
        "altura": "0.5-2 metros según variedad",
        "beneficios": "Flores para floreros, belleza clásica, variedad infinita de colores"
    },
    "bougan": {
        "nombre_cientifico": "Bougainvillea",
        "tipo": "enredadera floral espectacular",
        "caracteristicas": [
            "brácteas coloridas todo el año",
            "muy resistente a sequía",
            "trepadora vigorosa",
            "colores intensos",
            "bajo mantenimiento"
        ],
        "cuidados": "Sol pleno obligatorio, riego escaso una vez establecida, poda para controlar tamaño.",
        "altura": "3-10 metros",
        "beneficios": "Cobertura rápida de muros y pérgolas, color permanente, casi indestructible"
    },
    "glici": {
        "nombre_cientifico": "Wisteria",
        "tipo": "trepadora floral espectacular",
        "caracteristicas": [
            "flores colgantes en racimos largos",
            "perfumada intensamente",
            "floración primaveral masiva",
            "trepadora vigorosa",
            "colores lila, blanco, rosado"
        ],
        "cuidados": "Sol pleno, riego regular, estructura fuerte para soporte, poda después de floración.",
        "altura": "5-15 metros",
        "beneficios": "Espectáculo visual en primavera, sombra densa en verano, aroma embriagador"
    },
    "drac": {
        "nombre_cientifico": "Dracaena",
        "tipo": "planta de interior purificadora",
        "caracteristicas": [
            "purifica aire interior",
            "bajo mantenimiento",
            "follaje decorativo variegado",
            "resistente a sombra",
            "ideal oficinas y hogares"
        ],
        "cuidados": "Media sombra o sombra, riego cuando la tierra esté seca, limpieza de hojas mensual.",
        "altura": "0.5-2 metros en maceta",
        "beneficios": "Mejora calidad del aire, decoración verde sin complicaciones, larga vida"
    },
}


# Plantillas de descripción por tipo
DESCRIPTION_TEMPLATES = {
    "planta_con_info": """
El {nombre_completo} ({nombre_cientifico}) en presentación de {tamaño} es {tipo} perfecto para jardines mendocinos. {intro_especial}

Características principales:
- Tamaño: {tamaño} (planta {estado_desarrollo})
- Tipo: {categoria}
- {caracteristica_1}
- {caracteristica_2}
- {caracteristica_3}
- {caracteristica_destaque}

{seccion_cuidados}

Beneficios para tu jardín:
{beneficios}

Vivero Los Cocos te garantiza plantas de calidad superior, cultivadas en Mendoza para adaptarse perfectamente al clima local. Stock permanente y envío rápido en toda la provincia. Comprá con confianza, vendemos plantas saludables y listas para embellecer tu espacio verde.
""",
    
    "planta_generica": """
{nombre_producto} es una excelente opción para tu jardín o espacio verde. En presentación de {tamaño}, esta planta ha sido cultivada con los más altos estándares de calidad en Vivero Los Cocos, Mendoza.

Características del producto:
- Tamaño: {tamaño} - planta lista para plantar
- Cultivada en Mendoza, adaptada al clima local
- Stock disponible para entrega inmediata
- Garantía de calidad y plantas saludables

Cuidados generales:
Todas nuestras plantas vienen con instrucciones básicas de cuidado. Nuestro equipo de expertos está disponible para asesorarte sobre el mejor lugar de plantación, riego y mantenimiento según las condiciones específicas de tu jardín.

¿Por qué elegir Vivero Los Cocos?
- Más de 20 años de experiencia en el sector
- Plantas cultivadas localmente para mejor adaptación
- Envío rápido y seguro en Mendoza y alrededores
- Asesoramiento profesional sin cargo
- Stock permanente y variedad incomparable

Comprá con confianza. Tu jardín merece lo mejor, y nosotros te lo ofrecemos.
""",

    "maceta": """
{nombre_producto} es una maceta de alta calidad, perfecta para tus plantas y flores. {descripcion_maceta}

Características técnicas:
- Tamaño: {tamaño}
- Material: {material}
- Color: {color}
- Uso: {uso_recomendado}

Ideal para:
{ideal_para}

Calidad garantizada:
En Vivero Los Cocos seleccionamos cuidadosamente cada maceta para asegurar durabilidad y funcionalidad. {material} de primera calidad que resiste las condiciones climáticas de Mendoza.

Combiná esta maceta con nuestras plantas para crear composiciones espectaculares. Tenemos el conocimiento para ayudarte a elegir la combinación perfecta planta-maceta según tus necesidades.

Stock disponible para entrega inmediata. Comprá online y recibí en tu domicilio o retirá por nuestro vivero.
"""
}


class SEODescriptionGenerator:
    """Generador de descripciones SEO únicas"""
    
    def __init__(self, url: str, consumer_key: str, consumer_secret: str):
        self.url = url.rstrip('/')
        self.consumer_key = consumer_key
        self.consumer_secret = consumer_secret
        self.session = requests.Session()
        self.session.auth = HTTPBasicAuth(consumer_key, consumer_secret)
    
    def get_all_products(self) -> List[Dict]:
        """Obtiene todos los productos"""
        all_products = []
        page = 1
        
        print("📦 Obteniendo productos...")
        
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
    
    def decode_sku_info(self, sku: str) -> Dict:
        """Decodifica información del SKU"""
        sku_lower = sku.lower()
        
        # Extraer tamaño
        size_match = re.search(r'(\d+)\s*(l|litros?|cm)', sku_lower)
        size = None
        if size_match:
            num = size_match.group(1)
            unit = "Litros" if 'l' in size_match.group(2) else "cm"
            size = f"{num} {unit}"
        
        # Detectar tipo de producto
        is_maceta = any(x in sku_lower for x in ['mta', 'maceta', 'matri', 'jardinera'])
        
        # Buscar código de planta
        plant_code = None
        for key in PLANT_KNOWLEDGE.keys():
            if key in sku_lower:
                plant_code = key
                break
        
        return {
            'size': size,
            'is_maceta': is_maceta,
            'plant_code': plant_code
        }
    
    def generate_description(self, product: Dict) -> str:
        """Genera descripción SEO única para un producto"""
        title = product['name']
        sku = product.get('sku', '')
        categories = product.get('categories', [])
        
        sku_info = self.decode_sku_info(sku)
        
        # CASO 1: Planta con información botánica
        if sku_info['plant_code'] and sku_info['plant_code'] in PLANT_KNOWLEDGE:
            plant = PLANT_KNOWLEDGE[sku_info['plant_code']]
            
            # Determinar estado de desarrollo por tamaño
            estado_dev = "desarrollada lista para plantar"
            if sku_info['size']:
                if 'Litros' in sku_info['size']:
                    litros = int(re.search(r'\d+', sku_info['size']).group())
                    if litros <= 5:
                        estado_dev = "joven ideal para crecer con tu jardín"
                    elif litros <= 15:
                        estado_dev = "desarrollada lista para plantar"
                    else:
                        estado_dev = "de gran tamaño con presencia inmediata"
            
            description = DESCRIPTION_TEMPLATES['planta_con_info'].format(
                nombre_completo=plant.get('nombre_cientifico', title.split()[0]),
                nombre_cientifico=plant['nombre_cientifico'],
                tamaño=sku_info['size'] or "tamaño estándar",
                tipo=plant['tipo'],
                intro_especial=plant['caracteristicas'][0].capitalize() + ", esta planta es ideal para el clima mendocino.",
                estado_desarrollo=estado_dev,
                categoria=plant['tipo'].capitalize(),
                caracteristica_1=plant['caracteristicas'][0].capitalize(),
                caracteristica_2=plant['caracteristicas'][1].capitalize() if len(plant['caracteristicas']) > 1 else "Fácil mantenimiento",
                caracteristica_3=plant['caracteristicas'][2].capitalize() if len(plant['caracteristicas']) > 2 else "Adaptada al clima local",
                caracteristica_destaque=plant['caracteristicas'][3].capitalize() if len(plant['caracteristicas']) > 3 else "Alta resistencia",
                seccion_cuidados=f"Cuidados:\n{plant['cuidados']}",
                beneficios=plant.get('beneficios', 'Embellece tu jardín con naturaleza de calidad.')
            )
            
        # CASO 2: Maceta
        elif sku_info['is_maceta']:
            # Extraer color del título
            color = "disponible en varios colores"
            if 'Color:' in title:
                color_match = re.search(r'Color:\s*(\w+(?:\s+\w+)?)', title)
                if color_match:
                    color = color_match.group(1)
            
            material = "plástico de alta calidad" if 'Plástica' in title or 'Plastic' in title else "material resistente"
            
            description = DESCRIPTION_TEMPLATES['maceta'].format(
                nombre_producto=title.split('.')[0] if '.' in title else title,
                descripcion_maceta="Diseñada para ofrecer el espacio óptimo para el desarrollo de raíces.",
                tamaño=sku_info['size'] or "varios tamaños disponibles",
                material=material.capitalize(),
                color=color,
                uso_recomendado="interior y exterior",
                ideal_para="- Plantas ornamentales\n- Flores de estación\n- Hierbas aromáticas\n- Suculentas y cactus"
            )
            
        # CASO 3: Planta genérica (sin info específica)
        else:
            description = DESCRIPTION_TEMPLATES['planta_generica'].format(
                nombre_producto=title,
                tamaño=sku_info['size'] or "presentación estándar"
            )
        
        # Limpiar y formatear
        description = description.strip()
        description = re.sub(r'\n{3,}', '\n\n', description)  # Max 2 líneas en blanco
        
        return description
    
    def update_product_description(self, product_id: int, description: str, dry_run: bool = False) -> bool:
        """Actualiza la descripción de un producto"""
        if dry_run:
            return True
        
        endpoint = f"{self.url}/wp-json/wc/v3/products/{product_id}"
        data = {
            'description': description
        }
        
        try:
            response = self.session.put(endpoint, json=data)
            response.raise_for_status()
            return True
        except Exception as e:
            print(f"  ✗ Error actualizando producto {product_id}: {e}")
            return False
    
    def run(self, batch_size: int = 50, dry_run: bool = False, force: bool = False):
        """Ejecuta el generador de descripciones"""
        print("\n" + "="*70)
        print("📝 GENERADOR DE DESCRIPCIONES SEO")
        print("="*70 + "\n")
        
        products = self.get_all_products()
        total = len(products)
        
        # Filtrar productos sin descripción (o forzar todos)
        if not force:
            products = [p for p in products if not p.get('description') or len(p['description'].strip()) == 0]
            print(f"📊 Productos sin descripción: {len(products)}/{total}\n")
        else:
            print(f"📊 Modo FORCE: Actualizando TODOS los {total} productos\n")
        
        if len(products) == 0:
            print("✓ Todos los productos ya tienen descripción!")
            return
        
        updated = 0
        errors = 0
        
        print(f"🔄 Procesando {len(products)} productos...\n")
        
        for i, product in enumerate(products, 1):
            product_id = product['id']
            title = product['name']
            
            print(f"{'='*70}")
            print(f"Producto {i}/{len(products)}")
            print(f"{'='*70}")
            print(f"ID: {product_id}")
            print(f"Título: {title}")
            
            # Generar descripción
            description = self.generate_description(product)
            
            print(f"\n📝 Descripción generada ({len(description)} caracteres):")
            print(f"{description[:200]}...")
            
            if not dry_run:
                # Actualizar en WooCommerce
                print(f"\n🔄 Actualizando...")
                if self.update_product_description(product_id, description, dry_run):
                    print(f"✅ Actualizado correctamente")
                    updated += 1
                else:
                    print(f"❌ Error al actualizar")
                    errors += 1
                
                # Rate limiting
                time.sleep(1)
            else:
                print(f"\n⏭️  DRY-RUN - No se actualizó")
                updated += 1
            
            print()
            
            # Pausa cada batch
            if i % batch_size == 0 and i < len(products):
                print(f"\n⏸️  Pausa de 5 segundos... ({i}/{len(products)} completados)")
                time.sleep(5)
        
        # Resumen
        print("\n" + "="*70)
        print("📊 RESUMEN FINAL")
        print("="*70)
        print(f"\nTotal procesados: {len(products)}")
        print(f"✅ Actualizados: {updated}")
        print(f"❌ Errores: {errors}")
        print(f"📈 Tasa de éxito: {(updated/len(products)*100):.1f}%")
        print("\n" + "="*70)


def main():
    parser = argparse.ArgumentParser(description='Generar descripciones SEO')
    parser.add_argument('--url', required=True, help='URL del sitio')
    parser.add_argument('--key', required=True, help='Consumer Key')
    parser.add_argument('--secret', required=True, help='Consumer Secret')
    parser.add_argument('--batch-size', type=int, default=50, help='Tamaño del batch')
    parser.add_argument('--dry-run', action='store_true', help='Solo simular')
    parser.add_argument('--force', action='store_true', help='Actualizar todos, incluso con descripción')
    
    args = parser.parse_args()
    
    generator = SEODescriptionGenerator(args.url, args.key, args.secret)
    generator.run(args.batch_size, args.dry_run, args.force)


if __name__ == "__main__":
    main()
