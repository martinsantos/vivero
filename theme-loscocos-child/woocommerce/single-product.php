<?php
/**
 * The Template for displaying all single products
 * COMPLETE VERSION matching mockup and shop design
 *
 * @package WooCommerce\Templates
 * @version 1.6.4
 */

defined('ABSPATH') || exit;

get_header('shop');

do_action('woocommerce_before_main_content');
?>

<main class="bg-cream-light min-h-screen">
    <div class="container mx-auto px-4 py-8 md:py-12">
        <div class="max-w-7xl mx-auto">
            
            <?php while (have_posts()): the_post(); ?>
                <?php 
                global $product;
                if (!$product) {
                    $product = wc_get_product(get_the_ID());
                }
                ?>
                
                <?php do_action('woocommerce_before_single_product'); ?>
                
                <div id="product-<?php the_ID(); ?>" <?php wc_product_class('', $product); ?>>
                    
                    <!-- Main Product Section -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden p-6 md:p-10 mb-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                            
                            <!-- LEFT: Product Gallery -->
                            <div class="product-gallery">
                                <?php
                                if (has_post_thumbnail()) {
                                    $image_id = get_post_thumbnail_id();
                                    $image_url = wp_get_attachment_image_url($image_id, 'large');
                                    ?>
                                    <div class="aspect-square rounded-xl overflow-hidden bg-neutral-100 border border-neutral-200">
                                        <img src="<?php echo esc_url($image_url); ?>" 
                                             alt="<?php echo esc_attr(get_the_title()); ?>" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <?php
                                    $gallery_ids = $product->get_gallery_image_ids();
                                    if (!empty($gallery_ids)) {
                                        ?>
                                        <div class="grid grid-cols-4 gap-3 mt-4">
                                            <div class="aspect-square rounded-lg overflow-hidden border-2 border-primary cursor-pointer">
                                                <img src="<?php echo esc_url(wp_get_attachment_image_url($image_id, 'thumbnail')); ?>" 
                                                     alt="" class="w-full h-full object-cover">
                                            </div>
                                            <?php foreach (array_slice($gallery_ids, 0, 3) as $gallery_id): ?>
                                                <div class="aspect-square rounded-lg overflow-hidden border border-neutral-200 hover:border-primary cursor-pointer transition-colors">
                                                    <img src="<?php echo esc_url(wp_get_attachment_image_url($gallery_id, 'thumbnail')); ?>" 
                                                         alt="" class="w-full h-full object-cover">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo '<div class="aspect-square rounded-xl bg-neutral-100 flex items-center justify-center"><span class="text-neutral-400">Sin imagen</span></div>';
                                }
                                ?>
                            </div>

                            <!-- RIGHT: Product Info -->
                            <div class="product-info space-y-6">
                                
                                <h1 class="text-3xl md:text-4xl font-serif font-bold text-primary-dark leading-tight">
                                    <?php the_title(); ?>
                                </h1>
                                
                                <div class="text-3xl font-bold text-accent">
                                    <?php echo $product->get_price_html(); ?>
                                </div>
                                
                                <?php if ($product->get_short_description()): ?>
                                    <div class="text-neutral-medium leading-relaxed">
                                        <?php echo wpautop($product->get_short_description()); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex items-center gap-2">
                                    <?php if ($product->is_in_stock()): ?>
                                        <span class="inline-flex items-center gap-1 text-green-600 font-medium">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            En stock
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 text-red-600 font-medium">Sin stock</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Product Features - Dynamic Infographic -->
                                <?php 
                                // Usar el sistema dinámico de infografía que detecta la categoría
                                if (function_exists('loscocos_render_product_infographic')) {
                                    loscocos_render_product_infographic();
                                }
                                ?>

                                <!-- Add to Cart -->
                                <div class="pt-4">
                                    <?php if ($product->is_purchasable() && $product->is_in_stock()): ?>
                                        <form class="cart flex items-center gap-4" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
                                            <div class="quantity-wrapper flex items-center border border-neutral-300 rounded-lg overflow-hidden">
                                                <button type="button" class="qty-btn minus w-12 h-12 flex items-center justify-center text-xl font-bold text-neutral-600 hover:bg-neutral-100 transition-colors">−</button>
                                                <input type="number" id="quantity_<?php echo $product->get_id(); ?>" 
                                                       class="input-text qty text w-16 h-12 text-center border-x border-neutral-300 font-medium" 
                                                       name="quantity" value="1" min="1" max="<?php echo $product->get_max_purchase_quantity(); ?>" step="1">
                                                <button type="button" class="qty-btn plus w-12 h-12 flex items-center justify-center text-xl font-bold text-neutral-600 hover:bg-neutral-100 transition-colors">+</button>
                                            </div>
                                            <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" 
                                                    class="flex-1 bg-primary hover:bg-primary-dark text-white font-bold py-4 px-8 rounded-full transition-all duration-300 shadow-lg text-lg uppercase tracking-wide">
                                                Agregar al Carrito
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>

                                <!-- Meta -->
                                <div class="pt-4 space-y-2 text-sm text-neutral-medium border-t border-neutral-100">
                                    <?php if ($product->get_sku()): ?>
                                        <p><span class="font-medium text-neutral-dark">SKU:</span> <?php echo esc_html($product->get_sku()); ?></p>
                                    <?php endif; ?>
                                    <?php 
                                    $categories = wc_get_product_category_list($product->get_id(), ', ');
                                    if ($categories): ?>
                                        <p><span class="font-medium text-neutral-dark">Categoría:</span> <?php echo $categories; ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Tabs - Real tabs with JavaScript -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden p-6 md:p-10 mb-8">
                        <div class="product-tabs-custom">
                            <!-- Tab Headers -->
                            <div class="tab-headers flex border-b border-neutral-200 mb-6">
                                <button class="tab-btn active px-6 py-4 font-semibold text-neutral-500 border-b-2 border-transparent hover:text-primary transition-colors" data-tab="description">
                                    Descripción
                                </button>
                                <button class="tab-btn px-6 py-4 font-semibold text-neutral-500 border-b-2 border-transparent hover:text-primary transition-colors" data-tab="additional">
                                    Información adicional
                                </button>
                                <button class="tab-btn px-6 py-4 font-semibold text-neutral-500 border-b-2 border-transparent hover:text-primary transition-colors" data-tab="reviews">
                                    Valoraciones (<?php echo $product->get_review_count(); ?>)
                                </button>
                            </div>
                            
                            <!-- Tab Contents -->
                            <div class="tab-content active" id="tab-description">
                                <div class="prose max-w-none text-neutral-600 leading-relaxed">
                                    <?php the_content(); ?>
                                </div>
                            </div>
                            
                            <div class="tab-content hidden" id="tab-additional">
                                <?php 
                                $attributes = $product->get_attributes();
                                if (!empty($attributes)): ?>
                                    <table class="w-full">
                                        <tbody>
                                            <?php foreach ($attributes as $attribute): 
                                                if (!$attribute->get_visible()) continue;
                                                ?>
                                                <tr class="border-b border-neutral-100">
                                                    <th class="py-3 pr-4 text-left font-semibold text-neutral-dark w-1/3 bg-neutral-50 px-4">
                                                        <?php echo wc_attribute_label($attribute->get_name()); ?>
                                                    </th>
                                                    <td class="py-3 px-4 text-neutral-600">
                                                        <?php
                                                        $values = array();
                                                        if ($attribute->is_taxonomy()) {
                                                            $attribute_taxonomy = $attribute->get_taxonomy_object();
                                                            $attribute_values = wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'all'));
                                                            foreach ($attribute_values as $attribute_value) {
                                                                $values[] = esc_html($attribute_value->name);
                                                            }
                                                        } else {
                                                            $values = $attribute->get_options();
                                                        }
                                                        echo implode(', ', $values);
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <p class="text-neutral-500">No hay información adicional disponible.</p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="tab-content hidden" id="tab-reviews">
                                <?php comments_template(); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Related Products - Matching Shop Card Style -->
                    <?php
                    $related_products = wc_get_related_products($product->get_id(), 4);
                    if (!empty($related_products)): ?>
                        <section class="related-products-section bg-white rounded-2xl shadow-lg p-6 md:p-10">
                            <div class="text-center mb-8">
                                <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary-dark mb-2">Productos Relacionados</h2>
                                <p class="text-neutral-medium">Otros productos que te pueden interesar</p>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                <?php foreach ($related_products as $related_id):
                                    $related = wc_get_product($related_id);
                                    if (!$related) continue;
                                    $categories = wc_get_product_category_list($related_id, ', ');
                                    ?>
                                    <!-- Card matching shop style -->
                                    <div class="product-card-shop bg-white rounded-2xl shadow-md overflow-hidden border border-neutral-100 hover:shadow-xl transition-all duration-300 flex flex-col">
                                        <!-- Image -->
                                        <a href="<?php echo esc_url($related->get_permalink()); ?>" class="block aspect-square overflow-hidden bg-neutral-100">
                                            <?php if ($related->get_image_id()): ?>
                                                <img src="<?php echo esc_url(wp_get_attachment_image_url($related->get_image_id(), 'woocommerce_thumbnail')); ?>" 
                                                     alt="<?php echo esc_attr($related->get_name()); ?>"
                                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                                            <?php else: ?>
                                                <div class="w-full h-full flex items-center justify-center text-neutral-400">Sin imagen</div>
                                            <?php endif; ?>
                                        </a>
                                        
                                        <!-- Content -->
                                        <div class="p-4 flex flex-col flex-grow">
                                            <!-- Title -->
                                            <a href="<?php echo esc_url($related->get_permalink()); ?>" class="block">
                                                <h3 class="font-semibold text-neutral-dark mb-1 line-clamp-2 hover:text-primary transition-colors min-h-[2.5rem]">
                                                    <?php echo esc_html($related->get_name()); ?>
                                                </h3>
                                            </a>
                                            
                                            <!-- Category -->
                                            <?php if ($categories): ?>
                                                <p class="text-xs text-neutral-400 uppercase tracking-wide mb-2"><?php echo strip_tags($categories); ?></p>
                                            <?php endif; ?>
                                            
                                            <!-- Short description -->
                                            <div class="h-10 mb-3">
                                                <?php if ($related->get_short_description()): ?>
                                                    <p class="text-sm text-neutral-500 line-clamp-2"><?php echo wp_trim_words($related->get_short_description(), 10); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="mt-auto">
                                                <!-- Price and Button Row -->
                                                <div class="flex items-center justify-between gap-2 mb-3">
                                                    <span class="text-xl font-bold text-primary"><?php echo $related->get_price_html(); ?></span>
                                                    <a href="<?php echo esc_url($related->add_to_cart_url()); ?>" 
                                                       class="inline-flex items-center gap-1 px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                        </svg>
                                                        Añadir
                                                    </a>
                                                </div>
                                                
                                                <!-- Stock and SKU -->
                                                <div class="flex items-center justify-between text-xs text-neutral-400">
                                                    <span class="<?php echo $related->is_in_stock() ? 'text-green-600' : 'text-red-500'; ?>">
                                                        <?php echo $related->is_in_stock() ? 'En stock' : 'Sin stock'; ?>
                                                    </span>
                                                    <?php if ($related->get_sku()): ?>
                                                        <span>SKU: <?php echo esc_html($related->get_sku()); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                </div>
                
                <?php do_action('woocommerce_after_single_product'); ?>
                
            <?php endwhile; ?>
            
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity buttons
    document.querySelectorAll('.qty-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = this.parentNode.querySelector('input.qty');
            var currentVal = parseInt(input.value) || 1;
            var max = parseInt(input.getAttribute('max')) || 9999;
            var min = parseInt(input.getAttribute('min')) || 1;
            
            if (this.classList.contains('plus')) {
                if (currentVal < max) input.value = currentVal + 1;
            } else {
                if (currentVal > min) input.value = currentVal - 1;
            }
        });
    });
    
    // Custom Tabs
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tabId = this.getAttribute('data-tab');
            
            // Update buttons
            document.querySelectorAll('.tab-btn').forEach(function(b) {
                b.classList.remove('active', 'text-primary', 'border-primary');
                b.classList.add('text-neutral-500', 'border-transparent');
            });
            this.classList.add('active', 'text-primary', 'border-primary');
            this.classList.remove('text-neutral-500', 'border-transparent');
            
            // Update content
            document.querySelectorAll('.tab-content').forEach(function(c) {
                c.classList.add('hidden');
                c.classList.remove('active');
            });
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            document.getElementById('tab-' + tabId).classList.add('active');
        });
    });
    
    // Initialize first tab
    var firstTab = document.querySelector('.tab-btn');
    if (firstTab) {
        firstTab.classList.add('text-primary', 'border-primary');
    }
});
</script>

<style>
.tab-btn.active {
    color: #2d5a3d !important;
    border-bottom-color: #2d5a3d !important;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.product-card-shop {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.product-card-shop:hover {
    transform: translateY(-4px);
}
</style>

<?php
do_action('woocommerce_after_main_content');
get_footer('shop');
