<?php
/**
 * Edit account form
 *
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_edit_account_form'); ?>

<form class="woocommerce-EditAccountForm edit-account space-y-6" action="" method="post" <?php do_action('woocommerce_edit_account_form_tag'); ?> >
    <?php do_action('woocommerce_edit_account_form_start'); ?>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <?php esc_html_e('Account details', 'woocommerce'); ?>
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                <?php esc_html_e('Update your name and email address.', 'woocommerce'); ?>
            </p>
        </div>
        
        <div class="px-4 py-5 sm:p-6 space-y-6">
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label for="account_first_name" class="block text-sm font-medium text-gray-700">
                        <?php esc_html_e('First name', 'woocommerce'); ?>
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr($user->first_name); ?>" />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="account_last_name" class="block text-sm font-medium text-gray-700">
                        <?php esc_html_e('Last name', 'woocommerce'); ?>
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr($user->last_name); ?>" />
                    </div>
                </div>

                <div class="sm:col-span-6">
                    <label for="account_display_name" class="block text-sm font-medium text-gray-700">
                        <?php esc_html_e('Display name', 'woocommerce'); ?>
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" name="account_display_name" id="account_display_name" value="<?php echo esc_attr($user->display_name); ?>" />
                        <p class="mt-2 text-sm text-gray-500">
                            <?php esc_html_e('This will be how your name will be displayed in the account section and in reviews', 'woocommerce'); ?>
                        </p>
                    </div>
                </div>

                <div class="sm:col-span-6">
                    <label for="account_email" class="block text-sm font-medium text-gray-700">
                        <?php esc_html_e('Email address', 'woocommerce'); ?>
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="email" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr($user->user_email); ?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <?php esc_html_e('Password change', 'woocommerce'); ?>
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                <?php esc_html_e('Fill out the fields below to change your password.', 'woocommerce'); ?>
            </p>
        </div>
        
        <div class="px-4 py-5 sm:p-6 space-y-6">
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-6">
                    <label for="password_current" class="block text-sm font-medium text-gray-700">
                        <?php esc_html_e('Current password (leave blank to leave unchanged)', 'woocommerce'); ?>
                    </label>
                    <div class="mt-1">
                        <input type="password" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" name="password_current" id="password_current" autocomplete="off" />
                    </div>
                </div>

                <div class="sm:col-span-6">
                    <label for="password_1" class="block text-sm font-medium text-gray-700">
                        <?php esc_html_e('New password (leave blank to leave unchanged)', 'woocommerce'); ?>
                    </label>
                    <div class="mt-1">
                        <input type="password" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" name="password_1" id="password_1" autocomplete="off" />
                    </div>
                </div>

                <div class="sm:col-span-6">
                    <label for="password_2" class="block text-sm font-medium text-gray-700">
                        <?php esc_html_e('Confirm new password', 'woocommerce'); ?>
                    </label>
                    <div class="mt-1">
                        <input type="password" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" name="password_2" id="password_2" autocomplete="off" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php do_action('woocommerce_edit_account_form'); ?>

    <div class="flex justify-end">
        <?php wp_nonce_field('save_account_details', 'save-account-details-nonce'); ?>
        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" name="save_account_details" value="<?php esc_attr_e('Save changes', 'woocommerce'); ?>">
            <?php esc_html_e('Save changes', 'woocommerce'); ?>
        </button>
        <input type="hidden" name="action" value="save_account_details" />
    </div>

    <?php do_action('woocommerce_edit_account_form_end'); ?>
</form>

<?php do_action('woocommerce_after_edit_account_form'); ?>
