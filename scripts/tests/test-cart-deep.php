<?php
/**
 * SCRIPT DE TESTING PROFUNDO DEL CARRITO
 * Diagnóstica y prueba todas las funcionalidades
 */

// Configuración básica de WordPress
define('WP_USE_THEMES', false);
require_once('./wp-config.php');

// Asegurar que WooCommerce esté activo
if (!class_exists('WooCommerce')) {
    die('❌ WooCommerce no está activo');
}

echo "<h1>🔬 TESTING PROFUNDO DEL CARRITO LOS COCOS</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { background: #f9f9f9; padding: 15px; margin: 10px 0; border-radius: 8px; }
    .success { color: #10b981; font-weight: bold; }
    .error { color: #ef4444; font-weight: bold; }
    .info { color: #3b82f6; font-weight: bold; }
    pre { background: #f1f1f1; padding: 10px; border-radius: 4px; overflow-x: auto; }
</style>";

// TEST 1: Verificar configuración básica
echo "<div class='test-section'>";
echo "<h2>🔧 TEST 1: Configuración Básica</h2>";

echo "<h3>WordPress & WooCommerce:</h3>";
echo "✅ WordPress versión: " . get_bloginfo('version') . "<br>";
echo "✅ WooCommerce versión: " . WC()->version . "<br>";
echo "✅ Tema activo: " . get_option('stylesheet') . "<br>";

echo "<h3>URLs importantes:</h3>";
echo "🔗 Home URL: " . home_url() . "<br>";
echo "🔗 Cart URL: " . wc_get_cart_url() . "<br>";
echo "🔗 Checkout URL: " . wc_get_checkout_url() . "<br>";
echo "🔗 AJAX URL: " . admin_url('admin-ajax.php') . "<br>";

echo "<h3>Páginas de WooCommerce:</h3>";
$cart_page = get_option('woocommerce_cart_page_id');
$checkout_page = get_option('woocommerce_checkout_page_id');
echo "📄 Cart Page ID: " . ($cart_page ? $cart_page . " ✅" : "❌ No configurada") . "<br>";
echo "📄 Checkout Page ID: " . ($checkout_page ? $checkout_page . " ✅" : "❌ No configurada") . "<br>";

echo "</div>";

// TEST 2: Verificar productos en el carrito
echo "<div class='test-section'>";
echo "<h2>🛒 TEST 2: Estado del Carrito</h2>";

// Limpiar carrito primero
WC()->cart->empty_cart();

// Agregar productos de prueba
$products = wc_get_products(['limit' => 3]);
$added_products = [];

if (!empty($products)) {
    foreach ($products as $product) {
        $result = WC()->cart->add_to_cart($product->get_id(), 2);
        if ($result) {
            $added_products[] = $product->get_name();
            echo "✅ Agregado: " . $product->get_name() . " (ID: " . $product->get_id() . ")<br>";
        } else {
            echo "❌ Error agregando: " . $product->get_name() . "<br>";
        }
    }
} else {
    echo "❌ No hay productos disponibles para agregar al carrito<br>";
}

echo "<h3>Estado actual del carrito:</h3>";
echo "📦 Productos en carrito: " . WC()->cart->get_cart_contents_count() . "<br>";
echo "💰 Total del carrito: " . WC()->cart->get_total() . "<br>";
echo "🔢 Items únicos: " . count(WC()->cart->get_cart()) . "<br>";

echo "</div>";

// TEST 3: Testing de funciones AJAX
echo "<div class='test-section'>";
echo "<h2>🌐 TEST 3: Funciones AJAX</h2>";

// Simular una llamada AJAX
$_POST['action'] = 'update_cart_quantity';
$_POST['nonce'] = wp_create_nonce('cart_nonce');

if (!empty(WC()->cart->get_cart())) {
    $cart_keys = array_keys(WC()->cart->get_cart());
    $test_cart_key = $cart_keys[0];
    
    $_POST['cart_key'] = $test_cart_key;
    $_POST['quantity'] = 3;
    
    echo "<h3>Testing update_cart_quantity:</h3>";
    echo "🔑 Cart Key: " . $test_cart_key . "<br>";
    echo "🔢 Nueva cantidad: 3<br>";
    echo "🔐 Nonce: " . $_POST['nonce'] . "<br>";
    
    // Capturar output de la función AJAX
    ob_start();
    
    // Verificar que la función existe
    if (function_exists('handle_update_cart_quantity')) {
        echo "✅ Función handle_update_cart_quantity existe<br>";
        
        // Simular la llamada
        try {
            handle_update_cart_quantity();
            $ajax_output = ob_get_contents();
            echo "📤 Respuesta AJAX capturada<br>";
        } catch (Exception $e) {
            echo "❌ Error en AJAX: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ Función handle_update_cart_quantity NO existe<br>";
    }
    
    ob_end_clean();
    
} else {
    echo "❌ No hay productos en el carrito para probar AJAX<br>";
}

echo "</div>";

// TEST 4: Testing de imágenes
echo "<div class='test-section'>";
echo "<h2>🖼️ TEST 4: Sistema de Imágenes</h2>";

echo "<h3>Testing función loscocos_get_cart_product_image:</h3>";

if (function_exists('loscocos_get_cart_product_image')) {
    echo "✅ Función loscocos_get_cart_product_image existe<br>";
    
    if (!empty($products)) {
        $test_product = $products[0];
        $image_html = loscocos_get_cart_product_image($test_product->get_id());
        
        echo "<h4>Imagen generada para: " . $test_product->get_name() . "</h4>";
        echo "<div style='border: 1px solid #ccc; padding: 10px; background: white;'>";
        echo $image_html;
        echo "</div>";
        echo "<pre>" . htmlspecialchars($image_html) . "</pre>";
    }
} else {
    echo "❌ Función loscocos_get_cart_product_image NO existe<br>";
}

echo "<h3>Testing filtros de imágenes:</h3>";
$image_filters = [
    'woocommerce_cart_item_thumbnail',
    'woocommerce_product_get_image',
    'post_thumbnail_html',
    'wp_get_attachment_image'
];

foreach ($image_filters as $filter) {
    $priority = has_filter($filter, 'loscocos_custom_cart_item_thumbnail') ?: 
                has_filter($filter, 'loscocos_force_all_cart_images');
    
    if ($priority !== false) {
        echo "✅ Filtro '$filter' activo (prioridad: $priority)<br>";
    } else {
        echo "❌ Filtro '$filter' NO activo<br>";
    }
}

echo "</div>";

// TEST 5: Testing del template
echo "<div class='test-section'>";
echo "<h2>📄 TEST 5: Template del Carrito</h2>";

$cart_template = locate_template('page-cart.php');
echo "📁 Template del carrito: " . ($cart_template ? $cart_template . " ✅" : "❌ No encontrado") . "<br>";

if ($cart_template) {
    $template_size = filesize($cart_template);
    $template_modified = date('Y-m-d H:i:s', filemtime($cart_template));
    
    echo "📊 Tamaño del template: " . number_format($template_size) . " bytes<br>";
    echo "🕒 Última modificación: " . $template_modified . "<br>";
    
    // Verificar si contiene las funciones JavaScript
    $template_content = file_get_contents($cart_template);
    
    $js_functions = ['updateQuantity', 'removeItem', 'showNotification'];
    foreach ($js_functions as $func) {
        if (strpos($template_content, $func) !== false) {
            echo "✅ Función JavaScript '$func' encontrada<br>";
        } else {
            echo "❌ Función JavaScript '$func' NO encontrada<br>";
        }
    }
    
    // Verificar nonces
    if (strpos($template_content, 'wp_create_nonce') !== false) {
        echo "✅ Generación de nonces encontrada<br>";
    } else {
        echo "❌ Generación de nonces NO encontrada<br>";
    }
}

echo "</div>";

// TEST 6: Testing en vivo
echo "<div class='test-section'>";
echo "<h2>🚀 TEST 6: Prueba en Vivo</h2>";

echo "<h3>Generar URL de prueba:</h3>";
$test_url = home_url('/cart/?auto_fill=1');
echo "🔗 URL de prueba: <a href='$test_url' target='_blank'>$test_url</a><br>";

echo "<h3>Comandos de testing:</h3>";
echo "<pre>";
echo "# Test AJAX directo:\n";
echo "curl -X POST '" . admin_url('admin-ajax.php') . "' \\\n";
echo "  -d 'action=update_cart_quantity' \\\n";
echo "  -d 'cart_key=" . (isset($test_cart_key) ? $test_cart_key : 'TEST_KEY') . "' \\\n";
echo "  -d 'quantity=5' \\\n";
echo "  -d 'nonce=" . wp_create_nonce('cart_nonce') . "'\n\n";

echo "# Test de imágenes:\n";
echo "curl -s '$test_url' | grep -i 'loscocos-product-placeholder'\n\n";

echo "# Test JavaScript:\n";
echo "curl -s '$test_url' | grep -i 'updateQuantity'\n";
echo "</pre>";

echo "</div>";

// TEST 7: Recomendaciones de solución
echo "<div class='test-section'>";
echo "<h2>💡 TEST 7: Diagnóstico y Soluciones</h2>";

$issues = [];
$solutions = [];

// Verificar problemas comunes
if (empty(WC()->cart->get_cart())) {
    $issues[] = "Carrito vacío - no se pueden probar funcionalidades";
    $solutions[] = "Agregar productos al carrito antes de probar";
}

if (!function_exists('handle_update_cart_quantity')) {
    $issues[] = "Función AJAX handle_update_cart_quantity no existe";
    $solutions[] = "Verificar que functions.php contenga las funciones AJAX";
}

if (!$cart_template) {
    $issues[] = "Template page-cart.php no encontrado";
    $solutions[] = "Crear o verificar el template del carrito";
}

echo "<h3>🔍 Problemas detectados:</h3>";
if (!empty($issues)) {
    foreach ($issues as $issue) {
        echo "❌ " . $issue . "<br>";
    }
} else {
    echo "✅ No se detectaron problemas obvios<br>";
}

echo "<h3>🛠️ Soluciones recomendadas:</h3>";
if (!empty($solutions)) {
    foreach ($solutions as $solution) {
        echo "💡 " . $solution . "<br>";
    }
} else {
    echo "✅ Sistema funcionando correctamente<br>";
}

echo "<h3>🔄 Próximos pasos:</h3>";
echo "1. Visitar <a href='$test_url' target='_blank'>la página del carrito</a><br>";
echo "2. Abrir Developer Tools (F12) y revisar la consola<br>";
echo "3. Probar los botones + y - en los productos<br>";
echo "4. Verificar que aparezcan las notificaciones<br>";
echo "5. Comprobar que se actualicen las cantidades<br>";

echo "</div>";

// Limpiar el carrito al final
WC()->cart->empty_cart();
echo "<div class='info'>🧹 Carrito limpiado después del testing</div>";

?>

<script>
console.log('🧪 Script de testing cargado');
console.log('🔗 Para probar AJAX manualmente, usar:');
console.log(`
fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
    method: 'POST',
    body: new FormData(Object.assign(document.createElement('form'), {
        innerHTML: '<input name="action" value="update_cart_quantity"><input name="cart_key" value="TEST"><input name="quantity" value="5"><input name="nonce" value="<?php echo wp_create_nonce('cart_nonce'); ?>">'
    }))
}).then(r => r.text()).then(console.log);
`);
</script> 