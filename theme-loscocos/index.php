<?php get_header(); ?>

<?php if (is_home() || is_front_page() || (!isset($_GET['post_type']) && !is_search() && !is_archive())) : ?>

<section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-20 pb-24">
    <!-- Fondo Dinámico según Temporada -->
    <div class="absolute inset-0 z-0">
        <img id="hero-background" 
             src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80" 
             alt="Vivero Los Cocos" 
             class="w-full h-full object-cover">
        <div id="hero-overlay" class="absolute inset-0 bg-gradient-to-r from-orange-900/85 via-red-800/75 to-amber-900/85"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
    </div>

    <!-- Contenido Principal -->
    <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Indicador de Temporada Actual -->
        <div class="text-center mb-12">
            <div id="season-indicator" class="inline-flex items-center gap-3 bg-gradient-to-r from-orange-500/90 to-red-500/90 backdrop-blur-md text-white px-8 py-4 rounded-2xl shadow-2xl">
                <span class="text-2xl" id="season-icon-left">🍂</span>
                <div class="text-left">
                    <div class="text-sm font-medium opacity-90">Temporada Actual</div>
                    <div class="text-xl font-bold" id="current-season">Otoño 2024</div>
                </div>
                <span class="text-2xl" id="season-icon-right">🍁</span>
            </div>
            
            <!-- Selector de Temporadas para Testing -->
            <div class="mt-6">
                <p class="text-white/80 text-sm mb-3">🧪 Modo Testing - Cambiar Temporada:</p>
                <div class="flex justify-center gap-3 flex-wrap">
                    <button onclick="changeSeason('primavera')" 
                            class="bg-white/20 hover:bg-white/30 backdrop-blur-md text-white px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 hover:scale-105">
                        🌸 Primavera
                    </button>
                    <button onclick="changeSeason('verano')" 
                            class="bg-white/20 hover:bg-white/30 backdrop-blur-md text-white px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 hover:scale-105">
                        ☀️ Verano
                    </button>
                    <button onclick="changeSeason('otono')" 
                            class="bg-white/20 hover:bg-white/30 backdrop-blur-md text-white px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 hover:scale-105 ring-2 ring-white/50">
                        🍂 Otoño
                    </button>
                    <button onclick="changeSeason('invierno')" 
                            class="bg-white/20 hover:bg-white/30 backdrop-blur-md text-white px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 hover:scale-105">
                        ❄️ Invierno
                    </button>
                </div>
            </div>
        </div>

        <!-- Título Principal Dinámico -->
        <div class="text-center mb-12">
            <h1 class="text-6xl sm:text-7xl md:text-8xl font-black text-white mb-6 tracking-tight">
                <span id="hero-title" class="bg-gradient-to-r from-orange-300 via-amber-200 to-red-300 bg-clip-text text-transparent drop-shadow-2xl">
                    Los Cocos
                </span>
            </h1>
            <div id="hero-divider" class="w-40 h-3 bg-gradient-to-r from-orange-500 to-red-500 mx-auto rounded-full animate-pulse shadow-lg"></div>
            <p class="text-2xl sm:text-3xl md:text-4xl text-orange-100 font-light mt-8 max-w-5xl mx-auto leading-relaxed drop-shadow-lg">
                Tu <span id="hero-subtitle" class="font-bold text-white">refugio otoñal</span> en el corazón de Mendoza
                <br class="hidden sm:block">
                <span id="hero-description" class="text-amber-200 text-xl">Prepara tu jardín para el otoño • Plantas de temporada • Asesoramiento especializado</span>
            </p>
        </div>

        <!-- Oferta del Día -->
        <div class="text-center mb-12">
            <button id="offer-button" 
                    class="btn-clean btn-primary transition-all duration-300 group max-w-md mx-auto flex items-center gap-4">
                <span id="offer-icon-left" class="text-2xl">🔥</span>
                <div class="text-left">
                    <div id="offer-title" class="font-display font-bold">OFERTA ESPECIAL DE OTOÑO</div>
                    <div id="offer-text" class="font-body text-white/90">¡Pack Otoñal 50% OFF!</div>
                </div>
                <span id="offer-icon-right" class="text-2xl">🍂</span>
            </button>
        </div>
    </div>
</section>

