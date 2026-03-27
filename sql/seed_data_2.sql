-- ************************************************************
-- Наполнение таблиц тестовыми данными (~10 000 000 текстовых строк)
-- Запускать после cinema_db.sql и cinema_operational.sql
-- Время выполнения: несколько минут
-- ************************************************************

-- Удаляем тестовые данные, attributes и attribute_types не трогаем
TRUNCATE ticket, session, attribute_values, seat, hall, movie RESTART IDENTITY CASCADE;

-- ************************************************************
-- 1. Добавляем текстовые атрибуты (итого 10 текстовых)
--    critic_review(5) и unknown_academy_review(6) уже есть в cinema_db.sql
-- ************************************************************
INSERT INTO attributes (code, name, attribute_type_id, is_required) VALUES
('synopsis', 'Синопсис', 1, false),
('director_note', 'Заметка режиссёра', 1, false),
('tagline', 'Слоган', 1, false),
('press_release', 'Пресс-релиз', 1, false),
('audience_review', 'Зрительский отзыв', 1, false),
('festival_note', 'Фестивальная заметка', 1, false),
('distributor', 'Дистрибьютор', 1, false),
('country', 'Страна производства', 1, false)
ON CONFLICT (code) DO NOTHING;

-- ************************************************************
-- 2. Фильмы - 1 000 000 строк
-- ************************************************************
INSERT INTO movie (title, release_year)
SELECT
    'Фильм №' || i,
    2000 + (i % 25)
FROM generate_series(1, 1000000) AS i;

-- ************************************************************
-- 3. Текстовые attribute_values - 1 000 000 фильмов × 10 атрибутов
--    = 10 000 000 строк
-- ************************************************************
INSERT INTO attribute_values (movie_id, attribute_id, value_text)
SELECT
    m.id,
    a.id,
    CASE (m.id % 6)
        WHEN 0 THEN 'Lorem ipsum dolor sit amet consectetur.' || ' [' || a.code || ':' || m.id || ']'
        WHEN 1 THEN 'Adipiscing elit sed do eiusmod tempor.' || ' [' || a.code || ':' || m.id || ']'
        WHEN 2 THEN 'Incididunt ut labore et dolore magna.' || ' [' || a.code || ':' || m.id || ']'
        WHEN 3 THEN 'Aliqua enim ad minim veniam quis.' || ' [' || a.code || ':' || m.id || ']'
        WHEN 4 THEN 'Nostrud exercitation ullamco laboris.' || ' [' || a.code || ':' || m.id || ']'
        ELSE 'Nisi ut aliquip ex ea commodo consequat.' || ' [' || a.code || ':' || m.id || ']'
    END
FROM movie m
CROSS JOIN (
    SELECT id, code FROM attributes WHERE attribute_type_id = 1
) a;

-- ************************************************************
-- 4. Нетекстовые атрибуты для полноты картины
-- ************************************************************

-- Мировая премьера (date) - все фильмы
INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, DATE '2000-01-15' + ((m.id * 13) % 9000)::int
FROM movie m
CROSS JOIN (SELECT id FROM attributes WHERE code = 'world_premiere') a;

-- Оскар (boolean) - каждый 10-й фильм
INSERT INTO attribute_values (movie_id, attribute_id, value_boolean)
SELECT m.id, a.id, true
FROM movie m
CROSS JOIN (SELECT id FROM attributes WHERE code = 'oscar') a
WHERE m.id % 10 = 0;

-- ************************************************************
-- 5. Залы и места
-- ************************************************************
INSERT INTO hall (name) VALUES
('Зал 1'), ('Зал 2'), ('Зал 3'), ('VIP');

-- Зал 1: 10 рядов × 20 мест = 200
INSERT INTO seat (hall_id, row_num, seat_num)
SELECT 1, r, s FROM generate_series(1, 10) r, generate_series(1, 20) s;

-- Зал 2: 8 рядов × 15 мест = 120
INSERT INTO seat (hall_id, row_num, seat_num)
SELECT 2, r, s FROM generate_series(1, 8) r, generate_series(1, 15) s;

-- Зал 3: 12 рядов × 25 мест = 300
INSERT INTO seat (hall_id, row_num, seat_num)
SELECT 3, r, s FROM generate_series(1, 12) r, generate_series(1, 25) s;

-- VIP: 4 ряда × 10 мест = 40
INSERT INTO seat (hall_id, row_num, seat_num)
SELECT 4, r, s FROM generate_series(1, 4) r, generate_series(1, 10) s;

-- ************************************************************
-- 6. Сеансы - 2 на каждый фильм = 2 000 000 строк
-- ************************************************************
INSERT INTO session (movie_id, hall_id, starts_at, price_min, price_max)
SELECT
    m.id,
    (m.id % 4) + 1,
    now()::date - ((m.id % 30) || ' days')::interval + time '14:00',
    200 + (m.id % 6) * 50,
    400 + (m.id % 6) * 100
FROM movie m;

INSERT INTO session (movie_id, hall_id, starts_at, price_min, price_max)
SELECT
    m.id,
    ((m.id + 2) % 4) + 1,
    now()::date - ((m.id % 30) || ' days')::interval + time '20:00',
    200 + (m.id % 6) * 50,
    400 + (m.id % 6) * 100
FROM movie m;

-- ************************************************************
-- 7. Билеты - 1 000 000 строк
--    Предвычисляем first_seat и total_seats через CTE,
--    избегая коррелированного подзапроса
-- ************************************************************
WITH hall_seat_info AS (
    SELECT
        hall_id,
        min(id)   AS first_seat,
        count(*)  AS total_seats
    FROM seat
    GROUP BY hall_id
)
INSERT INTO ticket (session_id, seat_id, price, sold_at)
SELECT
    se.id,
    hsi.first_seat + ((se.id * 7) % hsi.total_seats),
    se.price_min + ((se.id % 5) * 20),
    now() - ((se.id % 30) || ' days')::interval
FROM session se
JOIN hall_seat_info hsi ON hsi.hall_id = se.hall_id
WHERE se.id <= 500000
ON CONFLICT DO NOTHING;

WITH hall_seat_info AS (
    SELECT
        hall_id,
        min(id)   AS first_seat,
        count(*)  AS total_seats
    FROM seat
    GROUP BY hall_id
)
INSERT INTO ticket (session_id, seat_id, price, sold_at)
SELECT
    se.id,
    hsi.first_seat + ((se.id * 13) % hsi.total_seats),
    se.price_min + ((se.id % 5) * 30),
    now() - ((se.id % 30) || ' days')::interval
FROM session se
JOIN hall_seat_info hsi ON hsi.hall_id = se.hall_id
WHERE se.id <= 500000
ON CONFLICT DO NOTHING;

-- ************************************************************
-- Итоговый подсчёт строк
-- ************************************************************
SELECT 'movie' AS tbl, count(*) FROM movie
UNION ALL
SELECT 'attribute_values (total)', count(*) FROM attribute_values
UNION ALL
SELECT 'attribute_values (text)', count(*) FROM attribute_values WHERE value_text IS NOT NULL
UNION ALL
SELECT 'hall', count(*) FROM hall
UNION ALL
SELECT 'seat', count(*) FROM seat
UNION ALL
SELECT 'session', count(*) FROM session
UNION ALL
SELECT 'ticket', count(*) FROM ticket
ORDER BY 1;