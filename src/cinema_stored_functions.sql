-- calculate end time based on film duration plus 10 minutes break for cleaning rounded ceil to 10 minutes interval
CREATE OR REPLACE FUNCTION ceil_time(p_time TIME, p_minutes INTEGER)
RETURNS TIME AS $$
BEGIN
    RETURN (
        date_trunc('hour', p_time) +
        (ceil(EXTRACT(MINUTE FROM p_time) / p_minutes::DECIMAL) * p_minutes) * INTERVAL '1 minute'
    )::TIME;
END;
$$ LANGUAGE plpgsql IMMUTABLE;

-- random film extractor not used
CREATE OR REPLACE FUNCTION get_random_film()
RETURNS BIGINT AS $$
    SELECT id FROM films ORDER BY RANDOM() LIMIT 1;
$$ LANGUAGE SQL STABLE;

-- schedule generator
CREATE OR REPLACE FUNCTION generate_random_schedule(
    p_start_date DATE,
    p_end_date DATE,
    p_max_screenings_per_day INTEGER DEFAULT 20  -- Limit to prevent infinite loops
)
RETURNS TABLE (
    hall_id BIGINT,
    hall_title VARCHAR,
    schedule_date DATE,
    film_title VARCHAR,
    start_time TIME,
    end_time TIME,
    cleaning_minutes INTEGER
) AS $$
DECLARE
    v_current_date DATE;
    v_hall_record RECORD;
    v_current_minutes INTEGER;
    v_end_minutes INTEGER;
    v_film_id BIGINT;
    v_film_title VARCHAR;
    v_film_duration INTEGER;
    v_cleaning_minutes INTEGER := 10;
    v_round_interval INTEGER := 10;
    v_screening_count INTEGER;
    v_max_attempts INTEGER := 10;  -- Max attempts to find a valid film
    v_attempt INTEGER;
    v_open_minutes INTEGER;
    v_close_minutes INTEGER := 23 * 60; -- 23:00 = 1380 minutes
BEGIN
    -- Loop through each date in range
    v_current_date := p_start_date;
    WHILE v_current_date <= p_end_date LOOP

        -- Loop through each hall
        FOR v_hall_record IN
            SELECT
                h.id,
                h.title,
                EXTRACT(HOUR FROM h.open_time) * 60 + EXTRACT(MINUTE FROM h.open_time) AS open_minutes
            FROM halls h
            ORDER BY h.id
        LOOP
            -- Use minutes since midnight for easier comparison
            v_open_minutes := v_hall_record.open_minutes;
            v_current_minutes := v_open_minutes;
            v_screening_count := 0;

            -- Generate screenings until closing time (23:00)
            WHILE v_current_minutes < v_close_minutes AND v_screening_count < p_max_screenings_per_day LOOP

                -- Reset attempt counter
                v_attempt := 0;
                v_film_id := NULL;

                -- Try to find a film that fits in remaining time
                WHILE v_attempt < v_max_attempts LOOP
                    -- Get random film
                    SELECT id, title, duration
                    INTO v_film_id, v_film_title, v_film_duration
                    FROM films
                    ORDER BY RANDOM()
                    LIMIT 1;

                    -- Calculate potential end time and round up to next 10 minute
                    v_end_minutes := v_current_minutes + v_film_duration + v_cleaning_minutes;
                    v_end_minutes := ceil(v_end_minutes / 10.0) * 10;

                    -- Check if film fits before closing time
                    IF v_end_minutes <= v_close_minutes THEN
                        EXIT;
                    END IF;

                    v_attempt := v_attempt + 1;
                END LOOP;

                -- If no film found that fits, break the loop
                IF v_film_id IS NULL THEN
                    EXIT;
                END IF;

                -- Return the scheduled screening
                hall_id := v_hall_record.id;
                hall_title := v_hall_record.title;
                schedule_date := v_current_date;
                film_title := v_film_title;
                start_time := (v_current_minutes || ' minutes')::INTERVAL::TIME;
                end_time := (v_end_minutes || ' minutes')::INTERVAL::TIME;
                cleaning_minutes := v_cleaning_minutes;

                RETURN NEXT;

                -- Update current time for next screening
                v_current_minutes := v_end_minutes;
                v_screening_count := v_screening_count + 1;

            END LOOP;
        END LOOP;

        -- Move to next date
        v_current_date := v_current_date + 1;
    END LOOP;

    RETURN;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION insert_random_schedule(
    p_start_date DATE,
    p_end_date DATE,
    p_time_factor DECIMAL(4, 2) DEFAULT 1.0  -- Default time factor
)
RETURNS INTEGER AS $$
DECLARE
    v_schedule_record RECORD;
    v_inserted_count INTEGER := 0;
    v_schedule_id BIGINT;
