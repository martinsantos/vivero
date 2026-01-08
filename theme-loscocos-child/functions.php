<?php
/**
 * Los Cocos Child Theme functions and definitions
 *
 * @package Los_Cocos_Child
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('LOSCOCOS_CHILD_VERSION', '1.0.0');
define('LOSCOCOS_CHILD_PATH', get_stylesheet_directory());
define('LOSCOCOS_CHILD_URI', get_stylesheet_directory_uri());

/**
 * Enqueue scripts and styles
 */
function loscocos_child_enqueue_styles()
{
    // Enqueue Google Fonts
    wp_enqueue_style(
        'loscocos-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700;900&family=Outfit:wght@300;400;500;700&display=swap',
        array(),
        null
    );

    // Enqueue parent theme styles
    wp_enqueue_style('loscocos-style', get_template_directory_uri() . '/style.css');

    // Enqueue child theme styles with cache busting
    $version = '1.0.3-' . time();
    wp_enqueue_style(
        'loscocos-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('loscocos-style'),
        $version
    );
    
    // Enqueue premium category navigation styles
    wp_enqueue_style(
        'loscocos-category-nav',
        get_stylesheet_directory_uri() . '/assets/css/category-navigation.css',
        array('loscocos-child-style'),
        $version
    );
    
    // Enqueue product infographic styles
    wp_enqueue_style(
        'loscocos-product-infographic',
        get_stylesheet_directory_uri() . '/assets/css/product-infographic.css',
        array('loscocos-child-style'),
        $version
    );
}
add_action('wp_enqueue_scripts', 'loscocos_child_enqueue_styles');

// FORCE CRITICAL CSS INLINE TO BYPASS CACHE ISSUES
function loscocos_child_critical_css()
{
    ?>
    <style>
        /* ========================================
               WOOCOMMERCE TABS - Premium Styling
               ======================================== */
        .woocommerce-tabs {
            margin-top: 0 !important;
        }

        .woocommerce-tabs ul.tabs {
            display: flex !important;
            gap: 0.5rem !important;
            padding: 0 !important;
            margin: 0 0 2rem 0 !important;
            border-bottom: 2px solid #e5e7eb !important;
            list-style: none !important;
            overflow: visible !important;
        }

        .woocommerce-tabs ul.tabs::before,
        .woocommerce-tabs ul.tabs::after {
            display: none !important;
        }

        .woocommerce-tabs ul.tabs li {
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            background: transparent !important;
            border-radius: 0 !important;
        }

        .woocommerce-tabs ul.tabs li a {
            display: block !important;
            padding: 1rem 1.5rem !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            color: #6b7280 !important;
            text-decoration: none !important;
            border-bottom: 3px solid transparent !important;
            margin-bottom: -2px !important;
            transition: all 0.3s ease !important;
            background: transparent !important;
        }

        .woocommerce-tabs ul.tabs li a:hover {
            color: #2d5a3d !important;
        }

        .woocommerce-tabs ul.tabs li.active a {
            color: #2d5a3d !important;
            border-bottom-color: #2d5a3d !important;
            background: transparent !important;
        }

        .woocommerce-tabs .panel {
            padding: 1.5rem 0 !important;
            margin: 0 !important;
            background: transparent !important;
            border: none !important;
        }

        .woocommerce-tabs .panel h2 {
            display: none !important;
        }

        .woocommerce-tabs .panel p {
            color: #4b5563 !important;
            line-height: 1.8 !important;
            margin-bottom: 1rem !important;
        }

        /* Additional Info Table */
        .woocommerce-product-attributes {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        .woocommerce-product-attributes tr {
            border-bottom: 1px solid #e5e7eb !important;
        }

        .woocommerce-product-attributes th,
        .woocommerce-product-attributes td {
            padding: 1rem !important;
            text-align: left !important;
        }

        .woocommerce-product-attributes th {
            font-weight: 600 !important;
            color: #374151 !important;
            width: 40% !important;
            background: #f9fafb !important;
        }

        .woocommerce-product-attributes td {
            color: #6b7280 !important;
        }

        /* Reviews Section */
        #reviews .comment-reply-title {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #2d5a3d !important;
            margin-bottom: 1rem !important;
        }

        /* ========================================
               RELATED PRODUCTS - Premium Cards
               ======================================== */
        .related-products-section {
            background: #fdfcfa !important;
            padding: 3rem !important;
            border-radius: 1rem !important;
            margin-top: 2rem !important;
        }

        .related-products-section .product-card {
            display: flex !important;
            flex-direction: column !important;
            height: 100% !important;
            background: white !important;
            border-radius: 1rem !important;
            overflow: hidden !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
        }

        .related-products-section .product-card:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important;
        }

        .related-products-section .product-card .aspect-square {
            aspect-ratio: 1 / 1 !important;
            width: 100% !important;
            overflow: hidden !important;
        }

        .related-products-section .product-card .aspect-square img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        .related-products-section .product-card .p-5 {
            flex-grow: 1 !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .related-products-section .product-card h3 {
            flex-grow: 1 !important;
            font-family: 'Merriweather', serif !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: #374151 !important;
            line-height: 1.4 !important;
            margin-bottom: 0.75rem !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            min-height: 2.8rem !important;
        }

        .related-products-section .product-card .text-lg {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #c97862 !important;
        }

        .related-products-section .product-card .px-5 a {
            display: block !important;
            text-align: center !important;
            padding: 0.75rem 1rem !important;
            background: #2d5a3d !important;
            color: white !important;
            font-weight: 600 !important;
            border-radius: 0.5rem !important;
            text-decoration: none !important;
            transition: background 0.3s ease !important;
        }

        .related-products-section .product-card .px-5 a:hover {
            background: #1e3d29 !important;
        }

        /* Grid Layout Fix */
        .related-products-section .grid {
            display: grid !important;
            gap: 1.5rem !important;
        }

        @media (min-width: 640px) {
            .related-products-section .grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        @media (min-width: 1024px) {
            .related-products-section .grid {
                grid-template-columns: repeat(4, 1fr) !important;
            }
        }

        /* Ensure Features are visible */
        .product-features {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'loscocos_child_critical_css', 9999);

/**
 * Enqueue scripts with proper dependencies
 */
function loscocos_child_enqueue_scripts()
{
    // Ensure WooCommerce scripts are loaded
    if (class_exists('WooCommerce') && is_product()) {
        wp_enqueue_script('wc-single-product');
    }

    // Enqueue custom JavaScript if file exists
    $main_js = get_stylesheet_directory() . '/assets/js/main.js';
    if (file_exists($main_js)) {
        wp_enqueue_script(
            'loscocos-child-scripts',
            get_stylesheet_directory_uri() . '/assets/js/main.js',
            array('jquery'),
            filemtime($main_js),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'loscocos_child_enqueue_scripts', 20);

/**
 * Add WooCommerce theme support
 */
function loscocos_child_woocommerce_setup()
{
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'loscocos_child_woocommerce_setup');

/**
 * Include Product Infographic Custom Fields
 */
require_once LOSCOCOS_CHILD_PATH . '/inc/product-infographic-fields.php';
