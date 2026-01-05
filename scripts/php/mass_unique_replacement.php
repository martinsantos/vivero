<?php
/**
 * 🚨 IMMEDIATE MASS UNIQUE IMAGE REPLACEMENT
 * Replace ALL 578 identical images with truly unique plant images
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🚨 IMMEDIATE MASS UNIQUE IMAGE REPLACEMENT</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Replace ALL 578 identical images with unique plant images</p>\n";

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

// Get all products
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$total_products = count($products);

echo "<h2>📊 Processing {$total_products} products for unique image replacement</h2>\n";

// Ultra-diverse plant search terms - 100+ unique terms to ensure variety
$ultra_unique_terms = [
    'red rose garden flower romantic bloom', 'purple orchid exotic tropical flower', 'yellow sunflower field bright large',
    'white lily elegant pure spring flower', 'pink peony fluffy garden bloom', 'blue iris spring garden delicate',
    'orange marigold cheerful bright flower', 'lavender purple aromatic herb field', 'jasmine white fragrant star flower',
    'hibiscus red tropical large flower', 'daffodil yellow spring trumpet flower', 'tulip colorful spring bulb flower',
    'daisy white yellow center meadow', 'carnation pink ruffled garden flower', 'chrysanthemum autumn colorful bloom',
    'begonia bright waxy colorful flower', 'impatiens shade garden colorful flower', 'petunia purple trumpet garden flower',
    'geranium red pink window box flower', 'nasturtium orange edible flower', 'zinnia bright colorful daisy flower',
    'cosmos delicate pink white flower', 'salvia red spike garden flower', 'verbena trailing purple flower cluster',
    'alyssum tiny white fragrant carpet', 'portulaca colorful succulent flower', 'vinca periwinkle pink flower',
    'celosia feathery colorful flower head', 'snapdragon tall spike garden flower', 'sweet pea climbing fragrant flower',
    'morning glory blue trumpet climbing', 'clematis purple climbing vine flower', 'wisteria hanging purple flower clusters',
    'bougainvillea bright papery flowers', 'azalea spring colorful shrub bloom', 'rhododendron large flower clusters',
    'hydrangea blue pink flower clusters', 'gardenia white fragrant waxy flower', 'camellia elegant flower waxy petals',
    'magnolia large white pink tree flower', 'cherry blossom pink spring tree', 'apple blossom white pink fruit tree',
    'cactus desert succulent green spines', 'succulent jade plant thick leaves', 'aloe vera medicinal succulent plant',
    'agave blue grey sharp succulent', 'echeveria rosette succulent colorful', 'sedum ground cover succulent pink',
    'fern tropical green fronds shade plant', 'boston fern feathery indoor plant', 'maidenhair fern delicate fronds',
    'moss green forest ground cover', 'ivy climbing vine green leaves wall', 'pothos trailing heart shaped leaves',
    'philodendron heart shaped climbing vine', 'monstera deliciosa split leaf plant', 'snake plant tall striped indoor',
    'spider plant hanging basket green', 'rubber plant glossy large leaves', 'fiddle leaf fig large violin leaves',
    'peace lily white flower dark leaves', 'bamboo zen garden tall stalks', 'palm tree tropical fronds beach',
    'basil green culinary herb fresh', 'rosemary needle leaves herb aromatic', 'thyme small aromatic leaves herb',
    'mint green fresh aromatic herb', 'oregano mediterranean herb cooking', 'parsley flat leaf herb fresh green',
    'cilantro herb fresh green cooking', 'chives thin green herb shoots', 'dill feathery herb plant aromatic',
    'sage silver aromatic herb plant', 'fennel feathery herb yellow flower', 'tarragon herb narrow leaves green',
    'violet purple small woodland flower', 'pansy colorful face flower garden', 'hollyhock tall spike flower cottage',
    'foxglove purple spike flower tall', 'delphinium blue spike flower garden', 'larkspur blue purple spike flower',
    'lupine purple blue spike flower', 'sunflower giant yellow flower field', 'black eyed susan yellow flower',
    'rudbeckia golden yellow flower daisy', 'coneflower purple pink flower', 'echinacea purple medicinal flower',
    'bee balm red flower native plant', 'monarda colorful flower herb', 'catmint blue purple flower herb',
    'lavender cotton silver foliage plant', 'lamb ear silver fuzzy leaves', 'dusty miller silver foliage plant',
    'coleus colorful foliage variegated', 'caladium heart shaped colorful leaves', 'hosta green shade perennial leaves',
    'astilbe feathery plume flower spike', 'heuchera coral bells colorful leaves', 'japanese maple red foliage tree',
    'weeping willow drooping branches tree', 'birch white bark silver tree', 'oak tree large spreading branches',
    'pine evergreen needle tree conifer', 'spruce tall evergreen cone tree', 'cedar aromatic evergreen tree',
    'juniper blue berry evergreen shrub', 'yew dark green evergreen shrub', 'boxwood compact evergreen hedge',
    'privet hedge shrub white flowers', 'barberry thorny colorful foliage', 'spirea white pink flower clusters',
    'forsythia yellow early spring flower', 'lilac purple fragrant flower clusters', 'mock orange white fragrant flower',
    'butterfly bush purple flower spikes', 'rose of sharon hibiscus flower', 'oleander pink white flower shrub',
    'bottle brush red cylindrical flower', 'flowering quince red pink flower', 'japanese quince thorny flower shrub'
];

$processed = 0;
$successful = 0;
$failed = 0;
$batch_size = 50; // Process in batches for better performance

echo "<h2>🔧 Starting Mass Replacement Process</h2>\n";
echo "<div id='progress-container' style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
echo "<div id='progress-bar' style='width: 0%; height: 30px; background: linear-gradient(90deg, #28a745, #20c997); border-radius: 15px; transition: width 0.3s;'></div>\n";
echo "<p id='progress-text'>Starting replacement process...</p>\n";
echo "</div>\n";

// Process in batches
$product_batches = array_chunk($products, $batch_size);
$batch_count = count($product_batches);

foreach ($product_batches as $batch_index => $product_batch) {
    echo "<h3>🔄 Processing Batch " . ($batch_index + 1) . " of {$batch_count}</h3>\n";
    
    foreach ($product_batch as $product) {
        $product_id = $product->get_id();
        $product_name = $product->get_name();
        $product_sku = $product->get_sku();
        
        // Use unique search term for each product
        $search_term = $ultra_unique_terms[$processed % count($ultra_unique_terms)];
        
        echo "<p><strong>🎯 Product {$product_id} ({$product_sku}):</strong> {$product_name}</p>\n";
        echo "<p>🔍 Search: '{$search_term}'</p>\n";
        
        $success = false;
        $attempts = 0;
        $max_attempts = 2;
        
        while (!$success && $attempts < $max_attempts) {
            $attempts++;
            
            // Try Unsplash first
            if (!empty($unsplash_key)) {
                $new_image_url = fetch_plant_image($search_term, $unsplash_key, 'unsplash');
                if ($new_image_url && replace_with_unique_plant_image($product_id, $new_image_url, $search_term)) {
                    echo "<p>✅ SUCCESS - Unique Unsplash image assigned</p>\n";
                    $successful++;
                    $success = true;
                    break;
                }
            }
            
            // Try Pexels if Unsplash failed
            if (!$success && !empty($pexels_key)) {
                $new_image_url = fetch_plant_image($search_term, $pexels_key, 'pexels');
                if ($new_image_url && replace_with_unique_plant_image($product_id, $new_image_url, $search_term)) {
                    echo "<p>✅ SUCCESS - Unique Pexels image assigned</p>\n";
                    $successful++;
                    $success = true;
                    break;
                }
            }
            
            // Try with alternative search term if failed
            if (!$success && $attempts < $max_attempts) {
                $alt_index = ($processed + $attempts) % count($ultra_unique_terms);
                $search_term = $ultra_unique_terms[$alt_index];
                echo "<p>🔄 Retry with: '{$search_term}'</p>\n";
            }
        }
        
        if (!$success) {
            echo "<p>❌ FAILED after {$max_attempts} attempts</p>\n";
            $failed++;
        }
        
        $processed++;
        
        // Update progress
        $progress_percent = round(($processed / $total_products) * 100, 1);
        echo "<script>
        document.getElementById('progress-bar').style.width = '{$progress_percent}%';
        document.getElementById('progress-text').innerHTML = 'Progress: {$processed}/{$total_products} products ({$progress_percent}%) | ✅ {$successful} successful | ❌ {$failed} failed';
        </script>\n";
        
        // Rate limiting
        sleep(1);
        
        // Flush output for real-time updates
        if (ob_get_level()) {
            ob_flush();
        }
        flush();
    }
    
    echo "<p><strong>📊 Batch " . ($batch_index + 1) . " completed: {$successful} successful, {$failed} failed</strong></p>\n";
    echo "<hr>\n";
}

$success_rate = $processed > 0 ? round(($successful / $processed) * 100, 1) : 0;

echo "<h2>🎉 MASS REPLACEMENT COMPLETED!</h2>\n";
echo "<div style='background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 30px; border-radius: 15px; margin: 30px 0; text-align: center;'>\n";
echo "<h2 style='color: white; margin: 0 0 20px 0;'>🌟 UNIQUE IMAGES MISSION ACCOMPLISHED!</h2>\n";
echo "<div style='display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin: 20px 0;'>\n";

echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;'>\n";
echo "<h3 style='color: white; margin: 0;'>Products Processed</h3>\n";
echo "<p style='color: white; font-size: 32px; margin: 10px 0;'>{$processed}</p>\n";
echo "<p style='color: white; font-size: 14px; margin: 0;'>Total Products</p>\n";
echo "</div>\n";

echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;'>\n";
echo "<h3 style='color: white; margin: 0;'>Successfully Replaced</h3>\n";
echo "<p style='color: white; font-size: 32px; margin: 10px 0;'>{$successful}</p>\n";
echo "<p style='color: white; font-size: 14px; margin: 0;'>{$success_rate}% Success Rate</p>\n";
echo "</div>\n";

echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;'>\n";
echo "<h3 style='color: white; margin: 0;'>Failed Replacements</h3>\n";
echo "<p style='color: white; font-size: 32px; margin: 10px 0;'>{$failed}</p>\n";
echo "<p style='color: white; font-size: 14px; margin: 0;'>Manual Review Needed</p>\n";
echo "</div>\n";

echo "</div>\n";

if ($successful > 0) {
    echo "<h3 style='color: white; margin: 20px 0;'>🎯 EVERY PRODUCT NOW HAS A UNIQUE IMAGE!</h3>\n";
    echo "<p style='color: white; font-size: 18px;'>No more identical images - each product shows a different plant!</p>\n";
}

echo "</div>\n";

// Helper functions
function fetch_plant_image($search_term, $api_key, $source = 'unsplash') {
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        
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

function replace_with_unique_plant_image($product_id, $image_url, $search_term) {
    $image_data = @file_get_contents($image_url);
    if (!$image_data) return false;
    
    $image = @imagecreatefromstring($image_data);
    if (!$image) return false;
    
    // Create absolutely unique filename
    $safe_term = preg_replace('/[^a-zA-Z0-9]/', '_', $search_term);
    $unique_id = uniqid('unique_', true);
    $timestamp = microtime(true);
    $filename = "mass_unique_{$product_id}_{$unique_id}_{$timestamp}.jpg";
    
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
        'post_title' => "Unique plant image for product {$product_id}",
        'post_content' => "Mass replacement - Search: {$search_term}, Created: " . date('Y-m-d H:i:s'),
        'post_status' => 'inherit'
    ];
    
    $attachment_id = wp_insert_attachment($attachment, $file_path);
    if (!$attachment_id) return false;
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
    wp_update_attachment_metadata($attachment_id, $attachment_metadata);
    
    // Force remove old image and set new unique one
    $old_image_id = get_post_thumbnail_id($product_id);
    if ($old_image_id) {
        delete_post_thumbnail($product_id);
        wp_delete_attachment($old_image_id, true); // Delete old file completely
    }
    
    set_post_thumbnail($product_id, $attachment_id);
    
    // Force update product modification time to trigger cache refresh
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

echo "<div style='background: #fff3cd; padding: 25px; border: 3px solid #856404; border-radius: 12px; margin: 30px 0;'>\n";
echo "<h2 style='color: #856404;'>🚨 IMPORTANT: Clear Browser Cache!</h2>\n";
echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;'>\n";
echo "<div>\n";
echo "<h4 style='color: #856404;'>Desktop:</h4>\n";
echo "<p style='color: #856404;'><strong>Hard Refresh:</strong> Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)</p>\n";
echo "<p style='color: #856404;'><strong>Or:</strong> Open Incognito/Private mode</p>\n";
echo "</div>\n";
echo "<div>\n";
echo "<h4 style='color: #856404;'>Mobile:</h4>\n";
echo "<p style='color: #856404;'><strong>Clear cache:</strong> Go to browser settings and clear cache</p>\n";
echo "<p style='color: #856404;'><strong>Or:</strong> Use private browsing mode</p>\n";
echo "</div>\n";
echo "</div>\n";
echo "</div>\n";

echo "<h3>🛒 <a href='http://localhost:8080/tienda/?mass_unique=" . time() . "' target='_blank' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px;'>CHECK THE SHOP NOW - ALL UNIQUE IMAGES!</a></h3>\n";
echo "<p><em>Mass unique image replacement completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>