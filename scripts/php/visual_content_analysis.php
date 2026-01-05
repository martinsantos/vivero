<?php
/**
 * 🔍 VISUAL CONTENT ANALYSIS
 * Downloads and analyzes the actual visual content of images to detect visual duplicates
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🔍 VISUAL CONTENT DUPLICATE ANALYSIS</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Analyze actual image content to find visual duplicates (not just filename duplicates)</p>\n";

// Get all products and analyze their image content
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$image_hashes = [];
$visual_duplicates = [];
$processed = 0;

echo "<h2>🔍 Step 1: Analyzing image content hashes</h2>\n";
echo "<p>This will download and hash each image to detect identical visual content...</p>\n";

foreach ($products as $product) {
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        if ($image_url && strpos($image_url, '.svg') === false && strpos($image_url, 'placeholder') === false) {
            
            // Download and hash the actual image content
            $image_data = @file_get_contents($image_url);
            if ($image_data) {
                $content_hash = md5($image_data);
                
                if (!isset($image_hashes[$content_hash])) {
                    $image_hashes[$content_hash] = [];
                }
                
                $image_hashes[$content_hash][] = [
                    'id' => $product_id,
                    'name' => $product_name,
                    'url' => $image_url,
                    'filename' => basename($image_url)
                ];
            }
        }
    }
    
    $processed++;
    if ($processed % 100 === 0) {
        echo "<p>📊 Processed {$processed} products...</p>\n";
        flush();
    }
}

// Find visual duplicates
foreach ($image_hashes as $hash => $products_list) {
    if (count($products_list) > 1) {
        $visual_duplicates[$hash] = $products_list;
    }
}

$duplicate_groups = count($visual_duplicates);
$total_affected = 0;
foreach ($visual_duplicates as $products_list) {
    $total_affected += count($products_list);
}

echo "<h2>🚨 VISUAL DUPLICATE ANALYSIS RESULTS</h2>\n";

echo "<div style='background: " . ($duplicate_groups > 0 ? "#f8d7da" : "#d4edda") . "; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
if ($duplicate_groups > 0) {
    echo "<h3>🚨 VISUAL DUPLICATES CONFIRMED!</h3>\n";
    echo "<p><strong>Visual duplicate groups:</strong> {$duplicate_groups}</p>\n";
    echo "<p><strong>Products with identical visual content:</strong> {$total_affected}</p>\n";
    echo "<p><strong>This explains what you're seeing!</strong></p>\n";
} else {
    echo "<h3>✅ No visual duplicates found</h3>\n";
    echo "<p>All images have unique visual content</p>\n";
}
echo "</div>\n";

if ($duplicate_groups > 0) {
    echo "<h2>📋 DETAILED VISUAL DUPLICATE REPORT</h2>\n";
    
    $group_number = 1;
    foreach ($visual_duplicates as $hash => $products_list) {
        $group_size = count($products_list);
        
        echo "<div style='border: 2px solid #dc3545; background: #fff; padding: 15px; margin: 15px 0; border-radius: 8px;'>\n";
        echo "<h3 style='color: #dc3545; margin: 0 0 15px 0;'>🚨 Visual Duplicate Group #{$group_number} - {$group_size} products</h3>\n";
        
        // Show the duplicate image
        $sample_image = $products_list[0]['url'];
        echo "<div style='text-align: center; margin: 15px 0;'>\n";
        echo "<img src='{$sample_image}' style='width: 200px; height: 200px; object-fit: cover; border: 2px solid #dc3545;' alt='Visual Duplicate'>\n";
        echo "<p style='font-size: 12px; color: #666; margin: 10px 0;'>Content Hash: " . substr($hash, 0, 12) . "...</p>\n";
        echo "</div>\n";
        
        echo "<h4>Products with identical visual content:</h4>\n";
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
        
        // Limit display to avoid overwhelming output
        if ($group_number > 20) {
            $remaining_groups = $duplicate_groups - 20;
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 15px 0;'>\n";
            echo "<p><strong>⚠️ Display limited to first 20 visual duplicate groups</strong></p>\n";
            echo "<p>Additional {$remaining_groups} visual duplicate groups exist.</p>\n";
            echo "</div>\n";
            break;
        }
    }
}

// Special focus on Glacoxan products
echo "<h2>🎯 GLACOXAN VISUAL ANALYSIS</h2>\n";

$glacoxan_hashes = [];
foreach ($image_hashes as $hash => $products_list) {
    foreach ($products_list as $product) {
        if (stripos($product['name'], 'glacoxan') !== false) {
            if (!isset($glacoxan_hashes[$hash])) {
                $glacoxan_hashes[$hash] = [];
            }
            $glacoxan_hashes[$hash][] = $product;
        }
    }
}

$glacoxan_visual_duplicates = 0;
foreach ($glacoxan_hashes as $products_list) {
    if (count($products_list) > 1) {
        $glacoxan_visual_duplicates++;
    }
}

if ($glacoxan_visual_duplicates > 0) {
    echo "<div style='background: #f8d7da; padding: 15px; border: 2px solid #f5c6cb; border-radius: 8px;'>\n";
    echo "<h3>🚨 GLACOXAN VISUAL DUPLICATES FOUND!</h3>\n";
    echo "<p>{$glacoxan_visual_duplicates} groups of Glacoxan products share identical visual content</p>\n";
    echo "</div>\n";
} else {
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 8px;'>\n";
    echo "<h3>✅ Glacoxan products have unique visual content</h3>\n";
    echo "</div>\n";
}

// Show sample of Glacoxan images with their hashes
echo "<h3>📸 Glacoxan Image Content Sample:</h3>\n";
echo "<div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 20px 0;'>\n";

$glacoxan_sample = [];
foreach ($image_hashes as $hash => $products_list) {
    foreach ($products_list as $product) {
        if (stripos($product['name'], 'glacoxan') !== false) {
            $glacoxan_sample[] = [
                'product' => $product,
                'hash' => substr($hash, 0, 8)
            ];
            break; // One product per hash for display
        }
    }
    if (count($glacoxan_sample) >= 8) break;
}

foreach ($glacoxan_sample as $item) {
    $product = $item['product'];
    $hash_preview = $item['hash'];
    echo "<div style='border: 1px solid #ddd; padding: 10px; text-align: center; background: white;'>\n";
    echo "<img src='{$product['url']}' style='width: 120px; height: 120px; object-fit: cover; border: 1px solid #ccc;' alt='Glacoxan'>\n";
    echo "<p style='font-size: 10px; margin: 5px 0; font-weight: bold;'>ID: {$product['id']}</p>\n";
    echo "<p style='font-size: 9px; margin: 5px 0;'>{$product['name']}</p>\n";
    echo "<p style='font-size: 8px; color: #666;'>Hash: {$hash_preview}...</p>\n";
    echo "</div>\n";
}

echo "</div>\n";

// Summary statistics
echo "<h2>📊 FINAL VISUAL ANALYSIS SUMMARY</h2>\n";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>\n";
echo "<tr style='background: #f8f9fa;'>\n";
echo "<th>Metric</th><th>Count</th><th>Status</th>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Total Products Analyzed</td>\n";
echo "<td>{$processed}</td>\n";
echo "<td>✅ Complete</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Visual Duplicate Groups</td>\n";
echo "<td>{$duplicate_groups}</td>\n";
echo "<td>" . ($duplicate_groups > 0 ? "❌ Found" : "✅ None") . "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Products with Duplicate Content</td>\n";
echo "<td>{$total_affected}</td>\n";
echo "<td>" . ($total_affected > 0 ? "❌ Need fixing" : "✅ All unique") . "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Glacoxan Visual Duplicates</td>\n";
echo "<td>{$glacoxan_visual_duplicates}</td>\n";
echo "<td>" . ($glacoxan_visual_duplicates > 0 ? "❌ Found" : "✅ None") . "</td>\n";
echo "</tr>\n";
echo "</table>\n";

if ($duplicate_groups > 0) {
    echo "<div style='background: #e7f3ff; padding: 20px; border: 2px solid #007bff; border-radius: 10px; margin: 30px 0;'>\n";
    echo "<h3>🔧 NEXT STEPS TO FIX VISUAL DUPLICATES</h3>\n";
    echo "<p><strong>Found {$duplicate_groups} groups with identical visual content affecting {$total_affected} products</strong></p>\n";
    echo "<p>These need to be replaced with unique images to eliminate visual duplication.</p>\n";
    echo "</div>\n";
}

echo "<h3>🔗 <a href='http://localhost:8080/tienda/' target='_blank'>Check current shop display</a></h3>\n";
echo "<p><em>Visual content analysis completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>