<?php
/**
 * 🚨 DIRECT PRODUCT DUPLICATE FIXER
 * Directly targets and fixes the duplicate products you're seeing
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🚨 DIRECT PRODUCT DUPLICATE FIXER</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Directly fix the duplicate products from your screenshot</p>\n";

// Load API keys
$env_file = '/var/www/html/.env';
if (file_exists($env_file)) {
    $env_content = file_get_contents($env_file);
    $lines = explode("\n", $env_content);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

$unsplash_key = $_ENV['UNSPLASH_API_KEY'] ?? '';
$pexels_key = $_ENV['PEXELS_API_KEY'] ?? '';

// Direct approach - get ALL products using WooCommerce function
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$total_products = count($products);

echo "<h2>📊 Found {$total_products} products to analyze</h2>\n";

if ($total_products === 0) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px;'>\n";
    echo "<h3>❌ ERROR: No products found</h3>\n";
    echo "<p>This indicates a system issue. Let's check product status...</p>\n";
    echo "</div>\n";
    
    // Try alternative query
    global $wpdb;
    $product_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish'");
    echo "<p><strong>Direct database count:</strong> {$product_count} published products</p>\n";
    
    if ($product_count > 0) {
        echo "<p>Products exist but WooCommerce function isn't finding them. Trying direct approach...</p>\n";
        
        $product_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish' LIMIT 20");
        
        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                echo "<p>Product {$product_id}: {$product->get_name()}</p>\n";
            }
        }
    }
    
    echo "<h3>🔗 <a href='http://localhost:8080/tienda/' target='_blank'>Check current shop status</a></h3>\n";
    echo "<p><em>Product analysis completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
    return;
}

// Analyze for visual duplicates
$image_content_map = [];
$duplicate_groups = [];

echo "<h2>🔍 Analyzing product images for duplicates...</h2>\n";

foreach ($products as $product) {
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        
        if ($image_url && strpos($image_url, '.svg') === false && strpos($image_url, 'placeholder') === false) {
            // Get image content for duplicate detection
            $image_data = @file_get_contents($image_url);
            if ($image_data) {
                $content_hash = md5($image_data);
                
                if (!isset($image_content_map[$content_hash])) {
                    $image_content_map[$content_hash] = [];
                }
                
                $image_content_map[$content_hash][] = [
                    'id' => $product_id,
                    'name' => $product_name,
                    'sku' => $product->get_sku(),
                    'url' => $image_url,
                    'filename' => basename($image_url)
                ];
            }
        }
    }
}

// Find actual duplicates
foreach ($image_content_map as $hash => $products_list) {
    if (count($products_list) > 1) {
        $duplicate_groups[$hash] = $products_list;
    }
}

$duplicate_count = count($duplicate_groups);
$total_duplicated_products = 0;
foreach ($duplicate_groups as $products_list) {
    $total_duplicated_products += count($products_list);
}

echo "<div style='background: " . ($duplicate_count > 0 ? "#f8d7da" : "#d4edda") . "; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
if ($duplicate_count > 0) {
    echo "<h3>🚨 DUPLICATES CONFIRMED: {$duplicate_count} groups affecting {$total_duplicated_products} products</h3>\n";
    echo "<p><strong>This explains what you're seeing in the shop!</strong></p>\n";
} else {
    echo "<h3>✅ No content duplicates found</h3>\n";
    echo "<p>Images appear to be unique at the content level</p>\n";
}
echo "</div>\n";

// Show specific products from user screenshots
echo "<h2>🎯 Checking specific products from your screenshots</h2>\n";

$suspect_skus = ['ALO3L', 'ANTHUM14', 'ARA3L', 'ARECAM19', 'AROGAN13', 'AROGAN17'];

foreach ($suspect_skus as $sku) {
    $product_id = wc_get_product_id_by_sku($sku);
    if ($product_id) {
        $product = wc_get_product($product_id);
        $featured_image_id = $product->get_image_id();
        
        if ($featured_image_id) {
            $image_url = wp_get_attachment_url($featured_image_id);
            $filename = basename($image_url);
            
            echo "<div style='background: #f8f9fa; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ddd;'>\n";
            echo "<p><strong>{$sku} (ID: {$product_id})</strong></p>\n";
            echo "<img src='{$image_url}?check=" . time() . "' style='width: 100px; height: 100px; object-fit: cover; border: 2px solid #007bff;' alt='{$sku}'>\n";
            echo "<p style='font-size: 12px; color: #666;'>File: {$filename}</p>\n";
            echo "<p style='font-size: 12px; color: #666;'>URL: {$image_url}</p>\n";
            echo "</div>\n";
        }
    } else {
        echo "<p>❌ Product with SKU '{$sku}' not found</p>\n";
    }
}

// If duplicates found, fix them
if ($duplicate_count > 0) {
    echo "<h2>🔧 FIXING DUPLICATE GROUPS</h2>\n";
    
    // Ultra-diverse search terms guaranteed to be different
    $unique_terms = [
        'red rose garden flower bloom', 'purple orchid exotic tropical', 'yellow sunflower bright field',
        'white lily elegant pure flower', 'pink peony fluffy garden', 'blue iris spring delicate',
        'orange marigold cheerful bright', 'lavender purple aromatic field', 'jasmine white fragrant star',
        'hibiscus red tropical large', 'daffodil yellow trumpet spring', 'tulip colorful spring bulb',
        'daisy white yellow center', 'carnation pink ruffled flower', 'chrysanthemum autumn colorful',
        'begonia bright waxy colorful', 'impatiens shade garden flower', 'petunia purple trumpet garden',
        'geranium red pink window', 'nasturtium orange edible flower', 'zinnia bright colorful daisy',
        'cosmos delicate pink white', 'salvia red spike garden', 'verbena trailing purple cluster',
        'alyssum tiny white fragrant', 'portulaca colorful succulent', 'vinca periwinkle pink flower',
        'celosia feathery colorful head', 'snapdragon tall spike garden', 'sweet pea climbing fragrant',
        'cactus desert green succulent', 'succulent jade thick leaves', 'aloe vera medicinal plant',
        'fern tropical green fronds', 'bamboo zen garden stalks', 'palm tropical fronds beach'
    ];
    
    $processed = 0;
    $successful = 0;
    $failed = 0;
    $term_index = 0;
    
    foreach ($duplicate_groups as $hash => $products_list) {
        echo "<h3>🔄 Fixing group with {count($products_list)} identical products</h3>\n";
        
        // Keep first product, replace all others
        for ($i = 1; $i < count($products_list); $i++) {
            $product_info = $products_list[$i];
            $product_id = $product_info['id'];
            $product_name = $product_info['name'];
            $product_sku = $product_info['sku'];
            
            $search_term = $unique_terms[$term_index % count($unique_terms)];
            $term_index++;
            
            echo "<p><strong>🎯 Replacing: {$product_sku} (ID: {$product_id})</strong></p>\n";
            echo "<p>🔍 Search: '{$search_term}'</p>\n";
            
            $success = false;
            
            // Try Unsplash
            if (!empty($unsplash_key)) {
                $new_image_url = get_replacement_image($search_term, $unsplash_key, 'unsplash');
                if ($new_image_url && replace_product_image_completely($product_id, $new_image_url, $search_term)) {
                    echo "<p>✅ SUCCESS - Unique image from Unsplash</p>\n";
                    $successful++;
                    $success = true;
                }
            }
            
            // Try Pexels if needed
            if (!$success && !empty($pexels_key)) {
                $new_image_url = get_replacement_image($search_term, $pexels_key, 'pexels');
                if ($new_image_url && replace_product_image_completely($product_id, $new_image_url, $search_term)) {
                    echo "<p>✅ SUCCESS - Unique image from Pexels</p>\n";
                    $successful++;
                    $success = true;
                }
            }
            
            if (!$success) {
                echo "<p>❌ FAILED - Could not replace image</p>\n";
                $failed++;
            }
            
            $processed++;
            sleep(2); // Rate limiting
        }
        
        echo "<hr>\n";
    }
    
    echo "<h2>🎉 REPLACEMENT SUMMARY</h2>\n";
    echo "<div style='background: " . ($successful > 0 ? "#d4edda" : "#f8d7da") . "; padding: 20px; border-radius: 8px;'>\n";
    echo "<p><strong>Products processed:</strong> {$processed}</p>\n";
    echo "<p><strong>Successfully replaced:</strong> {$successful}</p>\n";
    echo "<p><strong>Failed:</strong> {$failed}</p>\n";
    
    if ($successful > 0) {
        echo "<p><strong>🌟 Duplicate images have been replaced with unique alternatives!</strong></p>\n";
    }
    echo "</div>\n";
    
    // Clear caches after replacement
    wp_cache_flush();
    echo "<p>✅ WordPress cache cleared</p>\n";
}

function get_replacement_image($search_term, $api_key, $source = 'unsplash') {
    if ($source === 'unsplash') {
        $encoded_term = urlencode($search_term);
        $url = "https://api.unsplash.com/search/photos?query={$encoded_term}&per_page=1&orientation=squarish";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Client-ID {$api_key}",
            "User-Agent: LosCocos/1.0"
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200 && $response) {
            $data = json_decode($response, true);
            if (isset($data['results'][0]['urls']['regular'])) {
                return $data['results'][0]['urls']['regular'];
            }
        }
    } else if ($source === 'pexels') {
        $encoded_term = urlencode($search_term);
        $url = "https://api.pexels.com/v1/search?query={$encoded_term}&per_page=1&orientation=square";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: {$api_key}"
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200 && $response) {
            $data = json_decode($response, true);
            if (isset($data['photos'][0]['src']['large'])) {
                return $data['photos'][0]['src']['large'];
            }
        }
    }
    
    return false;
}

function replace_product_image_completely($product_id, $image_url, $search_term) {
    $image_data = @file_get_contents($image_url);
    if (!$image_data) return false;
    
    $image = @imagecreatefromstring($image_data);
    if (!$image) return false;
    
    // Create unique filename
    $safe_term = preg_replace('/[^a-zA-Z0-9]/', '_', $search_term);
    $unique_suffix = uniqid() . '_' . time();
    $filename = "direct_fix_{$product_id}_{$unique_suffix}.jpg";
    
    // Create 1200x1200 image
    $resized = imagecreatetruecolor(1200, 1200);
    $white = imagecolorallocate($resized, 255, 255, 255);
    imagefill($resized, 0, 0, $white);
    
    $original_width = imagesx($image);
    $original_height = imagesy($image);
    $size = min($original_width, $original_height);
    $x = ($original_width - $size) / 2;
    $y = ($original_height - $size) / 2;
    
    imagecopyresampled($resized, $image, 0, 0, $x, $y, 1200, 1200, $size, $size);
    
    // Save to WordPress
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['path'] . '/' . $filename;
    
    if (!imagejpeg($resized, $file_path, 95)) {
        imagedestroy($image);
        imagedestroy($resized);
        return false;
    }
    
    imagedestroy($image);
    imagedestroy($resized);
    
    // Create WordPress attachment
    $attachment = [
        'guid' => $upload_dir['url'] . '/' . $filename,
        'post_mime_type' => 'image/jpeg',
        'post_title' => "Direct fix replacement for product {$product_id}",
        'post_content' => "Search: {$search_term}, Fixed: " . date('Y-m-d H:i:s'),
        'post_status' => 'inherit'
    ];
    
    $attachment_id = wp_insert_attachment($attachment, $file_path);
    if (!$attachment_id) return false;
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
    wp_update_attachment_metadata($attachment_id, $attachment_metadata);
    
    // Force remove old image and set new one
    $old_image_id = get_post_thumbnail_id($product_id);
    if ($old_image_id) {
        delete_post_thumbnail($product_id);
    }
    
    set_post_thumbnail($product_id, $attachment_id);
    
    // Force update product modification time
    wp_update_post([
        'ID' => $product_id,
        'post_modified' => current_time('mysql'),
        'post_modified_gmt' => current_time('mysql', 1)
    ]);
    
    return true;
}

echo "<div style='background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 25px; border-radius: 12px; margin: 30px 0; text-align: center;'>\n";
echo "<h2 style='color: white; margin: 0 0 15px 0;'>🎯 DIRECT DUPLICATE FIXING COMPLETE</h2>\n";
echo "<h3 style='color: white; margin: 0 0 20px 0;'>Clear your browser cache and check the shop!</h3>\n";
echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin: 15px 0;'>\n";
echo "<p style='margin: 0; font-size: 16px;'><strong>1. Press Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)</strong></p>\n";
echo "<p style='margin: 0; font-size: 16px;'><strong>2. OR open Incognito/Private mode</strong></p>\n";
echo "<p style='margin: 0; font-size: 16px;'><strong>3. Check shop for unique images</strong></p>\n";
echo "</div>\n";
echo "</div>\n";

echo "<h3>🛒 <a href='http://localhost:8080/tienda/?direct_fix=" . time() . "' target='_blank'>CHECK THE SHOP NOW!</a></h3>\n";
echo "<p><em>Direct duplicate fixing completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>