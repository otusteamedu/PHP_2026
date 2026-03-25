#!/usr/bin/env bash
set -e
source .env

run_sql() {
    docker exec -i postgres_cinema psql -U "$DB_USER" -d "$DB_NAME" -c "SET search_path TO cinema; $1"
}

echo "1/3: Расширение инфраструктуры (15 залов и 600к сеансов)..."
# Сначала создаем залы и сеансы, иначе 10 млн билетов не влезут из-за UNIQUE
run_sql "
INSERT INTO halls (cinema_id, name, capacity)
SELECT 1, 'Зал №' || i, 50 FROM generate_series(3, 15) i ON CONFLICT DO NOTHING;

INSERT INTO seats_blueprint (hall_id, seat_row, seat_number, seat_type)
SELECT h.id, 'A', n.i, 'Standard'
FROM halls h CROSS JOIN generate_series(1, 50) n(i) ON CONFLICT DO NOTHING;

INSERT INTO screenings (movie_id, hall_id, start_time, end_time, base_price)
SELECT (SELECT id FROM movies LIMIT 1), h.id, s.t, s.t + interval '2 hours', 500.00
FROM (SELECT id FROM halls) h
CROSS JOIN LATERAL generate_series('2015-01-01'::timestamp, '2026-12-31'::timestamp, interval '135 minutes') AS s(t)
ON CONFLICT DO NOTHING;
"

echo "2/3: Очистка старых билетов..."
run_sql "TRUNCATE tickets CASCADE;"

echo "3/3: Симуляция продаж (10 000 000 билетов)..."
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
echo "Итоговое количество билетов в базе:"
run_sql "SELECT count(*) FROM tickets;"
