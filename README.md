# AErmolenko/hw9 - Индексирование данных

## Запуск

1. Запустить контейнер (схема и вьюхи буду применены автоматически):
```bash
docker compose up -d
```

2. Загрузить тестовые данные:
```bash
# Малый набор (10000 строк)
docker exec -i hw9-postgres psql -U cinema_user -d cinema_db < sql/seed_data.sql

# Большой набор (10000000 строк, выполняется несколько минут)
docker exec -i hw9-postgres psql -U cinema_user -d cinema_db < sql/seed_data_2.sql
```

3. Выполнить запросы и получить планы выполнения:
```bash
docker exec -i hw9-postgres psql -U cinema_user -d cinema_db < sql/explain_plans.sql > sql/explain_plans_output.txt
```

## Структура проекта

```
sql/
├── cinema_db.sql               # Схема: movie, attributes, attribute_types, attribute_values
├── cinema_operational.sql      # Схема: hall, seat, session, ticket + тестовые данные
├── view_marketing.sql          # Вьюха: все атрибуты фильма в одну строку (для маркетинга)
├── view_service_tasks.sql      # Вьюха: служебные задачи по фильмам (timestamp-атрибуты)
├── seed_data.sql               # Тестовые данные 10000 строк
├── seed_data_2.sql             # Тестовые данные 10000000 строк
├── query_1_movies_today.sql    # Запрос: все фильмы на сегодня
├── query_2_tickets_week.sql    # Запрос: количество проданных билетов за неделю
├── query_3_poster.sql          # Запрос: афиша на сегодня
├── query_4_top3_revenue.sql    # Запрос: топ-3 фильма по выручке за неделю
├── query_5_hall_map.sql        # Запрос: схема зала (свободные/занятые места)
├── query_6_price_range.sql     # Запрос: диапазон цен на конкретный сеанс
├── explain_plans.sql           # EXPLAIN ANALYZE для всех 6 запросов
├── explain_plans_output_1.txt  # Планы на БД 10000 строк
├── explain_plans_output_2.txt  # Планы на БД 10000000 строк (до оптимизаций)
├── explain_plans_output_3.txt  # Планы на БД 10000000 строк (после оптимизаций)
└── optimizations.txt           # Анализ индексов, сводная таблица, оптимизации
```

## Индексирование

Все индексы создаются в `cinema_db.sql` и `cinema_operational.sql`. Обоснование каждого индекса - в комментариях прямо в DDL.
Проблемные запросы на датасете 10M строк и применённые оптимизации - в `optimizations.txt`.