<!-- Sección de Productos Premium -->
<section class="py-24 bg-gradient-to-br from-green-50 via-white to-blue-50 relative overflow-hidden">
    <!-- Decoración de fondo -->
    <div class="absolute inset-0 opacity-30">
        <div class="absolute top-20 left-10 w-32 h-32 bg-green-200 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-40 h-40 bg-blue-200 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-60 h-60 bg-yellow-200 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container-clean relative">
        <!-- Header Premium -->
        <div class="text-center mb-20">
            <div class="inline-block mb-6">
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl px-8 py-4 shadow-2xl border border-green-100">
                    <span class="text-green-600 font-bold text-lg">✨ PRODUCTOS DESTACADOS ✨</span>
                </div>
            </div>
            <h2 class="text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-600 via-green-500 to-green-700 mb-6 font-display drop-shadow-lg">🌟 Lo Mejor de Nuestro Vivero</h2>
            <p class="text-gray-700 text-xl font-body font-medium max-w-3xl mx-auto leading-relaxed">Descubre una selección cuidadosamente curada de <span class="font-bold text-green-600">plantas premium</span> que transformarán tu espacio en un paraíso verde</p>
        </div>

        <!-- Grid de Productos Dinámicos Mejorado -->
        <div class="products-grid-featured">
            <?php 
            // Obtener productos reales de WooCommerce con nuestro sistema de imágenes
            if (class_exists('WooCommerce')) {
                $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => 8,
                    'post_status'    => 'publish',
                    'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
                );
                
                $featured_products = get_posts($args);
                if (empty($featured_products)) {
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => 8,
                        'post_status' => 'publish',
                        'orderby' => 'date',
                        'order' => 'DESC'
                    );
                    $featured_products = get_posts($args);
                }

                if ($featured_products) {
                    // Función para obtener emoji por categoría
                    function get_home_category_emoji($product_id) {
                        $product_categories = wp_get_post_terms($product_id, 'product_cat');
                        
                        $category_emojis = array(
                            'plantas-de-interior' => '🪴',
                            'arbustos' => '🌿',
                            'arboles' => '🌳',
                            'enredaderas' => '🌱',
                            'macetas-plasticas' => '🏺',
                            'macetas-fibrocemento' => '🏺',
                            'sustratos-y-tierras' => '🌱',
                            'fertilizantes' => '🧪',
                            'fitosanitarios' => '💊',
                            'hierros-soportes' => '🔧',
                            'pies-nordicos' => '❄️'
                        );
                        
                        if ($product_categories && !is_wp_error($product_categories)) {
                            foreach ($product_categories as $category) {
                                $category_slug = $category->slug;
                                if (isset($category_emojis[$category_slug])) {
                                    return $category_emojis[$category_slug];
                                }
                            }
                        }
                        
                        return '🌿'; // Emoji por defecto
                    }

                    // Función para obtener la imagen destacada del producto
                    function get_home_category_image($product_id) {
                        $product = wc_get_product($product_id);
                        if (!$product) {
                            return wc_placeholder_img_src('woocommerce_thumbnail');
                        }
                        
                        // Obtener la imagen destacada
                        $image_id = $product->get_image_id();
                        if ($image_id) {
                            return wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail');
                        }
                        
                        // Si no hay imagen destacada, obtener la primera imagen de la galería
                        $gallery_ids = $product->get_gallery_image_ids();
                        if (!empty($gallery_ids)) {
                            return wp_get_attachment_image_url($gallery_ids[0], 'woocommerce_thumbnail');
                        }
                        
                        // Si no hay imágenes, devolver placeholder
                        return wc_placeholder_img_src('woocommerce_thumbnail');
                    }

                    // Mostrar productos destacados usando el template part
                    $count = 0;
                    foreach ($featured_products as $product_post) {
                        if ($count >= 8) break;
                        
                        $product = wc_get_product($product_post->ID);
                        if ($product) {
                            $count++;
                            setup_postdata($GLOBALS['post'] =& $product_post);
                            
                            // Cargar el template part
                            get_template_part('template-parts/content', 'product-card');
                            
                            wp_reset_postdata();
                        }
                    }
                } else {
                    // Productos de ejemplo si no hay productos reales
                    echo '<div class="col-span-full text-center py-16">';
                    echo '<div class="bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg border border-green-100 max-w-md mx-auto">';
                    echo '<div class="text-6xl mb-4">🌱</div>';
                    echo '<h3 class="text-2xl font-bold text-gray-800 mb-4">Próximamente</h3>';
                    echo '<p class="text-gray-600">Estamos preparando nuestros productos destacados</p>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>

        <!-- Sección de Categorías Destacadas -->
        <div class="bg-white/70 backdrop-blur-lg rounded-3xl p-12 shadow-2xl border border-green-100 mb-16">
            <div class="text-center mb-12">
                <h3 class="text-4xl font-bold text-gray-800 mb-4">🌿 Explora por Categorías</h3>
                <p class="text-gray-600 text-lg">Encuentra exactamente lo que necesitas para tu jardín</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                <?php
                // Obtener categorías principales
                $product_categories = get_terms('product_cat', array('hide_empty' => true, 'number' => 6));
                if ($product_categories && !is_wp_error($product_categories)) {
                    $category_emojis = array(
                        'plantas-de-interior' => '🪴',
                        'arbustos' => '🌿',
                        'arboles' => '🌳',
                        'enredaderas' => '🌱',
                        'macetas-plasticas' => '🏺',
                        'macetas-fibrocemento' => '🏺',
                        'sustratos-y-tierras' => '🌱',
                        'fertilizantes' => '🧪',
                        'fitosanitarios' => '💊',
                        'hierros-soportes' => '🔧',
                        'pies-nordicos' => '❄️'
                    );
                    
                    foreach ($product_categories as $category) {
                        $emoji = isset($category_emojis[$category->slug]) ? $category_emojis[$category->slug] : '🌿';
                        $category_url = home_url('/?post_type=product&product_cat=' . $category->slug);
                        
                        echo '<a href="' . $category_url . '" class="group">';
                        echo '<div class="bg-white/80 hover:bg-white rounded-2xl p-6 text-center transition-all duration-300 hover:scale-105 hover:shadow-lg border border-green-100 hover:border-green-300">';
                        echo '<div class="text-4xl mb-3 group-hover:scale-110 transition-transform duration-300">' . $emoji . '</div>';
                        echo '<h4 class="font-bold text-gray-800 text-sm mb-2 group-hover:text-green-600 transition-colors">' . $category->name . '</h4>';
                        echo '<p class="text-xs text-gray-500">(' . $category->count . ' productos)</p>';
                        echo '</div>';
                        echo '</a>';
                    }
                }
                ?>
            </div>
        </div>

        <!-- Call to Action Premium -->
        <div class="text-center">
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-3xl p-12 shadow-2xl text-white relative overflow-hidden">
                <!-- Decoración de fondo -->
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute top-0 left-0 w-32 h-32 bg-white rounded-full blur-2xl"></div>
                    <div class="absolute bottom-0 right-0 w-40 h-40 bg-white rounded-full blur-2xl"></div>
                </div>
                
                <div class="relative">
                    <h3 class="text-4xl font-bold mb-4">🛒 ¿Listo para transformar tu jardín?</h3>
                    <p class="text-xl mb-8 text-green-100 max-w-2xl mx-auto">Explora nuestra tienda completa con más de 500 productos cuidadosamente seleccionados</p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <?php if (class_exists('WooCommerce')) : ?>
                            <a href="<?php echo home_url('/?post_type=product'); ?>" class="bg-white text-green-600 font-bold py-4 px-8 rounded-2xl hover:bg-green-50 transition-all duration-300 hover:scale-105 shadow-lg">
                                🌟 Ver Toda la Tienda
                            </a>
                        <?php endif; ?>
                        <button onclick="consultar('Información general del vivero')" class="bg-green-700 hover:bg-green-800 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 hover:scale-105 shadow-lg">
                            💬 Consultar por WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección de Consejos de Jardinería Estacional -->
<section class="py-24 bg-gradient-to-br from-blue-50 via-green-50 to-yellow-50 relative overflow-hidden" id="consejos">
    <!-- Decoración de fondo -->
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-10 left-20 w-40 h-40 bg-green-300 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-32 h-32 bg-blue-300 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/3 right-1/3 w-24 h-24 bg-yellow-300 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>
    
    <div class="container-clean relative">
        <!-- Header de Consejos -->
        <div class="text-center mb-20">
            <div class="inline-block mb-6">
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl px-8 py-4 shadow-2xl border border-green-200">
                    <span class="text-green-700 font-bold text-lg">🌱 CONSEJOS DE TEMPORADA 🌱</span>
                </div>
            </div>
            <h2 class="text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-600 via-blue-600 to-green-700 mb-6 font-display drop-shadow-lg">💡 Guía de Jardinería Estacional</h2>
            <p class="text-gray-700 text-xl font-body font-medium max-w-3xl mx-auto leading-relaxed">Consejos expertos que se adaptan a cada temporada para mantener tu jardín <span class="font-bold text-green-600">siempre perfecto</span></p>
        </div>

        <!-- Panel de Consejos Dinámico -->
        <div class="bg-white/80 backdrop-blur-lg rounded-3xl p-12 shadow-2xl border border-green-100 mb-16 relative overflow-hidden">
            <!-- Decoración interna -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-200/50 to-blue-200/50 rounded-full blur-2xl"></div>
            
            <div class="relative">
                <!-- Header del panel -->
                <div class="text-center mb-12">
                    <div id="tips-season-badge" class="inline-flex items-center gap-3 bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-3 rounded-2xl text-lg font-bold shadow-lg mb-6">
                        <span id="tips-season-icon">🍂</span>
                        <span id="tips-season-title">Consejos para Otoño</span>
                        <span id="tips-season-icon-2">🍁</span>
                    </div>
                    <p class="text-gray-600 text-lg" id="tips-season-subtitle">Guía completa para cuidar tu jardín durante el otoño mendocino</p>
                </div>
                
                <!-- Grid de Consejos -->
                <div id="tips-grid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    <!-- Los consejos se llenarán dinámicamente con JavaScript -->
                </div>
                
                <!-- Botón para ver más consejos -->
                <div class="text-center">
                    <button id="more-tips-button" onclick="openSeasonTipsModal()" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 hover:scale-105 shadow-lg">
                        <span id="more-tips-icon">🍂</span> Ver Consejos Completos
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección de Productos de Estación Recomendados -->
<section class="py-24 bg-gradient-to-br from-green-100 via-white to-orange-50 relative overflow-hidden">
    <!-- Decoración de fondo -->
    <div class="absolute inset-0 opacity-25">
        <div class="absolute top-20 left-10 w-36 h-36 bg-orange-200 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-20 w-48 h-48 bg-green-200 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container-clean relative">
        <!-- Header de Productos de Estación -->
        <div class="text-center mb-20">
            <div class="inline-block mb-6">
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl px-8 py-4 shadow-2xl border border-green-200">
                    <span class="text-green-700 font-bold text-lg">🌿 PRODUCTOS DE TEMPORADA 🌿</span>
                </div>
            </div>
            <h2 class="text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-600 via-green-500 to-green-700 mb-6 font-display drop-shadow-lg">🎯 Recomendados para <span id="season-products-title">Otoño</span></h2>
            <p class="text-gray-700 text-xl font-body font-medium max-w-3xl mx-auto leading-relaxed">Productos especialmente seleccionados para <span class="font-bold text-green-600" id="season-products-description">esta temporada otoñal</span> que te ayudarán a mantener tu jardín en perfecto estado</p>
        </div>

        <!-- Grid de Productos de Temporada -->
                    <div id="seasonal-products-grid" class="products-grid">
            <?php 
            // Obtener productos recomendados por temporada
            if (class_exists('WooCommerce')) {
                // Obtener productos aleatorios para productos de temporada
                $seasonal_args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 6,
                    'post_status' => 'publish',
                    'orderby' => 'rand'
                );
                
                $seasonal_products = get_posts($seasonal_args);
                
                if ($seasonal_products) {
                    $count = 0;
                    foreach ($seasonal_products as $product_post) {
                        $product = wc_get_product($product_post->ID);
                        if ($product && $count < 6) {
                            $count++;
                            $emoji = get_home_category_emoji($product_post->ID);
                            $product_url = home_url('/?product=' . $product_post->post_name);
                            
                            // Obtener imagen del producto
                            $image_url = get_home_category_image($product->get_id());
                            
                            // Determinar etiqueta del producto (más enfoque en temporada)
                            $product_label = 'TEMPORADA';
                            $label_class = 'temporada';
                            if ($product->is_on_sale()) {
                                $product_label = 'OFERTA';
                                $label_class = 'oferta';
                            } elseif (!$product->is_in_stock()) {
                                $product_label = 'AGOTADO';
                                $label_class = 'agotado';
                            }
                            
                            echo '<a href="' . $product_url . '" class="product-card-standard">';
                            
                            // Imagen del producto
                            echo '<div class="product-image">';
                            echo '<img src="' . $image_url . '" alt="' . $product->get_name() . '">';
                            
                            // Badge de categoría
                            echo '<div class="category-badge">' . $emoji . '</div>';
                            
                            // Etiqueta especial
                            echo '<div class="product-label ' . $label_class . '">' . $product_label . '</div>';
                            
                            // Overlay de hover
                            echo '<div class="hover-overlay">';
                            echo '<div class="hover-content">';
                            echo '<div class="hover-text">👁️ Ver Detalles</div>';
                            if ($product->is_in_stock()) {
                                echo '<button class="btn-add-to-cart-featured" data-product-id="' . $product->get_id() . '" onclick="event.preventDefault(); event.stopPropagation(); addToCartFeatured(' . $product->get_id() . ', \'' . esc_js($product->get_name()) . '\');">';
                                echo '🛒 Agregar al Carrito';
                                echo '</button>';
                            } else {
                                echo '<div class="btn-out-of-stock">Agotado</div>';
                            }
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            
                            // Contenido de la card
                            echo '<div class="product-content">';
                            echo '<h3 class="product-title">' . $product->get_name() . '</h3>';
                            
                            // Categoría
                            $categories = wp_get_post_terms($product->get_id(), 'product_cat');
                            if ($categories && !is_wp_error($categories)) {
                                echo '<div class="product-category">' . $categories[0]->name . '</div>';
                            }
                            
                            // Descripción corta
                            $short_description = $product->get_short_description();
                            if ($short_description) {
                                $short_desc = wp_trim_words($short_description, 12, '...');
                                echo '<div class="product-description">' . $short_desc . '</div>';
                            }
                            
                            // Precio con más detalles
                            echo '<div class="product-price">';
                            echo '<div class="price-container">';
                            if ($product->is_on_sale()) {
                                $discount = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
                                echo '<div class="discount-badge">-' . $discount . '%</div>';
                                echo '<span class="price-current">$' . number_format($product->get_sale_price(), 0, ',', '.') . '</span>';
                                echo '<span class="price-original">$' . number_format($product->get_regular_price(), 0, ',', '.') . '</span>';
                            } else {
                                echo '<span class="price-current">$' . number_format($product->get_price(), 0, ',', '.') . '</span>';
                            }
                            echo '<div class="price-label">Precio final</div>';
                            echo '</div>';
                            
                            // Stock status
                            if ($product->is_in_stock()) {
                                $stock_qty = $product->get_stock_quantity();
                                if ($stock_qty && $stock_qty <= 5) {
                                    echo '<div class="stock-warning">¡Últimas ' . $stock_qty . ' unidades!</div>';
                                } else {
                                    echo '<div class="stock-available">✅ Disponible</div>';
                                }
                            } else {
                                echo '<div class="stock-out">❌ Agotado</div>';
                            }
                            
                            echo '</div>'; // product-price
                            
                            echo '</div>'; // product-content
                            echo '</a>'; // product-card-standard
                        }
                    }
                }
            }
            ?>
        </div>

        <!-- Banner de Temporada -->
        <div class="bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 rounded-3xl p-12 text-white text-center shadow-2xl relative overflow-hidden">
            <!-- Decoración -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 left-0 w-40 h-40 bg-white rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 right-0 w-32 h-32 bg-white rounded-full blur-2xl"></div>
            </div>
            
            <div class="relative">
                <h3 class="text-4xl font-bold mb-4" id="seasonal-banner-title">🎯 Aprovecha la Temporada de Otoño</h3>
                <p class="text-xl mb-8 text-orange-100 max-w-2xl mx-auto" id="seasonal-banner-description">Los mejores productos para preparar tu jardín durante esta temporada especial</p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="openDailyOfferModal()" class="bg-white text-orange-600 font-bold py-4 px-8 rounded-2xl hover:bg-orange-50 transition-all duration-300 hover:scale-105 shadow-lg">
                        🔥 Ver Ofertas Especiales
                    </button>
                    <?php if (class_exists('WooCommerce')) : ?>
                        <a href="<?php echo home_url('/?post_type=product'); ?>" class="bg-orange-700 hover:bg-orange-800 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 hover:scale-105 shadow-lg">
                            🛒 Ver Toda la Tienda
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de Consejos de Temporada -->
<div id="season-tips-modal" class="fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[95vh] overflow-y-auto animate-modal-enter">
        <!-- Header del modal -->
        <div id="modal-tips-header" class="bg-gradient-to-r from-orange-600 to-red-600 text-white p-8 relative overflow-hidden">
            <!-- Botón cerrar -->
            <button onclick="closeSeasonTipsModal()" class="absolute top-4 right-4 bg-white/20 hover:bg-white/30 rounded-full p-2 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <!-- Título -->
            <div class="text-center">
                <div class="inline-flex items-center gap-3 text-4xl font-bold mb-4">
                    <span id="modal-tips-season-icon">🍂</span>
                    <span id="modal-tips-season-title">Consejos para Otoño</span>
                    <span id="modal-tips-season-icon-2">🍁</span>
                </div>
                <p class="text-xl opacity-90" id="modal-tips-season-subtitle">Guía completa para cuidar tu jardín durante el otoño mendocino</p>
            </div>
        </div>
        
        <!-- Contenido del modal -->
        <div class="p-8">
            <div id="modal-tips-content" class="space-y-8">
                <!-- El contenido se llenará dinámicamente -->
            </div>
            
            <!-- Botón de WhatsApp -->
            <div class="text-center mt-8 pt-8 border-t border-gray-200">
                <button onclick="consultar('Consejos de jardinería personalizados')" class="bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 hover:scale-105 shadow-lg">
                    💬 Consultar con Experto por WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Oferta Especial -->
