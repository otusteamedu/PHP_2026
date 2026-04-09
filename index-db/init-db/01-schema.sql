DROP SCHEMA IF EXISTS cinema CASCADE;
CREATE SCHEMA cinema;
SET search_path TO cinema;

CREATE EXTENSION IF NOT EXISTS btree_gist;

CREATE TABLE cinemas (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    city VARCHAR(120) NOT NULL,
    address VARCHAR(255) NOT NULL
);

CREATE TABLE halls (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    cinema_id BIGINT NOT NULL REFERENCES cinemas(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    UNIQUE (cinema_id, name)
);

CREATE TABLE seat_types (
    id SMALLINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    type VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE seats (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    hall_id BIGINT NOT NULL REFERENCES halls(id) ON DELETE CASCADE,
    seat_type_id SMALLINT NOT NULL REFERENCES seat_types(id) ON DELETE RESTRICT,
    row_code VARCHAR(20) NOT NULL,
    seat_number INT NOT NULL CHECK (seat_number > 0),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    UNIQUE (hall_id, row_code, seat_number),
    UNIQUE (id, hall_id)
);

CREATE TABLE movies (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    original_title VARCHAR(255),
    duration SMALLINT NOT NULL CHECK (duration > 0),
    release_date DATE,
    age_rating VARCHAR(10)
);

CREATE TABLE screenings (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    hall_id BIGINT NOT NULL REFERENCES halls(id) ON DELETE RESTRICT,
    movie_id BIGINT NOT NULL REFERENCES movies(id) ON DELETE RESTRICT,
    starts_at TIMESTAMPTZ NOT NULL,
    ends_at TIMESTAMPTZ NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'scheduled'
        CHECK (status IN ('scheduled', 'cancelled', 'finished')),
    CHECK (ends_at > starts_at),
    UNIQUE (id, hall_id)
);

CREATE INDEX idx_screenings_movie_id ON screenings(movie_id);

ALTER TABLE screenings
    ADD CONSTRAINT no_overlapping_screenings
    EXCLUDE USING gist (
        hall_id WITH =,
        tstzrange(starts_at, ends_at, '[)') WITH &&
    );

CREATE TABLE screening_prices (
    screening_id BIGINT NOT NULL REFERENCES screenings(id) ON DELETE CASCADE,
    seat_type_id SMALLINT NOT NULL REFERENCES seat_types(id) ON DELETE RESTRICT,
    price_amount NUMERIC(10,2) NOT NULL CHECK (price_amount >= 0),
    PRIMARY KEY (screening_id, seat_type_id)
);

CREATE TABLE customers (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(30)
);

CREATE TABLE orders (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    customer_id BIGINT REFERENCES customers(id) ON DELETE SET NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'reserved'
        CHECK (status IN ('reserved', 'paid', 'cancelled', 'refunded')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    paid_at TIMESTAMPTZ   
);

CREATE TABLE tickets (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    order_id BIGINT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    screening_id BIGINT NOT NULL,
    seat_id BIGINT NOT NULL,
    hall_id BIGINT NOT NULL,
    price_amount NUMERIC(10,2) NOT NULL CHECK (price_amount >= 0),
    status VARCHAR(20) NOT NULL DEFAULT 'reserved'
        CHECK (status IN ('reserved', 'paid', 'cancelled', 'refunded')),    
   
    UNIQUE (screening_id, seat_id),    
 
    FOREIGN KEY (screening_id, hall_id) REFERENCES screenings (id, hall_id) ON DELETE RESTRICT,
    FOREIGN KEY (seat_id, hall_id) REFERENCES seats (id, hall_id) ON DELETE RESTRICT
);

CREATE INDEX idx_tickets_screening_status ON tickets(screening_id, status);
CREATE INDEX idx_tickets_order_id ON tickets(order_id);