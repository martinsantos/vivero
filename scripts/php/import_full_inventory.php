<?php
/**
 * Importador Completo y Simple de Productos
 * Importa productos desde CSV con una configuración predeterminada de existencias
 */

// Cargar WordPress y WooCommerce
require_once __DIR__ . '/wp-load.php';

// Verificar permisos (permitir WP-CLI)
if (!defined('WP_CLI') && !current_user_can('manage_options')) {
    wp_die('Se requieren permisos de administrador.');
}

// Verificar WooCommerce
if (!class_exists('WooCommerce')) {
    wp_die('WooCommerce no está activo.');
}

// Helpers simples
function lcif_clean_price($value)
{
    $v = trim((string)$value);
    if ($v === '') return '';
    $v = preg_replace('/[^\d\.,-]/', '', $v);
    if (strpos($v, ',') !== false && strpos($v, '.') === false) {
        $v = str_replace(',', '.', $v);
    } elseif (strpos($v, ',') !== false && strpos($v, '.') !== false) {
        $lastComma = strrpos($v, ',');
        $lastDot = strrpos($v, '.');
        if ($lastComma > $lastDot) {
            $v = str_replace('.', '', $v);
            $v = str_replace(',', '.', $v);
        } else {
            $v = str_replace(',', '', $v);
        }
    }
    return $v;
}

function lcif_clean_stock($value)
{
    $v = preg_replace('/[^\d-]/', '', (string)$value);
    if ($v === '' || !is_numeric($v)) return '';
    $n = (int)$v;
    return max(0, $n);
}

function lcif_category_ids_from_names($categoriesString)
{
    if ($categoriesString === '' || $categoriesString === null) return [];
    $parts = preg_split('/\s*[|,>\/]\s*/', (string)$categoriesString);
    $ids = [];
    foreach ($parts as $name) {
        $name = trim($name);
        if ($name === '') continue;
        $term = get_term_by('name', $name, 'product_cat');
        if (!$term || is_wp_error($term)) {
            $created = wp_insert_term($name, 'product_cat');
            if (!is_wp_error($created)) {
                $ids[] = (int)$created['term_id'];
            }
        } else {
            $ids[] = (int)$term->term_id;
        }
    }
    return array_values(array_unique($ids));
}

// Ruta del archivo CSV (configurable). Por defecto en uploads/import/products_woocommerce.csv
$csv_file = isset($_GET['file']) ? trim((string)$_GET['file']) : WP_CONTENT_DIR . '/uploads/import/products_woocommerce.csv';
// Si es ruta relativa, resolver contra ABSPATH
if ($csv_file !== '' && !preg_match('#^/|^[A-Za-z]:\\\\#', $csv_file)) {
    $csv_file = ABSPATH . ltrim($csv_file, '/');
}
if (!file_exists($csv_file)) {
    wp_die('Archivo CSV no encontrado: ' . $csv_file);
}

// Configuración de delimitador
$delimiter = isset($_GET['delimiter']) ? (string)$_GET['delimiter'] : ',';

// Abrir CSV
$handle = fopen($csv_file, 'r');
if (!$handle) {
    wp_die('No se pudo abrir el archivo CSV.');
}

// Leer encabezados
$headers = fgetcsv($handle, 0, $delimiter);
if ($headers === false) {
    fclose($handle);
    wp_die('CSV vacío o sin encabezados.');
}

// Normalizar encabezados
$header_map = [];
foreach ($headers as $i => $h) {
    $key = strtolower(trim($h));
    $header_map[$key] = $i;
}

$idx = function($key) use ($header_map) {
    return $header_map[$key] ?? null;
};

// Importar productos
$imported = 0;
while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
    // Saltar filas vacías
    if (count(array_filter($row, fn($v) => trim((string)$v) !== '')) === 0) {
        continue;
    }

    $sku_i = $idx('sku') ?? 0;
    $name_i = $idx('name') ?? $idx('nombre') ?? 2;
    $price_i = $idx('regular price') ?? $idx('price') ?? $idx('precio');
    $stock_i = $idx('stock') ?? $idx('cantidad');
    $cats_i  = $idx('categories') ?? $idx('categoria') ?? $idx('categorias');

    $sku = isset($row[$sku_i]) ? trim((string)$row[$sku_i]) : '';
    $name = isset($row[$name_i]) ? trim((string)$row[$name_i]) : '';

    if ($sku === '' || $name === '') {
        continue;
    }

    // Si el producto ya existe por SKU, no crear
    if (wc_get_product_id_by_sku($sku)) {
        continue;
    }

    $price_raw = ($price_i !== null && isset($row[$price_i])) ? trim((string)$row[$price_i]) : '';
    $stock_raw = ($stock_i !== null && isset($row[$stock_i])) ? trim((string)$row[$stock_i]) : '';
    $cats_raw  = ($cats_i  !== null && isset($row[$cats_i]))  ? trim((string)$row[$cats_i])  : '';

    $price = lcif_clean_price($price_raw);
    $stock = lcif_clean_stock($stock_raw);
    $cat_ids = lcif_category_ids_from_names($cats_raw);

    // Crear producto simple
    $product = new WC_Product_Simple();
    $product->set_name($name);
    $product->set_sku($sku);

    if ($price !== '') {
        $product->set_regular_price($price);
    }

    if ($stock !== '') {
        $product->set_manage_stock(true);
        $product->set_stock_quantity($stock);
        $product->set_stock_status($stock > 0 ? 'instock' : 'outofstock');
    } else {
        $product->set_manage_stock(false);
    }

    if (!empty($cat_ids)) {
        $product->set_category_ids($cat_ids);
    }

    $product->save();

    $imported++;
}

fclose($handle);

echo "Productos importados: $imported\n";
