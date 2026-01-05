<?php
/**
 * Plugin Name: Los Cocos - CSV Import (WP-CLI)
 * Description: Comando WP-CLI para importar/actualizar productos WooCommerce desde un CSV con encabezados.
 * Author: Los Cocos
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_CLI')) {
    // Solo carga en contexto WP-CLI.
    return;
}

/**
 * Normaliza encabezados del CSV -> claves simples en minúsculas.
 */
function lc_normalize_header($header)
{
    $h = strtolower(trim($header));
    $h = preg_replace('/\s+/', ' ', $h);
    return $h;
}

/**
 * Limpia precio a formato decimal con punto.
 */
function lc_clean_price($value)
{
    $v = trim((string)$value);
    if ($v === '') return '';
    // Reemplazar separador decimal coma por punto si corresponde
    // Primero quitar espacios y símbolos no numéricos excepto dígitos, coma y punto
    $v = preg_replace('/[^\d\.,-]/', '', $v);
    // Si hay coma y punto, intentar detectar decimal (asumimos último separador es decimal)
    if (strpos($v, ',') !== false && strpos($v, '.') !== false) {
        $lastComma = strrpos($v, ',');
        $lastDot = strrpos($v, '.');
        if ($lastComma > $lastDot) {
            $v = str_replace('.', '', $v);
            $v = str_replace(',', '.', $v);
        } else {
            $v = str_replace(',', '', $v);
        }
    } elseif (strpos($v, ',') !== false && strpos($v, '.') === false) {
        // Solo coma -> usar como decimal
        $v = str_replace(',', '.', $v);
    } else {
        // Solo punto o nada -> quitar separadores de miles
        $parts = explode('.', $v);
        if (count($parts) > 2) {
            $last = array_pop($parts);
            $v = implode('', $parts) . '.' . $last;
        }
    }
    return $v;
}

/**
 * Limpia stock a entero >= 0
 */
function lc_clean_stock($value)
{
    $v = preg_replace('/[^\d-]/', '', (string)$value);
    if ($v === '' || !is_numeric($v)) return '';
    $n = (int)$v;
    return max(0, $n);
}

/**
 * Parsea CSV y devuelve un generador de filas asociativas.
 * Soporta columnas de atributos con prefijo "Attributes:" (cualquier caso).
 */
function lc_parse_csv($file, $delimiter = ',')
{
    if (!file_exists($file)) {
        throw new RuntimeException("Archivo CSV no encontrado: {$file}");
    }
    $handle = fopen($file, 'r');
    if (!$handle) {
        throw new RuntimeException('No se pudo abrir el archivo CSV.');
    }

    $headers = fgetcsv($handle, 0, $delimiter);
    if ($headers === false) {
        fclose($handle);
        throw new RuntimeException('CSV vacío o sin encabezados.');
    }

    // Normalizar encabezados y ubicar índices
    $normHeaders = [];
    foreach ($headers as $idx => $h) {
        $normHeaders[$idx] = lc_normalize_header($h);
    }

    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
        // Saltar filas vacías
        if (count(array_filter($row, fn($v) => trim((string)$v) !== '')) === 0) {
            continue;
        }
        $assoc = [];
        $attributes = [];
        foreach ($row as $i => $val) {
            $key = $normHeaders[$i] ?? 'col_' . $i;
            if (stripos($key, 'attributes:') === 0) {
                $attrName = trim(substr($key, strlen('attributes:')));
                if ($attrName !== '') {
                    $attributes[$attrName] = trim((string)$val);
                }
                continue;
            }
            $assoc[$key] = trim((string)$val);
        }
        if (!empty($attributes)) {
            $assoc['__attributes'] = $attributes;
        }
        yield $assoc;
    }

    fclose($handle);
}

/**
 * Convierte lista de categorías a IDs; crea términos faltantes.
 */
