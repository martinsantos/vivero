// Funcionalidades Otoñales Mejoradas - Los Cocos
// Detección automática de temporada y funciones interactivas
// Versión 2.0 - Funciones consolidadas y optimizadas

// Variables globales
let currentSeason = 'otono';
let offerQuantity = 1;
let offerCountdown;

// Inicialización cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('🍂 Sistema de temporadas inicializado');
    
    detectCurrentSeason();
    console.log(`Temporada detectada: ${currentSeason}`);
    
    // Actualizar todo el contenido dinámico
    updateSeasonDisplay();
    updateBackgroundAndParticles();
    updateHeroContent();
    updateFeaturesContent();
    updateActionButtons();
    updateOfferContent();
    updateSeasonProducts();
    updateSeasonButtons();
    updatePreparationGuide();
    updateSpeciesSection();  // Nueva función para actualizar la sección de especies
    updateSeasonBanner();    // Nueva función para actualizar el banner de ofertas
    updateSeasonTimeline();  // Nueva función para actualizar la línea de tiempo
    
    startOfferCountdown();
    loadSeasonTips();
    
    console.log('✅ Todo el contenido dinámico cargado');
});

// Detectar temporada actual basada en la fecha (Hemisferio Sur - Argentina)
function detectCurrentSeason() {
    const now = new Date();
    const month = now.getMonth() + 1; // getMonth() devuelve 0-11
    
    if (month >= 3 && month <= 5) {
        currentSeason = 'otono'; // Marzo-Mayo (otoño en hemisferio sur)
    } else if (month >= 6 && month <= 8) {
        currentSeason = 'invierno'; // Junio-Agosto
    } else if (month >= 9 && month <= 11) {
        currentSeason = 'primavera'; // Septiembre-Noviembre
    } else {
        currentSeason = 'verano'; // Diciembre-Febrero
    }
    
    console.log(`Mes actual: ${month}, Temporada detectada: ${currentSeason}`);
}

