<?php
/**
 * Show success messages
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!$notices) {
    return;
}

?>

<div class="woocommerce-message bg-green-50 border border-green-400 text-green-700 relative rounded-lg p-4 mb-4 shadow-sm" role="alert">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3 flex-1">
            <?php foreach ($notices as $notice) : ?>
                <p class="text-sm" <?php echo wc_get_notice_data_attr($notice); ?>>
                    <?php echo wc_kses_notice($notice['notice']); ?>
                </p>
            <?php endforeach; ?>
        </div>
        <div class="ml-4 flex-shrink-0 flex">
            <button type="button" class="inline-flex text-green-400 hover:text-green-500 focus:outline-none" onclick="this.closest('.woocommerce-message').remove();">
                <span class="sr-only"><?php esc_html_e('Cerrar', 'woocommerce'); ?></span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</div>
