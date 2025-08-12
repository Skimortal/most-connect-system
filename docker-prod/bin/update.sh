#!/usr/bin/env bash
set -euo pipefail

# 1) ins Projekt-Verzeichnis wechseln (falls Du das Script von woanders aufrufst)
cd "$(dirname "${BASH_SOURCE[0]}")"

# 2) .env laden
if [ -f ../.env ]; then
  # Export aller Variablen aus .env
  set -o allexport
  source ../.env
  set +o allexport
else
  echo "⚠️  .env nicht gefunden, breche ab."
  exit 1
fi

docker exec "$PHP_CONTAINER_NAME" bin/console doctrine:migrations:migrate --no-interaction --env=prod
docker exec "$PHP_CONTAINER_NAME" bin/console cache:clear --env=prod
docker exec "$PHP_CONTAINER_NAME" bin/console cache:warmup --env=prod

docker exec "$NGINX_CONTAINER_NAME" chown -R www-data:www-data var/ public/
