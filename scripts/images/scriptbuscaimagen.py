#!/usr/bin/env python3
"""
Buscar Productos WC - Script corregido para evitar errores al guardar CSV cuando la carpeta de destino no existe.
"""
import os
import sys
import time
import argparse
import logging
from typing import List, Dict, Any, Optional

import pandas as pd
from dotenv import load_dotenv
from tqdm import tqdm

try:
    from woocommerce import API
except Exception:
    API = None

_LOG = logging.getLogger("buscar_productos_wc")


def setup_logging(level=logging.INFO):
    logging.basicConfig(
        level=level,
        format="%(asctime)s %(levelname)s: %(message)s",
        datefmt="%Y-%m-%d %H:%M:%S",
    )


def ensure_parent_dir_for_file(path: str) -> None:
    parent = os.path.dirname(os.path.abspath(path))
    if parent and not os.path.exists(parent):
        os.makedirs(parent, exist_ok=True)


def load_config(env_file: Optional[str] = None) -> Dict[str, Optional[str]]:
    if env_file:
        load_dotenv(env_file)
    else:
        load_dotenv()
    return {
        "WORDPRESS_URL": os.getenv("WORDPRESS_URL"),
        "WC_CONSUMER_KEY": os.getenv("WC_CONSUMER_KEY"),
        "WC_CONSUMER_SECRET": os.getenv("WC_CONSUMER_SECRET"),
    }


def validate_and_normalize_url(url: Optional[str]) -> str:
    if not url:
        raise ValueError(
            "La variable de entorno WORDPRESS_URL no está definida. Agregala en tu archivo .env o exportala en el entorno: WORDPRESS_URL=http://localhost:8080"
        )
    url = url.strip()
    if not url:
        raise ValueError("WORDPRESS_URL está vacía.")
    if not (url.startswith("http://") or url.startswith("https://")):
        _LOG.info("WORDPRESS_URL no contiene esquema, se añadirá 'http://' por defecto.")
        url = "http://" + url
    return url


def init_wcapi_safe(url: str, key: Optional[str], secret: Optional[str]):
    if API is None:
        raise RuntimeError("La librería 'woocommerce' no está instalada o no se puede importar. Instalala con: pip install woocommerce")
    if not key or not secret:
        _LOG.warning("WC_CONSUMER_KEY o WC_CONSUMER_SECRET no están definidas. Puede que algunas operaciones requieran credenciales.")
    return API(url=url, consumer_key=key or "", consumer_secret=secret or "", version="wc/v3")


def load_product_names_from_csv(csv_path: str) -> List[str]:
    if not os.path.exists(csv_path):
        raise FileNotFoundError(f"CSV no encontrado: {csv_path}")
    encodings = ["utf-8", "latin1"]
    last_exc = None
    df = None
    for enc in encodings:
        try:
            df = pd.read_csv(csv_path, encoding=enc)
            break
        except Exception as e:
            last_exc = e
    if df is None:
        raise last_exc
    candidates = ["nombre_producto", "nombre", "name", "product_name", "producto"]
    columns_lower = {c.lower(): c for c in df.columns}
    chosen_col = None
    for cand in candidates:
        if cand in columns_lower:
            chosen_col = columns_lower[cand]
            break
    if chosen_col is None:
        for c in df.columns:
            if "nombre" in c.lower() or "product" in c.lower() or "name" == c.lower():
                chosen_col = c
                break
    if chosen_col is None:
        raise ValueError(f"No se encontró una columna con el nombre del producto en el CSV. Columnas disponibles: {list(df.columns)}. Asegurate de tener una columna 'nombre_producto' o 'name'.")
    series = df[chosen_col].dropna().astype(str).str.strip()
    names = [s for s in series.tolist() if s]
    seen = set()
    result = []
    for n in names:
        if n not in seen:
            seen.add(n)
            result.append(n)
    return result


class MockResponse:
    def __init__(self, json_data, status_code=200):
        self._json = json_data
        self.status_code = status_code
    def json(self):
        return self._json


