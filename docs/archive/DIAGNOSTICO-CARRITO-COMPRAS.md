# 🛒 DIAGNÓSTICO: FUNCIONALIDAD AGREGAR AL CARRITO

**Fecha:** 6 de Octubre 2025 - 19:58 ART  
**Sitio:** https://viveroloscocos.com.ar  
**Estado:** ⚠️ **PROBLEMA CRÍTICO DETECTADO**

---

## 🚨 PROBLEMA PRINCIPAL

### ❌ PRODUCTOS NO PUEDEN AGREGARSE AL CARRITO

**Causa raíz identificada:**
- **538/538 productos (100%)** SIN PRECIO configurado
- **538/538 productos (100%)** SIN STOCK configurado

**Impacto:**
- ❌ Botón "Agregar al carrito" no funcional
- ❌ Imposible realizar compras
- ❌ Carrito no puede procesar productos sin precio

---

## 📊 ANÁLISIS TÉCNICO

### Estado de WooCommerce

| Componente | Estado | Detalle |
|------------|--------|---------|
| **WooCommerce** | ✅ Activo | Versión 10.1.2 |
| **Scripts JS** | ✅ Cargando | 7 scripts detectados |
| **Tema** | ✅ loscocos-clean | Activo |
| **CSS Custom** | ✅ Aplicado | 798 líneas activas |

### Estado de Productos

| Métrica | Cantidad | Porcentaje |
|---------|----------|------------|
| Total productos | 538 | 100% |
| Con precio > $0 | 0 | 0% ❌ |
| En stock | 0 | 0% ❌ |
| Comprables (flag) | 538 | 100% ✅ |
| Con imágenes | 538 | 100% ✅ |

### Ejemplo de Producto

```
ID: 548
Nombre: Jazmín Lluvia de Oro 3 Litros
Precio: $0 ❌
Stock: outofstock ❌
Comprable (flag): true
Imagen: ✅ Presente
URL: https://viveroloscocos.com.ar/product/jazlluv3l/
```

---

## 🔍 TESTS REALIZADOS

### 1. Página de Tienda
- ✅ URL funcional (HTTP 200)
- ✅ Scripts WooCommerce cargando
- ⚠️ CSS custom no se detecta en HTML (requiere hard refresh)
- ❌ Botones "Agregar al carrito" no encontrados (productos sin precio)

### 2. Página de Producto Individual
- ✅ Página carga correctamente
- ⚠️ Botón presente pero inactivo (sin precio)
- ✅ Formulario de carrito presente
- ❌ No se puede agregar al carrito (precio = $0)

### 3. API WooCommerce
- ✅ REST API funcional
- ✅ Productos accesibles vía API
- ❌ Todos los productos precio = 0
- ❌ Todos los productos stock = "outofstock"

---

## 🎯 CAUSA RAÍZ

### Importación de Productos Sin Datos Comerciales

Los 538 productos fueron importados con:
- ✅ Nombres
- ✅ SKUs
- ✅ Categorías
- ✅ Tags
- ✅ Imágenes
- ❌ **Precios** (no configurados)
- ❌ **Stock** (no configurado)

**Origen probable:**
- CSV de importación sin columnas `regular_price` y `stock`
- O columnas vacías durante importación
- Configuración manual de precios pendiente

---

## 💡 SOLUCIONES PROPUESTAS

### Opción 1: Configuración Manual (Recomendada para producción)

**Proceso:**
1. Acceder a WP Admin → Productos
2. Configurar precio y stock producto por producto
3. O edición masiva desde lista de productos

**Ventajas:**
- Control total sobre precios reales
- Gestión profesional de inventario

**Desventajas:**
- Tiempo requerido: ~10-20 min por 50 productos
- 538 productos = ~2-3 horas de trabajo

---

### Opción 2: Script Automático de Precios de Prueba

**Implementación rápida** para testing:

```python
# Configurar precios de prueba basados en categoría
precios_por_categoria = {
    'Plantas': 1500,
    'Macetas': 800,
    'Herramientas': 500,
    'Sustratos': 300
}

# Configurar stock = "instock" para todos
stock_status = "instock"
stock_quantity = 10
```

**Ventajas:**
- Testing inmediato de funcionalidad
- Verificación de carrito y checkout
- Base para ajustes posteriores

