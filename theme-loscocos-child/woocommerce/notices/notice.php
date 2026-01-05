<?php
/**
 * Notice template
 *
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$message = $notice['notice'];
$notice_classes = $notice['data'] ?? array();
$notice_type = $notice['notice_type'] ?? 'info';

// Map WooCommerce notice types to Tailwind classes
$type_classes = array(
    'error'   => 'bg-red-50 border-red-400 text-red-700',
    'success' => 'bg-green-50 border-green-400 text-green-700',
    'notice'  => 'bg-blue-50 border-blue-400 text-blue-700',
    'info'    => 'bg-blue-50 border-blue-400 text-blue-700',
);

$icon_map = array(
    'error'   => '<svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
    </svg>',
    'success' => '<svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
    </svg>',
    'notice'  => '<svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
    </svg>',
    'info'    => '<svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
    </svg>',
);

$default_classes = array(
    'relative',
    'rounded-md',
    'border',
    'p-4',
    'mb-4',
    'flex',
    'items-start',
    'shadow-sm',
);

// Ensure we have a valid notice type
if (!array_key_exists($notice_type, $type_classes)) {
    $notice_type = 'info';
}

// Add the specific type classes
$classes = array_merge($default_classes, explode(' ', $type_classes[$notice_type]));

// Add any additional classes from the notice data
if (!empty($notice_classes) && is_array($notice_classes)) {
    $classes = array_merge($classes, $notice_classes);
}

$class_string = implode(' ', array_unique(array_filter($classes)));
?>

<div class="<?php echo esc_attr($class_string); ?>" <?php echo wc_get_notice_data_attr($notice); ?>>
    <div class="flex-shrink-0">
        <?php echo $icon_map[$notice_type]; ?>
    </div>
    <div class="ml-3 flex-1 md:flex md:justify-between">
        <p class="text-sm">
            <?php echo wc_kses_notice($message); ?>
        </p>
        <?php if ($notices && 1 < count($notices)) : ?>
            <p class="mt-3 text-sm md:mt-0 md:ml-6">
                <a href="#" class="whitespace-nowrap font-medium text-gray-900 hover:text-gray-700" data-notice-dismiss>
                    <?php esc_html_e('Dismiss', 'woocommerce'); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
    <?php if (apply_filters('woocommerce_notice_dismissible', true, $notice_type)) : ?>
        <div class="ml-4 flex-shrink-0 flex">
            <button type="button" class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none" data-dismiss="notice">
                <span class="sr-only"><?php esc_html_e('Dismiss', 'woocommerce'); ?></span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    <?php endif; ?>
</div>

<?php if ($notices && 1 < count($notices)) : ?>
    <div class="hidden" data-notice-count="<?php echo esc_attr(count($notices)); ?>"></div>
<?php endif; ?>
