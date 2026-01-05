<?php
/**
 * Fix Stock Status for All Products - Enable Add to Cart
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h2>🔧 Fixing Stock Status for All Products</h2>\n";

// Get all products
$products = wc_get_products(array(
    'limit' => -1,
    'status' => 'publish'
));

$fixed_count = 0;
$total_count = count($products);

foreach ($products as $product) {
    $product_id = $product->get_id();
    
    // Set stock status to 'instock'
    $product->set_stock_status('instock');
    
    // Set manage stock to false (unlimited stock)
    $product->set_manage_stock(false);
    
    // Set stock quantity to null (unlimited)
    $product->set_stock_quantity(null);
    
    // Save the product
    $product->save();
    
    $fixed_count++;
    
    if ($fixed_count % 50 == 0) {
        echo "✅ Fixed $fixed_count / $total_count products...\n";
    }
}

echo "<h3>📊 Summary:</h3>\n";
echo "Total products: $total_count\n";
echo "Products fixed: $fixed_count\n";
echo "✅ <strong>All products now have 'instock' status and can be added to cart!</strong>\n";

?>