# 👥 RESUMEN DE USUARIOS - viveroloscocos.com.ar

**Fecha:** 20 de Octubre 2025  
**Sitio:** https://viveroloscocos.com.ar

---

## 📋 USUARIO ACTUAL

### Usuario Principal

```
ID:              1
Usuario:         admin
Email:           santosma@gmail.com
Rol:             Administrator
Fecha Registro:  2025-09-11 18:04:45
Estado:          ✅ Activo
```

> ⚠️ **IMPORTANTE:** Las contraseñas están hasheadas con bcrypt y NO se pueden recuperar. Solo se pueden resetear.

---

## 🔧 CÓMO EDITAR USUARIOS

### Opción 1: Script Automatizado (Más Fácil) ✅

He creado el script `manage_users.sh` para facilitar la gestión:

```bash
# Ver todos los usuarios
./manage_users.sh list

# Ver información detallada
./manage_users.sh info admin

# Crear nuevo usuario
./manage_users.sh create tienda tienda@vivero.com shop_manager "Pass123!"

# Cambiar contraseña
./manage_users.sh password admin "NuevaPass123!"

# Cambiar email
./manage_users.sh email admin nuevo@email.com

# Cambiar rol
./manage_users.sh role admin shop_manager

# Generar contraseña segura
./manage_users.sh generate 20

# Ver ayuda completa
./manage_users.sh help
```

### Opción 2: Panel de WordPress

```
1. Ir a: https://viveroloscocos.com.ar/wp-admin/
2. Login con: admin / [tu contraseña]
3. Ir a: Usuarios → Todos los usuarios
4. Click en el usuario a editar
5. Modificar campos necesarios
6. Click en "Actualizar perfil"
```

### Opción 3: WP-CLI Directo

```bash
# Conectar al servidor
sshpass -p 'gsiB%s@0yD' ssh root@23.105.176.45

# Ir al directorio
cd /home/viveroloscocos.com.ar/public_html

# Listar usuarios
wp user list --allow-root

# Cambiar contraseña
wp user update admin --user_pass="NuevaPass123!" --allow-root

# Crear usuario
wp user create nuevo email@ejemplo.com --role=editor --user_pass="Pass123!" --allow-root
```

---

## 🆕 CREAR USUARIOS RECOMENDADOS

### 1. Gestor de Tienda (Shop Manager)

```bash
./manage_users.sh create tienda tienda@viveroloscocos.com.ar shop_manager "TiendaSegura2025!"
```

**Permisos:**
- ✅ Gestionar productos
- ✅ Ver pedidos
- ✅ Gestionar inventario
- ❌ Modificar temas/plugins
- ❌ Gestionar usuarios

### 2. Editor de Contenido

```bash
./manage_users.sh create editor editor@viveroloscocos.com.ar editor "EditorSeguro2025!"
```

**Permisos:**
- ✅ Publicar posts/páginas
- ✅ Editar contenido de otros
- ✅ Gestionar categorías
- ❌ Modificar configuración
- ❌ Gestionar productos

### 3. Cliente de Prueba

```bash
./manage_users.sh create cliente_prueba prueba@viveroloscocos.com.ar customer "Cliente123!"
```

**Permisos:**
- ✅ Realizar compras
- ✅ Ver historial de pedidos
- ✅ Gestionar su perfil
- ❌ Acceso al panel admin

---

## 🔐 CAMBIAR CONTRASEÑA

### Método 1: Con el Script

```bash
# Generar contraseña segura
./manage_users.sh generate 20

# Copiar la contraseña generada y aplicarla
./manage_users.sh password admin "ContraseñaGenerada123!"
```

### Método 2: Desde el Panel

```
1. Login en: https://viveroloscocos.com.ar/wp-admin/
2. Ir a: Usuarios → Tu perfil
3. Scroll hasta "Gestión de cuenta"
4. Click en "Generar contraseña"
5. Ingresar nueva contraseña
6. Click en "Actualizar perfil"
```

### Método 3: Recuperación por Email

```
1. Ir a: https://viveroloscocos.com.ar/wp-login.php
2. Click en "¿Olvidaste tu contraseña?"
3. Ingresar: santosma@gmail.com
4. Revisar email con enlace de recuperación
5. Crear nueva contraseña
```

