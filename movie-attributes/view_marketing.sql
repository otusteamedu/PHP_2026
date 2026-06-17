CREATE OR REPLACE VIEW v_movie_attributes_marketing AS
SELECT
    m.id AS movie_id,
    m.title,
    g.name AS attribute_group,
    a.name AS attribute_name,
    COALESCE(
        av.value_text, 
        av.value_number::text, 
        TO_CHAR(av.value_date, 'DD.MM.YYYY'), 
        CASE 
            WHEN av.value_boolean = true THEN 'Да' 
            WHEN av.value_boolean = false THEN 'Нет' 
        END
    ) AS display_value
FROM attribute_values av
JOIN movies m ON m.id = av.movie_id
JOIN attributes a ON a.id = av.attribute_id
JOIN attribute_groups g ON g.id = a.group_id;