# 🌱 Los Cocos E-commerce - Instalación Local con Docker

Esta guía te permitirá ejecutar el e-commerce de Vivero Los Cocos en tu máquina local usando Docker Desktop.

## 📋 Requisitos Previos

### Instalación de Docker Desktop

1. **Descargar Docker Desktop:**
   - Windows: https://docs.docker.com/desktop/install/windows-install/
   - macOS: https://docs.docker.com/desktop/install/mac-install/
   - Linux: https://docs.docker.com/desktop/install/linux-install/

2. **Verificar instalación:**
   ```bash
   docker --version
   docker-compose --version
   ```

### Recursos Recomendados

- **RAM:** Mínimo 4GB disponibles para Docker
- **Espacio en disco:** Al menos 2GB libres
- **CPU:** 2 núcleos o más

## 🚀 Instalación Rápida

### Paso 1: Clonar o Descargar el Proyecto

```bash
# Si tienes git instalado
git clone <url-del-repositorio>
cd vivero

# O simplemente descarga todos los archivos en una carpeta
```

### Paso 2: Verificar Archivos Necesarios

Asegúrate de tener estos archivos en tu directorio:

```
vivero/
├── docker-compose.yml
├── nginx.conf
├── nginx-site.conf
├── docker-init.sh
├── Dockerfile.init
├── wp-config.php
├── functions.php
├── style.css
├── index.php
├── header.php
├── footer.php
├── woocommerce.php
├── woocommerce/
│   └── single-product.php
├── import-products.php
├── setup-ecommerce.php
└── INVENTARIO VIVERO LOS COCOS 0.1  - Hierro Soportes.csv
```

### Paso 3: Iniciar los Contenedores

```bash
# Desde el directorio del proyecto
docker-compose up -d
```

### Paso 4: Ejecutar la Configuración Inicial

```bash
# Ejecutar el script de inicialización
docker-compose exec wpcli /usr/local/bin/docker-init.sh
```

### Paso 5: ¡Listo! 🎉

Ahora puedes acceder a:

- **🌐 Sitio web:** http://localhost:8080
- **🔧 Admin WordPress:** http://localhost:8080/wp-admin
  - Usuario: `admin`
  - Contraseña: `loscocos2024`
- **🗄️ phpMyAdmin:** http://localhost:8081
  - Usuario: `root`
  - Contraseña: `root_password_2024`

## 🛠️ Comandos Útiles

### Gestión de Contenedores

```bash
# Ver estado de los contenedores
docker-compose ps

# Ver logs en tiempo real
docker-compose logs -f

# Ver logs de un servicio específico
docker-compose logs -f wordpress

# Reiniciar todos los servicios
docker-compose restart

# Parar todos los servicios
docker-compose stop

# Eliminar contenedores (mantiene datos)
docker-compose down

# Eliminar contenedores y volúmenes (¡CUIDADO: borra todo!)
docker-compose down -v
```

### Comandos de WordPress CLI

```bash
# Acceder al contenedor de WP-CLI
docker-compose exec wpcli bash

# Ejecutar comandos WP-CLI directamente
docker-compose exec wpcli wp --info --allow-root

# Instalar un plugin
docker-compose exec wpcli wp plugin install contact-form-7 --activate --allow-root

# Actualizar WordPress
docker-compose exec wpcli wp core update --allow-root

# Crear un usuario
docker-compose exec wpcli wp user create testuser test@example.com --role=administrator --allow-root
```

### Base de Datos

```bash
# Backup de la base de datos
docker-compose exec db mysqldump -u root -proot_password_2024 loscocos_wp > backup.sql

# Restaurar backup
docker-compose exec -T db mysql -u root -proot_password_2024 loscocos_wp < backup.sql

# Acceder a MySQL directamente
docker-compose exec db mysql -u root -proot_password_2024
```

## 🔧 Configuración Avanzada

### Importar Productos desde CSV

1. Visita: http://localhost:8080/wp-content/themes/loscocos/import-products.php
2. El archivo CSV ya está incluido en el contenedor
3. Haz clic en "Importar Productos" para cargar los soportes de hierro

### Configuración Adicional de E-commerce

1. Visita: http://localhost:8080/wp-content/themes/loscocos/setup-ecommerce.php
2. Ejecuta las configuraciones adicionales para Argentina

### Personalizar el Tema

Los archivos del tema se sincronizan automáticamente:

- Edita `style.css` para cambiar estilos
- Modifica `functions.php` para agregar funcionalidades
- Actualiza `index.php`, `header.php`, `footer.php` para cambiar la estructura

