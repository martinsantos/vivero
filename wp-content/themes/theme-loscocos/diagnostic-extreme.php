<?php
// DIAGNÓSTICO EXTREMO - DIRECTO DESDE EL TEMA
// Este archivo DEBE estar en el tema para ser accesible

// Evitar que WordPress lo procese como template
if (!defined('ABSPATH')) {
    // Cargar WordPress manualmente con la ruta correcta
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
}

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>🚨 DIAGNÓSTICO EXTREMO</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { border: 3px solid #ef4444; margin: 20px 0; padding: 20px; border-radius: 10px; background: #fef2f2; }
        .success { color: #10b981; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .warning { color: #f59e0b; font-weight: bold; }
        .code { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; margin: 10px 0; border-radius: 5px; font-family: monospace; }
        .image-test { width: 100px; height: 100px; border: 3px solid #10b981; margin: 10px; display: inline-block; overflow: hidden; }
    </style>
</head>
<body>

<h1>🚨 DIAGNÓSTICO EXTREMO DEL PROBLEMA</h1>

<?php
// 1. VERIFICAR SISTEMA BÁSICO
echo "<div class='section'>";
echo "<h2>🔧 1. VERIFICACIÓN BÁSICA</h2>";
echo "<p><strong>WordPress cargado:</strong> " . (defined('ABSPATH') ? "✅ SÍ" : "❌ NO") . "</p>";
echo "<p><strong>WooCommerce activo:</strong> " . (class_exists('WooCommerce') ? "✅ SÍ" : "❌ NO") . "</p>";
echo "<p><strong>Archivo ejecutándose desde:</strong> " . __FILE__ . "</p>";
echo "<p><strong>URL actual:</strong> " . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'No disponible') . "</p>";
echo "</div>";

// 2. VERIFICAR FUNCIÓN
echo "<div class='section'>";
echo "<h2>🎯 2. VERIFICACIÓN DE FUNCIÓN</h2>";
$function_exists = function_exists('loscocos_get_cart_product_image');
echo "<p><strong>Función loscocos_get_cart_product_image existe:</strong> " . ($function_exists ? "✅ SÍ" : "❌ NO") . "</p>";

if ($function_exists) {
    echo "<h3>🧪 Prueba inmediata de la función:</h3>";
    
    try {
        $test_result = loscocos_get_cart_product_image(999, 'thumbnail');
        echo "<p class='success'>✅ Función ejecutada sin errores</p>";
        echo "<p><strong>Longitud del resultado:</strong> " . strlen($test_result) . " caracteres</p>";
        
        echo "<div class='code'>";
        echo htmlspecialchars($test_result);
        echo "</div>";
        
        echo "<h4>👁️ Vista previa:</h4>";
        echo "<div class='image-test'>";
        echo $test_result;
        echo "</div>";
        
        // Verificar si es SVG válido
        if (strpos($test_result, 'data:image/svg+xml') !== false) {
            echo "<p class='success'>✅ SVG embebido detectado</p>";
        } else {
            echo "<p class='error'>❌ No es SVG embebido</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error ejecutando función: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p class='error'>❌ FUNCIÓN NO DISPONIBLE - Este es el problema principal</p>";
}
echo "</div>";

// 3. FORZAR CARGA DEL CARRITO
echo "<div class='section'>";
echo "<h2>🛒 3. FORZAR PRODUCTOS EN CARRITO</h2>";

if (class_exists('WooCommerce')) {
    $cart_count_before = WC()->cart->get_cart_contents_count();
    echo "<p><strong>Productos en carrito (antes):</strong> $cart_count_before</p>";
    
    if ($cart_count_before == 0) {
        echo "<p class='warning'>⚠️ Carrito vacío - Agregando productos...</p>";
        
        $products = wc_get_products(array('limit' => 3, 'status' => 'publish'));
        if (!empty($products)) {
            WC()->cart->empty_cart();
            
            foreach ($products as $product) {
                $product_id = $product->get_id();
                $added = WC()->cart->add_to_cart($product_id, 1);
                echo "<p>" . ($added ? "✅" : "❌") . " Producto $product_id (" . $product->get_name() . ")</p>";
            }
            
            $cart_count_after = WC()->cart->get_cart_contents_count();
            echo "<p><strong>Productos en carrito (después):</strong> $cart_count_after</p>";
        } else {
            echo "<p class='error'>❌ No hay productos para agregar</p>";
        }
    }
    
    // Mostrar contenido actual del carrito
    if (WC()->cart->get_cart_contents_count() > 0) {
        echo "<h3>🔍 Contenido actual del carrito:</h3>";
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['product_id'];
            $product = $cart_item['data'];
            
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<h4>" . $product->get_name() . " (ID: $product_id)</h4>";
            
            if ($function_exists) {
                echo "<h5>🎨 Imagen generada por nuestra función:</h5>";
                $our_image = loscocos_get_cart_product_image($product_id, 'thumbnail');
                echo "<div class='image-test'>";
                echo $our_image;
                echo "</div>";
                echo "<div class='code'>";
                echo htmlspecialchars(substr($our_image, 0, 200)) . "...";
                echo "</div>";
            }
            
            echo "<h5>🏪 Imagen nativa de WooCommerce:</h5>";
            $native_image = $product->get_image('thumbnail');
            echo "<div class='image-test'>";
            echo $native_image;
            echo "</div>";
            
            echo "</div>";
        }
    }
} else {
    echo "<p class='error'>❌ WooCommerce NO está activo</p>";
}
echo "</div>";

// 4. VERIFICAR TEMPLATE DEL CARRITO
echo "<div class='section'>";
echo "<h2>📄 4. VERIFICACIÓN DEL TEMPLATE</h2>";
$cart_template = locate_template('woocommerce/cart/cart.php');
echo "<p><strong>Template del carrito:</strong> " . ($cart_template ? $cart_template : "No encontrado") . "</p>";

if ($cart_template && file_exists($cart_template)) {
    $template_content = file_get_contents($cart_template);
    $has_our_function = strpos($template_content, 'loscocos_get_cart_product_image') !== false;
    echo "<p><strong>Template contiene nuestra función:</strong> " . ($has_our_function ? "✅ SÍ" : "❌ NO") . "</p>";
    
    if ($has_our_function) {
        echo "<p class='success'>✅ Template configurado correctamente</p>";
    }
}
echo "</div>";

// 5. ACCESO DIRECTO AL CARRITO
echo "<div class='section'>";
echo "<h2>🌐 5. ACCESO DIRECTO AL CARRITO</h2>";
echo "<p><strong>URL del carrito:</strong> " . home_url('/cart/') . "</p>";

// Hacer una petición interna al carrito
$cart_response = wp_remote_get(home_url('/cart/'), array('timeout' => 10));
if (!is_wp_error($cart_response)) {
    $cart_html = wp_remote_retrieve_body($cart_response);
    $response_code = wp_remote_retrieve_response_code($cart_response);
    echo "<p><strong>Código de respuesta:</strong> $response_code</p>";
    
    // Buscar elementos específicos
    $has_product_thumbnail = strpos($cart_html, 'product-thumbnail') !== false;
    $has_cart_item = strpos($cart_html, 'cart_item') !== false;
    $has_empty_cart = strpos($cart_html, 'carrito está vacío') !== false;
    
    echo "<p><strong>Contiene 'product-thumbnail':</strong> " . ($has_product_thumbnail ? "✅ SÍ" : "❌ NO") . "</p>";
    echo "<p><strong>Contiene 'cart_item':</strong> " . ($has_cart_item ? "✅ SÍ" : "❌ NO") . "</p>";
    echo "<p><strong>Muestra 'carrito vacío':</strong> " . ($has_empty_cart ? "⚠️ SÍ" : "✅ NO") . "</p>";
} else {
    echo "<p class='error'>❌ Error accediendo al carrito: " . $cart_response->get_error_message() . "</p>";
}
echo "</div>";
?>

<div class="section">
    <h2>🎯 ACCIONES INMEDIATAS</h2>
    <p><a href="<?php echo home_url('/cart/'); ?>" target="_blank" style="background: #ef4444; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">🛒 ABRIR CARRITO EN NUEVA PESTAÑA</a></p>
    <p><a href="<?php echo home_url(); ?>" style="color: #10b981;">🏠 Volver al inicio</a></p>
</div>

</body>
</html> 