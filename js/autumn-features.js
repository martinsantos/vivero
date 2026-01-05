// Los Cocos - Sistema Estacional COMPLETO v6.1 - CORRECCIÓN TOTAL

console.log('🍂 Cargando sistema estacional Los Cocos v6.1 - CORRECCIÓN COMPLETA...');

// Variables globales
let currentSeason = 'otono';
let offerQuantity = 1;

// Configuración COMPLETA de temporadas
const seasons = {
  primavera: {
    name: 'Primavera',
    icon: '🌸',
    period: 'Septiembre - Noviembre',
    background: 'https://images.unsplash.com/photo-1490750967868-88aa4486c946?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
    overlay: 'from-green-900/85 via-emerald-800/75 to-teal-900/85',
    gradient: 'from-green-500 to-emerald-600',
    colors: { primary: '#10B981', secondary: '#059669' },
    title: 'refugio primaveral',
    description: 'Renueva tu jardín • Plantas en floración • Siembra y cuidados',
    
    // Sección especies de temporada
    speciesData: {
      bannerText: 'ESPECIAL PRIMAVERA 2024',
      title: 'Especies de Temporada',
      description: 'Descubre las plantas que florecen en primavera mendocina. Nuevos brotes, colores frescos y el renacer de tu jardín.',
      bannerGradient: 'from-green-500 to-emerald-600',
      titleGradient: 'from-green-600 to-emerald-600'
    },
    
    // Sección preparación
    preparationData: {
      sectionGradient: 'from-green-900 via-emerald-800 to-teal-900',
      bannerText: 'GUÍA PROFESIONAL',
      title: 'Prepara tu Jardín para la Primavera',
      description: 'Sigue nuestra guía paso a paso para que tu jardín despierte con toda su fuerza durante la primavera mendocina.',
      steps: [
        {
          number: 1,
          icon: '🌱',
          title: 'Siembra de Temporada',
          description: 'Planta semillas de flores anuales y prepara almácigos para la nueva temporada.',
          tips: '✓ Semillas de temporada • ✓ Sustrato premium • ✓ Bandejas de siembra'
        },
        {
          number: 2,
          icon: '🌿',
          title: 'Poda de Renovación',
          description: 'Elimina ramas secas y estimula el nuevo crecimiento con podas estratégicas.',
          tips: '✓ Herramientas afiladas • ✓ Cicatrizante • ✓ Desinfectante'
        },
        {
          number: 3,
          icon: '💚',
          title: 'Fertilización Abundante',
          description: 'Aplica fertilizantes ricos en nitrógeno para estimular el crecimiento vigoroso.',
          tips: '✓ NPK 20-10-10 • ✓ Compost fresco • ✓ Humus de lombriz'
        },
        {
          number: 4,
          icon: '💧',
          title: 'Sistema de Riego',
          description: 'Instala o revisa el sistema de riego para la temporada de mayor crecimiento.',
          tips: '✓ Goteo automático • ✓ Timer programable • ✓ Sensores de humedad'
        }
      ],
      kit: {
        title: '🛒 Kit Preparación Primavera',
        products: [
          { name: 'Semillas Premium Mix (10 variedades)', price: 3500 },
          { name: 'Sustrato Siembra Profesional (40L)', price: 2800 },
          { name: 'Fertilizante NPK 20-10-10 (5kg)', price: 4200 },
          { name: 'Sistema Riego Goteo Básico', price: 6800 },
          { name: 'Kit Herramientas Siembra', price: 3200 }
        ],
        individualPrice: 20500,
        finalPrice: 16400,
        savings: 4100
      }
    },
    
    featuredPlants: [
      { icon: '🌸', title: 'Rosas Primaverales', desc: 'Variedades tempranas que florecen con los primeros calores' },
      { icon: '🌺', title: 'Petunias Colgantes', desc: 'Perfectas para balcones y jardines verticales primaverales' },
      { icon: '🌻', title: 'Girasoles Enanos', desc: 'Alegría y color para macetas y jardines pequeños' }
    ],
    
    offers: {
      title: 'OFERTA ESPECIAL DE PRIMAVERA',
      subtitle: '¡Pack Primaveral 50% OFF!',
      originalPrice: 35000,
      price: 18500,
      products: [
        { name: 'Rosas Premium (3 unidades)', desc: 'Variedades David Austin', price: 15000, isGift: false },
        { name: 'Kit Sustrato Premium', desc: 'Compost + perlita + turba', price: 4500, isGift: false },
        { name: 'Fertilizante Floración', desc: 'NPK 10-30-20 concentrado', price: 0, isGift: true }
      ]
    },
    
    tips: [
      { title: 'Siembra directa', desc: 'Plantar semillas de flores anuales en tierra preparada' },
      { title: 'Poda de renovación', desc: 'Eliminar ramas viejas para estimular nuevo crecimiento' },
      { title: 'Fertilización abundante', desc: 'Aplicar fertilizantes ricos en fósforo para floración' },
      { title: 'Riego constante', desc: 'Mantener humedad constante durante la brotación' }
    ]
  },
  
  verano: {
    name: 'Verano',
    icon: '☀️',
    period: 'Diciembre - Febrero',
    background: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
    overlay: 'from-yellow-900/85 via-orange-800/75 to-red-900/85',
    gradient: 'from-yellow-500 to-orange-600',
    colors: { primary: '#F59E0B', secondary: '#D97706' },
    title: 'oasis veraniego',
    description: 'Protege tu jardín del calor • Riego eficiente • Plantas resistentes',
    
    speciesData: {
      bannerText: 'ESPECIAL VERANO 2024',
      title: 'Especies de Temporada',
      description: 'Plantas que resisten el intenso calor mendocino. Colores vibrantes y adaptadas al clima seco del verano.',
      bannerGradient: 'from-yellow-500 to-orange-600',
      titleGradient: 'from-yellow-600 to-orange-600'
    },
    
    preparationData: {
      sectionGradient: 'from-yellow-900 via-orange-800 to-red-900',
      bannerText: 'GUÍA PROFESIONAL',
      title: 'Protege tu Jardín del Verano',
      description: 'Estrategias efectivas para mantener tu jardín próspero durante el intenso calor mendocino.',
      steps: [
        {
          number: 1,
          icon: '🌵',
          title: 'Plantas Resistentes',
          description: 'Selecciona especies adaptadas al calor intenso y la sequía estival.',
          tips: '✓ Cactus nativos • ✓ Suculentas variadas • ✓ Aromáticas mediterráneas'
        },
        {
          number: 2,
          icon: '💧',
          title: 'Riego Eficiente',
          description: 'Implementa sistemas de riego que conserven agua y mantengan humedad.',
          tips: '✓ Riego por goteo • ✓ Mulch protector • ✓ Horarios estratégicos'
        },
        {
          number: 3,
          icon: '☂️',
          title: 'Sombra Protectora',
          description: 'Instala sistemas de sombreado para proteger plantas sensibles.',
          tips: '✓ Mallas de sombra • ✓ Pergolas naturales • ✓ Plantas protectoras'
        },
        {
          number: 4,
          icon: '🌿',
          title: 'Mantenimiento Mínimo',
          description: 'Reduce tareas de mantenimiento durante las horas de mayor calor.',
          tips: '✓ Podas ligeras • ✓ Fertilización moderada • ✓ Control preventivo'
        }
      ],
      kit: {
        title: '🛒 Kit Protección Verano',
        products: [
          { name: 'Cactus Mix Resistentes (5 variedades)', price: 4200 },
          { name: 'Sistema Riego Automático Premium', price: 8500 },
          { name: 'Malla Sombra UV 70% (10m²)', price: 3800 },
          { name: 'Mulch Corteza Premium (80L)', price: 2900 },
          { name: 'Fertilizante Verano Equilibrado', price: 2400 }
        ],
        individualPrice: 21800,
        finalPrice: 16350,
        savings: 5450
      }
    },
    
    featuredPlants: [
      { icon: '🌻', title: 'Girasoles Gigantes', desc: 'Resistentes al sol intenso, crecen hasta 3 metros' },
      { icon: '🌵', title: 'Cactus Ornamentales', desc: 'Mínimo riego, máximo impacto decorativo' },
      { icon: '🌺', title: 'Bougainvilleas', desc: 'Flores todo el verano, muy resistentes al calor' }
    ],
    
    offers: {
      title: 'OFERTA ESPECIAL DE VERANO',
      subtitle: '¡Pack Anti-Calor 50% OFF!',
      originalPrice: 32000,
      price: 16800,
      products: [
        { name: 'Cactus Mix (5 variedades)', desc: 'Resistentes al sol intenso', price: 12000, isGift: false },
        { name: 'Sistema Riego Automático', desc: 'Timer + goteros', price: 8500, isGift: false },
        { name: 'Sombra UV Premium', desc: 'Malla 70% protección', price: 0, isGift: true }
      ]
    },
    
    tips: [
      { title: 'Riego temprano', desc: 'Regar antes del amanecer para reducir evaporación' },
      { title: 'Mulch protector', desc: 'Cubrir suelo para mantener humedad y frescura' },
      { title: 'Sombra artificial', desc: 'Instalar mallas de sombra en horas de mayor calor' },
      { title: 'Plantas nativas', desc: 'Preferir especies adaptadas al clima mendocino' }
    ]
  },
  
  otono: {
    name: 'Otoño',
    icon: '🍂',
    period: 'Marzo - Mayo',
    background: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
    overlay: 'from-amber-900/85 via-orange-800/75 to-red-900/85',
    gradient: 'from-orange-500 to-red-600',
    colors: { primary: '#EA580C', secondary: '#DC2626' },
    title: 'refugio otoñal',
    description: 'Prepara tu jardín para el otoño • Plantas de temporada • Asesoramiento especializado',
    
    speciesData: {
      bannerText: 'ESPECIAL OTOÑO 2024',
      title: 'Especies de Temporada',
      description: 'Descubre las plantas que más brillan durante el otoño mendocino. Colores vibrantes y resistencia natural para esta época del año.',
      bannerGradient: 'from-orange-500 to-red-500',
      titleGradient: 'from-orange-600 to-red-600'
    },
    
    preparationData: {
      sectionGradient: 'from-amber-900 via-orange-800 to-red-900',
      bannerText: 'GUÍA PROFESIONAL',
      title: 'Prepara tu Jardín para el Otoño',
      description: 'Sigue nuestra guía paso a paso para que tu jardín luzca espectacular durante el otoño y esté preparado para el invierno mendocino.',
      steps: [
        {
          number: 1,
          icon: '🌱',
          title: 'Abona la Tierra',
          description: 'Aplica compost orgánico y abono de liberación lenta para nutrir el suelo antes del invierno.',
          tips: '✓ Compost orgánico • ✓ Abono NPK • ✓ Humus de lombriz'
        },
        {
          number: 2,
          icon: '🍂',
          title: 'Rota las Forrajeras',
          description: 'Cambia la ubicación de tus cultivos forrajeros para evitar el agotamiento del suelo.',
          tips: '✓ Rotación de cultivos • ✓ Leguminosas • ✓ Descanso del suelo'
        },
        {
          number: 3,
          icon: '💧',
          title: 'Rocía las Plantas',
          description: 'Aplica tratamientos preventivos contra plagas y hongos durante el otoño.',
          tips: '✓ Fungicidas naturales • ✓ Aceite de neem • ✓ Extractos vegetales'
        },
        {
          number: 4,
          icon: '❄️',
          title: 'Protege del Frío',
          description: 'Cubre las plantas sensibles con mantas térmicas para prepararlas para el invierno.',
          tips: '✓ Mantas térmicas • ✓ Mulch orgánico • ✓ Invernaderos mini'
        }
      ],
      kit: {
        title: '🛒 Kit Preparación Otoño',
        products: [
          { name: 'Compost Orgánico Premium (20kg)', price: 4500 },
          { name: 'Abono NPK Liberación Lenta', price: 3200 },
          { name: 'Aceite de Neem Concentrado', price: 2800 },
          { name: 'Manta Térmica Jardín (5m²)', price: 5100 },
          { name: 'Mulch Corteza Pino (50L)', price: 2900 }
        ],
        individualPrice: 18500,
        finalPrice: 14500,
        savings: 4000
      }
    },
    
    featuredPlants: [
      { icon: '🍂', title: 'Plantas de Otoño', desc: 'Crisantemos, asters y plantas que brillan en esta temporada dorada' },
      { icon: '🌰', title: 'Preparación Invernal', desc: 'Abonos, mulch y todo lo necesario para proteger tu jardín' },
      { icon: '🍁', title: 'Consejos Expertos', desc: 'Guías personalizadas para cada etapa del otoño mendocino' }
    ],
    
    offers: {
      title: 'OFERTA ESPECIAL DE OTOÑO',
      subtitle: '¡Pack Otoñal 50% OFF!',
      originalPrice: 38000,
      price: 20100,
      products: [
        { name: 'Crisantemos Premium (6 unidades)', desc: 'Colores surtidos otoñales', price: 18000, isGift: false },
        { name: 'Abono Otoñal Especial', desc: 'Rico en potasio 15kg', price: 6500, isGift: false },
        { name: 'Manta Térmica Jardín', desc: 'Protección contra heladas', price: 0, isGift: true }
      ]
    },
    
    tips: [
      { title: 'Poda de formación', desc: 'Eliminar ramas secas y dar forma antes del invierno' },
      { title: 'Protección contra heladas', desc: 'Mantas térmicas para plantas sensibles al frío' },
      { title: 'Abonado específico', desc: 'Fertilizantes ricos en potasio para fortalecer raíces' },
      { title: 'Control de plagas', desc: 'Inspeccionar y tratar pulgones y cochinillas antes del invierno' }
    ]
  },
  
  invierno: {
    name: 'Invierno',
    icon: '❄️',
    period: 'Junio - Agosto',
    background: 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
    overlay: 'from-blue-900/85 via-indigo-800/75 to-purple-900/85',
    gradient: 'from-blue-500 to-indigo-600',
    colors: { primary: '#3B82F6', secondary: '#1D4ED8' },
    title: 'refugio invernal',
    description: 'Protección total del frío • Plantas de interior • Planificación primaveral',
    
    speciesData: {
      bannerText: 'ESPECIAL INVIERNO 2024',
      title: 'Especies de Temporada',
      description: 'Plantas resistentes al frío mendocino. Variedades de interior y exterior que prosperan en temperaturas bajas.',
      bannerGradient: 'from-blue-500 to-indigo-600',
      titleGradient: 'from-blue-600 to-indigo-600'
    },
    
    preparationData: {
      sectionGradient: 'from-blue-900 via-indigo-800 to-purple-900',
      bannerText: 'GUÍA PROFESIONAL',
      title: 'Protege tu Jardín del Invierno',
      description: 'Estrategias de protección y cuidado para mantener tu jardín saludable durante el frío mendocino.',
      steps: [
        {
          number: 1,
          icon: '🏠',
          title: 'Plantas al Interior',
          description: 'Traslada las especies sensibles al frío a espacios protegidos y cálidos.',
          tips: '✓ Macetas móviles • ✓ Ubicación luminosa • ✓ Humedad controlada'
        },
        {
          number: 2,
          icon: '❄️',
          title: 'Protección Exterior',
          description: 'Cubre y protege las plantas que permanecen en el jardín.',
          tips: '✓ Mantas térmicas • ✓ Plásticos protectores • ✓ Mulch espeso'
        },
        {
          number: 3,
          icon: '💧',
          title: 'Riego Moderado',
          description: 'Reduce la frecuencia de riego debido a la menor evaporación.',
          tips: '✓ Riego espaciado • ✓ Evitar encharcamiento • ✓ Horarios de calor'
        },
        {
          number: 4,
          icon: '📋',
          title: 'Planificación Futura',
          description: 'Diseña y planea las mejoras para la próxima temporada de crecimiento.',
          tips: '✓ Diseño de espacios • ✓ Compra de semillas • ✓ Herramientas nuevas'
        }
      ],
      kit: {
        title: '🛒 Kit Protección Invierno',
        products: [
          { name: 'Plantas Interior Mix (4 unidades)', price: 7200 },
          { name: 'Mantas Térmicas Premium (pack 3)', price: 4800 },
          { name: 'Calefactor Para Plantas', price: 6500 },
          { name: 'Humidificador Natural', price: 2200 },
          { name: 'Fertilizante Invierno Especializado', price: 1800 }
        ],
        individualPrice: 22500,
        finalPrice: 17200,
        savings: 5300
      }
    },
    
    featuredPlants: [
      { icon: '❄️', title: 'Plantas de Frío', desc: 'Especies resistentes a heladas y bajas temperaturas' },
      { icon: '🏠', title: 'Variedades Interior', desc: 'Plantas de interior que purifican el aire en invierno' },
      { icon: '🌱', title: 'Preparación Futura', desc: 'Planificación y preparación para la próxima primavera' }
    ],
    
    offers: {
      title: 'OFERTA ESPECIAL DE INVIERNO',
      subtitle: '¡Pack Invernal 50% OFF!',
      originalPrice: 28000,
      price: 14500,
      products: [
        { name: 'Plantas Interior Mix (4 unidades)', desc: 'Pothos, sansevieria, ZZ', price: 16000, isGift: false },
        { name: 'Calefactor Para Plantas', desc: 'Protección anti-heladas', price: 7200, isGift: false },
        { name: 'Humidificador Natural', desc: 'Bandeja + piedras volcánicas', price: 0, isGift: true }
      ]
    },
    
    tips: [
      { title: 'Protección de heladas', desc: 'Cubrir plantas sensibles con mantas o plásticos' },
      { title: 'Riego moderado', desc: 'Reducir frecuencia de riego debido a menor evaporación' },
      { title: 'Planificación primaveral', desc: 'Diseñar y planear el jardín para la próxima temporada' },
      { title: 'Mantenimiento de herramientas', desc: 'Limpiar y mantener equipos de jardinería' }
    ]
  }
};

