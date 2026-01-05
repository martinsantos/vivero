<?php
/**
 * 🔍 Monitor Real Image Automation Progress
 * Shows live progress of the automation process
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🔍 Monitoreo de Automatización de Imágenes Reales</h1>\n";
echo "<p><em>Actualizando cada 30 segundos...</em></p>\n";

function get_image_statistics() {
    $products = wc_get_products(['limit' => -1, 'status' => 'publish']);
    $total_products = count($products);
    $real_images = 0;
    $svg_images = 0;

    foreach ($products as $product) {
        $featured_image_id = $product->get_image_id();
        
        if ($featured_image_id) {
            $image_url = wp_get_attachment_url($featured_image_id);
            $file_extension = strtolower(pathinfo($image_url, PATHINFO_EXTENSION));
            
            if (strpos($image_url, 'placeholder.svg') !== false || $file_extension === 'svg') {
                $svg_images++;
            } else {
                $real_images++;
            }
        }
    }
    
    return [
        'total' => $total_products,
        'real' => $real_images,
        'svg' => $svg_images,
        'percentage' => round(($real_images / $total_products) * 100, 1)
    ];
}

// Monitor for 10 minutes with updates every 30 seconds
$start_time = time();
$monitoring_duration = 600; // 10 minutes

while (time() - $start_time < $monitoring_duration) {
    $stats = get_image_statistics();
    $elapsed = time() - $start_time;
    
    echo "<h3>📊 Progreso actual (+" . gmdate("i:s", $elapsed) . ")</h3>\n";
    echo "<p><strong>✅ Imágenes reales:</strong> {$stats['real']}/{$stats['total']} ({$stats['percentage']}%)</p>\n";
    echo "<p><strong>🔄 SVG restantes:</strong> {$stats['svg']}</p>\n";
    
    // Progress bar
    $progress_width = ($stats['percentage'] / 100) * 50;
    $progress_bar = str_repeat('█', (int)$progress_width) . str_repeat('░', 50 - (int)$progress_width);
    echo "<p><code>[$progress_bar] {$stats['percentage']}%</code></p>\n";
    
    // Estimate completion time
    if ($stats['real'] > 56) { // If progress since start
        $products_processed = $stats['real'] - 56;
        $rate = $products_processed / ($elapsed / 60); // products per minute
        if ($rate > 0) {
            $remaining_time = $stats['svg'] / $rate;
            echo "<p><strong>⏱️ Tiempo estimado restante:</strong> " . round($remaining_time) . " minutos</p>\n";
        }
    }
    
    echo "<hr>\n";
    flush();
    
    if ($stats['svg'] == 0) {
        echo "<h2>🎉 ¡COMPLETADO! Todos los productos tienen imágenes reales</h2>\n";
        break;
    }
    
    sleep(30); // Wait 30 seconds before next check
}

echo "<p><em>Monitoreo finalizado</em></p>\n";
?>