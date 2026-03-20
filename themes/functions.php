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

    // Elementor full width support
    add_theme_support('elementor-default-template');
}
add_action('after_setup_theme', 'bdi_theme_setup');

// Register Elementor theme locations (for Elementor Pro header/footer builder)
function bdi_register_elementor_locations($elementor_theme_manager) {
    $elementor_theme_manager->register_all_core_location();
}
add_action('elementor/theme/register_locations', 'bdi_register_elementor_locations');

// Add Elementor support to all post types
function bdi_add_elementor_cpt_support() {
    $cpt_support = get_option('elementor_cpt_support', ['page', 'post']);
    if (!in_array('page', $cpt_support)) {
        $cpt_support[] = 'page';
        update_option('elementor_cpt_support', $cpt_support);
    }
}
add_action('after_switch_theme', 'bdi_add_elementor_cpt_support');

// Disable default colors and fonts in Elementor to use theme's
function bdi_elementor_defaults() {
    update_option('elementor_disable_color_schemes', 'yes');
    update_option('elementor_disable_typography_schemes', 'yes');
}
add_action('after_switch_theme', 'bdi_elementor_defaults');

// Check if current page is built with Elementor
function bdi_is_elementor_page() {
    if (!class_exists('\Elementor\Plugin')) {
        return false;
    }
    $post_id = get_the_ID();
    if ($post_id) {
        return \Elementor\Plugin::$instance->documents->get($post_id)->is_built_with_elementor();
    }
    return false;
}
