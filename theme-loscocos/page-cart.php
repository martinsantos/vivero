<?php
/**
 * Template para la página del carrito - Vivero Los Cocos
 * Versión simple y funcional
 */

get_header(); ?>

<div style="min-height: 100vh; background: linear-gradient(135deg, #f0fdf4, #dcfce7); padding: 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="background: white; border-radius: 16px; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            
            <h1 style="text-align: center; color: #10b981; font-size: 2.5rem; margin-bottom: 40px; font-weight: bold;">
                🛒 Tu Carrito de Compras
            </h1>

            <?php if (WC()->cart->is_empty()): ?>
                <div style="text-align: center; padding: 60px 20px; color: #6b7280;">
                    <div style="font-size: 5rem; margin-bottom: 20px;">🛒</div>
                    <h2 style="color: #374151; margin-bottom: 15px;">Tu carrito está vacío</h2>
                    <p style="font-size: 1.1rem; margin-bottom: 30px;">¡Descubre nuestras plantas y productos para tu jardín!</p>
                    <a href="<?php echo home_url(); ?>" style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 15px 30px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 1.1rem; display: inline-block; transition: all 0.3s;">
                        🌱 Ver Productos
                    </a>
                </div>
            <?php else: ?>
                
                <div id="cart-items" style="margin-bottom: 30px;">
                    <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item): ?>
                        <?php 
                        $product = $cart_item['data'];
                        $quantity = $cart_item['quantity'];
                        if ($product && $product->exists() && $quantity > 0):
                        ?>
                        <div class="cart-item" data-key="<?php echo esc_attr($cart_item_key); ?>" style="display: flex; align-items: center; gap: 20px; padding: 20px; margin-bottom: 15px; background: #f9fafb; border-radius: 12px; border: 1px solid #e5e7eb; transition: all 0.3s;">
                            
                            <!-- Imagen del producto -->
                            <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; flex-shrink: 0; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                                🌱
                            </div>
                            
                            <!-- Info del producto -->
                            <div style="flex: 1;">
                                <h3 style="font-size: 1.3rem; font-weight: bold; color: #1f2937; margin: 0 0 8px 0;">
                                    <?php echo esc_html($product->get_name()); ?>
                                </h3>
                                <div style="font-size: 1.2rem; color: #10b981; font-weight: 600;">
                                    <?php echo wc_price($product->get_price()); ?>
                                </div>
                                <div style="font-size: 0.9rem; color: #6b7280; margin-top: 4px;">
                                    Subtotal: <?php echo wc_price($product->get_price() * $quantity); ?>
                                </div>
                            </div>
                            
                            <!-- Controles de cantidad -->
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <button onclick="updateQuantity('<?php echo esc_attr($cart_item_key); ?>', -1)" style="width: 40px; height: 40px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 1.2rem; transition: all 0.3s;">
                                    -
                                </button>
                                <div class="qty-display" style="padding: 10px 20px; background: white; border: 2px solid #d1d5db; border-radius: 8px; font-weight: bold; min-width: 60px; text-align: center; font-size: 1.1rem;">
                                    <?php echo $quantity; ?>
                                </div>
                                <button onclick="updateQuantity('<?php echo esc_attr($cart_item_key); ?>', 1)" style="width: 40px; height: 40px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 1.2rem; transition: all 0.3s;">
                                    +
                                </button>
                                <button onclick="removeItem('<?php echo esc_attr($cart_item_key); ?>')" style="background: #ef4444; color: white; border: none; padding: 10px 16px; border-radius: 8px; cursor: pointer; font-weight: bold; margin-left: 8px; transition: all 0.3s;">
                                    🗑️
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <!-- Resumen del carrito -->
                <div style="background: linear-gradient(135deg, #f0fdf4, #dcfce7); padding: 30px; border-radius: 16px; border: 2px solid #10b981;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <span style="font-size: 1.3rem; font-weight: 600; color: #374151;">Total de productos:</span>
                        <span style="font-size: 1.3rem; font-weight: bold; color: #10b981;"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-top: 15px; border-top: 2px solid #10b981;">
                        <span style="font-size: 1.5rem; font-weight: bold; color: #1f2937;">TOTAL:</span>
                        <span style="font-size: 1.8rem; font-weight: bold; color: #10b981;"><?php echo WC()->cart->get_total(); ?></span>
                    </div>
                    <button onclick="window.location.href='<?php echo wc_get_checkout_url(); ?>'" style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 18px 40px; border: none; border-radius: 12px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%; transition: all 0.3s; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                        💳 Finalizar Compra
                    </button>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Notificaciones -->
<div id="notifications" style="position: fixed; top: 20px; right: 20px; z-index: 1000;"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🛒 Carrito Los Cocos cargado');
    
    // Configuración AJAX
    const AJAX_URL = '<?php echo admin_url("admin-ajax.php"); ?>';
    const NONCE = '<?php echo wp_create_nonce("cart_nonce"); ?>';
    
    // Función para mostrar notificaciones
    window.showNotification = function(message, type = 'success') {
        const notification = document.createElement('div');
        notification.style.cssText = `
            padding: 15px 25px;
            margin-bottom: 10px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            animation: slideIn 0.3s ease-out;
            ${type === 'success' ? 'background: #10b981;' : 'background: #ef4444;'}
        `;
        notification.textContent = message;
        
        document.getElementById('notifications').appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    };
    
    // Función para actualizar cantidad
    window.updateQuantity = function(cartKey, change) {
        const itemElement = document.querySelector(`[data-key="${cartKey}"]`);
        const qtyElement = itemElement.querySelector('.qty-display');
        const currentQty = parseInt(qtyElement.textContent);
        const newQty = Math.max(1, currentQty + change);
        
        // Actualizar visualmente primero
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
                showNotification('✅ Cantidad actualizada');
                // Actualizar totales si es necesario
                setTimeout(() => location.reload(), 500);
            } else {
                qtyElement.textContent = currentQty; // Revertir
                showNotification('❌ Error: ' + data.data, 'error');
            }
        })
        .catch(error => {
            qtyElement.textContent = currentQty; // Revertir
            showNotification('❌ Error de conexión', 'error');
            console.error('Error:', error);
        });
    };
    
    // Función para eliminar producto
    window.removeItem = function(cartKey) {
        if (!confirm('¿Estás seguro de eliminar este producto?')) return;
        
        const itemElement = document.querySelector(`[data-key="${cartKey}"]`);
        
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
                itemElement.style.opacity = '0';
                setTimeout(() => {
                    itemElement.remove();
                    showNotification('✅ Producto eliminado');
                    
                    // Recargar si no hay más productos
                    if (document.querySelectorAll('[data-key]').length === 0) {
                        setTimeout(() => location.reload(), 1000);
                    }
                }, 300);
            } else {
                showNotification('❌ Error eliminando producto', 'error');
            }
        })
        .catch(error => {
            showNotification('❌ Error de conexión', 'error');
            console.error('Error:', error);
        });
    };
    
    // Agregar efectos hover a los botones
    const buttons = document.querySelectorAll('button');
    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});

// CSS para animaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .cart-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
`;
document.head.appendChild(style);
</script>

<?php get_footer(); ?> 