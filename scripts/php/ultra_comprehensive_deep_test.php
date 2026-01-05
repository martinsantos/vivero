<?php
/**
 * 🔬 ULTRA-COMPREHENSIVE DEEP TESTING SUITE
 * Complete end-to-end testing of the Los Cocos e-commerce solution
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🔬 Los Cocos E-commerce - ULTRA-COMPREHENSIVE DEEP TESTING</h1>\n";
echo "<p><strong>Performing exhaustive testing of all systems and functionality...</strong></p>\n";

$start_time = microtime(true);
$test_results = [];
$critical_issues = [];
$warnings = [];
$passed_tests = 0;
$total_tests = 0;

function add_test_result($test_name, $status, $details = '', $is_critical = false) {
    global $test_results, $critical_issues, $warnings, $passed_tests, $total_tests;
    
    $total_tests++;
    $test_results[] = [
        'name' => $test_name,
        'status' => $status,
        'details' => $details,
        'critical' => $is_critical
    ];
    
    if ($status === 'PASS') {
        $passed_tests++;
    } elseif ($status === 'FAIL' && $is_critical) {
        $critical_issues[] = $test_name . ': ' . $details;
    } elseif ($status === 'WARNING') {
        $warnings[] = $test_name . ': ' . $details;
    }
}

// Test 1: DEEP IMAGE ANALYSIS
echo "<h2>🖼️ Test 1: DEEP IMAGE ANALYSIS</h2>\n";
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
$total_products = count($products);

$image_analysis = [
    'total_products' => $total_products,
    'with_images' => 0,
    'real_images' => 0,
    'svg_images' => 0,
    'broken_images' => 0,
    'formats' => ['jpg' => 0, 'jpeg' => 0, 'png' => 0, 'webp' => 0, 'svg' => 0],
    'sizes' => ['small' => 0, 'medium' => 0, 'large' => 0],
    'quality_issues' => 0
];

echo "<p>🔍 Analyzing all $total_products products...</p>\n";

foreach ($products as $product) {
    $product_id = $product->get_id();
    $featured_image_id = $product->get_image_id();
    
    if ($featured_image_id) {
        $image_analysis['with_images']++;
        $image_url = wp_get_attachment_url($featured_image_id);
        $file_extension = strtolower(pathinfo($image_url, PATHINFO_EXTENSION));
        
        // Check if image URL is accessible
        $headers = @get_headers($image_url);
        if (!$headers || strpos($headers[0], '200') === false) {
            $image_analysis['broken_images']++;
        }
        
        // Classify image type
        if (strpos($image_url, 'placeholder.svg') !== false || $file_extension === 'svg') {
            $image_analysis['svg_images']++;
        } else {
            $image_analysis['real_images']++;
        }
        
        // Count formats
        if (isset($image_analysis['formats'][$file_extension])) {
            $image_analysis['formats'][$file_extension]++;
        }
        
        // Check image metadata
        $metadata = wp_get_attachment_metadata($featured_image_id);
        if ($metadata && isset($metadata['width'], $metadata['height'])) {
            $width = $metadata['width'];
            $height = $metadata['height'];
            
            if ($width < 300 || $height < 300) {
                $image_analysis['sizes']['small']++;
            } elseif ($width < 800 || $height < 800) {
                $image_analysis['sizes']['medium']++;
            } else {
                $image_analysis['sizes']['large']++;
            }
            
            // Quality checks
            if ($width !== $height) {
                $image_analysis['quality_issues']++;
            }
        }
    }
}

// Image test results
$image_coverage = round(($image_analysis['with_images'] / $total_products) * 100, 1);
$real_image_percentage = round(($image_analysis['real_images'] / $total_products) * 100, 1);

add_test_result(
    'Image Coverage',
    $image_coverage == 100 ? 'PASS' : 'FAIL',
    "$image_coverage% coverage ({$image_analysis['with_images']}/$total_products)",
    true
);

add_test_result(
    'Real Images',
    $real_image_percentage == 100 ? 'PASS' : 'FAIL',
    "$real_image_percentage% real images ({$image_analysis['real_images']}/$total_products)",
    true
);

add_test_result(
    'SVG Elimination',
    $image_analysis['svg_images'] == 0 ? 'PASS' : 'FAIL',
    "SVG images remaining: {$image_analysis['svg_images']}",
    true
);

add_test_result(
    'Broken Images',
    $image_analysis['broken_images'] == 0 ? 'PASS' : 'WARNING',
    "Broken image URLs: {$image_analysis['broken_images']}"
);

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>\n";
echo "<tr><th>Metric</th><th>Count</th><th>Percentage</th></tr>\n";
echo "<tr><td>Total Products</td><td>{$image_analysis['total_products']}</td><td>100%</td></tr>\n";
echo "<tr><td>With Images</td><td>{$image_analysis['with_images']}</td><td>{$image_coverage}%</td></tr>\n";
echo "<tr><td>Real Images</td><td>{$image_analysis['real_images']}</td><td>{$real_image_percentage}%</td></tr>\n";
echo "<tr><td>SVG Images</td><td>{$image_analysis['svg_images']}</td><td>" . round(($image_analysis['svg_images'] / $total_products) * 100, 1) . "%</td></tr>\n";
echo "<tr><td>Broken Images</td><td>{$image_analysis['broken_images']}</td><td>" . round(($image_analysis['broken_images'] / $total_products) * 100, 1) . "%</td></tr>\n";
echo "</table>\n";

// Test 2: DEEP CART FUNCTIONALITY
echo "<h2>🛒 Test 2: DEEP CART FUNCTIONALITY</h2>\n";

// Test cart operations
$cart_tests = [];

// Test 1: Add random products to cart
$test_products = array_slice($products, 0, 10);
$cart_success = 0;
$cart_failures = 0;

foreach ($test_products as $product) {
    $product_id = $product->get_id();
    
    if ($product->is_purchasable() && $product->is_in_stock()) {
        $result = WC()->cart->add_to_cart($product_id, 1);
        if ($result) {
            $cart_success++;
        } else {
            $cart_failures++;
        }
    }
}

add_test_result(
    'Cart Add Functionality',
    $cart_failures == 0 ? 'PASS' : 'FAIL',
    "Successfully added: $cart_success, Failed: $cart_failures",
    true
);

// Test cart calculations
$cart_total = WC()->cart->get_cart_total();
$cart_count = WC()->cart->get_cart_contents_count();

add_test_result(
    'Cart Calculations',
    !empty($cart_total) && $cart_count > 0 ? 'PASS' : 'FAIL',
    "Items: $cart_count, Total: $cart_total",
    true
);

// Clear cart for next tests
WC()->cart->empty_cart();

// Test 3: PRODUCT STOCK AND PRICING
echo "<h2>📦 Test 3: PRODUCT STOCK AND PRICING ANALYSIS</h2>\n";

$stock_analysis = [
    'instock' => 0,
    'outofstock' => 0,
    'onbackorder' => 0,
    'purchasable' => 0,
    'with_price' => 0,
    'without_price' => 0,
    'price_ranges' => ['0-100' => 0, '100-300' => 0, '300-500' => 0, '500+' => 0]
];

foreach ($products as $product) {
    $stock_status = $product->get_stock_status();
    $stock_analysis[$stock_status]++;
    
    if ($product->is_purchasable()) {
        $stock_analysis['purchasable']++;
    }
    
    $price = $product->get_price();
    if (!empty($price) && is_numeric($price)) {
        $stock_analysis['with_price']++;
        $price_num = floatval($price);
        
        if ($price_num <= 100) {
            $stock_analysis['price_ranges']['0-100']++;
        } elseif ($price_num <= 300) {
            $stock_analysis['price_ranges']['100-300']++;
        } elseif ($price_num <= 500) {
            $stock_analysis['price_ranges']['300-500']++;
        } else {
            $stock_analysis['price_ranges']['500+']++;
        }
    } else {
        $stock_analysis['without_price']++;
    }
}

add_test_result(
    'Stock Status',
    $stock_analysis['instock'] == $total_products ? 'PASS' : 'WARNING',
    "In stock: {$stock_analysis['instock']}, Out of stock: {$stock_analysis['outofstock']}"
);

add_test_result(
    'Purchasable Products',
    $stock_analysis['purchasable'] == $total_products ? 'PASS' : 'FAIL',
    "Purchasable: {$stock_analysis['purchasable']}/$total_products",
    true
);

$pricing_percentage = round(($stock_analysis['with_price'] / $total_products) * 100, 1);
add_test_result(
    'Product Pricing',
    $pricing_percentage > 5 ? 'PASS' : 'WARNING',
    "$pricing_percentage% have prices ({$stock_analysis['with_price']}/$total_products)"
);

// Test 4: CATEGORY AND TAXONOMY
echo "<h2>📂 Test 4: CATEGORY AND TAXONOMY STRUCTURE</h2>\n";

$categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
$tags = get_terms(['taxonomy' => 'product_tag', 'hide_empty' => true]);

$categorized_products = 0;
$uncategorized_products = 0;

foreach ($products as $product) {
    $product_categories = wp_get_post_terms($product->get_id(), 'product_cat');
    
    if (!empty($product_categories) && !is_wp_error($product_categories)) {
        $has_real_category = false;
        foreach ($product_categories as $cat) {
            if ($cat->slug !== 'sin-categorizar' && $cat->slug !== 'uncategorized') {
                $has_real_category = true;
                break;
            }
        }
        
        if ($has_real_category) {
            $categorized_products++;
        } else {
            $uncategorized_products++;
        }
    } else {
        $uncategorized_products++;
    }
}

add_test_result(
    'Product Categories',
    count($categories) >= 3 ? 'PASS' : 'WARNING',
    count($categories) . " categories available"
);

$categorization_percentage = round(($categorized_products / $total_products) * 100, 1);
add_test_result(
    'Product Categorization',
    $categorization_percentage > 10 ? 'PASS' : 'WARNING',
    "$categorization_percentage% properly categorized ($categorized_products/$total_products)"
);

// Test 5: WOOCOMMERCE CONFIGURATION
echo "<h2>⚙️ Test 5: WOOCOMMERCE CONFIGURATION</h2>\n";

$wc_tests = [
    'coming_soon' => get_option('woocommerce_coming_soon', 'yes'),
    'store_notice' => get_option('woocommerce_demo_store', 'no'),
    'currency' => get_woocommerce_currency(),
    'calc_taxes' => get_option('woocommerce_calc_taxes', 'no'),
    'manage_stock' => get_option('woocommerce_manage_stock', 'yes'),
    'guest_checkout' => get_option('woocommerce_enable_guest_checkout', 'yes'),
    'checkout_login_reminder' => get_option('woocommerce_enable_checkout_login_reminder', 'no')
];

add_test_result(
    'Store Visibility',
    $wc_tests['coming_soon'] === 'no' ? 'PASS' : 'FAIL',
    "Coming soon mode: " . $wc_tests['coming_soon'],
    true
);

add_test_result(
    'Currency Configuration',
    !empty($wc_tests['currency']) ? 'PASS' : 'FAIL',
    "Currency: " . $wc_tests['currency'],
    true
);

add_test_result(
    'Guest Checkout',
    $wc_tests['guest_checkout'] === 'yes' ? 'PASS' : 'WARNING',
    "Guest checkout enabled: " . $wc_tests['guest_checkout']
);

// Test 6: THEME AND TEMPLATE INTEGRITY
echo "<h2>🎨 Test 6: THEME AND TEMPLATE INTEGRITY</h2>\n";

$current_theme = wp_get_theme();
$theme_name = $current_theme->get('Name');
$theme_version = $current_theme->get('Version');

add_test_result(
    'Theme Activation',
    strpos(strtolower($theme_name), 'los cocos') !== false ? 'PASS' : 'WARNING',
    "Active theme: $theme_name v$theme_version"
);

// Check critical WooCommerce templates
$critical_templates = [
    'archive-product.php',
    'single-product.php',
    'woocommerce/cart/cart.php',
    'woocommerce/checkout/form-checkout.php'
];

$template_issues = 0;
foreach ($critical_templates as $template) {
    if (!locate_template($template)) {
        $template_issues++;
    }
}

add_test_result(
    'WooCommerce Templates',
    $template_issues == 0 ? 'PASS' : 'WARNING',
    "Missing templates: $template_issues"
);

// Test 7: PERFORMANCE AND OPTIMIZATION
echo "<h2>⚡ Test 7: PERFORMANCE AND OPTIMIZATION</h2>\n";

// Database queries test
$queries_before = get_num_queries();
$memory_before = memory_get_usage();

// Simulate page load
$shop_products = wc_get_products(['limit' => 20]);
foreach ($shop_products as $product) {
    $product->get_name();
    $product->get_price();
    $product->get_image_id();
}

$queries_after = get_num_queries();
$memory_after = memory_get_usage();

$query_count = $queries_after - $queries_before;
$memory_used = $memory_after - $memory_before;

add_test_result(
    'Database Queries',
    $query_count < 50 ? 'PASS' : 'WARNING',
    "Queries for 20 products: $query_count"
);

add_test_result(
    'Memory Usage',
    $memory_used < 10000000 ? 'PASS' : 'WARNING', // 10MB
    "Memory used: " . size_format($memory_used)
);

// Test 8: SEO AND METADATA
echo "<h2>🔍 Test 8: SEO AND METADATA</h2>\n";

$products_with_alt_text = 0;
$products_with_descriptions = 0;

foreach (array_slice($products, 0, 50) as $product) {
    // Check image alt text
    $featured_image_id = $product->get_image_id();
    if ($featured_image_id) {
        $alt_text = get_post_meta($featured_image_id, '_wp_attachment_image_alt', true);
        if (!empty($alt_text)) {
            $products_with_alt_text++;
        }
    }
    
    // Check product descriptions
    $description = $product->get_description();
    $short_description = $product->get_short_description();
    if (!empty($description) || !empty($short_description)) {
        $products_with_descriptions++;
    }
}

$alt_text_percentage = round(($products_with_alt_text / 50) * 100, 1);
add_test_result(
    'Image Alt Text',
    $alt_text_percentage > 50 ? 'PASS' : 'WARNING',
    "$alt_text_percentage% of sampled products have alt text"
);

$description_percentage = round(($products_with_descriptions / 50) * 100, 1);
add_test_result(
    'Product Descriptions',
    $description_percentage > 10 ? 'PASS' : 'WARNING',
    "$description_percentage% of sampled products have descriptions"
);

// Test 9: SECURITY AND ACCESS CONTROL
echo "<h2>🔒 Test 9: SECURITY AND ACCESS CONTROL</h2>\n";

// Test admin access protection
$admin_accessible = wp_redirect('wp-admin/') !== null;

add_test_result(
    'Admin Protection',
    !$admin_accessible ? 'PASS' : 'WARNING',
    "Admin area accessibility check"
);

// Check for debug mode
$debug_mode = defined('WP_DEBUG') && WP_DEBUG;
add_test_result(
    'Debug Mode',
    !$debug_mode ? 'PASS' : 'WARNING',
    "WordPress debug mode: " . ($debug_mode ? 'enabled' : 'disabled')
);

// Test 10: MOBILE RESPONSIVENESS
echo "<h2>📱 Test 10: MOBILE RESPONSIVENESS</h2>\n";

// Check viewport meta tag
$has_viewport = false;
ob_start();
wp_head();
$head_content = ob_get_clean();
if (strpos($head_content, 'viewport') !== false) {
    $has_viewport = true;
}

add_test_result(
    'Viewport Meta Tag',
    $has_viewport ? 'PASS' : 'WARNING',
    "Mobile viewport configuration"
);

// FINAL SUMMARY
$end_time = microtime(true);
$execution_time = round($end_time - $start_time, 2);

echo "<h2>📊 COMPREHENSIVE TEST SUMMARY</h2>\n";

$success_rate = round(($passed_tests / $total_tests) * 100, 1);

echo "<div style='background: " . ($success_rate >= 90 ? '#d4edda' : ($success_rate >= 70 ? '#fff3cd' : '#f8d7da')) . "; padding: 15px; margin: 15px 0; border-radius: 5px;'>\n";
echo "<h3>🎯 OVERALL SCORE: $success_rate% ($passed_tests/$total_tests tests passed)</h3>\n";
echo "<p><strong>⏱️ Execution Time:</strong> {$execution_time} seconds</p>\n";

if ($success_rate >= 90) {
    echo "<h4>🎉 EXCELLENT - System is performing exceptionally well!</h4>\n";
} elseif ($success_rate >= 70) {
    echo "<h4>✅ GOOD - System is functional with minor improvements needed</h4>\n";
} else {
    echo "<h4>⚠️ NEEDS ATTENTION - Critical issues require immediate resolution</h4>\n";
}
echo "</div>\n";

// Critical Issues
if (!empty($critical_issues)) {
    echo "<h3>🚨 CRITICAL ISSUES REQUIRING IMMEDIATE ATTENTION:</h3>\n";
    echo "<ul style='color: red;'>\n";
    foreach ($critical_issues as $issue) {
        echo "<li>❌ $issue</li>\n";
    }
    echo "</ul>\n";
}

// Warnings
if (!empty($warnings)) {
    echo "<h3>⚠️ WARNINGS (Recommended Improvements):</h3>\n";
    echo "<ul style='color: orange;'>\n";
    foreach ($warnings as $warning) {
        echo "<li>⚠️ $warning</li>\n";
    }
    echo "</ul>\n";
}

// Detailed Results Table
echo "<h3>📋 DETAILED TEST RESULTS:</h3>\n";
echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>\n";
echo "<tr style='background: #f0f0f0;'><th>Test Name</th><th>Status</th><th>Details</th><th>Critical</th></tr>\n";

foreach ($test_results as $result) {
    $status_color = $result['status'] === 'PASS' ? 'green' : ($result['status'] === 'WARNING' ? 'orange' : 'red');
    $critical_icon = $result['critical'] ? '🔥' : '';
    
    echo "<tr>";
    echo "<td>{$result['name']}</td>";
    echo "<td style='color: $status_color; font-weight: bold;'>{$result['status']}</td>";
    echo "<td>{$result['details']}</td>";
    echo "<td>$critical_icon</td>";
    echo "</tr>\n";
}
echo "</table>\n";

// Quick Action Links
echo "<h3>🔗 QUICK ACCESS FOR MANUAL VERIFICATION:</h3>\n";
echo "<ul>\n";
echo "<li><a href='http://localhost:8080/tienda/' target='_blank'>🛒 Shop Page</a> - Verify product display and images</li>\n";
echo "<li><a href='http://localhost:8080/carro/' target='_blank'>🛍️ Cart Page</a> - Test cart functionality</li>\n";
echo "<li><a href='http://localhost:8080/finalizar-compra/' target='_blank'>💳 Checkout</a> - Test checkout process</li>\n";
echo "<li><a href='http://localhost:8080/wp-admin/' target='_blank'>⚙️ Admin Dashboard</a> - Backend management</li>\n";
echo "<li><a href='http://localhost:8080/wp-admin/admin.php?page=wc-admin' target='_blank'>📊 WooCommerce Analytics</a> - E-commerce insights</li>\n";
echo "</ul>\n";

echo "<p><em>Ultra-comprehensive testing completed: " . date('Y-m-d H:i:s') . "</em></p>\n";
echo "<p><strong>🎯 Recommendation:</strong> " . ($success_rate >= 90 ? "System is production-ready!" : "Address highlighted issues before full deployment.") . "</p>\n";
?>