DROP VIEW IF EXISTS marketing_data;
DROP VIEW IF EXISTS admin_tasks;
DROP TABLE IF EXISTS values;
DROP TABLE IF EXISTS attributes;
DROP TABLE IF EXISTS attribute_types;
DROP TABLE IF EXISTS films;
DROP TYPE IF EXISTS attribute_status;

CREATE TABLE films
(
    id   INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE attribute_types
(
    name VARCHAR(255) NOT NULL UNIQUE PRIMARY KEY,
    description VARCHAR
);

CREATE TYPE attribute_status AS ENUM ('admin', 'common');

CREATE TABLE attributes
(
    id   INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    status attribute_status default 'common',
    CONSTRAINT fk_attribute_type
        FOREIGN KEY (type) REFERENCES attribute_types(name) ON DELETE RESTRICT
);

CREATE TABLE values
(
    id   INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    film_id INT NOT NULL,
    attribute_id INT NOT NULL,
    v_text TEXT,
    v_bool BOOLEAN,
    v_int INT,
    v_float FLOAT,
    v_date DATE,
    v_time TIME,
    v_timestamp TIMESTAMP,
    v_timestamp_tz TIMESTAMP WITH TIME ZONE,
    CONSTRAINT fk_value_film
        FOREIGN KEY (film_id) REFERENCES films(id) ON DELETE CASCADE,
    CONSTRAINT fk_value_attribute
        FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE RESTRICT
);

CREATE INDEX idx_values_film_id ON values (film_id);
CREATE INDEX idx_values_attribute_id ON values (attribute_id);

INSERT INTO films (name)
VALUES ('фильм 1'),
       ('фильм 2'),
       ('фильм 3');

INSERT INTO attribute_types (name, description)
VALUES ('text', 'Текстовое значение'),
       ('bool', 'Логическое значение'),
       ('int', 'Целое число'),
       ('float', 'Десятичное число'),
       ('date', 'Дата YYYY-MM-DD'),
       ('time', 'Время HH:MM:SS'),
       ('timestamp', 'Дата и время YYYY-MM-DD HH:MM:SS'),
       ('timestamp_tz', 'Дата и время с часовым поясом YYYY-MM-DD HH:MM:SS+hh:mi');

INSERT INTO attributes (name, type, status)
VALUES ('рецензия', 'text', 'common'),
       ('премия Ника', 'bool', 'common'),
       ('премия Оскар', 'bool', 'common'),
       ('мировая премьера', 'date', 'common'),
       ('премьера в РФ', 'date', 'common'),
       ('дата начала продажи билетов', 'date', 'admin'),
       ('когда запускать рекламу на ТВ', 'date', 'admin'),
       ('служебная задача', 'date', 'admin');

INSERT INTO values (film_id, attribute_id, v_text) VALUES (1, 1, 'Положительная рецензия');
INSERT INTO values (film_id, attribute_id, v_bool) VALUES (1, 2, true);
INSERT INTO values (film_id, attribute_id, v_bool) VALUES (1, 3, false);
INSERT INTO values (film_id, attribute_id, v_date) VALUES (1, 4, '2003-12-25');
INSERT INTO values (film_id, attribute_id, v_date) VALUES (1, 5, '2004-07-25');
INSERT INTO values (film_id, attribute_id, v_date) VALUES (1, 6, '2003-11-25');
INSERT INTO values (film_id, attribute_id, v_date) VALUES (1, 7, '2003-10-25');
INSERT INTO values (film_id, attribute_id, v_date) VALUES (1, 8, DATE(NOW()));
INSERT INTO values (film_id, attribute_id, v_date) VALUES (1, 8, DATE(NOW() + INTERVAL '20 days'));

CREATE VIEW admin_tasks AS
    SELECT f.name film_name,
           a.name task_name,
           CASE
               WHEN v.v_date = DATE(NOW()) THEN 'Сегодня' ELSE 'Через 20 дней'
           END AS task_time
    FROM attributes a
    INNER JOIN values v on a.id = v.attribute_id
    INNER JOIN films f on f.id = v.film_id
    WHERE a.status = 'admin'
      AND v.v_date IN (DATE(NOW()), DATE(NOW() + INTERVAL '20 days'));

CREATE VIEW marketing_data AS
SELECT f.name film_name,
       at.description,
       a.name attr_name,
       CASE
           WHEN a.type = 'text' THEN v.v_text::TEXT
           WHEN a.type = 'bool' THEN v.v_bool::TEXT
           WHEN a.type = 'int' THEN v.v_int::TEXT
           WHEN a.type = 'float' THEN v.v_float::TEXT
           WHEN a.type = 'date' THEN v.v_date::TEXT
           WHEN a.type = 'time' THEN v.v_time::TEXT
           WHEN a.type = 'timestamp' THEN v.v_timestamp::TEXT
           WHEN a.type = 'timestamp_tz' THEN v.v_timestamp_tz::TEXT
       END AS value
FROM values v
     INNER JOIN attributes a on a.id = v.attribute_id
     INNER JOIN attribute_types at on a.type = at.name
     INNER JOIN films f on f.id = v.film_id;
