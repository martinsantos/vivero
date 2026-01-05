# 👥 USUARIOS WORDPRESS - viveroloscocos.com.ar

**Sitio:** https://viveroloscocos.com.ar  
**Servidor:** 23.105.176.45  
**Ruta:** /home/viveroloscocos.com.ar/public_html/

---

## 📋 LISTA DE USUARIOS ACTUALES

| ID | Usuario | Email | Rol | Fecha Registro |
|----|---------|-------|-----|----------------|
| 1 | **admin** | santosma@gmail.com | Administrator | 2025-09-11 18:04:45 |

### Detalles del Usuario Principal

**Usuario:** `admin`  
**Email:** `santosma@gmail.com`  
**Rol:** Administrator (acceso completo)  
**ID:** 1  
**Estado:** Activo ✅

> ⚠️ **NOTA IMPORTANTE:** Las contraseñas de WordPress están hasheadas con bcrypt y NO se pueden recuperar en texto plano por seguridad. Solo se pueden resetear.

---

## 🔧 MÉTODOS PARA EDITAR USUARIOS

### Método 1: Panel de Administración WordPress (Recomendado)

#### Acceso al Panel
```
URL: https://viveroloscocos.com.ar/wp-admin/
Usuario: admin
Contraseña: [La que tengas configurada]
```

#### Editar Usuario
1. Ir a: **Usuarios → Todos los usuarios**
2. Click en el usuario a editar
3. Modificar campos:
   - Nombre de usuario (no editable)
   - Email
   - Nombre
   - Apellido
   - Sitio web
   - Descripción biográfica
   - Nueva contraseña
   - Rol

#### Cambiar Contraseña
1. En la edición del usuario, scroll hasta **Gestión de cuenta**
2. Click en **Generar contraseña**
3. Ingresar nueva contraseña o usar la generada
4. Click en **Actualizar perfil**

---

### Método 2: WP-CLI (Línea de Comandos)

#### Listar Usuarios
```bash
# Conectar al servidor
sshpass -p 'gsiB%s@0yD' ssh root@23.105.176.45

# Ir al directorio de WordPress
cd /home/viveroloscocos.com.ar/public_html

# Listar todos los usuarios
wp user list --allow-root

# Listar con más detalles
wp user list --fields=ID,user_login,user_email,display_name,roles --format=table --allow-root
```

#### Ver Detalles de un Usuario
```bash
# Ver información completa del usuario ID 1
wp user get 1 --allow-root

# Ver solo el email
wp user get 1 --field=user_email --allow-root
```

#### Editar Usuario
```bash
# Cambiar email
wp user update 1 --user_email=nuevo@email.com --allow-root

# Cambiar nombre para mostrar
wp user update 1 --display_name="Nombre Completo" --allow-root

# Cambiar rol
wp user update 1 --role=editor --allow-root
```

#### Cambiar Contraseña
```bash
# Cambiar contraseña del usuario admin
wp user update admin --user_pass="NuevaContraseñaSegura123!" --allow-root

# O por ID
wp user update 1 --user_pass="NuevaContraseñaSegura123!" --allow-root
```

#### Crear Nuevo Usuario
```bash
# Crear usuario con rol de administrador
wp user create nuevo_usuario email@ejemplo.com --role=administrator --user_pass="ContraseñaSegura123!" --allow-root

# Crear usuario con rol de editor
wp user create editor email@ejemplo.com --role=editor --user_pass="Pass123!" --allow-root

# Crear usuario con rol de cliente (WooCommerce)
wp user create cliente email@ejemplo.com --role=customer --user_pass="Pass123!" --allow-root
```

#### Eliminar Usuario
```bash
# Eliminar usuario (reasignar contenido al usuario ID 1)
wp user delete 2 --reassign=1 --allow-root

# Eliminar sin reasignar contenido
wp user delete 2 --yes --allow-root
```

---

### Método 3: Base de Datos MySQL (Avanzado)

#### Acceder a la Base de Datos
```bash
# Conectar al servidor
sshpass -p 'gsiB%s@0yD' ssh root@23.105.176.45

# Acceder a MySQL
cd /home/viveroloscocos.com.ar/public_html
wp db cli --allow-root
```

#### Consultas Útiles
```sql
-- Ver todos los usuarios
SELECT ID, user_login, user_email, user_registered 
FROM wp_users;

-- Ver usuario específico
SELECT * FROM wp_users WHERE user_login = 'admin';

-- Cambiar email
UPDATE wp_users 
SET user_email = 'nuevo@email.com' 
WHERE user_login = 'admin';

-- Ver roles de usuarios
SELECT u.ID, u.user_login, m.meta_value as roles
FROM wp_users u
JOIN wp_usermeta m ON u.ID = m.user_id
WHERE m.meta_key = 'wp_capabilities';
```

