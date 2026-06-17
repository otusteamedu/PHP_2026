-- Выбор всех фильмов на сегодня
SELECT hall_id, movie_id FROM screenings 
WHERE starts_at >= CURRENT_DATE 
  AND starts_at < CURRENT_DATE + interval '1 day'

-- Подсчёт проданных билетов за неделю
SELECT COUNT(*) FROM tickets
WHERE status IN ('paid', 'reserved') 
AND screening_id IN (
    SELECT id FROM screenings WHERE starts_at >= NOW() - INTERVAL '7 days'
);

-- Формирование афиши (фильмы, которые показывают сегодня)
SELECT 
    m.title,
    s.starts_at,
    c.name AS cinema,
    h.name AS hall
FROM screenings s
JOIN movies m ON m.id = s.movie_id
JOIN halls h ON h.id = s.hall_id
JOIN cinemas c ON c.id = h.cinema_id
WHERE s.starts_at >= CURRENT_DATE 
  AND s.starts_at < CURRENT_DATE + interval '1 day'
ORDER BY s.starts_at;

-- Поиск 3 самых прибыльных фильмов за неделю
SELECT m.title, SUM(t.price_amount) AS total
FROM tickets t
JOIN screenings s ON t.screening_id = s.id
JOIN movies m ON s.movie_id = m.id
JOIN orders o ON t.order_id = o.id
WHERE t.status = 'paid' 
  AND o.paid_at >= NOW() - INTERVAL '7 days'
GROUP BY m.title
ORDER BY total DESC
LIMIT 3;

-- Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
SELECT st.row_code, st.seat_number,
       CASE WHEN t.id IS NOT NULL THEN 'Занято' ELSE 'Свободно' END AS seat_status
FROM seats st
JOIN screenings s ON st.hall_id = s.hall_id
LEFT JOIN tickets t ON st.id = t.seat_id 
                   AND t.screening_id = s.id 
                   AND t.status IN ('paid', 'reserved')
WHERE s.id = 2
ORDER BY st.row_code, st.seat_number;

-- Вывести диапазон минимальной и максимальной цены за билет на конкретный сеанс
SELECT MIN(price_amount) AS min_price, MAX(price_amount) AS max_price
FROM screening_prices
WHERE screening_id = 2;