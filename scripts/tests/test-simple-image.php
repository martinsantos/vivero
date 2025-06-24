<?php
/**
 * Test simple con imagen básica para el carrito
 */

define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

// Función de test con imagen simple
function test_simple_cart_image($product_id) {
    $product = wc_get_product($product_id);
    $product_name = $product ? $product->get_name() : 'Producto de Prueba';
    
    // Usar SVG data URL (siempre funciona)
    $svg = '<svg width="120" height="120" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#4CAF50;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#45a049;stop-opacity:1" />
            </linearGradient>
        </defs>
        <rect width="120" height="120" fill="url(#grad)" rx="8"/>
        <text x="60" y="75" text-anchor="middle" fill="white" font-size="40" font-family="Arial">🌱</text>
    </svg>';
    
    $data_url = 'data:image/svg+xml;base64,' . base64encode($svg);
    
    return '<img src="' . esc_url($data_url) . '" alt="' . esc_attr($product_name) . '" style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;" />';
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🧪 Test de Imagen Simple</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f0f9f0;
        }
        .test-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h1 { color: #2d5a2d; text-align: center; }
        .test-item {
            background: #f8f9fa;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 5px solid #4CAF50;
        }
        .image-test {
            display: flex;
            align-items: center;
            gap: 20px;
            margin: 15px 0;
        }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🧪 Test de Imagen Simple para Carrito</h1>
        
        <div class="test-item">
            <h3>🛒 Simulación de Carrito con SVG Data URL</h3>
            <p>Este método SIEMPRE funciona porque usa SVG embebido.</p>
            
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f0f9f0;">
                        <th style="padding: 10px; border: 1px solid #ddd;">Imagen</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Producto</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $test_products = wc_get_products(['limit' => 5, 'status' => 'publish']);
                    foreach ($test_products as $product) {
                        $product_id = $product->get_id();
                        $product_name = $product->get_name();
                        $product_price = $product->get_price();
                        $simple_image = test_simple_cart_image($product_id);
                        
                        echo '<tr>';
                        echo '<td style="padding: 10px; border: 1px solid #ddd; text-align: center;">' . $simple_image . '</td>';
                        echo '<td style="padding: 10px; border: 1px solid #ddd;">' . esc_html($product_name) . '</td>';
                        echo '<td style="padding: 10px; border: 1px solid #ddd;">$' . number_format($product_price, 0, ',', '.') . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="/cart/" style="background: #4CAF50; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 10px;">
                🛒 Ir al Carrito Real
            </a>
            <a href="/" style="background: #2196F3; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 10px;">
                🏠 Volver a Inicio
            </a>
        </div>
        
        <p style="text-align: center; margin-top: 20px; color: #666;">
            Si estas imágenes se ven correctamente, el problema está en la conectividad externa.
        </p>
    </div>
</body>
</html>