#### Cambiar Contraseña en Base de Datos
```sql
-- Generar hash MD5 (temporal, WordPress lo convertirá a bcrypt)
UPDATE wp_users 
SET user_pass = MD5('NuevaContraseña123!') 
WHERE user_login = 'admin';
```

> ⚠️ **ADVERTENCIA:** Editar directamente en la base de datos puede causar problemas. Usar WP-CLI o el panel de administración es más seguro.

---

### Método 4: Script PHP (Emergencia)

Si perdiste acceso al panel, puedes crear un archivo PHP temporal:

#### Crear archivo reset-password.php
```bash
# Conectar al servidor
sshpass -p 'gsiB%s@0yD' ssh root@23.105.176.45

# Crear archivo
cat > /home/viveroloscocos.com.ar/public_html/reset-password.php << 'EOF'
<?php
require_once('wp-load.php');

// Cambiar contraseña del usuario admin
$user_id = 1;
$new_password = 'NuevaContraseñaSegura123!';

wp_set_password($new_password, $user_id);

echo "Contraseña actualizada para usuario ID: $user_id\n";
echo "Nueva contraseña: $new_password\n";
echo "\n¡ELIMINA ESTE ARCHIVO INMEDIATAMENTE!\n";
?>
EOF

# Ejecutar
php /home/viveroloscocos.com.ar/public_html/reset-password.php

# ELIMINAR INMEDIATAMENTE
rm /home/viveroloscocos.com.ar/public_html/reset-password.php
```

---

## 🔐 ROLES DE USUARIO EN WORDPRESS

### Roles Disponibles

| Rol | Capacidades | Uso Recomendado |
|-----|-------------|------------------|
| **Administrator** | Acceso total al sitio | Propietario, desarrollador |
| **Editor** | Publicar y gestionar contenido | Gestor de contenido |
| **Author** | Publicar sus propios posts | Blogger, escritor |
| **Contributor** | Escribir posts (sin publicar) | Colaborador externo |
| **Subscriber** | Solo leer contenido | Usuario registrado básico |
| **Customer** (WooCommerce) | Realizar compras | Cliente de tienda |
| **Shop Manager** (WooCommerce) | Gestionar productos y pedidos | Administrador de tienda |

### Cambiar Rol de Usuario
```bash
# Cambiar a Shop Manager (gestor de tienda)
wp user set-role 1 shop_manager --allow-root

# Volver a Administrator
wp user set-role 1 administrator --allow-root
```

---

## 📧 RECUPERAR CONTRASEÑA

### Desde el Login
1. Ir a: https://viveroloscocos.com.ar/wp-login.php
2. Click en **"¿Olvidaste tu contraseña?"**
3. Ingresar email: `santosma@gmail.com`
4. Revisar email con enlace de recuperación
5. Crear nueva contraseña

### Por WP-CLI (Sin acceso a email)
```bash
# Generar enlace de recuperación
wp user reset-password admin --send-email --allow-root

# O cambiar directamente
wp user update admin --user_pass="NuevaPass123!" --allow-root
```

---

## 🆕 CREAR NUEVOS USUARIOS

### Usuarios Recomendados para Ecommerce

#### 1. Administrador de Tienda
```bash
wp user create tienda tienda@viveroloscocos.com.ar \
  --role=shop_manager \
  --user_pass="TiendaSegura2025!" \
  --display_name="Gestor de Tienda" \
  --first_name="Gestor" \
  --last_name="Tienda" \
  --allow-root
```

#### 2. Editor de Contenido
```bash
wp user create editor editor@viveroloscocos.com.ar \
  --role=editor \
  --user_pass="EditorSeguro2025!" \
  --display_name="Editor de Contenido" \
  --first_name="Editor" \
  --last_name="Contenido" \
  --allow-root
```

#### 3. Cliente de Prueba
```bash
wp user create cliente_prueba prueba@viveroloscocos.com.ar \
  --role=customer \
  --user_pass="Cliente123!" \
  --display_name="Cliente Prueba" \
  --allow-root
```

---

## 🛡️ SEGURIDAD DE USUARIOS

### Buenas Prácticas

1. **Contraseñas Seguras**
   - Mínimo 12 caracteres
   - Mayúsculas, minúsculas, números y símbolos
   - No usar palabras comunes
   - Cambiar cada 3-6 meses

