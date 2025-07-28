#!/usr/bin/env bash
set -euo pipefail

# 1) ins Projekt-Verzeichnis wechseln (falls Du das Script von woanders aufrufst)
cd "$(dirname "${BASH_SOURCE[0]}")"

# 2) .env laden
if [ -f .env ]; then
  # Export aller Variablen aus .env
  set -o allexport
  source .env
  set +o allexport
else
  echo "⚠️  .env nicht gefunden, breche ab."
  exit 1
fi

docker exec -i "$DB_CONTAINER_NAME" mysql -u$MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE < init.sql
