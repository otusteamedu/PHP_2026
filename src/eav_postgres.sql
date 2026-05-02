CREATE TABLE IF NOT EXISTS categories (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(40) NOT NULL,
    php_type VARCHAR(10) DEFAULT NULL,
    CONSTRAINT uq_title_type UNIQUE (title, php_type)
);

CREATE TABLE IF NOT EXISTS entities (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS attributes (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    category_id BIGINT NOT NULL,
    CONSTRAINT attribute_category_fk FOREIGN KEY (category_id)
    REFERENCES categories(id) ON DELETE CASCADE,
    CONSTRAINT uq_attribute_type UNIQUE (title, category_id)
);

CREATE TABLE IF NOT EXISTS entity_attribute_values (
    id BIGSERIAL PRIMARY KEY,
    entity_id BIGINT NOT NULL,
    attribute_id BIGINT NOT NULL,
    value TEXT NOT NULL,
    CONSTRAINT entity_eav_fk FOREIGN KEY (entity_id)
    REFERENCES entities(id) ON DELETE CASCADE,
    CONSTRAINT attribute_eav_fk FOREIGN KEY (attribute_id)
    REFERENCES attributes(id) ON DELETE CASCADE,
    CONSTRAINT uq_entity_attribute UNIQUE (entity_id, attribute_id)
);

CREATE INDEX idx_attributes_category_id ON attributes(category_id);
CREATE INDEX idx_eav_entity_id ON entity_attribute_values(entity_id);
CREATE INDEX idx_eav_attribute_id ON entity_attribute_values(attribute_id);
CREATE INDEX idx_value_pattern ON entity_attribute_values(value varchar_pattern_ops);

INSERT INTO categories (title, php_type) VALUES
('Boolean', 'bool'),
('Integer', 'int'),
('Float', 'float'),
('String', 'string'),
('Text', 'string'),
('Array', 'array'),
('Date', 'Date'),
('DateTime', 'DateTime'),
('Json', 'json');

INSERT INTO entities (title) VALUES
('Terminator 2. Judgment day'),
('Forrest Gump'),
('Amelie');

INSERT INTO attributes (title, category_id) VALUES
('description', 5),
('release date', 7),
('recency url', 4),
('ratio', 3),
('director', 4),
('revenue', 3),
('schedule', 7);

INSERT INTO entity_attribute_values (entity_id, attribute_id, value) VALUES
(1, 1, 'When John, Sarah''s 10-year-old son is attacked by T-1000, a new robot created by Skynet to destroy humanity, Terminator takes it upon himself to fight T-1000 to save John and the human race.'),
(2, 1, 'Forrest, a man with low IQ, recounts the early years of his life when he found himself in the middle of key historical events. All he wants now is to be reunited with his childhood sweetheart, Jenny.'),
(3, 1, 'Despite being caught in her imaginative world, Amelie, a young waitress, decides to help people find happiness. Her quest to spread joy leads her on a journey where she finds true love.'),
(1, 2, '1991-12-25'),
(2, 2, '1994-07-06'),
(3, 2, '2001-09-20'),
(1, 3, 'https://www.rottentomatoes.com/m/terminator_2_judgment_day'),
(2, 3, 'https://www.rottentomatoes.com/m/forrest_gump'),
(3, 3, 'https://www.rottentomatoes.com/m/amelie'),
(3, 4, '9.8'),
(2, 5, '');


CREATE OR REPLACE FUNCTION add_attribute_to_all_entities(
    p_title VARCHAR(100),
    p_category_id BIGINT,
    p_default_value TEXT
)
RETURNS TABLE(
    out_attribute_id BIGINT,
    out_attribute_title VARCHAR,
    out_entities_updated BIGINT,
    out_rows_affected BIGINT
)
LANGUAGE plpgsql
AS $$
DECLARE
v_attribute_id BIGINT;
    v_row_count BIGINT;
    v_total_entities BIGINT;
BEGIN
    -- Get or create attribute
SELECT id INTO v_attribute_id
FROM attributes
WHERE title = p_title;

IF NOT FOUND THEN
        INSERT INTO attributes (title, category_id)
        VALUES (p_title, p_category_id)
        RETURNING id INTO v_attribute_id;

        RAISE NOTICE 'Created new attribute: % (ID: %)', p_title, v_attribute_id;
ELSE
        RAISE NOTICE 'Found existing attribute: % (ID: %)', p_title, v_attribute_id;
END IF;

    -- Upsert values for all entities
WITH upsert AS (
INSERT INTO entity_attribute_values (entity_id, attribute_id, value)
SELECT e.id, v_attribute_id, p_default_value
FROM entities e
    ON CONFLICT (entity_id, attribute_id)
        DO UPDATE SET value = EXCLUDED.value
                   RETURNING 1
                   )
SELECT COUNT(*) INTO v_row_count FROM upsert;

-- Get total entities count
SELECT COUNT(*) INTO v_total_entities FROM entities;

-- Return summary
out_attribute_id := v_attribute_id;
    out_attribute_title := p_title;
    out_entities_updated := v_total_entities;
    out_rows_affected := v_row_count;

RETURN NEXT;
END;
$$;

CREATE OR REPLACE VIEW movie_details AS
SELECT
    e.id,
    e.title,
    MAX(CASE WHEN a.title = 'description' THEN eav.value END) AS description,
    MAX(CASE WHEN a.title = 'release date' THEN eav.value END) AS release_date,
    MAX(CASE WHEN a.title = 'recency url' THEN eav.value END) AS url,
    MAX(CASE WHEN a.title = 'ratio' THEN eav.value END) AS ratio,
    MAX(CASE WHEN a.title = 'schedule' THEN eav.value END) AS schedule,
    MAX(CASE WHEN a.title = 'director' THEN eav.value END) AS director,
    MAX(CASE WHEN a.title = 'revenue' THEN eav.value END) AS revenue
FROM entities e
         INNER JOIN entity_attribute_values eav ON e.id = eav.entity_id
         INNER JOIN attributes a ON eav.attribute_id = a.id
GROUP BY e.id, e.title;
