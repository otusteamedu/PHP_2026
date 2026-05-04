### Схема и данные для кинотеатра

* Запустить миграции - файл `cinema_schema.sql`
* Накатить минимальные необходимые данные - файл `cinema_data.sql`
* Запустить создание функций - файл `cinema_stored_functions.sql`

## Использование

* Добавить записи в таблицу расписаний `SELECT insert_random_schedule('2026-01-01', '2026-01-07', 1.0);`
  * 2026-01-01 - дата начала генерации расписания
  * 2026-01-07 - дата конца генерации расписания
  * 1.0 - коэффициент повышения цены

* Сгенерировать билеты `SELECT * FROM generate_tickets(1000, 5, 20, 60);`
  * 1000 - количество билетов
  * 5 - процент сеансов с полным заполнением зала
  * 20 - процент минимального заполнения
  * 60 - процент эффективного заполнения


## Контрольные запросы

Выбор всех фильмов на сегодня
```SQL
SELECT DISTINCT f.title FROM schedule s
JOIN films f ON f.id = s.film_id
WHERE date = '2026-01-01';
```
```
HashAggregate  (cost=46.72..48.32 rows=160 width=218)
  Group Key: f.title
  ->  Nested Loop  (cost=0.16..46.32 rows=160 width=218)
        ->  Seq Scan on schedule s  (cost=0.00..39.11 rows=160 width=8)
"              Filter: (date = '2026-01-01'::date)"
        ->  Memoize  (cost=0.16..0.47 rows=1 width=226)
              Cache Key: s.film_id
              Cache Mode: logical
              ->  Index Scan using films_pkey on films f  (cost=0.15..0.46 rows=1 width=226)
                    Index Cond: (id = s.film_id)
```
```aiignore
Sort  (cost=81.69..81.71 rows=9 width=16)
  Sort Key: (count(t.id)) DESC
  ->  HashAggregate  (cost=81.46..81.55 rows=9 width=16)
        Group Key: s.hall_id
        ->  Hash Join  (cost=57.47..78.11 rows=670 width=16)
              Hash Cond: (t.schedule_id = s.id)
              ->  Seq Scan on tickets t  (cost=0.00..18.00 rows=1000 width=16)
              ->  Hash  (cost=43.33..43.33 rows=1131 width=16)
                    ->  Seq Scan on schedule s  (cost=0.00..43.33 rows=1131 width=16)
"                          Filter: ((date >= '2026-01-01'::date) AND (date <= '2026-01-07'::date))"
```

Подсчёт проданных билетов за неделю
```SQL
SELECT COUNT(t.id) AS "tickets sold", s.hall_id FROM schedule s
JOIN tickets t ON t.schedule_id = s.id
WHERE date BETWEEN '2026-01-01' AND '2026-01-07'
GROUP BY s.hall_id
ORDER BY "tickets sold" DESC;
```
```aiignore
Sort  (cost=81.69..81.71 rows=9 width=16)
  Sort Key: (count(t.id)) DESC
  ->  HashAggregate  (cost=81.46..81.55 rows=9 width=16)
        Group Key: s.hall_id
        ->  Hash Join  (cost=57.47..78.11 rows=670 width=16)
              Hash Cond: (t.schedule_id = s.id)
              ->  Seq Scan on tickets t  (cost=0.00..18.00 rows=1000 width=16)
              ->  Hash  (cost=43.33..43.33 rows=1131 width=16)
                    ->  Seq Scan on schedule s  (cost=0.00..43.33 rows=1131 width=16)
"                          Filter: ((date >= '2026-01-01'::date) AND (date <= '2026-01-07'::date))"
```
```aiignore
Sort  (cost=81.69..81.71 rows=9 width=16)
  Sort Key: (count(t.id)) DESC
  ->  HashAggregate  (cost=81.46..81.55 rows=9 width=16)
        Group Key: s.hall_id
        ->  Hash Join  (cost=57.47..78.11 rows=670 width=16)
              Hash Cond: (t.schedule_id = s.id)
              ->  Seq Scan on tickets t  (cost=0.00..18.00 rows=1000 width=16)
              ->  Hash  (cost=43.33..43.33 rows=1131 width=16)
                    ->  Seq Scan on schedule s  (cost=0.00..43.33 rows=1131 width=16)
"                          Filter: ((date >= '2026-01-01'::date) AND (date <= '2026-01-07'::date))"
```

