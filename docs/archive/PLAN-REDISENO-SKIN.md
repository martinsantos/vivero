# 📐 PLAN DE REDISEÑO SKIN - VIVERO LOS COCOS

**Fecha:** 5 de Octubre 2025  
**Hora inicio:** 11:56 ART  
**Objetivo:** Implementar diseño moderno basado en bocetos sin romper funcionalidad

---

## 🎨 ANÁLISIS DE BOCETOS

### Páginas Disponibles

1. **Home Page** - Página principal con hero section
2. **Product Listing** - Catálogo de productos
3. **Product Detail** - Detalle de producto individual
4. **Checkout Page** - Proceso de compra
5. **Order Confirmation** - Confirmación de pedido
6. **User Account** - Cuenta de usuario

### Características del Diseño

**Tecnología:**
- TailwindCSS (CDN)
- Fuente: Epilogue (Google Fonts)
- Material Symbols icons

**Colores:**
```css
--primary: #1dc91d (verde vibrante)
--background-light: #f6f8f6
--background-dark: #112111
```

**Características:**
- ✅ Dark mode completo
- ✅ Diseño responsive
- ✅ Animaciones suaves (hover, scale)
- ✅ Componentes redondeados
- ✅ Backdrop blur en header
- ✅ Gradientes en imágenes

---

## ⚠️ PRECAUCIONES CRÍTICAS

### NO Modificar

1. ❌ Funcionalidad WooCommerce
2. ❌ Templates WordPress core
3. ❌ Proceso de checkout existente
4. ❌ Sistema de carrito
5. ❌ SEO (mantener 88.5/100)
6. ❌ Imágenes y tags asignados (462 productos)

### SÍ Implementar

1. ✅ Custom CSS vía Customizer
2. ✅ Variables CSS personalizadas
3. ✅ Estilos adicionales (no overrides destructivos)
4. ✅ Mejoras visuales incrementales
5. ✅ Testing continuo

---

## 🛠️ ESTRATEGIA DE IMPLEMENTACIÓN

### Enfoque: CSS-Only (Sin modificar PHP)

**Ventajas:**
- No rompe funcionalidad
- Reversible instantáneamente
- No afecta SEO
- Testing rápido

**Método:**
1. Crear archivo `custom-redesign.css`
2. Agregar vía WordPress Customizer → Additional CSS
3. Implementar por secciones
4. Testing incremental

---

## 📋 FASES DE IMPLEMENTACIÓN

### Fase 1: Variables y Base (12:00-12:10)

**Objetivo:** Establecer sistema de diseño

```css
:root {
  --color-primary: #1dc91d;
  --color-bg-light: #f6f8f6;
  --color-bg-dark: #112111;
  --font-display: 'Epilogue', sans-serif;
  --radius-default: 0.25rem;
  --radius-lg: 0.5rem;
  --radius-xl: 0.75rem;
  --radius-full: 9999px;
}
```

**Incluir:**
- Variables de color
- Tipografía
- Border radius
- Sombras

---

### Fase 2: Header Mejorado (12:10-12:25)

**Cambios:**
- Sticky header con backdrop blur
- Borde inferior verde sutil
- Hover states en nav
- Search bar estilizado
- Carrito con badge

**CSS Target:**
```css
.site-header {
  backdrop-filter: blur(8px);
  border-bottom: 1px solid rgba(29, 201, 29, 0.2);
}
```

---

### Fase 3: Hero Section (12:25-12:40)

**Implementar:**
- Background gradient sobre imagen
- Título grande y bold
- CTA button verde prominente
- Responsive text sizing

**CSS Target:**
```css
.hero-section {
  position: relative;
  background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.5)), url(...);
}
```

---

### Fase 4: Productos Grid (12:40-13:00)

**Mejoras:**
- Cards con hover scale
- Bordes redondeados
- Sombras suaves
- Botones verde vibrante
- Transiciones suaves

**CSS Target:**
```css
.woocommerce ul.products li.product {
  transition: transform 0.3s ease;
}

.woocommerce ul.products li.product:hover {
  transform: scale(1.05);
}
```

---

### Fase 5: Botones y CTAs (13:00-13:15)

