<?php
/**
 * Enable Application Passwords in local/dev without HTTPS.
 * This is safe for local environments and has no effect in prod.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('lc_enable_app_passwords_local')) {
    add_filter('wp_is_application_passwords_available', function ($available) {
        if (defined('WP_ENVIRONMENT_TYPE')) {
            $type = WP_ENVIRONMENT_TYPE;
            if ($type === 'local' || $type === 'development') {
                return true; // allow without HTTPS in local/dev
            }
        }
        return $available;
    }, 10, 1);
}
