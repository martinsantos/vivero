# 🌿 Los Cocos - Vivero Estacional Dinámico

> Sitio web para vivero en Mendoza con sistema estacional dinámico completo

[![Versión](https://img.shields.io/badge/versión-v6.1-green.svg)](https://github.com/martinsantos/vivero)
[![Estado](https://img.shields.io/badge/estado-funcional-brightgreen.svg)](http://localhost:8001/home.html)
[![Licencia](https://img.shields.io/badge/licencia-MIT-blue.svg)](LICENSE)

## 🚀 Inicio Rápido

```bash
# Clonar repositorio
git clone https://github.com/martinsantos/vivero.git
cd vivero

# Iniciar servidor
python3 -m http.server 8001

# Abrir navegador
open http://localhost:8001/home.html
```

## ✨ Características

- 🌸 **4 Temporadas Completas** - Primavera, Verano, Otoño, Invierno
- 🔄 **Cambio Dinámico Total** - Contenido, colores, productos, consejos
- 📱 **Responsive Design** - Tailwind CSS + diseño móvil
- 🛒 **Carrito Funcional** - Sistema de compras integrado
- 🎯 **Detección Automática** - Temporada según fecha actual
- 🧪 **Testing Integrado** - Botones para cambiar temporadas

## 📁 Estructura del Proyecto

```
vivero/
├── home.html              # Página principal
├── autumn-features.js     # Sistema estacional (CORE)
├── cart.js               # Carrito de compras
├── DEV_DOCUMENTATION.md  # Documentación completa
└── README.md            # Este archivo
```

## 🎮 Demo en Vivo

1. **Visita:** http://localhost:8001/home.html
2. **Prueba:** Usa los botones 🌸 🌞 🍂 ❄️ para cambiar temporadas
3. **Observa:** Cómo cambia TODO el contenido dinámicamente

## 🔧 Tecnologías

- **Frontend:** HTML5, JavaScript ES6+, Tailwind CSS
- **Servidor:** Python HTTP Server
- **Control:** Git + GitHub

## 📖 Documentación

- **[Documentación Completa](DEV_DOCUMENTATION.md)** - Guía detallada para desarrolladores
- **[Código Principal](autumn-features.js)** - Sistema estacional v6.1

## 🐛 Resolución de Problemas

**Puerto ocupado:**
```bash
pkill -f "python3 -m http.server"
python3 -m http.server 8001
```

**JavaScript no carga:**
- Verificar consola del navegador (F12)
- Comprobar versión del script: `autumn-features.js?v=6.1`

## 🤝 Contribuir

1. Fork del repositorio
2. Crear rama feature: `git checkout -b nueva-funcionalidad`
3. Commit cambios: `git commit -m "✨ feat: nueva funcionalidad"`
4. Push a la rama: `git push origin nueva-funcionalidad`
5. Crear Pull Request

## 📊 Estado del Proyecto

- ✅ **Sistema estacional:** 100% funcional
- ✅ **4 temporadas:** Datos completos
- ✅ **Responsive:** Móvil y desktop
- ✅ **Carrito:** Funcional
- 🔄 **En desarrollo:** Backend y pagos

## 📞 Contacto

- **Repositorio:** [github.com/martinsantos/vivero](https://github.com/martinsantos/vivero)
- **Demo local:** http://localhost:8001/home.html
- **Documentación:** [DEV_DOCUMENTATION.md](DEV_DOCUMENTATION.md)

---

## 🏷️ Tags

`vivero` `mendoza` `estacional` `javascript` `tailwind` `ecommerce` `plantas` `frontend`

---

*Desarrollado con 💚 para Los Cocos Vivero, Mendoza* 