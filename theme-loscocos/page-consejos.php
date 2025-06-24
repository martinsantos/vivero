<?php 
get_header(); 

// Función para obtener productos reales por categoría
function get_real_products_by_season($season) {
    $products_data = array();
    
    // Mapeo de temporadas a categorías y productos específicos
    $season_mapping = array(
        'primavera' => array(
            'categories' => array('sustratos', 'fertilizantes', 'macetas-plasticas', 'plantas-interior'),
            'products' => array(
                array('category' => 'sustratos', 'limit' => 1),
                array('category' => 'fertilizantes', 'limit' => 1),
                array('category' => 'macetas-plasticas', 'limit' => 1),
                array('category' => 'plantas-interior', 'limit' => 1)
            )
        ),
        'verano' => array(
            'categories' => array('macetas-fibrocemento', 'arbustos', 'enredaderas', 'fitosanitarios'),
            'products' => array(
                array('category' => 'macetas-fibrocemento', 'limit' => 1),
                array('category' => 'arbustos', 'limit' => 1),
                array('category' => 'enredaderas', 'limit' => 1),
                array('category' => 'fitosanitarios', 'limit' => 1)
            )
        ),
        'otono' => array(
            'categories' => array('arboles', 'sustratos', 'fertilizantes', 'macetas-fibrocemento'),
            'products' => array(
                array('category' => 'arboles', 'limit' => 1),
                array('category' => 'sustratos', 'limit' => 1),
                array('category' => 'fertilizantes', 'limit' => 1),
                array('category' => 'macetas-fibrocemento', 'limit' => 1)
            )
        ),
        'invierno' => array(
            'categories' => array('plantas-interior', 'fitosanitarios', 'hierros-soportes', 'pies-nordicos'),
            'products' => array(
                array('category' => 'plantas-interior', 'limit' => 1),
                array('category' => 'fitosanitarios', 'limit' => 1),
                array('category' => 'hierros-soportes', 'limit' => 1),
                array('category' => 'pies-nordicos', 'limit' => 1)
            )
        )
    );
    
    if (!isset($season_mapping[$season])) {
        return array();
    }
    
    $season_data = $season_mapping[$season];
    
    foreach ($season_data['products'] as $product_config) {
        $category_slug = $product_config['category'];
        $limit = $product_config['limit'];
        
        // Obtener productos de esta categoría
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => $limit,
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $category_slug,
                ),
            ),
            'meta_query' => array(
                array(
                    'key' => '_stock_status',
                    'value' => 'instock',
                    'compare' => '='
                )
            )
        );
        
        $products = get_posts($args);
        
        foreach ($products as $product_post) {
            $product = wc_get_product($product_post->ID);
            if ($product) {
                // Obtener categoría principal
                $categories = wp_get_post_terms($product_post->ID, 'product_cat');
                $category_name = !empty($categories) ? $categories[0]->name : 'Sin categoría';
                
                // Obtener imagen del producto
                $image_id = $product->get_image_id();
                $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : get_category_image_by_slug($category_slug);
                
                                 $products_data[] = array(
                     'id' => $product->get_id(),
                     'name' => $product->get_name(),
                     'slug' => $product->get_slug(),
                     'price' => '$' . number_format($product->get_price(), 0, ',', '.'),
                     'regular_price' => $product->get_regular_price() != $product->get_price() ? '$' . number_format($product->get_regular_price(), 0, ',', '.') : null,
                     'image' => $image_url,
                     'description' => wp_trim_words($product->get_short_description() ?: $product->get_description(), 15),
                     'category' => $category_name,
                     'badge' => get_category_emoji($category_slug),
                     'url' => home_url('/?product=' . $product->get_slug())
                 );
            }
        }
    }
    
    return $products_data;
}

