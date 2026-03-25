SET
    search_path TO otus;
    
CREATE TABLE
    IF NOT EXISTS cinemas (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        address VARCHAR(255) NOT NULL
    );

CREATE TABLE
    IF NOT EXISTS halls (
        id SERIAL PRIMARY KEY,
        cinema_id INT NOT NULL REFERENCES cinemas (id) ON DELETE CASCADE,
        number INT NOT NULL
    );

CREATE TABLE
    IF NOT EXISTS places (
        id SERIAL PRIMARY KEY,
        hall_id INT NOT NULL REFERENCES halls (id) ON DELETE CASCADE,
        row_num INT NOT NULL,
        place_num INT NOT NULL,
        place_type VARCHAR(15) NOT NULL,
        UNIQUE (hall_id, row_num, place_num)
    );

CREATE TABLE
    IF NOT EXISTS movies (
        id SERIAL PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description VARCHAR(255),
        duration INT NOT NULL CHECK (duration > 0)
    );

CREATE TABLE
    IF NOT EXISTS attribute_types (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        code VARCHAR(50) NOT NULL UNIQUE,
        value_type VARCHAR(20) NOT NULL CHECK (
            value_type IN ('text', 'boolean', 'date', 'int', 'float')
        )
    );

CREATE TABLE
    IF NOT EXISTS attributes (
        id SERIAL PRIMARY KEY,
        attribute_type_id INT NOT NULL REFERENCES attribute_types (id) ON DELETE CASCADE,
        name VARCHAR(150) NOT NULL,
        UNIQUE (attribute_type_id, name)
    );
CREATE INDEX IF NOT EXISTS idx_eav_attribute_type ON attributes(attribute_type_id);

CREATE TABLE
    IF NOT EXISTS attribute_values (
        id SERIAL PRIMARY KEY,
        movie_id INT NOT NULL REFERENCES movies (id) ON DELETE CASCADE,
        attribute_id INT NOT NULL REFERENCES attributes (id) ON DELETE CASCADE,
        value_text TEXT,
        value_boolean BOOLEAN,
        value_date DATE,
		value_float NUMERIC(10,2),
		value_int INTEGER,
        UNIQUE (movie_id, attribute_id),
        CHECK (
            (value_text IS NOT NULL AND value_boolean IS NULL AND value_date IS null AND value_float is NULL AND value_int IS NULL) OR
            (value_text IS NULL AND value_boolean IS NOT NULL AND value_date IS NULL AND value_float is NULL AND value_int IS NULL) OR
            (value_text IS NULL AND value_boolean IS NULL AND value_date IS NOT NULL AND value_float is NULL AND value_int IS NULL) OR
            (value_text IS NULL AND value_boolean IS NULL AND value_date IS NULL AND value_float is NOT NULL AND value_int IS NULL) OR
            (value_text IS NULL AND value_boolean IS NULL AND value_date IS NULL AND value_float is NULL AND value_int IS NOT NULL)
        )
    );
CREATE INDEX IF NOT EXISTS idx_eav_movie ON attribute_values(movie_id);
CREATE INDEX IF NOT EXISTS idx_eav_attribute ON attribute_values(attribute_id);

CREATE TABLE
    IF NOT EXISTS sessions (
        id SERIAL PRIMARY KEY,
        movie_id INT NOT NULL REFERENCES movies (id) ON DELETE CASCADE,
        hall_id INT NOT NULL REFERENCES halls (id) ON DELETE CASCADE,
        start_time TIMESTAMP NOT NULL,
        end_time TIMESTAMP NOT NULL,
        CHECK (start_time < end_time)
    );

CREATE TABLE
    IF NOT EXISTS prices (
        id SERIAL PRIMARY KEY,
        session_id INT NOT NULL REFERENCES sessions (id) ON DELETE CASCADE,
        place_type VARCHAR(15) NOT NULL,
        price DECIMAL(10, 2) NOT NULL CHECK (price >= 0),
        UNIQUE (session_id, place_type)
    );

CREATE TABLE
    IF NOT EXISTS customers (
        id SERIAL PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE,
        phone VARCHAR(11),
        created_at TIMESTAMP DEFAULT NOW ()
    );

CREATE TABLE
    IF NOT EXISTS orders (
        id SERIAL PRIMARY KEY,
        customer_id INT NOT NULL REFERENCES customers (id) ON DELETE SET NULL,
        total_amount DECIMAL(10, 2) NOT NULL CHECK (total_amount >= 0),
        order_date TIMESTAMP DEFAULT NOW (),
        status VARCHAR(20) DEFAULT 'pending' CHECK (
            status IN ('pending', 'confirmed', 'cancelled', 'completed')
        )
    );

CREATE TABLE
    IF NOT EXISTS tickets (
        id SERIAL PRIMARY KEY,
        session_id INT NOT NULL REFERENCES sessions (id) ON DELETE CASCADE,
        place_id INT NOT NULL REFERENCES places (id) ON DELETE CASCADE,
        order_id INT NOT NULL REFERENCES orders (id) ON DELETE CASCADE,
        created_at TIMESTAMP DEFAULT NOW (),
        UNIQUE (session_id, place_id)
    );
