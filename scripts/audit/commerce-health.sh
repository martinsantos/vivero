#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$ROOT"

echo "== PHP lint: theme-loscocos-child =="
for f in $(find theme-loscocos-child -path '*/node_modules' -prune -o -name '*.php' -print); do
  php -l "$f" >/tmp/loscocos_php_lint.out 2>&1 || {
    echo "FAIL: $f"
    cat /tmp/loscocos_php_lint.out
    exit 1
  }
done
echo "OK"

echo "== CSS build =="
(cd theme-loscocos-child && npm run build)

echo "== Catalog data =="
if scripts/audit/catalog-health.py; then
  echo "OK: catalog data passes baseline checks"
else
  echo "WARN: local import CSVs have commerce blockers; review output above"
fi

echo "== Local secret scan =="
if scripts/audit/secret-scan.sh; then
  echo "OK: no obvious active secrets"
else
  echo "WARN: secret scan found matches; review output above"
fi

echo "== Done =="
