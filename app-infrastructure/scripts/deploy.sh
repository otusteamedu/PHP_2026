#!/bin/bash
set -euo pipefail

export DEPLOY_DIR="${DEPLOY_DIR:-$(cd "$(dirname "$0")/.." && pwd)}"

COMPOSE="docker compose -f $DEPLOY_DIR/docker-compose.production.yml"

cd "$DEPLOY_DIR"

$COMPOSE up -d gateway db rabbitmq

ACTIVE=$(
docker exec gateway \
sh -c 'echo "show stat" | socat stdio unix-connect:/sock/admin.sock' \
| awk -F, '
$1=="blue_green" && $2=="blue" && $18=="UP" {print "blue"}
$1=="blue_green" && $2=="green" && $18=="UP" {print "green"}'
)

if [ "$ACTIVE" = "blue" ]; then
    export CURRENT=green
    export PREV=blue
else
    export CURRENT=blue
    export PREV=green
fi

exec "$DEPLOY_DIR/scripts/_deploy-general.sh"