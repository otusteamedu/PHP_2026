#!/bin/bash
set -e

if [ -z "$CURRENT" ] || [ -z "$PREV" ]; then
    echo "Ошибка: Не заданы переменные CURRENT или PREV"
    exit 1
fi

cd "$DEPLOY_DIR"

docker compose -f docker-compose.production.yml pull $CURRENT ${CURRENT}_consumer ${CURRENT}_nginx

docker compose -f docker-compose.production.yml up -d $CURRENT ${CURRENT}_consumer ${CURRENT}_nginx

ATTEMPTS=0
MAX_ATTEMPTS=30

until [ "$(docker inspect -f '{{.State.Health.Status}}' $CURRENT)" == "healthy" ]; do
    sleep 2
    ATTEMPTS=$((ATTEMPTS+1))
    
    if [ "$ATTEMPTS" -eq "$MAX_ATTEMPTS" ]; then
        docker compose -f docker-compose.production.yml stop $CURRENT ${CURRENT}_consumer ${CURRENT}_nginx
        exit 1
    fi
done

docker exec gateway sh -c "echo \"set server blue_green/${CURRENT} state ready\" | socat stdio unix-connect:/sock/admin.sock"

docker exec gateway sh -c "echo \"set server blue_green/${PREV} state maint\" | socat stdio unix-connect:/sock/admin.sock" || true

sleep 5

docker compose -f docker-compose.production.yml stop $PREV ${PREV}_nginx ${PREV}_consumer || true