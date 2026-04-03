-- 1. Все фильмы на сегодня
-- Оптимизация: EXISTS вместо DISTINCT + диапазон вместо ::date
-- EXISTS останавливается на первом найденном сеансе, не читает всю таблицу session.
-- Диапазон starts_at >= ... AND < ... использует idx_session_starts_at без функции.
SELECT
    m.id,
    m.title,
    m.release_year
FROM movie m
WHERE EXISTS (
    SELECT 1 FROM session se
    WHERE se.movie_id = m.id
      AND se.starts_at >= CURRENT_DATE::timestamptz
      AND se.starts_at <  (CURRENT_DATE + 1)::timestamptz
)
ORDER BY m.title;