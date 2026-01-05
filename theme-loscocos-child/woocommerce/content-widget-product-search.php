<?php
/**
 * Product Search Widget
 *
 * @package WooCommerce\Templates
 * @version 7.3.0
 */

defined('ABSPATH') || exit;
?>

<form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="sr-only" for="woocommerce-product-search-field-<?php echo isset($index) ? absint($index) : 0; ?>">
        <?php esc_html_e('Search for:', 'woocommerce'); ?>
    </label>
    
    <div class="relative">
        <input 
            type="search" 
            id="woocommerce-product-search-field-<?php echo isset($index) ? absint($index) : 0; ?>" 
            class="search-field block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6" 
            placeholder="<?php echo esc_attr__('Search products...', 'woocommerce'); ?>" 
            value="<?php echo get_search_query(); ?>" 
            name="s" 
        />
        <input type="hidden" name="post_type" value="product" />
        
        <button 
            type="submit" 
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-500"
            aria-label="<?php echo esc_attr__('Search', 'woocommerce'); ?>"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button>
    </div>
</form>
