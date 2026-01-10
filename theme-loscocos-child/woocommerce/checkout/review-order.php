<?php
/**
 * Review order table - Premium Design
 *
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;

// Get cart items count
$cart_items = WC()->cart->get_cart();
$items_count = WC()->cart->get_cart_contents_count();
?>

<!-- Products List -->
<div class="checkout-products-list">
    <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">
        <?php echo esc_html($items_count); ?> <?php echo esc_html(_n('producto', 'productos', $items_count, 'woocommerce')); ?> en tu pedido
    </div>

    <?php
    do_action('woocommerce_review_order_before_cart_contents');

    foreach ($cart_items as $cart_item_key => $cart_item) {
        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
            $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
            $thumbnail = $_product->get_image('thumbnail', array('class' => 'checkout-product-img'));
            $product_subtotal = WC()->cart->get_product_subtotal($_product, $cart_item['quantity']);
            ?>
            <div class="checkout-product <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                <div class="checkout-product-image">
                    <?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
                <div class="checkout-product-info">
                    <div class="checkout-product-name">
                        <?php echo wp_kses_post($product_name); ?>
                    </div>
                    <div class="checkout-product-meta">
                        <?php echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <span>Cantidad: <?php echo esc_html($cart_item['quantity']); ?></span>
                    </div>
                </div>
                <div class="checkout-product-price">
                    <?php echo apply_filters('woocommerce_cart_item_subtotal', $product_subtotal, $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </div>
            <?php
        }
    }

    do_action('woocommerce_review_order_after_cart_contents');
    ?>
</div>

<!-- Order Totals -->
<div class="checkout-totals" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
    <!-- Subtotal -->
    <div class="checkout-summary-row subtotal">
        <span><?php esc_html_e('Subtotal', 'woocommerce'); ?></span>
        <span><?php wc_cart_totals_subtotal_html(); ?></span>
    </div>

    <!-- Coupons -->
    <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
        <div class="checkout-summary-row discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
            <span><?php wc_cart_totals_coupon_label($coupon); ?></span>
            <span><?php wc_cart_totals_coupon_html($coupon); ?></span>
        </div>
    <?php endforeach; ?>

    <!-- Shipping -->
    <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
        <?php do_action('woocommerce_review_order_before_shipping'); ?>
        
        <div class="checkout-summary-row">
            <span><?php esc_html_e('Envío', 'woocommerce'); ?></span>
            <span>
                <?php 
                $packages = WC()->shipping->get_packages();
                foreach ($packages as $i => $package) {
                    $chosen_method = isset(WC()->session->chosen_shipping_methods[$i]) ? WC()->session->chosen_shipping_methods[$i] : '';
                    $available_methods = $package['rates'];
                    
                    if (!empty($available_methods)) {
                        foreach ($available_methods as $method) {
                            if ($method->id === $chosen_method) {
                                echo wp_kses_post($method->get_label() . ': ' . wc_price($method->cost));
                                break;
                            }
                        }
                    }
                }
                if (empty($packages) || empty($packages[0]['rates'])) {
                    esc_html_e('Calcular en el siguiente paso', 'woocommerce');
                }
                ?>
            </span>
        </div>
        
        <?php do_action('woocommerce_review_order_after_shipping'); ?>
    <?php endif; ?>

    <!-- Fees -->
    <?php foreach (WC()->cart->get_fees() as $fee) : ?>
        <div class="checkout-summary-row fee">
            <span><?php echo esc_html($fee->name); ?></span>
            <span><?php wc_cart_totals_fee_html($fee); ?></span>
        </div>
    <?php endforeach; ?>

    <!-- Taxes -->
    <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
        <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
            <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
                <div class="checkout-summary-row tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
                    <span><?php echo esc_html($tax->label); ?></span>
                    <span><?php echo wp_kses_post($tax->formatted_amount); ?></span>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="checkout-summary-row tax-total">
                <span><?php echo esc_html(WC()->countries->tax_or_vat()); ?></span>
                <span><?php wc_cart_totals_taxes_total_html(); ?></span>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php do_action('woocommerce_review_order_before_order_total'); ?>

    <!-- Total -->
    <div class="checkout-summary-row total">
        <span><?php esc_html_e('Total', 'woocommerce'); ?></span>
        <span class="final-price"><?php wc_cart_totals_order_total_html(); ?></span>
    </div>

    <?php do_action('woocommerce_review_order_after_order_total'); ?>
</div>

<style>
/* Ensure product images are normalized */
.checkout-product-image img,
.checkout-product-image .wp-post-image {
    width: 80px !important;
    height: 80px !important;
    object-fit: cover !important;
    border-radius: 0.75rem !important;
}
</style>
