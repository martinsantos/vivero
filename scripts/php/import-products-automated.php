<?php
/**
 * 📦 Los Cocos - Sistema de Importación Automatizada de Productos
 * Version: 2.0.0
 * 
 * Importa productos desde CSV y configura categorías automáticamente
 */

// Solo permitir ejecución desde wp-cli o admin
if (!defined('WP_CLI') && !is_admin()) {
    die('Acceso no autorizado');
}

require_once(dirname(__FILE__) . '/wp-load.php');

echo \"📦 ================================================\n\";
echo \"📦 LOS COCOS - IMPORTADOR DE PRODUCTOS v2.0\n\";
echo \"📦 ================================================\n\n\";

// Verificar que WooCommerce esté activo
if (!function_exists('wc_get_product')) {
    die(\"❌ Error: WooCommerce no está activo.\n\");
}

/**
 * Función para limpiar y normalizar texto
 */
function normalize_text($text) {
    $text = trim($text);
    $text = str_replace(['\"', \"'\"], '', $text);
    return $text;
}

/**
 * Función para crear categorías si no existen
 */
function ensure_category($category_name, $parent_id = 0) {
    $slug = sanitize_title($category_name);
    
    // Verificar si la categoría ya existe
    $existing = get_term_by('slug', $slug, 'product_cat');
    if ($existing) {
        return $existing->term_id;
    }
    
    // Crear nueva categoría
    $result = wp_insert_term($category_name, 'product_cat', array(
        'slug' => $slug,
        'parent' => $parent_id
    ));
    
    if (is_wp_error($result)) {
        echo \"⚠️  No se pudo crear categoría: $category_name\n\";
        return 0;
    }
    
    echo \"✅ Categoría creada: $category_name\n\";
    return $result['term_id'];
}

/**
 * Configurar categorías principales
 */
echo \"📂 Configurando categorías principales...\n\";

$main_categories = [
    'Plantas de Interior' => [
        'Plantas de Sombra',
        'Plantas de Sol Indirecto',
        'Plantas Purificadoras'
    ],
    'Plantas de Exterior' => [
        'Plantas de Sol',
        'Plantas de Sombra',
        'Plantas Resistentes'
    ],
    'Árboles y Arbustos' => [
        'Árboles Frutales',
        'Árboles Ornamentales',
        'Arbustos Decorativos'
    ],
    'Macetas y Contenedores' => [
        'Macetas de Barro',
        'Macetas de Plástico',
        'Jardineras'
    ],
    'Herramientas y Soportes' => [
        'Herramientas de Jardín',
        'Soportes para Plantas',
        'Sistemas de Riego'
    ],
    'Fertilizantes y Sustratos' => [
        'Fertilizantes Orgánicos',
        'Fertilizantes Químicos',
        'Sustratos y Tierras'
    ],
    'Semillas' => [
        'Semillas de Flores',
        'Semillas de Hortalizas',
        'Semillas de Hierbas'
    ],
    'Accesorios de Jardín' => [
        'Decoración',
        'Iluminación',
        'Muebles de Jardín'
    ]
];

$category_map = [];

foreach ($main_categories as $main_cat => $subcats) {
    $main_id = ensure_category($main_cat);
    $category_map[$main_cat] = $main_id;
    
    foreach ($subcats as $subcat) {
        $sub_id = ensure_category($subcat, $main_id);
        $category_map[$subcat] = $sub_id;
    }
}

echo \"\n📊 Categorías configuradas: \" . count($category_map) . \"\n\n\";

/**
 * Productos de muestra para generar un catálogo inicial
 */
echo \"🌱 Creando productos de muestra...\n\";

$sample_products = [
    // Plantas de Interior
    [
        'name' => 'Monstera Deliciosa',
        'price' => 15000,
        'category' => 'Plantas de Interior',
        'description' => 'Planta de interior muy popular por sus hojas grandes y perforadas. Perfecta para espacios con luz indirecta.',
        'short_description' => 'Planta tropical de interior con hojas decorativas únicas.',
        'stock' => 25
    ],
    [
        'name' => 'Pothos Dorado',
        'price' => 8000,
        'category' => 'Plantas de Interior',
        'description' => 'Planta colgante ideal para principiantes. Muy resistente y de crecimiento rápido.',
        'short_description' => 'Planta colgante perfecta para decorar estantes.',
        'stock' => 40
    ],
    [
        'name' => 'Sansevieria (Lengua de Suegra)',
        'price' => 12000,
        'category' => 'Plantas Purificadoras',
        'description' => 'Planta purificadora del aire, muy resistente y de bajo mantenimiento.',
        'short_description' => 'Planta purificadora de aire, ideal para dormitorios.',
        'stock' => 30
    ],
    
    // Plantas de Exterior
    [
        'name' => 'Rosal Trepador',
        'price' => 20000,
        'category' => 'Plantas de Sol',
        'description' => 'Rosal trepador con flores fragantes, ideal para pérgolas y muros.',
        'short_description' => 'Rosal trepador con hermosas flores aromáticas.',
        'stock' => 15
    ],
    [
        'name' => 'Lavanda',
        'price' => 6000,
        'category' => 'Plantas Resistentes',
        'description' => 'Planta aromática muy resistente, perfecta para jardines secos.',
        'short_description' => 'Planta aromática resistente a la sequía.',
        'stock' => 50
    ],
    
    // Árboles y Arbustos
    [
        'name' => 'Limonero en Maceta',
        'price' => 35000,
        'category' => 'Árboles Frutales',
        'description' => 'Limonero joven en maceta, perfecto para patios y terrazas.',
        'short_description' => 'Árbol frutal cítrico para espacios pequeños.',
        'stock' => 10
    ],
    [
        'name' => 'Jacarandá',
        'price' => 45000,
        'category' => 'Árboles Ornamentales',
        'description' => 'Árbol ornamental con hermosas flores violetas en primavera.',
        'short_description' => 'Árbol ornamental con flores violetas espectaculares.',
        'stock' => 8
    ],
    
    // Macetas y Contenedores
    [
        'name' => 'Maceta de Barro 30cm',
        'price' => 4000,
        'category' => 'Macetas de Barro',
        'description' => 'Maceta de barro cocido de 30cm de diámetro, ideal para plantas medianas.',
        'short_description' => 'Maceta tradicional de barro de 30cm.',
        'stock' => 100
    ],
    [
        'name' => 'Jardinera Rectangular 60cm',
        'price' => 8500,
        'category' => 'Jardineras',
        'description' => 'Jardinera rectangular de 60cm, perfecta para balcones y terrazas.',
        'short_description' => 'Jardinera rectangular para espacios alargados.',
        'stock' => 25
    ],
    
    // Herramientas y Soportes
    [
        'name' => 'Soporte Universal para Plantas',
        'price' => 12000,
        'category' => 'Soportes para Plantas',
        'description' => 'Soporte metálico ajustable para plantas trepadoras y colgantes.',
        'short_description' => 'Soporte metálico versátil para diferentes tipos de plantas.',
        'stock' => 50
    ],
    [
        'name' => 'Kit de Herramientas Básicas',
        'price' => 15000,
        'category' => 'Herramientas de Jardín',
        'description' => 'Kit completo con pala, rastrillo, tijeras de podar y guantes.',
        'short_description' => 'Kit esencial de herramientas para jardinería.',
        'stock' => 20
    ],
    
    // Fertilizantes y Sustratos
    [
        'name' => 'Sustrato Universal 50L',
        'price' => 3500,
        'category' => 'Sustratos y Tierras',
        'description' => 'Sustrato universal de alta calidad para todo tipo de plantas.',
        'short_description' => 'Sustrato nutritivo para plantas de interior y exterior.',
        'stock' => 80
    ],
    [
        'name' => 'Fertilizante Orgánico 1Kg',
        'price' => 2500,
        'category' => 'Fertilizantes Orgánicos',
        'description' => 'Fertilizante orgánico 100% natural, ideal para plantas comestibles.',
        'short_description' => 'Abono orgánico natural para un jardín saludable.',
        'stock' => 60
    ],
    
    // Semillas
    [
        'name' => 'Semillas de Girasol',
        'price' => 800,
        'category' => 'Semillas de Flores',
        'description' => 'Semillas de girasol gigante, fáciles de cultivar y muy vistosas.',
        'short_description' => 'Semillas para cultivar girasoles gigantes.',
        'stock' => 200
    ],
    [
        'name' => 'Semillas de Tomate Cherry',
        'price' => 1200,
        'category' => 'Semillas de Hortalizas',
        'description' => 'Semillas de tomate cherry, perfectas para huertos urbanos.',
        'short_description' => 'Semillas para cultivar tomates cherry en casa.',
        'stock' => 150
    ]
];

$created_products = 0;
$errors = 0;

foreach ($sample_products as $product_data) {
    // Verificar si el producto ya existe
    $existing = get_posts([
        'post_type' => 'product',
        'title' => $product_data['name'],
        'post_status' => 'any',
        'numberposts' => 1
    ]);
    
    if (!empty($existing)) {
        echo \"⏭️  Producto ya existe: {$product_data['name']}\n\";
        continue;
    }
    
    try {
        // Crear producto
        $product = new WC_Product_Simple();
        $product->set_name($product_data['name']);
        $product->set_description($product_data['description']);
        $product->set_short_description($product_data['short_description']);
        $product->set_regular_price($product_data['price']);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_manage_stock(true);
        $product->set_stock_quantity($product_data['stock']);
        $product->set_stock_status('instock');
        
        // Asignar categoría
        if (isset($category_map[$product_data['category']])) {
            $product->set_category_ids([$category_map[$product_data['category']]]);
        }
        
        // Guardar producto
        $product_id = $product->save();
        
        if ($product_id) {
            echo \"✅ Producto creado: {$product_data['name']} (ID: $product_id)\n\";
            $created_products++;
            
            // Generar imagen SVG para el producto
            if (function_exists('loscocos_get_product_image')) {
                loscocos_get_product_image($product_id);
                echo \"   🖼️  Imagen SVG generada\n\";
            }
        } else {
            echo \"❌ Error al crear producto: {$product_data['name']}\n\";
            $errors++;
        }
        
    } catch (Exception $e) {
        echo \"❌ Error al crear producto {$product_data['name']}: \" . $e->getMessage() . \"\n\";
        $errors++;
    }
}

echo \"\n📊 ================================================\n\";
echo \"📊 RESUMEN DE IMPORTACIÓN\n\";
echo \"📊 ================================================\n\";
echo \"✅ Productos creados: $created_products\n\";
echo \"❌ Errores: $errors\n\";
echo \"📂 Categorías disponibles: \" . count($category_map) . \"\n\";

// Mostrar algunas estadísticas
$total_products = wp_count_posts('product');
echo \"📦 Total de productos en la tienda: \" . $total_products->publish . \"\n\";

echo \"\n🎉 ¡Importación completada!\n\";
echo \"\n🌐 Próximos pasos:\n\";
echo \"   1. Visita la tienda: http://localhost:8080/shop/\n\";
echo \"   2. Verifica que los productos se muestren correctamente\n\";
echo \"   3. Comprueba que las imágenes SVG se generen automáticamente\n\";
echo \"   4. Prueba el proceso de compra completo\n\";

?>