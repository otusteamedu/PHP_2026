## Домашнее задание 4

### Реализован балансировщик на базе NGINX

    NGINX --- NGINX -\-/- PHP-FPM --- REDIS 
          \__ NGINX -/-\- PHP-FPM __/  

Каждый внутренний NGINX может работать с любым из PHP-FPM.
Хранение сессий происходит в общем Redis.

### В качестве приложения реализована проверка входной строки на соответствие правилам валидации.
Ответ возвращается в json формате с дополнительными параметрами для контроля отработавшего задачу контейнера

```
{
    "status": 200,
    "info": "String check was passed",
    "session_data": "Welcome back! This is visit #44. Session started at container: 9c2cc056f1c9. Current container is: afae7e8d6cf4."
}
```
* Для проверочных запросов из Postman

```shell
curl -X POST 'http://balancer.local:8083' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -d 'string=()()'
```

* Для проверочных запросов из терминала с сохранением cookie
```shell
# Первый запрос: создает сессию и сохраняет cookie
curl -X POST 'http://balancer.local:8083' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -d 'string=()()' \
  -c cookies.txt

# Следующие запросы: используют cookie для верификации файла сессии
curl -X POST 'http://balancer.local:8083' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -d 'string=()()' \
  -b cookies.txt
```

* Доступ к контейнеру REDIS возможен из вне по порту 6389
* Для доступа к информации необходима авторизация

```shell
docker exec -it balancer-redis redis-cli
AUTH oasjw44wcsnjd

KEYS "*"

  или
  
MONITOR
```

