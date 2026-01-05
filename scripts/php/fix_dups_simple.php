<?php
/**
 * Simple Duplicate Fixer
 * Identifica duplicados y asigna nuevas imágenes con paginación profunda
 */

require_once('/home/viveroloscocos.com.ar/public_html/wp-load.php');

echo "=== FIX DUPLICATES SIMPLE ===\n\n";

// Configuración
$UNSPLASH_KEY = "YjiiXP_kb4z7yhpBMrK3OWeWx1jf_VQrzl37VfFssLY";
$PEXELS_KEY = "Ru0qn9ob5D5XqNyzacPTNoZiaTzfbmjRrHduXFisV4G97BADZ4EDBNiw";

// Step 1: Analizar todas las imágenes actuales y sus hashes
echo "📊 Paso 1: Analizando imágenes actuales...\n";

$args = array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => -1
);

$products = get_posts($args);
$image_hashes = array();
$hash_to_products = array();

foreach ($products as $product) {
    $image_id = get_post_thumbnail_id($product->ID);
    if (!$image_id) continue;
    
    $image_path = get_attached_file($image_id);
    if (!$image_path || !file_exists($image_path)) continue;
    
    $hash = md5_file($image_path);
    
    if (!isset($hash_to_products[$hash])) {
        $hash_to_products[$hash] = array();
    }
    
    $hash_to_products[$hash][] = array(
        'id' => $product->ID,
        'title' => $product->post_title,
        'image_id' => $image_id
    );
}

// Step 2: Encontrar duplicados
$duplicates = array();
foreach ($hash_to_products as $hash => $prods) {
    if (count($prods) > 1) {
        $duplicates[$hash] = $prods;
    }
}

$total_unique = count($hash_to_products);
$total_duplicate_hashes = count($duplicates);
$total_products_to_fix = 0;
foreach ($duplicates as $prods) {
    $total_products_to_fix += (count($prods) - 1); // Mantener el primero
}

echo "Total hashes únicos:    $total_unique\n";
echo "Hashes duplicados:      $total_duplicate_hashes\n";
echo "Productos a corregir:   $total_products_to_fix\n\n";

if ($total_products_to_fix == 0) {
    echo "✅ No hay duplicados para corregir!\n";
    exit(0);
}

// Step 3: Corregir duplicados
echo "🔧 Paso 2: Corrigiendo duplicados...\n\n";

$fixed = 0;
$failed = 0;
$used_hashes = array_keys($hash_to_products);

foreach ($duplicates as $hash => $products_with_hash) {
    echo "Hash: " . substr($hash, 0, 16) . "... (" . count($products_with_hash) . " productos)\n";
    echo "  Manteniendo: #{$products_with_hash[0]['id']} - {$products_with_hash[0]['title']}\n";
    
    // Fix all except first
    for ($i = 1; $i < count($products_with_hash); $i++) {
        $product = $products_with_hash[$i];
        $pid = $product['id'];
        $pname = $product['title'];
        
        echo "  🔄 Corrigiendo: #$pid - " . substr($pname, 0, 40) . "...\n";
        
        // Try to find unique image
        $new_image_id = null;
        $providers = array('unsplash', 'pexels');
        
        for ($attempt = 0; $attempt < 20 && !$new_image_id; $attempt++) {
            $provider = $providers[$attempt % 2];
            $page = rand(1, 30); // Random deep page
            
            // Search query
            $words = explode(' ', $pname);
            $query = urlencode($words[0] . ' plant');
            
            $image_url = null;
            
            if ($provider == 'unsplash') {
                $api_url = "https://api.unsplash.com/search/photos?query=$query&page=$page&per_page=30&client_id=$UNSPLASH_KEY";
                $response = @file_get_contents($api_url);
                if ($response) {
                    $data = json_decode($response, true);
                    if (!empty($data['results'])) {
                        $random_idx = array_rand($data['results']);
                        $image_url = $data['results'][$random_idx]['urls']['regular'];
                    }
                }
            } else {
                $opts = array('http' => array('header' => "Authorization: $PEXELS_KEY\r\n"));
                $context = stream_context_create($opts);
                $api_url = "https://api.pexels.com/v1/search?query=$query&page=$page&per_page=30";
                $response = @file_get_contents($api_url, false, $context);
                if ($response) {
                    $data = json_decode($response, true);
                    if (!empty($data['photos'])) {
                        $random_idx = array_rand($data['photos']);
                        $image_url = $data['photos'][$random_idx]['src']['large'];
                    }
                }
            }
            
            if (!$image_url) continue;
            
            // Download
            $img_data = @file_get_contents($image_url);
            if (!$img_data) continue;
            
            // Check hash
            $temp_file = '/tmp/temp_check_' . time() . '_' . rand() . '.jpg';
            file_put_contents($temp_file, $img_data);
            $new_hash = md5_file($temp_file);
            
            if (in_array($new_hash, $used_hashes)) {
                unlink($temp_file);
                continue; // Duplicado, intentar otra
            }
            
            // ✅ Unique! Upload to WordPress
            $filename = 'wcimg_' . (microtime(true) * 1000) . '_' . $pid . '.webp';
            $upload_dir = wp_upload_dir();
            $target_file = $upload_dir['path'] . '/' . $filename;
            
            copy($temp_file, $target_file);
            unlink($temp_file);
            
            // Create attachment
            $attachment = array(
                'post_mime_type' => 'image/webp',
                'post_title' => sanitize_title($pname),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            
            $attach_id = wp_insert_attachment($attachment, $target_file);
            
            if (!is_wp_error($attach_id)) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata($attach_id, $target_file);
                wp_update_attachment_metadata($attach_id, $attach_data);
                
                // Assign to product
                set_post_thumbnail($pid, $attach_id);
                
                $used_hashes[] = $new_hash;
                $new_image_id = $attach_id;
                
                echo "    ✅ Éxito! (media $attach_id, intento $attempt)\n";
                $fixed++;
            }
            
            sleep(1);
        }
        
        if (!$new_image_id) {
            echo "    ❌ No se encontró imagen única después de 20 intentos\n";
            $failed++;
        }
        
        sleep(2);
    }
    
    echo "\n";
}

echo "\n=== RESUMEN FINAL ===\n";
echo "✅ Corregidos: $fixed\n";
echo "❌ Fallidos:   $failed\n";
echo "\n✅ Proceso completado!\n";
