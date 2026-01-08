<?php
/**
 * Gestor Unificado de URLs - Los Cocos
 * Centraliza todas las URLs del sitio para evitar duplicaciones
 * 
 * @package LosCocos
 * @version 1.0.0
 */

class LosCocos_URL_Manager {
    
    /**
     * Obtener URL de la tienda principal
     */
    public static function get_shop_url() {
        if (function_exists('wc_get_page_id')) {
            $shop_page_id = wc_get_page_id('shop');
            if ($shop_page_id > 0) {
                return get_permalink($shop_page_id);
            }
        }
        return home_url('/shop/');
    }
    
    /**
     * Obtener URL del carrito
     */
    public static function get_cart_url() {
        if (function_exists('wc_get_cart_url')) {
            return wc_get_cart_url();
        }
        return home_url('/cart/');
    }
    
    /**
     * Obtener URL del checkout
     */
    public static function get_checkout_url() {
        if (function_exists('wc_get_checkout_url')) {
            return wc_get_checkout_url();
        }
        return home_url('/checkout/');
    }
    
    /**
     * Obtener URL de una categoría de producto
     */
    public static function get_product_category_url($category_slug) {
        $term = get_term_by('slug', $category_slug, 'product_cat');
        if ($term) {
            return get_term_link($term);
        }
        return self::get_shop_url();
    }
    
    /**
     * Obtener URL de un producto individual
     */
    public static function get_product_url($product_id) {
        if (function_exists('get_permalink')) {
            return get_permalink($product_id);
        }
        return home_url('/product/' . $product_id . '/');
    }
    
    /**
     * Obtener URL de mi cuenta
     */
    public static function get_account_url() {
        if (function_exists('wc_get_account_endpoint_url')) {
            return wc_get_account_endpoint_url('dashboard');
        }
        return home_url('/my-account/');
    }
    
    /**
     * Limpiar URLs problemáticas
     */
    public static function clean_product_urls() {
        // NEVER redirect in admin area - multiple checks for robustness
        if (is_admin() || (defined('WP_ADMIN') && WP_ADMIN)) {
            return;
        }
        
        // Also check if we're in wp-admin path (ultimate fallback)
        if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/') !== false) {
            return;
        }
        
        // Remover parámetros problemáticos y manejar redirects (frontend only)
        if (isset($_GET['post_type']) && $_GET['post_type'] === 'product') {
            wp_redirect(self::get_shop_url(), 301);
            exit;
        }

        // Redireccionar URLs problemáticas según documentación
        $current_url = $_SERVER['REQUEST_URI'];

        // Manejar categoría "uncategorized"
        if (strpos($current_url, '/product-category/uncategorized/') !== false) {
            wp_redirect(self::get_shop_url(), 301);
            exit;
        }

        // Manejar formato antiguo de URLs de productos
        if (preg_match('/\?post_type=product&p=(\d+)/', $current_url, $matches)) {
            $product_id = $matches[1];
            wp_redirect(self::get_product_url($product_id), 301);
            exit;
        }

        // Limpiar parámetros innecesarios
        if (strpos($current_url, '?') !== false) {
            $clean_url = remove_query_arg(['post_type', 'p'], $current_url);
            if ($clean_url !== $current_url) {
                wp_redirect($clean_url, 301);
                exit;
            }
        }
    }
    
    /**
     * Obtener breadcrumbs correctos
     */
    public static function get_breadcrumbs($product_id = null) {
        $breadcrumbs = array();
        
        // Inicio
        $breadcrumbs[] = array(
            'name' => 'Inicio',
            'url' => home_url('/')
        );
        
        // Tienda
        $breadcrumbs[] = array(
            'name' => 'Tienda',
            'url' => self::get_shop_url()
        );
        
        // Si es un producto específico
        if ($product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                // Categoría del producto
                $categories = wp_get_post_terms($product_id, 'product_cat');
                if ($categories && !is_wp_error($categories)) {
                    $category = $categories[0];
                    $breadcrumbs[] = array(
                        'name' => $category->name,
                        'url' => get_term_link($category)
                    );
                }
                
                // Producto actual
                $breadcrumbs[] = array(
                    'name' => $product->get_name(),
                    'url' => null // Página actual
                );
            }
        }
        
        return $breadcrumbs;
    }
}

// Inicializar limpieza de URLs
add_action('init', array('LosCocos_URL_Manager', 'clean_product_urls'));

// Funciones helper globales
function loscocos_shop_url() {
    return LosCocos_URL_Manager::get_shop_url();
}

function loscocos_cart_url() {
    return LosCocos_URL_Manager::get_cart_url();
}

function loscocos_checkout_url() {
    return LosCocos_URL_Manager::get_checkout_url();
}

function loscocos_product_category_url($category_slug) {
    return LosCocos_URL_Manager::get_product_category_url($category_slug);
}

function loscocos_product_url($product_id) {
    return LosCocos_URL_Manager::get_product_url($product_id);
}

function loscocos_breadcrumbs($product_id = null) {
    return LosCocos_URL_Manager::get_breadcrumbs($product_id);
}