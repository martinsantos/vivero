<?php
/**
 * Edit address form
 *
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

$page_title = ('billing' === $load_address) ? __('Billing address', 'woocommerce') : __('Shipping address', 'woocommerce');

do_action('woocommerce_before_edit_account_address_form'); ?>

<?php if (!$load_address) : ?>
    <?php wc_get_template('myaccount/my-address.php'); ?>
<?php else : ?>
    <form method="post" class="space-y-6">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <?php echo esc_html(apply_filters('woocommerce_my_account_edit_address_title', $page_title, $load_address)); ?>
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    <?php echo $load_address === 'billing' ? __('Update your billing address.', 'woocommerce') : __('Update your shipping address.', 'woocommerce'); ?>
                </p>
            </div>

            <div class="px-4 py-5 sm:p-6">
                <div class="space-y-6">
                    <?php do_action("woocommerce_before_edit_address_form_{$load_address}"); ?>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <?php
                        foreach ($address as $key => $field) {
                            woocommerce_form_field($key, $field, wc_get_post_data_by_key($key, $field['value']));
                        }
                        ?>
                    </div>

                    <?php do_action("woocommerce_after_edit_address_form_{$load_address}"); ?>

                    <div class="flex justify-end pt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" name="save_address" value="<?php esc_attr_e('Save address', 'woocommerce'); ?>">
                            <?php esc_html_e('Save address', 'woocommerce'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <?php wp_nonce_field('woocommerce-edit_address', 'woocommerce-edit-address-nonce'); ?>
        <input type="hidden" name="action" value="edit_address" />
    </form>
<?php endif; ?>

<?php do_action('woocommerce_after_edit_account_address_form'); ?>