**Estilo unificado:**
- Background verde (#1dc91d)
- Bordes redondeados
- Hover: opacity 90%
- Transform scale on hover

**CSS Target:**
```css
.woocommerce #respond input#submit,
.woocommerce a.button,
.woocommerce button.button {
  background-color: var(--color-primary);
  border-radius: var(--radius-lg);
  transition: all 0.3s ease;
}
```

---

### Fase 6: Responsive + Dark Mode (13:15-13:30)

**Media queries:**
- Mobile: Stack elements
- Tablet: 2 columnas
- Desktop: 3-4 columnas

**Dark mode (opcional):**
```css
@media (prefers-color-scheme: dark) {
  :root {
    --color-bg: var(--color-bg-dark);
  }
}
```

---

## 📊 COMPONENTES A ESTILIZAR

### Prioridad Alta

1. ✅ Header navigation
2. ✅ Product cards
3. ✅ Buttons/CTAs
4. ✅ Hero section
5. ✅ Footer

### Prioridad Media

6. Search bar
7. Product detail page
8. Checkout styling
9. Forms

### Prioridad Baja

10. Dark mode toggle
11. Account page
12. Order confirmation

---

## 🎯 MÉTRICAS DE ÉXITO

### Funcionalidad

- ✅ WooCommerce funciona 100%
- ✅ Checkout completa correctamente
- ✅ Carrito funcional
- ✅ Productos se agregan correctamente

### SEO

- ✅ Score mantiene 88.5/100
- ✅ No hay broken links
- ✅ Performance no degradada

### Visual

- ✅ Diseño coherente con bocetos
- ✅ Responsive en mobile/tablet/desktop
- ✅ Transiciones suaves
- ✅ Colores según paleta

---

## 🔧 HERRAMIENTAS

### Archivos a Crear

1. `custom-redesign.css` - CSS principal
2. `custom-variables.css` - Variables CSS
3. `responsive-overrides.css` - Media queries

### Testing

```bash
# Verificar sintaxis CSS
css-validator custom-redesign.css

# Testing responsive
# Chrome DevTools → Toggle device toolbar

# Performance
# Lighthouse audit
```

---

## ⏰ CRONOGRAMA

| Hora | Tarea | Duración |
|------|-------|----------|
| 11:56-12:00 | Análisis bocetos | 4 min ✅ |
| 12:00-12:10 | Variables + base | 10 min |
| 12:10-12:25 | Header styling | 15 min |
| 12:25-12:40 | Hero section | 15 min |
| 12:40-13:00 | Product grid | 20 min |
| 13:00-13:15 | Buttons/CTAs | 15 min |
| 13:15-13:30 | Responsive | 15 min |
| 13:30-14:00 | Testing final | 30 min |

**Total:** 2 horas

---

## 🚨 ROLLBACK PLAN

### Si algo sale mal:

1. **Inmediato:** Remover CSS del Customizer
2. **Backup:** Restaurar desde `custom-redesign.css.backup`
3. **Testing:** Verificar funcionalidad WooCommerce
4. **SEO:** Re-auditar score

### Backups automáticos:

```bash
# Antes de cada fase
cp custom-redesign.css custom-redesign.css.backup-$(date +%H%M)
```

---

## 📝 NOTAS TÉCNICAS

### Fuentes a Cargar

```html
<link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;500;700;900&display=swap" rel="stylesheet"/>
```

### Icons (Material Symbols)

```html
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
```

### TailwindCSS (Opcional)

**NO usar:** Ya que WordPress tiene su propio sistema  
**Alternativa:** Extraer solo los estilos necesarios

---

## ✅ CHECKLIST PRE-IMPLEMENTACIÓN

- [✅] Bocetos extraídos y analizados
- [✅] Plan de implementación definido
- [✅] Estrategia CSS-only confirmada
- [ ] Backup de sitio actual
- [ ] Score SEO actual documentado (88.5/100)
- [ ] Testing environment preparado

---

## 🎨 PALETA DE COLORES FINAL

```css
/* Verde principal */
--primary: #1dc91d;
--primary-hover: #19b319;
--primary-light: rgba(29, 201, 29, 0.1);

/* Backgrounds */
--bg-light: #f6f8f6;
--bg-dark: #112111;
--bg-card: #ffffff;

/* Text */
--text-primary: #1a1a1a;
--text-secondary: #6b7280;
--text-white: #ffffff;

/* Borders */
--border-light: #e5e7eb;
--border-primary: rgba(29, 201, 29, 0.2);
```

---

**ESTADO:** 🟢 LISTO PARA IMPLEMENTAR

**Próximo paso:** Crear `custom-redesign.css` con variables base

---

*Documento generado: 11:57 ART - 5 de Octubre, 2025*  
*Implementación segura sin modificar funcionalidad*  
*Vivero Los Cocos - Rediseño de Skin*
