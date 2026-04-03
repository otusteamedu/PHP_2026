-- ************************************************************
-- Планы выполнения запросов
-- Запуск: docker exec -i hw9-postgres psql -U cinema_user -d cinema_db < sql/explain_plans.sql > sql/explain_plans_output.txt
-- ************************************************************

\echo ''
\echo '*** Query 1: Все фильмы на сегодня ***'
EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
SELECT DISTINCT
    m.id,
    m.title,
    m.release_year
FROM session se
JOIN movie m ON m.id = se.movie_id
WHERE se.starts_at::date = CURRENT_DATE
ORDER BY m.title;

\echo ''
\echo '*** Query 2: Подсчёт проданных билетов за неделю ***'
EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
SELECT count(*) AS tickets_sold
FROM ticket
WHERE sold_at >= date_trunc('day', now()) - interval '6 days';

\echo ''
\echo '*** Query 3: Афиша - фильмы с сеансами на сегодня ***'
EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
SELECT
    m.title,
    h.name AS hall,
    to_char(se.starts_at, 'DD-MM-YYYY HH24:MI') AS time,
    se.price_min AS price_from,
    se.price_max AS price_to
FROM session se
JOIN movie m ON m.id = se.movie_id
JOIN hall  h ON h.id = se.hall_id
WHERE se.starts_at::date = CURRENT_DATE
ORDER BY se.starts_at;

\echo ''
\echo '*** Query 4: Топ-3 самых прибыльных фильма за неделю ***'
EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
SELECT
    m.title,
    sum(t.price)  AS revenue,
    count(t.id)   AS tickets_sold
FROM ticket t
JOIN session se ON se.id = t.session_id
JOIN movie   m  ON m.id  = se.movie_id
WHERE t.sold_at >= date_trunc('day', now()) - interval '6 days'
GROUP BY m.id, m.title
ORDER BY revenue DESC
LIMIT 3;

\echo ''
\echo '*** Query 5: Схема зала - свободные и занятые места (session_id = 1) ***'
EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
SELECT
    s.row_num,
    s.seat_num,
    CASE WHEN t.id IS NOT NULL THEN 'занято' ELSE 'свободно' END AS status
FROM seat s
JOIN session se ON se.hall_id = s.hall_id AND se.id = 1
LEFT JOIN ticket t ON t.seat_id = s.id AND t.session_id = se.id
ORDER BY s.row_num, s.seat_num;

\echo ''
\echo '*** Query 6: Диапазон цен на билеты для сеанса (session_id = 1) ***'
EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
SELECT
    min(t.price) AS price_min,
    max(t.price) AS price_max
FROM ticket t
WHERE t.session_id = 1;