#!/bin/sh
set -e

nodes="
redis-node-1
redis-node-2
redis-node-3
"

for node in $nodes
do
  until redis-cli -h $node -a "$REDIS_PASSWORD" ping > /dev/null 2>&1
  do
    sleep 2
  done
done

cluster_state=$(redis-cli -h redis-node-1 -a "$REDIS_PASSWORD" cluster info 2>/dev/null | grep cluster_state | cut -d: -f2 | tr -d '[:space:]')

if [ "$cluster_state" = "ok" ]; then
  exit 0
fi

redis-cli --cluster create \
  redis-node-1:6379 \
  redis-node-2:6379 \
  redis-node-3:6379 \
  --cluster-replicas 0 \
  -a "$REDIS_PASSWORD" \
  --cluster-yes > /dev/null 2>&1