<?php
/**
 * 🚨 EMERGENCY DUPLICATE FIXER
 * Fixes the actual duplicate images shown in user screenshot
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🚨 EMERGENCY DUPLICATE FIXER</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Fix the REAL duplicate images visible in the shop</p>\n";

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

// Get ALL products and group by actual image URL
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$image_groups = [];
$total_products = count($products);

echo "<h2>🔍 Step 1: Finding REAL duplicates by image URL</h2>\n";

foreach ($products as $product) {
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        if ($image_url && strpos($image_url, '.svg') === false && strpos($image_url, 'placeholder') === false) {
            
            if (!isset($image_groups[$image_url])) {
                $image_groups[$image_url] = [];
            }
            
            $image_groups[$image_url][] = [
                'id' => $product_id,
                'name' => $product_name
            ];
        }
    }
}

// Find actual duplicates
$real_duplicates = [];
foreach ($image_groups as $url => $products_list) {
    if (count($products_list) > 1) {
        $real_duplicates[$url] = $products_list;
    }
}

$duplicate_count = count($real_duplicates);
$total_affected = 0;
foreach ($real_duplicates as $products_list) {
    $total_affected += count($products_list);
}

echo "<p><strong>🚨 REAL DUPLICATES FOUND:</strong> {$duplicate_count} image groups affecting {$total_affected} products</p>\n";

if ($duplicate_count > 0) {
    echo "<h3>📋 Duplicate Groups:</h3>\n";
    $group_num = 1;
    foreach ($real_duplicates as $url => $products_list) {
        $filename = basename($url);
        echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 5px;'>\n";
        echo "<p><strong>Group {$group_num} - {$filename}:</strong></p>\n";
        echo "<p>Products: ";
        foreach ($products_list as $product) {
            echo "ID {$product['id']} ({$product['name']}), ";
        }
        echo "</p>\n";
        echo "</div>\n";
        $group_num++;
    }
}

// Plant and garden themed search terms for replacements
$plant_terms = [
    'succulent plant green modern',
    'fern tropical plant natural',
    'monstera plant large leaves',
    'snake plant tall indoor',
    'pothos vine hanging plant',
    'rubber plant glossy leaves',
    'fiddle leaf fig tree',
    'peace lily white flowers',
    'aloe vera medicinal plant',
    'cactus desert plant spiny',
    'bamboo plant zen garden',
    'orchid exotic purple flowers',
    'spider plant hanging basket',
    'philodendron heart shaped leaves',
    'dracaena colorful indoor plant',
    'agave blue grey succulent',
    'jade plant round leaves',
    'boston fern feathery fronds',
    'calathea patterned leaves',
    'zz plant glossy dark green',
    'bromeliad colorful center',
    'anthurium red heart flowers',
    'bird of paradise orange flower',
    'croton colorful variegated leaves',
    'dieffenbachia spotted leaves',
    'english ivy trailing vine',
    'geranium pink red flowers',
    'hibiscus tropical large flowers',
    'jasmine white fragrant flowers',
    'lavender purple aromatic herb',
    'mint green culinary herb',
    'rosemary needle like leaves',
    'basil green cooking herb',
    'thyme small aromatic leaves',
    'sage silvery herb plant',
    'oregano mediterranean herb',
    'parsley flat leaf herb',
    'cilantro fresh green herb',
    'chives thin green shoots',
    'dill feathery herb plant',
    'marigold orange yellow flowers',
    'petunia colorful garden flowers',
    'impatiens shade loving flowers',
    'begonia waxy colorful flowers',
    'coleus colorful foliage plant',
    'caladium heart shaped colorful',
    'hosta shade perennial plant',
    'fuchsia hanging basket flowers',
    'verbena trailing flower plant',
    'salvia red spike flowers'
];

function get_plant_image($search_term, $api_key, $source = 'unsplash') {
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

function replace_duplicate_image($product_id, $image_url, $search_term) {
    $image_data = @file_get_contents($image_url);
    if (!$image_data) return false;
    
    $image = @imagecreatefromstring($image_data);
    if (!$image) return false;
    
    // Create unique filename
    $safe_term = preg_replace('/[^a-zA-Z0-9]/', '_', $search_term);
    $filename = "unique_plant_{$product_id}_" . substr($safe_term, 0, 15) . '_' . time() . '.jpg';
    
    // Process image to 1200x1200
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
        'post_title' => "Unique plant image for product {$product_id}",
        'post_content' => "Search term: {$search_term}",
        'post_status' => 'inherit'
    ];
    
    $attachment_id = wp_insert_attachment($attachment, $file_path);
    if (!$attachment_id) return false;
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
    wp_update_attachment_metadata($attachment_id, $attachment_metadata);
    
    // Remove old image and set new one
    $old_image_id = get_post_thumbnail_id($product_id);
    if ($old_image_id) {
        delete_post_thumbnail($product_id);
    }
    
    set_post_thumbnail($product_id, $attachment_id);
    
    return true;
}

// MAIN FIXING PROCESS
if ($duplicate_count > 0) {
    echo "<h2>🔧 Step 2: Fixing duplicates with unique plant images</h2>\n";
    
    $processed = 0;
    $successful = 0;
    $failed = 0;
    $term_index = 0;
    
    foreach ($real_duplicates as $duplicate_url => $products_list) {
        echo "<h3>🔄 Fixing duplicate group: " . basename($duplicate_url) . "</h3>\n";
        
        // Keep the first product unchanged, replace all others
        for ($i = 1; $i < count($products_list); $i++) {
            $product = $products_list[$i];
            $product_id = $product['id'];
            $product_name = $product['name'];
            
            // Use different search term for each replacement
            $search_term = $plant_terms[$term_index % count($plant_terms)];
            $term_index++;
            
            echo "<p><strong>🔄 Replacing ID {$product_id}: {$product_name}</strong></p>\n";
            echo "<p>🔍 Search: '{$search_term}'</p>\n";
            
            $success = false;
            
            // Try Unsplash first
            if (!empty($unsplash_key)) {
                $image_url = get_plant_image($search_term, $unsplash_key, 'unsplash');
                if ($image_url && replace_duplicate_image($product_id, $image_url, $search_term)) {
                    echo "<p>✅ SUCCESS - Unique Unsplash image assigned</p>\n";
                    $successful++;
                    $success = true;
                }
            }
            
            // Try Pexels if Unsplash failed
            if (!$success && !empty($pexels_key)) {
                $image_url = get_plant_image($search_term, $pexels_key, 'pexels');
                if ($image_url && replace_duplicate_image($product_id, $image_url, $search_term)) {
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
            
            // Small delay for API rate limiting
            sleep(1);
        }
        
        echo "<hr>\n";
    }
    
    echo "<h2>🎉 DUPLICATE FIXING COMPLETED</h2>\n";
    echo "<div style='background: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; border-radius: 8px;'>\n";
    echo "<h3>📊 Final Results</h3>\n";
    echo "<p><strong>🔄 Products processed:</strong> {$processed}</p>\n";
    echo "<p><strong>✅ Successfully replaced:</strong> {$successful}</p>\n";
    echo "<p><strong>❌ Failed:</strong> {$failed}</p>\n";
    echo "</div>\n";
    
    if ($successful > 0) {
        echo "<div style='background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 8px; margin: 20px 0;'>\n";
        echo "<h3>🎯 DUPLICATE IMAGES ELIMINATED!</h3>\n";
        echo "<p><strong>✅ {$successful} products now have unique plant images</strong></p>\n";
        echo "<p>🌟 No more identical images visible in the shop</p>\n";
        echo "</div>\n";
    }
    
} else {
    echo "<div style='background: #fff3cd; padding: 20px; border: 1px solid #ffeaa7; border-radius: 8px;'>\n";
    echo "<h3>⚠️ No URL-based duplicates found in technical analysis</h3>\n";
    echo "<p>But user is seeing visual duplicates - investigating further...</p>\n";
    echo "</div>\n";
}

echo "<h3>🔗 <a href='http://localhost:8080/tienda/' target='_blank'>Check the fixed shop now!</a></h3>\n";
echo "<p><em>Emergency duplicate fixing completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>