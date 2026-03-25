SET
    search_path TO otus;
    
INSERT INTO
    cinemas (id, name, address)
values
    (
        1,
        'Кинотеатр Юность',
        'г. Волгоград, ул. Мира, д. 2'
    );

INSERT INTO
    halls (id, cinema_id, number)
values
    (1, 1, 1),
    (2, 1, 2),
    (3, 1, 3);

INSERT INTO
    places (id, hall_id, row_num, place_num, place_type)
values
    (1, 1, 1, 1, 'disabled'),
    (2, 1, 1, 2, 'standard'),
    (3, 1, 1, 3, 'standard'),
    (4, 1, 2, 4, 'standard'),
    (5, 1, 2, 5, 'standard'),
    (6, 1, 2, 6, 'standard'),
    (7, 1, 3, 7, 'vip'),
    (8, 1, 3, 8, 'vip'),
    (9, 1, 3, 9, 'vip'),
    (10, 2, 1, 1, 'disabled'),
    (11, 2, 1, 2, 'standard'),
    (12, 2, 1, 3, 'standard'),
    (13, 2, 2, 4, 'standard'),
    (14, 2, 2, 5, 'standard'),
    (15, 2, 2, 6, 'standard'),
    (16, 2, 3, 7, 'vip'),
    (17, 2, 3, 8, 'vip'),
    (18, 2, 3, 9, 'vip'),
    (19, 3, 1, 1, 'disabled'),
    (20, 3, 1, 2, 'standard'),
    (21, 3, 1, 3, 'standard'),
    (22, 3, 2, 4, 'standard'),
    (23, 3, 2, 5, 'standard'),
    (24, 3, 2, 6, 'standard'),
    (25, 3, 3, 7, 'vip'),
    (26, 3, 3, 8, 'vip'),
    (27, 3, 3, 9, 'vip');

INSERT INTO
    customers (id, first_name, last_name, email, phone)
values
    (
        1,
        'Иван',
        'Иванов',
        'example1@example.ru',
        '79998887766'
    ),
    (
        2,
        'Петр',
        'Петров',
        'example2@example.ru',
        '79998887755'
    ),
    (
        3,
        'Василий',
        'Васильев',
        'example3@example.ru',
        '79998887744'
    );

INSERT INTO
    movies (id, title, description, duration)
values
    (1, 'Чебурашка', 'Первый фильм про Чебурашку', 90),
    (2, 'Чебурашка 2', 'Второй фильм про Чебурашку', 90),
    (3, 'Чебурашка 3', 'Третий фильм про Чебурашку', 90);

INSERT INTO
    sessions (id, movie_id, hall_id, start_time, end_time)
values
    (
        1,
        1,
        1,
        '2026-02-28 09:00:00',
        '2026-02-28 10:30:00'
    ),
    (
        2,
        1,
        1,
        '2026-02-28 12:00:00',
        '2026-02-28 13:30:00'
    ),
    (
        3,
        1,
        1,
        '2026-02-28 15:00:00',
        '2026-02-28 16:30:00'
    ),
    (
        4,
        2,
        2,
        '2026-02-28 09:00:00',
        '2026-02-28 10:30:00'
    ),
    (
        5,
        2,
        2,
        '2026-02-28 12:00:00',
        '2026-02-28 13:30:00'
    ),
    (
        6,
        2,
        2,
        '2026-02-28 15:00:00',
        '2026-02-28 16:30:00'
    ),
    (
        7,
        3,
        3,
        '2026-02-28 09:00:00',
        '2026-02-28 10:30:00'
    ),
    (
        8,
        3,
        3,
        '2026-02-28 12:00:00',
        '2026-02-28 13:30:00'
    ),
    (
        9,
        3,
        3,
        '2026-02-28 15:00:00',
        '2026-02-28 16:30:00'
    );

INSERT INTO
    prices (id, session_id, place_type, price)
