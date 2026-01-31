FROM php:8.5-fpm

# ставим необходимые для нормальной работы модули
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libssl-dev \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip gd \
        && docker-php-ext-configure gd --with-freetype --with-jpeg

# установка composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# запись конфига php-fpm, чтобы слушать через unix-сокет
COPY zzz-socket.conf /usr/local/etc/php-fpm.d/zzz-socket.conf
#
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /data

CMD ["php-fpm"]
