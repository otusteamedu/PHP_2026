# HW21 — Скрипт деплоя

## Цель

Научиться доставлять приложение до указанной среды: реализовать автоматическую выкатку мини-приложения на собственный виртуальный сервер с помощью инструмента CI/CD.

## Что сделано

Реализован автоматический деплой PHP-приложения (Slim) на VPS с использованием:

- **GitLab CI/CD** — пайплайн с джобами `deploy` и `rollback`
- **Docker Compose** — инфраструктура на сервере (PHP-FPM, Nginx, PostgreSQL, RabbitMQ, HAProxy)
- **Blue/Green deployment** — бесшовная смена активной версии без простоя

## Архитектура

Трафик идёт через HAProxy (`gateway`) на порт `80`. За ним работают два независимых окружения — **blue** и **green**. Каждое включает:

- PHP-FPM-контейнер с кодом релиза
- Nginx (проксирует запросы в PHP-FPM)
- воркеры RabbitMQ (`consumer_new`, `consumer_processing`)

Общие сервисы (запускаются изначально руками до деплоя приложения):

- PostgreSQL
- RabbitMQ

HAProxy проверяет здоровье бэкендов через `GET /up.php` и переключает трафик runtime-командами через admin socket (`ready` / `maint`).

```
Клиент → HAProxy (:80)
            ├─ blue  (Nginx → PHP-FPM + consumers)
            └─ green (Nginx → PHP-FPM + consumers)
         PostgreSQL / RabbitMQ
```

## Структура репозитория

```
project/
├── app/                  # Приложение из предыдущего ДЗ
└── deploy/               # инфраструктура на сервере
    ├── docker-compose.yml
    ├── docker/           # образ PHP-FPM
    ├── haproxy/          # gateway
    ├── nginx/            # конфиги blue / green
    ├── postgres/         # init.sql
    ├── scripts/          # deploy / rollback
    └── releases/         # клоны кода (blue / green)
```

На сервере каталог `deploy/` — рабочая директория деплоя (`DEPLOY_DIR`).

## CI/CD (GitLab)

Файл: `app/.gitlab-ci.yml`

| Джоба      | Когда              | Действие                                      |
|------------|--------------------|-----------------------------------------------|
| `deploy`   | push в `main`      | запускает `deploy/scripts/deploy.sh`          |
| `rollback` | вручную (`manual`) | запускает `deploy/scripts/rollback.sh`        |

### Deploy

1. Определяется текущий активный цвет (если запущен `blue` → новый релиз в `green`, и наоборот).
2. Код клонируется в `releases/$CURRENT`, подкладывается `.env`.
3. Поднимаются контейнеры нового релиза, выполняется `composer install`.
4. Новый бэкенд включается в HAProxy (`state ready`).
5. Старый бэкенд переводится в `maint` и останавливается.

### Rollback

Обратная операция: предыдущий цвет поднимается, текущий выводится из балансировки и останавливается.

## Стек приложения

- PHP 8.2 + Slim 4
- PostgreSQL 16
- RabbitMQ 3.13
- Nginx, HAProxy
- Docker / Docker Compose
