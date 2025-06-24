<?php
// Test simple de Unsplash
?>
<html>
<head>
    <title>Test Unsplash</title>
</head>
<body>
    <h1>🌱 Test de Imágenes Unsplash</h1>
    
    <h2>Imagen directa de Unsplash:</h2>
    <div style="border: 2px solid #10b981; width: 200px; height: 200px; display: inline-block;">
        <img src="https://source.unsplash.com/400x400/?plant,green&sig=123" 
             alt="Planta" 
             style="width:100%;height:100%;object-fit:cover;border-radius:12px;" />
    </div>
    
    <h2>Otra imagen con diferente sig:</h2>
    <div style="border: 2px solid #10b981; width: 200px; height: 200px; display: inline-block;">
        <img src="https://source.unsplash.com/400x400/?plant,green&sig=456" 
             alt="Planta" 
             style="width:100%;height:100%;object-fit:cover;border-radius:12px;" />
    </div>
    
    <h2>Imagen más específica:</h2>
    <div style="border: 2px solid #10b981; width: 200px; height: 200px; display: inline-block;">
        <img src="https://source.unsplash.com/400x400/?succulent,cactus&sig=789" 
             alt="Planta" 
             style="width:100%;height:100%;object-fit:cover;border-radius:12px;" />
    </div>
    
    <h2>Simulando nuestra función:</h2>
    <?php
    require_once 'wp-config.php';
    require_once ABSPATH . 'wp-load.php';
    
    if (function_exists('loscocos_get_cart_product_image')) {
        echo "<div style='border: 2px solid #059669; width: 200px; height: 200px; display: inline-block;'>";
        echo loscocos_get_cart_product_image(123, 'medium');
        echo "</div>";
    } else {
        echo "<p style='color: red;'>Función no encontrada</p>";
    }
    ?>
    
    <p><a href="/cart/">🛒 Ver carrito</a></p>
</body>
</html> 