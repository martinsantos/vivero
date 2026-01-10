<?php
/**
 * Checkout Form - Premium Design
 *
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}

// Calculate discount if exists
$cart_subtotal = WC()->cart->get_subtotal();
$cart_total = WC()->cart->get_total('edit');
$has_discount = $cart_subtotal > $cart_total;
$discount_percent = $has_discount ? round((($cart_subtotal - $cart_total) / $cart_subtotal) * 100) : 0;
?>

<div class="checkout-container">
    <div class="checkout-main">
        
        <!-- Progress Indicator -->
        <div class="checkout-progress">
            <div class="checkout-step completed">
                <span class="step-number">✓</span>
                <span class="step-text">Carrito</span>
            </div>
            <div class="checkout-step active">
                <span class="step-number">2</span>
                <span class="step-text">Datos</span>
            </div>
            <div class="checkout-step">
                <span class="step-number">3</span>
                <span class="step-text">Pago</span>
            </div>
            <div class="checkout-step">
                <span class="step-number">4</span>
                <span class="step-text">Confirmación</span>
            </div>
        </div>

        <!-- Trust Badges -->
        <div class="trust-badges">
            <div class="trust-badge">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span>Compra Segura SSL</span>
            </div>
            <div class="trust-badge">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>Múltiples Métodos de Pago</span>
            </div>
            <div class="trust-badge">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                <span>Envío a Todo el País</span>
            </div>
            <div class="trust-badge">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span>Plantas 100% Garantizadas</span>
            </div>
        </div>

        <?php do_action('woocommerce_before_checkout_form', $checkout); ?>

        <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
            
            <div class="checkout-grid">
                <!-- Left Column: Billing & Shipping -->
                <div class="checkout-left">
                    <?php if ($checkout->get_checkout_fields()) : ?>
                        <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                        <div id="customer_details">
                            <!-- Billing Details Card -->
                            <div class="checkout-card mb-6">
                                <div class="checkout-card-header">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <h3><?php esc_html_e('Datos de Facturación', 'woocommerce'); ?></h3>
                                </div>
                                <div class="checkout-card-body">
                                    <?php do_action('woocommerce_checkout_billing'); ?>
                                </div>
                            </div>

                            <!-- Shipping Details Card -->
                            <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                            <div class="checkout-card mb-6">
                                <div class="checkout-card-header">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <h3><?php esc_html_e('Dirección de Envío', 'woocommerce'); ?></h3>
                                </div>
                                <div class="checkout-card-body">
                                    <?php do_action('woocommerce_checkout_shipping'); ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Additional Information Card -->
                            <div class="checkout-card">
                                <div class="checkout-card-header">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    <h3><?php esc_html_e('Notas del Pedido', 'woocommerce'); ?></h3>
                                </div>
                                <div class="checkout-card-body">
                                    <?php do_action('woocommerce_before_order_notes', $checkout); ?>
                                    
                                    <?php if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>
                                        <div class="checkout-form-group">
                                            <label for="order_comments"><?php esc_html_e('Notas sobre tu pedido (opcional)', 'woocommerce'); ?></label>
                                            <textarea name="order_comments" id="order_comments" placeholder="<?php esc_attr_e('¿Alguna indicación especial para la entrega? Por ejemplo: horarios preferidos, instrucciones de acceso, etc.', 'woocommerce'); ?>" rows="3"></textarea>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php do_action('woocommerce_after_order_notes', $checkout); ?>
                                </div>
                            </div>
                        </div>

                        <?php do_action('woocommerce_checkout_after_customer_details'); ?>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="checkout-right">
                    <div class="checkout-summary">
                        <!-- Order Summary Card -->
                        <div class="checkout-card">
                            <div class="checkout-card-header">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <h3><?php esc_html_e('Resumen del Pedido', 'woocommerce'); ?></h3>
                            </div>
                            <div class="checkout-card-body">
                                <?php if ($has_discount && $discount_percent >= 10) : ?>
                                <div class="checkout-offer-banner">
                                    <span class="offer-text">🎉 ¡Estás ahorrando en tu compra!</span>
                                    <span class="offer-badge"><?php echo esc_html($discount_percent); ?>% OFF</span>
                                </div>
                                <?php endif; ?>

                                <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
                                
                                <div id="order_review" class="woocommerce-checkout-review-order">
                                    <?php do_action('woocommerce_checkout_order_review'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Security Notice -->
                        <div class="checkout-security">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Tus datos están protegidos con encriptación SSL de 256 bits</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <?php do_action('woocommerce_after_checkout_form', $checkout); ?>
    </div>
</div>
