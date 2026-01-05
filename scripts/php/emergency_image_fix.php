<?php
/**
 * Emergency Image Fix Script - Create SVG placeholders for products missing images
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h2>🔧 Los Cocos Emergency Image Fix</h2>\n";

// Get products without images
$products = wc_get_products(array(
    'limit' => -1,
    'status' => 'publish'
));

$fixed_count = 0;
$total_count = 0;

foreach ($products as $product) {
    $total_count++;
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    
    // Check if product has featured image
    $featured_image_id = $product->get_image_id();
    
    if (!$featured_image_id) {
        // Create SVG image for product
        $svg_content = generate_product_svg($product_id, $product_name);
        
        // Save SVG as attachment
        $upload_dir = wp_upload_dir();
        $svg_filename = 'product_' . $product_id . '_placeholder.svg';
        $svg_path = $upload_dir['path'] . '/' . $svg_filename;
        
        file_put_contents($svg_path, $svg_content);
        
        // Create attachment
        $attachment = array(
            'guid' => $upload_dir['url'] . '/' . $svg_filename,
            'post_mime_type' => 'image/svg+xml',
            'post_title' => $product_name . ' - Image',
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attachment_id = wp_insert_attachment($attachment, $svg_path);
        
        if ($attachment_id) {
            // Set as featured image
            set_post_thumbnail($product_id, $attachment_id);
            $fixed_count++;
            echo "✅ Fixed: $product_name (ID: $product_id)\n";
        }
    } else {
        echo "✓ Has image: $product_name\n";
    }
    
    if ($fixed_count >= 100) break; // Process more products in each run
}

echo "<h3>📊 Summary:</h3>\n";
echo "Total products checked: $total_count\n";
echo "Images fixed: $fixed_count\n";

function generate_product_svg($product_id, $product_name) {
    // Generate colors based on product ID
    $colors = [
        '#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', 
        '#ef4444', '#14b8a6', '#f97316', '#84cc16'
    ];
    
    $emojis = ['🌱', '🌿', '🍃', '🌺', '🌸', '🌻', '🌷', '🥀'];
    
    $color = $colors[$product_id % count($colors)];
    $emoji = $emojis[$product_id % count($emojis)];
    
    $short_name = strlen($product_name) > 15 ? substr($product_name, 0, 15) . '...' : $product_name;
    
    return '<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="grad' . $product_id . '" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:' . $color . ';stop-opacity:0.8" />
                <stop offset="100%" style="stop-color:' . $color . ';stop-opacity:0.4" />
            </linearGradient>
        </defs>
        <rect width="300" height="300" fill="url(#grad' . $product_id . ')" />
        <text x="150" y="120" font-family="Arial, sans-serif" font-size="48" text-anchor="middle" fill="white">' . $emoji . '</text>
        <text x="150" y="160" font-family="Arial, sans-serif" font-size="14" text-anchor="middle" fill="white" font-weight="bold">' . htmlspecialchars($short_name) . '</text>
        <text x="150" y="190" font-family="Arial, sans-serif" font-size="12" text-anchor="middle" fill="rgba(255,255,255,0.8)">Los Cocos</text>
    </svg>';
}
?>