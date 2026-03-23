-- 1. [Простой] Все сеансы на конкретную дату (сегодня)
SELECT * FROM cinema.screenings WHERE start_time::date = '2026-03-24';

-- 2. [Простой] Подсчёт проданных билетов за последнюю неделю (через связь с bookings)
SELECT count(*) 
FROM cinema.tickets t
JOIN cinema.bookings b ON t.booking_id = b.id
WHERE b.booking_time > now() - interval '7 days';

-- 3. [Сложный] Афиша (фильмы и время на сегодня)
SELECT DISTINCT m.title, s.start_time 
FROM cinema.movies m 
JOIN cinema.screenings s ON m.id = s.movie_id 
WHERE s.start_time >= '2026-03-24 00:00:00' AND s.start_time < '2026-03-25 00:00:00';

-- 4. [Сложный] ТОП-3 самых прибыльных фильма за неделю
SELECT m.title, SUM(t.actual_price) as revenue
FROM cinema.movies m
JOIN cinema.screenings s ON m.id = s.movie_id
JOIN cinema.tickets t ON s.id = t.screening_id
JOIN cinema.bookings b ON t.booking_id = b.id
WHERE b.booking_time > now() - interval '7 days'
GROUP BY m.title
ORDER BY revenue DESC
LIMIT 3;

-- 5. [Сложный] Свободные/занятые места на сеанс (ID 10)
SELECT sb.seat_row, sb.seat_number, 
       CASE WHEN t.id IS NULL THEN 'Free' ELSE 'Occupied' END as status
FROM cinema.seats_blueprint sb
LEFT JOIN cinema.tickets t ON t.seat_blueprint_id = sb.id AND t.screening_id = 10
WHERE sb.hall_id = (SELECT hall_id FROM cinema.screenings WHERE id = 10);

-- 6. [Простой] Минимум и максимум цены на сеанс (ID 10)
SELECT MIN(actual_price), MAX(actual_price) FROM cinema.tickets WHERE screening_id = 10;
