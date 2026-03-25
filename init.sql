-- DDL for cinema management system
DROP SCHEMA IF EXISTS cinema CASCADE;
CREATE SCHEMA cinema;
SET search_path TO cinema;
CREATE EXTENSION IF NOT EXISTS btree_gist; -- Для GiST индексов, если потребуются диапазоны времени

CREATE TABLE movies (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    duration_minutes INT NOT NULL CHECK (duration_minutes > 0),
    release_date DATE
);

CREATE TABLE cinemas (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL
);

CREATE TABLE halls (
    id SERIAL PRIMARY KEY,
    cinema_id INT NOT NULL REFERENCES cinemas(id) ON DELETE CASCADE,
    name VARCHAR(50) NOT NULL,
    capacity INT NOT NULL
);

-- Seats Blueprint: defines the layout and type (e.g., standard, VIP)
CREATE TABLE seats_blueprint (
    id SERIAL PRIMARY KEY,
    hall_id INT NOT NULL REFERENCES halls(id) ON DELETE CASCADE,
    seat_row VARCHAR(5) NOT NULL,
    seat_number INT NOT NULL,
    seat_type VARCHAR(50) NOT NULL DEFAULT 'Standard',
    UNIQUE (hall_id, seat_row, seat_number)
);

CREATE TABLE screenings (
    id SERIAL PRIMARY KEY,
    movie_id INT NOT NULL REFERENCES movies(id) ON DELETE RESTRICT,
    hall_id INT NOT NULL REFERENCES halls(id) ON DELETE RESTRICT,
    start_time TIMESTAMP WITH TIME ZONE NOT NULL,
    -- Добавляем колонку для хранения времени окончания, чтобы индекс работал быстро
    end_time TIMESTAMP WITH TIME ZONE NOT NULL,
    base_price NUMERIC(10, 2) NOT NULL CHECK (base_price >= 0),
    -- Теперь индекс использует только поля самой таблицы
    EXCLUDE USING gist (hall_id WITH =, tstzrange(start_time, end_time) WITH &&),
    -- Проверка, что сеанс не заканчивается раньше, чем начался
    CONSTRAINT check_times CHECK (end_time > start_time)
);

-- Flexible pricing based on screening and seat type
CREATE TABLE ticket_prices (
    id SERIAL PRIMARY KEY,
    screening_id INT NOT NULL REFERENCES screenings(id) ON DELETE CASCADE,
    seat_type VARCHAR(50) NOT NULL,
    price_modifier NUMERIC(5, 2) NOT NULL DEFAULT 1.0 -- e.g., 1.5 for VIP
);

