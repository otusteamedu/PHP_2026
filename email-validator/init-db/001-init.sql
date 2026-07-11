CREATE TABLE IF NOT EXISTS email_validation_jobs
(
    id BIGSERIAL PRIMARY KEY,

    status VARCHAR(20) NOT NULL
        CHECK (status IN (
            'QUEUED',
            'PROCESSING',
            'COMPLETED',
            'FAILED'
        )),

    emails JSONB NOT NULL,

    report_email VARCHAR(255) NOT NULL,

    created_at TIMESTAMP NOT NULL,

    updated_at TIMESTAMP NOT NULL
);