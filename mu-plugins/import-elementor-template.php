<?php
/**
 * Auto-import Elementor template into the homepage.
 * This mu-plugin runs once, imports the template, then deletes its lock to avoid re-running.
 */

add_action('init', function () {
    // Only run once
    if (get_option('bdi_elementor_imported')) {
        return;
    }

    // Check Elementor is active
    if (!defined('ELEMENTOR_VERSION')) {
        return;
    }

    // Find or create the front page
    $front_page_id = get_option('page_on_front');

    if (!$front_page_id) {
        $page = get_page_by_title('Inicio');
        if ($page) {
            $front_page_id = $page->ID;
        }
    }

    if (!$front_page_id) {
        // Create the page
        $front_page_id = wp_insert_post(array(
            'post_title'  => 'Inicio',
            'post_type'   => 'page',
            'post_status' => 'publish',
        ));
    }

    if (!$front_page_id || is_wp_error($front_page_id)) {
        return;
    }

    // Read the template JSON from multiple possible paths
    $possible_paths = array(
        '/templates/first-aid-kit.json',
        ABSPATH . '../templates/first-aid-kit.json',
        WP_CONTENT_DIR . '/../../templates/first-aid-kit.json',
        '/var/www/html/wp-content/mu-plugins/../../../templates/first-aid-kit.json',
    );

    $template_json = false;
    foreach ($possible_paths as $path) {
        $real = realpath($path);
        if ($real && file_exists($real)) {
            $template_json = file_get_contents($real);
            break;
        }
    }

    // If file not found, use inline template
    if (!$template_json) {
        // Fallback: read from the mu-plugins directory
        $fallback = dirname(__FILE__) . '/first-aid-kit-template.json';
        if (file_exists($fallback)) {
            $template_json = file_get_contents($fallback);
        }
    }

    if (!$template_json) {
        error_log('BDI: Template JSON file not found');
        return;
    }

    // Validate JSON
    $data = json_decode($template_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('BDI: Invalid template JSON: ' . json_last_error_msg());
        return;
    }

    // Set Elementor data
    update_post_meta($front_page_id, '_elementor_data', wp_slash($template_json));
    update_post_meta($front_page_id, '_elementor_edit_mode', 'builder');
    update_post_meta($front_page_id, '_elementor_template_type', 'wp-page');
    update_post_meta($front_page_id, '_elementor_version', ELEMENTOR_VERSION);
    update_post_meta($front_page_id, '_wp_page_template', 'elementor_canvas');

    // Ensure it's the front page
    update_option('show_on_front', 'page');
    update_option('page_on_front', $front_page_id);

    // Clear Elementor CSS cache
    if (class_exists('\Elementor\Plugin')) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
    }

    // Mark as imported so we don't run again
    update_option('bdi_elementor_imported', true);

    error_log('BDI: Elementor template imported successfully to page ' . $front_page_id);
});