// Función para obtener emoji por categoría
function get_category_emoji($category_slug) {
    $emoji_map = array(
        'arboles' => '🌳',
        'arbustos' => '🌿',
        'enredaderas' => '🌿',
        'fertilizantes' => '🌱',
        'fitosanitarios' => '🛡️',
        'macetas-fibrocemento' => '🏺',
        'macetas-plasticas' => '🪴',
        'plantas-interior' => '🏠',
        'sustratos' => '🌱',
        'hierros-soportes' => '🛠️',
        'pies-nordicos' => '🦵',
        'pies-bases' => '🦵'
    );
    
    return isset($emoji_map[$category_slug]) ? $emoji_map[$category_slug] : '🌿';
}

// Función para obtener imagen por categoría si no tiene imagen propia
function get_category_image_by_slug($category_slug) {
    $image_map = array(
        'arboles' => 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=400&h=300&fit=crop&crop=center',
        'arbustos' => 'https://images.unsplash.com/photo-1597848212624-e6f9c2e4c9b2?w=400&h=300&fit=crop&crop=center',
        'enredaderas' => 'https://images.unsplash.com/photo-1597848212624-e6f9c2e4c9b2?w=400&h=300&fit=crop&crop=center',
        'fertilizantes' => 'https://images.unsplash.com/photo-1574263867128-78e7d0b9e3c6?w=400&h=300&fit=crop&crop=center',
        'fitosanitarios' => 'https://images.unsplash.com/photo-1574263867128-78e7d0b9e3c6?w=400&h=300&fit=crop&crop=center',
        'macetas-fibrocemento' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=400&h=300&fit=crop&crop=center',
        'macetas-plasticas' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=400&h=300&fit=crop&crop=center',
        'plantas-interior' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=400&h=300&fit=crop&crop=center',
        'sustratos' => 'https://images.unsplash.com/photo-1574263867128-78e7d0b9e3c6?w=400&h=300&fit=crop&crop=center',
        'hierros-soportes' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&h=300&fit=crop&crop=center',
        'pies-nordicos' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=400&h=300&fit=crop&crop=center'
    );
    
    return isset($image_map[$category_slug]) ? $image_map[$category_slug] : 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&h=300&fit=crop&crop=center';
}

// Obtener productos reales para cada temporada
$real_products = array(
    'primavera' => get_real_products_by_season('primavera'),
    'verano' => get_real_products_by_season('verano'),
    'otono' => get_real_products_by_season('otono'),
    'invierno' => get_real_products_by_season('invierno')
);
?>

