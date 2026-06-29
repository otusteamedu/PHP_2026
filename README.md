# AErmolenko/hw21

REST API на Symfony: клиент отправляет запрос на обработку, получает его номер,
обработка идёт в фоне через очередь RabbitMQ. Клиент
периодически проверяет статус по номеру.

## Стек

- PHP 8.4, Symfony 6.4
- RabbitMQ
- Postgres
- nginx + php-fpm
- Swagger (nelmio/api-doc-bundle)
- Docker Compose

## Запуск

```bash
docker compose up -d --build
```

Миграции применяются автоматически при старте контейнера php.

Сервисы:
- API: http://localhost:8080
- Swagger UI: http://localhost:8080/api/doc
- OpenAPI JSON: http://localhost:8080/api/doc.json
- RabbitMQ UI: http://localhost:15672 (user / user)

## API

Все эндпоинты версионированы под `/api/v1`.

### `POST /api/v1/requests` — создать запрос

```bash
curl -X POST http://localhost:8080/api/v1/requests \
  -H 'Content-Type: application/json' \
  -d '{"payload":{"a":10,"b":20,"c":5}}'
```

```json
{ "id": 1, "status": "pending" }
```

### `GET /api/v1/requests` — список всех запросов (новые сверху)

```bash
curl "http://localhost:8080/api/v1/requests?limit=50&offset=0"
```
Параметры `limit` (1..200, по умолчанию 50) и `offset` — пагинация.

### `GET /api/v1/requests/{id}` — проверить статус

```bash
curl http://localhost:8080/api/v1/requests/1
```

```json
{
  "id": 1,
  "status": "done",
  "result": { "processed": true, "sum": 35, "keys": ["a", "b", "c"] },
  "createdAt": "2026-06-21T16:54:45+00:00",
  "updatedAt": "2026-06-21T16:54:48+00:00"
}
```

Статусы: `pending` → `processing` → `done` (или `failed`).

### `DELETE /api/v1/requests/{id}` — удалить запрос

```bash
curl -X DELETE http://localhost:8080/api/v1/requests/14 -i
```

- **204** — запись удалена (тело ответа пустое)
- **404** — `{"error": "not found"}`

## Как работает

1. `POST /api/v1/requests` сохраняет запись (`status=pending`), кладёт
   `ProcessRequestMessage` в очередь RabbitMQ и сразу возвращает `id`.
2. Контейнер `worker` (`messenger:consume async`) забирает сообщение,
   переводит в `processing`, выполняет работу, пишет `result`, ставит `done`.
3. Клиент опрашивает `GET /api/v1/requests/{id}` до появления результата.

```bash
docker compose logs -f worker # логи обработчика очереди
docker compose down -v # остановить и удалить данные
```

## Деплой

Автоматическая выкатка на VPS через GitHub Actions + `deploy.sh`.

### Как работает

1. Push в `main` (или ручной запуск `workflow_dispatch`) запускает
   workflow `.github/workflows/deploy.yml`.
2. **build**: собираются два образа и пушатся в GHCR:
   - `app` (`docker/php/Dockerfile.prod`) — php-fpm + worker, prod-зависимости;
   - `web` (`docker/nginx/Dockerfile.prod`) — nginx с вшитым `public/`.
   Тег образа — `:<commit-sha>` + `:latest`.
3. **deploy**: файлы (`docker-compose.prod.yml`, `deploy/`) копируются на сервер по
   SSH, затем удалённо запускается `deploy/deploy.sh`.

### `deploy.sh` (запускается на сервере)

```bash
./deploy/deploy.sh
```

Шаги:
1. рендерит `.env.deploy` из переменных окружения (секреты/настройки приходят из
   GitHub Actions);
2. `docker compose up -d --pull always` — тянет новые образы и поднимает сервисы;
3. прогоняет миграции одним запуском (`AUTO_MIGRATE=0` в контейнерах — реплики
   не гонятся за блокировку).

### Следует настроить в GitHub

Secrets: `SSH_HOST`, `SSH_USER`, `SSH_PRIVATE_KEY`, `APP_SECRET`,
`POSTGRES_PASSWORD`, `RABBITMQ_PASSWORD`, `POSTGRES_DB`, `POSTGRES_USER`, `RABBITMQ_USER`, `HTTP_PORT`.