<?php
/**
 * My Account - Payment Methods
 *
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined('ABSPATH') || exit;

$saved_methods     = wc_get_customer_saved_methods_list(get_current_user_id());
$has_methods       = (bool) $saved_methods;
$types             = wc_get_account_payment_methods_types();

do_action('woocommerce_before_account_payment_methods', $has_methods); ?>

<div class="space-y-6">
    <?php if ($has_methods) : ?>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <?php esc_html_e('Payment methods', 'woocommerce'); ?>
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    <?php esc_html_e('Manage your saved payment methods.', 'woocommerce'); ?>
                </p>
            </div>

            <div class="px-4 py-5 sm:p-6">
                <div class="space-y-6">
                    <?php foreach ($saved_methods as $type => $methods ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
                        <?php foreach ($methods as $method ) : ?>
                            <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                                <div class="flex items-center">
                                    <?php if (!empty($method['method']['last4'])) : ?>
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500 text-sm font-medium">
                                                <?php echo esc_html($method['method']['brand']); ?>
                                            </span>
                                        </div>
                                    <?php else : ?>
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo esc_html(wc_get_credit_card_type_label($method['method']['brand'])); ?>
                                            <?php if (!empty($method['method']['last4'])) : ?>
                                                <span class="text-gray-500">
                                                    <?php echo esc_html(sprintf('•••• %s', $method['method']['last4'])); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php
                                            /* translators: 1: credit card expiry date */
                                            printf(esc_html__('Expires %s', 'woocommerce'), esc_html($method['expires']));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <?php if (!empty($method['actions']['delete'])) : ?>
                                        <a href="<?php echo esc_url($method['actions']['delete']['url']); ?>" class="text-sm font-medium text-red-600 hover:text-red-500" onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this payment method?', 'woocommerce'); ?>')">
                                            <?php esc_html_e('Delete', 'woocommerce'); ?>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($method['actions']['default'])) : ?>
                                        <a href="<?php echo esc_url($method['actions']['default']['url']); ?>" class="text-sm font-medium text-green-600 hover:text-green-500">
                                            <?php echo esc_html($method['is_default'] ? __('Default', 'woocommerce') : __('Make default', 'woocommerce')); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900"><?php esc_html_e('No saved methods', 'woocommerce'); ?></h3>
            <p class="mt-1 text-sm text-gray-500">
                <?php esc_html_e('You have no saved payment methods.', 'woocommerce'); ?>
            </p>
        </div>
    <?php endif; ?>
    
    <?php if (WC()->payment_gateways->get_available_payment_gateways()) : ?>
        <div class="mt-10">
            <a href="<?php echo esc_url(wc_get_endpoint_url('add-payment-method')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <?php esc_html_e('Add payment method', 'woocommerce'); ?>
            </a>
        </div>
    <?php endif; ?>
</div>

<?php do_action('woocommerce_after_account_payment_methods', $has_methods); ?>
