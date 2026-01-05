# 🎨 Implementación Bocetos - Fase 2: Sidebar Filtros en /tienda

**Fecha**: Oct 27, 2025  
**Estado**: ✅ COMPLETADO (CSS Base)  
**Tema activo**: `loscocos-clean`

---

## 📋 Cambios Implementados

### 1. **Nuevo archivo CSS: tienda-sidebar.css**

#### Ubicación:
```
/Applications/um/vivero/loscocos-clean-theme/assets/css/tienda-sidebar.css
```

#### Características principales:

**a) Layout 2 columnas (Sidebar + Grid)**
```css
.woocommerce-page,
.woocommerce {
  display: flex;
  gap: 2rem;
  align-items: flex-start;
}

.woocommerce-sidebar {
  width: 280px;
  flex-shrink: 0;
  position: sticky;
  top: 100px;
}

.shop-content {
  flex: 1;
  min-width: 0;
}
```

**b) Sidebar Styling**
```css
.woocommerce-sidebar {
  padding: 1.5rem;
  background: var(--bg-secondary);
  border-radius: var(--border-radius-lg);
  border: 1px solid var(--border-color);
  height: fit-content;
  position: sticky;
  top: 100px;
}
```

**c) Checkboxes Personalizados (Verde #1dc91d)**
```css
.filter-checkbox {
  -webkit-appearance: none;
  appearance: none;
  width: 1.25rem;
  height: 1.25rem;
  border: 2px solid var(--border-color);
  border-radius: var(--border-radius-sm);
  background-color: transparent;
  cursor: pointer;
}

.filter-checkbox:checked {
  background-color: var(--primary);  /* #1dc91d */
  border-color: var(--primary);
  background-image: url('data:image/svg+xml,...');  /* Checkmark SVG */
}

.filter-checkbox:hover {
  border-color: var(--primary);
  background-color: rgba(29, 201, 29, 0.05);
}
```

**d) Radio Buttons Personalizados**
```css
.filter-radio {
  border-radius: 50%;  /* Circular */
  /* ... mismo comportamiento que checkbox ... */
}

.filter-radio:checked {
  background-image: url('data:image/svg+xml,...');  /* Dot SVG */
}
```

**e) Price Range Slider**
```css
.price-range-track {
  display: flex;
  height: 0.5rem;
  background: var(--bg-tertiary);
  border-radius: 9999px;
  overflow: hidden;
}

.price-range-fill {
  background: var(--primary);
  height: 100%;
}
```

**f) Botones Filtros**
```css
.filter-apply-btn {
  width: 100%;
  background: var(--primary);
  border-radius: 9999px;
  font-weight: 700;
  transition: all var(--transition-base);
}

.filter-apply-btn:hover {
  transform: scale(1.05);
}

.filter-clear-btn {
  background: transparent;
  border: 1px solid var(--border-color);
  color: var(--primary);
}
```

**g) Responsive Mobile**
```css
@media (max-width: 768px) {
  .woocommerce-page {
    flex-direction: column;
  }

  .woocommerce-sidebar {
    width: 100%;
    position: static;
  }
}
```

---

### 2. **Actualización functions.php**

#### Enqueue condicional:
```php
// Tienda - Sidebar Filtros CSS
if (is_shop() || is_product_category() || is_product_tag()) {
  $tiendaCss = get_stylesheet_directory() . "/assets/css/tienda-sidebar.css";
  if (file_exists($tiendaCss)) {
    wp_enqueue_style("loscocos-tienda-sidebar", 
      get_stylesheet_directory_uri() . "/assets/css/tienda-sidebar.css", 
      ["loscocos-clean"], 
      filemtime($tiendaCss)
    );
  }
}
```

**Ventajas:**
- ✅ Solo carga en páginas de tienda
- ✅ Usa filemtime para cache busting
- ✅ Depende de loscocos-clean

---

## 🎯 Características CSS

### Checkboxes
- ✅ Custom appearance (sin navegador default)
- ✅ Color verde #1dc91d cuando checked
- ✅ Checkmark SVG embebido
- ✅ Hover con fondo suave
- ✅ Focus con box-shadow

### Radio Buttons
- ✅ Circular (border-radius: 50%)
- ✅ Dot SVG embebido
- ✅ Mismo comportamiento que checkboxes

### Filtros
- ✅ Grupos con títulos
- ✅ Separadores entre grupos
- ✅ Labels clickeables
- ✅ Contador de filtros activos

### Botones
- ✅ "Aplicar Filtros" - Pill shape, verde, scale hover
- ✅ "Limpiar Filtros" - Outline, verde, hover suave

### Layout
- ✅ Sidebar sticky (top: 100px)
- ✅ Responsive mobile (full width)
- ✅ Gap 2rem entre sidebar y grid

---

## 📝 Estructura HTML Esperada

