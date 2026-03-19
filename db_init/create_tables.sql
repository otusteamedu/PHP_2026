CREATE TABLE IF NOT EXISTS rooms (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS seats (
     id SERIAL PRIMARY KEY,
     room_id INT NOT NULL REFERENCES rooms(id) ON DELETE CASCADE,
     position_x INT NOT NULL,
     position_y INT NOT NULL,
     CONSTRAINT unique_room_positions UNIQUE (room_id, position_x, position_y)
);

CREATE TABLE IF NOT EXISTS movies (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    duration_in_seconds INT NOT NULL
);

CREATE TABLE IF NOT EXISTS seanses (
    id SERIAL PRIMARY KEY,
    room_id INT NOT NULL REFERENCES rooms(id) ON DELETE CASCADE,
    movie_id INT NOT NULL REFERENCES movies(id) ON DELETE CASCADE,
    price NUMERIC(8,2) NOT NULL,
    start_at TIMESTAMP NOT NULL,
    CONSTRAINT unique_room_datetime UNIQUE (room_id, start_at)
);

CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    seanse_id INT NOT NULL REFERENCES seanses(id) ON DELETE CASCADE,
    seat_id INT NOT NULL REFERENCES seats(id) ON DELETE CASCADE,
    price NUMERIC(8,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    ticket_number VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    CONSTRAINT unique_seat_per_seans UNIQUE (seanse_id, seat_id)
);