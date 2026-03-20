<?php
/**
 * Import Elementor template into the homepage
 * Run via: wp eval-file /scripts/import-elementor-template.php --path=/var/www/html --allow-root
 */

echo "=== Importando template de Elementor ===\n";

// 1. Find or create the front page
$front_page_id = get_option('page_on_front');

if (!$front_page_id) {
    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'name' => 'inicio',
        'numberposts' => 1,
    ));
    if (!empty($pages)) {
        $front_page_id = $pages[0]->ID;
    }
}

if (!$front_page_id) {
    $front_page_id = wp_insert_post(array(
        'post_title'  => 'Inicio',
        'post_type'   => 'page',
        'post_status' => 'publish',
        'post_content' => '',
    ));
    echo "Pagina creada con ID: $front_page_id\n";
}

if (!$front_page_id || is_wp_error($front_page_id)) {
    echo "ERROR: No se pudo crear/encontrar la pagina\n";
    exit(1);
}

echo "Pagina ID: $front_page_id\n";

// 2. Read the template JSON
$template_json = false;
$paths = array(
    '/templates/first-aid-kit.json',
    dirname(__FILE__) . '/../templates/first-aid-kit.json',
);

foreach ($paths as $path) {
    if (file_exists($path)) {
        $template_json = file_get_contents($path);
        echo "Template encontrado: $path\n";
        break;
    }
}

if (!$template_json) {
    echo "ERROR: No se encontro el archivo JSON del template\n";
    foreach ($paths as $p) {
        echo "  Intentado: $p (existe: " . (file_exists($p) ? 'si' : 'no') . ")\n";
    }
    exit(1);
}

// 3. Validate JSON
$data = json_decode($template_json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "ERROR: JSON invalido: " . json_last_error_msg() . "\n";
    exit(1);
}
echo "Template cargado: " . count($data) . " secciones\n";

// 4. Write Elementor meta - wp_slash is CRITICAL for update_post_meta with JSON
update_post_meta($front_page_id, '_elementor_data', wp_slash($template_json));
update_post_meta($front_page_id, '_elementor_edit_mode', 'builder');
update_post_meta($front_page_id, '_elementor_template_type', 'wp-page');
update_post_meta($front_page_id, '_elementor_version', defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : '3.25.0');
update_post_meta($front_page_id, '_wp_page_template', 'elementor_canvas');

// 5. Make sure post content is empty so Elementor takes over
wp_update_post(array(
    'ID' => $front_page_id,
    'post_content' => '',
    'post_status' => 'publish',
));

// 6. Set as front page
update_option('show_on_front', 'page');
update_option('page_on_front', $front_page_id);

// 7. Clear Elementor CSS cache so it regenerates
delete_post_meta($front_page_id, '_elementor_css');
$upload_dir = wp_upload_dir();
$css_path = $upload_dir['basedir'] . '/elementor/css/post-' . $front_page_id . '.css';
if (file_exists($css_path)) {
    unlink($css_path);
}

// Also try clearing via Elementor API
if (class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->files_manager) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
    echo "Cache de Elementor limpiado\n";
}

// 8. Verify
$saved = get_post_meta($front_page_id, '_elementor_data', true);
$saved_template = get_post_meta($front_page_id, '_wp_page_template', true);
echo "Verificacion:\n";
echo "  _elementor_data guardado: " . (strlen($saved) > 0 ? 'SI (' . strlen($saved) . ' bytes)' : 'NO') . "\n";
echo "  _wp_page_template: $saved_template\n";
echo "  _elementor_edit_mode: " . get_post_meta($front_page_id, '_elementor_edit_mode', true) . "\n";

echo "\nSUCCESS: Template importado en pagina $front_page_id\n";
echo "URL: http://localhost:8080\n";
