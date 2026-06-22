FROM php:8.4-fpm-alpine

# System deps + build deps for PHP extensions
RUN apk add --no-cache \
        git unzip icu-dev postgresql-dev rabbitmq-c-dev \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && docker-php-ext-install -j$(nproc) pdo_pgsql intl opcache \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && apk del .build-deps

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/app

# Install dependencies first (better layer caching)
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-scripts --no-interaction --prefer-dist --no-progress

COPY . .

RUN composer dump-autoload --optimize \
    && mkdir -p var && chown -R www-data:www-data var

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]