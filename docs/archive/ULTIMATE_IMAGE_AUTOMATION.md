# 🌱 Ultimate Image Automation - Los Cocos E-commerce

## Descripción

Este es el script definitivo de automatización de imágenes que combina las mejores características de todos los scripts anteriores. Está especialmente diseñado para el vivero Los Cocos y optimizado para productos de jardinería y plantas.

## Características Principales

### ✨ Funcionalidades Destacadas

1. **Múltiples Proveedores de Imágenes**
   - **Unsplash**: Fotos profesionales de alta calidad
   - **Pixabay**: Amplia variedad de imágenes libres
   - **iNaturalist**: Especializado en plantas y naturaleza (ideal para viveros)
   - **Pexels**: Fotos comerciales de calidad

2. **Inteligencia para Viveros**
   - Traducción automática español-inglés para términos de plantas
   - Priorización de proveedores según tipo de producto (plantas vs herramientas)
   - Limpieza inteligente de nombres de productos (elimina medidas, etc.)
   - Generación de términos de búsqueda especializados

3. **Procesamiento Avanzado**
   - Redimensionamiento inteligente manteniendo proporción
   - Fondo blanco profesional
   - Optimización de calidad JPEG
   - Deduplicación automática

4. **Integración Completa**
   - API de WooCommerce para obtener productos
   - API de WordPress para subir imágenes
   - Asignación automática de imágenes a productos
   - Sistema de logging detallado

## Instalación

### 1. Dependencias

```bash
pip install requests python-dotenv pillow tqdm woocommerce
```

### 2. Variables de Entorno

Crea un archivo `.env` en el directorio del proyecto:

```env
# WordPress/WooCommerce
WORDPRESS_URL=http://localhost:8080
WC_CONSUMER_KEY=ck_xxxxxxxxxxxxxxxxx
WC_CONSUMER_SECRET=cs_xxxxxxxxxxxxxxxxx
WP_USERNAME=admin
WP_APP_PASSWORD=xxxx xxxx xxxx xxxx

# APIs de Imágenes
UNSPLASH_API_KEY=tu_clave_de_unsplash
PIXABAY_API_KEY=tu_clave_de_pixabay
PEXELS_API_KEY=tu_clave_de_pexels
```

### 3. Configuración de APIs

#### Unsplash (Recomendado para plantas)
1. Visita: https://unsplash.com/developers
2. Crea una nueva aplicación
3. Copia la "Access Key"

#### Pixabay (Buena variedad)
1. Visita: https://pixabay.com/api/docs/
2. Registra una cuenta
3. Obtén tu API key

#### iNaturalist (Especializado en naturaleza)
- No requiere API key
- Excelente para plantas reales

## Uso

### Comandos Básicos

```bash
# Procesar productos sin imágenes (recomendado)
python ultimate_image_automation.py --target missing

# Procesar todos los productos
python ultimate_image_automation.py --target all

# Modo de prueba (sin cambios reales)
python ultimate_image_automation.py --target missing --dry-run

# Lote pequeño para pruebas
python ultimate_image_automation.py --target missing --batch-size 5

# Con logging detallado
python ultimate_image_automation.py --target missing --log-level DEBUG
```

### Parámetros Disponibles

- `--target`: Qué productos procesar (`missing`, `all`)
- `--batch-size`: Número de productos por lote (default: 10)
- `--dry-run`: Simular sin hacer cambios reales
- `--log-level`: Nivel de logging (`DEBUG`, `INFO`, `WARNING`)

## Lógica de Búsqueda Inteligente

### 1. Detección de Tipo de Producto

El script identifica automáticamente si un producto es:
- **Planta/Vegetal**: Usa proveedores especializados (iNaturalist, Unsplash)
- **Herramienta/Accesorio**: Usa proveedores generales (Unsplash, Pixabay)

### 2. Generación de Términos de Búsqueda

Para un producto como "Planta de Interior Ficus 25cm":

