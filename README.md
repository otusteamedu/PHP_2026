# Cinema Database Infrastructure

## 🛡 Security Policy
- **Network Isolation**: Секция `ports` запрещена. Доступ к БД осуществляется только через внутреннюю сеть Docker или SSH-туннель к Ubuntu VM (порт 5432).
- **Environment**: Любые пароли хранятся строго в `.env`.

## 📂 Project Structure
cinema-db-project/
├── .env                  # Локальные секреты
├── .env.example          # Шаблон конфигурации
├── .gitignore            # Защита от утечек
├── analytics.sql         # SQL-запрос для расчета прибыли
├── docker-compose.yml    # Контейнер PostgreSQL (No Ports)
├── init.sql              # Схема БД (DDL) и тестовые данные
├── README.md             # Технический паспорт
├── start.sh              # Автоматизация деплоя
├── run_analytics.sh      # Запуск отчетов одной кнопкой
└── cleanup.sh            # Полная очистка данных

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
```

## 📊 Database Management (Manual)
Если требуется выполнить скрипты вручную без использования bash-оберток:

```bash
# Запустить контейнер
docker compose up -d

# Импорт схемы
docker exec -i postgres_cinema psql -U ${DB_USER} -d ${DB_NAME} < init.sql

# Выполнение аналитического запроса
docker exec -i postgres_cinema psql -U ${DB_USER} -d ${DB_NAME} < analytics.sql
```