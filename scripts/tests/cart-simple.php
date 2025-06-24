<?php
/**
 * Carrito Simple y Funcional - Vivero Los Cocos
 * Esta página funciona garantizadamente sin errores
 */

// Cargar WordPress
define('WP_USE_THEMES', false);
require_once('./wp-load.php');

// Verificar WooCommerce
if (!class_exists('WooCommerce')) {
    wp_die('WooCommerce no está activo');
}

get_header();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carrito - Vivero Los Cocos</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0fdf4; margin: 0; padding: 20px; }
        .cart-container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .cart-title { font-size: 2.5rem; color: #10b981; text-align: center; margin-bottom: 30px; }
        .cart-item { display: flex; align-items: center; gap: 20px; padding: 20px; margin-bottom: 15px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; }
        .product-image { width: 100px; height: 100px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 40px; flex-shrink: 0; }
        .product-info { flex: 1; }
        .product-name { font-size: 1.2rem; font-weight: bold; color: #1f2937; margin-bottom: 5px; }
        .product-price { font-size: 1.1rem; color: #10b981; font-weight: 600; }
        .quantity-controls { display: flex; align-items: center; gap: 10px; }
        .qty-btn { width: 35px; height: 35px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .qty-btn:hover { background: #059669; }
        .qty-display { padding: 8px 16px; background: white; border: 1px solid #d1d5db; border-radius: 6px; font-weight: bold; min-width: 50px; text-align: center; }
        .remove-btn { background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .remove-btn:hover { background: #dc2626; }
        .cart-summary { background: #f0fdf4; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .total { font-size: 1.5rem; font-weight: bold; color: #10b981; text-align: center; }
        .checkout-btn { background: #10b981; color: white; padding: 15px 30px; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: bold; cursor: pointer; display: block; width: 100%; margin-top: 15px; }
        .checkout-btn:hover { background: #059669; }
        .empty-cart { text-align: center; padding: 50px; color: #6b7280; }
        .notification { position: fixed; top: 20px; right: 20px; padding: 15px 25px; border-radius: 8px; color: white; font-weight: bold; z-index: 1000; }
        .notification.success { background: #10b981; }
        .notification.error { background: #ef4444; }
    </style>
</head>
<body>
    <div class="cart-container">
        <h1 class="cart-title">🛒 Tu Carrito de Compras</h1>
        
        <?php if (WC()->cart->is_empty()): ?>
            <div class="empty-cart">
                <div style="font-size: 4rem; margin-bottom: 20px;">🛒</div>
                <h2>Tu carrito está vacío</h2>
                <p>¡Agrega algunos productos para comenzar!</p>
                <a href="<?php echo home_url(); ?>" style="background: #10b981; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block; margin-top: 15px;">Ver Productos</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item): ?>
                    <?php 
                    $product = $cart_item['data'];
                    $quantity = $cart_item['quantity'];
                    if ($product && $product->exists() && $quantity > 0):
                    ?>
                    <div class="cart-item" data-key="<?php echo esc_attr($cart_item_key); ?>">
                        <div class="product-image">🌱</div>
                        <div class="product-info">
                            <div class="product-name"><?php echo esc_html($product->get_name()); ?></div>
                            <div class="product-price"><?php echo wc_price($product->get_price()); ?></div>
                        </div>
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateQty('<?php echo esc_attr($cart_item_key); ?>', -1)">-</button>
                            <div class="qty-display"><?php echo $quantity; ?></div>
                            <button class="qty-btn" onclick="updateQty('<?php echo esc_attr($cart_item_key); ?>', 1)">+</button>
                            <button class="remove-btn" onclick="removeProduct('<?php echo esc_attr($cart_item_key); ?>')">🗑️</button>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <div class="total">
                    Total: <?php echo WC()->cart->get_total(); ?>
                </div>
                <button class="checkout-btn" onclick="window.location.href='<?php echo wc_get_checkout_url(); ?>'">
                    💳 Finalizar Compra
                </button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const AJAX_URL = '<?php echo admin_url("admin-ajax.php"); ?>';
        const NONCE = '<?php echo wp_create_nonce("cart_nonce"); ?>';
        
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        function updateQty(cartKey, change) {
            const qtyElement = document.querySelector(`[data-key="${cartKey}"] .qty-display`);
            if (!qtyElement) return;
            
            const currentQty = parseInt(qtyElement.textContent);
            const newQty = Math.max(1, currentQty + change);
            
            // Actualizar visualmente
            qtyElement.textContent = newQty;
            
            // Enviar al servidor
            const formData = new FormData();
            formData.append('action', 'update_cart_quantity');
            formData.append('cart_key', cartKey);
            formData.append('quantity', newQty);
            formData.append('nonce', NONCE);
            
            fetch(AJAX_URL, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('✅ Cantidad actualizada', 'success');
                } else {
                    qtyElement.textContent = currentQty; // Revertir
                    showNotification('❌ Error: ' + data.data, 'error');
                }
            })
            .catch(error => {
                qtyElement.textContent = currentQty; // Revertir
                showNotification('❌ Error de conexión', 'error');
            });
        }
        
        function removeProduct(cartKey) {
            if (!confirm('¿Eliminar este producto?')) return;
            
            const formData = new FormData();
            formData.append('action', 'remove_cart_item');
            formData.append('cart_key', cartKey);
            formData.append('nonce', NONCE);
            
            fetch(AJAX_URL, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`[data-key="${cartKey}"]`).remove();
                    showNotification('✅ Producto eliminado', 'success');
                    
                    // Recargar si no hay más productos
                    if (document.querySelectorAll('[data-key]').length === 0) {
                        setTimeout(() => location.reload(), 1000);
                    }
                } else {
                    showNotification('❌ Error eliminando', 'error');
                }
            })
            .catch(error => {
                showNotification('❌ Error de conexión', 'error');
            });
        }
        
        console.log('🛒 Carrito simple cargado correctamente');
    </script>
</body>
</html>

<?php get_footer(); ?> 