// Datos completos de temporadas con todo el contenido dinámico
const seasonData = {
    'primavera': {
        name: 'Primavera 2024',
        icon: '🌸',
        icon2: '🌿',
        tipIcon: '🌸',
        tipIcon2: '🌿',
        tipTitle: 'Consejos para Primavera',
        tipPreview: 'Es tiempo de siembra y floración. Prepara el suelo, aumenta el riego y disfruta del renacimiento de tu jardín.',
        background: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80',
        overlay: 'from-green-900/80 via-emerald-800/70 to-teal-900/80',
        indicatorGradient: 'from-green-500/90 to-emerald-500/90',
        particles: ['🌸', '🌿', '🌱', '🌸', '🌿'],
        
        // Contenido del Hero
        heroTitle: 'Los Cocos',
        heroGradient: 'from-green-300 via-emerald-200 to-teal-300',
        heroSubtitle: 'refugio primaveral',
        heroDescription: 'Floración y renovación • Plantas de temporada • Asesoramiento especializado',
        heroDescriptionColor: 'text-green-200',
        
        // Oferta especial
        offerTitle: 'OFERTA ESPECIAL DE PRIMAVERA',
        offerText: '¡Pack Primaveral 40% OFF!',
        offerIcon: '🌸',
        offerGradient: 'from-green-600 via-emerald-500 to-green-600 hover:from-green-700 hover:via-emerald-600 hover:to-green-700',
        
        // Características principales
        features: [
            { icon: '🌸', title: 'Plantas de Primavera', description: 'Tulipanes, petunias y flores que despiertan con el sol primaveral' },
            { icon: '🌱', title: 'Siembra y Plantación', description: 'Semillas, plantines y todo para comenzar tu jardín' },
            { icon: '🌿', title: 'Consejos Expertos', description: 'Guías para aprovechar al máximo la temporada de crecimiento' }
        ],
        
        // Botones de acción
        actionButtons: [
            { icon: '🌸', text: 'Ver Plantas de Primavera', gradient: 'from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700' },
            { icon: '🌱', text: 'Guía de Siembra', gradient: 'from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700' }
        ],
        
        // Productos destacados
        products: [
            {
                id: 'tulipanes',
                name: 'Tulipanes Premium',
                description: 'Bulbos selectos en múltiples colores. Los heraldos de la primavera que llenan de alegría cualquier jardín.',
                price: 7200,
                image: 'https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                badge: '🌸 ESTRELLA',
                badgeColor: 'text-green-600',
                gradient: 'from-pink-400 to-rose-500',
                buttonGradient: 'from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600'
            },
            {
                id: 'petunias',
                name: 'Petunias Multicolor',
                description: 'Flores abundantes y coloridas que florecen toda la temporada. Perfectas para macetas y borduras.',
                price: 5800,
                image: 'https://images.unsplash.com/photo-1597848212624-e8932badd1db?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                badge: '🌈 POPULAR',
                badgeColor: 'text-purple-600',
                gradient: 'from-purple-400 to-pink-500',
                buttonGradient: 'from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600'
            },
            {
                id: 'primulas',
                name: 'Primulas Elegantes',
                description: 'Las primeras flores de la temporada. Delicadas y resistentes, ideales para comenzar el jardín.',
                price: 4900,
                image: 'https://images.unsplash.com/photo-1582794543139-8ac9cb0d2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                badge: '✨ DELICADA',
                badgeColor: 'text-yellow-600',
                gradient: 'from-yellow-400 to-orange-500',
                buttonGradient: 'from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600'
            }
        ],
        
        // Productos adicionales (tierra, utensilios, macetas)
        additionalProducts: [
            {
                id: 'tierra-primavera',
                name: 'Tierra Preparada Primaveral',
                description: 'Sustrato enriquecido con nutrientes para siembra y trasplante',
                price: 3200,
                image: 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            },
            {
                id: 'macetas-ceramica',
                name: 'Macetas de Cerámica',
                description: 'Macetas decorativas ideales para plantas de interior y exterior',
                price: 2800,
                image: 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            },
            {
                id: 'kit-siembra',
                name: 'Kit de Siembra Completo',
                description: 'Herramientas básicas para comenzar tu jardín primaveral',
                price: 4500,
                image: 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            }
        ],

        // Guía de preparación de jardín
        preparationGuide: {
            title: 'Prepara tu Jardín para la Primavera',
            subtitle: 'Sigue nuestra guía paso a paso para que tu jardín florezca espectacularmente durante la primavera mendocina.',
            backgroundGradient: 'from-green-900 via-emerald-800 to-teal-900',
            steps: [
                {
                    number: 1,
                    icon: '🌱',
                    title: 'Prepara la Tierra',
                    description: 'Remueve la tierra, agrega compost fresco y prepara los canteros para la nueva temporada de siembra.',
                    details: '✓ Compost fresco • ✓ Fertilizante de primavera • ✓ Tierra nueva',
                    gradient: 'from-green-400 to-emerald-500'
                },
                {
                    number: 2,
                    icon: '🌸',
                    title: 'Planta Flores de Temporada',
                    description: 'Es el momento perfecto para plantar bulbos y flores que florecerán durante toda la primavera.',
                    details: '✓ Bulbos de primavera • ✓ Plantines de flores • ✓ Semillas selectas',
                    gradient: 'from-pink-400 to-rose-500'
                },
                {
                    number: 3,
                    icon: '💧',
                    title: 'Ajusta el Riego',
                    description: 'Aumenta gradualmente la frecuencia de riego conforme las plantas despiertan del letargo invernal.',
                    details: '✓ Sistema de riego • ✓ Programadores • ✓ Aspersores',
                    gradient: 'from-blue-400 to-cyan-500'
                },
                {
                    number: 4,
                    icon: '✂️',
                    title: 'Poda de Renovación',
                    description: 'Realiza podas de limpieza y formación para estimular el crecimiento nuevo y vigoroso.',
                    details: '✓ Tijeras de podar • ✓ Sellador de cortes • ✓ Desinfectante',
                    gradient: 'from-purple-400 to-pink-500'
                }
            ],
            kitInfo: {
                title: 'Kit Preparación Primavera',
                originalPrice: 16800,
                finalPrice: 12800,
                savings: 4000,
                items: [
                    { name: 'Compost Orgánico Premium (20kg)', price: 4200 },
                    { name: 'Fertilizante Primaveral NPK', price: 3800 },
                    { name: 'Sistema de Riego Básico', price: 5200 },
                    { name: 'Kit de Herramientas de Jardín', price: 3600 }
                ]
            },
            calendar: [
                { month: 'Septiembre', activities: ['🌱 Siembra de primavera', '🌸 Plantación de bulbos', '💧 Aumento de riego'] },
                { month: 'Octubre', activities: ['✂️ Podas de formación', '🌿 Fertilización general', '🦋 Control de plagas'] },
                { month: 'Noviembre', activities: ['🌺 Trasplantes', '🌸 Segunda siembra', '🏠 Preparación de invernaderos'] }
            ]
        }
    },
    
    'verano': {
        name: 'Verano 2024',
        icon: '☀️',
        icon2: '🌻',
        tipIcon: '☀️',
        tipIcon2: '🌻',
        tipTitle: 'Consejos para Verano',
        tipPreview: 'Mantén la hidratación constante, protege del sol intenso y aprovecha el crecimiento vigoroso de las plantas.',
        background: 'https://images.unsplash.com/photo-1574263867128-a3d5c1b1deac?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80',
        overlay: 'from-yellow-900/80 via-orange-800/70 to-red-900/80',
        indicatorGradient: 'from-yellow-500/90 to-orange-500/90',
        particles: ['☀️', '🌻', '🌿', '☀️', '🌻'],
        
        // Contenido del Hero
        heroTitle: 'Los Cocos',
        heroGradient: 'from-yellow-300 via-orange-200 to-red-300',
        heroSubtitle: 'oasis estival',
        heroDescription: 'Resistencia y color • Plantas tropicales • Asesoramiento especializado',
        heroDescriptionColor: 'text-yellow-200',
        
        // Oferta especial
        offerTitle: 'OFERTA ESPECIAL DE VERANO',
        offerText: '¡Pack Tropical 45% OFF!',
        offerIcon: '☀️',
        offerGradient: 'from-yellow-600 via-orange-500 to-red-600 hover:from-yellow-700 hover:via-orange-600 hover:to-red-700',
        
        // Características principales
        features: [
            { icon: '☀️', title: 'Plantas de Verano', description: 'Girasoles, lavanda y especies que aman el calor intenso' },
            { icon: '💧', title: 'Sistemas de Riego', description: 'Soluciones para mantener tu jardín hidratado en el calor' },
            { icon: '🌻', title: 'Consejos Expertos', description: 'Guías para cuidar tu jardín durante el verano mendocino' }
        ],
        
        // Botones de acción
        actionButtons: [
            { icon: '☀️', text: 'Ver Plantas de Verano', gradient: 'from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700' },
            { icon: '💧', text: 'Sistemas de Riego', gradient: 'from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700' }
        ],
        
        // Productos destacados
        products: [
            {
                id: 'girasoles',
                name: 'Girasoles Gigantes',
                description: 'Flores espectaculares que siguen al sol. Perfectos para crear puntos focales en el jardín.',
                price: 6800,
                image: 'https://images.unsplash.com/photo-1572441710263-6f5b32fce6fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                badge: '☀️ RADIANTE',
                badgeColor: 'text-yellow-600',
                gradient: 'from-yellow-400 to-orange-500',
                buttonGradient: 'from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600'
            },
            {
                id: 'lavanda',
                name: 'Lavanda Aromática',
                description: 'Resistente al calor y de aroma relajante. Ideal para borduras y jardines de hierbas.',
                price: 8900,
                image: 'https://images.unsplash.com/photo-1611909023032-2d6b3134ecba?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                badge: '🌿 AROMÁTICA',
                badgeColor: 'text-purple-600',
                gradient: 'from-purple-400 to-blue-500',
                buttonGradient: 'from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600'
            },
            {
                id: 'geranios',
                name: 'Geranios Resistentes',
                description: 'Flores coloridas que soportan el calor extremo. Perfectos para macetas y balcones.',
                price: 5200,
                image: 'https://images.unsplash.com/photo-1595121290016-7f0e13c7b7b8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                badge: '🔥 RESISTENTE',
                badgeColor: 'text-red-600',
                gradient: 'from-red-400 to-pink-500',
                buttonGradient: 'from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600'
            }
        ],
        
        // Productos adicionales
        additionalProducts: [
            {
                id: 'tierra-cactus',
                name: 'Sustrato para Cactus',
                description: 'Tierra especial con drenaje perfecto para plantas suculentas',
                price: 2900,
                image: 'https://images.unsplash.com/photo-1509423350716-97f2360af2e4?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            },
            {
                id: 'macetas-terracota',
                name: 'Macetas de Terracota',
                description: 'Macetas transpirables ideales para el calor del verano',
                price: 2200,
                image: 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            },
            {
                id: 'sistema-riego',
                name: 'Sistema de Riego por Goteo',
                description: 'Mantén tus plantas hidratadas automáticamente',
                price: 7800,
                image: 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            }
        ],

        // Guía de preparación de jardín
        preparationGuide: {
            title: 'Prepara tu Jardín para el Verano',
            subtitle: 'Sigue nuestra guía paso a paso para que tu jardín resista el calor intenso del verano mendocino.',
            backgroundGradient: 'from-yellow-900 via-orange-800 to-red-900',
            steps: [
                {
                    number: 1,
                    icon: '💧',
                    title: 'Instala Riego Eficiente',
                    description: 'Implementa sistemas de riego por goteo y mulch para conservar la humedad durante el calor extremo.',
                    details: '✓ Sistema por goteo • ✓ Mulch orgánico • ✓ Programadores de riego',
                    gradient: 'from-blue-400 to-cyan-500'
                },
                {
                    number: 2,
                    icon: '☀️',
                    title: 'Protege del Sol Intenso',
                    description: 'Instala mallas de sombra y protege las plantas más sensibles del sol directo del mediodía.',
                    details: '✓ Mallas de sombra • ✓ Parasoles • ✓ Estructuras de protección',
                    gradient: 'from-yellow-400 to-orange-500'
                },
                {
                    number: 3,
                    icon: '🌱',
                    title: 'Fertiliza para Resistencia',
                    description: 'Aplica fertilizantes que fortalezcan las plantas contra el estrés térmico y la sequía.',
                    details: '✓ Fertilizante anti-estrés • ✓ Potasio extra • ✓ Aminoácidos',
                    gradient: 'from-green-400 to-emerald-500'
                },
                {
                    number: 4,
                    icon: '🌿',
                    title: 'Mantén la Humedad',
                    description: 'Crea microclimas húmedos y agrupa plantas con necesidades similares de agua.',
                    details: '✓ Agrupación por necesidades • ✓ Microaspersores • ✓ Recipientes de agua',
                    gradient: 'from-teal-400 to-green-500'
                }
            ],
            kitInfo: {
                title: 'Kit Preparación Verano',
                originalPrice: 19500,
                finalPrice: 14500,
                savings: 5000,
                items: [
                    { name: 'Sistema de Riego por Goteo Completo', price: 8200 },
                    { name: 'Malla de Sombra 70% (10m²)', price: 4800 },
                    { name: 'Fertilizante Anti-Estrés', price: 3200 },
                    { name: 'Mulch Orgánico Premium (50L)', price: 3300 }
                ]
            },
            calendar: [
                { month: 'Diciembre', activities: ['💧 Instalación de riego', '☀️ Colocación de sombras', '🌱 Fertilización preventiva'] },
                { month: 'Enero', activities: ['🌿 Mantenimiento de humedad', '✂️ Podas ligeras', '🦗 Control de plagas'] },
                { month: 'Febrero', activities: ['💧 Riego intensivo', '🌱 Fertilización de apoyo', '🏠 Preparación para otoño'] }
            ]
        }
    },
    
    'otono': {
        name: 'Otoño 2024',
        icon: '🍂',
        icon2: '🍁',
        tipIcon: '🍂',
        tipIcon2: '🍁',
        tipTitle: 'Consejos para Otoño',
        tipPreview: 'Es momento de preparar tu jardín para el invierno. Abona la tierra, protege las plantas sensibles y planifica las podas.',
        background: 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80',
        overlay: 'from-orange-900/85 via-red-800/75 to-amber-900/85',
        indicatorGradient: 'from-orange-500/90 to-red-500/90',
        particles: ['🍂', '🍁', '🍃', '🍂', '🍁'],
        
        // Contenido del Hero
        heroTitle: 'Los Cocos',
        heroGradient: 'from-orange-300 via-amber-200 to-red-300',
        heroSubtitle: 'refugio otoñal',
        heroDescription: 'Prepara tu jardín para el otoño • Plantas de temporada • Asesoramiento especializado',
        heroDescriptionColor: 'text-amber-200',
        
        // Oferta especial
        offerTitle: 'OFERTA ESPECIAL DE OTOÑO',
        offerText: '¡Pack Otoñal 50% OFF!',
        offerIcon: '🍂',
        offerGradient: 'from-red-600 via-orange-500 to-red-600 hover:from-red-700 hover:via-orange-600 hover:to-red-700',
        
        // Características principales
        features: [
            { icon: '🍂', title: 'Plantas de Otoño', description: 'Crisantemos, asters y plantas que brillan en esta temporada dorada' },
            { icon: '🌰', title: 'Preparación Invernal', description: 'Abonos, mulch y todo lo necesario para proteger tu jardín' },
            { icon: '🍁', title: 'Consejos Expertos', description: 'Guías personalizadas para cada etapa del otoño mendocino' }
        ],
        
        // Botones de acción
        actionButtons: [
            { icon: '🍂', text: 'Ver Plantas de Otoño', gradient: 'from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700' },
            { icon: '🌰', text: 'Guía de Preparación', gradient: 'from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700' }
        ],
        
        // Productos destacados
        products: [
            {
                id: 'crisantemos',
                name: 'Crisantemos Premium',
                description: 'Los reyes del otoño. Flores abundantes en tonos dorados, rojos y amarillos que iluminan cualquier espacio.',
                price: 8500,
                image: 'https://images.unsplash.com/photo-1603048297172-c92544798d5a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                badge: '🏆 ESTRELLA',
                badgeColor: 'text-orange-600',
                gradient: 'from-yellow-400 to-orange-500',
                buttonGradient: 'from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600'
            },
            {
                id: 'asters',
                name: 'Asters Multicolor',
                description: 'Pequeñas estrellas de colores que florecen hasta las primeras heladas. Perfectos para borduras y macetas.',
                price: 6200,
                image: 'https://images.unsplash.com/photo-1592150621744-aca64f48394a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                badge: '💜 POPULAR',
                badgeColor: 'text-purple-600',
                gradient: 'from-purple-400 to-pink-500',
                buttonGradient: 'from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600'
            },
            {
                id: 'cyclamen',
                name: 'Cyclamen Persicum',
                description: 'Flores delicadas en forma de mariposa. Ideales para interiores y exteriores protegidos durante el otoño.',
                price: 7800,
                image: 'https://images.unsplash.com/photo-1582794543139-8ac9cb0d2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                badge: '🌸 ELEGANTE',
                badgeColor: 'text-pink-600',
                gradient: 'from-pink-400 to-rose-500',
                buttonGradient: 'from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600'
            }
        ],
        
        // Productos adicionales
        additionalProducts: [
            {
                id: 'compost-organico',
                name: 'Compost Orgánico Premium',
                description: 'Abono natural para preparar la tierra antes del invierno',
                price: 4500,
                image: 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            },
            {
                id: 'mantas-termicas',
                name: 'Mantas Térmicas',
                description: 'Protección contra las primeras heladas',
                price: 5100,
                image: 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            },
            {
                id: 'herramientas-poda',
                name: 'Kit de Herramientas de Poda',
                description: 'Tijeras y herramientas para preparar las plantas',
                price: 6800,
                image: 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            }
        ],

        // Guía de preparación de jardín
        preparationGuide: {
            title: 'Prepara tu Jardín para el Otoño',
            subtitle: 'Sigue nuestra guía paso a paso para que tu jardín luzca espectacular durante el otoño y esté preparado para el invierno mendocino.',
            backgroundGradient: 'from-amber-900 via-orange-800 to-red-900',
            steps: [
                {
                    number: 1,
                    icon: '🌱',
                    title: 'Abona la Tierra',
                    description: 'Aplica compost orgánico y abono de liberación lenta para nutrir el suelo antes del invierno. Es el momento perfecto para preparar la tierra para la próxima temporada.',
                    details: '✓ Compost orgánico • ✓ Abono NPK • ✓ Humus de lombriz',
                    gradient: 'from-yellow-400 to-orange-500'
                },
                {
                    number: 2,
                    icon: '🍂',
                    title: 'Rota las Forrajeras',
                    description: 'Cambia la ubicación de tus cultivos forrajeros para evitar el agotamiento del suelo. Planta leguminosas que fijen nitrógeno naturalmente.',
                    details: '✓ Rotación de cultivos • ✓ Leguminosas • ✓ Descanso del suelo',
                    gradient: 'from-orange-400 to-red-500'
                },
                {
                    number: 3,
                    icon: '💧',
                    title: 'Rocía las Plantas',
                    description: 'Aplica tratamientos preventivos contra plagas y hongos. El otoño es ideal para fortalecer las defensas naturales de tus plantas.',
                    details: '✓ Fungicidas naturales • ✓ Aceite de neem • ✓ Extractos vegetales',
                    gradient: 'from-red-400 to-pink-500'
                },
                {
                    number: 4,
                    icon: '❄️',
                    title: 'Protege del Frío',
                    description: 'Cubre las plantas sensibles con mantas térmicas o mulch. Prepara estructuras de protección para las especies más delicadas.',
                    details: '✓ Mantas térmicas • ✓ Mulch orgánico • ✓ Invernaderos mini',
                    gradient: 'from-purple-400 to-blue-500'
                }
            ],
            kitInfo: {
                title: 'Kit Preparación Otoño',
                originalPrice: 18500,
                finalPrice: 14500,
                savings: 4000,
                items: [
                    { name: 'Compost Orgánico Premium (20kg)', price: 4500 },
                    { name: 'Abono NPK Liberación Lenta', price: 3200 },
                    { name: 'Aceite de Neem Concentrado', price: 2800 },
                    { name: 'Manta Térmica Jardín (5m²)', price: 5100 },
                    { name: 'Mulch Corteza Pino (50L)', price: 2900 }
                ]
            },
            calendar: [
                { month: 'Marzo', activities: ['🌱 Siembra de otoño', '🍂 Primera aplicación de abono', '💧 Ajuste de riego'] },
                { month: 'Abril', activities: ['✂️ Podas de formación', '🍂 Recolección de hojas', '🌿 Plantación de bulbos'] },
                { month: 'Mayo', activities: ['❄️ Protección contra heladas', '🌰 Última fertilización', '🏠 Preparación de invernaderos'] }
            ]
        }
    },
    
    'invierno': {
        name: 'Invierno 2024',
        icon: '❄️',
        icon2: '🌲',
        tipIcon: '❄️',
        tipIcon2: '🌲',
        tipTitle: 'Consejos para Invierno',
        tipPreview: 'Protege las plantas del frío, reduce el riego y planifica las mejoras para la próxima temporada.',
        background: 'https://images.unsplash.com/photo-1606041008023-472dfb5e530f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80',
        overlay: 'from-blue-900/80 via-slate-800/70 to-gray-900/80',
        indicatorGradient: 'from-blue-500/90 to-slate-500/90',
        particles: ['❄️', '🌲', '🍃', '❄️', '🌲'],
        
        // Contenido del Hero
        heroTitle: 'Los Cocos',
        heroGradient: 'from-blue-300 via-slate-200 to-gray-300',
        heroSubtitle: 'refugio invernal',
        heroDescription: 'Resistencia al frío • Plantas de interior • Asesoramiento especializado',
        heroDescriptionColor: 'text-blue-200',
        
        // Oferta especial
        offerTitle: 'OFERTA ESPECIAL DE INVIERNO',
        offerText: '¡Pack Invernal 35% OFF!',
        offerIcon: '❄️',
        offerGradient: 'from-blue-600 via-slate-500 to-blue-600 hover:from-blue-700 hover:via-slate-600 hover:to-blue-700',
        
        // Características principales
        features: [
            { icon: '❄️', title: 'Plantas de Invierno', description: 'Especies resistentes al frío y plantas de interior perfectas' },
            { icon: '🏠', title: 'Jardín Interior', description: 'Transforma tu hogar en un oasis verde durante el invierno' },
            { icon: '🌲', title: 'Consejos Expertos', description: 'Guías para mantener tu jardín vivo durante el frío mendocino' }
        ],
        
        // Botones de acción
        actionButtons: [
            { icon: '❄️', text: 'Ver Plantas de Invierno', gradient: 'from-blue-600 to-slate-600 hover:from-blue-700 hover:to-slate-700' },
            { icon: '🏠', text: 'Jardín Interior', gradient: 'from-slate-600 to-gray-600 hover:from-slate-700 hover:to-gray-700' }
        ],
        
        // Productos destacados
        products: [
            {
                id: 'poinsettia',
                name: 'Poinsettia Navideña',
                description: 'La estrella del invierno. Perfecta para decorar durante las fiestas con sus hojas rojas vibrantes.',
                price: 7000,
                image: 'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                badge: '🎄 FESTIVA',
                badgeColor: 'text-red-600',
                gradient: 'from-red-400 to-green-500',
                buttonGradient: 'from-red-500 to-green-500 hover:from-red-600 hover:to-green-600'
            },
            {
                id: 'ciclamen-invierno',
                name: 'Ciclamen Invernal',
                description: 'Flores delicadas que resisten el frío. Perfectas para dar color durante los meses grises.',
                price: 5500,
                image: 'https://images.unsplash.com/photo-1518020382113-a7e8fc38eac9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                badge: '❄️ RESISTENTE',
                badgeColor: 'text-blue-600',
                gradient: 'from-blue-400 to-purple-500',
                buttonGradient: 'from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600'
            },
            {
                id: 'hiedra',
                name: 'Hiedra Clásica',
                description: 'Planta trepadora perfecta para interiores. Purifica el aire y es muy fácil de cuidar.',
                price: 3000,
                image: 'https://images.unsplash.com/photo-1560615304-5caf078d1b92?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                badge: '🌿 PURIFICADORA',
                badgeColor: 'text-green-600',
                gradient: 'from-green-400 to-teal-500',
                buttonGradient: 'from-green-500 to-teal-500 hover:from-green-600 hover:to-teal-600'
            }
        ],
        
        // Productos adicionales
        additionalProducts: [
            {
                id: 'tierra-interior',
                name: 'Sustrato para Plantas de Interior',
                description: 'Tierra especial para plantas que viven en macetas',
                price: 2800,
                image: 'https://images.unsplash.com/photo-1509423350716-97f2360af2e4?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            },
            {
                id: 'macetas-ceramica-invierno',
                name: 'Macetas de Cerámica Decorativas',
                description: 'Macetas elegantes para el hogar durante el invierno',
                price: 3500,
                image: 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            },
            {
                id: 'lampara-crecimiento',
                name: 'Lámpara de Crecimiento LED',
                description: 'Luz artificial para plantas de interior en invierno',
                price: 8900,
                image: 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            }
        ],

        // Guía de preparación de jardín
        preparationGuide: {
            title: 'Prepara tu Jardín para el Invierno',
            subtitle: 'Sigue nuestra guía paso a paso para proteger tu jardín durante el frío invierno mendocino y mantener plantas hermosas en interior.',
            backgroundGradient: 'from-blue-900 via-slate-800 to-gray-900',
            steps: [
                {
                    number: 1,
                    icon: '🏠',
                    title: 'Traslada Plantas al Interior',
                    description: 'Mueve las plantas más sensibles al frío hacia el interior o invernaderos para protegerlas de las heladas.',
                    details: '✓ Plantas tropicales • ✓ Suculentas delicadas • ✓ Plantas en macetas',
                    gradient: 'from-blue-400 to-indigo-500'
                },
                {
                    number: 2,
                    icon: '💡',
                    title: 'Instala Iluminación Artificial',
                    description: 'Compensa la falta de luz solar con lámparas LED especiales para plantas de interior.',
                    details: '✓ Lámparas LED grow • ✓ Temporizadores • ✓ Reflectores',
                    gradient: 'from-yellow-400 to-amber-500'
                },
                {
                    number: 3,
                    icon: '💧',
                    title: 'Reduce el Riego',
                    description: 'Disminuye la frecuencia de riego ya que las plantas entran en período de dormancia durante el invierno.',
                    details: '✓ Riego espaciado • ✓ Control de humedad • ✓ Drenaje adecuado',
                    gradient: 'from-cyan-400 to-blue-500'
                },
                {
                    number: 4,
                    icon: '🌿',
                    title: 'Mantén la Humedad Interior',
                    description: 'Crea ambientes húmedos para las plantas de interior usando bandejas con agua y agrupando plantas.',
                    details: '✓ Bandejas de humedad • ✓ Humidificadores • ✓ Agrupación de plantas',
                    gradient: 'from-green-400 to-teal-500'
                }
            ],
            kitInfo: {
                title: 'Kit Preparación Invierno',
                originalPrice: 17200,
                finalPrice: 13200,
                savings: 4000,
                items: [
                    { name: 'Lámpara LED de Crecimiento', price: 8900 },
                    { name: 'Sustrato para Interior Premium', price: 2800 },
                    { name: 'Humidificador Ultrasónico', price: 3200 },
                    { name: 'Kit de Macetas Decorativas', price: 2300 }
                ]
            },
            calendar: [
                { month: 'Junio', activities: ['🏠 Traslado al interior', '💡 Instalación de luces', '💧 Reducción de riego'] },
                { month: 'Julio', activities: ['🌿 Control de humedad', '✂️ Podas de mantenimiento', '🔍 Monitoreo de plagas'] },
                { month: 'Agosto', activities: ['💡 Ajuste de iluminación', '🌱 Preparación para primavera', '🏠 Planificación de cambios'] }
            ]
        }
    }
};

// Actualizar display de temporada en el hero
function updateSeasonDisplay() {
    const data = seasonData[currentSeason];
    
    // Actualizar elementos del DOM
    const currentSeasonEl = document.getElementById('current-season');
    if (currentSeasonEl) currentSeasonEl.textContent = data.name;
    
    const seasonIconLeft = document.getElementById('season-icon-left');
    if (seasonIconLeft) seasonIconLeft.textContent = data.icon;
    
    const seasonIconRight = document.getElementById('season-icon-right');
    if (seasonIconRight) seasonIconRight.textContent = data.icon2;
    
    const seasonTipIcon = document.getElementById('season-tip-icon');
    if (seasonTipIcon) seasonTipIcon.textContent = data.tipIcon;
    
    const seasonTipIcon2 = document.getElementById('season-tip-icon-2');
    if (seasonTipIcon2) seasonTipIcon2.textContent = data.tipIcon2;
    
    const seasonTipPreview = document.getElementById('season-tip-preview');
    if (seasonTipPreview) seasonTipPreview.textContent = data.tipPreview;
    
    // Actualizar indicador de temporada
    const seasonIndicator = document.getElementById('season-indicator');
    if (seasonIndicator) {
        seasonIndicator.className = `inline-flex items-center gap-3 bg-gradient-to-r ${data.indicatorGradient} backdrop-blur-md text-white px-8 py-4 rounded-2xl shadow-2xl animate-pulse-glow transition-all duration-500`;
    }
    
    // Actualizar botones activos
    updateSeasonButtons();
}

// Función para cambiar manualmente la temporada (testing)
window.changeSeason = function(season) {
    console.log(`🔄 Cambiando temporada manualmente a: ${season}`);
    currentSeason = season;
    
    // Ejecutar todas las actualizaciones en orden
    updateSeasonDisplay();
    updateBackgroundAndParticles();
    updateHeroContent();
    updateFeaturesContent();
    updateActionButtons();
    updateOfferContent();
    updateSpeciesSection();
    updateSeasonBanner();
    updateSeasonProducts();
    updateSeasonButtons();
    updatePreparationGuide();
    updateSeasonTimeline();
    loadSeasonTips();
    
    console.log(`✅ Temporada cambiada a ${season} - Todo actualizado`);
};

// Globalizar todas las funciones necesarias
window.openDailyOfferPopup = function() {
    console.log('🔥 Abriendo modal de oferta del día');
    const modal = document.getElementById('daily-offer-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        updateOfferDisplay();
        startOfferCountdown();
    }
};

window.closeDailyOfferModal = function() {
    console.log('❌ Cerrando modal de oferta del día');
    const modal = document.getElementById('daily-offer-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
};

window.openSeasonTipsPopup = function() {
    console.log('💡 Abriendo modal de consejos de temporada');
    const modal = document.getElementById('season-tips-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        loadDetailedSeasonTips();
    }
};

window.closeSeasonTipsModal = function() {
    console.log('❌ Cerrando modal de consejos de temporada');
    const modal = document.getElementById('season-tips-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
};

window.submitSubscription = function(event) {
    if (event) event.preventDefault();
    
    console.log('📝 Procesando suscripción premium');
    
    const name = document.getElementById('sub-name')?.value;
    const email = document.getElementById('sub-email')?.value;
    const phone = document.getElementById('sub-phone')?.value;
    const address = document.getElementById('sub-address')?.value;
    const terms = document.getElementById('sub-terms')?.checked;
    
    if (!name || !email || !phone || !address || !terms) {
        showNotification('Por favor completa todos los campos y acepta los términos', 'error');
        return;
    }
    
    // Simular procesamiento
    const subscriber = { name, email, phone, address, date: new Date().toISOString() };
    localStorage.setItem('premiumSubscription', JSON.stringify(subscriber));
    
    showSubscriptionConfirmation(subscriber);
    closeSubscriptionModal();
    showPremiumBadge();
    updatePricesWithDiscount();
    
    showNotification('¡Bienvenido al Club Los Cocos! 🌟', 'success');
};

window.closeSubscriptionModal = function() {
    console.log('❌ Cerrando modal de suscripción');
    const modal = document.getElementById('subscription-confirmation-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
};

window.startShopping = function() {
    console.log('🛒 Iniciando compras');
    closeSubscriptionModal();
    smoothScrollTo('especies-otono');
};

// Actualizar fondo y partículas según temporada
function updateBackgroundAndParticles() {
    const data = seasonData[currentSeason];
    
    // Actualizar imagen de fondo
    const heroBackground = document.getElementById('hero-background');
    if (heroBackground) {
        heroBackground.style.opacity = '0';
        setTimeout(() => {
            heroBackground.src = data.background;
            heroBackground.style.opacity = '1';
        }, 500);
    }
    
    // Actualizar overlay
    const heroOverlay = document.getElementById('hero-overlay');
    if (heroOverlay) {
        heroOverlay.className = `absolute inset-0 bg-gradient-to-r ${data.overlay} transition-all duration-1000`;
    }
    
    // Actualizar partículas
    updateParticles(data.particles);
}

// Actualizar partículas flotantes
function updateParticles(particles) {
    const particleElements = document.querySelectorAll('.particle');
    particleElements.forEach((particle, index) => {
        if (particles[index]) {
            particle.textContent = particles[index];
        }
    });
}

// Actualizar botones de temporada
function updateSeasonButtons() {
    const buttons = document.querySelectorAll('.season-test-btn');
    buttons.forEach(button => {
        button.classList.remove('ring-2', 'ring-white/50');
        if (button.textContent.includes(seasonData[currentSeason].icon)) {
            button.classList.add('ring-2', 'ring-white/50');
        }
    });
    
    // Actualizar línea de tiempo estacional
    updateSeasonTimeline();
}

// Nueva función para actualizar la línea de tiempo estacional
function updateSeasonTimeline() {
    console.log(`📅 Actualizando línea de tiempo para ${currentSeason}`);
    
    // Posiciones de las temporadas en la línea de tiempo (porcentajes)
    const seasonPositions = {
        'primavera': 12.5,  // Primer cuarto
        'verano': 37.5,     // Segundo cuarto
        'otono': 62.5,      // Tercer cuarto
        'invierno': 87.5    // Cuarto cuarto
    };
    
    // Colores del indicador según temporada
    const seasonColors = {
        'primavera': { border: 'border-green-400', bg: 'bg-green-500' },
        'verano': { border: 'border-yellow-400', bg: 'bg-yellow-500' },
        'otono': { border: 'border-orange-400', bg: 'bg-orange-500' },
        'invierno': { border: 'border-blue-400', bg: 'bg-blue-500' }
    };
    
    // Actualizar posición del indicador
    const indicator = document.getElementById('current-season-indicator');
    if (indicator) {
        const position = seasonPositions[currentSeason];
        indicator.style.left = `${position}%`;
        
        // Actualizar colores del indicador
        const circle = indicator.querySelector('.w-6.h-6');
        const label = indicator.querySelector('.absolute.-top-8');
        
        if (circle) {
            // Remover clases anteriores
            circle.className = circle.className.replace(/border-\w+-\d+/g, '');
            circle.classList.add(seasonColors[currentSeason].border);
        }
        
        if (label) {
            // Remover clases anteriores
            label.className = label.className.replace(/bg-\w+-\d+/g, '');
            label.classList.add(seasonColors[currentSeason].bg);
        }
    }
    
    // Actualizar estilos de los marcadores estacionales
    const markers = document.querySelectorAll('.season-marker');
    markers.forEach(marker => {
        const season = marker.getAttribute('data-season');
        const circle = marker.querySelector('.w-16.h-16');
        
        if (circle) {
            // Remover efectos anteriores
            circle.classList.remove('ring-4', 'ring-white/60', 'scale-110');
            marker.classList.remove('active');
            
            // Agregar efecto si es la temporada actual
            if (season === currentSeason) {
                circle.classList.add('ring-4', 'ring-white/60', 'scale-110');
                marker.classList.add('active');
            }
        }
    });
    
    console.log(`✅ Línea de tiempo actualizada para ${currentSeason}`);
}

// Actualizar contenido del hero según temporada
function updateHeroContent() {
    const data = seasonData[currentSeason];
    
    // Actualizar título principal con gradiente
    const heroTitle = document.getElementById('hero-title');
    if (heroTitle) {
        heroTitle.className = `bg-gradient-to-r ${data.heroGradient} bg-clip-text text-transparent drop-shadow-2xl animate-title-glow`;
    }
    
    // Actualizar subtítulo
    const heroSubtitle = document.getElementById('hero-subtitle');
    if (heroSubtitle) {
        heroSubtitle.textContent = data.heroSubtitle;
    }
    
    // Actualizar descripción
    const heroDescription = document.getElementById('hero-description');
    if (heroDescription) {
        heroDescription.textContent = data.heroDescription;
        heroDescription.className = `${data.heroDescriptionColor} text-xl`;
    }
}

// Actualizar características principales
function updateFeaturesContent() {
    const data = seasonData[currentSeason];
    if (!data) return;
    
    console.log(`🎨 Actualizando características para ${currentSeason}`);
    
    // Actualizar el grid de características
    const featuresGrid = document.getElementById('features-grid');
    if (featuresGrid) {
        // Actualizar clases del grid
        featuresGrid.className = featuresGrid.className.replace(/features-\w+/g, '');
        featuresGrid.classList.add(`features-${currentSeason}`);
        
        // Actualizar cada característica individualmente
        data.features.forEach((feature, index) => {
            const featureIcon = document.getElementById(`feature-${index + 1}-icon`);
            const featureTitle = document.getElementById(`feature-${index + 1}-title`);
            const featureDescription = document.getElementById(`feature-${index + 1}-description`);
            
            if (featureIcon) featureIcon.textContent = feature.icon;
            if (featureTitle) featureTitle.textContent = feature.title;
            if (featureDescription) featureDescription.textContent = feature.description;
            
            // Actualizar clases de la tarjeta de característica
            const featureCard = featureIcon?.closest('.autumn-card, .spring-card, .summer-card, .winter-card');
            if (featureCard) {
                // Remover clases de temporada anteriores
                featureCard.classList.remove('autumn-card', 'spring-card', 'summer-card', 'winter-card');
                featureCard.classList.add(`${currentSeason}-card`, 'feature-card');
                
                // Actualizar clases de hover según temporada
                const hoverClasses = {
                    'primavera': 'hover:bg-green-500/20',
                    'verano': 'hover:bg-yellow-500/20',
                    'otono': 'hover:bg-orange-500/20',
                    'invierno': 'hover:bg-blue-500/20'
                };
                
                featureCard.className = featureCard.className.replace(/hover:bg-\w+-500\/20/g, '');
                featureCard.classList.add(hoverClasses[currentSeason]);
            }
        });
    }
    
    // Actualizar colores de texto según temporada
    const textElements = featuresGrid?.querySelectorAll('h3, p');
    if (textElements) {
        textElements.forEach(element => {
            element.classList.remove('text-orange-100', 'text-green-100', 'text-yellow-100', 'text-blue-100');
            
            const textColors = {
                'primavera': 'text-green-100',
                'verano': 'text-yellow-100',
                'otono': 'text-orange-100',
                'invierno': 'text-blue-100'
            };
            
            if (element.tagName === 'P') {
                element.classList.add(textColors[currentSeason]);
            }
        });
    }
}

// Actualizar botones de acción
function updateActionButtons() {
    const data = seasonData[currentSeason];
    
    // Actualizar primer botón
    const button1 = document.getElementById('action-button-1');
    if (button1 && data.actionButtons[0]) {
        const actionBtn = data.actionButtons[0];
        const icon = button1.querySelector('span span:first-child');
        const text = button1.querySelector('span span:nth-child(2)');
        
        if (icon) icon.textContent = actionBtn.icon;
        if (text) text.textContent = actionBtn.text;
        button1.className = button1.className.replace(/bg-gradient-to-r.*?hover:to-\w+-\d+/, `bg-gradient-to-r ${actionBtn.gradient}`);
    }
    
    // Actualizar segundo botón
    const button2 = document.getElementById('action-button-2');
    if (button2 && data.actionButtons[1]) {
        const actionBtn = data.actionButtons[1];
        const icon = button2.querySelector('span span:first-child');
        const text = button2.querySelector('span span:nth-child(2)');
        
        if (icon) icon.textContent = actionBtn.icon;
        if (text) text.textContent = actionBtn.text;
        button2.className = button2.className.replace(/bg-gradient-to-r.*?hover:to-\w+-\d+/, `bg-gradient-to-r ${actionBtn.gradient}`);
    }
}

// Actualizar contenido de oferta
function updateOfferContent() {
    const data = seasonData[currentSeason];
    
    // Actualizar título de oferta
    const offerTitle = document.getElementById('offer-title');
    if (offerTitle) {
        offerTitle.textContent = data.offerTitle;
    }
    
    // Actualizar texto de oferta
    const offerText = document.getElementById('offer-text');
    if (offerText) {
        offerText.textContent = data.offerText;
    }
    
    // Actualizar iconos de oferta
    const offerIconLeft = document.getElementById('offer-icon-left');
    const offerIconRight = document.getElementById('offer-icon-right');
    if (offerIconLeft) offerIconLeft.textContent = data.offerIcon;
    if (offerIconRight) offerIconRight.textContent = data.offerIcon;
    
    // Actualizar gradiente del botón de oferta
    const offerButton = document.getElementById('offer-button');
    if (offerButton) {
        offerButton.className = offerButton.className.replace(/bg-gradient-to-r.*?hover:to-\w+-\d+/, `bg-gradient-to-r ${data.offerGradient}`);
    }
    
    // Actualizar banner de oferta
    const bannerTitle = document.getElementById('banner-title');
    const bannerDesc = document.getElementById('banner-description');
    if (bannerTitle) bannerTitle.textContent = `🎯 Oferta Especial de ${data.name.split(' ')[0]}`;
    if (bannerDesc) bannerDesc.textContent = `Lleva 3 plantas de temporada y paga solo 2. Válido hasta fin de mes.`;
}

// Actualizar productos de temporada en la sección principal
function updateSeasonProducts() {
    const data = seasonData[currentSeason];
    if (!data) return;
    
    console.log(`🛍️ Actualizando productos de temporada para ${currentSeason}`);
    
    // Buscar el grid de productos principales de la sección de especies
    const productGrid = document.querySelector('#especies-otono .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3, #especies-primavera .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3, #especies-verano .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3, #especies-invierno .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3');
    
    if (productGrid) {
        productGrid.innerHTML = '';
        
        data.products.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'bg-white rounded-3xl shadow-xl overflow-hidden hover-lift group';
            productCard.innerHTML = `
                <div class="relative h-64 bg-gradient-to-br ${product.gradient} overflow-hidden">
                    <img src="${product.image}" 
                         alt="${product.name}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1 text-sm font-bold ${product.badgeColor}">
                        ${product.badge}
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">${product.name}</h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        ${product.description}
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-2xl font-bold ${product.badgeColor}">$${product.price.toLocaleString()} ARS</span>
                        <div class="flex items-center gap-1 text-yellow-500">
                            <span>⭐⭐⭐⭐⭐</span>
                            <span class="text-sm text-gray-500">(${Math.floor(Math.random() * 200) + 50})</span>
                        </div>
                    </div>
                    <button class="w-full bg-gradient-to-r ${product.buttonGradient} text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105"
                            data-add-cart data-product-id="${product.id}" data-product-name="${product.name}" data-product-price="${product.price}">
                        🛒 Añadir al Carrito
                    </button>
                </div>
            `;
            productGrid.appendChild(productCard);
        });
        
        console.log(`✅ ${data.products.length} productos de ${currentSeason} cargados`);
    } else {
        console.warn('⚠️ No se encontró el grid de productos principales');
    }
}

// Cargar productos adicionales (tierra, utensilios, macetas)
function loadAdditionalProducts() {
    const data = seasonData[currentSeason];
    const grid = document.getElementById('additional-products-grid');
    
    if (!grid || !data.additionalProducts) return;
    
    grid.innerHTML = '';
    
    data.additionalProducts.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'bg-white rounded-xl shadow-lg overflow-hidden hover-lift group';
        
        productCard.innerHTML = `
            <div class="relative h-48 bg-gray-100 overflow-hidden">
                <img src="${product.image}" 
                     alt="${product.name}" 
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            </div>
            <div class="p-6">
                <h4 class="text-lg font-bold text-gray-900 mb-2">${product.name}</h4>
                <p class="text-gray-600 mb-4 text-sm leading-relaxed">${product.description}</p>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xl font-bold text-green-600">$${product.price.toLocaleString()} ARS</span>
                    <div class="flex items-center gap-1 text-yellow-500">
                        <span>⭐⭐⭐⭐⭐</span>
                    </div>
                </div>
                <button class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-105"
                        data-add-cart data-product-id="${product.id}" data-product-name="${product.name}" data-product-price="${product.price}">
                    🛒 Añadir al Carrito
                </button>
            </div>
        `;
        
        grid.appendChild(productCard);
    });
}

// Actualizar guía de preparación de jardín según temporada
function updatePreparationGuide() {
    const data = seasonData[currentSeason];
    const guide = data.preparationGuide;
    
    if (!guide) return;
    
    // Actualizar título principal
    const mainTitle = document.querySelector('#preparacion-otono h2');
    if (mainTitle) {
        mainTitle.textContent = guide.title;
    }
    
    // Actualizar subtítulo
    const subtitle = document.querySelector('#preparacion-otono .text-xl.text-orange-100');
    if (subtitle) {
        subtitle.textContent = guide.subtitle;
        subtitle.className = 'text-xl text-orange-100 max-w-4xl mx-auto leading-relaxed';
    }
    
    // Actualizar fondo de la sección
    const section = document.getElementById('preparacion-otono');
    if (section) {
        section.className = `py-16 sm:py-20 lg:py-24 px-4 sm:px-6 lg:px-8 bg-gradient-to-br ${guide.backgroundGradient} text-white`;
    }
    
    // Actualizar pasos de la guía
    const stepCards = document.querySelectorAll('#preparacion-otono .space-y-8 > div');
    stepCards.forEach((card, index) => {
        if (guide.steps[index]) {
            const step = guide.steps[index];
            
            // Actualizar número y gradiente
            const numberDiv = card.querySelector('.rounded-full');
            if (numberDiv) {
                numberDiv.textContent = step.number;
                numberDiv.className = `bg-gradient-to-r ${step.gradient} rounded-full w-12 h-12 flex items-center justify-center text-2xl font-bold text-white shadow-lg`;
            }
            
            // Actualizar icono y título
            const titleElement = card.querySelector('h3');
            if (titleElement) {
                titleElement.innerHTML = `${step.icon} ${step.title}`;
            }
            
            // Actualizar descripción
            const description = card.querySelector('p.text-orange-100');
            if (description) {
                description.textContent = step.description;
            }
            
            // Actualizar detalles
            const details = card.querySelector('.text-yellow-200');
            if (details) {
                details.textContent = step.details;
            }
        }
    });
    
    // Actualizar información del kit
    const kitTitle = document.querySelector('#preparacion-otono h3');
    if (kitTitle) {
        kitTitle.innerHTML = `🛒 ${guide.kitInfo.title}`;
    }
    
    // Actualizar precios del kit
    const kitItems = document.querySelectorAll('#preparacion-otono .space-y-4 .flex.items-center.justify-between');
    kitItems.forEach((item, index) => {
        if (guide.kitInfo.items[index]) {
            const kitItem = guide.kitInfo.items[index];
            const nameSpan = item.querySelector('span:first-child');
            const priceSpan = item.querySelector('span:last-child');
            
            if (nameSpan) nameSpan.textContent = kitItem.name;
            if (priceSpan) priceSpan.textContent = `$${kitItem.price.toLocaleString()}`;
        }
    });
    
    // Actualizar totales del kit
    const originalPrice = document.querySelector('#preparacion-otono .text-orange-200');
    const finalPrice = document.querySelector('#preparacion-otono .text-3xl.font-black');
    const savings = document.querySelector('#preparacion-otono .text-yellow-200.font-bold');
    
    if (originalPrice) originalPrice.textContent = `Precio individual: $${guide.kitInfo.originalPrice.toLocaleString()}`;
    if (finalPrice) finalPrice.textContent = `Kit Completo: $${guide.kitInfo.finalPrice.toLocaleString()}`;
    if (savings) savings.textContent = `¡Ahorrás $${guide.kitInfo.savings.toLocaleString()}!`;
    
    // Actualizar calendario de actividades
    const calendarMonths = document.querySelectorAll('#preparacion-otono .grid.md\\:grid-cols-3 > div');
    calendarMonths.forEach((monthDiv, index) => {
        if (guide.calendar[index]) {
            const monthData = guide.calendar[index];
            
            // Actualizar mes
            const monthTitle = monthDiv.querySelector('h4');
            if (monthTitle) monthTitle.textContent = monthData.month;
            
            // Actualizar actividades
            const activitiesList = monthDiv.querySelector('ul');
            if (activitiesList) {
                activitiesList.innerHTML = '';
                monthData.activities.forEach(activity => {
                    const li = document.createElement('li');
                    li.textContent = activity;
                    activitiesList.appendChild(li);
                });
            }
        }
    });
    
    // Actualizar botón del kit
    const kitButton = document.querySelector('#preparacion-otono button[data-product-id="kit-otono"]');
    if (kitButton) {
        const seasonName = data.name.split(' ')[0].toLowerCase();
        kitButton.setAttribute('data-product-id', `kit-${seasonName}`);
        kitButton.setAttribute('data-product-name', guide.kitInfo.title);
        kitButton.setAttribute('data-product-price', guide.kitInfo.finalPrice);
    }
}

// Cerrar modal de suscripción - DISPONIBLE GLOBALMENTE
window.closeSubscriptionModal = function() {
  const modal = document.getElementById('subscription-confirmation-modal');
  if (modal) {
    modal.remove();
  }
};

// Verificar si el usuario es miembro premium
function isPremiumMember() {
  const membership = localStorage.getItem('losCocosMembership');
  return membership !== null;
}

// Aplicar descuento de miembro premium
function applyPremiumDiscount(price) {
  if (isPremiumMember()) {
    return Math.round(price * 0.85); // 15% descuento
  }
  return price;
}

// Mostrar badge de miembro premium en el header
function showPremiumBadge() {
  if (isPremiumMember()) {
    const membership = JSON.parse(localStorage.getItem('losCocosMembership'));
    const header = document.querySelector('header .flex.items-center.gap-3');
    
    if (header && !document.getElementById('premium-badge')) {
      const badge = document.createElement('div');
      badge.id = 'premium-badge';
      badge.className = 'hidden md:flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-3 py-1 rounded-full text-xs font-bold';
      badge.innerHTML = `
        <span>⭐</span>
        <span>PREMIUM</span>
        <span>${membership.name.split(' ')[0]}</span>
      `;
      
      header.insertBefore(badge, header.firstChild);
    }
  }
}

// Actualizar precios con descuento premium
function updatePricesWithDiscount() {
  if (isPremiumMember()) {
    // Actualizar todos los precios mostrados
    const priceElements = document.querySelectorAll('[data-product-price]');
    priceElements.forEach(element => {
      const originalPrice = parseInt(element.dataset.productPrice);
      const discountedPrice = applyPremiumDiscount(originalPrice);
      
      // Crear elemento de precio con descuento
      const priceContainer = element.closest('.p-5, .p-6');
      if (priceContainer && !priceContainer.querySelector('.premium-discount')) {
        const priceSpan = priceContainer.querySelector('.text-lg.font-bold, .text-xl.font-bold, .text-2xl.font-bold');
        if (priceSpan) {
          const discountDiv = document.createElement('div');
          discountDiv.className = 'premium-discount';
          discountDiv.innerHTML = `
            <div class="text-xs text-gray-500 line-through">$${originalPrice.toLocaleString('es-AR')} ARS</div>
            <div class="text-lg font-bold text-green-600">$${discountedPrice.toLocaleString('es-AR')} ARS</div>
            <div class="text-xs text-green-600 font-semibold">⭐ Precio Premium (-15%)</div>
          `;
          priceSpan.replaceWith(discountDiv);
        }
      }
    });
  }
}

// Inicializar funciones de suscripción al cargar la página
document.addEventListener('DOMContentLoaded', function() {
  showPremiumBadge();
  updatePricesWithDiscount();
});

// Nueva función para actualizar la sección de especies de temporada
function updateSpeciesSection() {
    const data = seasonData[currentSeason];
    if (!data) return;
    
    console.log(`📦 Actualizando sección de especies para ${currentSeason}`);
    
    // Actualizar el ID de la sección
    const speciesSection = document.getElementById('especies-otono');
    if (speciesSection) {
        speciesSection.id = `especies-${currentSeason}`;
        
        // Actualizar clases de fondo según temporada
        const bgClasses = {
            'primavera': 'bg-gradient-to-br from-green-50 to-emerald-50',
            'verano': 'bg-gradient-to-br from-yellow-50 to-orange-50',
            'otono': 'bg-gradient-to-br from-orange-50 to-amber-50',
            'invierno': 'bg-gradient-to-br from-blue-50 to-indigo-50'
        };
        
        // Remover clases anteriores y agregar nuevas
        speciesSection.className = speciesSection.className.replace(/bg-gradient-to-br from-\w+-50 to-\w+-50/g, '');
        speciesSection.classList.add(...bgClasses[currentSeason].split(' '));
    }
    
    // Actualizar el badge de temporada
    const seasonBadge = document.querySelector('#especies-otono .inline-flex, #especies-primavera .inline-flex, #especies-verano .inline-flex, #especies-invierno .inline-flex');
    if (seasonBadge) {
        const badgeContent = {
            'primavera': { icons: ['🌸', '🌿'], text: 'ESPECIAL PRIMAVERA 2024', gradient: 'from-green-500 to-emerald-500' },
            'verano': { icons: ['☀️', '🌻'], text: 'ESPECIAL VERANO 2024', gradient: 'from-yellow-500 to-orange-500' },
            'otono': { icons: ['🍂', '🍁'], text: 'ESPECIAL OTOÑO 2024', gradient: 'from-orange-500 to-red-500' },
            'invierno': { icons: ['❄️', '🌨️'], text: 'ESPECIAL INVIERNO 2024', gradient: 'from-blue-500 to-indigo-500' }
        };
        
        const content = badgeContent[currentSeason];
        seasonBadge.className = seasonBadge.className.replace(/from-\w+-500 to-\w+-500/g, content.gradient);
        seasonBadge.innerHTML = `
            <span>${content.icons[0]}</span>
            <span>${content.text}</span>
            <span>${content.icons[1]}</span>
        `;
    }
    
    // Actualizar título de la sección
    const sectionTitle = document.querySelector('#especies-otono h2 span, #especies-primavera h2 span, #especies-verano h2 span, #especies-invierno h2 span');
    if (sectionTitle) {
        const titleGradients = {
            'primavera': 'from-green-600 to-emerald-600',
            'verano': 'from-yellow-600 to-orange-600',
            'otono': 'from-orange-600 to-red-600',
            'invierno': 'from-blue-600 to-indigo-600'
        };
        
        sectionTitle.className = sectionTitle.className.replace(/from-\w+-600 to-\w+-600/g, titleGradients[currentSeason]);
        sectionTitle.textContent = 'Especies de Temporada';
    }
    
    // Actualizar descripción de la sección
    const sectionDescription = document.querySelector('#especies-otono p, #especies-primavera p, #especies-verano p, #especies-invierno p');
    if (sectionDescription) {
        const descriptions = {
            'primavera': 'Descubre las plantas que despiertan con la primavera mendocina. Colores vibrantes y crecimiento renovado para esta época de floración.',
            'verano': 'Explora las plantas que brillan bajo el sol mendocino. Resistentes al calor y llenas de vida durante el verano.',
            'otono': 'Descubre las plantas que más brillan durante el otoño mendocino. Colores vibrantes y resistencia natural para esta época del año.',
            'invierno': 'Conoce las plantas que resisten el frío mendocino. Belleza y resistencia para los meses más fríos del año.'
        };
        
        sectionDescription.textContent = descriptions[currentSeason];
    }
    
    // Actualizar grid de productos principales
    const productGrid = document.querySelector('#especies-otono .grid, #especies-primavera .grid, #especies-verano .grid, #especies-invierno .grid');
    if (productGrid) {
        productGrid.innerHTML = '';
        
        data.products.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'bg-white rounded-3xl shadow-xl overflow-hidden hover-lift group';
            productCard.innerHTML = `
                <div class="relative h-64 bg-gradient-to-br ${product.gradient} overflow-hidden">
                    <img src="${product.image}" 
                         alt="${product.name}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1 text-sm font-bold ${product.badgeColor}">
                        ${product.badge}
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">${product.name}</h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        ${product.description}
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-2xl font-bold ${product.badgeColor}">$${product.price.toLocaleString()} ARS</span>
                        <div class="flex items-center gap-1 text-yellow-500">
                            <span>⭐⭐⭐⭐⭐</span>
                            <span class="text-sm text-gray-500">(${Math.floor(Math.random() * 200) + 50})</span>
                        </div>
                    </div>
                    <button class="w-full bg-gradient-to-r ${product.buttonGradient} text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105"
                            data-add-cart data-product-id="${product.id}" data-product-name="${product.name}" data-product-price="${product.price}">
                        🛒 Añadir al Carrito
                    </button>
                </div>
            `;
            productGrid.appendChild(productCard);
        });
    }
    
    // Cargar productos adicionales
    loadAdditionalProducts();
}

