# 📐 ANÁLISIS DE DISEÑO - COMPARATIVA BOCETOS vs ACTUAL

**Fecha:** 27 de Octubre 2025  
**Objetivo:** Alinear el diseño actual con los bocetos profesionales

---

## 🎯 DIFERENCIAS IDENTIFICADAS

### 1. GRID DE PRODUCTOS

#### Boceto (Esperado)
```
- Desktop: 3 columnas (220px minmax)
- Tablet: 2 columnas
- Móvil: 1 columna
- Gap: 24px (1.5rem)
- Imágenes: aspect-square con rounded-lg
```

#### Actual (Incorrecto)
```
- Desktop: 4 columnas (demasiadas)
- Tablet: 3 columnas (demasiadas)
- Móvil: 2 columnas (demasiadas)
- Gap: 2rem (correcto)
- Imágenes: OK
```

**PROBLEMA:** Grid muy apretado, productos pequeños

---

### 2. TARJETAS DE PRODUCTO

#### Boceto (Esperado)
```
Estructura:
├── Imagen (aspect-square, rounded-lg)
├── Título (font-medium, 1rem)
├── Precio (text-sm, gris)
└── Botón (full-width, py-2 px-4, rounded-md)

Espaciado:
- Entre elementos: gap-3
- Padding: 0 (sin padding en tarjeta)
- Botón: py-2 px-4 (compacto)
```

#### Actual (Incorrecto)
```
- Títulos: 1rem (correcto)
- Precios: 0.95rem (muy grande)
- Botones: 0.625rem padding (muy pequeño)
- Gap: correcto
```

**PROBLEMA:** Botones muy pequeños, precios muy grandes

---

### 3. COLORES Y ESTILOS

#### Boceto
```
Primario: #1dc91d (verde)
Fondo: #f6f8f6 (gris muy claro)
Texto: #000 (negro)
Precio: #666 (gris oscuro)
Botón: #1dc91d (verde)
Botón hover: #1dc91d/90 (más oscuro)
```

#### Actual
```
Primario: #1dc91d ✅
Fondo: #f6f8f6 ✅
Texto: #1a1a1a (casi negro) ✅
Precio: #6B7280 (gris) ✅
Botón: #1dc91d ✅
```

**ESTADO:** Colores correctos ✅

---

### 4. TIPOGRAFÍA

#### Boceto
```
Font: Epilogue (display)
Títulos: font-medium (500)
Precios: text-sm (0.875rem)
Botones: font-medium (500)
```

#### Actual
```
Títulos: font-weight 700 (demasiado bold)
Precios: 0.95rem (demasiado grande)
Botones: font-weight 700 (demasiado bold)
```

**PROBLEMA:** Tipografía demasiado pesada

---

### 5. BOTONES

#### Boceto
```
Estilo: py-2 px-4 rounded-md
Tamaño: Compacto, proporcional
Hover: bg-primary/90
Icono: 16px (pequeño)
Texto: "Agregar al Carro"
```

#### Actual
```
Estilo: 0.625rem padding (muy pequeño)
Tamaño: Demasiado compacto
Hover: scale(1.05) + shadow (excesivo)
Icono: 1rem (correcto)
Texto: UPPERCASE (incorrecto)
```

**PROBLEMA:** Botones muy pequeños, texto en mayúsculas

---

## ✅ CHECKLIST DE CORRECCIONES NECESARIAS

### Grid
- [ ] Desktop: 3 columnas (no 4)
- [ ] Tablet: 2 columnas (no 3)
- [ ] Móvil: 1 columna (no 2)
- [ ] Gap: 1.5rem (no 2rem)

### Tarjetas
- [ ] Título: font-weight 500 (no 700)
- [ ] Precio: 0.875rem (no 0.95rem)
- [ ] Precio: text-sm (correcto)
- [ ] Botón: py-2 px-4 (no 0.625rem)
- [ ] Botón: rounded-md (no 6px)
- [ ] Botón: font-weight 500 (no 700)
- [ ] Botón: text normal (no UPPERCASE)

### Espaciado
- [ ] Gap entre tarjetas: 1.5rem (no 2rem)
- [ ] Padding tarjeta: 0 (correcto)
- [ ] Margin botón: 0 (correcto)

### Efectos
- [ ] Hover botón: bg-primary/90 (no scale)
- [ ] Hover imagen: opacity 0.95 (correcto)

---

## 🎨 COMPARATIVA VISUAL

### Boceto (Correcto)
```
┌─────────────────────────────────────────┐
│  3 COLUMNAS - DESKTOP                   │
├──────────────┬──────────────┬───────────┤
│   Imagen     │   Imagen     │  Imagen   │
│   220px      │   220px      │  220px    │
│              │              │           │
│   Título     │   Título     │  Título   │
│   $25        │   $15        │  $30      │
│   [Botón]    │   [Botón]    │ [Botón]   │
└──────────────┴──────────────┴───────────┘
```

### Actual (Incorrecto)
```
┌──────────────────────────────────────────────────┐
│  4 COLUMNAS - DESKTOP (MUY APRETADO)             │
├────────┬────────┬────────┬────────┐
│ Imagen │ Imagen │ Imagen │ Imagen │
│ 180px  │ 180px  │ 180px  │ 180px  │
│        │        │        │        │
│Título  │Título  │Título  │Título  │
│ $25    │ $15    │ $30    │ $20    │
│[Botón] │[Botón] │[Botón] │[Botón] │
└────────┴────────┴────────┴────────┘
```

---

## 📋 PLAN DE ACCIÓN

### Fase 1: Grid (CRÍTICO)
1. Cambiar grid a 3 columnas desktop
2. Cambiar a 2 columnas tablet
3. Cambiar a 1 columna móvil
4. Reducir gap a 1.5rem

### Fase 2: Tipografía (IMPORTANTE)
1. Título: 500 weight (no 700)
2. Precio: 0.875rem (no 0.95rem)
3. Botón: 500 weight (no 700)

### Fase 3: Botones (IMPORTANTE)
1. Padding: py-2 px-4 (no 0.625rem)
2. Border-radius: 6px (correcto)
3. Hover: sin scale, solo color
4. Texto: normal case (no UPPERCASE)

### Fase 4: Espaciado (MENOR)
1. Gap: 1.5rem (no 2rem)
2. Verificar padding tarjetas

---

## 🎯 RESULTADO ESPERADO

Después de las correcciones:
- ✅ Grid limpio: 3 cols desktop, 2 cols tablet, 1 col móvil
- ✅ Tarjetas bien proporcionadas
- ✅ Tipografía ligera y elegante
- ✅ Botones profesionales y compactos
- ✅ Espaciado armonioso
- ✅ Coincide con bocetos

---

**Estado:** Listo para implementar correcciones
