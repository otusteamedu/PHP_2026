-- Подсчет общего кол-ва строк во всех таблицах
SELECT SUM(n_live_tup) AS total_rows
FROM pg_stat_user_tables;

-- 1. Выбор всех фильмов на сегодня
EXPLAIN ANALYZE SELECT f.id, f.name
FROM screenings s
INNER JOIN films f ON s.film_id = f.id
WHERE s.start_time BETWEEN CURRENT_DATE AND CURRENT_DATE + 1
GROUP BY f.id;

CREATE INDEX screenings_start_time_idx ON screenings (start_time);
DROP INDEX screenings_start_time_idx;

-- 2. Подсчёт проданных билетов за неделю
EXPLAIN ANALYZE SELECT count(id)
FROM tickets
WHERE paid_at BETWEEN CURRENT_DATE AND CURRENT_DATE + 7
  AND status = 'paid';

CREATE INDEX screenings_status_paid_at_idx ON tickets (status, paid_at);
DROP INDEX screenings_status_paid_at_idx;

-- 3. Формирование афиши (фильмы, которые показывают сегодня)
EXPLAIN ANALYZE SELECT s.start_time, f.name, ch.name
FROM screenings s
         INNER JOIN films f ON s.film_id = f.id
         INNER JOIN cinema_halls ch ON s.cinema_hall_id = ch.id
WHERE s.start_time BETWEEN CURRENT_DATE AND CURRENT_DATE + 1;

-- 4. Поиск 3 самых прибыльных фильмов за неделю
EXPLAIN ANALYZE SELECT f.id, f.name, sum(t.price) as total_sum
FROM tickets t
         INNER JOIN screenings s ON s.id = t.screening_id
         INNER JOIN films f ON f.id = s.film_id
WHERE s.start_time BETWEEN CURRENT_DATE AND CURRENT_DATE + 7
  AND t.status = 'paid'
GROUP BY f.id
ORDER BY total_sum DESC
LIMIT 3;

-- 5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
EXPLAIN ANALYZE SELECT
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

CREATE INDEX screenings_status_screening_id_cinema_hall_seat_id_idx ON tickets (status, screening_id, cinema_hall_seat_id);
DROP INDEX screenings_status_screening_id_cinema_hall_seat_id_idx;

-- 6. Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс
EXPLAIN ANALYZE SELECT MIN(p.value) as min_price, MAX(p.value) as max_price
FROM screenings s
INNER JOIN prices p ON p.film_id = s.film_id AND p.cinema_hall_id = s.cinema_hall_id
WHERE s.id = :screening_id;

CREATE INDEX prices_film_id_cinema_hall_id_idx ON prices (film_id, cinema_hall_id);
DROP INDEX prices_film_id_cinema_hall_id_idx;
