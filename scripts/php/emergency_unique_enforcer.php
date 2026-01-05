<?php
/**
 * 🚫 EMERGENCY UNIQUE IMAGE ENFORCER
 * Fixes ALL remaining duplicate images with absolute uniqueness guarantee
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🚫 EMERGENCY UNIQUE IMAGE ENFORCER</h1>\n";
echo "<p><strong>🎯 MISSION:</strong> Ensure EVERY product has a completely unique image</p>\n";

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

// GLOBAL TRACKING - Store ALL used images
$used_image_hashes = [];
$processed_products = 0;
$fixed_duplicates = 0;

/**
 * Calculate image hash for duplicate detection
 */
function get_image_hash($image_url) {
    $image_data = @file_get_contents($image_url);
    if (!$image_data) return false;
    return md5($image_data);
}

/**
 * Generate ultra-specific search terms for absolute uniqueness
 */
function generate_ultra_unique_search($product_name, $product_id) {
    // Extract product characteristics
    $name_lower = strtolower($product_name);
    
    // Ultra-specific plant search variations
    $plant_specific_terms = [
        'jazmin' => [
            'white jasmine flower blooming garden',
            'star jasmine vine climbing wall',
            'night blooming jasmine fragrant',
            'confederate jasmine evergreen',
            'carolina jasmine yellow trumpet'
        ],
        'rosa' => [
            'red garden rose bloom close',
            'pink climbing rose bush',
            'white hybrid tea rose',
            'yellow floribunda rose garden',
            'purple grandiflora rose flower'
        ],
        'ficus' => [
            'rubber plant ficus elastica indoor',
            'weeping fig ficus benjamina',
            'fiddle leaf fig large leaves',
            'ficus lyrata bright green',
            'ficus microcarpa bonsai tree'
        ],
        'plastic' => [
            'decorative plant pot container',
            'garden planter ceramic round',
            'terracotta flower pot clay',
            'modern plant container white',
            'rustic garden pot outdoor'
        ]
    ];
    
    // Find matching plant type
    $search_terms = [];
    foreach ($plant_specific_terms as $plant => $variations) {
        if (strpos($name_lower, $plant) !== false) {
            // Use product ID to select a specific variation
            $variation_index = $product_id % count($variations);
            $search_terms[] = $variations[$variation_index];
            break;
        }
    }
    
    // If no specific match, create unique combinations
    if (empty($search_terms)) {
        $base_terms = [
            'tropical houseplant green indoor',
            'exotic garden plant colorful',
            'botanical specimen rare beautiful',
            'ornamental flowering plant bright',
            'succulent desert plant small',
            'herb aromatic garden fresh',
            'shrub landscape bush outdoor',
            'perennial flower garden bloom',
            'annual seasonal plant colorful',
            'vine climbing plant green'
        ];
        
        $variation_index = $product_id % count($base_terms);
        $search_terms[] = $base_terms[$variation_index];
    }
    
    // Add unique modifier based on product ID for absolute uniqueness
    $unique_modifiers = [
        'variety A premium',
        'cultivar B special',
        'species C rare',
        'hybrid D unique',
        'selection E elite',
        'strain F superior',
        'form G distinctive',
        'type H exclusive',
        'grade I exceptional',
        'class J magnificent'
    ];
    
    $modifier_index = $product_id % count($unique_modifiers);
    $search_terms[0] .= ' ' . $unique_modifiers[$modifier_index];
    
    return $search_terms;
}

/**
 * Get unique image from Unsplash with pagination for variety
 */
function get_guaranteed_unique_unsplash($search_term, $api_key, $page = 1) {
    global $used_image_hashes;
    
    // Try multiple pages to ensure uniqueness
    for ($current_page = $page; $current_page <= $page + 5; $current_page++) {
        $encoded_term = urlencode($search_term);
        $url = "https://api.unsplash.com/search/photos?query={$encoded_term}&per_page=20&page={$current_page}&orientation=squarish";
        
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
            if (isset($data['results']) && is_array($data['results'])) {
                foreach ($data['results'] as $image) {
                    $image_url = $image['urls']['regular'] ?? null;
                    if ($image_url) {
                        $image_hash = get_image_hash($image_url);
                        if ($image_hash && !in_array($image_hash, $used_image_hashes)) {
                            $used_image_hashes[] = $image_hash;
                            return $image_url;
                        }
                    }
                }
            }
        }
        usleep(500000); // 0.5 second delay between pages
    }
    
    return false;
}

