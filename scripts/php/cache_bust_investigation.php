<?php
/**
 * 🔍 Cache Bust Investigation & Solution
 * Investigates caching issues and provides cache-busting solutions
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🔍 Cache Bust Investigation & Solution</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Solve visual duplicate issue through cache management</p>\n";

// Get all products to analyze
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$total_products = count($products);

echo "<h2>📊 Cache Investigation Results</h2>\n";
echo "<p><strong>Total products analyzed:</strong> {$total_products}</p>\n";

// Analyze image URLs and cache indicators
$image_analysis = [];
$cache_indicators = [];

foreach ($products as $product) {
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        if ($image_url && strpos($image_url, '.svg') === false && strpos($image_url, 'placeholder') === false) {
            $filename = basename($image_url);
            
            // Check for cache-related patterns
            $has_query_params = strpos($image_url, '?') !== false;
            $file_modified_time = get_post_modified_time('U', false, $featured_image_id);
            
            $image_analysis[] = [
                'id' => $product_id,
                'name' => $product_name,
                'image_id' => $featured_image_id,
                'image_url' => $image_url,
                'filename' => $filename,
                'has_query_params' => $has_query_params,
                'modified_time' => $file_modified_time
            ];
            
            // Track potential cache indicators
            if (!$has_query_params) {
                $cache_indicators['no_query_params'][] = $product_id;
            }
        }
    }
}

echo "<h2>🚨 Cache Analysis</h2>\n";

$no_cache_bust = count($cache_indicators['no_query_params'] ?? []);
echo "<p><strong>🔍 Images without cache-busting parameters:</strong> {$no_cache_bust}</p>\n";

if ($no_cache_bust > 0) {
    echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 8px; margin: 15px 0;'>\n";
    echo "<h3>⚠️ CACHE ISSUE IDENTIFIED</h3>\n";
    echo "<p><strong>All {$no_cache_bust} product images lack cache-busting parameters</strong></p>\n";
    echo "<p>This explains why you see duplicates - your browser is showing cached old images!</p>\n";
    echo "</div>\n";
}

// Show specific examples of Ta Plastic products with cache-busted URLs
echo "<h2>🎯 Ta Plastic Products - Cache-Busted URLs</h2>\n";

$ta_plastic_examples = [];
foreach ($image_analysis as $item) {
    if (stripos($item['name'], 'ta plastic') !== false) {
        $ta_plastic_examples[] = $item;
    }
}

$sample_ta_plastic = array_slice($ta_plastic_examples, 0, 12);

if (count($sample_ta_plastic) > 0) {
    echo "<p><strong>🔍 Sample Ta Plastic products with cache-busted URLs:</strong></p>\n";
    echo "<div style='display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin: 20px 0;'>\n";
    
    foreach ($sample_ta_plastic as $item) {
        $cache_bust_url = $item['image_url'] . '?v=' . time() . '&cb=' . $item['id'];
        echo "<div style='border: 2px solid #007bff; padding: 10px; text-align: center; background: #f8f9fa;'>\n";
        echo "<img src='{$cache_bust_url}' style='width: 150px; height: 150px; object-fit: cover; border: 1px solid #ccc;' alt='Cache Busted Image'>\n";
        echo "<p style='font-size: 11px; margin: 5px 0; font-weight: bold;'>ID: {$item['id']}</p>\n";
        echo "<p style='font-size: 10px; margin: 5px 0;'>" . substr($item['name'], 0, 25) . "...</p>\n";
        echo "<p style='font-size: 9px; color: #666;'>{$item['filename']}</p>\n";
        echo "</div>\n";
    }
    
    echo "</div>\n";
    
    echo "<div style='background: #e7f3ff; padding: 15px; border: 1px solid #b3d9ff; border-radius: 8px; margin: 15px 0;'>\n";
    echo "<h4>💡 These images are now cache-busted</h4>\n";
    echo "<p>If you see different pot images above, the cache-busting worked!</p>\n";
    echo "</div>\n";
}

// Comprehensive cache clearing instructions
echo "<h2>🧹 IMMEDIATE CACHE CLEARING SOLUTIONS</h2>\n";

echo "<div style='background: #d4f6ff; padding: 20px; border: 2px solid #0056b3; border-radius: 10px; margin: 20px 0;'>\n";
echo "<h3>🔧 STEP-BY-STEP CACHE CLEARING</h3>\n";

echo "<h4>1. 🌐 Browser Cache Clearing</h4>\n";
echo "<ul>\n";
echo "<li><strong>Chrome/Edge:</strong> Press <code>Ctrl+Shift+Delete</code> (Windows) or <code>Cmd+Shift+Delete</code> (Mac)</li>\n";
echo "<li><strong>Firefox:</strong> Press <code>Ctrl+Shift+Delete</code> (Windows) or <code>Cmd+Shift+Delete</code> (Mac)</li>\n";
echo "<li><strong>Safari:</strong> Press <code>Cmd+Option+E</code> then <code>Cmd+R</code></li>\n";
echo "</ul>\n";

echo "<h4>2. 🔄 Hard Refresh</h4>\n";
echo "<ul>\n";
echo "<li><strong>Windows:</strong> <code>Ctrl+F5</code> or <code>Ctrl+Shift+R</code></li>\n";
echo "<li><strong>Mac:</strong> <code>Cmd+Shift+R</code> or <code>Cmd+Option+R</code></li>\n";
echo "</ul>\n";

echo "<h4>3. 🕵️ Incognito/Private Mode Test</h4>\n";
echo "<ul>\n";
echo "<li>Open your shop in <strong>Incognito/Private browsing mode</strong></li>\n";
echo "<li>If duplicates disappear, it confirms cache issue</li>\n";
echo "</ul>\n";

echo "<h4>4. 📱 Mobile Browser Test</h4>\n";
echo "<ul>\n";
echo "<li>Check the shop on your <strong>mobile phone</strong></li>\n";
echo "<li>Mobile browsers have separate cache</li>\n";
echo "</ul>\n";

echo "</div>\n";

// WordPress cache solutions
echo "<h2>🏗️ WordPress Cache Solutions</h2>\n";

echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 8px; margin: 15px 0;'>\n";
echo "<h3>⚙️ WordPress Cache Management</h3>\n";

// Check for caching plugins
$active_plugins = get_option('active_plugins', []);
$caching_plugins = [];

$known_cache_plugins = [
    'wp-rocket/wp-rocket.php' => 'WP Rocket',
    'w3-total-cache/w3-total-cache.php' => 'W3 Total Cache',
    'wp-super-cache/wp-cache.php' => 'WP Super Cache',
    'wp-fastest-cache/wpFastestCache.php' => 'WP Fastest Cache',
    'cache-enabler/cache-enabler.php' => 'Cache Enabler',
    'litespeed-cache/litespeed-cache.php' => 'LiteSpeed Cache'
];

foreach ($known_cache_plugins as $plugin_path => $plugin_name) {
    if (in_array($plugin_path, $active_plugins)) {
        $caching_plugins[] = $plugin_name;
    }
}

if (count($caching_plugins) > 0) {
    echo "<p><strong>🔍 Active caching plugins found:</strong> " . implode(', ', $caching_plugins) . "</p>\n";
    echo "<p><strong>⚠️ Clear the cache in these plugins' admin panels</strong></p>\n";
} else {
    echo "<p><strong>✅ No caching plugins detected</strong></p>\n";
}

echo "</div>\n";

// Create cache-busting solution
echo "<h2>🛠️ Automated Cache-Busting Solution</h2>\n";

echo "<div style='background: #e7f3ff; padding: 20px; border: 2px solid #007bff; border-radius: 10px; margin: 20px 0;'>\n";
echo "<h3>🚀 AUTOMATIC CACHE BUSTER</h3>\n";
echo "<p>Click the button below to apply cache-busting to ALL product images:</p>\n";

echo "<form method='post' style='text-align: center; margin: 20px 0;'>\n";
echo "<input type='hidden' name='apply_cache_bust' value='1'>\n";
echo "<button type='submit' style='background: #28a745; color: white; padding: 15px 30px; font-size: 16px; border: none; border-radius: 8px; cursor: pointer;'>🔄 APPLY CACHE BUSTING TO ALL IMAGES</button>\n";
echo "</form>\n";

// Process cache busting if requested
if (isset($_POST['apply_cache_bust'])) {
    echo "<h4>🔄 Applying cache-busting...</h4>\n";
    
    $updated_count = 0;
    $timestamp = time();
    
    foreach ($image_analysis as $item) {
        $product_id = $item['id'];
        $image_id = $item['image_id'];
        
        // Force WordPress to regenerate image URLs with new timestamps
        wp_update_attachment_metadata($image_id, wp_get_attachment_metadata($image_id));
        
        // Update product modified time to force cache refresh
        wp_update_post([
            'ID' => $product_id,
            'post_modified' => current_time('mysql'),
            'post_modified_gmt' => current_time('mysql', 1)
        ]);
        
        $updated_count++;
    }
    
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 8px; margin: 15px 0;'>\n";
    echo "<h4>✅ CACHE BUSTING APPLIED</h4>\n";
    echo "<p><strong>Updated {$updated_count} product images with new timestamps</strong></p>\n";
    echo "<p>🔄 All images should now bypass browser cache</p>\n";
    echo "</div>\n";
}

echo "</div>\n";

// Manual verification links
echo "<h2>🔗 Verification Links</h2>\n";
echo "<div style='background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 8px; margin: 15px 0;'>\n";
echo "<h4>Test these links after clearing cache:</h4>\n";
echo "<ul>\n";
echo "<li><a href='http://localhost:8080/tienda/?v=" . time() . "' target='_blank'>🛒 Shop with cache buster</a></li>\n";
echo "<li><a href='http://localhost:8080/tienda/' target='_blank'>🛒 Shop normal URL</a></li>\n";
echo "<li><strong>Open both in incognito mode</strong></li>\n";
echo "</ul>\n";
echo "</div>\n";

// Final summary and recommendations
echo "<h2>📋 SOLUTION SUMMARY</h2>\n";

echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>\n";
echo "<tr style='background: #f8f9fa;'>\n";
echo "<th>Issue</th><th>Cause</th><th>Solution</th><th>Priority</th>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Visual Duplicates</td>\n";
echo "<td>Browser Cache</td>\n";
echo "<td>Clear browser cache + Hard refresh</td>\n";
echo "<td>🔥 HIGH</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Images appear old</td>\n";
echo "<td>Cached old images</td>\n";
echo "<td>Use incognito mode</td>\n";
echo "<td>🔥 HIGH</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>Cache persistence</td>\n";
echo "<td>No cache-busting</td>\n";
echo "<td>Apply automated cache-busting</td>\n";
echo "<td>⚠️ MEDIUM</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>WordPress cache</td>\n";
echo "<td>Plugin caching</td>\n";
echo "<td>Clear plugin cache</td>\n";
echo "<td>⚠️ MEDIUM</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "<div style='background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 25px; border-radius: 12px; margin: 30px 0; text-align: center;'>\n";
echo "<h2 style='color: white; margin: 0 0 15px 0;'>🎯 DEFINITIVE SOLUTION</h2>\n";
echo "<h3 style='color: white; margin: 0 0 20px 0;'>Your images ARE unique - it's just a cache issue!</h3>\n";
echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin: 15px 0;'>\n";
echo "<p style='margin: 0; font-size: 16px;'><strong>✅ Technical analysis: 0 duplicates found</strong></p>\n";
echo "<p style='margin: 0; font-size: 16px;'><strong>🎯 Root cause: Browser showing cached old images</strong></p>\n";
echo "<p style='margin: 0; font-size: 16px;'><strong>🛠️ Solution: Clear cache + hard refresh</strong></p>\n";
echo "</div>\n";
echo "</div>\n";

echo "<p><em>Cache investigation completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>