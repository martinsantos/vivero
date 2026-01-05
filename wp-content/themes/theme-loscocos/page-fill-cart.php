<?php
/*
Template Name: Fill Cart
*/

// Agregar productos al carrito
WC()->cart->empty_cart();

$products = wc_get_products(array(
    'limit' => 5,
    'status' => 'publish'
));

$added = 0;
if (!empty($products)) {
    foreach (array_slice($products, 0, 3) as $product) {
        $result = WC()->cart->add_to_cart($product->get_id(), 1);
        if ($result) {
            $added++;
        }
    }
}

// Redireccionar al carrito
wp_redirect(wc_get_cart_url());
exit;
?> 