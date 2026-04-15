-- Отключаем проверку внешних ключей, чтобы очистить связанные таблицы
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE posts;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- Создаем структуру (на случай если её нет)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Наполняем данными
INSERT INTO users (id, name, email) VALUES (1, 'Evgeny', 'evgeny@otus.ru');
INSERT INTO users (id, name, email) VALUES (2, 'Ivan', 'ivan@otus.ru');

INSERT INTO posts (user_id, title) VALUES (1, 'Первый пост Евгения');
INSERT INTO posts (user_id, title) VALUES (1, 'Второй пост Евгения');
INSERT INTO posts (user_id, title) VALUES (2, 'Заметка Ивана');
