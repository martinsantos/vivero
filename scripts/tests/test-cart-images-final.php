<?php
/**
 * Test completo de imágenes del carrito - Diagnóstico y soluciones
 */

define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

if (!class_exists('WooCommerce')) {
    die('❌ WooCommerce no está activo');
}

// Incluir funciones del tema
require_once get_template_directory() . '/functions.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🛒 Test Completo - Imágenes del Carrito</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            padding: 20px; 
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f9f0 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1400px;
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
        .test-header {
            font-weight: bold;
            color: #2d5a2d;
            margin-bottom: 10px;
        }
        .test-result {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            align-items: center;
        }
        .test-info {
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .test-image {
            text-align: center;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
        }
        .test-image img {
            max-width: 150px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #FF9800; font-weight: bold; }
        .info { color: #2196F3; font-weight: bold; }
        .cart-simulation {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .cart-item {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .cart-item-image {
            flex-shrink: 0;
            width: 120px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cart-item-details {
            flex-grow: 1;
        }
        .solution-box {
            background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛒 Test Completo - Imágenes del Carrito</h1>
        
        <div class="test-section">
            <h2>1️⃣ Verificación de Funciones</h2>
            
            <div class="test-item">
                <div class="test-header">Función loscocos_get_product_image</div>
                <div class="test-info">
                    <?php if (function_exists('loscocos_get_product_image')): ?>
                        <span class="success">✅ Función existe</span><br>
                        <?php 
                        $test_url = loscocos_get_product_image(1);
                        echo "<strong>Test URL:</strong> " . esc_html($test_url);
                        ?>
                    <?php else: ?>
                        <span class="error">❌ Función NO existe</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="test-item">
                <div class="test-header">Función loscocos_get_cart_product_image</div>
                <div class="test-info">
                    <?php if (function_exists('loscocos_get_cart_product_image')): ?>
                        <span class="success">✅ Función existe</span><br>
                        <?php 
                        $test_html = loscocos_get_cart_product_image(1);
                        echo "<strong>Test HTML:</strong><br><code>" . esc_html(substr($test_html, 0, 100)) . "...</code>";
                        ?>
                    <?php else: ?>
                        <span class="error">❌ Función NO existe</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="test-section">
            <h2>2️⃣ Test de Imágenes por Producto</h2>
            
            <?php
            // Obtener algunos productos para testear
            $test_products = wc_get_products(array(
                'limit' => 6,
                'status' => 'publish'
            ));
            
            foreach ($test_products as $product):
                $product_id = $product->get_id();
                $product_name = $product->get_name();
                $categories = wp_get_post_terms($product_id, 'product_cat');
                $category_name = $categories && !is_wp_error($categories) ? $categories[0]->name : 'Sin categoría';
                
                // Test imagen Unsplash
                $unsplash_url = loscocos_get_product_image($product_id);
                
                // Test imagen carrito
                $cart_image_html = loscocos_get_cart_product_image($product_id);
                
                // Test imagen WordPress
                $wp_image_id = get_post_thumbnail_id($product_id);
                $wp_image_url = $wp_image_id ? wp_get_attachment_image_url($wp_image_id, 'thumbnail') : '';
            ?>
            
            <div class="test-item">
                <div class="test-header">Producto: <?php echo esc_html($product_name); ?> (ID: <?php echo $product_id; ?>)</div>
                <div class="test-result">
                    <div class="test-info">
                        <strong>Categoría:</strong> <?php echo esc_html($category_name); ?><br>
                        <strong>Precio:</strong> $<?php echo number_format($product->get_price(), 0, ',', '.'); ?><br><br>
                        
                        <strong>🔗 URL Unsplash:</strong><br>
                        <span class="<?php echo $unsplash_url ? 'success' : 'error'; ?>">
                            <?php echo $unsplash_url ? '✅ Disponible' : '❌ No disponible'; ?>
                        </span><br>
                        <small><?php echo esc_html(substr($unsplash_url, 0, 80)); ?>...</small><br><br>
                        
                        <strong>🖼️ Imagen WordPress:</strong><br>
                        <span class="<?php echo $wp_image_url ? 'success' : 'warning'; ?>">
                            <?php echo $wp_image_url ? '✅ Existe' : '⚠️ No asignada'; ?>
                        </span><br><br>
                        
                        <strong>🛒 HTML del Carrito:</strong><br>
                        <span class="<?php echo strpos($cart_image_html, '<img') !== false ? 'success' : 'warning'; ?>">
                            <?php echo strpos($cart_image_html, '<img') !== false ? '✅ IMG tag' : '⚠️ SVG inline'; ?>
                        </span>
                    </div>
                    
                    <div class="test-image">
                        <strong>Preview Carrito:</strong><br>
                        <?php echo $cart_image_html; ?>
                    </div>
                </div>
            </div>
            
            <?php endforeach; ?>
        </div>
        
        <div class="test-section">
            <h2>3️⃣ Test de Conectividad Unsplash</h2>
            
            <?php
            // Test de conectividad a Unsplash
            $test_unsplash_urls = [
                'https://images.unsplash.com/photo-1463320726281-696a485928c7?w=400&h=300&fit=crop&q=90',
                'https://images.unsplash.com/photo-1586093248882-4ec8a5e0b7e3?w=400&h=300&fit=crop&q=90',
                'https://images.unsplash.com/photo-1545239705-1564e58b9e4a?w=400&h=300&fit=crop&q=90'
            ];
            
            foreach ($test_unsplash_urls as $index => $url):
                $headers = @get_headers($url);
                $accessible = $headers && strpos($headers[0], '200') !== false;
            ?>
            
            <div class="test-item">
                <div class="test-header">Test Conectividad Unsplash #<?php echo $index + 1; ?></div>
                <div class="test-result">
                    <div class="test-info">
                        <strong>URL:</strong> <?php echo esc_html($url); ?><br>
                        <strong>Estado:</strong> 
                        <span class="<?php echo $accessible ? 'success' : 'error'; ?>">
                            <?php echo $accessible ? '✅ Accesible' : '❌ No accesible'; ?>
                        </span><br>
                        <strong>Headers:</strong> <?php echo $headers ? esc_html($headers[0]) : 'Error de conexión'; ?>
                    </div>
                    <div class="test-image">
                        <?php if ($accessible): ?>
                            <img src="<?php echo esc_url($url); ?>" alt="Test Unsplash" loading="lazy">
                        <?php else: ?>
                            <div style="padding: 40px; background: #f44336; color: white; border-radius: 8px;">
                                ❌ Imagen no accesible
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="/" style="background: #4CAF50; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold;">
                🏠 Volver a la Tienda
            </a>
            <a href="/cart/" style="background: #FF9800; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin-left: 15px;">
                🛒 Ver Carrito Real
            </a>
        </div>
    </div>
</body>
</html>
