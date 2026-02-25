FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    git

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]