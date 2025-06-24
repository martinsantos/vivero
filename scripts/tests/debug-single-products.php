<?php
/**
 * Diagnóstico completo de páginas individuales de productos
 */

define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🔍 Diagnóstico - Páginas Individuales de Productos</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            padding: 20px; 
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f9f0 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        h1 { color: #2d5a2d; text-align: center; margin-bottom: 30px; }
        h2 { color: #2d5a2d; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .test-section {
            background: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            border-left: 5px solid #4CAF50;
        }
        .test-item {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #FF9800; font-weight: bold; }
        .btn {
            background: #4CAF50;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 5px;
            display: inline-block;
        }
        .btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico - Páginas Individuales de Productos</h1>
        
        <div class="test-section">
            <h2>1️⃣ Verificación de Productos Existentes</h2>
            
            <?php
            $products = wc_get_products(array(
                'limit' => 5,
                'status' => 'publish'
            ));
            
            if (empty($products)) {
                echo '<div class="error">❌ No hay productos publicados</div>';
            } else {
                echo '<div class="success">✅ Encontrados ' . count($products) . ' productos</div>';
                
                foreach ($products as $product) {
                    $product_id = $product->get_id();
                    $product_name = $product->get_name();
                    $product_slug = $product->get_slug();
                    $product_url = get_permalink($product_id);
                    
                    echo '<div class="test-item">';
                    echo '<strong>Producto:</strong> ' . esc_html($product_name) . '<br>';
                    echo '<strong>ID:</strong> ' . $product_id . '<br>';
                    echo '<strong>Slug:</strong> ' . esc_html($product_slug) . '<br>';
                    echo '<strong>URL:</strong> <a href="' . esc_url($product_url) . '" target="_blank">' . esc_html($product_url) . '</a><br>';
                    echo '<a href="' . esc_url($product_url) . '" class="btn" target="_blank">🔗 Abrir Producto</a>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        
        <div class="test-section">
            <h2>2️⃣ Verificación de Templates</h2>
            
            <?php
            $template_files = array(
                'single-product.php' => get_template_directory() . '/single-product.php',
                'woocommerce/single-product.php' => get_template_directory() . '/woocommerce/single-product.php',
                'single.php' => get_template_directory() . '/single.php',
                'index.php' => get_template_directory() . '/index.php'
            );
            
            foreach ($template_files as $name => $path) {
                echo '<div class="test-item">';
                echo '<strong>Template:</strong> ' . esc_html($name) . '<br>';
                
                if (file_exists($path)) {
                    echo '<span class="success">✅ Existe</span><br>';
                    echo '<strong>Ruta:</strong> ' . esc_html($path) . '<br>';
                    echo '<strong>Tamaño:</strong> ' . number_format(filesize($path) / 1024, 2) . ' KB<br>';
                    echo '<strong>Modificado:</strong> ' . date('Y-m-d H:i:s', filemtime($path));
                } else {
                    echo '<span class="error">❌ No existe</span>';
                }
                echo '</div>';
            }
            ?>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="/" class="btn">🏠 Volver a Inicio</a>
            <a href="/cart/" class="btn">🛒 Ver Carrito</a>
        </div>
    </div>
</body>
</html>
