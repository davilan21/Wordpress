<?php
/**
 * Best Doctors Insurance Theme Functions
 */

// Enqueue styles
function bdi_enqueue_styles() {
    wp_enqueue_style('inter-font', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap', array(), null);
    wp_enqueue_style('bdi-style', get_stylesheet_uri(), array('inter-font'), '1.0');
}
add_action('wp_enqueue_scripts', 'bdi_enqueue_styles');

// Theme support
function bdi_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
}
add_action('after_setup_theme', 'bdi_theme_setup');
