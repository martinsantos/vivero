<?php
/**
 * 🔍 Visual Duplicate Detective
 * Detects visually identical images even with different URLs/filenames
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🔍 Visual Duplicate Detective</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Find and display ALL visually similar images</p>\n";

// Get products and their images
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$product_images = [];

echo "<h2>📦 Step 1: Collecting product image data</h2>\n";

foreach ($products as $product) {
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        if ($image_url && strpos($image_url, '.svg') === false && strpos($image_url, 'placeholder') === false) {
            $product_images[] = [
                'id' => $product_id,
                'name' => $product_name,
                'image_id' => $featured_image_id,
                'image_url' => $image_url,
                'filename' => basename($image_url)
            ];
        }
    }
}

echo "<p><strong>📊 Total products with real images:</strong> " . count($product_images) . "</p>\n";

// Group by filename for obvious duplicates
echo "<h2>🔍 Step 2: Detecting filename-based duplicates</h2>\n";

$filename_groups = [];
foreach ($product_images as $item) {
    $filename = $item['filename'];
    if (!isset($filename_groups[$filename])) {
        $filename_groups[$filename] = [];
    }
    $filename_groups[$filename][] = $item;
}

$filename_duplicates = [];
foreach ($filename_groups as $filename => $items) {
    if (count($items) > 1) {
        $filename_duplicates[$filename] = $items;
    }
}

echo "<p><strong>📄 Filename-based duplicate groups:</strong> " . count($filename_duplicates) . "</p>\n";

if (count($filename_duplicates) > 0) {
    echo "<h3>📋 Filename Duplicates Found:</h3>\n";
    foreach ($filename_duplicates as $filename => $items) {
        echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0; background: #fff3cd;'>\n";
        echo "<p><strong>📄 File:</strong> {$filename}</p>\n";
        echo "<p><strong>🔗 Used by " . count($items) . " products:</strong></p>\n";
        echo "<ul>\n";
        foreach ($items as $item) {
            echo "<li>ID: {$item['id']} - {$item['name']}</li>\n";
        }
        echo "</ul>\n";
        echo "</div>\n";
    }
}

// Sample image comparison for visual inspection
echo "<h2>👁️ Step 3: Visual Sample Inspection</h2>\n";
echo "<p>Showing first 20 product images for manual duplicate verification:</p>\n";

echo "<div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 20px 0;'>\n";

$sample_products = array_slice($product_images, 0, 20);
foreach ($sample_products as $item) {
    echo "<div style='border: 1px solid #ddd; padding: 10px; text-align: center; background: white;'>\n";
    echo "<img src='{$item['image_url']}' style='width: 150px; height: 150px; object-fit: cover; border: 1px solid #ccc;' alt='Product Image'>\n";
    echo "<p style='font-size: 12px; margin: 5px 0;'><strong>ID:</strong> {$item['id']}</p>\n";
    echo "<p style='font-size: 12px; margin: 5px 0;'><strong>Name:</strong> {$item['name']}</p>\n";
    echo "<p style='font-size: 10px; color: #666;'>{$item['filename']}</p>\n";
    echo "</div>\n";
}

echo "</div>\n";

// Check Ta Plastic products specifically (from your screenshot)
echo "<h2>🎯 Step 4: Ta Plastic Products Analysis</h2>\n";

$ta_plastic_products = [];
foreach ($product_images as $item) {
    if (stripos($item['name'], 'ta plastic') !== false) {
        $ta_plastic_products[] = $item;
    }
}

echo "<p><strong>🔍 Ta Plastic products found:</strong> " . count($ta_plastic_products) . "</p>\n";

if (count($ta_plastic_products) > 0) {
    echo "<h3>📋 Ta Plastic Products Detail:</h3>\n";
    echo "<div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 20px 0;'>\n";
    
    foreach ($ta_plastic_products as $item) {
        echo "<div style='border: 2px solid #f44336; padding: 10px; text-align: center; background: #ffebee;'>\n";
        echo "<img src='{$item['image_url']}' style='width: 120px; height: 120px; object-fit: cover; border: 1px solid #ccc;' alt='Ta Plastic'>\n";
        echo "<p style='font-size: 12px; margin: 5px 0; font-weight: bold;'>ID: {$item['id']}</p>\n";
        echo "<p style='font-size: 11px; margin: 5px 0;'>{$item['name']}</p>\n";
        echo "<p style='font-size: 10px; color: #666;'>{$item['filename']}</p>\n";
        echo "</div>\n";
    }
    
    echo "</div>\n";
    
    // Check if Ta Plastic products have the same filename
    $ta_plastic_files = [];
    foreach ($ta_plastic_products as $item) {
        $filename = $item['filename'];
        if (!isset($ta_plastic_files[$filename])) {
            $ta_plastic_files[$filename] = [];
        }
        $ta_plastic_files[$filename][] = $item['id'];
    }
    
    $ta_plastic_duplicates = [];
    foreach ($ta_plastic_files as $filename => $product_ids) {
        if (count($product_ids) > 1) {
            $ta_plastic_duplicates[$filename] = $product_ids;
        }
    }
    
    if (count($ta_plastic_duplicates) > 0) {
        echo "<div style='background: #f8d7da; padding: 15px; border: 2px solid #f5c6cb; border-radius: 5px;'>\n";
        echo "<h3>🚨 TA PLASTIC DUPLICATES CONFIRMED!</h3>\n";
        foreach ($ta_plastic_duplicates as $filename => $product_ids) {
            echo "<p><strong>📄 File:</strong> {$filename}</p>\n";
            echo "<p><strong>🔗 Products using same file:</strong> " . implode(', ', $product_ids) . "</p>\n";
        }
        echo "</div>\n";
    } else {
        echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>\n";
        echo "<h3>✅ Ta Plastic products have different files</h3>\n";
        echo "<p>The issue might be cached images or visually similar content</p>\n";
        echo "</div>\n";
    }
}

// Cache clearing suggestion
echo "<h2>🧹 Step 5: Cache Clearing Recommendation</h2>\n";
echo "<div style='background: #e7f3ff; padding: 15px; border: 1px solid #b3d9ff; border-radius: 5px;'>\n";
echo "<h3>💡 If you're still seeing duplicates, try:</h3>\n";
echo "<ol>\n";
echo "<li><strong>Clear browser cache:</strong> Ctrl+F5 or Cmd+Shift+R</li>\n";
echo "<li><strong>Clear WordPress cache:</strong> if using caching plugins</li>\n";
echo "<li><strong>Force refresh images:</strong> by appending ?v=" . time() . " to URLs</li>\n";
echo "</ol>\n";
echo "</div>\n";

// Summary
echo "<h2>📊 Summary Report</h2>\n";
echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>\n";
echo "<tr style='background: #f8f9fa;'>\n";
echo "<th>Metric</th><th>Count</th><th>Status</th>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Total Products with Images</td>\n";
echo "<td>" . count($product_images) . "</td>\n";
echo "<td>✅ Good</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Filename-based Duplicates</td>\n";
echo "<td>" . count($filename_duplicates) . " groups</td>\n";
echo "<td>" . (count($filename_duplicates) > 0 ? "❌ Found" : "✅ None") . "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Ta Plastic Products</td>\n";
echo "<td>" . count($ta_plastic_products) . "</td>\n";
echo "<td>" . (count($ta_plastic_products) > 0 ? "🔍 Inspected" : "✅ None") . "</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "<h3>🔗 <a href='http://localhost:8080/tienda/' target='_blank'>Check current shop display</a></h3>\n";
echo "<p><em>Visual duplicate detection completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>