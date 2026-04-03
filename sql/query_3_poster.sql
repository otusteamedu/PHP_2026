-- 3. Афиша: фильмы с сеансами на сегодня
SELECT
    m.title,
    h.name AS hall,
    to_char(se.starts_at, 'DD-MM-YYYY HH24:MI') AS time,
    se.price_min AS price_from,
    se.price_max AS price_to
FROM session se
JOIN movie m ON m.id = se.movie_id
JOIN hall  h ON h.id = se.hall_id
WHERE se.starts_at >= CURRENT_DATE::timestamptz
  AND se.starts_at <  (CURRENT_DATE + 1)::timestamptz
ORDER BY se.starts_at;