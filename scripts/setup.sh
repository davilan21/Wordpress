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

# Activar el tema custom (Best Doctors Insurance)
echo "=== Activando tema custom ==="
wp theme activate custom-theme --path=/var/www/html --allow-root || true

# Configuración básica
wp option update blogname "Best Doctors Insurance" --path=/var/www/html --allow-root || true
wp option update blogdescription "First Aid Kit - International Health Insurance" --path=/var/www/html --allow-root || true
wp option update timezone_string "America/Bogota" --path=/var/www/html --allow-root || true

# Crear página de inicio si no existe
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
  wp option update page_on_front $PAGE_ID --path=/var/www/html --allow-root || true
  wp option update show_on_front page --path=/var/www/html --allow-root || true
  echo "Página de inicio configurada: $PAGE_ID"
fi

echo ""
echo "=========================================="
echo "  WordPress configurado exitosamente!"
echo "  URL: http://localhost:8080"
echo "  Admin: http://localhost:8080/wp-admin"
echo "  Usuario: admin"
echo "  Contraseña: admin123"
echo "=========================================="
