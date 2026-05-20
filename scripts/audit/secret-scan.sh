#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/../.."

if rg -n \
  --glob '!**/node_modules/**' \
  --glob '!wp-admin/**' \
  --glob '!wp-includes/**' \
  --glob '!wp-content/plugins/**' \
  --glob '!docs/superpowers/**' \
  --glob '!docs/archive/**' \
  --glob '!_archive/**' \
  --glob '!scraped_data/**' \
  --glob '!*.xlsx' \
  --glob '!*.zip' \
  --glob '!**/package-lock.json' \
  -e 'sshpass -p[[:space:]]+['"'"'"][^'"'"'"]+['"'"'"]' \
  -e 'root@[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+' \
  -e 'define\([[:space:]]*['"'"'"]DB_PASSWORD['"'"'"][[:space:]]*,[[:space:]]*['"'"'"][^'"'"'"]+['"'"'"]' \
  -e '(MYSQL_ROOT_PASSWORD|WORDPRESS_DB_PASSWORD|DB_PASSWORD|WP_APP_PASSWORD|WC_CONSUMER_SECRET)[[:space:]]*[:=][[:space:]]*['"'"'"]?[^'"'"'"[:space:]#]+' \
  -e 'c[sk]_[A-Za-z0-9_-]{32,}' \
  -e 'sk-[A-Za-z0-9_-]{20,}' \
  .; then
  echo "FAIL: obvious secret-like values found in active repository files."
  exit 1
else
  status=$?
  if [ "$status" -eq 1 ]; then
    exit 0
  fi
  exit "$status"
fi
