<?php
/**
 * 🔍 INVESTIGATE GLACOXAN PRODUCTS
 * Directly investigate the Glacoxan products shown in user screenshot
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🔍 GLACOXAN PRODUCT INVESTIGATION</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Find and analyze ALL Glacoxan products with their actual images</p>\n";

// Search for Glacoxan products specifically
$glacoxan_products = [];
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);

foreach ($products as $product) {
    $product_name = $product->get_name();
    if (stripos($product_name, 'glacoxan') !== false || 
        stripos($product_name, 'glaco') !== false) {
        
        $product_id = $product->get_id();
        $featured_image_id = $product->get_image_id();
        $image_url = '';
        $filename = '';
        
        if ($featured_image_id) {
            $image_url = wp_get_attachment_url($featured_image_id);
            $filename = basename($image_url);
        }
        
        $glacoxan_products[] = [
            'id' => $product_id,
            'name' => $product_name,
            'image_id' => $featured_image_id,
            'image_url' => $image_url,
            'filename' => $filename,
            'sku' => $product->get_sku(),
            'price' => $product->get_price()
        ];
    }
}

$total_glacoxan = count($glacoxan_products);
echo "<h2>📊 GLACOXAN ANALYSIS</h2>\n";
echo "<p><strong>Found {$total_glacoxan} Glacoxan products</strong></p>\n";

if ($total_glacoxan > 0) {
    echo "<h3>📋 ALL GLACOXAN PRODUCTS:</h3>\n";
    
    // Group by image URL to see duplicates
    $image_groups = [];
    foreach ($glacoxan_products as $product) {
        $image_url = $product['image_url'];
        if ($image_url) {
            if (!isset($image_groups[$image_url])) {
                $image_groups[$image_url] = [];
            }
            $image_groups[$image_url][] = $product;
        }
    }
    
    // Display groups
    $group_num = 1;
    foreach ($image_groups as $image_url => $products_in_group) {
        $group_size = count($products_in_group);
        $filename = basename($image_url);
        
        echo "<div style='border: 2px solid " . ($group_size > 1 ? "#dc3545" : "#28a745") . "; padding: 15px; margin: 15px 0; border-radius: 8px;'>\n";
        echo "<h4 style='color: " . ($group_size > 1 ? "#dc3545" : "#28a745") . ";'>Group {$group_num} - {$filename}</h4>\n";
        echo "<p><strong>Products using this image: {$group_size}</strong></p>\n";
        
        if ($group_size > 1) {
            echo "<p style='color: #dc3545; font-weight: bold;'>🚨 DUPLICATE DETECTED!</p>\n";
        }
        
        // Show the image
        if ($image_url) {
            echo "<div style='text-align: center; margin: 15px 0;'>\n";
            echo "<img src='{$image_url}' style='width: 200px; height: 200px; object-fit: cover; border: 2px solid #ccc;' alt='Glacoxan Image'>\n";
            echo "</div>\n";
        }
        
        // List all products in this group
        echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 10px; margin-top: 10px;'>\n";
        foreach ($products_in_group as $product) {
            echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 5px; border: 1px solid #dee2e6;'>\n";
            echo "<p style='margin: 0; font-weight: bold; color: #495057;'>ID: {$product['id']}</p>\n";
            echo "<p style='margin: 5px 0 0 0; font-size: 14px;'>{$product['name']}</p>\n";
            echo "<p style='margin: 5px 0 0 0; font-size: 12px; color: #6c757d;'>SKU: {$product['sku']}</p>\n";
            echo "<p style='margin: 5px 0 0 0; font-size: 12px; color: #28a745;'>Price: \${$product['price']}</p>\n";
            echo "</div>\n";
        }
        echo "</div>\n";
        echo "</div>\n";
        
        $group_num++;
    }
    
    // Summary of duplicates
    $duplicate_groups = 0;
    $total_duplicated = 0;
    foreach ($image_groups as $products_in_group) {
        if (count($products_in_group) > 1) {
            $duplicate_groups++;
            $total_duplicated += count($products_in_group);
        }
    }
    
    echo "<h2>🚨 GLACOXAN DUPLICATE SUMMARY</h2>\n";
    echo "<div style='background: " . ($duplicate_groups > 0 ? "#f8d7da" : "#d4edda") . "; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
    if ($duplicate_groups > 0) {
        echo "<h3 style='color: #721c24;'>🚨 DUPLICATES CONFIRMED IN GLACOXAN PRODUCTS!</h3>\n";
        echo "<p><strong>Duplicate image groups:</strong> {$duplicate_groups}</p>\n";
        echo "<p><strong>Total duplicated products:</strong> {$total_duplicated}</p>\n";
        echo "<p><strong>This explains what you're seeing in the shop!</strong></p>\n";
    } else {
        echo "<h3 style='color: #155724;'>✅ No duplicates found in Glacoxan products</h3>\n";
    }
    echo "</div>\n";
    
} else {
    echo "<p>❌ No Glacoxan products found in the database</p>\n";
    echo "<p>This suggests the products might be named differently</p>\n";
}

// Also search for any products with similar images (broader search)
echo "<h2>🔍 BROADER DUPLICATE SEARCH</h2>\n";
echo "<p>Searching for any products that might be causing the visual duplicates...</p>\n";

// Get all products and their images
$all_image_urls = [];
foreach ($products as $product) {
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        if ($image_url && strpos($image_url, '.svg') === false && strpos($image_url, 'placeholder') === false) {
            if (!isset($all_image_urls[$image_url])) {
                $all_image_urls[$image_url] = [];
            }
            $all_image_urls[$image_url][] = [
                'id' => $product_id,
                'name' => $product_name
            ];
        }
    }
}

// Find actual duplicates
$real_duplicates = [];
foreach ($all_image_urls as $url => $products_list) {
    if (count($products_list) > 1) {
        $real_duplicates[$url] = $products_list;
    }
}

$total_duplicate_groups = count($real_duplicates);
echo "<p><strong>🔍 Total duplicate image groups found across ALL products:</strong> {$total_duplicate_groups}</p>\n";

if ($total_duplicate_groups > 0) {
    echo "<h3>📋 ALL DUPLICATE GROUPS (First 10):</h3>\n";
    $count = 0;
    foreach ($real_duplicates as $url => $products_list) {
        if ($count >= 10) break;
        
        $filename = basename($url);
        $group_size = count($products_list);
        
        echo "<div style='background: #fff3cd; padding: 10px; margin: 10px 0; border-radius: 5px;'>\n";
        echo "<p><strong>📄 {$filename} ({$group_size} products):</strong></p>\n";
        echo "<img src='{$url}' style='width: 100px; height: 100px; object-fit: cover; border: 1px solid #ccc; margin: 5px;' alt='Duplicate'>\n";
        echo "<p>Products: ";
        foreach ($products_list as $product) {
            echo "ID {$product['id']} ({$product['name']}), ";
        }
        echo "</p>\n";
        echo "</div>\n";
        
        $count++;
    }
    
    if ($total_duplicate_groups > 10) {
        echo "<p><em>... and " . ($total_duplicate_groups - 10) . " more duplicate groups</em></p>\n";
    }
}

echo "<h3>🔗 <a href='http://localhost:8080/tienda/' target='_blank'>Check current shop display</a></h3>\n";
echo "<p><em>Glacoxan investigation completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>