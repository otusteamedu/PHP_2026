-- Конфигурация и вставка количества записей

DO $$
    DECLARE
        v_cinemas_count      INT := 1;
        v_cinema_halls_count INT := 60;
        v_seat_types_count   INT := 10;
        v_seats_per_hall     INT := 150;
        v_films_count        INT := 15000;
        v_screenings_count   INT := 60000;
        v_clients_count      INT := 80000;
        v_tickets_target     INT := 100000;
    BEGIN
        TRUNCATE TABLE tickets, prices, clients, screenings, films, cinema_hall_seats, seat_types, cinema_halls, cinemas RESTART IDENTITY CASCADE;

        INSERT INTO cinemas (id, name)
        SELECT gs.id, 'Cinema ' || gs.id
        FROM generate_series(1, v_cinemas_count) AS gs(id);

        INSERT INTO cinema_halls (id, cinema_id, name)
        SELECT
            gs.id,
            ((gs.id - 1) % v_cinemas_count) + 1,
            'Hall ' || gs.id
        FROM generate_series(1, v_cinema_halls_count) AS gs(id);

        INSERT INTO seat_types (id, name)
        SELECT gs.id, 'Type ' || gs.id
        FROM generate_series(1, v_seat_types_count) AS gs(id);

        INSERT INTO cinema_hall_seats (hall_id, type_id, seat_row, seat_number)
        SELECT
            h.hall_id,
            ((s.seat_number - 1) % v_seat_types_count) + 1,
            ((s.seat_number - 1) / 10) + 1, -- Ряд (1-10)
            ((s.seat_number - 1) % 10) + 1   -- Номер в ряду (1-10)
        FROM generate_series(1, v_cinema_halls_count) AS h(hall_id)
                 CROSS JOIN generate_series(1, v_seats_per_hall) AS s(seat_number);

        INSERT INTO films (id, name, duration)
        SELECT
            gs.id,
            'Film ' || gs.id,
            (3600 + (random() * 7200)::INT) -- От 1 до 3 часов в секундах
        FROM generate_series(1, v_films_count) AS gs(id);

        INSERT INTO screenings (film_id, start_time, cinema_hall_id)
        SELECT
            ((gs.id - 1) % v_films_count) + 1,
            (NOW() + (gs.id || ' hours')::INTERVAL),
            ((gs.id - 1) % v_cinema_halls_count) + 1
        FROM generate_series(1, v_screenings_count) AS gs(id);

        INSERT INTO clients (id, name, email)
        SELECT
            gs.id,
            'Client ' || gs.id,
            'client' || gs.id || '@mail.ru'
        FROM generate_series(1, v_clients_count) AS gs(id);

        INSERT INTO prices (value, film_id, seat_type_id, cinema_hall_id)
        SELECT
            (100 + (random() * 900)::INT),
            f.id,
            st.id,
            ch.id
        FROM generate_series(1, v_films_count) f(id)
                 CROSS JOIN generate_series(1, v_seat_types_count) st(id)
                 CROSS JOIN generate_series(1, v_cinema_halls_count) ch(id);
        INSERT INTO tickets (client_id, screening_id, cinema_hall_seat_id, price, status, paid_at)
        SELECT
            ((t.id - 1) % v_clients_count) + 1,
            t.screening_id,
            t.seat_id,
            (200 + (random() * 800)::INT),
            ('{reserved,paid,returned}'::text[])[floor(random() * 3 + 1)::INT]::ticket_status,
            (NOW() - (TRUNC(random() * 30) || ' days')::INTERVAL) -- Билеты куплены в прошлом
        FROM (
                 SELECT
                     gs.id AS id,
                     s.id AS screening_id,
                     (
                         SELECT chs.id
                         FROM cinema_hall_seats chs
                         WHERE chs.hall_id = s.cinema_hall_id
                         ORDER BY random()
                         LIMIT 1
                     ) AS seat_id
                 FROM generate_series(1, v_tickets_target) AS gs(id)
                          CROSS JOIN LATERAL (
                     SELECT id, cinema_hall_id
                     FROM screenings
                     ORDER BY random()
                     LIMIT 1
                     ) s
             ) AS t
        WHERE t.seat_id IS NOT NULL;
    END;
$$ LANGUAGE plpgsql;