/**
 * Get unique image from Pexels with pagination
 */
function get_guaranteed_unique_pexels($search_term, $api_key, $page = 1) {
    global $used_image_hashes;
    
    for ($current_page = $page; $current_page <= $page + 5; $current_page++) {
        $encoded_term = urlencode($search_term);
        $url = "https://api.pexels.com/v1/search?query={$encoded_term}&per_page=20&page={$current_page}&orientation=square";
        
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
            if (isset($data['photos']) && is_array($data['photos'])) {
                foreach ($data['photos'] as $image) {
                    $image_url = $image['src']['large'] ?? null;
                    if ($image_url) {
                        $image_hash = get_image_hash($image_url);
                        if ($image_hash && !in_array($image_hash, $used_image_hashes)) {
                            $used_image_hashes[] = $image_hash;
                            return $image_url;
                        }
                    }
                }
            }
        }
        usleep(500000); // 0.5 second delay
    }
    
    return false;
}

/**
 * Download and assign guaranteed unique image
 */
function assign_guaranteed_unique_image($product_id, $image_url) {
    $image_data = @file_get_contents($image_url);
    if (!$image_data) return false;
    
    $image = @imagecreatefromstring($image_data);
    if (!$image) return false;
    
    // Create absolutely unique filename
    $microtime = microtime(true);
    $random = mt_rand(10000, 99999);
    $filename = "unique_enforced_{$product_id}_{$microtime}_{$random}.jpg";
    
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
        'post_title' => "Unique enforced image for product {$product_id}",
        'post_content' => '',
        'post_status' => 'inherit'
    ];
    
    $attachment_id = wp_insert_attachment($attachment, $file_path);
    if (!$attachment_id) return false;
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
    wp_update_attachment_metadata($attachment_id, $attachment_metadata);
    
    // Remove old featured image
    $old_image_id = get_post_thumbnail_id($product_id);
    if ($old_image_id) {
        delete_post_thumbnail($product_id);
    }
    
    // Set new unique image
    set_post_thumbnail($product_id, $attachment_id);
    
    return true;
}

// STEP 1: Build current image usage map
echo "<h2>🔍 STEP 1: Analyzing current image usage</h2>\n";

$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$image_usage = [];
$duplicates_found = [];

foreach ($products as $product) {
    $product_id = $product->get_id();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        if ($image_url && strpos($image_url, '.svg') === false) {
            $image_hash = get_image_hash($image_url);
            if ($image_hash) {
                if (!isset($image_usage[$image_hash])) {
                    $image_usage[$image_hash] = [];
                }
                $image_usage[$image_hash][] = $product_id;
            }
        }
    }
}

// Find duplicates
foreach ($image_usage as $hash => $product_ids) {
    if (count($product_ids) > 1) {
        $duplicates_found[$hash] = $product_ids;
    }
}

$duplicate_groups = count($duplicates_found);
$products_affected = 0;
foreach ($duplicates_found as $products) {
    $products_affected += count($products);
}

echo "<p><strong>🚨 Duplicate groups found:</strong> {$duplicate_groups}</p>\n";
echo "<p><strong>📦 Products affected:</strong> {$products_affected}</p>\n";

if ($duplicate_groups === 0) {
    echo "<h2>✅ NO DUPLICATES FOUND - All images are already unique!</h2>\n";
    exit;
}

// STEP 2: Fix all duplicates
echo "<h2>🔧 STEP 2: Enforcing unique images for ALL duplicates</h2>\n";

$start_time = time();

