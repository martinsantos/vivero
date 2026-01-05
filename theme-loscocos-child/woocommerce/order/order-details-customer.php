<?php
/**
 * Order Customer Details
 *
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined('ABSPATH') || exit;

$show_shipping = !wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            <?php esc_html_e('Customer details', 'woocommerce'); ?>
        </h3>
    </div>
    
    <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div class="sm:col-span-1">
                <h4 class="text-base font-medium text-gray-900 mb-4">
                    <?php esc_html_e('Billing address', 'woocommerce'); ?>
                </h4>
                <address class="not-italic text-sm text-gray-700">
                    <?php echo wp_kses_post($order->get_formatted_billing_address(esc_html__('N/A', 'woocommerce'))); ?>
                    
                    <?php if ($order->get_billing_phone()) : ?>
                        <p class="mt-2">
                            <span class="text-gray-600"><?php esc_html_e('Phone:', 'woocommerce'); ?></span>
                            <span class="text-gray-900"><?php echo esc_html($order->get_billing_phone()); ?></span>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($order->get_billing_email()) : ?>
                        <p>
                            <span class="text-gray-600"><?php esc_html_e('Email:', 'woocommerce'); ?></span>
                            <span class="text-gray-900"><?php echo esc_html($order->get_billing_email()); ?></span>
                        </p>
                    <?php endif; ?>
                </address>
            </div>
            
            <?php if ($show_shipping) : ?>
                <div class="sm:col-span-1">
                    <h4 class="text-base font-medium text-gray-900 mb-4">
                        <?php esc_html_e('Shipping address', 'woocommerce'); ?>
                    </h4>
                    <address class="not-italic text-sm text-gray-700">
                        <?php echo wp_kses_post($order->get_formatted_shipping_address(esc_html__('N/A', 'woocommerce'))); ?>
                        
                        <?php if ($order->get_shipping_phone()) : ?>
                            <p class="mt-2">
                                <span class="text-gray-600"><?php esc_html_e('Phone:', 'woocommerce'); ?></span>
                                <span class="text-gray-900"><?php echo esc_html($order->get_shipping_phone()); ?></span>
                            </p>
                        <?php endif; ?>
                    </address>
                </div>
            <?php endif; ?>
            
            <?php do_action('woocommerce_order_details_after_customer_details', $order); ?>
        </div>
    </div>
</div>
