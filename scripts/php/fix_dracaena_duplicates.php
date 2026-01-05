<?php
/**
 * 🎯 Fix Dracaena Duplicate Images
 * Specifically targets the duplicate Dracaena images visible in the shop
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🎯 Los Cocos - Fix Dracaena Duplicate Images</h1>\n";

// Load environment variables
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

// Find all Dracaena products
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$dracaena_products = [];

foreach ($products as $product) {
    $name = strtolower($product->get_name());
    if (strpos($name, 'dracma') !== false) {
        $dracaena_products[] = [
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'image_id' => $product->get_image_id()
        ];
    }
}

echo "<p>Found " . count($dracaena_products) . " Dracaena products</p>\n";

// Different search terms for each Dracaena product
$dracaena_searches = [
    'dracaena marginata red edge plant',
    'dragon tree indoor houseplant',
    'dracaena fragrans corn plant',
    'dracaena janet craig green',
    'dracaena reflexa song india',
    'dracaena compacta small plant',
    'dracaena sanderiana lucky bamboo',
    'dracaena deremensis white stripe',
    'madagascar dragon tree outdoor',
    'ribbon plant dracaena colorful',
    'dracaena lemon lime bright',
    'dracaena tricolor variegated',
    'dracaena warneckii indoor',
    'dracaena gold star yellow',
    'dracaena arturo variegated'
];

function get_unique_dracaena_image($search_term, $api_key) {
    $encoded_term = urlencode($search_term);
    $url = "https://api.unsplash.com/search/photos?query={$encoded_term}&per_page=10&orientation=squarish";
    
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
    
    return false;
}

function download_and_set_dracaena_image($product_id, $product_name, $image_url) {
    $image_data = @file_get_contents($image_url);
    if (!$image_data) {
        return false;
    }
    
    // Create unique filename
    $timestamp = time();
    $random = mt_rand(1000, 9999);
    $filename = "dracaena_unique_{$product_id}_{$timestamp}_{$random}.jpg";
    
    // Process image
    $image = @imagecreatefromstring($image_data);
    if (!$image) {
        return false;
    }
    
    // Resize to 1200x1200
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
        'post_title' => sanitize_title($product_name . ' unique'),
        'post_content' => '',
        'post_status' => 'inherit'
    ];
    
    $attachment_id = wp_insert_attachment($attachment, $file_path);
    if (!$attachment_id) {
        return false;
    }
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
    wp_update_attachment_metadata($attachment_id, $attachment_metadata);
    
    // Set as featured image
    set_post_thumbnail($product_id, $attachment_id);
    
    return true;
}

// Process each Dracaena product with unique search
$processed = 0;
$successful = 0;

foreach ($dracaena_products as $index => $product) {
    $product_id = $product['id'];
    $product_name = $product['name'];
    
    // Use different search term for each product
    $search_index = $index % count($dracaena_searches);
    $search_term = $dracaena_searches[$search_index];
    
    echo "<p><strong>🔄 Processing: {$product_name}</strong></p>\n";
    echo "<p>   🔍 Search: '$search_term'</p>\n";
    
    $image_url = get_unique_dracaena_image($search_term, $unsplash_key);
    
    if ($image_url) {
        $success = download_and_set_dracaena_image($product_id, $product_name, $image_url);
        if ($success) {
            $successful++;
            echo "<p>   ✅ SUCCESS: New unique image assigned</p>\n";
        } else {
            echo "<p>   ❌ FAILED: Could not process image</p>\n";
        }
    } else {
        echo "<p>   ❌ FAILED: Could not find image</p>\n";
    }
    
    $processed++;
    sleep(2); // Rate limiting
    flush();
}

echo "<h2>🎉 Dracaena Image Fix Completed</h2>\n";
echo "<p><strong>✅ Processed:</strong> $processed products</p>\n";
echo "<p><strong>🎯 Successful:</strong> $successful products</p>\n";
echo "<p><strong>📊 Success Rate:</strong> " . round(($successful / $processed) * 100, 1) . "%</p>\n";

echo "<h3>🔗 Check Results:</h3>\n";
echo "<p><a href='http://localhost:8080/tienda/' target='_blank'>🛒 View Shop</a> - Dracaena products should now have unique images</p>\n";
?>