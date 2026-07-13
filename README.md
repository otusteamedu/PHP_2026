# API

## Развёртывание

1. Создайте файл окружения:

```bash
cp .env.example .env
```

2. Установка звисимостей:

```bash
docker compose run --rm php composer install
```

3. Запустите приложение:

```bash
docker compose up -d --build
```

После запуска доступны:

- API — http://localhost:80/api/statements и http://localhost:80/api/statements/{id}
- RabbitMQ Management — http://localhost:15672 (логин и пароль из `.env`)
- Документация (Swagger) - http://localhost/docs
- Документация (OpenAPI)- http://localhost/openapi

Запрос к API:

```bash
curl -X POST http://localhost/api/report-request \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","from":"2026-01-01","to":"2026-01-31"}'
```

Сервис `consumer_new` и `consumer_processing` обрабатывает очередь и отправляет письма в фоне. Один забирает новые обращения и переводит их в статус обработки, другой забирает со статусом в обработке и пеперводит в статус выполнено.

Остановка:

```bash
docker compose down
```
