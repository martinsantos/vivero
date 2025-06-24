<?php
/*
Template Name: Debug Error
*/

// Activar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

get_header();
?>

<div style="max-width: 1200px; margin: 50px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h1 style="color: #2e7d32;">🔍 Debug de Errores PHP</h1>

    <?php
    // Verificar WordPress
    if (function_exists('wp_get_theme')) {
        echo "<p style='color: green;'>✅ WordPress cargado correctamente</p>";
        echo "<p><strong>Tema activo:</strong> " . wp_get_theme()->get('Name') . "</p>";
        echo "<p><strong>Directorio del tema:</strong> " . get_template_directory() . "</p>";
    } else {
        echo "<p style='color: red;'>❌ WordPress no está cargado correctamente</p>";
    }

    // Verificar WooCommerce
    if (class_exists('WooCommerce')) {
        echo "<p style='color: green;'>✅ WooCommerce está activo</p>";
    } else {
        echo "<p style='color: red;'>❌ WooCommerce no está activo</p>";
    }

    // Verificar si la función existe
    if (function_exists('loscocos_get_cart_product_image')) {
        echo "<p style='color: green;'>✅ Función loscocos_get_cart_product_image existe</p>";
        
        // Probar la función con manejo de errores
        echo "<h2>Prueba de función:</h2>";
        try {
            $result = loscocos_get_cart_product_image(548, 'medium');
            echo "<p><strong>Resultado visual:</strong></p>";
            echo "<div style='border: 2px solid #ccc; padding: 10px; width: 200px; height: 200px; overflow: auto; background: #f9f9f9;'>";
            echo $result;
            echo "</div>";
            
            echo "<p><strong>HTML generado:</strong></p>";
            echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 4px; font-size: 12px; overflow-x: auto;'>";
            echo htmlspecialchars($result);
            echo "</pre>";
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error ejecutando función: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Función loscocos_get_cart_product_image NO existe</p>";
        
        // Verificar si el archivo functions.php se está cargando
        $functions_file = get_template_directory() . '/functions.php';
        if (file_exists($functions_file)) {
            echo "<p style='color: orange;'>⚠️ functions.php existe en: $functions_file</p>";
            
            // Leer las primeras líneas del archivo
            $content = file_get_contents($functions_file);
            if (strpos($content, 'loscocos_get_cart_product_image') !== false) {
                echo "<p style='color: orange;'>⚠️ La función está definida en functions.php</p>";
            } else {
                echo "<p style='color: red;'>❌ La función NO está en functions.php</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ functions.php no existe</p>";
        }
    }

    // Verificar productos
    if (class_exists('WooCommerce')) {
        $products = wc_get_products(array('limit' => 3, 'status' => 'publish'));
        echo "<h2>Productos disponibles:</h2>";
        if ($products) {
            foreach ($products as $product) {
                echo "<p>ID: " . $product->get_id() . " - " . $product->get_name() . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ No hay productos</p>";
        }
    }

    // Mostrar información de archivos SVG
    $svg_dir = get_template_directory() . '/svg-products/';
    if (is_dir($svg_dir)) {
        $svg_files = glob($svg_dir . '*.svg');
        echo "<h2>Archivos SVG encontrados:</h2>";
        echo "<p>Directorio: $svg_dir</p>";
        echo "<p>Total archivos: " . count($svg_files) . "</p>";
        
        if (count($svg_files) > 0) {
            echo "<p>Primeros 5 archivos:</p><ul>";
            for ($i = 0; $i < min(5, count($svg_files)); $i++) {
                echo "<li>" . basename($svg_files[$i]) . "</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color: red;'>❌ Directorio SVG no existe: $svg_dir</p>";
    }

    // Mostrar errores PHP si los hay
    if (function_exists('error_get_last')) {
        $error = error_get_last();
        if ($error && $error['message']) {
            echo "<h2>Último error PHP:</h2>";
            echo "<pre style='background: #fee; padding: 10px; border: 1px solid #f00; border-radius: 4px;'>";
            print_r($error);
            echo "</pre>";
        } else {
            echo "<p style='color: green;'>✅ No hay errores PHP recientes</p>";
        }
    }
    ?>

    <hr>
    <p>
        <a href="<?php echo home_url('/test-simple.php'); ?>" style="color: #2e7d32; text-decoration: none;">🔄 Volver al test simple</a> | 
        <a href="<?php echo home_url('/image-debug-visual.php'); ?>" style="color: #2e7d32; text-decoration: none;">🔍 Debug visual completo</a> |
        <a href="<?php echo home_url('/cart/'); ?>" style="color: #2e7d32; text-decoration: none;">🛒 Ver carrito</a>
    </p>
</div>

<?php get_footer(); ?> 