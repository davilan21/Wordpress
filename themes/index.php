<?php get_header(); ?>

<main class="site-main" style="padding: 60px; min-height: 60vh;">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article>
            <?php if (!bdi_is_elementor_page()) : ?>
                <h1><?php the_title(); ?></h1>
            <?php endif; ?>
            <div><?php the_content(); ?></div>
        </article>
    <?php endwhile; else : ?>
        <p>No content found.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
