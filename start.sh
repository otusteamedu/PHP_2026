#!/usr/bin/env bash
set -euo pipefail

# LF clean
sed -i 's/\r$//' "$0"

echo "--- [1/2] Запуск инфраструктуры (BuildKit) ---"
COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker compose up -d

echo "--- [2/2] Тюнинг прав доступа (VirtualBox Fix) ---"
# Даем права на сокет и исходники
docker exec -u root evgeny87-php mkdir -p /var/run/php
docker exec -u root evgeny87-php chown -R www-data:www-data /var/run/php /var/www/html
docker exec -u root evgeny87-angie chown -R www-data:www-data /var/run/php

echo "СИСТЕМА СТАБИЛЬНА"
echo "Angie: http://192.168.56.20"
echo "Kibana: http://192.168.56.20:5601"

