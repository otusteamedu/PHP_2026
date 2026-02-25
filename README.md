## Запуск
```shell
docker-compose up -d
```

## Пример запроса:

### Everything is correct responce:
```shell
curl -i -X POST "http://localhost/validate" \
  -d "string=()()()()()(())"
```

### Brackets is invalid responce:
```shell
curl -i -X POST "http://localhost/validate" \
-d "string=)("
```