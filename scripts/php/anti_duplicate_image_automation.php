<?php
/**
 * 🚫 Anti-Duplicate Image Automation System
 * SOLUCIÓN DEFINITIVA para prevenir imágenes repetidas
 */

// Increase memory and time limits
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 7200);
set_time_limit(7200);

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🚫 Anti-Duplicate Image Automation System</h1>\n";
echo "<p><strong>🎯 OBJETIVO:</strong> Reemplazar TODAS las imágenes duplicadas con imágenes únicas</p>\n";

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

// GLOBAL TRACKING ARRAYS
$used_image_urls = []; // Track all used image URLs
$used_image_hashes = []; // Track image content hashes
$search_term_history = []; // Track used search terms
$product_image_map = []; // Map products to their assigned images

/**
 * Enhanced plant translations with MUCH MORE variety
 */
$plant_translations = [
    'jazmin' => ['jasmine white flower', 'jasmine blossom fragrant', 'star jasmine vine', 'jasmine bush garden', 'white jasmine bloom'],
    'rosa' => ['rose red garden', 'pink rose flower', 'climbing rose bush', 'white rose bloom', 'yellow rose garden'],
    'begonia' => ['begonia colorful flower', 'tuberous begonia bloom', 'wax begonia plant', 'rex begonia leaves', 'dragon wing begonia'],
    'ficus' => ['ficus houseplant green', 'rubber tree plant', 'weeping fig tree', 'ficus benjamina indoor', 'fiddle leaf fig'],
    'dracaena' => ['dracaena indoor plant', 'dragon tree houseplant', 'dracaena marginata red', 'corn plant dracaena', 'lucky bamboo dracaena'],
    'palmera' => ['palm tree tropical', 'date palm garden', 'fan palm plant', 'coconut palm tree', 'areca palm indoor'],
    'crisantemo' => ['chrysanthemum flower', 'mum flower autumn', 'daisy chrysanthemum', 'yellow chrysanthemum', 'white mum flower'],
    'azalea' => ['azalea flowering shrub', 'pink azalea bloom', 'white azalea flower', 'red azalea bush', 'azalea garden spring'],
    'camelia' => ['camellia flower bush', 'red camellia bloom', 'white camellia flower', 'pink camellia garden', 'camellia japonica'],
    'laurel' => ['laurel bay tree', 'sweet bay laurel', 'bay leaf plant', 'laurel hedge garden', 'mountain laurel'],
    'olivo' => ['olive tree branch', 'mediterranean olive', 'olive grove tree', 'olive tree garden', 'young olive plant'],
    'cedro' => ['cedar tree conifer', 'red cedar tree', 'atlas cedar blue', 'lebanon cedar', 'cedar wood tree'],
    'eucalipto' => ['eucalyptus tree leaves', 'silver eucalyptus', 'blue gum eucalyptus', 'eucalyptus branch', 'round leaf eucalyptus'],
    'pino' => ['pine tree evergreen', 'scots pine tree', 'white pine needle', 'stone pine tree', 'pine forest tree'],
    'cipres' => ['cypress tree mediterranean', 'italian cypress', 'monterey cypress', 'bald cypress tree', 'leyland cypress'],
    'limon' => ['lemon tree citrus', 'meyer lemon tree', 'lemon fruit tree', 'citrus lemon garden', 'lemon blossom tree'],
    'naranja' => ['orange tree citrus', 'sweet orange tree', 'orange fruit tree', 'valencia orange', 'navel orange tree'],
    'mandarina' => ['mandarin citrus tree', 'tangerine tree fruit', 'clementine tree', 'satsuma mandarin', 'mandarin orange tree'],
    'higuera' => ['fig tree fruit', 'common fig tree', 'brown turkey fig', 'fig leaf tree', 'mediterranean fig'],
    'granado' => ['pomegranate tree', 'punica granatum', 'pomegranate fruit tree', 'dwarf pomegranate', 'ornamental pomegranate'],
    'cerezo' => ['cherry tree blossom', 'sweet cherry tree', 'sakura cherry bloom', 'sour cherry tree', 'ornamental cherry'],
    'manzano' => ['apple tree orchard', 'red apple tree', 'green apple tree', 'apple blossom tree', 'crabapple tree'],
    'durazno' => ['peach tree fruit', 'peach blossom pink', 'nectarine peach tree', 'dwarf peach tree', 'white peach tree'],
    'almendro' => ['almond tree bloom', 'almond blossom white', 'sweet almond tree', 'bitter almond tree', 'flowering almond'],
    'nogal' => ['walnut tree nut', 'english walnut tree', 'black walnut tree', 'walnut leaf tree', 'persian walnut'],
    'castano' => ['chestnut tree autumn', 'horse chestnut tree', 'sweet chestnut tree', 'american chestnut', 'chestnut leaf tree'],
    'roble' => ['oak tree acorn', 'white oak tree', 'red oak tree', 'live oak tree', 'english oak tree'],
    'sauce' => ['willow tree drooping', 'weeping willow tree', 'pussy willow branch', 'white willow tree', 'babylonian willow'],
    'bambu' => ['bamboo plant zen', 'lucky bamboo plant', 'golden bamboo grove', 'black bamboo stems', 'giant bamboo plant'],
    'cactus' => ['cactus succulent desert', 'barrel cactus round', 'prickly pear cactus', 'saguaro cactus tall', 'christmas cactus bloom'],
    'aloe' => ['aloe vera succulent', 'aloe plant medicinal', 'spiral aloe plant', 'tiger aloe striped', 'coral aloe flower'],
    'lavanda' => ['lavender purple fragrant', 'english lavender field', 'french lavender flower', 'spanish lavender bloom', 'lavender bush garden'],
    'romero' => ['rosemary herb green', 'rosemary bush garden', 'prostrate rosemary', 'upright rosemary', 'flowering rosemary'],
    'tomillo' => ['thyme herb garden', 'lemon thyme plant', 'creeping thyme ground', 'wild thyme herb', 'woolly thyme'],
    'menta' => ['mint herb fresh', 'peppermint plant', 'spearmint herb garden', 'chocolate mint plant', 'apple mint herb'],
    'albahaca' => ['basil herb cooking', 'sweet basil plant', 'purple basil herb', 'thai basil plant', 'lemon basil herb'],
    'perejil' => ['parsley herb flat', 'curly parsley herb', 'italian parsley', 'hamburg parsley', 'japanese parsley'],
    'oregano' => ['oregano herb dried', 'wild oregano plant', 'greek oregano herb', 'mexican oregano', 'golden oregano'],
    'salvia' => ['sage herb silver', 'purple sage plant', 'white sage herb', 'pineapple sage', 'garden sage herb'],
    'ruda' => ['rue herb bitter', 'common rue plant', 'fringed rue herb', 'mountain rue plant', 'meadow rue'],
    'boj' => ['boxwood hedge trimmed', 'english boxwood', 'japanese boxwood', 'korean boxwood', 'common boxwood shrub'],
];