<div id="daily-offer-modal" class="fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[95vh] overflow-y-auto animate-modal-enter">
        <!-- Header de la oferta -->
        <div id="modal-offer-header" class="bg-gradient-to-r from-red-600 via-orange-500 to-red-600 text-white p-8 relative overflow-hidden">
            <!-- Decoración de fondo -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 left-0 w-32 h-32 bg-white rounded-full blur-2xl animate-pulse"></div>
                <div class="absolute bottom-0 right-0 w-40 h-40 bg-white rounded-full blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
            </div>
            
            <!-- Botón cerrar -->
            <button onclick="closeDailyOfferModal()" class="absolute top-4 right-4 bg-white/20 hover:bg-white/30 rounded-full p-3 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <!-- Contenido del header -->
            <div class="text-center relative">
                <div class="text-sm opacity-90 mb-2">⚡ OFERTA TERMINA EN:</div>
                <div id="offer-countdown" class="text-2xl font-bold mb-4 bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 inline-block">23:59:59</div>
                <h2 class="text-4xl font-black mb-4" id="modal-offer-title">🔥 OFERTA ESPECIAL DE OTOÑO 🔥</h2>
                <p class="text-xl opacity-90" id="modal-offer-subtitle">Pack especial con los mejores productos de temporada</p>
            </div>
        </div>
        
        <!-- Contenido de la oferta -->
        <div class="p-8">
            <!-- Productos de la oferta -->
            <div id="offer-products-grid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <?php 
                // Obtener productos para la oferta especial
                if (class_exists('WooCommerce')) {
                    $offer_args = array(
                        'post_type' => 'product',
                        'posts_per_page' => 3,
                        'post_status' => 'publish',
                        'orderby' => 'rand'
                    );
                    
                    $offer_products = get_posts($offer_args);
                    $total_original_price = 0;
                    
                    if ($offer_products) {
                        foreach ($offer_products as $product_post) {
                            $product = wc_get_product($product_post->ID);
                            if ($product) {
                                $image_url = get_home_category_image($product_post->ID);
                                $emoji = get_home_category_emoji($product_post->ID);
                                $total_original_price += $product->get_price();
                                
                                echo '<div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-6 text-center border border-orange-200">';
                                echo '<img src="' . $image_url . '" alt="' . $product->get_name() . '" class="w-full h-32 object-cover rounded-xl mb-4">';
                                echo '<div class="text-3xl mb-2">' . $emoji . '</div>';
                                echo '<h4 class="font-bold text-gray-900 mb-2">' . $product->get_name() . '</h4>';
                                echo '<p class="text-orange-600 font-bold text-lg">$' . number_format($product->get_price(), 0, ',', '.') . '</p>';
                                echo '</div>';
                            }
                        }
                        
                        // Calcular precio con descuento
                        $discounted_price = $total_original_price * 0.5; // 50% OFF
                    }
                }
                ?>
            </div>
            
            <!-- Resumen de la oferta -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-2xl p-8 text-center mb-8">
                <h3 class="text-3xl font-bold text-gray-900 mb-4">💰 Resumen de tu Oferta</h3>
                <div class="flex justify-center items-center gap-8 mb-6">
                    <div>
                        <p class="text-gray-600 text-lg">Precio Original:</p>
                        <p class="text-2xl font-bold text-gray-400 line-through">$<?php echo isset($total_original_price) ? number_format($total_original_price, 0, ',', '.') : '0'; ?></p>
                    </div>
                    <div class="text-6xl">→</div>
                    <div>
                        <p class="text-green-600 text-lg font-bold">¡50% OFF!</p>
                        <p class="text-4xl font-black text-green-600">$<?php echo isset($discounted_price) ? number_format($discounted_price, 0, ',', '.') : '0'; ?></p>
                    </div>
                </div>
                
                <!-- Selector de cantidad -->
                <div class="flex items-center justify-center gap-4 mb-6">
                    <span class="text-lg font-medium">Cantidad:</span>
                    <button onclick="decreaseOfferQuantity()" class="bg-gray-200 hover:bg-gray-300 rounded-full w-10 h-10 flex items-center justify-center font-bold">-</button>
                    <span id="offer-quantity" class="text-2xl font-bold">1</span>
                    <button onclick="increaseOfferQuantity()" class="bg-gray-200 hover:bg-gray-300 rounded-full w-10 h-10 flex items-center justify-center font-bold">+</button>
                </div>
                
                <!-- Botones de acción -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="addOfferToCart()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 hover:scale-105 shadow-lg">
                        🛒 Agregar al Carrito
                    </button>
                    <button onclick="buyOfferNow()" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 hover:scale-105 shadow-lg">
                        💳 Comprar Ahora
                    </button>
                </div>
            </div>
            
            <!-- Garantía y beneficios -->
            <div class="grid md:grid-cols-3 gap-6 text-center">
                <div class="bg-blue-50 rounded-xl p-4">
                    <div class="text-3xl mb-2">🚚</div>
                    <h4 class="font-bold text-gray-900 mb-1">Envío Gratis</h4>
                    <p class="text-gray-600 text-sm">En compras superiores a $5.000</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4">
                    <div class="text-3xl mb-2">✅</div>
                    <h4 class="font-bold text-gray-900 mb-1">Garantía</h4>
                    <p class="text-gray-600 text-sm">30 días de garantía total</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-4">
                    <div class="text-3xl mb-2">💬</div>
                    <h4 class="font-bold text-gray-900 mb-1">Soporte</h4>
                    <p class="text-gray-600 text-sm">Asesoramiento personalizado</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Sistema estacional dinámico completo con consejos y productos
