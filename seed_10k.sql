SET search_path TO cinema;

-- Добавляем сеансы, если их нет (гарантия наличия мест)
INSERT INTO screenings (movie_id, hall_id, start_time, end_time, base_price)
SELECT 
    (SELECT id FROM movies LIMIT 1),
    h.id,
    s.t,
    s.t + interval '2 hours',
    500.00
FROM (SELECT id FROM halls) h
CROSS JOIN LATERAL generate_series('2025-01-01'::timestamp, '2025-02-01'::timestamp, interval '4 hours') AS s(t)
ON CONFLICT DO NOTHING;

-- Очистка и вставка ровно 10 000 билетов
TRUNCATE tickets CASCADE;

INSERT INTO tickets (booking_id, screening_id, seat_blueprint_id, actual_price)
SELECT 
    (SELECT id FROM bookings LIMIT 1),
    s.id,
    sb.id,
    500.00
FROM (SELECT id, hall_id FROM screenings LIMIT 5000) s
JOIN (SELECT id, hall_id FROM seats_blueprint LIMIT 50) sb ON s.id IS NOT NULL AND s.hall_id = sb.hall_id
LIMIT 10000;

ANALYZE tickets;
