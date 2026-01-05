<?php
/*
Template Name: Add Products to Cart
*/

get_header();
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 text-center">🛒 Agregar Productos al Carrito</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $products = wc_get_products(array(
            'limit' => 6,
            'status' => 'publish'
        ));
        
        foreach ($products as $product):
            $product_id = $product->get_id();
            $product_name = $product->get_name();
            $product_price = $product->get_price_html();
        ?>
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="mb-4 w-full h-48 overflow-hidden rounded-lg">
                <?php echo loscocos_get_cart_product_image($product_id, 'medium'); ?>
            </div>
            <h3 class="text-lg font-semibold mb-2"><?php echo esc_html($product_name); ?></h3>
            <p class="text-gray-600 mb-4"><?php echo $product_price; ?></p>
            <button 
                onclick="addToCartQuick(<?php echo $product_id; ?>)"
                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition-colors"
            >
                ➕ Añadir al Carrito
            </button>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="mt-8 text-center bg-green-50 p-6 rounded-lg">
        <p class="mb-4 text-lg">Productos en carrito: <span id="cart-count" class="font-bold text-green-600"><?php echo WC()->cart->get_cart_contents_count(); ?></span></p>
        <a href="/cart/" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg inline-block">
            🛒 Ver Carrito
        </a>
    </div>
</div>

<script>
function addToCartQuick(productId) {
    console.log('Agregando producto:', productId);
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'add_to_cart_quick',
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta:', data);
        if (data.success) {
            alert('✅ ' + data.data.message);
            document.getElementById('cart-count').textContent = data.data.cart_count;
        } else {
            alert('❌ Error: ' + data.data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error de conexión');
    });
}
</script>

<?php get_footer(); ?> 