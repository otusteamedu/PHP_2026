SET search_path TO cinema;

INSERT INTO cinemas (name, city, address) VALUES
    ('КАРО 8', 'Краснодар', 'улица Володи Головатого, дом 313'),
    ('ЛЕНТА 3', 'Краснодар', 'улица Петра Метальникова, дом 66'),
    ('Монитор', 'Краснодар', 'улица Дзержинского, дом 100');

INSERT INTO halls (cinema_id, name) VALUES
    ((SELECT id FROM cinemas WHERE name = 'КАРО 8'), 'Зал 1'),
    ((SELECT id FROM cinemas WHERE name = 'КАРО 8'), 'Зал 2'),
    ((SELECT id FROM cinemas WHERE name = 'ЛЕНТА 3'), 'Зал 01'),
    ((SELECT id FROM cinemas WHERE name = 'ЛЕНТА 3'), 'Зал 02'),
    ((SELECT id FROM cinemas WHERE name = 'Монитор'), 'Красный зал'),
    ((SELECT id FROM cinemas WHERE name = 'Монитор'), 'Белый зал');