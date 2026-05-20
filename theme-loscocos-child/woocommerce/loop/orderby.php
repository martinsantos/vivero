<?php
/**
 * Show options to order the shop page
 *
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

if (!wc_get_loop_prop('is_paginated') || !woocommerce_products_will_display()) {
    return;
}

$orderby                 = isset($_GET['orderby']) ? wc_clean(wp_unslash($_GET['orderby'])) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
$show_default_orderby    = 'menu_order' === apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
$catalog_orderby_options = apply_filters(
    'woocommerce_catalog_orderby',
    array(
        'menu_order' => __('Orden recomendado', 'woocommerce'),
        'popularity' => __('Más vendidos', 'woocommerce'),
        'rating'     => __('Mejor valorados', 'woocommerce'),
        'date'       => __('Más recientes', 'woocommerce'),
        'price'      => __('Precio: menor a mayor', 'woocommerce'),
        'price-desc' => __('Precio: mayor a menor', 'woocommerce'),
    )
);

$default_orderby = wc_get_loop_prop('is_search') ? 'relevance' : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby', ''));
$orderby        = isset($_GET['orderby']) ? wc_clean(wp_unslash($_GET['orderby'])) : $default_orderby; // WPCS: sanitization ok, input var ok, CSRF ok.

if (wc_get_loop_prop('is_search')) {
    $catalog_orderby_options = array_merge(array('relevance' => __('Relevancia', 'woocommerce')), $catalog_orderby_options);
    unset($catalog_orderby_options['menu_order']);
}

if (!$show_default_orderby) {
    unset($catalog_orderby_options['menu_order']);
}

if ('no' === get_option('woocommerce_enable_review_rating')) {
    unset($catalog_orderby_options['rating']);
}

if (!array_key_exists($orderby, $catalog_orderby_options)) {
    $orderby = current(array_keys($catalog_orderby_options));
}

?>
<div class="mb-8 flex flex-col gap-4 rounded-lg border border-neutral-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
    <p class="text-sm font-medium text-neutral-medium">
        <?php
        $results = wc_get_loop_prop('total');
        if ($results <= 0) {
            _e('No products found', 'woocommerce');
        } else {
            printf(
                _n('%d producto disponible', '%d productos disponibles', $results, 'woocommerce'),
                $results
            );
        }
        ?>
    </p>
    
    <form class="woocommerce-ordering" method="get">
        <div class="relative">
            <select name="orderby" class="block min-h-[44px] w-full appearance-none rounded-full border border-neutral-200 bg-white py-2 pl-4 pr-10 text-sm font-semibold text-neutral-dark focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" aria-label="<?php esc_attr_e('Shop order', 'woocommerce'); ?>">
                <?php foreach ($catalog_orderby_options as $id => $name) : ?>
                    <option value="<?php echo esc_attr($id); ?>" <?php selected($orderby, $id); ?>><?php echo esc_html($name); ?></option>
                <?php endforeach; ?>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-neutral-medium">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                </svg>
            </div>
        </div>
        <input type="hidden" name="paged" value="1" />
        <?php wc_query_string_form_fields(null, array('orderby', 'submit', 'paged', 'product-page')); ?>
    </form>
</div>
