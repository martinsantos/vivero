<?php
/**
 * Importador de Productos WooCommerce - Los Cocos
 * 
 * @package LosCocos
 * @version 1.0.0
 */

// Cargar WordPress
require_once(dirname(dirname(dirname(__DIR__))) . '/wp-load.php');

// Verificar WooCommerce
if (!class_exists('WC_Product_Simple')) {
    die('WooCommerce no está activo.');
}

// Archivo CSV
$csv_file = __DIR__ . '/../../uploads/import-products-seasonal.csv';
if (!file_exists($csv_file)) {
    die('No se encontró el archivo CSV.');
}

// Función para crear producto
function create_product($data) {
    $product = new WC_Product_Simple();
    
    // Datos básicos
    $product->set_name($data['Name']);
    $product->set_description($data['Description']);
    $product->set_sku($data['SKU']);
    $product->set_regular_price($data['Regular Price']);
    $product->set_manage_stock(true);
    $product->set_stock_quantity($data['Stock']);
    $product->set_status('publish');
    
    // Guardar producto
    $product_id = $product->save();
    
    if ($product_id) {
        // Asignar categorías
        if (!empty($data['Categories'])) {
            $categories = array_map('trim', explode(',', $data['Categories']));
            foreach ($categories as $category) {
                $term = get_term_by('name', $category, 'product_cat');
                if ($term) {
                    wp_set_post_terms($product_id, [$term->term_id], 'product_cat', true);
                }
            }
        }
        
        // Asignar etiquetas
        if (!empty($data['Tags'])) {
            $tags = array_map('trim', explode(',', $data['Tags']));
            foreach ($tags as $tag) {
                $term = get_term_by('slug', $tag, 'product_tag');
                if ($term) {
                    wp_set_post_terms($product_id, [$term->term_id], 'product_tag', true);
                }
            }
        }
        
        return true;
    }
    
    return false;
}

// Leer CSV
$handle = fopen($csv_file, 'r');
if (!$handle) {
    die('No se pudo abrir el archivo CSV.');
}

// Saltar header
$header = fgetcsv($handle);
$mapping = array_flip($header);

$imported = 0;
$skipped = 0;

while (($data = fgetcsv($handle)) !== FALSE) {
    $product_data = [];
    foreach ($mapping as $field => $index) {
        $product_data[$field] = $data[$index];
    }
    
    // Verificar SKU
    if (empty($product_data['SKU'])) {
        $skipped++;
        continue;
    }
    
    // Verificar si el producto ya existe
    if (wc_get_product_id_by_sku($product_data['SKU'])) {
        echo "Producto existente: {$product_data['SKU']}\n";
        $skipped++;
        continue;
    }
    
    if (create_product($product_data)) {
        echo "Producto importado: {$product_data['Name']}\n";
        $imported++;
    } else {
        echo "Error al importar: {$product_data['Name']}\n";
        $skipped++;
    }
}

fclose($handle);

echo "\nResumen de importación:\n";
echo "Productos importados: $imported\n";
echo "Productos omitidos: $skipped\n";
