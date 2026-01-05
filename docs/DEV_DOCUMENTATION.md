# 🌿 Los Cocos - Documentación para Desarrolladores

## 📋 Resumen del Proyecto

**Los Cocos** es un sitio web para un vivero en Mendoza, Argentina, que presenta un **sistema estacional dinámico** avanzado. El sitio adapta completamente su contenido, colores, productos y funcionalidades según la temporada del año (Primavera, Verano, Otoño, Invierno).

### 🎯 Características Principales
- **Sistema estacional 100% dinámico** - Todo el contenido cambia según la temporada
- **Detección automática de temporada** basada en la fecha actual
- **4 temporadas completas** con datos específicos para cada una
- **Interfaz responsive** y moderna con Tailwind CSS
- **Carrito de compras funcional**
- **Modales interactivos** para ofertas y consejos
- **Servidor de desarrollo** integrado

---

## 🏗️ Arquitectura del Proyecto

### Estructura de Archivos
```
vivero/
├── home.html                 # Página principal con IDs dinámicos
├── cart.html                 # Página del carrito (funcional)
├── single.html               # Página de producto individual
├── cart.js                   # Lógica del carrito de compras
├── autumn-features.js        # Sistema estacional v6.1 (CORE)
├── js/                       # Scripts adicionales
│   ├── cart-page.js
│   ├── home-search.js
│   ├── products.js
│   └── single-product.js
├── test-seasons.html         # Página de testing (opcional)
└── DEV_DOCUMENTATION.md      # Esta documentación
```

### Tecnologías Utilizadas
- **HTML5** con estructura semántica
- **Tailwind CSS** vía CDN para estilos
- **JavaScript ES6+** vanilla (sin frameworks)
- **Python HTTP Server** para desarrollo local
- **Git** para control de versiones

---

## 🔧 Sistema Estacional (CORE del Proyecto)

### Archivo Principal: `autumn-features.js v6.1`

Este es el **corazón del proyecto**. Contiene toda la lógica para el cambio dinámico de temporadas.

#### Estructura de Datos por Temporada
```javascript
const seasons = {
  primavera: {
    name: 'Primavera',
    icon: '🌸',
    period: 'Septiembre - Noviembre',
    background: 'URL_imagen_primavera',
    overlay: 'gradiente_css',
    gradient: 'from-green-500 to-emerald-600',
    colors: { primary: '#10B981', secondary: '#059669' },
    
    // Datos específicos de la sección especies
    speciesData: { ... },
    
    // Datos de preparación del jardín
    preparationData: {
      steps: [...],  // 4 pasos específicos
      kit: { ... }   // Productos y precios
    },
    
    // Plantas destacadas
    featuredPlants: [...],
    
    // Ofertas especiales
    offers: { ... },
    
    // Consejos de temporada
    tips: [...]
  },
  // ... verano, otono, invierno con estructura similar
}
```

#### Funciones Principales

1. **`changeSeason(season)`** - Función master que coordina todo el cambio
2. **`updateSeasonIndicator(config)`** - Actualiza indicador de temporada actual
3. **`updateBackground(config)`** - Cambia fondo e overlay
4. **`updateHeroContent(config)`** - Actualiza título y descripción principal
5. **`updateTimelineCards(activeSeason)`** - Marca temporada activa en timeline
6. **`updateSpeciesSection(config)`** - Actualiza sección de plantas
7. **`updatePreparationSection(config)`** - Actualiza guía de preparación
8. **`updatePreparationKit(config)`** - Actualiza kit de productos
9. **`updateCalendar(config)`** - Actualiza calendario de actividades
10. **`updateSeasonInfoPanel(config)`** - Actualiza panel de consejos
11. **`updateActionButtons(config)`** - Actualiza botones principales

### Elementos HTML Dinámicos

Todos estos elementos tienen IDs específicos y se actualizan automáticamente:

```html
<!-- Indicadores de temporada -->
<div id="season-indicator">...</div>
<span id="season-icon-left">🍂</span>
<span id="current-season">Otoño 2024</span>

<!-- Contenido hero -->
<span id="hero-title">Los Cocos</span>
<span id="hero-subtitle">refugio otoñal</span>
<span id="hero-description">Prepara tu jardín...</span>

<!-- Panel de información -->
<div id="season-info-badge">...</div>
<span id="season-info-icon">🍂</span>
<span id="season-info-text">Consejos para Otoño</span>
<div id="season-tips-grid">...</div>

<!-- Calendario -->
<h3 id="calendar-title">Calendario de Actividades Otoñales</h3>
<div id="calendar-months">...</div>

<!-- Botones de acción -->
<a id="action-button-1">Ver Plantas de Otoño</a>

<!-- Sección preparación -->
<div id="preparation-section">...</div>
<h2 id="preparation-title">Prepara tu Jardín para el Otoño</h2>
<div id="kit-products">...</div>
```

