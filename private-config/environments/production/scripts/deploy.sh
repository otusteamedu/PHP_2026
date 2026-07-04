#!/bin/bash

cd /app/deploy/scripts

echo "Ensuring infrastructure is running..."
sudo docker compose up -d gateway redis rabbitmq

echo "Waiting for services to be ready..."
sleep 5

if [ "$(sudo docker ps -q -f name=blue)" ]; then
    echo "Container 'blue' is running."
    CURRENT=green PREV=blue bash ./_deploy-general.sh
else
    echo "Container 'blue' is not running."
    CURRENT=blue PREV=green bash ./_deploy-general.sh
fi
