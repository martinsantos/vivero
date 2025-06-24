<?php
/**
 * DIAGNÓSTICO FINAL - BOTONES DEL CARRITO
 */

define('WP_USE_THEMES', false);
require_once('./wp-config.php');

header('Content-Type: text/html; charset=utf-8');

// Limpiar y agregar productos
WC()->cart->empty_cart();
$products = wc_get_products(['limit' => 2]);

if (!empty($products)) {
    foreach ($products as $product) {
        WC()->cart->add_to_cart($product->get_id(), 3);
    }
}

$cart_items = WC()->cart->get_cart();
$nonce = wp_create_nonce('cart_nonce');

?>
<!DOCTYPE html>
<html>
<head>
    <title>🔍 Diagnóstico Final - Botones Carrito</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0fdf4; }
        .container { max-width: 1000px; margin: 0 auto; }
        .section { background: white; padding: 20px; margin: 15px 0; border-radius: 12px; border: 2px solid #10b981; }
        .product-item { background: #f9f9f9; padding: 15px; margin: 10px 0; border-radius: 8px; border: 1px solid #ddd; }
        .quantity-controls { display: flex; align-items: center; gap: 10px; margin: 10px 0; }
        .btn { padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-primary { background: #10b981; color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .quantity-display { background: white; padding: 8px 16px; border: 1px solid #ddd; border-radius: 4px; font-weight: bold; min-width: 30px; text-align: center; }
        .log { background: #f1f1f1; padding: 10px; border-radius: 4px; margin: 10px 0; font-family: monospace; font-size: 12px; max-height: 200px; overflow-y: auto; }
        .status { padding: 10px; border-radius: 6px; margin: 10px 0; font-weight: bold; }
        .success { background: #d1fae5; color: #065f46; }
        .error { background: #fee2e2; color: #991b1b; }
        .info { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico Final - Botones del Carrito</h1>
        
        <div class="section">
            <h2>📊 Estado del Sistema</h2>
            <div class="status info">
                <strong>WooCommerce:</strong> <?php echo class_exists('WooCommerce') ? '✅ Activo' : '❌ Inactivo'; ?><br>
                <strong>Productos en carrito:</strong> <?php echo WC()->cart->get_cart_contents_count(); ?><br>
                <strong>Total:</strong> <?php echo WC()->cart->get_total(); ?><br>
                <strong>Nonce generado:</strong> <?php echo $nonce; ?><br>
                <strong>AJAX URL:</strong> <?php echo admin_url('admin-ajax.php'); ?>
            </div>
        </div>

        <div class="section">
            <h2>🛒 Productos en el Carrito</h2>
            <?php if (!empty($cart_items)): ?>
                <?php foreach ($cart_items as $cart_key => $cart_item): ?>
                    <?php 
                    $product = $cart_item['data'];
                    $quantity = $cart_item['quantity'];
                    ?>
                    
                    <div class="product-item" data-key="<?php echo esc_attr($cart_key); ?>">
                        <h3><?php echo esc_html($product->get_name()); ?></h3>
                        <p><strong>Cart Key:</strong> <code><?php echo $cart_key; ?></code></p>
                        <p><strong>Producto ID:</strong> <?php echo $product->get_id(); ?></p>
                        
                        <div class="quantity-controls">
                            <button class="btn btn-primary" onclick="testUpdateQuantity('<?php echo esc_attr($cart_key); ?>', -1)">➖</button>
                            <span class="quantity-display"><?php echo $quantity; ?></span>
                            <button class="btn btn-primary" onclick="testUpdateQuantity('<?php echo esc_attr($cart_key); ?>', 1)">➕</button>
                            <button class="btn btn-danger" onclick="testRemoveItem('<?php echo esc_attr($cart_key); ?>')">🗑️</button>
                        </div>
                        
                        <div class="log" id="log-<?php echo esc_attr($cart_key); ?>">
                            Logs para este producto aparecerán aquí...
                        </div>
                    </div>
                    
                <?php endforeach; ?>
            <?php else: ?>
                <div class="status error">❌ No hay productos en el carrito</div>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>📋 Log General</h2>
            <div class="log" id="general-log">Logs generales aparecerán aquí...</div>
        </div>
        
        <div class="section">
            <h2>🧪 Tests Automáticos</h2>
            <button class="btn btn-primary" onclick="runAllTests()">▶️ Ejecutar Todos los Tests</button>
            <button class="btn btn-primary" onclick="location.reload()">🔄 Recargar</button>
            <a href="<?php echo home_url('/cart/'); ?>" class="btn btn-primary">🛒 Carrito Real</a>
        </div>
    </div>

    <script>
        // Variables globales
        const AJAX_URL = '<?php echo admin_url("admin-ajax.php"); ?>';
        const CART_NONCE = '<?php echo $nonce; ?>';
        
        // Función de logging
        function logToElement(elementId, message, type = 'info') {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            const timestamp = new Date().toLocaleTimeString();
            const color = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#333';
            
            element.innerHTML += `<div style="color: ${color};">[${timestamp}] ${message}</div>`;
            element.scrollTop = element.scrollHeight;
        }
        
        function logGeneral(message, type = 'info') {
            logToElement('general-log', message, type);
            console.log(message);
        }
        
        // Test de actualización de cantidad
        function testUpdateQuantity(cartKey, change) {
            const logId = `log-${cartKey}`;
            logToElement(logId, `🔄 Iniciando test updateQuantity: ${cartKey}, cambio: ${change}`, 'info');
            logGeneral(`🔄 Test updateQuantity: ${cartKey}, cambio: ${change}`);
            
            // Verificar elemento
            const quantityElement = document.querySelector(`[data-key="${cartKey}"] .quantity-display`);
            if (!quantityElement) {
                logToElement(logId, '❌ No se encontró elemento de cantidad', 'error');
                logGeneral('❌ Elemento de cantidad no encontrado', 'error');
                return;
            }
            
            const currentQty = parseInt(quantityElement.textContent);
            const newQty = Math.max(1, currentQty + change);
            
            logToElement(logId, `📊 Cantidad: ${currentQty} -> ${newQty}`, 'info');
            
            // Verificar variables globales
            if (!AJAX_URL || !CART_NONCE) {
                logToElement(logId, `❌ Variables no disponibles: AJAX_URL=${AJAX_URL}, NONCE=${CART_NONCE}`, 'error');
                logGeneral('❌ Variables globales no disponibles', 'error');
                return;
            }
            
            // Actualizar visualmente
            quantityElement.textContent = newQty;
            logToElement(logId, '✅ Actualizado visualmente', 'success');
            
            // Preparar datos
            const formData = new FormData();
            formData.append('action', 'update_cart_quantity');
            formData.append('cart_key', cartKey);
            formData.append('quantity', newQty);
            formData.append('nonce', CART_NONCE);
            
            logToElement(logId, `📤 Enviando: action=update_cart_quantity, cart_key=${cartKey}, quantity=${newQty}`, 'info');
            
            // Enviar AJAX
            fetch(AJAX_URL, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                logToElement(logId, `📡 Respuesta: ${response.status} ${response.statusText}`, 'info');
                return response.text();
            })
            .then(text => {
                logToElement(logId, `📄 Texto recibido: ${text.substring(0, 100)}...`, 'info');
                
                try {
                    const data = JSON.parse(text);
                    logToElement(logId, `📦 JSON parseado: ${JSON.stringify(data)}`, 'info');
                    
                    if (data.success) {
                        logToElement(logId, '✅ Éxito del servidor', 'success');
                        logGeneral(`✅ Cantidad actualizada: ${cartKey}`, 'success');
                    } else {
                        logToElement(logId, `❌ Error del servidor: ${data.data}`, 'error');
                        logGeneral(`❌ Error servidor: ${data.data}`, 'error');
                        quantityElement.textContent = currentQty; // Revertir
                    }
                } catch (parseError) {
                    logToElement(logId, `❌ Error parseando JSON: ${parseError}`, 'error');
                    logGeneral(`❌ Error parseando: ${parseError}`, 'error');
                    quantityElement.textContent = currentQty; // Revertir
                }
            })
            .catch(error => {
                logToElement(logId, `❌ Error de red: ${error}`, 'error');
                logGeneral(`❌ Error de red: ${error}`, 'error');
                quantityElement.textContent = currentQty; // Revertir
            });
        }
        
        // Test de eliminación
        function testRemoveItem(cartKey) {
            const logId = `log-${cartKey}`;
            logToElement(logId, `🗑️ Iniciando test removeItem: ${cartKey}`, 'info');
            logGeneral(`🗑️ Test removeItem: ${cartKey}`);
            
            if (!confirm('¿Eliminar este producto para el test?')) {
                logToElement(logId, '❌ Test cancelado por usuario', 'info');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'remove_cart_item');
            formData.append('cart_key', cartKey);
            formData.append('nonce', CART_NONCE);
            
            logToElement(logId, `📤 Enviando eliminación`, 'info');
            
            fetch(AJAX_URL, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                logToElement(logId, `📄 Respuesta eliminar: ${text.substring(0, 100)}...`, 'info');
                
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        logToElement(logId, '✅ Eliminado exitosamente', 'success');
                        logGeneral(`✅ Producto eliminado: ${cartKey}`, 'success');
                        
                        // Ocultar elemento
                        const productElement = document.querySelector(`[data-key="${cartKey}"]`);
                        if (productElement) {
                            productElement.style.opacity = '0.5';
                            productElement.style.pointerEvents = 'none';
                        }
                    } else {
                        logToElement(logId, `❌ Error eliminando: ${data.data}`, 'error');
                        logGeneral(`❌ Error eliminando: ${data.data}`, 'error');
                    }
                } catch (parseError) {
                    logToElement(logId, `❌ Error parseando eliminación: ${parseError}`, 'error');
                }
            })
            .catch(error => {
                logToElement(logId, `❌ Error red eliminando: ${error}`, 'error');
            });
        }
        
        // Tests automáticos
        function runAllTests() {
            logGeneral('🧪 Iniciando tests automáticos...', 'info');
            
            // Test 1: Verificar variables
            logGeneral(`🔑 AJAX_URL: ${AJAX_URL}`, 'info');
            logGeneral(`🔐 CART_NONCE: ${CART_NONCE}`, 'info');
            
            // Test 2: Verificar elementos
            const cartItems = document.querySelectorAll('[data-key]');
            logGeneral(`📦 Productos encontrados: ${cartItems.length}`, 'info');
            
            cartItems.forEach((item, index) => {
                const cartKey = item.getAttribute('data-key');
                logGeneral(`📦 Producto ${index + 1}: ${cartKey}`, 'info');
                
                const quantityElement = item.querySelector('.quantity-display');
                if (quantityElement) {
                    logGeneral(`📊 Cantidad actual: ${quantityElement.textContent}`, 'info');
                } else {
                    logGeneral('❌ No se encontró elemento de cantidad', 'error');
                }
            });
            
            // Test 3: Test automático de cantidad (solo el primer producto)
            if (cartItems.length > 0) {
                const firstCartKey = cartItems[0].getAttribute('data-key');
                logGeneral(`🧪 Ejecutando test automático en: ${firstCartKey}`, 'info');
                
                setTimeout(() => {
                    testUpdateQuantity(firstCartKey, 1);
                }, 2000);
            }
        }
        
        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            logGeneral('🚀 Diagnóstico cargado', 'success');
            logGeneral(`🔧 Variables: AJAX_URL=${AJAX_URL}, NONCE=${CART_NONCE}`, 'info');
            
            // Auto-ejecutar tests en 3 segundos
            setTimeout(() => {
                logGeneral('⏰ Ejecutando tests automáticos...', 'info');
                runAllTests();
            }, 3000);
        });
    </script>
</body>
</html> 