foreach ($duplicates_found as $hash => $product_ids) {
    echo "<h3>🔄 Fixing duplicate group with " . count($product_ids) . " products</h3>\n";
    
    // Keep first product, replace others
    $products_to_fix = array_slice($product_ids, 1);
    
    foreach ($products_to_fix as $product_id) {
        $product = wc_get_product($product_id);
        if (!$product) continue;
        
        $product_name = $product->get_name();
        echo "<p><strong>🔧 Fixing:</strong> {$product_name} (ID: {$product_id})</p>\n";
        
        $search_terms = generate_ultra_unique_search($product_name, $product_id);
        $success = false;
        
        foreach ($search_terms as $search_term) {
            // Try Unsplash first
            if (!empty($unsplash_key)) {
                $page = ($product_id % 10) + 1; // Different page for each product
                $image_url = get_guaranteed_unique_unsplash($search_term, $unsplash_key, $page);
                if ($image_url && assign_guaranteed_unique_image($product_id, $image_url)) {
                    echo "<p>✅ SUCCESS - Unique Unsplash image assigned</p>\n";
                    $fixed_duplicates++;
                    $success = true;
                    break;
                }
            }
            
            // Try Pexels as backup
            if (!$success && !empty($pexels_key)) {
                $page = ($product_id % 15) + 1; // Different page for variety
                $image_url = get_guaranteed_unique_pexels($search_term, $pexels_key, $page);
                if ($image_url && assign_guaranteed_unique_image($product_id, $image_url)) {
                    echo "<p>✅ SUCCESS - Unique Pexels image assigned</p>\n";
                    $fixed_duplicates++;
                    $success = true;
                    break;
                }
            }
        }
        
        if (!$success) {
            echo "<p>❌ FAILED - Could not find unique image</p>\n";
        }
        
        $processed_products++;
        flush();
        sleep(2); // API rate limiting
    }
    
    echo "<hr>\n";
}

$total_time = time() - $start_time;

// STEP 3: Final verification
echo "<h2>🔍 STEP 3: Final verification</h2>\n";

$final_verification = [];
foreach ($products as $product) {
    $product_id = $product->get_id();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        if ($image_url && strpos($image_url, '.svg') === false) {
            $image_hash = get_image_hash($image_url);
            if ($image_hash) {
                if (!isset($final_verification[$image_hash])) {
                    $final_verification[$image_hash] = [];
                }
                $final_verification[$image_hash][] = $product_id;
            }
        }
    }
}

$remaining_duplicates = 0;
foreach ($final_verification as $hash => $product_ids) {
    if (count($product_ids) > 1) {
        $remaining_duplicates++;
    }
}

echo "<h2>📊 FINAL RESULTS</h2>\n";
echo "<p><strong>⏱️ Processing time:</strong> " . gmdate("H:i:s", $total_time) . "</p>\n";
echo "<p><strong>🔧 Products processed:</strong> {$processed_products}</p>\n";
echo "<p><strong>✅ Duplicates fixed:</strong> {$fixed_duplicates}</p>\n";
echo "<p><strong>🚨 Remaining duplicates:</strong> {$remaining_duplicates} groups</p>\n";

if ($remaining_duplicates === 0) {
    echo "<div style='background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 8px; margin: 20px 0;'>\n";
    echo "<h3>🎉 MISSION ACCOMPLISHED!</h3>\n";
    echo "<p><strong>✅ ALL IMAGES ARE NOW UNIQUE</strong></p>\n";
    echo "<p>🌟 Every product has its own distinctive image</p>\n";
    echo "<p>🛒 Ready for production with professional appearance</p>\n";
    echo "</div>\n";
} else {
    echo "<div style='background: #fff3cd; padding: 20px; border: 1px solid #ffeaa7; border-radius: 8px; margin: 20px 0;'>\n";
    echo "<h3>⚠️ Some duplicates may remain</h3>\n";
    echo "<p>Run this script again to process remaining duplicates</p>\n";
    echo "</div>\n";
}

echo "<h3>🌐 <a href='http://localhost:8080/tienda/' target='_blank'>Verify results in the shop</a></h3>\n";
echo "<p><em>Emergency fix completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>