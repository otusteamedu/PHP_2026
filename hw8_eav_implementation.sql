-- 1. Справочник атрибутов (названия характеристик)
CREATE TABLE IF NOT EXISTS cinema.movie_attributes_list (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    data_type VARCHAR(20) NOT NULL -- integer, decimal, string
);

-- Наполняем справочник
INSERT INTO cinema.movie_attributes_list (name, data_type) VALUES 
('IMDb Rating', 'decimal'),
('Budget ($)', 'integer'),
('Country', 'string'),
('Age Rating', 'string')
ON CONFLICT DO NOTHING;

-- 2. Таблица значений (EAV)
-- Здесь movie_id ссылается на твою существующую таблицу cinema.movies
CREATE TABLE IF NOT EXISTS cinema.movie_attribute_values (
    movie_id INTEGER REFERENCES cinema.movies(id) ON DELETE CASCADE,
    attr_id INTEGER REFERENCES cinema.movie_attributes_list(id) ON DELETE CASCADE,
    attr_value TEXT NOT NULL,
    PRIMARY KEY (movie_id, attr_id)
);

-- 3. Наполняем данными (привязываем к первому попавшемуся фильму из твоей базы)
INSERT INTO cinema.movie_attribute_values (movie_id, attr_id, attr_value)
SELECT 
    m.id, 
    a.id, 
    CASE 
        WHEN a.name = 'IMDb Rating' THEN '8.9'
        WHEN a.name = 'Budget ($)' THEN '150000000'
        WHEN a.name = 'Country' THEN 'USA'
        WHEN a.name = 'Age Rating' THEN '16+'
    END
FROM cinema.movies m
CROSS JOIN cinema.movie_attributes_list a
WHERE m.id = (SELECT id FROM cinema.movies LIMIT 1)
ON CONFLICT DO NOTHING;

-- 4. Создаем VIEW для удобного чтения (показываем мастерство)
CREATE OR REPLACE VIEW cinema.v_movie_details AS
SELECT 
    m.title,
    MAX(CASE WHEN al.name = 'IMDb Rating' THEN av.attr_value END) AS imdb_rating,
    MAX(CASE WHEN al.name = 'Budget ($)' THEN av.attr_value END) AS budget,
    MAX(CASE WHEN al.name = 'Country' THEN av.attr_value END) AS country,
    MAX(CASE WHEN al.name = 'Age Rating' THEN av.attr_value END) AS age_limit
FROM cinema.movies m
JOIN cinema.movie_attribute_values av ON m.id = av.movie_id
JOIN cinema.movie_attributes_list al ON av.attr_id = al.id
GROUP BY m.id, m.title;
