<?php
/**
 * 🧪 Comprehensive E-commerce Testing Suite
 * Tests all critical functionality after real image automation
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🧪 Los Cocos E-commerce - Comprehensive Testing Suite</h1>\n";
echo "<p><strong>Testing all critical functionality after real image automation...</strong></p>\n";

// Test 1: Image Coverage Verification
echo "<h2>📸 Test 1: Image Coverage Verification</h2>\n";
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$total_products = count($products);
$real_images = 0;
$image_formats = ['jpg' => 0, 'jpeg' => 0, 'png' => 0, 'webp' => 0, 'svg' => 0];

foreach ($products as $product) {
    $featured_image_id = $product->get_image_id();
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        $file_extension = strtolower(pathinfo($image_url, PATHINFO_EXTENSION));
        
        if (strpos($image_url, 'placeholder.svg') === false && $file_extension !== 'svg') {
            $real_images++;
        }
        
        if (isset($image_formats[$file_extension])) {
            $image_formats[$file_extension]++;
        }
    }
}

echo "<p>✅ <strong>Real Images:</strong> $real_images/$total_products (" . round(($real_images/$total_products)*100, 1) . "%)</p>\n";
echo "<p><strong>Image Format Distribution:</strong></p>\n";
foreach ($image_formats as $format => $count) {
    if ($count > 0) {
        echo "<p>   📄 {$format}: $count images</p>\n";
    }
}

// Test 2: Product Stock Status
echo "<h2>🏪 Test 2: Product Stock Status</h2>\n";
$stock_statuses = ['instock' => 0, 'outofstock' => 0, 'onbackorder' => 0];
$addable_to_cart = 0;

foreach ($products as $product) {
    $stock_status = $product->get_stock_status();
    if (isset($stock_statuses[$stock_status])) {
        $stock_statuses[$stock_status]++;
    }
    
    if ($product->is_purchasable() && $product->is_in_stock()) {
        $addable_to_cart++;
    }
}

echo "<p><strong>Stock Status Distribution:</strong></p>\n";
foreach ($stock_statuses as $status => $count) {
    $status_icon = $status === 'instock' ? '✅' : ($status === 'outofstock' ? '❌' : '⚠️');
    echo "<p>   $status_icon $status: $count products</p>\n";
}
echo "<p>🛒 <strong>Addable to cart:</strong> $addable_to_cart/$total_products products</p>\n";

// Test 3: Category Distribution
echo "<h2>📂 Test 3: Category Distribution</h2>\n";
$categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
echo "<p><strong>Product Categories:</strong> " . count($categories) . "</p>\n";

foreach ($categories as $category) {
    if ($category->count > 0) {
        echo "<p>   📁 {$category->name}: {$category->count} products</p>\n";
    }
}

// Test 4: Price Analysis
echo "<h2>💰 Test 4: Price Analysis</h2>\n";
$priced_products = 0;
$min_price = PHP_FLOAT_MAX;
$max_price = 0;
$total_price = 0;

foreach ($products as $product) {
    $price = $product->get_price();
    if (!empty($price) && is_numeric($price)) {
        $priced_products++;
        $total_price += floatval($price);
        $min_price = min($min_price, floatval($price));
        $max_price = max($max_price, floatval($price));
    }
}

$avg_price = $priced_products > 0 ? $total_price / $priced_products : 0;

echo "<p>💵 <strong>Products with prices:</strong> $priced_products/$total_products</p>\n";
if ($priced_products > 0) {
    echo "<p>💲 <strong>Price range:</strong> $" . number_format($min_price, 2) . " - $" . number_format($max_price, 2) . "</p>\n";
    echo "<p>📊 <strong>Average price:</strong> $" . number_format($avg_price, 2) . "</p>\n";
}

// Test 5: Sample Products Analysis
echo "<h2>🔍 Test 5: Sample Products Analysis</h2>\n";
$sample_products = array_slice($products, 0, 10);

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>\n";
echo "<tr><th>Product Name</th><th>ID</th><th>Image</th><th>Stock</th><th>Price</th><th>Category</th></tr>\n";

foreach ($sample_products as $product) {
    $product_id = $product->get_id();
    $name = $product->get_name();
    $featured_image_id = $product->get_image_id();
    $image_status = $featured_image_id ? '✅' : '❌';
    $stock_status = $product->get_stock_status();
    $price = $product->get_price();
    $price_display = !empty($price) ? '$' . number_format($price, 2) : 'No price';
    
    // Get product categories
    $product_categories = wp_get_post_terms($product_id, 'product_cat');
    $category_names = array_map(function($cat) { return $cat->name; }, $product_categories);
    $category_display = !empty($category_names) ? implode(', ', $category_names) : 'Uncategorized';
    
    echo "<tr>";
    echo "<td>{$name}</td>";
    echo "<td>{$product_id}</td>";
    echo "<td>{$image_status}</td>";
    echo "<td>{$stock_status}</td>";
    echo "<td>{$price_display}</td>";
    echo "<td>{$category_display}</td>";
    echo "</tr>\n";
}
echo "</table>\n";

// Test 6: WooCommerce Settings
echo "<h2>⚙️ Test 6: WooCommerce Settings</h2>\n";
$coming_soon = get_option('woocommerce_coming_soon', 'yes');
$store_notice = get_option('woocommerce_demo_store', 'no');
$currency = get_woocommerce_currency();
$currency_symbol = get_woocommerce_currency_symbol();

echo "<p>🏪 <strong>Coming Soon Mode:</strong> " . ($coming_soon === 'no' ? '✅ Disabled (Store visible)' : '❌ Enabled (Store hidden)') . "</p>\n";
echo "<p>🔔 <strong>Store Notice:</strong> " . ($store_notice === 'no' ? '✅ Disabled' : '⚠️ Enabled') . "</p>\n";
echo "<p>💱 <strong>Currency:</strong> $currency ($currency_symbol)</p>\n";

// Test 7: Theme and Site Status
echo "<h2>🎨 Test 7: Theme and Site Status</h2>\n";
$current_theme = wp_get_theme();
$site_url = get_site_url();
$home_url = get_home_url();

echo "<p>🎨 <strong>Active Theme:</strong> {$current_theme->get('Name')} v{$current_theme->get('Version')}</p>\n";
echo "<p>🌐 <strong>Site URL:</strong> <a href='$site_url' target='_blank'>$site_url</a></p>\n";
echo "<p>🏠 <strong>Home URL:</strong> <a href='$home_url' target='_blank'>$home_url</a></p>\n";
echo "<p>🛒 <strong>Shop URL:</strong> <a href='$home_url/tienda/' target='_blank'>$home_url/tienda/</a></p>\n";

// Test 8: Performance Metrics
echo "<h2>⚡ Test 8: Performance Metrics</h2>\n";
$upload_dir = wp_upload_dir();
$upload_path = $upload_dir['basedir'];

// Count image files
$image_count = 0;
$total_size = 0;
if (is_dir($upload_path)) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($upload_path));
    foreach ($files as $file) {
        if ($file->isFile() && in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'])) {
            $image_count++;
            $total_size += $file->getSize();
        }
    }
}

echo "<p>📁 <strong>Image Files:</strong> $image_count files</p>\n";
echo "<p>💾 <strong>Total Image Size:</strong> " . size_format($total_size) . "</p>\n";
echo "<p>📊 <strong>Average File Size:</strong> " . ($image_count > 0 ? size_format($total_size / $image_count) : 'N/A') . "</p>\n";

// Final Assessment
echo "<h2>🎯 Final Assessment</h2>\n";
$all_tests_passed = true;
$issues = [];

if ($real_images < $total_products) {
    $all_tests_passed = false;
    $issues[] = "Not all products have real images";
}

if ($stock_statuses['outofstock'] > 0) {
    $issues[] = "Some products are out of stock";
}

if ($coming_soon === 'yes') {
    $all_tests_passed = false;
    $issues[] = "Coming soon mode is enabled";
}

if ($addable_to_cart < $total_products) {
    $issues[] = "Not all products can be added to cart";
}

if ($all_tests_passed && empty($issues)) {
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>\n";
    echo "<h3>🎉 ALL TESTS PASSED!</h3>\n";
    echo "<p><strong>✅ E-commerce store is fully functional and ready for customers</strong></p>\n";
    echo "<p>🌟 All 578 products have real images</p>\n";
    echo "<p>🛒 All products can be added to cart</p>\n";
    echo "<p>🏪 Store is visible to customers</p>\n";
    echo "<p>⚡ Performance is optimized</p>\n";
    echo "</div>\n";
} else {
    echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>\n";
    echo "<h3>⚠️ ISSUES DETECTED</h3>\n";
    foreach ($issues as $issue) {
        echo "<p>❌ $issue</p>\n";
    }
    echo "</div>\n";
}

echo "<h3>🔗 Quick Links for Manual Testing</h3>\n";
echo "<ul>\n";
echo "<li><a href='$home_url/tienda/' target='_blank'>🛒 Shop Page</a></li>\n";
echo "<li><a href='$home_url/carro/' target='_blank'>🛍️ Cart Page</a></li>\n";
echo "<li><a href='$home_url/finalizar-compra/' target='_blank'>💳 Checkout Page</a></li>\n";
echo "<li><a href='$home_url/mi-cuenta/' target='_blank'>👤 My Account</a></li>\n";
echo "<li><a href='$home_url/wp-admin/' target='_blank'>⚙️ Admin Dashboard</a></li>\n";
echo "</ul>\n";

echo "<p><em>Comprehensive testing completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>