<?php
/*
Template Name: Quick Add to Cart
*/

// Cargar WordPress
require_once dirname(dirname(dirname(__FILE__))) . '/wp-load.php';

// Verificar si es una petición AJAX
if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart_quick') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']) ?: 1;
    
    if ($product_id > 0) {
        $added = WC()->cart->add_to_cart($product_id, $quantity);
        
        if ($added) {
            wp_send_json_success(array(
                'message' => 'Producto agregado al carrito',
                'cart_count' => WC()->cart->get_cart_contents_count()
            ));
        } else {
            wp_send_json_error('Error al agregar producto');
        }
    } else {
        wp_send_json_error('ID de producto inválido');
    }
}

// Si no es AJAX, mostrar interfaz
get_header();
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">🛒 Agregar Productos al Carrito</h1>
    
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
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="mb-4">
                <?php echo loscocos_get_cart_product_image($product_id, 'medium'); ?>
            </div>
            <h3 class="text-lg font-semibold mb-2"><?php echo $product_name; ?></h3>
            <p class="text-gray-600 mb-4"><?php echo $product_price; ?></p>
            <button 
                onclick="addToCartQuick(<?php echo $product_id; ?>)"
                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition-colors"
            >
                Añadir al Carrito
            </button>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="mt-8 text-center">
        <p class="mb-4">Productos en carrito: <span id="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span></p>
        <a href="/cart/" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg">
            Ver Carrito 🛒
        </a>
    </div>
</div>

<script>
function addToCartQuick(productId) {
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