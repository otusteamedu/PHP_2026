-- отсортированный список (15 значений) самых больших по размеру объектов БД (таблицы, включая индексы, сами индексы)
SELECT
    c.relname AS object_name,
    CASE c.relkind
        WHEN 'r' THEN 'table'
        WHEN 'i' THEN 'index'
        END AS object_type,
    pg_size_pretty(pg_total_relation_size(c.oid)) AS size
FROM pg_class c
         JOIN pg_namespace n ON n.oid = c.relnamespace
WHERE n.nspname NOT IN ('pg_catalog', 'information_schema')
  AND c.relkind IN ('r', 'i')
ORDER BY pg_total_relation_size(c.oid) DESC
LIMIT 15;

-- +------------------------------------------------------+-----------+-------+
-- |object_name                                           |object_type|size   |
-- +------------------------------------------------------+-----------+-------+
-- |prices                                                |table      |722 MB |
-- |prices_pkey                                           |index      |193 MB |
-- |prices_film_id_cinema_hall_id_idx                     |index      |81 MB  |
-- |clients                                               |table      |12 MB  |
-- |tickets                                               |table      |9592 kB|
-- |screenings                                            |table      |5752 kB|
-- |clients_email_key                                     |index      |5568 kB|
-- |tickets_pkey                                          |index      |2208 kB|
-- |clients_pkey                                          |index      |1768 kB|
-- |screenings_pkey                                       |index      |1328 kB|
-- |screenings_start_time_idx                             |index      |1328 kB|
-- |films                                                 |table      |1144 kB|
-- |screenings_status_paid_at_idx                         |index      |736 kB |
-- |screenings_status_screening_id_cinema_hall_seat_id_idx|index      |728 kB |
-- |cinema_hall_seats                                     |table      |712 kB |
-- +------------------------------------------------------+-----------+-------+
