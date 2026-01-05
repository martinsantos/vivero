#!/usr/bin/env python3
"""
LIMPIEZA DE ARCHIVOS HUÉRFANOS EN SERVIDOR - VIVERO LOS COCOS
==============================================================

Elimina archivos físicos en wp-content/uploads/ que no corresponden
a ningún adjunto activo en WordPress.

Estrategia:
1. Obtener lista de adjuntos válidos vía WP-CLI
2. Construir whitelist de archivos legítimos (originales + derivados)
3. Identificar archivos huérfanos en filesystem
4. Generar reporte y eliminar con confirmación

Uso:
    python3 limpiar_filesystem_servidor.py --dry-run
    python3 limpiar_filesystem_servidor.py --ejecutar
"""

import argparse
import json
import logging
import os
import re
import subprocess
import sys
from collections import defaultdict

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s %(levelname)s: %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S"
)
log = logging.getLogger(__name__)

# Credenciales SSH
SSH_HOST = "root@23.105.176.45"
SSH_PASS = "gsiB%s@0yD"
WP_PATH = "/home/viveroloscocos.com.ar/public_html"
UPLOADS_PATH = f"{WP_PATH}/wp-content/uploads"


def ssh_exec(cmd):
    """Ejecuta comando SSH y retorna stdout"""
    full_cmd = f"sshpass -p '{SSH_PASS}' ssh -o StrictHostKeyChecking=no {SSH_HOST} \"{cmd}\""
    result = subprocess.run(full_cmd, shell=True, capture_output=True, text=True)
    if result.returncode != 0:
        log.error(f"SSH error: {result.stderr}")
        return None
    return result.stdout.strip()


def obtener_adjuntos_validos():
    """Obtiene lista de adjuntos válidos desde WordPress"""
    log.info("Obteniendo adjuntos válidos desde WordPress...")
    
    cmd = f"cd {WP_PATH} && wp post list --post_type=attachment --fields=ID,guid --format=json"
    output = ssh_exec(cmd)
    
    if not output:
        log.error("No se pudo obtener lista de adjuntos")
        return []
    
    try:
        adjuntos = json.loads(output)
        log.info(f"Total adjuntos válidos: {len(adjuntos)}")
        return adjuntos
    except json.JSONDecodeError as e:
        log.error(f"Error parseando JSON: {e}")
        return []


def construir_whitelist(adjuntos):
    """Construye whitelist de archivos legítimos (originales + derivados)"""
    log.info("Construyendo whitelist de archivos legítimos...")
    
    whitelist = set()
    
    for adj in adjuntos:
        guid = adj.get('guid', '')
        
        # Extraer path relativo desde uploads/
        match = re.search(r'/wp-content/uploads/(.+)$', guid)
        if not match:
            continue
        
        rel_path = match.group(1)
        whitelist.add(rel_path)
        
        # Agregar derivados comunes de WordPress
        # Ejemplo: 2025/10/imagen.webp → 2025/10/imagen-*.webp, 2025/10/imagen-scaled.webp
        dir_path = os.path.dirname(rel_path)
        filename = os.path.basename(rel_path)
        name, ext = os.path.splitext(filename)
        
        # Patrones de derivados WordPress
        derivados_patterns = [
            f"{name}-scaled{ext}",
            f"{name}-[0-9]{{2,4}}x[0-9]{{2,4}}{ext}",  # -300x300, -1024x1024, etc.
        ]
        
        for pattern in derivados_patterns:
            whitelist.add(f"{dir_path}/{pattern}")
    
    log.info(f"Whitelist construida: {len(whitelist)} patrones")
    return whitelist


def obtener_archivos_filesystem():
    """Obtiene lista completa de archivos en uploads/"""
    log.info("Escaneando filesystem en servidor...")
    
    cmd = f"find {UPLOADS_PATH} -type f -printf '%P\\n'"
    output = ssh_exec(cmd)
    
    if not output:
        log.error("No se pudo obtener lista de archivos")
        return []
    
    archivos = output.split('\n')
    log.info(f"Total archivos en filesystem: {len(archivos)}")
    return archivos


