#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."

cat > .env.deploy <<EOF
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=${APP_SECRET}

APP_IMAGE=${APP_IMAGE}
WEB_IMAGE=${WEB_IMAGE}
HTTP_PORT=${HTTP_PORT}

POSTGRES_DB=${POSTGRES_DB}
POSTGRES_USER=${POSTGRES_USER}
POSTGRES_PASSWORD=${POSTGRES_PASSWORD}
DATABASE_URL=postgresql://${POSTGRES_USER}:${POSTGRES_PASSWORD}@database:5432/${POSTGRES_DB}?serverVersion=16&charset=utf8

RABBITMQ_USER=${RABBITMQ_USER}
RABBITMQ_PASSWORD=${RABBITMQ_PASSWORD}
MESSENGER_TRANSPORT_DSN=amqp://${RABBITMQ_USER}:${RABBITMQ_PASSWORD}@rabbitmq:5672/%2f/messages
EOF

docker compose -f docker-compose.prod.yml --env-file .env.deploy up -d --pull always
docker compose -f docker-compose.prod.yml --env-file .env.deploy run --rm php php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration