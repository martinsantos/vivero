<?php
/**
 * The template for displaying single products
 */

get_header();

// Get the product object
global $product;
if (!$product) {
    $product = wc_get_product(get_the_ID());
}

// Get product data
$product_id = $product->get_id();
$categories = get_the_terms($product_id, 'product_cat');
$image_id = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();
$has_gallery = !empty($gallery_ids);
?>

<main class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <div class="flex items-center space-x-2 text-sm text-gray-500 font-body">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-green-600 transition-colors">Inicio</a>
                <span>›</span>
                <a href="<?php echo esc_url(home_url('/tienda')); ?>" class="hover:text-green-600 transition-colors">Tienda</a>
                <?php if ($categories && !is_wp_error($categories)) : ?>
                    <span>›</span>
                    <a href="<?php echo esc_url(get_term_link($categories[0])); ?>" class="hover:text-green-600 transition-colors">
                        <?php echo esc_html($categories[0]->name); ?>
                    </a>
                <?php endif; ?>
                <span>›</span>
                <span class="text-gray-900 font-medium"><?php the_title(); ?></span>
            </div>
        </nav>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-12">
            <div class="grid lg:grid-cols-2 gap-0">
                <!-- Product Gallery -->
                <div class="relative">
                    <div class="aspect-square bg-gray-100 overflow-hidden relative">
                        <?php if ($image_id) : ?>
                            <img id="main-product-image" 
                                 src="<?php echo esc_url(wp_get_attachment_image_url($image_id, 'woocommerce_single')); ?>" 
                                 alt="<?php echo esc_attr($product->get_name()); ?>"
                                 class="w-full h-full object-cover transition-opacity duration-300">
                        <?php else : ?>
                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                <span class="material-icons text-gray-400 text-6xl">image_not_supported</span>
                            </div>
                        <?php endif; ?>

                        <?php if ($has_gallery) : ?>
                            <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
                                <button id="prev-image" class="gallery-nav bg-white/80 rounded-full p-2 shadow-md hover:bg-white transition-colors">
                                    <span class="material-icons">chevron_left</span>
                                </button>
                                <button id="next-image" class="gallery-nav bg-white/80 rounded-full p-2 shadow-md hover:bg-white transition-colors">
                                    <span class="material-icons">chevron_right</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($has_gallery) : ?>
                        <div class="flex space-x-2 p-4 overflow-x-auto">
                            <div class="flex-shrink-0 w-20 h-20 border-2 border-transparent hover:border-green-500 rounded-md overflow-hidden cursor-pointer transition-colors" 
                                 onclick="document.getElementById('main-product-image').src='<?php echo esc_url(wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail')); ?>'">
                                <?php echo wp_get_attachment_image($image_id, 'woocommerce_thumbnail', false, ['class' => 'w-full h-full object-cover']); ?>
                            </div>
                            <?php foreach ($gallery_ids as $gallery_id) : ?>
                                <div class="flex-shrink-0 w-20 h-20 border-2 border-transparent hover:border-green-500 rounded-md overflow-hidden cursor-pointer transition-colors"
                                     onclick="document.getElementById('main-product-image').src='<?php echo esc_url(wp_get_attachment_image_url($gallery_id, 'woocommerce_single')); ?>'">
                                    <?php echo wp_get_attachment_image($gallery_id, 'woocommerce_thumbnail', false, ['class' => 'w-full h-full object-cover']); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Product Info -->
                <div class="p-6 md:p-8 lg:p-12">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        <?php the_title(); ?>
                    </h1>

                    <div class="flex items-center space-x-4 mb-6">
                        <?php if ($product->is_on_sale()) : ?>
                            <div class="flex items-baseline space-x-2">
                                <span class="text-2xl font-bold text-red-600">
                                    <?php echo wc_price($product->get_sale_price()); ?>
                                </span>
                                <span class="text-lg text-gray-500 line-through">
                                    <?php echo wc_price($product->get_regular_price()); ?>
                                </span>
                            </div>
                        <?php else : ?>
                            <span class="text-2xl font-bold text-gray-900">
                                <?php echo wc_price($product->get_price()); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($product->is_in_stock()) : ?>
                            <span class="text-sm bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                <?php echo esc_html($product->get_stock_quantity()); ?> disponibles
                            </span>
                        <?php else : ?>
                            <span class="text-sm bg-red-100 text-red-800 px-2 py-1 rounded-full">
                                Agotado
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="prose max-w-none text-gray-600 mb-8">
                        <?php the_content(); ?>
                    </div>

                    <?php if ($product->is_in_stock()) : ?>
                        <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype="multipart/form-data">
                            <?php 
                            do_action('woocommerce_before_add_to_cart_button');
                            
                            if ($product->is_type('variable')) {
                                woocommerce_variable_add_to_cart();
                            } else {
                                woocommerce_quantity_input(array(
                                    'min_value' => 1,
                                    'max_value' => $product->get_max_purchase_quantity(),
                                    'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : 1,
                                ));
                                
                                echo sprintf(
                                    '<button type="submit" name="add-to-cart" value="%s" class="single_add_to_cart_button button alt bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md font-medium transition-colors">%s</button>',
                                    esc_attr($product->get_id()),
                                    esc_html($product->single_add_to_cart_text())
                                );
                            }
                            
                            do_action('woocommerce_after_add_to_cart_button');
                            ?>
                        </form>
                    <?php else : ?>
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                            <p class="text-red-700">Este producto no está disponible actualmente.</p>
                        </div>
                    <?php endif; ?>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <span class="material-icons text-green-600">local_shipping</span>
                            <span>Envío a todo el país</span>
                        </div>
                        <div class="mt-2 flex items-center space-x-2 text-sm text-gray-600">
                            <span class="material-icons text-green-600">security</span>
                            <span>Pago seguro</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-12">
            <?php wc_get_template('single-product/tabs/tabs.php'); ?>
        </div>

        <!-- Related Products -->
        <?php
        $related_products = wc_get_related_products($product_id, 4);
        if (!empty($related_products)) : ?>
            <div class="mb-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Productos Relacionados</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php
                    $args = array(
                        'post_type' => 'product',
                        'post__in' => $related_products,
                        'posts_per_page' => 4,
                        'orderby' => 'post__in'
                    );
                    $related_query = new WP_Query($args);

                    if ($related_query->have_posts()) :
                        while ($related_query->have_posts()) : $related_query->the_post();
                            wc_get_template_part('content', 'product');
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>

<script>
// Simple gallery navigation
jQuery(document).ready(function($) {
    let currentImageIndex = 0;
    const galleryImages = [
        '<?php echo esc_js(wp_get_attachment_image_url($image_id, 'woocommerce_single')); ?>',
        <?php foreach ($gallery_ids as $gallery_id) : ?>
            '<?php echo esc_js(wp_get_attachment_image_url($gallery_id, 'woocommerce_single')); ?>',
        <?php endforeach; ?>
    ];

    function updateMainImage(index) {
        if (index >= 0 && index < galleryImages.length) {
            currentImageIndex = index;
            $('#main-product-image').attr('src', galleryImages[currentImageIndex]);
        }
    }

    $('#next-image').on('click', function() {
        updateMainImage((currentImageIndex + 1) % galleryImages.length);
    });

    $('#prev-image').on('click', function() {
        updateMainImage((currentImageIndex - 1 + galleryImages.length) % galleryImages.length);
    });
});
</script>
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
            <div class="mt-16 mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-8 font-display">También te puede interesar</h2>
                <?php
                $related_products = wc_get_related_products($product->get_id(), 4);
                if (!empty($related_products)) :
                    $args = array(
                        'post_type' => 'product',
                        'post__in' => $related_products,
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