// Función principal mejorada para cambiar temporada
function changeSeason(season) {
  console.log(`🔄 Cambiando a temporada: ${season}`);
  
  if (!seasons[season]) {
    console.error(`❌ Temporada no encontrada: ${season}`);
    return;
  }
  
  currentSeason = season;
  const config = seasons[season];
  
  try {
    // Actualizar TODOS los elementos de la interfaz
    updateSeasonIndicator(config);
    updateBackground(config);
    updateHeroContent(config);
    updateTimelineCards(season);
    updateOfferButton(config);
    updateFeaturedPlants(config);
    updateSeasonTips(config);
    updateBanner(config);
    updateSpeciesSection(config);
    updatePreparationSection(config);
    updateCalendar(config);
    updateSeasonInfoPanel(config);
    updateActionButtons(config);
    
    console.log(`✅ Temporada ${config.name} aplicada completamente`);
  } catch (error) {
    console.error('❌ Error al cambiar temporada:', error);
  }
}

// NUEVA FUNCIÓN: Actualizar sección de especies de temporada
function updateSpeciesSection(config) {
  console.log('🔄 Actualizando sección de especies de temporada...');
  
  try {
    const speciesData = config.speciesData;
    
    // Actualizar banner de especies
    const speciesBanner = document.getElementById('species-banner');
    const speciesIconLeft = document.getElementById('species-icon-left');
    const speciesBannerText = document.getElementById('species-banner-text');
    const speciesIconRight = document.getElementById('species-icon-right');
    
    if (speciesBanner) {
      speciesBanner.className = `inline-flex items-center gap-3 bg-gradient-to-r ${speciesData.bannerGradient} text-white px-6 py-3 rounded-full text-sm font-bold mb-6 shadow-lg`;
    }
    if (speciesIconLeft) speciesIconLeft.textContent = config.icon;
    if (speciesBannerText) speciesBannerText.textContent = speciesData.bannerText;
    if (speciesIconRight) speciesIconRight.textContent = config.icon;
    
    // Actualizar título y descripción
    const speciesTitle = document.getElementById('species-title');
    const speciesDescription = document.getElementById('species-description');
    
    if (speciesTitle) {
      speciesTitle.className = `bg-gradient-to-r ${speciesData.titleGradient} bg-clip-text text-transparent`;
      speciesTitle.textContent = speciesData.title;
    }
    if (speciesDescription) {
      speciesDescription.textContent = speciesData.description;
    }
    
    console.log('✅ Sección de especies actualizada correctamente');
  } catch (error) {
    console.error('❌ Error actualizando sección de especies:', error);
  }
}