---

## 🚀 Configuración de Desarrollo

### Requisitos Previos
- Python 3.9+ instalado
- Navegador web moderno
- Editor de código (VS Code recomendado)
- Git configurado

### Instalación y Ejecución

1. **Clonar el repositorio:**
```bash
git clone https://github.com/martinsantos/vivero.git
cd vivero
```

2. **Iniciar servidor de desarrollo:**
```bash
# Opción 1: Puerto 8001 (recomendado)
python3 -m http.server 8001

# Opción 2: Puerto 8000 (alternativo)
python3 -m http.server 8000
```

3. **Acceder al sitio:**
```
http://localhost:8001/home.html
```

### Comandos de Desarrollo Útiles

```bash
# Matar procesos del servidor si hay conflictos
pkill -f "python3 -m http.server"

# Verificar estado de Git
git status

# Ver log de commits
git log --oneline -5

# Hacer commit de cambios
git add .
git commit -m "Descripción del cambio"
git push origin main
```

---

## 🔄 Flujo de Desarrollo

### Modificar Temporadas

1. **Editar datos en `autumn-features.js`:**
```javascript
// Ejemplo: Agregar nueva planta a primavera
seasons.primavera.featuredPlants.push({
  icon: '🌷',
  title: 'Tulipanes Holandeses',
  desc: 'Flores tempranas de colores vibrantes'
});
```

2. **Actualizar precios del kit:**
```javascript
seasons.primavera.preparationData.kit.products[0].price = 4000;
seasons.primavera.preparationData.kit.finalPrice = 18000;
```

3. **Agregar nuevos consejos:**
```javascript
seasons.verano.tips.push({
  title: 'Riego nocturno',
  desc: 'Regar después de las 20hs para evitar evaporación'
});
```

### Agregar Nuevos Elementos Dinámicos

1. **En HTML - Agregar ID único:**
```html
<div id="nuevo-elemento">Contenido estático temporal</div>
```

2. **En JavaScript - Crear función de actualización:**
```javascript
function updateNuevoElemento(config) {
  const elemento = document.getElementById('nuevo-elemento');
  if (elemento) {
    elemento.textContent = `Contenido para ${config.name}`;
    elemento.className = `clase-base ${config.gradient}`;
  }
}
```

3. **Integrar en `changeSeason()`:**
```javascript
function changeSeason(season) {
  // ... código existente ...
  updateNuevoElemento(config);
  // ... resto del código ...
}
```

### Testing de Temporadas

El sitio incluye botones de testing en la sección hero:
```html
<button onclick="changeSeason('primavera')">🌸 Primavera</button>
<button onclick="changeSeason('verano')">☀️ Verano</button>
<button onclick="changeSeason('otono')">🍂 Otoño</button>
<button onclick="changeSeason('invierno')">❄️ Invierno</button>
```

---

## 🐛 Debugging y Resolución de Problemas

### Console Logs del Sistema
El sistema incluye logs detallados:
```javascript
console.log('🍂 Cargando sistema estacional Los Cocos v6.1...');
console.log('🔄 Cambiando a temporada:', season);
console.log('✅ Sección de especies actualizada');
```

### Errores Comunes y Soluciones

1. **Error: "Address already in use"**
```bash
# Solución: Matar procesos existentes
pkill -f "python3 -m http.server"
sleep 2
python3 -m http.server 8001
```

2. **JavaScript no se carga:**
```html
<!-- Verificar versión en HTML -->
<script src="autumn-features.js?v=6.1"></script>
```

3. **Elementos no se actualizan:**
```javascript
// Verificar que el ID existe en HTML
const elemento = document.getElementById('mi-elemento');
if (!elemento) {
  console.error('Elemento no encontrado:', 'mi-elemento');
}
```

4. **Temporada no cambia:**
```javascript
// Verificar que la temporada existe en el objeto seasons
if (!seasons[season]) {
  console.error('Temporada no encontrada:', season);
  return;
}
```

### Herramientas de Debug

1. **Consola del navegador:** F12 → Console
2. **Network tab:** Verificar carga de archivos
3. **Elements tab:** Inspeccionar cambios en DOM
4. **Sources tab:** Debug de JavaScript

---

## 📊 Estado Actual del Proyecto (v6.1)

