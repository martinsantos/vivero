# Despliegue de Cambios - Vivero Los Cocos

## Archivos modificados localmente

Los siguientes archivos del tema necesitan ser subidos al servidor:

1. `theme-loscocos-child/header.php` - Navegación en español, links funcionales
2. `theme-loscocos-child/footer.php` - Info de contacto, Instagram, WhatsApp

## Comandos para desplegar

```bash
# Conectar al servidor
ssh root@23.105.176.45

# Subir archivos del tema (desde local)
scp /Applications/um/vivero/theme-loscocos-child/header.php \
    root@23.105.176.45:/home/viveroloscocos.com.ar/public_html/wp-content/themes/loscocos-child/

scp /Applications/um/vivero/theme-loscocos-child/footer.php \
    root@23.105.176.45:/home/viveroloscocos.com.ar/public_html/wp-content/themes/loscocos-child/

# Limpiar caché en servidor
ssh root@23.105.176.45 "cd /home/viveroloscocos.com.ar/public_html && wp cache flush"
```

## Verificación post-despliegue

1. Visitar https://viveroloscocos.com.ar
2. Verificar footer muestra:
   - Dirección: Perito Moreno 1295, Godoy Cruz, Mendoza
   - Teléfono: 0261 439-9025
   - Horarios: L-S 9:30-18:30 | Dom 10:30-13:30
   - Link Instagram funcionando
   - Link WhatsApp funcionando
3. Verificar header muestra navegación en español (Tienda, Plantas, Macetas, Contacto)
4. Verificar precios de macetas pequeñas >= $300

## Cambios de precios ejecutados

10 productos ajustados de precios < $300:
- Macetas Rocío 6cm: $200 → $300
- Macetas Rocío 8cm: $250 → $400
