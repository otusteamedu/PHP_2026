DROP VIEW IF EXISTS view_marketing;
CREATE VIEW view_marketing AS
SELECT
    m.title,
    t.name AS attribute_type,
    a.name AS attribute,
    COALESCE(
            av.value_text,
            to_char(av.value_date, 'YYYY-MM-DD'),
            av.value_boolean::text,
            av.value_integer::text,
            av.value_float::text
    ) AS value
FROM attribute_values av
JOIN attributes a ON a.id = av.attribute_id
JOIN attribute_types t ON t.id = a.attribute_type_id
JOIN movie m ON m.id = av.movie_id
WHERE t.code != 'timestamp';