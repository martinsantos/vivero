<?php
/**
 * Script to fix missing product images
 */

define('WP_LOAD_PATH', __DIR__ . '/');
require_once('wp-load.php');

// Ensure we have the required functions
if (!function_exists('wp_upload_dir')) {
    die('WordPress not loaded. Please run this script from the WordPress root directory.');
}

$upload_dir = wp_upload_dir();
$base_dir = $upload_dir['basedir'] . '/loscocos-images';

// Create the directory if it doesn't exist
if (!file_exists($base_dir)) {
    wp_mkdir_p($base_dir);
}

// Copy placeholder to all missing product SVGs
$products = wc_get_products(['limit' => -1]);

foreach ($products as $product) {
    $product_id = $product->get_id();
    $target_file = $base_dir . '/' . $product_id . '.svg';
    
    if (!file_exists($target_file)) {
        $placeholder = file_get_contents(__DIR__ . '/placeholder.svg');
        file_put_contents($target_file, $placeholder);
        echo "Created placeholder for product {$product_id}\n";
    }
}

echo "Done!\n";
?>
