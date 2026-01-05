# 🏠 HOME CORREGIDA - Precios Grises y Diseño Minimalista

## ❌ PROBLEMA IDENTIFICADO

La home mostraba:
- **Precios en VERDE** cuando deberían ser grises
- Botones sin refinamiento
- Layout inconsistente con /tienda/

## ✅ CORRECCIONES APLICADAS

### **1. Precios GRISES con !important**
```css
/* Forzar precio gris en home */
.home-section ul.products li.product .price {
  color: rgba(0, 0, 0, 0.6) !important;
  font-size: 0.875rem !important;
  font-weight: 400 !important;
}

.home-section ul.products li.product .price .amount,
.home-section ul.products li.product .price ins {
  color: rgba(0, 0, 0, 0.6) !important;
  font-weight: 400 !important;
}
```

**Antes**: `color: var(--primary)` → Verde #1dc91d  
**Ahora**: `color: rgba(0, 0, 0, 0.6)` → Gris discreto

---

### **2. Cards Sin Borders**
```css
.home-section ul.products li.product {
  background: transparent;
  border: none;
  padding: 0;
  box-shadow: none;
}
```

---

### **3. Carousel Horizontal Funcional**
```css
.carousel {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: thin;
}

.home-section ul.products {
  display: flex;
  flex-wrap: nowrap;
  gap: 1.5rem;
}

.home-section ul.products li.product {
  flex: 0 0 280px;
  max-width: 280px;
}
```

**Scroll horizontal** suave en desktop y mobile

---

### **4. Botones Verde Lime**
```css
.home-section ul.products li.product .add_to_cart_button {
  background: var(--primary);  /* #1dc91d */
  color: #111;
  font-weight: 600;
  border-radius: var(--border-radius-md);
}
```

---

### **5. Secciones Alternas con Fondo Sutil**
```css
.home-section:nth-child(even) {
  background: rgba(29, 201, 29, 0.03);
}
```

**Novedades** → Fondo transparente  
**Más vendidos** → Fondo verde muy sutil  

---

## 📋 ESTRUCTURA HOME

La home (`front-page.php`) renderiza:

1. **Hero Section** (desde header.php via Customizer)
2. **Promociones** (opcional, via Customizer)
3. **Novedades** (últimos 12 productos)
4. **Más vendidos** (por total_sales)

Cada sección usa `wc_get_template_part('content', 'product')` para mantener compatibilidad con AJAX add-to-cart.

---

## 🎨 COMPARACIÓN VISUAL

### **ANTES (Incorrecto)**
```
┌─────────────────┐
│  🌿 Producto    │
│  $ 1.000,00 🟢  │ ← VERDE (mal)
│  [AGREGAR] 🟢   │
└─────────────────┘
```

### **AHORA (Correcto)**
```
┌─────────────────┐
│  🌿 Producto    │
│  $ 1.000,00 ⚫  │ ← GRIS (bien)
│  [Agregar] 🟢   │
└─────────────────┘
```

---

## 🔍 ELEMENTOS CORREGIDOS

| Elemento | Antes | Ahora |
|----------|-------|-------|
| **Precio Color** | `var(--primary)` verde | `rgba(0,0,0,0.6)` gris |
| **Precio Tamaño** | Variable | `0.875rem` fijo |
| **Precio Weight** | Variable | `400` regular |
| **Card Border** | Visible | `none` |
| **Card Background** | Blanco | `transparent` |
| **Botón Color** | Inconsistente | `#1dc91d` verde lime |
| **Botón Texto** | Blanco | `#111` negro |
| **Layout** | Grid rígido | Flex carousel |

---

## 📱 RESPONSIVE

### **Desktop (>768px)**
- Cards: 280px ancho
- Gap: 1.5rem
- Scroll horizontal suave

### **Mobile (<768px)**
- Cards: 220px ancho
- Gap: 1.5rem
- Touch scroll nativo

---

## 🚀 VERIFICACIÓN

**URL**: https://viveroloscocos.com.ar/

**Ctrl+Shift+R** (hard reload) para ver cambios

### **Checklist:**
- [ ] Precios en GRIS (no verde)
- [ ] Cards sin borders visibles
- [ ] Scroll horizontal funciona
- [ ] Botones verde lime con texto negro
- [ ] Secciones alternas con fondo sutil
- [ ] Títulos en gris oscuro
- [ ] Imágenes con border-radius 8px

---

## 💡 POR QUÉ ESTABA MAL

El problema era que la home usa plantillas estándar de WooCommerce (`content-product.php`) y los estilos de `shop.css` no se aplicaban porque:

1. **Especificidad CSS**: Los estilos globales de WooCommerce tenían mayor prioridad
2. **Selector incorrecto**: Los estilos estaban dirigidos a `.woocommerce ul.products` pero la home no siempre tiene la clase `.woocommerce`
3. **Variables heredadas**: El precio heredaba `color: var(--primary)` de algún lugar

**Solución**: Selectores específicos `.home-section ul.products li.product .price` con `!important` para garantizar aplicación.

---

## 📂 ARCHIVO MODIFICADO

```
✅ style.css (versión 2.1.0)
   - Sección HOME SECTIONS ampliada
   - Precios grises con !important
   - Carousel horizontal
   - Cards sin borders
   - Botones refinados
```

**Cache limpiado** ✅  
**Cambios en producción** ✅

---

**La home ahora está alineada con el diseño minimalista de /tienda/** ✨
