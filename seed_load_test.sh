#!/usr/bin/env bash

# Strict Mode
set -e

# Загружаем переменные
[ -f .env ] && source .env || { echo "Error: .env not found"; exit 1; }

run_sql() {
    docker exec -i postgres_cinema psql -U "$DB_USER" -d "$DB_NAME" -c "SET search_path TO cinema; $1"
}

echo "--- Подготовка базы для малого замера (10 000 строк) ---"

# 1. Генерируем сеансы (2015-2026)
run_sql "INSERT INTO screenings (movie_id, hall_id, start_time, end_time, base_price)
SELECT (SELECT id FROM movies LIMIT 1), h.id, s.t, s.t + interval '2 hours', 500.00
FROM (SELECT id FROM halls) h
CROSS JOIN LATERAL generate_series('2015-01-01'::timestamp, '2026-12-31'::timestamp, interval '135 minutes') AS s(t)
ON CONFLICT DO NOTHING;"

# 2. Создаем бронирования
run_sql "INSERT INTO bookings (customer_id, booking_time, total_amount)
SELECT (SELECT id FROM customers LIMIT 1), '2015-01-01'::timestamp + (random() * interval '11 years'), 0
FROM generate_series(1, 500000) ON CONFLICT DO NOTHING;"

# 3. Наполняем билеты (ровно 10 000)
run_sql "TRUNCATE tickets CASCADE;
INSERT INTO tickets (booking_id, screening_id, seat_blueprint_id, actual_price)
SELECT (SELECT id FROM bookings LIMIT 1), s.id, sb.id, 500.00
FROM (SELECT id, hall_id FROM screenings LIMIT 5000) s
JOIN (SELECT id, hall_id FROM seats_blueprint LIMIT 50) sb ON s.hall_id = sb.hall_id
LIMIT 10000;
ANALYZE tickets;"
