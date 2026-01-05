<?php
/**
 * Force Unique Images V2 - Diversificación Agresiva
 * Asigna imágenes únicas a cada producto usando rotación de providers
 */

require_once('/home/viveroloscocos.com.ar/public_html/wp-load.php');

// Proveedores de imágenes
$providers = ['unsplash', 'pexels', 'inaturalist'];
$used_hashes = [];
$stats = ['success' => 0, 'skipped' => 0, 'failed' => 0];

function get_image_from_provider($query, $provider, $api_keys) {
    $query = urlencode($query);
    
    switch($provider) {
        case 'unsplash':
            $url = "https://api.unsplash.com/search/photos?query={$query}&per_page=30&client_id={$api_keys['unsplash']}";
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            if (!empty($data['results'])) {
                // Seleccionar imagen aleatoria
                $random = $data['results'][array_rand($data['results'])];
                return $random['urls']['regular'];
            }
            break;
            
        case 'pexels':
            $opts = [
                'http' => [
                    'header' => "Authorization: {$api_keys['pexels']}\r\n"
                ]
            ];
            $context = stream_context_create($opts);
            $url = "https://api.pexels.com/v1/search?query={$query}&per_page=30";
            $response = file_get_contents($url, false, $context);
            $data = json_decode($response, true);
            if (!empty($data['photos'])) {
                $random = $data['photos'][array_rand($data['photos'])];
                return $random['src']['large'];
            }
            break;
            
        case 'inaturalist':
            $url = "https://api.inaturalist.org/v1/observations?q={$query}&photo_license=cc0,cc-by,cc-by-sa&per_page=30";
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            if (!empty($data['results'])) {
                foreach($data['results'] as $obs) {
                    if (!empty($obs['photos'])) {
                        $photo = $obs['photos'][0];
                        return str_replace('/square.', '/original.', $photo['url']);
                    }
                }
            }
            break;
    }
    
    return null;
}

function download_and_upload_image($image_url, $product_id, $product_name) {
    // Descargar imagen
    $image_data = file_get_contents($image_url);
    if (!$image_data) return false;
    
    // Generar nombre único
    $filename = 'wcimg_' . round(microtime(true) * 1000) . '_' . $product_id . '.webp';
    
    // Guardar temporalmente
    $temp_file = '/tmp/' . $filename;
    file_put_contents($temp_file, $image_data);
    
    // Convertir a WebP si no lo es
    if (function_exists('imagewebp')) {
        $img = @imagecreatefromstring($image_data);
        if ($img) {
            imagewebp($img, $temp_file, 85);
            imagedestroy($img);
        }
    }
    
    // Upload a WordPress
    $upload_dir = wp_upload_dir();
    $target_file = $upload_dir['path'] . '/' . $filename;
    
    if (copy($temp_file, $target_file)) {
        unlink($temp_file);
        
        // Crear attachment
        $attachment = [
            'post_mime_type' => 'image/webp',
            'post_title' => sanitize_title($product_name),
            'post_content' => '',
            'post_status' => 'inherit'
        ];
        
        $attach_id = wp_insert_attachment($attachment, $target_file);
        
        if (!is_wp_error($attach_id)) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $target_file);
            wp_update_attachment_metadata($attach_id, $attach_data);
            
            return $attach_id;
        }
    }
    
    return false;
}

echo "=== FORCE UNIQUE IMAGES V2 ===\n";
echo "Iniciando...\n\n";

// API Keys
$api_keys = [
    'unsplash' => 'YjiiXP_kb4z7yhpBMrK3OWeWx1jf_VQrzl37VfFssLY',
    'pexels' => 'Ru0qn9ob5D5XqNyzacPTNoZiaTzfbmjRrHduXFisV4G97BADZ4EDBNiw'
];

// Obtener productos con imágenes duplicadas
$args = [
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'ID',
    'order' => 'ASC'
];

$products = get_posts($args);
$total = count($products);
$provider_index = 0;

echo "Total productos: {$total}\n\n";

foreach ($products as $index => $product) {
    $product_id = $product->ID;
    $product_name = $product->post_title;
    $current_image_id = get_post_thumbnail_id($product_id);
    
    // Verificar si la imagen actual está duplicada
    if ($current_image_id) {
        $current_file = get_attached_file($current_image_id);
        if ($current_file && file_exists($current_file)) {
            $current_hash = md5_file($current_file);
            
            // Si el hash ya se usó, reemplazar
            if (isset($used_hashes[$current_hash])) {
                echo "[{$index}/{$total}] Producto {$product_id}: {$product_name} - DUPLICADO detectado\n";
            } else {
                $used_hashes[$current_hash] = $product_id;
                echo "[{$index}/{$total}] Producto {$product_id}: OK (imagen única)\n";
                $stats['skipped']++;
                continue;
            }
        }
    }
    
    // Buscar nueva imagen única
    $max_attempts = 10;
    $attempts = 0;
    $new_image_id = false;
    
    // Preparar query de búsqueda
    $search_terms = explode(' ', $product_name);
    $search_query = implode(' ', array_slice($search_terms, 0, 3)); // Primeras 3 palabras
    
    while ($attempts < $max_attempts && !$new_image_id) {
        $provider = $providers[$provider_index % count($providers)];
        $provider_index++;
        
        echo "  → Intentando con {$provider}... ";
        
        $image_url = get_image_from_provider($search_query, $provider, $api_keys);
        
        if ($image_url) {
            // Descargar y verificar unicidad
            $temp_download = '/tmp/temp_check_' . time() . '.jpg';
            $img_data = file_get_contents($image_url);
            
            if ($img_data) {
                file_put_contents($temp_download, $img_data);
                $new_hash = md5_file($temp_download);
                unlink($temp_download);
                
                if (!isset($used_hashes[$new_hash])) {
                    // ¡Imagen única encontrada!
                    $new_image_id = download_and_upload_image($image_url, $product_id, $product_name);
                    
                    if ($new_image_id) {
                        set_post_thumbnail($product_id, $new_image_id);
                        $used_hashes[$new_hash] = $product_id;
                        echo "✅ ÉXITO\n";
                        $stats['success']++;
                        break;
                    }
                } else {
                    echo "duplicada, reintentando...\n";
                }
            }
        } else {
            echo "sin resultados\n";
        }
        
        $attempts++;
        usleep(500000); // 0.5 segundos entre intentos
    }
    
    if (!$new_image_id) {
        echo "  ❌ No se pudo encontrar imagen única después de {$max_attempts} intentos\n";
        $stats['failed']++;
    }
    
    // Delay entre productos
    sleep(2);
}

echo "\n=== RESULTADOS FINALES ===\n";
echo "Éxitos: {$stats['success']}\n";
echo "Saltados (ya únicos): {$stats['skipped']}\n";
echo "Fallos: {$stats['failed']}\n";
echo "\n✅ Proceso completado\n";
