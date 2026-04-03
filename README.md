# AErmolenko/hw11 - Redis

## Запуск

```bash
cp .env.example .env
docker compose up --build
```

### Внутри контейнера 
```bash
composer install
```

## Эндпоинты

- `POST /api/events` — добавить событие
- `DELETE /api/events` — очистить все события
- `POST /api/events/match` — найти лучшее совпадение

## Примеры

```bash
# Добавить
curl -X POST http://localhost/api/events \
  -d '{"priority":3000,"conditions":{"param1":"1","param2":"2"},"event":{"type":"sale"}}'

# Найти
curl -X POST http://localhost/api/events/match \
  -d '{"params":{"param1":"1","param2":"2"}}'

# Очистить
curl -X DELETE http://localhost/api/events
```

## Хранилище

Переключается через `STORAGE_DRIVER` в `.env`:
- `redis` (по умолчанию)
- `mongodb`