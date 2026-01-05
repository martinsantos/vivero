<?php
/**
 * Plugin Name: API Debug
 * Description: Debug REST API access issues
 * Version: 1.0.1
 */

// Add a custom REST API endpoint for debugging
add_action('rest_api_init', function() {
    register_rest_route('api-debug/v1', '/test', [
        'methods' => 'GET',
        'callback' => 'api_debug_test',
        'permission_callback' => '__return_true',
    ]);
});

function api_debug_test() {
    // Safely get all headers
    $headers = [];
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
    } else {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) === 'HTTP_') {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$name] = $value;
            }
        }
    }

    $data = [
        'success' => true,
        'message' => 'API Debug Test',
        'wp' => [
            'rest_enabled' => true,
            'rest_url' => function_exists('get_rest_url') ? get_rest_url() : 'get_rest_url() not available',
            'site_url' => function_exists('site_url') ? site_url() : 'site_url() not available',
            'home_url' => function_exists('home_url') ? home_url() : 'home_url() not available',
            'is_ssl' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        ],
        'server' => [
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
            'https' => !empty($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'off',
            'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'N/A',
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'http_host' => $_SERVER['HTTP_HOST'] ?? 'N/A',
            'http_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
        ],
        'auth' => [
            'http_auth' => !empty($_SERVER['PHP_AUTH_USER']),
            'http_auth_user' => $_SERVER['PHP_AUTH_USER'] ?? null,
            'http_authorization' => $_SERVER['HTTP_AUTHORIZATION'] ?? null,
        ],
    ];

    return new WP_REST_Response($data, 200);
}

// Log REST API requests
add_action('rest_api_init', function() {
    if (!empty($_SERVER['REQUEST_URI'])) {
        error_log('REST API Request: ' . $_SERVER['REQUEST_URI']);
    }
}, 0);
