# Приложение обработки отложенных запросов

1. Инициализация
   ```bash
   make init
   ```
2. В терминале запустить consumer
   ```bash
   make consume
   ```

3. Отправить сообщение в брокера можно через форму на http://localhost

4. Результат обработки отправляется 2 способами:
   1. Email — письмо попадает в Mailpit: http://localhost:8025
   2. Telegram — опционально, если настроить бота, сообщение приходит в указанный в форме чат.
      - нужно указать `TELEGRAM_BOT_TOKEN` в `.env.local`
      - Пересобрать контейнеры
      ```bash
        make docker-up
      ```

---
Админка RabbitMQ — http://localhost:15672 (guest / guest)
