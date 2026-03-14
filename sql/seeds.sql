TRUNCATE TABLE ticket RESTART IDENTITY CASCADE;
TRUNCATE TABLE showtime RESTART IDENTITY CASCADE;
TRUNCATE TABLE customer RESTART IDENTITY CASCADE;
TRUNCATE TABLE hall RESTART IDENTITY CASCADE;
TRUNCATE TABLE movie RESTART IDENTITY CASCADE;
TRUNCATE TABLE cinema RESTART IDENTITY CASCADE;

INSERT INTO cinema (name, address) VALUES
    ('Cinema One', '123 Main St'),
    ('Cinema Two', '456 Broadway Ave');

INSERT INTO hall (cinema_id, name, rows_count, seats_per_row) VALUES
    (1, 'Hall A', 5, 10),
    (1, 'Hall B', 6, 12),
    (2, 'Hall 1', 4, 8);

INSERT INTO movie (title, duration_minutes, rating, price) VALUES
    ('Spider-Man: No Way Home', 150, 'PG-13', 10.00),
    ('The Batman', 140, 'PG-13', 12.00),
    ('Avatar 2', 180, 'PG-13', 15.00);

INSERT INTO showtime (hall_id, movie_id, start_time) VALUES
    (1, 1, '2026-03-10 12:00:00'),
    (1, 2, '2026-03-10 15:00:00'),
    (2, 1, '2026-03-10 14:00:00'),
    (2, 3, '2026-03-10 18:00:00'),
    (3, 2, '2026-03-10 16:00:00');

INSERT INTO customer (first_name, last_name, email, phone) VALUES
    ('Alice', 'Smith', 'alice@example.com', '123456789'),
    ('Bob', 'Johnson', 'bob@example.com', '987654321'),
    ('Charlie', 'Brown', 'charlie@example.com', '555666777'),
    ('Diana', 'Prince', 'diana@example.com', '111222333'),
    ('Eve', 'Adams', 'eve@example.com', '444555666');

INSERT INTO ticket (showtime_id, customer_id, row_num, seat_number, price) VALUES
    (1, 1, 1, 1, 10.00),
    (1, 2, 1, 2, 10.00),
    (2, 3, 2, 5, 12.00),
    (2, 4, 2, 6, 12.00),
    (3, 5, 3, 4, 10.00),
    (4, 1, 1, 1, 15.00),
    (4, 2, 1, 2, 15.00),
    (5, 3, 2, 3, 12.00);
