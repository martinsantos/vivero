<?php
/**
 * The template for displaying all pages
 *
 * @package Los_Cocos_Child
 */

get_header(); ?>

<main id="primary" class="site-main pt-24 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php if (!is_checkout() && !is_cart()) : ?>
                    <header class="entry-header mb-8">
                        <h1 class="entry-title text-4xl font-serif font-bold text-primary-dark"><?php the_title(); ?></h1>
                    </header>
                <?php endif; ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<?php get_footer(); ?>
