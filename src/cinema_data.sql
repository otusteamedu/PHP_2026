INSERT INTO cinema (title, address, url) VALUES
    ('Звезда', 'Сиреневый бульвар, 26, Москва', 'zvezda.kino'),
    ('Победа', 'Сиреневый бульвар, 12, Москва', 'pobeda.kino'),
    ('Мир', 'Сиреневый бульвар, 24, Москва', 'mir.kino');

INSERT INTO phones (cinema_id, number) VALUES
    (1, '3125478'),
    (1, '3125479'),
    (1, '31254710'),
    (3, '2144344'),
    (2, '1244546');

INSERT INTO halls (cinema_id, title, open_time) VALUES
    (1, 'Зеленый', '09:30:00'),
    (1, 'Желтый', '08:30:00'),
    (1, 'Красный', '09:15:00'),
    (2, 'Большой', '08:30:00'),
    (2, 'Малый', '10:30:00'),
    (3, 'Лето', '08:45:00'),
    (3, 'Осень', '09:00:00'),
    (3, 'Зима', '09:15:00'),
    (3, 'Весна', '09:30:00');

INSERT INTO films (title, duration, base_price) VALUES
    ('Чебурашка', 82, 1),
    ('Чебурашка 2', 76, 1.1),
    ('Гена', 112, 1.1),
    ('Возвращение Гены', 84, 1.1),
    ('Гена навсегда', 68, .8),
    ('Чебурашка против всех', 72, 1.3);

SELECT * FROM generate_random_schedule('2026-03-01', '2026-03-07');
SELECT insert_random_schedule('2026-01-01', '2026-01-07', 1.0);

INSERT INTO seat_maps (hall_id, image) VALUES
    (1, 'map.jpg'),
    (2, 'map.jpg'),
    (3, 'map.jpg'),
    (4, 'map.jpg'),
    (5, 'map.jpg'),
    (6, 'map.jpg'),
    (7, 'map.jpg'),
    (8, 'map.jpg'),
    (9, 'map.jpg');


INSERT INTO seats (map_id, row_number, seat_number, price_multiplier, seat_type) VALUES
    (1, 1, 1, 1, 'regular'),
    (1, 1, 2, 1, 'regular'),
    (1, 1, 3, 1, 'regular'),
    (1, 1, 4, 1, 'regular'),
    (1, 2, 1, 1, 'regular'),
    (1, 2, 2, 1, 'regular'),
    (1, 2, 3, 1, 'regular'),
    (1, 2, 4, 1, 'regular'),
    (1, 3, 1, 1.3, 'vip'),
    (1, 3, 2, 1.3, 'vip'),
    (1, 3, 3, 1.3, 'vip'),
    (1, 3, 4, 1.3, 'vip'),
    (1, 4, 1, 1.8, 'love seat'),
    (1, 4, 2, 1.8, 'love seat'),

    (2, 1, 1, 1, 'regular'),
    (2, 1, 2, 1, 'regular'),
    (2, 1, 3, 1, 'regular'),
    (2, 1, 4, 1, 'regular'),
    (2, 2, 1, 1, 'regular'),
    (2, 2, 2, 1, 'regular'),
    (2, 2, 3, 1, 'regular'),
    (2, 2, 4, 1, 'regular'),

    (3, 1, 1, 1.3, 'vip'),
    (3, 1, 2, 1.3, 'vip'),
    (3, 1, 3, 1.3, 'vip'),
    (3, 1, 4, 1.3, 'vip'),
    (3, 2, 1, 1.8, 'love seat'),
    (3, 2, 2, 1.8, 'love seat'),

    (4, 1, 1, 1, 'regular'),
    (4, 1, 2, 1, 'regular'),
    (4, 1, 3, 1, 'regular'),
    (4, 1, 4, 1, 'regular'),
    (4, 2, 1, 1, 'regular'),
    (4, 2, 2, 1, 'regular'),
    (4, 2, 3, 1, 'regular'),
    (4, 2, 4, 1, 'regular'),
    (4, 3, 1, 1.3, 'vip'),
    (4, 3, 2, 1.3, 'vip'),
    (4, 3, 3, 1.3, 'vip'),
    (4, 3, 4, 1.3, 'vip'),
    (4, 4, 1, 1.8, 'love seat'),
    (4, 4, 2, 1.8, 'love seat');