const seasonData = {
    primavera: {
        icon: '🌸',
        title: 'Primavera 2024',
        heroTitle: 'Los Cocos',
        subtitle: 'oasis primaveral',
        description: 'Renueva tu jardín • Plantas en flor • Semillas de temporada',
        background: 'https://images.unsplash.com/photo-1490750967868-88aa4486c946?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
        overlay: 'bg-gradient-to-r from-pink-900/85 via-green-800/75 to-emerald-900/85',
        badgeGradient: 'from-pink-500/90 to-green-500/90',
        offerTitle: 'OFERTA ESPECIAL DE PRIMAVERA',
        offerText: '¡Pack Primaveral 50% OFF!',
        tips: [
            { icon: '🌱', title: 'Siembra de Primavera', description: 'Planta semillas de flores anuales y prepara almácigos para el verano' },
            { icon: '💧', title: 'Riego Moderado', description: 'Aumenta gradualmente el riego conforme suben las temperaturas' },
            { icon: '✂️', title: 'Poda de Renovación', description: 'Poda rosales y arbustos para estimular el crecimiento primaveral' }
        ],
        modalTips: [
            { title: '🌸 Preparación del Suelo', content: 'Incorpora compost fresco y remueve la tierra después del invierno. Agrega fertilizante orgánico para nutrir las plantas que están despertando.' },
            { title: '🌱 Siembra Estratégica', content: 'Es el momento perfecto para sembrar: petunias, begonias, impatiens, y preparar almácigos de tomates y pimientos para el verano.' },
            { title: '💧 Sistema de Riego', content: 'Revisa y limpia el sistema de riego. Aumenta la frecuencia gradualmente conforme aumentan las temperaturas.' },
            { title: '🦋 Control de Plagas', content: 'Aplica tratamientos preventivos contra pulgones y cochinillas que aparecen con el calor primaveral.' }
        ]
    },
    verano: {
        icon: '☀️',
        title: 'Verano 2024',
        heroTitle: 'Los Cocos',
        subtitle: 'refugio de verano',
        description: 'Jardines frescos • Riego eficiente • Plantas resistentes al calor',
        background: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
        overlay: 'bg-gradient-to-r from-yellow-900/85 via-orange-800/75 to-red-900/85',
        badgeGradient: 'from-yellow-500/90 to-orange-500/90',
        offerTitle: 'OFERTA ESPECIAL DE VERANO',
        offerText: '¡Pack Veraniego 50% OFF!',
        tips: [
            { icon: '💧', title: 'Riego Intensivo', description: 'Riega temprano en la mañana o al atardecer para evitar evaporación' },
            { icon: '🌳', title: 'Sombra Natural', description: 'Protege plantas delicadas con mallas de sombra o plantas más altas' },
            { icon: '🌿', title: 'Mulching', description: 'Cubre el suelo con mulch para conservar humedad y proteger raíces' }
        ],
        modalTips: [
            { title: '💧 Riego Eficiente', content: 'Riega profundamente pero menos frecuente. Usa sistemas de goteo para maximizar la eficiencia del agua en el calor intenso.' },
            { title: '🌳 Protección Solar', content: 'Instala mallas de sombra del 50% para proteger plantas sensibles. Ubica macetas en zonas con sombra parcial durante las horas más calurosas.' },
            { title: '🌿 Conservación de Humedad', content: 'Aplica una capa gruesa de mulch orgánico alrededor de las plantas para mantener la humedad del suelo.' },
            { title: '🌺 Plantas de Verano', content: 'Planta especies resistentes al calor: lantanas, vinca, portulacas, y suculentas que prosperan con altas temperaturas.' }
        ]
    },
    otono: {
        icon: '🍂',
        title: 'Otoño 2024',
        heroTitle: 'Los Cocos',
        subtitle: 'refugio otoñal',
        description: 'Prepara tu jardín para el otoño • Plantas de temporada • Asesoramiento especializado',
        background: 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
        overlay: 'bg-gradient-to-r from-orange-900/85 via-red-800/75 to-amber-900/85',
        badgeGradient: 'from-orange-500/90 to-red-500/90',
        offerTitle: 'OFERTA ESPECIAL DE OTOÑO',
        offerText: '¡Pack Otoñal 50% OFF!',
        tips: [
            { icon: '🍁', title: 'Preparación de Tierra', description: 'Incorpora compost y hojas secas para nutrir el suelo' },
            { icon: '🌰', title: 'Plantas de Temporada', description: 'Planta bulbos de primavera y protege las sensibles' },
            { icon: '✂️', title: 'Poda Selectiva', description: 'Realiza podas de limpieza y prepara plantas para el invierno' }
        ],
        modalTips: [
            { title: '🍁 Enriquecimiento del Suelo', content: 'Incorpora compost maduro y hojas descompuestas. Es el momento ideal para preparar la tierra para el próximo ciclo de crecimiento.' },
            { title: '🌰 Plantación de Bulbos', content: 'Planta bulbos de primavera: tulipanes, jacintos, narcisos. También es ideal para plantar árboles frutales y rosales.' },
            { title: '✂️ Poda de Preparación', content: 'Realiza podas de limpieza eliminando ramas secas y enfermas. Prepara las plantas para resistir el frío invernal.' },
            { title: '🛡️ Protección Invernal', content: 'Protege plantas sensibles con mantas térmicas. Reduce gradualmente el riego preparando las plantas para el período de dormancia.' }
        ]
    },
    invierno: {
        icon: '❄️',
        title: 'Invierno 2024',
        heroTitle: 'Los Cocos',
        subtitle: 'refugio invernal',
        description: 'Plantas de interior • Cuidados especiales • Planificación del próximo año',
        background: 'https://images.unsplash.com/photo-1511593358241-7eea1f3c84e5?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
        overlay: 'bg-gradient-to-r from-blue-900/85 via-indigo-800/75 to-slate-900/85',
        badgeGradient: 'from-blue-500/90 to-indigo-500/90',
        offerTitle: 'OFERTA ESPECIAL DE INVIERNO',
        offerText: '¡Pack Invernal 50% OFF!',
        tips: [
            { icon: '🏠', title: 'Plantas de Interior', description: 'Traslada plantas sensibles al interior y cuida la iluminación' },
            { icon: '💧', title: 'Riego Reducido', description: 'Disminuye la frecuencia de riego, las plantas están en reposo' },
            { icon: '📋', title: 'Planificación', description: 'Planifica el jardín del próximo año y prepara herramientas' }
        ],
        modalTips: [
            { title: '🏠 Cuidado de Interiores', content: 'Traslada plantas tropicales al interior. Colócalas cerca de ventanas con buena luz pero protegidas de corrientes de aire frío.' },
            { title: '💧 Riego Invernal', content: 'Reduce significativamente el riego. La mayoría de plantas están en dormancia y el exceso de agua puede causar pudrición de raíces.' },
            { title: '🛠️ Mantenimiento de Herramientas', content: 'Limpia, afila y engrasa todas las herramientas de jardín. Es el momento perfecto para el mantenimiento del equipo.' },
            { title: '📋 Planificación Anual', content: 'Diseña los cambios para el próximo año, ordena semillas y planifica nuevas áreas de plantación para la primavera.' }
        ]
    }
};

