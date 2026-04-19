CREATE TABLE IF NOT EXISTS movies (
    id         SERIAL PRIMARY KEY,
    title      VARCHAR(255)             NOT NULL,
    year       INTEGER                  NOT NULL,
    genre      VARCHAR(100)             NOT NULL DEFAULT '',
    director   VARCHAR(255)             NOT NULL DEFAULT '',
    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);