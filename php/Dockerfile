FROM php:8.5-fpm

# Копируем настройки в папку конфигов php-fpm
#COPY ./docker-php-env.conf /usr/local/etc/php-fpm.d/zz-docker-env.conf

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libssl-dev \
        libmemcached-dev \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip gd \
        && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && pecl install redis && docker-php-ext-enable redis \
        && pecl install memcached && docker-php-ext-enable memcached \
        && docker-php-ext-install pdo pdo_mysql

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

WORKDIR /var/www/html

CMD ["php-fpm"]

