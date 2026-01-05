<?php
/**
 * 🔍 DIRECT SHOP PRODUCT INVESTIGATION
 * Directly checks what's actually displayed in the shop
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🔍 DIRECT SHOP PRODUCT INVESTIGATION</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Show exactly what products and images are currently in the shop</p>\n";

// Get the shop products as they would appear on the frontend
$args = array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => '_visibility',
            'value' => array('catalog', 'visible'),
            'compare' => 'IN'
        )
    )
);

$products = get_posts($args);
$total_products = count($products);

echo "<h2>📊 SHOP PRODUCTS ANALYSIS</h2>\n";
echo "<p><strong>Total products in shop:</strong> {$total_products}</p>\n";

// Group products by their actual displayed image
$displayed_images = [];
$products_by_image = [];

foreach ($products as $post) {
    $product = wc_get_product($post->ID);
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        $image_src = wp_get_attachment_image_src($featured_image_id, 'woocommerce_thumbnail');
        $display_url = $image_src ? $image_src[0] : $image_url;
        
        if (!isset($products_by_image[$display_url])) {
            $products_by_image[$display_url] = [];
        }
        
        $products_by_image[$display_url][] = [
            'id' => $product_id,
            'name' => $product_name,
            'sku' => $product->get_sku(),
            'price' => $product->get_price()
        ];
    }
}

// Find displayed duplicates
$displayed_duplicates = [];
foreach ($products_by_image as $image_url => $products_list) {
    if (count($products_list) > 1) {
        $displayed_duplicates[$image_url] = $products_list;
    }
}

$duplicate_count = count($displayed_duplicates);
$total_affected = 0;
foreach ($displayed_duplicates as $products_list) {
    $total_affected += count($products_list);
}

echo "<div style='background: " . ($duplicate_count > 0 ? "#f8d7da" : "#d4edda") . "; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
if ($duplicate_count > 0) {
    echo "<h3>🚨 SHOP DISPLAY DUPLICATES FOUND!</h3>\n";
    echo "<p><strong>Duplicate image groups:</strong> {$duplicate_count}</p>\n";
    echo "<p><strong>Products showing same image:</strong> {$total_affected}</p>\n";
    echo "<p><strong>This is what you're seeing in the shop!</strong></p>\n";
} else {
    echo "<h3>✅ No display duplicates found</h3>\n";
    echo "<p>All products show unique images in the shop</p>\n";
}
echo "</div>\n";

if ($duplicate_count > 0) {
    echo "<h2>📋 SHOP DISPLAY DUPLICATE GROUPS</h2>\n";
    
    $group_num = 1;
    foreach ($displayed_duplicates as $image_url => $products_list) {
        $group_size = count($products_list);
        $filename = basename($image_url);
        
        echo "<div style='border: 2px solid #dc3545; background: #fff; padding: 15px; margin: 15px 0; border-radius: 8px;'>\n";
        echo "<h3 style='color: #dc3545;'>🚨 Shop Display Group #{$group_num} - {$group_size} products</h3>\n";
        
        // Show the image as it appears in shop
        echo "<div style='text-align: center; margin: 15px 0;'>\n";
        echo "<img src='{$image_url}' style='width: 200px; height: 200px; object-fit: cover; border: 2px solid #dc3545;' alt='Shop Display'>\n";
        echo "<p style='font-size: 12px; color: #666;'>Display URL: {$filename}</p>\n";
        echo "</div>\n";
        
        echo "<h4>Products showing this image in shop:</h4>\n";
        echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 10px;'>\n";
        
        foreach ($products_list as $product) {
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
        
        if ($group_num > 15) {
            $remaining = $duplicate_count - 15;
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px;'>\n";
            echo "<p><strong>⚠️ Showing first 15 groups. {$remaining} more duplicate groups exist.</strong></p>\n";
            echo "</div>\n";
            break;
        }
    }
}

// Show sample of current shop products
echo "<h2>🛒 CURRENT SHOP PRODUCT SAMPLE</h2>\n";
echo "<p>First 20 products as they appear in the shop:</p>\n";

echo "<div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 20px 0;'>\n";

$sample_count = 0;
foreach ($products_by_image as $image_url => $products_list) {
    if ($sample_count >= 20) break;
    
    $product = $products_list[0]; // Show first product using this image
    $is_duplicate = count($products_list) > 1;
    
    echo "<div style='border: 2px solid " . ($is_duplicate ? "#dc3545" : "#28a745") . "; padding: 10px; text-align: center; background: " . ($is_duplicate ? "#fff5f5" : "#f8fff8") . ";'>\n";
    echo "<img src='{$image_url}' style='width: 120px; height: 120px; object-fit: cover; border: 1px solid #ccc;' alt='Shop Product'>\n";
    echo "<p style='font-size: 10px; margin: 5px 0; font-weight: bold;'>ID: {$product['id']}</p>\n";
    echo "<p style='font-size: 9px; margin: 5px 0;'>" . substr($product['name'], 0, 20) . "...</p>\n";
    if ($is_duplicate) {
        echo "<p style='font-size: 8px; color: #dc3545; font-weight: bold;'>DUPLICATE (" . count($products_list) . " products)</p>\n";
    }
    echo "</div>\n";
    
    $sample_count++;
}

echo "</div>\n";

// Special check for Glacoxan products in shop display
echo "<h2>🎯 GLACOXAN SHOP DISPLAY CHECK</h2>\n";

$glacoxan_in_shop = [];
foreach ($products_by_image as $image_url => $products_list) {
    foreach ($products_list as $product) {
        if (stripos($product['name'], 'glacoxan') !== false) {
            $glacoxan_in_shop[$image_url][] = $product;
        }
    }
}

$glacoxan_display_duplicates = 0;
foreach ($glacoxan_in_shop as $products_list) {
    if (count($products_list) > 1) {
        $glacoxan_display_duplicates++;
    }
}

echo "<p><strong>Glacoxan products in shop:</strong> " . count($glacoxan_in_shop) . " unique images</p>\n";

if ($glacoxan_display_duplicates > 0) {
    echo "<div style='background: #f8d7da; padding: 15px; border: 2px solid #f5c6cb; border-radius: 8px;'>\n";
    echo "<h3>🚨 GLACOXAN SHOP DUPLICATES CONFIRMED!</h3>\n";
    echo "<p>{$glacoxan_display_duplicates} groups of Glacoxan products show identical images in shop</p>\n";
    echo "</div>\n";
} else {
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 8px;'>\n";
    echo "<h3>✅ Glacoxan products show unique images in shop</h3>\n";
    echo "</div>\n";
}

echo "<h2>📊 FINAL SHOP ANALYSIS</h2>\n";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>\n";
echo "<tr style='background: #f8f9fa;'>\n";
echo "<th>Metric</th><th>Count</th><th>Status</th>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Total Products in Shop</td>\n";
echo "<td>{$total_products}</td>\n";
echo "<td>✅ Active</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Unique Images Displayed</td>\n";
echo "<td>" . count($products_by_image) . "</td>\n";
echo "<td>ℹ️ Info</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Duplicate Display Groups</td>\n";
echo "<td>{$duplicate_count}</td>\n";
echo "<td>" . ($duplicate_count > 0 ? "❌ Found" : "✅ None") . "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Products with Duplicate Display</td>\n";
echo "<td>{$total_affected}</td>\n";
echo "<td>" . ($total_affected > 0 ? "❌ Need fixing" : "✅ All unique") . "</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "<h3>🌐 <a href='http://localhost:8080/tienda/' target='_blank'>View live shop</a></h3>\n";
echo "<p><em>Shop investigation completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>