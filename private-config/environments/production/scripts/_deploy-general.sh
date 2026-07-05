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
    echo "Cloning code to $DEPLOY_DIR/releases/$CURRENT"
    sudo rm -rf $DEPLOY_DIR/releases/$CURRENT
    git clone --branch main https://github.com/${GITHUB_REPO}.git $DEPLOY_DIR/releases/$CURRENT
}

function buildApp {
    echo "Building $CURRENT..."

    cd $DEPLOY_DIR

    sudo docker compose build php-cli
    sudo docker compose run --rm \
            -v $DEPLOY_DIR/releases/$CURRENT:/app \
            --user root \
            php-cli \
            composer install --no-interaction --optimize-autoloader --no-dev

    sudo docker compose build $CURRENT ${CURRENT}_nginx ${CURRENT}_consumer
    sudo docker compose up -d $CURRENT
}

function changeOwnership {
    sudo chown aleksandrkrasnyatov:aleksandrkrasnyatov -R $DEPLOY_DIR/releases/$CURRENT
}

function startCurrentRelease {
    echo "Starting ${CURRENT}_nginx..."
    cd $DEPLOY_DIR
    sudo docker compose up -d ${CURRENT}_nginx

    echo "Starting ${CURRENT}_consumer..."
    sudo docker compose up -d ${CURRENT}_consumer

    sleep 10

    echo "Switching traffic to $CURRENT..."
    sudo docker exec --user root gateway sh -c "echo \"set server blue_green/${CURRENT} state ready\" | socat stdio unix-connect:/sock/admin.sock"
}

function stopPrevRelease {
    echo "Stopping $PREV..."
    cd $DEPLOY_DIR

    sudo docker exec --user root gateway sh -c "echo \"set server blue_green/${PREV} state maint\" | socat stdio unix-connect:/sock/admin.sock"

    sleep 10
    sudo docker compose stop $PREV ${PREV}_nginx

    echo "Stopping ${PREV}_consumer..."
    sudo docker compose stop ${PREV}_consumer
}

downloadNewCode
buildApp
changeOwnership
startCurrentRelease
stopPrevRelease

echo "Deploy completed! Active: $CURRENT"
