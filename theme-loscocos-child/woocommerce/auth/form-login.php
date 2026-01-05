<?php
/**
 * Login Form
 *
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

do_action('woocommerce_before_customer_login_form'); ?>

<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            <?php esc_html_e('Sign in to your account', 'woocommerce'); ?>
        </h2>
        <?php if ('yes' === get_option('woocommerce_enable_myaccount_registration')) : ?>
            <p class="mt-2 text-center text-sm text-gray-600">
                <?php esc_html_e('Or', 'woocommerce'); ?>
                <a href="#register" class="font-medium text-green-600 hover:text-green-500">
                    <?php esc_html_e('create a new account', 'woocommerce'); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <?php if (isset($_GET['login_failed'])) : ?>
                <div class="rounded-md bg-red-50 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                <?php esc_html_e('Error', 'woocommerce'); ?>
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p><?php esc_html_e('Invalid username or password. Please try again.', 'woocommerce'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form class="space-y-6" method="post">
                <?php do_action('woocommerce_login_form_start'); ?>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">
                        <?php esc_html_e('Username or email address', 'woocommerce'); ?>
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" name="username" id="username" autocomplete="username" value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" />
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            <?php esc_html_e('Password', 'woocommerce'); ?>
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="text-sm">
                            <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="font-medium text-green-600 hover:text-green-500">
                                <?php esc_html_e('Forgot your password?', 'woocommerce'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" />
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="rememberme" name="rememberme" type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded" />
                    <label for="rememberme" class="ml-2 block text-sm text-gray-900">
                        <?php esc_html_e('Remember me', 'woocommerce'); ?>
                    </label>
                </div>

                <?php do_action('woocommerce_login_form'); ?>

                <div>
                    <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" name="login" value="<?php esc_attr_e('Log in', 'woocommerce'); ?>">
                        <?php esc_html_e('Sign in', 'woocommerce'); ?>
                    </button>
                </div>

                <?php do_action('woocommerce_login_form_end'); ?>
            </form>

            <?php if ('yes' === get_option('woocommerce_enable_myaccount_registration')) : ?>
                <div id="register" class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php esc_html_e('Create an account', 'woocommerce'); ?>
                    </h3>
                    <form method="post" class="space-y-6" <?php do_action('woocommerce_register_form_tag'); ?> >
                        <?php do_action('woocommerce_register_form_start'); ?>

                        <?php if ('no' === get_option('woocommerce_registration_generate_username')) : ?>
                            <div>
                                <label for="reg_username" class="block text-sm font-medium text-gray-700">
                                    <?php esc_html_e('Username', 'woocommerce'); ?>
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <input type="text" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" name="username" id="reg_username" autocomplete="username" value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" />
                                </div>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label for="reg_email" class="block text-sm font-medium text-gray-700">
                                <?php esc_html_e('Email address', 'woocommerce'); ?>
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="email" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" name="email" id="reg_email" autocomplete="email" value="<?php echo (!empty($_POST['email'])) ? esc_attr(wp_unslash($_POST['email'])) : ''; ?>" />
                            </div>
                        </div>

                        <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>
                            <div>
                                <label for="reg_password" class="block text-sm font-medium text-gray-700">
                                    <?php esc_html_e('Password', 'woocommerce'); ?>
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <input type="password" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" name="password" id="reg_password" autocomplete="new-password" />
                                </div>
                            </div>
                        <?php else : ?>
                            <p class="text-sm text-gray-500">
                                <?php esc_html_e('A password will be sent to your email address.', 'woocommerce'); ?>
                            </p>
                        <?php endif; ?>

                        <?php do_action('woocommerce_register_form'); ?>

                        <div class="flex items-center">
                            <input type="checkbox" name="privacy_policy" id="privacy_policy" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded" required />
                            <label for="privacy_policy" class="ml-2 block text-sm text-gray-900">
                                <?php
                                /* translators: %s: Privacy policy page name. */
                                printf(esc_html__('I have read and agree to the website %s', 'woocommerce'), '<a href="' . esc_url(wc_privacy_policy_url()) . '" class="text-green-600 hover:text-green-500">' . esc_html__('privacy policy', 'woocommerce') . '</a>');
                                ?>
                                <span class="text-red-500">*</span>
                            </label>
                        </div>

                        <?php do_action('woocommerce_register_form_end'); ?>

                        <div>
                            <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" name="register" value="<?php esc_attr_e('Register', 'woocommerce'); ?>">
                                <?php esc_html_e('Create account', 'woocommerce'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php do_action('woocommerce_after_customer_login_form'); ?>
