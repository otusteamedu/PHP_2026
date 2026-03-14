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

CREATE INDEX idx_hall_cinema_id ON hall(cinema_id);
CREATE INDEX idx_showtime_hall_id ON showtime(hall_id);
CREATE INDEX idx_showtime_movie_id ON showtime(movie_id);
CREATE INDEX idx_ticket_showtime_id ON ticket(showtime_id);
CREATE INDEX idx_ticket_customer_id ON ticket(customer_id);

CREATE OR REPLACE FUNCTION check_ticket_seat_in_hall()
RETURNS TRIGGER AS $$
DECLARE
    v_rows_count    INT;
    v_seats_per_row INT;
BEGIN
    SELECT h.rows_count, h.seats_per_row
      INTO v_rows_count, v_seats_per_row
      FROM showtime s
      JOIN hall h ON h.id = s.hall_id
     WHERE s.id = NEW.showtime_id;

    IF NEW.row_num < 1 OR NEW.row_num > v_rows_count THEN
        RAISE EXCEPTION 'row_num % выходит за пределы зала (1..%)', NEW.row_num, v_rows_count;
    END IF;

    IF NEW.seat_number < 1 OR NEW.seat_number > v_seats_per_row THEN
        RAISE EXCEPTION 'seat_number % выходит за пределы ряда (1..%)', NEW.seat_number, v_seats_per_row;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_ticket_seat_in_hall
BEFORE INSERT OR UPDATE ON ticket
FOR EACH ROW EXECUTE FUNCTION check_ticket_seat_in_hall();