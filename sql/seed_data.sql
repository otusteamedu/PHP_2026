-- ************************************************************
-- Наполнение таблиц тестовыми данными (~10 000 строк текста)
-- Запускать после cinema_db.sql и cinema_operational.sql
-- ************************************************************

-- Удаляем тестовые данные из cinema_db.sql чтобы избежать конфликтов
TRUNCATE ticket, session, attribute_values, seat, hall, movie RESTART IDENTITY CASCADE;

-- ************************************************************
-- 1. Фильмы - 1000 строк
-- ************************************************************
INSERT INTO movie (title, release_year)
SELECT
    'Фильм №' || i,
    2000 + (i % 25)   -- годы от 2000 до 2024
FROM generate_series(1, 1000) AS i;

-- ************************************************************
-- 2. Attribute_values: текстовые атрибуты (attr 5 и 6)
--    1000 фильмов × 2 рецензии = 2000 строк
--    + ещё 8000 строк через дополнительные attribute_types
-- ************************************************************

-- Добавляем extra text-атрибуты чтобы набрать 10 000 строк текста
INSERT INTO attributes (code, name, attribute_type_id, is_required) VALUES
('synopsis',       'Синопсис',               1, false),
('director_note',  'Заметка режиссёра',       1, false),
('tagline',        'Слоган',                  1, false),
('press_release',  'Пресс-релиз',             1, false);
-- Итого text-атрибутов: 6 (id 5,6 + 4 новых = id 9,10,11,12)

-- Рецензия критиков (attr_id = 5): все 1000 фильмов
INSERT INTO attribute_values (movie_id, attribute_id, value_text)
SELECT
    m.id,
    5,
    'Рецензия критиков на фильм "' || m.title || '": ' ||
    CASE (m.id % 5)
        WHEN 0 THEN 'Шедевр современного кинематографа, обязателен к просмотру.'
        WHEN 1 THEN 'Захватывающий сюжет и великолепная операторская работа.'
        WHEN 2 THEN 'Неоднозначный фильм, вызвавший споры среди критиков.'
        WHEN 3 THEN 'Режиссёр мастерски передал атмосферу эпохи.'
        ELSE 'Достойная работа, хотя и не без недостатков.'
    END
FROM movie m;

-- Отзыв академии (attr_id = 6): все 1000 фильмов
INSERT INTO attribute_values (movie_id, attribute_id, value_text)
SELECT
    m.id,
    6,
    'Отзыв академии на "' || m.title || '": ' ||
    CASE (m.id % 4)
        WHEN 0 THEN 'Рекомендовано к включению в золотой фонд.'
        WHEN 1 THEN 'Выдвинуто на соискание премии в трёх номинациях.'
        WHEN 2 THEN 'Высокая художественная ценность, широкая аудитория.'
        ELSE        'Представляет исторический интерес для исследователей.'
    END
FROM movie m;

-- Синопсис (attr_id = 9): все 1000 фильмов
INSERT INTO attribute_values (movie_id, attribute_id, value_text)
SELECT
    m.id,
    9,
    'Синопсис: история разворачивается в ' ||
    CASE (m.id % 6)
        WHEN 0 THEN 'небольшом провинциальном городе, где случается непредвиденное.'
        WHEN 1 THEN 'мегаполисе будущего, полном тайн и противоречий.'
        WHEN 2 THEN 'горном селении, отрезанном от внешнего мира.'
        WHEN 3 THEN 'морском порту в эпоху великих открытий.'
        WHEN 4 THEN 'современном Токио на фоне технологического бума.'
        ELSE        'постапокалиптическом мире, где выживают сильнейшие.'
    END
FROM movie m;

-- Заметка режиссёра (attr_id = 10): все 1000 фильмов
INSERT INTO attribute_values (movie_id, attribute_id, value_text)
SELECT
    m.id,
    10,
    'Режиссёр о фильме "' || m.title || '": ' ||
    CASE (m.id % 5)
        WHEN 0 THEN 'Я хотел показать, что за внешней простотой скрывается глубина.'
        WHEN 1 THEN 'Этот проект стал для меня самым личным за всю карьеру.'
        WHEN 2 THEN 'Мы снимали в реальных локациях, чтобы передать подлинную атмосферу.'
        WHEN 3 THEN 'Команда работала в сложных условиях, но результат превзошёл ожидания.'
        ELSE        'Вдохновением послужили реальные события начала века.'
    END
