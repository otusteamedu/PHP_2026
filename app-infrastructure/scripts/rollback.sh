#!/bin/bash
set -euo pipefail

export DEPLOY_DIR="${DEPLOY_DIR:-$(cd "$(dirname "$0")/.." && pwd)}"

ACTIVE=$(
docker exec gateway \
sh -c 'echo "show stat" | socat stdio unix-connect:/sock/admin.sock' \
| awk -F, '
$1=="blue_green" && $2=="blue" && $18=="UP" {print "blue"}
$1=="blue_green" && $2=="green" && $18=="UP" {print "green"}'
)

if [ "$ACTIVE" = "blue" ]; then
    export CURRENT=blue
    export PREV=green
else
    export CURRENT=green
    export PREV=blue
fi

exec "$DEPLOY_DIR/scripts/_rollback-general.sh"