// Actualizar indicador de temporada
function updateSeasonIndicator(config) {
  const elements = {
    currentSeason: document.getElementById('current-season'),
    iconLeft: document.getElementById('season-icon-left'),
    iconRight: document.getElementById('season-icon-right'),
    indicator: document.getElementById('season-indicator')
  };
  
  if (elements.currentSeason) {
    elements.currentSeason.textContent = `${config.name} 2024`;
  }
  if (elements.iconLeft) {
    elements.iconLeft.textContent = config.icon;
  }
  if (elements.iconRight) {
    elements.iconRight.textContent = config.icon;
  }
  if (elements.indicator) {
    elements.indicator.className = `inline-flex items-center gap-3 bg-gradient-to-r ${config.gradient}/90 backdrop-blur-md text-white px-8 py-4 rounded-2xl shadow-2xl transition-all duration-500`;
  }
}

// Actualizar fondo
function updateBackground(config) {
  const background = document.getElementById('hero-background');
  const overlay = document.getElementById('hero-overlay');
  
  if (background) {
    background.style.transition = 'opacity 0.5s ease';
    background.style.opacity = '0';
    
    setTimeout(() => {
      background.src = config.background;
      background.onload = () => {
        background.style.opacity = '1';
      };
    }, 250);
  }
  
  if (overlay) {
    overlay.className = `absolute inset-0 bg-gradient-to-r ${config.overlay} transition-all duration-1000`;
  }
}

