FROM php:8.5-fpm

# ставим необходимые для нормальной работы модули
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libssl-dev \
        libpq-dev \
        zlib1g-dev \
        && pecl install redis && docker-php-ext-enable redis \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql pdo_pgsql zip gd \
        && docker-php-ext-configure gd --with-freetype --with-jpeg

# установка composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./conf.d /usr/local/etc/php/conf.d

WORKDIR /app

CMD ["php-fpm"]