/**
 * Generate UNIQUE search terms for each product using advanced algorithms
 */
function generate_unique_search_terms($product_name, $used_terms = []) {
    global $plant_translations, $search_term_history;
    
    $name_lower = strtolower($product_name);
    $search_terms = [];
    
    // Extract base plant name
    $base_plant = '';
    foreach ($plant_translations as $spanish => $english_variants) {
        if (strpos($name_lower, $spanish) !== false) {
            $base_plant = $spanish;
            break;
        }
    }
    
    if ($base_plant && isset($plant_translations[$base_plant])) {
        $variants = $plant_translations[$base_plant];
        
        // Find unused variants for this plant
        foreach ($variants as $variant) {
            $search_key = $base_plant . '_' . md5($variant);
            if (!in_array($search_key, $search_term_history)) {
                $search_terms[] = $variant;
                $search_term_history[] = $search_key;
                break; // Only use one variant per product to ensure uniqueness
            }
        }
    }
    
    // If no specific variant found, generate unique combinations
    if (empty($search_terms)) {
        $unique_descriptors = [
            'tropical plant garden',
            'exotic houseplant indoor',
            'ornamental garden plant',
            'flowering bush garden',
            'evergreen shrub landscape',
            'decorative plant pot',
            'botanical specimen rare',
            'horticultural variety',
            'landscape plant design',
            'container garden plant'
        ];
        
        foreach ($unique_descriptors as $descriptor) {
            $search_key = 'generic_' . md5($descriptor . $product_name);
            if (!in_array($search_key, $search_term_history)) {
                $search_terms[] = $descriptor;
                $search_term_history[] = $search_key;
                break;
            }
        }
    }
    
    // Add product-specific modifier based on ID for absolute uniqueness
    if (!empty($search_terms)) {
        preg_match('/\d+/', $product_name, $matches);
        $product_modifier = !empty($matches) ? $matches[0] : substr(md5($product_name), 0, 6);
        $search_terms[0] .= ' variety ' . $product_modifier;
    }
    
    return $search_terms;
}

