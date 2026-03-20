#!/bin/bash
set -e

echo "=== Configurando WordPress ==="

# Esperar a que WordPress esté listo
until wp core is-installed --path=/var/www/html --allow-root 2>/dev/null || wp core install \
  --url="http://localhost:8080" \
  --title="Mi Sitio WordPress" \
  --admin_user="admin" \
  --admin_password="admin123" \
  --admin_email="admin@example.com" \
  --path=/var/www/html \
  --allow-root 2>/dev/null; do
  echo "Esperando que WordPress esté listo..."
  sleep 5
done

echo "=== WordPress instalado ==="

# Instalar Elementor
echo "=== Instalando Elementor ==="
wp plugin install elementor --activate --path=/var/www/html --allow-root || true

# Instalar Hello Elementor theme (tema recomendado para Elementor)
echo "=== Instalando Hello Elementor theme ==="
wp theme install hello-elementor --activate --path=/var/www/html --allow-root || true

# Instalar plugin adicional para importar diseños
echo "=== Instalando plugins adicionales ==="
wp plugin install envato-elements --activate --path=/var/www/html --allow-root || true

# Configuración básica
wp option update blogdescription "Sitio creado con Elementor desde Figma" --path=/var/www/html --allow-root || true
wp option update timezone_string "America/Bogota" --path=/var/www/html --allow-root || true

# Crear una página de inicio de ejemplo
wp post create \
  --post_type=page \
  --post_title="Inicio" \
  --post_status=publish \
  --path=/var/www/html \
  --allow-root || true

# Establecer la página como página de inicio
PAGE_ID=$(wp post list --post_type=page --name="inicio" --field=ID --path=/var/www/html --allow-root 2>/dev/null | head -1)
if [ ! -z "$PAGE_ID" ]; then
  wp option update page_on_front $PAGE_ID --path=/var/www/html --allow-root || true
  wp option update show_on_front page --path=/var/www/html --allow-root || true
fi

echo ""
echo "=========================================="
echo "  WordPress configurado exitosamente!"
echo "  URL: http://localhost:8080"
echo "  Admin: http://localhost:8080/wp-admin"
echo "  Usuario: admin"
echo "  Contraseña: admin123"
echo "=========================================="
