CREATE TABLE IF NOT EXISTS rooms (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS movies (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    price NUMERIC(8,2) NOT NULL,
    duration_in_seconds INT NOT NULL
);

CREATE TABLE IF NOT EXISTS seanses (
    id SERIAL PRIMARY KEY,
    room_id INT NOT NULL REFERENCES rooms(id) ON DELETE CASCADE,
    movie_id INT NOT NULL REFERENCES movies(id) ON DELETE CASCADE,
    date DATE NOT NULL,
    time TIME NOT NULL,
    CONSTRAINT unique_room_datetime UNIQUE (room_id, date, time)
);

CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    seans_id INT NOT NULL REFERENCES seanses(id) ON DELETE CASCADE,
    price NUMERIC(8,2) NOT NULL,
    ticket_number VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);