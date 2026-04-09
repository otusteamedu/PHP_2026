SET search_path TO cinema;
SET synchronous_commit = off;

TRUNCATE cinemas, halls, seat_types, seats, movies, screenings, screening_prices, customers, orders, tickets
RESTART IDENTITY CASCADE;

INSERT INTO cinemas (name, city, address) VALUES
    ('КАРО 8', 'Краснодар', 'улица Володи Головатого, дом 313'),
    ('Монитор', 'Краснодар', 'улица Дзержинского, дом 100');

INSERT INTO halls (cinema_id, name) VALUES
    ((SELECT id FROM cinemas WHERE name = 'КАРО 8'), 'Зал 1'),
    ((SELECT id FROM cinemas WHERE name = 'КАРО 8'), 'Зал 2'),
    ((SELECT id FROM cinemas WHERE name = 'Монитор'), 'Красный зал'),
    ((SELECT id FROM cinemas WHERE name = 'Монитор'), 'Белый зал');

INSERT INTO seat_types (type) VALUES
    ('Стандарт'),
    ('Комфорт'),
    ('VIP');

INSERT INTO seats (hall_id, seat_type_id, row_code, seat_number)
SELECT
    h.id,
    CASE 
        WHEN gs <= 150 THEN 1
        WHEN gs <= 225 THEN 2 
        ELSE 3               
    END,
    'Ряд ' || ((gs - 1) / 10 + 1),
    ((gs - 1) % 10 + 1)           
FROM halls h
CROSS JOIN generate_series(1, 250) AS gs;

INSERT INTO movies (title, original_title, duration, release_date, age_rating)
SELECT
    'Фильм №' || gs,
    'Оригинальное название ' || gs,
    120,             
    '2026-01-01',
    '16+'
FROM generate_series(1, 200) gs;

INSERT INTO customers (email, phone)
SELECT
    'user' || gs || '@example.com',
    '900' || lpad(gs::text, 7, '0')
FROM generate_series(1, 1000) gs;

INSERT INTO screenings (hall_id, movie_id, starts_at, ends_at, status)
SELECT
    (gs % 4) + 1 AS hall_id,
    (gs % 200) + 1 AS movie_id,
    NOW() + (gs * interval '3 hours') AS starts_at,
    NOW() + (gs * interval '3 hours') + interval '2 hours' AS ends_at,
    'scheduled'
FROM generate_series(1, 40000) AS gs;

INSERT INTO screening_prices (screening_id, seat_type_id, price_amount)
SELECT
    s.id AS screening_id,
    st.id AS seat_type_id,
    CASE st.type
        WHEN 'Стандарт' THEN 500.00
        WHEN 'Комфорт' THEN 800.00
        ELSE 1200.00
    END AS price_amount
FROM screenings s
CROSS JOIN seat_types st
WHERE st.type IN ('Стандарт', 'Комфорт', 'VIP');

INSERT INTO orders (customer_id, status, created_at, paid_at)
SELECT
    (gs % 1000) + 1 AS customer_id,
    'paid' AS status,
    NOW() AS created_at,
    NOW() AS paid_at
FROM generate_series(1, 40) AS gs;

INSERT INTO tickets (order_id, screening_id, seat_id, hall_id, price_amount, status)
SELECT
    s.id AS order_id,
    s.id AS screening_id,
    se.id AS seat_id,
    s.hall_id,
    sp.price_amount,
    'paid' AS status
FROM screenings s
JOIN seats se ON se.hall_id = s.hall_id
JOIN screening_prices sp ON sp.screening_id = s.id AND sp.seat_type_id = se.seat_type_id;