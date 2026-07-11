FROM php:8.3.4-fpm AS base

WORKDIR /var/www/app

RUN apt-get update && apt-get install -y \
    curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    libc6 zip unzip libpq-dev libmemcached-dev zlib1g-dev libssl-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip sockets
RUN pecl install redis memcached && docker-php-ext-enable redis memcached

COPY --from=composer:2.5.8 /usr/bin/composer /usr/bin/composer

FROM base AS dev

ARG user
ARG uid

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN if ! id "$user" >/dev/null 2>&1; then \
    useradd -G www-data,root -u $uid -d /home/$user $user; \
    fi && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

COPY ./docker/php/config/php-dev.ini /usr/local/etc/php/conf.d/php.ini
COPY ./docker/php/config/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

USER $user
CMD ["php-fpm"]

# ==========================================
FROM base AS prod

COPY ./docker/php/config/php-prod.ini /usr/local/etc/php/conf.d/php.ini
COPY ./docker/php/config/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

COPY . /var/www/app

RUN composer install --no-interaction --no-dev --optimize-autoloader

RUN chmod +x bin/console

USER www-data
CMD ["php-fpm"]