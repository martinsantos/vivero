<?php
/**
 * Script para regenerar miniaturas de productos
 * Ejecutar desde: http://localhost:8080/regenerate-thumbnails.php
 */

// Cargar WordPress
$wp_load_paths = array(
    './wp-load.php',
    '../wp-load.php',
    '../../wp-load.php',
    '../../../wp-load.php'
);

$wp_loaded = false;
foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $wp_loaded = true;
        break;
    }
}

if (!$wp_loaded) {
    die('❌ No se pudo cargar WordPress. Asegúrate de que este archivo esté en la raíz de WordPress.');
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🖼️ Regenerar Miniaturas - Vivero Los Cocos</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            margin: 0;
            padding: 2rem;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 1rem;
        }
        .subtitle {
            color: #64748b;
            font-size: 1.125rem;
        }
        .result {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
            border-left: 4px solid #10b981;
        }
        .success {
            color: #059669;
            font-weight: 600;
        }
        .info {
            color: #0ea5e9;
            font-weight: 600;
        }
        .error {
            color: #ef4444;
            font-weight: 600;
        }
        .btn {
            background: #10b981;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 1rem 0;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .progress {
            background: #e5e7eb;
            border-radius: 8px;
            height: 20px;
            margin: 1rem 0;
            overflow: hidden;
        }
        .progress-bar {
            background: linear-gradient(90deg, #10b981, #059669);
            height: 100%;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">🖼️ Regenerar Miniaturas</h1>
            <p class="subtitle">Regenerando imágenes de productos para el carrito</p>
        </div>

        <?php
        echo '<h3>🔍 Verificando productos...</h3>';
        
        // Obtener todos los productos
        $products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        $total_products = count($products);
        echo '<div class="result"><span class="info">ℹ️ Encontrados ' . $total_products . ' productos</span></div>';
        
        if ($total_products > 0) {
            echo '<h3>🔧 Regenerando miniaturas...</h3>';
            
            $processed = 0;
            $with_images = 0;
            $without_images = 0;
            
            foreach ($products as $product) {
                $product_id = $product->ID;
                $product_name = $product->post_title;
                
                if (has_post_thumbnail($product_id)) {
                    // Regenerar miniaturas
                    $attachment_id = get_post_thumbnail_id($product_id);
                    if ($attachment_id) {
                        // Regenerar diferentes tamaños
                        $image_sizes = array('thumbnail', 'medium', 'large', 'woocommerce_thumbnail', 'woocommerce_single');
                        foreach ($image_sizes as $size) {
                            wp_get_attachment_image_src($attachment_id, $size);
                        }
                        $with_images++;
                        echo '<div class="result"><span class="success">✅ ' . esc_html($product_name) . ' - Miniaturas regeneradas</span></div>';
                    }
                } else {
                    $without_images++;
                    echo '<div class="result"><span class="error">⚠️ ' . esc_html($product_name) . ' - Sin imagen</span></div>';
                }
                
                $processed++;
                
                // Mostrar progreso cada 5 productos
                if ($processed % 5 == 0) {
                    $percentage = ($processed / $total_products) * 100;
                    echo '<div class="progress"><div class="progress-bar" style="width: ' . $percentage . '%"></div></div>';
                    echo '<p>Progreso: ' . $processed . '/' . $total_products . ' (' . round($percentage, 1) . '%)</p>';
                    flush();
                }
            }
            
            echo '<h3>📊 Resumen final:</h3>';
            echo '<div class="result"><span class="success">✅ Productos con imágenes: ' . $with_images . '</span></div>';
            echo '<div class="result"><span class="error">⚠️ Productos sin imágenes: ' . $without_images . '</span></div>';
            echo '<div class="result"><span class="info">📈 Total procesados: ' . $processed . '</span></div>';
            
            // Limpiar cache
            if (function_exists('wp_cache_flush')) {
                wp_cache_flush();
                echo '<div class="result"><span class="success">🧹 Cache limpiado</span></div>';
            }
            
        } else {
            echo '<div class="result"><span class="error">❌ No se encontraron productos</span></div>';
        }
        ?>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="<?php echo home_url('/cart/'); ?>" class="btn">🛒 Probar Carrito</a>
            <a href="<?php echo home_url(); ?>" class="btn">🏠 Volver al Inicio</a>
        </div>

        <div class="result" style="margin-top: 2rem; background: #fef3c7; border-left-color: #f59e0b;">
            <span style="color: #92400e; font-weight: 600;">
                💡 Nota: Si aún no ves las imágenes, verifica que los productos tengan imágenes destacadas asignadas en el admin de WordPress.
            </span>
        </div>
    </div>
</body>
</html> 