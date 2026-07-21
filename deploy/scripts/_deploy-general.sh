#!/bin/bash

if [ -z "$CURRENT" ]; then
    echo "Please set CURRENT variable"
    exit 1
fi

if [ -z "$PREV" ]; then
    echo "Please set PREV variable"
    exit 1
fi

function downloadNewCode {
    sudo rm -rf $DEPLOY_DIR/releases/$CURRENT
    git clone http://gitlab-ci-token:${CI_JOB_TOKEN}@${CI_SERVER_FQDN}/${CI_PROJECT_PATH} $DEPLOY_DIR/releases/$CURRENT

    if [ -f "$DEPLOY_DIR/.env" ]; then
        cp "$DEPLOY_DIR/.env" "$DEPLOY_DIR/releases/$CURRENT/.env"
    else
        echo "WARNING: $DEPLOY_DIR/.env not found. Application may fail without env vars."
    fi
}

function buildApp {
    cd $DEPLOY_DIR

    sudo docker compose up -d postgres rabbitmq
    sudo docker compose up -d $CURRENT ${CURRENT}_consumer_new ${CURRENT}_consumer_processing

    sudo docker exec --user root $CURRENT composer install --no-interaction --optimize-autoloader
}

function changeOwnership {
    sudo chown 1000:1000 -R $DEPLOY_DIR/releases/$CURRENT
}

function startCurrentRelease {
    cd $DEPLOY_DIR

    sudo docker compose up -d ${CURRENT}_nginx

    sleep 10

    sudo docker exec --user root gateway sh -c "echo \"set server blue_green/${CURRENT} state ready\" | socat stdio unix-connect:/sock/admin.sock"
}

function stopPrevRelease {
    cd $DEPLOY_DIR

    sudo docker exec --user root gateway sh -c "echo \"set server blue_green/${PREV} state maint\" | socat stdio unix-connect:/sock/admin.sock"

    sleep 10
    sudo docker compose stop $PREV ${PREV}_nginx ${PREV}_consumer_new ${PREV}_consumer_processing
}

downloadNewCode
buildApp
changeOwnership
startCurrentRelease
stopPrevRelease
