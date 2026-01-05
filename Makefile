SHELL := /bin/bash
PY ?= python3
SCRIPT := wc_image_automation.py

# Defaults (override via: make wc-images-run PROVIDERS="unsplash,pixabay" BATCH=12 DELAY=2)
PROVIDERS ?= unsplash
BATCH ?= 10
DELAY ?= 1
LOG ?= INFO
SIZE ?= 1200x1200
QUALITY ?= 85
MINRES ?= 800x600
WATERMARK ?=
TARGET ?= missing            # missing | with-images | all
ASSIGN ?= featured           # featured | append-gallery
ENRICH ?= 0                  # 1/true/yes/on to enable
PREFIX ?=                    # optional query prefix, e.g. VIVERO DE PLANTAS
EXTRA_WM := $(if $(strip $(WATERMARK)),--watermark "$(WATERMARK)",)
EXTRA_TARGET := --target $(TARGET)
EXTRA_ASSIGN := --assign-mode $(ASSIGN)
EXTRA_ENRICH := $(if $(filter 1 true yes on,$(ENRICH)),--enrich-queries,)
EXTRA_PREFIX := $(if $(strip $(PREFIX)),--query-prefix "$(PREFIX)",)

.PHONY: wc-images-help wc-images-dry-run wc-images-run wc-images-resume

wc-images-help:
	@echo "Targets:"
	@echo "  wc-images-dry-run  - Buscar y procesar imágenes (sin cambios en WP)"
	@echo "  wc-images-run      - Ejecutar en modo real (sube y asigna)"
	@echo "  wc-images-resume   - Reintentar pendientes/fallidos usando estado"
	@echo "Variables (override): PROVIDERS, BATCH, DELAY, LOG, SIZE, QUALITY, MINRES, WATERMARK, TARGET, ASSIGN, ENRICH, PREFIX"
	@echo "Ej.: make wc-images-run PROVIDERS=unsplash,pixabay WATERMARK='Vivero'"
	@echo "Ej.: make wc-images-run TARGET=with-images ASSIGN=append-gallery ENRICH=1"
	@echo "Ej.: make wc-images-run PREFIX='VIVERO DE PLANTAS' ASSIGN=replace-featured"

wc-images-dry-run:
	$(PY) $(SCRIPT) \
	  --dry-run \
	  --providers $(PROVIDERS) \
	  --batch-size $(BATCH) \
	  --delay $(DELAY) \
	  --log-level $(LOG) \
	  --image-size $(SIZE) \
	  --quality $(QUALITY) \
	  --min-resolution $(MINRES) \
	  $(EXTRA_WM) $(EXTRA_TARGET) $(EXTRA_ASSIGN) $(EXTRA_ENRICH) $(EXTRA_PREFIX)

wc-images-run:
	$(PY) $(SCRIPT) \
	  --providers $(PROVIDERS) \
	  --batch-size $(BATCH) \
	  --delay $(DELAY) \
	  --log-level $(LOG) \
	  --image-size $(SIZE) \
	  --quality $(QUALITY) \
	  --min-resolution $(MINRES) \
	  $(EXTRA_WM) $(EXTRA_TARGET) $(EXTRA_ASSIGN) $(EXTRA_ENRICH) $(EXTRA_PREFIX)

wc-images-resume:
	$(PY) $(SCRIPT) \
	  --resume \
	  --providers $(PROVIDERS) \
	  --batch-size $(BATCH) \
	  --delay $(DELAY) \
	  --log-level $(LOG) \
	  --image-size $(SIZE) \
	  --quality $(QUALITY) \
	  --min-resolution $(MINRES) \
	  $(EXTRA_WM) $(EXTRA_TARGET) $(EXTRA_ASSIGN) $(EXTRA_ENRICH) $(EXTRA_PREFIX)