Формирование афиши (фильмы, которые показывают сегодня)
```SQL
SELECT f.title as MOVIE, h.title as HALL, STRING_AGG(s.time::TEXT, ', ' ORDER BY s.time) AS SCHEDULE FROM schedule s
JOIN films f ON f.id = s.film_id
JOIN halls h ON h.id = s.hall_id
WHERE s.date = '2026-01-01' AND h.cinema_id = 3
GROUP BY f.title, h.title;
```
```aiignore
GroupAggregate  (cost=48.22..48.25 rows=1 width=468)
  Group Key: f.title, h.title
  ->  Sort  (cost=48.22..48.23 rows=1 width=444)
"        Sort Key: f.title, h.title, s.""time"""
        ->  Nested Loop  (cost=0.30..48.21 rows=1 width=444)
              ->  Nested Loop  (cost=0.16..47.75 rows=1 width=234)
                    ->  Seq Scan on schedule s  (cost=0.00..39.11 rows=160 width=24)
"                          Filter: (date = '2026-01-01'::date)"
                    ->  Memoize  (cost=0.16..0.48 rows=1 width=226)
                          Cache Key: s.hall_id
                          Cache Mode: logical
                          ->  Index Scan using halls_pkey on halls h  (cost=0.15..0.47 rows=1 width=226)
                                Index Cond: (id = s.hall_id)
                                Filter: (cinema_id = 3)
              ->  Index Scan using films_pkey on films f  (cost=0.15..0.46 rows=1 width=226)
                    Index Cond: (id = s.film_id)
```
```aiignore
GroupAggregate  (cost=28.55..29.05 rows=18 width=468)
  Group Key: f.title, h.title
  ->  Sort  (cost=28.55..28.60 rows=18 width=444)
"        Sort Key: f.title, h.title, s.""time"""
        ->  Nested Loop  (cost=4.62..28.18 rows=18 width=444)
              ->  Nested Loop  (cost=4.46..24.63 rows=18 width=234)
                    ->  Seq Scan on halls h  (cost=0.00..1.11 rows=1 width=226)
                          Filter: (cinema_id = 3)
                    ->  Bitmap Heap Scan on schedule s  (cost=4.46..23.34 rows=18 width=24)
"                          Recheck Cond: ((date = '2026-01-01'::date) AND (hall_id = h.id))"
                          ->  Bitmap Index Scan on idx_schedule_date_hall  (cost=0.00..4.46 rows=18 width=0)
"                                Index Cond: ((date = '2026-01-01'::date) AND (hall_id = h.id))"
              ->  Memoize  (cost=0.16..0.47 rows=1 width=226)
                    Cache Key: s.film_id
                    Cache Mode: logical
                    ->  Index Scan using films_pkey on films f  (cost=0.15..0.46 rows=1 width=226)
                          Index Cond: (id = s.film_id)
```