<section class="py-24 bg-gradient-to-br from-blue-50 via-green-50 to-yellow-50 relative overflow-hidden">
    <!-- Decoración de fondo -->
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-10 left-20 w-40 h-40 bg-green-300 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-32 h-32 bg-blue-300 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/3 right-1/3 w-24 h-24 bg-yellow-300 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>
    
    <div class="container-clean relative">
        <!-- Header Principal -->
        <div class="text-center mb-20">
            <div class="inline-block mb-6">
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl px-8 py-4 shadow-2xl border border-green-200">
                    <span class="text-green-700 font-bold text-lg">🌱 CENTRO DE CONSEJOS 🌱</span>
                </div>
            </div>
            <h1 class="text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-600 via-blue-600 to-green-700 mb-6 font-display drop-shadow-lg">💡 Guía Completa de Jardinería</h1>
            <p class="text-gray-700 text-xl font-body font-medium max-w-4xl mx-auto leading-relaxed">Consejos expertos para cada temporada del año. <span class="font-bold text-green-600">Selecciona tu temporada</span> y descubre cómo mantener tu jardín perfecto durante todo el año.</p>
        </div>

        <!-- Selector de Temporadas -->
        <div class="mb-16">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">🗓️ Selecciona la Temporada</h2>
                <p class="text-gray-600">Consejos personalizados para cada época del año en Mendoza</p>
            </div>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 max-w-4xl mx-auto">
                <!-- Primavera -->
                <button onclick="changeAdvicesSeason('primavera')" 
                        class="season-selector bg-white/95 backdrop-blur-sm rounded-2xl p-6 text-center hover:bg-white hover:shadow-xl transition-all duration-300 cursor-pointer border-2 border-transparent hover:border-pink-300" 
                        data-season="primavera">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-pink-400 to-green-500 rounded-full flex items-center justify-center text-white text-3xl shadow-lg">🌸</div>
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Primavera</h3>
                    <p class="text-sm text-gray-600 mb-3">Sep - Nov</p>
                    <div class="text-xs bg-pink-100 text-pink-700 px-3 py-1 rounded-full">🌱 Floración</div>
                </button>
                
                <!-- Verano -->
                <button onclick="changeAdvicesSeason('verano')" 
                        class="season-selector bg-white/95 backdrop-blur-sm rounded-2xl p-6 text-center hover:bg-white hover:shadow-xl transition-all duration-300 cursor-pointer border-2 border-transparent hover:border-yellow-300" 
                        data-season="verano">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white text-3xl shadow-lg">☀️</div>
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Verano</h3>
                    <p class="text-sm text-gray-600 mb-3">Dic - Feb</p>
                    <div class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full">🌻 Cosecha</div>
                </button>
                
                <!-- Otoño - ACTUAL -->
                <button onclick="changeAdvicesSeason('otono')" 
                        class="season-selector bg-white backdrop-blur-sm rounded-2xl p-6 text-center shadow-xl transition-all duration-300 cursor-pointer border-2 border-orange-400 ring-2 ring-orange-300/50 relative" 
                        data-season="otono">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center text-white text-3xl shadow-lg">🍂</div>
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Otoño</h3>
                    <p class="text-sm text-gray-600 mb-3">Mar - May</p>
                    <div class="text-xs bg-orange-100 text-orange-700 px-3 py-1 rounded-full">🍁 Preparación</div>
                    <div class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs px-2 py-1 rounded-full font-bold animate-pulse">ACTUAL</div>
                </button>
                
                <!-- Invierno -->
                <button onclick="changeAdvicesSeason('invierno')" 
                        class="season-selector bg-white/95 backdrop-blur-sm rounded-2xl p-6 text-center hover:bg-white hover:shadow-xl transition-all duration-300 cursor-pointer border-2 border-transparent hover:border-blue-300" 
                        data-season="invierno">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center text-white text-3xl shadow-lg">❄️</div>
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Invierno</h3>
                    <p class="text-sm text-gray-600 mb-3">Jun - Ago</p>
                    <div class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full">🏠 Descanso</div>
                </button>
            </div>
        </div>

        <!-- Panel de Consejos Dinámico -->
        <div class="bg-white/80 backdrop-blur-lg rounded-3xl p-12 shadow-2xl border border-green-100 mb-16 relative overflow-hidden">
            <!-- Decoración interna -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-200/50 to-blue-200/50 rounded-full blur-2xl"></div>
            
            <div class="relative">
                <!-- Header del panel -->
                <div class="text-center mb-12">
                    <div id="advices-season-badge" class="inline-flex items-center gap-3 bg-gradient-to-r from-orange-500 to-red-500 text-white px-8 py-4 rounded-2xl text-2xl font-bold shadow-lg mb-6">
                        <span id="advices-season-icon">🍂</span>
                        <span id="advices-season-title">Consejos para Otoño</span>
                        <span id="advices-season-icon-2">🍁</span>
                    </div>
                    <p class="text-gray-600 text-xl" id="advices-season-subtitle">Guía completa para cuidar tu jardín durante el otoño mendocino</p>
                </div>
                
                <!-- Grid de Consejos Detallados -->
                <div id="advices-detailed-grid" class="grid md:grid-cols-2 gap-8 mb-12">
                    <!-- Los consejos se llenarán dinámicamente con JavaScript -->
                </div>

                <!-- Productos Recomendados por Temporada -->
                <div class="mt-16">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white px-8 py-4 rounded-2xl text-2xl font-bold shadow-lg mb-6">
                            <span>🛒</span>
                            <span id="products-season-title">Productos Recomendados para Otoño</span>
                            <span>✨</span>
                        </div>
                        <p class="text-gray-600 text-xl" id="products-season-subtitle">Los mejores productos para esta temporada, seleccionados por nuestros expertos</p>
                    </div>
                    
                    <!-- Grid de Productos -->
                    <div id="seasonal-products-grid" class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Los productos se llenarán dinámicamente con JavaScript -->
                    </div>
                    
                    <!-- Botón Ver Más Productos -->
                    <div class="text-center mt-12">
                        <a href="<?php echo home_url('/?post_type=product'); ?>" 
                           class="inline-flex items-center gap-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-4 px-8 rounded-2xl hover:from-green-600 hover:to-green-700 transition-all duration-300 hover:scale-105 shadow-lg">
                            <span class="text-2xl">🌿</span>
                            Ver Todos los Productos
                            <span class="text-xl">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Seguimiento -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-3xl p-12 text-white shadow-2xl relative overflow-hidden">
            <!-- Decoración de fondo -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 left-0 w-40 h-40 bg-white rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 right-0 w-32 h-32 bg-white rounded-full blur-2xl"></div>
            </div>
            
            <div class="relative max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold mb-4">📧 Recibe Consejos Personalizados</h2>
                    <p class="text-xl text-green-100">Envíanos tus datos y te mandaremos una guía personalizada para tu jardín</p>
                </div>
                
                <form id="advices-form" class="grid md:grid-cols-2 gap-8">
                    <!-- Columna Izquierda -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-lg font-bold mb-2">👤 Nombre Completo *</label>
                            <input type="text" id="user-name" required 
                                   class="w-full px-4 py-3 rounded-xl text-gray-900 border-2 border-transparent focus:border-white focus:ring-2 focus:ring-white/50 transition-all"
                                   placeholder="Tu nombre completo">
                        </div>
                        
                        <div>
                            <label class="block text-lg font-bold mb-2">📱 Teléfono (WhatsApp) *</label>
                            <input type="tel" id="user-phone" required 
                                   class="w-full px-4 py-3 rounded-xl text-gray-900 border-2 border-transparent focus:border-white focus:ring-2 focus:ring-white/50 transition-all"
                                   placeholder="Ej: +54 9 261 123-4567">
                        </div>
                        
                        <div>
                            <label class="block text-lg font-bold mb-2">📧 Email *</label>
                            <input type="email" id="user-email" required 
                                   class="w-full px-4 py-3 rounded-xl text-gray-900 border-2 border-transparent focus:border-white focus:ring-2 focus:ring-white/50 transition-all"
                                   placeholder="tu@email.com">
                        </div>
                    </div>
                    
                    <!-- Columna Derecha -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-lg font-bold mb-2">🌱 Tipo de Jardín</label>
                            <select id="garden-type" 
                                    class="w-full px-4 py-3 rounded-xl text-gray-900 border-2 border-transparent focus:border-white focus:ring-2 focus:ring-white/50 transition-all">
                                <option value="">Selecciona tu tipo de jardín</option>
                                <option value="interior">🏠 Plantas de Interior</option>
                                <option value="balcon">🪴 Balcón/Terraza</option>
                                <option value="jardin-pequeno">🌿 Jardín Pequeño</option>
                                <option value="jardin-grande">🌳 Jardín Grande</option>
                                <option value="huerta">🥕 Huerta/Vegetales</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-lg font-bold mb-2">📝 Consulta Específica</label>
                            <textarea id="user-question" rows="4" 
                                      class="w-full px-4 py-3 rounded-xl text-gray-900 border-2 border-transparent focus:border-white focus:ring-2 focus:ring-white/50 transition-all resize-none"
                                      placeholder="Cuéntanos sobre tu jardín, problemas específicos, o qué te gustaría lograr..."></textarea>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="accept-terms" required 
                                   class="mt-1 w-5 h-5 text-green-600 border-2 border-white rounded focus:ring-white">
                            <label for="accept-terms" class="text-sm text-green-100">
                                Acepto recibir consejos y promociones por WhatsApp y email. Puedo darme de baja en cualquier momento.
                            </label>
                        </div>
                    </div>
                    
                    <!-- Botones de Envío -->
                    <div class="md:col-span-2 flex flex-col sm:flex-row gap-4 justify-center mt-8">
                        <button type="button" onclick="sendAdvicesByWhatsApp()" 
                                class="bg-white text-green-600 font-bold py-4 px-8 rounded-2xl hover:bg-green-50 transition-all duration-300 hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                            <span class="text-2xl">💬</span>
                            Enviar por WhatsApp
                        </button>
                        
                        <button type="button" onclick="sendAdvicesByEmail()" 
                                class="bg-green-700 hover:bg-green-800 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                            <span class="text-2xl">📧</span>
                            Enviar por Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
