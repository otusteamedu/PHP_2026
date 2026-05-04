CREATE TABLE IF NOT EXISTS cinema (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    url VARCHAR(255) NOT NULL,
    UNIQUE (title, address)
);
COMMENT ON TABLE cinema IS 'Кинотеатр. Основная информация';

CREATE TABLE IF NOT EXISTS phones (
    id BIGSERIAL PRIMARY KEY,
    cinema_id BIGINT NOT NULL,
    number VARCHAR(15) NOT NULL,
    CONSTRAINT phones_cinema_fk FOREIGN KEY (cinema_id)
        REFERENCES cinema(id) ON DELETE CASCADE
);
COMMENT ON TABLE phones IS 'Телефоны';

CREATE TABLE IF NOT EXISTS halls (
    id BIGSERIAL PRIMARY KEY,
    cinema_id BIGINT NOT NULL,
    title VARCHAR(100) NOT NULL,
    open_time TIME NOT NULL,
    CONSTRAINT halls_cinema_fk FOREIGN KEY (cinema_id)
        REFERENCES cinema(id) ON DELETE CASCADE,
    UNIQUE (title, cinema_id)
);
COMMENT ON TABLE halls IS 'Кинотеатр. Залы';

CREATE TABLE IF NOT EXISTS films (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL UNIQUE,
    duration SMALLINT NOT NULL CHECK (duration > 0),
    base_price DECIMAL(10, 2) NOT NULL CHECK (base_price >= 0)
);
COMMENT ON TABLE films IS 'Фильмы';
COMMENT ON COLUMN films.duration IS 'Duration in minutes';

CREATE TABLE IF NOT EXISTS schedule (
    id BIGSERIAL PRIMARY KEY,
    hall_id BIGINT NOT NULL,
    film_id BIGINT NOT NULL,
    date DATE NOT NULL,
    time TIME,
    end_time TIME NOT NULL,
    time_factor DECIMAL(4, 2) NOT NULL DEFAULT 1,
    CONSTRAINT schedule_halls_fk FOREIGN KEY (hall_id)
        REFERENCES halls(id) ON DELETE CASCADE,
    CONSTRAINT schedule_films_fk FOREIGN KEY (film_id)
        REFERENCES films(id) ON DELETE CASCADE
);
COMMENT ON TABLE schedule IS 'Расписание';
COMMENT ON COLUMN schedule.time_factor IS 'Коэффициент по времени дня';

CREATE TABLE IF NOT EXISTS seat_maps (
    id BIGSERIAL PRIMARY KEY,
    hall_id BIGINT NOT NULL UNIQUE,
    image VARCHAR(100) NOT NULL,
    CONSTRAINT maps_halls_fk FOREIGN KEY (hall_id)
        REFERENCES halls(id) ON DELETE CASCADE
);
COMMENT ON TABLE seat_maps IS 'Карта зала';
COMMENT ON COLUMN seat_maps.image IS 'Map name/version';

CREATE TYPE seat_type_enum AS ENUM ('regular', 'vip', 'love seat');

CREATE TABLE IF NOT EXISTS seats (
    id BIGSERIAL PRIMARY KEY,
    map_id BIGINT NOT NULL,
    row_number SMALLINT NOT NULL,
    seat_number SMALLINT NOT NULL,
    price_multiplier DECIMAL(3, 2) DEFAULT 1.00,
    seat_type seat_type_enum DEFAULT 'regular',
    CONSTRAINT seats_maps_fk FOREIGN KEY (map_id)
        REFERENCES seat_maps(id) ON DELETE CASCADE,
    CONSTRAINT unique_seat UNIQUE (map_id, row_number, seat_number)
);
COMMENT ON TABLE seats IS 'Список мест';
COMMENT ON COLUMN seats.price_multiplier IS 'Multiplier for base price';

CREATE TABLE IF NOT EXISTS tickets (
    id BIGSERIAL PRIMARY KEY,
    schedule_id BIGINT NOT NULL,
    seat_id BIGINT NOT NULL,
    real_price DECIMAL(10, 2) NOT NULL DEFAULT 1,
    CONSTRAINT ticket_schedule_fk FOREIGN KEY (schedule_id)
        REFERENCES schedule(id) ON DELETE CASCADE,
    CONSTRAINT ticket_seats_fk FOREIGN KEY (seat_id)
        REFERENCES seats(id) ON DELETE CASCADE
);
COMMENT ON TABLE tickets IS 'Билеты';
COMMENT ON COLUMN tickets.real_price IS 'Фактическая цена продажи';