values
    (1, 1, 'disabled', 100),
    (2, 1, 'standard', 200),
    (3, 1, 'vip', 300),
    (4, 2, 'disabled', 100),
    (5, 2, 'standard', 200),
    (6, 2, 'vip', 300),
    (7, 3, 'disabled', 100),
    (8, 3, 'standard', 200),
    (9, 3, 'vip', 300),
    (10, 4, 'disabled', 100),
    (11, 4, 'standard', 200),
    (12, 4, 'vip', 300),
    (13, 5, 'disabled', 100),
    (14, 5, 'standard', 200),
    (15, 5, 'vip', 300),
    (16, 6, 'disabled', 100),
    (17, 6, 'standard', 200),
    (18, 6, 'vip', 300),
    (19, 7, 'disabled', 100),
    (20, 7, 'standard', 200),
    (21, 7, 'vip', 300),
    (22, 8, 'disabled', 100),
    (23, 8, 'standard', 200),
    (24, 8, 'vip', 300),
    (25, 9, 'disabled', 100),
    (26, 9, 'standard', 200),
    (27, 9, 'vip', 300);

INSERT INTO
    orders (id, customer_id, total_amount)
values
    (1, 1, 0),
    (2, 1, 0),
    (3, 1, 0),
    (4, 2, 0),
    (5, 2, 0),
    (6, 2, 0),
    (7, 3, 0),
    (8, 3, 0),
    (9, 3, 0),
    (10, 3, 0);

INSERT INTO
    tickets (id, session_id, place_id, order_id)
values
    (1, 3, 1, 1),
    (2, 4, 2, 1),
    (3, 8, 3, 1),
    (4, 6, 25, 2),
    (5, 9, 26, 2),
    (6, 7, 27, 2),
    (7, 7, 4, 3),
    (8, 7, 5, 4),
    (9, 6, 1, 5),
    (10, 6, 4, 6),
    (11, 5, 5, 7),
    (12, 5, 6, 8),
    (13, 9, 25, 9);

INSERT INTO 
    attribute_types (id, name, code, value_type)
VALUES
    (1, 'Рецензии', 'reviews', 'text'),
    (2, 'Премия', 'awards', 'boolean'),
    (3, 'Важные даты', 'important_dates', 'date'),
    (4, 'Служебные даты', 'internal_dates', 'date'),
    (5, 'Некое числовое значение', 'some_integer', 'int'),
    (6, 'Некое числовое значнеие с плавающей запятой', 'some_float', 'float');

INSERT INTO 
    attributes (id, attribute_type_id, name) 
VALUES
    (1, 1, 'Рецензия критика Иванова'),
    (2, 1, 'Отзыв киноакадемии X'),
    (3, 2, 'Оскар'),
    (4, 2, 'Ника'),
    (5, 3, 'Мировая премьера'),
    (6, 3, 'Премьера в РФ'),
    (7, 4, 'Дата начала продажи билетов'),
    (8, 4, 'Запуск ТВ‑рекламы'),
    (9, 5, 'Какое то число'),
    (10, 6, 'Какое то число с запятой');

INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (1, 6, '2024-03-21');
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (2, 6, '2025-03-21');
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (3, 6, '2026-03-21');
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (1, 5, '2024-03-28');
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (2, 5, '2025-03-28');
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (3, 5, '2026-03-28');
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (1, 7, '2024-03-14');
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (2, 7, '2025-03-14');
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES (3, 7, '2026-03-14');
INSERT INTO attribute_values (movie_id, attribute_id, value_text) VALUES (1, 1, 'Хорошо');
INSERT INTO attribute_values (movie_id, attribute_id, value_text) VALUES (2, 1, 'Отлично');
INSERT INTO attribute_values (movie_id, attribute_id, value_text) VALUES (3, 1, 'Превосходно');
INSERT INTO attribute_values (movie_id, attribute_id, value_boolean) VALUES (1, 3, true);
INSERT INTO attribute_values (movie_id, attribute_id, value_boolean) VALUES (2, 3, true);
INSERT INTO attribute_values (movie_id, attribute_id, value_boolean) VALUES (3, 3, false);
INSERT INTO attribute_values (movie_id, attribute_id, value_int) VALUES (1, 9, 5);
INSERT INTO attribute_values (movie_id, attribute_id, value_float) VALUES (2, 10, 8);
INSERT INTO attribute_values (movie_id, attribute_id, value_float) VALUES (3, 10, 8.2432);




