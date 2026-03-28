DROP TABLE IF EXISTS attribute_types CASCADE;
DROP TABLE IF EXISTS movie CASCADE;
DROP TABLE IF EXISTS attributes CASCADE;
DROP TABLE IF EXISTS attribute_values CASCADE;

-- 1. attribute_types
CREATE TABLE attribute_types (
    id SMALLSERIAL PRIMARY KEY,
    code TEXT NOT NULL UNIQUE, -- text, date, boolean, numeric, timestamp
    name TEXT NOT NULL
);
INSERT INTO attribute_types (code, name) VALUES
('text',      'Текст'),
('date',      'Дата'),
('boolean',   'Логическое'),
('integer',   'Целое число'),
('float',     'Дробное число'),
('timestamp', 'Дата и время');

-- 2. movie
CREATE TABLE movie (
    id BIGSERIAL PRIMARY KEY,
    title TEXT NOT NULL,
    release_year SMALLINT,
    created_at TIMESTAMP NOT NULL DEFAULT now()
);
CREATE INDEX idx_movie_title ON movie(title);

-- 3. attributes
CREATE TABLE attributes (
    id BIGSERIAL PRIMARY KEY,
    code TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    attribute_type_id SMALLINT NOT NULL,
    is_required BOOLEAN NOT NULL DEFAULT false,
    CONSTRAINT fk_attr_type FOREIGN KEY (attribute_type_id) REFERENCES attribute_types(id)
);
CREATE INDEX idx_attributes_type ON attributes(attribute_type_id);

-- 4. attribute_values
CREATE TABLE attribute_values (
    id BIGSERIAL PRIMARY KEY,
    movie_id BIGINT NOT NULL,
    attribute_id BIGINT NOT NULL,
    value_text TEXT,
    value_date DATE,
    value_boolean BOOLEAN,
    value_integer INT,
    value_float FLOAT,
    value_timestamp TIMESTAMPTZ,
    created_at TIMESTAMP NOT NULL DEFAULT now(),
        CONSTRAINT fk_attribute_values_movie FOREIGN KEY (movie_id) REFERENCES movie(id),
        CONSTRAINT fk_attribute_values_attr FOREIGN KEY (attribute_id) REFERENCES attributes(id),
        CONSTRAINT chk_one_value CHECK (
            (value_text IS NOT NULL)::int +
            (value_date IS NOT NULL)::int +
            (value_boolean IS NOT NULL)::int +
            (value_integer IS NOT NULL)::int +
            (value_float IS NOT NULL)::int +
            (value_timestamp IS NOT NULL)::int = 1
        )
);

-- индексы
CREATE INDEX idx_attribute_values_movie ON attribute_values(movie_id);
CREATE INDEX idx_attribute_values_attr ON attribute_values(attribute_id);
CREATE INDEX idx_attribute_values_movie_attr ON attribute_values(movie_id, attribute_id);
CREATE INDEX idx_attribute_values_date ON attribute_values(value_date) WHERE value_date IS NOT NULL;
CREATE INDEX idx_attribute_values_timestamp ON attribute_values(value_timestamp) WHERE value_timestamp IS NOT NULL;
CREATE INDEX idx_attribute_values_true ON attribute_values(attribute_id, movie_id) WHERE value_boolean = true;
CREATE INDEX idx_attribute_values_integer ON attribute_values(value_integer) WHERE value_integer IS NOT NULL;
CREATE INDEX idx_attribute_values_float ON attribute_values(value_float) WHERE value_float IS NOT NULL;

-- attribute_type_id: 1=text, 2=date, 3=boolean, 4=integer, 5=float, 6=timestamp
-- Премии (boolean)
INSERT INTO attributes (code, name, attribute_type_id, is_required) VALUES
('oscar', 'Оскар', 3, false),
('nika',  'Ника',  3, false);
-- Важные даты (date)
INSERT INTO attributes (code, name, attribute_type_id, is_required) VALUES
('world_premiere', 'Мировая премьера', 2, false),
('ru_premiere', 'Премьера в РФ', 2, false);
-- Рецензии (text)
INSERT INTO attributes (code, name, attribute_type_id, is_required) VALUES
('critic_review',          'Рецензия критиков',                1, false),
('unknown_academy_review', 'Отзыв неизвестной киноакадемии',   1, false);
-- Служебные даты (timestamp)
INSERT INTO attributes (code, name, attribute_type_id, is_required) VALUES
('ads_start', 'Запуск рекламы на ТВ', 6, false),
('ticket_sales_start', 'Начало продажи билетов', 6, false);


INSERT INTO movie (title, release_year) VALUES
('Фильм 1', 2022),
('Фильм 2', 2023),
('Фильм 3', 2024);

-- Фильм 1
INSERT INTO attribute_values (movie_id, attribute_id, value_boolean) VALUES (1, 1, true);
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (1, 3, '2022-03-15');
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (1, 4, '2022-04-07');
INSERT INTO attribute_values (movie_id, attribute_id, value_text) VALUES (1, 5, 'Новый супер фильм');
INSERT INTO attribute_values (movie_id, attribute_id, value_timestamp) VALUES (1, 7, CURRENT_TIMESTAMP);

-- Фильм 2
INSERT INTO attribute_values (movie_id, attribute_id, value_boolean) VALUES (2, 2, true);
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (2, 3, '2023-07-01');
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (2, 4, '2023-08-10');
INSERT INTO attribute_values (movie_id, attribute_id, value_text) VALUES (2, 6, 'Новый артхаус');
INSERT INTO attribute_values (movie_id, attribute_id, value_timestamp) VALUES (2, 8, CURRENT_TIMESTAMP + INTERVAL '10 days');

-- Фильм 3
INSERT INTO attribute_values (movie_id, attribute_id, value_boolean) VALUES (3, 1, true);
INSERT INTO attribute_values (movie_id, attribute_id, value_boolean) VALUES (3, 2, true);
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (3, 3, '2024-01-20');
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (3, 4, '2024-02-14');
INSERT INTO attribute_values (movie_id, attribute_id, value_text) VALUES (3, 5, 'Лучший фильм тысячелетия');
INSERT INTO attribute_values (movie_id, attribute_id, value_timestamp) VALUES (3, 7, CURRENT_TIMESTAMP + INTERVAL '5 days');
INSERT INTO attribute_values (movie_id, attribute_id, value_timestamp) VALUES (3, 8, CURRENT_TIMESTAMP + INTERVAL '60 days');