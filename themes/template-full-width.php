<?php
/**
 * Template Name: Elementor Full Width (Con Header/Footer)
 * Description: Plantilla ancho completo con header y footer del tema, contenido editable en Elementor
 */
get_header(); ?>

<main class="site-main elementor-full-width">
    <?php while (have_posts()) : the_post(); ?>
        <?php the_content(); ?>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
