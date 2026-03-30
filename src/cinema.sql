CREATE TABLE IF NOT EXISTS `cinema` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Кинотеатр. Основная информация';

CREATE TABLE IF NOT EXISTS `phones` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT,
    `cinema_id` BIGINT UNSIGNED NOT NULL,
    `number` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `phones_cinema` (`cinema_id`),
    CONSTRAINT `phones_cinema_fk` FOREIGN KEY (`cinema_id`) REFERENCES `cinema` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Телефоны';

CREATE TABLE IF NOT EXISTS `halls` (
   `id` BIGINT UNSIGNED AUTO_INCREMENT,
   `cinema_id` BIGINT UNSIGNED NOT NULL,
   `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `halls_cinema` (`cinema_id`),
    CONSTRAINT `halls_cinema_fk` FOREIGN KEY (`cinema_id`) REFERENCES `cinema` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Кинотеатр. Залы';

CREATE TABLE IF NOT EXISTS `films` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT,
    `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `duration` SMALLINT UNSIGNED NOT NULL COMMENT 'Duration in minutes',
    `base_price` DECIMAL(10,2) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Фильмы';

CREATE TABLE IF NOT EXISTS `schedule` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT,
    `hall_id` BIGINT UNSIGNED NOT NULL,
    `film_id` BIGINT UNSIGNED NOT NULL,
    `date` DATE NOT NULL,
    `time` TIME NOT NULL,
    `time_factor` DECIMAL(4,2) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Коэффициент по времени дня',
    PRIMARY KEY (`id`),
    KEY `schedule_halls` (`hall_id`),
    CONSTRAINT `schedule_halls_fk` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE CASCADE,
    KEY `schedule_films` (`film_id`),
    CONSTRAINT `schedule_films_fk` FOREIGN KEY (`film_id`) REFERENCES `films` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Расписание';

CREATE TABLE IF NOT EXISTS `maps` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT,
    `hall_id` BIGINT UNSIGNED NOT NULL,
    `image` varchar(100) NOT NULL COMMENT 'Map name/version',
    PRIMARY KEY (`id`),
    KEY `maps_halls` (`hall_id`),
    CONSTRAINT `maps_halls_fk` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Карта зала';

CREATE TABLE IF NOT EXISTS `seats` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT,
    `map_id` BIGINT UNSIGNED NOT NULL,
    `row_number` SMALLINT UNSIGNED NOT NULL,
    `seat_number` SMALLINT UNSIGNED NOT NULL,
    `price_multiplier` DECIMAL(3,2) UNSIGNED DEFAULT 1.00 COMMENT 'Multiplier for base price',
    `seat_type` ENUM('regular', 'vip', 'love seat') DEFAULT 'regular',
    PRIMARY KEY (`id`),
    KEY `seats_map` (`map_id`),
    UNIQUE KEY `unique_seat` (`map_id`, `row_number`, `seat_number`),
    CONSTRAINT `seats_maps_fk` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Список мест';

CREATE TABLE IF NOT EXISTS `tickets` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT,
    `schedule_id` BIGINT UNSIGNED NOT NULL,
    `seat_id` BIGINT UNSIGNED NOT NULL,
    `real_price` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Фактическая цена продажи',
    PRIMARY KEY (`id`),
    KEY `ticket_schedule` (`schedule_id`),
    CONSTRAINT `ticket_schedule_fk` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`id`) ON DELETE CASCADE,
    KEY `ticket_seat` (`seat_id`),
    CONSTRAINT `ticket_seats_fk` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Билеты';



INSERT INTO `cinema` (`title`, `address`, `url`)
VALUES
    ('Звезда', 'Сиреневый бульвар, 26, Москва', 'zvezda.kino'),
    ('Победа', 'Сиреневый бульвар, 12, Москва', 'pobeda.kino'),
    ('Мир', 'Сиреневый бульвар, 24, Москва', 'mir.kino');

INSERT INTO `phones` (`cinema_id`, `number`)
VALUES
    (1, '3125478'),
    (3, '2144344'),
    (2, '1244546');

INSERT INTO `halls` (`cinema_id`, `title`)
VALUES
    (1, 'Зеленый'),
    (1, 'Желтый'),
    (1, 'Красный'),
    (2, 'Большой'),
    (2, 'Малый'),
    (3, 'Лето'),
    (3, 'Осень'),
    (3, 'Зима'),
    (3, 'Весна');