let currentSeason = 'otono';
let offerQuantity = 1;

// Función principal para cambiar temporada
function changeSeason(season) {
    currentSeason = season;
    const data = seasonData[season];
    if (!data) return;

    // Actualizar hero section
    updateHeroSection(data);
    
    // Actualizar sección de consejos
    updateTipsSection(data);
    
    // Actualizar productos de temporada
    updateSeasonalProducts(data);
    
    // Actualizar ofertas
    updateSeasonalOffers(data);
}

function updateHeroSection(data) {
    // Actualizar indicador de temporada
    document.getElementById('season-icon-left').textContent = data.icon;
    document.getElementById('season-icon-right').textContent = data.icon;
    document.getElementById('current-season').textContent = data.title;
    
    // Actualizar imagen de fondo
    document.getElementById('hero-background').src = data.background;
    document.getElementById('hero-overlay').className = `absolute inset-0 ${data.overlay}`;
    
    // Actualizar indicador
    document.getElementById('season-indicator').className = `inline-flex items-center gap-3 bg-gradient-to-r ${data.badgeGradient} backdrop-blur-md text-white px-8 py-4 rounded-2xl shadow-2xl`;
    
    // Actualizar título
    document.getElementById('hero-subtitle').textContent = data.subtitle;
    document.getElementById('hero-description').textContent = data.description;
    
    // Actualizar oferta
    document.getElementById('offer-title').textContent = data.offerTitle;
    document.getElementById('offer-text').textContent = data.offerText;
}

