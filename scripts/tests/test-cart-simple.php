<?php
define('WP_USE_THEMES', false);
require_once('./wp-config.php');

// Limpiar y agregar productos
WC()->cart->empty_cart();
$products = wc_get_products(['limit' => 2]);
if (!empty($products)) {
    WC()->cart->add_to_cart($products[0]->get_id(), 2);
}

$cart_items = WC()->cart->get_cart();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Carrito Simple</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0fdf4; }
        .cart-item { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; border: 2px solid #10b981; }
        .quantity-controls { display: flex; align-items: center; gap: 10px; margin: 10px 0; }
        .btn { padding: 10px 15px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-primary { background: #10b981; color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .quantity { background: #f9f9f9; padding: 8px 16px; border: 1px solid #ddd; border-radius: 4px; font-weight: bold; min-width: 30px; text-align: center; }
        .log { background: #f1f1f1; padding: 10px; border-radius: 4px; margin: 10px 0; font-family: monospace; font-size: 12px; max-height: 150px; overflow-y: auto; }
    </style>
</head>
<body>
    <h1>🛒 Test Carrito Simple</h1>
    
    <div class="log" id="log">Logs aparecerán aquí...</div>
    
    <?php foreach ($cart_items as $cart_key => $cart_item): ?>
        <?php $product = $cart_item['data']; ?>
        
        <div class="cart-item" data-key="<?php echo esc_attr($cart_key); ?>">
            <h3><?php echo esc_html($product->get_name()); ?></h3>
            <p>Cart Key: <code><?php echo $cart_key; ?></code></p>
            
            <div class="quantity-controls">
                <button class="btn btn-primary" onclick="updateQuantity('<?php echo esc_attr($cart_key); ?>', -1)">-</button>
                <span class="quantity"><?php echo $cart_item['quantity']; ?></span>
                <button class="btn btn-primary" onclick="updateQuantity('<?php echo esc_attr($cart_key); ?>', 1)">+</button>
                <button class="btn btn-danger" onclick="removeItem('<?php echo esc_attr($cart_key); ?>')">🗑️</button>
            </div>
        </div>
        
    <?php endforeach; ?>
    
    <div style="margin-top: 20px;">
        <button class="btn btn-primary" onclick="testAll()">🧪 Test Todo</button>
        <button class="btn btn-primary" onclick="location.reload()">🔄 Recargar</button>
    </div>

    <script>
        // Variables globales
        const AJAX_URL = '<?php echo admin_url("admin-ajax.php"); ?>';
        const CART_NONCE = '<?php echo wp_create_nonce("cart_nonce"); ?>';
        
        function log(message) {
            const logEl = document.getElementById('log');
            const time = new Date().toLocaleTimeString();
            logEl.innerHTML += `[${time}] ${message}<br>`;
            logEl.scrollTop = logEl.scrollHeight;
            console.log(message);
        }
        
        function updateQuantity(cartKey, change) {
            log(`🔄 updateQuantity: ${cartKey}, cambio: ${change}`);
            
            const quantityElement = document.querySelector(`[data-key="${cartKey}"] .quantity`);
            if (!quantityElement) {
                log('❌ No se encontró elemento de cantidad');
                return;
            }
            
            const currentQty = parseInt(quantityElement.textContent);
            const newQty = Math.max(1, currentQty + change);
            
            log(`📊 Cantidad: ${currentQty} -> ${newQty}`);
            
            // Actualizar visualmente
            quantityElement.textContent = newQty;
            
            // Enviar AJAX
            const formData = new FormData();
            formData.append('action', 'update_cart_quantity');
            formData.append('cart_key', cartKey);
            formData.append('quantity', newQty);
            formData.append('nonce', CART_NONCE);
            
            log(`📤 Enviando AJAX...`);
            
            fetch(AJAX_URL, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                log(`📡 Respuesta: ${response.status}`);
                return response.text();
            })
            .then(text => {
                log(`📄 Texto: ${text.substring(0, 50)}...`);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        log('✅ Éxito!');
                    } else {
                        log(`❌ Error: ${data.data}`);
                        quantityElement.textContent = currentQty;
                    }
                } catch (e) {
                    log(`❌ Error parseando: ${e}`);
                    quantityElement.textContent = currentQty;
                }
            })
            .catch(error => {
                log(`❌ Error red: ${error}`);
                quantityElement.textContent = currentQty;
            });
        }
        
        function removeItem(cartKey) {
            log(`🗑️ removeItem: ${cartKey}`);
            
            if (!confirm('¿Eliminar?')) return;
            
            const formData = new FormData();
            formData.append('action', 'remove_cart_item');
            formData.append('cart_key', cartKey);
            formData.append('nonce', CART_NONCE);
            
            fetch(AJAX_URL, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                log(`📄 Respuesta eliminar: ${text.substring(0, 50)}...`);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        log('✅ Eliminado!');
                        document.querySelector(`[data-key="${cartKey}"]`).remove();
                    } else {
                        log(`❌ Error eliminando: ${data.data}`);
                    }
                } catch (e) {
                    log(`❌ Error parseando eliminar: ${e}`);
                }
            })
            .catch(error => {
                log(`❌ Error red eliminando: ${error}`);
            });
        }
        
        function testAll() {
            log('🧪 Iniciando tests...');
            log(`🔑 AJAX_URL: ${AJAX_URL}`);
            log(`🔐 CART_NONCE: ${CART_NONCE}`);
            
            const items = document.querySelectorAll('[data-key]');
            log(`📦 Items encontrados: ${items.length}`);
            
            items.forEach((item, i) => {
                const key = item.getAttribute('data-key');
                log(`📦 Item ${i+1}: ${key}`);
            });
            
            if (items.length > 0) {
                const firstKey = items[0].getAttribute('data-key');
                log(`🧪 Test automático en 2 segundos: ${firstKey}`);
                setTimeout(() => {
                    updateQuantity(firstKey, 1);
                }, 2000);
            }
        }
        
        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            log('🚀 Carrito simple cargado');
            log(`Variables: AJAX_URL=${AJAX_URL}, NONCE=${CART_NONCE}`);
            
            setTimeout(() => {
                log('⏰ Auto-test en 3 segundos...');
                testAll();
            }, 3000);
        });
    </script>
</body>
</html> 