<?php
/**
 * 🌱 Los Cocos - Complete Real Image Automation
 * =============================================
 * 
 * This script completes the real image automation for ALL products
 * - Processes all remaining products with SVG placeholders
 * - Uses enhanced search terms and multiple API sources
 * - Provides progress tracking and error handling
 * - Creates final verification report
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

// Configuration
$UNSPLASH_API_KEY = 'YjiiXP_kb4z7yhpBMrK3OWeWx1jf_VQrzl37VfFssLY';
$PEXELS_API_KEY = 'Ru0qn9ob5D5XqNyzacPTNoZiaTzfbmjRrHduXFisV4G97BADZ4EDBNiw';
$BATCH_SIZE = 100; // Process 100 products at a time
$DELAY_SECONDS = 1; // Delay between API calls

echo "<h1>🌱 Los Cocos - Complete Real Image Automation</h1>\n";
echo "<p><strong>Procesando TODOS los productos restantes con imágenes SVG...</strong></p>\n";

// Enhanced plant translations
$plant_translations = [
    // Basic plants
    'jazmin' => 'jasmine flower white',
    'rosa' => 'rose flower garden',
    'begonia' => 'begonia colorful flower',
    'ficus' => 'ficus houseplant green',
    'dracaena' => 'dracaena indoor plant',
    'nandina' => 'nandina shrub berries',
    'eucalyptus' => 'eucalyptus tree leaves',
    'liquidambar' => 'liquidambar autumn tree',
    'fresno' => 'ash tree leaves',
    'abedul' => 'birch tree white bark',
    'acacia' => 'acacia yellow flowers',
    'acer' => 'maple tree colorful',
    'brachichiton' => 'bottle tree flowers',
    'agave' => 'agave succulent plant',
    'strelizia' => 'bird of paradise flower',
    'forsythia' => 'forsythia yellow flowers',
    'laurel' => 'laurel evergreen shrub',
    'thuja' => 'thuja evergreen tree',
    'callistemon' => 'bottlebrush red flowers',
    'evonymus' => 'euonymus colorful shrub',
    'oleander' => 'oleander pink flowers',
    
    // Plant containers and tools
    'plastic' => 'colorful plastic pot',
    'jardinera' => 'decorative planter box',
    'maceta' => 'terracotta flower pot',
    'matri' => 'ceramic garden pot',
    'bols' => 'decorative bowl planter',
    'monaco' => 'elegant plant container',
    'rocio' => 'modern plant pot',
    'cultivo' => 'growing container',
    'redonda' => 'round garden pot',
    'cuadrada' => 'square planter',
    'erika' => 'stylish flower pot',
    'owen' => 'designer plant pot',
    'finny' => 'decorative container',
    'paris' => 'striped plant pot',
    
    // Generic terms
    'planta' => 'beautiful houseplant',
    'plantas' => 'garden plants',
    'arbusto' => 'flowering shrub',
    'árbol' => 'garden tree',
    'interior' => 'indoor houseplant',
    'exterior' => 'outdoor garden plant',
    'vivero' => 'nursery plant care'
];

// Function to generate enhanced search terms
function generate_enhanced_search_terms($product_name, $translations) {
    $name_lower = strtolower($product_name);
    $search_terms = [];
    
    // Plant name recognition
    foreach ($translations as $spanish => $english) {
        if (strpos($name_lower, $spanish) !== false) {
            $search_terms[] = $english;
            // Add variations
            if (strpos($spanish, 'jazmin') !== false) {
                $search_terms[] = 'white jasmine plant';
                $search_terms[] = 'jasmine bush garden';
            } elseif (strpos($spanish, 'rosa') !== false) {
                $search_terms[] = 'pink rose garden';
                $search_terms[] = 'rose bush flowering';
            }
            break;
        }
    }
    
    // Container type detection with colors
    if (preg_match('/plastic.*?(negro|negra|black)/i', $name_lower)) {
        $search_terms[] = 'black plastic garden pot';
    } elseif (preg_match('/plastic.*?(blanco|blanca|white)/i', $name_lower)) {
        $search_terms[] = 'white plastic flower pot';
    } elseif (preg_match('/plastic.*?(verde|green)/i', $name_lower)) {
        $search_terms[] = 'green plastic plant pot';
    } elseif (strpos($name_lower, 'plastic') !== false) {
        $search_terms[] = 'colorful plastic garden pot';
        $search_terms[] = 'modern plant container';
    }
    
    // Size-based terms
    if (preg_match('/(\d+)\s*cm/', $product_name, $matches)) {
        $size = intval($matches[1]);
        if ($size >= 40) {
            $search_terms[] = 'large garden planter';
        } elseif ($size >= 25) {
            $search_terms[] = 'medium flower pot';
        } else {
            $search_terms[] = 'small decorative pot';
        }
    }
    
    // Default fallbacks
    if (empty($search_terms)) {
        if (strpos($name_lower, 'l') !== false) { // Likely a plant (liters)
            $search_terms[] = 'beautiful garden plant';
            $search_terms[] = 'potted plant nursery';
        } else {
            $search_terms[] = 'garden container';
            $search_terms[] = 'plant pot decoration';
        }
    }
    
    return array_unique($search_terms);
}

// Enhanced image search functions
function search_unsplash_enhanced($query, $api_key) {
    $url = "https://api.unsplash.com/search/photos?query=" . urlencode($query) . "&per_page=10&orientation=squarish&order_by=relevant";
    $headers = [
        "Authorization: Client-ID $api_key",
        "User-Agent: LosCocos-EnhancedBot/1.0"
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
            'timeout' => 15
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    if ($response === false) return [];
    
    $data = json_decode($response, true);
    $images = [];
    
    if (isset($data['results'])) {
        foreach ($data['results'] as $result) {
            if ($result['width'] >= 800 && $result['height'] >= 600) {
                $score = $result['likes'] + ($result['width'] * $result['height'] / 1000000);
                $images[] = [
                    'url' => $result['urls']['regular'],
                    'width' => $result['width'],
                    'height' => $result['height'],
                    'source' => 'unsplash',
                    'score' => $score
                ];
            }
        }
    }
    
    // Sort by quality score
    usort($images, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });
    
    return $images;
}

function search_pexels_enhanced($query, $api_key) {
    $url = "https://api.pexels.com/v1/search?query=" . urlencode($query) . "&per_page=10&orientation=square";
    $headers = [
        "Authorization: $api_key",
        "User-Agent: LosCocos-EnhancedBot/1.0"
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
            'timeout' => 15
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    if ($response === false) return [];
    
    $data = json_decode($response, true);
    $images = [];
    
    if (isset($data['photos'])) {
        foreach ($data['photos'] as $photo) {
            if ($photo['width'] >= 800 && $photo['height'] >= 600) {
                $score = ($photo['width'] * $photo['height'] / 1000000) + 2; // Base score for Pexels
                $images[] = [
                    'url' => $photo['src']['large'],
                    'width' => $photo['width'],
                    'height' => $photo['height'],
                    'source' => 'pexels',
                    'score' => $score
                ];
            }
        }
    }
    
    return $images;
}

// Enhanced image download and processing
function download_and_process_image_enhanced($image_url, $product_name) {
    $upload_dir = wp_upload_dir();
    $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $product_name);
    $filename = $safe_name . '_real_' . time() . '.jpg';
    $file_path = $upload_dir['path'] . '/' . $filename;
    
    // Download with retry logic
    $max_retries = 3;
    $image_data = false;
    
    for ($i = 0; $i < $max_retries; $i++) {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: LosCocos-EnhancedBot/1.0\r\n",
                'timeout' => 30
            ]
        ]);
        
        $image_data = @file_get_contents($image_url, false, $context);
        if ($image_data !== false) break;
        
        sleep(1); // Wait before retry
    }
    
    if ($image_data === false) return false;
    
    // Save and process
    if (file_put_contents($file_path, $image_data) === false) return false;
    
    // Enhanced image processing with GD
    if (extension_loaded('gd')) {
        $image_info = getimagesize($file_path);
        if ($image_info !== false) {
            $source = false;
            
            switch ($image_info[2]) {
                case IMAGETYPE_JPEG:
                    $source = imagecreatefromjpeg($file_path);
                    break;
                case IMAGETYPE_PNG:
                    $source = imagecreatefrompng($file_path);
                    break;
                case IMAGETYPE_WEBP:
                    $source = imagecreatefromwebp($file_path);
                    break;
            }
            
            if ($source !== false) {
                // Create high-quality 1200x1200 image
                $size = 1200;
                $resized = imagecreatetruecolor($size, $size);
                
                // Enable alpha blending for transparency
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                
                // Create white background
                $white = imagecolorallocate($resized, 255, 255, 255);
                imagefill($resized, 0, 0, $white);
                
                // Copy and resize image
                imagecopyresampled($resized, $source, 0, 0, 0, 0, 
                                 $size, $size, imagesx($source), imagesy($source));
                
                // Save as high-quality JPEG
                imagejpeg($resized, $file_path, 95);
                
                imagedestroy($source);
                imagedestroy($resized);
            }
        }
    }
    
    return $file_path;
}

// Main execution starts here
echo "<h2>🔍 Identificando productos pendientes...</h2>\n";

// Get all products with SVG images
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$svg_products = [];

foreach ($products as $product) {
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        if (strpos($image_url, 'placeholder.svg') !== false || pathinfo($image_url, PATHINFO_EXTENSION) === 'svg') {
            $svg_products[] = $product;
        }
    } else {
        $svg_products[] = $product;
    }
}

$total_svg = count($svg_products);
echo "<p><strong>Productos con SVG pendientes:</strong> $total_svg</p>\n";

if ($total_svg === 0) {
    echo "<p>✅ <strong>¡Todos los productos ya tienen imágenes reales!</strong></p>\n";
    exit;
}

echo "<h2>🚀 Procesando productos en lotes de $BATCH_SIZE...</h2>\n";

$processed = 0;
$success = 0;
$errors = 0;
$start_time = time();

// Process in batches
for ($batch = 0; $batch < ceil($total_svg / $BATCH_SIZE); $batch++) {
    $batch_start = $batch * $BATCH_SIZE;
    $batch_products = array_slice($svg_products, $batch_start, $BATCH_SIZE);
    
    echo "<h3>📦 Lote " . ($batch + 1) . ": Procesando productos " . ($batch_start + 1) . " - " . min($batch_start + $BATCH_SIZE, $total_svg) . "</h3>\n";
    
    foreach ($batch_products as $product) {
        $product_id = $product->get_id();
        $product_name = $product->get_name();
        
        echo "<p><strong>🔄 $product_name (ID: $product_id)</strong></p>\n";
        
        try {
            // Generate enhanced search terms
            $search_terms = generate_enhanced_search_terms($product_name, $plant_translations);
            
            $best_image = null;
            $best_score = 0;
            
            // Search across multiple terms and sources
            foreach (array_slice($search_terms, 0, 3) as $term) { // Limit to 3 terms for speed
                // Try Unsplash first
                $unsplash_images = search_unsplash_enhanced($term, $UNSPLASH_API_KEY);
                foreach ($unsplash_images as $img) {
                    if ($img['score'] > $best_score) {
                        $best_image = $img;
                        $best_score = $img['score'];
                    }
                }
                
                // Try Pexels
                $pexels_images = search_pexels_enhanced($term, $PEXELS_API_KEY);
                foreach ($pexels_images as $img) {
                    if ($img['score'] > $best_score) {
                        $best_image = $img;
                        $best_score = $img['score'];
                    }
                }
                
                // If we have a good image, no need to search more
                if ($best_score > 50) break;
                
                usleep(500000); // 0.5 second delay between API calls
            }
            
            if (!$best_image) {
                echo "<p>⚠️ No se encontró imagen</p>\n";
                $errors++;
                continue;
            }
            
            // Download and process
            $local_file = download_and_process_image_enhanced($best_image['url'], $product_name);
            if (!$local_file) {
                echo "<p>❌ Error descargando</p>\n";
                $errors++;
                continue;
            }
            
            // Create WordPress attachment
            $attachment_id = create_wordpress_attachment($local_file, $product_name);
            if (!$attachment_id) {
                echo "<p>❌ Error creando attachment</p>\n";
                unlink($local_file);
                $errors++;
                continue;
            }
            
            // Set as featured image
            set_post_thumbnail($product_id, $attachment_id);
            
            echo "<p>✅ Éxito - {$best_image['source']} ({$best_image['width']}x{$best_image['height']})</p>\n";
            $success++;
            
        } catch (Exception $e) {
            echo "<p>❌ Error: " . $e->getMessage() . "</p>\n";
            $errors++;
        }
        
        $processed++;
        
        // Progress update every 10 products
        if ($processed % 10 === 0) {
            $elapsed = time() - $start_time;
            $rate = $processed / max($elapsed, 1);
            $eta = round(($total_svg - $processed) / max($rate, 0.1));
            
            echo "<hr>\n";
            echo "<p><strong>Progreso:</strong> $processed/$total_svg | <strong>Éxitos:</strong> $success | <strong>Errores:</strong> $errors</p>\n";
            echo "<p><strong>Tiempo transcurrido:</strong> {$elapsed}s | <strong>ETA:</strong> {$eta}s</p>\n";
            echo "<hr>\n";
            flush();
        }
        
        // Rate limiting
        sleep($DELAY_SECONDS);
    }
    
    echo "<p>✅ <strong>Lote " . ($batch + 1) . " completado</strong></p>\n";
    flush();
}

// Final results
$total_time = time() - $start_time;
$success_rate = round(($success / $processed) * 100, 1);

echo "<h2>🎉 AUTOMATIZACIÓN COMPLETA FINALIZADA</h2>\n";
echo "================================================\n";
echo "<p><strong>Total procesados:</strong> $processed</p>\n";
echo "<p><strong>✅ Éxitos:</strong> $success</p>\n";
echo "<p><strong>❌ Errores:</strong> $errors</p>\n";
echo "<p><strong>📈 Tasa de éxito:</strong> {$success_rate}%</p>\n";
echo "<p><strong>⏱️ Tiempo total:</strong> {$total_time} segundos</p>\n";

if ($success > 0) {
    echo "<h3>🌟 ¡MISIÓN CUMPLIDA!</h3>\n";
    echo "<p><strong>$success productos ahora tienen imágenes reales originales</strong> de alta calidad obtenidas de Unsplash y Pexels.</p>\n";
    
    echo "<h3>📋 Características de las imágenes:</h3>\n";
    echo "<ul>\n";
    echo "<li>✅ Resolución: 1200x1200 píxeles</li>\n";
    echo "<li>✅ Formato: JPEG de alta calidad (95%)</li>\n";
    echo "<li>✅ Fuentes: Unsplash y Pexels (profesionales)</li>\n";
    echo "<li>✅ Búsqueda inteligente con términos específicos</li>\n";
    echo "<li>✅ Procesamiento optimizado para web</li>\n";
    echo "</ul>\n";
}

echo "<p><em>Procesamiento completado: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>