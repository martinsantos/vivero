# 🎨 TICKET: REDISEÑO COMPLETO SITIO WEB

**Fecha creación:** Sábado 4 de Octubre, 2025 - 12:10 ART  
**Prioridad:** Media  
**Estado:** Pendiente análisis  
**Tema objetivo:** `loscocos-clean` (tema oficial activo)

---

## 📋 DESCRIPCIÓN

Implementar rediseño completo del sitio web **viveroloscocos.com.ar** basado en bocetos proporcionados.

### Alcance
- ✅ Diseño homepage
- ✅ Diseño páginas de producto
- ✅ Diseño páginas de categoría
- ✅ Diseño carrito y checkout
- ✅ Responsive mobile/tablet
- ✅ Optimización UX/UI

---

## 📁 RECURSOS DISPONIBLES

### Bocetos Proporcionados
Ubicación: `/Applications/um/vivero/bocetos/`

**Archivos ZIP (10 variantes):**
1. `stitch_vivero_los_cocos_home_page.zip` (996 KB)
2. `stitch_vivero_los_cocos_home_page (1).zip` (1.2 MB)
3. `stitch_vivero_los_cocos_home_page (2).zip` (1.2 MB)
4. `stitch_vivero_los_cocos_home_page (3).zip` (977 KB)
5. `stitch_vivero_los_cocos_home_page (4).zip` (196 KB)
6. `stitch_vivero_los_cocos_home_page (5).zip` (97 KB)
7. `stitch_vivero_los_cocos_home_page (6).zip` (117 KB)
8. `stitch_vivero_los_cocos_home_page (7).zip` (150 KB)
9. `stitch_vivero_los_cocos_home_page (8).zip` (231 KB)
10. `stitch_vivero_los_cocos_home_page (9).zip` (192 KB)

**Total:** 10 variantes de diseño para analizar

---

## 🎯 OBJETIVOS

### Funcionales
1. Implementar diseño moderno y limpio
2. Mejorar experiencia de usuario (UX)
3. Optimizar conversión (CRO)
4. Mantener compatibilidad WooCommerce
5. Responsive design (mobile-first)

### Técnicos
1. Trabajar sobre tema `loscocos-clean` (oficial)
2. Mantener performance (Core Web Vitals)
3. Preservar SEO actual (88.8/100 → 95/100)
4. Compatibilidad cross-browser
5. Accesibilidad (WCAG 2.1)

### Comerciales
1. Aumentar tasa de conversión
2. Reducir bounce rate
3. Mejorar tiempo en sitio
4. Facilitar navegación de catálogo
5. Optimizar proceso de compra

---

## 📊 ANÁLISIS REQUERIDO

### Fase 1: Extracción y Análisis de Bocetos
- [ ] Descomprimir todos los archivos ZIP
- [ ] Catalogar imágenes/diseños por tipo de página
- [ ] Identificar elementos comunes y variantes
- [ ] Documentar paleta de colores
- [ ] Documentar tipografías
- [ ] Documentar componentes UI
- [ ] Crear inventario de assets necesarios

### Fase 2: Evaluación Técnica
- [ ] Revisar tema `loscocos-clean` actual
- [ ] Identificar archivos a modificar
- [ ] Evaluar compatibilidad con plugins
- [ ] Planificar estructura CSS/JS
- [ ] Definir breakpoints responsive
- [ ] Evaluar necesidad de nuevos assets

### Fase 3: Planificación
- [ ] Crear roadmap de implementación
- [ ] Estimar tiempos por componente
- [ ] Definir prioridades (MVP vs. nice-to-have)
- [ ] Planificar testing
- [ ] Definir estrategia de deploy

---

## 🛠️ STACK TÉCNICO

### Frontend
- **Tema base:** `loscocos-clean` (WordPress)
- **CSS:** Custom CSS / SCSS
- **JS:** Vanilla JS / jQuery (WordPress compatible)
- **Framework UI:** A definir según bocetos

### Herramientas
- **Diseño:** Figma / Sketch (si es necesario refinar)
- **Optimización imágenes:** ImageMagick / TinyPNG
- **Testing:** BrowserStack / Chrome DevTools
- **Performance:** Lighthouse / GTmetrix

---

## 📅 FASES DE IMPLEMENTACIÓN (Estimadas)

### Fase 1: Análisis y Setup (1-2 días)
- Extraer y analizar bocetos
- Documentar especificaciones
- Preparar assets
- Setup ambiente de desarrollo

### Fase 2: Homepage (2-3 días)
- Header y navegación
- Hero section
- Secciones de contenido
- Footer
- Responsive

### Fase 3: Páginas de Producto (2-3 días)
- Layout de producto individual
- Galería de imágenes
- Información y specs
- Add to cart optimizado
- Related products

