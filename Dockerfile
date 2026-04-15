# Используем точную версию
FROM php:8.4.18-fpm-alpine

# Устанавливаем необходимые системные зависимости и расширения
RUN apk add --no-cache \
    bash \
    icu-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo_mysql \
    intl \
    zip

# Фиксируем Composer 2.9.5
COPY --from=composer:2.9.5 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
