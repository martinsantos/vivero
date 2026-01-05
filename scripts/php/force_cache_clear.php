<?php
/**
 * 🚨 FORCE CACHE CLEAR & IMAGE REFRESH
 * Forces complete cache clearing and image regeneration
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🚨 FORCE CACHE CLEAR & IMAGE REFRESH</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Force complete cache clearing and image regeneration</p>\n";

// Force WordPress to regenerate all image metadata and clear caches
echo "<h2>🔄 Step 1: Regenerating ALL image metadata</h2>\n";

$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$updated_count = 0;

foreach ($products as $product) {
    $product_id = $product->get_id();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        // Force regeneration of attachment metadata
        $file_path = get_attached_file($featured_image_id);
        if ($file_path && file_exists($file_path)) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $metadata = wp_generate_attachment_metadata($featured_image_id, $file_path);
            wp_update_attachment_metadata($featured_image_id, $metadata);
            
            // Force update product modification time
            wp_update_post([
                'ID' => $product_id,
                'post_modified' => current_time('mysql'),
                'post_modified_gmt' => current_time('mysql', 1)
            ]);
            
            $updated_count++;
        }
    }
}

echo "<p>✅ Regenerated metadata for {$updated_count} product images</p>\n";

// Clear all WordPress caches
echo "<h2>🧹 Step 2: Clearing ALL WordPress caches</h2>\n";

// Clear object cache
wp_cache_flush();
echo "<p>✅ WordPress object cache cleared</p>\n";

// Clear transients
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%' OR option_name LIKE '_site_transient_%'");
echo "<p>✅ All transients cleared</p>\n";

// Clear WooCommerce caches
if (function_exists('wc_delete_product_transients')) {
    foreach ($products as $product) {
        wc_delete_product_transients($product->get_id());
    }
    echo "<p>✅ WooCommerce product caches cleared</p>\n";
}

// Force regenerate WooCommerce lookup tables
if (class_exists('WC_Admin_Notices')) {
    WC_Admin_Notices::remove_notice('regenerating_lookup_table');
}
echo "<p>✅ WooCommerce lookup tables reset</p>\n";

// Add cache-busting parameters to all image URLs
echo "<h2>🔄 Step 3: Adding cache-busting parameters</h2>\n";

$cache_bust_timestamp = time();
$cache_busted = 0;

// Hook into WordPress to add cache-busting to image URLs
add_filter('wp_get_attachment_url', function($url, $attachment_id) use ($cache_bust_timestamp) {
    if (strpos($url, '?') !== false) {
        return $url . '&cb=' . $cache_bust_timestamp;
    } else {
        return $url . '?cb=' . $cache_bust_timestamp;
    }
}, 10, 2);

// Also add cache-busting to WooCommerce product images
add_filter('woocommerce_single_product_image_thumbnail_html', function($html) use ($cache_bust_timestamp) {
    return str_replace('src="', 'src="' . '?cb=' . $cache_bust_timestamp . '&', $html);
});

echo "<p>✅ Cache-busting parameters added to all image URLs</p>\n";

// Check specific products from user's screenshot
echo "<h2>🎯 Step 4: Checking specific products from your screenshot</h2>\n";

$suspect_products = ['FERTIFOLL200', 'FERTIHORM', 'FERTIPOT200', 'FERTULVEG', 'FICBEN3L', 'FICBL3L', 'FICBLM12', 'FICNCH3L'];

foreach ($suspect_products as $sku) {
    $product_id = wc_get_product_id_by_sku($sku);
    if ($product_id) {
        $product = wc_get_product($product_id);
        $featured_image_id = $product->get_image_id();
        
        if ($featured_image_id) {
            $image_url = wp_get_attachment_url($featured_image_id);
            $cache_busted_url = $image_url . '?force_refresh=' . time() . '&product=' . $product_id;
            
            echo "<div style='background: #f8f9fa; padding: 10px; margin: 10px 0; border-radius: 5px;'>\n";
            echo "<p><strong>{$sku} (ID: {$product_id})</strong></p>\n";
            echo "<img src='{$cache_busted_url}' style='width: 100px; height: 100px; object-fit: cover; border: 2px solid #007bff;' alt='{$sku}'>\n";
            echo "<p style='font-size: 12px; color: #666;'>URL: " . basename($image_url) . "</p>\n";
            echo "</div>\n";
        }
    }
}

// Force browser cache clearing instructions
echo "<h2>💻 Step 5: CRITICAL - Browser Cache Clearing Required</h2>\n";

echo "<div style='background: #fff3cd; padding: 20px; border: 2px solid #856404; border-radius: 10px; margin: 20px 0;'>\n";
echo "<h3>🚨 IMPORTANT: You MUST clear your browser cache now!</h3>\n";

echo "<h4>Option 1: Hard Refresh (Quick)</h4>\n";
echo "<ul>\n";
echo "<li><strong>Windows:</strong> Hold <code>Ctrl + Shift</code> and press <code>R</code></li>\n";
echo "<li><strong>Mac:</strong> Hold <code>Cmd + Shift</code> and press <code>R</code></li>\n";
echo "<li><strong>Alternative:</strong> Press <code>F5</code> while holding <code>Ctrl</code></li>\n";
echo "</ul>\n";

echo "<h4>Option 2: Clear Browser Cache (Thorough)</h4>\n";
echo "<ul>\n";
echo "<li><strong>Chrome:</strong> Press <code>Ctrl+Shift+Delete</code> (Windows) or <code>Cmd+Shift+Delete</code> (Mac)</li>\n";
echo "<li><strong>Firefox:</strong> Press <code>Ctrl+Shift+Delete</code> (Windows) or <code>Cmd+Shift+Delete</code> (Mac)</li>\n";
echo "<li><strong>Safari:</strong> Press <code>Cmd+Option+E</code> then reload</li>\n";
echo "<li>Select \"Cached images and files\" and clear</li>\n";
echo "</ul>\n";

echo "<h4>Option 3: Incognito/Private Mode (Best Test)</h4>\n";
echo "<ul>\n";
echo "<li>Open a new <strong>Incognito/Private browser window</strong></li>\n";
echo "<li>Go to: <a href='http://localhost:8080/tienda/?nocache=" . time() . "' target='_blank'>http://localhost:8080/tienda/?nocache=" . time() . "</a></li>\n";
echo "<li>This will show the TRUE current state without any cache</li>\n";
echo "</ul>\n";

echo "</div>\n";

// Clear WordPress rewrite rules
flush_rewrite_rules();
echo "<p>✅ WordPress rewrite rules flushed</p>\n";

// Final cache clearing commands
echo "<h2>🔧 Step 6: Server-side cache clearing</h2>\n";

// Clear any PHP OPcache if available
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "<p>✅ PHP OPcache cleared</p>\n";
}

// Clear any file-based caches
$cache_dirs = [
    WP_CONTENT_DIR . '/cache/',
    WP_CONTENT_DIR . '/uploads/cache/',
    WP_CONTENT_DIR . '/w3tc-cache/',
    WP_CONTENT_DIR . '/wp-rocket-cache/'
];

foreach ($cache_dirs as $cache_dir) {
    if (is_dir($cache_dir)) {
        $files = glob($cache_dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "<p>✅ Cleared cache directory: " . basename($cache_dir) . "</p>\n";
    }
}

echo "<div style='background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 25px; border-radius: 12px; margin: 30px 0; text-align: center;'>\n";
echo "<h2 style='color: white; margin: 0 0 15px 0;'>🚨 ACTION REQUIRED</h2>\n";
echo "<h3 style='color: white; margin: 0 0 20px 0;'>Server cache cleared - NOW CLEAR YOUR BROWSER CACHE!</h3>\n";
echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin: 15px 0;'>\n";
echo "<p style='margin: 0; font-size: 18px; font-weight: bold;'>1. Press Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)</p>\n";
echo "<p style='margin: 0; font-size: 18px; font-weight: bold;'>2. OR open Incognito/Private mode</p>\n";
echo "<p style='margin: 0; font-size: 18px; font-weight: bold;'>3. Then check the shop again</p>\n";
echo "</div>\n";
echo "</div>\n";

echo "<h3>🛒 <a href='http://localhost:8080/tienda/?force_no_cache=" . time() . "' target='_blank'>CHECK SHOP WITH CACHE-BUSTING</a></h3>\n";
echo "<p><em>Cache clearing completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>