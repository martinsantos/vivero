<?php
/**
 * Importador Completo del Inventario Vivero Los Cocos
 * Procesa TODAS las categorías de productos
 */

// Verificar que estamos en WordPress
if (!defined('ABSPATH')) {
    exit('Este script debe ejecutarse desde WordPress');
}

function loscocos_import_full_inventory() {
    // Verificar WooCommerce
    if (!class_exists('WooCommerce')) {
        return array('error' => 'WooCommerce no está instalado');
    }
    
    $csv_file = get_template_directory() . '/INVENTARIOTODASLASHOJAS.csv';
    
    if (!file_exists($csv_file)) {
        return array('error' => 'Archivo CSV no encontrado: ' . $csv_file);
    }
    
    $imported = 0;
    $errors = array();
    $current_category = '';
    $current_table = '';
    
    // Crear categorías principales
    loscocos_create_main_categories();
    
    // Abrir CSV
    $handle = fopen($csv_file, 'r');
    
    while (($line = fgets($handle)) !== FALSE) {
        $line = trim($line);
        
        // Detectar nueva categoría/tabla
        if (strpos($line, ': Tabla') !== false) {
            $current_table = $line;
            $current_category = loscocos_extract_category_from_table($line);
            echo "📂 Procesando categoría: {$current_category}\n";
            continue;
        }
        
        // Saltar headers y líneas vacías
        if (empty($line) || strpos($line, 'Codigo') === 0 || strpos($line, ';;') === 0) {
            continue;
        }
        
        // Procesar línea de producto
        $data = str_getcsv($line, ';');
        
        try {
            if (loscocos_is_valid_product_row($data, $current_category)) {
                $product_id = loscocos_create_product_from_row($data, $current_category);
                if ($product_id) {
                    $imported++;
                    echo "✓ Importado: " . loscocos_get_product_name_from_row($data, $current_category) . " (ID: {$product_id})\n";
                }
            }
        } catch (Exception $e) {
            $errors[] = "Error en categoría {$current_category}: " . $e->getMessage();
        }
    }
    
    fclose($handle);
    
    return array(
        'imported' => $imported,
        'errors' => $errors
    );
}

function loscocos_create_main_categories() {
    $categories = array(
        'macetas-plasticas' => array(
            'name' => 'Macetas Plásticas',
            'description' => 'Macetas de plástico de diferentes tamaños y colores'
        ),
        'macetas-fibrocemento' => array(
            'name' => 'Macetas Fibrocemento', 
            'description' => 'Macetas de fibrocemento resistentes y duraderas'
        ),
        'pies-nordicos' => array(
            'name' => 'Pies Nórdicos',
            'description' => 'Bases de madera estilo nórdico para macetas'
        ),
        'hierros-soportes' => array(
            'name' => 'Hierros y Soportes',
            'description' => 'Estructuras metálicas para plantas'
        ),
        'sustratos' => array(
            'name' => 'Sustratos y Tierras',
            'description' => 'Tierras preparadas, turbas y sustratos especiales'
        ),
        'fertilizantes' => array(
            'name' => 'Fertilizantes',
            'description' => 'Nutrientes y fertilizantes para plantas'
        ),
        'fitosanitarios' => array(
            'name' => 'Fitosanitarios',
            'description' => 'Productos para el cuidado y protección de plantas'
        ),
        'productos-organicos' => array(
            'name' => 'Productos Orgánicos',
            'description' => 'Fertilizantes y tratamientos orgánicos'
        ),
        'plantas-interior' => array(
            'name' => 'Plantas de Interior',
            'description' => 'Plantas perfectas para interiores'
        ),
        'arbustos' => array(
            'name' => 'Arbustos',
            'description' => 'Arbustos ornamentales y decorativos'
        ),
        'arboles' => array(
            'name' => 'Árboles',
            'description' => 'Árboles para jardín y paisajismo'
        ),
        'enredaderas' => array(
            'name' => 'Enredaderas',
            'description' => 'Plantas trepadoras y enredaderas'
        ),
        'platos' => array(
            'name' => 'Platos y Accesorios',
            'description' => 'Platos para macetas y accesorios'
        )
    );
    
    foreach ($categories as $slug => $cat_data) {
        wp_insert_term($cat_data['name'], 'product_cat', array(
            'description' => $cat_data['description'],
            'slug' => $slug
        ));
    }
}

