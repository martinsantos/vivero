<?php
/**
 * 🧪 Comprehensive Site and Duplicate Solution Test
 * Tests the entire site functionality and verifies duplicate image solution
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🧪 Comprehensive Site & Duplicate Solution Test</h1>\n";
echo "<p><em>Testing all functionality and verifying duplicate image fixes...</em></p>\n";

// Test Results Storage
$test_results = [];
$all_tests_passed = true;

function test_result($test_name, $status, $details = '', $critical = false) {
    global $test_results, $all_tests_passed;
    $test_results[] = [
        'name' => $test_name,
        'status' => $status,
        'details' => $details,
        'critical' => $critical
    ];
    
    if ($critical && $status !== 'PASS') {
        $all_tests_passed = false;
    }
    
    $icon = $status === 'PASS' ? '✅' : ($status === 'FAIL' ? '❌' : '⚠️');
    $critical_indicator = $critical ? ' [CRITICAL]' : '';
    echo "<p>{$icon} <strong>{$test_name}</strong>{$critical_indicator}: {$status}</p>\n";
    if ($details) {
        echo "<p style='margin-left: 20px; color: #666;'>{$details}</p>\n";
    }
}

// ===== TEST 1: DUPLICATE IMAGE DETECTION =====
echo "<h2>🔍 Test 1: Duplicate Image Analysis</h2>\n";

$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$image_urls = [];
$duplicates = [];
$svg_count = 0;
$real_images_count = 0;
$total_products = count($products);

foreach ($products as $product) {
    $product_id = $product->get_id();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        $file_extension = strtolower(pathinfo($image_url, PATHINFO_EXTENSION));
        
        if (strpos($image_url, 'placeholder.svg') !== false || $file_extension === 'svg') {
            $svg_count++;
        } else {
            $real_images_count++;
            // Check for duplicates in real images
            if (isset($image_urls[$image_url])) {
                if (!isset($duplicates[$image_url])) {
                    $duplicates[$image_url] = [$image_urls[$image_url]];
                }
                $duplicates[$image_url][] = $product_id;
            } else {
                $image_urls[$image_url] = $product_id;
            }
        }
    }
}

$duplicate_groups = count($duplicates);
$products_with_duplicates = 0;
foreach ($duplicates as $products_list) {
    $products_with_duplicates += count($products_list);
}

test_result('Image Coverage', $real_images_count > 0 ? 'PASS' : 'FAIL', 
    "Real images: {$real_images_count}/{$total_products}, SVG: {$svg_count}", true);
test_result('Duplicate Detection', $duplicate_groups === 0 ? 'PASS' : 'FAIL', 
    "Found {$duplicate_groups} duplicate groups affecting {$products_with_duplicates} products", true);

if ($duplicate_groups > 0) {
    echo "<h3>📋 Duplicate Details:</h3>\n";
    foreach ($duplicates as $url => $product_ids) {
        $filename = basename($url);
        echo "<p><strong>📄 {$filename}:</strong> Products " . implode(', ', $product_ids) . "</p>\n";
    }
}

// ===== TEST 2: SITE ACCESSIBILITY =====
echo "<h2>🌐 Test 2: Site Accessibility</h2>\n";

$site_url = get_site_url();
$shop_url = $site_url . '/tienda/';
$cart_url = $site_url . '/carro/';
$checkout_url = $site_url . '/finalizar-compra/';

// Test main pages
$pages_to_test = [
    'Home Page' => $site_url,
    'Shop Page' => $shop_url,
    'Cart Page' => $cart_url,
    'Checkout Page' => $checkout_url
];

foreach ($pages_to_test as $page_name => $url) {
    $response = @get_headers($url);
    $accessible = $response && strpos($response[0], '200') !== false;
    test_result($page_name . ' Accessibility', $accessible ? 'PASS' : 'FAIL', $url, true);
}

// ===== TEST 3: WOOCOMMERCE CONFIGURATION =====
echo "<h2>⚙️ Test 3: WooCommerce Configuration</h2>\n";

$coming_soon = get_option('woocommerce_coming_soon', 'yes');
$store_notice = get_option('woocommerce_demo_store', 'no');
$currency = get_woocommerce_currency();

test_result('Coming Soon Mode', $coming_soon === 'no' ? 'PASS' : 'FAIL', 
    "Status: " . ($coming_soon === 'no' ? 'Disabled (Store visible)' : 'Enabled (Store hidden)'), true);
test_result('Store Notice', $store_notice === 'no' ? 'PASS' : 'WARN', 
    "Demo notice: " . ($store_notice === 'no' ? 'Disabled' : 'Enabled'));
test_result('Currency Configuration', !empty($currency) ? 'PASS' : 'FAIL', 
    "Currency: {$currency}", true);

// ===== TEST 4: PRODUCT FUNCTIONALITY =====
echo "<h2>📦 Test 4: Product Functionality</h2>\n";

$sample_products = array_slice($products, 0, 10);
$addable_to_cart = 0;
$properly_priced = 0;

foreach ($sample_products as $product) {
    if ($product->is_purchasable() && $product->is_in_stock()) {
        $addable_to_cart++;
    }
    
    $price = $product->get_price();
    if (!empty($price) && is_numeric($price)) {
        $properly_priced++;
    }
}

$total_sample = count($sample_products);
test_result('Products Addable to Cart', $addable_to_cart > 0 ? 'PASS' : 'FAIL', 
    "{$addable_to_cart}/{$total_sample} products can be added to cart", true);
test_result('Product Pricing', $properly_priced > 0 ? 'PASS' : 'WARN', 
    "{$properly_priced}/{$total_sample} products have valid prices");

// ===== TEST 5: CART FUNCTIONALITY =====
echo "<h2>🛒 Test 5: Cart Functionality</h2>\n";

// Clear any existing cart
WC()->cart->empty_cart();

// Test adding a product to cart
$test_product = null;
foreach ($products as $product) {
    if ($product->is_purchasable() && $product->is_in_stock()) {
        $test_product = $product;
        break;
    }
}

$cart_add_success = false;
if ($test_product) {
    $cart_add_result = WC()->cart->add_to_cart($test_product->get_id(), 1);
    $cart_add_success = $cart_add_result !== false;
    $cart_count = WC()->cart->get_cart_contents_count();
    
    test_result('Add to Cart Function', $cart_add_success ? 'PASS' : 'FAIL', 
        "Added product ID {$test_product->get_id()}, Cart count: {$cart_count}", true);
    
    // Test cart totals
    $cart_total = WC()->cart->get_total();
    $cart_subtotal = WC()->cart->get_subtotal();
    
    test_result('Cart Calculations', !empty($cart_total) ? 'PASS' : 'FAIL', 
        "Total: {$cart_total}, Subtotal: {$cart_subtotal}", true);
    
    // Clean up
    WC()->cart->empty_cart();
} else {
    test_result('Add to Cart Function', 'FAIL', 'No purchasable products found', true);
}

// ===== TEST 6: IMAGE QUALITY ANALYSIS =====
echo "<h2>📸 Test 6: Image Quality Analysis</h2>\n";

$image_quality_stats = [
    'high_res' => 0,
    'medium_res' => 0,
    'low_res' => 0,
    'format_jpeg' => 0,
    'format_png' => 0,
    'format_webp' => 0,
    'format_other' => 0
];

$sample_image_products = array_slice($products, 0, 20);
foreach ($sample_image_products as $product) {
    $featured_image_id = $product->get_image_id();
    if ($featured_image_id) {
        $image_url = wp_get_attachment_url($featured_image_id);
        $file_extension = strtolower(pathinfo($image_url, PATHINFO_EXTENSION));
        
        if ($file_extension !== 'svg' && strpos($image_url, 'placeholder') === false) {
            $metadata = wp_get_attachment_metadata($featured_image_id);
            $width = $metadata['width'] ?? 0;
            $height = $metadata['height'] ?? 0;
            
            if ($width >= 1000 && $height >= 1000) {
                $image_quality_stats['high_res']++;
            } elseif ($width >= 500 && $height >= 500) {
                $image_quality_stats['medium_res']++;
            } else {
                $image_quality_stats['low_res']++;
            }
            
            switch ($file_extension) {
                case 'jpg':
                case 'jpeg':
                    $image_quality_stats['format_jpeg']++;
                    break;
                case 'png':
                    $image_quality_stats['format_png']++;
                    break;
                case 'webp':
                    $image_quality_stats['format_webp']++;
                    break;
                default:
                    $image_quality_stats['format_other']++;
            }
        }
    }
}

$total_analyzed = $image_quality_stats['high_res'] + $image_quality_stats['medium_res'] + $image_quality_stats['low_res'];
test_result('High Resolution Images', $image_quality_stats['high_res'] > 0 ? 'PASS' : 'WARN', 
    "High-res: {$image_quality_stats['high_res']}, Medium-res: {$image_quality_stats['medium_res']}, Low-res: {$image_quality_stats['low_res']}");
test_result('Image Format Distribution', $image_quality_stats['format_jpeg'] > 0 ? 'PASS' : 'WARN', 
    "JPEG: {$image_quality_stats['format_jpeg']}, PNG: {$image_quality_stats['format_png']}, WebP: {$image_quality_stats['format_webp']}");

// ===== TEST 7: PERFORMANCE METRICS =====
echo "<h2>⚡ Test 7: Performance Metrics</h2>\n";

$upload_dir = wp_upload_dir();
$upload_path = $upload_dir['basedir'];
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

$avg_file_size = $image_count > 0 ? $total_size / $image_count : 0;
test_result('Media Library Size', $image_count > 0 ? 'PASS' : 'WARN', 
    "Files: {$image_count}, Total size: " . size_format($total_size) . ", Avg: " . size_format($avg_file_size));

// ===== TEST 8: THEME AND FUNCTIONALITY =====
echo "<h2>🎨 Test 8: Theme and Core Functionality</h2>\n";

$current_theme = wp_get_theme();
$active_plugins = get_option('active_plugins', []);
$woocommerce_active = in_array('woocommerce/woocommerce.php', $active_plugins);

test_result('Active Theme', !empty($current_theme->get('Name')) ? 'PASS' : 'FAIL', 
    "{$current_theme->get('Name')} v{$current_theme->get('Version')}", true);
test_result('WooCommerce Plugin', $woocommerce_active ? 'PASS' : 'FAIL', 
    'WooCommerce ' . ($woocommerce_active ? 'active' : 'inactive'), true);

// ===== FINAL SUMMARY =====
echo "<h2>📊 Final Test Summary</h2>\n";

$total_tests = count($test_results);
$passed_tests = count(array_filter($test_results, function($test) { return $test['status'] === 'PASS'; }));
$failed_tests = count(array_filter($test_results, function($test) { return $test['status'] === 'FAIL'; }));
$warning_tests = count(array_filter($test_results, function($test) { return $test['status'] === 'WARN'; }));

$pass_rate = round(($passed_tests / $total_tests) * 100, 1);

echo "<div style='background: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; border-radius: 8px; margin: 20px 0;'>\n";
echo "<h3>📈 Test Statistics</h3>\n";
echo "<p><strong>Total Tests:</strong> {$total_tests}</p>\n";
echo "<p><strong>✅ Passed:</strong> {$passed_tests}</p>\n";
echo "<p><strong>❌ Failed:</strong> {$failed_tests}</p>\n";
echo "<p><strong>⚠️ Warnings:</strong> {$warning_tests}</p>\n";
echo "<p><strong>🎯 Pass Rate:</strong> {$pass_rate}%</p>\n";
echo "</div>\n";

// Overall Assessment
if ($duplicate_groups === 0 && $all_tests_passed && $pass_rate >= 80) {
    echo "<div style='background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 8px; margin: 20px 0;'>\n";
    echo "<h3>🎉 EXCELLENT! ALL SYSTEMS OPERATIONAL</h3>\n";
    echo "<p><strong>✅ No duplicate images detected</strong></p>\n";
    echo "<p><strong>✅ All critical tests passed</strong></p>\n";
    echo "<p><strong>✅ Site is production ready</strong></p>\n";
    echo "<p><strong>🌟 Pass rate: {$pass_rate}%</strong></p>\n";
    echo "</div>\n";
} elseif ($duplicate_groups === 0 && $pass_rate >= 70) {
    echo "<div style='background: #fff3cd; padding: 20px; border: 1px solid #ffeaa7; border-radius: 8px; margin: 20px 0;'>\n";
    echo "<h3>✅ GOOD! Duplicate issue resolved, minor improvements needed</h3>\n";
    echo "<p><strong>✅ No duplicate images found</strong></p>\n";
    echo "<p><strong>⚠️ Some non-critical issues detected</strong></p>\n";
    echo "<p><strong>📊 Pass rate: {$pass_rate}%</strong></p>\n";
    echo "</div>\n";
} else {
    echo "<div style='background: #f8d7da; padding: 20px; border: 1px solid #f5c6cb; border-radius: 8px; margin: 20px 0;'>\n";
    echo "<h3>❌ ISSUES DETECTED - Action Required</h3>\n";
    if ($duplicate_groups > 0) {
        echo "<p><strong>🚫 {$duplicate_groups} duplicate image groups still exist</strong></p>\n";
    }
    if (!$all_tests_passed) {
        echo "<p><strong>❌ Critical tests failed</strong></p>\n";
    }
    echo "<p><strong>📊 Pass rate: {$pass_rate}%</strong></p>\n";
    echo "</div>\n";
}

// Detailed Results Table
echo "<h3>📋 Detailed Test Results</h3>\n";
echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>\n";
echo "<tr style='background: #f8f9fa;'><th>Test Name</th><th>Status</th><th>Details</th><th>Critical</th></tr>\n";

foreach ($test_results as $test) {
    $row_color = $test['status'] === 'PASS' ? '#d4edda' : ($test['status'] === 'FAIL' ? '#f8d7da' : '#fff3cd');
    $critical_text = $test['critical'] ? 'Yes' : 'No';
    echo "<tr style='background: {$row_color};'>";
    echo "<td><strong>{$test['name']}</strong></td>";
    echo "<td>{$test['status']}</td>";
    echo "<td>{$test['details']}</td>";
    echo "<td>{$critical_text}</td>";
    echo "</tr>\n";
}
echo "</table>\n";

// Quick Links for Manual Verification
echo "<h3>🔗 Manual Verification Links</h3>\n";
echo "<ul>\n";
echo "<li><a href='{$site_url}' target='_blank'>🏠 Home Page</a></li>\n";
echo "<li><a href='{$shop_url}' target='_blank'>🛒 Shop Page</a></li>\n";
echo "<li><a href='{$cart_url}' target='_blank'>🛍️ Cart Page</a></li>\n";
echo "<li><a href='{$checkout_url}' target='_blank'>💳 Checkout Page</a></li>\n";
echo "<li><a href='{$site_url}/wp-admin/' target='_blank'>⚙️ Admin Dashboard</a></li>\n";
echo "</ul>\n";

echo "<p><em>Comprehensive testing completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>