1. **Limpieza**: "planta interior ficus" (elimina medidas)
2. **Traducción**: "indoor plant ficus"
3. **Términos finales**: 
   - "planta interior ficus"
   - "indoor plant ficus"
   - "indoor plant ficus plant"

### 3. Priorización de Resultados

- **Puntuación de calidad**: Resolución + popularidad
- **Deduplicación**: Elimina URLs duplicadas
- **Ranking**: Mejor calidad primero

## Archivo de Configuración

El script utiliza estas traducciones automáticas:

```python
PLANT_TRANSLATIONS = {
    "planta": "plant",
    "plantas": "plants",
    "arbusto": "shrub",
    "árbol": "tree",
    "interior": "indoor houseplant",
    "exterior": "outdoor garden plant",
    "jardín": "garden",
    "vivero": "nursery plant",
    "maceta": "potted plant",
    # ... más traducciones
}
```

## Logs y Monitoreo

### Archivo de Log

Ubicación: `logs/ultimate_image_automation.log`

### Ejemplo de Salida

```
2024-08-30 10:30:15 INFO: 🌱 Iniciando procesamiento (target: missing)
2024-08-30 10:30:16 INFO: 📦 Productos encontrados: 25
2024-08-30 10:30:17 INFO: 🔍 Procesando: Planta de Interior Monstera
2024-08-30 10:30:19 INFO: ✅ Éxito para Planta de Interior Monstera
```

## Solución de Problemas

### Error: "WordPress credentials required"
- Verifica que `WP_USERNAME` y `WP_APP_PASSWORD` estén configurados
- Asegúrate de que la Application Password sea correcta

### Error: "WooCommerce API error"
- Verifica `WC_CONSUMER_KEY` y `WC_CONSUMER_SECRET`
- Confirma que las claves tengan permisos de lectura/escritura

### Sin imágenes encontradas
- Verifica las API keys de los proveedores
- Intenta con `--log-level DEBUG` para ver más detalles
- Algunos productos pueden tener nombres muy específicos

### Error de conexión
- Verifica que WordPress esté corriendo en la URL configurada
- Confirma que no hay problemas de red o firewall

## Mejores Prácticas

### 1. Configuración Inicial
```bash
# Siempre empezar con modo dry-run
python ultimate_image_automation.py --target missing --dry-run --batch-size 5

# Si todo está bien, procesar lotes pequeños
python ultimate_image_automation.py --target missing --batch-size 5
```

### 2. Monitoreo
- Revisar los logs regularmente
- Verificar manualmente las primeras imágenes asignadas
- Usar batch-size pequeño para empezar

### 3. Optimización
- iNaturalist es excelente para plantas reales
- Unsplash para fotos profesionales
- Pixabay para variedad general

## Comparación con Scripts Anteriores

| Característica | Script Anterior | Ultimate Script |
|---------------|-----------------|-----------------|
| Proveedores | 2-3 básicos | 4+ especializados |
| Inteligencia | Búsqueda simple | IA para viveros |
| Calidad | Básica | Ranking avanzado |
| Traducción | No | Español ↔ Inglés |
| Especialización | General | Vivero/plantas |
| Error Handling | Limitado | Completo |

## Estructura del Código

```
ultimate_image_automation.py
├── Config: Configuración y validación
├── ImageProvider: Búsqueda en múltiples APIs
├── SearchTermGenerator: Generación inteligente de términos
├── UltimateProcessor: Motor principal de procesamiento
└── main(): Interfaz de línea de comandos
```

## Contribución

Para mejorar el script:

1. **Nuevos proveedores**: Agregar en `ImageProvider`
2. **Traducciones**: Expandir `PLANT_TRANSLATIONS`
3. **Lógica de plantas**: Mejorar `_is_plant()`
4. **Calidad**: Ajustar algoritmo de puntuación

## Licencia

Este script está diseñado específicamente para Los Cocos E-commerce y combina las mejores prácticas de automatización de imágenes para viveros.