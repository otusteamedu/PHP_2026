#!/bin/sh
set -e

# Apply database migrations on startup (safe to run repeatedly).
# Disabled in prod (AUTO_MIGRATE=0): deploy.sh runs migrations once before the
# rolling update so the scaled-up replicas don't race each other.
if [ "${AUTO_MIGRATE:-1}" = "1" ]; then
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true
fi

exec "$@"