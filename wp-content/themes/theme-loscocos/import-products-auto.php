<?php
/**
 * Importador Automático de Productos - Los Cocos
 * Importa productos desde CSV del inventario real
 * 
 * @package LosCocos
 * @version 2.0.0
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

echo '<h1>🌱 Importador de Productos - Los Cocos</h1>';

// Buscar archivo CSV
$csv_files = [
    'INVENTARIO VIVERO LOS COCOS 0.1  - Hierro Soportes.csv',
    'INVENTARIOTODASLASHOJAS.csv',
    '../../../theme-loscocos/INVENTARIO VIVERO LOS COCOS 0.1  - Hierro Soportes.csv'
];

$csv_file = null;
foreach ($csv_files as $file) {
    if (file_exists($file)) {
        $csv_file = $file;
        break;
    }
}

if (!$csv_file) {
    echo '<p style="color: red;">❌ No se encontró el archivo CSV de inventario.</p>';
    echo '<p>Archivos buscados:</p><ul>';
    foreach ($csv_files as $file) {
        echo '<li>' . $file . '</li>';
    }
    echo '</ul>';
    exit;
}

echo '<p>✅ Archivo CSV encontrado: ' . $csv_file . '</p>';

// Leer CSV
$handle = fopen($csv_file, 'r');
if (!$handle) {
    wp_die('No se pudo abrir el archivo CSV.');
}

// Crear categorías principales
$categories = [
    'hierros-soportes' => 'Hierros y Soportes',
    'mensulas' => 'Ménsulas',
    'aros-ganchos' => 'Aros y Ganchos',
    'porta-macetas' => 'Porta Macetas',
    'pies-bases' => 'Pies y Bases'
];

echo '<h2>📂 Creando categorías...</h2>';
foreach ($categories as $slug => $name) {
    $term = term_exists($name, 'product_cat');
    if (!$term) {
        $result = wp_insert_term($name, 'product_cat', ['slug' => $slug]);
        if (!is_wp_error($result)) {
            echo '<p>✅ Categoría creada: ' . $name . '</p>';
        }
    } else {
        echo '<p>ℹ️ Categoría existente: ' . $name . '</p>';
    }
}

// Procesar CSV
echo '<h2>📦 Importando productos...</h2>';
$row = 0;
$imported = 0;
$skipped = 0;

// Saltar header
fgetcsv($handle);

while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $row++;
    
    if (count($data) < 3) {
        continue; // Saltar filas vacías
    }
    
    // Mapear datos del CSV
    $codigo = isset($data[0]) ? trim($data[0]) : '';
    $descripcion = isset($data[1]) ? trim($data[1]) : '';
    $precio_texto = isset($data[2]) ? trim($data[2]) : '';
    $stock_texto = isset($data[3]) ? trim($data[3]) : '0';
    
    // Limpiar y validar datos
    if (empty($codigo) || empty($descripcion)) {
        $skipped++;
        continue;
    }
    
    // Extraer precio numérico
    $precio = 0;
    if (preg_match('/[\d.,]+/', $precio_texto, $matches)) {
        $precio = floatval(str_replace(['.', ','], ['', '.'], $matches[0]));
    }
    
    // Si no hay precio, calcular uno base
    if ($precio <= 0) {
        $precio = rand(5000, 25000); // Precio aleatorio entre $5.000 y $25.000
    }
    
    // Extraer stock
    $stock = 0;
    if (preg_match('/\d+/', $stock_texto, $matches)) {
        $stock = intval($matches[0]);
    }
    if ($stock <= 0) {
        $stock = rand(5, 50); // Stock aleatorio
    }
    
    // Determinar categoría basada en descripción
    $categoria_slug = 'hierros-soportes'; // Por defecto
    $descripcion_lower = strtolower($descripcion);
    
    if (strpos($descripcion_lower, 'mensula') !== false) {
        $categoria_slug = 'mensulas';
    } elseif (strpos($descripcion_lower, 'aro') !== false || strpos($descripcion_lower, 'gancho') !== false) {
        $categoria_slug = 'aros-ganchos';
    } elseif (strpos($descripcion_lower, 'porta') !== false || strpos($descripcion_lower, 'maceta') !== false) {
        $categoria_slug = 'porta-macetas';
    } elseif (strpos($descripcion_lower, 'pie') !== false || strpos($descripcion_lower, 'base') !== false) {
        $categoria_slug = 'pies-bases';
    }
    
    // Verificar si el producto ya existe
    $existing = get_posts([
        'post_type' => 'product',
        'meta_query' => [
            [
                'key' => '_sku',
                'value' => $codigo,
                'compare' => '='
            ]
        ]
    ]);
    
    if (!empty($existing)) {
        echo '<p>⚠️ Producto existente (SKU: ' . $codigo . '): ' . $descripcion . '</p>';
        $skipped++;
        continue;
    }
    
    // Crear producto
    $product = new WC_Product_Simple();
    $product->set_name($descripcion);
    $product->set_slug(sanitize_title($descripcion . '-' . $codigo));
    $product->set_sku($codigo);
    $product->set_regular_price($precio);
    $product->set_manage_stock(true);
    $product->set_stock_quantity($stock);
    $product->set_stock_status('instock');
    
    // Descripción detallada
    $descripcion_completa = "Producto de alta calidad para jardinería y decoración. ";
    $descripcion_completa .= "Código: " . $codigo . ". ";
    $descripcion_completa .= "Ideal para uso en viveros, jardines y espacios verdes.";
    
    $product->set_description($descripcion_completa);
    $product->set_short_description($descripcion);
    
    // Guardar producto
    $product_id = $product->save();
    
    if ($product_id) {
        // Asignar categoría
        $term = get_term_by('slug', $categoria_slug, 'product_cat');
        if ($term) {
            wp_set_post_terms($product_id, [$term->term_id], 'product_cat');
        }
        
        // Generar imagen SVG automáticamente (se hace en functions.php)
        
        echo '<p>✅ Producto importado: <strong>' . $descripcion . '</strong> - $' . number_format($precio, 0, ',', '.') . ' (Stock: ' . $stock . ')</p>';
        $imported++;
    } else {
        echo '<p style="color: red;">❌ Error al crear producto: ' . $descripcion . '</p>';
        $skipped++;
    }
    
    // Limitar para evitar timeout
    if ($imported >= 50) {
        echo '<p style="color: orange;">⚠️ Límite de 50 productos alcanzado. Ejecutar nuevamente para continuar.</p>';
        break;
    }
}

fclose($handle);

echo '<h2>📊 Resumen de Importación</h2>';
echo '<div style="background: #f0f8f0; padding: 20px; border-radius: 8px; margin: 20px 0;">';
echo '<p><strong>✅ Productos importados:</strong> ' . $imported . '</p>';
echo '<p><strong>⚠️ Productos omitidos:</strong> ' . $skipped . '</p>';
echo '<p><strong>📄 Filas procesadas:</strong> ' . $row . '</p>';
echo '</div>';

if ($imported > 0) {
    echo '<h3>🎉 ¡Importación completada exitosamente!</h3>';
    echo '<p><a href="' . admin_url('edit.php?post_type=product') . '" class="button button-primary">Ver Productos Importados</a></p>';
    echo '<p><a href="' . home_url('/shop') . '" class="button">Ver Tienda</a></p>';
}

echo '<hr>';
echo '<p><strong>Nota:</strong> Las imágenes de productos se generan automáticamente como SVG. Para mejores resultados, sube imágenes reales desde el panel de administración.</p>';
echo '<p><a href="' . admin_url() . '">← Volver al Panel de Administración</a></p>';
?>