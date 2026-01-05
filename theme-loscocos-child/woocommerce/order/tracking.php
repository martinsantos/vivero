<?php
/**
 * Order Tracking
 *
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined('ABSPATH') || exit;

$order_status_text = sprintf(
    /* translators: 1: order number 2: order status */
    esc_html__('Order #%1$s was placed on %2$s and is currently %3$s.', 'woocommerce'),
    '<span class="font-medium">' . $order->get_order_number() . '</span>',
    '<time datetime="' . esc_attr($order->get_date_created()->date('c')) . '">' . esc_html(wc_format_datetime($order->get_date_created())) . '</time>',
    '<span class="text-green-600">' . esc_html(wc_get_order_status_name($order->get_status())) . '</span>'
);
?>

<div class="space-y-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <?php esc_html_e('Order updates', 'woocommerce'); ?>
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                <?php echo wp_kses($order_status_text, array('span' => array('class' => array()), 'time' => array('datetime' => array()))); ?>
            </p>
        </div>

        <div class="px-4 py-5 sm:p-6">
            <div class="flow-root">
                <ul class="-mb-8">
                    <?php
                    $notes = $order->get_customer_order_notes();
                    
                    if ($notes) : 
                        foreach ($notes as $note) :
                            $note_classes   = array('note');
                            $note_classes[] = $note->customer_note ? 'customer-note' : '';
                            $note_classes[] = 'system' === $note->added_by ? 'system-note' : '';
                            $note_classes   = apply_filters('woocommerce_order_note_class', array_filter($note_classes), $note);
                            ?>
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    <?php echo wp_kses_post(wpautop(wptexturize($note->comment_content))); ?>
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="<?php echo esc_attr(date('Y-m-d H:i:s', strtotime($note->comment_date))); ?>">
                                                    <?php echo esc_html(date_i18n('F j, Y g:i a', strtotime($note->comment_date))); ?>
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; 
                    else : ?>
                        <li class="text-center py-8">
                            <p class="text-sm text-gray-500">
                                <?php esc_html_e('There are no updates for this order yet.', 'woocommerce'); ?>
                            </p>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <?php esc_html_e('Order details', 'woocommerce'); ?>
            </h3>
        </div>
        
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-base font-medium text-gray-900 mb-4">
                        <?php esc_html_e('Billing address', 'woocommerce'); ?>
                    </h4>
                    <address class="not-italic text-sm text-gray-700">
                        <?php echo wp_kses_post($order->get_formatted_billing_address(esc_html__('N/A', 'woocommerce'))); ?>
                        
                        <?php if ($order->get_billing_phone()) : ?>
                            <p class="mt-2">
                                <span class="text-gray-600"><?php esc_html_e('Phone:', 'woocommerce'); ?></span>
                                <span class="text-gray-900"><?php echo esc_html($order->get_billing_phone()); ?></span>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($order->get_billing_email()) : ?>
                            <p>
                                <span class="text-gray-600"><?php esc_html_e('Email:', 'woocommerce'); ?></span>
                                <span class="text-gray-900"><?php echo esc_html($order->get_billing_email()); ?></span>
                            </p>
                        <?php endif; ?>
                    </address>
                </div>
                
                <?php if (!wc_ship_to_billing_address_only() && $order->needs_shipping_address() && $order->has_shipping_address()) : ?>
                    <div>
                        <h4 class="text-base font-medium text-gray-900 mb-4">
                            <?php esc_html_e('Shipping address', 'woocommerce'); ?>
                        </h4>
                        <address class="not-italic text-sm text-gray-700">
                            <?php echo wp_kses_post($order->get_formatted_shipping_address(esc_html__('N/A', 'woocommerce'))); ?>
                            
                            <?php if ($order->get_shipping_phone()) : ?>
                                <p class="mt-2">
                                    <span class="text-gray-600"><?php esc_html_e('Phone:', 'woocommerce'); ?></span>
                                    <span class="text-gray-900"><?php echo esc_html($order->get_shipping_phone()); ?></span>
                                </p>
                            <?php endif; ?>
                        </address>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php do_action('woocommerce_view_order', $order->get_id()); ?>
