<?php
/**
 * Reset password form
 *
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_reset_password_form');
?>

<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            <?php esc_html_e('Enter a new password', 'woocommerce'); ?>
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            <?php esc_html_e('Please enter a new password below.', 'woocommerce'); ?>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form method="post" class="woocommerce-ResetPassword space-y-6">
                <p class="text-sm text-gray-600">
                    <?php echo apply_filters('woocommerce_reset_password_message', esc_html__('Enter a new password below.', 'woocommerce')); ?>
                </p>

                <?php do_action('woocommerce_resetpassword_form'); ?>

                <div>
                    <label for="password_1" class="block text-sm font-medium text-gray-700">
                        <?php esc_html_e('New password', 'woocommerce'); ?>
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="password" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" name="password_1" id="password_1" autocomplete="new-password" />
                    </div>
                </div>

                <div>
                    <label for="password_2" class="block text-sm font-medium text-gray-700">
                        <?php esc_html_e('Re-enter new password', 'woocommerce'); ?>
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="password" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" name="password_2" id="password_2" autocomplete="new-password" />
                    </div>
                </div>

                <input type="hidden" name="reset_key" value="<?php echo esc_attr($args['key']); ?>" />
                <input type="hidden" name="reset_login" value="<?php echo esc_attr($args['login']); ?>" />

                <?php do_action('woocommerce_resetpassword_form'); ?>

                <input type="hidden" name="wc_reset_password" value="true" />
                <?php wp_nonce_field('reset_password', 'woocommerce-reset-password-nonce'); ?>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" value="<?php esc_attr_e('Save', 'woocommerce'); ?>">
                        <?php esc_html_e('Save', 'woocommerce'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
do_action('woocommerce_after_reset_password_form');
?>
