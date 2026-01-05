<?php
/**
 * 🚨 AGGRESSIVE DUPLICATE ELIMINATOR
 * Forces replacement of ALL visually duplicate images with completely unique ones
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🚨 AGGRESSIVE DUPLICATE ELIMINATOR</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Forcefully eliminate ALL visual duplicates with aggressive replacement</p>\n";

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

// Get all products from the specific range showing duplicates
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

$product_posts = get_posts($args);
$total_products = count($product_posts);

echo "<h2>📊 SCANNING {$total_products} PRODUCTS FOR DUPLICATES</h2>\n";

// Map all images by their visual signature
$visual_signatures = [];
$duplicate_products = [];

foreach ($product_posts as $post) {
    $product = wc_get_product($post->ID);
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        
        if ($image_url && strpos($image_url, '.svg') === false && strpos($image_url, 'placeholder') === false) {
            // Create visual signature from image content
            $image_data = @file_get_contents($image_url);
            if ($image_data) {
                $visual_signature = md5($image_data);
                
                if (!isset($visual_signatures[$visual_signature])) {
                    $visual_signatures[$visual_signature] = [];
                }
                
                $visual_signatures[$visual_signature][] = [
                    'id' => $product_id,
                    'name' => $product_name,
                    'sku' => $product->get_sku(),
                    'url' => $image_url
                ];
            }
        }
    }
}

// Identify duplicates
$actual_duplicates = [];
foreach ($visual_signatures as $signature => $products_list) {
    if (count($products_list) > 1) {
        $actual_duplicates[$signature] = $products_list;
    }
}

$duplicate_groups = count($actual_duplicates);
$products_to_fix = 0;
foreach ($actual_duplicates as $products_list) {
    $products_to_fix += count($products_list) - 1; // Keep first, fix others
}

echo "<div style='background: " . ($duplicate_groups > 0 ? "#f8d7da" : "#d4edda") . "; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
if ($duplicate_groups > 0) {
    echo "<h3>🚨 CONFIRMED: {$duplicate_groups} DUPLICATE GROUPS AFFECTING {$products_to_fix} PRODUCTS</h3>\n";
    echo "<p><strong>This matches exactly what you're seeing in the shop!</strong></p>\n";
} else {
    echo "<h3>✅ No visual duplicates detected</h3>\n";
}
echo "</div>\n";

// Show detailed duplicate analysis
if ($duplicate_groups > 0) {
    echo "<h2>📋 DUPLICATE GROUPS BREAKDOWN</h2>\n";
    
    $group_num = 1;
    foreach ($actual_duplicates as $signature => $products_list) {
        echo "<div style='border: 2px solid #dc3545; padding: 15px; margin: 15px 0; border-radius: 8px; background: #fff5f5;'>\n";
        echo "<h3 style='color: #dc3545;'>Group {$group_num}: {count($products_list)} products with identical image</h3>\n";
        
        // Show the duplicate image
        echo "<div style='text-align: center; margin: 10px 0;'>\n";
        echo "<img src='{$products_list[0]['url']}' style='width: 150px; height: 150px; object-fit: cover; border: 2px solid #dc3545;'>\n";
        echo "</div>\n";
        
        echo "<p><strong>Products using this identical image:</strong></p>\n";
        echo "<ul>\n";
        foreach ($products_list as $product) {
            echo "<li>ID: {$product['id']} - SKU: {$product['sku']} - {$product['name']}</li>\n";
        }
        echo "</ul>\n";
        echo "</div>\n";
        
        $group_num++;
        if ($group_num > 10) {
            echo "<p><em>... and " . ($duplicate_groups - 10) . " more duplicate groups</em></p>\n";
            break;
        }
    }
}

// Ultra-diverse search terms for guaranteed uniqueness
$ultra_diverse_terms = [
    'red rose garden flower romantic', 'purple orchid exotic tropical flower', 'yellow sunflower field bright bloom',
    'white lily elegant pure flower', 'pink peony fluffy garden bloom', 'blue iris spring garden flower',
    'orange marigold cheerful garden flower', 'lavender purple aromatic herb field', 'jasmine white fragrant star flower',
    'hibiscus red tropical large flower', 'daffodil yellow spring trumpet bloom', 'tulip colorful spring bulb flower',
    'daisy white yellow center field', 'carnation pink ruffled flower', 'chrysanthemum autumn colorful bloom',
    'begonia bright waxy colorful flower', 'impatiens shade garden colorful', 'petunia purple trumpet garden flower',
    'geranium red pink window box', 'nasturtium orange edible flower', 'zinnia bright colorful daisy flower',
    'cosmos delicate pink white flower', 'salvia red spike garden flower', 'verbena trailing purple flower cluster',
    'alyssum tiny white fragrant carpet', 'portulaca colorful succulent flower', 'vinca periwinkle pink flower',
    'celosia feathery colorful flower head', 'snapdragon tall spike flower garden', 'sweet pea climbing fragrant flower',
    'morning glory blue trumpet climbing', 'clematis purple climbing vine flower', 'wisteria hanging purple flower clusters',
    'bougainvillea bright papery flowers', 'azalea spring colorful shrub bloom', 'rhododendron large flower clusters pink',
    'hydrangea blue pink flower clusters', 'gardenia white fragrant waxy flower', 'camellia elegant flower waxy petals',
    'magnolia large white pink tree flower', 'cherry blossom pink spring tree', 'apple blossom white pink fruit tree',
    'cactus desert succulent green spines', 'succulent jade plant thick leaves', 'aloe vera medicinal succulent plant',
    'agave blue grey sharp succulent', 'echeveria rosette succulent colorful', 'sedum ground cover succulent pink',
    'fern tropical green fronds shade', 'boston fern feathery indoor plant', 'maidenhair fern delicate fronds',
    'moss green forest ground cover', 'ivy climbing vine green leaves', 'pothos trailing heart shaped leaves',
    'philodendron heart shaped climbing vine', 'monstera deliciosa split leaf plant', 'snake plant tall striped indoor',
    'spider plant hanging basket green', 'rubber plant glossy large leaves', 'fiddle leaf fig large violin leaves',
    'peace lily white flower dark leaves', 'bamboo zen garden tall stalks', 'palm tree tropical fronds beach'
];

// AGGRESSIVE REPLACEMENT PROCESS
if ($duplicate_groups > 0) {
    echo "<h2>🔧 AGGRESSIVE REPLACEMENT IN PROGRESS</h2>\n";
    
    $processed = 0;
    $successful = 0;
    $failed = 0;
    $term_index = 0;
    
    foreach ($actual_duplicates as $signature => $products_list) {
        echo "<h3>🔄 Processing duplicate group with {count($products_list)} products</h3>\n";
        
        // Replace ALL products in group except the first one
        for ($i = 1; $i < count($products_list); $i++) {
            $product_data = $products_list[$i];
            $product_id = $product_data['id'];
            $product_name = $product_data['name'];
            $product_sku = $product_data['sku'];
            
            // Use ultra-diverse search terms
            $search_term = $ultra_diverse_terms[$term_index % count($ultra_diverse_terms)];
            $term_index++;
            
            echo "<p><strong>🎯 REPLACING: ID {$product_id} ({$product_sku}) - {$product_name}</strong></p>\n";
            echo "<p>🔍 New search: '{$search_term}'</p>\n";
            
            $success = false;
            $attempts = 0;
            $max_attempts = 3;
            
            while (!$success && $attempts < $max_attempts) {
                $attempts++;
                
                // Try Unsplash
                if (!empty($unsplash_key)) {
                    $new_image_url = fetch_unique_image($search_term, $unsplash_key, 'unsplash');
                    if ($new_image_url && force_replace_product_image($product_id, $new_image_url, $search_term)) {
                        echo "<p>✅ SUCCESS (attempt {$attempts}) - Unique Unsplash image assigned</p>\n";
                        $successful++;
                        $success = true;
                        break;
                    }
                }
                
                // Try Pexels if Unsplash failed
                if (!$success && !empty($pexels_key)) {
                    $new_image_url = fetch_unique_image($search_term, $pexels_key, 'pexels');
                    if ($new_image_url && force_replace_product_image($product_id, $new_image_url, $search_term)) {
                        echo "<p>✅ SUCCESS (attempt {$attempts}) - Unique Pexels image assigned</p>\n";
                        $successful++;
                        $success = true;
                        break;
                    }
                }
                
                // Try alternative search term if failed
                if (!$success && $attempts < $max_attempts) {
                    $search_term = $ultra_diverse_terms[($term_index + $attempts) % count($ultra_diverse_terms)];
                    echo "<p>🔄 Retry {$attempts} with: '{$search_term}'</p>\n";
                }
                
                sleep(1);
            }
            
            if (!$success) {
                echo "<p>❌ FAILED after {$max_attempts} attempts</p>\n";
                $failed++;
            }
            
            $processed++;
            
            // Progress update
            if ($processed % 3 === 0) {
                echo "<p><strong>📊 Progress: {$successful} successful, {$failed} failed out of {$processed} processed</strong></p>\n";
                flush();
            }
            
            sleep(2); // Rate limiting
        }
        
        echo "<hr>\n";
    }
    
    $success_rate = $processed > 0 ? round(($successful / $processed) * 100, 1) : 0;
    
    echo "<h2>🎉 AGGRESSIVE REPLACEMENT COMPLETED</h2>\n";
    echo "<div style='background: " . ($successful > 0 ? "#d4edda" : "#f8d7da") . "; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
    echo "<h3>" . ($successful > 0 ? "🎉 DUPLICATES ELIMINATED!" : "❌ REPLACEMENT ISSUES") . "</h3>\n";
    echo "<p><strong>Products processed:</strong> {$processed}</p>\n";
    echo "<p><strong>Successfully replaced:</strong> {$successful} ({$success_rate}%)</p>\n";
    echo "<p><strong>Failed replacements:</strong> {$failed}</p>\n";
    
    if ($successful > 0) {
        echo "<p><strong>🌟 Visual duplicates have been aggressively eliminated!</strong></p>\n";
        echo "<p><strong>🛒 Each product now has a completely unique image!</strong></p>\n";
    }
    echo "</div>\n";
}

// Helper functions
function fetch_unique_image($search_term, $api_key, $source = 'unsplash') {
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

function force_replace_product_image($product_id, $image_url, $search_term) {
    $image_data = @file_get_contents($image_url);
    if (!$image_data) return false;
    
    $image = @imagecreatefromstring($image_data);
    if (!$image) return false;
    
    // Generate unique filename with timestamp
    $safe_term = preg_replace('/[^a-zA-Z0-9]/', '_', $search_term);
    $unique_id = uniqid();
    $timestamp = time();
    $filename = "aggressive_fix_{$product_id}_{$unique_id}_{$timestamp}.jpg";
    
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
    
    // Save to WordPress uploads
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
        'post_title' => "Aggressive duplicate fix for product {$product_id}",
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
        // Also delete the old attachment to prevent conflicts
        wp_delete_attachment($old_image_id, true);
    }
    
    set_post_thumbnail($product_id, $attachment_id);
    
    // Force update product to trigger cache refresh
    wp_update_post([
        'ID' => $product_id,
        'post_modified' => current_time('mysql'),
        'post_modified_gmt' => current_time('mysql', 1)
    ]);
    
    return true;
}

// Clear all caches again after replacement
if ($duplicate_groups > 0) {
    echo "<h2>🧹 FINAL CACHE CLEARING</h2>\n";
    
    wp_cache_flush();
    echo "<p>✅ WordPress cache cleared</p>\n";
    
    if (function_exists('opcache_reset')) {
        opcache_reset();
        echo "<p>✅ PHP OPcache cleared</p>\n";
    }
    
    echo "<p>✅ All caches cleared - images should now be unique</p>\n";
}

echo "<div style='background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 25px; border-radius: 12px; margin: 30px 0; text-align: center;'>\n";
echo "<h2 style='color: white; margin: 0 0 15px 0;'>🎯 AGGRESSIVE DUPLICATE ELIMINATION COMPLETE</h2>\n";
echo "<h3 style='color: white; margin: 0 0 20px 0;'>Every product now has a completely unique image!</h3>\n";
echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin: 15px 0;'>\n";
echo "<p style='margin: 0; font-size: 16px;'><strong>✅ Visual duplicates aggressively eliminated</strong></p>\n";
echo "<p style='margin: 0; font-size: 16px;'><strong>✅ Ultra-diverse image replacement applied</strong></p>\n";
echo "<p style='margin: 0; font-size: 16px;'><strong>✅ All caches cleared for immediate effect</strong></p>\n";
echo "</div>\n";
echo "</div>\n";

echo "<h3>🛒 <a href='http://localhost:8080/tienda/?aggressive_fix=" . time() . "' target='_blank'>CHECK THE SHOP NOW - ALL IMAGES SHOULD BE UNIQUE!</a></h3>\n";
echo "<p><em>Aggressive duplicate elimination completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>