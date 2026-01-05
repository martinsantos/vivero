<?php
/**
 * Remove Generic SVG Images Script
 *
 * Identifies and removes generic SVG images created by emergency_image_fix.php
 * that contain "Los Cocos" branding, leaving products without images so they
 * can be processed by the Python automation script.
 */

// Include WordPress core
require_once('wp-load.php');

echo "=== Removing Generic SVG Images ===\n";

$products_removed = 0;
$images_removed = 0;

// Get all published products
$args = array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => -1
);

$products = get_posts($args);

echo "Found " . count($products) . " products to check.\n";

foreach ($products as $product) {
    $product_id = $product->ID;

    // Get featured image ID
    $featured_image_id = get_post_thumbnail_id($product_id);

    if (!$featured_image_id) {
        continue; // No image, skip
    }

    // Get attachment post
    $attachment = get_post($featured_image_id);

    if (!$attachment) {
        continue;
    }

    // Check if it's an SVG
    $mime_type = get_post_mime_type($featured_image_id);
    if ($mime_type !== 'image/svg+xml') {
        continue; // Not SVG, skip
    }

    // Get SVG content
    $file_path = get_attached_file($featured_image_id);
    if (!file_exists($file_path)) {
        continue;
    }

    $svg_content = file_get_contents($file_path);

    // Check if it contains "Los Cocos" branding (unique to emergency_image_fix.php)
    if (strpos($svg_content, 'Los Cocos') === false) {
        continue; // Not a generic SVG, skip
    }

    // Remove featured image
    delete_post_thumbnail($product_id);

    // Optionally delete the attachment file (uncomment if wanted)
    // wp_delete_attachment($featured_image_id, true);

    $products_removed++;
    echo "Removed SVG from product ID: $product_id (" . $product->post_title . ")\n";
}

echo "\n=== Summary ===\n";
echo "Products processed: " . count($products) . "\n";
echo "Generic SVGs removed: $products_removed\n";
echo "These products now have no images and can be processed by wc_image_automation.py\n";

echo "\nDone!\n";
?>
