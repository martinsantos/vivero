#!/usr/bin/env python3
import csv
import sys
from decimal import Decimal, InvalidOperation
from pathlib import Path


ROOT = Path(__file__).resolve().parents[2]
FINAL_CSV = ROOT / "data" / "products_woocommerce_final.csv"
NO_PRICE_CSV = ROOT / "data" / "productos_sin_precio.csv"


def read_rows(path):
    if not path.exists():
        print(f"FAIL: missing {path.relative_to(ROOT)}")
        return []

    with path.open(newline="", encoding="utf-8-sig") as handle:
        return list(csv.DictReader(handle))


def valid_positive_price(value):
    if value is None:
        return False

    normalized = value.strip().replace("$", "").replace(".", "").replace(",", ".")
    if not normalized:
        return False

    try:
        return Decimal(normalized) > 0
    except InvalidOperation:
        return False


def main():
    final_rows = read_rows(FINAL_CSV)
    no_price_rows = read_rows(NO_PRICE_CSV)
    failures = []

    if no_price_rows:
        failures.append(f"{len(no_price_rows)} products listed in productos_sin_precio.csv")

    invalid_prices = [
        row for row in final_rows
        if not valid_positive_price(row.get("Regular price"))
    ]
    missing_skus = [row for row in final_rows if not (row.get("SKU") or "").strip()]
    missing_names = [row for row in final_rows if not (row.get("Name") or "").strip()]

    if invalid_prices:
        failures.append(f"{len(invalid_prices)} products with missing, zero, or invalid Regular price")
    if missing_skus:
        failures.append(f"{len(missing_skus)} products with missing SKU")
    if missing_names:
        failures.append(f"{len(missing_names)} products with missing Name")

    print("== Catalog data health ==")
    print(f"Products in final CSV: {len(final_rows)}")
    print(f"Products in no-price CSV: {len(no_price_rows)}")
    print(f"Invalid regular prices: {len(invalid_prices)}")
    print(f"Missing SKUs: {len(missing_skus)}")
    print(f"Missing names: {len(missing_names)}")

    if failures:
        print("FAIL:")
        for failure in failures:
            print(f"- {failure}")
        return 1

    print("OK")
    return 0


if __name__ == "__main__":
    sys.exit(main())
