<?php
/**
 * 🌱 Los Cocos - Global Real Image Association Test
 * ==================================================
 * 
 * This script performs comprehensive testing to associate real images with products
 * - Identifies products with SVG placeholder images
 * - Downloads real images from multiple sources (Unsplash, Pexels)
 * - Associates images with products in WordPress
 * - Provides detailed progress reporting
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🌱 Los Cocos - Global Real Image Association Test</h1>\n";
echo "<p><strong>Objetivo:</strong> Asociar imágenes reales a todos los productos que tienen SVG placeholders</p>\n";

// Configuration
$UNSPLASH_API_KEY = 'YjiiXP_kb4z7yhpBMrK3OWeWx1jf_VQrzl37VfFssLY';
$PEXELS_API_KEY = 'Ru0qn9ob5D5XqNyzacPTNoZiaTzfbmjRrHduXFisV4G97BADZ4EDBNiw';

// Spanish to English translations for plants
$plant_translations = [
    'jazmin' => 'jasmine plant',
    'rosa' => 'rose plant',
    'begonia' => 'begonia flower',
    'ficus' => 'ficus plant',
    'dracaena' => 'dracaena plant',
    'nandina' => 'nandina plant',
    'eucalyptus' => 'eucalyptus tree',
    'liquidambar' => 'liquidambar tree',
    'fresno' => 'ash tree',
    'abedul' => 'birch tree',
    'acacia' => 'acacia tree',
    'planta' => 'plant',
    'arbusto' => 'shrub',
    'árbol' => 'tree',
    'interior' => 'indoor houseplant',
    'exterior' => 'outdoor garden plant',
    'maceta' => 'potted plant',
    'jardinera' => 'planter pot'
];

// Function to generate search terms for a product
function generate_search_terms($product_name, $translations) {
    $name_lower = strtolower($product_name);
    $search_terms = [];
    
    // Look for plant names
    foreach ($translations as $spanish => $english) {
        if (strpos($name_lower, $spanish) !== false) {
            $search_terms[] = $english;
            break;
        }
    }
    
    // Container detection
    if (strpos($name_lower, 'plastic') !== false || strpos($name_lower, 'jardinera') !== false) {
        $search_terms[] = 'garden pot';
        $search_terms[] = 'plant container';
    }
    
    // Default fallback
    if (empty($search_terms)) {
        $search_terms[] = 'plant';
        $search_terms[] = 'garden plant';
    }
    
    return array_unique($search_terms);
}

// Function to search Unsplash for images
function search_unsplash($query, $api_key) {
    $url = "https://api.unsplash.com/search/photos?query=" . urlencode($query) . "&per_page=5&orientation=squarish";
    $headers = [
        "Authorization: Client-ID $api_key",
        "User-Agent: LosCocos-ImageBot/1.0"
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        return [];
    }
    
    $data = json_decode($response, true);
    $images = [];
    
    if (isset($data['results'])) {
        foreach ($data['results'] as $result) {
            if ($result['width'] >= 800 && $result['height'] >= 600) {
                $images[] = [
                    'url' => $result['urls']['regular'],
                    'width' => $result['width'],
                    'height' => $result['height'],
                    'source' => 'unsplash'
                ];
            }
        }
    }
    
    return $images;
}

// Function to search Pexels for images
function search_pexels($query, $api_key) {
    $url = "https://api.pexels.com/v1/search?query=" . urlencode($query) . "&per_page=5&orientation=square";
    $headers = [
        "Authorization: $api_key",
        "User-Agent: LosCocos-ImageBot/1.0"
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        return [];
    }
    
    $data = json_decode($response, true);
    $images = [];
    
    if (isset($data['photos'])) {
        foreach ($data['photos'] as $photo) {
            if ($photo['width'] >= 800 && $photo['height'] >= 600) {
                $images[] = [
                    'url' => $photo['src']['large'],
                    'width' => $photo['width'],
                    'height' => $photo['height'],
                    'source' => 'pexels'
                ];
            }
        }
    }
    
    return $images;
}

// Function to download and process image
function download_and_process_image($image_url, $product_name) {
    $upload_dir = wp_upload_dir();
    
    // Create safe filename
    $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $product_name);
    $filename = $safe_name . '_' . time() . '.jpg';
    $file_path = $upload_dir['path'] . '/' . $filename;
    
    // Download image
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: LosCocos-ImageBot/1.0\r\n",
            'timeout' => 30
        ]
    ]);
    
    $image_data = @file_get_contents($image_url, false, $context);
    if ($image_data === false) {
        return false;
    }
    
    // Save temporary file
    if (file_put_contents($file_path, $image_data) === false) {
        return false;
    }
    
    // Process with GD if available
    if (extension_loaded('gd')) {
        $image_info = getimagesize($file_path);
        if ($image_info !== false) {
            // Create image from file
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
                default:
                    $source = false;
            }
            
            if ($source !== false) {
                // Resize to 1200x1200
                $target_width = 1200;
                $target_height = 1200;
                
                $resized = imagecreatetruecolor($target_width, $target_height);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, 
                                 $target_width, $target_height, 
                                 imagesx($source), imagesy($source));
                
                // Save as JPEG
                imagejpeg($resized, $file_path, 90);
                
                imagedestroy($source);
                imagedestroy($resized);
            }
        }
    }
    
    return $file_path;
}

// Function to create WordPress attachment
function create_wordpress_attachment($file_path, $product_name) {
    $filename = basename($file_path);
    $upload_dir = wp_upload_dir();
    
    // Create attachment data
    $attachment = [
        'guid' => $upload_dir['url'] . '/' . $filename,
        'post_mime_type' => 'image/jpeg',
        'post_title' => "Image for $product_name",
        'post_content' => '',
        'post_status' => 'inherit'
    ];
    
    // Insert attachment
    $attachment_id = wp_insert_attachment($attachment, $file_path);
    
    if ($attachment_id) {
        // Generate metadata
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attachment_id, $file_path);
        wp_update_attachment_metadata($attachment_id, $attach_data);
        
        return $attachment_id;
    }
    
    return false;
}

// Main execution
echo "<h2>🔍 Paso 1: Identificando productos con SVG placeholders</h2>\n";

// Get all products
$products = wc_get_products([
    'limit' => -1,
    'status' => 'publish'
]);

$svg_products = [];
$total_products = count($products);

foreach ($products as $product) {
    $product_id = $product->get_id();
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

echo "<p><strong>Total productos:</strong> $total_products</p>\n";
echo "<p><strong>Productos con SVG/sin imagen:</strong> " . count($svg_products) . "</p>\n";

if (empty($svg_products)) {
    echo "<p>✅ <strong>Todos los productos ya tienen imágenes reales!</strong></p>\n";
    exit;
}

echo "<h2>🔍 Paso 2: Procesando productos (máximo 50 en esta prueba)</h2>\n";

$processed_count = 0;
$success_count = 0;
$error_count = 0;
$max_process = 50; // Limit for testing

foreach (array_slice($svg_products, 0, $max_process) as $product) {
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    
    echo "<h3>📦 Procesando: $product_name (ID: $product_id)</h3>\n";
    
    try {
        // Generate search terms
        $search_terms = generate_search_terms($product_name, $plant_translations);
        echo "<p><strong>Términos de búsqueda:</strong> " . implode(', ', $search_terms) . "</p>\n";
        
        $best_image = null;
        
        // Search for images
        foreach ($search_terms as $term) {
            // Try Unsplash first
            $unsplash_images = search_unsplash($term, $UNSPLASH_API_KEY);
            if (!empty($unsplash_images)) {
                $best_image = $unsplash_images[0];
                echo "<p>✅ Encontrada imagen en Unsplash: {$best_image['url']}</p>\n";
                break;
            }
            
            // Try Pexels as backup
            $pexels_images = search_pexels($term, $PEXELS_API_KEY);
            if (!empty($pexels_images)) {
                $best_image = $pexels_images[0];
                echo "<p>✅ Encontrada imagen en Pexels: {$best_image['url']}</p>\n";
                break;
            }
            
            // Small delay between API calls
            usleep(500000); // 0.5 seconds
        }
        
        if (!$best_image) {
            echo "<p>⚠️ No se encontró imagen adecuada</p>\n";
            $error_count++;
            continue;
        }
        
        // Download and process image
        echo "<p>⬇️ Descargando imagen...</p>\n";
        $local_file = download_and_process_image($best_image['url'], $product_name);
        
        if (!$local_file) {
            echo "<p>❌ Error descargando imagen</p>\n";
            $error_count++;
            continue;
        }
        
        echo "<p>✅ Imagen descargada y procesada</p>\n";
        
        // Create WordPress attachment
        $attachment_id = create_wordpress_attachment($local_file, $product_name);
        
        if (!$attachment_id) {
            echo "<p>❌ Error creando attachment en WordPress</p>\n";
            unlink($local_file);
            $error_count++;
            continue;
        }
        
        echo "<p>✅ Attachment creado (ID: $attachment_id)</p>\n";
        
        // Set as product featured image
        set_post_thumbnail($product_id, $attachment_id);
        
        echo "<p>✅ <strong>Imagen real asociada exitosamente al producto!</strong></p>\n";
        echo "<p>🌟 <strong>Fuente:</strong> {$best_image['source']} | <strong>Resolución:</strong> {$best_image['width']}x{$best_image['height']}</p>\n";
        
        $success_count++;
        
    } catch (Exception $e) {
        echo "<p>❌ Error: " . $e->getMessage() . "</p>\n";
        $error_count++;
    }
    
    $processed_count++;
    
    // Progress indicator
    if ($processed_count % 5 == 0) {
        echo "<hr>\n";
        echo "<p><strong>Progreso:</strong> $processed_count/$max_process productos procesados</p>\n";
        echo "<p><strong>Éxitos:</strong> $success_count | <strong>Errores:</strong> $error_count</p>\n";
        echo "<hr>\n";
    }
    
    // Rate limiting
    sleep(2);
}

// Final summary
echo "<h2>📊 RESUMEN FINAL DEL TESTEO GLOBAL</h2>\n";
echo "=========================================\n";
echo "<p><strong>Total productos procesados:</strong> $processed_count</p>\n";
echo "<p><strong>✅ Éxitos:</strong> $success_count</p>\n";
echo "<p><strong>❌ Errores:</strong> $error_count</p>\n";
echo "<p><strong>📈 Tasa de éxito:</strong> " . round(($success_count / $processed_count) * 100, 1) . "%</p>\n";

if ($success_count > 0) {
    echo "<h3>🎉 ¡TESTEO EXITOSO!</h3>\n";
    echo "<p><strong>$success_count productos ahora tienen imágenes reales originales</strong> obtenidas de fuentes profesionales (Unsplash y Pexels).</p>\n";
    
    echo "<h3>📋 Próximos pasos:</h3>\n";
    echo "<ul>\n";
    echo "<li>✅ Las imágenes reales han sido asociadas correctamente</li>\n";
    echo "<li>🔄 Para procesar todos los " . count($svg_products) . " productos restantes, ejecutar el script completo</li>\n";
    echo "<li>🌐 Verificar que las imágenes se muestren correctamente en la tienda</li>\n";
    echo "</ul>\n";
} else {
    echo "<h3>⚠️ Revisar configuración</h3>\n";
    echo "<p>No se pudieron obtener imágenes. Verificar:</p>\n";
    echo "<ul>\n";
    echo "<li>Conexión a internet</li>\n";
    echo "<li>API keys de Unsplash y Pexels</li>\n";
    echo "<li>Permisos de escritura en wp-content/uploads</li>\n";
    echo "</ul>\n";
}

echo "<p><em>Testeo ejecutado: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>