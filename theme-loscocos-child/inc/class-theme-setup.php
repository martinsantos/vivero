<?php
/**
 * Theme Setup Class - Los Cocos Child Theme
 * Handles all theme initialization and configuration
 * 
 * @package LosCocos_Child
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class LosCocos_Theme_Setup {
    
    /**
     * Initialize the theme setup
     */
    public static function init() {
        // Enqueue styles and scripts
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_assets']);
        
        // Theme supports
        add_action('after_setup_theme', [self::class, 'theme_supports']);
    }
    
    /**
     * Enqueue theme assets
     */
    public static function enqueue_assets() {
        // Google Fonts
        wp_enqueue_style(
            'loscocos-google-fonts',
            'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700;900&family=Outfit:wght@300;400;500;700&display=swap',
            [],
            null
        );
        
        // Parent theme styles
        wp_enqueue_style('loscocos-style', get_template_directory_uri() . '/style.css');
        
        // Child theme styles with cache busting
        $version = '1.0.4-' . filemtime(get_stylesheet_directory() . '/style.css');
        wp_enqueue_style(
            'loscocos-child-style',
            get_stylesheet_directory_uri() . '/style.css',
            ['loscocos-style'],
            $version
        );
        
        // Category navigation styles
        $cat_nav_file = get_stylesheet_directory() . '/assets/css/category-navigation.css';
        if (file_exists($cat_nav_file)) {
            wp_enqueue_style(
                'loscocos-category-nav',
                get_stylesheet_directory_uri() . '/assets/css/category-navigation.css',
                ['loscocos-child-style'],
                filemtime($cat_nav_file)
            );
        }
        
        // Product infographic styles
        $infographic_file = get_stylesheet_directory() . '/assets/css/product-infographic.css';
        if (file_exists($infographic_file)) {
            wp_enqueue_style(
                'loscocos-product-infographic',
                get_stylesheet_directory_uri() . '/assets/css/product-infographic.css',
                ['loscocos-child-style'],
                filemtime($infographic_file)
            );
        }
        
        // Custom JavaScript
        $main_js = get_stylesheet_directory() . '/assets/js/main.js';
        if (file_exists($main_js)) {
            wp_enqueue_script(
                'loscocos-child-scripts',
                get_stylesheet_directory_uri() . '/assets/js/main.js',
                ['jquery'],
                filemtime($main_js),
                true
            );
        }
    }
    
    /**
     * Add theme supports
     */
    public static function theme_supports() {
        // WooCommerce support
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
        
        // HTML5 support
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption'
        ]);
        
        // Title tag
        add_theme_support('title-tag');
        
        // Post thumbnails
        add_theme_support('post-thumbnails');
    }
}
