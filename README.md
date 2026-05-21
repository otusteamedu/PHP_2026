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

# Задание 5: IS/hw17

## Установил библиотеку для тестирования phpunit

```sh
composer require phpunit/phpunit
```

## Описание файла (phpunit.xml)

```text
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php"
         colors="true"
         stopOnFailure="false">
    <testsuites>
        <testsuite name="Application Test Suite">
            <directory>tests</directory>
        </testsuite>
    </testsuites>
</phpunit>

```

## Тесты

* Проверка не валидного email - должны получить false;
* Проверка валидного email - должны получить true;
* Проверка не валидных email - должны получить массив который совпадает с ожидаемым;
* Проверка валидных email - должны получить массив который совпадает с ожидаемым;

## Запуск тестов

```sh
./vendor/bin/phpunit
```

## Результат

```text
OK (4 tests, 6 assertions)
```
