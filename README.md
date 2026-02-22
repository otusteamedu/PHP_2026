# Bracket Validator Cluster (Zero-Trust Edition)

Инфраструктура для валидации скобочных последовательностей с балансировкой нагрузки и Redis-кластером.

## Архитектура
- **PHP 8.4.18-fpm**: Strict typing, использование исключений вместо `die/@`.
- **Nginx LB**: Балансировщик (Round Robin) на 3 ноды приложения.
- **Redis 7.4.2 Cluster**: Распределенное хранилище сессий (3 Master-узла).
- **Network**: Изолированная сеть Docker, доступ только через Host-only IP.

## Требования
- Docker & Docker Compose
- VirtualBox (с настроенным Host-only адаптером)
- Настроенный `.env` (см. `.env.example`)

## Развертывание
1. Поднять контейнеры:
   \`\`\`bash
   docker compose up -d --build
   \`\`\`

2. Установить зависимости:
   \`\`\`bash
   docker compose exec -u www-data php composer install
   \`\`\`

3. Инициализировать Redis Cluster:
   \`\`\`bash
   docker compose exec redis-1 sh -c 'redis-cli -a "\$REDIS_PASSWORD" --cluster create \$(getent hosts redis-1 | awk "{print \$1}"):6379 \$(getent hosts redis-2 | awk "{print \$1}"):6379 \$(getent hosts redis-3 | awk "{print \$1}"):6379 --cluster-replicas 0 --cluster-yes'
   \`\`\`

## Тестирование
Отправка POST-запроса на Host-only IP (например, 192.168.56.101):
\`\`\`bash
# Валидная строка (200 OK)
curl -X POST -d "string=(())" http://192.168.56.101

# Невалидная строка (400 Bad Request)
curl -X POST -d "string=)(" http://192.168.56.101
\`\`\`

## Критерии оценки (Compliance)
- [x] Любая длина и сложность строки.
- [x] Кейс ")(" возвращает 400 Bad Request.
- [x] Использование исключений (Exception) для обработки ошибок.
- [x] Сессии синхронизированы через Redis Cluster.

