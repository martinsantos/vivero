<?php
/**
 * My Account Dashboard
 *
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$allowed_html = array(
    'a' => array(
        'href' => array(),
    ),
);
?>

<div class="space-y-6">
    <?php
    /* translators: 1: user display name 2: logout url */
    $welcome = sprintf(
        /* translators: 1: user display name 2: logout url */
        __('Hello %1$s (not %1$s? <a href="%2$s" class="text-green-600 hover:text-green-800">Log out</a>)', 'woocommerce'),
        '<strong>' . esc_html($current_user->display_name) . '</strong>',
        esc_url(wc_logout_url())
    );
    ?>
    
    <div class="text-gray-700">
        <?php echo wp_kses($welcome, $allowed_html); ?>
    </div>

    <p class="text-gray-700">
        <?php
        /* translators: 1: Orders URL 2: Address URL 3: Account URL. */
        $dashboard_desc = __(
            'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">billing address</a>, and <a href="%3$s">edit your password and account details</a>.',
            'woocommerce'
        );
        printf(
            wp_kses($dashboard_desc, $allowed_html),
            esc_url(wc_get_endpoint_url('orders')),
            esc_url(wc_get_endpoint_url('edit-address')),
            esc_url(wc_get_endpoint_url('edit-account'))
        );
        ?>
    </p>

    <?php
    /**
     * My Account dashboard.
     *
     * @since 2.6.0
     */
    do_action('woocommerce_account_dashboard');

    /**
     * Deprecated woocommerce_before_my_account action.
     *
     * @deprecated 2.6.0
     */
    do_action('woocommerce_before_my_account');

    /**
     * Deprecated woocommerce_after_my_account action.
     *
     * @deprecated 2.6.0
     */
    do_action('woocommerce_after_my_account');
    ?>
</div>