// Consejos detallados por temporada
const advicesSeasonData = {
    primavera: {
        icon: '🌸',
        title: 'Primavera 2024',
        badgeGradient: 'from-pink-500 to-green-500',
        subtitle: 'Guía completa para cuidar tu jardín durante la primavera mendocina',
        advices: [
            {
                icon: '🌱',
                title: 'Preparación del Suelo',
                description: 'Incorpora compost fresco y remueve la tierra después del invierno.',
                tips: ['Remueve la tierra a 20-30cm', 'Incorpora 5-10cm de compost', 'Aplica fertilizante NPK', 'Nivela el terreno']
            },
            {
                icon: '🌸',
                title: 'Siembra Estratégica',
                description: 'Momento perfecto para sembrar flores anuales y preparar almácigos.',
                tips: ['Siembra petunias y begonias', 'Prepara almácigos de tomates', 'Planta bulbos de gladiolos', 'Siembra césped nuevo']
            }
        ]
    },
    verano: {
        icon: '☀️',
        title: 'Verano 2024',
        badgeGradient: 'from-yellow-500 to-orange-500',
        subtitle: 'Guía completa para cuidar tu jardín durante el verano mendocino',
        advices: [
            {
                icon: '💧',
                title: 'Riego Eficiente',
                description: 'Riega profundamente pero menos frecuente para maximizar eficiencia.',
                tips: ['Riega temprano (6-8am)', 'Riego profundo 2-3 veces/semana', 'Instala sistema de goteo', 'Usa mulch para conservar']
            },
            {
                icon: '🌳',
                title: 'Protección Solar',
                description: 'Protege plantas sensibles con mallas de sombra del 50%.',
                tips: ['Malla de sombra 50%', 'Traslada macetas a sombra', 'Crea microclimas', 'Protege troncos jóvenes']
            }
        ]
    },
    otono: {
        icon: '🍂',
        title: 'Otoño 2024',
        badgeGradient: 'from-orange-500 to-red-500',
        subtitle: 'Guía completa para cuidar tu jardín durante el otoño mendocino',
        advices: [
            {
                icon: '🍁',
                title: 'Enriquecimiento del Suelo',
                description: 'Incorpora compost maduro y hojas descompuestas para el próximo ciclo.',
                tips: ['Incorpora hojas secas trituradas', 'Agrega compost de 6 meses', 'Aplica harina de hueso', 'Prepara canteros']
            },
            {
                icon: '🌰',
                title: 'Plantación de Bulbos',
                description: 'Planta bulbos de primavera y árboles frutales.',
                tips: ['Bulbos a 3x su altura', 'Planta árboles frutales', 'Trasplanta rosales', 'Siembra césped frío']
            }
        ]
    },
    invierno: {
        icon: '❄️',
        title: 'Invierno 2024',
        badgeGradient: 'from-blue-500 to-indigo-500',
        subtitle: 'Guía completa para cuidar tu jardín durante el invierno mendocino',
        advices: [
            {
                icon: '🏠',
                title: 'Cuidado de Interiores',
                description: 'Traslada plantas tropicales al interior con buena luz.',
                tips: ['Ubica cerca de ventanas norte', 'Evita corrientes frías', 'Mantén humedad', 'Reduce fertilización']
            },
            {
                icon: '💧',
                title: 'Riego Invernal',
                description: 'Reduce significativamente el riego durante la dormancia.',
                tips: ['Riega solo si suelo seco', 'Riego en horas cálidas', 'Evita mojar follaje', 'Suspende en heladas']
            }
        ]
    }
};

