-- 5. Схема зала: свободные и занятые места на конкретный сеанс
SELECT
    s.row_num,
    s.seat_num,
    CASE WHEN t.id IS NOT NULL THEN 'занято' ELSE 'свободно' END AS status
FROM seat s
JOIN session se ON se.hall_id = s.hall_id AND se.id = 1  -- session_id
LEFT JOIN ticket t ON t.seat_id = s.id AND t.session_id = se.id
ORDER BY s.row_num, s.seat_num;