CREATE TABLE customers (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE bookings (
    id SERIAL PRIMARY KEY,
    customer_id INT NOT NULL REFERENCES customers(id) ON DELETE RESTRICT,
    booking_time TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total_amount NUMERIC(10, 2) NOT NULL
);

CREATE TABLE tickets (
    id SERIAL PRIMARY KEY,
    booking_id INT NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
    screening_id INT NOT NULL REFERENCES screenings(id) ON DELETE RESTRICT,
    seat_blueprint_id INT NOT NULL REFERENCES seats_blueprint(id) ON DELETE RESTRICT,
    actual_price NUMERIC(10, 2) NOT NULL,
    -- Ensure a seat is only booked once per screening
    UNIQUE (screening_id, seat_blueprint_id) 
);

-- TEST DATA: Populating Cinema System
SET search_path TO cinema;

-- 1. Movies
INSERT INTO movies (title, duration_minutes, release_date) VALUES 
('Inception', 148, '2010-07-16'),
('Interstellar', 169, '2014-11-07');

-- 2. Cinemas & Halls
INSERT INTO cinemas (name, city) VALUES ('Galaxy Cinema', 'Nizhny Novgorod');
INSERT INTO halls (cinema_id, name, capacity) VALUES (1, 'Main Hall', 100), (1, 'VIP Hall', 20);

-- 3. Seats Blueprint (Rows and Numbers)
INSERT INTO seats_blueprint (hall_id, seat_row, seat_number, seat_type) VALUES 
(1, 'A', 1, 'Standard'), (1, 'A', 2, 'Standard'),
(2, 'VIP', 1, 'VIP'), (2, 'VIP', 2, 'VIP');

-- 4. Screenings (разнесли по времени, чтобы не было конфликтов)
INSERT INTO cinema.screenings (movie_id, hall_id, start_time, end_time, base_price) VALUES 
(1, 1, '2026-03-02 10:00:00+03', '2026-03-02 12:30:00+03', 500.00), -- Утро
(1, 2, '2026-03-02 21:00:00+03', '2026-03-02 23:30:00+03', 1500.00), -- Другой зал (VIP)
(2, 1, '2026-03-02 19:00:00+03', '2026-03-02 21:50:00+03', 400.00);  -- Вечер

-- 5. Customers
INSERT INTO customers (name, email) VALUES 
('Evgeny', 'evgeny@example.com'),
('Architect', 'architect@example.com');

-- 6. Bookings & Tickets
-- Booking 1: Inception VIP
INSERT INTO bookings (customer_id, total_amount) VALUES (1, 1500.00);
INSERT INTO tickets (booking_id, screening_id, seat_blueprint_id, actual_price) 
VALUES (1, 2, 3, 1500.00);

-- Booking 2: Interstellar Standard
INSERT INTO bookings (customer_id, total_amount) VALUES (2, 400.00);
INSERT INTO tickets (booking_id, screening_id, seat_blueprint_id, actual_price) 
VALUES (2, 3, 1, 400.00);

-- =========================================================================
-- ДЗ №8: ФИНАЛЬНАЯ ТИПИЗИРОВАННАЯ EAV-МОДЕЛЬ (БЕЗ ПОТЕРИ ТОЧНОСТИ)
-- =========================================================================

-- 1. Таблица ТИПОВ атрибутов
CREATE TABLE IF NOT EXISTS cinema.attribute_types (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    type_name VARCHAR(50) NOT NULL UNIQUE 
);

INSERT INTO cinema.attribute_types (type_name) VALUES 
('текст'), ('логика'), ('дата'), ('число') 
ON CONFLICT DO NOTHING;

-- 2. Таблица АТРИБУТОВ
CREATE TABLE IF NOT EXISTS cinema.attributes (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    type_id INTEGER REFERENCES cinema.attribute_types(id)
);

INSERT INTO cinema.attributes (name, type_id) VALUES 
('рецензия критиков', 1),
('оскар', 2),
('мировая премьера', 3),
('дата начала продажи билетов', 3),
('IMDb Rating', 4)
ON CONFLICT DO NOTHING;

-- 3. ТИПИЗИРОВАННАЯ ТАБЛИЦА ЗНАЧЕНИЙ (Решение проблемы TEXT)
CREATE TABLE IF NOT EXISTS cinema.movie_attribute_values (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    movie_id INTEGER REFERENCES cinema.movies(id) ON DELETE CASCADE,
    attr_id INTEGER REFERENCES cinema.attributes(id) ON DELETE CASCADE,
    
    -- Раздельные колонки для сохранения точности и типов
    val_text    TEXT,
    val_boolean BOOLEAN,
    val_date    DATE,
    val_int     INTEGER,
    val_float   DOUBLE PRECISION,
    
    -- Проверка: заполнено ровно одно поле
    CONSTRAINT check_single_value CHECK (
        (val_text IS NOT NULL)::int + 
        (val_boolean IS NOT NULL)::int + 
        (val_date IS NOT NULL)::int + 
        (val_int IS NOT NULL)::int + 
        (val_float IS NOT NULL)::int = 1
    ),
    UNIQUE (movie_id, attr_id)
);

CREATE INDEX IF NOT EXISTS idx_eav_movie ON cinema.movie_attribute_values(movie_id);
CREATE INDEX IF NOT EXISTS idx_eav_attr ON cinema.movie_attribute_values(attr_id);

-- 4. Наполнение данными (Inception)
INSERT INTO cinema.movie_attribute_values (movie_id, attr_id, val_text, val_boolean, val_date, val_int, val_float)
SELECT 
    m.id, 
    a.id,
    CASE WHEN a.name = 'рецензия критиков' THEN 'Шедевр визуализации' END,
    CASE WHEN a.name = 'оскар' THEN TRUE END,
    CASE WHEN a.name = 'мировая премьера' THEN '2026-03-23'::DATE END,
    NULL,
    CASE WHEN a.name = 'IMDb Rating' THEN 8.8000 END
FROM cinema.movies m, cinema.attributes a 
WHERE m.title = 'Inception' 
  AND a.name IN ('рецензия критиков', 'оскар', 'мировая премьера', 'IMDb Rating');

-- 5. VIEW для МАРКЕТИНГА (универсальное отображение через COALESCE)
CREATE OR REPLACE VIEW cinema.v_marketing_data AS
SELECT 
    m.title as фильм,
    t.type_name as тип_атрибута,
    a.name as атрибут,
    COALESCE(
        val_text, 
        val_boolean::text, 
        val_date::text, 
        val_int::text, 
        val_float::text
    ) as значение
FROM cinema.movies m
JOIN cinema.movie_attribute_values av ON m.id = av.movie_id
JOIN cinema.attributes a ON av.attr_id = a.id
JOIN cinema.attribute_types t ON a.type_id = t.id;

-- 6. VIEW для СЛУЖЕБНЫХ ДАННЫХ (используем родные типы данных, а не TEXT)
CREATE OR REPLACE VIEW cinema.v_service_tasks AS
SELECT 
    m.title as фильм,
    MAX(CASE WHEN a.name = 'мировая премьера' AND av.val_date <= CURRENT_DATE THEN 'Актуально сегодня' END) as задачи_на_сегодня,
    MAX(CASE WHEN a.name = 'дата начала продажи билетов' AND av.val_date <= (CURRENT_DATE + INTERVAL '20 days') THEN 'Старт через 20 дней' END) as через_20_дней
FROM cinema.movies m
JOIN cinema.movie_attribute_values av ON m.id = av.movie_id
JOIN cinema.attributes a ON av.attr_id = a.id
GROUP BY m.title;
