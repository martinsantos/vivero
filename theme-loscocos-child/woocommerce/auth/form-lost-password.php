<?php
/**
 * Lost password form
 *
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_lost_password_form');
?>

<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            <?php esc_html_e('Reset password', 'woocommerce'); ?>
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            <?php echo apply_filters('woocommerce_lost_password_message', esc_html__('Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce')); ?>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <?php wc_print_notices(); ?>

            <form method="post" class="space-y-6 woocommerce-ResetPassword lost_reset_password">
                <?php wp_nonce_field('lost_password', 'woocommerce-lost-password-nonce'); ?>

                <div>
                    <label for="user_login" class="block text-sm font-medium text-gray-700">
                        <?php esc_html_e('Username or email', 'woocommerce'); ?>
                    </label>
                    <div class="mt-1">
                        <input class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" type="text" name="user_login" id="user_login" autocomplete="username" />
                    </div>
                </div>

                <?php do_action('woocommerce_lostpassword_form', WC()->checkout()); ?>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" value="<?php esc_attr_e('Reset password', 'woocommerce'); ?>">
                        <?php esc_html_e('Reset password', 'woocommerce'); ?>
                    </button>
                </div>

                <div class="text-sm text-center">
                    <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="font-medium text-green-600 hover:text-green-500">
                        <?php esc_html_e('Back to login', 'woocommerce'); ?>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
do_action('woocommerce_after_lost_password_form');
?>