FROM movie m;

-- Слоган (attr_id = 11): все 1000 фильмов
INSERT INTO attribute_values (movie_id, attribute_id, value_text)
SELECT
    m.id,
    11,
    CASE (m.id % 8)
        WHEN 0 THEN 'Lorem ipsum dolor sit amet.'
        WHEN 1 THEN 'Consectetur adipiscing elit.'
        WHEN 2 THEN 'Sed do eiusmod tempor incididunt.'
        WHEN 3 THEN 'Ut labore et dolore magna.'
        WHEN 4 THEN 'Aliqua enim ad minim veniam.'
        WHEN 5 THEN 'Quis nostrud exercitation ullam.'
        WHEN 6 THEN 'Laboris nisi ut aliquip ex ea.'
        ELSE 'Duis aute irure dolor reprehenderit.'
    END
FROM movie m;

-- Пресс-релиз (attr_id = 12): все 1000 фильмов
INSERT INTO attribute_values (movie_id, attribute_id, value_text)
SELECT
    m.id,
    12,
    'Пресс-релиз: кинокомпания с гордостью представляет "' || m.title ||
    '" - ' ||
    CASE (m.id % 4)
        WHEN 0 THEN 'долгожданную премьеру, которую ждали поклонники по всему миру.'
        WHEN 1 THEN 'совместный проект ведущих студий двух континентов.'
        WHEN 2 THEN 'экранизацию одноимённого бестселлера, проданного тиражом 2 млн экз.'
        ELSE        'авторское высказывание, получившее признание на международных фестивалях.'
    END
FROM movie m;

-- Итого текстовых строк в attribute_values: 6 000

-- Дополнительные нетекстовые атрибуты (date, boolean, numeric) для полноты
-- Мировая премьера (attr_id = 3)
INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, 3, DATE '2000-01-15' + ((m.id * 13) % 9000)::int
FROM movie m;

-- Оскар (attr_id = 1) - каждый 5-й фильм
INSERT INTO attribute_values (movie_id, attribute_id, value_boolean)
SELECT m.id, 1, true
FROM movie m WHERE m.id % 5 = 0;

-- ************************************************************
-- 3. Залы и места
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
-- 4. Сеансы - по 2 на каждый фильм (2 000 строк)
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
-- 5. Билеты - ~2 000 строк
--    Берём первые 1000 сеансов и продаём по 2 билета
-- ************************************************************
INSERT INTO ticket (session_id, seat_id, price, sold_at)
SELECT
    se.id,
    (
        SELECT s.id FROM seat s
        WHERE s.hall_id = se.hall_id
        ORDER BY s.id
        LIMIT 1 OFFSET ((se.id * 7) % (SELECT count(*) FROM seat WHERE hall_id = se.hall_id))
    ),
    se.price_min + ((se.id % 5) * 20),
    now() - ((se.id % 30) || ' days')::interval
FROM session se
WHERE se.id <= 1000
ON CONFLICT DO NOTHING;

INSERT INTO ticket (session_id, seat_id, price, sold_at)
SELECT
    se.id,
    (
        SELECT s.id FROM seat s
        WHERE s.hall_id = se.hall_id
        ORDER BY s.id
        LIMIT 1 OFFSET ((se.id * 13) % (SELECT count(*) FROM seat WHERE hall_id = se.hall_id))
    ),
    se.price_min + ((se.id % 5) * 30),
    now() - ((se.id % 30) || ' days')::interval
FROM session se
WHERE se.id <= 1000
ON CONFLICT DO NOTHING;

-- ************************************************************
-- Итоговый подсчёт строк
-- ************************************************************
SELECT 'movie'             AS tbl, count(*) FROM movie
UNION ALL
SELECT 'attribute_values',          count(*) FROM attribute_values
UNION ALL
SELECT 'hall',                       count(*) FROM hall
UNION ALL
SELECT 'seat',                       count(*) FROM seat
UNION ALL
SELECT 'session',                    count(*) FROM session
UNION ALL
SELECT 'ticket',                     count(*) FROM ticket
ORDER BY 1;