-- 1. Выбор всех фильмов на сегодня
SELECT f.id, f.name
FROM screenings s
INNER JOIN films f ON s.film_id = f.id
WHERE s.start_time BETWEEN CURRENT_DATE AND CURRENT_DATE + 1
GROUP BY f.id;

-- 2. Подсчёт проданных билетов за неделю
SELECT count(id)
FROM tickets
WHERE paid_at BETWEEN CURRENT_DATE AND CURRENT_DATE + 7
AND status = 'paid';

-- 3. Формирование афиши (фильмы, которые показывают сегодня)
SELECT s.start_time, f.name, ch.name
FROM screenings s
INNER JOIN films f ON s.film_id = f.id
INNER JOIN cinema_halls ch ON s.cinema_hall_id = ch.id
WHERE s.start_time BETWEEN CURRENT_DATE AND CURRENT_DATE + 1;

-- 4. Поиск 3 самых прибыльных фильмов за неделю
SELECT f.id, f.name, sum(t.price) as total_sum
FROM tickets t
INNER JOIN screenings s ON s.id = t.screening_id
INNER JOIN films f ON f.id = s.film_id
WHERE s.start_time BETWEEN CURRENT_DATE AND CURRENT_DATE + 7
AND t.status = 'paid'
GROUP BY f.id
ORDER BY total_sum DESC
LIMIT 3;

-- 5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
SELECT
    chs.seat_row,
    chs.seat_number,
    CASE
        WHEN t.status = 'paid' THEN 'paid'
        WHEN t.status = 'reserved' THEN 'reserved'
        ELSE 'free'
    END as seat_status
FROM screenings s
INNER JOIN cinema_halls ch ON s.cinema_hall_id = ch.id
INNER JOIN cinema_hall_seats chs ON chs.hall_id = ch.id
LEFT JOIN tickets t ON chs.id = t.cinema_hall_seat_id AND s.id = t.screening_id AND t.status != 'returned'
WHERE s.id = :screening_id;

-- 6. Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс
SELECT MIN(p.value) as min_price, MAX(p.value) as max_price
FROM screenings s
INNER JOIN prices p ON p.film_id = s.film_id AND p.cinema_hall_id = s.cinema_hall_id
WHERE s.id = :screening_id;
