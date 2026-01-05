<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined('ABSPATH') || exit;

div class="cart_totals <?php echo (WC()->customer->has_calculated_shipping()) ? 'calculated_shipping' : ''; ?>">

    <?php do_action('woocommerce_before_cart_totals'); ?>

    <h2 class="text-lg font-medium text-gray-900 mb-6"><?php esc_html_e('Resumen del pedido', 'woocommerce'); ?></h2>

    <div class="shop_table shop_table_responsive">
        <!-- Subtotal -->
        <div class="cart-subtotal flex justify-between py-3 border-b border-gray-200">
            <span class="text-gray-700"><?php esc_html_e('Subtotal', 'woocommerce'); ?></span>
            <span class="text-gray-900 font-medium" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
                <?php wc_cart_totals_subtotal_html(); ?>
            </span>
        </div>

        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <div class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?> flex justify-between py-3 border-b border-gray-200">
                <span class="text-gray-700">
                    <?php wc_cart_totals_coupon_label($coupon); ?>
                    <a href="<?php echo esc_url(add_query_arg('remove_coupon', rawurlencode($coupon->get_code()), defined('WOOCOMMERCE_CHECKOUT') ? wc_get_checkout_url() : wc_get_cart_url())); ?>
                       class="text-red-500 hover:text-red-700"
                       aria-label="<?php esc_attr_e('Remove coupon', 'woocommerce'); ?>">
                        <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
                <span class="text-gray-900 font-medium" data-title="<?php echo esc_attr(wc_cart_totals_coupon_label($coupon, false)); ?>">
                    <?php wc_cart_totals_coupon_html($coupon); ?>
                </span>
            </div>
        <?php endforeach; ?>

        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
            <div class="shipping py-3 border-b border-gray-200">
                <?php do_action('woocommerce_cart_totals_before_shipping'); ?>
                
                <?php wc_cart_totals_shipping_html(); ?>
                
                <?php do_action('woocommerce_cart_totals_after_shipping'); ?>
            </div>
        <?php elseif (WC()->cart->needs_shipping() && 'yes' === get_option('woocommerce_enable_shipping_calc')) : ?>
            <div class="shipping py-3 border-b border-gray-200">
                <span class="text-gray-700"><?php esc_html_e('Shipping', 'woocommerce'); ?></span>
                <span class="text-gray-900 font-medium" data-title="<?php esc_attr_e('Shipping', 'woocommerce'); ?>">
                    <?php woocommerce_shipping_calculator(); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
            <div class="fee flex justify-between py-3 border-b border-gray-200">
                <span class="text-gray-700"><?php echo esc_html($fee->name); ?></span>
                <span class="text-gray-900 font-medium" data-title="<?php echo esc_attr($fee->name); ?>">
                    <?php wc_cart_totals_fee_html($fee); ?>
                </span>
            </div>
        <?php endforeach; ?>

        <?php
        if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) {
            $taxable_address = WC()->customer->get_taxable_address();
            $estimated_text  = '';

            if (WC()->customer->is_customer_outside_base() && !WC()->customer->has_calculated_shipping()) {
                /* translators: %s location. */
                $estimated_text = ' <small>' . esc_html(sprintf('(estimated for %s)', WC()->countries->estimated_for_prefix() . WC()->countries->countries[$estimated_text ? $taxable_address[0] : WC()->countries->get_base_country()] . (WC()->countries->states[WC()->countries->get_base_country()] && !empty($taxable_address[1]) ? ', ' . WC()->countries->states[WC()->countries->get_base_country()][$taxable_address[1]] : ''))) . '</small>';
            }

            if ('itemized' === get_option('woocommerce_tax_total_display')) {
                foreach (WC()->cart->get_tax_totals() as $code => $tax) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                    ?>
                    <div class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?> flex justify-between py-3 border-b border-gray-200">
                        <span class="text-gray-700"><?php echo esc_html($tax->label) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                        <span class="text-gray-900 font-medium" data-title="<?php echo esc_attr($tax->label); ?>">
                            <?php echo wp_kses_post($tax->formatted_amount); ?>
                        </span>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="tax-total flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-700"><?php echo esc_html(WC()->countries->tax_or_vat()) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                    <span class="text-gray-900 font-medium" data-title="<?php echo esc_attr(WC()->countries->tax_or_vat()); ?>">
                        <?php wc_cart_totals_taxes_total_html(); ?>
                    </span>
                </div>
                <?php
            }
        }
        ?>

        <?php do_action('woocommerce_cart_totals_before_order_total'); ?>

        <!-- Order Total -->
        <div class="order-total flex justify-between py-4 font-bold text-lg">
            <span class="text-gray-900"><?php esc_html_e('Total', 'woocommerce'); ?></span>
            <span class="text-gray-900" data-title="<?php esc_attr_e('Total', 'woocommerce'); ?>">
                <?php wc_cart_totals_order_total_html(); ?>
            </span>
        </div>

        <?php do_action('woocommerce_cart_totals_after_order_total'); ?>
    </div>

    <?php do_action('woocommerce_after_cart_totals'); ?>
</div>
