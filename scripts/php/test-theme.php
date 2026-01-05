<?php
// Test file to verify the Los Cocos Clean theme is working correctly
require_once('wp-config.php');

// Check if WordPress is loaded
if (!function_exists('get_option')) {
    echo "WordPress is not loaded properly.";
    exit;
}

// Check if the theme is active
$current_theme = wp_get_theme();
if ($current_theme->stylesheet !== 'loscocos-clean') {
    echo "The loscocos-clean theme is not active. Current theme: " . $current_theme->name;
    exit;
}

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "WooCommerce is not active.";
    exit;
}

// Get product count
global $wpdb;
$product_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish'");

echo "SUCCESS: Los Cocos Clean theme is active and properly configured!\n";
echo "WooCommerce is active with $product_count products.\n";
echo "Theme: " . $current_theme->name . " v" . $current_theme->version . "\n";

// Show some product information
echo "\nSample products:\n";
$products = wc_get_products(array('limit' => 5));
foreach ($products as $product) {
    echo "- " . $product->get_name() . " (" . $product->get_price_html() . ")\n";
}

echo "\nThe theme is ready at http://localhost:8080/\n";
?>