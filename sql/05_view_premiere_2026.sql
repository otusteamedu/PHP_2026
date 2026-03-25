SET search_path TO otus;

CREATE OR REPLACE VIEW premiere_2026 AS
SELECT
    m.title,
    av.value_date::text AS premiere
FROM
    movies m
    JOIN attribute_values av ON av.movie_id = m.id
    JOIN attributes a ON a.id = av.attribute_id
    JOIN attribute_types at ON at.id = a.attribute_type_id
WHERE 
	a.id = 6
	AND av.value_date > '2026-01-01';

SELECT * FROM premiere_2026;