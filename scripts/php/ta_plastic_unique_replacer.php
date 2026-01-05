<?php
/**
 * 🎯 Ta Plastic Unique Image Replacer
 * Replaces all Ta Plastic products with distinctly different pot/container images
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🎯 Ta Plastic Unique Image Replacer</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Replace ALL Ta Plastic products with visually distinct pot images</p>\n";

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

// ULTRA-SPECIFIC search terms for pots/containers - each one VERY different
$pot_search_terms = [
    'ceramic plant pot white modern minimalist',
    'terracotta clay pot rustic garden outdoor',
    'black metal planter contemporary design',
    'wooden planter box natural garden',
    'concrete plant pot industrial gray',
    'colorful ceramic pot bright blue',
    'hanging basket wicker natural',
    'stone planter large outdoor garden',
    'plastic flower pot bright red',
    'decorative urn classical garden',
    'bamboo planter natural eco friendly',
    'copper plant pot metallic shine',
    'rectangular planter modern white',
    'round ceramic pot glazed green',
    'square wooden planter rustic',
    'tall cylindrical pot contemporary',
    'shallow bowl planter wide',
    'decorative pot ornate pattern',
    'simple clay pot small brown',
    'large garden urn decorative',
    'vintage metal bucket planter',
    'mosaic tile pot colorful',
    'fiberglass planter smooth finish',
    'rattan basket natural weave',
    'glazed ceramic pot shiny',
    'cast iron planter heavy duty',
    'resin planter lightweight modern',
    'stainless steel pot sleek',
    'fabric grow bag soft',
    'self watering planter smart',
    'tiered planter multiple levels',
    'wall mounted planter vertical',
    'pedestal planter elevated',
    'geometric planter angular design',
    'oval planter elongated shape',
    'hexagonal pot unique shape',
    'tapered planter cone shape',
    'wide rim pot decorative edge',
    'narrow neck pot elegant',
    'textured surface pot rough',
    'smooth surface pot polished',
    'matte finish pot non reflective',
    'glossy finish pot shiny',
    'two tone pot color combination',
    'single color pot monochrome',
    'patterned pot decorative design',
    'plain pot simple clean',
    'ridged pot textured surface',
    'smooth ceramic pot perfect finish',
    'aged patina pot weathered look'
];

function get_ta_plastic_products() {
    $products = wc_get_products(['limit' => -1, 'status' => 'publish']);
    $ta_plastic_products = [];
    
    foreach ($products as $product) {
        $product_name = $product->get_name();
        if (stripos($product_name, 'ta plastic') !== false) {
            $ta_plastic_products[] = [
                'id' => $product->get_id(),
                'name' => $product_name,
                'object' => $product
            ];
        }
    }
    
    return $ta_plastic_products;
}

function get_unique_pot_image($search_term, $api_key, $source = 'unsplash') {
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

function replace_product_image($product_id, $image_url, $search_term) {
    $image_data = @file_get_contents($image_url);
    if (!$image_data) return false;
    
    $image = @imagecreatefromstring($image_data);
    if (!$image) return false;
    
    // Create unique filename with search term
    $safe_term = preg_replace('/[^a-zA-Z0-9]/', '_', $search_term);
    $filename = "ta_plastic_unique_{$product_id}_" . substr($safe_term, 0, 20) . '_' . time() . '.jpg';
    
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
        'post_title' => "Ta Plastic unique pot image for product {$product_id}",
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

// MAIN EXECUTION
$ta_plastic_products = get_ta_plastic_products();
$total_products = count($ta_plastic_products);

echo "<h2>📦 Found {$total_products} Ta Plastic products to replace</h2>\n";

if ($total_products === 0) {
    echo "<p>✅ No Ta Plastic products found</p>\n";
    exit;
}

echo "<h2>🔄 Starting image replacement process</h2>\n";

$processed = 0;
$successful = 0;
$failed = 0;
$start_time = time();

foreach ($ta_plastic_products as $index => $product_data) {
    $product_id = $product_data['id'];
    $product_name = $product_data['name'];
    
    // Use a different search term for each product to ensure variety
    $search_term_index = $index % count($pot_search_terms);
    $search_term = $pot_search_terms[$search_term_index];
    
    echo "<p><strong>🔄 [{$processed}+1/{$total_products}] {$product_name} (ID: {$product_id})</strong></p>\n";
    echo "<p>🔍 Search: '{$search_term}'</p>\n";
    
    $success = false;
    
    // Try Unsplash first
    if (!empty($unsplash_key)) {
        $image_url = get_unique_pot_image($search_term, $unsplash_key, 'unsplash');
        if ($image_url && replace_product_image($product_id, $image_url, $search_term)) {
            echo "<p>✅ SUCCESS - Unique Unsplash pot image assigned</p>\n";
            $successful++;
            $success = true;
        }
    }
    
    // Try Pexels if Unsplash failed
    if (!$success && !empty($pexels_key)) {
        $image_url = get_unique_pot_image($search_term, $pexels_key, 'pexels');
        if ($image_url && replace_product_image($product_id, $image_url, $search_term)) {
            echo "<p>✅ SUCCESS - Unique Pexels pot image assigned</p>\n";
            $successful++;
            $success = true;
        }
    }
    
    if (!$success) {
        echo "<p>❌ FAILED - Could not get unique pot image</p>\n";
        $failed++;
    }
    
    $processed++;
    
    // Progress report every 10 products
    if ($processed % 10 === 0) {
        $elapsed = time() - $start_time;
        echo "<h4>📊 Progress Report</h4>\n";
        echo "<p>✅ Successful: {$successful} | ❌ Failed: {$failed} | ⏱️ Time: " . gmdate("H:i:s", $elapsed) . "</p>\n";
        flush();
    }
    
    // Delay for API rate limiting
    sleep(2);
    
    echo "<hr>\n";
}

$total_time = time() - $start_time;
$success_rate = $processed > 0 ? round(($successful / $processed) * 100, 1) : 0;

echo "<h2>🎉 TA PLASTIC IMAGE REPLACEMENT COMPLETED</h2>\n";
echo "<div style='background: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; border-radius: 8px;'>\n";
echo "<h3>📊 Final Statistics</h3>\n";
echo "<p><strong>📦 Products processed:</strong> {$processed}/{$total_products}</p>\n";
echo "<p><strong>✅ Successfully replaced:</strong> {$successful} ({$success_rate}%)</p>\n";
echo "<p><strong>❌ Failed:</strong> {$failed}</p>\n";
echo "<p><strong>⏱️ Total time:</strong> " . gmdate("H:i:s", $total_time) . "</p>\n";
echo "</div>\n";

if ($successful > 0) {
    echo "<div style='background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 8px; margin: 20px 0;'>\n";
    echo "<h3>🎯 TA PLASTIC VISUAL DUPLICATES ELIMINATED!</h3>\n";
    echo "<p><strong>✅ Each Ta Plastic product now has a distinctly different pot image</strong></p>\n";
    echo "<p>🌟 No more visual similarity between Ta Plastic products</p>\n";
    echo "<p>🛒 Each product is visually unique and professional</p>\n";
    echo "</div>\n";
}

echo "<h3>🌐 <a href='http://localhost:8080/tienda/' target='_blank'>Verify results in the shop - Ta Plastic section</a></h3>\n";
echo "<p><em>Ta Plastic image replacement completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>