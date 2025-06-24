<?php
// ANÁLISIS DEL HTML EXACTO DEL CARRITO
require_once 'wp-config.php';
require_once ABSPATH . 'wp-load.php';

echo "<html><head><title>🔍 ANÁLISIS HTML DEL CARRITO</title></head><body>";
echo "<h1>🔍 ANÁLISIS DEL HTML EXACTO DEL CARRITO</h1>";
echo "<style>
.code-block { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; margin: 10px 0; border-radius: 5px; font-family: monospace; white-space: pre-wrap; }
.highlight { background: #fef3c7; padding: 2px 4px; }
</style>";

// Asegurar que hay productos en el carrito
if (class_exists('WooCommerce')) {
    if (WC()->cart->get_cart_contents_count() == 0) {
        echo "<p>🛒 Agregando productos al carrito...</p>";
        $products = wc_get_products(array('limit' => 2, 'status' => 'publish'));
        if (!empty($products)) {
            WC()->cart->empty_cart();
            foreach ($products as $product) {
                WC()->cart->add_to_cart($product->get_id(), 1);
            }
        }
    }
    
    echo "<h2>📊 Estado del carrito:</h2>";
    echo "<p><strong>Productos en carrito:</strong> " . WC()->cart->get_cart_contents_count() . "</p>";
}

// Obtener el HTML completo del carrito
echo "<h2>🌐 Obteniendo HTML del carrito...</h2>";
$cart_url = home_url('/cart/');
echo "<p><strong>URL del carrito:</strong> $cart_url</p>";

$response = wp_remote_get($cart_url, array(
    'timeout' => 30,
    'headers' => array(
        'User-Agent' => 'Mozilla/5.0 (compatible; WordPress/DiagnosticBot)'
    )
));

if (is_wp_error($response)) {
    echo "<p style='color: red;'>❌ Error obteniendo HTML: " . $response->get_error_message() . "</p>";
    exit;
}

$html = wp_remote_retrieve_body($response);
$response_code = wp_remote_retrieve_response_code($response);

echo "<p><strong>Código de respuesta:</strong> $response_code</p>";
echo "<p><strong>Tamaño del HTML:</strong> " . strlen($html) . " bytes</p>";

// Analizar el HTML
echo "<h2>🔍 ANÁLISIS DEL CONTENIDO</h2>";

// Buscar elementos relacionados con el carrito
$patterns = [
    'product-thumbnail' => '/class="[^"]*product-thumbnail[^"]*"[^>]*>(.*?)<\/td>/s',
    'cart_item' => '/class="[^"]*cart_item[^"]*"[^>]*>(.*?)<\/tr>/s',
    'img tags' => '/<img[^>]*>/i',
    'DEBUG comments' => '/<!--\s*DEBUG:.*?-->/s',
    'loscocos function' => '/loscocos_get_cart_product_image/i',
    'data:image' => '/data:image[^"\']+/i',
    'source.unsplash' => '/source\.unsplash\.com[^"\']+/i'
];

foreach ($patterns as $name => $pattern) {
    echo "<h3>🔍 Buscando: $name</h3>";
    preg_match_all($pattern, $html, $matches);
    $count = count($matches[0]);
    echo "<p><strong>Coincidencias encontradas:</strong> $count</p>";
    
    if ($count > 0) {
        echo "<div class='code-block'>";
        foreach (array_slice($matches[0], 0, 3) as $i => $match) {
            echo "<strong>Coincidencia " . ($i + 1) . ":</strong>\n";
            echo htmlspecialchars(substr($match, 0, 500));
            if (strlen($match) > 500) echo "... (truncado)";
            echo "\n\n";
        }
        echo "</div>";
    }
}

// Buscar específicamente la tabla del carrito
echo "<h2>🛒 ANÁLISIS ESPECÍFICO DE LA TABLA DEL CARRITO</h2>";
preg_match('/<table[^>]*class="[^"]*shop_table[^"]*"[^>]*>(.*?)<\/table>/s', $html, $table_match);

if (!empty($table_match[1])) {
    echo "<p class='highlight'>✅ Tabla del carrito encontrada</p>";
    
    // Buscar filas de productos
    preg_match_all('/<tr[^>]*class="[^"]*cart_item[^"]*"[^>]*>(.*?)<\/tr>/s', $table_match[1], $row_matches);
    echo "<p><strong>Filas de productos encontradas:</strong> " . count($row_matches[0]) . "</p>";
    
    if (!empty($row_matches[0])) {
        foreach ($row_matches[0] as $i => $row) {
            echo "<h4>🔍 Fila de producto " . ($i + 1) . ":</h4>";
            
            // Buscar la celda de thumbnail
            preg_match('/<td[^>]*class="[^"]*product-thumbnail[^"]*"[^>]*>(.*?)<\/td>/s', $row, $thumbnail_match);
            if (!empty($thumbnail_match[1])) {
                echo "<p class='highlight'>✅ Celda de thumbnail encontrada</p>";
                echo "<div class='code-block'>";
                echo htmlspecialchars($thumbnail_match[1]);
                echo "</div>";
                
                // Analizar el contenido de la imagen
                if (strpos($thumbnail_match[1], '<img') !== false) {
                    echo "<p class='highlight'>✅ Tag IMG encontrado</p>";
                    preg_match('/<img[^>]*src="([^"]*)"[^>]*>/i', $thumbnail_match[1], $img_match);
                    if (!empty($img_match[1])) {
                        echo "<p><strong>URL de la imagen:</strong></p>";
                        echo "<div class='code-block'>" . htmlspecialchars($img_match[1]) . "</div>";
                        
                        // Verificar el tipo de imagen
                        if (strpos($img_match[1], 'data:image') === 0) {
                            echo "<p class='highlight'>✅ Imagen SVG embebida (data:image) detectada</p>";
                        } elseif (strpos($img_match[1], 'source.unsplash.com') !== false) {
                            echo "<p class='highlight'>✅ Imagen de Unsplash detectada</p>";
                        } elseif (strpos($img_match[1], 'placeholder') !== false) {
                            echo "<p style='color: orange;'>⚠️ Imagen placeholder detectada</p>";
                        } else {
                            echo "<p style='color: red;'>❌ Tipo de imagen desconocido</p>";
                        }
                    }
                } else {
                    echo "<p style='color: red;'>❌ No se encontró tag IMG en la celda</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ Celda de thumbnail NO encontrada</p>";
            }
        }
    }
} else {
    echo "<p style='color: red;'>❌ Tabla del carrito NO encontrada</p>";
    
    // Buscar mensaje de carrito vacío
    if (strpos($html, 'carrito está vacío') !== false || strpos($html, 'cart is empty') !== false) {
        echo "<p style='color: orange;'>⚠️ CARRITO VACÍO detectado en HTML</p>";
    }
}

// Verificar si hay errores de JavaScript
echo "<h2>🐛 VERIFICACIÓN DE ERRORES POTENCIALES</h2>";
if (strpos($html, 'Fatal error') !== false) {
    echo "<p style='color: red;'>❌ Error fatal detectado en PHP</p>";
}
if (strpos($html, 'Warning:') !== false) {
    echo "<p style='color: orange;'>⚠️ Advertencias de PHP detectadas</p>";
}
if (strpos($html, 'Notice:') !== false) {
    echo "<p style='color: orange;'>⚠️ Notices de PHP detectados</p>";
}

echo "<hr>";
echo "<h2>🎯 CONCLUSIONES</h2>";
echo "<p><a href='/cart/' target='_blank' style='background: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🛒 ABRIR CARRITO EN NUEVA PESTAÑA</a></p>";
echo "<p><a href='/diagnostic-ultra-deep.php' style='background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔬 EJECUTAR DIAGNÓSTICO COMPLETO</a></p>";

echo "</body></html>";
?> 