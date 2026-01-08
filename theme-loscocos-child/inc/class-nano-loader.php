<?php
/**
 * Nano Banana Loader
 * Handles asset enqueueing selectively for the Nano prototype
 */

if (!defined('ABSPATH')) {
    exit;
}

class LosCocos_Nano_Loader {
    
    public static function init() {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_nano_assets'], 20);
    }

    public static function enqueue_nano_assets() {
        if (is_page_template('page-nano-concept.php')) {
            wp_enqueue_style(
                'loscocos-nano-framework',
                get_stylesheet_directory_uri() . '/assets/css/nano-framework.css',
                [],
                time() // Dev mode: bust cache
            );
            
            // Unload standard styles to avoid conflict if desired
            // wp_dequeue_style('loscocos-style');
        }
    }
}