function lc_get_category_ids($categoriesString)
{
    if ($categoriesString === '' || $categoriesString === null) return [];
    // Separadores permitidos: | , > /
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

/**
 * Aplica atributos personalizados al producto (no taxonómicos).
 */
function lc_apply_product_attributes(WC_Product $product, array $attrs)
{
    if (empty($attrs)) return;
    $wcAttrs = [];
    $pos = 0;
    foreach ($attrs as $name => $value) {
        $name = wc_clean($name);
        $value = trim((string)$value);
        if ($name === '' || $value === '') continue;
        $pa = new WC_Product_Attribute();
        $pa->set_name($name);
        $pa->set_options([ $value ]);
        $pa->set_position($pos++);
        $pa->set_visible(true);
        $pa->set_variation(false);
        $wcAttrs[] = $pa;
    }
    if (!empty($wcAttrs)) {
        $product->set_attributes($wcAttrs);
    }
}

/**
 * Crea o actualiza un producto simple por SKU.
 */
function lc_upsert_product(array $data, $dry_run = false)
{
    $sku = $data['sku'] ?? $data['codigo'] ?? '';
    $sku = trim((string)$sku);
    if ($sku === '') {
        throw new InvalidArgumentException('Fila sin SKU.');
    }

    $product_id = wc_get_product_id_by_sku($sku);
    $is_update = $product_id ? true : false;
    $product = $is_update ? wc_get_product($product_id) : new WC_Product_Simple();

    // Campos básicos
    $name = $data['name'] ?? $data['nombre'] ?? '';
    $price = lc_clean_price($data['regular price'] ?? $data['price'] ?? $data['precio'] ?? '');
    $stock = lc_clean_stock($data['stock'] ?? $data['cantidad'] ?? '');
    $categories = $data['categories'] ?? $data['categoria'] ?? $data['categorias'] ?? '';
    $attributes = $data['__attributes'] ?? [];

    if ($name !== '') $product->set_name($name);

    if (!$is_update) {
        $product->set_sku($sku);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_stock_status('instock');
    }

    if ($price !== '') {
        $product->set_regular_price($price);
    }

    if ($stock !== '') {
        $product->set_manage_stock(true);
        $product->set_stock_quantity($stock);
        $product->set_stock_status($stock > 0 ? 'instock' : 'outofstock');
    }

    if ($categories !== '') {
        $cat_ids = lc_get_category_ids($categories);
        if (!empty($cat_ids)) {
            $product->set_category_ids($cat_ids);
        }
    }

    if (!empty($attributes)) {
        lc_apply_product_attributes($product, $attributes);
    }

    if ($dry_run) {
        return [ 'id' => $product_id ?: 0, 'updated' => $is_update, 'dry_run' => true ];
    }

    $saved_id = $product->save();
    if (!$saved_id) {
        throw new RuntimeException('No se pudo guardar el producto.');
    }
    return [ 'id' => $saved_id, 'updated' => $is_update, 'dry_run' => false ];
}

/**
 * Comando WP-CLI: wp loscocos import-csv --file=/ruta.csv [--delimiter=,] [--dry-run]
 */
WP_CLI::add_command('loscocos import-csv', function ($args, $assoc_args) {
    $file = $assoc_args['file'] ?? '';
    $delimiter = $assoc_args['delimiter'] ?? ',';
    $dry_run = isset($assoc_args['dry-run']);

    if ($file === '') {
        // Default sugerido
        $file = WP_CONTENT_DIR . '/uploads/import/products_woocommerce.csv';
    }

    WP_CLI::log('Archivo: ' . $file);
    WP_CLI::log('Delimitador: ' . $delimiter);
    WP_CLI::log('Modo: ' . ($dry_run ? 'dry-run' : 'write'));

    $total = 0;
    $created = 0;
    $updated = 0;
    $errors = 0;

    try {
        foreach (lc_parse_csv($file, $delimiter) as $row) {
            $total++;
            try {
                $result = lc_upsert_product($row, $dry_run);
                $sku = $row['sku'] ?? $row['codigo'] ?? '';
                $name = $row['name'] ?? $row['nombre'] ?? '';
                $prefix = $dry_run ? '[DRY]' : '';
                if ($result['updated']) {
                    $updated++;
                    WP_CLI::log("{$prefix} ✔ Actualizado SKU {$sku} {$name} (ID {$result['id']})");
                } else {
                    $created++;
                    WP_CLI::log("{$prefix} ✔ Creado SKU {$sku} {$name} (ID {$result['id']})");
                }
            } catch (Throwable $e) {
                $errors++;
                WP_CLI::warning('Error en fila ' . $total . ': ' . $e->getMessage());
            }
        }
    } catch (Throwable $e) {
        WP_CLI::error($e->getMessage());
        return;
    }

    $summary = "Filas: {$total} | Creados: {$created} | Actualizados: {$updated} | Errores: {$errors}";
    if ($dry_run) {
        WP_CLI::success('[DRY] ' . $summary);
    } else {
        WP_CLI::success($summary);
    }
});

/**
 * Comando WP-CLI: wp loscocos ensure-wc-pages [--flush]
 * Verifica/crea páginas núcleo de WooCommerce y las vincula en ajustes.
 */
function lc_ensure_page($option_name, $title, $slug, $content = '')
{
    $page_id = (int) get_option($option_name);
    if ($page_id) {
        $post = get_post($page_id);
        if ($post && $post->post_status !== 'trash') {
            return $page_id;
        }
    }

    // Buscar por slug o título existentes
    $existing = get_page_by_path($slug);
    if (!$existing) {
        $existing = get_page_by_title($title);
    }

    if ($existing && $existing->post_status !== 'trash') {
        $page_id = (int) $existing->ID;
    } else {
        $page_id = wp_insert_post([
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);
        if (is_wp_error($page_id) || !$page_id) {
            throw new RuntimeException('No se pudo crear la página: ' . $title);
        }
    }

    update_option($option_name, $page_id);
    return $page_id;
}

WP_CLI::add_command('loscocos ensure-wc-pages', function ($args, $assoc_args) {
    if (!class_exists('WooCommerce')) {
        WP_CLI::error('WooCommerce no está activo.');
        return;
    }

    $pages = [];
    try {
        $pages['shop'] = lc_ensure_page('woocommerce_shop_page_id', 'Tienda', 'tienda', '');
        $pages['cart'] = lc_ensure_page('woocommerce_cart_page_id', 'Carrito', 'carrito', '[woocommerce_cart]');
        $pages['checkout'] = lc_ensure_page('woocommerce_checkout_page_id', 'Finalizar compra', 'finalizar-compra', '[woocommerce_checkout]');
        $pages['myaccount'] = lc_ensure_page('woocommerce_myaccount_page_id', 'Mi cuenta', 'mi-cuenta', '[woocommerce_my_account]');
    } catch (Throwable $e) {
        WP_CLI::error($e->getMessage());
        return;
    }

    foreach ($pages as $key => $id) {
        WP_CLI::log("Página {$key}: ID {$id}");
    }

    if (isset($assoc_args['flush'])) {
        flush_rewrite_rules();
        WP_CLI::success('Páginas verificadas/vinculadas y reglas de reescritura regeneradas.');
    } else {
        WP_CLI::success('Páginas verificadas/vinculadas. Use --flush para regenerar permalinks.');
    }
});