/**
 * Enhanced Unsplash search with pagination and uniqueness
 */
function get_unique_unsplash_image($search_term, $api_key, $page = 1) {
    global $used_image_urls;
    
    $encoded_term = urlencode($search_term);
    $url = "https://api.unsplash.com/search/photos?query={$encoded_term}&per_page=10&page={$page}&orientation=squarish";
    
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
        if (isset($data['results']) && is_array($data['results'])) {
            // Try each image until we find one not used
            foreach ($data['results'] as $image) {
                $image_url = $image['urls']['regular'] ?? null;
                if ($image_url && !in_array($image_url, $used_image_urls)) {
                    return $image_url;
                }
            }
        }
    }
    
    return false;
}

/**
 * Enhanced Pexels search with pagination and uniqueness
 */
function get_unique_pexels_image($search_term, $api_key, $page = 1) {
    global $used_image_urls;
    
    $encoded_term = urlencode($search_term);
    $url = "https://api.pexels.com/v1/search?query={$encoded_term}&per_page=10&page={$page}&orientation=square";
    
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
        if (isset($data['photos']) && is_array($data['photos'])) {
            // Try each image until we find one not used
            foreach ($data['photos'] as $image) {
                $image_url = $image['src']['large'] ?? null;
                if ($image_url && !in_array($image_url, $used_image_urls)) {
                    return $image_url;
                }
            }
        }
    }
    
    return false;
}

/**
 * Generate SHA-256 hash of image content for duplicate detection
 */
function get_image_content_hash($image_data) {
    return hash('sha256', $image_data);
}

/**
 * Process image with absolute uniqueness guarantee
 */
function process_unique_image($product_id, $product_name, $unsplash_key, $pexels_key) {
    global $used_image_urls, $used_image_hashes;
    
    $search_terms = generate_unique_search_terms($product_name);
    
    foreach ($search_terms as $search_term) {
        // Try multiple pages to find unique images
        for ($page = 1; $page <= 3; $page++) {
            $image_url = null;
            
            // Try Unsplash first
            if (!empty($unsplash_key)) {
                $image_url = get_unique_unsplash_image($search_term, $unsplash_key, $page);
                if ($image_url) {
                    $result = download_and_verify_unique_image($product_id, $product_name, $image_url, 'unsplash');
                    if ($result) return true;
                }
            }
            
            // Try Pexels as backup
            if (!empty($pexels_key)) {
                $image_url = get_unique_pexels_image($search_term, $pexels_key, $page);
                if ($image_url) {
                    $result = download_and_verify_unique_image($product_id, $product_name, $image_url, 'pexels');
                    if ($result) return true;
                }
            }
            
            // Small delay between API calls
            usleep(300000); // 0.3 seconds
        }
    }
    
    return false;
}

/**
 * Download and verify image uniqueness before setting
 */
function download_and_verify_unique_image($product_id, $product_name, $image_url, $source) {
    global $used_image_urls, $used_image_hashes;
    
    // Download image data
    $image_data = @file_get_contents($image_url);
    if (!$image_data) {
        return false;
    }
    
    // Check content hash for uniqueness
    $content_hash = get_image_content_hash($image_data);
    if (in_array($content_hash, $used_image_hashes)) {
        return false; // Identical content, skip
    }
    
    // Process image
    $image = @imagecreatefromstring($image_data);
    if (!$image) {
        return false;
    }
    
    // Create unique filename with timestamp and random component
    $random_suffix = substr(md5(uniqid(mt_rand(), true)), 0, 8);
    $timestamp = time();
    $filename = "unique_product_{$product_id}_{$timestamp}_{$random_suffix}.jpg";
    
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
        'post_content' => "Unique image for {$product_name} from {$source}",
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
    
    // Mark as used
    $used_image_urls[] = $image_url;
    $used_image_hashes[] = $content_hash;
    
    return true;
}

/**
 * Find and analyze duplicate images
 */
function detect_duplicate_images() {
    $products = wc_get_products(['limit' => -1, 'status' => 'publish']);
    $image_urls = [];
    $duplicates = [];
    
    foreach ($products as $product) {
        $product_id = $product->get_id();
        $featured_image_id = $product->get_image_id();
        
        if ($featured_image_id) {
            $image_url = wp_get_attachment_url($featured_image_id);
            $file_extension = strtolower(pathinfo($image_url, PATHINFO_EXTENSION));
            
            // Skip SVG files
            if (strpos($image_url, 'placeholder.svg') === false && $file_extension !== 'svg') {
                if (isset($image_urls[$image_url])) {
                    $duplicates[$image_url][] = $product_id;
                    if (!isset($duplicates[$image_url][0])) {
                        $duplicates[$image_url] = [$image_urls[$image_url], $product_id];
                    }
                } else {
                    $image_urls[$image_url] = $product_id;
                }
            }
        }
    }
    
    return $duplicates;
}

