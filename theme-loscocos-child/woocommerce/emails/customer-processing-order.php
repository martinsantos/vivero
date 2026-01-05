<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 8.6.0
 */

defined('ABSPATH') || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action('woocommerce_email_header', $email_heading, $email);
?>

<table class="w-full max-w-2xl mx-auto" cellspacing="0" cellpadding="0" border="0" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; width: 100%; max-width: 600px; margin: 0 auto; border-collapse: collapse;">
    <tr>
        <td class="px-6 py-8 text-center" style="padding: 24px 0 32px 0; text-align: center;">
            <h1 class="text-2xl font-bold text-gray-900 mb-4" style="font-size: 24px; font-weight: 700; color: #111827; margin-bottom: 16px;">
                <?php echo esc_html($email_heading); ?>
            </h1>
            <p class="text-gray-600 mb-6" style="color: #4B5563; margin-bottom: 24px; line-height: 1.5;">
                <?php 
                /* translators: %s: Customer first name */
                printf(esc_html__('Hi %s,', 'woocommerce'), esc_html($order->get_billing_first_name())); 
                ?>
            </p>
            <p class="text-gray-600 mb-8" style="color: #4B5563; margin-bottom: 32px; line-height: 1.5;">
                <?php 
                /* translators: %s: Order number */
                printf(esc_html__('Just to let you know &mdash; we\'ve received your order #%s, and it is now being processed:', 'woocommerce'), esc_html($order->get_order_number())); 
                ?>
            </p>
        </td>
    </tr>

    <?php 
    /*
     * @hooked WC_Emails::order_details() Shows the order details table.
     * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
     * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
     * @since 2.5.0
     */
    do_action('woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email);
    ?>

    <?php 
    /*
     * @hooked WC_Emails::order_meta() Shows order meta data.
     */
    do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email);
    ?>

    <?php 
    /*
     * @hooked WC_Emails::customer_details() Shows customer details
     * @hooked WC_Emails::email_address() Shows email address
     */
    do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email);
    ?>

    <tr>
        <td class="px-6 py-8 text-center" style="padding: 32px 0 24px 0; text-align: center;">
            <p class="text-gray-600 mb-6" style="color: #4B5563; margin-bottom: 24px; line-height: 1.5;">
                <?php esc_html_e('Thanks for shopping with us.', 'woocommerce'); ?>
            </p>
            <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="inline-block bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-md transition-colors duration-200" style="display: inline-block; background-color: #059669; color: #ffffff; font-weight: 500; padding: 8px 24px; border-radius: 6px; text-decoration: none; transition: background-color 0.2s;">
                <?php esc_html_e('View your account', 'woocommerce'); ?>
            </a>
        </td>
    </tr>
</table>

<?php
/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action('woocommerce_email_footer', $email);
?>
