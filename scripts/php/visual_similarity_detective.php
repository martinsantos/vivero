<?php
/**
 * 🔍 VISUAL SIMILARITY DETECTIVE
 * Specifically targets the products you mentioned: CHIP70, COPRO3L, CROMONM12, etc.
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🔍 VISUAL SIMILARITY DETECTIVE</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Investigate specific products mentioned in screenshot</p>\n";

// Target products from your screenshot
$target_products = ['CHIP70', 'COPRO3L', 'CROMONM12', 'CROTIRM12', 'CROVARM12', 'DIEFMARM12', 'DIEFMARM14', 'CRESPON15L'];

echo "<h2>🎯 Investigating Screenshot Products</h2>\n";

$found_products = [];

// Get all products and find the ones mentioned
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);

foreach ($products as $product) {
    $product_name = $product->get_name();
    foreach ($target_products as $target) {
        if (stripos($product_name, $target) !== false) {
            $featured_image_id = $product->get_image_id();
            if ($featured_image_id) {
                $image_url = wp_get_attachment_url($featured_image_id);
                $found_products[] = [
                    'id' => $product->get_id(),
                    'name' => $product_name,
                    'target' => $target,
                    'image_url' => $image_url,
                    'filename' => basename($image_url)
                ];
            }
        }
    }
}

echo "<p><strong>Found " . count($found_products) . " matching products:</strong></p>\n";

// Display the products in a grid similar to your screenshot
echo "<div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 20px 0; max-width: 1200px;'>\n";

foreach ($found_products as $product) {
    echo "<div style='border: 2px solid #dc3545; padding: 15px; background: white; text-align: center; border-radius: 8px;'>\n";
    echo "<img src='{$product['image_url']}' style='width: 150px; height: 150px; object-fit: cover; border: 1px solid #ccc; margin-bottom: 10px;' alt='Product Image'>\n";
    echo "<h4 style='margin: 10px 0 5px 0; font-size: 14px; color: #dc3545;'>{$product['target']}</h4>\n";
    echo "<p style='margin: 5px 0; font-size: 12px;'><strong>ID:</strong> {$product['id']}</p>\n";
    echo "<p style='margin: 5px 0; font-size: 11px; color: #666;'>{$product['name']}</p>\n";
    echo "<p style='margin: 5px 0; font-size: 10px; color: #888;'>{$product['filename']}</p>\n";
    echo "</div>\n";
}

echo "</div>\n";

// Analyze if they're using similar images
echo "<h2>🔍 Image Analysis</h2>\n";

$image_analysis = [];
foreach ($found_products as $product) {
    $image_data = @file_get_contents($product['image_url']);
    if ($image_data) {
        $content_hash = md5($image_data);
        $file_size = strlen($image_data);
        
        if (!isset($image_analysis[$content_hash])) {
            $image_analysis[$content_hash] = [];
        }
        
        $image_analysis[$content_hash][] = [
            'product' => $product,
            'size' => $file_size
        ];
    }
}

$visual_duplicates = 0;
foreach ($image_analysis as $hash => $products_with_hash) {
    if (count($products_with_hash) > 1) {
        $visual_duplicates++;
        echo "<div style='background: #f8d7da; padding: 15px; margin: 15px 0; border-radius: 8px; border: 2px solid #dc3545;'>\n";
        echo "<h3 style='color: #dc3545;'>🚨 IDENTICAL IMAGES FOUND!</h3>\n";
        echo "<p><strong>Content Hash:</strong> " . substr($hash, 0, 12) . "...</p>\n";
        echo "<p><strong>Products using identical image:</strong></p>\n";
        foreach ($products_with_hash as $item) {
            echo "<p>• {$item['product']['target']} (ID: {$item['product']['id']}) - {$item['product']['filename']}</p>\n";
        }
        echo "</div>\n";
    }
}

if ($visual_duplicates === 0) {
    echo "<div style='background: #fff3cd; padding: 15px; margin: 15px 0; border-radius: 8px; border: 2px solid #ffc107;'>\n";
    echo "<h3 style='color: #856404;'>⚠️ NO IDENTICAL IMAGES BUT VISUAL SIMILARITY POSSIBLE</h3>\n";
    echo "<p>The images have different content hashes but may look very similar.</p>\n";
    echo "<p>This could be because they're different photos of very similar plants/objects.</p>\n";
    echo "</div>\n";
}

// Check for cache busting
echo "<h2>🧹 Cache Investigation</h2>\n";
echo "<div style='background: #e7f3ff; padding: 15px; border: 2px solid #007bff; border-radius: 8px; margin: 15px 0;'>\n";
echo "<h3 style='color: #004085;'>💡 Possible Caching Issue</h3>\n";
echo "<p>If you're seeing identical images but our analysis shows they're different, this could be:</p>\n";
echo "<ol>\n";
echo "<li><strong>Browser Cache:</strong> Your browser is showing cached versions of old images</li>\n";
echo "<li><strong>CDN Cache:</strong> WordPress/server caching is serving old images</li>\n";
echo "<li><strong>Image Similarity:</strong> Different photos that look very similar</li>\n";
echo "</ol>\n";

echo "<h4>🔧 Try these solutions:</h4>\n";
echo "<ol>\n";
echo "<li><strong>Hard Refresh:</strong> Press Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)</li>\n";
echo "<li><strong>Clear Browser Cache:</strong> Clear your browser's cache completely</li>\n";
echo "<li><strong>Incognito/Private Mode:</strong> Open the shop in private browsing mode</li>\n";
echo "<li><strong>Force Cache Bust:</strong> Add ?v=" . time() . " to image URLs</li>\n";
echo "</ol>\n";
echo "</div>\n";

// Test with cache-busted URLs
echo "<h2>🧪 Cache-Busted Image Test</h2>\n";
echo "<p>Showing the same images with cache-busting parameters:</p>\n";

echo "<div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 20px 0; max-width: 1200px;'>\n";

foreach ($found_products as $product) {
    $cache_busted_url = $product['image_url'] . '?v=' . time() . '&cb=' . rand(1000, 9999);
    echo "<div style='border: 2px solid #28a745; padding: 15px; background: white; text-align: center; border-radius: 8px;'>\n";
    echo "<img src='{$cache_busted_url}' style='width: 150px; height: 150px; object-fit: cover; border: 1px solid #ccc; margin-bottom: 10px;' alt='Cache Busted Image'>\n";
    echo "<h4 style='margin: 10px 0 5px 0; font-size: 14px; color: #28a745;'>{$product['target']} (Cache Busted)</h4>\n";
    echo "<p style='margin: 5px 0; font-size: 10px; color: #666;'>With ?v=" . time() . "</p>\n";
    echo "</div>\n";
}

echo "</div>\n";

// If still seeing duplicates, create immediate fix
if ($visual_duplicates > 0) {
    echo "<div style='background: #dc3545; color: white; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
    echo "<h3>🚨 CONFIRMED DUPLICATES - IMMEDIATE FIX NEEDED</h3>\n";
    echo "<p><strong>Found {$visual_duplicates} groups of identical images among the screenshot products</strong></p>\n";
    echo "<p>Creating emergency fix script...</p>\n";
    echo "</div>\n";
} else {
    echo "<div style='background: #ffc107; color: #212529; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
    echo "<h3>⚠️ LIKELY CACHING ISSUE</h3>\n";
    echo "<p>No identical images found in backend, but you're seeing duplicates in frontend.</p>\n";
    echo "<p><strong>Recommendation:</strong> Clear your browser cache and try the cache-busted images above.</p>\n";
    echo "</div>\n";
}

echo "<h3>🔗 <a href='http://localhost:8080/tienda/' target='_blank'>Test shop with fresh browser cache</a></h3>\n";
echo "<p><em>Visual similarity investigation completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>