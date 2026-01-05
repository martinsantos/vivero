<?php
/**
 * Add a new payment method
 *
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined('ABSPATH') || exit;

if (!is_ajax()) {
    do_action('woocommerce_before_add_payment_method_page');
}
?>

<div class="space-y-6">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <?php esc_html_e('Add payment method', 'woocommerce'); ?>
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                <?php esc_html_e('Add a new payment method.', 'woocommerce'); ?>
            </p>
        </div>

        <div class="px-4 py-5 sm:p-6">
            <?php if ($available_gateways = WC()->payment_gateways->get_available_payment_gateways()) : ?>
                <form id="add_payment_method" method="post" class="space-y-6">
                    <div id="payment" class="woocommerce-Payment">
                        <ul class="wc_payment_methods payment_methods methods">
                            <?php
                            // Chosen Method.
                            if (count($available_gateways)) {
                                current($available_gateways)->set_selected();
                            }

                            foreach ($available_gateways as $gateway) {
                                ?>
                                <li class="wc_payment_method payment_method_<?php echo esc_attr($gateway->id); ?>">
                                    <input id="payment_method_<?php echo esc_attr($gateway->id); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr($gateway->id); ?>" <?php checked($gateway->chosen, true); ?> />
                                    <label for="payment_method_<?php echo esc_attr($gateway->id); ?>">
                                        <?php echo $gateway->get_title(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?> 
                                        <?php echo $gateway->get_icon(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
                                    </label>
                                    <?php
                                    if ($gateway->has_fields() || $gateway->get_description()) {
                                        echo '<div class="payment_box payment_method_' . esc_attr($gateway->id) . '" style="display:none;">' . $gateway->payment_fields() . '</div>'; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */
                                    }
                                    ?>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>

                        <div class="form-row">
                            <?php wp_nonce_field('woocommerce-add-payment-method', 'woocommerce-add-payment-method-nonce'); ?>
                            <input type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" id="place_order" value="<?php esc_attr_e('Add payment method', 'woocommerce'); ?>" />
                            <input type="hidden" name="woocommerce_add_payment_method" id="woocommerce_add_payment_method" value="1" />
                        </div>
                    </div>
                </form>
            <?php else : ?>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900"><?php esc_html_e('No payment methods available', 'woocommerce'); ?></h3>
                    <p class="mt-1 text-sm text-gray-500">
                        <?php esc_html_e('Sorry, it seems there are no payment methods which support adding a new payment method. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce'); ?>
                    </p>
                    <div class="mt-6">
                        <a href="<?php echo esc_url(wc_get_account_endpoint_url('payment-methods')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <?php esc_html_e('Return to payment methods', 'woocommerce'); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
if (!is_ajax()) {
    do_action('woocommerce_after_add_payment_method_page');
}