### Fase 4: Catálogo y Categorías (1-2 días)
- Grid de productos
- Filtros y ordenamiento
- Paginación
- Breadcrumbs

### Fase 5: Carrito y Checkout (2-3 días)
- Carrito optimizado
- Proceso de checkout
- Formularios
- Confirmación de orden

### Fase 6: Testing y Optimización (2-3 días)
- Testing cross-browser
- Testing responsive
- Optimización performance
- Ajustes finales

**Total estimado:** 10-16 días de desarrollo

---

## 🔍 CONSIDERACIONES ESPECIALES

### SEO
- ⚠️ **CRÍTICO:** No afectar score SEO actual (88.8/100)
- Mantener estructura HTML semántica
- Preservar meta tags y Schema markup
- No cambiar URLs ni estructura de enlaces
- Mantener alt text de imágenes

### Performance
- Target: Core Web Vitals en verde
- LCP < 2.5s
- FID < 100ms
- CLS < 0.1
- Optimizar imágenes (WebP)
- Lazy loading

### WooCommerce
- Mantener compatibilidad con hooks/filters
- No romper funcionalidad de carrito
- Preservar integraciones de pago
- Mantener cálculo de envíos

### Accesibilidad
- Contraste de colores (WCAG AA)
- Navegación por teclado
- Screen reader friendly
- Focus states visibles

---

## 📦 ENTREGABLES

1. **Documentación de análisis**
   - Inventario de bocetos
   - Especificaciones de diseño
   - Paleta de colores y tipografías

2. **Assets preparados**
   - Imágenes optimizadas
   - Iconos/SVGs
   - Fuentes (si aplica)

3. **Código implementado**
   - Archivos de tema modificados
   - CSS/SCSS custom
   - JavaScript custom
   - Templates PHP

4. **Documentación técnica**
   - Guía de implementación
   - Changelog
   - Guía de mantenimiento

5. **Testing y QA**
   - Reporte de testing
   - Screenshots de validación
   - Métricas de performance

---

## 🚀 PRÓXIMOS PASOS INMEDIATOS

### 1. Extraer y Analizar Bocetos
```bash
cd /Applications/um/vivero/bocetos
mkdir -p extracted
for zip in *.zip; do
    unzip -q "$zip" -d "extracted/${zip%.zip}"
done
```

### 2. Crear Inventario
```bash
# Listar todos los archivos extraídos
find extracted -type f -name "*.png" -o -name "*.jpg" -o -name "*.svg" > inventario_bocetos.txt
```

### 3. Revisar Tema Actual
```bash
# Ubicación del tema (servidor producción)
ssh root@23.105.176.45 "ls -la /home/viveroloscocos.com.ar/public_html/wp-content/themes/loscocos-clean/"
```

---

## 📞 INFORMACIÓN DE CONTACTO

**Proyecto:** Vivero Los Cocos - Rediseño Web  
**Sitio:** viveroloscocos.com.ar  
**Tema:** loscocos-clean  
**Servidor:** 23.105.176.45  
**Documentación:** README-PROYECTO-SEO.md  

---

## 🔗 RELACIÓN CON PROYECTO SEO

Este rediseño debe **complementar** el proyecto SEO en curso:

- **Día 3 (hoy):** Completar galería de imágenes → 91-92/100
- **Días 4-7:** Schema markup + optimizaciones → 95/100
- **Post-95/100:** Implementar rediseño manteniendo score

**⚠️ IMPORTANTE:** No iniciar implementación hasta completar objetivo 95/100 SEO para no interferir con optimizaciones en curso.

---

## 📊 MÉTRICAS DE ÉXITO

### Pre-Rediseño (Baseline)
- Score SEO: 88.8/100 (objetivo: 95/100)
- Bounce rate: TBD
- Avg. session duration: TBD
- Conversion rate: TBD
- Page load time: TBD

### Post-Rediseño (Objetivos)
- Score SEO: ≥95/100 (mantener o mejorar)
- Bounce rate: -20%
- Avg. session duration: +30%
- Conversion rate: +25%
- Page load time: <2s

---

## 🏷️ TAGS

`#rediseño` `#ui-ux` `#wordpress` `#woocommerce` `#loscocos-clean` `#frontend` `#responsive` `#performance`

---

**Estado:** 📋 PENDIENTE ANÁLISIS DE BOCETOS  
**Bloqueado por:** Completar Día 3-7 SEO (95/100)  
**Siguiente acción:** Extraer y analizar bocetos cuando se autorice inicio

---

*Creado: 12:10 ART - 4 Oct 2025*  
*Actualizado: 12:10 ART - 4 Oct 2025*