2. **Usuarios Limitados**
   - Crear usuarios con roles específicos
   - No dar acceso Administrator innecesariamente
   - Eliminar usuarios inactivos

3. **Autenticación de Dos Factores (2FA)**
   ```bash
   # Instalar plugin de 2FA
   wp plugin install two-factor --activate --allow-root
   ```

4. **Limitar Intentos de Login**
   ```bash
   # Instalar plugin Limit Login Attempts
   wp plugin install limit-login-attempts-reloaded --activate --allow-root
   ```

5. **Auditoría de Usuarios**
   ```bash
   # Ver últimos logins
   wp user list --fields=ID,user_login,user_email --allow-root
   
   # Ver usuarios inactivos (sin posts)
   wp user list --field=ID --allow-root | while read id; do
     posts=$(wp post list --author=$id --format=count --allow-root)
     if [ $posts -eq 0 ]; then
       echo "Usuario ID $id sin actividad"
     fi
   done
   ```

---

## 📝 SCRIPT DE GESTIÓN DE USUARIOS

He creado un script para facilitar la gestión:

```bash
#!/bin/bash
# Script: manage_users.sh

SSH_CMD="sshpass -p 'gsiB%s@0yD' ssh root@23.105.176.45"
WP_PATH="/home/viveroloscocos.com.ar/public_html"

# Función: Listar usuarios
list_users() {
    $SSH_CMD "cd $WP_PATH && wp user list --fields=ID,user_login,user_email,roles --format=table --allow-root"
}

# Función: Crear usuario
create_user() {
    local username=$1
    local email=$2
    local role=$3
    local password=$4
    
    $SSH_CMD "cd $WP_PATH && wp user create $username $email --role=$role --user_pass='$password' --allow-root"
}

# Función: Cambiar contraseña
change_password() {
    local username=$1
    local new_password=$2
    
    $SSH_CMD "cd $WP_PATH && wp user update $username --user_pass='$new_password' --allow-root"
}

# Función: Eliminar usuario
delete_user() {
    local user_id=$1
    
    $SSH_CMD "cd $WP_PATH && wp user delete $user_id --reassign=1 --yes --allow-root"
}

# Menú
case "$1" in
    list)
        list_users
        ;;
    create)
        create_user "$2" "$3" "$4" "$5"
        ;;
    password)
        change_password "$2" "$3"
        ;;
    delete)
        delete_user "$2"
        ;;
    *)
        echo "Uso: $0 {list|create|password|delete}"
        echo "  list                                    - Listar usuarios"
        echo "  create <user> <email> <role> <pass>    - Crear usuario"
        echo "  password <user> <new_pass>             - Cambiar contraseña"
        echo "  delete <user_id>                       - Eliminar usuario"
        ;;
esac
```

---

## 🔍 INFORMACIÓN ADICIONAL

### Verificar Configuración Actual
```bash
# Ver configuración de WordPress
wp config list --allow-root

# Ver plugins activos
wp plugin list --status=active --allow-root

# Ver tema activo
wp theme list --status=active --allow-root
```

### Backup Antes de Cambios
```bash
# Backup de base de datos
wp db export /tmp/backup_$(date +%Y%m%d_%H%M%S).sql --allow-root

# Backup de usuarios específicamente
wp db query "SELECT * FROM wp_users" --allow-root > /tmp/users_backup.sql
```

---

## 📞 ACCESO RÁPIDO

### Credenciales Actuales

**Panel WordPress:**
- URL: https://viveroloscocos.com.ar/wp-admin/
- Usuario: `admin`
- Email: `santosma@gmail.com`
- Contraseña: [Configurada por el propietario]

**Servidor SSH:**
- Host: `23.105.176.45`
- Usuario: `root`
- Contraseña: `gsiB%s@0yD`

**Base de Datos:**
- Acceso vía WP-CLI: `wp db cli --allow-root`

---

## ⚠️ NOTAS IMPORTANTES

1. **Contraseñas:** WordPress usa bcrypt para hashear contraseñas. No se pueden ver en texto plano.
2. **Seguridad:** Nunca compartas contraseñas por email o chat sin cifrar.
3. **Backups:** Siempre haz backup antes de modificar usuarios.
4. **Auditoría:** Revisa regularmente los usuarios activos.
5. **Acceso SSH:** Protege las credenciales SSH, tienen acceso root al servidor.

---

**Última actualización:** 20 de Octubre 2025  
**Sitio:** viveroloscocos.com.ar  
**Total usuarios activos:** 1
