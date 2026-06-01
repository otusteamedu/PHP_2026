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

#### Добавлен `tests/Support/FunctionOverrides.php`
Вспомогательный модуль для тестов. Подменяет глобальные PHP-функции, которые
продакшен-код дергает напрямую, и которые нельзя замокать обычными средствами

### Запуск тестов
Unit и интеграционные тесты:
```bash
docker-compose exec -e XDEBUG_MODE=coverage php-fpm vendor/bin/phpunit --testsuite unit

docker-compose exec -e XDEBUG_MODE=coverage php-fpm vendor/bin/phpunit --testsuite integration
```

Дополнительные функциональные (E2E) тесты:
```bash
docker-compose exec -e FUNCTIONAL_BASE_URL=http://nginx php-fpm vendor/bin/phpunit --testsuite functional
```

Полный проход с отчётом по покрытию:
```bash
docker-compose exec -e XDEBUG_MODE=coverage -e FUNCTIONAL_BASE_URL=http://nginx php-fpm vendor/bin/phpunit --coverage-text
```