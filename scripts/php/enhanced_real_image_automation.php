<?php
/**
 * 🚀 Enhanced Real Image Automation - Robust Processing
 * Processes ALL 522 remaining products with SVG images
 */

// Increase memory and time limits
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 7200); // 2 hours
set_time_limit(7200);

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🚀 Enhanced Real Image Automation - INICIANDO PROCESAMIENTO MASIVO</h1>\n";
echo "<p><strong>Memoria límite:</strong> " . ini_get('memory_limit') . "</p>\n";
echo "<p><strong>Tiempo límite:</strong> " . ini_get('max_execution_time') . " segundos</p>\n";

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
$pexels_key = $_ENV['PEXELS_API_KEY'] ?? '';

if (empty($unsplash_key) && empty($pexels_key)) {
    die("❌ Error: No image API keys found");
}

// Enhanced plant translations
$plant_translations = [
    'jazmin' => 'jasmine flower white',
    'rosa' => 'rose flower garden',
    'begonia' => 'begonia colorful flower',
    'ficus' => 'ficus houseplant green',
    'dracaena' => 'dracaena indoor plant',
    'palmera' => 'palm tree plant',
    'crisantemo' => 'chrysanthemum flower',
    'azalea' => 'azalea flowering shrub',
    'camelia' => 'camellia flower bush',
    'laurel' => 'laurel bay tree',
    'olivo' => 'olive tree branch',
    'cedro' => 'cedar tree conifer',
    'eucalipto' => 'eucalyptus tree leaves',
    'pino' => 'pine tree evergreen',
    'cipres' => 'cypress tree Mediterranean',
    'limon' => 'lemon tree citrus',
    'naranja' => 'orange tree citrus',
    'mandarina' => 'mandarin citrus tree',
    'higuera' => 'fig tree fruit',
    'granado' => 'pomegranate tree',
    'cerezo' => 'cherry tree blossom',
    'manzano' => 'apple tree orchard',
    'durazno' => 'peach tree fruit',
    'almendro' => 'almond tree bloom',
    'nogal' => 'walnut tree nut',
    'castano' => 'chestnut tree autumn',
    'roble' => 'oak tree acorn',
    'sauce' => 'willow tree drooping',
    'bambu' => 'bamboo plant zen',
    'cactus' => 'cactus succulent desert',
    'aloe' => 'aloe vera succulent',
    'lavanda' => 'lavender purple fragrant',
    'romero' => 'rosemary herb green',
    'tomillo' => 'thyme herb garden',
    'menta' => 'mint herb fresh',
    'albahaca' => 'basil herb cooking',
    'perejil' => 'parsley herb flat',
    'oregano' => 'oregano herb dried',
    'salvia' => 'sage herb silver',
    'ruda' => 'rue herb bitter',
    'boj' => 'boxwood hedge trimmed',
    'tejo' => 'yew tree evergreen',
    'abeto' => 'fir tree Christmas',
    'enebro' => 'juniper berry blue',
    'tuya' => 'thuja evergreen hedge',
    'photinia' => 'photinia red tip',
    'pittosporum' => 'pittosporum shrub variegated',
    'viburnum' => 'viburnum flowering bush',
    'espino' => 'hawthorn thorny bush',
    'madroño' => 'strawberry tree fruit',
    'lentisco' => 'mastic tree Mediterranean',
    'adelfa' => 'oleander pink flower',
    'buganvilla' => 'bougainvillea purple flower',
    'madreselva' => 'honeysuckle climbing vine',
    'glicinia' => 'wisteria purple cascade',
    'parra' => 'grapevine vineyard',
    'hiedra' => 'ivy climbing green',
    'geranio' => 'geranium red flower',
    'petunia' => 'petunia colorful flower',
    'pensamiento' => 'pansy violet flower',
    'margarita' => 'daisy white flower',
    'girasol' => 'sunflower yellow tall',
    'tulipan' => 'tulip spring bulb',
    'clavel' => 'carnation pink flower',
    'orquidea' => 'orchid exotic flower',
    'lirio' => 'lily elegant flower',
    'gladiolo' => 'gladiolus sword flower',
    'dalia' => 'dahlia colorful bloom',
    'hortensia' => 'hydrangea blue flower',
    'magnolia' => 'magnolia tree bloom',
    'hibisco' => 'hibiscus tropical flower',
    'gardenia' => 'gardenia white fragrant',
    'jacaranda' => 'jacaranda purple tree',
    'flamboyant' => 'flame tree red',
    'acacia' => 'acacia yellow flower',
    'mimosa' => 'mimosa silver wattle',
    'eucgen' => 'eucalyptus gene variety',
    'eugmyr' => 'eucalyptus myrtus hybrid'
];

function generate_search_terms($product_name) {
    global $plant_translations;
    
    $name_lower = strtolower($product_name);
    $search_terms = [];
    
    // Check for exact matches in translations
    foreach ($plant_translations as $spanish => $english) {
        if (strpos($name_lower, $spanish) !== false) {
            $search_terms[] = $english;
            break;
        }
    }
    
    // Extract plant patterns
    if (preg_match('/([a-z]+)/', $name_lower, $matches)) {
        $base_name = $matches[1];
        if (isset($plant_translations[$base_name])) {
            $search_terms[] = $plant_translations[$base_name];
        }
    }
    
    // Fallback terms
    if (empty($search_terms)) {
        $search_terms = ['plant green garden', 'tree nature outdoor', 'flower botanical beautiful'];
    }
    
    return $search_terms;
}

