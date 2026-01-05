# 📋 COMANDOS DE MONITOREO - DÍA 3

**Sistema Autónomo Activo** - Comandos para seguimiento en tiempo real

---

## 🔍 MONITOREO VISUAL

### Monitor principal (actualiza cada 10 seg)
```bash
bash monitor_galeria_completa.sh
```

### Ver estado completo
```bash
cat AUTOMATIZACION-COMPLETA-ACTIVA.md
```

### Estado en vivo actualizado
```bash
cat ESTADO-DIA-3-EN-VIVO.md
```

---

## 📊 PROGRESO EN TIEMPO REAL

### Contar imágenes totales subidas
```bash
grep -r "Uploaded media" logs/galeria_*.log | wc -l
```

### Progreso por iteración
```bash
# Iteración 1
grep -c "Uploaded media" logs/galeria_img2_*.log

# Iteración 2
grep -c "Uploaded media" logs/galeria_iter2_*.log

# Iteración 3
grep -c "Uploaded media" logs/galeria_iter3_*.log

# Iteración 4
grep -c "Uploaded media" logs/galeria_iter4_*.log

# Iteración 5
grep -c "Uploaded media" logs/galeria_iter5_*.log

# Iteración 6
grep -c "Uploaded media" logs/galeria_iter6_*.log
```

### Progreso porcentual
```bash
TOTAL=$(grep -r "Uploaded media" logs/galeria_*.log | wc -l | tr -d ' ')
echo "Progreso: $TOTAL/538 imágenes ($((TOTAL * 100 / 538))%)"
```

---

## 🔄 LOGS EN TIEMPO REAL

### Ver log de iteración activa
```bash
# Log más reciente
tail -f $(ls -t logs/galeria_*.log | head -1)

# Específicamente Iteración 1
tail -f logs/galeria_img2_20251004_095251.log

# Cuando estén activas las otras:
tail -f logs/galeria_iter2_*.log
tail -f logs/galeria_iter3_*.log
# ... etc
```

### Log del orquestador
```bash
tail -f logs/secuencia_auto_*.log
```

### Log del sistema completo
```bash
tail -f logs/galeria_completa_*.log
```

---

## ⚙️ PROCESOS ACTIVOS

### Ver procesos de automatización
```bash
ps aux | grep -E "wc_image_automation|iniciar_cuando|ejecutar_galeria" | grep -v grep
```

### Estado detallado de procesos
```bash
ps aux | grep wc_image_automation | grep -v grep | awk '{print "PID: " $2 " | CPU: " $3 "% | Mem: " $4 "% | Started: " $9}'
```

### Verificar PID orquestador
```bash
ps -p 62304
```

---

## 📈 ESTADÍSTICAS

### Últimas 10 imágenes subidas
```bash
grep "Uploaded media" logs/galeria_*.log | tail -10
```

### Tasa de éxito
```bash
TOTAL=$(grep -c "INFO:" logs/galeria_*.log 2>/dev/null || echo "1")
SUCCESS=$(grep -c "Uploaded media" logs/galeria_*.log 2>/dev/null || echo "0")
echo "Tasa de éxito: $((SUCCESS * 100 / TOTAL))%"
```

### Errores (si hay)
```bash
grep -i "error\|warning\|failed" logs/galeria_*.log | tail -20
```

---

## 🕐 TIMELINE Y ETAs

### Tiempo transcurrido Iteración 1
```bash
START="09:52"
CURRENT=$(date '+%H:%M')
echo "Inicio: $START | Actual: $CURRENT"
```

### ETA Iteraciones restantes
```bash
echo "Iteración 1: ETA 10:36 ART"
echo "Iteración 2: ETA 10:38-11:03 ART"
echo "Iteración 3: ETA 11:05-11:30 ART"
echo "Iteración 4: ETA 11:32-11:57 ART"
echo "Iteración 5: ETA 11:59-12:24 ART"
echo "Iteración 6: ETA 12:26-12:38 ART"
echo "Auditoría: ETA 12:40 ART"
echo "Finalización total: 13:00 ART"
```

---

## 📁 ARCHIVOS DE ESTADO

### Lista de todos los documentos
```bash
ls -lh ESTADO-* PLAN-* AUTOMATIZACION-* PROXIMOS-* RESUMEN-* README-*
```

### Ver plan completo
```bash
cat PLAN-COMPLETO-95-PUNTOS.md
```

### Ver README del proyecto
```bash
cat README-PROYECTO-SEO.md
```

---

## 🎯 QUICK STATUS

### Resumen de una línea
```bash
IMGS=$(grep -r "Uploaded media" logs/galeria_*.log 2>/dev/null | wc -l | tr -d ' ')
PCT=$((IMGS * 100 / 538))
ACTIVE=$(ps aux | grep wc_image_automation | grep -v grep | wc -l | tr -d ' ')
echo "📊 $IMGS/538 imágenes ($PCT%) | Procesos activos: $ACTIVE | Score objetivo: 91-92/100"
```

### Dashboard completo en terminal
```bash
clear
echo "═══════════════════════════════════════════════════════"
echo "📊 DÍA 3 - GALERÍA DE IMÁGENES - DASHBOARD"
echo "═══════════════════════════════════════════════════════"
echo ""
echo "⏰ $(date '+%H:%M:%S ART')"
echo ""
TOTAL=$(grep -r "Uploaded media" logs/galeria_*.log 2>/dev/null | wc -l | tr -d ' ')
echo "📈 Progreso: $TOTAL/538 imágenes ($((TOTAL * 100 / 538))%)"
echo ""
echo "🔄 Procesos activos:"
ps aux | grep wc_image_automation | grep -v grep | wc -l
echo ""
echo "📁 Última actualización:"
ls -lt logs/galeria_*.log | head -1 | awk '{print $6, $7, $8, $9}'
echo ""
echo "═══════════════════════════════════════════════════════"
```

---

## 🛠️ COMANDOS DE GESTIÓN

### Detener proceso actual (SOLO SI ES NECESARIO)
```bash
# ⚠️ ADVERTENCIA: Solo usar en emergencia
pkill -f wc_image_automation
```

### Reiniciar desde iteración específica
```bash
# Si necesitas reiniciar manualmente una iteración
bash preparar_iteracion_2.sh  # o 3, 4, 5, 6
```

### Ver espacio en disco
```bash
df -h | grep -E "Filesystem|/Applications"
```

---

## 📞 INFORMACIÓN ÚTIL

**Directorio de trabajo:** `/Applications/um/vivero`  
**Directorio de logs:** `/Applications/um/vivero/logs`  
**Score actual:** 88.8/100  
**Score objetivo Día 3:** 91-92/100  
**Productos totales:** 538  
**Imágenes objetivo:** 538-600  

---

## 🚨 SOLUCIÓN DE PROBLEMAS

### Si no ves progreso en 5 minutos
```bash
# Verificar si el proceso está activo
ps aux | grep wc_image_automation | grep -v grep

# Ver últimas líneas del log
tail -20 $(ls -t logs/galeria_*.log | head -1)

# Verificar errores
grep -i error $(ls -t logs/galeria_*.log | head -1)
```

### Si necesitas verificar el score actual
```bash
python3 wc_seo_audit.py --detail summary
```

---

**🟢 SISTEMA FUNCIONANDO - MONITOREO DISPONIBLE 24/7**

---

*Comandos actualizados: 10:27 ART*  
*Documentación completa: README-PROYECTO-SEO.md*
