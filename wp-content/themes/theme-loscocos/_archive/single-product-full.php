<?php get_header(); ?>

<!-- Contenido Principal -->
<main class="min-h-screen bg-gray-50 py-8">
    <div class="container-clean">
        
        <?php while (have_posts()) : the_post(); ?>
            <?php 
            global $product;
            if (!$product) {
                $product = wc_get_product(get_the_ID());
            }
            
            // Función para obtener imagen por categoría
            function get_single_category_image($product_id) {
                $product_categories = wp_get_post_terms($product_id, 'product_cat');
                
                $category_images = array(
                    'plantas-de-interior' => 'https://images.unsplash.com/photo-1463320726281-696a485928c7?w=600&h=400&fit=crop&q=80',
                    'arbustos' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&h=400&fit=crop&q=80',
                    'arboles' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=600&h=400&fit=crop&q=80',
                    'enredaderas' => 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6?w=600&h=400&fit=crop&q=80',
                    'macetas-plasticas' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=600&h=400&fit=crop&q=80',
                    'macetas-fibrocemento' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=600&h=400&fit=crop&q=80',
                    'porta-macetas' => 'https://images.unsplash.com/photo-1463320726281-696a485928c7?w=600&h=400&fit=crop&q=80',
                    'sustratos-y-tierras' => 'https://images.unsplash.com/photo-1464207687429-7505649dae38?w=600&h=400&fit=crop&q=80',
                    'fertilizantes' => 'https://images.unsplash.com/photo-1464207687429-7505649dae38?w=600&h=400&fit=crop&q=80',
                    'fitosanitarios' => 'https://images.unsplash.com/photo-1464207687429-7505649dae38?w=600&h=400&fit=crop&q=80',
                    'hierros-y-soportes' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&h=400&fit=crop&q=80',
                    'mensulas' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=600&h=400&fit=crop&q=80',
                    'aros-y-ganchos' => 'https://images.unsplash.com/photo-1463320726281-696a485928c7?w=600&h=400&fit=crop&q=80',
                    'pies-y-bases' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=600&h=400&fit=crop&q=80',
                    'pies-nordicos' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=600&h=400&fit=crop&q=80'
                );
                
                if ($product_categories && !is_wp_error($product_categories)) {
                    foreach ($product_categories as $category) {
                        $category_slug = $category->slug;
                        if (isset($category_images[$category_slug])) {
                            return $category_images[$category_slug];
                        }
                    }
                }
                
                return 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&h=400&fit=crop&q=80';
            }
            
            function get_single_category_emoji($product_id) {
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
            
            $image_url = get_single_category_image(get_the_ID());
            $emoji = get_single_category_emoji(get_the_ID());
            $categories = wp_get_post_terms(get_the_ID(), 'product_cat');
            ?>
            
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <div class="flex items-center space-x-2 text-sm text-gray-500 font-body">
                    <a href="<?php echo home_url(); ?>" class="hover:text-green-600 transition-colors">Inicio</a>
                    <span>›</span>
                    <a href="<?php echo home_url('/?post_type=product'); ?>" class="hover:text-green-600 transition-colors">Tienda</a>
                    <?php if ($categories && !is_wp_error($categories)) : ?>
                        <span>›</span>
                        <a href="<?php echo home_url('/?post_type=product&product_cat=' . $categories[0]->slug); ?>" class="hover:text-green-600 transition-colors">
                            <?php echo $categories[0]->name; ?>
                        </a>
                    <?php endif; ?>
                    <span>›</span>
                    <span class="text-gray-900 font-medium"><?php the_title(); ?></span>
                </div>
            </nav>
            
            <!-- Producto Principal -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-12">
                <div class="grid lg:grid-cols-2 gap-0">
                    
                    <!-- Imagen del Producto -->
                    <div class="relative">
                        <div class="aspect-square lg:aspect-auto lg:h-full bg-gray-100 overflow-hidden">
                            <img src="<?php echo $image_url; ?>" 
                                 alt="<?php the_title(); ?>" 
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent"></div>
                            
                            <!-- Badge de categoría -->
                            <div class="absolute top-6 left-6">
                                <div class="bg-white/95 backdrop-blur-sm rounded-full px-4 py-2 flex items-center space-x-2 shadow-lg">
                                    <span class="text-xl"><?php echo $emoji; ?></span>
                                    <?php if ($categories && !is_wp_error($categories)) : ?>
                                        <span class="text-sm font-medium text-gray-700"><?php echo $categories[0]->name; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Badge de stock -->
                            <?php if ($product->is_in_stock()) : ?>
                                <div class="absolute top-6 right-6">
                                    <div class="bg-green-500 text-white rounded-full px-3 py-1 text-sm font-medium shadow-lg">
                                        ✓ En Stock
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="absolute top-6 right-6">
                                    <div class="bg-red-500 text-white rounded-full px-3 py-1 text-sm font-medium shadow-lg">
                                        ✗ Sin Stock
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Información del Producto -->
                    <div class="p-8 lg:p-12">
                        
                        <!-- Título y Precio -->
                        <div class="mb-8">
                            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4 font-display">
                                <?php the_title(); ?>
                            </h1>
                            
                            <div class="flex items-center space-x-4 mb-6">
                                <div class="text-4xl font-bold text-green-600 font-body">
                                    $<?php echo number_format($product->get_price(), 0, ',', '.'); ?>
                                </div>
                                <?php if ($product->is_on_sale()) : ?>
                                    <div class="text-2xl text-gray-400 line-through font-body">
                                        $<?php echo number_format($product->get_regular_price(), 0, ',', '.'); ?>
                                    </div>
                                    <div class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">
                                        ¡Oferta!
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Descripción -->
                        <?php if ($product->get_description()) : ?>
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 font-display">Descripción</h3>
                                <div class="text-gray-600 font-body leading-relaxed">
                                    <?php echo wp_kses_post($product->get_description()); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Información adicional -->
                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-2 font-display">SKU</h4>
                                <p class="text-gray-600 font-body"><?php echo $product->get_sku() ?: 'N/A'; ?></p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-2 font-display">Stock</h4>
                                <p class="text-gray-600 font-body">
                                    <?php 
                                    if ($product->managing_stock()) {
                                        echo $product->get_stock_quantity() . ' unidades';
                                    } else {
                                        echo $product->is_in_stock() ? 'Disponible' : 'Sin stock';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Formulario de compra -->
                        <?php if ($product->is_in_stock()) : ?>
                            <form class="woocommerce-cart-form mb-8" method="post" enctype="multipart/form-data">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                        <button type="button" onclick="decreaseQuantity()" class="px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors">
                                            <span class="text-gray-600">−</span>
                                        </button>
                                        <input type="number" name="quantity" id="quantity" value="1" min="1" 
                                               class="w-16 text-center py-3 border-0 focus:ring-0 font-body">
                                        <button type="button" onclick="increaseQuantity()" class="px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors">
                                            <span class="text-gray-600">+</span>
                                        </button>
                                    </div>
                                    
                                    <input type="hidden" name="add-to-cart" value="<?php echo $product->get_id(); ?>">
                                    <button type="submit" class="btn-clean btn-primary flex-1 py-4 text-lg">
                                        <span class="mr-2">🛒</span>
                                        Agregar al Carrito
                                    </button>
                                </div>
                            </form>
                        <?php else : ?>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-8">
                                <p class="text-red-700 font-body">Este producto no está disponible en este momento.</p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Información de envío -->
                        <div class="border-t border-gray-200 pt-8">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-green-600">🚚</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 font-display">Envío Gratis</h4>
                                        <p class="text-sm text-gray-600 font-body">En Mendoza el mismo día</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600">🛡️</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 font-display">Garantía</h4>
                                        <p class="text-sm text-gray-600 font-body">Plantas garantizadas</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-purple-600">💬</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 font-display">Asesoramiento</h4>
                                        <p class="text-sm text-gray-600 font-body">Expertos en jardinería</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            
            <!-- Productos relacionados -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-8 font-display">Productos Relacionados</h2>
                
                <?php
                // Obtener productos de la misma categoría
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
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <?php foreach ($related_products as $related_post) : 
                            $related_product = wc_get_product($related_post->ID);
                            if ($related_product) :
                                $related_image = get_single_category_image($related_post->ID);
                                $related_emoji = get_single_category_emoji($related_post->ID);
                        ?>
                            <div class="clean-card overflow-hidden group">
                                <div class="relative h-48 overflow-hidden">
                                    <img src="<?php echo $related_image; ?>" 
                                         alt="<?php echo $related_product->get_name(); ?>" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute top-3 left-3 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-sm">
                                        <?php echo $related_emoji; ?>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-900 mb-2 font-display line-clamp-2">
                                        <?php echo $related_product->get_name(); ?>
                                    </h3>
                                    <p class="text-green-600 font-bold text-lg mb-3 font-body">
                                        $<?php echo number_format($related_product->get_price(), 0, ',', '.'); ?>
                                    </p>
                                    <a href="<?php echo get_permalink($related_post->ID); ?>" 
                                       class="btn-clean btn-secondary w-full text-center">
                                        Ver Producto
                                    </a>
                                </div>
                            </div>
                        <?php endif; endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
        <?php endwhile; ?>
        
    </div>
</main>

<script>
function increaseQuantity() {
    const input = document.getElementById('quantity');
    input.value = parseInt(input.value) + 1;
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}
</script>

<?php get_footer(); ?> 