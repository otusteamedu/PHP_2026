FROM php:8.4.4-fpm-alpine3.21

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    libmemcached-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zlib-dev \
    linux-headers \
    nano \
    shadow && usermod -u 1000 www-data \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    gd \
    zip \
    bcmath \
    sockets \
    && pecl install redis xdebug memcached \
    && docker-php-ext-enable redis xdebug memcached

WORKDIR /var/www/html

CMD ["php-fpm"]

# Файлы конфигурации в контейнере /usr/local/etc/php
# /usr/local/etc - php-fpm.conf
# /usr/local/etc/php-fpm.d/www.conf - конфигурация воркеров:
#    настраивается www-data,
#    9000 fpm port,
#    /path/to/unix/socket
#    pm mode static | dinamic | ondemand
#    max-childrens
#    start-servers
#    max-requests после котороро