class MockWCAPI:
    def get(self, endpoint: str, params: Optional[Dict[str, Any]] = None):
        term = (params or {}).get("search") if params else None
        data = [{"id": 1000, "name": f"{term} - mock match", "status": "publish", "images": []}]
        return MockResponse(data, 200)


def safe_get_first_image_src(product_obj: Dict[str, Any]) -> Optional[str]:
    images = product_obj.get("images") or []
    if not isinstance(images, list) or len(images) == 0:
        return None
    first = images[0]
    if not isinstance(first, dict):
        return None
    return first.get("src") or first.get("url") or None


def search_products_in_wc(wcapi, terms: List[str], delay: float = 0.0) -> List[Dict[str, Any]]:
    results = []
    for term in tqdm(terms, desc="Buscando productos", unit="producto"):
        try:
            params = {"search": term, "per_page": 100}
            resp = wcapi.get("products", params=params)
        except Exception as e:
            _LOG.error("Error llamando a la API para '%s': %s", term, e)
            continue
        if getattr(resp, "status_code", None) != 200:
            _LOG.warning("Respuesta no OK para '%s': %s", term, getattr(resp, "status_code", None))
            continue
        try:
            data = resp.json()
        except Exception as e:
            _LOG.error("No se pudo parsear JSON para '%s': %s", term, e)
            continue
        if not isinstance(data, list):
            _LOG.debug("La respuesta no es una lista para '%s': %s", term, type(data))
            continue
        for p in data:
            results.append({
                "search_term": term,
                "id": p.get("id"),
                "nombre": p.get("name"),
                "status": p.get("status"),
                "imagen": safe_get_first_image_src(p),
            })
        if delay and delay > 0:
            time.sleep(delay)
    return results


def save_results_csv(results: List[Dict[str, Any]], out_path: str) -> None:
    ensure_parent_dir_for_file(out_path)
    df = pd.DataFrame(results)
    df.to_csv(out_path, index=False)
    _LOG.info("Resultados guardados en %s (registros: %d)", out_path, len(results))


def build_argparser() -> argparse.ArgumentParser:
    p = argparse.ArgumentParser(description="Buscar productos en WooCommerce desde un CSV de términos")
    p.add_argument("--csv", required=False, help="Ruta al CSV con la lista de productos (columna nombre_producto / name)")
    p.add_argument("--out", default="/mnt/data/productos_wc_encontrados.csv", help="Archivo CSV de salida")
    p.add_argument("--env-file", default=None, help="Archivo .env a utilizar (opcional)")
    p.add_argument("--delay", type=float, default=0.0, help="Delay entre requests (segundos)")
    p.add_argument("--use-mock", action="store_true", help="Usar API mock en lugar de WooCommerce (para pruebas)")
    p.add_argument("--dry-run", action="store_true", help="No llamar a la API: genera lista de términos y sale")
    p.add_argument("--run-tests", action="store_true", help="Ejecutar tests de humo incluidos")
    p.add_argument("--strict", action="store_true", help="Si se activa y faltan argumentos, el script fallará (útil en CI).")
    p.add_argument("--log-level", default="INFO", help="Nivel de logging (DEBUG, INFO, WARNING)")
    return p