// Actualizar contenido del hero
function updateHeroContent(config) {
  const subtitle = document.getElementById('hero-subtitle');
  const description = document.getElementById('hero-description');
  const divider = document.getElementById('hero-divider');
  
  if (subtitle) {
    subtitle.textContent = config.title;
  }
  if (description) {
    description.textContent = config.description;
  }
  if (divider) {
    divider.className = `w-40 h-3 bg-gradient-to-r ${config.gradient} mx-auto rounded-full animate-pulse shadow-lg`;
  }
}

// Actualizar cards del timeline
function updateTimelineCards(activeSeason) {
  const cards = document.querySelectorAll('[data-season]');
  
  cards.forEach(card => {
    const season = card.getAttribute('data-season');
    const existingBadge = card.querySelector('.absolute');
    
    // Remover badge existente
    if (existingBadge) {
      existingBadge.remove();
    }
    
    // Remover clase active
    card.classList.remove('active-season');
    card.classList.remove('border-2', 'border-orange-400', 'ring-2', 'ring-orange-300/50');
    card.classList.remove('border-green-400', 'ring-green-300/50');
    card.classList.remove('border-yellow-400', 'ring-yellow-300/50');
    card.classList.remove('border-blue-400', 'ring-blue-300/50');
    
    // Si es la temporada activa, agregar badge y estilos
    if (season === activeSeason) {
      const config = seasons[activeSeason];
      card.classList.add('active-season');
      
      // Aplicar colores de la temporada actual
      const colorClass = getSeasonColorClass(activeSeason);
      card.classList.add('border-2', colorClass.border, 'ring-2', colorClass.ring);
      
      const badge = document.createElement('div');
      badge.className = `absolute -top-2 -right-2 bg-gradient-to-r ${config.gradient} text-white text-xs px-2 py-1 rounded-full font-bold animate-pulse shadow-lg`;
      badge.textContent = 'ACTUAL';
      card.appendChild(badge);
    }
  });
}

