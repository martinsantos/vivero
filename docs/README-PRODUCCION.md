# 🌱 Los Cocos E-commerce - GUÍA DE PRODUCCIÓN

## 🚀 E-commerce Completamente Funcional

Este proyecto es un **e-commerce completo y listo para producción** para el Vivero Los Cocos en Mendoza, Argentina. Incluye WordPress + WooCommerce con todas las funcionalidades necesarias para vender online.

---

## ✅ CARACTERÍSTICAS IMPLEMENTADAS

### 🛒 **E-commerce Completo**
- ✅ **WordPress 6.0+** con tema personalizado
- ✅ **WooCommerce** configurado para Argentina
- ✅ **Catálogo de productos** con imágenes SVG automáticas
- ✅ **Carrito de compras** funcional
- ✅ **Checkout completo** con múltiples métodos de pago
- ✅ **Gestión de inventario** en tiempo real
- ✅ **Sistema de envíos** configurado para Mendoza
- ✅ **Emails automáticos** de confirmación y seguimiento

### 💳 **Métodos de Pago (Argentina)**
- ✅ **Transferencia Bancaria** - Con datos del Banco Nación
- ✅ **Pago contra entrega** - Efectivo al recibir
- ✅ **Pago con cheque** - Para clientes corporativos
- 🔄 **MercadoPago** - Listo para configurar (requiere cuenta)

### 🚚 **Sistema de Envíos**
- ✅ **Envío gratis** para Mendoza desde $50.000
- ✅ **Zonas de envío** configuradas
- ✅ **Cálculo automático** de costos
- ✅ **Seguimiento** de pedidos

### 📦 **Gestión de Productos**
- ✅ **Importación automática** desde CSV del inventario real
- ✅ **Categorización inteligente** por tipo de producto
- ✅ **Imágenes SVG** generadas automáticamente
- ✅ **Control de stock** en tiempo real
- ✅ **Precios dinámicos** con ofertas y descuentos

---

## 🚀 INSTALACIÓN RÁPIDA (5 MINUTOS)

### **Opción 1: Instalación Automática (Recomendada)**

```bash
# 1. Clonar el repositorio
git clone [tu-repo] loscocos-ecommerce
cd loscocos-ecommerce

# 2. Ejecutar instalación automática
chmod +x quick-start.sh
./quick-start.sh
```

**¡Eso es todo!** El script automático:
- ✅ Verifica Docker y dependencias
- ✅ Inicia contenedores (WordPress, MySQL, phpMyAdmin)
- ✅ Instala y configura WordPress + WooCommerce
- ✅ Configura el tema Los Cocos
- ✅ Crea productos de ejemplo
- ✅ Configura métodos de pago y envío para Argentina

### **Opción 2: Instalación Manual**

```bash
# 1. Iniciar contenedores
docker-compose up -d

# 2. Esperar 30 segundos y ejecutar configuración
docker-compose exec wpcli /usr/local/bin/docker-init.sh
```

---

## 📋 ACCESO AL SISTEMA

### **URLs Principales**
- 🌐 **Sitio web**: http://localhost:8080
- 🔧 **Admin WordPress**: http://localhost:8080/wp-admin
- 🛒 **Tienda**: http://localhost:8080/shop
- 🗄️ **phpMyAdmin**: http://localhost:8081

### **Credenciales de Acceso**
- 👤 **Usuario Admin**: `admin`
- 🔑 **Contraseña**: `loscocos2024`
- 📧 **Email**: `admin@loscocos.com.ar`

### **Base de Datos**
- 🗄️ **Host**: `localhost:3306`
- 📊 **Base de datos**: `loscocos_wp`
- 👤 **Usuario**: `loscocos_user`
- 🔑 **Contraseña**: `loscocos_2024`

---

## ⚙️ CONFIGURACIÓN POST-INSTALACIÓN

### **1. Importar Productos Reales**
```
http://localhost:8080/wp-content/themes/theme-loscocos/import-products-auto.php
```
- Importa automáticamente desde el CSV del inventario
- Crea categorías inteligentemente
- Genera imágenes SVG para cada producto
- Configura precios y stock

