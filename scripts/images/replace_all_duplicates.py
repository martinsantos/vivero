#!/usr/bin/env python3
"""
Replace ALL Duplicates - Identificar y reemplazar TODAS las imágenes duplicadas
"""

import os
import sys
import subprocess

# Ejecutar wc_image_automation.py FORZANDO reemplazo
# Strategy: Ejecutar sin --global-dedupe pero con randomización

print("=== REPLACE ALL DUPLICATES - Reemplazo Total ===\n")
print("Ejecutando regeneración COMPLETA sin deduplicación global...")
print("Esto permitirá que cada producto obtenga una imagen, incluso si es similar\n")

cmd = [
    "python3", "wc_image_automation.py",
    "--target", "with-images",  # Productos CON imágenes (los duplicados)
    "--batch-size", "20",
    "--delay", "2",
    "--providers", "unsplash,pexels,inaturalist,wikimedia,flickr",
    "--enrich-queries",
    "--log-level", "INFO"
]

print(f"Comando: {' '.join(cmd)}\n")
print("Iniciando...\n")

result = subprocess.run(cmd, cwd="/home/viveroloscocos.com.ar")
sys.exit(result.returncode)
