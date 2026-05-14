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


• Сделал правки в коде по анализу из docs/code-analysis.md, также добавил uml-схемы до изменений uml-before.puml и после uml-after.puml:

- добавил Container вместо StorageFactory;
- заменил MovieStorage на MovieRepositoryInterface + PdoMovieRepository;
- добавил MovieService;
- вынес парсинг JSON в app/Core/Http/Request.php;
- добавил DTO: CreateMovieRequest, UpdateMovieRequest;
- усилил app/Entity/Movie.php: фабричные методы, валидация title/year/id, доменные методы rename, changeYear, changeGenre, changeDirector;
- обновил app/Core/Http/Controller/MovieController.php, теперь он тоньше и работает через сервис;
- MovieMapper теперь получает IdentityMap через DI;
- поправил public/index.php, чтобы аварийный JsonResponse реально отправлялся.
