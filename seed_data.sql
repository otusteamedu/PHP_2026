SET search_path TO cinema;

-- 1. Полная очистка
TRUNCATE tickets, bookings, screenings, seats_blueprint, halls CASCADE;

-- 2. Создаем 15 залов
INSERT INTO halls (cinema_id, name, capacity)
SELECT 1, 'Зал №' || i, 50
FROM generate_series(1, 15) i;

-- 3. Создаем по 50 мест
INSERT INTO seats_blueprint (hall_id, seat_row, seat_number, seat_type)
SELECT h.id, ((s.i / 10) + 1)::text, (s.i % 10) + 1, 'Standard'
FROM halls h
CROSS JOIN generate_series(0, 49) AS s(i);

-- 4. Сеансы: интервал 2 часа 15 минут (реализм!), период 2015-2026
-- 15 залов * 10 сеансов/день * 365 дней * 11 лет = ~600 000 сеансов
INSERT INTO screenings (movie_id, hall_id, start_time, end_time, base_price)
SELECT 
    (SELECT id FROM movies LIMIT 1),
    h.id,
    s.t,
    s.t + interval '2 hours',
    500.00
FROM halls h
CROSS JOIN LATERAL generate_series('2015-01-01'::timestamp, '2026-12-31'::timestamp, interval '135 minutes') AS s(t);

-- 5. Создаем 500 000 бронирований
INSERT INTO bookings (customer_id, booking_time, total_amount)
SELECT (SELECT id FROM customers LIMIT 1), '2015-01-01'::timestamp + (random() * interval '11 years'), 0
FROM generate_series(1, 500000);

-- 6. ПРОДАЕМ БИЛЕТЫ (Tickets)
-- Чтобы не падать на FK, используем гарантированно существующие ID через подзапрос
INSERT INTO tickets (booking_id, screening_id, seat_blueprint_id, actual_price)
SELECT 
    (SELECT id FROM bookings OFFSET floor(random() * 500000) LIMIT 1),
    s.id,
    sb.id,
    (400 + random() * 1100)::numeric(10,2)
FROM (SELECT id, hall_id FROM screenings LIMIT 100000) s -- Начнем с 100к сеансов для веса
JOIN seats_blueprint sb ON s.hall_id = sb.hall_id
WHERE sb.seat_number <= 30 -- Заполняемость 60% (30 мест из 50)
ON CONFLICT DO NOTHING;

ANALYZE;
