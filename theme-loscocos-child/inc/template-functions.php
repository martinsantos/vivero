<?php
/**
 * Template Functions - Los Cocos Child Theme
 * Helper functions for use in templates
 * 
 * @package LosCocos_Child
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get product image URL with fallback
 * 
 * @param int $product_id Product ID
 * @param string $size Image size
 * @return string Image URL
 */
function loscocos_get_product_image($product_id, $size = 'woocommerce_thumbnail') {
    $product = wc_get_product($product_id);
    if (!$product) {
        return wc_placeholder_img_src($size);
    }
    
    $image_id = $product->get_image_id();
    if ($image_id) {
        return wp_get_attachment_image_url($image_id, $size);
    }
    
    return wc_placeholder_img_src($size);
}

/**
 * Get product categories as string
 * 
 * @param int $product_id Product ID
 * @param string $separator Category separator
 * @return string Categories
 */
function loscocos_get_product_categories($product_id, $separator = ', ') {
    $categories = wp_get_post_terms($product_id, 'product_cat');
    if (!$categories || is_wp_error($categories)) {
        return '';
    }
    
    $names = array_map(function($cat) {
        return $cat->name;
    }, $categories);
    
    return implode($separator, $names);
}

/**
 * Format price for display
 * 
 * @param float $price Price value
 * @return string Formatted price
 */
function loscocos_format_price($price) {
    return '$' . number_format($price, 0, ',', '.');
}

/**
 * Get truncated description
 * 
 * @param string $text Text to truncate
 * @param int $words Word count
 * @return string Truncated text
 */
function loscocos_truncate_text($text, $words = 15) {
    return wp_trim_words($text, $words, '...');
}

/**
 * Check if product is low on stock
 * 
 * @param WC_Product $product Product object
 * @param int $threshold Low stock threshold
 * @return bool Is low stock
 */
function loscocos_is_low_stock($product, $threshold = 5) {
    $stock = $product->get_stock_quantity();
    return $stock && $stock <= $threshold;
}
