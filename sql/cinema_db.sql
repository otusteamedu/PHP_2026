DROP TABLE IF EXISTS ticket CASCADE;
DROP TABLE IF EXISTS showtime CASCADE;
DROP TABLE IF EXISTS customer CASCADE;
DROP TABLE IF EXISTS hall CASCADE;
DROP TABLE IF EXISTS movie CASCADE;
DROP TABLE IF EXISTS cinema CASCADE;

CREATE TABLE cinema (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL
);

CREATE TABLE hall (
    id SERIAL PRIMARY KEY,
    cinema_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    rows_count INT NOT NULL,
    seats_per_row INT NOT NULL,
    CONSTRAINT fk_hall_cinema
        FOREIGN KEY (cinema_id) REFERENCES cinema(id) ON DELETE CASCADE
);

CREATE TABLE movie (
    id SERIAL PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    duration_minutes INT NOT NULL,
    rating VARCHAR(10),
    price DECIMAL(8,2) NOT NULL
);

CREATE TABLE showtime (
    id SERIAL PRIMARY KEY,
    hall_id INT NOT NULL,
    movie_id INT NOT NULL,
    start_time TIMESTAMP NOT NULL,
    CONSTRAINT fk_showtime_hall
        FOREIGN KEY (hall_id) REFERENCES hall(id) ON DELETE CASCADE,
    CONSTRAINT fk_showtime_movie
        FOREIGN KEY (movie_id) REFERENCES movie(id) ON DELETE RESTRICT
);

CREATE TABLE customer (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20)
);

CREATE TABLE ticket (
    id SERIAL PRIMARY KEY,
    showtime_id INT NOT NULL,
    customer_id INT NOT NULL,
    row_num INT NOT NULL,
    seat_number INT NOT NULL,
    price DECIMAL(8,2) NOT NULL,
    CONSTRAINT fk_ticket_showtime
        FOREIGN KEY (showtime_id)
            REFERENCES showtime(id)
            ON DELETE CASCADE,
    CONSTRAINT fk_ticket_customer
        FOREIGN KEY (customer_id)
            REFERENCES customer(id)
            ON DELETE CASCADE,
    CONSTRAINT uq_ticket_seat
        UNIQUE (showtime_id, row_num, seat_number)
);