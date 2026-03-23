DROP VIEW IF EXISTS view_service_tasks;
CREATE VIEW view_service_tasks AS
SELECT
    m.id,
    m.title,
    string_agg(a.name || ': ' || to_char(av.value_timestamp AT TIME ZONE 'UTC', 'YYYY-MM-DD HH24:MI'), ', ') FILTER (
        WHERE av.value_timestamp >= CURRENT_TIMESTAMP
          AND av.value_timestamp < CURRENT_TIMESTAMP + INTERVAL '7 days'
    ) AS tasks_next_7_days,
    string_agg(a.name || ': ' || to_char(av.value_timestamp AT TIME ZONE 'UTC', 'YYYY-MM-DD HH24:MI'), ', ') FILTER (
        WHERE av.value_timestamp >= CURRENT_TIMESTAMP + INTERVAL '7 days'
          AND av.value_timestamp < CURRENT_TIMESTAMP + INTERVAL '30 days'
    ) AS tasks_next_30_days
FROM attribute_values av
         JOIN attributes a ON a.id = av.attribute_id
         JOIN attribute_types t ON t.id = a.attribute_type_id
         JOIN movie m ON m.id = av.movie_id
WHERE t.code = 'timestamp'
GROUP BY m.id, m.title;