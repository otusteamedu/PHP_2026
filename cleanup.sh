#!/usr/bin/env bash

# Strict Mode
set -e

echo "⚠️  Starting total annihilation of the infrastructure..."

# 1. Shutdown: Остановка и удаление контейнеров, сетей и анонимных volume
docker compose down --volumes --remove-orphans

# 2. Data Wipe: Удаление физических данных PostgreSQL
# Мы используем rm -rf, так как в VirtualBox права на pgdata могут быть специфичными
if [ -d "pgdata" ]; then
    echo "Wiping pgdata directory..."
    rm -rf pgdata
    mkdir -p pgdata
fi

# 3. Cache Clean: Очистка неиспользуемых ресурсов Docker (по желанию)
# docker system prune -f

echo "✅ Cleaned up. Ready for a fresh start."