def main(argv: Optional[List[str]] = None):
    parser = build_argparser()
    if argv is None and len(sys.argv) <= 1:
        setup_logging()
        _LOG.info("No se pasaron argumentos. Ejecutando smoke tests por defecto (modo seguro).")
        run_smoke_tests()
        return
    try:
        args = parser.parse_args(argv)
    except SystemExit:
        parser.print_help()
        sys.exit(2)
    level = getattr(logging, args.log_level.upper(), logging.INFO)
    setup_logging(level)
    if args.run_tests:
        _LOG.info("Ejecutando tests de humo (run_tests) solicitados por argumento.")
        run_smoke_tests()
        return
    if not args.csv:
        if args.strict:
            _LOG.error("--csv es requerido en modo --strict. Abortando.")
            sys.exit(2)
        _LOG.warning("No se proporcionó --csv. Ejecutando smoke tests en lugar de abortar.")
        run_smoke_tests()
        return
    try:
        terms = load_product_names_from_csv(args.csv)
    except Exception as e:
        _LOG.error("Error cargando CSV: %s", e)
        sys.exit(2)
    _LOG.info("Términos de búsqueda cargados: %d", len(terms))
    if args.dry_run:
        out_df = pd.DataFrame({"search_term": terms})
        ensure_parent_dir_for_file(args.out)
        out_df.to_csv(args.out, index=False)
        _LOG.info("Dry-run: lista de términos guardada en %s", args.out)
        print(out_df.head(30).to_string(index=False))
        return
    cfg = load_config(args.env_file)
    if args.use_mock:
        _LOG.info("Modo mock activado: no se usará WooCommerce real.")
        wcapi = MockWCAPI()
    else:
        try:
            wc_url = validate_and_normalize_url(cfg.get("WORDPRESS_URL"))
        except ValueError as ve:
            _LOG.error(str(ve))
            _LOG.error("Si estás en entorno local podés crear un .env con: WORDPRESS_URL=http://localhost:8080")
            sys.exit(3)
        wc_key = cfg.get("WC_CONSUMER_KEY")
        wc_secret = cfg.get("WC_CONSUMER_SECRET")
        try:
            wcapi = init_wcapi_safe(wc_url, wc_key, wc_secret)
        except Exception as e:
            _LOG.error("No se pudo inicializar la API de WooCommerce: %s", e)
            sys.exit(4)
    results = search_products_in_wc(wcapi, terms, delay=args.delay)
    try:
        save_results_csv(results, args.out)
    except Exception as e:
        _LOG.error("Error guardando resultados: %s", e)
        sys.exit(5)
    print(f"Se encontraron {len(results)} resultados. Guardados en: {args.out}")


def _create_sample_csv(path: str):
    ensure_parent_dir_for_file(path)
    df = pd.DataFrame({"nombre_producto": ["Zapato deportivo", "Camisa blanca", "Auriculares Bluetooth"]})
    df.to_csv(path, index=False)


def run_smoke_tests():
    setup_logging()
    tmp = "/mnt/data/_test_products_sample.csv"
    _create_sample_csv(tmp)
    sys_argv = ["--csv", tmp, "--out", "/mnt/data/_test_out.csv", "--use-mock", "--log-level", "DEBUG"]
    _LOG.info("Ejecutando smoke test con mock API...")
    main(sys_argv)
    _LOG.info("Smoke test finalizado. Revisa /mnt/data/_test_out.csv")


def run_env_mock_test():
    setup_logging()
    orig = {k: os.environ.get(k) for k in ("WORDPRESS_URL", "WC_CONSUMER_KEY", "WC_CONSUMER_SECRET")}
    try:
        os.environ["WORDPRESS_URL"] = "http://localhost:8080"
        os.environ["WC_CONSUMER_KEY"] = "ck_test"
        os.environ["WC_CONSUMER_SECRET"] = "cs_test"
        tmp = "/mnt/data/_test_products_sample_2.csv"
        _create_sample_csv(tmp)
        args = ["--csv", tmp, "--out", "/mnt/data/_test_out_2.csv", "--use-mock"]
        _LOG.info("Ejecutando run_env_mock_test...")
        main(args)
        _LOG.info("run_env_mock_test finalizado. Revisa /mnt/data/_test_out_2.csv")
    finally:
        for k, v in orig.items():
            if v is None:
                os.environ.pop(k, None)
            else:
                os.environ[k] = v


def run_all_tests():
    run_smoke_tests()
    run_env_mock_test()


if __name__ == "__main__":
    if len(sys.argv) <= 1:
        setup_logging()
        _LOG.info("No se recibieron argumentos en la línea de comandos. Ejecutando smoke tests por defecto.")
        run_smoke_tests()
    else:
        main()
