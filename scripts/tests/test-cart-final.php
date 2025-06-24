<?php
require_once 'wp-config.php';
require_once ABSPATH . 'wp-load.php';

echo "<!DOCTYPE html>\n<html>\n<head>\n<title>Test Carrito Final</title>\n</head>\n<body>\n";
echo "<h1>🛒 Test Final del Carrito - Los Cocos</h1>\n";

// Verificar si hay productos en el carrito
if (WC()->cart->is_empty()) {
    echo "<p>❌ El carrito está vacío. Agregando un producto de prueba...</p>\n";
    
    // Obtener el primer producto disponible
    $products = wc_get_products(array('limit' => 1, 'status' => 'publish'));
    if (!empty($products)) {
        $product = $products[0];
        WC()->cart->add_to_cart($product->get_id(), 1);
        echo "<p>✅ Producto agregado: " . $product->get_name() . " (ID: " . $product->get_id() . ")</p>\n";
    } else {
        echo "<p>❌ No hay productos disponibles</p>\n";
        exit;
    }
}

echo "<h2>📋 Contenido del Carrito:</h2>\n";
echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>\n";
echo "<tr><th>Imagen</th><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr>\n";

foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
    $product = $cart_item['data'];
    $product_id = $cart_item['product_id'];
    
    echo "<tr>\n";
    echo "<td style='padding: 10px;'>";
    
    // USAR NUESTRA FUNCIÓN PERSONALIZADA
    $image_html = loscocos_get_cart_product_image($product_id, 'woocommerce_thumbnail');
    echo $image_html;
    
    echo "</td>\n";
    echo "<td style='padding: 10px;'>" . $product->get_name() . "</td>\n";
    echo "<td style='padding: 10px;'>" . wc_price($product->get_price()) . "</td>\n";
    echo "<td style='padding: 10px;'>" . $cart_item['quantity'] . "</td>\n";
    echo "<td style='padding: 10px;'>" . wc_price($product->get_price() * $cart_item['quantity']) . "</td>\n";
    echo "</tr>\n";
}

echo "</table>\n";

echo "<h2>🔍 Debug de la Función:</h2>\n";
$products = wc_get_products(array('limit' => 1, 'status' => 'publish'));
if (!empty($products)) {
    $product = $products[0];
    $product_id = $product->get_id();
    
    echo "<p><strong>Producto de prueba:</strong> " . $product->get_name() . " (ID: $product_id)</p>\n";
    echo "<p><strong>Función result:</strong></p>\n";
    echo "<div style='border: 2px solid #10b981; padding: 10px; margin: 10px 0; background: #f0f9ff;'>\n";
    echo loscocos_get_cart_product_image($product_id, 'woocommerce_thumbnail');
    echo "</div>\n";
    
    echo "<p><strong>HTML generado:</strong></p>\n";
    echo "<pre style='background: #f5f5f5; padding: 10px; font-size: 12px;'>";
    echo htmlspecialchars(loscocos_get_cart_product_image($product_id, 'woocommerce_thumbnail'));
    echo "</pre>\n";
}

echo "<h2>🔗 Enlaces:</h2>\n";
echo "<p><a href='http://localhost:8080/cart/' target='_blank'>🛒 Ver Carrito Real</a></p>\n";
echo "<p><a href='http://localhost:8080/' target='_blank'>🏠 Ir al Home</a></p>\n";

echo "</body>\n</html>";
?> 