function loscocos_extract_category_from_table($table_line) {
    if (strpos($table_line, 'Macetas Plasticas') !== false) return 'macetas-plasticas';
    if (strpos($table_line, 'Macetas Fibrocemento') !== false) return 'macetas-fibrocemento';
    if (strpos($table_line, 'Pie Nordico') !== false) return 'pies-nordicos';
    if (strpos($table_line, 'Hierro Soportes') !== false) return 'hierros-soportes';
    if (strpos($table_line, 'Sustratos') !== false) return 'sustratos';
    if (strpos($table_line, 'Fertilizante') !== false) return 'fertilizantes';
    if (strpos($table_line, 'Glacoxan') !== false) return 'fitosanitarios';
    if (strpos($table_line, 'Fertifox') !== false) return 'fitosanitarios';
    if (strpos($table_line, 'Organico') !== false) return 'productos-organicos';
    if (strpos($table_line, 'Plantas de Interior') !== false) return 'plantas-interior';
    if (strpos($table_line, 'Arbustos') !== false) return 'arbustos';
    if (strpos($table_line, 'Arboles') !== false) return 'arboles';
    if (strpos($table_line, 'Enredaderas') !== false) return 'enredaderas';
    return 'otros';
}

function loscocos_is_valid_product_row($data, $category) {
    if (empty($data) || count($data) < 3) return false;
    
    $codigo = trim($data[0]);
    
    // Debe tener código o descripción válida
    if (empty($codigo) && empty(trim($data[2]))) return false;
    
    // Saltar líneas de header o separadores
    if (in_array($codigo, ['Codigo', '', ';;'])) return false;
    
    return true;
}

function loscocos_create_product_from_row($data, $category) {
    $codigo = trim($data[0]);
    $marca = isset($data[1]) ? trim($data[1]) : '';
    $descripcion = isset($data[2]) ? trim($data[2]) : '';
    
    // Generar nombre del producto
    $nombre = loscocos_generate_product_name($data, $category);
    $precio = loscocos_generate_product_price($data, $category);
    $descripcion_completa = loscocos_generate_product_description($data, $category);
    $sku = !empty($codigo) ? $codigo : sanitize_title($nombre);
    
    // Crear producto
    $product = new WC_Product_Simple();
    $product->set_name($nombre);
    $product->set_slug(sanitize_title($sku . '-' . $nombre));
    $product->set_description($descripcion_completa);
    $product->set_short_description(substr($descripcion_completa, 0, 150) . '...');
    $product->set_sku($sku);
    $product->set_regular_price($precio);
    $product->set_stock_quantity(rand(5, 50)); // Stock aleatorio
    $product->set_manage_stock(true);
    $product->set_stock_status('instock');
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $product->set_featured(rand(0, 1) == 1); // 50% productos destacados
    
    // Asignar categoría
    $categoria_id = loscocos_get_category_id_by_slug($category);
    if ($categoria_id) {
        $product->set_category_ids(array($categoria_id));
    }
    
    // Guardar producto
    return $product->save();
}

function loscocos_generate_product_name($data, $category) {
    $codigo = trim($data[0]);
    $marca = isset($data[1]) ? trim($data[1]) : '';
    $descripcion = isset($data[2]) ? trim($data[2]) : '';
    
    $nombre = '';
    
    switch ($category) {
        case 'macetas-plasticas':
            $diametro = isset($data[3]) ? trim($data[3]) : '';
            $color = isset($data[4]) ? trim($data[4]) : '';
            $nombre = $descripcion;
            if (!empty($diametro)) $nombre .= " {$diametro}cm";
            if (!empty($color)) $nombre .= " - {$color}";
            if (!empty($marca)) $nombre = "{$marca} {$nombre}";
            break;
            
        case 'macetas-fibrocemento':
            $altura = isset($data[3]) ? trim($data[3]) : '';
            $ancho = isset($data[4]) ? trim($data[4]) : '';
            $nombre = $descripcion;
            if (!empty($altura) && !empty($ancho)) {
                $nombre .= " {$altura}x{$ancho}cm";
            } elseif (!empty($altura)) {
                $nombre .= " {$altura}cm";
            }
            break;
            
        case 'pies-nordicos':
            $altura = isset($data[3]) ? trim($data[3]) : '';
            $diametro = isset($data[4]) ? trim($data[4]) : '';
            $nombre = $descripcion;
            if (!empty($altura)) $nombre .= " - Altura {$altura}cm";
            if (!empty($diametro)) $nombre .= " - Diámetro {$diametro}cm";
            break;
            
        case 'plantas-interior':
        case 'arbustos':
        case 'arboles':
        case 'enredaderas':
            $tamaño = isset($data[3]) ? trim($data[3]) : '';
            $nombre = $descripcion;
            if (!empty($tamaño)) $nombre .= " - {$tamaño}";
            break;
            
        case 'sustratos':
            $litros = isset($data[3]) ? trim($data[3]) : '';
            $nombre = $descripcion;
            if (!empty($litros)) $nombre .= " - {$litros}L";
            if (!empty($marca)) $nombre = "{$marca} {$nombre}";
            break;
            
        default:
            $nombre = !empty($descripcion) ? $descripcion : $codigo;
            if (!empty($marca)) $nombre = "{$marca} {$nombre}";
            break;
    }
    
    return !empty($nombre) ? $nombre : $codigo;
}

