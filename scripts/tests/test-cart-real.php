<?php
// Test del carrito real
require_once 'wp-config.php';

// Agregar producto al carrito si no hay nada
if (isset($_GET['add'])) {
    WC()->cart->add_to_cart(548, 1); // Agregar producto ID 548
    echo "Producto agregado al carrito<br>";
}

// Mostrar contenido del carrito
echo "<h1>🛒 Test del Carrito Real</h1>";

if (WC()->cart->get_cart_contents_count() > 0) {
    echo "<p>Productos en carrito: " . WC()->cart->get_cart_contents_count() . "</p>";
    
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        $product_id = $cart_item['product_id'];
        
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<h3>Producto: " . $product->get_name() . " (ID: $product_id)</h3>";
        
        // Test directo de la función
        if (function_exists('loscocos_get_cart_product_image')) {
            echo "<p style='color: green;'>✅ Función existe</p>";
            $image_html = loscocos_get_cart_product_image($product_id, 'medium');
            echo "<p><strong>Imagen generada:</strong></p>";
            echo "<div style='border: 2px solid #000; padding: 5px; width: 150px; height: 150px;'>";
            echo $image_html;
            echo "</div>";
            echo "<p><strong>HTML:</strong></p>";
            echo "<textarea rows='5' cols='60'>" . htmlspecialchars($image_html) . "</textarea>";
        } else {
            echo "<p style='color: red;'>❌ Función NO existe</p>";
        }
        
        // Comparar con imagen original
        echo "<p><strong>Imagen original WooCommerce:</strong></p>";
        echo "<div style='border: 2px solid #00f; padding: 5px; width: 150px; height: 150px;'>";
        echo $product->get_image('medium');
        echo "</div>";
        
        echo "</div>";
    }
    
    echo "<p><a href='?clear=1'>🗑️ Vaciar carrito</a></p>";
} else {
    echo "<p>El carrito está vacío</p>";
    echo "<p><a href='?add=1'>➕ Agregar producto de prueba</a></p>";
}

// Vaciar carrito si se solicita
if (isset($_GET['clear'])) {
    WC()->cart->empty_cart();
    echo "<script>window.location.href = 'test-cart-real.php';</script>";
}

echo "<hr>";
echo "<p><a href='/cart/'>🛒 Ver carrito oficial</a> | <a href='/'>🏠 Inicio</a></p>";
?> 