FROM php:8.4.15-fpm-alpine

RUN apk add --no-cache $PHPIZE_DEPS openssl-dev libssh2-dev \
    && pecl channel-update pecl.php.net \
    && pecl install redis mongodb-1.20.1 \
    && docker-php-ext-enable redis mongodb \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2.9.5 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