// Nueva función para actualizar el banner de ofertas de temporada
function updateSeasonBanner() {
    const data = seasonData[currentSeason];
    if (!data) return;
    
    console.log(`🎯 Actualizando banner de ofertas para ${currentSeason}`);
    
    const banner = document.getElementById('season-offer-banner');
    if (banner) {
        const bannerGradients = {
            'primavera': 'from-green-600 via-emerald-500 to-green-600',
            'verano': 'from-yellow-600 via-orange-500 to-yellow-600',
            'otono': 'from-orange-600 via-red-500 to-orange-600',
            'invierno': 'from-blue-600 via-indigo-500 to-blue-600'
        };
        
        const bannerContent = {
            'primavera': { 
                title: '🎯 Oferta Especial de Primavera',
                description: 'Lleva 3 plantas de temporada y paga solo 2. Válido hasta fin de mes.',
                icon: '🌸'
            },
            'verano': { 
                title: '🎯 Oferta Especial de Verano',
                description: 'Pack de plantas resistentes al calor con 30% OFF. Incluye sistema de riego.',
                icon: '☀️'
            },
            'otono': { 
                title: '🎯 Oferta Especial de Otoño',
                description: 'Lleva 3 plantas de temporada y paga solo 2. Válido hasta fin de mes.',
                icon: '🍂'
            },
            'invierno': { 
                title: '🎯 Oferta Especial de Invierno',
                description: 'Plantas de interior con protección incluida. Descuento del 25%.',
                icon: '❄️'
            }
        };
        
        banner.className = banner.className.replace(/from-\w+-600 via-\w+-500 to-\w+-600/g, bannerGradients[currentSeason]);
        
        const bannerTitle = document.getElementById('banner-title');
        const bannerDescription = document.getElementById('banner-description');
        
        if (bannerTitle) bannerTitle.textContent = bannerContent[currentSeason].title;
        if (bannerDescription) bannerDescription.textContent = bannerContent[currentSeason].description;
    }
}

