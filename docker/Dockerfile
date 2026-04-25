FROM php:8.5-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libmemcached-dev \
    zlib1g-dev \
    libssl-dev \
    libsasl2-dev \
    pkg-config && \
    docker-php-ext-install zip && \
    pecl install redis memcached xdebug && \
    docker-php-ext-enable redis memcached xdebug && \
    rm -rf /var/lib/apt/lists/*

COPY ./conf.d /usr/local/etc/php/conf.d

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /app