function get_unsplash_image($search_term, $api_key) {
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
    
    return false;
}

function get_pexels_image($search_term, $api_key) {
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
    
    return false;
}

function process_real_image($product_id, $product_name, $unsplash_key, $pexels_key) {
    $search_terms = generate_search_terms($product_name);
    
    foreach ($search_terms as $search_term) {
        // Try Unsplash first
        if (!empty($unsplash_key)) {
            $image_url = get_unsplash_image($search_term, $unsplash_key);
            if ($image_url) {
                return download_and_set_image($product_id, $product_name, $image_url, 'unsplash');
            }
        }
        
        // Try Pexels as backup
        if (!empty($pexels_key)) {
            $image_url = get_pexels_image($search_term, $pexels_key);
            if ($image_url) {
                return download_and_set_image($product_id, $product_name, $image_url, 'pexels');
            }
        }
        
        // Small delay between different search terms
        usleep(500000); // 0.5 seconds
    }
    
    return false;
}

function download_and_set_image($product_id, $product_name, $image_url, $source) {
    $image_data = @file_get_contents($image_url);
    if (!$image_data) {
        return false;
    }
    
    // Create unique filename
    $timestamp = time();
    $filename = "product_{$product_id}_{$timestamp}.jpg";
    
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
        'post_title' => sanitize_title($product_name),
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

// Main processing
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$products_to_process = [];

// Find products with SVG images
foreach ($products as $product) {
    $product_id = $product->get_id();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        $file_extension = strtolower(pathinfo($image_url, PATHINFO_EXTENSION));
        
        if (strpos($image_url, 'placeholder.svg') !== false || $file_extension === 'svg') {
            $products_to_process[] = [
                'id' => $product_id,
                'name' => $product->get_name()
            ];
        }
    }
}

$total_to_process = count($products_to_process);
echo "<p><strong>🔍 Productos con SVG detectados:</strong> $total_to_process</p>\n";

if ($total_to_process === 0) {
    echo "<h2>🎉 ¡Todos los productos ya tienen imágenes reales!</h2>\n";
    exit;
}

echo "<h2>🚀 PROCESAMIENTO MASIVO INICIADO</h2>\n";
echo "<p><strong>🎯 Objetivo:</strong> Reemplazar $total_to_process imágenes SVG con imágenes reales</p>\n";

$processed = 0;
$successful = 0;
$failed = 0;
$batch_size = 50; // Smaller batch size for stability
$start_time = time();

foreach ($products_to_process as $index => $product_data) {
    $product_id = $product_data['id'];
    $product_name = $product_data['name'];
    
    $current_num = $processed + 1;
    echo "<p><strong>🔄 [$current_num/$total_to_process] {$product_name} (ID: $product_id)</strong></p>\n";
    
    $success = process_real_image($product_id, $product_name, $unsplash_key, $pexels_key);
    
    if ($success) {
        $successful++;
        echo "<p>✅ Éxito - Imagen real asignada</p>\n";
    } else {
        $failed++;
        echo "<p>❌ Fallo - No se pudo obtener imagen</p>\n";
    }
    
    $processed++;
    
    // Progress report every 10 products
    if ($processed % 10 === 0) {
        $elapsed = time() - $start_time;
        $rate = $processed / ($elapsed / 60); // products per minute
        $remaining = $total_to_process - $processed;
        $eta = $remaining / $rate;
        
        echo "<h3>📊 Progreso del lote (Procesados: $processed)</h3>\n";
        echo "<p><strong>✅ Exitosos:</strong> $successful</p>\n";
        echo "<p><strong>❌ Fallidos:</strong> $failed</p>\n";
        echo "<p><strong>⏱️ ETA:</strong> " . round($eta) . " minutos</p>\n";
        echo "<hr>\n";
        flush();
    }
    
    // Delay between requests to respect API limits
    sleep(2); // 2 seconds between products
    
    // Memory cleanup every batch
    if ($processed % $batch_size === 0) {
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        echo "<p><em>🧹 Limpieza de memoria realizada</em></p>\n";
    }
}

$total_time = time() - $start_time;
$success_rate = round(($successful / $processed) * 100, 1);

echo "<h2>🎉 PROCESAMIENTO COMPLETADO</h2>\n";
echo "<p><strong>📊 Productos procesados:</strong> $processed</p>\n";
echo "<p><strong>✅ Exitosos:</strong> $successful ($success_rate%)</p>\n";
echo "<p><strong>❌ Fallidos:</strong> $failed</p>\n";
echo "<p><strong>⏱️ Tiempo total:</strong> " . gmdate("H:i:s", $total_time) . "</p>\n";

if ($successful > 0) {
    echo "<h3>🌐 Verificar resultados en: <a href='http://localhost:8080/tienda/'>http://localhost:8080/tienda/</a></h3>\n";
}
?>