**Desventajas:**
- Precios no reales
- Requiere revisión posterior

---

### Opción 3: Importación Masiva con CSV Actualizado

**Proceso:**
1. Exportar productos actuales
2. Agregar columnas `regular_price` y `stock`
3. Completar con datos reales
4. Re-importar

**Ventajas:**
- Solución definitiva
- Datos centralizados en CSV

**Desventajas:**
- Requiere datos de precios reales
- Riesgo de sobrescribir datos

---

## 🛠️ SCRIPT DE SOLUCIÓN RÁPIDA

### Configurar Precios y Stock de Prueba

```python
#!/usr/bin/env python3
"""
Configurar precios y stock de prueba para habilitar compras
"""

import os, requests
from dotenv import load_dotenv

load_dotenv()
url_base = os.getenv('WORDPRESS_URL').rstrip('/') + '/wp-json/wc/v3/products'
auth = (os.getenv('WC_CONSUMER_KEY'), os.getenv('WC_CONSUMER_SECRET'))

# Obtener productos
productos = []
page = 1
while True:
    r = requests.get(url_base, auth=auth, params={'per_page': 100, 'page': page})
    data = r.json()
    if not data:
        break
    productos.extend(data)
    page += 1

# Configurar precios de prueba
for producto in productos:
    producto_id = producto['id']
    
    # Precio de prueba (ejemplo)
    precio_prueba = 1000  # $1000 pesos
    
    # Actualizar producto
    datos = {
        'regular_price': str(precio_prueba),
        'stock_status': 'instock',
        'manage_stock': True,
        'stock_quantity': 10
    }
    
    r = requests.put(f'{url_base}/{producto_id}', json=datos, auth=auth)
    if r.status_code == 200:
        print(f'✅ Producto {producto_id} actualizado')
    else:
        print(f'❌ Error en producto {producto_id}')
```

---

## ✅ RECOMENDACIÓN

### Para Ambiente de Testing (Inmediato)

1. **Ejecutar script de precios de prueba**
   - Precio base: $1000
   - Stock: 10 unidades
   - Tiempo: 5-10 minutos

2. **Verificar funcionalidad**
   - Test agregar al carrito
   - Test proceso checkout
   - Test email confirmación

### Para Producción (Definitivo)

1. **Obtener lista de precios reales**
   - Por categoría o producto
   - Del cliente/proveedor

2. **Importar vía CSV actualizado**
   - Columnas: `ID`, `regular_price`, `stock_quantity`
   - WooCommerce → Herramientas → Importar

3. **Configurar gestión de stock**
   - WooCommerce → Ajustes → Productos → Inventario
   - Activar notificaciones de stock bajo

---

## 📋 CHECKLIST POST-CONFIGURACIÓN

Una vez configurados precios y stock:

- [ ] Verificar botón "Agregar al carrito" visible
- [ ] Test agregar producto al carrito
- [ ] Verificar página /carrito/ funcional
- [ ] Test proceso checkout completo
- [ ] Verificar emails de WooCommerce
- [ ] Test responsive en mobile
- [ ] Verificar cálculo de envío (si aplica)
- [ ] Test métodos de pago configurados

---

## 🔗 ENLACES ÚTILES

**Documentación:**
- [WooCommerce: Gestión de Stock](https://woocommerce.com/document/managing-products/#inventory-tab)
- [WooCommerce: Importar Productos](https://woocommerce.com/document/product-csv-importer-exporter/)

**Herramientas:**
- [WP Admin - Productos](https://viveroloscocos.com.ar/wp-admin/edit.php?post_type=product)
- [WooCommerce Settings](https://viveroloscocos.com.ar/wp-admin/admin.php?page=wc-settings)

---

## 📊 ESTADO ACTUAL

```
Funcionalidad Carrito: ❌ NO FUNCIONAL
Causa: Sin precios ni stock
Solución: Configurar precios y stock
Tiempo estimado: 5-10 min (prueba) | 2-3h (producción)
Prioridad: 🔴 ALTA
```

---

*Diagnóstico realizado: 6 de Octubre 2025 - 19:58 ART*  
*Vivero Los Cocos - Análisis funcionalidad e-commerce*
