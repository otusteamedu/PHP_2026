erDiagram
    cinemas ||--o{ halls : "has"
    halls ||--o{ seats : "contains"
    halls ||--o{ screenings : "hosts"
    seat_types ||--o{ seats : "defines"
    seat_types ||--o{ screening_prices : "priced in"
    movies ||--o{ screenings : "shown as"
    screenings ||--o{ screening_prices : "has"
    screenings ||--o{ tickets : "includes"
    seats ||--o{ tickets : "booked via"
    customers ||--o{ orders : "places"
    orders ||--o{ tickets : "contains"

    cinemas {
        BIGINT id PK
        VARCHAR name
        VARCHAR city
        VARCHAR address
    }

    halls {
        BIGINT id PK
        BIGINT cinema_id FK
        VARCHAR name
    }

    seat_types {
        SMALLINT id PK
        VARCHAR type      
    }

    seats {
        BIGINT id PK
        BIGINT hall_id FK
        SMALLINT seat_type_id FK
        VARCHAR row_code
        INT seat_number
        BOOLEAN is_active
    }

    movies {
        BIGINT id PK
        VARCHAR title
        VARCHAR original_title
        SMALLINT duration
        DATE release_date
        VARCHAR age_rating
    }

    screenings {
        BIGINT id PK
        BIGINT hall_id FK
        BIGINT movie_id FK
        TIMESTAMPTZ starts_at
        TIMESTAMPTZ ends_at
        VARCHAR status
    }

    screening_prices {
        BIGINT screening_id PK, FK
        SMALLINT seat_type_id PK, FK
        NUMERIC price_amount
    }

    customers {
        BIGINT id PK      
        VARCHAR email
        VARCHAR phone
    }

    orders {
        BIGINT id PK
        BIGINT customer_id FK
        VARCHAR status
        TIMESTAMPTZ created_at
        TIMESTAMPTZ paid_at
    }

    tickets {
        BIGINT id PK
        BIGINT order_id FK
        BIGINT screening_id FK
        BIGINT seat_id FK
        BIGINT hall_id FK "Composite FK constraint"
        NUMERIC price_amount
        VARCHAR status
    }