-- Конфигурация и вставка количества записей

DO $$
DECLARE
    v_cinemas_count      INT := 1;
    v_cinema_halls_count INT := 100;
    v_seat_types_count   INT := 10;
    v_seats_per_hall     INT := 1000;
    v_films_count        INT := 1000;
    v_screenings_count   INT := 10000;
    v_clients_count      INT := 1000;
BEGIN
    INSERT INTO cinemas (id, name)
    SELECT gs.id, 'Cinema ' || gs.id
    FROM generate_series(1, v_cinemas_count) AS gs(id);

    INSERT INTO cinema_halls (id, cinema_id, name)
    SELECT gs.id, 1, 'Hall ' || gs.id
    FROM generate_series(1, v_cinema_halls_count) AS gs(id);

    INSERT INTO seat_types (id, name)
    SELECT gs.id, 'Type ' || gs.id
    FROM generate_series(1, v_seat_types_count) AS gs(id);

    INSERT INTO cinema_hall_seats (hall_id, type_id, seat_row, seat_number)
    SELECT hall_id, type_id, seat_row, seat_number
    FROM generate_series(1, v_cinema_halls_count) AS h(hall_id)
    CROSS JOIN generate_series(1, v_seat_types_count) AS st(type_id)
    CROSS JOIN generate_series(1, v_seats_per_hall / v_seat_types_count) AS s(seat_number)
    CROSS JOIN LATERAL (
        SELECT ((seat_number - 1) / 10) + 1 AS seat_row,
               ((seat_number - 1) % 10) + 1 AS seat_num
    ) coords
    ON CONFLICT (hall_id, seat_row, seat_number) DO NOTHING;

    INSERT INTO films (id, name, duration)
    SELECT gs.id, 'Film ' || gs.id, (3600 + random() * 7200)::INT
    FROM generate_series(1, v_films_count) AS gs(id);

    INSERT INTO screenings (film_id, start_time, cinema_hall_id)
    SELECT
        (1 + (gs.id - 1) % v_films_count) AS film_id,
        (NOW() + (gs.id || ' hours')::INTERVAL) AS start_time,
        (1 + (gs.id - 1) % v_cinema_halls_count) AS cinema_hall_id
    FROM generate_series(1, v_screenings_count) AS gs(id);

    INSERT INTO clients (id, name, email)
    SELECT gs.id, 'Client ' || gs.id, 'client' || gs.id || '@mail.ru'
    FROM generate_series(1, v_clients_count) AS gs(id);

    -- (все комбинации film × seat_type × cinema_hall)
    INSERT INTO prices (value, film_id, seat_type_id, cinema_hall_id)
    SELECT
        (100 + random() * 900)::INT,
        f.id,
        st.id,
        ch.id
    FROM generate_series(1, v_films_count) f(id)
    CROSS JOIN generate_series(1, v_seat_types_count) st(id)
    CROSS JOIN generate_series(1, v_cinema_halls_count) ch(id)
    ON CONFLICT (film_id, seat_type_id, cinema_hall_id) DO NOTHING;

    INSERT INTO tickets (client_id, screening_id, cinema_hall_seat_id, price, status, paid_at)
    SELECT
        (1 + (row_num - 1) % v_clients_count) AS client_id,
        screening_id,
        seat_id AS cinema_hall_seat_id,
        (200 + random() * 800)::INT AS price,
        ('{reserved,paid,returned}'::ticket_status[])[floor(random() * 3 + 1)::INT] AS status,
        (NOW() + (TRUNC(random() * 10) || ' hours')::INTERVAL) AS paid_at
    FROM (
        SELECT
            s.id AS screening_id,
            chs.id AS seat_id,
            ROW_NUMBER() OVER (PARTITION BY s.id ORDER BY chs.id) AS row_num
        FROM screenings s
        JOIN cinema_hall_seats chs ON chs.hall_id = s.cinema_hall_id
    ) AS all_combinations
    WHERE random() < 0.5 -- (~процент от всех возможных комбинаций screening × seat)
    ON CONFLICT (screening_id, cinema_hall_seat_id) DO NOTHING;
END;
$$ LANGUAGE plpgsql;