def identificar_huerfanos(archivos_fs, whitelist):
    """Identifica archivos que no están en whitelist"""
    log.info("Identificando archivos huérfanos...")
    
    huerfanos = []
    
    for archivo in archivos_fs:
        # Verificar si el archivo coincide con algún patrón de whitelist
        es_valido = False
        
        for patron in whitelist:
            # Si el patrón contiene regex, usar match
            if '[' in patron or '*' in patron:
                regex_pattern = patron.replace('[', r'\[').replace(']', r'\]')
                regex_pattern = regex_pattern.replace('*', '.*')
                if re.match(regex_pattern, archivo):
                    es_valido = True
                    break
            else:
                # Coincidencia exacta
                if archivo == patron:
                    es_valido = True
                    break
        
        if not es_valido:
            huerfanos.append(archivo)
    
    log.info(f"Archivos huérfanos identificados: {len(huerfanos)}")
    return huerfanos


def generar_reporte(huerfanos):
    """Genera reporte de archivos huérfanos"""
    log.info("\n" + "="*60)
    log.info("REPORTE DE ARCHIVOS HUÉRFANOS")
    log.info("="*60)
    
    # Agrupar por directorio
    por_directorio = defaultdict(list)
    for archivo in huerfanos:
        dir_path = os.path.dirname(archivo)
        por_directorio[dir_path].append(archivo)
    
    log.info(f"\nTotal archivos huérfanos: {len(huerfanos)}")
    log.info(f"Directorios afectados: {len(por_directorio)}")
    
    # Mostrar top 5 directorios con más huérfanos
    top_dirs = sorted(por_directorio.items(), key=lambda x: len(x[1]), reverse=True)[:5]
    log.info("\nTop 5 directorios con más huérfanos:")
    for dir_path, archivos in top_dirs:
        log.info(f"  {dir_path}: {len(archivos)} archivos")
    
    # Ejemplos
    log.info("\nEjemplos de archivos huérfanos:")
    for i, archivo in enumerate(huerfanos[:10], 1):
        log.info(f"  {i}. {archivo}")
    
    return por_directorio


def eliminar_huerfanos(huerfanos, dry_run=True):
    """Elimina archivos huérfanos del servidor"""
    if dry_run:
        log.info("\n🔍 DRY-RUN: No se eliminarán archivos")
        log.info(f"   Se eliminarían {len(huerfanos)} archivos")
        return
    
    log.info(f"\n🗑️  Eliminando {len(huerfanos)} archivos huérfanos...")
    
    # Guardar lista en servidor para eliminación por lotes
    lista_temp = "/tmp/uploads_orphans_to_delete.txt"
    
    # Crear archivo con lista
    archivos_quoted = [f"'{UPLOADS_PATH}/{f}'" for f in huerfanos]
    lista_content = '\n'.join(archivos_quoted)
    
    # Escribir lista en servidor
    cmd_write = f"cat > {lista_temp} << 'EOFLIST'\n{lista_content}\nEOFLIST"
    ssh_exec(cmd_write)
    
    # Eliminar en lotes
    cmd_delete = f"xargs rm -f < {lista_temp}"
    result = ssh_exec(cmd_delete)
    
    log.info(f"✅ Eliminación completada")
    log.info(f"   Comando ejecutado: xargs rm -f < {lista_temp}")
    
    # Limpiar archivo temporal
    ssh_exec(f"rm -f {lista_temp}")


def main():
    parser = argparse.ArgumentParser(description='Limpieza de archivos huérfanos en servidor')
    parser.add_argument('--dry-run', action='store_true', help='Simular sin cambios')
    parser.add_argument('--ejecutar', action='store_true', help='Ejecutar eliminación real')
    args = parser.parse_args()
    
    if not args.dry_run and not args.ejecutar:
        log.error("Especifica --dry-run o --ejecutar")
        sys.exit(1)
    
    log.info("="*60)
    log.info("LIMPIEZA DE FILESYSTEM - VIVERO LOS COCOS")
    log.info("="*60)
    
    # 1. Obtener adjuntos válidos
    adjuntos = obtener_adjuntos_validos()
    if not adjuntos:
        log.error("No se pudieron obtener adjuntos válidos")
        sys.exit(1)
    
    # 2. Construir whitelist
    whitelist = construir_whitelist(adjuntos)
    
    # 3. Obtener archivos del filesystem
    archivos_fs = obtener_archivos_filesystem()
    if not archivos_fs:
        log.error("No se pudieron obtener archivos del filesystem")
        sys.exit(1)
    
    # 4. Identificar huérfanos
    huerfanos = identificar_huerfanos(archivos_fs, whitelist)
    
    # 5. Generar reporte
    generar_reporte(huerfanos)
    
    # 6. Eliminar (si no es dry-run)
    eliminar_huerfanos(huerfanos, dry_run=args.dry_run)
    
    log.info("\n" + "="*60)
    log.info("Proceso completado")
    log.info("="*60)


if __name__ == '__main__':
    main()