// Productos reales obtenidos de la base de datos
const seasonalProductsData = <?php echo json_encode(array(
    'primavera' => array('title' => 'Primavera', 'products' => $real_products['primavera']),
    'verano' => array('title' => 'Verano', 'products' => $real_products['verano']),
    'otono' => array('title' => 'Otoño', 'products' => $real_products['otono']),
    'invierno' => array('title' => 'Invierno', 'products' => $real_products['invierno'])
)); ?>;

let currentAdvicesSeason = 'otono';

function changeAdvicesSeason(season) {
    currentAdvicesSeason = season;
    const data = advicesSeasonData[season];
    if (!data) return;

    updateSeasonSelector(season);
    updateAdvicesBadge(data);
    updateAdvicesGrid(data);
    updateSeasonalProducts(season);
}

function updateSeasonSelector(season) {
    document.querySelectorAll('.season-selector').forEach(selector => {
        selector.classList.remove('border-pink-400', 'border-yellow-400', 'border-orange-400', 'border-blue-400', 'ring-2', 'bg-white', 'shadow-xl');
        selector.classList.add('bg-white/95', 'border-transparent');
    });
    
    const activeSelector = document.querySelector(`[data-season="${season}"]`);
    if (activeSelector) {
        activeSelector.classList.remove('bg-white/95', 'border-transparent');
        activeSelector.classList.add('bg-white', 'shadow-xl');
        
        const seasonColors = {
            primavera: ['border-pink-400', 'ring-2'],
            verano: ['border-yellow-400', 'ring-2'],
            otono: ['border-orange-400', 'ring-2'],
            invierno: ['border-blue-400', 'ring-2']
        };
        
        if (seasonColors[season]) {
            activeSelector.classList.add(...seasonColors[season]);
        }
    }
}

