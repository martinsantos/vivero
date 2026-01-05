<?php
/**
 * My Addresses
 *
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

defined('ABSPATH') || exit;

$customer_id = get_current_user_id();

if (!wc_ship_to_billing_address_only() && wc_shipping_enabled()) {
    $get_addresses = apply_filters(
        'woocommerce_my_account_get_addresses',
        array(
            'billing'  => __('Billing address', 'woocommerce'),
            'shipping' => __('Shipping address', 'woocommerce'),
        ),
        $customer_id
    );
} else {
    $get_addresses = apply_filters(
        'woocommerce_my_account_get_addresses',
        array(
            'billing' => __('Billing address', 'woocommerce'),
        ),
        $customer_id
    );
}

$col = 1;
?>

<div class="space-y-6">
    <p class="text-gray-700">
        <?php echo apply_filters('woocommerce_my_account_my_address_description', esc_html__('The following addresses will be used on the checkout page by default.', 'woocommerce')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </p>

    <?php $this->add_inline_editing_attributes('address', 'basic'); ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($get_addresses as $name => $address_title) : ?>
            <?php $address = wc_get_account_formatted_address($name); ?>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        <?php echo esc_html($address_title); ?>
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="text-sm text-gray-700">
                        <?php echo $address ? wp_kses_post($address) : esc_html_e('You have not set up this type of address yet.', 'woocommerce'); ?>
                    </div>
                    <div class="mt-5">
                        <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address', $name)); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            <?php echo $address ? esc_html__('Edit', 'woocommerce') : esc_html__('Add', 'woocommerce'); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
