<?php
/**
 * 🚫 Simple Duplicate Fix Script
 * Fixes duplicate images by assigning unique ones
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🚫 Eliminador de Duplicados Simple</h1>\n";

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

// Unique search variations for each product
$unique_searches = [
    'tropical plant indoor',
    'garden plant outdoor',
    'flowering plant colorful',
    'green houseplant decorative',
    'ornamental plant beautiful',
    'botanical plant natural',
    'exotic plant rare',
    'landscape plant design',
    'container plant pot',
    'foliage plant leaves',
    'succulent plant desert',
    'herb plant aromatic',
    'shrub plant bushy',
    'tree plant tall',
    'vine plant climbing',
    'perennial plant garden',
    'annual plant seasonal',
    'medicinal plant healing',
    'fragrant plant scented',
    'variegated plant striped'
];

function get_unique_image($search_term, $api_key) {
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
    
    return false;
}

function download_and_set_unique($product_id, $image_url) {
    $image_data = @file_get_contents($image_url);
    if (!$image_data) return false;
    
    $image = @imagecreatefromstring($image_data);
    if (!$image) return false;
    
    // Create unique filename
    $unique_id = uniqid('fix_') . '_' . $product_id;
    $filename = "fixed_duplicate_{$unique_id}.jpg";
    
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
        'post_title' => "Fixed duplicate for product {$product_id}",
        'post_content' => '',
        'post_status' => 'inherit'
    ];
    
    $attachment_id = wp_insert_attachment($attachment, $file_path);
    if (!$attachment_id) return false;
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
    wp_update_attachment_metadata($attachment_id, $attachment_metadata);
    
    set_post_thumbnail($product_id, $attachment_id);
    return true;
}

// Find duplicates quickly
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$image_urls = [];
$duplicates = [];

foreach ($products as $product) {
    $product_id = $product->get_id();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        if (strpos($image_url, '.svg') === false && strpos($image_url, 'placeholder') === false) {
            if (isset($image_urls[$image_url])) {
                $duplicates[] = $product_id;
            } else {
                $image_urls[$image_url] = $product_id;
            }
        }
    }
}

echo "<p><strong>Duplicados encontrados:</strong> " . count($duplicates) . "</p>\n";

if (count($duplicates) === 0) {
    echo "<h2>✅ ¡No hay duplicados!</h2>\n";
    exit;
}

// Fix duplicates
$fixed = 0;
$search_index = 0;

foreach ($duplicates as $product_id) {
    $product = wc_get_product($product_id);
    $product_name = $product->get_name();
    
    // Use a unique search term for each duplicate
    $search_term = $unique_searches[$search_index % count($unique_searches)];
    $search_index++;
    
    echo "<p>🔄 Arreglando: {$product_name} (ID: $product_id) con término: '$search_term'</p>\n";
    
    $image_url = get_unique_image($search_term, $unsplash_key);
    if ($image_url && download_and_set_unique($product_id, $image_url)) {
        $fixed++;
        echo "<p>✅ Corregido</p>\n";
    } else {
        echo "<p>❌ Error</p>\n";
    }
    
    flush();
    sleep(2); // Rate limiting
}

echo "<h2>📊 Resumen</h2>\n";
echo "<p><strong>Duplicados procesados:</strong> " . count($duplicates) . "</p>\n";
echo "<p><strong>Exitosamente corregidos:</strong> $fixed</p>\n";
echo "<p><strong>Tasa de éxito:</strong> " . round(($fixed / count($duplicates)) * 100, 1) . "%</p>\n";

echo "<h3>🌐 <a href='http://localhost:8080/tienda/'>Verificar resultados en la tienda</a></h3>\n";
?>