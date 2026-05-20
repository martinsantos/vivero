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