### ✅ Funcionalidades Implementadas

- [x] Sistema estacional completo para 4 temporadas
- [x] Detección automática de temporada por fecha
- [x] Cambio dinámico de todos los elementos visuales
- [x] Calendario de actividades por temporada
- [x] Panel de consejos adaptativos
- [x] Kit de productos variables
- [x] Modales interactivos (ofertas y consejos)
- [x] Carrito de compras funcional
- [x] Diseño responsive
- [x] Sistema de testing integrado

### 🔧 Funcionalidades Técnicas

- [x] Manejo de errores con try-catch
- [x] Event listeners para interacciones
- [x] Funciones modulares y reutilizables
- [x] Código documentado con comentarios
- [x] Versionado de archivos JavaScript
- [x] Logs de debug detallados

### 📈 Métricas del Proyecto

- **Archivos principales:** 2 (home.html, autumn-features.js)
- **Líneas de código JS:** ~1,200 líneas
- **Funciones principales:** 15+
- **Temporadas soportadas:** 4 completas
- **Elementos dinámicos:** 20+
- **Versión actual:** v6.1

---

## 🚀 Próximas Iteraciones Sugeridas

### Prioridad Alta
1. **Sistema de inventario:** Conectar con base de datos real
2. **Checkout funcional:** Integrar pasarela de pagos
3. **Panel de administración:** CRUD para productos y temporadas
4. **API REST:** Backend para gestión de datos

### Prioridad Media
5. **Optimización de imágenes:** Lazy loading y WebP
6. **PWA:** Service Workers y funcionamiento offline
7. **SEO:** Meta tags dinámicos por temporada
8. **Analytics:** Tracking de interacciones por temporada

### Prioridad Baja
9. **Tests automatizados:** Jest/Cypress para testing
10. **Internacionalización:** Soporte multi-idioma
11. **Theme personalizable:** Modo oscuro/claro
12. **Notificaciones:** Push notifications para ofertas

---

## 📝 Convenciones de Código

### Nomenclatura
- **Variables:** camelCase (`currentSeason`)
- **Funciones:** camelCase con verbo (`updateCalendar`)
- **IDs HTML:** kebab-case (`season-info-panel`)
- **Clases CSS:** kebab-case (`btn-primary`)

### Estructura de Commits
```
✨ feat: Nueva funcionalidad
🔧 fix: Corrección de bug
📝 docs: Actualización de documentación
🎨 style: Cambios de estilo/formato
♻️ refactor: Refactorización de código
🚀 perf: Mejora de performance
🧪 test: Agregado/actualización de tests
```

### Comentarios en Código
```javascript
// Función principal - Descripción breve
function miFuncion() {
  // Paso 1: Explicación del paso
  const variable = valor;
  
  // Paso 2: Otra explicación
  try {
    // Lógica principal
  } catch (error) {
    console.error('❌ Error en miFuncion:', error);
  }
}
```

---

## 🤝 Guía para Handover

### Para LLMs
1. **Contexto completo:** Esta documentación + código fuente
2. **Problema a resolver:** Especificar qué funcionalidad agregar/modificar
3. **Archivos principales:** Enfocar en `home.html` y `autumn-features.js`
4. **Testing:** Usar botones de temporada para verificar cambios
5. **Versionado:** Incrementar versión en script tag al hacer cambios

### Para Humanos
1. **Setup inicial:** Seguir sección "Configuración de Desarrollo"
2. **Explorar código:** Comenzar por `autumn-features.js` líneas 1-100
3. **Testing local:** Cambiar temporadas y observar comportamiento
4. **Modificaciones:** Seguir "Flujo de Desarrollo"
5. **Debug:** Usar herramientas de navegador + console logs

### Archivos Críticos a Entender
1. **`autumn-features.js`** - Sistema estacional completo
2. **`home.html`** - Estructura HTML con IDs dinámicos
3. **`cart.js`** - Funcionalidad del carrito
4. **Esta documentación** - Referencia completa

---

## 📞 Información de Contacto

- **Repositorio:** https://github.com/martinsantos/vivero
- **Servidor local:** http://localhost:8001/home.html
- **Versión actual:** v6.1
- **Última actualización:** Junio 2025

---

## 🏷️ Tags y Keywords

`#vivero` `#mendoza` `#javascript` `#estacional` `#dinamico` `#temporadas` `#plantas` `#ecommerce` `#responsive` `#tailwind` `#vanilla-js` `#frontend` `#desarrollo-web`

---

*Esta documentación está viva y debe actualizarse con cada iteración significativa del proyecto.* 