CREATE TABLE IF NOT EXISTS `otus.users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `otus.users` (`email`, `password`) VALUES
    ('admin@example.com', '$2y$10$QBiuqPfPC/VBHxmqoX.z5u7GhrQmBy0ikBB0iJkrHyceiI80pv2sC'),
    ('user@example.com', '$2y$10$QBiuqPfPC/VBHxmqoX.z5u7GhrQmBy0ikBB0iJkrHyceiI80pv2sC');
