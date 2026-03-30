FROM php:8.4.15-fpm-alpine

# Установка зависимостей для сборки и расширения phpredis
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2.9.5 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
