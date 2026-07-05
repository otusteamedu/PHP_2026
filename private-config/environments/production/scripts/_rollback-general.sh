#!/bin/bash

if [ -z "$CURRENT" ]; then
     echo "Please set CURRENT variable"
     exit 1
fi

if [ -z "$PREV" ]; then
     echo "Please set PREV variable"
     exit 1
fi

function startPrevRelease {
     cd $DEPLOY_DIR
     echo "Starting previous web containers: ${PREV} and ${PREV}_nginx..."
     sudo docker compose up -d $PREV ${PREV}_nginx

     echo "Starting previous consumer: ${PREV}_consumer..."
     sudo docker compose up -d ${PREV}_consumer

     sleep 10

     sudo docker exec --user root gateway sh -c "echo \"set server blue_green/${PREV} state ready\" | socat stdio unix-connect:/sock/admin.sock"
}

function stopCurrentRelease {
     cd $DEPLOY_DIR

     sudo docker exec --user root gateway sh -c "echo \"set server blue_green/${CURRENT} state maint\" | socat stdio unix-connect:/sock/admin.sock"

     sleep 10

     echo "Stopping current web containers: ${CURRENT} and ${CURRENT}_nginx..."
     sudo docker compose stop $CURRENT ${CURRENT}_nginx

     echo "Stopping current consumer: ${CURRENT}_consumer..."
     sudo docker compose stop ${CURRENT}_consumer
}

startPrevRelease
stopCurrentRelease
