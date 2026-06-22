#!/bin/sh
set -e

# Apply database migrations on startup (safe to run repeatedly).
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true

exec "$@"