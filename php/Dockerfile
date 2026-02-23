FROM php:8.3-fpm

RUN apt-get update \
    && apt-get install -y git unzip libzip-dev libmemcached-dev zlib1g-dev libssl-dev libxml2-dev --no-install-recommends \
    && pecl install redis memcached \
    && docker-php-ext-enable redis memcached \
    && docker-php-ext-install pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

WORKDIR /var/www/html

EXPOSE 9000
