<?php
/*
Template Name: Add to Cart Quick
*/

get_header();

// Verificar si se debe agregar productos
if (isset($_GET['add_products']) && $_GET['add_products'] == '1') {
    // Limpiar carrito
    WC()->cart->empty_cart();
    
    // Obtener productos
    $products = wc_get_products(array(
        'limit' => 5,
        'status' => 'publish'
    ));
    
    $added_count = 0;
    if (!empty($products)) {
        foreach (array_slice($products, 0, 3) as $product) {
            $result = WC()->cart->add_to_cart($product->get_id(), 1);
            if ($result) {
                $added_count++;
            }
        }
    }
    
    // Redireccionar al carrito
    wp_redirect(wc_get_cart_url());
    exit;
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto text-center">
        <h1 class="text-3xl font-bold mb-6 text-green-600">🛒 Configurar Carrito</h1>
        
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <p class="text-gray-600 mb-6">
                Para probar las imágenes del carrito, necesitamos agregar algunos productos.
            </p>
            
            <div class="space-y-4">
                <a href="?add_products=1" 
                   class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                    ✅ Agregar Productos al Carrito
                </a>
                
                <br>
                
                <a href="<?php echo wc_get_cart_url(); ?>" 
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    🛒 Ver Carrito
                </a>
                
                <br>
                
                <a href="<?php echo wc_get_page_permalink('shop'); ?>" 
                   class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                    🛍️ Ir a la Tienda
                </a>
            </div>
        </div>
        
        <div class="bg-gray-100 rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Estado Actual del Carrito</h2>
            
            <?php if (WC()->cart->is_empty()): ?>
                <p class="text-red-600">❌ El carrito está vacío</p>
            <?php else: ?>
                <p class="text-green-600">✅ El carrito tiene <?php echo WC()->cart->get_cart_contents_count(); ?> productos</p>
                
                <div class="mt-4 space-y-2">
                    <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item): ?>
                        <?php $product = $cart_item['data']; ?>
                        <div class="text-sm text-gray-700">
                            • <?php echo $product->get_name(); ?> (Cantidad: <?php echo $cart_item['quantity']; ?>)
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?> 