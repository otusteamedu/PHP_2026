SET search_path TO cinema;

INSERT INTO cinemas (name, city, address) VALUES
    ('КАРО 8', 'Краснодар', 'улица Володи Головатого, дом 313'),
    ('Монитор', 'Краснодар', 'улица Дзержинского, дом 100');

INSERT INTO halls (cinema_id, name) VALUES
    (1, 'Зал 1'),
    (1, 'Зал 2'),
    (2, 'Красный зал'),
    (2, 'Белый зал');

INSERT INTO seat_types (type) VALUES
    ('Стандарт'),
    ('ПолуВИП'),
    ('ВИП');

INSERT INTO seats (hall_id, seat_type_id, row_code, seat_number) VALUES
    (1, 1, '1', 1), (1, 1, '1', 2), (1, 1, '1', 3),
    (1, 2, 'VIP', 1), (1, 2, 'VIP', 2);

INSERT INTO seats (hall_id, seat_type_id, row_code, seat_number) VALUES
    (3, 1, 'A', 1), (3, 1, 'A', 2);

    INSERT INTO seats (hall_id, seat_type_id, row_code, seat_number) VALUES
    (4, 1, 'A', 1), (4, 1, 'A', 2);

INSERT INTO movies (title, original_title, duration, release_date, age_rating) VALUES
    ('Звёздные войны: Супер главная надежда', 'Звёздные во́йны', 150, '2026-03-28', 'PG-13'),
    ('Дюна: Часть третья', 'Дюна', 140, '2026-03-29', 'PG-13');

INSERT INTO screenings (hall_id, movie_id, starts_at, ends_at, status) VALUES
    (1, 1, '2026-04-10 12:00:00+00', '2026-04-10 14:30:00+00', 'scheduled'),
    (3, 2, '2026-04-10 16:00:00+00', '2026-04-10 18:20:00+00', 'scheduled');

INSERT INTO screening_prices (screening_id, seat_type_id, price_amount) VALUES
    (1, 1, 500.00),
    (1, 2, 1000.00),
    (2, 1, 600.00),
    (2, 2, 2000.00);

INSERT INTO customers (email, phone) VALUES
    ('a@example.com', '123456789'),
    ('b@example.com', '987654321');

INSERT INTO orders (customer_id, status, paid_at) VALUES
    (1, 'paid', NOW()),
    (2, 'paid', NOW());

INSERT INTO tickets (order_id, screening_id, seat_id, hall_id, price_amount, status) VALUES
    (1, 1, 1, 1, 500.00, 'paid'), 
    (1, 1, 4, 1, 1000.00, 'paid'),
    (2, 2, 6, 3, 600.00, 'paid'),
    (2, 2, 7, 3, 2000.00, 'paid');