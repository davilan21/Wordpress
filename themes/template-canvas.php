<?php
/**
 * Template Name: Elementor Canvas (Sin Header/Footer)
 * Description: Plantilla limpia sin header ni footer para diseñar todo desde Elementor
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('elementor-canvas'); ?>>
<?php wp_body_open(); ?>

<?php while (have_posts()) : the_post(); ?>
    <?php the_content(); ?>
<?php endwhile; ?>

<?php wp_footer(); ?>
</body>
</html>
