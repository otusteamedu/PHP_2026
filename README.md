# Задание 11: IS/hw11

## Общее описание

```
Научиться взаимодействовать с Redis.
```

## Запуск

```bash
# Старт сервисов (Elasticsearch, Kibana, PHP)
docker compose up --build -d

# Установить зависимости
docker compose exec php-fpm composer install

```

## Примеры запросов

```shell
# Добавление эвента
curl --request POST \
  --url http://localhost:80/api/v1/events/add \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
	"priority": 2500,
	"conditions": {
		"param1": 1
	},
	"event": {
		"title": "event 5"
	}
}'
```

```shell
# Поиск по параметрам
curl --request POST \
  --url http://localhost:80/api/v1/events/best-matching-event \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
	"params": {
		"param1": 1
	}
}'
```

```shell
# Очистка
curl --request POST \
  --url http://localhost:80/api/v1/events/clear \
  --header 'Accept: application/json'
```

## Структура проекта

```
.
├── docker-compose.yml                           # конфигурация Docker Compose (запуск Nginx, PHP-FPM, Redis)
├── docker/                                      # директория с Docker-файлами для сервисов
│   ├── nginx/
│   │   ├── Dockerfile                          # файл сборки образа Nginx
│   │   └── nginx.conf                          # конфигурация Nginx
│   ├── php-fpm/
│   │   └── Dockerfile                          # файл сборки образа PHP-FPM
│   └── redis/
│       ├── Dockerfile                          # файл сборки образа Redis
│       └── redis.conf                          # конфигурация Redis
├── composer.json                               # описание зависимостей и скриптов проекта
├── composer.lock                               # зафиксированные версии зависимостей
├── .env                                        # файл с переменными окружения
├── .gitignore                                  # список игнорируемых файлов для Git
├── app/                                        # основная логика приложения
│   ├── Controllers/Api/
│   │   └── EventController.php                 # контроллер для API-запросов (работа с событиями)
│   ├── Core/
│   │   ├── Kernel.php                          # ядро приложения (инициализация)
│   │   ├── Request.php                         # работа с HTTP-запросами
│   │   └── Response.php                        # формирование HTTP-ответов
│   ├── Interfaces/
│   │   └── StorageInterface.php                # интерфейс для работы с хранилищем данных
│   ├── Models/
│   │   └── Event.php                           # модель для работы с событиями
│   ├── Repositories/
│   │   └── EventRepository.php                 # репозиторий для работы с событиями
│   └── Storages/
│       └── RedisStorage.php                    # реализация хранилища на основе Redis
├── public/                                        # веб-ресурсы (доступны через веб-сервер)
│   └── index.php                               # точка входа в приложение
│── routes/
│       └── api.php                             # маршруты для API
├── data/                                        # директория для данных
├── vendor/                                      # зависимости (установленные через Composer)
└── README.md                                    # документация по проекту
```
