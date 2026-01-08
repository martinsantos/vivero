<?php
// Mostrar errores para depurar 500
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Debug profundo de imágenes de productos
define('WP_USE_THEMES', false);
// Ruta absoluta al root de WordPress (tres niveles arriba)
$wp_root = dirname(__FILE__, 4); // /var/www/html
require_once $wp_root . '/wp-config.php';
require_once $wp_root . '/wp-load.php';

header('Content-Type: text/html; charset=utf-8');

echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #f0f0f0; }
    .ok { background: #d1fae5; }
    .error { background: #fee2e2; }
    .warn { background: #fef9c3; }
    img { max-width: 80px; height: auto; border: 1px solid #999; }
</style>";

echo "<h1>🔍 Diagnóstico Profundo de Imágenes de Productos</h1>";

$products = get_posts(array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'post_status' => 'publish'
));

if (!$products) {
    echo "<p style='color:red;'>❌ No se encontraron productos.</p>";
    exit;
}

echo "<table>";
echo "<tr><th>ID</th><th>Nombre</th><th>Thumbnail ID</th><th>Ruta física</th><th>Existe?</th><th>URL</th><th>Visibilidad CSS</th><th>Render Woo</th></tr>";

$missing_files = 0;
$missing_thumbnails = 0;

foreach ($products as $p) {
    $pid = $p->ID;
    $name = esc_html($p->post_title);
    $thumb_id = get_post_thumbnail_id($pid);

    if (!$thumb_id) {
        $missing_thumbnails++;
    }

    $file_path = $thumb_id ? get_attached_file($thumb_id) : '';
    $file_exists = $file_path && file_exists($file_path);
    if (!$file_exists) {
        $missing_files++;
    }

    $file_url = $thumb_id ? wp_get_attachment_url($thumb_id) : '';

    // Check CSS visibility through inline style
    $img_html = wp_get_attachment_image($thumb_id, 'thumbnail', false, array('style' => 'border:1px solid #333;'));

    // WooCommerce render
    $woo_img_html = function_exists('wc_get_product') ? wc_get_product($pid)->get_image('thumbnail') : '';

    echo "<tr class='" . ($file_exists && $thumb_id ? 'ok' : 'error') . "'>";
    echo "<td>$pid</td>";
    echo "<td>$name</td>";
    echo "<td>" . ($thumb_id ?: '<span style=\'color:red;\'>N/A</span>') . "</td>";
    echo "<td>" . ($file_path ? esc_html($file_path) : '-') . "</td>";
    echo "<td>" . ($file_exists ? '✅' : '❌') . "</td>";
    echo "<td>" . ($file_url ? '<a href="' . esc_url($file_url) . '" target="_blank">link</a>' : '-') . "</td>";
    echo "<td>" . ($img_html ?: '-') . "</td>";
    echo "<td>" . ($woo_img_html ?: '-') . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>Resumen</h2>";
echo "<p>Total productos: " . count($products) . "</p>";
echo "<p>Productos sin thumbnail: $missing_thumbnails</p>";
echo "<p>Archivos faltantes: $missing_files</p>";

echo "<hr><p><a href='/'>🏠 Volver al inicio</a></p>";
?> 