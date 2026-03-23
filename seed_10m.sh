#!/usr/bin/env bash
set -e
source .env

run_sql() {
    docker exec -i postgres_cinema psql -U "$DB_USER" -d "$DB_NAME" -c "SET search_path TO cinema; $1"
}

echo "1/2: Очистка старых билетов..."
run_sql "TRUNCATE tickets CASCADE;"

echo "2/2: Симуляция продаж (10 000 000 билетов)..."
# Мы берем сеансы и соединяем их с местами в их залах, пока не наберем ровно 10 млн.
run_sql "
INSERT INTO tickets (booking_id, screening_id, seat_blueprint_id, actual_price)
SELECT 
    (SELECT id FROM bookings LIMIT 1),
    screening_id,
    seat_id,
    (400 + random() * 1100)::numeric(10,2)
FROM (
    SELECT 
        s.id AS screening_id, 
        sb.id AS seat_id
    FROM screenings s
    JOIN seats_blueprint sb ON s.hall_id = sb.hall_id
    LIMIT 10000000
) AS inventory;
"

run_sql "ANALYZE tickets;"
run_sql "SELECT count(*) FROM tickets;"
