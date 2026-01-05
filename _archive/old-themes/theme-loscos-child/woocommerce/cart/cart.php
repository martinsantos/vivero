<?php
/**
 * Cart Page - Tailwind Optimized
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart'); ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8"><?php esc_html_e('Tu Carrito', 'woocommerce'); ?></h1>

    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <?php do_action('woocommerce_before_cart_table'); ?>

        <div class="hidden md:block">
            <div class="grid grid-cols-12 gap-4 border-b border-gray-200 pb-2 mb-4">
                <div class="col-span-5">
                    <span class="text-sm font-medium text-gray-500 uppercase"><?php esc_html_e('Producto', 'woocommerce'); ?></span>
                </div>
                <div class="col-span-2 text-center">
                    <span class="text-sm font-medium text-gray-500 uppercase"><?php esc_html_e('Precio', 'woocommerce'); ?></span>
                </div>
                <div class="col-span-2 text-center">
                    <span class="text-sm font-medium text-gray-500 uppercase"><?php esc_html_e('Cantidad', 'woocommerce'); ?></span>
                </div>
                <div class="col-2 text-right">
                    <span class="text-sm font-medium text-gray-500 uppercase"><?php esc_html_e('Subtotal', 'woocommerce'); ?></span>
                </div>
                <div class="col-1">
                    <span class="sr-only"><?php esc_html_e('Remove item', 'woocommerce'); ?></span>
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <?php do_action('woocommerce_before_cart_contents'); ?>

            <?php
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                    ?>
                    <div class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item flex flex-col md:flex-row items-center border-b border-gray-200 pb-6', $cart_item, $cart_item_key)); ?>" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                        <!-- Mobile view -->
                        <div class="md:hidden w-full mb-4">
                            <div class="flex justify-between items-start">
                                <?php
                                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                                if (!$product_permalink) {
                                    echo wp_kses_post($thumbnail);
                                } else {
                                    printf('<a href="%s" class="block w-20 h-20 flex-shrink-0">%s</a>', esc_url($product_permalink), $thumbnail);
                                }
                                ?>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-sm font-medium text-gray-900">
                                        <?php
                                        if (!$product_permalink) {
                                            echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                                        } else {
                                            echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                        }
                                        ?>
                                    </h3>
                                    <div class="mt-1 text-sm text-gray-500">
                                        <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                                    </div>
                                    <div class="mt-2 text-sm font-medium text-gray-900">
                                        <?php
                                            echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                                        ?>
                                    </div>
                                </div>
                                <?php
                                echo apply_filters('woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a href="%s" class="text-red-500 hover:text-red-700" aria-label="%s" data-product_id="%s" data-product_sku="%s">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </a>',
                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                        esc_html__('Remove this item', 'woocommerce'),
                                        esc_attr($product_id),
                                        esc_attr($_product->get_sku())
                                    ),
                                    $cart_item_key
                                );
                                ?>
                            </div>
                            
                            <div class="mt-4 flex justify-between items-center">
                                <div class="quantity">
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
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    <?php
                                        echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Desktop view -->
                        <div class="hidden md:grid md:grid-cols-12 md:gap-4 md:items-center w-full">
                            <!-- Product info -->
                            <div class="col-span-5 flex items-center">
                                <?php
                                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
                                if (!$product_permalink) {
                                    echo wp_kses_post($thumbnail);
                                } else {
                                    printf('<a href="%s" class="block w-20 h-20 flex-shrink-0">%s</a>', esc_url($product_permalink), $thumbnail);
                                }
                                ?>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-900">
                                        <?php
                                        if (!$product_permalink) {
                                            echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                                        } else {
                                            echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s" class="hover:text-indigo-600">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                        }
                                        ?>
                                    </h3>
                                    <?php if ($_product->get_sku()) : ?>
                                        <p class="text-sm text-gray-500"><?php echo esc_html__('SKU:', 'woocommerce') . ' ' . esc_html($_product->get_sku()); ?></p>
                                    <?php endif; ?>
                                    <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                                </div>
                            </div>
                            
                            <!-- Price -->
                            <div class="col-span-2 text-center text-sm text-gray-900">
                                <?php
                                    echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                                ?>
                            </div>
                            
                            <!-- Quantity -->
                            <div class="col-span-2">
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
                                            'classes'      => 'max-w-20 text-center',
                                        ),
                                        $_product,
                                        false
                                    );
                                }
                                echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
                                ?>
                            </div>
                            
                            <!-- Subtotal -->
                            <div class="col-span-2 text-right text-sm font-medium text-gray-900">
                                <?php
                                    echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
                                ?>
                            </div>
                            
                            <!-- Remove -->
                            <div class="col-span-1 text-right">
                                <?php
                                echo apply_filters('woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a href="%s" class="text-red-500 hover:text-red-700" aria-label="%s" data-product_id="%s" data-product_sku="%s">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </a>',
                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                        esc_html__('Remove this item', 'woocommerce'),
                                        esc_attr($product_id),
                                        esc_attr($_product->get_sku())
                                    ),
                                    $cart_item_key
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            
            <?php do_action('woocommerce_cart_contents'); ?>
            
            <div class="actions flex flex-col sm:flex-row justify-between items-center mt-8 pt-6 border-t border-gray-200">
                <?php if (wc_coupons_enabled()) { ?>
                    <div class="coupon mb-4 sm:mb-0 w-full sm:w-auto">
                        <label for="coupon_code" class="sr-only"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label>
                        <div class="flex">
                            <input type="text" name="coupon_code" class="input-text px-4 py-2 border border-gray-300 rounded-l-md w-full sm:w-auto" id="coupon_code" value="" placeholder="<?php esc_attr_e('Cupón de descuento', 'woocommerce'); ?>" />
                            <button type="submit" class="button bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" name="apply_coupon" value="<?php esc_attr_e('Aplicar cupón', 'woocommerce'); ?>">
                                <?php esc_attr_e('Aplicar', 'woocommerce'); ?>
                            </button>
                        </div>
                        <?php do_action('woocommerce_cart_coupon'); ?>
                    </div>
                <?php } ?>
                
                <button type="submit" class="button bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full sm:w-auto text-center" name="update_cart" value="<?php esc_attr_e('Actualizar carrito', 'woocommerce'); ?>">
                    <?php esc_html_e('Actualizar carrito', 'woocommerce'); ?>
                </button>
                
                <?php do_action('woocommerce_cart_actions'); ?>
                
                <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
            </div>
            
            <?php do_action('woocommerce_after_cart_contents'); ?>
        </div>
        <?php do_action('woocommerce_after_cart_table'); ?>
    </form>

    <?php do_action('woocommerce_before_cart_collaterals'); ?>

    <div class="cart-collaterals mt-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cross-sell products -->
        <div class="lg:col-span-2">
            <?php woocommerce_cross_sell_display(); ?>
        </div>
        
        <!-- Cart totals -->
        <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
            <?php woocommerce_cart_totals(); ?>
            
            <!-- Proceed to checkout button -->
            <div class="mt-6">
                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <?php esc_html_e('Proceder al pago', 'woocommerce'); ?>
                </a>
            </div>
            
            <!-- Continue shopping link -->
            <div class="mt-4 text-center">
                <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    &larr; <?php esc_html_e('Seguir comprando', 'woocommerce'); ?>
                </a>
            </div>
        </div>
    </div>

    <?php do_action('woocommerce_after_cart'); ?>
