# AErmolenko/hw8
# Задача
Спроектировать EAV-хранение для базы данных кинотеатра
## Запуск
```bash
docker compose build
docker compose up -d
```

## Пример запроса
```sql
SELECT
    m.title,
    json_object_agg(
            a.name,
            COALESCE(
                    av.value_text,
                    to_char(av.value_date, 'YYYY-MM-DD'),
                    av.value_boolean::text,
                    av.value_numeric::text,
                    to_char(av.value_timestamp AT TIME ZONE 'UTC', 'YYYY-MM-DD HH24:MI')
            )
    ) AS attributes
FROM movie m
         JOIN attribute_values av ON av.movie_id = m.id
         JOIN attributes a ON a.id = av.attribute_id
GROUP BY m.id, m.title
ORDER BY m.title;
```
