CREATE OR REPLACE VIEW v_movie_tasks AS
SELECT
    m.title,
    a.name AS task_name,
    av.value_date AS task_date,
    
    CASE 
        WHEN av.value_date = CURRENT_DATE THEN 'Сегодня'
        WHEN av.value_date > CURRENT_DATE AND av.value_date <= CURRENT_DATE + INTERVAL '25 days' 
            THEN 'Через 25 дней'
        ELSE 'Скоро'
    END AS task_period

FROM attribute_values av
JOIN movies m ON m.id = av.movie_id
JOIN attributes a ON a.id = av.attribute_id
JOIN attribute_groups g ON g.id = a.group_id

WHERE a.data_type = 'date'
  AND g.code = 'service_dates';