<?php
/**
 * Loop Product Thumbnails
 *
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get the product thumbnail or the placeholder image if not set
$thumbnail = woocommerce_get_product_thumbnail('woocommerce_thumbnail');

if (!$thumbnail) {
    $thumbnail = '<img src="' . wc_placeholder_img_src('woocommerce_thumbnail') . '" alt="' . esc_attr__('Placeholder', 'woocommerce') . '" class="w-full h-auto object-cover" />';
}

echo '<div class="w-full h-48 md:h-56 flex items-center justify-center p-4 bg-white">' . $thumbnail . '</div>';
