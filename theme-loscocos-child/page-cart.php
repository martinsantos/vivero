<?php
/**
 * Template Name: Premium Organic Cart
 * Description: A unified, botanical cart experience for Vivero Los Cocos.
 */

if (!defined('ABSPATH')) exit;

// Enqueue specialized styles with cache busting for development
$cart_css = get_stylesheet_directory() . '/assets/css/premium-cart.css';
$cart_version = file_exists($cart_css) ? filemtime($cart_css) : LOSCOCOS_CHILD_VERSION;
wp_enqueue_style('premium-cart-styles', get_stylesheet_directory_uri() . '/assets/css/premium-cart.css', array(), $cart_version);

get_header(); ?>

<div class="premium-cart-wrapper">
    <div class="premium-cart-container">
        
        <div class="cart-left-section">
            <header class="cart-title-section">
                <h1 class="text-primary-dark">Tu Compra<br>en Espera.</h1>
                <p class="text-neutral-medium mt-4 text-lg font-light">Revisá cantidades, precios y productos antes de avanzar al pago.</p>
            </header>

            <?php if (WC()->cart->is_empty()) : ?>
                <div class="empty-cart-message py-20 text-center">
                    <h2 class="text-3xl font-bold mb-6 text-primary">Tu carrito está vacío.</h2>
                    <a href="<?php echo wc_get_page_permalink('shop'); ?>" class="btn-checkout-premium inline-block w-auto px-12">Ver tienda</a>
                </div>
            <?php else : ?>
                
                <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
                    <div class="premium-cart-items">
                        <?php
                        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                            $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                ?>
                                <div class="item-glass-card woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                                    
                                    <!-- Image Column -->
                                    <div class="item-image-box">
                                        <?php
                                        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                                        if (!$product_permalink) {
                                            echo $thumbnail;
                                        } else {
                                            printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
                                        }
                                        ?>
                                    </div>

                                    <!-- Details Column -->
                                    <div class="item-details">
                                        <span class="sku">REF: <?php echo esc_html($_product->get_sku()); ?></span>
                                        <h3 class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                                            <?php
                                            if (!$product_permalink) {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                                            } else {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                            }
                                            ?>
                                        </h3>
                                        
                                        <div class="item-price-tag" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                                            <?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
                                        </div>

                                        <div class="mt-6">
                                            <div class="premium-qty-control">
                                                <button type="button" class="qty-btn minus">-</button>
                                                <?php
                                                if ($_product->is_sold_individually()) {
                                                    $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                                                } else {
                                                    $product_quantity = woocommerce_quantity_input(
                                                        array(
                                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                                            'input_value'  => $cart_item['quantity'],
                                                            'max_value'    => $_product->get_max_purchase_quantity(),
                                                            'min_value'    => '0',
                                                            'product_name' => $_product->get_name(),
                                                        ),
                                                        $_product,
                                                        false
                                                    );
                                                }
                                                echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
                                                ?>
                                                <button type="button" class="qty-btn plus">+</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price/Subtotal Column -->
                                    <div class="product-subtotal font-bold text-2xl text-primary" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
                                        <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                                    </div>

                                    <!-- Remove Action -->
                                    <?php
                                    echo apply_filters(
                                        'woocommerce_cart_item_remove_link',
                                        sprintf(
                                            '<a href="%s" class="remove-item-btn" aria-label="%s" data-product_id="%s" data-product_sku="%s"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg></a>',
                                            esc_url(wc_get_cart_remove_url($cart_item_key)),
                                            esc_html__('Remove this item', 'woocommerce'),
                                            esc_attr($product_id),
                                            esc_attr($_product->get_sku())
                                        ),
                                        $cart_item_key
                                    );
                                    ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    
                    <button type="submit" class="hidden" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>
                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                </form>
            <?php endif; ?>
        </div>

        <!-- Sidebar Section -->
        <?php if (!WC()->cart->is_empty()) : ?>
        <aside class="cart-sidebar-totals">
            <div class="totals-nano-glass">
                <h2>Resumen</h2>
                
                <div class="cart-summary-rows">
                    <div class="row-total">
                        <span class="font-medium">Subtotal</span>
                        <span class="text-primary font-bold"><?php wc_cart_totals_subtotal_html(); ?></span>
                    </div>
                    
                    <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
                        <div class="row-total coupon-row text-green-400">
                            <span>Cupón: <?php echo esc_html($code); ?></span>
                            <span><?php wc_cart_totals_coupon_html($coupon); ?></span>
                        </div>
                    <?php endforeach; ?>

                    <div class="row-total shipping-row">
                        <span>Envío</span>
                        <span><?php foreach (WC()->cart->get_shipping_packages() as $i => $package) {
                            $chosen_method = isset(WC()->session->chosen_shipping_methods[$i]) ? WC()->session->chosen_shipping_methods[$i] : '';
                            echo "Calculado en Checkout";
                        } ?></span>
                    </div>

                    <div class="row-grand-total">
                        <span>Total</span>
                        <span><?php wc_cart_totals_order_total_html(); ?></span>
                    </div>
                </div>

                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn-checkout-premium">
                    Ir al checkout
                </a>

                <p class="text-xs opacity-40 mt-8 text-center uppercase tracking-widest">Pago seguro y garantía Los Cocos</p>
            </div>
            
            <div class="mt-8 px-8 py-6 border border-gray-100 rounded-3xl bg-white flex items-center gap-4">
                <span class="text-2xl">📦</span>
                <div>
                    <h4 class="font-bold text-sm">Envío Sin Costo</h4>
                    <p class="text-xs text-gray-500">En pedidos superiores a $50.000</p>
                </div>
            </div>
        </aside>
        <?php endif; ?>

    </div>
</div>

<script>
jQuery(function($) {
    // Elegant handle for quantity buttons
    $(document).on('click', '.qty-btn', function() {
        var $btn = $(this);
        var $input = $btn.siblings('.quantity').find('input.qty') || $btn.closest('.premium-qty-control').find('input.qty');
        var val = parseInt($input.val());
        
        if ($btn.hasClass('plus')) {
            $input.val(val + 1);
        } else {
            if (val > 1) $input.val(val - 1);
        }
        
        // Trigger WooCommerce update
        $("[name='update_cart']").removeAttr('disabled').trigger('click');
    });

    // Handle AJAX updates for smoother experience
    $(document.body).on('updated_cart_totals', function() {
        // We could re-trigger animations here if needed
    });
});
</script>

<?php get_footer(); ?>
