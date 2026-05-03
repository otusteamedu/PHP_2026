FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    && docker-php-ext-install zip

RUN pecl install redis \
    && docker-php-ext-enable redis

WORKDIR /var/www/html