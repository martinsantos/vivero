<?php
/**
 * 🔍 Quick Duplicate Detection Report
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🔍 Reporte de Imágenes Duplicadas</h1>\n";

function detect_duplicates() {
    $products = wc_get_products(['limit' => -1, 'status' => 'publish']);
    $image_urls = [];
    $duplicates = [];
    $svg_count = 0;
    $real_images_count = 0;
    
    foreach ($products as $product) {
        $product_id = $product->get_id();
        $product_name = $product->get_name();
        $featured_image_id = $product->get_image_id();
        
        if ($featured_image_id) {
            $image_url = wp_get_attachment_url($featured_image_id);
            $file_extension = strtolower(pathinfo($image_url, PATHINFO_EXTENSION));
            
            if (strpos($image_url, 'placeholder.svg') !== false || $file_extension === 'svg') {
                $svg_count++;
            } else {
                $real_images_count++;
                // Check for duplicates in real images
                if (isset($image_urls[$image_url])) {
                    if (!isset($duplicates[$image_url])) {
                        $duplicates[$image_url] = [$image_urls[$image_url]];
                    }
                    $duplicates[$image_url][] = ['id' => $product_id, 'name' => $product_name];
                } else {
                    $image_urls[$image_url] = ['id' => $product_id, 'name' => $product_name];
                }
            }
        }
    }
    
    return [
        'duplicates' => $duplicates,
        'svg_count' => $svg_count,
        'real_images_count' => $real_images_count,
        'total_products' => count($products)
    ];
}

$result = detect_duplicates();
$duplicates = $result['duplicates'];

echo "<h2>📊 Estadísticas Generales</h2>\n";
echo "<p><strong>📦 Total productos:</strong> {$result['total_products']}</p>\n";
echo "<p><strong>📸 Imágenes reales:</strong> {$result['real_images_count']}</p>\n";
echo "<p><strong>🔄 SVG restantes:</strong> {$result['svg_count']}</p>\n";
echo "<p><strong>🚫 Grupos de duplicados:</strong> " . count($duplicates) . "</p>\n";

if (count($duplicates) > 0) {
    echo "<h2>❌ IMÁGENES DUPLICADAS DETECTADAS</h2>\n";
    
    $total_affected = 0;
    foreach ($duplicates as $url => $products) {
        $total_affected += count($products);
    }
    
    echo "<p><strong>⚠️ Total productos con duplicados:</strong> $total_affected</p>\n";
    
    echo "<h3>📋 Detalles de Duplicados:</h3>\n";
    foreach ($duplicates as $url => $products) {
        $filename = basename($url);
        echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0; background: #f9f9f9;'>\n";
        echo "<p><strong>🖼️ Archivo:</strong> $filename</p>\n";
        echo "<p><strong>📦 Productos afectados (" . count($products) . "):</strong></p>\n";
        echo "<ul>\n";
        foreach ($products as $product) {
            echo "<li>ID: {$product['id']} - {$product['name']}</li>\n";
        }
        echo "</ul>\n";
        echo "</div>\n";
    }
    
    echo "<div style='background: #ffebee; padding: 15px; border: 1px solid #f44336; border-radius: 5px; margin: 20px 0;'>\n";
    echo "<h3>🚨 ACCIÓN REQUERIDA</h3>\n";
    echo "<p><strong>Se encontraron " . count($duplicates) . " grupos de imágenes duplicadas.</strong></p>\n";
    echo "<p>Esto significa que múltiples productos están mostrando exactamente la misma imagen.</p>\n";
    echo "<p><strong>Para solucionarlo, ejecute el script anti-duplicados.</strong></p>\n";
    echo "</div>\n";
    
} else {
    echo "<div style='background: #e8f5e8; padding: 15px; border: 1px solid #4caf50; border-radius: 5px;'>\n";
    echo "<h3>✅ ¡PERFECTO!</h3>\n";
    echo "<p><strong>No se encontraron imágenes duplicadas</strong></p>\n";
    echo "<p>Todas las imágenes reales son únicas</p>\n";
    echo "</div>\n";
}

echo "<p><em>Reporte generado: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>