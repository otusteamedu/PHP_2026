FROM php:8.5-fpm

# устанавливаем необходимые модули для корректной работы php контейнера
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

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

# устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# добавляем Composer в PATH
ENV PATH="$PATH:/usr/local/bin"

CMD ["php-fpm"]
