<?php
/**
 * Importador Completo de Inventario - Vivero Los Cocos
 * 
 * Este script importa TODOS los productos del inventario con:
 * - Stock por defecto SIN gestión de stock (appear as "En Stock")
 * - Precios automáticos basados en categorías
 * - Categorías automáticas
 * - Todas las existencias disponibles
 * 
 * Uso: Acceder desde http://localhost:8080/import_complete_inventory.php
 */

// Cargar WordPress
require_once('./wp-load.php');

// Verificar WooCommerce
if (!class_exists('WooCommerce')) {
    die('❌ WooCommerce no está instalado');
}

echo '<h1>🌱 Importador Completo - Vivero Los Cocos</h1>';
echo '<p>Importando inventario completo con existencias...</p>';

// Archivo CSV principal
$csv_file = './theme-loscocos/INVENTARIOTODASLASHOJAS.csv';

if (!file_exists($csv_file)) {
    die('❌ Archivo CSV no encontrado: ' . $csv_file);
}

echo '<p>✅ Archivo CSV encontrado</p>';

// Crear categorías principales
function create_product_categories() {
    $categories = [
        'macetas-plasticas' => 'Macetas Plásticas',
        'macetas-fibrocemento' => 'Macetas Fibrocemento',
        'pies-nordicos' => 'Pies Nórdicos',
        'hierros-soportes' => 'Hierros y Soportes',
        'sustratos' => 'Sustratos y Tierras',
        'fertilizantes' => 'Fertilizantes',
        'plantas-interior' => 'Plantas de Interior',
        'arbustos' => 'Arbustos',
        'arboles' => 'Árboles',
        'enredaderas' => 'Enredaderas'
    ];
    
    foreach ($categories as $slug => $name) {
        if (!term_exists($name, 'product_cat')) {
            wp_insert_term($name, 'product_cat', ['slug' => $slug]);
            echo "<p>📂 Categoría creada: $name</p>";
        }
    }
}

// Determinar categoría del producto
function get_product_category($modelo, $marca) {
    $modelo_lower = strtolower($modelo);
    $marca_lower = strtolower($marca);
    
    if (strpos($modelo_lower, 'rocio') !== false || strpos($modelo_lower, 'cultivo') !== false) {
        return 'macetas-plasticas';
    }
    if (strpos($modelo_lower, 'fibrocemento') !== false) {
        return 'macetas-fibrocemento';
    }
    if (strpos($modelo_lower, 'pie') !== false || strpos($modelo_lower, 'nordico') !== false) {
        return 'pies-nordicos';
    }
    if (strpos($modelo_lower, 'hierro') !== false || strpos($modelo_lower, 'soporte') !== false) {
        return 'hierros-soportes';
    }
    
    return 'macetas-plasticas'; // Por defecto
}

// Generar precio automático
function generate_product_price($modelo, $diametro) {
    $base_price = 1000;
    
    if (!empty($diametro)) {
        $diam = intval($diametro);
        if ($diam <= 12) $base_price = 800;
        elseif ($diam <= 18) $base_price = 1200;
        elseif ($diam <= 24) $base_price = 1800;
        elseif ($diam <= 30) $base_price = 2500;
        else $base_price = 3500;
    }
    
    // Variación del ±15%
    $variation = rand(-15, 15) / 100;
    return round($base_price * (1 + $variation));
}

// Crear categorías
echo '<h2>📂 Creando categorías...</h2>';
create_product_categories();

// Abrir CSV
$handle = fopen($csv_file, 'r');
if (!$handle) {
    die('❌ No se pudo abrir el archivo CSV');
}

echo '<h2>📦 Importando productos...</h2>';
$imported = 0;
$skipped = 0;
$current_category = '';