### **2. Configuración Completa de E-commerce**
```
http://localhost:8080/wp-content/themes/theme-loscocos/setup-ecommerce-complete.php
```
- Configura todos los aspectos del e-commerce
- Métodos de pago para Argentina
- Zonas de envío para Mendoza
- Emails automáticos
- Cupones de descuento

### **3. Configurar MercadoPago (Opcional)**
1. Ir a **WooCommerce → Configuración → Pagos**
2. Instalar plugin oficial de MercadoPago
3. Configurar credenciales de tu cuenta MercadoPago
4. Activar métodos de pago (tarjetas, Pago Fácil, etc.)

---

## 🎨 PERSONALIZACIÓN

### **Cambiar Logo y Colores**
1. Ir a **Apariencia → Personalizar**
2. Subir logo del vivero
3. Cambiar colores principales
4. Configurar información de contacto

### **Editar Tema**
Los archivos del tema están en:
```
wp-content/themes/theme-loscocos/
├── style.css          # Estilos principales
├── functions.php      # Funcionalidades PHP
├── index.php          # Página principal
├── woocommerce.php    # Template de tienda
└── header.php         # Cabecera del sitio
```

### **Agregar Productos Manualmente**
1. Ir a **Productos → Añadir nuevo**
2. Completar información del producto
3. Asignar categoría
4. Configurar precio y stock
5. Subir imágenes (opcional, se generan automáticamente)

---

## 📊 GESTIÓN DE LA TIENDA

### **Panel de Control WooCommerce**
- 📈 **Informes**: Ventas, productos más vendidos, clientes
- 📦 **Pedidos**: Gestión completa de pedidos
- 👥 **Clientes**: Base de datos de clientes
- 🎫 **Cupones**: Crear ofertas y descuentos
- ⚙️ **Configuración**: Ajustar todos los aspectos

### **Gestión de Inventario**
- ✅ Control automático de stock
- ⚠️ Alertas de stock bajo (menos de 5 unidades)
- 📧 Notificaciones por email
- 🔄 Actualización en tiempo real

### **Procesamiento de Pedidos**
1. **Pedido recibido** → Email automático al cliente
2. **Pago confirmado** → Cambiar estado a "Procesando"
3. **Envío preparado** → Cambiar a "Enviado"
4. **Entregado** → Cambiar a "Completado"

---

## 🔧 COMANDOS ÚTILES

### **Docker**
```bash
# Ver estado de contenedores
docker-compose ps

# Ver logs en tiempo real
docker-compose logs -f

# Reiniciar servicios
docker-compose restart

# Detener servicios
docker-compose stop

# Eliminar todo (¡CUIDADO!)
docker-compose down -v

# Backup de base de datos
docker-compose exec db mysqldump -u loscocos_user -ploscocos_2024 loscocos_wp > backup.sql
```

### **WordPress CLI**
```bash
# Ejecutar comandos WP-CLI
docker-compose exec wpcli wp --help

# Actualizar WordPress
docker-compose exec wpcli wp core update --allow-root

# Instalar plugin
docker-compose exec wpcli wp plugin install [plugin-name] --activate --allow-root

# Limpiar caché
docker-compose exec wpcli wp cache flush --allow-root
```

---

## 🚀 DEPLOYMENT A PRODUCCIÓN

### **Preparar para Hosting Real**

1. **Cambiar URLs en wp-config.php**:
```php
define('WP_HOME', 'https://tudominio.com');
define('WP_SITEURL', 'https://tudominio.com');
```

2. **Configurar SSL**:
```php
define('FORCE_SSL_ADMIN', true);
```

3. **Optimizar para producción**:
```php
define('WP_DEBUG', false);
define('WP_CACHE', true);
```

### **Hosting Recomendado para Argentina**
- **SiteGround** - Hosting WordPress optimizado
- **Hostinger** - Económico y confiable
- **DonWeb** - Hosting argentino
- **NIC Argentina** - Hosting local

### **Configuración de Dominio**
1. Registrar dominio `.com.ar` o `.com`
2. Configurar DNS hacia el hosting
3. Instalar certificado SSL
4. Configurar redirects HTTP → HTTPS

---

## 📈 OPTIMIZACIÓN Y SEO

### **SEO Básico Incluido**
- ✅ URLs amigables configuradas
- ✅ Meta tags para productos
- ✅ Schema.org para e-commerce
- ✅ Sitemap XML automático
- ✅ Open Graph para redes sociales