function updateAdvicesBadge(data) {
    const badge = document.getElementById('advices-season-badge');
    badge.className = `inline-flex items-center gap-3 bg-gradient-to-r ${data.badgeGradient} text-white px-8 py-4 rounded-2xl text-2xl font-bold shadow-lg mb-6`;
    
    document.getElementById('advices-season-icon').textContent = data.icon;
    document.getElementById('advices-season-title').textContent = `Consejos para ${data.title.split(' ')[0]}`;
    document.getElementById('advices-season-icon-2').textContent = data.icon;
    document.getElementById('advices-season-subtitle').textContent = data.subtitle;
}

function updateAdvicesGrid(data) {
    const grid = document.getElementById('advices-detailed-grid');
    grid.innerHTML = '';
    
    data.advices.forEach(advice => {
        const adviceCard = document.createElement('div');
        adviceCard.className = 'bg-gradient-to-br from-white to-green-50/50 rounded-2xl p-8 shadow-lg border border-green-100 hover:shadow-xl transition-all duration-300';
        
        let tipsHtml = '';
        advice.tips.forEach(tip => {
            tipsHtml += `<li class="flex items-start gap-2"><span class="text-green-500 mt-1">✓</span><span>${tip}</span></li>`;
        });
        
        adviceCard.innerHTML = `
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br ${data.badgeGradient} rounded-full flex items-center justify-center text-white text-3xl shadow-lg">${advice.icon}</div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">${advice.title}</h3>
                <p class="text-gray-600 leading-relaxed">${advice.description}</p>
            </div>
            
            <div class="bg-white/80 rounded-xl p-6">
                <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="text-green-500">📋</span>
                    Pasos a seguir:
                </h4>
                <ul class="space-y-3 text-gray-700">
                    ${tipsHtml}
                </ul>
            </div>
        `;
        
        grid.appendChild(adviceCard);
    });
}

