INSERT INTO attribute_types (name, description)
VALUES ('review', 'Рецензии критиков и отзывы'),
       ('award', 'Премии, номинации (логическое значение: получена или нет)'),
       ('important_date', 'Важные даты (мировая премьера, премьера в РФ и т.п.)'),
       ('service_date', 'Служебные даты для планирования');

INSERT INTO movies (name)
VALUES ('Дюна: Часть вторая'),
       ('Оппенгеймер'),
       ('Барби');

INSERT INTO attributes (attribute_type_id, name, data_type)
VALUES ((SELECT id FROM attribute_types WHERE name = 'review'), 'Рецензия критика (The Guardian)', 'text'),
       ((SELECT id FROM attribute_types WHERE name = 'review'), 'Отзыв неизвестной киноакадемии', 'text');

INSERT INTO attributes (attribute_type_id, name, data_type)
VALUES ((SELECT id FROM attribute_types WHERE name = 'award'), 'Оскар', 'boolean'),
       ((SELECT id FROM attribute_types WHERE name = 'award'), 'Ника', 'boolean');

INSERT INTO attributes (attribute_type_id, name, data_type)
VALUES ((SELECT id FROM attribute_types WHERE name = 'important_date'), 'Мировая премьера', 'date'),
       ((SELECT id FROM attribute_types WHERE name = 'important_date'), 'Премьера в РФ', 'date');

INSERT INTO attributes (attribute_type_id, name, data_type)
VALUES ((SELECT id FROM attribute_types WHERE name = 'service_date'), 'Дата начала продажи билетов', 'date'),
       ((SELECT id FROM attribute_types WHERE name = 'service_date'), 'Старт рекламной кампании на ТВ', 'date');

INSERT INTO attributes (attribute_type_id, name, data_type)
VALUES
    ((SELECT id FROM attribute_types WHERE name = 'service_date'), 'Бюджет фильма', 'float'),
    ((SELECT id FROM attribute_types WHERE name = 'service_date'), 'Продано билетов', 'integer');

INSERT INTO attribute_values (movie_id, attribute_id, value_text)
SELECT m.id, a.id, 'Визуально потрясающе, но сюжет немного затянут.'
FROM movies m, attributes a
WHERE m.name = 'Дюна: Часть вторая' AND a.name = 'Рецензия критика (The Guardian)';

INSERT INTO attribute_values (movie_id, attribute_id, value_boolean)
SELECT m.id, a.id, false
FROM movies m, attributes a
WHERE m.name = 'Дюна: Часть вторая' AND a.name = 'Оскар';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, '2024-02-15'::date
FROM movies m, attributes a
WHERE m.name = 'Дюна: Часть вторая' AND a.name = 'Мировая премьера';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, '2024-02-29'::date
FROM movies m, attributes a
WHERE m.name = 'Дюна: Часть вторая' AND a.name = 'Премьера в РФ';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, CURRENT_DATE - 5
FROM movies m, attributes a
WHERE m.name = 'Дюна: Часть вторая' AND a.name = 'Дата начала продажи билетов';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, CURRENT_DATE + 20
FROM movies m, attributes a
WHERE m.name = 'Дюна: Часть вторая' AND a.name = 'Старт рекламной кампании на ТВ';

INSERT INTO attribute_values (movie_id, attribute_id, value_text)
SELECT m.id, a.id, 'Гениальная работа Нолана, три часа пролетают незаметно.'
FROM movies m, attributes a
WHERE m.name = 'Оппенгеймер' AND a.name = 'Рецензия критика (The Guardian)';

INSERT INTO attribute_values (movie_id, attribute_id, value_boolean)
SELECT m.id, a.id, true
FROM movies m, attributes a
WHERE m.name = 'Оппенгеймер' AND a.name = 'Оскар';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, '2023-07-11'::date
FROM movies m, attributes a
WHERE m.name = 'Оппенгеймер' AND a.name = 'Мировая премьера';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, '2023-07-20'::date
FROM movies m, attributes a
WHERE m.name = 'Оппенгеймер' AND a.name = 'Премьера в РФ';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, CURRENT_DATE - 10
FROM movies m, attributes a
WHERE m.name = 'Оппенгеймер' AND a.name = 'Дата начала продажи билетов';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, CURRENT_DATE
FROM movies m, attributes a
WHERE m.name = 'Оппенгеймер' AND a.name = 'Старт рекламной кампании на ТВ';

INSERT INTO attribute_values (movie_id, attribute_id, value_text)
SELECT m.id, a.id, 'Яркий, весёлый, с неожиданно глубокими мыслями.'
FROM movies m, attributes a
WHERE m.name = 'Барби' AND a.name = 'Рецензия критика (The Guardian)';

INSERT INTO attribute_values (movie_id, attribute_id, value_boolean)
SELECT m.id, a.id, false
FROM movies m, attributes a
WHERE m.name = 'Барби' AND a.name = 'Оскар';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, '2023-07-09'::date
FROM movies m, attributes a
WHERE m.name = 'Барби' AND a.name = 'Мировая премьера';

INSERT INTO attribute_values (movie_id, attribute_id, value_int)
SELECT m.id, a.id, 500000
FROM movies m, attributes a
WHERE m.name = 'Дюна: Часть вторая' AND a.name = 'Продано билетов';

INSERT INTO attribute_values (movie_id, attribute_id, value_float)
SELECT m.id, a.id, 145.75
FROM movies m, attributes a
WHERE m.name = 'Барби' AND a.name = 'Бюджет фильма';