<?php
/**
 * Importador Automático de Productos con Categorías Estacionales - Los Cocos
 * 
 * @package LosCocos
 * @version 1.0.0
 */

// Cargar WordPress
require_once('../../../wp-load.php');

// Cargar utilidades de debug
require_once(get_template_directory() . '/includes/debug-utilities.php');

// Verificar permisos
if (!current_user_can('manage_options')) {
    wp_die('Se requieren permisos de administrador.');
}

// Verificar WooCommerce
if (!class_exists('WooCommerce')) {
    wp_die('WooCommerce no está activo.');
}

echo '<h1>🌱 Importador de Productos Estacionales - Los Cocos</h1>';

// Crear categorías principales
$categories = [
    'interior' => 'Interior',
    'exterior' => 'Exterior',
    'arboles' => 'Árboles',
    'suculentas' => 'Suculentas'
];

// Crear etiquetas estacionales
$seasonal_tags = [
    'spring' => 'Primavera',
    'summer' => 'Verano',
    'autumn' => 'Otoño',
    'winter' => 'Invierno'
];

echo '<h2>📂 Creando categorías...</h2>';
foreach ($categories as $slug => $name) {
    $term = term_exists($name, 'product_cat');
    if (!$term) {
        $result = wp_insert_term($name, 'product_cat', [
            'slug' => $slug,
            'description' => "Productos para $name"
        ]);
        if (!is_wp_error($result)) {
            echo "<p>✅ Categoría creada: $name</p>";
        }
    } else {
        echo "<p>ℹ️ Categoría existente: $name</p>";
    }
}

echo '<h2>🏷️ Creando etiquetas estacionales...</h2>';
foreach ($seasonal_tags as $slug => $name) {
    $term = term_exists($name, 'product_tag');
    if (!$term) {
        $result = wp_insert_term($name, 'product_tag', [
            'slug' => $slug,
            'description' => "Productos ideales para $name"
        ]);
        if (!is_wp_error($result)) {
            echo "<p>✅ Etiqueta creada: $name</p>";
        }
    } else {
        echo "<p>ℹ️ Etiqueta existente: $name</p>";
    }
}

// Buscar archivo CSV
$csv_file = 'import-products-seasonal.csv';

if (!file_exists($csv_file)) {
    echo '<p style="color: red;">❌ No se encontró el archivo CSV de productos estacionales.</p>';
    exit;
}

echo '<p>✅ Archivo CSV encontrado: ' . $csv_file . '</p>';

// Leer CSV
$handle = fopen($csv_file, 'r');
if (!$handle) {
    wp_die('No se pudo abrir el archivo CSV.');
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
    $sku = isset($data[2]) ? trim($data[2]) : '';
    $name = isset($data[3]) ? trim($data[3]) : '';
    $description = isset($data[5]) ? trim($data[5]) : '';
    $stock = isset($data[6]) ? intval($data[6]) : 0;
    $price = isset($data[7]) ? floatval($data[7]) : 0;
    $categories_str = isset($data[8]) ? trim($data[8]) : '';
    $tags_str = isset($data[9]) ? trim($data[9]) : '';
    
    // Limpiar y validar datos
    if (empty($sku) || empty($name)) {
        $skipped++;
        continue;
    }
    
    // Verificar si el producto ya existe
    $existing = get_posts([
        'post_type' => 'product',
        'meta_query' => [
            [
                'key' => '_sku',
                'value' => $sku,
                'compare' => '='
            ]
        ]
    ]);
    
    if (!empty($existing)) {
        echo '<p>⚠️ Producto existente (SKU: ' . $sku . '): ' . $name . '</p>';
        $skipped++;
        continue;
    }
    
// Crear producto
try {
    $product = new WC_Product_Simple();
    $product->set_name($name);
    $product->set_slug(sanitize_title($name));
    $product->set_sku($sku);
    $product->set_regular_price($price);
    $product->set_manage_stock(true);
    $product->set_stock_quantity($stock);
    $product->set_stock_status('instock');
    $product->set_description($description);
    $product->set_short_description($name);
} catch (Exception $e) {
    echo loscocos_handle_seasonal_error('create_product', $e->getMessage());
    $skipped++;
    continue;
}
    
    // Guardar producto
    $product_id = $product->save();
    
    if ($product_id) {
// Asignar categorías
if (!empty($categories_str)) {
    try {
        $categories = array_map('trim', explode(',', $categories_str));
        foreach ($categories as $category) {
            $term = get_term_by('name', $category, 'product_cat');
            if ($term) {
                $result = wp_set_post_terms($product_id, [$term->term_id], 'product_cat', true);
                if (is_wp_error($result)) {
                    throw new Exception($result->get_error_message());
                }
            }
        }
    } catch (Exception $e) {
        loscocos_debug_log('Error al asignar categorías', [
            'product_id' => $product_id,
            'categories' => $categories_str,
            'error' => $e->getMessage()
        ]);
    }
}
        
// Asignar etiquetas
if (!empty($tags_str)) {
    try {
        $tags = array_map('trim', explode(',', $tags_str));
        foreach ($tags as $tag) {
            $term = get_term_by('slug', $tag, 'product_tag');
            if ($term) {
                $result = wp_set_post_terms($product_id, [$term->term_id], 'product_tag', true);
                if (is_wp_error($result)) {
                    throw new Exception($result->get_error_message());
                }
            }
        }
    } catch (Exception $e) {
        loscocos_debug_log('Error al asignar etiquetas', [
            'product_id' => $product_id,
            'tags' => $tags_str,
            'error' => $e->getMessage()
        ]);
    }
}
        
        echo '<p>✅ Producto importado: <strong>' . $name . '</strong> - $' . number_format($price, 0, ',', '.') . ' (Stock: ' . $stock . ')</p>';
        $imported++;
    } else {
        echo '<p style="color: red;">❌ Error al crear producto: ' . $name . '</p>';
        $skipped++;
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
echo '<p><strong>Nota:</strong> Para mejores resultados, sube imágenes reales para cada producto desde el panel de administración.</p>';
echo '<p><a href="' . admin_url() . '">← Volver al Panel de Administración</a></p>';
?>
