# AErmolenko/hw12 - Паттерны работы с данными

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

- `GET /api/movies` - список всех фильмов
- `GET /api/movies/{id}` - получить фильм по ID
- `POST /api/movies` - добавить фильм
- `PUT /api/movies/{id}` - обновить фильм
- `DELETE /api/movies/{id}` - удалить фильм

## Примеры

```bash
# Получить все фильмы
curl http://localhost/api/movies

# Получить фильм по ID
curl http://localhost/api/movies/1

# Добавить фильм
curl -X POST http://localhost/api/movies \
  -H 'Content-Type: application/json' \
  -d '{"title":"movie 1","year":2010,"genre":"Sci-Fi","director":"Ivan Ivanov"}'

# Обновить фильм
curl -X PUT http://localhost/api/movies/1 \
  -H 'Content-Type: application/json' \
  -d '{"genre":"Thriller"}'

# Удалить фильм
curl -X DELETE http://localhost/api/movies/1
```
