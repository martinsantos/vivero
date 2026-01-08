<?php
/**
 * Debug de producto individual
 */

// Obtener el producto por slug
$product_slug = 'foratrona10l-10-litros';
$product_post = get_page_by_path($product_slug, OBJECT, 'product');

if ($product_post) {
    $product = wc_get_product($product_post->ID);
    
    echo '<h1>DEBUG - Producto Individual</h1>';
    echo '<div style="background: #f0f0f0; padding: 20px; margin: 20px 0;">';
    echo '<h2>Información del producto:</h2>';
    echo '<ul>';
    echo '<li><strong>ID:</strong> ' . $product->get_id() . '</li>';
    echo '<li><strong>Nombre:</strong> ' . $product->get_name() . '</li>';
    echo '<li><strong>Precio:</strong> $' . number_format($product->get_price(), 0, ',', '.') . '</li>';
    echo '<li><strong>Stock:</strong> ' . ($product->is_in_stock() ? 'Disponible' : 'Agotado') . '</li>';
    echo '<li><strong>Descripción:</strong> ' . wp_trim_words($product->get_description(), 20) . '</li>';
    echo '</ul>';
    echo '</div>';
    
    echo '<div style="background: #e0f0e0; padding: 20px; margin: 20px 0;">';
    echo '<h2>Template de producto:</h2>';
    echo '<div style="border: 1px solid #ccc; padding: 15px; background: white;">';
    
    // Simular el template
    $product_image = loscocos_get_product_image($product->get_id());
    echo '<img src="' . $product_image . '" alt="' . $product->get_name() . '" style="width: 200px; height: 200px; object-fit: cover; margin-bottom: 15px;">';
    echo '<h3>' . $product->get_name() . '</h3>';
    echo '<p><strong>Precio: $' . number_format($product->get_price(), 0, ',', '.') . '</strong></p>';
    echo '<button style="background: #10b981; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Añadir al Carrito</button>';
    
    echo '</div>';
    echo '</div>';
    
} else {
    echo '<h1>Producto no encontrado</h1>';
    echo '<p>No se pudo encontrar el producto con slug: ' . $product_slug . '</p>';
}
?>