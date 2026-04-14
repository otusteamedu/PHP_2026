INSERT INTO rooms (title)
SELECT 'Room ' || i
FROM generate_series(1, 10) i;

INSERT INTO movies (title, duration_in_seconds)
SELECT 'Movie ' || i, (90 + random() * 60) * 60
FROM generate_series(1, 100) i;

INSERT INTO seats (room_id, position_x, position_y)
SELECT r.id, x, y
FROM rooms r,
     generate_series(1, 10) x,
     generate_series(1, 10) y;

INSERT INTO seanses (room_id, movie_id, price, start_at)
SELECT
       (random() * 9 + 1)::int,
       (random() * 99 + 1)::int,
       (5 + random() * 10)::numeric(8,2),
        NOW() - (random() * 10) * INTERVAL '1 day'
FROM generate_series(1, 100000);

-- сталкивался с проблемой сперва с wal размером
-- Подумал чтобы не поставить размер в настройках, для инсерта сделать так
-- использовал еще и вариант ALTER TABLE orders SET UNLOGGED; но очень сильно медленно работал
SET synchronous_commit = OFF;
INSERT INTO orders (seanse_id, seat_id, price, status, ticket_number,paid_at)
SELECT
    s.id,
    seat.id,
    (5 + random()*10)::numeric(8,2),
    CASE WHEN random() > 0.2 THEN 'paid' ELSE 'pending' END,
    md5(random()::text),
    CASE WHEN random() > 0.2
             THEN NOW() - (random() * 30) * INTERVAL '1 day'
        ELSE NULL
    END
FROM seanses s
         JOIN seats seat ON seat.room_id = s.room_id
    LIMIT 10000000;

SET synchronous_commit = ON;