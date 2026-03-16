FROM php:8.4-cli

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock* /app/
RUN composer install

CMD ["tail", "-f", "/dev/null"]