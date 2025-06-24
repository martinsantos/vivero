<?php
require_once __DIR__ . '/wp-load.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🔗 Test de Enlaces a Productos</title>
    <style>
        body { 
            font-family: system-ui, -apple-system, sans-serif; 
            padding: 20px; 
            background: #f5f9f5;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1 { color: #2d5a2d; text-align: center; margin-bottom: 30px; }
        .product-item {
            background: #f8f9fa;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 4px solid #4CAF50;
        }
        .product-name {
            font-size: 1.3rem;
            font-weight: bold;
            color: #2d5a2d;
            margin-bottom: 10px;
        }
        .product-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        .link-btn {
            background: #4CAF50;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9rem;
            transition: background 0.3s;
        }
        .link-btn:hover {
            background: #45a049;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 Test de Enlaces a Productos Individuales</h1>
        
        <?php
        if (!class_exists('WooCommerce')) {
            echo '<div class="error">❌ WooCommerce no está activo</div>';
            exit;
        }
        
        // Obtener todos los productos
        $products = wc_get_products(array(
            'limit' => -1,
            'status' => 'publish',
            'orderby' => 'name',
            'order' => 'ASC'
        ));
        
        if (empty($products)) {
            echo '<div class="error">❌ No hay productos publicados</div>';
            echo '<p>Necesitas crear algunos productos primero.</p>';
        } else {
            echo '<div class="success">✅ Encontrados ' . count($products) . ' productos</div>';
            
            foreach ($products as $product) {
                $product_id = $product->get_id();
                $product_name = $product->get_name();
                $product_slug = $product->get_slug();
                $product_price = $product->get_price();
                
                echo '<div class="product-item">';
                echo '<div class="product-name">' . esc_html($product_name) . '</div>';
                echo '<div><strong>ID:</strong> ' . $product_id . '</div>';
                echo '<div><strong>Slug:</strong> ' . esc_html($product_slug) . '</div>';
                echo '<div><strong>Precio:</strong> $' . number_format($product_price, 0, ',', '.') . '</div>';
                
                // URLs diferentes para probar
                $urls = array(
                    'Permalink oficial' => get_permalink($product_id),
                    'URL con /product/' => home_url('/product/' . $product_slug . '/'),
                    'URL con parámetro' => home_url('/?post_type=product&p=' . $product_id),
                    'URL con ?product=' => home_url('/?product=' . $product_slug)
                );
                
                echo '<div class="product-links">';
                foreach ($urls as $type => $url) {
                    echo '<a href="' . esc_url($url) . '" class="link-btn" target="_blank">' . esc_html($type) . '</a>';
                }
                echo '</div>';
                
                echo '</div>';
            }
        }
        ?>
        
        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd;">
            <a href="/" class="link-btn" style="background: #2196F3;">🏠 Ir a Inicio</a>
            <a href="/cart/" class="link-btn" style="background: #FF9800;">🛒 Ver Carrito</a>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background: #e3f2fd; border-radius: 5px; font-size: 0.9rem;">
            <strong>💡 Instrucciones:</strong><br>
            1. Haz clic en cualquier enlace de arriba<br>
            2. Deberías ver la página individual del producto<br>
            3. Si ves una página 404, hay un problema con las URLs<br>
            4. Si funciona, verás el producto con imagen, precio y botón de agregar al carrito
        </div>
    </div>
</body>
</html>
