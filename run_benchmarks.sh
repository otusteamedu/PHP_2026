#!/usr/bin/env bash

# Strict Mode
set -e

# 1. Загружаем доступы
if [ -f .env ]; then
    source .env
else
    echo "Error: .env file not found."
    exit 1
fi

# 2. Определяем имя файла для отчета (зависит от количества строк)
COUNT=$(docker exec -i postgres_cinema psql -U "$DB_USER" -d "$DB_NAME" -t -c "SELECT count(*) FROM cinema.tickets;" | xargs)
REPORT_FILE="plan_${COUNT}_rows.txt"

echo "--- Начинаем замеры для базы объемом $COUNT строк ---"
echo "=== ANALYSIS ($COUNT rows) ===" > "$REPORT_FILE"

# 3. Цикл по 6 запросам из queries.sql
for i in {1..6}; do
    echo "Замер запроса №$i..."
    echo -e "\n--- QUERY $i ---" >> "$REPORT_FILE"
    
    # Извлекаем SQL-запрос из файла
    QUERY=$(sed -n "/-- $i\./,/;/p" queries.sql)
    
    # Выполняем EXPLAIN ANALYZE
    docker exec -i postgres_cinema psql -U "$DB_USER" -d "$DB_NAME" -c "SET search_path TO cinema; EXPLAIN (ANALYZE, BUFFERS) $QUERY" >> "$REPORT_FILE"
done

echo "--- Замеры завершены! Отчет сохранен в файл: $REPORT_FILE ---"
echo "Итоговое время выполнения (Execution Time):"
grep "Execution Time" "$REPORT_FILE"
