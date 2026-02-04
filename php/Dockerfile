FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libssl-dev \
        git \
        unzip \
        libmemcached-dev \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
    && docker-php-ext-configure gd \
        --with-freetype=/usr/include/freetype2 \
        --with-jpeg=/usr/include \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install redis memcached \
    && docker-php-ext-enable redis memcached

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /var/run/php
COPY ./www.conf /usr/local/etc/php-fpm.d/www.conf

RUN usermod -u 1000 www-data 2>/dev/null || true

# Копируем пользовательский php.ini
COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

WORKDIR /var/www/html

CMD ["php-fpm"]