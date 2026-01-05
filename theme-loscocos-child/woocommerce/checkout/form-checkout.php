<?php
/**
 * Checkout Form
 *
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}

?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8"><?php the_title(); ?></h1>

    <?php do_action('woocommerce_before_checkout_form', $checkout); ?>

    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <?php if ($checkout->get_checkout_fields()) : ?>
                    <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                    <div id="customer_details" class="space-y-6">
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4"><?php esc_html_e('Billing details', 'woocommerce'); ?></h3>
                            <?php do_action('woocommerce_checkout_billing'); ?>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm overflow-hidden p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4"><?php esc_html_e('Additional information', 'woocommerce'); ?></h3>
                            <?php do_action('woocommerce_checkout_shipping'); ?>
                        </div>
                    </div>

                    <?php do_action('woocommerce_checkout_after_customer_details'); ?>
                <?php endif; ?>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden p-6">
                    <h3 id="order_review_heading" class="text-lg font-medium text-gray-900 mb-4">
                        <?php esc_html_e('Your order', 'woocommerce'); ?>
                    </h3>

                    <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
                    
                    <div id="order_review" class="woocommerce-checkout-review-order">
                        <?php do_action('woocommerce_checkout_order_review'); ?>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php do_action('woocommerce_after_checkout_form', $checkout); ?>
</div>
