SET search_path TO cinema;

-- 1. Топ-15 самых больших объектов (таблицы и индексы)
SELECT 
    relname AS object_name, 
    pg_size_pretty(pg_total_relation_size(oid)) AS total_size
FROM pg_class c
JOIN pg_namespace n ON n.oid = c.relnamespace
WHERE n.nspname = 'cinema' AND relkind IN ('r', 'i')
ORDER BY pg_total_relation_size(oid) DESC 
LIMIT 15;

-- 2. Топ-5 самых часто используемых индексов
SELECT 
    indexrelname AS index_name, 
    idx_scan AS scan_count
FROM pg_stat_user_indexes 
WHERE schemaname = 'cinema'
ORDER BY idx_scan DESC 
LIMIT 5;

-- 3. Топ-5 самых редко используемых индексов (могут быть лишними)
SELECT 
    indexrelname AS index_name, 
    idx_scan AS scan_count
FROM pg_stat_user_indexes 
WHERE schemaname = 'cinema'
ORDER BY idx_scan ASC 
LIMIT 5;