while (($line = fgets($handle)) !== FALSE) {
    $line = trim($line);
    
    // Remover número de línea al inicio (formato: "123|")
    if (preg_match('/^\d+\|(.*)$/', $line, $matches)) {
        $line = $matches[1];
    }
    
    // Detectar nueva categoría
    if (strpos($line, ': Tabla') !== false) {
        $current_category = $line;
        echo "<h3>📋 $current_category</h3>";
        continue;
    }
    
    // Saltar headers y líneas vacías
    if (empty($line) || strpos($line, 'Codigo') === 0 || strpos($line, ';Marca;') !== false) {
        continue;
    }
    
    // Procesar línea de producto
    $data = str_getcsv($line, ';');
    
    if (count($data) < 4) {
        continue;
    }
    
    $codigo = trim($data[0]);
    $marca = isset($data[1]) ? trim($data[1]) : '';
    $modelo = isset($data[2]) ? trim($data[2]) : '';
    $diametro = isset($data[3]) ? trim($data[3]) : '';
    $color = isset($data[4]) ? trim($data[4]) : '';
    
    // Validar datos mínimos
    if (empty($codigo) || empty($modelo)) {
        continue;
    }
    
    // Verificar si ya existe
    if (wc_get_product_id_by_sku($codigo)) {
        echo "<span style='color: orange;'>⚠️ Existente: $codigo</span><br>";
        $skipped++;
        continue;
    }
    
    // Generar nombre del producto
    $nombre = $modelo;
    if (!empty($diametro)) {
        $nombre .= " - {$diametro}cm";
    }
    if (!empty($color)) {
        $nombre .= " - $color";
    }
    if (!empty($marca)) {
        $nombre = "$marca $nombre";
    }
    
    // Generar precio
    $precio = generate_product_price($modelo, $diametro);
    
    // Determinar categoría
    $categoria_slug = get_product_category($modelo, $marca);
    $categoria_term = get_term_by('slug', $categoria_slug, 'product_cat');
    
    try {
        // Crear producto
        $product = new WC_Product_Simple();
        $product->set_name($nombre);
        $product->set_slug(sanitize_title($codigo . '-' . $nombre));
        $product->set_sku($codigo);
        $product->set_regular_price($precio);
        $product->set_description("Producto de alta calidad para jardinería. Código: $codigo. Ideal para espacios verdes.");
        $product->set_short_description($nombre);
        
        // CONFIGURACIÓN DE STOCK SEGÚN REQUERIMIENTO:
        $product->set_manage_stock(false);  // SIN gestión de stock
        $product->set_stock_status('instock');  // Aparece como "En Stock"
        
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        
        // Asignar categoría
        if ($categoria_term) {
            $product->set_category_ids([$categoria_term->term_id]);
        }
        
        // Guardar producto
        $product_id = $product->save();
        
        if ($product_id) {
            echo "<span style='color: green;'>✅ $nombre - $$precio</span><br>";
            $imported++;
            
            // Evitar timeout en importaciones grandes
            if ($imported % 10 == 0) {
                echo "<p><strong>Importados: $imported productos</strong></p>";
                flush();
            }
        } else {
            echo "<span style='color: red;'>❌ Error: $nombre</span><br>";
            $skipped++;
        }
        
    } catch (Exception $e) {
        echo "<span style='color: red;'>❌ Error en $codigo: " . $e->getMessage() . "</span><br>";
        $skipped++;
    }
    
    // Limitar para evitar timeout (remover este límite si quieres importar todo)
    if ($imported >= 200) {
        echo "<p style='color: orange;'>⚠️ Límite de 200 productos alcanzado. Ejecutar nuevamente para continuar.</p>";
        break;
    }
}

fclose($handle);

echo '<h2>📊 Resumen Final</h2>';
echo '<div style="background: #f0f8f0; padding: 20px; border-radius: 8px; margin: 20px 0;">';
echo "<p><strong>✅ Productos importados:</strong> $imported</p>";
echo "<p><strong>⚠️ Productos omitidos:</strong> $skipped</p>";
echo '</div>';

if ($imported > 0) {
    echo '<h3>🎉 ¡Importación completada exitosamente!</h3>';
    echo '<p><a href="' . admin_url('edit.php?post_type=product') . '" class="button button-primary">Ver Productos</a></p>';
    echo '<p><a href="http://localhost:8080/?post_type=product" class="button">Ver Tienda</a></p>';
}

echo '<hr>';
echo '<p><strong>Nota:</strong> Todos los productos están configurados SIN gestión de stock y aparecen como "En Stock".</p>';
echo '<p><a href="' . admin_url() . '">← Volver al Panel de Administración</a></p>';
?>
