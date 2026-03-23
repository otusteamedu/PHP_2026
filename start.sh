#!/usr/bin/env bash

# Strict Mode: выход при любой ошибке
set -e

# 1. Validation: Проверка наличия .env
if [ ! -f .env ]; then
    echo "Error: .env file not found. Copying from .env.example..."
    cp .env.example .env
    echo "Please edit .env and restart."
    exit 1
fi

# Загружаем переменные (DB_NAME, DB_USER и т.д.)
source .env

# 2. Host-Guest Synergy: Исправление прав для Shared Folders (VirtualBox)
# chown не работает на vboxsf, поэтому просто убеждаемся в наличии папок
mkdir -p pgdata

# 3. Infrastructure: Запуск контейнеров в фоне
docker compose up -d

echo "Waiting for PostgreSQL to be ready..."

# 4. Healthcheck: Ждем готовности порта 5432 внутри контейнера
until docker exec postgres_cinema pg_isready -U "$DB_USER" -d "$DB_NAME" > /dev/null 2>&1; do
  echo -n "."
  sleep 1
done

echo -e "\nDatabase is up! Deploying schema..."

# 5. Initialization: Накатываем DDL и тестовые данные
docker exec -i postgres_cinema psql -U "$DB_USER" -d "$DB_NAME" < init.sql

echo "Deployment complete. Total isolation achieved."