# Задание 10: IS/hw10

## Общее описание

```
Поиск по книжному интернет-магазину с помощью Elasticsearch
```

## Запуск

```bash
# Старт сервисов (Elasticsearch, Kibana, PHP)
docker compose up --build -d

# Установить зависимости
docker compose exec hw10-php-cli composer install

# Создание индекса
docker compose exec hw10-php-cli php app/Scripts/ScriptCreateIndex.php --index="example"

# Удаление индекса
docker compose exec hw10-php-cli php app/Scripts/ScriptDeleteIndex.php --index="example"

# Загрузка данных
docker compose exec hw10-php-cli php app/Scripts/ScriptSeed.php --index="example"

# Поиск=
docker compose exec hw10-php-cli php app/Scripts/ScriptSearch.php --index="example" --search="рыцори"
docker compose exec hw10-php-cli php app/Scripts/ScriptSearch.php --index="example" --search="рыцари"
docker compose exec hw10-php-cli php app/Scripts/ScriptSearch.php --index="example" --search="фантастыка"
```

## Результаты

```
1) "рыцори"
            Название             |  Категория  |    Цена     | На складе 
------------------------------------------------------------------------
Рыцари круглова стола            | Фантастика  | 2500 руб.   | 3

2) "рыцари"
            Название             |  Категория  |    Цена     | На складе
------------------------------------------------------------------------
Рыцари круглова стола            | Фантастика  | 2500 руб.   | 3

3) "фантастыка"
            Название             |  Категория  |    Цена     | На складе
------------------------------------------------------------------------
Космические путешествия          | Фантастика  | 1250 руб.   | 15
Рыцари круглова стола            | Фантастика  | 2500 руб.   | 3

```