function updateTipsSection(data) {
    // Actualizar badge de consejos
    const tipsBadge = document.getElementById('tips-season-badge');
    tipsBadge.className = `inline-flex items-center gap-3 bg-gradient-to-r ${data.badgeGradient} text-white px-6 py-3 rounded-2xl text-lg font-bold shadow-lg mb-6`;
    
    document.getElementById('tips-season-icon').textContent = data.icon;
    document.getElementById('tips-season-title').textContent = `Consejos para ${data.title.split(' ')[0]}`;
    document.getElementById('tips-season-icon-2').textContent = data.icon;
    document.getElementById('tips-season-subtitle').textContent = `Guía completa para cuidar tu jardín durante ${data.title.split(' ')[0].toLowerCase()} mendocino`;
    
    // Actualizar grid de consejos
    const tipsGrid = document.getElementById('tips-grid');
    tipsGrid.innerHTML = '';
    
    data.tips.forEach(tip => {
        const tipCard = document.createElement('div');
        tipCard.className = 'bg-white/90 backdrop-blur-sm rounded-2xl p-6 text-center transition-all duration-300 hover:scale-105 hover:shadow-lg border border-green-100';
        tipCard.innerHTML = `
            <div class="text-4xl mb-4">${tip.icon}</div>
            <h4 class="font-bold text-gray-900 text-lg mb-3">${tip.title}</h4>
            <p class="text-gray-600 leading-relaxed">${tip.description}</p>
        `;
        tipsGrid.appendChild(tipCard);
    });
    
    // Actualizar botón de más consejos
    document.getElementById('more-tips-icon').textContent = data.icon;
    document.getElementById('more-tips-button').innerHTML = `<span>${data.icon}</span> Ver Consejos Completos`;
}

function updateSeasonalProducts(data) {
    const seasonName = data.title.split(' ')[0];
    document.getElementById('season-products-title').textContent = seasonName;
    document.getElementById('season-products-description').textContent = `esta temporada ${seasonName.toLowerCase()}`;
    
    // Actualizar banner de temporada
    document.getElementById('seasonal-banner-title').textContent = `🎯 Aprovecha la Temporada de ${seasonName}`;
    document.getElementById('seasonal-banner-description').textContent = `Los mejores productos para preparar tu jardín durante esta temporada especial`;
}