Поиск 3 самых прибыльных фильмов за неделю
```SQL
SELECT SUM(t.real_price) AS REVENUE, f.title AS MOVIE from tickets t
JOIN schedule s ON s.id = t.schedule_id
JOIN films f ON f.id = s.film_id
WHERE s.date BETWEEN DATE '2026-01-01' AND DATE '2026-01-01' + INTERVAL '7 days'
GROUP BY f.title
ORDER BY REVENUE DESC
LIMIT 3
```
```aiignore
Limit  (cost=109.60..109.61 rows=3 width=250)
  ->  Sort  (cost=109.60..110.35 rows=300 width=250)
        Sort Key: (sum(t.real_price)) DESC
        ->  HashAggregate  (cost=101.97..105.72 rows=300 width=250)
              Group Key: f.title
              ->  Nested Loop  (cost=58.62..98.39 rows=716 width=224)
                    ->  Hash Join  (cost=58.46..79.10 rows=716 width=14)
                          Hash Cond: (t.schedule_id = s.id)
                          ->  Seq Scan on tickets t  (cost=0.00..18.00 rows=1000 width=14)
                          ->  Hash  (cost=43.33..43.33 rows=1210 width=16)
                                ->  Seq Scan on schedule s  (cost=0.00..43.33 rows=1210 width=16)
"                                      Filter: ((date >= '2026-01-01'::date) AND (date <= '2026-01-08 00:00:00'::timestamp without time zone))"
                    ->  Memoize  (cost=0.16..0.21 rows=1 width=226)
                          Cache Key: s.film_id
                          Cache Mode: logical
                          ->  Index Scan using films_pkey on films f  (cost=0.15..0.20 rows=1 width=226)
                                Index Cond: (id = s.film_id)
```
```aiignore
Limit  (cost=109.60..109.61 rows=3 width=250)
  ->  Sort  (cost=109.60..110.35 rows=300 width=250)
        Sort Key: (sum(t.real_price)) DESC
        ->  HashAggregate  (cost=101.97..105.72 rows=300 width=250)
              Group Key: f.title
              ->  Nested Loop  (cost=58.62..98.39 rows=716 width=224)
                    ->  Hash Join  (cost=58.46..79.10 rows=716 width=14)
                          Hash Cond: (t.schedule_id = s.id)
                          ->  Seq Scan on tickets t  (cost=0.00..18.00 rows=1000 width=14)
                          ->  Hash  (cost=43.33..43.33 rows=1210 width=16)
                                ->  Seq Scan on schedule s  (cost=0.00..43.33 rows=1210 width=16)
"                                      Filter: ((date >= '2026-01-01'::date) AND (date <= '2026-01-08 00:00:00'::timestamp without time zone))"
                    ->  Memoize  (cost=0.16..0.21 rows=1 width=226)
                          Cache Key: s.film_id
                          Cache Mode: logical
                          ->  Index Scan using films_pkey on films f  (cost=0.15..0.20 rows=1 width=226)
                                Index Cond: (id = s.film_id)
```

