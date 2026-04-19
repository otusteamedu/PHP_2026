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
    pecl install redis memcached && \
    docker-php-ext-enable redis memcached && \
    rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
