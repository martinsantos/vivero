<?php
/**
 * Script de diagnóstico para imágenes de productos
 * Ejecutar desde: http://localhost:8080/debug-images.php
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
    die('❌ No se pudo cargar WordPress.');
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 Diagnóstico de Imágenes - Vivero Los Cocos</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            margin: 0;
            padding: 2rem;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
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
        .product-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
            border: 1px solid #e2e8f0;
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }
        .product-image {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
            background: #e5e7eb;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-details h3 {
            margin: 0 0 1rem 0;
            color: #1f2937;
        }
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-right: 0.5rem;
        }
        .status.success {
            background: #dcfce7;
            color: #166534;
        }
        .status.error {
            background: #fee2e2;
            color: #dc2626;
        }
        .status.warning {
            background: #fef3c7;
            color: #92400e;
        }
        .btn {
            background: #10b981;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
            font-weight: 600;
        }
        .btn:hover {
            background: #059669;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">🔍 Diagnóstico de Imágenes</h1>
            <p>Verificando el estado de las imágenes de productos en el carrito</p>
        </div>

        <?php
        // Verificar si hay productos en el carrito
        if (class_exists('WooCommerce') && !WC()->cart->is_empty()) {
            echo '<h2>🛒 Productos en el carrito:</h2>';
            
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $product = $cart_item['data'];
                $product_id = $cart_item['product_id'];
                $quantity = $cart_item['quantity'];
                
                if ($product && $product->exists()) {
                    echo '<div class="product-card">';
                    
                    // Imagen del producto
                    echo '<div class="product-image">';
                    
                    $has_thumbnail = has_post_thumbnail($product_id);
                    $attachment_id = get_post_thumbnail_id($product_id);
                    $image_url = '';
                    
                    if ($has_thumbnail && $attachment_id) {
                        $image_url = wp_get_attachment_image_url($attachment_id, 'medium');
                        if ($image_url) {
                            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($product->get_name()) . '" />';
                        } else {
                            echo '<span style="font-size: 2rem;">❌</span>';
                        }
                    } else {
                        echo '<span style="font-size: 2rem;">📷</span>';
                    }
                    
                    echo '</div>';
                    
                    // Detalles del producto
                    echo '<div class="product-details">';
                    echo '<h3>' . esc_html($product->get_name()) . '</h3>';
                    
                    // Estado de la imagen
                    if ($has_thumbnail) {
                        if ($attachment_id && $image_url) {
                            echo '<span class="status success">✅ Imagen OK</span>';
                        } else {
                            echo '<span class="status error">❌ Error en imagen</span>';
                        }
                    } else {
                        echo '<span class="status warning">⚠️ Sin imagen</span>';
                    }
                    
                    // Información técnica
                    echo '<div style="margin-top: 1rem; font-size: 0.875rem; color: #6b7280;">';
                    echo '<strong>ID del producto:</strong> ' . $product_id . '<br>';
                    echo '<strong>Tiene thumbnail:</strong> ' . ($has_thumbnail ? 'Sí' : 'No') . '<br>';
                    echo '<strong>ID del attachment:</strong> ' . ($attachment_id ?: 'N/A') . '<br>';
                    echo '<strong>URL de imagen:</strong> ' . ($image_url ?: 'N/A') . '<br>';
                    echo '<strong>Cantidad:</strong> ' . $quantity . '<br>';
                    
                    // Verificar si el archivo existe
                    if ($image_url) {
                        $upload_dir = wp_upload_dir();
                        $relative_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $image_url);
                        $file_exists = file_exists($relative_path);
                        echo '<strong>Archivo existe:</strong> ' . ($file_exists ? 'Sí' : 'No') . '<br>';
                        
                        if (!$file_exists) {
                            echo '<span class="status error">⚠️ El archivo de imagen no existe en el servidor</span>';
                        }
                    }
                    
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            
        } else {
            echo '<div style="text-align: center; padding: 2rem;">';
            echo '<h2>🛒 Carrito vacío</h2>';
            echo '<p>Agrega algunos productos al carrito para verificar sus imágenes.</p>';
            echo '<a href="' . home_url('/shop/') . '" class="btn">🏪 Ir a la Tienda</a>';
            echo '</div>';
        }
        
        // Verificar todos los productos disponibles
        echo '<h2>📦 Todos los productos:</h2>';
        
        $all_products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => 10,
            'post_status' => 'publish'
        ));
        
        if ($all_products) {
            foreach ($all_products as $product_post) {
                $product_id = $product_post->ID;
                $product_name = $product_post->post_title;
                
                echo '<div class="product-card">';
                echo '<div class="product-image">';
                
                $has_thumbnail = has_post_thumbnail($product_id);
                $attachment_id = get_post_thumbnail_id($product_id);
                $image_url = '';
                
                if ($has_thumbnail && $attachment_id) {
                    $image_url = wp_get_attachment_image_url($attachment_id, 'medium');
                    if ($image_url) {
                        echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($product_name) . '" />';
                    } else {
                        echo '<span style="font-size: 2rem;">❌</span>';
                    }
                } else {
                    echo '<span style="font-size: 2rem;">📷</span>';
                }
                
                echo '</div>';
                echo '<div class="product-details">';
                echo '<h3>' . esc_html($product_name) . '</h3>';
                
                if ($has_thumbnail) {
                    if ($attachment_id && $image_url) {
                        echo '<span class="status success">✅ Imagen OK</span>';
                    } else {
                        echo '<span class="status error">❌ Error en imagen</span>';
                    }
                } else {
                    echo '<span class="status warning">⚠️ Sin imagen</span>';
                }
                
                echo '<div style="margin-top: 1rem; font-size: 0.875rem; color: #6b7280;">';
                echo '<strong>ID:</strong> ' . $product_id . ' | ';
                echo '<strong>Thumbnail:</strong> ' . ($has_thumbnail ? 'Sí' : 'No') . ' | ';
                echo '<strong>Attachment ID:</strong> ' . ($attachment_id ?: 'N/A');
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No se encontraron productos.</p>';
        }
        ?>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="<?php echo home_url('/cart/'); ?>" class="btn">🛒 Ver Carrito</a>
            <a href="<?php echo home_url('/regenerate-thumbnails.php'); ?>" class="btn">🖼️ Regenerar Miniaturas</a>
            <a href="<?php echo home_url(); ?>" class="btn">🏠 Inicio</a>
        </div>
    </div>
</body>
</html> 