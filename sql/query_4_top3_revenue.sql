-- 4. Топ-3 самых прибыльных фильма за неделю
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