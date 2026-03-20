#!/bin/bash
set -e

echo "=== Configurando WordPress ==="

# Esperar a que WordPress esté listo
until wp core is-installed --path=/var/www/html --allow-root 2>/dev/null || wp core install \
  --url="http://localhost:8080" \
  --title="Best Doctors Insurance" \
  --admin_user="admin" \
  --admin_password="admin123" \
  --admin_email="admin@example.com" \
  --path=/var/www/html \
  --allow-root 2>/dev/null; do
  echo "Esperando que WordPress esté listo..."
  sleep 5
done

echo "=== WordPress instalado ==="

# Instalar y activar Elementor
echo "=== Instalando Elementor ==="
wp plugin install elementor --activate --path=/var/www/html --allow-root || true

# Instalar Hello Elementor theme (compatible con Elementor Canvas)
echo "=== Instalando Hello Elementor theme ==="
wp theme install hello-elementor --activate --path=/var/www/html --allow-root || true

# Configuración básica
wp option update blogdescription "First Aid Kit - International Health Insurance" --path=/var/www/html --allow-root || true
wp option update timezone_string "America/Bogota" --path=/var/www/html --allow-root || true

# Crear página de inicio
echo "=== Creando página de inicio ==="
PAGE_ID=$(wp post list --post_type=page --name="inicio" --field=ID --path=/var/www/html --allow-root 2>/dev/null | head -1)

if [ -z "$PAGE_ID" ]; then
  PAGE_ID=$(wp post create \
    --post_type=page \
    --post_title="Inicio" \
    --post_status=publish \
    --path=/var/www/html \
    --allow-root --porcelain 2>/dev/null)
  echo "Página creada con ID: $PAGE_ID"
fi

if [ ! -z "$PAGE_ID" ]; then
  # Establecer como página de inicio
  wp option update page_on_front $PAGE_ID --path=/var/www/html --allow-root || true
  wp option update show_on_front page --path=/var/www/html --allow-root || true

  # Configurar para usar Elementor Canvas (sin header/footer del tema)
  wp post meta update $PAGE_ID _wp_page_template elementor_canvas --path=/var/www/html --allow-root || true

  # Importar template de Elementor
  echo "=== Importando template de Elementor ==="

  # Leer template JSON
  TEMPLATE_FILE=""
  if [ -f "/templates/first-aid-kit.json" ]; then
    TEMPLATE_FILE="/templates/first-aid-kit.json"
  elif [ -f "/var/www/html/wp-content/mu-plugins/first-aid-kit-template.json" ]; then
    TEMPLATE_FILE="/var/www/html/wp-content/mu-plugins/first-aid-kit-template.json"
  fi

  if [ ! -z "$TEMPLATE_FILE" ]; then
    echo "Template encontrado: $TEMPLATE_FILE"

    # Importar via WP-CLI eval
    wp eval "
      \$json = file_get_contents('$TEMPLATE_FILE');
      \$page_id = $PAGE_ID;
      update_post_meta(\$page_id, '_elementor_data', wp_slash(\$json));
      update_post_meta(\$page_id, '_elementor_edit_mode', 'builder');
      update_post_meta(\$page_id, '_elementor_template_type', 'wp-page');
      update_post_meta(\$page_id, '_elementor_version', defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : '3.25.0');
      update_post_meta(\$page_id, '_wp_page_template', 'elementor_canvas');
      echo 'Template importado en pagina ' . \$page_id;
    " --path=/var/www/html --allow-root || true

    # Limpiar cache de Elementor CSS
    wp eval "
      if (class_exists('\Elementor\Plugin')) {
        \Elementor\Plugin::\$instance->files_manager->clear_cache();
        echo 'Cache limpiado';
      }
    " --path=/var/www/html --allow-root || true
  else
    echo "ADVERTENCIA: Template JSON no encontrado"
  fi
fi

# Desactivar opción de Elementor que bloquea el renderizado
wp option update elementor_experiment-e_optimized_css_loading inactive --path=/var/www/html --allow-root || true

echo ""
echo "=========================================="
echo "  WordPress configurado exitosamente!"
echo "  URL: http://localhost:8080"
echo "  Admin: http://localhost:8080/wp-admin"
echo "  Usuario: admin"
echo "  Contraseña: admin123"
echo "=========================================="