function loscocos_generate_product_price($data, $category) {
    // Precios base por categoría
    $base_prices = array(
        'macetas-plasticas' => 500,
        'macetas-fibrocemento' => 2000,
        'pies-nordicos' => 3000,
        'hierros-soportes' => 1500,
        'sustratos' => 800,
        'fertilizantes' => 1200,
        'fitosanitarios' => 1800,
        'productos-organicos' => 2200,
        'plantas-interior' => 1500,
        'arbustos' => 2500,
        'arboles' => 4500,
        'enredaderas' => 2000,
        'platos' => 300
    );
    
    $base_price = isset($base_prices[$category]) ? $base_prices[$category] : 1000;
    
    // Ajustar precio según tamaño/características
    switch ($category) {
        case 'macetas-plasticas':
            $diametro = isset($data[3]) ? intval($data[3]) : 12;
            $base_price = 200 + ($diametro * 25);
            break;
            
        case 'macetas-fibrocemento':
            $altura = isset($data[3]) ? intval($data[3]) : 20;
            $base_price = 1000 + ($altura * 80);
            break;
            
        case 'plantas-interior':
            $tamaño = isset($data[3]) ? $data[3] : '';
            if (strpos($tamaño, '3 litros') !== false) $base_price = 2500;
            elseif (strpos($tamaño, '5 litros') !== false) $base_price = 4000;
            elseif (strpos($tamaño, '7 litros') !== false) $base_price = 5500;
            elseif (strpos($tamaño, '10 litros') !== false) $base_price = 7000;
            elseif (strpos($tamaño, '15 litros') !== false) $base_price = 9000;
            else $base_price = 1200;
            break;
            
        case 'arboles':
            $tamaño = isset($data[2]) ? $data[2] : '';
            if (strpos($tamaño, '15 litros') !== false) $base_price = 8000;
            elseif (strpos($tamaño, '20 litros') !== false) $base_price = 12000;
            else $base_price = 5000;
            break;
    }
    
    // Variación aleatoria del ±20%
    $variation = rand(-20, 20) / 100;
    return round($base_price * (1 + $variation));
}

function loscocos_generate_product_description($data, $category) {
    $marca = isset($data[1]) ? trim($data[1]) : '';
    $descripcion = isset($data[2]) ? trim($data[2]) : '';
    
    $desc_base = "Producto de alta calidad para tu jardín y hogar. ";
    
    switch ($category) {
        case 'macetas-plasticas':
            $desc_base .= "Maceta de plástico resistente a la intemperie, perfecta para plantas de interior y exterior. ";
            $color = isset($data[4]) ? trim($data[4]) : '';
            if (!empty($color)) $desc_base .= "Disponible en color {$color}. ";
            break;
            
        case 'macetas-fibrocemento':
            $desc_base .= "Maceta de fibrocemento, material duradero y elegante que combina resistencia y estética. ";
            break;
            
        case 'plantas-interior':
            $desc_base .= "Planta de interior perfecta para decorar tu hogar. Fácil cuidado y gran resistencia. ";
            break;
            
        case 'sustratos':
            $desc_base .= "Sustrato de primera calidad, rico en nutrientes para el óptimo crecimiento de tus plantas. ";
            break;
            
        case 'arboles':
            $desc_base .= "Árbol ornamental perfecto para jardines y espacios exteriores. Crecimiento vigoroso y gran belleza. ";
            break;
            
        case 'fitosanitarios':
            $desc_base .= "Producto fitosanitario para el cuidado y protección de tus plantas contra plagas y enfermedades. ";
            break;
    }
    
    if (!empty($marca)) {
        $desc_base .= "Marca: {$marca}. ";
    }
    
    $desc_base .= "Producto disponible en Vivero Los Cocos, tu lugar de confianza para plantas y jardinería.";
    
    return $desc_base;
}

function loscocos_get_category_id_by_slug($slug) {
    $term = get_term_by('slug', $slug, 'product_cat');
    return $term ? $term->term_id : null;
}

function loscocos_get_product_name_from_row($data, $category) {
    return loscocos_generate_product_name($data, $category);
}

// Ejecutar si se llama desde WP-CLI
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('loscocos import-full', function() {
        $result = loscocos_import_full_inventory();
        
        if (isset($result['error'])) {
            WP_CLI::error($result['error']);
        } else {
            WP_CLI::success("Importados {$result['imported']} productos del inventario completo.");
            if (!empty($result['errors'])) {
                WP_CLI::warning("Errores encontrados:");
                foreach ($result['errors'] as $error) {
                    WP_CLI::log($error);
                }
            }
        }
    });
}
?> 