function updateSeasonalOffers(data) {
    // Actualizar título de oferta en modal
    document.getElementById('modal-offer-title').textContent = data.offerTitle;
    document.getElementById('modal-offer-subtitle').textContent = `Pack especial con los mejores productos de ${data.title.split(' ')[0].toLowerCase()}`;
}

// Funciones para modales
function openSeasonTipsModal() {
    const data = seasonData[currentSeason];
    
    // Actualizar header del modal
    const headerGradient = data.badgeGradient.replace('from-', 'from-').replace('to-', 'to-');
    document.getElementById('modal-tips-header').className = `bg-gradient-to-r ${headerGradient.replace('/90', '')} text-white p-8 relative overflow-hidden`;
    
    document.getElementById('modal-tips-season-icon').textContent = data.icon;
    document.getElementById('modal-tips-season-title').textContent = `Consejos para ${data.title.split(' ')[0]}`;
    document.getElementById('modal-tips-season-icon-2').textContent = data.icon;
    document.getElementById('modal-tips-season-subtitle').textContent = `Guía completa para cuidar tu jardín durante ${data.title.split(' ')[0].toLowerCase()} mendocino`;
    
    // Llenar contenido del modal
    const modalContent = document.getElementById('modal-tips-content');
    modalContent.innerHTML = '';
    
    data.modalTips.forEach(tip => {
        const tipSection = document.createElement('div');
        tipSection.className = 'bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl p-6 border border-green-100';
        tipSection.innerHTML = `
            <h3 class="text-2xl font-bold text-gray-900 mb-4">${tip.title}</h3>
            <p class="text-gray-700 leading-relaxed text-lg">${tip.content}</p>
        `;
        modalContent.appendChild(tipSection);
    });
    
    // Mostrar modal
    document.getElementById('season-tips-modal').classList.remove('hidden');
    document.getElementById('season-tips-modal').classList.add('flex');
}

function closeSeasonTipsModal() {
    document.getElementById('season-tips-modal').classList.add('hidden');
    document.getElementById('season-tips-modal').classList.remove('flex');
}

function openDailyOfferModal() {
    // Actualizar contenido del modal según la temporada actual
    updateOfferModal();
    
    // Iniciar countdown
    startOfferCountdown();
    
    // Mostrar modal
    document.getElementById('daily-offer-modal').classList.remove('hidden');
    document.getElementById('daily-offer-modal').classList.add('flex');
}

function updateOfferModal() {
    const data = seasonData[currentSeason];
    
    // Actualizar header del modal
    const headerGradient = data.badgeGradient.replace('/90', '');
    document.getElementById('modal-offer-header').className = `bg-gradient-to-r ${headerGradient} text-white p-8 relative overflow-hidden`;
    
    // Actualizar título y descripción
    document.getElementById('modal-offer-title').textContent = `🔥 ${data.offerTitle} 🔥`;
    document.getElementById('modal-offer-subtitle').textContent = `Pack especial con los mejores productos de ${data.title.split(' ')[0].toLowerCase()}`;
}

function closeDailyOfferModal() {
    document.getElementById('daily-offer-modal').classList.add('hidden');
    document.getElementById('daily-offer-modal').classList.remove('flex');
}

// Funciones para el carrito y ofertas
function addOfferToCart() {
    const quantity = document.getElementById('offer-quantity').textContent;
    alert(`✅ Pack de temporada (${quantity} unidad${quantity > 1 ? 'es' : ''}) agregado al carrito!\n\n🛒 Ir al carrito para finalizar la compra.`);
}

function buyOfferNow() {
    const quantity = document.getElementById('offer-quantity').textContent;
    alert(`🚀 Redirigiendo a checkout para comprar ${quantity} pack${quantity > 1 ? 's' : ''} de temporada...\n\n💳 Procesando compra inmediata.`);
}

function increaseOfferQuantity() {
    offerQuantity = Math.min(offerQuantity + 1, 10);
    document.getElementById('offer-quantity').textContent = offerQuantity;
}

function decreaseOfferQuantity() {
    offerQuantity = Math.max(offerQuantity - 1, 1);
    document.getElementById('offer-quantity').textContent = offerQuantity;
}

// Countdown para ofertas
function startOfferCountdown() {
    const countdownElement = document.getElementById('offer-countdown');
    let timeLeft = 24 * 60 * 60; // 24 horas en segundos
    
    const timer = setInterval(() => {
        const hours = Math.floor(timeLeft / 3600);
        const minutes = Math.floor((timeLeft % 3600) / 60);
        const seconds = timeLeft % 60;
        
        countdownElement.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        timeLeft--;
        
        if (timeLeft < 0) {
            clearInterval(timer);
            countdownElement.textContent = '¡OFERTA TERMINADA!';
        }
    }, 1000);
}

// Función de WhatsApp
function consultar(producto) {
    const mensaje = encodeURIComponent(`Hola! Me interesa consultar sobre: ${producto}`);
    const numero = '5491112345678'; // Cambiar por tu número real
    window.open(`https://wa.me/${numero}?text=${mensaje}`, '_blank');
}

// Auto-popup de oferta para nuevos usuarios
function showOfferPopup() {
    // Verificar si es un nuevo usuario o si han pasado más de 4 horas (reducido para testing)
    const lastOfferShown = localStorage.getItem('lastOfferShown');
    const now = new Date().getTime();
    const fourHoursAgo = now - (4 * 60 * 60 * 1000); // 4 horas en milisegundos
    
    // Mostrar popup si es nuevo usuario o si han pasado más de 4 horas
    if (!lastOfferShown || parseInt(lastOfferShown) < fourHoursAgo) {
        console.log('🔥 Mostrando popup de oferta estacional automático');
        openDailyOfferModal();
        localStorage.setItem('lastOfferShown', now.toString());
    } else {
        console.log('⏰ Popup de oferta ya mostrado recientemente');
    }
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    changeSeason('otono');
    
    // Mostrar popup de oferta automáticamente después de 5 segundos para nuevos usuarios
    setTimeout(() => showOfferPopup(), 5000);
    
    // Agregar botón de testing para desarrolladores (solo en desarrollo)
    if (window.location.hostname === 'localhost') {
        const testButton = document.createElement('button');
        testButton.textContent = '🔥 Test Popup';
        testButton.className = 'fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg z-50 font-bold';
        testButton.onclick = () => {
            localStorage.removeItem('lastOfferShown');
            showOfferPopup();
        };
        document.body.appendChild(testButton);
    }
    
    // Cerrar modales con ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeSeasonTipsModal();
            closeDailyOfferModal();
        }
    });
    
    // Cerrar modales haciendo clic fuera
    document.getElementById('season-tips-modal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeSeasonTipsModal();
        }
    });
    
    document.getElementById('daily-offer-modal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeDailyOfferModal();
        }
    });
});