Схема зала со свободными/занятыми местами
```SQL
SELECT
  c.title AS cinema,
  h.title AS hall,
  f.title AS movie,
  sc.id,
  sc.date,
  sc.time,
  s.row_number,
  COUNT(s.seat_number) AS total_seats_in_row,
  COUNT(t.id) AS sold_seats_in_row,
  ROUND(COUNT(t.id) * 100.0 / COUNT(s.seat_number), 2) AS occupancy_percentage,
  STRING_AGG(
          s.seat_number || ':' ||
          CASE
            WHEN t.id IS NOT NULL THEN '█'  -- Sold symbol
            ELSE '░'                         -- Free symbol
            END,
          ', ' ORDER BY s.seat_number
  ) AS seat_map,
  STRING_AGG(
          s.seat_number || ':' ||
          CASE
            WHEN t.id IS NOT NULL THEN 'sold'
            ELSE 'free'
            END,
          ', ' ORDER BY s.seat_number
  ) AS seat_status_detail
FROM schedule sc
       JOIN halls h ON h.id = sc.hall_id
       JOIN seat_maps m ON m.hall_id = h.id
       JOIN seats s ON s.map_id = m.id
       JOIN films f ON f.id = sc.film_id
       JOIN cinema c ON c.id = h.cinema_id
       LEFT JOIN tickets t ON t.schedule_id = sc.id AND t.seat_id = s.id
WHERE TRUE
--   AND sc.id = 1 
  AND h.id = 2
--   AND f.id = 4
--   AND f.id = 4
--   AND sc.date = '2026-03-01'  -- Specify date
GROUP BY c.title, h.title, f.title, sc.id, m.image, sc.date, sc.time, s.row_number
ORDER BY s.row_number, sc.date, sc.time;
```
```aiignore
Incremental Sort  (cost=171.08..252.22 rows=795 width=1006)
"  Sort Key: s.row_number, sc.date, sc.""time"""
  Presorted Key: s.row_number
  ->  GroupAggregate  (cost=170.74..230.36 rows=795 width=1006)
        Group Key: s.row_number, c.title, h.title, f.title, sc.id, m.image
        ->  Sort  (cost=170.74..172.73 rows=795 width=904)
              Sort Key: s.row_number, c.title, h.title, f.title, sc.id, m.image, s.seat_number
              ->  Hash Right Join  (cost=106.93..132.44 rows=795 width=904)
                    Hash Cond: ((t.schedule_id = sc.id) AND (t.seat_id = s.id))
                    ->  Seq Scan on tickets t  (cost=0.00..18.00 rows=1000 width=24)
                    ->  Hash  (cost=95.01..95.01 rows=795 width=904)
                          ->  Nested Loop  (cost=4.79..95.01 rows=795 width=904)
                                ->  Nested Loop  (cost=0.16..46.81 rows=194 width=246)
                                      ->  Seq Scan on schedule sc  (cost=0.00..39.11 rows=194 width=36)
                                            Filter: (hall_id = 2)
                                      ->  Memoize  (cost=0.16..0.42 rows=1 width=226)
                                            Cache Key: sc.film_id
                                            Cache Mode: logical
                                            ->  Index Scan using films_pkey on films f  (cost=0.15..0.41 rows=1 width=226)
                                                  Index Cond: (id = sc.film_id)
                                ->  Materialize  (cost=4.63..38.51 rows=4 width=674)
                                      ->  Nested Loop  (cost=4.63..38.49 rows=4 width=674)
                                            ->  Nested Loop  (cost=0.43..24.76 rows=1 width=670)
                                                  ->  Nested Loop  (cost=0.29..16.34 rows=1 width=460)
                                                        ->  Index Scan using halls_pkey on halls h  (cost=0.15..8.17 rows=1 width=234)
                                                              Index Cond: (id = 2)
                                                        ->  Index Scan using seat_maps_hall_id_key on seat_maps m  (cost=0.15..8.17 rows=1 width=234)
                                                              Index Cond: (hall_id = 2)
                                                  ->  Index Scan using cinema_pkey on cinema c  (cost=0.14..8.16 rows=1 width=226)
                                                        Index Cond: (id = h.cinema_id)
                                            ->  Bitmap Heap Scan on seats s  (cost=4.20..13.67 rows=6 width=20)
                                                  Recheck Cond: (m.id = map_id)
                                                  ->  Bitmap Index Scan on unique_seat  (cost=0.00..4.20 rows=6 width=0)
                                                        Index Cond: (map_id = m.id)
```
```aiignore
Incremental Sort  (cost=146.85..244.83 rows=905 width=1006)
"  Sort Key: s.row_number, sc.date, sc.""time"""
  Presorted Key: s.row_number
  ->  GroupAggregate  (cost=144.76..212.63 rows=905 width=1006)
        Group Key: s.row_number, c.title, h.title, f.title, sc.id, m.image
        ->  Sort  (cost=144.76..147.02 rows=905 width=904)
              Sort Key: s.row_number, c.title, h.title, f.title, sc.id, m.image, s.seat_number
              ->  Hash Right Join  (cost=64.69..100.32 rows=905 width=904)
                    Hash Cond: ((t.schedule_id = sc.id) AND (t.seat_id = s.id))
                    ->  Seq Scan on tickets t  (cost=0.00..18.00 rows=1000 width=24)
                    ->  Hash  (cost=51.11..51.11 rows=905 width=904)
                          ->  Hash Join  (cost=8.02..51.11 rows=905 width=904)
                                Hash Cond: (m.id = s.map_id)
                                ->  Nested Loop  (cost=6.08..46.50 rows=194 width=900)
                                      ->  Nested Loop  (cost=5.92..38.80 rows=194 width=690)
                                            ->  Nested Loop  (cost=0.14..10.66 rows=1 width=670)
                                                  ->  Nested Loop  (cost=0.00..2.23 rows=1 width=460)
                                                        ->  Seq Scan on halls h  (cost=0.00..1.11 rows=1 width=234)
                                                              Filter: (id = 2)
                                                        ->  Seq Scan on seat_maps m  (cost=0.00..1.11 rows=1 width=234)
                                                              Filter: (hall_id = 2)
                                                  ->  Index Scan using cinema_pkey on cinema c  (cost=0.14..8.16 rows=1 width=226)
                                                        Index Cond: (id = h.cinema_id)
                                            ->  Bitmap Heap Scan on schedule sc  (cost=5.78..26.21 rows=194 width=36)
                                                  Recheck Cond: (hall_id = 2)
                                                  ->  Bitmap Index Scan on idx_schedule_halls  (cost=0.00..5.73 rows=194 width=0)
                                                        Index Cond: (hall_id = 2)
                                      ->  Memoize  (cost=0.16..0.42 rows=1 width=226)
                                            Cache Key: sc.film_id
                                            Cache Mode: logical
                                            ->  Index Scan using films_pkey on films f  (cost=0.15..0.41 rows=1 width=226)
                                                  Index Cond: (id = sc.film_id)
                                ->  Hash  (cost=1.42..1.42 rows=42 width=20)
                                      ->  Seq Scan on seats s  (cost=0.00..1.42 rows=42 width=20)
```

