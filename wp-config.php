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

// ** Tipo de entorno ** //
// Habilita contraseñas de aplicación en entornos de desarrollo sin exigir HTTPS.
// Valores válidos: 'local', 'development', 'staging', 'production'
if (!defined('WP_ENVIRONMENT_TYPE')) {
    define('WP_ENVIRONMENT_TYPE', 'local');
}

// ** Configuración específica para Los Cocos ** //
// Habilitar debug general - Cambiar a false en producción
define('WP_DEBUG', true);
// Habilitar registro de errores en wp-content/debug.log
define('WP_DEBUG_LOG', true);
// Ocultar errores en frontend - Los admins verán notificaciones
define('WP_DEBUG_DISPLAY', false);
// Forzar modo de desarrollo JavaScript
define('SCRIPT_DEBUG', true);
// Guardar queries SQL de debug
define('SAVEQUERIES', WP_DEBUG);
// Desactivar JavaScript concatenado
define('CONCATENATE_SCRIPTS', false);

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
define('FORCE_SSL_ADMIN', false); // Cambiar a true en producción con SSL
define('AUTOMATIC_UPDATER_DISABLED', false);
define('WP_AUTO_UPDATE_CORE', 'minor');

// Configuración específica para WooCommerce
define('WC_LOG_HANDLER', 'WC_Log_Handler_File');
define('WOOCOMMERCE_CHECKOUT_DEBUG_MODE', false);

// Configuración de caché
define('WP_CACHE', true);
define('ENABLE_CACHE', true);

// Configuración de URLs para producción
define('WP_HOME', 'https://viveroloscocos.com.ar');
define('WP_SITEURL', 'https://viveroloscocos.com.ar');

// Configuración de idioma
define('WPLANG', 'es_ES');

// ¡Eso es todo, deja de editar! Feliz blogging.
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';