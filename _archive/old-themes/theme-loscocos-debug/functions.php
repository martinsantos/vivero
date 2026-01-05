<?php
if (!defined('ABSPATH')) {
    exit;
}

function loscocos_child_enqueue_styles() {
    wp_enqueue_style('loscocos-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('loscocos-child-style', get_stylesheet_directory_uri() . '/style.css', array('loscocos-style'));
}
add_action('wp_enqueue_scripts', 'loscocos_child_enqueue_styles');

function loscocos_child_woocommerce_setup() {
    add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'loscocos_child_woocommerce_setup');

function loscocos_child_enqueue_scripts() {
    wp_dequeue_script('loscocos-cart');
    wp_deregister_script('loscocos-cart');
    
    if (function_exists('is_woocommerce')) {
        wp_enqueue_script('wc-cart-fragments');
        wp_enqueue_script('woocommerce');
        wp_enqueue_script('wc-add-to-cart');
    }
    
    wp_enqueue_script(
        'loscocos-child-scripts',
        get_stylesheet_directory_uri() . '/assets/js/main.js',
        array('jquery', 'wc-add-to-cart', 'wc-cart-fragments'),
        '1.0.0',
        true
    );
    
    wp_localize_script('loscocos-child-scripts', 'wc_add_to_cart_params', array(
        'ajax_url' => function_exists('WC') ? WC()->ajax_url() : admin_url('admin-ajax.php'),
        'wc_ajax_url' => class_exists('WC_AJAX') ? WC_AJAX::get_endpoint('%%endpoint%%') : '',
        'i18n_view_cart' => 'View cart',
        'i18n_added_to_cart' => 'Added to cart',
        'i18n_failed_to_add_to_cart' => 'Failed to add to cart',
        'cart_url' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : '',
        'is_cart' => is_cart() ? '1' : '0',
        'cart_redirect_after_add' => get_option('woocommerce_cart_redirect_after_add'),
        'add_to_cart_nonce' => wp_create_nonce('add-to-cart-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'loscocos_child_enqueue_scripts', 20);

function loscocos_child_woocommerce_placeholder_img_src($src) {
    $placeholder = get_site_url() . '/wp-content/uploads/woocommerce-placeholder.webp';
    return $placeholder;
}
add_filter('woocommerce_placeholder_img_src', 'loscocos_child_woocommerce_placeholder_img_src');

function loscocos_child_loop_product_thumbnail() {
    global $product;
    if (!$product) return;
    
    $product_id = $product->get_id();
    $upload_dir = wp_upload_dir();
    $svg_url = $upload_dir['baseurl'] . '/loscocos-images/' . $product_id . '.svg';
    
    echo '<div class="woocommerce-loop-product__image">';
    echo '<img src="' . esc_url($svg_url) . '" alt="' . esc_attr($product->get_name()) . '" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail wp-post-image" loading="lazy" />';
    echo '</div>';
}

// remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action('woocommerce_before_shop_loop_item_title', 'loscocos_child_loop_product_thumbnail', 10);

function loscocos_child_single_product_image($image, $product_id) {
    $upload_dir = wp_upload_dir();
    $svg_url = $upload_dir['baseurl'] . '/loscocos-images/' . $product_id . '.svg';
    return '<img src="' . esc_url($svg_url) . '" alt="' . esc_attr(get_the_title($product_id)) . '" class="wp-post-image" />';
}
add_filter('woocommerce_single_product_image_thumbnail_html', 'loscocos_child_single_product_image', 20, 2);
add_filter('woocommerce_single_product_image_html', 'loscocos_child_single_product_image', 20, 2);

function loscocos_child_image_sizes() {
    add_image_size('woocommerce_thumbnail', 300, 300, true);
    add_image_size('woocommerce_single', 600, 600, true);
    add_image_size('woocommerce_gallery_thumbnail', 100, 100, true);
}
add_action('after_setup_theme', 'loscocos_child_image_sizes');

function loscocos_child_woocommerce_placeholder_img($image_html, $size, $dimensions) {
    $placeholder_src = function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src() : '';
    $image = '<img src="' . esc_url($placeholder_src) . '" alt="' . esc_attr__('Placeholder', 'woocommerce') . '" class="woocommerce-placeholder wp-post-image" width="' . esc_attr($dimensions['width']) . '" height="' . esc_attr($dimensions['height']) . '" />';
    return $image;
}
add_filter('woocommerce_placeholder_img', 'loscocos_child_woocommerce_placeholder_img', 10, 3);
