#!/usr/bin/env bash

# Strict Mode: выход при любой ошибке
set -e

# 1. Validation: Проверка наличия .env
if [ ! -f .env ]; then
    echo "❌ Error: .env file not found. Run ./start.sh first."
    exit 1
fi

# Загружаем переменные (DB_NAME, DB_USER)
source .env

# 2. Status Check: Проверяем, запущен ли контейнер postgres_cinema
CONTAINER_STATUS=$(docker inspect -f '{{.State.Running}}' postgres_cinema 2>/dev/null || echo "false")

if [ "$CONTAINER_STATUS" != "true" ]; then
    echo "⚠️  Error: Container 'postgres_cinema' is not running."
    echo "Please run ./start.sh to deploy the infrastructure first."
    exit 1
fi

echo "📊 Calculating Movie Profitability Reports..."
echo "-------------------------------------------"

# 3. Execution: Запуск аналитики внутри изолированной сети
# Флаг --quiet убирает лишний вывод psql (строки 'SET', 'SELECT' и т.д.)
docker exec -i postgres_cinema psql -U "$DB_USER" -d "$DB_NAME" --quiet < analytics.sql

echo "-------------------------------------------"
echo "Report generation complete."