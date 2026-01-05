<?php
/**
 * Plugin Name: REST API Test
 * Description: Test REST API functionality
 * Version: 1.0.0
 */

// Add a test endpoint
add_action('rest_api_init', function() {
    register_rest_route('test/v1', '/status', [
        'methods' => 'GET',
        'callback' => 'test_rest_api_status',
        'permission_callback' => '__return_true',
    ]);
});

function test_rest_api_status() {
    return new WP_REST_Response([
        'success' => true,
        'message' => 'REST API is working',
        'data' => [
            'site_url' => site_url(),
            'rest_url' => get_rest_url(),
            'wc_active' => class_exists('WooCommerce'),
            'wc_rest_active' => class_exists('WC_REST_System_Status_Controller'),
            'server' => [
                'php_version' => phpversion(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
                'https' => !empty($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'off',
            ],
        ],
    ], 200);
}