INSERT INTO `maps` (`hall_id`, `image`)
VALUES
    (1, 'map.jpg'),
    (2, 'map.jpg'),
    (3, 'map.jpg'),
    (4, 'map.jpg'),
    (5, 'map.jpg'),
    (6, 'map.jpg'),
    (7, 'map.jpg'),
    (8, 'map.jpg'),
    (9, 'map.jpg');

INSERT INTO `seats` (`map_id`, `row_number`, `seat_number`, `price_multiplier`, `seat_type`)
VALUES
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

INSERT INTO `films` (`id`, `title`, `duration`, `base_price`) VALUES
    (1, 'Чебурашка', 82, 1),
    (2, 'Чебурашка 2', 76, 1.1),
    (3, 'Чебурашка против всех', 72, 1.3);


INSERT INTO `schedule` (`id`, `hall_id`, `film_id`, `date`, `time`, `time_factor`) VALUES
    (1, 1, 1, '2026-03-01', '09:00:00', 0.70),
    (2, 1, 2, '2026-03-01', '10:30:00', 0.80),
    (3, 1, 3, '2026-03-01', '12:00:00', 0.90),
    (4, 1, 1, '2026-03-01', '13:30:00', 1.00),
    (5, 1, 2, '2026-03-01', '16:00:00', 1.00),
    (6, 1, 3, '2026-03-01', '19:00:00', 1.10),
    (7, 1, 1, '2026-03-01', '20:30:00', 1.30),

    (8, 2, 2, '2026-03-01', '09:00:00', 0.70),
    (9, 2, 3, '2026-03-01', '10:30:00', 0.80),
    (10, 2, 1, '2026-03-01', '12:00:00', 0.90),
    (11, 2, 2, '2026-03-01', '13:30:00', 1.00),
    (12, 2, 3, '2026-03-01', '16:00:00', 1.00),
    (13, 2, 1, '2026-03-01', '19:00:00', 1.10),
    (14, 2, 2, '2026-03-01', '20:30:00', 1.30),

    (15, 3, 3, '2026-03-01', '09:00:00', 0.70),
    (16, 3, 1, '2026-03-01', '10:30:00', 0.80),
    (17, 3, 2, '2026-03-01', '12:00:00', 0.90),
    (18, 3, 3, '2026-03-01', '13:30:00', 1.00),
    (19, 3, 1, '2026-03-01', '16:00:00', 1.00),
    (20, 3, 2, '2026-03-01', '19:00:00', 1.10),
    (21, 3, 3, '2026-03-01', '20:30:00', 1.30);


-- БИЛЕТЫ
INSERT INTO `tickets` (`schedule_id`, `seat_id`, `real_price`)
SELECT
    s.id,
    seats.id,
    ROUND(
            s.time_factor * f.base_price * seats.price_multiplier *
            (0.8 + (RAND() * 0.6)),
            2
    )
FROM `schedule` s
         INNER JOIN `films` f ON s.film_id = f.id
         INNER JOIN `seats` seats ON seats.map_id = (
    SELECT m.id FROM maps m
    WHERE m.hall_id = s.hall_id
    LIMIT 1
    )
WHERE s.date = '2026-03-01'
  AND s.hall_id IN (1, 2, 3)
  AND (
    s.time < '18:00:00'
   OR (s.time >= '18:00:00' AND seats.seat_type IN ('vip', 'love seat'))
    )
-- ПРОВЕРКА: место ещё не занято на этот сеанс
  AND NOT EXISTS (
    SELECT 1
    FROM `tickets` t
    WHERE t.schedule_id = s.id
  AND t.seat_id = seats.id
    )
ORDER BY RAND()
    LIMIT 200;


-- ПОИСК КАССОВОГО ФИЛЬМА В ПРЕДЕЛАХ ОДНОГО ДНЯ
SELECT
    f.id,
    f.title,
    COUNT(DISTINCT s.id) AS showtimes_count,
    COUNT(t.id) AS tickets_sold,
    SUM(t.real_price) AS total_revenue,
    ROUND(AVG(t.real_price), 2) AS avg_ticket_price,
    ROUND(SUM(t.real_price) / COUNT(DISTINCT s.id), 2) AS revenue_per_showtime,
    ROUND(COUNT(t.id) / COUNT(DISTINCT s.id), 1) AS avg_tickets_per_showtime,
    -- Процент от общей кассы дня
    ROUND(100 * SUM(t.real_price) / SUM(SUM(t.real_price)) OVER(), 1) AS revenue_percentage
FROM `tickets` t
         JOIN `schedule` s ON t.schedule_id = s.id
         JOIN `films` f ON s.film_id = f.id
WHERE s.date = '2026-03-01'
GROUP BY f.id, f.title
ORDER BY total_revenue DESC;