// MAIN EXECUTION
echo "<h2>🔍 PASO 1: Detectar imágenes duplicadas</h2>\n";
$duplicates = detect_duplicate_images();
$total_duplicate_groups = count($duplicates);
$total_products_affected = 0;

foreach ($duplicates as $url => $product_ids) {
    $total_products_affected += count($product_ids);
}

echo "<p><strong>📊 Grupos de duplicados encontrados:</strong> $total_duplicate_groups</p>\n";
echo "<p><strong>📦 Productos afectados:</strong> $total_products_affected</p>\n";

if ($total_duplicate_groups === 0) {
    echo "<h2>🎉 ¡No se encontraron imágenes duplicadas!</h2>\n";
    exit;
}

echo "<h3>📋 Detalles de duplicados:</h3>\n";
foreach ($duplicates as $url => $product_ids) {
    echo "<p><strong>URL:</strong> " . basename($url) . " → Productos: " . implode(', ', $product_ids) . "</p>\n";
}

echo "<h2>🔄 PASO 2: Reemplazar duplicados con imágenes únicas</h2>\n";

$processed = 0;
$successful = 0;
$failed = 0;
$start_time = time();

foreach ($duplicates as $url => $product_ids) {
    // Keep the first product with original image, replace others
    $products_to_replace = array_slice($product_ids, 1);
    
    foreach ($products_to_replace as $product_id) {
        $product = wc_get_product($product_id);
        if (!$product) continue;
        
        $product_name = $product->get_name();
        
        echo "<p><strong>🔄 Reemplazando imagen duplicada para: {$product_name} (ID: $product_id)</strong></p>\n";
        
        $success = process_unique_image($product_id, $product_name, $unsplash_key, $pexels_key);
        
        if ($success) {
            $successful++;
            echo "<p>✅ Éxito - Nueva imagen única asignada</p>\n";
        } else {
            $failed++;
            echo "<p>❌ Fallo - No se pudo obtener imagen única</p>\n";
        }
        
        $processed++;
        
        // Progress report
        if ($processed % 5 === 0) {
            $elapsed = time() - $start_time;
            echo "<h4>📊 Progreso: $processed procesados</h4>\n";
            echo "<p>✅ Exitosos: $successful | ❌ Fallidos: $failed</p>\n";
            flush();
        }
        
        // Delay between requests
        sleep(3); // 3 seconds between products for API rate limiting
    }
}

$total_time = time() - $start_time;
$success_rate = $processed > 0 ? round(($successful / $processed) * 100, 1) : 0;

echo "<h2>🎉 PROCESAMIENTO COMPLETADO</h2>\n";
echo "<p><strong>📊 Productos procesados:</strong> $processed</p>\n";
echo "<p><strong>✅ Exitosos:</strong> $successful ($success_rate%)</p>\n";
echo "<p><strong>❌ Fallidos:</strong> $failed</p>\n";
echo "<p><strong>⏱️ Tiempo total:</strong> " . gmdate("H:i:s", $total_time) . "</p>\n";

// Final verification
echo "<h2>🔍 VERIFICACIÓN FINAL</h2>\n";
$final_duplicates = detect_duplicate_images();
$final_duplicate_count = count($final_duplicates);

echo "<p><strong>🎯 Duplicados restantes:</strong> $final_duplicate_count grupos</p>\n";

if ($final_duplicate_count === 0) {
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>\n";
    echo "<h3>🎉 ¡ÉXITO TOTAL!</h3>\n";
    echo "<p><strong>✅ NO hay imágenes duplicadas en el sitio</strong></p>\n";
    echo "<p>🌟 Todas las imágenes son ahora únicas y distintas</p>\n";
    echo "</div>\n";
} else {
    echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>\n";
    echo "<h3>⚠️ Aún quedan algunos duplicados</h3>\n";
    echo "<p>Ejecutar el script nuevamente para procesar los restantes</p>\n";
    echo "</div>\n";
}

echo "<h3>🌐 Verificar resultados en: <a href='http://localhost:8080/tienda/' target='_blank'>Tienda Los Cocos</a></h3>\n";
echo "<p><em>Script completado: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>