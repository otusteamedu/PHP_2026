# docker/Dockerfile.php
FROM php:8.4.18-fpm-alpine

# Набор системного ПО для сборки расширений
RUN apk add --no-cache \
    icu-dev libzip-dev libpng-dev oniguruma-dev $PHPIZE_DEPS

# Установка расширений (mysqli, pdo_mysql, mbstring и др.)
RUN docker-php-ext-install \
    mysqli pdo_mysql mbstring opcache bcmath zip intl gd

# Фиксированная версия pecl-redis
RUN pecl install redis-6.1.0 && docker-php-ext-enable redis

# Redis
RUN printf "session.save_handler = rediscluster\nsession.save_path = \"seed[]=redis-1:6379&seed[]=redis-2:6379&seed[]=redis-3:6379&auth=\${REDIS_PASSWORD}\"" \
    > /usr/local/etc/php/conf.d/docker-php-ext-redis-sessions.ini

# Composer 2.9.5 (Multi-stage build)
COPY --from=composer:2.9.5 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
USER www-data
