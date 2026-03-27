-- 6. Диапазон минимальной и максимальной цены за билет на конкретный сеанс
SELECT
    min(t.price) AS price_min,
    max(t.price) AS price_max
FROM ticket t
WHERE t.session_id = 1;  -- session_id