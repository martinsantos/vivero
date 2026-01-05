<?php
/**
 * Review order table
 *
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;
?>
<table class="w-full mb-6">
    <thead>
        <tr class="border-b border-gray-200">
            <th class="text-left text-sm font-medium text-gray-500 uppercase tracking-wider py-3"><?php esc_html_e('Product', 'woocommerce'); ?></th>
            <th class="text-right text-sm font-medium text-gray-500 uppercase tracking-wider py-3"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        do_action('woocommerce_review_order_before_cart_contents');

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
                ?>
                <tr class="border-b border-gray-200 <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                    <td class="py-4">
                        <div class="flex items-center">
                            <?php
                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
                            echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            ?>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $cart_item['quantity']) . '</strong>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-right text-sm font-medium text-gray-900 py-4">
                        <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </td>
                </tr>
                <?php
            }
        }

        do_action('woocommerce_review_order_after_cart_contents');
        ?>
    </tbody>
    <tfoot>
        <tr class="border-b border-gray-200">
            <th class="text-left text-sm font-medium text-gray-500 py-3"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
            <td class="text-right text-sm text-gray-900 py-3"><?php wc_cart_totals_subtotal_html(); ?></td>
        </tr>

        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <tr class="border-b border-gray-200 cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
                <th class="text-left text-sm font-medium text-gray-500 py-3"><?php wc_cart_totals_coupon_label($coupon); ?></th>
                <td class="text-right text-sm text-gray-900 py-3"><?php wc_cart_totals_coupon_html($coupon); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
            <?php do_action('woocommerce_review_order_before_shipping'); ?>
            <?php wc_cart_totals_shipping_html(); ?>
            <?php do_action('woocommerce_review_order_after_shipping'); ?>
        <?php endif; ?>

        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
            <tr class="border-b border-gray-200 fee">
                <th class="text-left text-sm font-medium text-gray-500 py-3"><?php echo esc_html($fee->name); ?></th>
                <td class="text-right text-sm text-gray-900 py-3"><?php wc_cart_totals_fee_html($fee); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
            <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
                <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
                    <tr class="border-b border-gray-200 tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
                        <th class="text-left text-sm font-medium text-gray-500 py-3"><?php echo esc_html($tax->label); ?></th>
                        <td class="text-right text-sm text-gray-900 py-3"><?php echo wp_kses_post($tax->formatted_amount); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr class="border-b border-gray-200 tax-total">
                    <th class="text-left text-sm font-medium text-gray-500 py-3"><?php echo esc_html(WC()->countries->tax_or_vat()); ?></th>
                    <td class="text-right text-sm text-gray-900 py-3"><?php wc_cart_totals_taxes_total_html(); ?></td>
                </tr>
            <?php endif; ?>
        <?php endif; ?>

        <?php do_action('woocommerce_review_order_before_order_total'); ?>

        <tr class="border-b border-gray-200">
            <th class="text-left text-base font-bold text-gray-900 py-3"><?php esc_html_e('Total', 'woocommerce'); ?></th>
            <td class="text-right text-base font-bold text-gray-900 py-3"><?php wc_cart_totals_order_total_html(); ?></td>
        </tr>

        <?php do_action('woocommerce_review_order_after_order_total'); ?>
    </tfoot>
</table>
