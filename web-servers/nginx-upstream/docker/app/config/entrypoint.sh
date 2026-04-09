#!/bin/sh
set -e

if [ -n "$REDIS_PASSWORD" ]; then
  sed -i "s/PASSWORD/${REDIS_PASSWORD}/g" /usr/local/etc/php-fpm.d/www.conf
fi

exec "$@"