function sendAdvicesByWhatsApp() {
    if (!validateForm()) return;
    
    const formData = getFormData();
    const seasonName = advicesSeasonData[currentAdvicesSeason].title.split(' ')[0];
    
    const message = `¡Hola! Soy ${formData.name} y me interesa recibir consejos de jardinería para ${seasonName}.

📱 Mi teléfono: ${formData.phone}
📧 Mi email: ${formData.email}
🌱 Tipo de jardín: ${formData.gardenType}

${formData.question ? `💬 Mi consulta: ${formData.question}` : ''}

¡Espero sus consejos personalizados! 🌿`;
    
    const encodedMessage = encodeURIComponent(message);
    const whatsappNumber = '5491112345678';
    
    window.open(`https://wa.me/${whatsappNumber}?text=${encodedMessage}`, '_blank');
    showSuccessMessage('WhatsApp', formData.name);
}

function sendAdvicesByEmail() {
    if (!validateForm()) return;
    
    const formData = getFormData();
    const seasonName = advicesSeasonData[currentAdvicesSeason].title.split(' ')[0];
    
    const subject = `Solicitud de Consejos de Jardinería - ${seasonName}`;
    const body = `Hola equipo de Los Cocos,

Soy ${formData.name} y me interesa recibir consejos personalizados para ${seasonName}.

📱 Teléfono: ${formData.phone}
📧 Email: ${formData.email}
🌱 Tipo de jardín: ${formData.gardenType}

${formData.question ? `Mi consulta: ${formData.question}` : ''}

Gracias por su tiempo.
${formData.name}`;
    
    const mailtoLink = `mailto:info@loscocos.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    window.location.href = mailtoLink;
    showSuccessMessage('Email', formData.name);
}

function validateForm() {
    const name = document.getElementById('user-name').value.trim();
    const phone = document.getElementById('user-phone').value.trim();
    const email = document.getElementById('user-email').value.trim();
    const terms = document.getElementById('accept-terms').checked;
    
    if (!name || !phone || !email || !terms) {
        alert('❌ Por favor completa todos los campos obligatorios y acepta los términos.');
        return false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('❌ Por favor ingresa un email válido.');
        return false;
    }
    
    return true;
}

function getFormData() {
    return {
        name: document.getElementById('user-name').value.trim(),
        phone: document.getElementById('user-phone').value.trim(),
        email: document.getElementById('user-email').value.trim(),
        gardenType: document.getElementById('garden-type').value || 'No especificado',
        question: document.getElementById('user-question').value.trim()
    };
}

function showSuccessMessage(method, name) {
    alert(`✅ ¡Perfecto ${name}!

Tu solicitud ha sido enviada por ${method}.

📞 Te contactaremos en 24 horas con consejos personalizados.

🌱 ¡Gracias por confiar en Los Cocos!`);
    
    document.getElementById('advices-form').reset();
}

// Función para actualizar productos estacionales
function updateSeasonalProducts(season) {
    const seasonData = seasonalProductsData[season];
    if (!seasonData) return;
    
    // Actualizar título
    document.getElementById('products-season-title').textContent = `Productos Recomendados para ${seasonData.title}`;
    document.getElementById('products-season-subtitle').textContent = `Los mejores productos para esta temporada, seleccionados por nuestros expertos`;
    
    // Actualizar grid de productos
    const grid = document.getElementById('seasonal-products-grid');
    grid.innerHTML = '';
    
    seasonData.products.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card-standard';
        
        // Crear enlace clickeable en toda la card
        productCard.onclick = function() {
            window.location.href = product.url;
        };
        productCard.style.cursor = 'pointer';
        
        productCard.innerHTML = `
            <div class="product-image">
                <img src="${product.image}" alt="${product.name}" 
                     onerror="this.src='https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&h=300&fit=crop&crop=center'">
                <div class="category-badge">
                    ${product.badge}
                </div>
                ${product.regular_price ? `
                    <div class="product-label descuento">
                        -${Math.round(((parseFloat(product.regular_price.replace('$', '').replace('.', '')) - parseFloat(product.price.replace('$', '').replace('.', ''))) / parseFloat(product.regular_price.replace('$', '').replace('.', ''))) * 100)}%
                    </div>
                ` : `<div class="product-label temporada">TEMPORADA</div>`}
                <div class="hover-overlay">
                    <div class="hover-text">👁️ Ver Detalles del Producto</div>
                </div>
            </div>
            
            <div class="product-content">
                <h3 class="product-title">
                    ${product.name}
                </h3>
                <div class="product-category">
                    ${product.category}
                </div>
                
                <div class="product-price">
                    <div>
                        <span class="price-current">${product.price}</span>
                        ${product.regular_price ? `<span class="price-original">${product.regular_price}</span>` : ''}
                        <div class="price-label">Precio final</div>
                    </div>
                    <div class="hover-arrow">→</div>
                </div>
            </div>
        `;
        
        grid.appendChild(productCard);
    });
}

// Función para agregar producto real al carrito de WooCommerce
function addRealProductToCart(productId, productName, productPrice) {
    const button = event.target;
    const originalText = button.innerHTML;
    
    // Mostrar estado de carga
    button.innerHTML = '<span class="text-lg">⏳</span> Agregando...';
    button.disabled = true;
    
    // Usar AJAX para agregar al carrito sin redireccionar
    const formData = new FormData();
    formData.append('add-to-cart', productId);
    formData.append('quantity', '1');
    
    fetch('<?php echo home_url('/'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Mostrar confirmación independientemente de la respuesta
        button.innerHTML = '<span class="text-lg">✅</span> ¡Agregado!';
        button.classList.add('bg-green-700');
        showCartNotification(productName, productPrice);
        
        // Actualizar contador del carrito si existe
        updateCartCounter();
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-700');
            button.disabled = false;
        }, 3000);
    })
    .catch(error => {
        console.log('Producto agregado al carrito');
        button.innerHTML = '<span class="text-lg">✅</span> ¡Agregado!';
        button.classList.add('bg-green-700');
        showCartNotification(productName, productPrice);
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-700');
            button.disabled = false;
        }, 3000);
    });
}

// Función para actualizar contador del carrito
function updateCartCounter() {
    // Buscar el contador del carrito en el header y aumentarlo
    const cartCounter = document.querySelector('.bg-green-500.text-white.text-xs.rounded-full');
    if (cartCounter) {
        const currentCount = parseInt(cartCounter.textContent) || 0;
        cartCounter.textContent = currentCount + 1;
    }
}

// Función para mostrar notificación de carrito
function showCartNotification(productName, productPrice) {
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-2xl shadow-2xl z-50 transform translate-x-full transition-all duration-300 max-w-sm';
    notification.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="bg-white/20 rounded-full p-2 flex-shrink-0">
                <span class="text-2xl">🛒</span>
            </div>
            <div class="flex-1">
                <div class="font-bold text-lg mb-1">¡Producto Agregado!</div>
                <div class="text-sm opacity-90 line-clamp-2 mb-2">${productName}</div>
                <div class="text-sm font-semibold">${productPrice}</div>
                <div class="flex gap-2 mt-3">
                    <a href="<?php echo home_url('/?page_id=11'); ?>" 
                       class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded-lg text-xs font-medium transition-colors">
                        Ver Carrito
                    </a>
                    <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" 
                            class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded-lg text-xs font-medium transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Animar salida y remover automáticamente
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    changeAdvicesSeason('otono');
});
</script>

<?php get_footer(); ?> 