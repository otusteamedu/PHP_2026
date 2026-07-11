#!/bin/bash
set -e

if [ -z "$CURRENT" ] || [ -z "$PREV" ]; then
     exit 1
fi

cd "$DEPLOY_DIR"

docker compose -f docker-compose.production.yml up -d $PREV ${PREV}_consumer ${PREV}_nginx

ATTEMPTS=0
until [ "$(docker inspect -f '{{.State.Health.Status}}' $PREV)" == "healthy" ]; do
    sleep 2
    ATTEMPTS=$((ATTEMPTS+1))
    if [ "$ATTEMPTS" -eq 30 ]; then
         echo "ФАТАЛЬНАЯ ОШИБКА: Предыдущая версия ($PREV) также не проходит Healthcheck!"
         echo "Требуется ручное вмешательство."
         exit 1
    fi
done

docker exec gateway sh -c "echo \"set server blue_green/${PREV} state ready\" | socat stdio unix-connect:/sock/admin.sock"

sleep 5

docker exec gateway sh -c "echo \"set server blue_green/${CURRENT} state maint\" | socat stdio unix-connect:/sock/admin.sock"

docker compose -f docker-compose.production.yml stop $CURRENT ${CURRENT}_nginx ${CURRENT}_consumer
