-- отсортированные списки (по 5 значений) самых часто используемых индексов
SELECT
    relname AS table_name,
    indexrelname AS index_name,
    idx_scan AS index_scans
FROM pg_stat_user_indexes
WHERE schemaname NOT IN ('pg_catalog', 'information_schema')
ORDER BY idx_scan DESC
LIMIT 5;

-- +------------+---------------------------------+-----------+
-- |table_name  |index_name                       |index_scans|
-- +------------+---------------------------------+-----------+
-- |films       |films_pkey                       |294        |
-- |cinema_halls|cinema_halls_pkey                |52         |
-- |screenings  |screenings_pkey                  |28         |
-- |prices      |prices_film_id_cinema_hall_id_idx|3          |
-- |tickets     |screenings_status_paid_at_idx    |0          |
-- +------------+---------------------------------+-----------+


-- отсортированные списки (по 5 значений) самых редко используемых индексов
SELECT
    relname AS table_name,
    indexrelname AS index_name,
    idx_scan AS index_scans
FROM pg_stat_user_indexes
WHERE schemaname NOT IN ('pg_catalog', 'information_schema')
ORDER BY idx_scan
LIMIT 5;

-- +----------+------------------------------------------------------+-----------+
-- |table_name|index_name                                            |index_scans|
-- +----------+------------------------------------------------------+-----------+
-- |tickets   |screenings_status_screening_id_cinema_hall_seat_id_idx|0          |
-- |tickets   |screenings_status_paid_at_idx                         |0          |
-- |screenings|screenings_start_time_idx                             |0          |
-- |cinemas   |cinemas_pkey                                          |0          |
-- |seat_types|seat_types_pkey                                       |0          |
-- +----------+------------------------------------------------------+-----------+

