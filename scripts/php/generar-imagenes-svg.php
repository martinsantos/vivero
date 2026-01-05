<?php
/**
 * 🖼️ Generador de Imágenes SVG para Los Cocos
 * Soluciona los errores 404 en imágenes placeholder
 */

// Configuración
define('WP_USE_THEMES', false);
require_once('wp-load.php');

echo "🖼️ ================================================\n";
echo "🖼️ GENERADOR DE IMÁGENES SVG - LOS COCOS\n";
echo "🖼️ ================================================\n\n";

// Obtener todos los productos
$products = get_posts([
    'post_type' => 'product',
    'numberposts' => -1,
    'post_status' => 'publish'
]);

echo "📦 Productos encontrados: " . count($products) . "\n\n";

// Crear directorio de imágenes
$upload_dir = wp_upload_dir();
$images_dir = $upload_dir['basedir'] . '/loscocos-images';

if (!file_exists($images_dir)) {
    wp_mkdir_p($images_dir);
    echo "📁 Directorio creado: $images_dir\n";
} else {
    echo "📁 Directorio existente: $images_dir\n";
}

// Colores para gradientes
$colors = [
    ['#10b981', '#059669'], // Verde
    ['#3b82f6', '#1d4ed8'], // Azul
    ['#8b5cf6', '#7c3aed'], // Púrpura
    ['#f59e0b', '#d97706'], // Amarillo
    ['#ef4444', '#dc2626'], // Rojo
    ['#06b6d4', '#0891b2'], // Cian
    ['#84cc16', '#65a30d'], // Lima
    ['#f97316', '#ea580c'], // Naranja
    ['#ec4899', '#db2777'], // Rosa
    ['#6b7280', '#4b5563'], // Gris
];

// Emojis para productos
$emojis = ['🌱', '🌿', '🍃', '🌾', '🌳', '🌲', '🌴', '🌵', '🌺', '🌻', '🌼', '🌷', '🌹', '🥀', '🌸'];

$generated = 0;
$skipped = 0;
$errors = 0;

foreach ($products as $product) {
    $product_id = $product->ID;
    $product_obj = wc_get_product($product_id);
    
    if (!$product_obj) {
        echo "❌ Error: No se pudo cargar producto ID $product_id\n";
        $errors++;
        continue;
    }
    
    $image_path = $images_dir . '/' . $product_id . '.svg';
    
    // Si la imagen ya existe, omitir
    if (file_exists($image_path)) {
        echo "⏭️  Imagen ya existe para producto $product_id\n";
        $skipped++;
        continue;
    }
    
    // Obtener datos del producto
    $name = $product_obj->get_name();
    $price = $product_obj->get_price();
    $short_description = $product_obj->get_short_description();
    
    // Seleccionar colores y emoji aleatorios
    $color_pair = $colors[array_rand($colors)];
    $emoji = $emojis[array_rand($emojis)];
    
    // Generar nombre corto para mostrar
    $display_name = mb_strlen($name) > 15 ? mb_substr($name, 0, 15) . '...' : $name;
    
    // Crear SVG
    $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="280" height="280" viewBox="0 0 280 280" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="grad-' . $product_id . '" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="' . $color_pair[0] . '" />
            <stop offset="100%" stop-color="' . $color_pair[1] . '" />
        </linearGradient>
        <filter id="shadow-' . $product_id . '" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="2" dy="2" stdDeviation="3" flood-color="rgba(0,0,0,0.3)"/>
        </filter>
    </defs>
    
    <!-- Fondo con gradiente -->
    <rect width="100%" height="100%" fill="url(#grad-' . $product_id . ')" rx="16" ry="16"/>
    
    <!-- Patrón decorativo -->
    <circle cx="50" cy="50" r="30" fill="rgba(255,255,255,0.1)"/>
    <circle cx="230" cy="80" r="20" fill="rgba(255,255,255,0.1)"/>
    <circle cx="200" cy="200" r="25" fill="rgba(255,255,255,0.1)"/>
    <circle cx="80" cy="220" r="15" fill="rgba(255,255,255,0.1)"/>
    
    <!-- Emoji principal -->
    <text x="50%" y="45%" dominant-baseline="middle" text-anchor="middle" 
          font-size="64" filter="url(#shadow-' . $product_id . ')">' . $emoji . '</text>
    
    <!-- Nombre del producto -->
    <text x="50%" y="70%" dominant-baseline="middle" text-anchor="middle" 
          font-family="system-ui, -apple-system, sans-serif" font-size="14" 
          fill="white" font-weight="600" filter="url(#shadow-' . $product_id . ')">' . htmlspecialchars($display_name) . '</text>
    
    <!-- Precio -->
    <text x="50%" y="85%" dominant-baseline="middle" text-anchor="middle" 
          font-family="system-ui, -apple-system, sans-serif" font-size="18" 
          fill="white" font-weight="bold" filter="url(#shadow-' . $product_id . ')">$' . number_format($price, 0) . '</text>
    
    <!-- Indicador "Los Cocos" -->
    <text x="50%" y="95%" dominant-baseline="middle" text-anchor="middle" 
          font-family="system-ui, -apple-system, sans-serif" font-size="10" 
          fill="rgba(255,255,255,0.8)" font-weight="400">Los Cocos</text>
</svg>';
    
    // Guardar archivo
    if (file_put_contents($image_path, $svg)) {
        echo "✅ Imagen generada: $product_id.svg ($display_name)\n";
        $generated++;
    } else {
        echo "❌ Error al generar imagen para producto $product_id\n";
        $errors++;
    }
}

echo "\n🖼️ ================================================\n";
echo "🖼️ RESUMEN DE GENERACIÓN\n";
echo "🖼️ ================================================\n";
echo "✅ Imágenes generadas: $generated\n";
echo "⏭️  Imágenes omitidas: $skipped\n";
echo "❌ Errores: $errors\n";
echo "📊 Total procesado: " . count($products) . "\n";

// Verificar algunas imágenes generadas
echo "\n🔍 VERIFICACIÓN DE IMÁGENES GENERADAS:\n";
$sample_products = array_slice($products, 0, 5);
foreach ($sample_products as $product) {
    $image_path = $images_dir . '/' . $product->ID . '.svg';
    $image_url = $upload_dir['baseurl'] . '/loscocos-images/' . $product->ID . '.svg';
    
    if (file_exists($image_path)) {
        $file_size = filesize($image_path);
        echo "✅ $product->ID.svg - $file_size bytes - $image_url\n";
    } else {
        echo "❌ $product->ID.svg - No encontrada\n";
    }
}

echo "\n🌐 URLs DE PRUEBA:\n";
echo "📁 Directorio: " . $upload_dir['baseurl'] . "/loscocos-images/\n";
echo "🖼️ Ejemplo: " . $upload_dir['baseurl'] . "/loscocos-images/1.svg\n";

echo "\n🎉 ¡Proceso completado!\n";
echo "Ahora las imágenes SVG deberían cargar correctamente y no habrá más errores 404.\n";
?>