// Obtener clases de color por temporada
function getSeasonColorClass(season) {
  const colorMap = {
    primavera: { border: 'border-green-400', ring: 'ring-green-300/50' },
    verano: { border: 'border-yellow-400', ring: 'ring-yellow-300/50' },
    otono: { border: 'border-orange-400', ring: 'ring-orange-300/50' },
    invierno: { border: 'border-blue-400', ring: 'ring-blue-300/50' }
  };
  return colorMap[season] || colorMap.otono;
}

// Actualizar botón de oferta
function updateOfferButton(config) {
  const offerButton = document.getElementById('offer-button');
  const offerTitle = document.getElementById('offer-title');
  const offerText = document.getElementById('offer-text');
  const offerIconLeft = document.getElementById('offer-icon-left');
  const offerIconRight = document.getElementById('offer-icon-right');
  
  if (offerTitle) {
    offerTitle.textContent = config.offers.title;
  }
  if (offerText) {
    offerText.textContent = config.offers.subtitle;
  }
  if (offerIconLeft) {
    offerIconLeft.textContent = config.icon;
  }
  if (offerIconRight) {
    offerIconRight.textContent = config.icon;
  }
  if (offerButton) {
    offerButton.className = `btn-clean bg-gradient-to-r ${config.gradient} hover:opacity-90 text-white smooth-transition group max-w-md mx-auto flex items-center gap-4 shadow-xl`;
  }
}

// Actualizar plantas destacadas
function updateFeaturedPlants(config) {
  const featuresGrid = document.getElementById('features-grid');
  if (!featuresGrid) return;
  
  const cards = featuresGrid.querySelectorAll('.clean-card');
  config.featuredPlants.forEach((plant, index) => {
    if (cards[index]) {
      const icon = cards[index].querySelector('[id^="feature-"][id$="-icon"]');
      const title = cards[index].querySelector('[id^="feature-"][id$="-title"]');
      const description = cards[index].querySelector('[id^="feature-"][id$="-description"]');
      
      if (icon) icon.textContent = plant.icon;
      if (title) title.textContent = plant.title;
      if (description) description.textContent = plant.desc;
    }
  });
}

// Actualizar consejos de temporada
function updateSeasonTips(config) {
  const tipTitle = document.getElementById('season-tip-title');
  const tipIcon = document.getElementById('season-tip-icon');
  const tipIcon2 = document.getElementById('season-tip-icon-2');
  const tipPreview = document.getElementById('season-tip-preview');
  
  if (tipTitle) {
    tipTitle.textContent = `Consejos para ${config.name}`;
  }
  if (tipIcon) {
    tipIcon.textContent = config.icon;
  }
  if (tipIcon2) {
    tipIcon2.textContent = config.icon;
  }
  if (tipPreview) {
    tipPreview.textContent = `Es momento de ${config.tips[0].desc.toLowerCase()}. ${config.tips[1].desc}`;
  }
}

// Actualizar banner de ofertas
function updateBanner(config) {
  const bannerTitle = document.getElementById('banner-title');
  const bannerDescription = document.getElementById('banner-description');
  const seasonOfferBanner = document.getElementById('season-offer-banner');
  
  if (bannerTitle) {
    bannerTitle.textContent = `🎯 Oferta Especial de ${config.name}`;
  }
  if (bannerDescription) {
    bannerDescription.textContent = `${config.offers.subtitle}. Válido hasta fin de mes.`;
  }
  if (seasonOfferBanner) {
    seasonOfferBanner.className = `bg-gradient-to-r ${config.gradient} rounded-3xl p-8 text-white text-center shadow-2xl transition-all duration-500`;
  }
}

// Funciones de modal mejoradas
function openDailyOfferPopup() {
  const modal = document.getElementById('daily-offer-modal');
  if (modal) {
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    updateOfferModal();
  }
}

function updateOfferModal() {
  console.log('🔄 Actualizando modal de ofertas...');
  const config = seasons[currentSeason];
  
  try {
    // Actualizar header del modal
    const headerDiv = document.querySelector('#daily-offer-modal .bg-gradient-to-r');
    if (headerDiv) {
      headerDiv.className = `bg-gradient-to-r ${config.gradient} text-white p-8 relative overflow-hidden`;
    }
    
    // Actualizar título del modal
    const titleElement = document.querySelector('#daily-offer-modal h2');
    if (titleElement) {
      titleElement.innerHTML = `🔥 ${config.offers.title} 🔥`;
    }
    
    // Actualizar descripción
    const descElement = document.querySelector('#daily-offer-modal .text-xl.opacity-90');
    if (descElement) {
      descElement.textContent = `${config.offers.subtitle} - Solo por hoy`;
    }
    
    // Actualizar productos en el modal
    const productContainers = document.querySelectorAll('#daily-offer-modal .flex.items-center.gap-4');
    console.log(`📦 Encontrados ${productContainers.length} contenedores de productos`);
    
    config.offers.products.forEach((product, index) => {
      if (productContainers[index]) {
        const container = productContainers[index];
        const nameElement = container.querySelector('.font-bold.text-gray-900');
        const descElement = container.querySelector('.text-sm.text-gray-600');
        const priceElements = container.querySelectorAll('.font-bold');
        const priceElement = priceElements[priceElements.length - 1]; // Último elemento con clase font-bold
        
        if (nameElement) {
          nameElement.textContent = product.name;
          console.log(`✅ Actualizado producto ${index + 1}: ${product.name}`);
        }
        if (descElement) {
          descElement.textContent = product.desc;
        }
        if (priceElement) {
          if (product.isGift) {
            priceElement.textContent = 'REGALO';
            priceElement.className = 'text-green-600 font-bold';
          } else {
            priceElement.textContent = `$${product.price.toLocaleString()}`;
            priceElement.className = getSeasonPriceClass(currentSeason);
          }
        }
        
        // Actualizar color de fondo del contenedor
        if (product.isGift) {
          container.className = container.className.replace(/bg-\w+-50/g, 'bg-green-50');
        } else {
          const bgClass = getSeasonBgClass(currentSeason);
          container.className = container.className.replace(/bg-\w+-50/g, bgClass);
        }
      }
    });
    
    // Actualizar precios totales
    const originalPriceElement = document.querySelector('#daily-offer-modal .line-through');
    const finalPriceElement = document.querySelector('#daily-offer-modal .text-2xl.font-bold:not(.line-through)');
    
    if (originalPriceElement) {
      originalPriceElement.textContent = `$${config.offers.originalPrice.toLocaleString()}`;
    }
    if (finalPriceElement) {
      finalPriceElement.textContent = `$${config.offers.price.toLocaleString()}`;
      finalPriceElement.className = `text-2xl font-bold ${getSeasonTextClass(currentSeason)}`;
    }
    
    console.log('✅ Modal de ofertas actualizado correctamente');
  } catch (error) {
    console.error('❌ Error actualizando modal de ofertas:', error);
  }
}

