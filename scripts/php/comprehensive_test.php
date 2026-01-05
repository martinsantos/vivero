<?php
/**
 * Comprehensive Testing Script for Los Cocos E-commerce
 * Tests all functionality: products, cart, checkout, theme, performance
 */

// WordPress loader
if (!defined('ABSPATH')) {
    require_once '/var/www/html/wp-load.php';
}

echo "<h1>🧪 Los Cocos E-commerce - Comprehensive Test Suite</h1>\n";
echo "<p>Testing all functionality following Docker-based testing requirements...</p>\n";

// Test 1: Product Images Verification
echo "<h2>🖼️ Test 1: Product Images Verification</h2>\n";
$products = wc_get_products(array('limit' => -1, 'status' => 'publish'));
$total_products = count($products);
$products_with_images = 0;
$products_without_images = 0;

foreach ($products as $product) {
    $featured_image_id = $product->get_image_id();
    if ($featured_image_id) {
        $products_with_images++;
    } else {
        $products_without_images++;
        echo "❌ Missing image: " . $product->get_name() . " (ID: " . $product->get_id() . ")\n";
    }
}

echo "📊 <strong>Results:</strong>\n";
echo "Total products: $total_products\n";
echo "Products with images: $products_with_images\n";
echo "Products without images: $products_without_images\n";
echo $products_without_images === 0 ? "✅ <strong>PASS: All products have images</strong>\n" : "❌ <strong>FAIL: Some products missing images</strong>\n";

// Test 2: Theme Verification
echo "<h2>🎨 Test 2: Theme Verification</h2>\n";
$current_theme = wp_get_theme();
$theme_name = $current_theme->get('Name');
$theme_status = $current_theme->exists() ? 'active' : 'missing';

echo "Current theme: $theme_name\n";
echo "Theme status: $theme_status\n";

if ($theme_name === 'Los Cocos Clean' && $theme_status === 'active') {
    echo "✅ <strong>PASS: Los Cocos Clean theme is active</strong>\n";
} else {
    echo "❌ <strong>FAIL: Theme issues detected</strong>\n";
}

// Test 3: WooCommerce Configuration
echo "<h2>🛒 Test 3: WooCommerce Configuration</h2>\n";
$wc_version = WC()->version;
$shop_page_id = wc_get_page_id('shop');
$cart_page_id = wc_get_page_id('cart');
$checkout_page_id = wc_get_page_id('checkout');
$coming_soon = get_option('woocommerce_coming_soon', 'yes');

echo "WooCommerce version: $wc_version\n";
echo "Shop page ID: $shop_page_id\n";
echo "Cart page ID: $cart_page_id\n";
echo "Checkout page ID: $checkout_page_id\n";
echo "Coming soon mode: $coming_soon\n";

$wc_tests_passed = 0;
if ($shop_page_id > 0) { $wc_tests_passed++; echo "✅ Shop page configured\n"; } else { echo "❌ Shop page missing\n"; }
if ($cart_page_id > 0) { $wc_tests_passed++; echo "✅ Cart page configured\n"; } else { echo "❌ Cart page missing\n"; }
if ($checkout_page_id > 0) { $wc_tests_passed++; echo "✅ Checkout page configured\n"; } else { echo "❌ Checkout page missing\n"; }
if ($coming_soon === 'no') { $wc_tests_passed++; echo "✅ Coming soon mode disabled\n"; } else { echo "❌ Coming soon mode enabled\n"; }

echo $wc_tests_passed === 4 ? "✅ <strong>PASS: WooCommerce properly configured</strong>\n" : "❌ <strong>FAIL: WooCommerce configuration issues</strong>\n";

// Test 4: Navigation Menu
echo "<h2>🧭 Test 4: Navigation Menu</h2>\n";
$menu_locations = get_nav_menu_locations();
$primary_menu_id = isset($menu_locations['primary']) ? $menu_locations['primary'] : 0;

if ($primary_menu_id > 0) {
    $menu_items = wp_get_nav_menu_items($primary_menu_id);
    $menu_count = count($menu_items);
    echo "Primary menu ID: $primary_menu_id\n";
    echo "Menu items count: $menu_count\n";
    
    if ($menu_count >= 3) {
        echo "✅ <strong>PASS: Navigation menu properly configured</strong>\n";
        foreach ($menu_items as $item) {
            echo "  - " . $item->title . " → " . $item->url . "\n";
        }
    } else {
        echo "❌ <strong>FAIL: Insufficient menu items</strong>\n";
    }
} else {
    echo "❌ <strong>FAIL: No primary menu configured</strong>\n";
}