```html
<div class="woocommerce-page">
  <!-- SIDEBAR FILTROS -->
  <aside class="woocommerce-sidebar">
    <h2>Filtros</h2>
    
    <!-- Categoría -->
    <div class="filter-group">
      <h3>Categoría</h3>
      <label class="filter-label">
        <input type="checkbox" class="filter-checkbox">
        <span>Indoor Plants</span>
      </label>
      <label class="filter-label">
        <input type="checkbox" class="filter-checkbox">
        <span>Outdoor Plants</span>
      </label>
    </div>
    
    <!-- Precio -->
    <div class="filter-group">
      <h3>Precio</h3>
      <div class="price-range-slider">
        <div class="price-range-track">
          <div class="price-range-fill" style="width: 60%"></div>
        </div>
        <div class="price-range-labels">
          <span>$0</span>
          <span>$100+</span>
        </div>
      </div>
    </div>
    
    <!-- Cuidado -->
    <div class="filter-group">
      <h3>Nivel de Cuidado</h3>
      <label class="filter-label">
        <input type="radio" name="care" class="filter-radio">
        <span>Fácil</span>
      </label>
      <label class="filter-label">
        <input type="radio" name="care" class="filter-radio">
        <span>Medio</span>
      </label>
    </div>
    
    <!-- Botones -->
    <button class="filter-apply-btn">Aplicar Filtros</button>
    <button class="filter-clear-btn">Limpiar</button>
  </aside>
  
  <!-- GRID DE PRODUCTOS -->
  <section class="shop-content">
    <!-- Productos grid aquí -->
  </section>
</div>
```

---

## 🔄 Próximos Pasos (Fase 3)

### 1. **Integración con WooCommerce Widgets**
- Mapear clases CSS a widgets de WooCommerce
- Aplicar estilos a `woocommerce-widget-layered-nav`
- Aplicar estilos a `woocommerce-widget-price-filter`

### 2. **JavaScript para Filtros Dinámicos**
- AJAX para aplicar filtros sin recargar
- Actualizar contador de productos
- Animación de aplicación

### 3. **Checkout - Detalles**
- Progress bar (Shipping → Payment → Review)
- Radios personalizados
- CTA "Continue to Payment"

### 4. **Product Detail Page**
- Tabs con underline verde
- Gallery con border-radius
- CTA con icon

### 5. **Material Icons en Botones**
- Reemplazar emoji 🛒 con Material Icon
- Aplicar en todos los CTA

---

## 📂 Archivos Modificados

```
✅ /Applications/um/vivero/loscocos-clean-theme/assets/css/tienda-sidebar.css (NUEVO)
   - Layout 2 columnas
   - Checkboxes personalizados
   - Radio buttons
   - Price range slider
   - Botones filtros
   - Responsive mobile

✅ /Applications/um/vivero/loscocos-clean-theme/functions.php
   - Enqueue condicional tienda-sidebar.css
```

---

## 🚀 Cómo Testear

### 1. Desplegar cambios
```bash
scp tienda-sidebar.css root@server:/path/to/theme/assets/css/
scp functions.php root@server:/path/to/theme/
wp cache flush
```

### 2. Verificar en /tienda
```
https://viveroloscocos.com.ar/tienda/
```

### 3. Verificar estructura
- Sidebar debe estar a la izquierda
- Grid de productos a la derecha
- Checkboxes verdes cuando se seleccionan
- Botones con pill shape

### 4. Verificar responsive
```
Ctrl+Shift+M (DevTools mobile)
- Sidebar debe estar arriba
- Grid debe ocupar full width
```

---

## 📊 Comparación vs Bocetos

| Elemento | Boceto | Implementado | Estado |
|----------|--------|--------------|--------|
| **Layout 2 col** | Sí | ✅ Flex | ✅ |
| **Sidebar sticky** | Sí | ✅ position: sticky | ✅ |
| **Checkboxes** | Verde | ✅ #1dc91d | ✅ |
| **Radio buttons** | Verde | ✅ #1dc91d | ✅ |
| **Price slider** | Sí | ✅ CSS | ✅ |
| **Botones** | Pill | ✅ 9999px | ✅ |
| **Responsive** | Sí | ✅ Mobile | ✅ |
| **Integración WC** | - | ⏳ Pendiente | ⏳ |
| **AJAX Filtros** | - | ⏳ Pendiente | ⏳ |

---

## 💡 Notas Técnicas

### Checkboxes SVG
- Checkmark: `M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z`
- Dot: `circle cx='8' cy='8' r='3'`

### Sticky Positioning
- `top: 100px` asume header de ~60px + padding
- Ajustar según altura real del header

### Responsive Breakpoint
- 768px es el breakpoint principal
- Mobile: flex-direction: column

### Performance
- CSS-only (sin JavaScript requerido)
- Enqueue condicional (solo en tienda)
- Cache busting con filemtime()

---

## ✅ Checklist

- [x] Crear tienda-sidebar.css
- [x] Checkboxes personalizados
- [x] Radio buttons personalizados
- [x] Price range slider
- [x] Botones filtros
- [x] Layout 2 columnas
- [x] Responsive mobile
- [x] Enqueue en functions.php
- [x] Documentación
- [ ] Integración WooCommerce widgets
- [ ] AJAX filtros dinámicos
- [ ] Checkout progress bar
- [ ] Product detail tabs
- [ ] Material Icons

---

**Próximo paso**: Integración con WooCommerce Widgets (Fase 3)