function openSeasonTipsPopup() {
  const modal = document.getElementById('season-tips-modal');
  if (modal) {
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    updateTipsModal();
  }
}

function updateTipsModal() {
  console.log('🔄 Actualizando modal de consejos...');
  const config = seasons[currentSeason];
  
  try {
    // Actualizar header
    const titleElement = document.getElementById('tips-season-title');
    const iconElement = document.getElementById('tips-season-icon');
    const icon2Element = document.getElementById('tips-season-icon-2');
    const subtitleElement = document.getElementById('tips-season-subtitle');
    const headerElement = document.getElementById('tips-header');
    
    if (titleElement) titleElement.textContent = `Consejos para ${config.name}`;
    if (iconElement) iconElement.textContent = config.icon;
    if (icon2Element) icon2Element.textContent = config.icon;
    if (subtitleElement) {
      subtitleElement.textContent = `Guía completa para cuidar tu jardín durante ${config.period.toLowerCase()}`;
    }
    if (headerElement) {
      headerElement.className = `bg-gradient-to-r ${config.gradient} text-white p-8 relative overflow-hidden transition-all duration-500`;
    }
    
    // Actualizar contenido de consejos
    const tipsContent = document.getElementById('tips-content');
    if (tipsContent) {
      tipsContent.innerHTML = config.tips.map(tip => `
        <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors">
          <h4 class="font-bold text-gray-900 mb-2">${tip.title}</h4>
          <p class="text-gray-600 text-sm">${tip.desc}</p>
        </div>
      `).join('');
      console.log(`✅ Actualizados ${config.tips.length} consejos`);
    }
    
    // Actualizar plantas recomendadas
    const recommendedPlants = document.getElementById('recommended-plants');
    if (recommendedPlants) {
      recommendedPlants.innerHTML = config.featuredPlants.map(plant => `
        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
          <div class="text-2xl">${plant.icon}</div>
          <div>
            <h5 class="font-semibold text-gray-900">${plant.title}</h5>
            <p class="text-xs text-gray-600">${plant.desc}</p>
          </div>
        </div>
      `).join('');
      console.log(`✅ Actualizadas ${config.featuredPlants.length} plantas recomendadas`);
    }
    
    // Actualizar calendario mensual
    const monthlyCalendar = document.getElementById('monthly-calendar');
    if (monthlyCalendar) {
      const calendarItems = [
        `📅 Periodo: ${config.period}`,
        `🌱 Actividad principal: ${config.tips[0]?.title || 'Cuidado general'}`,
        `💡 Consejo destacado: ${config.tips[1]?.title || 'Mantenimiento'}`,
        `🎯 Enfoque: ${config.title}`
      ];
      
      monthlyCalendar.innerHTML = calendarItems.map(item => `
        <div class="text-sm text-gray-700">${item}</div>
      `).join('');
    }
    
    console.log('✅ Modal de consejos actualizado correctamente');
  } catch (error) {
    console.error('❌ Error actualizando modal de consejos:', error);
  }
}

// Funciones auxiliares para clases de colores
function getSeasonPriceClass(season) {
  const classMap = {
    primavera: 'text-green-600 font-bold',
    verano: 'text-yellow-600 font-bold',
    otono: 'text-orange-600 font-bold',
    invierno: 'text-blue-600 font-bold'
  };
  return classMap[season] || 'text-orange-600 font-bold';
}

function getSeasonTextClass(season) {
  const classMap = {
    primavera: 'text-green-600',
    verano: 'text-yellow-600',
    otono: 'text-orange-600',
    invierno: 'text-blue-600'
  };
  return classMap[season] || 'text-orange-600';
}

function getSeasonBgClass(season) {
  const classMap = {
    primavera: 'bg-green-50',
    verano: 'bg-yellow-50',
    otono: 'bg-orange-50',
    invierno: 'bg-blue-50'
  };
  return classMap[season] || 'bg-orange-50';
}

// Funciones de cantidad y carrito
function increaseOfferQuantity() {
  offerQuantity++;
  updateQuantityDisplay();
}

function decreaseOfferQuantity() {
  if (offerQuantity > 1) {
    offerQuantity--;
    updateQuantityDisplay();
  }
}

function updateQuantityDisplay() {
  const quantityElement = document.getElementById('offer-quantity');
  if (quantityElement) {
    quantityElement.textContent = offerQuantity;
  }
}

function addOfferToCart() {
  const config = seasons[currentSeason];
  alert(`¡${config.offers.title} agregado al carrito! Cantidad: ${offerQuantity} - Total: $${(config.offers.price * offerQuantity).toLocaleString()}`);
  closeDailyOfferModal();
}

function buyOfferNow() {
  const config = seasons[currentSeason];
  alert(`¡Comprando ${config.offers.title} ahora! Total: $${(config.offers.price * offerQuantity).toLocaleString()}`);
  closeDailyOfferModal();
}

// Funciones para cerrar modales
function closeDailyOfferModal() {
  const modal = document.getElementById('daily-offer-modal');
  if (modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }
}