// Test 5: Product Categories
echo "<h2>📂 Test 5: Product Categories</h2>\n";
$categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => false,
));

$category_count = count($categories);
echo "Total product categories: $category_count\n";

if ($category_count > 0) {
    echo "✅ <strong>PASS: Product categories exist</strong>\n";
    foreach ($categories as $category) {
        $product_count = $category->count;
        echo "  - " . $category->name . " ($product_count products)\n";
    }
} else {
    echo "❌ <strong>FAIL: No product categories found</strong>\n";
}

// Test 6: Database Connection
echo "<h2>💾 Test 6: Database Connection</h2>\n";
global $wpdb;
$db_test = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish'");

if ($db_test == $total_products) {
    echo "✅ <strong>PASS: Database connection working</strong>\n";
    echo "Database product count matches: $db_test\n";
} else {
    echo "❌ <strong>FAIL: Database inconsistency</strong>\n";
    echo "Expected: $total_products, Found: $db_test\n";
}

// Test 7: Cart Functionality Test
echo "<h2>🛒 Test 7: Cart Functionality</h2>\n";
// Clear existing cart
WC()->cart->empty_cart();

// Add a test product to cart
if ($total_products > 0) {
    $test_product = $products[0];
    $product_id = $test_product->get_id();
    $added = WC()->cart->add_to_cart($product_id, 1);
    
    if ($added) {
        $cart_count = WC()->cart->get_cart_contents_count();
        echo "✅ Product added to cart successfully\n";
        echo "Cart count: $cart_count\n";
        
        // Test cart removal
        WC()->cart->empty_cart();
        $cart_count_after = WC()->cart->get_cart_contents_count();
        
        if ($cart_count_after === 0) {
            echo "✅ <strong>PASS: Cart functionality working</strong>\n";
        } else {
            echo "❌ <strong>FAIL: Cart not properly cleared</strong>\n";
        }
    } else {
        echo "❌ <strong>FAIL: Could not add product to cart</strong>\n";
    }
} else {
    echo "❌ <strong>FAIL: No products to test cart with</strong>\n";
}

// Test 8: Performance Check
echo "<h2>⚡ Test 8: Performance Check</h2>\n";
$start_time = microtime(true);

// Simulate page load by getting products
$performance_products = wc_get_products(array('limit' => 50));
$product_load_time = microtime(true) - $start_time;

echo "Time to load 50 products: " . round($product_load_time * 1000, 2) . "ms\n";

if ($product_load_time < 1.0) {
    echo "✅ <strong>PASS: Good performance (< 1 second)</strong>\n";
} elseif ($product_load_time < 3.0) {
    echo "⚠️ <strong>WARNING: Acceptable performance (< 3 seconds)</strong>\n";
} else {
    echo "❌ <strong>FAIL: Poor performance (> 3 seconds)</strong>\n";
}

// Final Summary
echo "<h2>📋 FINAL TEST SUMMARY</h2>\n";
echo "=================================\n";

$tests_passed = 0;
$total_tests = 8;

// Recalculate results
if ($products_without_images === 0) $tests_passed++;
if ($theme_name === 'Los Cocos Clean' && $theme_status === 'active') $tests_passed++;
if ($wc_tests_passed === 4) $tests_passed++;
if ($primary_menu_id > 0 && count(wp_get_nav_menu_items($primary_menu_id)) >= 3) $tests_passed++;
if ($category_count > 0) $tests_passed++;
if ($db_test == $total_products) $tests_passed++;
if (isset($added) && $added && $cart_count_after === 0) $tests_passed++;
if ($product_load_time < 3.0) $tests_passed++;

echo "Tests passed: $tests_passed / $total_tests\n";
echo "Success rate: " . round(($tests_passed / $total_tests) * 100, 1) . "%\n";

if ($tests_passed === $total_tests) {
    echo "🎉 <strong>ALL TESTS PASSED - SYSTEM FULLY FUNCTIONAL</strong>\n";
} elseif ($tests_passed >= 6) {
    echo "✅ <strong>MOST TESTS PASSED - SYSTEM MOSTLY FUNCTIONAL</strong>\n";
} else {
    echo "❌ <strong>MULTIPLE FAILURES - SYSTEM NEEDS ATTENTION</strong>\n";
}

echo "\n=================================\n";
echo "🏪 Los Cocos E-commerce Store Status: ";
echo $tests_passed >= 7 ? "READY FOR BUSINESS 🚀" : "NEEDS FIXES 🔧";
echo "\n=================================\n";

?>