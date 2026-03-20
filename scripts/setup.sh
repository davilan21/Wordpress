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
wp plugin install elementor --activate --path=/var/www/html --allow-root 2>/dev/null || true
wp plugin is-installed elementor --path=/var/www/html --allow-root && echo "Elementor instalado OK"

# Instalar y activar Hello Elementor (tema ligero compatible con Elementor Canvas)
echo "=== Instalando Hello Elementor theme ==="
wp theme install hello-elementor --activate --path=/var/www/html --allow-root 2>/dev/null || true
echo "Tema activo: $(wp theme list --status=active --field=name --path=/var/www/html --allow-root 2>/dev/null)"

# Configuración básica
wp option update blogname "Best Doctors Insurance" --path=/var/www/html --allow-root || true
wp option update blogdescription "First Aid Kit" --path=/var/www/html --allow-root || true

# Desactivar experimentos de Elementor que pueden interferir
wp option update elementor_experiment-e_optimized_css_loading inactive --path=/var/www/html --allow-root 2>/dev/null || true
wp option update elementor_experiment-e_font_icon_svg inactive --path=/var/www/html --allow-root 2>/dev/null || true

# Verificar que el archivo JSON existe
echo "=== Verificando template JSON ==="
if [ -f "/templates/first-aid-kit.json" ]; then
  echo "Template encontrado: /templates/first-aid-kit.json"
  echo "Tamaño: $(wc -c < /templates/first-aid-kit.json) bytes"
else
  echo "ERROR: /templates/first-aid-kit.json no encontrado!"
  ls -la /templates/ 2>/dev/null || echo "Directorio /templates/ no existe"
fi

# Importar template de Elementor
echo "=== Importando template de Elementor ==="
wp eval-file /scripts/import-elementor-template.php --path=/var/www/html --allow-root 2>&1 || {
  echo "Error al importar template, reintentando..."
  sleep 3
  wp eval-file /scripts/import-elementor-template.php --path=/var/www/html --allow-root 2>&1 || echo "FALLO la importacion"
}

# Verificar resultado
echo "=== Verificación final ==="
FRONT_PAGE=$(wp option get page_on_front --path=/var/www/html --allow-root 2>/dev/null)
echo "Front page ID: $FRONT_PAGE"
if [ ! -z "$FRONT_PAGE" ] && [ "$FRONT_PAGE" != "0" ]; then
  TEMPLATE=$(wp post meta get $FRONT_PAGE _wp_page_template --path=/var/www/html --allow-root 2>/dev/null)
  EDIT_MODE=$(wp post meta get $FRONT_PAGE _elementor_edit_mode --path=/var/www/html --allow-root 2>/dev/null)
  DATA_LEN=$(wp post meta get $FRONT_PAGE _elementor_data --path=/var/www/html --allow-root 2>/dev/null | wc -c)
  echo "Page template: $TEMPLATE"
  echo "Elementor edit mode: $EDIT_MODE"
  echo "Elementor data length: $DATA_LEN bytes"
fi

echo ""
echo "=========================================="
echo "  WordPress configurado exitosamente!"
echo "  URL: http://localhost:8080"
echo "  Admin: http://localhost:8080/wp-admin"
echo "  Usuario: admin"
echo "  Contraseña: admin123"
echo "=========================================="
