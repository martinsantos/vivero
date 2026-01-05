<?php
/**
 * The template for displaying product content within loops
 *
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<li <?php wc_product_class('group relative bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 border border-gray-100', $product); ?>>
    <?php
    /**
     * Hook: woocommerce_before_shop_loop_item.
     *
     * @hooked woocommerce_template_loop_product_link_open - 10
     */
    do_action('woocommerce_before_shop_loop_item');

    /**
     * Hook: woocommerce_before_shop_loop_item_title.
     *
     * @hooked woocommerce_show_product_loop_sale_flash - 10
     * @hooked woocommerce_template_loop_product_thumbnail - 10
     */
    echo '<div class="relative overflow-hidden bg-gray-50 aspect-w-1 aspect-h-1">';
    do_action('woocommerce_before_shop_loop_item_title');
    echo '</div>';

    echo '<div class="p-4">';
    
    /**
     * Hook: woocommerce_shop_loop_item_title.
     *
     * @hooked woocommerce_template_loop_product_title - 10
     */
    echo '<h2 class="text-lg font-medium text-gray-900 mb-2 hover:text-green-600 transition-colors">';
    do_action('woocommerce_shop_loop_item_title');
    echo '</h2>';

    /**
     * Hook: woocommerce_after_shop_loop_item_title.
     *
     * @hooked woocommerce_template_loop_rating - 5
     * @hooked woocommerce_template_loop_price - 10
     */
    echo '<div class="mt-2">';
    do_action('woocommerce_after_shop_loop_item_title');
    echo '</div>';

    echo '</div>';

    /**
     * Hook: woocommerce_after_shop_loop_item.
     *
     * @hooked woocommerce_template_loop_product_link_close - 5
     * @hooked woocommerce_template_loop_add_to_cart - 10
     */
    echo '<div class="px-4 pb-4">';
    do_action('woocommerce_after_shop_loop_item');
    echo '</div>';
    ?>
</li>
