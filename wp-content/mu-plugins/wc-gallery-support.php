<?php
/*
Plugin Name: WC Gallery Support
Description: Habilita zoom, lightbox y carrusel en la galería de productos de WooCommerce.
Version: 1.0
*/

add_action('after_setup_theme', function () {
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}, 20);
