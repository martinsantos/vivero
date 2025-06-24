<?php
// Test súper simple
require_once 'wp-config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Simple</title>
    <style>body{font-family:Arial;margin:20px;} .test{border:2px solid #ccc;padding:20px;margin:10px;width:200px;height:200px;}</style>
</head>
<body>
    <h1>🧪 Test Simple de Función</h1>
    
    <h2>1. Test directo de función</h2>
    <div class="test">
        <?php
        if (function_exists('loscocos_get_cart_product_image')) {
            echo loscocos_get_cart_product_image(548, 'medium');
        } else {
            echo '<div style="color:red;">❌ Función no existe</div>';
        }
        ?>
    </div>
    
    <h2>2. Test con producto diferente</h2>
    <div class="test">
        <?php
        if (function_exists('loscocos_get_cart_product_image')) {
            echo loscocos_get_cart_product_image(549, 'medium');
        } else {
            echo '<div style="color:red;">❌ Función no existe</div>';
        }
        ?>
    </div>
    
    <h2>3. Test con producto inexistente</h2>
    <div class="test">
        <?php
        if (function_exists('loscocos_get_cart_product_image')) {
            echo loscocos_get_cart_product_image(99999, 'medium');
        } else {
            echo '<div style="color:red;">❌ Función no existe</div>';
        }
        ?>
    </div>
    
    <hr>
    <p><strong>Función existe:</strong> <?php echo function_exists('loscocos_get_cart_product_image') ? '✅ Sí' : '❌ No'; ?></p>
    <p><strong>WooCommerce activo:</strong> <?php echo class_exists('WooCommerce') ? '✅ Sí' : '❌ No'; ?></p>
    
    <hr>
    <p><a href="/image-debug-visual.php">🔍 Debug completo</a></p>
    <p><a href="/cart/">🛒 Ver carrito</a></p>
    <p><a href="/quick-add-cart.php">➕ Agregar productos</a></p>
</body>
</html> 