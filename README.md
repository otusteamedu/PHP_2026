# Задание 5: AErmolenko/hw5

## Приложение верификации email

### Запуск приложения
```bash
docker compose up --build -d
docker compose exec php-fpm composer i
```

### Примеры запросов
```sh
curl -i -X POST "http://localhost/api/email-verify" \
  -H 'Content-Type: application/json' \
  -d '{
	"email": "example@mail.ru"
}'
```

```sh
curl -i -X POST "http://localhost/api/email-verify" \
  -H 'Content-Type: application/json' \
  -d '{
	"emails": [
		"example1@mail.ru",
		"example2@mail.ruu"
	]
}'
```