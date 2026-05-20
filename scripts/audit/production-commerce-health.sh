#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${LOSCOS_BASE_URL:-https://viveroloscocos.com.ar}"

echo "== Public endpoint health =="

check_status() {
  local path="$1"
  local expected="$2"
  local status

  status="$(curl -I -sS -o /dev/null -w '%{http_code}' "${BASE_URL}${path}")"
  if [ "$status" = "$expected" ]; then
    echo "OK: ${path} -> ${status}"
  else
    echo "FAIL: ${path} -> ${status}, expected ${expected}"
    return 1
  fi
}

check_status "/" "200"
check_status "/ingre" "200"
check_status "/wp-login.php" "404"
check_status "/xmlrpc.php" "403"
check_status "/wp-json/wp/v2/users" "404"

echo "== Optional WooCommerce catalog health =="
if [ -z "${LOSCOS_SSH_TARGET:-}" ]; then
  echo "SKIP: set LOSCOS_SSH_TARGET to run WP-CLI catalog checks over SSH"
  exit 0
fi

ssh "$LOSCOS_SSH_TARGET" "cd /home/viveroloscocos.com.ar/public_html && wp eval '
\$products = wc_get_products([\"limit\" => -1, \"status\" => [\"publish\", \"draft\", \"private\"]]);
\$total = count(\$products);
\$bad = 0;
foreach (\$products as \$product) {
    \$price = \$product->get_regular_price();
    if (\$price === \"\" || !is_numeric(\$price) || (float) \$price <= 0) {
        \$bad++;
    }
}
echo \"products={\$total} bad_regular_price={\$bad}\n\";
exit(\$bad > 0 ? 1 : 0);
' --allow-root"
