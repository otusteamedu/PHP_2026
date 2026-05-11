FROM php:8.4-fpm-alpine

WORKDIR /app

# Keep image lean and provide common extensions for web apps.
RUN set -eux; \
    apk add --no-cache icu-dev oniguruma-dev libzip-dev; \
    docker-php-ext-install -j"$(nproc)" intl mbstring pdo pdo_mysql zip
