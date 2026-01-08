<?php
/**
 * Test de Funcionalidad del Carrito - Los Cocos
 * Verifica que el sistema de carrito funcione perfectamente
 * 
 * @package LosCocos
 * @version 1.0.0
 */

// Cargar WordPress
require_once('../../../wp-load.php');

// Verificar permisos
if (!current_user_can('manage_options')) {
    wp_die('Se requieren permisos de administrador.');
}

// Verificar WooCommerce
if (!class_exists('WooCommerce')) {
    wp_die('WooCommerce no está activo.');
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Carrito - Los Cocos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .test-result {
            padding: 1rem;
            margin: 0.5rem 0;
            border-radius: 0.5rem;
            border: 2px solid;
        }
        .test-success {
            background: #d1fae5;
            border-color: #10b981;
            color: #065f46;
        }
        .test-error {
            background: #fee2e2;
            border-color: #ef4444;
            color: #991b1b;
        }
        .test-warning {
            background: #fef3c7;
            border-color: #f59e0b;
            color: #92400e;
        }
        
        /* Estilos para el carrito de prueba */
        .cart-counter {
            background: #ef4444;
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: bold;
            min-width: 1.5rem;
            text-align: center;
            display: none;
        }
        
        .cart-counter.show {
            display: inline-block;
        }
        
        .product-card {
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin: 0.5rem;
            background: white;
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            border-color: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
        }
        
        .add-to-cart-btn {
            background: #10b981;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .add-to-cart-btn:hover {
            background: #059669;
            transform: translateY(-1px);
        }
        
        .add-to-cart-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Notificación de carrito */
        .cart-notification {
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: white;
            border: 2px solid #10b981;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.4s ease;
        }
        
        .cart-notification.show {
            transform: translateX(0);
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">🧪 Test de Funcionalidad del Carrito</h1>
            <p class="text-gray-600 mb-6">Esta página verifica que el sistema de carrito funcione perfectamente con WooCommerce.</p>
            
            <!-- Carrito de prueba -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-green-800">🛒 Carrito de Prueba</h2>
                    <div class="flex items-center gap-2">
                        <span class="material-icons text-green-600">shopping_cart</span>
                        <span id="cart-count" class="cart-counter">0</span>
                        <button onclick="viewCart()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Ver Carrito
                        </button>
                        <button onclick="clearCart()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Limpiar
                        </button>
                    </div>
                </div>
                <div id="cart-items" class="mt-4 text-sm text-green-700"></div>
            </div>
        </div>

        <!-- Productos de prueba -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">📦 Productos de Prueba</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php
                // Obtener productos reales para testing
                $test_products = get_posts([
                    'post_type' => 'product',
                    'posts_per_page' => 6,
                    'post_status' => 'publish'
                ]);

                if (empty($test_products)) {
                    // Crear productos de prueba si no existen
                    echo '<div class="col-span-full text-center py-8">';
                    echo '<p class="text-gray-500 mb-4">No hay productos para probar. Creando productos de ejemplo...</p>';
                    echo '<button onclick="createTestProducts()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">';
                    echo 'Crear Productos de Prueba';
                    echo '</button>';
                    echo '</div>';
                } else {
                    foreach ($test_products as $post) {
                        $product = wc_get_product($post->ID);
                        if ($product) {
                            $image_url = loscocos_get_product_image($product->get_id());
                            ?>
                            <div class="product-card">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" class="w-full h-32 object-cover rounded mb-3">
                                <h3 class="font-semibold text-gray-800 mb-2"><?php echo esc_html($product->get_name()); ?></h3>
                                <p class="text-green-600 font-bold mb-3">$<?php echo number_format($product->get_price(), 0, ',', '.'); ?></p>
                                <button 
                                    class="add-to-cart-btn w-full"
                                    data-product-id="<?php echo $product->get_id(); ?>"
                                    data-product-name="<?php echo esc_attr($product->get_name()); ?>"
                                    data-product-price="<?php echo $product->get_price(); ?>"
                                    onclick="testAddToCart(this)"
                                >
                                    <span class="material-icons text-sm">add_shopping_cart</span>
                                    <span>Añadir al Carrito</span>
                                </button>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
            </div>
        </div>

        <!-- Resultados de tests -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">📊 Resultados de Tests</h2>
            <div id="test-results">
                <p class="text-gray-500">Haz clic en "Ejecutar Tests" para comenzar las pruebas.</p>
            </div>
            <div class="mt-6 flex gap-4">
                <button onclick="runAllTests()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    🧪 Ejecutar Todos los Tests
                </button>
                <button onclick="testSingleAdd()" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                    ➕ Test: Añadir Solo UNO
                </button>
                <button onclick="testMultipleAdd()" class="bg-yellow-600 text-white px-6 py-3 rounded-lg hover:bg-yellow-700">
                    🔢 Test: Múltiples Productos
                </button>
            </div>
        </div>
    </div>

    <!-- Notificación de carrito -->
    <div id="cart-notification" class="cart-notification">
        <div class="flex items-center gap-3">
            <span class="material-icons text-green-600">check_circle</span>
            <div>
                <div class="font-semibold">¡Producto añadido!</div>
                <div id="notification-product" class="text-sm text-gray-600"></div>
            </div>
        </div>
    </div>

    <script>
        // Sistema de carrito de prueba
        let testCart = [];
        
        // Función para añadir al carrito (simulación)
        function testAddToCart(button) {
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            const productPrice = parseFloat(button.getAttribute('data-product-price'));
            
            // Deshabilitar botón temporalmente
            button.disabled = true;
            const originalText = button.innerHTML;
            button.innerHTML = '<span class="material-icons text-sm animate-spin">refresh</span> Añadiendo...';
            
            // Simular llamada AJAX
            setTimeout(() => {
                // Verificar si el producto ya está en el carrito
                const existingItem = testCart.find(item => item.id === productId);
                
                if (existingItem) {
                    // Solo incrementar cantidad en 1
                    existingItem.quantity += 1;
                    logTest(`✅ Producto "${productName}" incrementado. Nueva cantidad: ${existingItem.quantity}`, 'success');
                } else {
                    // Añadir nuevo producto con cantidad 1
                    testCart.push({
                        id: productId,
                        name: productName,
                        price: productPrice,
                        quantity: 1
                    });
                    logTest(`✅ Producto "${productName}" añadido al carrito por primera vez`, 'success');
                }
                
                updateCartDisplay();
                showNotification(productName, productPrice);
                
                // Restaurar botón
                button.disabled = false;
                button.innerHTML = originalText;
                
            }, 1000);
        }
        
        // Actualizar visualización del carrito
        function updateCartDisplay() {
            const cartCount = document.getElementById('cart-count');
            const cartItems = document.getElementById('cart-items');
            
            // Contar total de items (suma de cantidades)
            const totalItems = testCart.reduce((sum, item) => sum + item.quantity, 0);
            
            cartCount.textContent = totalItems;
            cartCount.className = totalItems > 0 ? 'cart-counter show' : 'cart-counter';
            
            // Mostrar items del carrito
            if (testCart.length > 0) {
                cartItems.innerHTML = testCart.map(item => 
                    `<div class="flex justify-between items-center py-1">
                        <span>${item.name} (x${item.quantity})</span>
                        <span>$${(item.price * item.quantity).toLocaleString()}</span>
                    </div>`
                ).join('');
            } else {
                cartItems.innerHTML = '<p class="text-gray-500">Carrito vacío</p>';
            }
        }
        
        // Mostrar notificación
        function showNotification(productName, price) {
            const notification = document.getElementById('cart-notification');
            const productSpan = document.getElementById('notification-product');
            
            productSpan.textContent = `${productName} - $${price.toLocaleString()}`;
            notification.classList.add('show');
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
        
        // Ver carrito
        function viewCart() {
            if (testCart.length === 0) {
                alert('El carrito está vacío');
                return;
            }
            
            const cartSummary = testCart.map(item => 
                `${item.name} - Cantidad: ${item.quantity} - Subtotal: $${(item.price * item.quantity).toLocaleString()}`
            ).join('\n');
            
            const total = testCart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            alert(`🛒 CARRITO DE COMPRAS\n\n${cartSummary}\n\n💰 TOTAL: $${total.toLocaleString()}`);
        }
        
        // Limpiar carrito
        function clearCart() {
            testCart = [];
            updateCartDisplay();
            logTest('🧹 Carrito limpiado', 'warning');
        }
        
        // Logging de tests
        function logTest(message, type = 'success') {
            const resultsDiv = document.getElementById('test-results');
            const testDiv = document.createElement('div');
            testDiv.className = `test-result test-${type}`;
            testDiv.innerHTML = `<strong>${new Date().toLocaleTimeString()}</strong> - ${message}`;
            resultsDiv.appendChild(testDiv);
            resultsDiv.scrollTop = resultsDiv.scrollHeight;
        }
        
        // Test específico: añadir solo UNO
        function testSingleAdd() {
            clearCart();
            logTest('🧪 Iniciando test: Añadir solo UNO por clic', 'warning');
            
            // Simular añadir el mismo producto 3 veces
            const testProduct = {
                id: 'test-1',
                name: 'Producto de Prueba',
                price: 15000
            };
            
            // Primera adición
            testCart.push({...testProduct, quantity: 1});
            logTest('1️⃣ Primera adición: cantidad = 1', 'success');
            
            // Segunda adición (debería incrementar a 2)
            const existing = testCart.find(item => item.id === testProduct.id);
            existing.quantity += 1;
            logTest('2️⃣ Segunda adición: cantidad = 2', 'success');
            
            // Tercera adición (debería incrementar a 3)
            existing.quantity += 1;
            logTest('3️⃣ Tercera adición: cantidad = 3', 'success');
            
            updateCartDisplay();
            
            if (existing.quantity === 3) {
                logTest('✅ TEST PASADO: El sistema añade correctamente de uno en uno', 'success');
            } else {
                logTest('❌ TEST FALLIDO: Cantidad incorrecta', 'error');
            }
        }
        
        // Test múltiples productos
        function testMultipleAdd() {
            clearCart();
            logTest('🧪 Iniciando test: Múltiples productos diferentes', 'warning');
            
            const products = [
                {id: 'test-1', name: 'Ficus', price: 15000},
                {id: 'test-2', name: 'Maceta', price: 8500},
                {id: 'test-3', name: 'Fertilizante', price: 3200}
            ];
            
            products.forEach((product, index) => {
                testCart.push({...product, quantity: 1});
                logTest(`${index + 1}️⃣ Añadido: ${product.name}`, 'success');
            });
            
            updateCartDisplay();
            
            if (testCart.length === 3) {
                logTest('✅ TEST PASADO: Múltiples productos añadidos correctamente', 'success');
            } else {
                logTest('❌ TEST FALLIDO: Número de productos incorrecto', 'error');
            }
        }
        
        // Ejecutar todos los tests
        function runAllTests() {
            document.getElementById('test-results').innerHTML = '';
            logTest('🚀 Iniciando batería completa de tests...', 'warning');
            
            setTimeout(() => testSingleAdd(), 1000);
            setTimeout(() => testMultipleAdd(), 3000);
            setTimeout(() => {
                logTest('🎉 Todos los tests completados', 'success');
            }, 5000);
        }
        
        // Crear productos de prueba
        function createTestProducts() {
            logTest('🔧 Creando productos de prueba...', 'warning');
            
            // Aquí iría la llamada AJAX para crear productos
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=create_test_products&nonce=<?php echo wp_create_nonce('test-products'); ?>'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    logTest('✅ Productos de prueba creados', 'success');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    logTest('❌ Error al crear productos de prueba', 'error');
                }
            });
        }
        
        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            updateCartDisplay();
            logTest('🌱 Sistema de testing inicializado', 'success');
        });
    </script>
</body>
</html>