// Actualizar función updateActionButtons para cambiar las referencias de href
function updateActionButtons() {
    const data = seasonData[currentSeason];
    if (!data) return;
    
    console.log(`🔗 Actualizando botones de acción para ${currentSeason}`);
    
    const actionButton1 = document.getElementById('action-button-1');
    const actionButton2 = document.getElementById('action-button-2');
    
    if (actionButton1 && data.actionButtons[0]) {
        const button = data.actionButtons[0];
        actionButton1.href = `#especies-${currentSeason}`;
        actionButton1.className = actionButton1.className.replace(/from-\w+-600 to-\w+-600 hover:from-\w+-700 hover:to-\w+-700/g, button.gradient);
        
        const buttonContent = actionButton1.querySelector('span.relative');
        if (buttonContent) {
            buttonContent.innerHTML = `
                <span>${button.icon}</span>
                <span>${button.text}</span>
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            `;
        }
    }
    
    if (actionButton2 && data.actionButtons[1]) {
        const button = data.actionButtons[1];
        actionButton2.href = `#preparacion-${currentSeason}`;
        actionButton2.className = actionButton2.className.replace(/from-\w+-600 to-\w+-600 hover:from-\w+-700 hover:to-\w+-700/g, button.gradient);
        
        const buttonContent = actionButton2.querySelector('span.relative');
        if (buttonContent) {
            buttonContent.innerHTML = `
                <span>${button.icon}</span>
                <span>${button.text}</span>
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            `;
        }
    }
}