BEGIN
    -- Generate schedule and insert directly
    FOR v_schedule_record IN
        SELECT * FROM generate_random_schedule(p_start_date, p_end_date)
    LOOP
        -- Insert into schedule table
        INSERT INTO schedule (
            hall_id,
            film_id,
            date,
            time,
            end_time,
            time_factor
        )
        VALUES (
            v_schedule_record.hall_id,
            (SELECT id FROM films WHERE title = v_schedule_record.film_title LIMIT 1),
            v_schedule_record.schedule_date,
            v_schedule_record.start_time,
            v_schedule_record.end_time,
            CASE
                WHEN v_schedule_record.start_time < '12:00:00' THEN p_time_factor * 0.8
                WHEN v_schedule_record.start_time >= '12:00:00' AND v_schedule_record.start_time < '18:00:00' THEN p_time_factor * 1.0
                WHEN v_schedule_record.start_time >= '18:00:00' AND v_schedule_record.start_time < '21:00:00' THEN p_time_factor * 1.3
                ELSE p_time_factor * 0.8
            END
        )
        RETURNING id INTO v_schedule_id;

        v_inserted_count := v_inserted_count + 1;

        -- Optional: Raise notice for debugging
        RAISE NOTICE 'Inserted: Hall: %, Film: %, Date: %, Time: %',
            v_schedule_record.hall_title,
            v_schedule_record.film_title,
            v_schedule_record.schedule_date,
            v_schedule_record.start_time;
    END LOOP;

    RETURN v_inserted_count;
END;
$$ LANGUAGE plpgsql;

-- Function to get available seats count for a screening
CREATE OR REPLACE FUNCTION get_available_seats_count(p_schedule_id BIGINT)
RETURNS INTEGER AS $$
DECLARE
    v_total_seats INTEGER;
    v_taken_seats INTEGER;
BEGIN
    -- Get total seats in the hall
    SELECT COUNT(s.id) INTO v_total_seats
    FROM seats s
        JOIN seat_maps m ON s.map_id = m.id
        JOIN schedule sc ON sc.hall_id = m.hall_id
    WHERE sc.id = p_schedule_id;

    -- Get already taken seats
    SELECT COUNT(t.id) INTO v_taken_seats
    FROM tickets t
    WHERE t.schedule_id = p_schedule_id;

    RETURN v_total_seats - v_taken_seats;
END;
$$ LANGUAGE plpgsql STABLE;

-- Function to get random available seat for a screening
CREATE OR REPLACE FUNCTION get_random_available_seat(p_schedule_id BIGINT)
RETURNS BIGINT AS $$
DECLARE
    v_hall_id BIGINT;
    v_seat_id BIGINT;
BEGIN
    -- Get hall_id from schedule
    SELECT hall_id INTO v_hall_id
    FROM schedule
    WHERE id = p_schedule_id;

    -- Find random available seat
    SELECT s.id INTO v_seat_id
    FROM seats s
        JOIN seat_maps m ON s.map_id = m.id
    WHERE m.hall_id = v_hall_id
        AND NOT EXISTS (
            SELECT 1 FROM tickets t
            WHERE t.schedule_id = p_schedule_id
                AND t.seat_id = s.id
        )
    ORDER BY RANDOM()
    LIMIT 1;

    RETURN v_seat_id;
END;
$$ LANGUAGE plpgsql STABLE;

