FROM php:8.2-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev default-libmysqlclient-dev \
    && docker-php-ext-install sockets bcmath pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Расширение sockets полезно php-amqplib (быстрее, чем stream),
# bcmath - для корректной работы с большими числами в протоколе AMQP.

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

CMD ["php-fpm"]