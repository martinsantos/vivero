<?php
/**
 * Script para importar productos desde CSV
 * Vivero Los Cocos
 */

// Verificar que estamos en WordPress
if (!defined('ABSPATH')) {
    exit('Este script debe ejecutarse desde WordPress');
}

function loscocos_import_csv_products() {
    // Verificar WooCommerce
    if (!class_exists('WooCommerce')) {
        return array('error' => 'WooCommerce no está instalado');
    }
    
    $csv_file = get_template_directory() . '/INVENTARIO VIVERO LOS COCOS 0.1  - Hierro Soportes.csv';
    
    if (!file_exists($csv_file)) {
        return array('error' => 'Archivo CSV no encontrado: ' . $csv_file);
    }
    
    $imported = 0;
    $errors = array();
    
    // Abrir CSV
    $handle = fopen($csv_file, 'r');
    
    // Leer header
    $header = fgetcsv($handle);
    
    // Crear categorías base
    $categoria_hierros = wp_insert_term('Hierros y Soportes', 'product_cat', array(
        'description' => 'Hierros, soportes y accesorios para plantas',
        'slug' => 'hierros-soportes'
    ));
    
    $categoria_mensulas = wp_insert_term('Ménsulas', 'product_cat', array(
        'description' => 'Ménsulas para plantas',
        'slug' => 'mensulas'
    ));
    
    $categoria_aros = wp_insert_term('Aros y Ganchos', 'product_cat', array(
        'description' => 'Aros de pared y con ganchos',
        'slug' => 'aros-ganchos'
    ));
    
    $categoria_pies = wp_insert_term('Pies y Bases', 'product_cat', array(
        'description' => 'Pies y bases para macetas',
        'slug' => 'pies-bases'
    ));
    
    $categoria_portamacetas = wp_insert_term('Porta Macetas', 'product_cat', array(
        'description' => 'Porta macetas de diferentes tamaños',
        'slug' => 'porta-macetas'
    ));
    
    while (($data = fgetcsv($handle)) !== FALSE) {
        try {
            $codigo = trim($data[0]);
            $marca = trim($data[1]);
            $modelo = trim($data[2]);
            $diametro = trim($data[3]);
            $cantidad = trim($data[4]);
            $precio_csv = trim($data[5]);
            
            // Saltar filas vacías
            if (empty($codigo) || empty($modelo)) {
                continue;
            }
            
            // Generar nombre del producto
            $nombre = $modelo;
            if (!empty($diametro)) {
                $nombre .= " - Diámetro {$diametro}cm";
            }
            
            // Generar precio basado en el tipo de producto
            $precio = loscocos_generate_price($modelo, $diametro);
            
            // Generar descripción
            $descripcion = loscocos_generate_description($modelo, $diametro, $marca);
            
            // Determinar categoría
            $categoria_id = loscocos_get_category_id($modelo);
            
            // Crear producto
            $product = new WC_Product_Simple();
            $product->set_name($nombre);
            $product->set_slug(sanitize_title($codigo . '-' . $modelo));
            $product->set_description($descripcion);
            $product->set_short_description(substr($descripcion, 0, 150) . '...');
            $product->set_sku($codigo);
            $product->set_regular_price($precio);
            $product->set_stock_quantity($cantidad ? intval($cantidad) : 10);
            $product->set_manage_stock(true);
            $product->set_stock_status('instock');
            $product->set_status('publish');
            $product->set_catalog_visibility('visible');
            $product->set_featured(true);
            
            // Asignar categoría
            if ($categoria_id) {
                $product->set_category_ids(array($categoria_id));
            }
            
            // Guardar producto
            $product_id = $product->save();
            
            if ($product_id) {
                $imported++;
                echo "✓ Importado: {$nombre} (ID: {$product_id})\n";
            } else {
                $errors[] = "Error al crear producto: {$nombre}";
            }
            
        } catch (Exception $e) {
            $errors[] = "Error en fila {$imported}: " . $e->getMessage();
        }
    }
    
    fclose($handle);
    
    return array(
        'imported' => $imported,
        'errors' => $errors
    );
}

function loscocos_generate_price($modelo, $diametro) {
    // Generar precios realistas basados en el tipo de producto
    $base_price = 1000;
    
    if (stripos($modelo, 'mensula') !== false) {
        $base_price = stripos($modelo, 'chica') !== false ? 800 : 1200;
    } elseif (stripos($modelo, 'aro') !== false) {
        $base_price = 600;
    } elseif (stripos($modelo, 'porta') !== false) {
        $base_price = 1500;
    } elseif (stripos($modelo, 'pie') !== false) {
        if (stripos($modelo, 'bajo') !== false) {
            $base_price = 2500;
        } elseif (stripos($modelo, 'alto') !== false) {
            $base_price = 3200;
        } elseif (stripos($modelo, 'escalera') !== false) {
            $base_price = 4500;
        } elseif (stripos($modelo, 'nordico') !== false) {
            $base_price = 2800;
        } else {
            $base_price = 2000;
        }
    }
    
    // Ajustar por diámetro
    if (!empty($diametro)) {
        $diam = intval($diametro);
        if ($diam > 20) {
            $base_price += ($diam - 20) * 50;
        }
    }
    
    return $base_price;
}

function loscocos_generate_description($modelo, $diametro, $marca) {
    $desc = "Producto de hierro forjado ideal para el cuidado y decoración de plantas. ";
    
    if (stripos($modelo, 'mensula') !== false) {
        $desc .= "Mensula resistente para colgar macetas en paredes. Fabricada en hierro de alta calidad.";
    } elseif (stripos($modelo, 'aro') !== false) {
        $desc .= "Aro de pared perfecto para sostener plantas colgantes. Diseño elegante y funcional.";
    } elseif (stripos($modelo, 'porta') !== false) {
        $desc .= "Porta macetas diseñado para realzar la belleza de tus plantas. Estructura sólida y duradera.";
    } elseif (stripos($modelo, 'pie') !== false) {
        $desc .= "Base decorativa para macetas que combina funcionalidad y estilo. Ideal para interiores y exteriores.";
    }
    
    if (!empty($diametro)) {
        $desc .= " Diámetro: {$diametro}cm.";
    }
    
    $desc .= " Producto fabricado con materiales de primera calidad para garantizar durabilidad y resistencia a la intemperie.";
    
    return $desc;
}

function loscocos_get_category_id($modelo) {
    if (stripos($modelo, 'mensula') !== false) {
        $term = get_term_by('slug', 'mensulas', 'product_cat');
        return $term ? $term->term_id : null;
    } elseif (stripos($modelo, 'aro') !== false) {
        $term = get_term_by('slug', 'aros-ganchos', 'product_cat');
        return $term ? $term->term_id : null;
    } elseif (stripos($modelo, 'porta') !== false) {
        $term = get_term_by('slug', 'porta-macetas', 'product_cat');
        return $term ? $term->term_id : null;
    } elseif (stripos($modelo, 'pie') !== false) {
        $term = get_term_by('slug', 'pies-bases', 'product_cat');
        return $term ? $term->term_id : null;
    }
    
    // Categoría por defecto
    $term = get_term_by('slug', 'hierros-soportes', 'product_cat');
    return $term ? $term->term_id : null;
}

// Ejecutar si se llama desde WP-CLI
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('loscocos import-csv', function() {
        $result = loscocos_import_csv_products();
        
        if (isset($result['error'])) {
            WP_CLI::error($result['error']);
        } else {
            WP_CLI::success("Importados {$result['imported']} productos.");
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