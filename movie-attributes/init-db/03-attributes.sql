SET search_path TO cinema;

CREATE TABLE attribute_groups (
    id SMALLINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE attributes (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    group_id SMALLINT NOT NULL REFERENCES attribute_groups(id),
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    data_type VARCHAR(20) NOT NULL,

    CHECK (data_type IN ('text', 'boolean', 'date', 'numeric'))
);

CREATE TABLE attribute_values (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    movie_id BIGINT NOT NULL REFERENCES movies(id) ON DELETE CASCADE,
    attribute_id INT NOT NULL REFERENCES attributes(id) ON DELETE CASCADE,
    value_text TEXT,
    value_number NUMERIC,
    value_date DATE,
    value_boolean BOOLEAN,

    UNIQUE (movie_id, attribute_id),

    CHECK (
        (value_text IS NOT NULL)::int +
        (value_number IS NOT NULL)::int +
        (value_date IS NOT NULL)::int +
        (value_boolean IS NOT NULL)::int = 1
    )
);

CREATE INDEX idx_attr_values_movie_attr ON attribute_values(movie_id, attribute_id);

CREATE INDEX idx_attr_values_value ON attribute_values(attribute_id, value_text);