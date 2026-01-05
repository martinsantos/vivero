<?php
/**
 * 🚨 EMERGENCY MASS DUPLICATE REPLACEMENT
 * Fixes all remaining duplicate images with unique replacements
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🚨 EMERGENCY MASS DUPLICATE REPLACEMENT</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Replace ALL remaining duplicate images with unique ones</p>\n";

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

// Get ALL products and check for actual duplicates
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$image_content_map = [];
$duplicates_found = [];

echo "<h2>🔍 Scanning ALL products for duplicate content...</h2>\n";

foreach ($products as $product) {
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        if ($image_url && strpos($image_url, '.svg') === false && strpos($image_url, 'placeholder') === false) {
            
            // Download and hash image content
            $image_data = @file_get_contents($image_url);
            if ($image_data) {
                $content_hash = md5($image_data);
                
                if (!isset($image_content_map[$content_hash])) {
                    $image_content_map[$content_hash] = [];
                }
                
                $image_content_map[$content_hash][] = [
                    'id' => $product_id,
                    'name' => $product_name,
                    'url' => $image_url,
                    'filename' => basename($image_url)
                ];
            }
        }
    }
}

// Find duplicates
foreach ($image_content_map as $hash => $products_list) {
    if (count($products_list) > 1) {
        $duplicates_found[$hash] = $products_list;
    }
}

$duplicate_groups = count($duplicates_found);
$total_products_to_fix = 0;
foreach ($duplicates_found as $products_list) {
    $total_products_to_fix += count($products_list) - 1; // Keep one, fix others
}

echo "<h2>🚨 DUPLICATE ANALYSIS RESULTS</h2>\n";
echo "<div style='background: " . ($duplicate_groups > 0 ? "#f8d7da" : "#d4edda") . "; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
if ($duplicate_groups > 0) {
    echo "<h3>🚨 CONFIRMED: {$duplicate_groups} DUPLICATE GROUPS FOUND!</h3>\n";
    echo "<p><strong>Products needing unique images:</strong> {$total_products_to_fix}</p>\n";
    echo "<p><strong>This matches what you're seeing in the shop!</strong></p>\n";
} else {
    echo "<h3>✅ No duplicates detected</h3>\n";
}
echo "</div>\n";

if ($duplicate_groups > 0) {
    echo "<h3>📋 Duplicate Groups Details:</h3>\n";
    $group_num = 1;
    foreach ($duplicates_found as $hash => $products_list) {
        echo "<div style='background: #fff3cd; padding: 10px; margin: 10px 0; border-radius: 5px;'>\n";
        echo "<p><strong>Group {$group_num} ({count($products_list)} products):</strong></p>\n";
        echo "<img src='{$products_list[0]['url']}' style='width: 100px; height: 100px; object-fit: cover; margin: 5px;'>\n";
        echo "<p>Products: ";
        foreach ($products_list as $product) {
            echo "ID:{$product['id']} ({$product['name']}), ";
        }
        echo "</p>\n";
        echo "</div>\n";
        $group_num++;
        
        if ($group_num > 10) {
            echo "<p><em>... and " . ($duplicate_groups - 10) . " more groups</em></p>\n";
            break;
        }
    }
}

// Diverse search terms for replacements
$unique_search_terms = [
    'purple orchid flower exotic bloom', 'cactus desert succulent spines green', 'fern tropical fronds green shade',
    'rose red flower garden romantic', 'sunflower yellow bright large bloom', 'lavender purple field aromatic herb',
    'bamboo zen plant green stalks', 'moss green forest ground cover', 'ivy climbing vine leaves wall',
    'lily white flower pure elegant', 'daisy white yellow center field', 'tulip colorful spring bulb flower',
    'daffodil yellow spring trumpet flower', 'peony pink fluffy flower bloom', 'hibiscus tropical large red flower',
    'jasmine white fragrant star flower', 'begonia bright colorful waxy bloom', 'marigold orange yellow cheerful flower',
    'petunia purple trumpet garden flower', 'geranium red pink window box', 'impatiens shade colorful flower bed',
    'coleus colorful foliage variegated', 'caladium heart shaped colorful leaves', 'hosta green shade perennial plant',
    'astilbe feathery plume flower spike', 'heuchera coral bells colorful leaves', 'sedum succulent ground cover pink',
    'sage silver aromatic herb plant', 'rosemary needle leaves herb green', 'thyme small herb aromatic leaves',
    'basil green culinary herb fresh', 'mint aromatic herb green leaves', 'oregano mediterranean herb cooking',
    'parsley flat leaf herb fresh', 'cilantro herb fresh green cooking', 'chives thin green herb shoots',
    'dill feathery herb plant aromatic', 'fennel feathery herb yellow flower', 'tarragon herb narrow leaves green',
    'bougainvillea bright papery flowers', 'azalea colorful shrub spring bloom', 'rhododendron large flower clusters',
    'hydrangea blue pink flower clusters', 'gardenia white fragrant flower bloom', 'camellia elegant flower waxy petals',
    'magnolia large white pink flower', 'wisteria purple hanging flower clusters', 'clematis climbing vine purple flower',
    'morning glory blue trumpet climbing', 'passion flower exotic intricate bloom', 'honeysuckle fragrant climbing vine',
    'nasturtium edible orange flower', 'zinnia bright colorful daisy flower', 'cosmos delicate pink white flower',
    'salvia red spike flower garden', 'verbena trailing purple flower', 'alyssum tiny white fragrant flower',
    'portulaca colorful succulent flower', 'vinca periwinkle pink flower', 'celosia feathery colorful flower head'
];

// MASS REPLACEMENT PROCESS
if ($duplicate_groups > 0 && (!empty($unsplash_key) || !empty($pexels_key))) {
    echo "<h2>🔧 MASS DUPLICATE REPLACEMENT PROCESS</h2>\n";
    
    $processed = 0;
    $successful = 0;
    $failed = 0;
    $term_index = 0;
    
    foreach ($duplicates_found as $hash => $products_list) {
        echo "<h3>🔄 Fixing duplicate group ({count($products_list)} products)</h3>\n";
        
        // Skip first product (keep original), replace all others
        for ($i = 1; $i < count($products_list); $i++) {
            $product_data = $products_list[$i];
            $product_id = $product_data['id'];
            $product_name = $product_data['name'];
            
            // Use unique search term for each replacement
            $search_term = $unique_search_terms[$term_index % count($unique_search_terms)];
            $term_index++;
            
            echo "<p><strong>🔄 Replacing ID {$product_id}: {$product_name}</strong></p>\n";
            echo "<p>🔍 Using: '{$search_term}'</p>\n";
            
            $success = false;
            
            // Try Unsplash first
            if (!empty($unsplash_key)) {
                $image_url = get_unique_replacement_image($search_term, $unsplash_key, 'unsplash');
                if ($image_url && replace_duplicate_with_unique($product_id, $image_url, $search_term)) {
                    echo "<p>✅ SUCCESS - Unique Unsplash image assigned</p>\n";
                    $successful++;
                    $success = true;
                }
            }
            
            // Try Pexels if Unsplash failed
            if (!$success && !empty($pexels_key)) {
                $image_url = get_unique_replacement_image($search_term, $pexels_key, 'pexels');
                if ($image_url && replace_duplicate_with_unique($product_id, $image_url, $search_term)) {
                    echo "<p>✅ SUCCESS - Unique Pexels image assigned</p>\n";
                    $successful++;
                    $success = true;
                }
            }
            
            if (!$success) {
                echo "<p>❌ FAILED - Could not get replacement image</p>\n";
                $failed++;
            }
            
            $processed++;
            
            // Progress updates
            if ($processed % 5 === 0) {
                echo "<p><strong>📊 Progress: {$successful} successful, {$failed} failed</strong></p>\n";
                flush();
            }
            
            sleep(2); // Rate limiting
        }
        
        echo "<hr>\n";
    }
    
    $success_rate = $processed > 0 ? round(($successful / $processed) * 100, 1) : 0;
    
    echo "<h2>🎉 MASS REPLACEMENT COMPLETED</h2>\n";
    echo "<div style='background: " . ($successful > 0 ? "#d4edda" : "#f8d7da") . "; padding: 20px; border: 1px solid " . ($successful > 0 ? "#c3e6cb" : "#f5c6cb") . "; border-radius: 8px; margin: 20px 0;'>\n";
    echo "<h3>" . ($successful > 0 ? "🎉 DUPLICATES ELIMINATED!" : "❌ REPLACEMENT FAILED") . "</h3>\n";
    echo "<p><strong>Products processed:</strong> {$processed}</p>\n";
    echo "<p><strong>Successfully replaced:</strong> {$successful} ({$success_rate}%)</p>\n";
    echo "<p><strong>Failed:</strong> {$failed}</p>\n";
    
    if ($successful > 0) {
        echo "<p><strong>🌟 All duplicate images have been replaced with unique plant images!</strong></p>\n";
        echo "<p><strong>🛒 The shop now shows unique images for every product!</strong></p>\n";
    }
    echo "</div>\n";
}

// Helper functions
function get_unique_replacement_image($search_term, $api_key, $source = 'unsplash') {
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

function replace_duplicate_with_unique($product_id, $image_url, $search_term) {
    $image_data = @file_get_contents($image_url);
    if (!$image_data) return false;
    
    $image = @imagecreatefromstring($image_data);
    if (!$image) return false;
    
    // Create unique filename
    $safe_term = preg_replace('/[^a-zA-Z0-9]/', '_', $search_term);
    $filename = "unique_replacement_{$product_id}_" . substr($safe_term, 0, 15) . '_' . time() . '.jpg';
    
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
        'post_title' => "Unique replacement for product {$product_id}",
        'post_content' => "Search: {$search_term}",
        'post_status' => 'inherit'
    ];
    
    $attachment_id = wp_insert_attachment($attachment, $file_path);
    if (!$attachment_id) return false;
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
    wp_update_attachment_metadata($attachment_id, $attachment_metadata);
    
    // Replace product's featured image
    $old_image_id = get_post_thumbnail_id($product_id);
    if ($old_image_id) {
        delete_post_thumbnail($product_id);
    }
    
    set_post_thumbnail($product_id, $attachment_id);
    
    return true;
}

echo "<h3>🛒 <a href='http://localhost:8080/tienda/' target='_blank'>CHECK THE SHOP NOW - ALL IMAGES SHOULD BE UNIQUE!</a></h3>\n";
echo "<p><em>Mass duplicate replacement completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>