// Actualizar función updatePreparationGuide para cambiar el ID de la sección
function updatePreparationGuide() {
    const data = seasonData[currentSeason];
    if (!data || !data.preparationGuide) return;
    
    console.log(`🛠️ Actualizando guía de preparación para ${currentSeason}`);
    
    // Actualizar ID de la sección
    const preparationSection = document.getElementById('preparacion-otono');
    if (preparationSection) {
        preparationSection.id = `preparacion-${currentSeason}`;
        
        // Actualizar clases de fondo
        preparationSection.className = preparationSection.className.replace(/from-\w+-900 via-\w+-800 to-\w+-900/g, data.preparationGuide.backgroundGradient);
    }
    
    // Actualizar contenido de la guía
    const guide = data.preparationGuide;
    
    // Actualizar título y subtítulo
    const title = document.querySelector('#preparacion-otono h2, #preparacion-primavera h2, #preparacion-verano h2, #preparacion-invierno h2');
    if (title) {
        title.textContent = guide.title;
    }
    
    const subtitle = document.querySelector('#preparacion-otono .text-xl, #preparacion-primavera .text-xl, #preparacion-verano .text-xl, #preparacion-invierno .text-xl');
    if (subtitle) {
        subtitle.textContent = guide.subtitle;
    }
    
    // Actualizar pasos de la guía
    const stepsContainer = document.querySelector('#preparacion-otono .space-y-8, #preparacion-primavera .space-y-8, #preparacion-verano .space-y-8, #preparacion-invierno .space-y-8');
    if (stepsContainer) {
        stepsContainer.innerHTML = '';
        
        guide.steps.forEach(step => {
            const stepElement = document.createElement('div');
            stepElement.className = 'bg-white/10 backdrop-blur-md rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 hover-lift';
            stepElement.innerHTML = `
                <div class="flex items-start gap-4">
                    <div class="bg-gradient-to-r ${step.gradient} rounded-full w-12 h-12 flex items-center justify-center text-2xl font-bold text-white shadow-lg">
                        ${step.number}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white mb-2">${step.icon} ${step.title}</h3>
                        <p class="text-orange-100 leading-relaxed mb-3">
                            ${step.description}
                        </p>
                        <div class="text-sm text-yellow-200 font-medium">
                            ${step.details}
                        </div>
                    </div>
                </div>
            `;
            stepsContainer.appendChild(stepElement);
        });
    }
    
    // Actualizar información del kit
    const kitContainer = document.querySelector('#preparacion-otono .bg-white\\/10.backdrop-blur-md.rounded-3xl, #preparacion-primavera .bg-white\\/10.backdrop-blur-md.rounded-3xl, #preparacion-verano .bg-white\\/10.backdrop-blur-md.rounded-3xl, #preparacion-invierno .bg-white\\/10.backdrop-blur-md.rounded-3xl');
    if (kitContainer && guide.kitInfo) {
        const kit = guide.kitInfo;
        kitContainer.innerHTML = `
            <h3 class="text-2xl font-bold text-white mb-6 text-center">🛒 ${kit.title}</h3>
            <div class="space-y-4 mb-6">
                ${kit.items.map(item => `
                    <div class="flex items-center justify-between py-3 border-b border-white/20">
                        <span class="text-orange-100">${item.name}</span>
                        <span class="text-white font-bold">$${item.price.toLocaleString()}</span>
                    </div>
                `).join('')}
            </div>
            
            <div class="text-center mb-6">
                <div class="text-sm text-orange-200 mb-2">Precio individual: $${kit.originalPrice.toLocaleString()}</div>
                <div class="text-3xl font-black text-white">Kit Completo: $${kit.finalPrice.toLocaleString()}</div>
                <div class="text-sm text-yellow-200 font-bold">¡Ahorrás $${kit.savings.toLocaleString()}!</div>
            </div>

            <button class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105 shadow-xl"
                    data-add-cart data-product-id="kit-${currentSeason}" data-product-name="${kit.title}" data-product-price="${kit.finalPrice}">
                🛒 Comprar Kit Completo
            </button>
        `;
    }
    
    // Actualizar calendario de actividades
    const calendarContainer = document.querySelector('#preparacion-otono .grid.md\\:grid-cols-3, #preparacion-primavera .grid.md\\:grid-cols-3, #preparacion-verano .grid.md\\:grid-cols-3, #preparacion-invierno .grid.md\\:grid-cols-3');
    if (calendarContainer && guide.calendar) {
        calendarContainer.innerHTML = '';
        
        guide.calendar.forEach(month => {
            const monthElement = document.createElement('div');
            monthElement.className = 'bg-white/10 rounded-2xl p-6';
            monthElement.innerHTML = `
                <h4 class="text-xl font-bold text-yellow-300 mb-3">${month.name}</h4>
                <ul class="text-orange-100 text-sm space-y-2">
                    ${month.activities.map(activity => `<li>${activity}</li>`).join('')}
                </ul>
            `;
            calendarContainer.appendChild(monthElement);
        });
    }
}

