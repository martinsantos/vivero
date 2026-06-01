<?php
/**
 * Configuración de WordPress para Vivero Los Cocos E-commerce
 * 
 * @package LosCocos
 * @version 1.0.0
 */

// ** Configuración de la base de datos ** //
define('DB_NAME', 'loscocos_wp');
define('DB_USER', 'loscocos_user');
define('DB_PASSWORD', 'loscocos_2024');
define('DB_HOST', 'db');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// ** Claves de autenticación únicas ** //
define('AUTH_KEY',         'loscocos-auth-key-2024-secure-vivero-mendoza');
define('SECURE_AUTH_KEY',  'loscocos-secure-auth-key-plantas-argentina');
define('LOGGED_IN_KEY',    'loscocos-logged-in-key-ecommerce-woocommerce');
define('NONCE_KEY',        'loscocos-nonce-key-jardineria-premium');
define('AUTH_SALT',        'loscocos-auth-salt-vivero-profesional');
define('SECURE_AUTH_SALT', 'loscocos-secure-salt-plantas-mendoza');
define('LOGGED_IN_SALT',   'loscocos-logged-salt-ecommerce-seguro');
define('NONCE_SALT',       'loscocos-nonce-salt-tienda-online');

// ** Prefijo de tablas ** //
$table_prefix = 'lc_';

// Detectar requests locales para no forzar URLs de producción en Docker.
$loscocos_http_host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
$loscocos_is_local_request = (bool) preg_match('/^(localhost|127\.0\.0\.1)(:\d+)?$/', $loscocos_http_host);
$loscocos_site_url = $loscocos_is_local_request
    ? 'http://' . $loscocos_http_host
    : 'https://viveroloscocos.com.ar';

// ** Tipo de entorno ** //
if (!defined('WP_ENVIRONMENT_TYPE')) {
    define('WP_ENVIRONMENT_TYPE', $loscocos_is_local_request ? 'local' : 'production');
}

// ** Configuración específica para Los Cocos ** //
// Modo producción: debug deshabilitado
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);
define('SAVEQUERIES', false);
define('CONCATENATE_SCRIPTS', true);

// Configuración de memoria y rendimiento
define('WP_MEMORY_LIMIT', '512M');
define('WP_MAX_MEMORY_LIMIT', '1024M');
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);

// Configuración de uploads
define('UPLOAD_MAX_FILESIZE', '64M');
define('POST_MAX_SIZE', '64M');

// Configuración de seguridad
define('DISALLOW_FILE_EDIT', true);
define('FORCE_SSL_ADMIN', !$loscocos_is_local_request);
define('AUTOMATIC_UPDATER_DISABLED', false);
define('WP_AUTO_UPDATE_CORE', 'minor');

// Configuración específica para WooCommerce
define('WC_LOG_HANDLER', 'WC_Log_Handler_File');
define('WOOCOMMERCE_CHECKOUT_DEBUG_MODE', false);

// Configuración de caché
// WP_CACHE se activa cuando se instala un plugin de caché (ej: WP Super Cache)
define('WP_CACHE', false);
define('ENABLE_CACHE', false);

// Configuración de URLs para producción y Docker local
define('WP_HOME', $loscocos_site_url);
define('WP_SITEURL', $loscocos_site_url);

// Configuración de idioma
define('WPLANG', 'es_ES');

// ¡Eso es todo, deja de editar! Feliz blogging.
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
