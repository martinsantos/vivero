# 🔍 Auditoría Detallada de Estilos - Vivero Los Cocos

**Fecha**: Oct 27, 2025  
**Estado**: ⚠️ PROBLEMAS IDENTIFICADOS  
**Tema**: loscocos-clean

---

## 🐛 PROBLEMAS IDENTIFICADOS

### 1. **Tarjetas de Productos - Inconsistencias**

#### Problema 1a: Botones muy grandes
```
OBSERVADO: Botones "AGREGAR AL CARRITO" ocupan mucho espacio
ESPERADO: Botones más compactos, pill shape
CAUSA: Padding excesivo en shop.css
```

#### Problema 1b: Precios no visibles
```
OBSERVADO: Precios aparecen pequeños/grises
ESPERADO: Precios verdes o más visibles
CAUSA: Color #6B7280 (gris) es muy suave
```

#### Problema 1c: Títulos truncados
```
OBSERVADO: Títulos de productos se cortan
ESPERADO: Títulos legibles en 2 líneas
CAUSA: -webkit-line-clamp: 2 correcto, pero min-height insuficiente
```

#### Problema 1d: Subtítulos no visibles
```
OBSERVADO: No se ven subtítulos en tarjetas
ESPERADO: Subtítulos grises bajo títulos
CAUSA: Clase .product-subtitle no existe en HTML real
```

---

### 2. **Sidebar Filtros - No Existe**

#### Problema 2a: CSS sin HTML
```
OBSERVADO: tienda-sidebar.css busca .woocommerce-sidebar
ESPERADO: Sidebar visible en /tienda
CAUSA: Template archive-product.php no incluye sidebar
SOLUCIÓN: Necesita template personalizado o hooks
```

#### Problema 2b: Layout 2 columnas no aplicado
```
OBSERVADO: Productos ocupan full width
ESPERADO: Sidebar (280px) + Grid (flex: 1)
CAUSA: HTML no tiene estructura para sidebar
```

---

### 3. **Coherencia de Estilos**

#### Problema 3a: Botones inconsistentes
```
HOME:
- Pill shape ✅
- Font-weight 700 ✅
- Scale hover ✅

/TIENDA:
- Pill shape ✅
- Font-weight 700 ✅
- Scale hover ✅
- PERO: Tamaño diferente (más pequeño)
- PERO: Padding diferente
```

#### Problema 3b: Precios inconsistentes
```
HOME:
- Gris #6B7280
- Font-size 0.875rem

/TIENDA:
- Verde #1dc91d (según imagen)
- Font-size 0.875rem
- INCONSISTENCIA: Colores diferentes
```

#### Problema 3c: Tarjetas inconsistentes
```
HOME:
- Imagen 1:1 aspect-ratio ✅
- Título 2 líneas ✅
- Precio gris ✅
- Botón pill ✅

/TIENDA:
- Imagen 1:1 aspect-ratio ✅
- Título truncado (no 2 líneas) ❌
- Precio verde (no gris) ❌
- Botón más pequeño ❌
```

---

## 📋 Análisis de HTML Real

### Estructura actual /tienda
```html
<div class="shop-archive-wrap">
  <h1 class="page-title">Tienda</h1>
  
  <!-- WooCommerce hooks aquí -->
  
  <ul class="products columns-4">
    <li class="product">
      <div class="product-image-container">
        <a class="product-image-link">
          <img>
        </a>
      </div>
      <div class="product-content">
        <a class="product-title-link">
          <h2 class="woocommerce-loop-product__title">Título</h2>
        </a>
        <!-- NO HAY .product-subtitle -->
        <span class="price">$1.000,00</span>
      </div>
      <div class="product-actions">
        <button class="button">AGREGAR AL CARRITO</button>
      </div>
    </li>
  </ul>
</div>
```

### Clases que NO existen
```
❌ .woocommerce-sidebar
❌ .shop-content
❌ .product-subtitle
❌ .filter-group
❌ .filter-checkbox
```

