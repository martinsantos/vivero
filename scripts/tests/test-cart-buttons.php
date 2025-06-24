<?php
/**
 * TEST ESPECÍFICO DE BOTONES DEL CARRITO
 */

define('WP_USE_THEMES', false);
require_once('./wp-config.php');

header('Content-Type: text/html; charset=utf-8');

// Limpiar y agregar productos
WC()->cart->empty_cart();
$products = wc_get_products(['limit' => 2]);

if (!empty($products)) {
    foreach ($products as $product) {
        WC()->cart->add_to_cart($product->get_id(), 2);
    }
}

$cart_items = WC()->cart->get_cart();
$cart_keys = array_keys($cart_items);
$test_nonce = wp_create_nonce('cart_nonce');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Botones Carrito</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0fdf4; }
        .test-container { max-width: 800px; margin: 0 auto; }
        .product-test { background: white; padding: 20px; margin: 10px 0; border-radius: 12px; border: 2px solid #10b981; }
        .quantity-controls { display: flex; align-items: center; gap: 10px; margin: 10px 0; }
        .btn { padding: 10px 15px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-primary { background: #10b981; color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .result { background: #f1f1f1; padding: 10px; border-radius: 4px; margin: 10px 0; font-family: monospace; }
        .quantity-display { background: #f9f9f9; padding: 8px 16px; border: 1px solid #ddd; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🧪 Test de Botones del Carrito</h1>
        
        <div class="result">
            <strong>Estado Inicial:</strong><br>
            Productos en carrito: <?php echo WC()->cart->get_cart_contents_count(); ?><br>
            Total: <?php echo WC()->cart->get_total(); ?><br>
            Nonce: <?php echo $test_nonce; ?>
        </div>

        <?php if (!empty($cart_items)): ?>
            <?php foreach ($cart_items as $cart_key => $cart_item): ?>
                <?php 
                $product = $cart_item['data'];
                $quantity = $cart_item['quantity'];
                ?>
                
                <div class="product-test" data-key="<?php echo esc_attr($cart_key); ?>">
                    <h3><?php echo esc_html($product->get_name()); ?></h3>
                    <p>Cart Key: <code><?php echo $cart_key; ?></code></p>
                    
                    <div class="quantity-controls">
                        <button class="btn btn-primary" onclick="updateQuantity('<?php echo esc_attr($cart_key); ?>', -1)">-</button>
                        <span class="quantity-display"><?php echo $quantity; ?></span>
                        <button class="btn btn-primary" onclick="updateQuantity('<?php echo esc_attr($cart_key); ?>', 1)">+</button>
                        <button class="btn btn-danger" onclick="removeItem('<?php echo esc_attr($cart_key); ?>')">🗑️ Eliminar</button>
                    </div>
                </div>
                
            <?php endforeach; ?>
        <?php else: ?>
            <div class="result">❌ No hay productos en el carrito para probar</div>
        <?php endif; ?>
        
        <div id="ajax-results" class="result">Los resultados de AJAX aparecerán aquí...</div>
        
        <div style="margin-top: 20px;">
            <button class="btn btn-primary" onclick="location.reload()">🔄 Recargar Test</button>
            <a href="<?php echo home_url('/cart/'); ?>" class="btn btn-primary">🛒 Ir al Carrito Real</a>
        </div>
    </div>

    <script>
        const AJAX_URL = '<?php echo admin_url("admin-ajax.php"); ?>';
        const CART_NONCE = '<?php echo $test_nonce; ?>';
        
        function logResult(message, type = 'info') {
            const results = document.getElementById('ajax-results');
            const timestamp = new Date().toLocaleTimeString();
            const color = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#333';
            
            results.innerHTML += `<div style="color: ${color};">[${timestamp}] ${message}</div>`;
            results.scrollTop = results.scrollHeight;
        }
        
        function updateQuantity(cartKey, change) {
            logResult(`🔄 Iniciando updateQuantity: ${cartKey}, cambio: ${change}`);
            
            const quantityElement = document.querySelector(`[data-key="${cartKey}"] .quantity-display`);
            if (!quantityElement) {
                logResult('❌ No se encontró elemento de cantidad', 'error');
                return;
            }
            
            const currentQty = parseInt(quantityElement.textContent);
            const newQty = Math.max(1, currentQty + change);
            
            logResult(`📊 Cantidad: ${currentQty} -> ${newQty}`);
            
            // Actualizar visualmente
            quantityElement.textContent = newQty;
            
            // Enviar AJAX
            const formData = new FormData();
            formData.append('action', 'update_cart_quantity');
            formData.append('cart_key', cartKey);
            formData.append('quantity', newQty);
            formData.append('nonce', CART_NONCE);
            
            fetch(AJAX_URL, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                logResult(`📡 Respuesta recibida: ${response.status}`);
                return response.json();
            })
            .then(data => {
                logResult(`📦 Datos: ${JSON.stringify(data)}`);
                
                if (data.success) {
                    logResult('✅ Cantidad actualizada correctamente', 'success');
                } else {
                    logResult(`❌ Error: ${data.data}`, 'error');
                    // Revertir cambio
                    quantityElement.textContent = currentQty;
                }
            })
            .catch(error => {
                logResult(`❌ Error de red: ${error}`, 'error');
                // Revertir cambio
                quantityElement.textContent = currentQty;
            });
        }
        
        function removeItem(cartKey) {
            if (!confirm('¿Eliminar este producto?')) return;
            
            logResult(`🗑️ Eliminando producto: ${cartKey}`);
            
            const formData = new FormData();
            formData.append('action', 'remove_cart_item');
            formData.append('cart_key', cartKey);
            formData.append('nonce', CART_NONCE);
            
            fetch(AJAX_URL, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                logResult(`📦 Respuesta eliminar: ${JSON.stringify(data)}`);
                
                if (data.success) {
                    logResult('✅ Producto eliminado correctamente', 'success');
                    document.querySelector(`[data-key="${cartKey}"]`).remove();
                } else {
                    logResult(`❌ Error eliminando: ${data.data}`, 'error');
                }
            })
            .catch(error => {
                logResult(`❌ Error eliminando: ${error}`, 'error');
            });
        }
        
        // Log inicial
        logResult('🚀 Test de botones iniciado');
        logResult(`🔑 AJAX URL: ${AJAX_URL}`);
        logResult(`🔐 Nonce: ${CART_NONCE}`);
        
        // Test automático
        setTimeout(() => {
            logResult('🧪 Ejecutando test automático en 3 segundos...');
        }, 1000);
    </script>
</body>
</html> 