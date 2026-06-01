<?php
/**
 * The template for displaying all pages
 *
 * @package Los_Cocos_Child
 */

get_header();

$is_commerce_page = (function_exists('is_cart') && is_cart())
    || (function_exists('is_checkout') && is_checkout())
    || (function_exists('is_account_page') && is_account_page());

$main_classes = $is_commerce_page
    ? 'lc-commerce-page lc-page site-main pt-24 min-h-screen'
    : 'lc-page site-main pt-24 min-h-screen';
?>

<main id="primary" class="<?php echo esc_attr($main_classes); ?>">
    <div class="container mx-auto px-4 py-12 md:py-16">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('lc-page__article'); ?>>
                <?php if ($is_commerce_page || (!is_checkout() && !is_cart())) : ?>
                    <header class="lc-page__header entry-header mb-8">
                        <p class="lc-page__eyebrow"><?php echo $is_commerce_page ? esc_html__('Compra online', 'loscocos-child') : esc_html__('Vivero Los Cocos', 'loscocos-child'); ?></p>
                        <h1 class="lc-page__title entry-title text-4xl font-serif font-bold text-primary-dark"><?php the_title(); ?></h1>
                    </header>
                <?php endif; ?>

                <div class="lc-page__content entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<?php get_footer(); ?>
