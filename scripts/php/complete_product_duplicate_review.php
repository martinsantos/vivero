<?php
/**
 * 🔍 COMPLETE PRODUCT LIST DUPLICATE REVIEW
 * Reviews ALL products systematically to find and display ALL duplicate images
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🔍 COMPLETE PRODUCT LIST DUPLICATE REVIEW</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Review ALL 578 products to identify EVERY duplicate image</p>\n";

// Get ALL products
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$total_products = count($products);

echo "<h2>📊 Product Overview</h2>\n";
echo "<p><strong>Total products found:</strong> {$total_products}</p>\n";

// Group products by actual image content
$image_groups = [];
$processed_count = 0;

echo "<h2>🔍 Step 1: Analyzing all product images...</h2>\n";
echo "<p>This may take a moment to download and analyze all images...</p>\n";

foreach ($products as $product) {
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        if ($image_url && strpos($image_url, '.svg') === false && strpos($image_url, 'placeholder') === false) {
            
            // Download image and create content hash for true duplicate detection
            $image_data = @file_get_contents($image_url);
            if ($image_data) {
                $content_hash = md5($image_data);
                
                if (!isset($image_groups[$content_hash])) {
                    $image_groups[$content_hash] = [];
                }
                
                $image_groups[$content_hash][] = [
                    'id' => $product_id,
                    'name' => $product_name,
                    'url' => $image_url,
                    'filename' => basename($image_url)
                ];
            }
        }
    }
    
    $processed_count++;
    
    // Progress update every 50 products
    if ($processed_count % 50 === 0) {
        echo "<p>📊 Processed {$processed_count}/{$total_products} products...</p>\n";
        flush();
    }
}

echo "<h2>🚨 Step 2: Duplicate Analysis Results</h2>\n";

// Find duplicates
$duplicate_groups = [];
$total_duplicated_products = 0;

foreach ($image_groups as $hash => $products_list) {
    if (count($products_list) > 1) {
        $duplicate_groups[$hash] = $products_list;
        $total_duplicated_products += count($products_list);
    }
}

$duplicate_count = count($duplicate_groups);

echo "<div style='background: " . ($duplicate_count > 0 ? "#f8d7da" : "#d4edda") . "; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
echo "<h3>" . ($duplicate_count > 0 ? "🚨 DUPLICATES FOUND!" : "✅ NO DUPLICATES") . "</h3>\n";
echo "<p><strong>Duplicate image groups:</strong> {$duplicate_count}</p>\n";
echo "<p><strong>Total products affected:</strong> {$total_duplicated_products}</p>\n";
echo "</div>\n";

if ($duplicate_count > 0) {
    echo "<h2>📋 DETAILED DUPLICATE REPORT</h2>\n";
    
    $group_number = 1;
    foreach ($duplicate_groups as $hash => $products_list) {
        $group_size = count($products_list);
        
        echo "<div style='border: 2px solid #dc3545; background: #fff; padding: 15px; margin: 15px 0; border-radius: 8px;'>\n";
        echo "<h3 style='color: #dc3545; margin: 0 0 15px 0;'>🚨 Duplicate Group #{$group_number} - {$group_size} products</h3>\n";
        
        // Show the duplicate image
        $sample_image = $products_list[0]['url'];
        echo "<div style='text-align: center; margin: 15px 0;'>\n";
        echo "<img src='{$sample_image}' style='width: 200px; height: 200px; object-fit: cover; border: 2px solid #dc3545;' alt='Duplicate Image'>\n";
        echo "<p style='font-size: 12px; color: #666; margin: 10px 0;'>Content Hash: " . substr($hash, 0, 12) . "...</p>\n";
        echo "</div>\n";
        
        // List all products using this image
        echo "<h4>Products using this identical image:</h4>\n";
        echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-top: 10px;'>\n";
        
        foreach ($products_list as $product_info) {
            echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 5px; border: 1px solid #dee2e6;'>\n";
            echo "<p style='margin: 0; font-weight: bold; color: #495057;'>ID: {$product_info['id']}</p>\n";
            echo "<p style='margin: 5px 0 0 0; font-size: 14px;'>{$product_info['name']}</p>\n";
            echo "<p style='margin: 5px 0 0 0; font-size: 11px; color: #6c757d;'>{$product_info['filename']}</p>\n";
            echo "</div>\n";
        }
        
        echo "</div>\n";
        echo "</div>\n";
        
        $group_number++;
        
        // Limit display to first 10 groups to avoid overwhelming output
        if ($group_number > 10) {
            $remaining_groups = $duplicate_count - 10;
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 15px 0;'>\n";
            echo "<p><strong>⚠️ Display limited to first 10 duplicate groups</strong></p>\n";
            echo "<p>Additional {$remaining_groups} duplicate groups exist and need to be fixed.</p>\n";
            echo "</div>\n";
            break;
        }
    }
    
    // Create fix button/recommendation
    echo "<div style='background: #e7f3ff; padding: 20px; border: 2px solid #007bff; border-radius: 8px; margin: 30px 0;'>\n";
    echo "<h3 style='color: #004085; margin: 0 0 15px 0;'>🔧 IMMEDIATE ACTION REQUIRED</h3>\n";
    echo "<p><strong>Found {$duplicate_count} groups of duplicate images affecting {$total_duplicated_products} products</strong></p>\n";
    echo "<p>These duplicates need to be fixed by replacing all but one product in each group with unique images.</p>\n";
    echo "<h4>Recommended next steps:</h4>\n";
    echo "<ol>\n";
    echo "<li>Run the comprehensive duplicate fix script</li>\n";
    echo "<li>Replace all duplicate images with unique alternatives</li>\n";
    echo "<li>Verify results across the entire catalog</li>\n";
    echo "</ol>\n";
    echo "</div>\n";
    
} else {
    echo "<div style='background: #d4edda; padding: 20px; border: 2px solid #c3e6cb; border-radius: 8px; margin: 30px 0;'>\n";
    echo "<h3 style='color: #155724; margin: 0 0 15px 0;'>🎉 PERFECT! NO DUPLICATES FOUND</h3>\n";
    echo "<p><strong>All {$total_products} products have unique images</strong></p>\n";
    echo "<p>The catalog has achieved perfect image uniqueness!</p>\n";
    echo "</div>\n";
}

// Summary statistics
echo "<h2>📊 FINAL STATISTICS</h2>\n";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>\n";
echo "<tr style='background: #f8f9fa;'>\n";
echo "<th>Metric</th><th>Count</th><th>Percentage</th>\n";
echo "</tr>\n";

$unique_products = $total_products - $total_duplicated_products;
$unique_percentage = round(($unique_products / $total_products) * 100, 1);
$duplicate_percentage = round(($total_duplicated_products / $total_products) * 100, 1);

echo "<tr>\n";
echo "<td>Total Products</td>\n";
echo "<td>{$total_products}</td>\n";
echo "<td>100%</td>\n";
echo "</tr>\n";
echo "<tr style='background: #d4edda;'>\n";
echo "<td>Products with Unique Images</td>\n";
echo "<td>{$unique_products}</td>\n";
echo "<td>{$unique_percentage}%</td>\n";
echo "</tr>\n";
echo "<tr style='background: " . ($total_duplicated_products > 0 ? "#f8d7da" : "#d4edda") . ";'>\n";
echo "<td>Products with Duplicate Images</td>\n";
echo "<td>{$total_duplicated_products}</td>\n";
echo "<td>{$duplicate_percentage}%</td>\n";
echo "</tr>\n";
echo "<tr style='background: " . ($duplicate_count > 0 ? "#fff3cd" : "#d4edda") . ";'>\n";
echo "<td>Duplicate Image Groups</td>\n";
echo "<td>{$duplicate_count}</td>\n";
echo "<td>-</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "<h3>🔗 <a href='http://localhost:8080/tienda/' target='_blank'>Review current shop display</a></h3>\n";
echo "<p><em>Complete product review completed: " . date('Y-m-d H:i:s') . "</em></p>\n";

// Store results for potential fix script
if ($duplicate_count > 0) {
    $fix_data = [
        'duplicate_groups' => $duplicate_groups,
        'total_duplicates' => $total_duplicated_products,
        'scan_date' => date('Y-m-d H:i:s')
    ];
    
    // Save to temporary file for fix script
    file_put_contents('/tmp/duplicate_fix_data.json', json_encode($fix_data, JSON_PRETTY_PRINT));
    echo "<p style='color: #666; font-size: 12px;'><em>Duplicate data saved for fix script processing</em></p>\n";
}
?>