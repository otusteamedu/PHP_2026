# AErmolenko/hw10 - ElasticSearch

## Запуск

```bash
# Создать .env файл на основе .env.example
cp .env.example .env
# Поднять все сервисы (Elasticsearch, Kibana, PHP)
docker compose up -d --build

# Установить зависимости
docker compose exec app composer install

# Загрузить данные в индекс
docker compose exec app php app/import_data.php

# Поиск
docker compose exec app php app/cli.php --query="рыцОри" --max-price=2000 --min-stock=14
```

## Сервисы

| Сервис        | Адрес                   |
|---------------|-------------------------|
| Elasticsearch | http://localhost:9200   |
| Kibana        | http://localhost:5601   |


## Структура проекта

```
.
├── docker-compose.yml
├── docker/
│   └── Dockerfile
├── composer.json
├── .env
├── app/
│   ├── cli.php                # точка входа CLI
│   ├── import_data.php        # загрузка данных в индекс
│   ├── Search/
│   │   └── BookRepository.php # слой работы с ES
│   └── Formatter/
│       └── TableFormatter.php # вывод таблицы в консоль
└── data/
    └── books.json             # каталог книг
```