Диапазон минимальной и максимальной цены за билет на конкретный сеанс
```SQL
SELECT
  sc.id AS schedule_id,
  f.title AS film_title,
  h.title AS hall_name,
  sc.date,
  sc.time,
  MIN(t.real_price) AS min_ticket_price,
  MAX(t.real_price) AS max_ticket_price,
  ROUND(AVG(t.real_price), 2) AS avg_ticket_price,
  COUNT(t.id) AS tickets_sold,
  SUM(t.real_price) AS total_revenue
FROM schedule sc
       JOIN films f ON f.id = sc.film_id
       JOIN halls h ON h.id = sc.hall_id
       LEFT JOIN tickets t ON t.schedule_id = sc.id
WHERE sc.id = 1  -- Replace with your schedule ID
GROUP BY sc.id, f.title, h.title, sc.date, sc.time;
```
```aiignore
GroupAggregate  (cost=45.43..45.71 rows=8 width=592)
  Group Key: f.title, h.title
  ->  Sort  (cost=45.43..45.45 rows=8 width=470)
        Sort Key: f.title, h.title
        ->  Nested Loop Left Join  (cost=0.57..45.31 rows=8 width=470)
              ->  Nested Loop  (cost=0.57..24.73 rows=1 width=456)
                    ->  Nested Loop  (cost=0.43..16.51 rows=1 width=246)
                          ->  Index Scan using schedule_pkey on schedule sc  (cost=0.28..8.29 rows=1 width=36)
                                Index Cond: (id = 1)
                          ->  Index Scan using films_pkey on films f  (cost=0.15..8.17 rows=1 width=226)
                                Index Cond: (id = sc.film_id)
                    ->  Index Scan using halls_pkey on halls h  (cost=0.15..8.17 rows=1 width=226)
                          Index Cond: (id = sc.hall_id)
              ->  Seq Scan on tickets t  (cost=0.00..20.50 rows=8 width=22)
                    Filter: (schedule_id = 1)
```
```aiignore
GroupAggregate  (cost=30.76..31.04 rows=8 width=592)
  Group Key: f.title, h.title
  ->  Sort  (cost=30.76..30.78 rows=8 width=470)
        Sort Key: f.title, h.title
        ->  Nested Loop Left Join  (cost=4.76..30.64 rows=8 width=470)
              ->  Nested Loop  (cost=0.43..17.72 rows=1 width=456)
                    Join Filter: (sc.hall_id = h.id)
                    ->  Nested Loop  (cost=0.43..16.51 rows=1 width=246)
                          ->  Index Scan using schedule_pkey on schedule sc  (cost=0.28..8.29 rows=1 width=36)
                                Index Cond: (id = 1)
                          ->  Index Scan using films_pkey on films f  (cost=0.15..8.17 rows=1 width=226)
                                Index Cond: (id = sc.film_id)
                    ->  Seq Scan on halls h  (cost=0.00..1.09 rows=9 width=226)
              ->  Bitmap Heap Scan on tickets t  (cost=4.34..12.85 rows=8 width=22)
                    Recheck Cond: (schedule_id = 1)
                    ->  Bitmap Index Scan on idx_tickets_schedule_seat  (cost=0.00..4.33 rows=8 width=0)
                          Index Cond: (schedule_id = 1)
```
Добавление индексов позволило значительно увеличить скорость обработки запросов
