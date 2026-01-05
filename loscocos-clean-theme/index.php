<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <div><?php the_content(); ?></div>
  </article>
<?php endwhile; else: ?>
  <p>No hay contenido.</p>
<?php endif; ?>

<?php get_footer(); ?>
