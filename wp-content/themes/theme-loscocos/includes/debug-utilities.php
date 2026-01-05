<?php
/**
 * Debug Utilities for Los Cocos Theme
 * 
 * @package LosCocos
 * @version 1.0.0
 */

// Ensure this file is being included by WordPress
defined('ABSPATH') || exit;

/**
 * Wrapper for error logging with admin notification
 */
function loscocos_debug_log($message, $data = null) {
    if (WP_DEBUG === true) {
        $timestamp = current_time('mysql');
        $log_entry = "[{$timestamp}] ";
        
        if (is_array($data) || is_object($data)) {
            $log_entry .= $message . ' ' . print_r($data, true);
        } else {
            $log_entry .= $message . ($data ? ' ' . $data : '');
        }
        
        error_log($log_entry . "\n", 3, WP_CONTENT_DIR . '/debug.log');
        
        // Show admin notice if user is admin
        if (is_admin() && current_user_can('manage_options')) {
            add_action('admin_notices', function() use ($message) {
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p><?php echo esc_html($message); ?></p>
                </div>
                <?php
            });
        }
    }
}

/**
 * Error handler for seasonal template functions
 */
function loscocos_handle_seasonal_error($function, $error_message) {
    $error = "Error in {$function}: {$error_message}";
    loscocos_debug_log($error);
    
    if (current_user_can('manage_options')) {
        return "<div class='notice notice-error'><p>{$error}</p></div>";
    }
    return '';
}

/**
 * Enqueue debug scripts
 */
function loscocos_enqueue_debug_scripts() {
    if (WP_DEBUG === true) {
        wp_enqueue_script(
            'loscocos-debug',
            get_template_directory_uri() . '/js/debug.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'loscocos_enqueue_debug_scripts');