### Clases que SÍ existen
```
✅ .shop-archive-wrap
✅ .products
✅ .product
✅ .product-image-container
✅ .product-content
✅ .product-actions
✅ .button
✅ .price
```

---

## 🔧 SOLUCIONES NECESARIAS

### Solución 1: Ajustar CSS a HTML real

**Cambiar en shop.css:**
```css
/* ANTES - Buscaba clases inexistentes */
.woocommerce-sidebar { ... }
.shop-content { ... }

/* AHORA - Usar clases reales */
.shop-archive-wrap { ... }
.products { ... }
```

### Solución 2: Crear subtítulos en HTML

**Opción A: Modificar template content-product.php**
```php
<div class="product-content">
  <a class="product-title-link">
    <h2 class="woocommerce-loop-product__title">Título</h2>
  </a>
  <!-- AGREGAR SUBTÍTULO -->
  <p class="product-subtitle">
    <?php echo wp_trim_words(get_the_excerpt(), 10); ?>
  </p>
  <span class="price">$1.000,00</span>
</div>
```

**Opción B: Usar CSS para generar subtítulo**
```css
.product-content::after {
  content: attr(data-subtitle);
  display: block;
  font-size: 0.875rem;
  color: #6B7280;
}
```

### Solución 3: Sidebar Filtros

**Opción A: Template personalizado**
- Crear `woocommerce/archive-product-with-sidebar.php`
- Incluir sidebar con filtros WooCommerce

**Opción B: Hooks**
- Usar `woocommerce_before_main_content` para insertar sidebar
- Usar `woocommerce_after_main_content` para cerrar

### Solución 4: Coherencia de Precios

**Decidir:**
- ¿Precios grises (#6B7280) o verdes (#1dc91d)?
- Según bocetos: Gris suave
- Actual en /tienda: Verde

**Acción:**
```css
/* Unificar a gris */
.price {
  color: #6B7280;
  font-weight: 400;
}
```

### Solución 5: Coherencia de Botones

**Revisar tamaños:**
```css
/* Debe ser igual en home y /tienda */
.button,
.product-actions .button {
  padding: 0.625rem 1rem;
  border-radius: 9999px;
  font-weight: 700;
  font-size: 0.875rem;
}
```

---

## 📊 Tabla de Problemas vs Soluciones

| Problema | Causa | Solución | Prioridad |
|----------|-------|----------|-----------|
| Botones grandes | Padding excesivo | Ajustar padding | 🔴 Alta |
| Precios grises/verdes | Inconsistencia | Unificar a gris | 🔴 Alta |
| Títulos truncados | min-height bajo | Aumentar a 3.5rem | 🟡 Media |
| Subtítulos no visibles | No existen en HTML | Agregar en template | 🟡 Media |
| Sidebar no existe | Template sin sidebar | Crear template o hooks | 🔴 Alta |
| CSS sin HTML | tienda-sidebar.css incompleto | Reescribir CSS | 🔴 Alta |

---

## ✅ CHECKLIST DE CORRECCIONES

### Inmediatas (Hoy)
- [ ] Revisar y ajustar padding de botones
- [ ] Unificar color de precios a gris
- [ ] Verificar tamaños de títulos
- [ ] Revisar coherencia home vs /tienda

### Corto plazo (Mañana)
- [ ] Agregar subtítulos en template
- [ ] Crear sidebar filtros
- [ ] Reescribir tienda-sidebar.css

### Mediano plazo (Esta semana)
- [ ] Integración WooCommerce widgets
- [ ] AJAX filtros dinámicos
- [ ] Testing completo

---

## 🎯 PRÓXIMAS ACCIONES

1. **Revisar shop.css** - Ajustar padding/tamaños
2. **Unificar precios** - Todos a gris #6B7280
3. **Verificar coherencia** - Home vs /tienda
4. **Crear template sidebar** - Para filtros
5. **Reescribir tienda-sidebar.css** - Con clases reales

---

**Estado**: ⚠️ REQUIERE CORRECCIONES  
**Próximo**: Implementar soluciones

