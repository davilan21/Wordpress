<?php
/**
 * Single post template - Works with Elementor
 */
get_header(); ?>

<main class="site-main">
    <?php while (have_posts()) : the_post(); ?>
        <?php if (bdi_is_elementor_page()) : ?>
            <?php the_content(); ?>
        <?php else : ?>
            <article class="page-content" style="padding: 60px; min-height: 60vh; max-width: 1200px; margin: 0 auto;">
                <h1><?php the_title(); ?></h1>
                <div><?php the_content(); ?></div>
            </article>
        <?php endif; ?>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
