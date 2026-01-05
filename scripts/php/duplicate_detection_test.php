<?php
/**
 * Duplicate Cart and Functionality Detection Test
 * Verifies no duplicate cart instances or conflicting functionalities exist
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🔍 Duplicate Cart & Functionality Detection Test</h1>\n";

// Test 1: Check for multiple cart instances
echo "<h2>🛒 Test 1: Cart Instance Check</h2>\n";
$cart_instance = WC()->cart;
$cart_contents = $cart_instance->get_cart();
$cart_count = count($cart_contents);

echo "Cart instance exists: " . (isset($cart_instance) ? "✅ Yes" : "❌ No") . "\n";
echo "Cart contents count: $cart_count\n";

// Clear cart and test uniqueness
WC()->cart->empty_cart();
$cleared_count = WC()->cart->get_cart_contents_count();
echo "Cart cleared successfully: " . ($cleared_count === 0 ? "✅ Yes" : "❌ No") . "\n";

// Test 2: Check for duplicate pages
echo "<h2>📄 Test 2: Duplicate Page Detection</h2>\n";
$important_pages = array(
    'shop' => 'Tienda',
    'cart' => 'Carrito', 
    'checkout' => 'Checkout',
    'my-account' => 'Mi cuenta'
);

$duplicates_found = false;
foreach ($important_pages as $page_type => $page_name) {
    $pages = get_posts(array(
        'post_type' => 'page',
        'meta_query' => array(
            array(
                'key' => '_wp_page_template',
                'value' => 'page-' . $page_type . '.php',
                'compare' => 'LIKE'
            )
        ),
        'posts_per_page' => -1
    ));
    
    // Also check by title
    $pages_by_title = get_posts(array(
        'post_type' => 'page',
        'title' => $page_name,
        'posts_per_page' => -1
    ));
    
    $total_pages = count($pages) + count($pages_by_title);
    if ($total_pages > 1) {
        echo "❌ Multiple $page_name pages found: $total_pages\n";
        $duplicates_found = true;
    } else {
        echo "✅ Single $page_name page: OK\n";
    }
}

if (!$duplicates_found) {
    echo "✅ <strong>No duplicate pages detected</strong>\n";
}

// Test 3: Check WooCommerce page assignments
echo "<h2>⚙️ Test 3: WooCommerce Page Assignments</h2>\n";
$wc_pages = array(
    'woocommerce_shop_page_id' => 'Shop Page',
    'woocommerce_cart_page_id' => 'Cart Page',
    'woocommerce_checkout_page_id' => 'Checkout Page',
    'woocommerce_myaccount_page_id' => 'My Account Page'
);

$conflicts = 0;
foreach ($wc_pages as $option_key => $page_name) {
    $page_id = get_option($option_key);
    if ($page_id) {
        $page_title = get_the_title($page_id);
        $page_status = get_post_status($page_id);
        echo "✅ $page_name: ID $page_id ('$page_title') - Status: $page_status\n";
        
        if ($page_status !== 'publish') {
            echo "⚠️ Warning: $page_name is not published\n";
            $conflicts++;
        }
    } else {
        echo "❌ $page_name: Not assigned\n";
        $conflicts++;
    }
}

echo $conflicts === 0 ? "✅ <strong>All WooCommerce pages properly assigned</strong>\n" : "❌ <strong>$conflicts WooCommerce page conflicts found</strong>\n";

// Test 4: Check for multiple cart widgets/shortcodes
echo "<h2>🔧 Test 4: Cart Widget/Shortcode Duplication</h2>\n";

// Check active widgets
$sidebars_widgets = wp_get_sidebars_widgets();
$cart_widgets = 0;
foreach ($sidebars_widgets as $sidebar => $widgets) {
    if (is_array($widgets)) {
        foreach ($widgets as $widget) {
            if (strpos($widget, 'woocommerce_widget_cart') !== false) {
                $cart_widgets++;
            }
        }
    }
}

echo "Cart widgets found: $cart_widgets\n";
echo $cart_widgets <= 1 ? "✅ <strong>No duplicate cart widgets</strong>\n" : "⚠️ <strong>Multiple cart widgets detected</strong>\n";

// Test 5: Check for conflicting plugins
echo "<h2>🔌 Test 5: Plugin Conflict Detection</h2>\n";
$active_plugins = get_option('active_plugins');
$woocommerce_related = array();
$cart_related = array();

foreach ($active_plugins as $plugin) {
    if (strpos(strtolower($plugin), 'woocommerce') !== false) {
        $woocommerce_related[] = $plugin;
    }
    if (strpos(strtolower($plugin), 'cart') !== false) {
        $cart_related[] = $plugin;
    }
}

echo "WooCommerce-related plugins: " . count($woocommerce_related) . "\n";
foreach ($woocommerce_related as $plugin) {
    echo "  - $plugin\n";
}

echo "Cart-related plugins: " . count($cart_related) . "\n";
foreach ($cart_related as $plugin) {
    echo "  - $plugin\n";
}

// Test 6: Test cart functionality uniqueness
echo "<h2>🧪 Test 6: Cart Functionality Uniqueness</h2>\n";
WC()->cart->empty_cart();

// Add product to cart
$products = wc_get_products(array('limit' => 3, 'status' => 'publish'));
$test_results = array();

foreach ($products as $index => $product) {
    $product_id = $product->get_id();
    $added = WC()->cart->add_to_cart($product_id, 1);
    
    if ($added) {
        $cart_count = WC()->cart->get_cart_contents_count();
        $test_results[] = array(
            'product_id' => $product_id,
            'expected_count' => $index + 1,
            'actual_count' => $cart_count,
            'success' => ($cart_count === $index + 1)
        );
    }
}

$cart_tests_passed = 0;
foreach ($test_results as $result) {
    if ($result['success']) {
        echo "✅ Product {$result['product_id']}: Expected {$result['expected_count']}, Got {$result['actual_count']}\n";
        $cart_tests_passed++;
    } else {
        echo "❌ Product {$result['product_id']}: Expected {$result['expected_count']}, Got {$result['actual_count']}\n";
    }
}

$total_cart_tests = count($test_results);
echo $cart_tests_passed === $total_cart_tests ? "✅ <strong>Cart functionality is unique and working correctly</strong>\n" : "❌ <strong>Cart functionality issues detected</strong>\n";

// Clear cart after test
WC()->cart->empty_cart();

// Final Summary
echo "<h2>📋 DUPLICATE DETECTION SUMMARY</h2>\n";
echo "=================================\n";

$total_issues = 0;
if ($duplicates_found) $total_issues++;
if ($conflicts > 0) $total_issues++;
if ($cart_widgets > 1) $total_issues++;
if ($cart_tests_passed !== $total_cart_tests) $total_issues++;

echo "Duplicate pages: " . ($duplicates_found ? "❌ Found" : "✅ None") . "\n";
echo "WooCommerce conflicts: " . ($conflicts > 0 ? "❌ $conflicts issues" : "✅ None") . "\n";
echo "Cart widget duplicates: " . ($cart_widgets > 1 ? "❌ $cart_widgets widgets" : "✅ None") . "\n";
echo "Cart functionality: " . ($cart_tests_passed === $total_cart_tests ? "✅ Unique" : "❌ Issues") . "\n";

echo "\nTotal issues found: $total_issues\n";

if ($total_issues === 0) {
    echo "🎉 <strong>NO DUPLICATES OR CONFLICTS FOUND - SYSTEM IS CLEAN</strong>\n";
} else {
    echo "⚠️ <strong>$total_issues ISSUES DETECTED - REVIEW RECOMMENDED</strong>\n";
}

echo "=================================\n";

?>