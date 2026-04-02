# PHP_2026

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus


# 15. MySQL и форки
## Примеры запросов к MySQL:

```sql

-- Создание таблицы users
CREATE TABLE users
(
    id         INTEGER PRIMARY KEY AUTO_INCREMENT,
    username   VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    comment 'asdsad'
);

-- Создание таблицы messages
CREATE TABLE messages
(
    id         INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id    INTEGER NOT NULL,
    content    TEXT    NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Разница в сукундах между первым созданным сообщением и последнм
SELECT TIMESTAMPDIFF(SECOND,
 (SELECT created_at FROM messages ORDER BY id LIMIT 1),
 (SELECT created_at FROM messages ORDER BY id DESC LIMIT 1)
       ) AS diff_seconds;


-- информация о таблице users
SHOW TABLE STATUS LIKE 'users';

-- информация о схеме таблицы users
SELECT *
FROM information_schema.statistics
WHERE table_name = 'users';

-- информация о запросе
EXPLAIN
SELECT *
FROM users FORCE INDEX (username)
WHERE username LIKE 'user_4%';

-- создание полнотекстового индекса для поля content в таблице messages
ALTER TABLE messages ADD FULLTEXT(content);

-- информация о запросе с полнотекстовым поиском
EXPLAIN
SELECT *
FROM messages
WHERE MATCH(content) AGAINST ('Messag* -user_1' IN BOOLEAN MODE)

