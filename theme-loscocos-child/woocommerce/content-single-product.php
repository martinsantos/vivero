<?php
/**
 * The template for displaying product content in the single-product.php template
 * Custom version matching the mockup design
 *
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form();
    return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class('bg-cream-light', $product); ?>>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start">

        <!-- Product Images -->
        <div class="product-gallery">
            <?php
            do_action('woocommerce_before_single_product_summary');
            ?>
        </div>

        <!-- Product Summary -->
        <div class="product-summary space-y-6">
            <?php
            // Title
            do_action('woocommerce_template_single_title');

            // Price
            do_action('woocommerce_template_single_price');

            // Excerpt/Short Description
            do_action('woocommerce_template_single_excerpt');
            ?>

            <!-- Product Features with Icons (matching mockup) -->
            <div class="product-features space-y-4 py-6 border-t border-b border-neutral-300">
                <!-- Feature 1: Light -->
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-primary-light rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-neutral-dark mb-1">Luz necesaria</h4>
                        <p class="text-sm text-neutral-medium">Sol directo o sombra parcial</p>
                    </div>
                </div>

                <!-- Feature 2: Water -->
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-accent-light rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-neutral-dark mb-1">Riego</h4>
                        <p class="text-sm text-neutral-medium">Moderado, 2-3 veces por semana</p>
                    </div>
                </div>

                <!-- Feature 3: Care Level -->
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-neutral-dark mb-1">Nivel de cuidado</h4>
                        <p class="text-sm text-neutral-medium">Fácil - Ideal para principiantes</p>
                    </div>
                </div>
            </div>

            <?php
            // Add to cart
            do_action('woocommerce_template_single_add_to_cart');

            // Meta (SKU, Categories, Tags)
            do_action('woocommerce_template_single_meta');
            ?>
        </div>
    </div>

    <?php
    /**
     * Hook: woocommerce_after_single_product_summary.
     *
     * @hooked woocommerce_output_product_data_tabs - 10
     * @hooked woocommerce_upsell_display - 15
     * @hooked woocommerce_output_related_products - 20
     */
    do_action('woocommerce_after_single_product_summary');
    ?>
</div>

<?php do_action('woocommerce_after_single_product'); ?>