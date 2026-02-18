FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    git

RUN pecl install redis \
	&& docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

CMD ["php-fpm"]