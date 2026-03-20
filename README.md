# WordPress + Elementor con Docker

## Inicio rápido

```bash
docker compose up -d
```

Espera ~30 segundos y abre: **http://localhost:8080**

- **Panel admin:** http://localhost:8080/wp-admin
- **Usuario:** admin
- **Contraseña:** admin123

## Estructura

```
Wordpress/
├── docker-compose.yml    # Configuración Docker
├── scripts/
│   ├── setup.sh          # Auto-instalación WordPress + Elementor
│   └── import-figma.sh   # Guía para importar desde Figma
├── themes/               # Tu tema personalizado (montado en WP)
└── plugins/              # Plugins adicionales (montado en WP)
```

## Comandos útiles

```bash
# Iniciar
docker compose up -d

# Ver logs
docker compose logs -f wordpress

# Detener
docker compose down

# Detener y borrar datos
docker compose down -v

# Ejecutar WP CLI
docker compose run --rm wpcli wp --info
```

## Flujo Figma → Elementor

1. Comparte capturas o el link de Figma con Claude
2. Claude genera el template JSON de Elementor
3. En WordPress: Elementor → Templates → Importar
4. ¡Listo!