// Función para agregar productos al carrito desde cards destacadas
function addToCartFeatured(productId, productName) {
    // Deshabilitar el botón temporalmente
    const button = document.querySelector(`[data-product-id="${productId}"]`);
    const originalText = button.innerHTML;
    button.innerHTML = '⏳ Agregando...';
    button.disabled = true;
    
    // Preparar datos para AJAX
    const formData = new FormData();
    formData.append('action', 'add_to_cart_featured');
    formData.append('product_id', productId);
    formData.append('quantity', 1);
    formData.append('nonce', ajax_cart_params.nonce);
    
    fetch(ajax_cart_params.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('✅ Respuesta del servidor:', data);
        
        if (data.success) {
            // Mostrar notificación de éxito
            showCartNotificationFeatured(productName, true);
            
            // Actualizar contador del carrito si existe
            updateCartCount();
            
            // Trigger evento de WooCommerce para actualizar fragmentos
            if (typeof jQuery !== 'undefined') {
                jQuery('body').trigger('added_to_cart', [data.fragments, data.cart_hash, button]);
            }
        } else {
            console.error('❌ Error:', data.data);
            showCartNotificationFeatured(productName, false, data.data || 'Error desconocido');
        }
    })
    .catch(error => {
        console.error('❌ Error de red:', error);
        showCartNotificationFeatured(productName, false, 'Error de conexión');
    })
    .finally(() => {
        // Restaurar el botón
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 1000);
    });
}

// Función para mostrar notificaciones elegantes
function showCartNotificationFeatured(productName, success, errorMessage = '') {
    // Crear el elemento de notificación
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background: ${success ? 'linear-gradient(135deg, #4CAF50, #45a049)' : 'linear-gradient(135deg, #FF4444, #CC0000)'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        transform: translateX(400px);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        max-width: 350px;
        font-weight: 600;
        backdrop-filter: blur(10px);
    `;
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="font-size: 1.5rem;">${success ? '✅' : '❌'}</div>
            <div>
                <div style="font-weight: 700; margin-bottom: 0.25rem;">
                    ${success ? '¡Agregado al carrito!' : '¡Error!'}
                </div>
                <div style="font-size: 0.875rem; opacity: 0.9;">
                    ${success ? productName : errorMessage}
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animar entrada
    requestAnimationFrame(() => {
        notification.style.transform = 'translateX(0)';
    });
    
    // Animar salida después de 4 segundos
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 400);
    }, 4000);
}

// Función para actualizar contador del carrito
function updateCartCount() {
    // Buscar elementos comunes de contador de carrito
    const cartCounters = document.querySelectorAll('.cart-count, .cart-counter, .woocommerce-cart-count');
    
    // Si existe jQuery y WooCommerce, usar su sistema
    if (typeof jQuery !== 'undefined' && typeof wc_cart_fragments_params !== 'undefined') {
        jQuery('body').trigger('wc_fragment_refresh');
    }
}
</script>

<?php else : ?>
<!-- Template para productos -->
<main style="max-width: 1400px; margin: 0 auto; padding: 2rem;">
  <section style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); padding: 3rem; border-radius: 12px; margin-bottom: 3rem;">
    <h1 style="font-size: 2.5rem; font-weight: 800; color: #1f2937; text-align: center;">
      Tu Jardín, <span style="color: #10b981;">Nuestro Paraíso</span>
    </h1>
    <p style="text-align: center; font-size: 1.125rem; color: #6b7280; margin-top: 1rem;">
      Descubre nuestra amplia selección de plantas, herramientas y todo lo necesario para transformar tu espacio.
    </p>
  </section>
  
  <!-- Categorías -->
  <section style="margin-bottom: 3rem;">
    <h2 style="text-align: center; font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: 2rem;">
      Explora por Categorías
    </h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
      <?php
      $product_categories = get_terms(array(
          'taxonomy' => 'product_cat',
          'hide_empty' => true,
          'exclude' => array(15)
      ));
      
      if ($product_categories && !is_wp_error($product_categories)) {
          foreach (array_slice($product_categories, 0, 8) as $category) {
              $category_link = get_term_link($category);
              echo '<a href="' . esc_url($category_link) . '" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 1.5rem; background: white; border: 2px solid #e5e7eb; border-radius: 12px; text-decoration: none; color: inherit; transition: all 0.3s ease; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">';
              echo '<div style="width: 60px; height: 60px; border-radius: 50%; background-color: #10b98120; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">';
              echo '<span style="font-size: 2rem; color: #10b981;">🌿</span>';
              echo '</div>';
              echo '<p style="font-weight: 600; font-size: 1rem; color: #1f2937; margin-bottom: 0.5rem; margin-top: 0;">' . esc_html($category->name) . '</p>';
              echo '<p style="font-size: 0.875rem; color: #6b7280; margin: 0;">' . $category->count . ' productos</p>';
              echo '</a>';
          }
      }
      ?>
    </div>
  </section>
  
  <!-- Productos -->
  <section>
    <h2 style="text-align: center; font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: 2rem;">
      Nuestros Productos
    </h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
      <?php
      // Crear consulta personalizada para productos
      $products_query = new WP_Query(array(
          'post_type' => 'product',
          'posts_per_page' => 12,
          'post_status' => 'publish'
      ));
      
      if ($products_query->have_posts()) {
          while ($products_query->have_posts()) {
              $products_query->the_post();
              global $product;
              
              if ($product) {
                  $image_url = get_home_category_image($product->get_id());
                  $product_url = get_permalink($product->get_id());
                  
                  echo '<a href="' . esc_url($product_url) . '" style="display: block; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: transform 0.3s ease; text-decoration: none; color: inherit;">';
                  echo '<div style="height: 220px; overflow: hidden;">';
                  echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($product->get_name()) . '" style="width: 100%; height: 100%; object-fit: cover;"/>';
                  echo '</div>';
                  echo '<div style="padding: 1.5rem;">';
                  echo '<h3 style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem;">' . esc_html($product->get_name()) . '</h3>';
                  echo '<p style="font-size: 1.25rem; font-weight: 800; color: #10b981;">$' . number_format($product->get_price(), 0, ',', '.') . '</p>';
                  echo '</div>';
                  echo '</a>';
              }
          }
          wp_reset_postdata();
      } else {
          echo '<div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: #f9fafb; border-radius: 12px;">';
          echo '<h3 style="font-size: 1.5rem; color: #1f2937;">No se encontraron productos</h3>';
          echo '</div>';
      }
      ?>
    </div>
  </section>
</main>

<?php endif; ?>

<?php get_footer(); ?>
