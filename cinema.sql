-- PostgreSQL version of cinema database

DROP TABLE IF EXISTS prices CASCADE;
DROP TABLE IF EXISTS tickets CASCADE;
DROP TABLE IF EXISTS cinema_hall_seats CASCADE;
DROP TABLE IF EXISTS clients CASCADE;
DROP TABLE IF EXISTS screenings CASCADE;
DROP TABLE IF EXISTS cinema_halls CASCADE;
DROP TABLE IF EXISTS cinemas CASCADE;
DROP TABLE IF EXISTS films CASCADE;
DROP TABLE IF EXISTS seat_types CASCADE;

CREATE TABLE cinemas
(
    id   SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE cinema_halls
(
    id        SERIAL PRIMARY KEY,
    cinema_id INT          NOT NULL,
    name      VARCHAR(255) NOT NULL,
    CONSTRAINT fk_cinema_halls_cinema_id
        FOREIGN KEY (cinema_id) REFERENCES cinemas (id)
            ON DELETE CASCADE
);

CREATE TABLE seat_types
(
    id   SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL -- тип места (кресло, диван, шезлонг)
);

CREATE TABLE cinema_hall_seats
(
    id          SERIAL PRIMARY KEY,
    hall_id     INT NOT NULL,
    type_id     INT NOT NULL,
    seat_row    INT NOT NULL,
    seat_number INT NOT NULL,
    CONSTRAINT fk_cinema_hall_seats_hall_id
        FOREIGN KEY (hall_id) REFERENCES cinema_halls (id)
            ON DELETE CASCADE,
    CONSTRAINT fk_cinema_hall_seats_type_id
        FOREIGN KEY (type_id) REFERENCES seat_types (id)
            ON DELETE RESTRICT,
    CONSTRAINT unique_hall_id_seat_row_seat_number
        UNIQUE (hall_id, seat_row, seat_number)
);

CREATE TABLE films
(
    id       SERIAL PRIMARY KEY,
    name     VARCHAR(255) NOT NULL,
    duration INT          NOT NULL -- продолжительность в секундах
);

CREATE TABLE screenings
(
    id             SERIAL PRIMARY KEY,
    film_id        INT       NOT NULL,
    start_time     TIMESTAMP NOT NULL,
    cinema_hall_id INT       NOT NULL,
    CONSTRAINT fk_screenings_film_id
        FOREIGN KEY (film_id) REFERENCES films (id)
            ON DELETE RESTRICT,
    CONSTRAINT fk_screenings_cinema_hall_id
        FOREIGN KEY (cinema_hall_id) REFERENCES cinema_halls (id)
            ON DELETE RESTRICT
);

CREATE TABLE clients
(
    id    SERIAL PRIMARY KEY,
    name  VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL UNIQUE
);

CREATE TYPE ticket_status AS ENUM ('reserved', 'paid', 'returned');

CREATE TABLE tickets
(
    id                  SERIAL PRIMARY KEY,
    client_id           INT           NOT NULL,
    screening_id        INT           NOT NULL,
    cinema_hall_seat_id INT           NOT NULL,
    price               INT           NOT NULL,
    status              ticket_status DEFAULT 'paid',
    CONSTRAINT fk_tickets_screening_id
        FOREIGN KEY (screening_id) REFERENCES screenings (id)
            ON DELETE RESTRICT,
    CONSTRAINT fk_cinema_hall_seats_cinema_hall_seat_id
        FOREIGN KEY (cinema_hall_seat_id) REFERENCES cinema_hall_seats (id)
            ON DELETE RESTRICT,
    CONSTRAINT fk_cinema_hall_seats_client_id
        FOREIGN KEY (client_id) REFERENCES clients (id)
            ON DELETE RESTRICT,
    CONSTRAINT unique_screening_id_cinema_hall_seat_id
        UNIQUE (screening_id, cinema_hall_seat_id)
);

CREATE TABLE prices
(
    id             SERIAL PRIMARY KEY,
    value          INT NOT NULL,
    film_id        INT NOT NULL,
    seat_type_id   INT NOT NULL,
    cinema_hall_id INT NOT NULL,
    CONSTRAINT fk_prices_film_id
        FOREIGN KEY (film_id) REFERENCES films (id)
            ON DELETE CASCADE,
    CONSTRAINT fk_prices_seat_type_id
        FOREIGN KEY (seat_type_id) REFERENCES seat_types (id)
            ON DELETE CASCADE,
    CONSTRAINT fk_prices_cinema_hall_id
        FOREIGN KEY (cinema_hall_id) REFERENCES cinema_halls (id)
            ON DELETE CASCADE,
    CONSTRAINT unique_film_id_seat_type_id_cinema_hall_id
        UNIQUE (film_id, seat_type_id, cinema_hall_id)
);


INSERT INTO cinemas (id, name)
VALUES (1, 'Кинотеатр Центральный');

INSERT INTO seat_types (id, name)
VALUES (1, 'Стандарт'),
       (2, 'VIP');

INSERT INTO films (id, name, duration)
VALUES (1, 'Интерстеллар', 12000),
       (2, 'Начало', 10000),
       (3, 'Дюна', 11000);

INSERT INTO cinema_halls (id, cinema_id, name)
VALUES (1, 1, 'Зал №1'),
       (2, 1, 'Зал №2');

INSERT INTO cinema_hall_seats (id, hall_id, type_id, seat_number, seat_row)
VALUES
-- Зал №1
(1, 1, 1, 1, 1),
(2, 1, 1, 2, 1),
(3, 1, 1, 3, 1),

-- Зал №2
(4, 2, 2, 1, 1),
(5, 2, 2, 2, 1),
(6, 2, 2, 3, 1);

INSERT INTO screenings (id, film_id, start_time, cinema_hall_id)
VALUES (1, 1, '2026-03-08 13:00:00', 1),
       (2, 2, '2026-03-08 16:00:00', 2);

INSERT INTO prices (id, value, film_id, seat_type_id, cinema_hall_id)
VALUES (1, 500, 1, 1, 1),
       (2, 800, 1, 2, 1),
       (3, 600, 2, 1, 2),
       (4, 900, 2, 2, 2);

INSERT INTO clients (id, name, email)
VALUES (1, 'Петр Иванов', 'petya@mail.ru'),
       (2, 'Иван Иванов', 'vanya@mail.ru'),
       (3, 'Семен Васильев', 'semen@mail.ru');

INSERT INTO tickets (id, client_id, screening_id, cinema_hall_seat_id, price)
VALUES (1, 1, 1, 1, 200),
       (2, 2, 1, 2, 300),
       (3, 3, 2, 4, 900);

-- Индекс для ускорения поиска самого прибыльного фильма
CREATE INDEX idx_tickets_screening_status ON tickets (screening_id, status);

-- Запрос для поиска самого прибыльного фильма
SELECT f.id, f.name, SUM(t.price) as total_film_cash
FROM films f
         INNER JOIN screenings s ON f.id = s.film_id
         INNER JOIN tickets t ON t.screening_id = s.id
WHERE t.status = 'paid'
GROUP BY f.id
ORDER BY total_film_cash DESC
LIMIT 1;