### **Plugins Recomendados**
```bash
# SEO
wp plugin install wordpress-seo --activate

# Seguridad
wp plugin install wordfence --activate

# Backup
wp plugin install backwpup --activate

# Performance
wp plugin install w3-total-cache --activate

# Analytics
wp plugin install google-analytics-for-wordpress --activate
```

---

## 📞 SOPORTE Y MANTENIMIENTO

### **Actualizaciones Automáticas**
- ✅ WordPress: Actualizaciones menores automáticas
- ⚠️ WooCommerce: Revisar mensualmente
- ⚠️ Tema: Mantener respaldos antes de actualizar

### **Backup Automático**
```bash
# Crear backup completo
./backup-complete.sh

# Restaurar desde backup
./restore-backup.sh backup-fecha.tar.gz
```

### **Monitoreo**
- 📊 **Google Analytics**: Tráfico y conversiones
- 📈 **Google Search Console**: SEO y indexación
- 🔍 **Hotjar**: Comportamiento de usuarios
- 📧 **Uptime monitoring**: Disponibilidad del sitio

---

## 🎯 MÉTRICAS DE ÉXITO

### **KPIs a Monitorear**
- 📈 **Tasa de conversión**: Meta >2.5%
- 🛒 **Abandono de carrito**: Meta <70%
- 💰 **Valor promedio del pedido**: Incrementar con cross-selling
- 📱 **Tráfico móvil**: >60% del total
- ⭐ **Satisfacción del cliente**: Reviews y ratings

### **Herramientas de Análisis**
- **WooCommerce Analytics**: Incluido en el plugin
- **Google Analytics 4**: Configurar para e-commerce
- **Facebook Pixel**: Para remarketing
- **Google Tag Manager**: Gestión de tags

---

## 🆘 SOLUCIÓN DE PROBLEMAS

### **Problemas Comunes**

**1. Contenedores no inician**
```bash
docker-compose down -v
docker system prune -f
docker-compose up -d
```

**2. Sitio no carga**
```bash
# Verificar logs
docker-compose logs wordpress

# Reiniciar servicios
docker-compose restart
```

**3. Base de datos no conecta**
```bash
# Verificar estado de MySQL
docker-compose logs db

# Recrear base de datos
docker-compose down -v
docker-compose up -d
```

**4. Productos no se importan**
- Verificar que el archivo CSV esté en la ubicación correcta
- Comprobar permisos de archivos
- Revisar logs de PHP en el contenedor

### **Logs Importantes**
```bash
# Logs de WordPress
docker-compose logs wordpress

# Logs de base de datos
docker-compose logs db

# Logs de WP-CLI
docker-compose logs wpcli
```

---

## 📚 RECURSOS ADICIONALES

### **Documentación**
- 📖 [WordPress Codex](https://codex.wordpress.org/)
- 🛒 [WooCommerce Docs](https://docs.woocommerce.com/)
- 🐳 [Docker Compose](https://docs.docker.com/compose/)
- 🇦🇷 [MercadoPago Developers](https://www.mercadopago.com.ar/developers/)

### **Comunidades**
- 💬 [WordPress Argentina](https://es-ar.wordpress.org/)
- 🛒 [WooCommerce Community](https://wordpress.org/support/plugin/woocommerce/)
- 🐳 [Docker Community](https://www.docker.com/community)

---

## 🎉 ¡FELICITACIONES!

Tu **Vivero Los Cocos** ahora tiene un **e-commerce completamente funcional** con:

- ✅ **Tienda online profesional** lista para vender
- ✅ **Gestión completa de productos** e inventario
- ✅ **Métodos de pago** configurados para Argentina
- ✅ **Sistema de envíos** optimizado para Mendoza
- ✅ **Administración fácil** desde panel WordPress
- ✅ **Escalable** para crecimiento futuro
- ✅ **SEO optimizado** para posicionamiento
- ✅ **Responsive** para móviles y tablets

**¡Comienza a vender online hoy mismo!** 🚀

---

*Desarrollado con ❤️ para Vivero Los Cocos - Mendoza, Argentina*

**Versión**: 2.0.0 - Producción  
**Fecha**: Enero 2025  
**Soporte**: admin@loscocos.com.ar