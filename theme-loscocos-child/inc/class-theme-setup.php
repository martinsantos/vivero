<?php
/**
 * Theme Setup Class - Los Cocos Child Theme
 * Handles all theme initialization and configuration
 * 
 * @package LosCocos_Child
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class LosCocos_Theme_Setup {
    
    /**
     * Initialize the theme setup
     */
    public static function init() {
        // Enqueue styles and scripts
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_assets']);

        // Theme supports
        add_action('after_setup_theme', [self::class, 'theme_supports']);

        // Lazy loading for all images
        add_filter('wp_get_attachment_image_attributes', [self::class, 'add_lazy_loading'], 10, 3);

        // Keep retired feature routes focused on the commerce journey.
        add_action('template_redirect', [self::class, 'redirect_retired_routes']);
    }

    /**
     * Redirect retired feature routes back into the buying journey.
     */
    public static function redirect_retired_routes() {
        if (is_admin()) {
            return;
        }

        $path = isset($_SERVER['REQUEST_URI']) ? wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH) : '';
        $path = is_string($path) ? untrailingslashit($path) : '';

        if ('/jardin' === $path) {
            $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
            wp_safe_redirect($shop_url, 301);
            exit;
        }

        if ('/carrito' === $path && function_exists('wc_get_cart_url')) {
            wp_safe_redirect(wc_get_cart_url(), 301);
            exit;
        }

        if ('/finalizar-compra' === $path && function_exists('wc_get_checkout_url')) {
            wp_safe_redirect(wc_get_checkout_url(), 301);
            exit;
        }

        if ('/mi-cuenta' === $path && function_exists('wc_get_page_permalink')) {
            wp_safe_redirect(wc_get_page_permalink('myaccount'), 301);
            exit;
        }
    }

    /**
     * Add lazy loading and dimensions to all WordPress images
     */
    public static function add_lazy_loading($attr, $attachment, $size) {
        // Skip if already set or if it's the admin
        if (is_admin() || isset($attr['loading'])) {
            return $attr;
        }
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
        return $attr;
    }
    
    /**
     * Enqueue theme assets
     */
    public static function enqueue_assets() {
        // Resource hints for Google Fonts
        add_action('wp_head', function() {
            echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
            echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
            echo '<link rel="dns-prefetch" href="https://fonts.googleapis.com">' . "\n";
            echo '<link rel="dns-prefetch" href="https://fonts.gstatic.com">' . "\n";
        }, 1);

        // Google Fonts
        wp_enqueue_style(
            'loscocos-google-fonts',
            'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700;900&family=Outfit:wght@300;400;500;600;700;800&display=swap',
            [],
            null
        );

        // Parent theme styles
        wp_enqueue_style('loscocos-style', get_template_directory_uri() . '/style.css');

        // Child theme styles
        $child_css = get_stylesheet_directory() . '/style.css';
        $version = file_exists($child_css) ? filemtime($child_css) : LOSCOCOS_CHILD_VERSION;
        wp_enqueue_style(
            'loscocos-child-style',
            get_stylesheet_directory_uri() . '/style.css',
            ['loscocos-style'],
            $version
        );

        // Category navigation styles
        $cat_nav_file = get_stylesheet_directory() . '/assets/css/category-navigation.css';
        if (file_exists($cat_nav_file)) {
            wp_enqueue_style(
                'loscocos-category-nav',
                get_stylesheet_directory_uri() . '/assets/css/category-navigation.css',
                ['loscocos-child-style'],
                filemtime($cat_nav_file)
            );
        }

        // Product infographic styles
        $infographic_file = get_stylesheet_directory() . '/assets/css/product-infographic.css';
        if (file_exists($infographic_file)) {
            wp_enqueue_style(
                'loscocos-product-infographic',
                get_stylesheet_directory_uri() . '/assets/css/product-infographic.css',
                ['loscocos-child-style'],
                filemtime($infographic_file)
            );
        }

        // Custom JavaScript (no jQuery dependency needed)
        $main_js = get_stylesheet_directory() . '/assets/js/main.js';
        if (file_exists($main_js)) {
            wp_enqueue_script(
                'loscocos-child-scripts',
                get_stylesheet_directory_uri() . '/assets/js/main.js',
                [],
                filemtime($main_js),
                true
            );
        }

        // Single product JavaScript (product pages only)
        if (is_product()) {
            $single_product_js = get_stylesheet_directory() . '/assets/js/single-product.js';
            if (file_exists($single_product_js)) {
                wp_enqueue_script(
                    'loscocos-single-product',
                    get_stylesheet_directory_uri() . '/assets/js/single-product.js',
                    [],
                    filemtime($single_product_js),
                    true
                );
            }
        }
    }
    
    /**
     * Add theme supports
     */
    public static function theme_supports() {
        // WooCommerce support
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
        
        // HTML5 support
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption'
        ]);
        
        // Title tag
        add_theme_support('title-tag');
        
        // Post thumbnails
        add_theme_support('post-thumbnails');
    }
}
