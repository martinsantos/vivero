<?php
/**
 * 🚨 COMPREHENSIVE DUPLICATE ANALYSIS
 * Deep investigation of image duplication issue
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🚨 COMPREHENSIVE DUPLICATE ANALYSIS</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Find and analyze ALL duplicate images in the system</p>\n";

// Get ALL products
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$total_products = count($products);

echo "<h2>📊 Analyzing {$total_products} products for image duplicates</h2>\n";

// Multiple analysis approaches
$url_groups = [];          // Group by exact URL
$filename_groups = [];     // Group by filename
$content_groups = [];      // Group by content hash
$size_groups = [];         // Group by file size

$analysis_results = [];

foreach ($products as $product) {
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $product_sku = $product->get_sku();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        
        if ($image_url && strpos($image_url, '.svg') === false && strpos($image_url, 'placeholder') === false) {
            $filename = basename($image_url);
            
            // Get image content for deep analysis
            $image_data = @file_get_contents($image_url);
            $content_hash = $image_data ? md5($image_data) : 'no_content';
            $file_size = $image_data ? strlen($image_data) : 0;
            
            $product_info = [
                'id' => $product_id,
                'name' => $product_name,
                'sku' => $product_sku,
                'url' => $image_url,
                'filename' => $filename,
                'content_hash' => $content_hash,
                'file_size' => $file_size
            ];
            
            // Group by URL
            if (!isset($url_groups[$image_url])) {
                $url_groups[$image_url] = [];
            }
            $url_groups[$image_url][] = $product_info;
            
            // Group by filename
            if (!isset($filename_groups[$filename])) {
                $filename_groups[$filename] = [];
            }
            $filename_groups[$filename][] = $product_info;
            
            // Group by content hash
            if (!isset($content_groups[$content_hash])) {
                $content_groups[$content_hash] = [];
            }
            $content_groups[$content_hash][] = $product_info;
            
            // Group by file size
            if (!isset($size_groups[$file_size])) {
                $size_groups[$file_size] = [];
            }
            $size_groups[$file_size][] = $product_info;
        }
    }
}

// Analyze each grouping method
$url_duplicates = array_filter($url_groups, function($group) { return count($group) > 1; });
$filename_duplicates = array_filter($filename_groups, function($group) { return count($group) > 1; });
$content_duplicates = array_filter($content_groups, function($group) { return count($group) > 1; });
$size_duplicates = array_filter($size_groups, function($group) { return count($group) > 1; });

echo "<h2>📋 DUPLICATE ANALYSIS RESULTS</h2>\n";

echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;'>\n";

// URL duplicates
echo "<div style='background: " . (count($url_duplicates) > 0 ? "#f8d7da" : "#d4edda") . "; padding: 15px; border-radius: 8px;'>\n";
echo "<h3>🔗 URL Duplicates</h3>\n";
echo "<p><strong>Groups:</strong> " . count($url_duplicates) . "</p>\n";
if (count($url_duplicates) > 0) {
    echo "<p><strong>🚨 CRITICAL: Multiple products sharing same image URL</strong></p>\n";
    foreach (array_slice($url_duplicates, 0, 3) as $url => $products_list) {
        echo "<p style='font-size: 12px;'>" . basename($url) . " → " . count($products_list) . " products</p>\n";
    }
} else {
    echo "<p>✅ No URL duplicates found</p>\n";
}
echo "</div>\n";

// Content duplicates
echo "<div style='background: " . (count($content_duplicates) > 0 ? "#f8d7da" : "#d4edda") . "; padding: 15px; border-radius: 8px;'>\n";
echo "<h3>🎭 Content Duplicates</h3>\n";
echo "<p><strong>Groups:</strong> " . count($content_duplicates) . "</p>\n";
if (count($content_duplicates) > 0) {
    echo "<p><strong>🚨 CRITICAL: Identical image content</strong></p>\n";
    $sample_group = array_values($content_duplicates)[0];
    echo "<p style='font-size: 12px;'>Sample: " . count($sample_group) . " products with identical content</p>\n";
} else {
    echo "<p>✅ No content duplicates found</p>\n";
}
echo "</div>\n";

echo "</div>\n";

// Show specific duplicate groups in detail
if (count($content_duplicates) > 0) {
    echo "<h2>🚨 DETAILED CONTENT DUPLICATE GROUPS</h2>\n";
    
    $group_num = 1;
    foreach (array_slice($content_duplicates, 0, 10) as $hash => $products_list) {
        if (count($products_list) > 1) {
            echo "<div style='background: #fff3cd; padding: 15px; margin: 15px 0; border-radius: 8px; border: 2px solid #856404;'>\n";
            echo "<h3>Group {$group_num}: {count($products_list)} products with identical content</h3>\n";
            
            // Show the image
            $sample_url = $products_list[0]['url'];
            echo "<div style='text-align: center; margin: 10px 0;'>\n";
            echo "<img src='{$sample_url}?v=" . time() . "' style='width: 150px; height: 150px; object-fit: cover; border: 2px solid #dc3545;'>\n";
            echo "</div>\n";
            
            echo "<p><strong>Products using this identical image:</strong></p>\n";
            echo "<table style='width: 100%; border-collapse: collapse; font-size: 12px;'>\n";
            echo "<tr style='background: #f8f9fa;'><th>SKU</th><th>ID</th><th>Name</th><th>Filename</th></tr>\n";
            
            foreach ($products_list as $product) {
                echo "<tr><td>{$product['sku']}</td><td>{$product['id']}</td><td>" . substr($product['name'], 0, 30) . "...</td><td>{$product['filename']}</td></tr>\n";
            }
            echo "</table>\n";
            echo "</div>\n";
            
            $group_num++;
        }
    }
    
    if (count($content_duplicates) > 10) {
        echo "<p><em>... and " . (count($content_duplicates) - 10) . " more duplicate groups</em></p>\n";
    }
}

// Summary and action plan
$total_duplicate_groups = count($content_duplicates);
$total_affected_products = 0;
foreach ($content_duplicates as $products_list) {
    $total_affected_products += count($products_list);
}

echo "<div style='background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 25px; border-radius: 12px; margin: 30px 0;'>\n";
echo "<h2 style='color: white; margin: 0 0 15px 0;'>🎯 DUPLICATE ANALYSIS SUMMARY</h2>\n";
echo "<div style='display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin: 15px 0;'>\n";
echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px;'>\n";
echo "<h4 style='color: white; margin: 0;'>Content Duplicates</h4>\n";
echo "<p style='color: white; font-size: 24px; margin: 10px 0;'>{$total_duplicate_groups}</p>\n";
echo "<p style='color: white; font-size: 12px; margin: 0;'>Groups Found</p>\n";
echo "</div>\n";
echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px;'>\n";
echo "<h4 style='color: white; margin: 0;'>Affected Products</h4>\n";
echo "<p style='color: white; font-size: 24px; margin: 10px 0;'>{$total_affected_products}</p>\n";
echo "<p style='color: white; font-size: 12px; margin: 0;'>Need Unique Images</p>\n";
echo "</div>\n";
echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px;'>\n";
echo "<h4 style='color: white; margin: 0;'>Replacement Needed</h4>\n";
echo "<p style='color: white; font-size: 24px; margin: 10px 0;'>" . ($total_affected_products - $total_duplicate_groups) . "</p>\n";
echo "<p style='color: white; font-size: 12px; margin: 0;'>Images to Replace</p>\n";
echo "</div>\n";
echo "</div>\n";

if ($total_duplicate_groups > 0) {
    echo "<h3 style='color: white; margin: 15px 0;'>🚨 CONFIRMATION: This explains why you see identical images!</h3>\n";
    echo "<p style='color: white;'>Multiple products are sharing the exact same image files.</p>\n";
} else {
    echo "<h3 style='color: white; margin: 15px 0;'>⚠️ No technical duplicates found - investigating cache issue</h3>\n";
}
echo "</div>\n";

echo "<h3>🛒 <a href='http://localhost:8080/tienda/' target='_blank'>Current Shop Status</a></h3>\n";
echo "<p><em>Comprehensive analysis completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>