-- 1. Все фильмы на сегодня
SELECT DISTINCT
    m.id,
    m.title,
    m.release_year
FROM session se
JOIN movie m ON m.id = se.movie_id
WHERE se.starts_at::date = CURRENT_DATE
ORDER BY m.title;