# Проектирование БД кинотеатра

1. cp .env.example .env
```bash
docker-compose up -d
```

2. БД будет доступна на localhost:5432

3. SQL-скрипты монтируются в `/docker-entrypoint-initdb.d/` и выполняются автоматически при первом запуске контейнера.

4. Для ручного запуска скриптов:

5. Скрипт создания бд:
```bash
source .env && docker exec -i postgres psql -U $POSTGRES_USER -d $POSTGRES_DB < sql/cinema_db.sql
```

6. Скрипт заполнения данными бд:
```bash
source .env && docker exec -i postgres psql -U $POSTGRES_USER -d $POSTGRES_DB < sql/seeds.sql
```
7. Запрос для нахождения самого прибыльного фильма в файле get_most_profitable_movie.sql
8. Логическая модель данных в файле er_diagram.md