---

## 📧 CAMBIAR EMAIL

```bash
# Cambiar email del usuario admin
./manage_users.sh email admin nuevo@email.com
```

O desde el panel:
```
Usuarios → admin → Editar → Email → Actualizar perfil
```

---

## 👤 CAMBIAR ROL

```bash
# Cambiar a Shop Manager
./manage_users.sh role admin shop_manager

# Volver a Administrator
./manage_users.sh role admin administrator
```

---

## 🗑️ ELIMINAR USUARIO

```bash
# Eliminar usuario por ID (requiere confirmación)
./manage_users.sh delete 2
```

> ⚠️ El contenido del usuario eliminado se reasignará al usuario ID 1

---

## 🛡️ ROLES DISPONIBLES

| Rol | Descripción | Uso |
|-----|-------------|-----|
| **administrator** | Acceso total al sitio | Propietario |
| **shop_manager** | Gestión completa de WooCommerce | Administrador de tienda |
| **editor** | Publicar y gestionar todo el contenido | Gestor de contenido |
| **author** | Publicar sus propios posts | Blogger |
| **contributor** | Escribir posts sin publicar | Colaborador |
| **subscriber** | Solo leer contenido | Usuario básico |
| **customer** | Realizar compras en WooCommerce | Cliente |

---

## 🔑 GENERAR CONTRASEÑAS SEGURAS

```bash
# Generar contraseña de 16 caracteres (por defecto)
./manage_users.sh generate

# Generar contraseña de 20 caracteres
./manage_users.sh generate 20

# Generar contraseña de 32 caracteres
./manage_users.sh generate 32
```

**Características de contraseñas seguras:**
- ✅ Mínimo 12 caracteres
- ✅ Mayúsculas y minúsculas
- ✅ Números
- ✅ Símbolos especiales
- ✅ Sin palabras comunes

---

## 📊 COMANDOS ÚTILES

### Ver Todos los Usuarios
```bash
./manage_users.sh list
```

### Ver Información Detallada
```bash
./manage_users.sh info admin
```

### Crear Usuario Completo
```bash
./manage_users.sh create \
  nombre_usuario \
  email@ejemplo.com \
  administrator \
  "ContraseñaSegura123!"
```

### Cambiar Múltiples Datos
```bash
# Cambiar contraseña
./manage_users.sh password admin "NuevaPass123!"

# Cambiar email
./manage_users.sh email admin nuevo@email.com

# Cambiar rol
./manage_users.sh role admin shop_manager
```

---

## 🚨 RECUPERACIÓN DE ACCESO

### Si perdiste la contraseña:

**Opción 1: Recuperación por email**
```
https://viveroloscocos.com.ar/wp-login.php?action=lostpassword
```

**Opción 2: Por SSH (acceso directo)**
```bash
sshpass -p 'gsiB%s@0yD' ssh root@23.105.176.45
cd /home/viveroloscocos.com.ar/public_html
wp user update admin --user_pass="NuevaPass123!" --allow-root
```

**Opción 3: Con el script**
```bash
./manage_users.sh password admin "NuevaPass123!"
```

---

## 📝 DOCUMENTACIÓN COMPLETA

Para más detalles, consulta:
- **`USUARIOS_WORDPRESS.md`** - Documentación completa
- **`manage_users.sh`** - Script de gestión
- **`manage_users.sh help`** - Ayuda del script

---

## 🔗 ACCESOS RÁPIDOS

### Panel de WordPress
```
URL:      https://viveroloscocos.com.ar/wp-admin/
Usuario:  admin
Email:    santosma@gmail.com
```

### Servidor SSH
```
Host:     23.105.176.45
Usuario:  root
Password: gsiB%s@0yD
```

### WooCommerce
```
URL:      https://viveroloscocos.com.ar/wp-admin/admin.php?page=wc-admin
Acceso:   Mismo que WordPress
```

---

## ✅ RESUMEN

- **Total usuarios:** 1
- **Usuario activo:** admin (Administrator)
- **Email:** santosma@gmail.com
- **Script disponible:** ✅ `manage_users.sh`
- **Documentación:** ✅ `USUARIOS_WORDPRESS.md`

**Para gestionar usuarios, usa:**
```bash
./manage_users.sh help
```