function closeSeasonTipsModal() {
  const modal = document.getElementById('season-tips-modal');
  if (modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }
}

// NUEVA FUNCIÓN: Actualizar sección de preparación completa
function updatePreparationSection(config) {
  console.log('🔄 Actualizando sección de preparación...');
  
  try {
    const prepData = config.preparationData;
    
    // Actualizar gradiente de fondo de la sección
    const prepSection = document.getElementById('preparation-section');
    if (prepSection) {
      prepSection.className = `py-16 sm:py-20 lg:py-24 px-4 sm:px-6 lg:px-8 bg-gradient-to-br ${prepData.sectionGradient} text-white`;
    }
    
    // Actualizar banner y título
    const prepIconLeft = document.getElementById('prep-icon-left');
    const prepBannerText = document.getElementById('prep-banner-text');
    const prepIconRight = document.getElementById('prep-icon-right');
    const prepTitle = document.getElementById('preparation-title');
    const prepDescription = document.getElementById('preparation-description');
    
    if (prepIconLeft) prepIconLeft.textContent = config.icon;
    if (prepBannerText) prepBannerText.textContent = prepData.bannerText;
    if (prepIconRight) prepIconRight.textContent = config.icon;
    if (prepTitle) prepTitle.textContent = prepData.title;
    if (prepDescription) prepDescription.textContent = prepData.description;
    
    // Actualizar kit de productos
    updatePreparationKit(config);
    
    console.log('✅ Sección de preparación actualizada correctamente');
  } catch (error) {
    console.error('❌ Error actualizando sección de preparación:', error);
  }
}

// NUEVA FUNCIÓN: Actualizar kit de preparación
function updatePreparationKit(config) {
  console.log('🔄 Actualizando kit de preparación...');
  
  try {
    const kit = config.preparationData.kit;
    
    // Actualizar título del kit
    const kitTitle = document.getElementById('kit-title');
    if (kitTitle) {
      kitTitle.textContent = kit.title;
    }
    
    // Actualizar productos del kit
    const kitProducts = document.getElementById('kit-products');
    if (kitProducts) {
      kitProducts.innerHTML = kit.products.map(product => `
        <div class="flex items-center justify-between py-3 border-b border-white/20">
          <span class="text-orange-100">${product.name}</span>
          <span class="text-white font-bold">$${product.price.toLocaleString()}</span>
        </div>
      `).join('');
    }
    
    // Actualizar precios
    const kitIndividualPrice = document.getElementById('kit-individual-price');
    const kitFinalPrice = document.getElementById('kit-final-price');
    const kitSavings = document.getElementById('kit-savings');
    
    if (kitIndividualPrice) {
      kitIndividualPrice.textContent = `Precio individual: $${kit.individualPrice.toLocaleString()}`;
    }
    if (kitFinalPrice) {
      kitFinalPrice.textContent = `Kit Completo: $${kit.finalPrice.toLocaleString()}`;
    }
    if (kitSavings) {
      kitSavings.textContent = `¡Ahorrás $${kit.savings.toLocaleString()}!`;
    }
    
    // Actualizar botón del kit
    const kitButton = document.getElementById('kit-button');
    if (kitButton) {
      kitButton.className = `w-full bg-gradient-to-r ${config.gradient} hover:opacity-90 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105 shadow-xl`;
      
      // Actualizar atributos del botón
      kitButton.setAttribute('data-product-id', `kit-${currentSeason}`);
      kitButton.setAttribute('data-product-name', kit.title.replace('🛒 ', ''));
      kitButton.setAttribute('data-product-price', kit.finalPrice);
    }
    
    console.log('✅ Kit de preparación actualizado correctamente');
  } catch (error) {
    console.error('❌ Error actualizando kit de preparación:', error);
  }
}

// NUEVA FUNCIÓN: Actualizar calendario de actividades
function updateCalendar(config) {
  console.log('🔄 Actualizando calendario de actividades...');
  
  try {
    // Actualizar título del calendario
    const calendarTitle = document.getElementById('calendar-title');
    if (calendarTitle) {
      calendarTitle.textContent = `📅 Calendario de Actividades ${config.name === 'Otoño' ? 'Otoñales' : 
        config.name === 'Primavera' ? 'Primaverales' : 
        config.name === 'Verano' ? 'Estivales' : 'Invernales'}`;
    }
    
    // Datos específicos del calendario por temporada
    const calendarData = {
      primavera: [
        { month: 'Septiembre', activities: ['🌱 Siembra de primavera', '🌸 Primera floración', '💧 Aumento de riego'] },
        { month: 'Octubre', activities: ['🌺 Plantación de flores', '🌿 Podas de estimulación', '🌱 Fertilización abundante'] },
        { month: 'Noviembre', activities: ['🌻 Cosecha temprana', '🌸 Mantenimiento de flores', '🌿 Control de plagas'] }
      ],
      verano: [
        { month: 'Diciembre', activities: ['☀️ Protección solar', '💧 Riego intensivo', '🌵 Plantas resistentes'] },
        { month: 'Enero', activities: ['🌻 Cosecha principal', '☂️ Instalación de sombra', '💧 Sistemas de riego'] },
        { month: 'Febrero', activities: ['🌿 Mantenimiento mínimo', '🌵 Cuidado de suculentas', '☀️ Protección del calor'] }
      ],
      otono: [
        { month: 'Marzo', activities: ['🌱 Siembra de otoño', '🍂 Primera aplicación de abono', '💧 Ajuste de riego'] },
        { month: 'Abril', activities: ['✂️ Podas de formación', '🍂 Recolección de hojas', '🌿 Plantación de bulbos'] },
        { month: 'Mayo', activities: ['❄️ Protección contra heladas', '🌰 Última fertilización', '🏠 Preparación de invernaderos'] }
      ],
      invierno: [
        { month: 'Junio', activities: ['🏠 Traslado al interior', '❄️ Protección del frío', '💧 Riego reducido'] },
        { month: 'Julio', activities: ['🌱 Planificación futura', '❄️ Mantenimiento de protecciones', '📋 Diseño de jardín'] },
        { month: 'Agosto', activities: ['🌱 Preparación primaveral', '🛠️ Mantenimiento de herramientas', '📚 Estudio de variedades'] }
      ]
    };
    
    // Actualizar contenido del calendario
    const calendarMonths = document.getElementById('calendar-months');
    if (calendarMonths && calendarData[currentSeason]) {
      calendarMonths.innerHTML = calendarData[currentSeason].map(monthData => `
        <div class="bg-white/10 rounded-2xl p-6">
          <h4 class="text-xl font-bold text-yellow-300 mb-3">${monthData.month}</h4>
          <ul class="text-orange-100 text-sm space-y-2">
            ${monthData.activities.map(activity => `<li>${activity}</li>`).join('')}
          </ul>
        </div>
      `).join('');
    }
    
    console.log('✅ Calendario actualizado correctamente');
  } catch (error) {
    console.error('❌ Error actualizando calendario:', error);
  }
}

