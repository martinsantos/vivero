<?php
/**
 * Product card for catalog loops.
 *
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

if (empty($product) || !$product->is_visible()) {
    return;
}

$product_id = $product->get_id();
$product_url = get_permalink($product_id);
$image_attrs = array(
    'class' => 'h-full w-full object-cover transition-transform duration-500 group-hover:scale-105',
    'loading' => 'lazy',
);
$image = $product->get_image_id()
    ? wp_get_attachment_image($product->get_image_id(), 'woocommerce_thumbnail', false, $image_attrs)
    : '<img src="' . esc_url(wc_placeholder_img_src('woocommerce_thumbnail')) . '" alt="' . esc_attr($product->get_name()) . '" class="' . esc_attr($image_attrs['class']) . '" loading="lazy">';
$categories = wp_get_post_terms($product_id, 'product_cat');
$default_category_id = (int) get_option('default_product_cat');
$category_name = '';

if ($categories && !is_wp_error($categories)) {
    foreach ($categories as $category) {
        if ((int) $category->term_id !== $default_category_id && 'sin-categorizar' !== $category->slug) {
            $category_name = $category->name;
            break;
        }
    }
}

if (!$category_name) {
    $category_name = 'Vivero Los Cocos';
}

$short_description = wp_trim_words(wp_strip_all_tags($product->get_short_description()), 18, '...');

if (!$short_description) {
    $short_description = sprintf(
        '%s seleccionado para compra directa o consulta personalizada.',
        $category_name
    );
}
$is_quick_add = $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock();
?>

<li <?php wc_product_class('lc-product-card group flex h-full flex-col overflow-hidden rounded-lg border border-neutral-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg', $product); ?>>
    <a href="<?php echo esc_url($product_url); ?>" class="lc-product-card__media relative block aspect-square overflow-hidden bg-neutral-100">
        <?php echo wp_kses_post($image); ?>

        <div class="absolute left-3 top-3 flex flex-col gap-2">
            <?php if ($product->is_on_sale()) : ?>
                <span class="rounded-full bg-accent px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">Oferta</span>
            <?php elseif ($product->is_featured()) : ?>
                <span class="rounded-full bg-primary px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">Destacado</span>
            <?php endif; ?>
        </div>

        <?php if (!$product->is_in_stock()) : ?>
            <span class="absolute right-3 top-3 rounded-full bg-neutral-dark px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">Agotado</span>
        <?php endif; ?>
    </a>

    <div class="lc-product-card__body flex flex-1 flex-col p-5">
        <?php if ($category_name) : ?>
            <p class="lc-product-card__category mb-2 text-xs font-semibold uppercase tracking-widest text-primary-light">
                <?php echo esc_html($category_name); ?>
            </p>
        <?php endif; ?>

        <h2 class="lc-product-card__title mb-3 min-h-[3.5rem] text-lg font-bold leading-snug text-primary-dark">
            <a href="<?php echo esc_url($product_url); ?>" class="hover:text-accent">
                <?php echo esc_html($product->get_name()); ?>
            </a>
        </h2>

        <p class="lc-product-card__description mb-5 min-h-[2.75rem] text-sm leading-relaxed text-neutral-medium">
            <?php echo esc_html($short_description); ?>
        </p>

        <div class="mt-auto">
            <div class="lc-product-card__meta mb-4 flex items-end justify-between gap-3">
                <div class="lc-product-card__price text-xl font-extrabold text-neutral-dark">
                    <?php echo wp_kses_post($product->get_price_html()); ?>
                </div>
                <?php if ($product->is_in_stock()) : ?>
                    <span class="rounded-full bg-secondary-light px-3 py-1 text-xs font-semibold text-primary-dark">En stock</span>
                <?php else : ?>
                    <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-medium">Sin stock</span>
                <?php endif; ?>
            </div>

            <div class="lc-product-card__actions grid grid-cols-1 gap-2">
                <?php if ($is_quick_add) : ?>
                    <a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
                       data-quantity="1"
                       data-product_id="<?php echo esc_attr($product_id); ?>"
                       data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                       class="button product_type_simple add_to_cart_button ajax_add_to_cart inline-flex min-h-[44px] items-center justify-center rounded-full bg-primary px-5 py-3 text-sm font-bold text-white transition-colors hover:bg-primary-dark">
                        Agregar al carrito
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url($product_url); ?>"
                       class="inline-flex min-h-[44px] items-center justify-center rounded-full bg-primary px-5 py-3 text-sm font-bold text-white transition-colors hover:bg-primary-dark">
                        Ver producto
                    </a>
                <?php endif; ?>

                <a href="<?php echo esc_url($product_url); ?>" class="inline-flex min-h-[40px] items-center justify-center rounded-full border border-neutral-200 px-5 py-2 text-sm font-semibold text-neutral-dark transition-colors hover:border-primary hover:text-primary">
                    Detalles y cuidados
                </a>
            </div>
        </div>
    </div>
</li>