CREATE OR REPLACE FUNCTION generate_tickets(
    p_total_tickets INTEGER DEFAULT 1000,
    p_fully_booked_percentage INTEGER DEFAULT 5,  -- 5% of screenings fully booked
    p_min_occupancy INTEGER DEFAULT 20,           -- Min 20% occupancy
    p_max_occupancy INTEGER DEFAULT 60            -- Max 60% occupancy
)
RETURNS TABLE(
    schedule_id BIGINT,
    hall_title VARCHAR,
    film_title VARCHAR,
    screening_date DATE,
    screening_time TIME,
    total_seats INTEGER,
    tickets_generated INTEGER,
    occupancy_percentage DECIMAL(5,2),
    is_fully_booked BOOLEAN
) AS $$
DECLARE
    v_schedule_record RECORD;
    v_screening_count INTEGER;
    v_fully_booked_count INTEGER;
    v_available_seats INTEGER;
    v_tickets_to_generate INTEGER;
    v_tickets_generated INTEGER;
    v_total_seats INTEGER;
    v_occupancy_percentage DECIMAL(5,2);
    v_seat_id BIGINT;
    v_random_factor DECIMAL(5,2);
    v_is_fully_booked BOOLEAN;
    v_ticket_price DECIMAL(10,2);
    v_fully_booked_ids BIGINT[] := '{}';
BEGIN
    -- Get total number of screenings
    SELECT COUNT(*) INTO v_screening_count FROM schedule;

    -- Calculate how many screenings should be fully booked
    v_fully_booked_count := (v_screening_count * p_fully_booked_percentage / 100);

    -- Select random screenings to be fully booked
    SELECT ARRAY_AGG(id) INTO v_fully_booked_ids
    FROM (
        SELECT id FROM schedule ORDER BY RANDOM() LIMIT v_fully_booked_count
    ) AS random_screenings;

    -- Loop through each screening
    FOR v_schedule_record IN
        SELECT
            s.id,
            s.hall_id,
            s.date,
            s.time,
            s.time_factor,
            f.title AS film_title,
            f.base_price,
            h.title AS hall_title,
            (
                SELECT COUNT(*)
                FROM seats
                    JOIN seat_maps m ON seats.map_id = m.id
                WHERE m.hall_id = s.hall_id
            ) AS total_seats
        FROM schedule s
            JOIN films f ON s.film_id = f.id
            JOIN halls h ON s.hall_id = h.id
        ORDER BY s.date, s.time
    LOOP
        -- Check if this screening should be fully booked
        v_is_fully_booked := v_schedule_record.id = ANY(v_fully_booked_ids);

        -- Calculate occupancy percentage
        IF v_is_fully_booked THEN
            v_occupancy_percentage := 100.0;
            v_tickets_to_generate := v_schedule_record.total_seats;
        ELSE
            -- Random occupancy between min and max
            v_random_factor := p_min_occupancy + (RANDOM() * (p_max_occupancy - p_min_occupancy));
            v_occupancy_percentage := ROUND(v_random_factor, 2);
            v_tickets_to_generate := FLOOR(v_schedule_record.total_seats * v_random_factor / 100);
        END IF;

        -- Limit by total available tickets parameter
        IF v_tickets_to_generate > p_total_tickets THEN
            v_tickets_to_generate := p_total_tickets;
        END IF;

        v_tickets_generated := 0;

        -- Generate tickets for this screening
        WHILE v_tickets_generated < v_tickets_to_generate AND p_total_tickets > 0 LOOP
            -- Get random available seat
            SELECT get_random_available_seat(v_schedule_record.id) INTO v_seat_id;

            -- If no available seats, break
            IF v_seat_id IS NULL THEN
                EXIT;
            END IF;

            -- Calculate ticket price
            v_ticket_price := ROUND(
                (v_schedule_record.base_price * v_schedule_record.time_factor * (0.8 + RANDOM() * 0.6))::NUMERIC,
                2
            );

            -- Insert ticket
            INSERT INTO tickets (schedule_id, seat_id, real_price)
            VALUES (v_schedule_record.id, v_seat_id, v_ticket_price);

            v_tickets_generated := v_tickets_generated + 1;
            p_total_tickets := p_total_tickets - 1;

            -- Return progress row
            schedule_id := v_schedule_record.id;
            hall_title := v_schedule_record.hall_title;
            film_title := v_schedule_record.film_title;
            screening_date := v_schedule_record.date;
            screening_time := v_schedule_record.time;
            total_seats := v_schedule_record.total_seats;
            tickets_generated := v_tickets_generated;
            occupancy_percentage := v_occupancy_percentage;
            is_fully_booked := v_is_fully_booked;

            RETURN NEXT;
        END LOOP;

        -- Exit if we've generated all tickets
        EXIT WHEN p_total_tickets <= 0;
    END LOOP;

    RETURN;
END;
$$ LANGUAGE plpgsql;
