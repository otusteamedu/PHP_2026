CREATE TABLE IF NOT EXISTS `categories` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `title` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `php_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CONSTRAINT uq_title_type UNIQUE (title, php_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `entities` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attributes` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `category_id` BIGINT UNSIGNED NOT NULL,
    KEY `attribute_category` (`category_id`),
    CONSTRAINT `attribute_category_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
    CONSTRAINT uq_attribute_type UNIQUE (title, category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `entity_attribute_values` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `entity_id` BIGINT UNSIGNED NOT NULL,
    `attribute_id` BIGINT UNSIGNED NOT NULL,
    `value` TEXT NOT NULL,
    KEY `entity_eav` (`entity_id`),
    CONSTRAINT `entity_eav_fk` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE,
    KEY `attribute_eav` (`attribute_id`),
    CONSTRAINT `attribute_eav_fk` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE,
    CONSTRAINT uq_entity_attribute UNIQUE (entity_id, attribute_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_attributes_category_id ON attributes(category_id);

ALTER TABLE `entity_attribute_values`
ADD INDEX `idx_attribute_value` (`attribute_id`, `value`(100));

INSERT INTO `categories` (`title`, `php_type`)
VALUES
    ('Boolean', 'bool'),
    ('Integer', 'int'),
    ('Float', 'float'),
    ('String', 'string'),
    ('Text', 'string'),
    ('Array', 'array'),
    ('Date', 'Date'),
    ('DateTime', 'DateTime'),
    ('Json', 'json');

INSERT INTO `entities` (`title`)
VALUES
    ('Terminator 2. Judgment day'),
    ('Forrest Gump'),
    ('Amelie');

INSERT INTO `attributes` (`title`, `category_id`)
VALUES
    ('description', 5),
    ('release date', 7),
    ('recency url', 4),
    ('ratio', 3),
    ('director', 4),
    ('revenue', 3),
    ('schedule', 7);

INSERT INTO `entity_attribute_values` (`entity_id`, `attribute_id`, `value`)
VALUES
    (1, 1, 'When John, Sarahs 10-year-old son is attacked by T-1000, a new robot created by Skynet to destroy humanity, Terminator takes it upon himself to fight T-1000 to save John and the human race.'),
    (2, 1, 'Forrest, a man with low IQ, recounts the early years of his life when he found himself in the middle of key historical events. All he wants now is to be reunited with his childhood sweetheart, Jenny.'),
    (3, 1, 'Despite being caught in her imaginative world, Amelie, a young waitress, decides to help people find happiness. Her quest to spread joy leads her on a journey where she finds true love.'),
    (1, 2, '1991-12-25'),
    (2, 2, '1994-07-06'),
    (3, 2, '2001-09-20'),
    (1, 3, 'https://www.rottentomatoes.com/m/terminator_2_judgment_day'),
    (2, 3, 'https://www.rottentomatoes.com/m/forrest_gump'),
    (3, 3, 'https://www.rottentomatoes.com/m/amelie');

INSERT INTO `entity_attribute_values` (`entity_id`, `attribute_id`, `value`)
VALUES
    (3, 4, 9.8);

INSERT INTO `entity_attribute_values` (`entity_id`, `attribute_id`, `value`)
VALUES
    (2, 5, '');


DELIMITER $$
CREATE PROCEDURE `AddAttributeToAllEntities`(
    IN p_title VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_category_id BIGINT UNSIGNED,
    IN p_default_value TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_attribute_id BIGINT UNSIGNED;
    DECLARE v_exists INT DEFAULT 0;

    -- Search if attribute already exists
SELECT `id` INTO v_attribute_id
FROM `attributes`
WHERE `title` = p_title
    LIMIT 1;

-- If attribute not found, insert new one
IF v_attribute_id IS NULL THEN
        INSERT INTO `attributes` (`title`, `category_id`)
        VALUES (p_title, p_category_id);

        -- Get the new attribute ID
        SET v_attribute_id = LAST_INSERT_ID();

SELECT CONCAT('Created new attribute: ', p_title, ' (ID: ', v_attribute_id, ')') AS message;
ELSE
SELECT CONCAT('Found existing attribute: ', p_title, ' (ID: ', v_attribute_id, ')') AS message;
END IF;

    -- Insert values for all entities (with duplicate handling)
INSERT INTO `entity_attribute_values` (`entity_id`, `attribute_id`, `value`)
SELECT `id`, v_attribute_id, p_default_value
FROM `entities`
    ON DUPLICATE KEY UPDATE `value` = p_default_value;

-- Return summary
SELECT
    v_attribute_id AS attribute_id,
    p_title AS attribute_title,
    COUNT(DISTINCT e.id) AS entities_updated,
    ROW_COUNT() AS rows_affected
FROM `entities` e;
END$$

DELIMITER ;


CREATE OR REPLACE VIEW `movie_details` AS
SELECT
    e.id,
    e.title,
    MAX(CASE WHEN a.title = 'description' THEN eav.value END) AS description,
    MAX(CASE WHEN a.title = 'release date' THEN eav.value END) AS release_date,
    MAX(CASE WHEN a.title = 'recency url' THEN eav.value END) AS url,
    MAX(CASE WHEN a.title = 'ratio' THEN eav.value END) AS ratio,
    MAX(CASE WHEN a.title = 'schedule' THEN eav.value END) AS schedule,
    MAX(CASE WHEN a.title = 'director' THEN eav.value END) AS director,
    MAX(CASE WHEN a.title = 'revenue' THEN eav.value END) AS revenue
FROM entities e
         INNER JOIN entity_attribute_values eav ON e.id = eav.entity_id
         INNER JOIN attributes a ON eav.attribute_id = a.id
GROUP BY e.id, e.title;
