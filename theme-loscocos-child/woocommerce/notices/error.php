<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
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

<div class="woocommerce-error bg-red-50 border border-red-400 text-red-700 relative rounded-lg p-4 mb-4 shadow-sm" role="alert">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3 flex-1">
            <h3 class="text-sm font-medium text-red-800 mb-1">
                <?php esc_html_e('Por favor, corrige los siguientes errores:', 'woocommerce'); ?>
            </h3>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                <?php foreach ($notices as $notice) : ?>
                    <li <?php echo wc_get_notice_data_attr($notice); ?>>
                        <?php echo wc_kses_notice($notice['notice']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="ml-4 flex-shrink-0 flex">
            <button type="button" class="inline-flex text-red-400 hover:text-red-500 focus:outline-none" onclick="this.closest('.woocommerce-error').remove();">
                <span class="sr-only"><?php esc_html_e('Cerrar', 'woocommerce'); ?></span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</div>
