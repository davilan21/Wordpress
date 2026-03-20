<?php
/**
 * Import Elementor template into the homepage
 * Run via: wp eval-file /scripts/import-elementor-template.php --path=/var/www/html --allow-root
 */

// Find the homepage
$front_page_id = get_option('page_on_front');

if (!$front_page_id) {
    // Try to find the "Inicio" page
    $page = get_page_by_title('Inicio');
    if ($page) {
        $front_page_id = $page->ID;
    }
}

if (!$front_page_id) {
    // Try any page
    $pages = get_pages(array('number' => 1));
    if (!empty($pages)) {
        $front_page_id = $pages[0]->ID;
    }
}

if (!$front_page_id) {
    echo "ERROR: No front page found.\n";
    exit(1);
}

echo "Found front page ID: $front_page_id\n";

// Read the template JSON
$template_path = '/scripts/../templates/first-aid-kit.json';
if (!file_exists($template_path)) {
    $template_path = '/var/www/html/wp-content/themes/custom-theme/../../../templates/first-aid-kit.json';
}

// Try multiple paths
$possible_paths = array(
    '/templates/first-aid-kit.json',
    '/scripts/../templates/first-aid-kit.json',
    dirname(__FILE__) . '/../templates/first-aid-kit.json',
);

$template_json = false;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        $template_json = file_get_contents($path);
        echo "Template found at: $path\n";
        break;
    }
}

if (!$template_json) {
    echo "ERROR: Template file not found. Tried paths:\n";
    foreach ($possible_paths as $path) {
        echo "  - $path\n";
    }
    exit(1);
}

// Validate JSON
$template_data = json_decode($template_json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "ERROR: Invalid JSON: " . json_last_error_msg() . "\n";
    exit(1);
}

echo "Template loaded with " . count($template_data) . " sections\n";

// Set Elementor data on the page
update_post_meta($front_page_id, '_elementor_data', $template_json);
update_post_meta($front_page_id, '_elementor_edit_mode', 'builder');
update_post_meta($front_page_id, '_elementor_template_type', 'wp-page');
update_post_meta($front_page_id, '_elementor_version', '3.35.7');
update_post_meta($front_page_id, '_wp_page_template', 'template-canvas.php');

// Clear Elementor CSS cache
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
    echo "Elementor cache cleared.\n";
}

// Update the page to ensure it's published
wp_update_post(array(
    'ID' => $front_page_id,
    'post_status' => 'publish',
));

// Ensure front page settings
update_option('show_on_front', 'page');
update_option('page_on_front', $front_page_id);

echo "SUCCESS: Elementor template imported to page $front_page_id\n";
echo "Visit http://localhost:8080 to see the page.\n";
echo "Edit with Elementor: http://localhost:8080/wp-admin/post.php?post=$front_page_id&action=elementor\n";
