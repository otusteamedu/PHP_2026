# Задание 5: IS/hw5

## Общее описание

```
Приложение верификации email
```

## Инфраструктура

### Схема развёртывания

Развёрнута следующая инфраструктура:

1. **Виртуальная машина**  
   - ОС: Ubuntu  
   - Среда: Oracle VirtualBox

2. **Nginx**  
   - Принимает запросы

3. **Контейнер с `php-fpm`**  
   - Имеет доступ к общей папке с проектом на локальной машине (через монтирование).

## Запуск приложения
```bash
cd /путь/к/вашему/проекту
docker compose up --build -d
docker compose exec php-fpm composer install
```

## Примеры запросов
```sh
curl --request POST \
  --url http://localhost/api/email-verify \
  --header 'Content-Type: application/json' \
  --data '{
	"email": "example@yandex.ru"
}'
```

```sh
curl --request POST \
  --url http://localhost/api/email-verify \
  --header 'Content-Type: application/json' \
  --data '{
	"emails": [
		"example1@yandex.ru",
		"example2@yandex.ruu"
	]
}'
```