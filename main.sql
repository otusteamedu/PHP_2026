DROP SCHEMA IF EXISTS otus CASCADE;

CREATE SCHEMA otus;

SET
    search_path TO otus;

drop table if exists cinemas CASCADE;

drop table if exists halls CASCADE;

drop table if exists places CASCADE;

drop table if exists movies CASCADE;

drop table if exists prices CASCADE;

drop table if exists sessions CASCADE;

drop table if exists customers CASCADE;

drop table if exists prices CASCADE;

drop table if exists orders CASCADE;

drop table if exists tickets CASCADE;

CREATE TABLE
    IF NOT EXISTS cinemas (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        address VARCHAR(255) NOT NULL
    );

CREATE TABLE
    IF NOT EXISTS halls (
        id SERIAL PRIMARY KEY,
        cinema_id INT NOT NULL REFERENCES cinemas (id) ON DELETE CASCADE,
        number INT NOT NULL
    );

CREATE TABLE
    IF NOT EXISTS places (
        id SERIAL PRIMARY KEY,
        hall_id INT NOT NULL REFERENCES halls (id) ON DELETE CASCADE,
        row_num INT NOT NULL,
        place_num INT NOT NULL,
        place_type VARCHAR(15) NOT NULL,
        UNIQUE (hall_id, row_num, place_num)
    );

CREATE TABLE
    IF NOT EXISTS movies (
        id SERIAL PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description VARCHAR(255),
        duration INT NOT NULL CHECK (duration > 0)
    );

CREATE TABLE
    IF NOT EXISTS sessions (
        id SERIAL PRIMARY KEY,
        movie_id INT NOT NULL REFERENCES movies (id) ON DELETE CASCADE,
        hall_id INT NOT NULL REFERENCES halls (id) ON DELETE CASCADE,
        start_time TIMESTAMP NOT NULL,
        end_time TIMESTAMP NOT NULL,
        CHECK (start_time < end_time)
    );

CREATE TABLE
    IF NOT EXISTS prices (
        id SERIAL PRIMARY KEY,
        session_id INT NOT NULL REFERENCES sessions (id) ON DELETE CASCADE,
        place_type VARCHAR(15) NOT NULL,
        price DECIMAL(10, 2) NOT NULL CHECK (price >= 0),
        UNIQUE (session_id, place_type)
    );

CREATE TABLE
    IF NOT EXISTS customers (
        id SERIAL PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE,
        phone VARCHAR(11),
        created_at TIMESTAMP DEFAULT NOW ()
    );

CREATE TABLE
    IF NOT EXISTS orders (
        id SERIAL PRIMARY KEY,
        customer_id INT NOT NULL REFERENCES customers (id) ON DELETE SET NULL,
        total_amount DECIMAL(10, 2) NOT NULL CHECK (total_amount >= 0),
        order_date TIMESTAMP DEFAULT NOW (),
        status VARCHAR(20) DEFAULT 'pending' CHECK (
            status IN ('pending', 'confirmed', 'cancelled', 'completed')
        )
    );

CREATE TABLE
    IF NOT EXISTS tickets (
        id SERIAL PRIMARY KEY,
        session_id INT NOT NULL REFERENCES sessions (id) ON DELETE CASCADE,
        place_id INT NOT NULL REFERENCES places (id) ON DELETE CASCADE,
        order_id INT NOT NULL REFERENCES orders (id) ON DELETE CASCADE,
        created_at TIMESTAMP DEFAULT NOW (),
        UNIQUE (session_id, place_id)
    );

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
    (
        2,
        'Чебурашка 2',
        'Второй фильм про Чебурашку',
        90
    ),
    (
        3,
        'Чебурашка 3',
        'Третий фильм про Чебурашку',
        90
    );

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

SELECT
    m.title AS movie,
    SUM(p.price) AS "revenue"
FROM
    movies m
    JOIN sessions s ON m.id = s.movie_id
    JOIN tickets t ON s.id = t.session_id
    JOIN prices p ON s.id = p.session_id
    AND p.place_type = (
        SELECT
            pl.place_type
        FROM
            places pl
        WHERE
            pl.id = t.place_id
    )
GROUP BY
    m.id,
    m.title
ORDER BY
    "revenue" DESC
LIMIT
    1;