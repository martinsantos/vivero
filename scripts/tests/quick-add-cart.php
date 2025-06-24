<?php
// Script rápido para agregar productos al carrito
require_once 'wp-config.php';

// Inicializar WooCommerce si existe
if (class_exists('WooCommerce')) {
    WC()->frontend_includes();
    WC()->session = new WC_Session_Handler();
    WC()->session->init();
    WC()->customer = new WC_Customer(get_current_user_id(), true);
    WC()->cart = new WC_Cart();
    
    // Limpiar carrito
    WC()->cart->empty_cart();
    
    // Agregar algunos productos específicos
    $products_to_add = array(548, 549, 550);
    
    foreach ($products_to_add as $product_id) {
        $product = wc_get_product($product_id);
        if ($product && $product->is_purchasable()) {
            WC()->cart->add_to_cart($product_id, 1);
        }
    }
    
    // Redirigir al carrito inmediatamente
    wp_redirect(home_url('/cart/'));
    exit;
} else {
    echo "WooCommerce no está activo";
}
?> 