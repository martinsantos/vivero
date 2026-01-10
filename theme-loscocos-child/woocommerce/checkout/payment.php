<?php
/**
 * Checkout Payment Section - Premium Design
 *
 * @package WooCommerce\Templates
 * @version 8.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!is_ajax()) {
    do_action('woocommerce_review_order_before_payment');
}
?>

<div id="payment" class="woocommerce-checkout-payment" style="margin-top: 1.5rem;">
    <?php if (WC()->cart->needs_payment()) : ?>
        <div style="margin-bottom: 1.5rem;">
            <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#059669">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <?php esc_html_e('Método de Pago', 'woocommerce'); ?>
            </h4>
            
            <ul class="wc_payment_methods payment_methods methods">
                <?php
                if (!empty($available_gateways)) {
                    foreach ($available_gateways as $gateway) {
                        wc_get_template('checkout/payment-method.php', array('gateway' => $gateway));
                    }
                } else {
                    echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info" style="padding: 1rem; background: #f0fdf4; border: 1px solid #059669; border-radius: 0.5rem; color: #047857;">';
                    echo apply_filters(
                        'woocommerce_no_available_payment_methods_message',
                        WC()->customer->get_billing_country() 
                            ? esc_html__('Lo sentimos, no hay métodos de pago disponibles para tu ubicación. Por favor, contactanos si necesitás asistencia.', 'woocommerce') 
                            : esc_html__('Por favor, completá tus datos arriba para ver los métodos de pago disponibles.', 'woocommerce')
                    );
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <!-- Terms & Conditions -->
    <div style="margin-bottom: 1.5rem;">
        <?php wc_get_template('checkout/terms.php'); ?>
    </div>
    
    <?php do_action('woocommerce_review_order_before_submit'); ?>

    <!-- Place Order Button -->
    <button type="submit" class="checkout-submit" id="place_order" value="<?php echo esc_attr($order_button_text); ?>" data-value="<?php echo esc_attr($order_button_text); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
        <?php echo esc_html($order_button_text); ?>
    </button>

    <?php do_action('woocommerce_review_order_after_submit'); ?>

    <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
</div>

<?php
if (!is_ajax()) {
    do_action('woocommerce_review_order_after_payment');
}
