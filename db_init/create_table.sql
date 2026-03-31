CREATE TABLE IF NOT EXISTS movies
(
    id   SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS attribute_types
(
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE IF NOT EXISTS attributes
(
    id                SERIAL PRIMARY KEY,
    attribute_type_id INTEGER      NOT NULL REFERENCES attribute_types (id) ON DELETE CASCADE,
    name              VARCHAR(255) NOT NULL,
    data_type         VARCHAR(20)  NOT NULL,
    display_order     INTEGER DEFAULT 0,
    UNIQUE (attribute_type_id, name)
);

CREATE TABLE IF NOT EXISTS attribute_values
(
    id            SERIAL PRIMARY KEY,
    movie_id      INTEGER NOT NULL REFERENCES movies (id) ON DELETE CASCADE,
    attribute_id  INTEGER NOT NULL REFERENCES attributes (id) ON DELETE CASCADE,
    value_text    TEXT,
    value_boolean BOOLEAN,
    value_date    DATE,
    value_float   NUMERIC(12, 4),
    CONSTRAINT chk_one_value CHECK (
        (value_text IS NOT NULL):: integer +
        (value_boolean IS NOT NULL):: integer +
        (value_date IS NOT NULL):: integer +
        (value_float IS NOT NULL):: integer = 1
    ),
    UNIQUE (movie_id, attribute_id)
);

CREATE OR REPLACE VIEW marketing_view AS
SELECT
    m.name AS movie,
    at.name AS attribute_type,
    a.name AS attribute,
    CASE
        WHEN a.data_type = 'text'    THEN av.value_text
        WHEN a.data_type = 'boolean' THEN CASE WHEN av.value_boolean THEN 'Да' ELSE 'Нет' END
        WHEN a.data_type = 'date'    THEN to_char(av.value_date, 'YYYY-MM-DD')
        WHEN a.data_type = 'float'   THEN av.value_float::text
        ELSE NULL
        END AS value
FROM movies m
JOIN attribute_values av ON m.id = av.movie_id
JOIN attributes a ON av.attribute_id = a.id
JOIN attribute_types at ON a.attribute_type_id = at.id;

CREATE OR REPLACE VIEW service_tasks_view AS
WITH service_attrs AS (
    SELECT a.id AS attr_id, a.name AS attr_name
    FROM attributes a
             JOIN attribute_types at ON a.attribute_type_id = at.id
        WHERE at.name = 'service_date'
        )
SELECT
    m.name AS movie,
    string_agg(DISTINCT CASE WHEN av.value_date = CURRENT_DATE THEN sa.attr_name END, ', ') AS tasks_today,
    string_agg(DISTINCT CASE WHEN av.value_date = CURRENT_DATE + INTERVAL '20 days' THEN sa.attr_name END, ', ') AS tasks_in_20_days
FROM movies m
         JOIN attribute_values av ON m.id = av.movie_id
         JOIN service_attrs sa ON av.attribute_id = sa.attr_id
WHERE av.value_date IN (CURRENT_DATE, CURRENT_DATE + INTERVAL '20 days')
GROUP BY m.id, m.name;

