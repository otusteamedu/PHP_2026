SET search_path TO cinema;

INSERT INTO movies (title, original_title, duration, release_date, age_rating) VALUES
    ('Звёздные войны: Супер главная надежда', 'Звёздные войны', 150, '2026-03-28', 'PG-13'),
    ('Дюна: Часть третья', 'Дюна', 140, '2026-03-29', 'PG-13');

INSERT INTO attribute_groups (name, code) VALUES
    ('Рецензии', 'reviews'),
    ('Премии', 'awards'),
    ('Важные даты', 'important_dates'),
    ('Служебные даты', 'service_dates'),
    ('Рейтинг', 'rating')
ON CONFLICT (code) DO NOTHING;

INSERT INTO attributes (group_id, name, code, data_type) VALUES
    (1, 'Рецензия Кинопоиск', 'kinopoisk_review', 'text'),
    (1, 'Отзыв зрителей', 'user_review', 'text'),

    (2, 'Премия Оскар', 'oscar_win', 'boolean'),
    (2, 'Золотой глобус', 'golden_globe', 'boolean'),

    (3, 'Мировая премьера', 'world_premiere', 'date'),
    (3, 'Премьера в РФ', 'ru_premiere', 'date'),

    (4, 'Старт продаж билетов', 'ticket_sales_start', 'date'),
    (4, 'Запуск рекламы на ТВ', 'tv_ads_start', 'date'),

    (5, 'Рейтинг Кинопоиска', 'kp_rating', 'float'),
    (5, 'Рейтинг Okko', 'okko_rating', 'float')
ON CONFLICT (code) DO NOTHING;

INSERT INTO attribute_values (movie_id, attribute_id, value_text, value_int, value_float, value_date, value_boolean) 
VALUES
    (1, (SELECT id FROM attributes WHERE code='kinopoisk_review'), 'Шедевральное возвращение...', NULL, NULL, NULL, NULL),
    (1, (SELECT id FROM attributes WHERE code='user_review'), 'Очень понравился зрителям', NULL, NULL, NULL, NULL),
    (1, (SELECT id FROM attributes WHERE code='oscar_win'), NULL, NULL, NULL, NULL, TRUE),
    (1, (SELECT id FROM attributes WHERE code='golden_globe'), NULL, NULL, NULL, NULL, FALSE),
    (1, (SELECT id FROM attributes WHERE code='world_premiere'), NULL, NULL, NULL, '2026-03-28', NULL),
    (1, (SELECT id FROM attributes WHERE code='ru_premiere'), NULL, NULL, NULL, '2026-03-20', NULL),
    (1, (SELECT id FROM attributes WHERE code='ticket_sales_start'), NULL, NULL, NULL, CURRENT_DATE, NULL),
    (1, (SELECT id FROM attributes WHERE code='tv_ads_start'), NULL, NULL, NULL, CURRENT_DATE + INTERVAL '22 days', NULL),
    (1, (SELECT id FROM attributes WHERE code='kp_rating'), NULL, NULL, 8.5, NULL, NULL),
    (1, (SELECT id FROM attributes WHERE code='okko_rating'), NULL, NULL, 5.2, NULL, NULL),

    (2, (SELECT id FROM attributes WHERE code='kinopoisk_review'), 'Глубокая философская картина', NULL, NULL, NULL, NULL),
    (2, (SELECT id FROM attributes WHERE code='user_review'), 'Слишком затянуто, но красиво', NULL, NULL, NULL, NULL),
    (2, (SELECT id FROM attributes WHERE code='oscar_win'), NULL, NULL, NULL, NULL, FALSE),
    (2, (SELECT id FROM attributes WHERE code='golden_globe'), NULL, NULL, NULL, NULL, TRUE),
    (2, (SELECT id FROM attributes WHERE code='world_premiere'), NULL, NULL, NULL, '2026-03-25', NULL),
    (2, (SELECT id FROM attributes WHERE code='ru_premiere'), NULL, NULL, NULL, '2026-03-29', NULL),
    (2, (SELECT id FROM attributes WHERE code='ticket_sales_start'), NULL, NULL, NULL, CURRENT_DATE, NULL),
    (2, (SELECT id FROM attributes WHERE code='tv_ads_start'), NULL, NULL, NULL, CURRENT_DATE + INTERVAL '25 days', NULL),
    (2, (SELECT id FROM attributes WHERE code='kp_rating'), NULL, NULL, 8.0, NULL, NULL),
    (2, (SELECT id FROM attributes WHERE code='okko_rating'), NULL, NULL, 3.8, NULL, NULL)
ON CONFLICT (movie_id, attribute_id) DO NOTHING;