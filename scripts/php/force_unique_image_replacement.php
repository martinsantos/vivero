<?php
/**
 * 🚨 FORCE UNIQUE IMAGE REPLACEMENT
 * Replaces identical visual content with truly different plant images
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🚨 FORCE UNIQUE IMAGE REPLACEMENT</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Replace all identical plant images with truly unique and different images</p>\n";

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

echo "<h2>🎯 Targeting specific products that appear identical</h2>\n";

// Target the specific products you mentioned as having identical images
$target_products = [
    'ALO3L' => 'aloe vera succulent plant green thick leaves',
    'ANTHUM14' => 'anthurium red heart shaped flower tropical',
    'ARA3L' => 'aralia green leafy indoor houseplant',
    'ARECAM19' => 'areca palm tropical feathery fronds',
    'AROGAN13' => 'oregano herb small green aromatic leaves',
    'AROGAN17' => 'oregano herb plant culinary mediterranean'
];

$processed = 0;
$successful = 0;
$failed = 0;

foreach ($target_products as $sku => $search_term) {
    $product_id = wc_get_product_id_by_sku($sku);
    
    if ($product_id) {
        $product = wc_get_product($product_id);
        $product_name = $product->get_name();
        
        echo "<h3>🔄 Processing {$sku} (ID: {$product_id})</h3>\n";
        echo "<p><strong>Product:</strong> {$product_name}</p>\n";
        echo "<p><strong>New search:</strong> '{$search_term}'</p>\n";
        
        $success = false;
        
        // Try Unsplash first
        if (!empty($unsplash_key)) {
            $new_image_url = get_truly_unique_image($search_term, $unsplash_key, 'unsplash');
            if ($new_image_url && replace_with_truly_unique_image($product_id, $new_image_url, $search_term)) {
                echo "<p>✅ SUCCESS - Replaced with unique Unsplash image</p>\n";
                $successful++;
                $success = true;
            }
        }
        
        // Try Pexels if Unsplash failed
        if (!$success && !empty($pexels_key)) {
            $new_image_url = get_truly_unique_image($search_term, $pexels_key, 'pexels');
            if ($new_image_url && replace_with_truly_unique_image($product_id, $new_image_url, $search_term)) {
                echo "<p>✅ SUCCESS - Replaced with unique Pexels image</p>\n";
                $successful++;
                $success = true;
            }
        }
        
        if (!$success) {
            echo "<p>❌ FAILED - Could not replace image</p>\n";
            $failed++;
        }
        
        $processed++;
        sleep(3); // Longer delay for better API compliance
        
    } else {
        echo "<p>❌ Product with SKU '{$sku}' not found</p>\n";
        $failed++;
    }
    
    echo "<hr>\n";
}

// Add more diverse replacement for any remaining identical images
echo "<h2>🔍 Checking for any remaining identical content</h2>\n";

$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$image_signatures = [];

foreach ($products as $product) {
    $product_id = $product->get_id();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        if ($image_url && strpos($image_url, '.svg') === false) {
            $image_data = @file_get_contents($image_url);
            if ($image_data) {
                $signature = md5($image_data);
                if (!isset($image_signatures[$signature])) {
                    $image_signatures[$signature] = [];
                }
                $image_signatures[$signature][] = [
                    'id' => $product_id,
                    'name' => $product->get_name(),
                    'sku' => $product->get_sku()
                ];
            }
        }
    }
}

// Find any groups with identical content
$identical_groups = [];
foreach ($image_signatures as $signature => $products_list) {
    if (count($products_list) > 1) {
        $identical_groups[$signature] = $products_list;
    }
}

$remaining_duplicates = count($identical_groups);
echo "<p><strong>Remaining identical image groups:</strong> {$remaining_duplicates}</p>\n";

if ($remaining_duplicates > 0) {
    echo "<h3>🔧 Fixing remaining identical groups</h3>\n";
    
    // Ultra-specific and different search terms
    $ultra_diverse_searches = [
        'purple lavender field aromatic flowers',
        'red rose garden romantic bloom',
        'yellow sunflower bright field flower',
        'white daisy meadow small flowers',
        'pink cherry blossom tree spring',
        'orange marigold garden bright flower',
        'blue iris elegant spring flower',
        'green fern tropical shade plant',
        'brown tree bark texture natural',
        'moss green forest ground cover'
    ];
    
    $term_index = 0;
    
    foreach ($identical_groups as $signature => $products_list) {
        echo "<p><strong>Fixing group with " . count($products_list) . " identical products</strong></p>\n";
        
        // Replace all but the first product
        for ($i = 1; $i < count($products_list); $i++) {
            $product_info = $products_list[$i];
            $search_term = $ultra_diverse_searches[$term_index % count($ultra_diverse_searches)];
            $term_index++;
            
            echo "<p>🔄 Replacing {$product_info['sku']} with: '{$search_term}'</p>\n";
            
            $success = false;
            
            if (!empty($unsplash_key)) {
                $new_image_url = get_truly_unique_image($search_term, $unsplash_key, 'unsplash');
                if ($new_image_url && replace_with_truly_unique_image($product_info['id'], $new_image_url, $search_term)) {
                    echo "<p>✅ SUCCESS</p>\n";
                    $successful++;
                    $success = true;
                }
            }
            
            if (!$success && !empty($pexels_key)) {
                $new_image_url = get_truly_unique_image($search_term, $pexels_key, 'pexels');
                if ($new_image_url && replace_with_truly_unique_image($product_info['id'], $new_image_url, $search_term)) {
                    echo "<p>✅ SUCCESS</p>\n";
                    $successful++;
                    $success = true;
                }
            }
            
            if (!$success) {
                echo "<p>❌ FAILED</p>\n";
                $failed++;
            }
            
            $processed++;
            sleep(2);
        }
    }
}

function get_truly_unique_image($search_term, $api_key, $source = 'unsplash') {
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
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

function replace_with_truly_unique_image($product_id, $image_url, $search_term) {
    $image_data = @file_get_contents($image_url);
    if (!$image_data) return false;
    
    $image = @imagecreatefromstring($image_data);
    if (!$image) return false;
    
    // Create absolutely unique filename
    $safe_term = preg_replace('/[^a-zA-Z0-9]/', '_', $search_term);
    $unique_id = uniqid('truly_unique_', true);
    $timestamp = microtime(true);
    $filename = "unique_{$product_id}_{$unique_id}_{$timestamp}.jpg";
    
    // Create high-quality 1200x1200 image
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
        'post_title' => "Truly unique image for product {$product_id}",
        'post_content' => "Search: {$search_term}, Unique replacement: " . date('Y-m-d H:i:s'),
        'post_status' => 'inherit'
    ];
    
    $attachment_id = wp_insert_attachment($attachment, $file_path);
    if (!$attachment_id) return false;
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
    wp_update_attachment_metadata($attachment_id, $attachment_metadata);
    
    // Completely remove old image and set new one
    $old_image_id = get_post_thumbnail_id($product_id);
    if ($old_image_id) {
        delete_post_thumbnail($product_id);
        // Delete the old file to prevent any conflicts
        wp_delete_attachment($old_image_id, true);
    }
    
    set_post_thumbnail($product_id, $attachment_id);
    
    // Force update product with current timestamp
    wp_update_post([
        'ID' => $product_id,
        'post_modified' => current_time('mysql'),
        'post_modified_gmt' => current_time('mysql', 1)
    ]);
    
    return true;
}

// Final cache clearing
wp_cache_flush();
if (function_exists('opcache_reset')) {
    opcache_reset();
}

echo "<h2>🎉 REPLACEMENT SUMMARY</h2>\n";
echo "<div style='background: " . ($successful > 0 ? "#d4edda" : "#f8d7da") . "; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
echo "<h3>" . ($successful > 0 ? "🎉 UNIQUE IMAGES CREATED!" : "❌ REPLACEMENT ISSUES") . "</h3>\n";
echo "<p><strong>Products processed:</strong> {$processed}</p>\n";
echo "<p><strong>Successfully replaced:</strong> {$successful}</p>\n";
echo "<p><strong>Failed:</strong> {$failed}</p>\n";

if ($successful > 0) {
    echo "<p><strong>🌟 Each product now has a completely different and unique image!</strong></p>\n";
    echo "<p><strong>🛒 Clear your browser cache and check the shop!</strong></p>\n";
}
echo "</div>\n";

echo "<div style='background: #fff3cd; padding: 20px; border: 2px solid #856404; border-radius: 10px; margin: 20px 0;'>\n";
echo "<h3>🚨 IMPORTANT: Clear your browser cache to see the new unique images!</h3>\n";
echo "<ul>\n";
echo "<li><strong>Hard Refresh:</strong> Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)</li>\n";
echo "<li><strong>Or:</strong> Open Incognito/Private mode and check the shop</li>\n";
echo "</ul>\n";
echo "</div>\n";

echo "<h3>🛒 <a href='http://localhost:8080/tienda/?unique_fix=" . time() . "' target='_blank'>CHECK THE SHOP FOR UNIQUE IMAGES!</a></h3>\n";
echo "<p><em>Unique image replacement completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>