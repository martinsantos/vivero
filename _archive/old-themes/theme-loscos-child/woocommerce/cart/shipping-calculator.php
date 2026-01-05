<?php
/**
 * Shipping calculator
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/shipping-calculator.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_shipping_calculator'); ?>

<form class="woocommerce-shipping-calculator space-y-4" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
    <h2 class="text-lg font-medium text-gray-900">
        <a href="#" class="shipping-calculator-button flex items-center text-indigo-600 hover:text-indigo-800">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <?php esc_html_e('Calculate shipping', 'woocommerce'); ?>
        </a>
    </h2>

    <section class="shipping-calculator-form space-y-4" style="display:none;">
        <?php if (apply_filters('woocommerce_shipping_calculator_enable_country', true)) : ?>
            <p class="form-row form-row-wide" id="calc_shipping_country_field">
                <label for="calc_shipping_country" class="block text-sm font-medium text-gray-700 mb-1">
                    <?php esc_html_e('Country / region', 'woocommerce'); ?>
                </label>
                <select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="default"><?php esc_html_e('Select a country / region&hellip;', 'woocommerce'); ?></option>
                    <?php
                    foreach (WC()->countries->get_shipping_countries() as $key => $value) {
                        echo '<option value="' . esc_attr($key) . '"' . selected(WC()->customer->get_shipping_country(), esc_attr($key), false) . '>' . esc_html($value) . '</option>';
                    }
                    ?>
                </select>
            </p>
        <?php endif; ?>

        <?php if (apply_filters('woocommerce_shipping_calculator_enable_state', true)) : ?>
            <p class="form-row form-row-wide" id="calc_shipping_state_field">
                <?php
                $current_cc = WC()->customer->get_shipping_country();
                $current_r  = WC()->customer->get_shipping_state();
                $states     = WC()->countries->get_states($current_cc);
                ?>
                <label for="calc_shipping_state" class="block text-sm font-medium text-gray-700 mb-1">
                    <?php esc_html_e('State / County', 'woocommerce'); ?>
                </label>
                <?php if (is_array($states) && empty($states)) : ?>
                    <input type="hidden" name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php esc_attr_e('State / County', 'woocommerce'); ?>" />
                <?php else : ?>
                    <select name="calc_shipping_state" class="state_select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" id="calc_shipping_state" data-placeholder="<?php esc_attr_e('State / County', 'woocommerce'); ?>">
                        <option value=""><?php esc_html_e('Select an option&hellip;', 'woocommerce'); ?></option>
                        <?php
                        foreach ($states as $ckey => $cvalue) {
                            echo '<option value="' . esc_attr($ckey) . '" ' . selected($current_r, $ckey, false) . '>' . esc_html($cvalue) . '</option>';
                        }
                        ?>
                    </select>
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <?php if (apply_filters('woocommerce_shipping_calculator_enable_city', true)) : ?>
            <p class="form-row form-row-wide" id="calc_shipping_city_field">
                <label for="calc_shipping_city" class="block text-sm font-medium text-gray-700 mb-1">
                    <?php esc_html_e('City', 'woocommerce'); ?>
                </label>
                <input type="text" class="input-text w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="<?php echo esc_attr(WC()->customer->get_shipping_city()); ?>" placeholder="<?php esc_attr_e('City', 'woocommerce'); ?>" name="calc_shipping_city" id="calc_shipping_city" />
            </p>
        <?php endif; ?>

        <?php if (apply_filters('woocommerce_shipping_calculator_enable_postcode', true)) : ?>
            <p class="form-row form-row-wide" id="calc_shipping_postcode_field">
                <label for="calc_shipping_postcode" class="block text-sm font-medium text-gray-700 mb-1">
                    <?php esc_html_e('Postcode / ZIP', 'woocommerce'); ?>
                </label>
                <input type="text" class="input-text w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="<?php echo esc_attr(WC()->customer->get_shipping_postcode()); ?>" placeholder="<?php esc_attr_e('Postcode / ZIP', 'woocommerce'); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
            </p>
        <?php endif; ?>

        <p>
            <button type="submit" name="calc_shipping" value="1" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <?php esc_html_e('Update', 'woocommerce'); ?>
            </button>
        </p>

        <?php wp_nonce_field('woocommerce-shipping-calculator', 'woocommerce-shipping-calculator-nonce'); ?>
    </section>
</form>

<?php do_action('woocommerce_after_shipping_calculator'); ?>
