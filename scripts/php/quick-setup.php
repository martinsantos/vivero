<?php
/**
 * Simple Product Setup for Los Cocos
 */

// Load WordPress
require_once(dirname(__FILE__) . '/wp-load.php');

echo "🌱 LOS COCOS - QUICK PRODUCT SETUP\n";
echo "===================================\n\n";

// Create sample products using direct database insertion
global $wpdb;

// First, let's ensure we have a simple product structure
$products = [
    [
        'title' => 'Monstera Deliciosa',
        'content' => 'Planta de interior muy popular por sus hojas grandes y perforadas.',
        'price' => 15000
    ],
    [
        'title' => 'Rosal Trepador',
        'content' => 'Rosal trepador con flores fragantes, ideal para pérgolas.',
        'price' => 20000
    ],
    [
        'title' => 'Maceta de Barro 30cm',
        'content' => 'Maceta de barro cocido de 30cm, ideal para plantas medianas.',
        'price' => 4000
    ]
];

foreach ($products as $product) {
    // Check if product exists
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'product'",
        $product['title']
    ));
    
    if (!$existing) {
        // Insert product
        $result = wp_insert_post([
            'post_title' => $product['title'],
            'post_content' => $product['content'],
            'post_status' => 'publish',
            'post_type' => 'product'
        ]);
        
        if ($result && !is_wp_error($result)) {
            // Add product meta
            update_post_meta($result, '_regular_price', $product['price']);
            update_post_meta($result, '_price', $product['price']);
            update_post_meta($result, '_manage_stock', 'yes');
            update_post_meta($result, '_stock', 50);
            update_post_meta($result, '_stock_status', 'instock');
            
            echo "✅ Created product: {$product['title']}\n";
        }
    } else {
        echo "⏭️  Product already exists: {$product['title']}\n";
    }
}

echo "\n🎉 Setup completed!\n";
echo "Visit: http://localhost:8080/tienda/ to see your products\n";
?>