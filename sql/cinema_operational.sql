-- Дополнения схемы кинотеатра: залы, места, сеансы, билеты
DROP TABLE IF EXISTS ticket CASCADE;
DROP TABLE IF EXISTS session CASCADE;
DROP TABLE IF EXISTS seat CASCADE;
DROP TABLE IF EXISTS hall CASCADE;

-- 1. Залы
CREATE TABLE hall (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL
);

-- 2. Места в зале
CREATE TABLE seat (
    id SERIAL PRIMARY KEY,
    hall_id INT NOT NULL REFERENCES hall(id),
    row_num SMALLINT NOT NULL,
    seat_num SMALLINT NOT NULL,
    UNIQUE (hall_id, row_num, seat_num)
);

-- 3. Сеансы
CREATE TABLE session (
    id SERIAL PRIMARY KEY,
    movie_id BIGINT NOT NULL REFERENCES movie(id),
    hall_id INT NOT NULL REFERENCES hall(id),
    starts_at TIMESTAMPTZ NOT NULL,
    price_min FLOAT NOT NULL,
    price_max FLOAT NOT NULL
);

-- 4. Билеты
CREATE TABLE ticket (
    id BIGSERIAL PRIMARY KEY,
    session_id INT NOT NULL REFERENCES session(id),
    seat_id INT NOT NULL REFERENCES seat(id),
    price FLOAT NOT NULL,
    sold_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    UNIQUE (session_id, seat_id)
);

-- idx_session_starts_at: обслуживает Query 1 и Query 3 (фильтр по дате сеанса).
-- Примечание: фильтр вида starts_at::date = CURRENT_DATE НЕ использует этот индекс
-- (каст снимает sargability). Для его использования нужен функциональный индекс
-- idx_session_date (см. sql/indexes_after.sql) или переписать фильтр на диапазон.
-- Индекс полезен для диапазонных запросов без каста: starts_at >= ... AND starts_at < ...
CREATE INDEX idx_session_starts_at ON session(starts_at);

-- idx_ticket_session: обслуживает Query 5 (схема зала) и Query 6 (диапазон цен).
-- Оба запроса ищут билеты по конкретному session_id = $id.
-- Без индекса: Seq Scan по всей таблице ticket.
-- С индексом: Index Scan / Bitmap Index Scan — сотни строк вместо всей таблицы.
CREATE INDEX idx_ticket_session ON ticket(session_id);

-- idx_ticket_sold_at: обслуживает Query 2 (счётчик за неделю) и Query 4 (топ-3 по выручке).
-- Оба запроса фильтруют ticket.sold_at >= date_trunc('day', now()) - interval '6 days'.
-- На большом датасете даёт Index Only Scan для Query 2 (0,2 ms вместо Seq Scan).
-- Для Query 4 используется как Bitmap Index Scan, ограничивая выборку нужными строками.
CREATE INDEX idx_ticket_sold_at ON ticket(sold_at);

-- Тестовые данные
INSERT INTO hall (name) VALUES ('Зал 1'), ('Зал 2');

-- Зал 1: 5 рядов по 8 мест
INSERT INTO seat (hall_id, row_num, seat_num)
SELECT 1, r, s
FROM generate_series(1, 5) r, generate_series(1, 8) s;

-- Зал 2: 4 ряда по 6 мест
INSERT INTO seat (hall_id, row_num, seat_num)
SELECT 2, r, s
FROM generate_series(1, 4) r, generate_series(1, 6) s;

-- Сеансы: сегодня и на этой неделе
INSERT INTO session (movie_id, hall_id, starts_at, price_min, price_max) VALUES
(1, 1, now()::date + time '10:00', 300, 500),
(2, 2, now()::date + time '13:00', 250, 450),
(3, 1, now()::date + time '18:00', 400, 700),
(1, 2, now()::date - interval '2 days' + time '16:00', 300, 500),
(2, 1, now()::date - interval '4 days' + time '19:00', 250, 450),
(3, 2, now()::date + interval '1 day' + time '11:00', 400, 700);

-- Билеты: несколько продаж за последнюю неделю
-- Сеанс 1 (сегодня, Зал 1): продано 10 билетов
INSERT INTO ticket (session_id, seat_id, price, sold_at)
SELECT 1, s.id, 400, now() - interval '1 hour'
FROM seat s WHERE s.hall_id = 1 AND s.row_num <= 2
LIMIT 10;

-- Сеанс 2 (сегодня, Зал 2): продано 6 билетов
INSERT INTO ticket (session_id, seat_id, price, sold_at)
SELECT 2, s.id, 300, now() - interval '2 hours'
FROM seat s WHERE s.hall_id = 2 AND s.row_num = 1
LIMIT 6;

-- Сеанс 4 (2 дня назад)
INSERT INTO ticket (session_id, seat_id, price, sold_at)
SELECT 4, s.id, 450, now() - interval '2 days'
FROM seat s WHERE s.hall_id = 2 AND s.row_num <= 3
LIMIT 12;

-- Сеанс 5 (4 дня назад)
INSERT INTO ticket (session_id, seat_id, price, sold_at)
SELECT 5, s.id, 350, now() - interval '4 days'
FROM seat s WHERE s.hall_id = 1 AND s.row_num <= 4
LIMIT 20;

-- ============================================================
-- 1. Все фильмы на сегодня
-- ============================================================
SELECT DISTINCT
    m.id,
    m.title,
    m.release_year
FROM session se
JOIN movie m ON m.id = se.movie_id
WHERE se.starts_at::date = CURRENT_DATE
ORDER BY m.title;

-- ============================================================
-- 2. Подсчёт проданных билетов за неделю
-- ============================================================
SELECT count(*) AS tickets_sold
FROM ticket
WHERE sold_at >= date_trunc('day', now()) - interval '6 days';

-- 3. Афиша: фильмы с сеансами на сегодня
SELECT
    m.title,
    h.name AS hall,
    to_char(se.starts_at, 'DD-MM-YYYY HH24:MI') AS time,
    se.price_min AS price_from,
    se.price_max AS price_to
FROM session se
JOIN movie m ON m.id  = se.movie_id
JOIN hall  h ON h.id  = se.hall_id
WHERE se.starts_at::date = CURRENT_DATE
ORDER BY se.starts_at;

-- 4. Топ-3 самых прибыльных фильма за неделю
SELECT
    m.title,
    sum(t.price)  AS revenue,
    count(t.id)   AS tickets_sold
FROM ticket t
JOIN session se ON se.id = t.session_id
JOIN movie m  ON m.id  = se.movie_id
WHERE t.sold_at >= date_trunc('day', now()) - interval '6 days'
GROUP BY m.id, m.title
ORDER BY revenue DESC
LIMIT 3;

-- 5. Схема зала: свободные и занятые места на конкретный сеанс
--    (замените $session_id на нужный id)
SELECT
    s.row_num,
    s.seat_num,
    CASE WHEN t.id IS NOT NULL THEN 'занято' ELSE 'свободно' END AS status
FROM seat s
JOIN session se ON se.hall_id = s.hall_id AND se.id = 1  -- $session_id
LEFT JOIN ticket t ON t.seat_id = s.id AND t.session_id = se.id
ORDER BY s.row_num, s.seat_num;

-- ============================================================
-- 6. Диапазон цен на конкретный сеанс
--    (замените $session_id на нужный id)
-- ============================================================
SELECT
    min(t.price) AS price_min,
    max(t.price) AS price_max
FROM ticket t
WHERE t.session_id = 1;  -- $session_id