// Cargar consejos iniciales (versión simplificada)
function loadSeasonTips() {
    // Esta función se puede usar para cargar consejos en otros lugares de la página
    updateSeasonDisplay();
}

// Iniciar countdown de oferta
function startOfferCountdown() {
    const countdownEl = document.getElementById('offer-countdown');
    if (!countdownEl) return;
    
    let timeLeft = 24 * 60 * 60; // 24 horas en segundos
    
    offerCountdown = setInterval(() => {
        const hours = Math.floor(timeLeft / 3600);
        const minutes = Math.floor((timeLeft % 3600) / 60);
        const seconds = timeLeft % 60;
        
        countdownEl.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        timeLeft--;
        
        if (timeLeft < 0) {
            clearInterval(offerCountdown);
            countdownEl.textContent = '¡Oferta Expirada!';
        }
    }, 1000);
}

// Funciones de cantidad para ofertas
function increaseOfferQuantity() {
    offerQuantity++;
    updateOfferDisplay();
}

function decreaseOfferQuantity() {
    if (offerQuantity > 1) {
        offerQuantity--;
        updateOfferDisplay();
    }
}

function updateOfferDisplay() {
    const quantityEl = document.getElementById('offer-quantity');
    if (quantityEl) {
        quantityEl.textContent = offerQuantity;
    }
}

