<?php
/**
 * WooCommerce Customizations Class - Los Cocos Child Theme
 * Handles all WooCommerce-specific hooks and customizations
 * 
 * @package LosCocos_Child
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class LosCocos_WC_Customizations {
    
    /**
     * Initialize WooCommerce customizations
     */
    public static function init() {
        // Single product scripts
        add_action('wp_enqueue_scripts', [self::class, 'single_product_scripts'], 20);
        
        // Product tabs customization
        add_filter('woocommerce_product_tabs', [self::class, 'customize_product_tabs'], 98);

        // The catalog design is a classic WooCommerce template in this theme.
        add_filter('woocommerce_has_block_template', [self::class, 'prefer_classic_catalog_templates'], 20, 2);
    }
    
    /**
     * Enqueue single product specific scripts
     */
    public static function single_product_scripts() {
        if (class_exists('WooCommerce') && is_product()) {
            wp_enqueue_script('wc-single-product');
        }
    }
    
    /**
     * Customize product tabs
     */
    public static function customize_product_tabs($tabs) {
        // Rename description tab
        if (isset($tabs['description'])) {
            $tabs['description']['title'] = __('Descripción', 'loscocos');
        }
        
        // Rename additional info tab
        if (isset($tabs['additional_information'])) {
            $tabs['additional_information']['title'] = __('Información', 'loscocos');
        }
        
        // Rename reviews tab
        if (isset($tabs['reviews'])) {
            $tabs['reviews']['title'] = __('Reseñas', 'loscocos');
        }
        
        return $tabs;
    }

    /**
     * Keep shop and taxonomy archives on the theme templates used in production.
     */
    public static function prefer_classic_catalog_templates($has_template, $template_name) {
        $classic_catalog_templates = [
            'archive-product',
            'taxonomy-product_cat',
            'taxonomy-product_tag',
            'taxonomy-product_attribute',
        ];

        if (in_array($template_name, $classic_catalog_templates, true)) {
            return false;
        }

        return $has_template;
    }
}
