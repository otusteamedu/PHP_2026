#!/usr/bin/env bash

set -e

# 1. Проверка окружения
if [[ ! -f .env ]]; then
    echo "[!] Error: .env file not found. Create it from .env.example."
    exit 1
fi

# 2. Подготовка прав для VirtualBox Shared Folders
echo "[+] Setting permissions for VBox Shared Folders..."
chmod -R 777 src/ 2>/dev/null || true

# 3. Полная перезагрузка инфраструктуры
echo "[+] Destroying old traces and building the Fortress..."
docker compose down -v --remove-orphans
docker compose up -d --build --scale php=3

# 4. Ожидание готовности Redis (Healthcheck)
echo "[+] Waiting for Redis nodes to wake up..."
sleep 5 # Даем контейнерам время прогрузиться

# 5. Инициализация Redis Cluster (Пункт 3.1)
echo "[+] Orchestrating Redis Cluster..."
docker compose exec redis-1 sh -c 'redis-cli -a SecurePass123 --cluster create \
$(getent hosts redis-1 | awk "{print \$1}"):6379 \
$(getent hosts redis-2 | awk "{print \$1}"):6379 \
$(getent hosts redis-3 | awk "{print \$1}"):6379 \
--cluster-replicas 0 --cluster-yes'

#6. Установка PHP зависимостей
echo "[+] Orchestrating Composer dependencies..."
docker compose exec -u www-data php composer install --no-interaction --optimize-autoloader

echo "--------------------------------------------------"
echo "[SUCCESS] Infrastructure is online and clustered."
echo "[?] Access: http://192.168.56.101"
echo "[?] Redis Status:"
docker compose exec redis-1 sh -c 'redis-cli -a "$REDIS_PASSWORD" cluster nodes'

