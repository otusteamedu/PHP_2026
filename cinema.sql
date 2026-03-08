CREATE DATABASE IF NOT EXISTS cinema;

USE cinema;

DROP TABLE IF EXISTS prices;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS cinema_hall_seats;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS screenings;
DROP TABLE IF EXISTS cinema_halls;
DROP TABLE IF EXISTS cinemas;
DROP TABLE IF EXISTS films;
DROP TABLE IF EXISTS seat_types;

CREATE TABLE cinemas
(
    id   int auto_increment primary key,
    name varchar(255) not null
);

CREATE TABLE cinema_halls
(
    id        int auto_increment primary key,
    cinema_id int          not null,
    name      varchar(255) not null,
    constraint `fk-cinema_halls-cinema_id`
        foreign key (cinema_id) references cinemas (id)
            on delete cascade
);

CREATE TABLE seat_types
(
    id   int auto_increment primary key,
    name varchar(255) not null comment 'тип места (кресло, диван, шезлонг)'
);

CREATE TABLE cinema_hall_seats
(
    id      int auto_increment primary key,
    hall_id int not null,
    type_id int not null,
    `row`   int not null,
    number  int not null,
    constraint `fk-cinema_hall_seats-hall_id`
        foreign key (hall_id) references cinema_halls (id)
            on delete cascade,
    constraint `fk-cinema_hall_seats-type_id`
        foreign key (type_id) references seat_types (id)
            on delete restrict,
    constraint `unique-hall_id-row-number`
        unique (hall_id, `row`, number)
);

CREATE TABLE films
(
    id       int auto_increment primary key,
    name     varchar(255) not null,
    duration int not null comment 'продолжительность в секундах'
);

CREATE TABLE screenings
(
    id             int auto_increment primary key,
    film_id        int not null,
    start_time     timestamp not null,
    cinema_hall_id int not null,
    constraint `fk-screenings-film_id`
        foreign key (film_id) references films (id)
            on delete restrict,
    constraint `fk-screenings-cinema_hall_id`
        foreign key (cinema_hall_id) references cinema_halls (id)
            on delete restrict
);

CREATE TABLE clients
(
    id    int auto_increment primary key,
    name  varchar(255) not null,
    email varchar(255) null unique
);

CREATE TABLE tickets
(
    id                  int auto_increment primary key,
    client_id           int not null,
    screening_id        int not null,
    cinema_hall_seat_id int not null,
    price               int not null,
    status              enum('reserved', 'paid', 'returned') default 'paid',
    constraint `fk-tickets-screening_id`
        foreign key (screening_id) references screenings (id)
            on delete restrict,
    constraint `fk-cinema_hall_seats-cinema_hall_seat_id`
        foreign key (cinema_hall_seat_id) references cinema_hall_seats (id)
            on delete restrict,
    constraint `fk-cinema_hall_seats-client_id`
        foreign key (client_id) references clients (id)
            on delete restrict,
    constraint `unique-screening_id-cinema_hall_seat_id`
        unique (screening_id, cinema_hall_seat_id)
);

CREATE TABLE prices
(
    id           int auto_increment primary key,
    value        int not null,
    film_id      int not null,
    seat_type_id int not null,
    constraint `fk-prices-film_id`
        foreign key (film_id) references films (id)
            on delete cascade,
    constraint `fk-prices-seat_type_id`
        foreign key (seat_type_id) references seat_types (id)
            on delete cascade,
    constraint `unique-film_id-seat_type_id`
        unique (film_id, seat_type_id)
);


INSERT INTO cinemas (id, name)
VALUES (1, 'Кинотеатр Центральный');

INSERT INTO seat_types (id, name)
VALUES (1, 'Стандарт'),
       (2, 'VIP');

INSERT INTO films (id, name, duration)
VALUES (1, 'Интерстеллар', 200),
       (2, 'Начало', 180),
       (3, 'Дюна', 210);

INSERT INTO cinema_halls (id, cinema_id, name)
VALUES (1, 1, 'Зал №1'),
       (2, 1, 'Зал №2');

INSERT INTO cinema_hall_seats (id, hall_id, type_id, number, `row`)
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

INSERT INTO prices (id, value, film_id, seat_type_id)
VALUES (1, 500, 1, 1),
       (2, 800, 1, 2),
       (3, 600, 2, 1),
       (4, 900, 2, 2);

INSERT INTO clients (id, name, email)
VALUES (1, 'Петр Иванов', 'petya@mail.ru'),
       (2, 'Иван Иванов', 'vanya@mail.ru'),
       (3, 'Семен Васильев', 'semen@mail.ru');

INSERT INTO tickets (id, client_id, screening_id, cinema_hall_seat_id, price)
VALUES (1, 1,1, 1, 200),
       (2, 2, 1, 2, 300),
       (3, 3, 2, 4, 900);

-- Индекс для ускорения поиска самого прибыльного фильма
CREATE INDEX idx_tickets_screening_status ON tickets(screening_id, status);

-- Запрос для поиска самого прибыльного фильма
SELECT f.id, f.name, SUM(t.price) as total_film_cash
FROM films f
         INNER JOIN screenings s ON f.id = s.film_id
         INNER JOIN tickets t ON t.screening_id = s.id
WHERE t.status = 'paid'
GROUP BY f.id
ORDER BY total_film_cash DESC
LIMIT 1
