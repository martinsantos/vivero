<?php
/**
 * Los Cocos Child Theme - Functions
 * Modular architecture with separated concerns
 *
 * @package Los_Cocos_Child
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('LOSCOCOS_CHILD_VERSION', '2.0.0');
define('LOSCOCOS_CHILD_PATH', get_stylesheet_directory());
define('LOSCOCOS_CHILD_URI', get_stylesheet_directory_uri());

/**
 * Autoload inc/ modules
 */
$inc_files = [
    '/inc/class-theme-setup.php',
    '/inc/class-wc-customizations.php',
    '/inc/class-seo-geo.php',
    '/inc/template-functions.php',
    '/inc/product-infographic-fields.php',
];

foreach ($inc_files as $file) {
    $filepath = LOSCOCOS_CHILD_PATH . $file;
    if (file_exists($filepath)) {
        require_once $filepath;
    }
}

/**
 * Initialize theme classes
 */
add_action('after_setup_theme', function() {
    if (class_exists('LosCocos_Theme_Setup')) {
        LosCocos_Theme_Setup::init();
    }
    
    if (class_exists('LosCocos_WC_Customizations')) {
        LosCocos_WC_Customizations::init();
    }

    if (class_exists('LosCocos_SEO_GEO')) {
        LosCocos_SEO_GEO::init();
    }

}, 5);

/**
 * Critical CSS for WooCommerce tabs and components
 * (Inline for guaranteed application)
 */
add_action('wp_head', function() {
    ?>
    <style>
        /* WooCommerce Tabs */
        .woocommerce-tabs ul.tabs {
            display: flex !important;
            gap: 0.5rem !important;
            padding: 0 !important;
            margin: 0 0 2rem 0 !important;
            border-bottom: 2px solid #e5e7eb !important;
            list-style: none !important;
        }
        .woocommerce-tabs ul.tabs::before,
        .woocommerce-tabs ul.tabs::after { display: none !important; }
        .woocommerce-tabs ul.tabs li {
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            background: transparent !important;
        }
        .woocommerce-tabs ul.tabs li a {
            display: block !important;
            padding: 1rem 1.5rem !important;
            font-weight: 600 !important;
            color: #6b7280 !important;
            text-decoration: none !important;
            border-bottom: 3px solid transparent !important;
            margin-bottom: -2px !important;
            transition: all 0.3s ease !important;
            background: transparent !important;
        }
        .woocommerce-tabs ul.tabs li a:hover { color: #2d5a3d !important; }
        .woocommerce-tabs ul.tabs li.active a {
            color: #2d5a3d !important;
            border-bottom-color: #2d5a3d !important;
        }
        .woocommerce-tabs .panel {
            padding: 1.5rem 0 !important;
            border: none !important;
        }
        .woocommerce-tabs .panel h2 { display: none !important; }
    </style>
    <?php
}, 9999);
