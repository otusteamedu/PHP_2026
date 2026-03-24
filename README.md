# Cinema Database Infrastructure

## 🛡 Security Policy
- **Network Isolation**: Секция `ports` запрещена. Доступ к БД осуществляется только через внутреннюю сеть Docker или SSH-туннель к Ubuntu VM (порт 5432).
- **Environment**: Любые пароли хранятся строго в `.env`.

## 📂 Project Structure

```text
cinema-db-project/
├── .env                       # Локальные секреты
├── .env.example               # Шаблон конфигурации
├── .gitignore                 # Защита от утечек
├── analytics.sql              # SQL-запрос для расчета прибыли
├── docker-compose.yml         # Контейнер PostgreSQL (No Ports)
├── init.sql                   # Схема БД (DDL) и тестовые данные
├── README.md                  # Технический паспорт
├── start.sh                   # Автоматизация деплоя
├── run_analytics.sh           # Запуск отчетов одной кнопкой
└── cleanup.sh                 # Полная очистка данных
```

## 🚀 Operations
Подготовка:
1. Создать локальный конфиг: cp .env.example .env
2. Выдать права скриптам: chmod +x *.sh

Запуск системы:
1. Deployment: ./start.sh
2. Analytics:  ./run_analytics.sh
3. Cleanup:    ./cleanup.sh

## 📊 Database Schema (ER Diagram)

```mermaid
erDiagram
    MOVIES ||--o{ SCREENINGS : "has"
    MOVIES ||--o{ MOVIE_ATTRIBUTE_VALUES : "has"
    ATTRIBUTES ||--o{ MOVIE_ATTRIBUTE_VALUES : "defines"
    ATTRIBUTE_TYPES ||--o{ ATTRIBUTES : "categorizes"
    CINEMAS ||--|{ HALLS : "contains"
    HALLS ||--|{ SEATS_BLUEPRINT : "defined_by"
    HALLS ||--o{ SCREENINGS : "hosts"
    SCREENINGS ||--o{ TICKETS : "sold_for"
    SCREENINGS ||--|{ TICKET_PRICES : "has_pricing"
    CUSTOMERS ||--o{ BOOKINGS : "makes"
    BOOKINGS ||--|{ TICKETS : "contains"
    SEATS_BLUEPRINT ||--o{ TICKETS : "assigned_to"

    MOVIES {
        int id PK
        string title
        int duration_minutes
        date release_date
    }

    CINEMAS {
        int id PK
        string name
        string city
    }

    HALLS {
        int id PK
        int cinema_id FK
        string name
        int capacity
    }

    SEATS_BLUEPRINT {
        int id PK
        int hall_id FK
        string seat_row
        int seat_number
        string seat_type
    }

    SCREENINGS {
        int id PK
        int movie_id FK
        int hall_id FK
        datetime start_time
		datetime end_time
        decimal base_price
    }

    TICKET_PRICES {
        int id PK
        int screening_id FK
        string seat_type
        decimal price_modifier
    }

    CUSTOMERS {
        int id PK
        string name
        string email
    }

    BOOKINGS {
        int id PK
        int customer_id FK
        datetime booking_time
        decimal total_amount
    }

    TICKETS {
        int id PK
        int booking_id FK
        int screening_id FK
        int seat_blueprint_id FK
        decimal actual_price
    }

    ATTRIBUTE_TYPES {
        int id PK
        string type_name
    }
    ATTRIBUTES {
        int id PK
        int type_id FK
        string name
    }
    MOVIE_ATTRIBUTE_VALUES {
        int id PK
        int movie_id FK
        int attr_id FK
        string val_text
        boolean val_boolean
        date val_date
        decimal val_numeric
    }
```

## 📊 Database Management (Manual)
Если требуется выполнить скрипты вручную без использования bash-оберток:


# Запустить контейнер
```bash
docker compose up -d
```

# Импорт схемы
```bash
docker exec -i postgres_cinema psql -U ${DB_USER} -d ${DB_NAME} < init.sql
```

# Выполнение аналитического запроса
```bash
docker exec -i postgres_cinema psql -U ${DB_USER} -d ${DB_NAME} < analytics.sql
```

## 🛠 Проверка ДЗ №8 (EAV)
Реализация модели EAV (таблицы, данные и VIEW) интегрирована непосредственно в основной файл инициализации `init.sql`.

Для проверки гибкой схемы атрибутов и корректности сборки данных выполнить:
1. **Маркетинговые данные** (фильм, тип, атрибут, значение):
```bash
docker exec -it postgres_cinema psql -U evgeny87_user -d cinema_db -c "SELECT * FROM cinema.v_marketing_data;"
```
2. **Служебные задачи** (актуально сегодня и через 20 дней):
```bash
docker exec -it postgres_cinema psql -U evgeny87_user -d cinema_db -c "SELECT * FROM cinema.v_service_tasks;"
```
