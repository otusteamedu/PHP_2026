DROP TABLE IF EXISTS prices CASCADE;
DROP TABLE IF EXISTS tickets CASCADE;
DROP TABLE IF EXISTS cinema_hall_seats CASCADE;
DROP TABLE IF EXISTS clients CASCADE;
DROP TABLE IF EXISTS screenings CASCADE;
DROP TABLE IF EXISTS cinema_halls CASCADE;
DROP TABLE IF EXISTS cinemas CASCADE;
DROP TABLE IF EXISTS films CASCADE;
DROP TABLE IF EXISTS seat_types CASCADE;
DROP TYPE IF EXISTS ticket_status;

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
    cinema_hall_id INT       NOT NULL
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
    paid_at             TIMESTAMP     NOT NULL, -- время оплаты
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
    CONSTRAINT unique_film_id_seat_type_id_cinema_hall_id
        UNIQUE (film_id, seat_type_id, cinema_hall_id)
);
