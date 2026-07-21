CREATE TABLE IF NOT EXISTS statement_requests (
    id UUID PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    date_from DATE NOT NULL,
    date_to DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'new',
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);
