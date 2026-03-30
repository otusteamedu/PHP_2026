FROM php:8.3.4-fpm AS base

WORKDIR /var/www/app

# Nginx + Supervisor
RUN apt-get update && apt-get install -y \
    nginx supervisor \
    curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    libc6 zip unzip libpq-dev libmemcached-dev zlib1g-dev libssl-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP
RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip
RUN pecl install redis memcached && docker-php-ext-enable redis memcached

COPY --from=composer:2.5.8 /usr/bin/composer /usr/bin/composer

FROM base AS prod

RUN rm -f /etc/nginx/conf.d/default.conf
RUN rm /etc/nginx/sites-enabled/default

# конфиги
COPY ./docker/app/config/php-prod.ini /usr/local/etc/php/conf.d/app.ini
COPY ./docker/app/config/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/app/config/nginx-backend.conf /etc/nginx/conf.d/default.conf
COPY ./docker/app/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY ./docker/app/config/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY . /var/www/app

RUN composer install --no-interaction --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/app && \
    chmod -R 755 /var/www/app

RUN mkdir -p /run/nginx /var/log/nginx && \
    chown -R www-data:www-data /run/nginx /var/log/nginx /var/lib/nginx

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]