// Agregar oferta al carrito
function addOfferToCart() {
    const product = {
        id: 'pack-otono-especial',
        name: 'Pack Otoño Premium',
        price: 17000,
        quantity: offerQuantity,
        image: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
    };
    
    // Usar la función existente del carrito si está disponible
    if (typeof addToCart === 'function') {
        for (let i = 0; i < offerQuantity; i++) {
            addToCart(product.id, product.name, product.price);
        }
    }
    
    // Mostrar notificación
    showNotification('¡Pack Otoño Premium agregado al carrito!', 'success');
    
    // Cerrar modal
    closeDailyOfferModal();
}

// Comprar oferta ahora
function buyOfferNow() {
    addOfferToCart();
    // Redirigir a checkout o mostrar proceso de compra
    showNotification('Redirigiendo al checkout...', 'info');
    
    // Simular redirección después de un breve delay
    setTimeout(() => {
        window.location.href = 'checkout.html';
    }, 1500);
}

// Mostrar notificaciones
function showNotification(message, type = 'info') {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white font-medium transform transition-all duration-300 translate-x-full`;
    
    // Aplicar estilos según tipo
    switch (type) {
        case 'success':
            notification.classList.add('bg-green-500');
            break;
        case 'error':
            notification.classList.add('bg-red-500');
            break;
        case 'warning':
            notification.classList.add('bg-yellow-500');
            break;
        default:
            notification.classList.add('bg-blue-500');
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Funciones de utilidad para scroll suave
function smoothScrollTo(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Event listeners para navegación suave
document.addEventListener('DOMContentLoaded', function() {
    // Agregar event listeners a los enlaces de navegación
    const navLinks = document.querySelectorAll('a[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            smoothScrollTo(targetId);
        });
    });
});

// Limpiar intervalos cuando se cierra la página
window.addEventListener('beforeunload', function() {
    if (offerCountdown) {
        clearInterval(offerCountdown);
    }
});

// ========================================
// SISTEMA DE SUSCRIPCIÓN PREMIUM
// ========================================

// Enviar formulario de suscripción - DISPONIBLE GLOBALMENTE
window.submitSubscription = function(event) {
  event.preventDefault();
  
  const name = document.getElementById('sub-name').value;
  const email = document.getElementById('sub-email').value;
  const phone = document.getElementById('sub-phone').value;
  const address = document.getElementById('sub-address').value;
  const terms = document.getElementById('sub-terms').checked;
  
  // Validaciones
  if (!name || !email || !phone || !address) {
    showNotification('Por favor completa todos los campos', 'error');
    return;
  }
  
  if (!terms) {
    showNotification('Debes aceptar los términos y condiciones', 'error');
    return;
  }
  
  // Validar email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showNotification('Por favor ingresa un email válido', 'error');
    return;
  }
  
  // Simular envío de datos
  const button = event.target;
  const originalText = button.innerHTML;
  button.innerHTML = '⏳ Procesando...';
  button.disabled = true;
  
  setTimeout(() => {
    // Guardar datos del suscriptor
    const subscriber = {
      name,
      email,
      phone,
      address,
      subscriptionDate: new Date().toISOString(),
      membershipType: 'premium',
      monthlyFee: 1500
    };
    
    localStorage.setItem('losCocosMembership', JSON.stringify(subscriber));
    
    // Mostrar modal de confirmación
    showSubscriptionConfirmation(subscriber);
    
    // Resetear formulario
    document.getElementById('subscription-form').reset();
    button.innerHTML = originalText;
    button.disabled = false;
    
    showNotification('¡Bienvenido al Club Los Cocos! 🌟', 'success');
  }, 2000);
};

// Modal de confirmación de suscripción
function showSubscriptionConfirmation(subscriber) {
  const modal = document.createElement('div');
  modal.className = 'fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm z-50 flex items-center justify-center p-4';
  modal.id = 'subscription-confirmation-modal';
  
  modal.innerHTML = `
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full animate-modal-enter">
      <!-- Header -->
      <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white p-8 text-center rounded-t-3xl">
        <div class="text-6xl mb-4">🎉</div>
        <h2 class="text-3xl font-black mb-2">¡Bienvenido al Club!</h2>
        <p class="text-xl opacity-90">Ya eres miembro premium de Los Cocos</p>
      </div>
      
      <!-- Contenido -->
      <div class="p-8">
        <div class="text-center mb-6">
          <h3 class="text-xl font-bold text-gray-900 mb-4">Hola ${subscriber.name} 👋</h3>
          <p class="text-gray-600 mb-6">Tu membresía premium está activa y ya puedes disfrutar de todos los beneficios:</p>
        </div>
        
        <div class="grid md:grid-cols-2 gap-4 mb-6">
          <div class="bg-green-50 rounded-xl p-4 text-center">
            <div class="text-2xl mb-2">🚚</div>
            <div class="font-semibold text-green-800">Envío Gratis</div>
            <div class="text-sm text-green-600">Mismo día en Mendoza</div>
          </div>
          
          <div class="bg-blue-50 rounded-xl p-4 text-center">
            <div class="text-2xl mb-2">💰</div>
            <div class="font-semibold text-blue-800">15% Descuento</div>
            <div class="text-sm text-blue-600">En todas tus compras</div>
          </div>
          
          <div class="bg-purple-50 rounded-xl p-4 text-center">
            <div class="text-2xl mb-2">🌱</div>
            <div class="font-semibold text-purple-800">Asesoramiento</div>
            <div class="text-sm text-purple-600">Consultas ilimitadas</div>
          </div>
          
          <div class="bg-orange-50 rounded-xl p-4 text-center">
            <div class="text-2xl mb-2">🎁</div>
            <div class="font-semibold text-orange-800">Regalos</div>
            <div class="text-sm text-orange-600">Plantas mensuales</div>
          </div>
        </div>
        
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
          <h4 class="font-semibold text-gray-900 mb-2">📧 Te hemos enviado por email:</h4>
          <ul class="text-sm text-gray-600 space-y-1">
            <li>✅ Confirmación de membresía</li>
            <li>✅ Guía de beneficios completa</li>
            <li>✅ Código de descuento personal</li>
            <li>✅ Contacto directo con expertos</li>
          </ul>
        </div>
        
        <div class="flex gap-4">
          <button onclick="closeSubscriptionModal()" 
                  class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-xl transition-all">
            Cerrar
          </button>
          <button onclick="startShopping()" 
                  class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 px-6 rounded-xl transition-all">
            🛒 Empezar a Comprar
          </button>
        </div>
      </div>
    </div>
  `;
  
  document.body.appendChild(modal);
}

// Cargar consejos detallados según temporada
function loadDetailedSeasonTips() {
    const seasonTips = {
        'primavera': {
            icon: '🌸',
            icon2: '🌿',
            title: 'Consejos para Primavera',
            subtitle: 'Aprovecha al máximo la temporada de crecimiento',
            headerClass: 'from-green-500 to-emerald-600',
            tips: [
                {
                    title: 'Siembra y Plantación',
                    content: 'Es el momento ideal para plantar nuevas especies. El suelo está caliente y las condiciones son perfectas para el crecimiento.',
                    icon: '🌱'
                },
                {
                    title: 'Aumento de Riego',
                    content: 'Incrementa gradualmente la frecuencia de riego. Las plantas están despertando del invierno y necesitan más agua.',
                    icon: '💧'
                },
                {
                    title: 'Fertilización Activa',
                    content: 'Aplica fertilizantes ricos en nitrógeno para promover el crecimiento verde y vigoroso de las plantas.',
                    icon: '🌿'
                },
                {
                    title: 'Control de Plagas',
                    content: 'Vigila la aparición de plagas que despiertan con el calor. Aplica tratamientos preventivos naturales.',
                    icon: '🐛'
                }
            ],
            plants: [
                { name: 'Tulipanes', description: 'Bulbos coloridos que anuncian la primavera', price: '$7.200' },
                { name: 'Petunias', description: 'Flores abundantes en múltiples colores', price: '$5.800' },
                { name: 'Primulas', description: 'Primeras flores de la temporada', price: '$4.900' }
            ],
            calendar: [
                'Septiembre: Preparación del suelo y siembra',
                'Octubre: Plantación masiva y fertilización',
                'Noviembre: Primeros cuidados y riego regular'
            ]
        },
        'verano': {
            icon: '☀️',
            icon2: '🌻',
            title: 'Consejos para Verano',
            subtitle: 'Mantén tu jardín fresco y vibrante en el calor',
            headerClass: 'from-yellow-500 to-orange-500',
            tips: [
                {
                    title: 'Riego Intensivo',
                    content: 'Aumenta la frecuencia de riego, preferiblemente en las primeras horas de la mañana o al atardecer.',
                    icon: '💧'
                },
                {
                    title: 'Protección Solar',
                    content: 'Instala mallas de sombra para proteger las plantas más sensibles del sol directo intenso.',
                    icon: '🌞'
                },
                {
                    title: 'Mulching',
                    content: 'Aplica mulch alrededor de las plantas para conservar la humedad y mantener las raíces frescas.',
                    icon: '🍃'
                },
                {
                    title: 'Cosecha Continua',
                    content: 'Cosecha regularmente frutas y verduras para estimular la producción continua.',
                    icon: '🍅'
                }
            ],
            plants: [
                { name: 'Girasoles', description: 'Flores gigantes que siguen al sol', price: '$6.800' },
                { name: 'Lavanda', description: 'Aromática y resistente al calor', price: '$8.900' },
                { name: 'Geranios', description: 'Coloridos y duraderos', price: '$5.200' }
            ],
            calendar: [
                'Diciembre: Riego intensivo y protección',
                'Enero: Mantenimiento y cosecha',
                'Febrero: Preparación para otoño'
            ]
        },
        'otono': {
            icon: '🍂',
            icon2: '🍁',
            title: 'Consejos para Otoño',
            subtitle: 'Guía completa para cuidar tu jardín durante el otoño mendocino',
            headerClass: 'from-orange-600 to-red-600',
            tips: [
                {
                    title: 'Preparación del Suelo',
                    content: 'Aplica compost orgánico y abono de liberación lenta. Es el momento perfecto para enriquecer la tierra antes del invierno.',
                    icon: '🌱'
                },
                {
                    title: 'Protección contra Heladas',
                    content: 'Cubre las plantas sensibles con mantas térmicas o mulch. Prepara estructuras de protección para especies delicadas.',
                    icon: '❄️'
                },
                {
                    title: 'Poda y Mantenimiento',
                    content: 'Realiza podas de limpieza y formación. Retira hojas secas y ramas dañadas para prevenir enfermedades.',
                    icon: '✂️'
                },
                {
                    title: 'Riego Controlado',
                    content: 'Reduce gradualmente la frecuencia de riego. Las plantas necesitan menos agua durante el otoño.',
                    icon: '💧'
                }
            ],
            plants: [
                { name: 'Crisantemos', description: 'Flores coloridas perfectas para el otoño', price: '$8.500' },
                { name: 'Asters', description: 'Pequeñas estrellas que florecen hasta las heladas', price: '$6.200' },
                { name: 'Cyclamen', description: 'Flores delicadas ideales para interiores', price: '$7.800' }
            ],
            calendar: [
                'Marzo: Siembra de especies otoñales',
                'Abril: Aplicación de abonos y podas',
                'Mayo: Protección contra primeras heladas'
            ]
        },
        'invierno': {
            icon: '❄️',
            icon2: '🌲',
            title: 'Consejos para Invierno',
            subtitle: 'Protege y planifica durante la temporada de descanso',
            headerClass: 'from-blue-500 to-slate-600',
            tips: [
                {
                    title: 'Protección contra Heladas',
                    content: 'Cubre las plantas sensibles con mantas térmicas. Riega antes de las heladas para crear una capa protectora.',
                    icon: '❄️'
                },
                {
                    title: 'Riego Reducido',
                    content: 'Disminuye significativamente el riego. Las plantas necesitan menos agua durante su período de dormancia.',
                    icon: '💧'
                },
                {
                    title: 'Planificación',
                    content: 'Planifica las mejoras para la próxima temporada. Diseña nuevos espacios y selecciona plantas.',
                    icon: '📋'
                },
                {
                    title: 'Mantenimiento de Herramientas',
                    content: 'Limpia y mantén las herramientas de jardín. Prepara todo para la próxima temporada de crecimiento.',
                    icon: '🔧'
                }
            ],
            plants: [
                { name: 'Ciclamen', description: 'Flores de invierno resistentes al frío', price: '$7.800' },
                { name: 'Pensamientos', description: 'Coloridas flores que toleran el frío', price: '$4.500' },
                { name: 'Coníferas', description: 'Árboles perennes para estructura invernal', price: '$15.200' }
            ],
            calendar: [
                'Junio: Protección intensiva contra frío',
                'Julio: Mantenimiento mínimo y planificación',
                'Agosto: Preparación para primavera'
            ]
        }
    };

    const data = seasonTips[currentSeason] || seasonTips['otono'];
    
    console.log(`💡 Cargando consejos detallados para ${currentSeason}`);
    
    // Actualizar header del modal
    const tipsHeader = document.getElementById('tips-header');
    if (tipsHeader) {
        tipsHeader.className = `bg-gradient-to-r ${data.headerClass} text-white p-8 relative overflow-hidden`;
    }
    
    // Actualizar contenido del header
    const seasonIcon = document.getElementById('tips-season-icon');
    const seasonTitle = document.getElementById('tips-season-title');
    const seasonIcon2 = document.getElementById('tips-season-icon-2');
    const seasonSubtitle = document.getElementById('tips-season-subtitle');
    
    if (seasonIcon) seasonIcon.textContent = data.icon;
    if (seasonTitle) seasonTitle.textContent = data.title;
    if (seasonIcon2) seasonIcon2.textContent = data.icon2;
    if (seasonSubtitle) seasonSubtitle.textContent = data.subtitle;
    
    // Cargar consejos principales
    const tipsContent = document.getElementById('tips-content');
    if (tipsContent) {
        tipsContent.innerHTML = data.tips.map(tip => `
            <div class="bg-gray-50 rounded-xl p-6 hover:bg-gray-100 transition-colors">
                <div class="flex items-start gap-4">
                    <span class="text-2xl">${tip.icon}</span>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-2">${tip.title}</h4>
                        <p class="text-gray-700 leading-relaxed">${tip.content}</p>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    // Cargar plantas recomendadas
    const recommendedPlants = document.getElementById('recommended-plants');
    if (recommendedPlants) {
        recommendedPlants.innerHTML = data.plants.map(plant => `
            <div class="bg-green-50 rounded-xl p-4 hover:bg-green-100 transition-colors">
                <h5 class="font-bold text-gray-900">${plant.name}</h5>
                <p class="text-sm text-gray-600 mb-2">${plant.description}</p>
                <span class="text-green-600 font-bold">${plant.price}</span>
            </div>
        `).join('');
    }
    
    // Cargar calendario
    const monthlyCalendar = document.getElementById('monthly-calendar');
    if (monthlyCalendar) {
        monthlyCalendar.innerHTML = data.calendar.map(item => `
            <div class="flex items-center gap-2">
                <span class="text-orange-500">•</span>
                <span>${item}</span>
            </div>
        `).join('');
    }
    
    console.log(`✅ Consejos detallados de ${currentSeason} cargados`);
}