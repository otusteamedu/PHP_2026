SET search_path TO otus;

CREATE OR REPLACE VIEW v_at_a_v AS
SELECT
    m.title,
    at.name AS attribute_type_name,
    a.name AS attribute_name,
    COALESCE(
        av.value_text,
        CASE
            WHEN av.value_boolean IS TRUE THEN 'да'
            WHEN av.value_boolean IS FALSE THEN 'нет'
            ELSE NULL
        END,
        av.value_date::text,
        av.value_float::text,
        av.value_int::text
    ) AS value
FROM
    movies m
    JOIN attribute_values av ON av.movie_id = m.id
    JOIN attributes a ON a.id = av.attribute_id
    JOIN attribute_types at ON at.id = a.attribute_type_id;

SELECT * FROM v_at_a_v;