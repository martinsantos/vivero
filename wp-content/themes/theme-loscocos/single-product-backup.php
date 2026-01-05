<?php get_header(); ?>

<!-- Contenido Principal -->
<main class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-green-50 py-8">
    <div class="container-clean">
        
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php 
                global $product;
                if (!$product) {
                    $product = wc_get_product(get_the_ID());
                }
                
                // Función para obtener imagen por categoría
                function get_premium_category_image($product_id) {
                    $product_categories = wp_get_post_terms($product_id, 'product_cat');
                    
                    $category_images = array(
                        'plantas-de-interior' => 'https://images.unsplash.com/photo-1463320726281-696a485928c7?w=800&h=600&fit=crop&q=90',
                        'arbustos' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&h=600&fit=crop&q=90',
                        'arboles' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=800&h=600&fit=crop&q=90',
                        'enredaderas' => 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6?w=800&h=600&fit=crop&q=90',
                        'macetas-plasticas' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=800&h=600&fit=crop&q=90',
                        'macetas-fibrocemento' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=600&fit=crop&q=90',
                        'porta-macetas' => 'https://images.unsplash.com/photo-1463320726281-696a485928c7?w=800&h=600&fit=crop&q=90',
                        'sustratos-y-tierras' => 'https://images.unsplash.com/photo-1464207687429-7505649dae38?w=800&h=600&fit=crop&q=90',
                        'fertilizantes' => 'https://images.unsplash.com/photo-1530836369250-ef72a3f5cda8?w=800&h=600&fit=crop&q=90',
                        'fitosanitarios' => 'https://images.unsplash.com/photo-1530836369250-ef72a3f5cda8?w=800&h=600&fit=crop&q=90',
                        'hierros-y-soportes' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?w=800&h=600&fit=crop&q=90',
                        'mensulas' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=800&h=600&fit=crop&q=90',
                        'aros-y-ganchos' => 'https://images.unsplash.com/photo-1463320726281-696a485928c7?w=800&h=600&fit=crop&q=90',
                        'pies-y-bases' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=800&h=600&fit=crop&q=90',
                        'pies-nordicos' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=600&fit=crop&q=90'
                    );
                    
                    if ($product_categories && !is_wp_error($product_categories)) {
                        foreach ($product_categories as $category) {
                            $category_slug = $category->slug;
                            if (isset($category_images[$category_slug])) {
                                return $category_images[$category_slug];
                            }
                        }
                    }
                    
                    return 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&h=600&fit=crop&q=90';
                }
                
                function get_premium_category_emoji($product_id) {
                    $product_categories = wp_get_post_terms($product_id, 'product_cat');
                    
                    $category_emojis = array(
                        'plantas-de-interior' => '🪴',
                        'arbustos' => '🌿',
                        'arboles' => '🌳',
                        'enredaderas' => '🌱',
                        'macetas-plasticas' => '🏺',
                        'macetas-fibrocemento' => '🏺',
                        'porta-macetas' => '🪴',
                        'sustratos-y-tierras' => '🌱',
                        'fertilizantes' => '🧪',
                        'fitosanitarios' => '💊',
                        'hierros-y-soportes' => '🔧',
                        'mensulas' => '📐',
                        'aros-y-ganchos' => '⭕',
                        'pies-y-bases' => '🦵',
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
                    
                    return '🌿';
                }
                
                $image_url = loscocos_get_product_image(get_the_ID());
                $emoji = get_premium_category_emoji(get_the_ID());
                $categories = wp_get_post_terms(get_the_ID(), 'product_cat');
                ?>
                
                <!-- Breadcrumb Premium -->
                <nav class="mb-8 animate-fade-in">
                    <div class="flex items-center space-x-3 text-sm text-gray-500 font-body bg-white/60 backdrop-blur-sm rounded-full px-6 py-3 shadow-sm border border-white/20">
                        <a href="<?php echo home_url(); ?>" class="hover:text-green-600 transition-all duration-300 hover:scale-105">
                            <span class="flex items-center space-x-1">
                                <span>🏠</span>
                                <span>Inicio</span>
                            </span>
                        </a>
                        <span class="text-green-400">›</span>
                        <a href="<?php echo home_url('/?post_type=product'); ?>" class="hover:text-green-600 transition-all duration-300 hover:scale-105">
                            <span class="flex items-center space-x-1">
                                <span>🛍️</span>
                                <span>Tienda</span>
                            </span>
                        </a>
                        <?php if ($categories && !is_wp_error($categories)) : ?>
                            <span class="text-green-400">›</span>
                            <a href="<?php echo home_url('/?post_type=product&product_cat=' . $categories[0]->slug); ?>" class="hover:text-green-600 transition-all duration-300 hover:scale-105">
                                <span class="flex items-center space-x-1">
                                    <span><?php echo $emoji; ?></span>
                                    <span><?php echo $categories[0]->name; ?></span>
                                </span>
                            </a>
                        <?php endif; ?>
                        <span class="text-green-400">›</span>
                        <span class="text-gray-900 font-medium"><?php the_title(); ?></span>
                    </div>
                </nav>
                
                <!-- Producto Principal Premium -->
                <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden mb-16 border border-white/20 animate-slide-up">
                    <div class="grid lg:grid-cols-2 gap-0">
                        
                        <!-- Galería de Imagen Premium -->
                        <div class="relative group">
                            <div class="aspect-square lg:aspect-auto lg:h-full bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden relative">
                                <!-- Imagen principal con efectos -->
                                <img src="<?php echo $image_url; ?>" 
                                     alt="<?php the_title(); ?>" 
                                     class="w-full h-full object-cover transition-all duration-700 group-hover:scale-110">
                                
                                <!-- Overlay gradiente -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-white/10"></div>
                                
                                <!-- Efectos de brillo -->
                                <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                
                                <!-- Badge de categoría flotante -->
                                <div class="absolute top-8 left-8 animate-bounce-gentle">
                                    <div class="bg-white/95 backdrop-blur-md rounded-2xl px-6 py-3 flex items-center space-x-3 shadow-xl border border-white/30">
                                        <span class="text-2xl animate-pulse"><?php echo $emoji; ?></span>
                                        <?php if ($categories && !is_wp_error($categories)) : ?>
                                            <div>
                                                <span class="text-sm font-semibold text-gray-800 block"><?php echo $categories[0]->name; ?></span>
                                                <span class="text-xs text-gray-500">Categoría Premium</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Badge de stock premium -->
                                <?php if ($product->is_in_stock()) : ?>
                                    <div class="absolute top-8 right-8 animate-pulse">
                                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-full px-4 py-2 text-sm font-bold shadow-lg flex items-center space-x-2">
                                            <span class="w-2 h-2 bg-white rounded-full animate-ping"></span>
                                            <span>✓ En Stock</span>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="absolute top-8 right-8">
                                        <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-full px-4 py-2 text-sm font-bold shadow-lg flex items-center space-x-2">
                                            <span class="w-2 h-2 bg-white rounded-full"></span>
                                            <span>✗ Sin Stock</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Indicador de calidad premium -->
                                <div class="absolute bottom-8 left-8">
                                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-full px-4 py-2 text-sm font-bold shadow-lg flex items-center space-x-2">
                                        <span class="text-base">⭐</span>
                                        <span>Calidad Premium</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información del Producto Premium -->
                        <div class="p-8 lg:p-12 xl:p-16 bg-gradient-to-br from-white to-gray-50">
                            
                            <!-- Título y Precio Premium -->
                            <div class="mb-10">
                                <div class="flex items-start justify-between mb-6">
                                    <div class="flex-1">
                                        <h1 class="text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 mb-4 font-display leading-tight">
                                            <?php the_title(); ?>
                                        </h1>
                                        
                                        <?php if ($categories && !is_wp_error($categories)) : ?>
                                            <div class="flex flex-wrap gap-2 mb-6">
                                                <?php foreach ($categories as $category) : ?>
                                                    <span class="bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold border border-green-200">
                                                        <?php echo $category->name; ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Botón de favoritos -->
                                    <button class="p-3 rounded-full bg-gradient-to-r from-pink-100 to-red-100 text-red-500 hover:from-pink-200 hover:to-red-200 transition-all duration-300 hover:scale-110 group">
                                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                <!-- Precio espectacular -->
                                <div class="flex items-baseline space-x-4 mb-8">
                                    <div class="text-5xl lg:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-emerald-600 font-body">
                                        $<?php echo number_format($product->get_price(), 0, ',', '.'); ?>
                                    </div>
                                    <?php if ($product->is_on_sale()) : ?>
                                        <div class="text-3xl text-gray-400 line-through font-body">
                                            $<?php echo number_format($product->get_regular_price(), 0, ',', '.'); ?>
                                        </div>
                                        <div class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-4 py-2 rounded-full text-sm font-bold animate-pulse">
                                            ¡OFERTA ESPECIAL!
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Indicadores de valor -->
                                <div class="grid grid-cols-3 gap-4 mb-8">
                                    <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                                        <div class="text-2xl mb-2">🚚</div>
                                        <div class="text-sm font-semibold text-blue-800">Envío Gratis</div>
                                        <div class="text-xs text-blue-600">Mendoza</div>
                                    </div>
                                    <div class="text-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-100">
                                        <div class="text-2xl mb-2">🛡️</div>
                                        <div class="text-sm font-semibold text-green-800">Garantía</div>
                                        <div class="text-xs text-green-600">30 días</div>
                                    </div>
                                    <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl border border-purple-100">
                                        <div class="text-2xl mb-2">💬</div>
                                        <div class="text-sm font-semibold text-purple-800">Soporte</div>
                                        <div class="text-xs text-purple-600">24/7</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Descripción Premium -->
                            <?php if ($product->get_description()) : ?>
                                <div class="mb-10">
                                    <h3 class="text-xl font-bold text-gray-900 mb-4 font-display flex items-center">
                                        <span class="w-8 h-8 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center text-white text-sm mr-3">📝</span>
                                        Descripción Detallada
                                    </h3>
                                    <div class="text-gray-700 font-body leading-relaxed text-lg bg-gradient-to-br from-gray-50 to-white p-6 rounded-2xl border border-gray-100 shadow-inner">
                                        <?php echo wp_kses_post($product->get_description()); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Información técnica premium -->
                            <div class="grid grid-cols-2 gap-6 mb-10">
                                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                    <h4 class="font-bold text-gray-900 mb-3 font-display flex items-center">
                                        <span class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs mr-2">#</span>
                                        Código SKU
                                    </h4>
                                    <p class="text-gray-600 font-body font-mono text-lg"><?php echo $product->get_sku() ?: 'N/A'; ?></p>
                                </div>
                                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                    <h4 class="font-bold text-gray-900 mb-3 font-display flex items-center">
                                        <span class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-xs mr-2">📦</span>
                                        Disponibilidad
                                    </h4>
                                    <p class="text-gray-600 font-body text-lg">
                                        <?php 
                                        if ($product->managing_stock()) {
                                            echo '<span class="text-green-600 font-semibold">' . $product->get_stock_quantity() . ' unidades disponibles</span>';
                                        } else {
                                            echo '<span class="' . ($product->is_in_stock() ? 'text-green-600' : 'text-red-600') . ' font-semibold">';
                                            echo $product->is_in_stock() ? 'En stock' : 'Sin stock';
                                            echo '</span>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Formulario de compra premium -->
                            <?php if ($product->is_in_stock()) : ?>
                                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-3xl p-8 border-2 border-green-100 shadow-xl mb-8">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-6 font-display flex items-center">
                                        <span class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white mr-3">🛒</span>
                                        Agregar al Carrito
                                    </h3>
                                    
                                    <form class="woocommerce-cart-form" method="post" enctype="multipart/form-data">
                                        <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
                                            <!-- Selector de cantidad premium -->
                                            <div class="flex items-center bg-white rounded-2xl border-2 border-green-200 overflow-hidden shadow-lg">
                                                <button type="button" onclick="decreaseQuantity()" class="px-6 py-4 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 transition-all duration-300 text-gray-700 font-bold text-xl">
                                                    −
                                                </button>
                                                <input type="number" name="quantity" id="quantity" value="1" min="1" 
                                                       class="w-20 text-center py-4 border-0 focus:ring-0 font-body text-xl font-bold bg-white">
                                                <button type="button" onclick="increaseQuantity()" class="px-6 py-4 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 transition-all duration-300 text-gray-700 font-bold text-xl">
                                                    +
                                                </button>
                                            </div>
                                            
                                            <!-- Botón de agregar premium -->
                                            <input type="hidden" name="add-to-cart" value="<?php echo $product->get_id(); ?>">
                                            <button type="submit" class="flex-1 sm:flex-none bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-4 px-8 rounded-2xl text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-2xl flex items-center justify-center space-x-3 min-w-[200px]">
                                                <span class="text-xl">🛒</span>
                                                <span>Agregar al Carrito</span>
                                            </button>
                                        </div>
                                        
                                        <!-- Precio total dinámico -->
                                        <div class="mt-6 text-center">
                                            <div class="text-sm text-gray-600 mb-2">Total:</div>
                                            <div id="total-price" class="text-3xl font-bold text-green-600">
                                                $<?php echo number_format($product->get_price(), 0, ',', '.'); ?>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?php else : ?>
                                <div class="bg-gradient-to-br from-red-50 to-pink-50 border-2 border-red-200 rounded-3xl p-8 text-center">
                                    <div class="text-6xl mb-4">😔</div>
                                    <h3 class="text-2xl font-bold text-red-800 mb-2">Producto No Disponible</h3>
                                    <p class="text-red-600 font-body">Este producto no está disponible en este momento. ¡Vuelve pronto!</p>
                                </div>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                </div>
                
                <!-- Productos relacionados premium -->
                <div class="mb-16">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl font-black text-gray-900 mb-4 font-display">Productos Relacionados</h2>
                        <p class="text-xl text-gray-600 font-body">Descubre más productos increíbles de la misma categoría</p>
                        <div class="w-24 h-1 bg-gradient-to-r from-green-400 to-emerald-500 mx-auto mt-6 rounded-full"></div>
                    </div>
                    
                    <?php
                    // Obtener productos relacionados
                    $related_args = array(
                        'post_type' => 'product',
                        'posts_per_page' => 4,
                        'post_status' => 'publish',
                        'post__not_in' => array(get_the_ID())
                    );
                    
                    if ($categories && !is_wp_error($categories)) {
                        $related_args['tax_query'] = array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'slug',
                                'terms' => $categories[0]->slug
                            )
                        );
                    }
                    
                    $related_products = get_posts($related_args);
                    
                    if ($related_products) :
                    ?>
                        <div class="products-grid">
                            <?php foreach ($related_products as $related_post) : 
                                $related_product = wc_get_product($related_post->ID);
                                if ($related_product) :
                                    $related_image = loscocos_get_product_image($related_post->ID);
                                    $related_emoji = get_premium_category_emoji($related_post->ID);
                            ?>
                                <a href="<?php echo home_url('/?product=' . $related_post->post_name); ?>" class="product-card-standard">
                                    <!-- Imagen del producto -->
                                    <div class="product-image">
                                        <img src="<?php echo $related_image; ?>" 
                                             alt="<?php echo $related_product->get_name(); ?>">
                                        
                                        <!-- Badge de categoría -->
                                        <div class="category-badge"><?php echo $related_emoji; ?></div>
                                        
                                        <!-- Etiqueta especial -->
                                        <?php if ($related_product->is_on_sale()) : ?>
                                            <div class="product-label oferta">OFERTA</div>
                                        <?php else : ?>
                                            <div class="product-label temporada">RELACIONADO</div>
                                        <?php endif; ?>
                                        
                                        <!-- Overlay de hover -->
                                        <div class="hover-overlay">
                                            <div class="hover-text">👁️ Ver Detalles del Producto</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Contenido de la card -->
                                    <div class="product-content">
                                        <h3 class="product-title">
                                            <?php echo $related_product->get_name(); ?>
                                        </h3>
                                        
                                        <!-- Categoría -->
                                        <?php 
                                        $related_categories = wp_get_post_terms($related_post->ID, 'product_cat');
                                        if ($related_categories && !is_wp_error($related_categories)) :
                                        ?>
                                            <div class="product-category"><?php echo $related_categories[0]->name; ?></div>
                                        <?php endif; ?>
                                        
                                        <!-- Precio -->
                                        <div class="product-price">
                                            <div>
                                                <?php if ($related_product->is_on_sale()) : ?>
                                                    <span class="price-current">$<?php echo number_format($related_product->get_sale_price(), 0, ',', '.'); ?></span>
                                                    <span class="price-original">$<?php echo number_format($related_product->get_regular_price(), 0, ',', '.'); ?></span>
                                                <?php else : ?>
                                                    <span class="price-current">$<?php echo number_format($related_product->get_price(), 0, ',', '.'); ?></span>
                                                <?php endif; ?>
                                                <div class="price-label">Precio final</div>
                                            </div>
                                            <div class="hover-arrow">→</div>
                                        </div>
                                    </div>
                                </a>
                            <?php endif; endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
            <?php endwhile; ?>
        <?php else : ?>
            <div class="text-center py-20">
                <div class="text-8xl mb-8">😕</div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Producto No Encontrado</h2>
                <p class="text-xl text-gray-600 mb-8">Lo sentimos, no pudimos encontrar el producto que buscas.</p>
                <a href="<?php echo home_url('/?post_type=product'); ?>" class="btn-clean btn-primary">
                    Volver a la Tienda
                </a>
            </div>
        <?php endif; ?>
        
    </div>
