<?php
/**
 * Template Name: Debug Test
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Test - Los Cocos</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .debug-box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
    </style>
</head>
<body>
    <h1>🔧 Debug Test - Vivero Los Cocos</h1>
    
    <div class="debug-box">
        <h2>✅ Pruebas Básicas</h2>
        <p class="success">✓ PHP está funcionando</p>
        <p class="success">✓ WordPress está cargado</p>
        <p class="info">Versión PHP: <?php echo PHP_VERSION; ?></p>
        <p class="info">WordPress Version: <?php echo get_bloginfo('version'); ?></p>
    </div>
    
    <div class="debug-box">
        <h2>🛒 WooCommerce Status</h2>
        <?php if (class_exists('WooCommerce')) : ?>
            <p class="success">✓ WooCommerce está activo</p>
            <p class="info">WooCommerce Version: <?php echo WC()->version; ?></p>
        <?php else : ?>
            <p class="error">✗ WooCommerce NO está activo</p>
        <?php endif; ?>
    </div>
    
    <div class="debug-box">
        <h2>📦 Productos Test</h2>
        <?php
        $products = wc_get_products(array('limit' => 5, 'status' => 'publish'));
        if ($products) {
            echo '<p class="success">✓ Productos encontrados: ' . count($products) . '</p>';
            echo '<ul>';
            foreach ($products as $product) {
                echo '<li>' . $product->get_name() . ' - $' . $product->get_price() . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="error">✗ No se encontraron productos</p>';
        }
        ?>
    </div>
    
    <div class="debug-box">
        <h2>🏷️ Categorías Test</h2>
        <?php
        $categories = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => false));
        if ($categories && !is_wp_error($categories)) {
            echo '<p class="success">✓ Categorías encontradas: ' . count($categories) . '</p>';
            echo '<ul>';
            foreach (array_slice($categories, 0, 10) as $category) {
                echo '<li>' . $category->name . ' (' . $category->count . ' productos)</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="error">✗ No se encontraron categorías</p>';
        }
        ?>
    </div>
    
    <div class="debug-box">
        <h2>🛒 Carrito Test</h2>
        <?php if (WC()->cart) : ?>
            <p class="success">✓ Carrito disponible</p>
            <p class="info">Productos en carrito: <?php echo WC()->cart->get_cart_contents_count(); ?></p>
            <p class="info">Total carrito: <?php echo WC()->cart->get_cart_total(); ?></p>
        <?php else : ?>
            <p class="error">✗ Carrito no disponible</p>
        <?php endif; ?>
    </div>
    
    <div class="debug-box">
        <h2>🔧 Memory & Performance</h2>
        <p class="info">Memory Usage: <?php echo round(memory_get_usage() / 1024 / 1024, 2); ?>MB</p>
        <p class="info">Memory Limit: <?php echo ini_get('memory_limit'); ?></p>
        <p class="info">Max Execution Time: <?php echo ini_get('max_execution_time'); ?>s</p>
    </div>
    
    <div class="debug-box">
        <h2>🌐 Enlaces de Navegación</h2>
        <p><a href="<?php echo home_url(); ?>" style="color: blue;">🏠 Página Principal</a></p>
        <p><a href="<?php echo home_url('/?page_id=10'); ?>" style="color: blue;">🛒 Tienda (Template Original)</a></p>
        <p><a href="<?php echo admin_url(); ?>" style="color: blue;">⚙️ Panel Admin</a></p>
    </div>
    
    <div class="debug-box">
        <h2>🛍️ Test de Producto Simple</h2>
        <?php
        $test_product = wc_get_products(array('limit' => 1, 'status' => 'publish'));
        if ($test_product) {
            $product = $test_product[0];
            echo '<div style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px;">';
            echo '<h3>' . $product->get_name() . '</h3>';
            echo '<p><strong>Precio: $' . number_format($product->get_price(), 0, ',', '.') . '</strong></p>';
            echo '<p>Stock: ' . $product->get_stock_quantity() . '</p>';
            echo '<p>SKU: ' . $product->get_sku() . '</p>';
            echo '<form method="post" style="margin-top: 10px;">';
            echo '<input type="hidden" name="add-to-cart" value="' . $product->get_id() . '">';
            echo '<button type="submit" style="background: #10B981; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">🛒 Test Agregar al Carrito</button>';
            echo '</form>';
            echo '</div>';
        }
        ?>
    </div>
    
    <p style="text-align: center; margin-top: 30px; color: #666;">
        🌿 Vivero Los Cocos - Debug Page 🌿
    </p>
</body>
</html> 