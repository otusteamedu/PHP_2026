FROM php:8.2-fpm

RUN pecl install redis \
	&& docker-php-ext-enable redis

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

CMD ["php-fpm"]