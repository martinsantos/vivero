<?php
/*
Template Name: Add to Cart Direct
*/

get_header();

// Agregar productos al carrito si no están ya
if (class_exists('WooCommerce') && WC()->cart->get_cart_contents_count() == 0) {
    $products = wc_get_products(array(
        'limit' => 3,
        'status' => 'publish'
    ));
    
    if (!empty($products)) {
        WC()->cart->empty_cart();
        
        foreach ($products as $product) {
            WC()->cart->add_to_cart($product->get_id(), 1);
        }
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 text-center">✅ Productos Agregados al Carrito</h1>
    
    <?php if (class_exists('WooCommerce')): ?>
        <div class="text-center mb-8">
            <p class="text-lg mb-4">
                Total de productos en carrito: 
                <span class="font-bold text-green-600"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
            </p>
            
            <a href="/cart/" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg inline-block">
                🛒 Ver Carrito
            </a>
        </div>
        
        <div class="bg-green-50 p-6 rounded-lg">
            <h2 class="text-xl font-semibold mb-4">Productos agregados:</h2>
            <?php
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $product = $cart_item['data'];
                $product_id = $cart_item['product_id'];
                ?>
                <div class="flex items-center mb-4 p-4 bg-white rounded-lg shadow">
                    <div class="w-16 h-16 mr-4 border-2 border-green-500 rounded-lg overflow-hidden">
                        <?php echo loscocos_get_cart_product_image($product_id, 'thumbnail'); ?>
                    </div>
                    <div>
                        <h3 class="font-semibold"><?php echo $product->get_name(); ?></h3>
                        <p class="text-gray-600">ID: <?php echo $product_id; ?></p>
                        <p class="text-green-600 font-bold"><?php echo $product->get_price_html(); ?></p>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    <?php else: ?>
        <p class="text-red-600">WooCommerce no está activo</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?> 