// NUEVA FUNCIÓN: Actualizar panel de información estacional
function updateSeasonInfoPanel(config) {
  console.log('🔄 Actualizando panel de información estacional...');
  
  try {
    // Actualizar badge del panel
    const seasonInfoBadge = document.getElementById('season-info-badge');
    const seasonInfoIcon = document.getElementById('season-info-icon');
    const seasonInfoText = document.getElementById('season-info-text');
    
    if (seasonInfoBadge) {
      seasonInfoBadge.className = `inline-flex items-center gap-2 bg-gradient-to-r ${config.gradient} text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg`;
    }
    if (seasonInfoIcon) seasonInfoIcon.textContent = config.icon;
    if (seasonInfoText) seasonInfoText.textContent = `Consejos para ${config.name}`;
    
    // Actualizar grid de consejos
    const seasonTipsGrid = document.getElementById('season-tips-grid');
    if (seasonTipsGrid && config.tips) {
      const halfLength = Math.ceil(config.tips.length / 2);
      const firstHalf = config.tips.slice(0, halfLength);
      const secondHalf = config.tips.slice(halfLength);
      
      seasonTipsGrid.innerHTML = `
        <div class="space-y-3">
          ${firstHalf.map(tip => `
            <div class="flex items-start gap-3">
              <span class="text-orange-400 mt-1">•</span>
              <div>
                <h5 class="font-semibold mb-1 capitalize">${tip.title}</h5>
                <p class="text-sm text-white/80">${tip.desc}</p>
              </div>
            </div>
          `).join('')}
        </div>
        <div class="space-y-3">
          ${secondHalf.map(tip => `
            <div class="flex items-start gap-3">
              <span class="text-orange-400 mt-1">•</span>
              <div>
                <h5 class="font-semibold mb-1 capitalize">${tip.title}</h5>
                <p class="text-sm text-white/80">${tip.desc}</p>
              </div>
            </div>
          `).join('')}
        </div>
      `;
    }
    
    // Actualizar botón de consejos
    const seasonTipsButton = document.getElementById('season-tips-button');
    if (seasonTipsButton) {
      seasonTipsButton.className = `bg-gradient-to-r ${config.gradient} hover:opacity-90 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg`;
      seasonTipsButton.innerHTML = `${config.icon} Ver más consejos para ${config.name}`;
    }
    
    console.log('✅ Panel de información estacional actualizado');
  } catch (error) {
    console.error('❌ Error actualizando panel de información estacional:', error);
  }
}

// NUEVA FUNCIÓN: Actualizar botones de acción principales
function updateActionButtons(config) {
  console.log('🔄 Actualizando botones de acción...');
  
  try {
    // Botón "Ver Plantas de [Temporada]"
    const actionButton1 = document.getElementById('action-button-1');
    if (actionButton1) {
      actionButton1.className = `btn-clean btn-primary smooth-transition flex items-center gap-3 bg-gradient-to-r ${config.gradient} hover:opacity-90 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg`;
      actionButton1.innerHTML = `
        <span>${config.icon}</span>
        <span class="font-display font-semibold">Ver Plantas de ${config.name}</span>
        <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
        </svg>
      `;
    }
    
    console.log('✅ Botones de acción actualizados');
  } catch (error) {
    console.error('❌ Error actualizando botones de acción:', error);
  }
}

// Inicialización completa
document.addEventListener('DOMContentLoaded', function() {
  console.log('🍂 DOM cargado, inicializando sistema estacional completo...');
  
  // Detectar temporada actual
  const currentMonth = new Date().getMonth() + 1;
  let detectedSeason = 'otono';
  
  if (currentMonth >= 9 && currentMonth <= 11) detectedSeason = 'primavera';
  else if (currentMonth >= 12 || currentMonth <= 2) detectedSeason = 'verano';
  else if (currentMonth >= 3 && currentMonth <= 5) detectedSeason = 'otono';
  else if (currentMonth >= 6 && currentMonth <= 8) detectedSeason = 'invierno';
  
  console.log(`🎯 Temporada detectada: ${detectedSeason}`);
  changeSeason(detectedSeason);
  
  // Event listeners
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeDailyOfferModal();
      closeSeasonTipsModal();
    }
  });
  
  // Event listeners para cerrar modales clickeando fuera
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('bg-black')) {
      closeDailyOfferModal();
      closeSeasonTipsModal();
    }
  });
  
  console.log('✅ Sistema estacional completo inicializado correctamente');
});

// Exportar funciones globalmente
window.changeSeason = changeSeason;
window.openDailyOfferPopup = openDailyOfferPopup;
window.closeDailyOfferModal = closeDailyOfferModal;
window.openSeasonTipsPopup = openSeasonTipsPopup;
window.closeSeasonTipsModal = closeSeasonTipsModal;
window.increaseOfferQuantity = increaseOfferQuantity;
window.decreaseOfferQuantity = decreaseOfferQuantity;
window.addOfferToCart = addOfferToCart;
window.buyOfferNow = buyOfferNow;

console.log('🌟 Los Cocos - Sistema Estacional Integrado v6.1 cargado completamente');