</main>

<!-- Scripts premium -->
<script>
// Funciones para el selector de cantidad
function increaseQuantity() {
    const input = document.getElementById('quantity');
    const newValue = parseInt(input.value) + 1;
    input.value = newValue;
    updateTotalPrice(newValue);
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        const newValue = currentValue - 1;
        input.value = newValue;
        updateTotalPrice(newValue);
    }
}

// Actualizar precio total
function updateTotalPrice(quantity) {
    const basePrice = <?php echo $product->get_price(); ?>;
    const totalPrice = basePrice * quantity;
    const formattedPrice = new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
        minimumFractionDigits: 0
    }).format(totalPrice);
    
    const totalPriceElement = document.getElementById('total-price');
    if (totalPriceElement) {
        totalPriceElement.textContent = '$' + totalPrice.toLocaleString('es-AR');
        totalPriceElement.classList.add('animate-pulse');
        setTimeout(() => {
            totalPriceElement.classList.remove('animate-pulse');
        }, 300);
    }
}

// Animaciones al cargar
document.addEventListener('DOMContentLoaded', function() {
    // Animación de entrada
    const elements = document.querySelectorAll('.animate-fade-in, .animate-slide-up');
    elements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        setTimeout(() => {
            el.style.transition = 'all 0.6s ease-out';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

<!-- Estilos adicionales -->
<style>
@keyframes bounce-gentle {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

.animate-bounce-gentle {
    animation: bounce-gentle 3s ease-in-out infinite;
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

.animate-slide-up {
    animation: slideUp 0.8s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        opacity: 0; 
        transform: translateY(30px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

/* Efectos de hover mejorados */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

/* Scrollbar personalizado */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #059669, #047857);
}
</style>

<?php get_footer(); ?> 