Los cambios se reflejan inmediatamente en http://localhost:8080

## 🐛 Solución de Problemas

### Error: "Puerto ya en uso"

```bash
# Cambiar puertos en docker-compose.yml
# Por ejemplo, cambiar 8080:80 por 8090:80
```

### Error: "No se puede conectar a la base de datos"

```bash
# Verificar que todos los contenedores estén ejecutándose
docker-compose ps

# Reiniciar servicios
docker-compose restart

# Ver logs de la base de datos
docker-compose logs db
```

### Error: "Memoria insuficiente"

1. Aumenta la memoria asignada a Docker Desktop:
   - Docker Desktop → Settings → Resources → Memory
   - Asigna al menos 4GB

### Error: "Archivos no encontrados"

```bash
# Verificar que todos los archivos estén en el directorio correcto
ls -la

# Verificar montajes de volúmenes
docker-compose config
```

### Resetear Completamente

```bash
# Eliminar todo y empezar de nuevo
docker-compose down -v
docker system prune -a
docker-compose up -d
docker-compose exec wpcli /usr/local/bin/docker-init.sh
```

## 📊 Monitoreo y Logs

### Ver Logs de Nginx

```bash
docker-compose exec nginx tail -f /var/log/nginx/access.log
docker-compose exec nginx tail -f /var/log/nginx/error.log
```

### Ver Logs de WordPress

```bash
docker-compose exec wordpress tail -f /var/log/php_errors.log
```

### Verificar Performance

```bash
# Uso de recursos
docker stats

# Espacio en disco
docker system df
```

## 🔐 Seguridad en Desarrollo

### Cambiar Contraseñas por Defecto

```bash
# Cambiar contraseña de admin de WordPress
docker-compose exec wpcli wp user update admin --user_pass=nueva_contraseña --allow-root

# Cambiar contraseñas en docker-compose.yml para producción
```

### Acceso Remoto (Opcional)

Para acceder desde otras máquinas en tu red local:

1. Encuentra tu IP local: `ipconfig` (Windows) o `ifconfig` (Mac/Linux)
2. Accede usando: `http://TU_IP:8080`

## 📝 Desarrollo y Personalización

### Estructura del Proyecto

```
vivero/
├── docker-compose.yml          # Configuración de contenedores
├── nginx.conf                  # Configuración principal de Nginx
├── nginx-site.conf            # Configuración del sitio
├── docker-init.sh             # Script de inicialización
├── wp-config.php              # Configuración de WordPress
├── functions.php              # Funciones del tema
├── style.css                  # Estilos del tema
├── index.php                  # Plantilla principal
├── header.php                 # Cabecera del sitio
├── footer.php                 # Pie del sitio
├── woocommerce.php           # Plantilla de WooCommerce
├── woocommerce/              # Plantillas específicas de WooCommerce
├── import-products.php       # Importador de productos
├── setup-ecommerce.php       # Configurador de e-commerce
└── inventory.csv             # Inventario de productos
```

### Agregar Nuevas Funcionalidades

1. **Nuevos Plugins:**
   ```bash
   docker-compose exec wpcli wp plugin install nombre-plugin --activate --allow-root
   ```

2. **Nuevas Plantillas:**
   - Crea archivos PHP en el directorio raíz
   - Se sincronizarán automáticamente con el contenedor

3. **Nuevos Productos:**
   - Usa el importador: http://localhost:8080/wp-content/themes/loscocos/import-products.php
   - O crea productos manualmente en el admin

## 🎯 Próximos Pasos

Una vez que tengas todo funcionando:

1. **Personaliza el diseño** editando `style.css`
2. **Agrega más productos** usando el importador
3. **Configura métodos de pago** para Argentina
4. **Personaliza emails** de WooCommerce
5. **Optimiza para SEO** con plugins adicionales

## 🆘 Soporte

Si tienes problemas:

1. **Revisa los logs:** `docker-compose logs -f`
2. **Verifica el estado:** `docker-compose ps`
3. **Reinicia servicios:** `docker-compose restart`
4. **Consulta la documentación oficial:**
   - [Docker Compose](https://docs.docker.com/compose/)
   - [WordPress](https://wordpress.org/support/)
   - [WooCommerce](https://woocommerce.com/documentation/)

---

## 🌟 ¡Disfruta desarrollando con Los Cocos!

Este entorno te permite desarrollar y probar el e-commerce completo de manera local, con todas las funcionalidades de un sitio en producción. 