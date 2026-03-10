FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libssl-dev \
    libmemcached-dev \
    zlib1g-dev \
    libpq-dev \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        iconv \
        mbstring \
        pdo \
        pdo_pgsql \
        zip \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin \
    --filename=composer

RUN pecl install redis memcached \
    && docker-php-ext-enable redis memcached

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

WORKDIR /var/www/otus_first_app

CMD ["php-fpm"]